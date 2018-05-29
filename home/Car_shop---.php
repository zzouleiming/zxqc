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
        $this->load->model('cg/Car_user_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Pro_cate_model');
        $this->load->model('us/Pro_navigation_model');
        $this->load->model('H5_Day_info_model');
        $this->load->model('H5_Fly_info_model');
        $this->load->model('us/Pro_info_model');
        $this->load->model('Wx_account_model');
       $this->load->model('us/Package_hotel_info_model');

        $this->load->helper('url');
        $this->load->library('common');
    }
    // 车购展示页面接口
    public  function car_view($id=''){
      $data['home']=  $this->home_page($id);
      $data['car_good']=$this->car_good($id);
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
        $res=$this->Car_user_model->get_user_detail($where);
        $data['top_image']=$res['top_image'];
        $data['cover_image']=$res['cover_image'];
        $data['share_image']=$res['share_image'];
        $data['name']=$res['name'];
        $data['page_title']=$res['title_name'];
        $data['cg_type']=$res['cg_type'];
        $data['kf']=  json_decode($res['kf_image'],true);
        $data['time']=  date('Y-m-d',$res['add_time']);
        return $data;   
    }
// 车购商品
    public function  car_good($page_id=''){
        $where=array('page_id'=>$page_id,'is_del'=>0, 'order_by' => 'goods_id asc');
        $res=$this->Car_goods_model->get_goods_list($where);
        foreach($res as $k=>$v){
            $data[$k]['goods_id']=$v['goods_id'];
            $data[$k]['goods_name']=$v['goods_name'];
            $data[$k]['price']=$v['price'];
            $data[$k]['selling']=$v['selling'];
            $data[$k]['goods_top']=$v['goods_top'];
            $data[$k]['goods_spec']=$v['goods_spec'];
            $data[$k]['goods_share']=$v['goods_share'];
            $data[$k]['goods_details']=$v['goods_details'];
            $data[$k]['time']=date('Y-m-d',$v['add_time']);
            
        }
         return $data; 
    }
 //车购商品详情页面接口
 public function goods_details($goods_id=''){
     $where=array('goods_id'=>$goods_id,'is_del'=>0);
     $res=$this->Car_goods_model->get_goods_detail($where);
     if($res){
         $data['dt']['goods_name']=$res['goods_name'];
         $data['dt']['goods_id']=$res['goods_id'];
         $data['dt']['price']=$res['price'];
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
}
