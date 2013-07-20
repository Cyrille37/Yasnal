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

		$csrf = isset($_REQUEST['csrf']) ? $_REQUEST['csrf'] : null ;
		if( ! \Yasnal\AuthEngine::CsrfGet($csrf) )
		{
			$response['status']='error';
			$response['message']='CSRF check failed';
		}
		else
		{
		}

		return json_encode($response);
	}
}
