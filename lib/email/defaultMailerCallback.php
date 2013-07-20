<?php

/**
 *
 * @param string $email
 * @param string $code
 * @return boolean
 */
function yasnal_mailerCallback($email, $pincode)
{
	$headers= array();
	$headers[] = \Yasnal\AuthEngine::$config['mailerFrom'];
	$headers[] = 'Content-Type: text/html';
	$subject = \Yasnal\AuthEngine::$config['mailerSubject'] ;
	$body = str_replace( '{PINCODE}', $pincode, \Yasnal\AuthEngine::$config['mailerBody']);

	$res = mail($email, $subject, $body, implode( "\r\n", $headers) );

	if( $res === true )
	{
		return array('status'=>'ok', 'message'=>'email sent');
	}
	return array('status'=>'error', 'message'=>'Failed to send email');
}
