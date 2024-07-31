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
        DB::statement("INSERT
        INTO document_types
        (service,description)
        VALUES ('$service','$description')
        ");
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
}
