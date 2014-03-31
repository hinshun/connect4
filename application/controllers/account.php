<?php

class Account extends CI_Controller {
     
    function __construct() {
		// Call the Controller constructor
    	parent::__construct();
    	session_start();
    }
        
    public function _remap($method, $params = array()) {
    	// enforce access control to protected functions	

		$protected = array('updatePasswordForm','updatePassword','index','logout');
		
		if (in_array($method,$protected) && !isset($_SESSION['user']))
			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
    	
    	return call_user_func_array(array($this, $method), $params);
    }
          
    /*
     * Returns a view for the login page
     */
    function loginForm() {
    	$this->load->model('user_model');
    	$data['partial'] = 'account/loginForm';
		$this->load->view('shared/layout', $data);
    }
    
    /*
     * Logins a user for a POST request
     */
    function login() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		$data['partial'] = 'account/loginForm';

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('shared/layout', $data);
		} else {
			$login = $this->input->post('username');
			$clearPassword = $this->input->post('password');
			 
			$this->load->model('user_model');
		
			$user = $this->user_model->get($login);
			 
			if (isset($user) && $user->comparePassword($clearPassword)) {
				$_SESSION['user'] = $user;
				$data['user']=$user;
				
				$this->user_model->updateStatus($user->id, User::AVAILABLE);
				
				redirect('arcade/index', 'refresh'); //redirect to the main application page
			} else {   			
				$data['errorMsg']='Incorrect username or password!';
				
				$this->load->view('shared/layout', $data);
			}
		}
    }

    /*
     * Logs out the user in session
     */
    function logout() {
		$user = $_SESSION['user'];
		$this->load->model('user_model');
		$this->user_model->updateStatus($user->id, User::OFFLINE);
		session_destroy();
		redirect('account/index', 'refresh'); //Then we redirect to the index page again
    }

    /*
     * Returns a view for registration
     */
    function newForm() {
    	$data['partial'] = 'account/newForm';
    	$data['js'] = array('view/newForm.js', 'view/updatePasswordForm.js');
    	$this->load->view('shared/layout', $data);
    }
    
    /*
     * Attempts to create a user from a POST request
     */
    function createNew() {    
		$this->load->library('form_validation');
	    $this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.login]');
    	$this->form_validation->set_rules('password', 'Password', 'required');
    	$this->form_validation->set_rules('first', 'First', "required");
    	$this->form_validation->set_rules('last', 'last', "required");
    	$this->form_validation->set_rules('email', 'Email', "required|is_unique[user.email]");
    	
    	include_once $_SERVER['DOCUMENT_ROOT'] . '/connect4/securimage/securimage.php';
    	$securimage = new Securimage();
    	
    	// If it isn't valid, return to registration page
    	if ($this->form_validation->run() == FALSE) {
    		$data['partial'] = 'account/newForm';
    		$data['js'] = 'view/newForm.js';
    	} else if ($securimage->check($_POST['captcha_code']) == false) {
    		// If captcha is not correct, return to registration page
    		$data['partial'] = 'account/newForm';
    		$data['js'] = 'view/newForm.js';
    	} else {
    		$user = new User();
    		 
    		$user->login = $this->input->post('username');
    		$user->first = $this->input->post('first');
    		$user->last = $this->input->post('last');
    		$clearPassword = $this->input->post('password');
    		$user->encryptPassword($clearPassword);
    		$user->email = $this->input->post('email');
    		
    		$this->load->model('user_model');
    		
    		$error = $this->user_model->insert($user);
    		
    		$data['partial'] = 'account/loginForm';
    	}
    	$this->load->view('shared/layout', $data);
    }

    /*
     * Returns a view for changing the password
     */
    function updatePasswordForm() {
    	$data['partial'] = 'account/updatePasswordForm';
    	$data['js'] = 'view/updatePasswordForm.js';
    	$this->load->view('shared/layout', $data);
    }
    
    /*
     * Changes a password for a POST request
     */
    function updatePassword() {
    	$this->load->library('form_validation');
    	$this->form_validation->set_rules('oldPassword', 'Old Password', 'required');
    	$this->form_validation->set_rules('newPassword', 'New Password', 'required');
    	
    	$data['partial'] = 'account/updatePasswordForm';
    	$data['js'] = 'view/updatePasswordForm.js';
    	
    	if ($this->form_validation->run() == FALSE) {
    		$this->load->view('shared/layout', $data);
    	} else {
    		$user = $_SESSION['user'];
    		
    		$oldPassword = $this->input->post('oldPassword');
    		$newPassword = $this->input->post('newPassword');
    		 
    		if ($user->comparePassword($oldPassword)) {
    			$user->encryptPassword($newPassword);
    			$this->load->model('user_model');
    			$this->user_model->updatePassword($user);
    			redirect('arcade/index', 'refresh'); //Then we redirect to the index page again
    		} else {
    			$data['errorMsg']="Incorrect password!";
    			$this->load->view('shared/layout', $data);
    		}
    	}
    }
    
    /*
     * Returns a view for password recovery
     */
    function recoverPasswordForm() {
		$data['partial'] = 'account/recoverPasswordForm';
		$this->load->view('shared/layout', $data);
    }
    
    /*
     * Sends a new email to the user for a POST request
     */
    function recoverPassword() {
    	$this->load->library('form_validation');
    	$this->form_validation->set_rules('email', 'email', 'required');
    	
    	if ($this->form_validation->run() == FALSE) {
    		$data['partial'] = 'account/recoverPasswordForm';
    		$this->load->view('shared/layout', $data);
    	} else { 
    		$email = $this->input->post('email');
    		$this->load->model('user_model');
    		$user = $this->user_model->getFromEmail($email);

    		if (isset($user)) {
    			$newPassword = $user->initPassword();
    			$this->user_model->updatePassword($user);
    			
    			$this->load->library('email');
    		
    			// Initialize email variables
    			$config['protocol']    = 'smtp';
    			$config['smtp_host']    = 'ssl://smtp.gmail.com';
    			$config['smtp_port']    = '465';
    			$config['smtp_timeout'] = '7';
    			$config['smtp_user']    = 'mail.connect4online';
    			$config['smtp_pass']    = 'connect1234';
    			$config['charset']    = 'utf-8';
    			$config['newline']    = "\r\n";
    			$config['mailtype'] = 'text'; // or html
    			$config['validation'] = TRUE; // bool whether to validate email or not
    			
	    	  	$this->email->initialize($config);
    			
    			$this->email->from('mail.connect4online@gmail.com', 'Connect 4 Online');
    			$this->email->to($user->email);
    			
    			$this->email->subject('Password recovery');
    			$this->email->message("A request has been issued to recover your password. Your new password is $newPassword");
    			
    			$result = $this->email->send();
    			
    			$this->email->print_debugger();	
				
    			$data['partial'] = 'account/emailPage';
    			$this->load->view('shared/layout', $data);
    			
    		} else {
    			$data['errorMsg']="No record exists for this email!";
    			$data['partial'] = 'account/recoverPasswordForm';
    			$this->load->view('shared/layout', $data);
    		}
    	}
    }    
 }

