<?php 

    function upload($file, $name_type, $path){
        $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
        $name = $name_type . '-' . $timestamp . "." . $file->extension();
        // $request->file->move(base_path('public/upload/avatar/'), $name); #save in /public
        $file->move($path, $name);  #save in /storage
        
        return $name;
    }
?>