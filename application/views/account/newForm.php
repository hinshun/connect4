<div class="account-wrapper two-forms-wrapper">
	<div class="text-center">
		<h1>Create your Connect <span class="glyphicon glyphicon-th-large"></span> Account</h1>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="blurb text-center">
				<h2>One step away</h2>
				<p>
					Once you have registered, you can jump straight into a game!
				</p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-wrapper">
				<?php
				echo form_open('account/createNew');
				
				echo form_label('Name');
				echo '<br/><div class="half-form-wrapper">';
					echo form_error('first');
					echo form_input('first',set_value('first'),'required placeholder="First"');
				echo '</div>';
				echo '<div class="half-form-wrapper none">';
					echo form_error('last');
					echo form_input('last',set_value('last'),'required placeholder="Last"');
				echo '</div>';
				echo form_label('Choose your username'); 
				echo form_error('username');
				echo form_input('username',set_value('username'),'required id="username"');
				echo form_label('Create a password'); 
				echo form_error('password');
				echo form_password('password','','required id="pass1" oninput="updateStrength();"');
				echo form_label('Confirm your password'); 
				echo form_error('passconf');
				echo form_password('passconf','','required id="pass2" oninput="checkPassword();"');
				
				echo form_label('Your current email address');
				echo form_error('email');
				echo form_input('email',set_value('email'),'required id="email"');
				
				?>
				
				<div class="captcha-wrapper">
					<?= form_label("Prove you're not a robot") ?>
					<img id="captcha" src="<?= base_url() ?>securimage/securimage_show.php" alt="CAPTCHA Image" />			
				</div>
				<div class="input-group">
					<input class="form-control" type="text" name="captcha_code" size="10" maxlength="6" />
					<span class="input-group-btn">
						<button id="captcha-btn" type="button" class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span></button>
					</span>
			    </div>
				
				<?php
				echo form_submit('submit', 'Register');
				
				echo form_close();
				?>
				<div class="password-strength hidden">
					<strong>Password strength:</strong> <span id="strength-desc"></span>
					<div id="password-bar">
						<span id="strength-bar"></span>
					</div>
					Use at least 6 characters. Don't use a password from another site, 
					or something too obvious like your pet's name.
				</div>
			</div>
		</div>
	</div>
</div>