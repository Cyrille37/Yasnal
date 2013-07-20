<?php
/**
 * Yasnal
 */

namespace Yasnal ;

class AuthEngine {

	const YASNAL_CSRF = 'YASNAL_CSRF' ;

	public function __constructor()
	{
	}

	public static function sendSpecialHttpHeaders()
	{
		if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] )
			exit;
		@header('X-Robots-Tag: noindex');
		@header('X-Content-Type-Options: nosniff' );
		@header('Access-Control-Allow-Origin: *' );
		@header('Access-Control-Allow-Credentials: true' );
		$headers = array(
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
			'Pragma' => 'no-cache',
			'Last-Modified' => ''
		);
		foreach( $headers as $name => $field_value )
			@header("{$name}: {$field_value}");
	}

	public static function CsrfGet()
	{
		// Do not check CSRF if no session available
		if( ! self::session_start() )
			return '' ;
		$csrf = rand(1,9999).time().rand(1,9999) ;
		$_SESSION[self::YASNAL_CSRF] = $csrf ;
		return md5($csrf);
	}
	public static function CsrfCheck($token)
	{
		// Do not check CSRF if no session available
		if( ! self::session_start() )
			return true ;
		if( ! isset($_SESSION[self::YASNAL_CSRF]) )
			return false ;
		else if( $token != md5($_SESSION[self::YASNAL_CSRF]) )
			return false ;
		return true ;
	}
	
	protected static function session_start()
	{
		if (!isset($_SESSION))
			session_start();
		if (isset($_SESSION))
			return true ;
		return false ;
		/*
		// PHP >= 5.4.0
		switch( session_status() )
		{
		case PHP_SESSION_DISABLED:
			break;
		case PHP_SESSION_NONE:
			break;
		case PHP_SESSION_ACTIVE:
			break;
		}
		*/
	}
}
