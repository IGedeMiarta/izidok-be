<?php 

use App\AuditsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

function saveAudits($event, $request, $dta){
    $userAgent = $request->server('HTTP_USER_AGENT');
    if(preg_match("/login/i", $event)) {
        $userId = $request->userId;
    } else {
        $userId = empty(Auth::user()) ? null : Auth::user()->id;
    }
    $data = is_null($dta) ? null : json_encode($dta);

    $log = new AuditsModel();
    $log->user_id = $userId;
    $log->event = $event;
    $log->auditable_type = 'App\User';
    $log->auditable_id = $userId;
    $log->url = URL::current();
    $log->ip_address = $request->ip();
    $log->user_agent = $userAgent;
    $log->created_at = date('Y-m-d H:i:s');
    $log->updated_at = date('Y-m-d H:i:s');
    $log->save();

    return true;
}
?>