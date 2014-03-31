<div class="account-wrapper form-wrapper">
	<div class="back-wrapper">
		<a href="<?= base_url() ?>"><span class="glyphicon glyphicon-share-alt"></span> Back</a>
	</div>
	<h1>Change Password</h1>
	<?php 
		if (isset($errorMsg)) {
			echo "<p>" . $errorMsg . "</p>";
		}
	
		echo form_open('account/updatePassword');
		echo form_label('Current Password'); 
		echo form_error('oldPassword');
		echo form_password('oldPassword',set_value('oldPassword'),"required");
		echo form_label('New Password'); 
		echo form_error('newPassword');
		echo form_password('newPassword','',"id='pass1' required");
		echo form_label('Confirm new password'); 
		echo form_error('passconf');
		echo form_password('passconf','',"id='pass2' required oninput='checkPassword();'");
		echo form_submit('submit', 'Change Password');
		echo form_close();
	?>
</div>