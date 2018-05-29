<?php

/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Index_type extends MY_Controller {

    public function __construct() {
        parent::__construct();
     
        $this->load->helper('url');
         $this->load->model('User_model');
        $this->load->model('hx/Hx_channel_model');
        $this->load->model('hx/Hx_coupon_class_model');
        $this->load->model('hx/Hx_coupon_data_model');
        $this->load->model('hx/Hx_coupon_info_model');
        $this->load->model('hx/Hx_coupon_type_model');
        $this->load->model('hx/Hx_machine_model');
        $this->load->model('hx/Hx_manager_model');
        $this->load->model('hx/Hx_order_model');
        $this->load->model('hx/Hx_count_model');
        $this->load->model('hx/Hx_security_info_model');
        $this->load->model('hx/Hx_security_code_model');
        $this->load->model('Business_info_model');

        $this->load->library('MY_Session');
     //   $this->load->library('Redis');
    }

    public  function project_add(){
     
     
          $result['errcode'] = 200;
           $result['msg'] = "成功";
           $result['data']=1;
          return $this->ajax_return($result);          
    }
        public  function project_info(){
     
     
          $result['errcode'] = 200;
           $result['msg'] = "成功";
           $result['data']=1;
          return $this->ajax_return($result);          
    }
        public  function project_list(){
     
     
          $result['errcode'] = 200;
           $result['msg'] = "成功";
           $result['data']=1;
          return $this->ajax_return($result);          
    }
 
    //优惠券 新增 编辑
  



}
