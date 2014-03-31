<div class="account-wrapper form-wrapper">
	<h1>Sign in</h1>
	<?php 
		if (isset($errorMsg)) {
			echo "<p>" . $errorMsg . "</p>";
		}
	
		echo form_open('account/login');
		echo form_label('Username'); 
		echo form_error('username');
		echo form_input('username',set_value('username'),"required");
		echo form_label('Password'); 
		echo form_error('password');
		echo form_password('password','',"required");
		
		echo form_submit('submit', 'Login');
		
		echo "<p>" . anchor('account/newForm','Register') . "</p>";
	
		echo "<p>" . anchor('account/recoverPasswordForm','Forgot your password?') . "</p>";
		
		
		echo form_close();
	?>
</div>