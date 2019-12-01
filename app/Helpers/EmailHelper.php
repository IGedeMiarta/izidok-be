<?php 

use Illuminate\Support\Facades\Mail;

function sendEmail($data, $template){  
    Mail::send($template, $data, function ($msg) use ($data){
        $msg->subject($data['subject']); 
        $msg->from([$data['from']], 'Izidok.com'); 
        $msg->to($data['to']); 
    });

    return true;
}

/*
    1. send email with HTML view use this -> Mail::send('emails.welcome', $data, function ($message) {});
    2. to add cc on recipient use this -> $message->cc($address, $name = null);
    3. to add bcc on recipient use this -> $message->bcc($address, $name = null);
    4. to send email with attachments use this -> $message->attach($pathToFile);
    5. to send email with attachments use this -> $message->attachData($pdf, 'invoice.pdf');
    6. to send email with attachments use this -> $message->attachData($pdf, 'invoice.pdf', ['mime' => $mime]);
*/

?>