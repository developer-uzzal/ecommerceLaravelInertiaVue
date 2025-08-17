<?php

namespace App;

use Illuminate\Support\Facades\Storage;

trait FileUploadMedia
{
    public static function upload($file, $name, $dir = "others", $disk = 'public', $oldFile = null)
    {
        if ($file) {
            // ফাইলের নাম বানানো
            $fileName = $name 
                ? $name . "." . $file->getClientOriginalExtension() 
                : $file->getClientOriginalName() . time() . "." . $file->getClientOriginalExtension();

            // পুরোনো ফাইল ডিলিট
            if ($oldFile) {
                // "storage/" prefix থাকলে সরানো
                $oldFilePath = str_replace('storage/', '', $oldFile);

                if (Storage::disk($disk)->exists($oldFilePath)) {
                    Storage::disk($disk)->delete($oldFilePath);
                }
            }

            $upload = $file->storeAs($dir, $fileName, $disk);

            return $upload;
        }

        return null;
    }
}
