
jQuery(document).ready( function($) {

	var form ;

	// Hide email auth forms
	$('#auth-form-email').hide();
	$('#auth-form-email .auth-email-error').hide();
	$('#auth-form-email-code').hide();
	$('#auth-error').hide();

	// Define authentification services icons onclick()
	form = $('#auth-form');
	$('.auth-email-button', form).click(function() {
		Yasnal.authEmail();
		return false ;
	});

	// Define email authentification service onclick()
	form = $('#auth-form-email');

	$('.auth-email-send', form).click(function() {
		Yasnal.authEmail('emailSend');
	});
	$('.auth-email-cancel', form).click(function() {
		Yasnal.authEmail('emailSendCancel');
	});

});
