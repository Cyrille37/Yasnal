jQuery(document).ready( function($) {

	var form ;

	// Hide email auth forms
	$('#auth-form-email').hide();
	$('#auth-form-email-code').hide();

	// Define authentification services icons onclick()
	form = $('#auth-form');
	$('.auth-email-button', form).click(function() {
		auth_email();
	});

	// Define email authentification service onclick()
	form = $('#auth-form-email');

	$('.auth-email-send', form).click(function() {
		auth_email('emailSend');
	});
	$('.auth-email-sendCancel', form).click(function() {
		auth_email('emailSendCancel');
	});

});

function auth_email(step) {

	var form = jQuery('#auth-form');

	switch(step){
	case undefined :
		jQuery('#auth-form').hide();
		jQuery('#auth-form-email').show();
		break;
	case 'emailSendCancel':
		jQuery('#auth-form-email').hide();
		jQuery('#auth-form').show();
		break;
	case 'emailSend':
		break;
	}

}
