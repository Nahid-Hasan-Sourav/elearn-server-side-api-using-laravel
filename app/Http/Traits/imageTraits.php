<?php
namespace App\Http\Traits;

trait imageTrait{

    public function getImageUrl($file, $path)
    {
        $file_path = null;
        if ($file && $file !== 'null') {
            $file_name = date('Ymd-his') . '.' . $file->getClientOriginalName();
            $destinationPath = public_path($path);
           
            // Move the uploaded file to the destination path without resizing
            $file->move($destinationPath, $file_name);           
            $file_path = $path . $file_name;
        }
        return $file_path ?? null;
    }
    



}



?>

