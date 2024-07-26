<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class BarangayOfficialController extends Controller
{
    public function assignBarangayOfficial(Request $request)
    {
        $user_id = $request->user_id;
        $user_details = DB::select("SELECT
        u.id,
        u.first_name,
        u.middle_name,
        u.last_name,
        bo.id as barangay_official_id
        FROM users as u
        LEFT JOIN barangay_officials as bo on bo.user_id = u.id
        where u.id = '$user_id'
         ");
        if(count($user_details) < 1)
        {
            return response()->json([
                'error_msg' => 'User with specified id does not exist'
            ],400);
        }
        if(!is_null($user_details[0]->barangay_official_id))
        {
            return response()->json([
                'error_msg' => 'User already has a barangay official entry'
            ],400);
        }
        $chairmanship = $request->chairmanship;
        $position = $request->position;

        DB::statement("INSERT INTO
        barangay_officials
        (user_id,chairmanship,position,status)
        VALUES ('$user_id','$chairmanship','$position','1')
        ");

        return response()->json([
            'msg' => 'User has been assigned as a barangay official'
        ],200);
    }
    public function viewAssignableToBarangayOfficial()
    {

        return DB::select("SELECT
            u.id,
            CONCAT(u.first_name,' ',u.middle_name,'',u.last_name) as full_name
            FROM users as u
            LEFT JOIN barangay_officials as bo on bo.user_id = u.id
            where bo.id IS NULL
        ");
    }
    public function viewBarangayOfficials()
    {
        $barangay_officials = DB::select("SELECT
        u.id as user_id,
        CONCAT(u.first_name,' ',u.middle_name,'',u.last_name) as full_name,
        bo.chairmanship,
        bo.position,
        bo.status
        FROM users as u
        LEFT JOIN barangay_officials as bo on bo.user_id = u.id
        WHERE bo.id IS NOT NULL
        ");
        return response()->json($barangay_officials,200);
    }
}
