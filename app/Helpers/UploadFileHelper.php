<?php 

    function upload($file, $name_type, $path){
        $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
        $name = $name_type . '-' . $timestamp . "." . $file->extension();
        // $request->file->move(base_path('public/upload/avatar/'), $name); #save in /public
        $file->move($path, $name);  #save in /storage
        
        return $name;
    }

    function uploadToCloud($prefix, $file){
        $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
        $rand = rand();
        $filename = $prefix .'/'.$prefix. '-' . date('Ymdhms') .'-'. $rand . '.png';
        
        Storage::cloud()->put($filename, $file);
        return $filename;
    }

    function testUpload($prefix, $file){

        $rand = rand();
        $filename = $prefix. '-' . date('Ymdhms').'-'. $rand .'.'. $file->extension();
        
        // Storage::cloud()->putFile($filename, $file);
        Storage::cloud()->putFileAs($prefix, $file, $filename);
        $url = Storage::cloud()->url($filename);

        return $url;
    }
