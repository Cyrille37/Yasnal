<?php
/**
 * Yasnal
 */

namespace Yasnal ;

//error_log( basename($_SERVER['PHP_SELF']).' '.var_export($_REQUEST,true));

class AuthEngine {

	const YASNAL_CSRF = 'YASNAL_CSRF' ;
	const AUTH_COOKIE = 'Yasnal' ;
	
	const AUTH_CALLBACK = 'yasnal_authCallback';
	const GETAUTH_CALLBACK = 'yasnal_getAuthCallback';
	const UNAUTH_CALLBACK = 'yasnal_unAuthCallback';

	/**
	 * Default value assigned at the end of file.
	 * TODO: manage this settings to permit its easy overloading.
	 * @var string
	 */
	public static $config = array();

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

	public static function sign($data)
	{
		if( is_array($data))
			$data = implode('',$data);
		return hash('SHA256', self::$config['auth_secret'] . $data) ;
	}
	public static function signCheck($sign, $data)
	{
		if( is_array($data))
			$data = implode('',$data);
		if( $sign == hash('SHA256', self::$config['auth_secret'] . $data) )
			return true ;
		return false ;
	}

	protected static function session_start()
	{
		if (!isset($_SESSION))
			session_start();
		if (isset($_SESSION))
			return true ;
		return false ;

		/* PHP >= 5.4.0
		 switch( session_status() ){
		case PHP_SESSION_DISABLED: break;
		case PHP_SESSION_NONE: break;
		case PHP_SESSION_ACTIVE: break;
		}*/
	}

	public static function loadCallback( $name, $file, $function )
	{
		$err = null ;
		if( empty($file) )
		{
			$err=$name.' callback file not defined';
		}
		else if( ! file_exists($file) )
		{
			$err=$name.' callback file not found';
		}
		require_once( $file );
		if( ! is_callable($function) )
		{
			$err= $name.' callback function not found';
		}
		if( $err != null )
			return $err ;
		return true ;
	}

	public static function isValidEMail($email)
	{
		// Minimum length the email can be
		if ( strlen( $email ) < 3 )
			return false ;
		// An @ character after the first position
		if ( strpos( $email, '@', 1 ) === false )
			return false ;
		// Split out the local and domain parts
		list( $local, $domain ) = explode( '@', $email, 2 );

		// LOCAL PART
		// Test for invalid characters
		if ( !preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) )
			return false ;

		// DOMAIN PART
		// leading and trailing periods and whitespace
		if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain )
			return false ;
		// Split the domain into subs
		$subs = explode( '.', $domain );
		// Assume the domain will have at least two subs
		if ( 2 > count( $subs ) )
			return false ;
		// Loop through each sub
		foreach ( $subs as $sub ) {
			// leading and trailing hyphens and whitespace
			if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub )
				return false ;
			// invalid characters
			if ( !preg_match('/^[a-z0-9-]+$/i', $sub ) )
				return false ;
		}
		return true ;
	}

}

AuthEngine::$config['auth_secret'] = '6"0[b&16=/91e6*44£bç14b%µ05§|@8a92d5' ;
/**
 * Default value assigned at the end of file.
 * TODO: manage this settings to permit its easy overloading.
 * @var string
 */
AuthEngine::$config['authCallbackPhpFile'] = __DIR__.'/defaultAuthCallback.php' ;
AuthEngine::$config['mailerCallbackPhpFile'] = __DIR__.'/email/defaultMailerCallback.php' ;
AuthEngine::$config['mailerFrom'] = 'From: Yasnal Auth Email <root@oueb.org>' ;
AuthEngine::$config['mailerSubject'] = 'Yasnal checking your authentification' ;
AuthEngine::$config['mailerBody'] = '<p>Here is your PIN code: {PINCODE}</p>' ;

