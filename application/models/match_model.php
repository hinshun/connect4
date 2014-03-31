<?php
class Match_model extends CI_Model {
	
	/*
	 * Returns the match model with a given id and places an exclusive lock
	 */
	function getExclusive($id)
	{
		$sql = "select * from `match` where id=? for update";
		$query = $this->db->query($sql,array($id));
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Match');
		else
			return null;
	}

	/*
	 * Returns the match model with a given id
	 */
	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('match');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Match');
		else
			return null;
	}
	
	/*
	 * Insert a match into the database
	 */
	function insert($match) {
		return $this->db->insert('match',$match);
	}
	
	/*
	 * Updates the message for user 1
	 */
	function updateMsgU1($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('u1_msg'=>$msg));
	}
	
	/*
	 * Updates the message for user 2
	 */
	function updateMsgU2($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('u2_msg'=>$msg));
	}
	
	/*
	 * Updates the status of the match
	 */
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('match',array('match_status_id'=>$status));
	}
	
	/*
	 * Updates the board state of the match
	 */
	function updateBoardState($id, $board_state) {
		$this->db->where('id', $id);
		return $this->db->update('match', array('board_state'=>$board_state));		
	}
}
?>