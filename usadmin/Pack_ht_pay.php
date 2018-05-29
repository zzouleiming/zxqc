<?php

/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');
define('DEFAULT_DAY_DETAILS', '<p class="i_food dayTit">餐饮</p><p><br/></p><p class="i_stroke dayTit">行程</p><p><br/></p><p class="i_hotel dayTit">住宿</p><p><br/></p>');
define('US_PACKAGE_TOUR_CACHE_KEY', 'us_package_tour_');
define('SESSION_KEY_PRE', 'us');
define('TRAVEL_FILE_KEY', 'Travel schedule');
define('DEFAULT_CITY_NAME', '__DEFAULT__');
define('DEFAULT_SHOW_CITY_NAME', '无城市');
define('DEFAULT_PACKAGE_NAME', '__DEFAULT__');
define('DEFAULT_SHOW_PACKAGE_NAME', '无套餐');
define('DEFAULT_DAY_ORDER', 999);
define('DEFAULT_TEMPLATE_TYPE', 'normal');
define('SHOW_PAGE_TITLE_LENGTH', 24);

class Pack_ht_pay extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Temp_type_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Company_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Package_hotel_info_model');
        $this->load->model('us/Pro_info_model');
        $this->load->model('us/Pro_navigation_model');
        $this->load->model('us/Hotel_package_model');
        $this->load->model('us/Form_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
        $this->load->model('tl/Page_access_model');
        $this->load->model('tl/Page_group_model');
        $this->load->model('tl/Page_role_model');
        $this->load->model('tl/Page_user_model');
         $this->load->model('tl/Page_order_model');
        $this->load->model('tl/Page_register_model');
        $this->load->model('us/Page_info_model');

        $this->load->helper('url');
        $this->load->helper('common');
        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');
    }
// 合途在线
// 订单生成

 //表单信息填写
 public function ht_form(){
	/*
        $ht['contact_name'] = $this->input->post('contact_name', true);
        $ht['passenger_name'] = $this->input->post('passenger_name', true);
        $ht['sender_name'] = $this->input->post('sender_name', true);
        $ht['receiver_name'] = $this->input->post('receiver_name', true);
        $ht['correct_name'] = $this->input->post('correct_name', true);
        $ht['card'] = $this->input->post('card', true);
        $ht['phone_number'] = $this->input->post('phone_number', true);
        $ht['flight_in_date'] = $this->input->post('flight_in_date', true);
        $ht['flight_out_date'] = $this->input->post('flight_out_date', true);
        $ht['travel_date'] = $this->input->post('flight_date', true);
        $ht['starting_address'] = $this->input->post('starting_address', true);
        $ht['arriving_address'] = $this->input->post('arriving_address', true);
        $ht['address'] = $this->input->post('address', true);
        $ht['banner'] = $this->input->post('banner', true);
        $ht['ticket_number'] = $this->input->post('ticket_number', true);
        $ht['over_weight'] = $this->input->post('over_weight', true);
        $ht['remark'] = $this->input->post('remark', true);
        $ht['order_sn'] = $this->input->post('order_sn', true);
        $ht['flight_number'] = $this->input->post('flight_number', true);
        $ht['sender_tel'] = $this->input->post('sender_tel', true);
        $ht['receiver_tel'] = $this->input->post('receiver_tel', true);
        $ht['form_id'] = $this->input->post('form_id', true);
		*/
		//Print_r($_POST);die;
		$ht =  $this->input->post(NULL,true);
		//if(!$ht['form_id']){uset($ht['form_id'],$ht['order_id']);}
		
      $res=  $this->Form_info_model->save_info_info($ht);
      $data['order_id']=$this->input->post('order_id',TRUE);
      $data['form_id']=$res;
      if(empty($data['order_id'])){
          echo '参数d不能为空';die;
      }
      $order=$this->Page_order_model->save_order_info($data);
      if ($res) {
          if($order){
                  $return ['form_id']=$res;
           $return['errorcode'] = 200;
           $return['msg'] = "成功";
           return $this->ajax_return($return);   
          }else{
              $return['errorcode'] = 400;
			$return['msg'] = "失败";
			return $this->ajax_return($return); 
          }
    
		 }else{
			$return['errorcode'] = 400;
			$return['msg'] = "失败";
			return $this->ajax_return($return); 
		 }
    }
	 //表单信息填写
 public function form_item(){
	
        $ht['form_id'] = $this->input->post('form_id', true);
        $ht['order_sn'] = $this->input->post('order_sn', true);
		if(!$ht['form_id']){die;}
		
      $res=  $this->Form_info_model->get_info_detail($ht);
      if ($res) {
           $return ['form_item']=$res;
           $return['errorcode'] = 200;
           $return['msg'] = "成功";
           return $this->ajax_return($return);   
          }else{
              $return['errorcode'] = 400;
			$return['msg'] = "失败";
			return $this->ajax_return($return); 
          }
    }
	
}
