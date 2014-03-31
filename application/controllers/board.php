<?php

class Board extends CI_Controller {
     
    function __construct() {
		// Call the Controller constructor
    	parent::__construct();
    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
    	// enforce access control to protected functions	
		
		if (!isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
    	return call_user_func_array(array($this, $method), $params);
    }

    /*
     * Returns a view for the game
     */
    function index() {
		$user = $_SESSION['user'];
    		    	
    	$this->load->model('user_model');
    	$this->load->model('invite_model');
    	$this->load->model('match_model');
    	
    	$user = $this->user_model->get($user->login);
    	$match = $this->match_model->get($user->match_id);
    	
    	if ($user->user_status_id == User::PLAYING) {
    		if ($match->user1_id == $user->id) {
    			$otherUser = $this->user_model->getFromId($match->user2_id);
    		} else {
    			$otherUser = $this->user_model->getFromId($match->user1_id);
			}
    	} else {
    		redirect('arcade/index', 'refresh');
    		return;
    	}
    	
    	if ($match->user1_id == $user->id) {
    		$data['user1'] = $user->login;
			$data['user2'] = $otherUser->login;
    	} else {
    		$data['user1'] = $otherUser->login;
			$data['user2'] = $user->login;
    	}
    	
    	$data['user']=$user;
    	$data['otherUser']=$otherUser;
    	
    	$data['partial'] = 'match/board';
    	$data['js'] = array('view/board.js', 'view/chat.js', 'array_equals.js');
		$this->load->view('shared/layout',$data);
    }
    
    /*
     * Returns the board state for a GET request
     */
	function getBoard() {
		$user = $_SESSION['user'];
	
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 		
 		$user = $this->user_model->getExclusive($user->login);
 			
 		$match = $this->match_model->getExclusive($user->match_id);	
 		if (!$match)
 			goto error;
 				
 		$board_state = $match->board_state;
 		$match_status = $match->match_status_id;
 		
 		echo json_encode(array('status'=>'success','board_state'=>$board_state,'match_status'=>$match_status));
		return;
		
		error:
		echo json_encode(array('status'=>'failure','err'=>$errMsg));
 	}
    
 	/*
 	 * Returns post status of a board for a POST request
 	 */
    function postBoard() {
    	$user = $_SESSION['user'];
    
		$this->load->model('user_model');
		$this->load->model('match_model');
		$this->load->helper('board');
		
		// Start transactional mode
		$this->db->trans_begin();
		
		$user = $this->user_model->getExclusive($user->login);
		if ($user->user_status_id != User::PLAYING) {
			$errMsg = 'user';
			goto error;
		}
		
		// Initializes variables for validity checking
		$match = $this->match_model->getExclusive($user->match_id);
		$json = $this->input->post('board_state');
		$board_state = json_decode($json);
		$board = array_slice($board_state, 0, 42);
		$position = $board_state[44];
		$prevBoard = null;
		
		// Checks whether the current board state is null or not
		if (isset($match->board_state))
			$prevBoard = json_decode($match->board_state);
		
		// Checks whether the POSTed board is legal or not
		$validity = getBoardValidity($board, $prevBoard, $position);
		
		if ($validity == 'valid')
			$this->match_model->updateBoardState($match->id, $json);
		else { // Board is not legal
			$errMsg = $validity;
			goto error;
		}
		
		// Obtain and returns match status after board has been updated
		$match_status = getMatchStatus($board, $position);
		if ($match_status == 2 || $match_status == 3) {
			$this->match_model->updateStatus($match->id, $match_status);
			$this->user_model->updateStatus($match->user1_id, USER::AVAILABLE);
			$this->user_model->updateStatus($match->user2_id, USER::AVAILABLE);
			echo json_encode(array('status'=>'end','match_status'=>$match_status));
			return;
		} else if ($match_status == 0) {
			$empty_board = array_fill(0, 44, 0);
			$empty_board[42] = $board_state[42];
			$empty_board[43] = $board_state[43];
			$empty_board[44] = null;
			$json = json_encode($empty_board);
			$this->match_model->updateBoardState($match->id, $json);
			echo json_encode(array('status'=>'tie'));
			return;
		}
		
		if ($this->db->trans_status() === FALSE) {
			$errormsg = "Transaction error";
			goto transactionerror;
		}
			
		// If all went well commit changes
		$this->db->trans_commit();
		
		echo json_encode(array('status'=>'success'));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','errMsg'=>$errMsg));
    }
    
    /*
     * Returns the message of the opposing player for a GET request
     */
    function getMsg() {
    	$this->load->model('user_model');
    	$this->load->model('match_model');
    
    	$user = $_SESSION['user'];
    
    	$user = $this->user_model->get($user->login);
    		
    	// Start transactional mode
    	$this->db->trans_begin();
    
    	$match = $this->match_model->getExclusive($user->match_id);
    
    	if ($match->user1_id == $user->id) {
    		$msg = $match->u2_msg;
    		$this->match_model->updateMsgU2($match->id,"");
    	} else {
    		$msg = $match->u1_msg;
    		$this->match_model->updateMsgU1($match->id,"");
    	}
    
    	if ($this->db->trans_status() === FALSE) {
    		$errormsg = "Transaction error";
    		goto transactionerror;
    	}
    		
    	// If all went well commit changes
    	$this->db->trans_commit();
    		
    	echo json_encode(array('status'=>'success','message'=>$msg));
    	return;
    
    	transactionerror:
    	$this->db->trans_rollback();
    
    	error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
    
    /*
     * Returns the status of updating the players message for a POST request
     */
 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			
 			// Start transactional mode
 			$this->db->trans_begin();
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id) {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			} else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 			
 			if ($this->db->trans_status() === FALSE) {
 				$errormsg = "Transaction error";
 				goto transactionerror;
 			}
 				
 			// If all went well commit changes
 			$this->db->trans_commit();
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		} else {
			$errormsg = "Missing argument";
			goto error;
 		}
 		
 		transactionerror:
 			$this->db->trans_rollback();
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
}