<div class="account-wrapper form-wrapper">
	<div class="back-wrapper">
		<a href="<?= base_url() ?>"><span class="glyphicon glyphicon-share-alt"></span> Back</a>
	</div>
	<h1>Forgotten password</h1>
	<?php 
		if (isset($errorMsg)) {
			echo "<p>" . $errorMsg . "</p>";
		}
	
		echo form_open('account/recoverPassword');
		echo form_label('Email address'); 
		echo form_error('email');
		echo form_input('email',set_value('email'),"required");
		echo form_submit('submit', 'Recover Password');
		echo form_close();
	?>
</div>