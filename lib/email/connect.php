<?php

namespace Yasnal\Email ;

require_once( __DIR__.'/../Yasnal.php');

error_log( var_export($_REQUEST,true));

echo AuthEmail::run();

class AuthEmail {
	
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
		if( empty( \Yasnal\AuthEngine::$mailerCallbackPhpFile ) )
		{
			$response['status']='error';
			$response['message']='Config error, mailer callback file not defined';
			return ;
		}
		else if( ! file_exists(\Yasnal\AuthEngine::$mailerCallbackPhpFile) )
		{
			$response['status']='error';
			$response['message']='Config error, mailer callback file not found';
			return ;
		}
		require_once( \Yasnal\AuthEngine::$mailerCallbackPhpFile );

		$csrf = isset($_REQUEST['csrf']) ? $_REQUEST['csrf'] : null ;
		if( ! \Yasnal\AuthEngine::CsrfCheck($csrf) )
		{
			$response['status']='error';
			$response['message']='CSRF check failed';
		}
		else if( ! is_callable(\Yasnal\AuthEngine::$mailerCallback) )
		{
			$response['status']='error';
			$response['message']='Config error, missing mailer callback function';
		}
		else
		{
			$email = $_REQUEST['email'];
			if( ! \Yasnal\AuthEngine::isValidEMail(email) )
			{
				$response['status']='error';
				$response['message']='Invalid email';
				return ;
			}

			$pincode = rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
			$signature = hash('SHA256', \Yasnal\AuthEngine::$auth_secret . $pincode) ;

			$res = call_user_func( 'yasnal_mailerCallback' );
			if( is_array($res) && isset($res['status']) )
			{
				switch($res['status'])
				{
				case 'ok' :
					$response['status']='ok';
					$response['message']='Mail sent';
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
}
