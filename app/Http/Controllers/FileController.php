<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class FileController extends Controller
{
    
    function upload(Request $request){

        $originName = $request->file('file')->getClientOriginalName();
        $extension = $request->file('file')->getClientOriginalExtension();

        $fileName = Carbon::now()->timestamp . '_' . uniqid().'.'.$extension;
    
        $request->file('file')->move(public_path('files'), $fileName);
        $fileRoute = url('/').'/files/'.$fileName;

        return response()->json(["fileRoute" => $fileRoute, "originalName" => $originName,"extension" => $extension, "success" => true]);

    }

}
