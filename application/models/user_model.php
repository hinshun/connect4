<?php
class User_model extends CI_Model {
	
	/*
	 * Returns a user model for a given username
	 */
	function get($username)
	{
		$this->db->where('login',$username);
		$query = $this->db->get('user');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	/*
	 * Returns a user model for a given id
	 */
	function getFromId($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('user');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	/*
	 * Returns a user model for a given email
	 */
	function getFromEmail($email)
	{
		$this->db->where('email',$email);
		$query = $this->db->get('user');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	/*
	 * Inserts a user into the database
	 */
	function insert($user) {
		return $this->db->insert('user',$user);
	}
	
	/*
	 * Sets all users as offline
	 */
	function reset() {
		return $this->db->update('user',array('user_status_id'=>User::OFFLINE));
	}
	
	/*
	 * Updates the password of a user
	 */
	function updatePassword($user) {
		$this->db->where('id',$user->id);
		return $this->db->update('user',array('password'=>$user->password,
				                                'salt' => $user->salt));
	}
	
	/*
	 * Update the status of a user
	 */
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('user',array('user_status_id'=>$status));
	}
	
	/*
	 * Updates the inviation of a user with given id and invitation id
	 */
	function updateInvitation($id, $invitationId) {
		$this->db->where('id',$id);
		return $this->db->update('user',array('invite_id'=>$invitationId));
	}
	
	/*
	 * Updates the match of a user with a given id and match id
	 */
	function updateMatch($id, $matchId) {
		$this->db->where('id',$id);
		return $this->db->update('user',array('match_id'=>$matchId));
	}
	
	/*
	 * Returns the users that are online with their status code
	 */
	function getOnlineUsers() {
		$sql = "SELECT user.login, CONCAT(user.first, ' ', user.last) AS name,
				CONCAT(UPPER(LEFT(user_status.name, 1)), RIGHT(user_status.name, LENGTH(user_status.name) - 1)) AS status
				FROM user JOIN user_status ON user.user_status_id=user_status.id
				WHERE user_status.name != 'offline'
				LOCK IN SHARE MODE";
		$query = $this->db->query($sql);
		if ($query && $query->num_rows() > 0)
			return $query->result();
		else
			return null;
	}
	
	/*
	 * Returns a user model for a given username and places an exclusive lock
	 */
	function getExclusive($username)
	{
		$sql = "select * from user where login=? for update";
		$query = $this->db->query($sql,array($username));
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'User');
		else
			return null;
	}
	
	/*
	 * Returns an array of user models sorted by their win/loss ratio. Only
	 * users with more than one match are shown on the leaderboard
	 */
	function getLeaderboard() {
		$sql = "SELECT name, COUNT(*) AS num_matches,
				COUNT(CASE WHEN (match_result='won') THEN 1 ELSE NULL END) AS num_won,
				(COUNT(CASE WHEN (match_result='won') THEN 1 ELSE NULL END) / COUNT(*)) AS ratio
				FROM (SELECT user.login AS login, CONCAT(user.first, ' ', user.last) AS name,
				(CASE WHEN (m1.match_status_id=2) THEN 'won' ELSE 'lost' END) AS match_result
				FROM user JOIN `match` AS m1 ON user.id=m1.user1_id
				WHERE m1.match_status_id != 1
				UNION ALL
				SELECT user.login AS login, CONCAT(user.first, ' ', user.last) AS name,
				(CASE WHEN (m2.match_status_id=3) THEN 'won' ELSE 'lost' END) AS match_result
				FROM user JOIN `match` AS m2 ON user.id=m2.user2_id
				WHERE m2.match_status_id != 1) AS R
				GROUP BY login
				HAVING num_matches > 1
				ORDER BY ratio DESC
				LOCK IN SHARE MODE";
		$query = $this->db->query($sql);
		if ($query && $query->num_rows() > 0)
			return $query->result();
		else
			return null;
	}
}
?>
