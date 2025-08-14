<?php

namespace App;

use Illuminate\Support\Facades\Storage;

trait FileUploadMedia
{
    public static function upload( $file, $name, $dir = "others",$disk = 'public', $oldFile = null)
    {
        if($file){

            

            $fileName = $name ? $name . "." . $file->getClientOriginalExtension() : $file->getClientOriginalName().time().".".$file->getClientOriginalExtension();

            if($oldFile){
                str_replace(url('storage') . '/', '', $oldFile);
                Storage::disk($disk)->delete($oldFile);
            }



            $upload = $file->storeAs($dir, $fileName, $disk);

            $url = 'storage/'.$upload;

            
            return $url;

        }

    }




}
