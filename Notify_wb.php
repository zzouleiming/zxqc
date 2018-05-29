<?php
/**
 * 阿里云回调
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Notify_wb extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->table = 'v_users';
	$this->load->model('User_Api_model');
    $this->load->model('Admin_model');
  }

  /**
   * [回调操作]
   * @param  
   * @return 
   */
	public function index()
	{
			echo 1;
	}
	
}