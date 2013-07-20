<?php

require_once( __DIR__.'/Yasnal.php');

$response =array(
		'status' => 'error',
		'message' => 'unknow error'
);

$action = isset($_REQUEST['action'])?$_REQUEST['action']:null;

switch( $action )
{
	case 'auth':
		auth($response);
		break;
	case 'getAuth':
		getAuth($response);
		break;
	case 'unAuth':
		unAuth($response);
		break;
}

function getAuth(&$response)
{
	$loaded = \Yasnal\AuthEngine::loadCallback('auth', \Yasnal\AuthEngine::$config['authCallbackPhpFile'],\Yasnal\AuthEngine::GETAUTH_CALLBACK);
	if( $loaded !== true )
	{
		$response['status']='error';
		$response['message']='Config error, '.$loaded;
		return ;
	}
	
	$response['status'] = 'ok';
	$response['message'] = 'Ok';
	$response['auth'] = call_user_func( \Yasnal\AuthEngine::GETAUTH_CALLBACK );
}

function unAuth(&$response)
{
	$loaded = \Yasnal\AuthEngine::loadCallback('auth', \Yasnal\AuthEngine::$config['authCallbackPhpFile'],\Yasnal\AuthEngine::UNAUTH_CALLBACK);
	if( $loaded !== true )
	{
		$response['status']='error';
		$response['message']='Config error, '.$loaded;
		return ;
	}

	$response['status'] = 'ok';
	$response['message'] = 'Ok';
	call_user_func( \Yasnal\AuthEngine::UNAUTH_CALLBACK );
}

function auth(&$response)
{
	$provider = isset($_REQUEST['provider'])?$_REQUEST['provider']:null;
	$email = isset($_REQUEST['email'])?$_REQUEST['email']:null;
	$signature = isset($_REQUEST['signature'])?$_REQUEST['signature']:null;
	$token = isset($_REQUEST['token'])?$_REQUEST['token']:null;

	// Processing Auth

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

		case 'google':
			if( \Yasnal\AuthEngine::signCheck($signature, $email) )
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

	// Call the Auth callback

	if( $response['status']!='error')
	{

		$loaded = \Yasnal\AuthEngine::loadCallback('auth', \Yasnal\AuthEngine::$config['authCallbackPhpFile'],\Yasnal\AuthEngine::AUTH_CALLBACK);
		if( $loaded !== true )
		{
			$response['status']='error';
			$response['message']='Config error, '.$loaded;
		}

		if( $response['status']!='error')
		{
			$res = call_user_func( \Yasnal\AuthEngine::AUTH_CALLBACK, $provider, $email );
		}
	}
}

// Done, return result to client

\Yasnal\AuthEngine::sendSpecialHttpHeaders();

echo json_encode($response);
