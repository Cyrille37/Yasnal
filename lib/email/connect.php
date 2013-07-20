<?php

namespace Yasnal\Email ;

error_reporting(-1);

require_once( __DIR__.'/../Yasnal.php');

echo AuthEmail::run();

class AuthEmail {

	const MAILER_CALLBACK = 'yasnal_mailerCallback';

	public static function run()
	{
		$response = array(
				'status' => 'error',
				'message' => 'nothing done'
		);
		\Yasnal\AuthEngine::sendSpecialHttpHeaders();

		$action = $_REQUEST['action'];
		switch($action)
		{
			case 'emailSend':
				self::mailSend($response);
				break;
			default:
				$response['message']='CSRF check failed';
				break;
		}
		return json_encode($response);
	}

	protected static function mailSend(&$response)
	{
		// Check for mailer callback (file & function=

		if( empty( \Yasnal\AuthEngine::$config['mailerCallbackPhpFile'] ) )
		{
			$response['status']='error';
			$response['message']='Config error, mailer callback file not defined';
			return ;
		}
		else if( ! file_exists(\Yasnal\AuthEngine::$config['mailerCallbackPhpFile']) )
		{
			$response['status']='error';
			$response['message']='Config error, mailer callback file not found';
			return ;
		}
		require_once( \Yasnal\AuthEngine::$config['mailerCallbackPhpFile'] );
		if( ! is_callable(self::MAILER_CALLBACK) )
		{
			$response['status']='error';
			$response['message']='Config error, missing mailer callback function';
			return ;
		}

		// Check CSRF

		$csrf = isset($_REQUEST['csrf']) ? $_REQUEST['csrf'] : null ;
		if( ! \Yasnal\AuthEngine::CsrfCheck($csrf) )
		{
			$response['status']='error';
			$response['message']='CSRF check failed';
			return ;
		}

		$email = $_REQUEST['email'];
		if( ! \Yasnal\AuthEngine::isValidEMail($email) )
		{
			$response['status']='error';
			$response['message']='Invalid email';
			return ;
		}

		$pincode = rand(1,9).rand(0,9).rand(0,9).rand(0,9);
		$signature = \Yasnal\AuthEngine::sign($email.$pincode);

		$res = call_user_func( self::MAILER_CALLBACK, $email, $pincode );

		if( is_array($res) && isset($res['status']) )
		{
			switch($res['status'])
			{
				case 'ok' :
					$response['status']='ok';
					$response['message']='Mail sent';
					$response['signature']=$signature;
					break;
				case 'error':
				default:
					$response['status']='error';
					$response['message']= isset($res['message'])?$res['message']:'unknow error';
					break;
			}
		}
		else
		{
			$response['message']= 'Malformed mailer callback response';
		}

	}
}
