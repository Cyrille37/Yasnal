<?php
/**
 *
 */

namespace Yasnal\Google ;

error_reporting(-1);

require_once( __DIR__.'/../Yasnal.php');
require_once( __DIR__.'/../openId/LightOpenID.php');

echo AuthGoogle::run();

/**
 *
 * @author cyrille
*/
class AuthGoogle {

	public static function run()
	{
		try {
			if (!isset($_GET['openid_mode']) || $_GET['openid_mode'] == 'cancel') {
				self::goGoogle();
			}
			else {
				self::backFromGoogle();
			}
		}
		catch (ErrorException $e) {
			echo $e->getMessage();
		}
	}

	public static function backFromGoogle()
	{
		$openid = new \LightOpenID();

		if (!$openid->validate()) {
			throw new \Exception('Failed to authenticate with a Google account');
		}
		//$google_id = $openid->identity;
		$attributes = $openid->getAttributes();
		$email = $attributes['contact/email'];
		//$first_name = isset($attributes['namePerson/first']) ? $attributes['namePerson/first'] : '';
		//$last_name = isset($attributes['namePerson/last']) ? $attributes['namePerson/last'] : '';

		$signature = \Yasnal\AuthEngine::sign($email);
		?>
<html>
<head>
<title>Back from Google</title>
<script>
						function backFromGoogle() {
							window.opener.Yasnal.auth_callback({
								'provider' : 'google', 
								'email' : '<?php echo $email ?>',
								'signature' : '<?php echo $signature ?>'
						  });
							window.close();
						}
					</script>
</head>
<body onload="backFromGoogle();">
</body>
</html>
<?php

	}

	public static function goGoogle()
	{
		$openid = new \LightOpenID();
		$openid->identity = 'https://www.google.com/accounts/o8/id';
		//$openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
		$openid->required = array('contact/email');
		header('Location: ' . $openid->authUrl());
	}

}
