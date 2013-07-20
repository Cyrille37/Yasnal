/**
 * 
 */

/**
 * Yasnal singleton
 */
var Yasnal = {
    config : {
	lib_uri : '',
	email_connect_url : '/email/connect.php'
    },
    auth_signature : null,

    /**
     */
    authEmail : function(step) {

	switch (step) {

	case undefined:
	    jQuery('#auth-form').hide();
	    jQuery('#auth-form-email').show();
	    break;

	case 'emailSendCancel':
	    jQuery('#auth-error').hide();
	    jQuery('#auth-form-email').hide();
	    jQuery('#auth-form').show();
	    break;

	case 'emailSend':
	    var email = jQuery('#auth-form-email input:text[name=auth-email]')
		    .val();
	    var csrf = jQuery('#auth-form input:hidden[name=auth-csrf]').val();
	    if (!this.isValidEmail(email)) {
		this
			.displayError('Cette adresse email semble invalide, vérifiez votre saisie');
		return;
	    }
	    var yasnal = this;
	    jQuery('#auth-error').hide();
	    jQuery.post(this.config.lib_uri + this.config.email_connect_url, {
		action : 'emailSend',
		email : email,
		csrf : csrf
	    }, function(jsonString) {
		var res = JSON.parse(jsonString);
		if (res.status == 'ok') {
		    yasnal.auth_signature = res.social_auth_signature;
		    jQuery('#auth-form-email').hide();
		    jQuery('#auth-form-email-code').show();
		} else {
		    if (res.status == 'error') {
			yasnal.displayError(res.message);
		    } else {
			var s = '';
			for (prop in res) {
			    s += prop + '=' + res[prop] + "\n";
			}
			alert('unknow result. Got: ' + "\n" + s);
		    }
		}
	    });
	    break;

	case 'emailCodeCancel':
	    jQuery('#auth-form-email-code').hide();
	    jQuery('#auth-form-email-code input:text[name=auth-emailCode]')
		    .val('');
	    jQuery('#auth-form-email').show();
	    break;

	case 'codeConfirm':
	    window.auth_callback({
		'social_auth_provider' : 'mail',
		'social_auth_email' : jQuery('input:text[name=email]', root)
			.val(),
		'social_auth_signature' : jQuery('input.spc-auth-email-sign',
			root).val(),
		'social_auth_access_token' : jQuery(
			'input:text[name=emailCode]', root).val()
	    });
	    break;

	} // switch(step)

    }, // authEmail

    displayError : function(message) {
	var d = jQuery('#auth-error');
	jQuery('.message', d).text(message);
	d.show();
    },

    isValidEmail : function(email) {
	if ('' == email)
	    return false;
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if (!emailReg.test(email)) {
	    return false;
	} else {
	    return true;
	}
    }

};
