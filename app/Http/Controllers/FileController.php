<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function upload($folder, Request $req){
        
        $file = $req->file('file');
        $name_source = $file->getClientOriginalName();
        $file_path = $file->getPathName();
        $name_dest = md5(time().$name_source);
        $ext = $file->getClientOriginalExtension();
        $path_full = 'avatars/full/' . $name_dest; 
        $mime = $file->getClientMimeType();

        if($mime != "application/octet-stream"){
            return response()->json(0, 400);
        }
        // Put the file in local to then 
        Storage::disk('local')->put('public/' . $path_full, File::get($file_path));
        // Put the file in the cloud
        Storage::disk('gcs')->put(
            $path_full, 
            file_get_contents(storage_path('app/public/' + $path_full)),
            \Illuminate\Contracts\Filesystem\Filesystem::VISIBILITY_PUBLIC);
        // Get the file from the cloud to access the URL
        Storage::disk('gcs')->get($path_full);
        return response()->json($name_dest, 200);
    }
    public function download(){

    }
    // public function serveAvatar($profile){
    //     $storagePath = storage_path() . '/avatar/' . $profile . ''; 

    //     return Image::make($storagePath)->response();
    // }
}
