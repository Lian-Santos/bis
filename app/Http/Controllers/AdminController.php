<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AdminController extends Controller
{
    /*
    public function viewAdminableUsers()
    {
        $adminable_users = DB::select("SELECT
        CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name) as full_name,
        u.id as user_id
        FROM users as u
        LEFT JOIN user_roles as ur on ur.user_id = u.id
        WHERE ur.role_id NOT IN ('2','3')
        ");
        return response()->json($adminable_users,200);
    }
    */
    public function assignRole(Request $request)
    {
        $role_id = $request->role_id;
        $user_id = $request->user_id;
        DB::statement("UPDATE
        user_roles
        set role_id = '$role_id'
        WHERE user_id = '$user_id'
        ");
        return response()->json([
            'msg' => 'user has been assigned new role'
        ],200);
    }
    public function viewAssignableRoles()
    {
        return DB::select("SELECT
        *
        FROM roles
        WHERE id != '1'
        ");
    }
    public function viewPrivilegedUsers()
    {
        $user_id = session("UserId");
        return DB::select("SELECT
        u.id,
        CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name) as full_name
        FROM users as u
        LEFT JOIN user_roles as ur on ur.user_id = u.id
        WHERE ur.role_id IN ('2','3') AND u.id != '$user_id'
        ");
    }
    public function revokeAdminAccess(Request $request)
    {
        $user_id = $request->user_id;
        DB::statement("UPDATE
        user_roles
        set role_id = '1'
        WHERE user_id = '$user_id'
        ");
        return response()->json([
            'msg' => 'Revoked user admin privileges'
        ]);
    }
}
