<?php
class Invite_model extends CI_Model {
	
	/*
	 * Returns an invite model for given id
	 */
	function get($id) {
		$this->db->where('id',$id);
		$query = $this->db->get('invite');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Invite');
		else
			return null;
	}

	/*
	 * Returns an invite model where userId is the id of user2 of an invite
	 */
	function getUser1($userId) {
		$this->db->where('user1_id',$userId);
		$query = $this->db->get('invite');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Invite');
		else
			return null;
	}
	
	/*
	 * Returns an invite model where userId is the id of user2 of an invite
	 */
	function getUser2($userId) {
		$this->db->where('user2_id',$userId);
		$query = $this->db->get('invite');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Invite');
		else
			return null;
	}
	
	/*
	 * Inserts an invite into the database
	 */
	function insert($invite) {
		return $this->db->insert('invite',$invite);
	}
	
	/*
	 * Updates the status of an invite
	 */
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('invite',array('invite_status_id'=>$status));
	}
}
?>