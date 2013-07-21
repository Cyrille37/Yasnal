/**
 * All this code is for the demo, to show the use of Yasnal API.
 */
// Configuration

jQuery(document).ready(function($) {

    // Connect social netword buttons

    Yasnal.initForms($);
    update_auth();
});

/**
 * Fired when authentification successed
 */
function auth_success() {
    alert('Yep, you are authenticated');
    update_auth();
}

/**
 * Show how to retreive the current authentification
 */
function update_auth() {
    jQuery.post(Yasnal.config.lib_uri + Yasnal.config.auth_url, {
	action : 'getAuth'
    }, function(jsonString) {
	var res = JSON.parse(jsonString);
	if (res.status == 'ok') {
	    jQuery('#demo-auth').text(res.auth);
	} else {
	    if (res.status == 'error') {
		Yasnal.displayError(res.message);
	    } else {
		var s = '';
		for (prop in res) {
		    s += prop + '=' + res[prop] + "\n";
		}
		alert('unknow result. Got: ' + "\n" + s);
	    }
	}
    });
}

/**
 * Show how to forget the authentification
 */
function logout() {
    jQuery.post(Yasnal.config.lib_uri + Yasnal.config.auth_url, {
	action : 'unAuth'
    }, function(jsonString) {
	var res = JSON.parse(jsonString);
	if (res.status == 'ok') {
	    jQuery('#demo-auth').text(null);
	} else {
	    if (res.status == 'error') {
		Yasnal.displayError(res.message);
	    } else {
		var s = '';
		for (prop in res) {
		    s += prop + '=' + res[prop] + "\n";
		}
		alert('unknow result. Got: ' + "\n" + s);
	    }
	}
    });
    update_auth();
}
