<?php
/**
 *
 */

namespace Yasnal\Facebook ;

error_reporting(-1);

require_once( __DIR__.'/../Yasnal.php');

echo AuthFacebook::run();

/**
 *
 * @author cyrille
*/
class AuthFacebook {

	public static function run()
	{
		error_log( 'PHP_SELF: '.$_SERVER['PHP_SELF'] );
		error_log( 'REQUEST_URI: '.$_SERVER['REQUEST_URI'] );

		if( isset($_GET['code']) || isset($_GET['error']) )
		{
			self::backFromFacebook();
		}
		else
		{
			self::goFacebook();
		}
	}

	protected static function backFromFacebook()
	{
		if( isset($_GET['error']) )
		{
			//error_reason=user_denied&error=access_denied&error_description=The+user+denied+your+request.
			?>
<html>
<head>
<title>Back from Facebook</title>
<script>
		window.close();
</script>
</head>
<body>
	<p>
		<?php echo urldecode($_GET['error_description']) ?>
	</p>
</body>
</html>
<?php
exit();
		}

		$redirect_uri = urlencode(\Yasnal\AuthEngine::getCurrentUrl(false).$_SERVER['REQUEST_URI'].'/facebook/connect.php');
		$client_id = \Yasnal\AuthEngine::$config['facebook_clientid'] ;
		$secret_key = \Yasnal\AuthEngine::$config['facebook_secretkey'] ;
		$url = 'https://graph.facebook.com/oauth/access_token?'
			.	'client_id=' . $client_id
			. '&redirect_uri=' . $redirect_uri
			.	'&client_secret=' .  $secret_key
			.	'&code=' . urlencode($_GET['code']) ;

		$res = file_get_contents( $url );
		error_log( 'RES: '.var_export( $res,true ));

		$res = explode( '=', $res );
		if( count($res)<2 || $res[0] != 'access_token')
		{
			echo 'Authentification error' ;
			exit();
		}
		$access_token = $res[1];
		
		$fb_json = json_decode( file_get_contents('https://graph.facebook.com/me?access_token=' . $access_token ));
		$email = $fb_json->{'email'};
		//['first_name'] = $fb_json->{'first_name'};
		//['last_name'] = $fb_json->{'last_name'};

		$signature = \Yasnal\AuthEngine::sign($email);
		?>
<html>
<head>
<title>Back from Google</title>
<script>
			function backFromGoogle() {
				window.opener.Yasnal.auth_callback({
					provider : 'facebook', 
					email : '<?php echo $email ?>',
					signature: '<?php echo $signature ?>'
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

	protected static function goFacebook()
	{
		$redirect_uri = urlencode(\Yasnal\AuthEngine::getCurrentUrl(false).$_SERVER['REQUEST_URI'].'/facebook/connect.php');
		$client_id = \Yasnal\AuthEngine::$config['facebook_clientid'] ;
		$url = 'https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email&response_type=code' ;
		header('Location: ' . $url );

	}
}
