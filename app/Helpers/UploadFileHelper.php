<?php 

    function upload($file, $name_type, $path){
        $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
        $name = $name_type . '-' . $timestamp . "." . $file->extension();
        // $request->file->move(base_path('public/upload/avatar/'), $name); #save in /public
        $file->move($path, $name);  #save in /storage
        
        return $name;
    }

    function uploadToMinio($file, $folder){
        $path = Storage::disk('minio')->putFile($folder, $file, 'public');
        return $path;
    }
