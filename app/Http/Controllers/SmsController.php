<?php
/*
*  Title: SMS Helper
*  Version: 1.0 from 21 August 2021
*  Author: Fadli Saad
*  Website: https://github.com/fadlisaad
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsController extends Controller
{
	private $username = "bptmjkmm";
	private $password = "bptmjkmm";

	public function __construct(){

	}

	public function postData($url, $data){

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}

	public function sendSms(Request $request)
	{
		$data = $request->all();
		return send_sms($data);
	}

	public function login(){

		/*
		<?xml version="1.0" encoding="utf-8" ?>
		<rsp status="ok">
		    <sessionid>897f8klhcr1vcgq1em54piql74</sessionid>
		</rsp>
		*/

		$url = 'http://www.infoblast.com.my/openapi/login.php';
		$data = array(
			'username' => $this->username,
			'password' => sha1($this->password)
		);
		$load = $this->postData($url, $data);
		$xml = simplexml_load_string($load) or die("Error: Cannot create object");
		return $xml->sessionid;
	}

	public function send($data){

		/*
		<?xml version="1.0" encoding="utf-8" ?>
		<rsp status="OK">
		    <messageid>051551162806001</messageid>
		</rsp>
		*/

		$url = 'http://www.infoblast.com.my/openapi/sendmsg.php';
		$go = array(
			'sessionid' => $data['sessionid'],
			'msgtype' => 'text',
			'message' => $data['message'],
			'to' => $data['to']
		);
		$load = $this->postData($url, $go);
		$xml = simplexml_load_string($load) or die("Error: Cannot create object");

		if($xml->err) return $xml->err;
		return $xml->messageid;
	}
	
	public function details($data){

		/*
		<?xml version="1.0" encoding="utf-8" ?>
		<rsp status="OK">
		    <stats>
		        <record>
		            <msgid>051550473838001</msgid>
		            <aparty>063338383</aparty>
		            <bparty>0126471057</bparty>
		            <msgtype>netsms</msgtype>
		            <startdate>1550473845</startdate>
		            <enddate>1550473845</enddate>
		            <status>Pending</status>
		            <statusdesc>Pending</statusdesc>
		        </record>
		    </stats>
		</rsp>
		*/

		$url = 'http://www.infoblast.com.my/openapi/getsendstatus.php';
		$go = array(
			'sessionid' => $data['sessionid'],
			'msgid' => $data['messageid']
		);
		$load = $this->postData($url, $go);
		$xml = simplexml_load_string($load) or die("Error: Cannot create object");
		return $xml;
	}

	public function logout($session){

		/*
		<?xml version="1.0" encoding="utf-8" ?>
		<rsp status="OK"></rsp>
		*/

		$url = 'http://www.infoblast.com.my/openapi/logout.php';
		$data = array(
			'sessionid' => $session
		);
		$load = $this->postData($url, $data);
		$xml = simplexml_load_string($load) or die("Error: Cannot create object");
		return $xml;
	}
}