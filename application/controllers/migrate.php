<?php defined("BASEPATH") or exit("No direct script access allowed");

class Migrate extends CI_Controller {

  public function index() {
    $this->load->library('migration'); 
    if (!$this->migration->current())
    {
      show_error($this->migration->error_string());
    }
  }
}

