/*********************************
** updatePasswordForm FUNCTIONS **
**********************************/
/*
 * Checks whether the password and confirmed password are equivalent
 */
function checkPassword() {
	var p1 = $("#pass1"); 
	var p2 = $("#pass2");
	
	if (p1.val() == p2.val()) {
		p1.get(0).setCustomValidity("");  // All is well, clear error message
		return true;
	}	
	else	 {
		p1.get(0).setCustomValidity("Passwords do not match");
		return false;
	}
}