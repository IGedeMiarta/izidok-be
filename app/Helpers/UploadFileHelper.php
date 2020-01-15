<?php

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;


function upload($file, $name_type, $path)
{
    $path = storage_path($path);
    $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
    $name = $name_type . '-' . $timestamp . "." . $file->extension();

    $file->move($path, $name);

    $filepath = $path . '/' . $name;
    return $filepath;
}

function uploadToCloud($prefix, $file)
{
    $filename = $prefix . '/' . $prefix . '-' . date('Ymdhms') . '-' . rand();

    if (is_string($file)) {
        $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
        $filename .= '.png';
        Storage::cloud()->put($filename, $file);
    } else {

        if (strtolower($file->extension()) !== 'pdf') {
            return false;
        }
        #run script compress PDF
        $compressed = compressPDF($file);
        $compressed_file = file_get_contents($compressed['output']);

        $filename .= '.pdf';
        Storage::cloud()->put($filename, $compressed_file);
    }

    $url = Storage::cloud()->url($filename);

    if ($url) {
        #delete local files
        $file = new Filesystem;
        $file->cleanDirectory(storage_path('tmp'));
    }

    return $url;
}

function compressPDF($file)
{
    $timestamp = str_replace([' ', ':'], '-', date('Ymd h:i:s'));
    $input_path = upload($file, 'test', 'tmp');
    $output_path = storage_path('tmp/compressed-' . rand() . '-' . $timestamp . '.pdf');

    $process = new Process('./shrinkpdf.sh ' . $input_path . ' ' . $output_path);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    $data['input'] = $input_path;
    $data['output'] = $output_path;

    return $data;
}
