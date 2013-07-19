<!DOCTYPE html>
<html>
<?php
//define( 'YASNAL_PATH',  __DIR__.'/../lib' );
define( 'YASNAL_URI',  dirname($_SERVER['PHP_SELF']).'/../lib' );
?>
    <head>
        <meta charset="UTF-8" />
        <title>Yasnal demo</title>
		<meta name="viewport" content="width=device-width" />
		<meta name="author" content="Cyrille Giquello" />
		<meta name="description" content="Yasnal, Yet Another Social Network Authentification Library" />
		<meta name="keywords" content="expériences connectées" />

		<link rel="stylesheet" type="text/css" media="all" href="demo.css" />
		<script src="jquery.min.js" ></script>
		<script src="demo.js" ></script>
   </head>
    <body>
        <h1>Yasnal demo</h1>

		<div id="auth-form">
			<form title="Social Network Authentification">
				<div>
					<h2>Identification</h2>
					<p>Indentiez vous avec le service de votre choix:</p>
				</div>
				<ul class="services">
					<li><a href="javascript:void(0);" title="identifiez vous avec Google+" class="auth-google-button"><img alt="Google" src="<?php echo YASNAL_URI,'/img/google_32.png' ?>" /></a></li>
					<li><a href="javascript:void(0);" title="identifiez vous avec un email" class="auth-email-button"><img alt="eMail" src="<?php echo YASNAL_URI,'/img/email_32.jpg' ?>" /></a></li>
				</ul>
				<input type="hidden" class="auth-email-redirectUri" name="redirect_uri" value="<?php echo YASNAL_URI,'/email/connect.php' ?>" />
			</form>
		</div>
		<div id="auth-form-email">
			<form title="Email Authentification">
				<p>Merci d'indiquer votre adresse email à laquelle nous allons vous expédier un code pour vous identifier:</p>
				<p>
					Votre adresse email: <input type="text" name="email" size="30" />
					<input type="button" value="Envoyer le code" class="auth-email-send"  onclick="return auth_email('emailSend');" />
					<input type="button" value="Annuler" class="auth-email-sendCancel"  onclick="return auth_email('emailSendCancel');" />
				</p>
			</form>
		</div>
		<div id="auth-form-email-code">
			<form title="Social Authentification">
				<p>Indiquer le code que vous avez reçu à l'adresse email <span id="auth-email"></span></p>
				<p>
					Le code: <input type="text" name="emailCode" size="10" />
					<input type="button" value="Valider" onclick="return auth_email('emailCode');" />
					<input type="button" value="Annuler" onclick="return auth_email('emailCodeCancel');" />
				</p>
			</form>
		</div>

    </body>
</html>
