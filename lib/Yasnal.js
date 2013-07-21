/**
 * 
 */

/**
 * Yasnal singleton
 */
var Yasnal = {

  config : {
    lib_uri : null,
    auth_success_callback : null,
    email_connect_url : '/email/connect.php',
    google_connect_url : '/google/connect.php',
    facebook_connect_url : '/facebook/connect.php',
    wlogin_w : 800,
    wlogin_h : 640,
    auth_url : '/auth.php'
  },
  tmp_auth_signature : null,

  /**
	 * 
	 */
  initForms : function( $)
  {
	  var form;
	  var cfg = Yasnal.config;
	  var winDef = 'scrollbars=yes,menubar=no,height=' + Yasnal.config.wlogin_h + ',width=' + Yasnal.config.wlogin_w
	      + ',resizable=yes,toolbar=no,status=no';

	  // Define authentification services icons onclick()
	  form = $('#auth-form');

	  $('.auth-google-button', form).click(function()
	  {
		  var redirect_uri = cfg.lib_uri + cfg.google_connect_url;
		  window.open(redirect_uri, 'auth', winDef);
		  return false;
	  });
	  $('.auth-facebook-button', form).click(function()
	  {
		  var redirect_uri = cfg.lib_uri + cfg.facebook_connect_url;
		  window.open(redirect_uri, 'auth', winDef);
		  return false;
	  });
	  $('.auth-email-button', form).click(function()
	  {
		  Yasnal.authEmail();
		  return false;
	  });

	  $('#auth-error').hide();

	  // Email specific stuff

	  // Hide email auth forms
	  $('#auth-form-email').hide();
	  $('#auth-form-email .auth-email-error').hide();
	  $('#auth-form-email-code').hide();

	  // Define email authentification buttons onclick()
	  form = $('#auth-form-email');
	  $('.auth-email-send', form).click(function()
	  {
		  Yasnal.authEmail('emailSend');
	  });
	  $('.auth-email-cancel', form).click(function()
	  {
		  Yasnal.authEmail('sendCancel');
	  });
	  form = $('#auth-form-email-code');
	  $('.auth-emailCode-confirm', form).click(function()
	  {
		  Yasnal.authEmail('codeConfirm');
	  });
	  $('.auth-emailCode-cancel', form).click(function()
	  {
		  Yasnal.authEmail('codeCancel');
	  });

  },

  /**
   */
  authEmail : function( step)
  {

	  switch( step )
	  {

	  case undefined:
		  jQuery('#auth-form').hide();
		  jQuery('#auth-form-email').show();
		  break;

	  case 'sendCancel':
		  jQuery('#auth-error').hide();
		  jQuery('#auth-form-email').hide();
		  jQuery('#auth-form').show();
		  break;

	  case 'emailSend':
		  var email = jQuery('#auth-form-email input:text[name=auth-email]').val();
		  var csrf = jQuery('#auth-form input:hidden[name=auth-csrf]').val();
		  if( !this.isValidEmail(email) )
		  {
			  Yasnal.displayError('Cette adresse email semble invalide, vérifiez votre saisie');
			  return;
		  }
		  jQuery('#auth-error').hide();
		  jQuery.post(this.config.lib_uri + this.config.email_connect_url, {
		    action : 'emailSend',
		    email : email,
		    csrf : csrf
		  }, function( jsonString)
		  {
			  var res = JSON.parse(jsonString);
			  if( res.status == 'ok' )
			  {
				  Yasnal.tmp_auth_signature = res.signature;
				  jQuery('#auth-form-email').hide();
				  jQuery('#auth-form-email-code').show();
			  } else
			  {
				  if( res.status == 'error' )
				  {
					  Yasnal.displayError(res.message);
				  } else
				  {
					  var s = '';
					  for( prop in res )
					  {
						  s += prop + '=' + res[prop] + "\n";
					  }
					  alert('unknow result. Got: ' + "\n" + s);
				  }
			  }
		  });
		  break;

	  case 'codeCancel':
		  jQuery('#auth-form-email-code').hide();
		  jQuery('#auth-form-email-code input:text[name=auth-emailCode]').val('');
		  jQuery('#auth-form-email').show();
		  break;

	  case 'codeConfirm':
		  var email = jQuery('#auth-form-email input:text[name=auth-email]').val();
		  var pincode = jQuery('#auth-form-email-code input:text[name=auth-emailCode]').val();
		  this.auth_callback({
		    'provider' : 'email',
		    'email' : email,
		    'signature' : Yasnal.tmp_auth_signature,
		    'token' : pincode
		  });
		  break;

	  } // switch(step)

  }, // authEmail

  displayError : function( message)
  {
	  var d = jQuery('#auth-error');
	  jQuery('.message', d).text(message);
	  d.show();
  },

  isValidEmail : function( email)
  {
	  if( '' == email )
		  return false;
	  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	  if( !emailReg.test(email) )
	  {
		  return false;
	  } else
	  {
		  return true;
	  }
  },

  /**
	 * 
	 * @param auth_data
	 */
  auth_callback : function( auth_data)
  {

	  var params = {};
	  jQuery.each(auth_data, function( key, value)
	  {
		  params[key] = value;
	  });
	  params['action'] = 'auth';

	  var cfg = Yasnal.config;
	  jQuery.post(cfg.lib_uri + cfg.auth_url, params, function( jsonString)
	  {
		  var res = JSON.parse(jsonString);
		  if( res.status == 'ok' )
		  {
		  	var cb = cfg.auth_success_callback ;
			  if( cb && typeof (cb) === "function" )
			  {
			  	cb();
			  } else
			  {
				  Yasnal.displayError('Config error, auth_success_callback not defined');
			  }
		  } else
		  {
			  if( res.status == 'error' )
			  {
				  Yasnal.displayError(res.message);
			  } else
			  {
				  var s = '';
				  for( prop in res )
				  {
					  s += prop + '=' + res[prop] + "\n";
				  }
				  alert('unknow result. Got: ' + "\n" + s);
			  }
		  }
	  });

  }

};
