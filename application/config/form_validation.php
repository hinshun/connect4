<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
		'auto/register' => array(
				array(
						'field' => 'username',
						'label' => 'Username',
						'rules' => 'required|min_length[5]|max_length[12]'
				),
				array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'required|min_length[4]'
				),
				array(
						'field' => 'passconf',
						'label' => 'Password Confirmation',
						'rules' => 'required|min_length[4]|matches[password]'
				),
				array(
						'field' => 'phone',
						'label' => 'Phone Number',
						'rules' => 'required|callback_phone_check'
				)
		)
);


