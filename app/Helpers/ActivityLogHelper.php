<?php 

use App\ActivityLog;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;

function saveActivityLog($activity, $request, $data){
    $agent = new Agent();
    $userId = empty(Auth::user()) ? null : Auth::user()->id;

    $log = new ActivityLog();
    $log->activity = $activity;
    $log->ip = $request->ip();
    $log->user_id = $userId;
    $log->browser = $agent->browser();
    $log->device = $agent->device();
    $log->platform = $agent->platform();
    $log->data = json_encode($data);
    $log->save();

    return true;
}
?>