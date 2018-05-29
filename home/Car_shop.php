<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('US_PACKAGE_TOUR_CACHE_KEY', 'us_package_travel_');
define('US_PACKAGE_TOUR_CACHE_TIME', 24*60*60);
define('DEFAULT_CITY_NAME', '__DEFAULT__');
define('DEFAULT_PACKAGE_NAME', '__DEFAULT__');
define('HTTP_OK', 200);
define('HTTP_ERROR', 400);
define('DEFAULT_RS_NAMESPACE', 'normal_normal');
class Car_shop extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cg/Car_goods_model');
        $this->load->model('cg/Car_page_model');
		$this->load->model('cg/Car_modify_model');
		$this->load->model('cg/Car_order_model');
		$this->load->model('cg/Car_session');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Pro_cate_model');
        $this->load->model('us/Pro_navigation_model');
		

        $this->load->helper('url');
        $this->load->library('common');
        $this->load->library('MY_Session');
    }
    // 车购展示页面接口
    public  function car_view($id=''){
      $data['home']=  $this->home_page($id);
      $data['car_goods']=$this->car_goods($id);
       if(!empty($data)){
           $data['errorcode'] = 200;
            $data['msg'] = "成功";
            return $this->ajax_return($data); 
       }else{
           $data['errorcode'] = 400;
            $data['msg'] = "失败";
            return $this->ajax_return($data);  
       }
    }
// 车购首页
    public  function home_page($id=''){
        $where=array('id'=>$id,'is_del'=>0);
        $res=$this->Car_page_model->get_user_detail($where);
       
        $data['top_image']=$res['top_image'];
        $data['cover_image']=$res['cover_image'];
        $data['share_image']=$res['share_image'];
        $data['name']=$res['name'];
        $data['page_title']=$res['title_name'];
        $data['cg_type']=$res['cg_type'];
        $data['kf']=  json_decode($res['kf_image'],true);
        $data['time']=  date('Y-m-d',$res['add_time']);
       return $data; 
      // $this->ajax_return($data);
        
    }
// 车购商品
    public function  car_goods($page_id=''){
		$cg['page_id'] = $page_id;
		$cg['business_id'] = $this->Car_session->get_session('business_id');
		$cg['goods_type'] = 'car_g';
		$cg['is_del'] = 0;
		$cg['order_by'] = 'goods_id asc';		
        $res=$this->Car_goods_model->get_goods_list($cg);
		
		$cg['modify_id']=$this->Car_session->get_session('modify_id');
		unset($cg['goods_type'],$cg['order_by'] );
//  print_r($cg);die;
//		$where=array('id'=>$modify_id);
 //       $modify_list=$this->Car_modify_model->get_modify_detail($where);
  //      $modify_list=$this->Car_modify_model->get_modify_detail($cg);
  //      $goods_list= json_decode($modify_list['goods_list'],TRUE); // 查询修改后的价格

        foreach($res as $k=>$v){
            $data[$k]['goods_id']=$v['goods_id'];
            $data[$k]['goods_name']=$v['goods_name'];
            $data[$k]['price']=($this->get_modify_price($v['goods_id']))?$this->get_modify_price($v['goods_id']):$v['price'];
            $data[$k]['selling']=$v['selling'];
            $data[$k]['goods_top']=$v['goods_top'];
            $data[$k]['goods_spec']=$v['goods_spec'];
            $data[$k]['goods_share']=$v['goods_share'];
            $data[$k]['goods_details']=$v['goods_details'];
            $data[$k]['time']=date('Y-m-d',$v['add_time']);
            
        }
         return $data; 
        
    }

	   // 获取商品
 public  function goods_list($page_id=''){
     $business_id=$this->Car_session->get_session('business_id');
     $where=array('business_id'=>$business_id,'page_id'=>$page_id,'is_del'=>0);
    $res=$this->Car_goods_model->get_goods_list($where);
    foreach($res as $k=>$v){
        $data[$k]['page_id']=$v['page_id'];
        $data[$k]['goods_id']=$v['goods_id'];
        $data[$k]['goods_name']=$v['goods_name'];
        $data[$k]['price']=$v['price'];
    }
    return $data;
 }

 //车购商品详情页面接口
 public function goods_details($goods_id=''){
	$cg['goods_id'] = $goods_id;
	$cg['page_id'] = $this->input->post('page_id', TRUE);
    $cg['business_id'] = $this->Car_session->get_session('business_id');
	$cg['goods_type'] = 'car_g';
	$cg['is_del'] = 0;
     $where=array('goods_id'=>$goods_id,'is_del'=>0);
     $res=$this->Car_goods_model->get_goods_detail($where);
	
//	$cg['guide_id'] = $this->Car_session->get_session('guide_id');
//	$cg['group_id'] = $this->Car_session->get_session('group_id');
	
     if($res){
         $data['dt']['goods_name']=$res['goods_name'];
         $data['dt']['goods_id']=$res['goods_id'];
         $data['dt']['price']=($this->get_modify_price($res['goods_id']))?$this->get_modify_price($res['goods_id']):$res['price'];
         $data['dt']['goods_share']=$res['goods_share'];
         $data['dt']['goods_details']=$res['goods_details'];
         $data['dt']['add_time']=date('Y-m-d',$res['add_time']);
         $data['errorcode'] = 200;
         $data['msg'] = "成功";
         return $this->ajax_return($data); 
         
     }else{
            $data['errorcode'] = 400;
            $data['msg'] = "失败";
            return $this->ajax_return($data); 
     }
 }

 //车购用户订单列表页面接口
 public function order_list(){
	 $cg['page_id'] = $this->input->post('page_id', TRUE);
     $cg['buyers_name'] = $this->input->post('buyers_name', TRUE);
    $cg['business_id'] = $this->Car_session->get_session('business_id');
    $cg['guide_id'] = $this->Car_session->get_session('guide_id');
	$cg['group_id'] = $this->Car_session->get_session('group_id');	
	 $type = $this->input->post('type', TRUE);
	if($type=='group'){ //后台由post获取团号 
		$cg['group_id'] = $this->input->post('group_id', TRUE);
		unset($cg['buyers_name'],$cg['page_id']);
		}
	$cg['is_del'] = 0;
	if($cg['buyers_name'] or $type=='group'){
     $res=$this->Car_order_model->get_order_list($cg);
     if($res){
        foreach($res as $k=>$v){
            $data['list'][$k]['id']=$v['id'];
            $data['list'][$k]['group_id']=$v['group_id'];
			$data['list'][$k]['order_sn']=$v['order_sn'];
			$data['list'][$k]['pay_type']=$v['pay_type'];
			$data['list'][$k]['buyers_name']=$v['buyers_name'];
			$data['list'][$k]['buyers_mobile']=$v['buyers_mobile'];
			$data['list'][$k]['price']=$v['price'];
			$data['list'][$k]['order_state']=$v['order_state'];
            $data['list'][$k]['order_list']=json_decode($v['order_list'],true);
            $data['list'][$k]['add_time']=date('Y-m-d',$v['add_time']);
            
        }
         $data['errorcode'] = 200;
         $data['msg'] = "成功";
         return $this->ajax_return($data); 
         
     }else{
            $data['errorcode'] = 400;
            $data['msg'] = "没有订单";
            return $this->ajax_return($data); 
     }
	}else{
		$data['errorcode'] = 200;
         $data['msg'] = "成功";
         return $this->ajax_return($data); 
	}
 }

 //车购价格修改页面接口
 public function modify_list($page_id=''){
	$cg['page_id'] = $this->input->post('page_id', TRUE);
    $cg['business_id'] = $this->Car_session->get_session('business_id');
    $cg['guide_id'] = $this->Car_session->get_session('guide_id');
	if(!$cg['guide_id']) $cg['guide_id'] = $this->Car_session->get_session('user_id');
	$cg['is_del'] = 0;
	$cg['order_by'] = 'id desc';
	$type = $this->input->post('type', TRUE);
	if($type=='group'){ //后台由post获取团号 
		$cg['group_id'] = $this->input->post('group_id', TRUE);
		unset($cg['buyers_mobile']);
		if(!$cg['group_id']){unset($cg['group_id']);}
		if(!$cg['page_id']){unset($cg['page_id']);}
		}
     $res=$this->Car_modify_model->get_modify_list($cg);
 // echo $this->db->last_query();
	 if($cg['page_id'])$data['goods']=$this->goods_list($cg['page_id']);
     if($res){
        foreach($res as $k=>$v){
            $data['list'][$k]['id']=$v['id'];
            $data['list'][$k]['group_id']=$v['group_id'];
			$data['list'][$k]['url'] = 'http://api.etjourney.com/H5info_temp_zyx/qrcode_infos?name='.base64_encode(base_url('cg/car_home/index/'.$v['page_id']).'?g='.$v['guide_id'].'&b='.$v['business_id'].'&r='.$v['group_id'].'&m='.$v['id']);
            $data['list'][$k]['goods_list']=json_decode($v['goods_list'],true);
            $data['list'][$k]['add_time']=date('Y-m-d',$v['add_time']);
            
        }
         $data['errorcode'] = 200;
         $data['msg'] = "成功";
         return $this->ajax_return($data); 
         
     }else{
            $data['errorcode'] = 400;
            $data['msg'] = "失败";
            return $this->ajax_return($data); 
     }
 }
 
// 查询车购商品价格
    public  function get_modify_price($goods_id=''){
       $modify_id=$this->Car_session->get_session('modify_id');
       $where=array('id'=>$modify_id);
       $goods_list=$this->Car_modify_model->get_modify_detail($where);

		if($goods_list){
       $list=  json_decode($goods_list['goods_list'],true);
       foreach($list as $k=>$v){
          if($goods_id==$v['goods_id']){
              $data=$v['price'];
          }
       }
       return $data;
  }else{return false;}     
   }	
// 导游后台首页页面列表
    public  function page_list(){
        $business_id=$this->Car_session->get_session('business_id');
        $where=array('business_id'=>$business_id,'is_del'=>0,'order_by' => 'id asc');
        $res=$this->Car_page_model->get_user_list($where);
        if($res){
        foreach($res as $k=>$v){
			$data['list'][$k]['page_id']=$v['id'];
            $data['list'][$k]['cover_image']=$v['cover_image'];
            $data['list'][$k]['title_name']=$v['title_name'];
            $data['list'][$k]['name']=$v['name'];
            $data['list'][$k]['add_time']=date('Y-m-d',$v['add_time']); 
        }
		 $data['errorcode'] = 200;
         $data['msg'] = "成功";
         return $this->ajax_return($data); 
         
     }else{
            $data['errorcode'] = 400;
            $data['msg'] = "失败";
            return $this->ajax_return($data); 
     }
    }

}
