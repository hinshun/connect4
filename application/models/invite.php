<?php
class Invite  {
	const PENDING = 1;
	const ACCEPTED = 2;
	const REJECTED = 3;
	const TIMEOUT = 4;
	
	public $id;
	
	public $user1_id;
	public $user2_id;
	
	public $invite_status_id = self::PENDING;
	
}
?>