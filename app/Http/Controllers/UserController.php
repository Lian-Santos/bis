<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserController extends Controller
{
    public function noVerificationRegistration(Request $request)
    {
        $email = $request->email;
        $pass = $request->pass;
        $encrypted_pass = password_hash($pass, PASSWORD_DEFAULT);
        $exists_email = DB::select("
            SELECT *
            FROM users
            where email = '$request->email'
        ");
        if(count($exists_email) > 0)
        {
            return response()->json([
                'error_msg' => 'Email already in use'
            ],400);
        }
        DB::statement("INSERT 
        INTO users
        (first_name,middle_name,last_name,email,email_verified_at,password)
        VALUES
        (
        '$request->first_name',
        '$request->middle_name',
        '$request->last_name',
        '$request->email',
        NULL,
        '$encrypted_pass'
        )
        ");
        DB::statement("INSERT
        INTO user_roles (user_id,role_id)
        SELECT
        us.id as user_id,
        '1' as role_id
        FROM users as us
        where us.email = '$request->email'
        ");
        return response()->json([
            'msg' => 'Account created'
        ],200);

    }
    public function manualLogin(Request $request)
    {
        $current_date_time = date('Y-m-d H:i:s');
        $email = $request->email;
        $pass = $request->pass;
        $user_details = DB::select(
            "SELECT
            id,
            password
            FROM users
            where email = '$email'
            "
        );
        if(count($user_details) < 1 || !password_verify($pass, $user_details[0]->password))
        {
            return response()->json([
                'error_msg' => 'User with that email and password combination cannot be found'
            ],400);
        }
        $user_id = $user_details[0]->id;
        $token_value = hash('sha256', $user_id . $email . $current_date_time);
        DB::statement("INSERT
        INTO custom_tokens
        (user_id,token,session_role_id,expires_at,created_at,updated_at)
        VALUES
        (
        '$user_id',
        '$token_value',
        '1',
        date_add('$current_date_time',interval 30 minute),
        '$current_date_time',
        '$current_date_time'
        )
        ");
        return response()->json([
            'access_token' => $token_value
        ],200);
    }
    public function adminLogin(Request $request)
    {
        $current_date_time = date('Y-m-d H:i:s');
        $email = $request->email;
        $pass = $request->pass;
        $user_details = DB::select(
            "SELECT
            us.id,
            us.password,
            ur.role_id as role_id
            FROM users as us
            LEFT JOIN user_roles as ur on ur.user_id = us.id
            where email = '$email'
            "
        );
        if(count($user_details) < 1 && !password_verify($pass, $user_details[0]->password))
        {
            return response()->json([
                'error_msg' => 'User with that email and password combination cannot be found'
            ],400);
        }
        if(!in_array($user_details[0]->role_id,['2','3']))
        {
            return response()->json([
                'error_msg' => 'User has no admin role'
            ],400);
        }
        $role_id = $user_details[0]->role_id;
        $user_id = $user_details[0]->id;
        $token_value = hash('sha256', $user_id . $email . $current_date_time);
        DB::statement("INSERT
        INTO custom_tokens
        (user_id,token,session_role_id,expires_at,created_at,updated_at)
        VALUES
        (
        '$user_id',
        '$token_value',
        '$role_id',
        date_add('$current_date_time',interval 30 minute),
        '$current_date_time',
        '$current_date_time'
        )
        ");
        return response()->json([
            'access_token' => $token_value
        ],200);
    }
    public function getUserDetails()
    {
        $user_id = session("UserId");
        $current_session_role = session("SessionRole");
        $user_details = DB::select("SELECT
            u.Email,
            u.first_name,
            u.middle_name,
            u.last_name,
            CONCAT(u.first_name,' ',u.middle_name,'',u.last_name) as full_name,
            r.role_type,
            '$current_session_role' as current_session_role
        FROM users as u
        LEFT JOIN user_roles as ur on ur.user_id = u.id
        LEFT JOIN roles as r on r.id = ur.role_id
        where u.id = '$user_id'
        ");
        return response()->json($user_details,200);
    }

}
