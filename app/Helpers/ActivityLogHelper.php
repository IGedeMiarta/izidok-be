<?php 

use App\ActivityLog;

function saveActivityLog($activity, $userAgent, $data){  
    $log = new ActivityLog();
    $log->activity = $activity;
    $log->ip = $ip;
    $log->browser = $browser;
    $log->data = json_encode($data);
    $log->save();

    return true;
}
?>