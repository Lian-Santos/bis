<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class DocumentController extends Controller
{
    public function addDocumentType(Request $request)
    {
        $service = $request->service;
        $description = $request->description;
        DB::table('document_types')
            ->insert([
                'service' => $service,
                'description' => $description
            ]);
        /*
        DB::statement("INSERT
        INTO document_types
        (service,description)
        VALUES ('$service','$description')
        ");
        */
        return response()->json([
            'msg' => 'New document type added'
        ]);
    }
    public function deleteDocumentType(Request $request)
    {
        $document_type_id = $request->document_type_id;
        DB::statement("DELETE
        FROM
        document_types
        WHERE id = '$document_type_id'
        ");
        return response()->json([
            'msg' => 'Document type has been deleted'
        ]);
    }
    public function getDocumentTypes()
    {
        $values = DB::select("SELECT
        * 
        FROM document_types
        ");
        return response()->json($values,200);
    }
    public function testString(Request $request)
    {

        $string = $request->format;

        // Regular expression pattern to match substrings starting with << and ending with >>
        $pattern = '/<<([^>]+)>>/';

        // Use preg_match_all to find all matches
        preg_match_all($pattern, $string, $matches);
        $sample_user = DB::select("SELECT 
        u.first_name as firstName,
        u.middle_name as middleName,
        u.last_name as lastName,
        ct.civil_status_type as civilStatus
        FROM users as u
        LEFT JOIN civil_status_types as ct on ct.id = u.civil_status_id
        
        ")[0];
        foreach($matches[1] as $match)
        {
            $string = str_replace("<<" . $match . ">>", ((array)$sample_user)[$match], $string);
        }
        return $string;
        return $sample_user;
        $array_version =  ((array)$sample_user);
        return gettype((array)$sample_user);

        return response()->json($matches);
    }
}
