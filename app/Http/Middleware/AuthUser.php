<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;


class AuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $userType){
        $current_date_time = date('Y-m-d H:i:s');
        $bearer_token= request()->bearerToken();

        $userTypeArray = explode('-',$userType);
        $validate_token = DB::select("SELECT
        user_id
        FROM custom_tokens
        WHERE token = '$bearer_token' and CAST(expires_at AS DATETIME) > CAST('$current_date_time' AS DATETIME)
        ");
        if(count($validate_token) < 1)
        {
            return response()->json(['message' => 'You do not have permission to access for this API.'], 404);
        }
        $user_id = $validate_token[0]->user_id;
        $user = DB::select("SELECT
            u.Id,
            u.Email,
            u.first_name,
            u.middle_name,
            u.last_name,
            CONCAT(u.first_name,' ',u.middle_name,'',u.last_name) as full_name,
            ur.role_id as role_id
            FROM users as u
            LEFT JOIN user_roles as ur on ur.user_id = u.id
            where u.id = '$user_id'
        ");
        if(count($user) > 0){
            session(['Email' => $user[0]->Email]);
            session(['UserId' => $user[0]->Id]);
            return $next($request);
            $user_id = session('UserId');
            session(['EmployeeCode' => $user[0]->EmployeeCode]);
            if(count($role_check)>0)
            {
                session(['UserAccess' => $role_check[0]->access_option]);
            }
            /*
            else
            {
                session(['UserAccess' => 'Employee']);
            }
            */
            $role_raw = DB::table('dbo.Users as u')
                ->where('u.Id','=',$user[0]->Id)
                ->select(
                    'u.role_id',
                )
                ->get();
            session(['RoleId' => $role_raw[0]->role_id]);
            $role = array_map(function($entry){return $entry->role_id;},json_decode($role_raw));

            if($userTypeArray[0] == 'User')
            {
                return $next($request);
                //return response()->json(['message'=>'You do not have the necessary role for access for this API'],404);
            }
            else if(in_array(session('UserAccess'),$userTypeArray) && $user[0]->ExpiryDate > date('Y-m-d H:i:s') && $user[0]->role_status == 'Active'){
                return $next($request);
            }
            else{
                return response()->json(['message'=>'You do not have the necessary role for access for this API'],404);
            }
            return $next($request);

        }
        return response()->json(['message' => 'You do not have permission to access for this API.'], 404);

    }

}
