<?php

class Arcade extends CI_Controller {
     
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
     * Returns a view for game lobby
     */
    function index() {
    	$data['user']=$_SESSION['user'];
    	if (isset($_SESSION['errmsg'])) {
    		$data['errmsg']=	$_SESSION['errmsg'];
    		unset($_SESSION['errmsg']);
    	}
    	$data['partial'] = 'arcade/mainPage';
    	$data['js'] = 'view/mainPage.js';
    	$this->load->view('shared/layout', $data);
    }

    /*
     * Returns a partial view for online users
     */
    function getOnlineUsers() {
 	   	$this->load->model('user_model');
		$users = $this->user_model->getOnlineUsers();
		$data['users'] = $users;
		$data['currentUser'] = $_SESSION['user'];
		$this->load->view('arcade/onlineUsers', $data);
    }
    
    /*
     * Returns a partial view for leaderboard
     */
    function getLeaderboard() {
 	   	$this->load->model('user_model');
		$users = $this->user_model->getLeaderboard();
		$data['users'] = $users;
		$data['currentUser'] = $_SESSION['user'];
		$this->load->view('arcade/leaderboard', $data);
    }
    
    /*
     * Returns invite data for a user for a GET request
     */
    function getInvitation() {
    	$user = $_SESSION['user'];
    		
    	$this->load->model('user_model');
    	$this->load->model('invite_model');
    	$user = $this->user_model->get($user->login);
    	$invite = $this->invite_model->get($user->invite_id);
    	
    	if ($invite->invite_status_id == Invite::REJECTED) {
    		// Start transactional mode
    		$this->db->trans_begin();
    		
    		// Update status
    		$this->user_model->updateStatus($user->id,User::AVAILABLE);
    			
    		if ($this->db->trans_status() === FALSE)
    			goto transactionerror;
    			
    		// If all went well commit changes
    		$this->db->trans_commit();
    			
    		$msg = array('invited'=>false);
    		echo json_encode($msg);
    			
    		return;
    	}
    	
    	// If the current user has been invited to match
    	if ($user->user_status_id == User::INVITED) {
    		$this->load->model('invite_model');
    		$invite = $this->invite_model->get($user->invite_id);
    		$hostUser = $this->user_model->getFromId($invite->user1_id);

    		$msg = array('invited'=>true,'login'=>$hostUser->login);
    		echo json_encode($msg);	
    	} else {
    		$msg = array('invited'=>false);
    		echo json_encode($msg);
    	}
    	
    	return;
    	
    	transactionerror:
    	$this->db->trans_rollback();
    	echo json_encode(array('invited'=>false));
    }
    
    /*
     * Accepts an invitation for a user for a POST request
     */
    function acceptInvitation() {
    	$user = $_SESSION['user'];
    	 
    	$this->load->model('user_model');
    	$this->load->model('invite_model');
    	$this->load->model('match_model');
    	
    	$user = $this->user_model->get($user->login);
	    	
	    $invite = $this->invite_model->get($user->invite_id);
	    $hostUser = $this->user_model->getFromId($invite->user1_id);

	    // Start transactional mode
	    $this->db->trans_begin();
	    
	    // Change status of invitation to ACCEPTED
	    $this->invite_model->updateStatus($invite->id,Invite::ACCEPTED);
	    
	    // Create a match entry
	    $match = new Match();
	    $match->user1_id = $user->id;
	    $match->user2_id = $hostUser->id;
	    $this->match_model->insert($match);
	    $matchId = mysql_insert_id();

	    // Update status of both users
	    $this->user_model->updateStatus($user->id,User::PLAYING);
	    $this->user_model->updateStatus($hostUser->id,User::PLAYING);
	    
	    $this->user_model->updateMatch($user->id,$matchId);
	    $this->user_model->updateMatch($hostUser->id,$matchId);
	     
	    
	    if ($this->db->trans_status() === FALSE)
    		goto transactionerror;
	    
	    // If all went well commit changes
	    $this->db->trans_commit();
	    
	    echo json_encode(array('status'=>'success'));
	    
	    return;
	    
	    transactionerror:
	    	$this->db->trans_rollback();
	    	echo json_encode(array('status'=>'failure'));
    }
    
    /*
     * Declines an inviation for a user for a POST request
     */
	function declineInvitation() {
		$user = $_SESSION['user'];
		 
		$this->load->model('user_model');
		$this->load->model('invite_model');
		
		$user = $this->user_model->get($user->login);
		$invite = $this->invite_model->get($user->invite_id);
		 
		// Start transactional mode
		$this->db->trans_begin();
		 
		// Change status of invitation to REJECTED
		$this->invite_model->updateStatus($invite->id,Invite::REJECTED);
		
		// Update status 
		$this->user_model->updateStatus($user->id,User::AVAILABLE);
		 
		if ($this->db->trans_status() === FALSE)
			goto transactionerror;
		 
		// If all went well commit changes
		$this->db->trans_commit();
		 
		echo json_encode(array('status'=>'success'));
		 
		return;
		
		transactionerror:
			$this->db->trans_rollback();
			echo json_encode(array('status'=>'failure'));
	}    
    
	/*
	 * Returns invitation status for a GET request
	 */
	function checkInvitation() {
		$user = $_SESSION['user'];
			
		$this->load->model('user_model');
		$this->load->model('invite_model');
	
		$user = $this->user_model->get($user->login);
	
		$invite = $this->invite_model->get($user->invite_id);

		switch($invite->invite_status_id) {
			case Invite::ACCEPTED:
				echo json_encode(array('status'=>'accepted'));
				break;
			case Invite::PENDING:
				echo json_encode(array('status'=>'pending'));
				break;
			case Invite::REJECTED:
				$this->user_model->updateStatus($user->id,User::AVAILABLE);
				echo json_encode(array('status'=>'rejected'));
		} 
	}
	
	/*
	 * Creates an invite for a POST request
	 */
    function invite() {
		try {
    		$login = $this->input->get('login');
			
			if (!isset($login)) 
				goto loginerror;
	
			$user1 = $_SESSION['user'];
			$user2 = null;
			
			$this->load->model('user_model');
			$this->load->model('invite_model');
			
			// Start transactional mode
			$this->db->trans_begin();	
	
	
			// Lock both user records in alphabetic order to prevent deadlocks	
			if (strcmp($user1->login, $login) < 0) {
				$user1 = $this->user_model->getExclusive($user1->login);
				$user2 = $this->user_model->getExclusive($login); 
			} else {
				$user2 = $this->user_model->getExclusive($login);
				$user1 = $this->user_model->getExclusive($user1->login);
			}
				
			if (!isset($user2) || $user2->user_status_id != User::AVAILABLE) 
				goto nouser2;
	
			// Update status of both users
			$this->user_model->updateStatus($user1->id,User::WAITING);
			$this->user_model->updateStatus($user2->id,User::INVITED);
			
			// Create an invite entry
			$invite = new Invite();
			$invite->user1_id = $user1->id;
			$invite->user2_id = $user2->id;
					   
			$this->invite_model->insert($invite);
	
			$inviteId = mysql_insert_id();
			
			$this->user_model->updateInvitation($user1->id,$inviteId);
			$this->user_model->updateInvitation($user2->id,$inviteId);
			
			
			if ($this->db->trans_status() === FALSE) 
				goto transactionerror;
			
			
			// If all went well commit changes
			$this->db->trans_commit();
			
			echo json_encode(array('status'=>'success'));
			
			return;
			
			transactionerror:
				nouser2:	
				$this->db->trans_rollback();
		
	    	loginerror:
				$errMsg = "Sorry, this user is no longer available.";
				echo json_encode(array('status'=>'failure','errMsg'=>$errMsg));
		} catch(Exception $e) {
			$this->db->trans_rollback();
			echo json_encode(array('status'=>'failure'));
		}
    }
 
 }

