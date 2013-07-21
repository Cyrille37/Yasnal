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
		if(isset($_GET['code'])) {
			
		}
		else{
			self::goFacebook();
		}
	}
	
	protected static function goFacebook()
	{
		$redirect_uri = urlencode(SimplePhotosContest::$plugin_url . 'auth/facebook/callback.php');
		wp_redirect('https://graph.facebook.com/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=email');
		
	}
}
