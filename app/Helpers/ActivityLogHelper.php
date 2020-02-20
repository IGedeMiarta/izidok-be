<?php 

use App\ActivityLog;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;

function saveActivityLog($activity, $request, $dta){
    $agent = new Agent();
    $userId = empty(Auth::user()) ? null : Auth::user()->id;
    $data = is_null($dta) ? null : json_encode($dta);

    $log = new ActivityLog();
    $log->activity = $activity;
    $log->ip = $request->ip();
    $log->user_id = $userId;
    $log->browser = $agent->browser();
    $log->device = $agent->device();
    $log->platform = $agent->platform();
    $log->data = $data;
    $log->save();

    return true;
}
?>