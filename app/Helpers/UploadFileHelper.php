<?php 

    function upload($file, $name_type, $path){
        $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
        $name = $name_type . '-' . $timestamp . "." . $file->extension();
        // $request->file->move(base_path('public/upload/avatar/'), $name); #save in /public
        $file->move($path, $name);  #save in /storage
        
        return $name;
    }

    function uploadToCloud($prefix, $file){
        $filename = $prefix .'/'.$prefix. '-' . date('Ymdhms') .'-'. rand(); 

        if ( is_string($file)){
            $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
            $filename = $filename. '.png';
            Storage::cloud()->put($filename, $file);
        } else {
            $filename = $filename. '.' .$file->extension();
            Storage::cloud()->putFileAs($prefix, $file, $filename);
        }

        $url = Storage::cloud()->url($prefix . '/' . $filename);
        return $url;
    }
