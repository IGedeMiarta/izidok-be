<?php 

use App\ActivityLog;
use Jenssegers\Agent\Agent;

function saveActivityLog($activity, $data){
    $agent = new Agent();

    $log = new ActivityLog();
    $log->activity = $activity;
    $log->ip = $data->ip();
    $log->browser = $agent->browser();
    $log->device = $agent->device();
    $log->platform = $agent->platform();
    $log->data = json_encode($data);
    $log->save();

    return true;
}
?>