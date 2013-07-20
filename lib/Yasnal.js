/**
 * 
 */

/**
 * Yasnal singleton
 */
var Yasnal = {
    config: {
		lib_uri: '',
		email_callback: '/email/connect.php'
	},
	auth_signature: null,

    /**
     */
    authEmail: function (step) {

		switch(step){
		case undefined :
			jQuery('#auth-form').hide();
			jQuery('#auth-form-email').show();
			jQuery('#auth-form-email .auth-email-error').hide();
			break;
		case 'emailSendCancel':
			jQuery('#auth-form-email').hide();
			jQuery('#auth-form').show();
			break;
		case 'emailSend':
			var email = jQuery('input:text[name=auth-email]').val();
			if( ! this.isValidEmail(email) )
			{
				jQuery('#auth-form-email .auth-email-error').text('Cette adresse email semble invalide, vérifiez votre saisie');
				jQuery('#auth-form-email .auth-email-error').show();
				return ;
			}
			jQuery('#auth-form-email .auth-email-error').hide();
			var params = {};
			jQuery.post( lib_uri+email_callback, {action:'emailSend', email: email },
			function( jsonString ) {
				var res = JSON.parse(jsonString);
				if( res.status == 'mailSent' )
				{
					this.auth_signature = res.social_auth_signature ;
					jQuery('#auth-form-email').hide();
					jQuery('#auth-form-email-code').show();
				}
				else
				{
					if( res.status == 'error' ){
						alert( res.message );
					}else{
						alert('unknow result');
					}
				}
			});
			break;

		} // switch(step)

	}, // authEmail

	isValidEmail: function(email) {
		if( '' == email )
			return false ;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		if( !emailReg.test( email ) ) {
			return false;
		} else {
			return true;
		}
	}

};
