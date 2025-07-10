<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocController extends Controller
{
    public function index()
    {
        $docs = DB::table('docs')->get();
        return $this->responseJson($docs);
    }
    public function upload(Request $request)
    {
        $files = $request->file('fileList');

        foreach ($files as $file) {
            $filename = $file['raw']->getClientOriginalName();
            $file['raw']->move(public_path() . '/uploads/', $filename);
            // $file->move(public_path(). '/uploads/', $filename);
            return $this->jsonOk(['filename' => $filename]);
        }
        // return $this->jsonOk(['filename' => $filename]);
    }
}
