<div class="account-wrapper form-wrapper">
	<h1>Password Recovery</h1>
	
	<p>Please check your email for your new password.
	</p>
		
	<?php 
		if (isset($errorMsg)) {
			echo "<p>" . $errorMsg . "</p>";
		}
	
		echo "<p>" . anchor('account/index','Back to login') . "</p>";
	?>
</div>