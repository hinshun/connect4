<table class="table">
	<th>
		Name
	</th>
	<th>
		Status
	</th>
	<th></th>
<?php 
	if ($users) {
		foreach ($users as $user) {
?>		
			<tr>
				<td><?= $user->name ?></td>
				<td><?= $user->status ?></td>
				<td>
					<?php if($user->login != $currentUser->login) { ?>
					<button id="<?= $user->login ?>" type="button" class="btn btn-success" <?php if($user->status != 'Available') echo 'disabled="disabled"'; ?>>
						<span class="glyphicon glyphicon-ok-sign"></span> Challenge
					</button>
					<?php } ?>
				</td>
			</tr>
<?php 	
		}
	}
?>
</table>
<input id="num_users" type="hidden" value="<?= count($users) ?>"/>