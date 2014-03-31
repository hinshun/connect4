<div class="content-wrapper">
	<div class="user-profile">
		<div class="dropdown btn-group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
		    	<?= $user->fullName() ?> <span class="glyphicon glyphicon-user"></span> <span class="caret"></span>
	  		</button>
		  	<ul class="dropdown-menu">
		    	<li><?= anchor('account/logout','Logout') ?></li>
		    	<li><?= anchor('account/updatePasswordForm','Change Password') ?></li>
		  	</ul>
		</div>
	</div>
	
	<div>
		<h1>Welcome back!</h1>
		<p>Jump right into a game of Connect <span class="glyphicon glyphicon-th-large"></span> by challenging an available player below.</p>
	</div>
	
	<div class="tables-wrapper container">
		<div class="col-md-6">
			<?php 
				if (isset($errmsg)) 
					echo "<p>$errmsg</p>";
			?>
			<h2>Online Users <small><span id="num_users_label" class="label label-default"></span></small></h2>
			<div id="onlineUsers">
			</div>
		</div>
		<div class="col-md-6">
			<h2>Leaderboards</h2>
			<div id="leaderboard">
			</div>
		</div>
	</div>
		
	<div class="invitation">
		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">Game Invitation</h3>
		  </div>
		  <div class="panel-body">
		    <p>You have been challenged by <span id="challenger"></span></p>
		    
		    <div class="invitation-btn-wrapper">
			    <button id="invitation-accept" type="button" class="btn btn-primary">
					<span class="glyphicon glyphicon-thumbs-up"></span> Accept
				</button>
				<button id="invitation-decline" type="button" class="btn btn-danger">
					<span class="glyphicon glyphicon-thumbs-down"></span> Decline
				</button>
			</div>
		  </div>
		</div>
	</div>
	
	<div class="waiting">
		<div class="panel panel-warning">
		  <div class="panel-heading">
		    <h3 class="panel-title">Waiting for Player Response</h3>
		  </div>
		  <div class="panel-body">
		  	<img id="loading" src="<?= base_url() ?>css/images/loading.gif"/>
		  	<button id="waiting-cancel" type="button" class="btn btn-danger">
				<span class="glyphicon glyphicon-remove-circle"></span> Cancel
			</button>
		  </div>
		</div>
	</div>
	
	<div class="screen-blocker"></div>
</div>