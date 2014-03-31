<div class="container">
	<div class="content-wrapper">
		<div class="col-xs-7 board-wrapper">
			<div class="row players">
				<div class="col-xs-6 text-center">
					<div id="board-red-player"></div>
					<img src="<?= base_url() ?>css/images/reddisc.png"/>
				</div>
				<div class="col-xs-6 text-center">
					<div id="board-yellow-player"></div>
					<img src="<?= base_url() ?>css/images/yellowdisc.png"/>
				</div>
				<img id="versus" src="<?= base_url() ?>css/images/vs.png"/>
			</div>
			<div class="board-gui-wrapper" id="board-text-wrapper">
				<div class="board-text" id="board-text-userTurn">Your Turn</div>
				<div class="board-text" id="board-text-otherUserTurn">Opponent's Turn</div>
				<div class="board-text" id="board-text-win">You Win</div>
				<div class="board-text" id="board-text-lose">You Lose</div>
			</div>
			<div class="board-gui-wrapper">
				<?php 
					for ($i = 0; $i < 7; $i++) {
						echo '<div class="board-column" id="col-' . $i . '"></div>';
					}
				?>
			</div>
			<div class="board-gui-wrapper">
				<?php 
					for ($i = 0; $i < 7; $i++) {
						echo '<img class="board-arrow" id="arrow-' . $i . '" src="' . base_url() . 'css/images/selection.png" />';
					}
				?>
			</div>
			<div class="board-gui-wrapper" id="disc-wrapper">
			</div>
			<div class="board-blocker-wrapper"><div class="board-blocker"></div></div>
			<img id="board-back" src="<?= base_url() ?>css/images/board-back.png"/>
			<img id="board-front" src="<?= base_url() ?>css/images/board-front.png"/>
		</div>
		<div class="col-xs-5">
			<div class="chat-label">
				<h4><span class="glyphicon glyphicon-comment"></span> CHAT</h4>
			</div>
			<ul class="chat"></ul>
			<?= form_open(); ?>
			<div class="input-group chat-input">
				<?= form_input('msg'); ?>
	      		<span class="input-group-btn">
	        		<button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-chevron-right"></span></button>
	      		</span>
	    	</div>
	    	<?= form_close(); ?>
		</div>
	</div>
</div>

<input id="user" type="hidden" value="<?= $user->login ?>"/>
<input id="userFirst" type="hidden" value="<?= $user->first ?>"/>
<input id="otherUser" type="hidden" value="<?= $otherUser->login ?>"/>
<input id="otherUserFirst" type="hidden" value="<?= $otherUser->first ?>"/>
<input id="user1" type="hidden" value="<?= $user1 ?>"/>
<input id="user2" type="hidden" value="<?= $user2 ?>"/>