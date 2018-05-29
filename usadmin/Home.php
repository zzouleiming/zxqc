<?php
/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends MY_Controller
{
	public function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }

    //首页
    public function index(){
        
        $this->load->view('usadmin/home/index');
    }
}
