/**********************
** newForm FUNCTIONS **
***********************/
/*
 * Updates the password strength meter with the current password inputted
 */
function updateStrength() {
	var pass = $('#pass1').val();
	
	if (pass.length == 0) {
		removeStrength();
		return;
	} else if (pass.length < 6) {
		setStrength('brittle');
		return;
	}
	
	var num_match = pass.match(/\d+/g);
	var ul_match = pass.match(/[a-z].*[A-Z]|[A-Z].*[a-z]/);
	var strength_count = 0;
	if (num_match != null) {
		// password contains a digit
		strength_count++;
	}
	if (ul_match != null) {
		// password contains at least one upper and lower case letter
		strength_count++;
	}
	
	switch (strength_count) {
		case 0: setStrength('weak');
			break;
		case 1: setStrength('fair');
			break;
		case 2: setStrength('strong');
			break;
	}
}

/*
 * Sets the password strength meter to a specified strength level
 */
function setStrength(strength) {
	$('#strength-bar').removeClass();
	$('#strength-bar').addClass(strength);
	
	var desc = $('#strength-desc');
	
	switch (strength) {
		case 'brittle': desc.html('Too short');
			break;
		case 'weak': desc.html('Weak');
			break;
		case 'fair': desc.html('Fair');
			break;
		case 'strong': desc.html('Strong');
			break;
	}
	
	updatePopover();
}

/*
 * Clears the password strength meter
 */
function removeStrength() {
	$('#strength-bar').removeClass();
	$('#strength-desc').html('');
	
	updatePopover();
}

/*
 * Show the password strength popover
 */
function updatePopover() {
	$('#pass1').popover('show');
}

$(function () {
	var base_url = $('#base_url').val();

	// Renews captcha on click
	$('#captcha-btn').on('click', function () {
		$('#captcha').attr('src', base_url + 'securimage/securimage_show.php?' + Math.random());
	});

	// Sets bootstrap popover handler for username
	$('#username').popover({
		content:'You can use letters, numbers, and underscores.', 
		container:'body',
		trigger:'focus',
		animation: false
	});
	
	// Sets bootstrap popover for password strength
	$('#pass1').popover({
		html: true,
		content: function() {
			return $('.password-strength').html();
	    }, 
		container: 'body',
		trigger: 'focus',
		animation: false
	});
	
	// Sets bootstrap popover handler for email
	$('#email').popover({
		content:'We will use this address for things like keeping your ' +
			'account secure, helping people find you, and sending notifications',
		container:'body',
		trigger:'focus',
		animation: false
	});
});