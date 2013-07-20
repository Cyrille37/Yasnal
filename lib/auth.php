<?php

require_once( __DIR__.'/Yasnal.php');

$provider = isset($_REQUEST['provider'])?$_REQUEST['provider']:null;
$email = isset($_REQUEST['email'])?$_REQUEST['email']:null;
$signature = isset($_REQUEST['signature'])?$_REQUEST['signature']:null;
$token = isset($_REQUEST['token'])?$_REQUEST['token']:null;

$response =array(
		'status' => 'error',
		'message' => 'unknow error'
);

switch($provider)
{
	case 'email':
		if( \Yasnal\AuthEngine::signCheck($signature, $email.$token) )
		{
			$response['status'] = 'ok';
			$response['message'] = 'Ok';
		}
		else
		{
			$response['message'] = 'Auth signature mismatch';
		}
		break;
	default:
		$response['message'] = 'Unknow auth provider';
}
echo json_encode($response);
