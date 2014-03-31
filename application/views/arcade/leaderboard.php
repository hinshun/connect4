<table class="table">
	<th>
		Name
	</th>
	<th>
		Matches
	</th>
	<th>
		Wins
	</th>
<?php 
	if ($users) {
		foreach ($users as $user) {
?>		
			<tr>
				<td><?= $user->name ?></td>
				<td><?= $user->num_matches ?></td>
				<td><?= $user->num_won ?></td>
			</tr>
<?php 	
		}
	}
?>
</table>