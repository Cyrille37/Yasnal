<?php

/**
 * @param string $provider
 * @param string $email
 */
function yasnal_authCallback($provider, $email)
{
	$cookie = base64_encode( $email . '#' . base64_encode(hash_hmac('MD5', $email, \Yasnal\AuthEngine::$config['auth_secret'],true))) ;
	setcookie(\Yasnal\AuthEngine::AUTH_COOKIE, $cookie, 0, '/');
}

/**
 * @return NULL|string
 */
function yasnal_getAuthCallback()
{
	if (!isset($_COOKIE[\Yasnal\AuthEngine::AUTH_COOKIE]))
		return null;
	
	$c = explode('#', base64_decode( $_COOKIE[\Yasnal\AuthEngine::AUTH_COOKIE]));
	if (!isset($c[1]) || empty($c[1]))
		return null;
	$h2 = hash_hmac('MD5', $c[0], \Yasnal\AuthEngine::$config['auth_secret'], true);
	if ($h2 != base64_decode($c[1]))
		return null ;
	return $c[0];
}

function yasnal_unAuthCallback()
{
	setcookie(\Yasnal\AuthEngine::AUTH_COOKIE, null, -1, '/');
}
