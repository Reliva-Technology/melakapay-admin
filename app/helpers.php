<?php
use App\Models\LogSms as LogSms;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

if (! function_exists('send_sms')) {

    function send_sms($data)
    {
        $sms = new SmsController();

        $message = "RM0.00 MelakaPay - Kata laluan sementara anda ialah ".$data['password'].". Dijana pada ".$data['timestamp'];

		(string) $newSessionID = $sms->login();

		$sendData = array(
		    'sessionid' => $newSessionID,
		    'message' => $message,
		    'to' => $data['phone_number']
		);

		(string) $newMsgid = $sms->send($sendData);

        $log = new LogSms;
        $log->user_id = $data['user_id'];
        $log->phone_number = $data['phone_number'];
        $log->sessionid = $newSessionID;
        $log->messageid = $newMsgid;
        $log->save();

        $sms->logout($newMsgid);
    }
}