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

class Car_purchase extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cg/Car_goods_model');
        $this->load->model('cg/Car_page_model');
        $this->load->model('cg/Car_order_model');
        $this->load->model('cg/Car_modify_model');
         $this->load->model('cg/Car_session');
        $this->load->model('User_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Company_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Package_hotel_info_model');
        $this->load->model('us/Pro_info_model');
        $this->load->model('us/Pro_navigation_model');
        $this->load->model('us/Hotel_package_model');
        $this->load->model('us/Cate_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
        $this->load->model('tl/Page_access_model');
        $this->load->model('tl/Page_group_model');
        $this->load->model('tl/Page_role_model');
        $this->load->model('tl/Page_user_model');
        $this->load->model('tl/Page_register_model');
        $this->load->model('us/Page_info_model');
        $this->load->helper('url');
        $this->load->helper('common');
        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');
    }

    // 基础信息上传
    public function index() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'];
        $data['menu_list'] = $this->_get_menu();
        $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        $data['business_all'] = $this->_get_business_all();
        $business_id = $business_account['business_id'];
        if ($business_id != 1) {
            $where = array('is_del' => 0, 'limit' => 20, 'offset' => 0, 'business_id' => $business_, 'order_by' => 'id desc');
        } else {
            $where = array('is_del' => 0, 'limit' => 20, 'offset' => 0, 'order_by' => 'id desc');
        }
        if (!$business_account['is_us']) {
            $where['business_id'] = $business_id;
        } else {
            if (!empty($data['param']['business_id'])) {
                $where['business_id'] = $data['param']['business_id'];
            }
        }
        if (!in_array('2007', $data['access_list'])) {
            $where['uploader'] = $business_account['business_account'];
        }
        if (!empty($data['param']['keyword'])) {
            $where['like'] = array('title_name' => $data['param']['keyword']);
        }
        if (!empty($data['param']['upload_date'])) {
            $where['add_time >= '] = strtotime($data['param']['upload_date']);
            $where['add_time < '] = strtotime($data['param']['upload_date']) + 24 * 60 * 60;
        }
        if (!empty($data['param']['per_page'])) {
            $where['offset'] = $data['param']['per_page'];
        }
        if (!empty($data['param']['page_id'])) {
            $where['page_id'] = $data['param']['page_id'];
        }
        if (!empty($data['param']['per_total'])) {
            $where['limit'] = $data['param']['per_total'];
        }
        $data['page_info_list'] = $this->Car_page_model->get_user_list($where);
        $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        foreach ($data['page_info_list'] as $k => $v) {
            if (mb_strlen($v['page_title']) > SHOW_PAGE_TITLE_LENGTH) {
                $data['page_info_list'][$k]['show_page_title'] = mb_substr($v['page_title'], 0, SHOW_PAGE_TITLE_LENGTH) . '...';
            } else {
                $data['page_info_list'][$k]['show_page_title'] = $v['page_title'];
            }
            $data['page_info_list'][$k]['image_data'] = json_decode($v['kf_image'], true);
            $data['page_info_list'][$k]['image_data']['top'] = $data['page_info_list'][$k]['image_data']['top'] ? $data['page_info_list'][$k]['image_data']['top'] : base_url('public/images/usadmin/no_head.jpg');
        }
        $data['page_prev_url'] = base_url('home/package_travel/view/' . $page_id) . '?' . $data['info']['share_url'];
        $data['business_account'] = $business_account;
        $data['add_page'] = base_url('cg/car_purchase/car_page_info');
        //编辑页面
        $data['edit_page'] = base_url('cg/car_purchase/car_page_info');
        //删除页面
        $data['page_del'] = base_url('cg/car_purchase/car_page_del');
        //调用页面搜索
        $data['index_url'] = base_url('cg/car_purchase/index');
        $this->load->view('usadmin/car/index', $this->_set_common($data));
    }

    public  function car_page_del(){
        $data['id']=$this->input->post('id',true);
        $data['is_del']=1;
        $res=$this->Car_page_model->save_user_info($data);
        if($res){
            $result['code'] = 0;
            $result['msg'] = "删除成功";
            return $this->ajax_return($result);
        }else{
            $result['code'] = 1;
            $result['msg'] = "删除失败";
            return $this->ajax_return($result);
        }
    }
    public  function car_page_info($id=''){
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['menu_list'] = $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
        $data['save_url']=base_url('cg/car_purchase/seve_car_user');
        $data['url']=$this->_url($id);
        $where=array('id'=>$id,'is_del'=>0);
        $data['list']=$this->Car_page_model->get_user_detail($where);
        $data['list']['kf']=  json_decode($data['list']['kf_image'],true);
        $data['business_id']=$business_account['business_id'];
        $this->load->view('usadmin/car/car_page_info', $this->_set_common($data));   
    }
    //车购商品上传页面
    public function car_goods_add($page_id = '') {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['menu_list'] = $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['page_id']=$page_id;
        $data['business_all'] = $this->_get_business_all();
        $data['type'] = $this->input->get('type', true);
        $data['id'] = $goods_id;
        $data['save_good'] = base_url('cg/car_purchase/seve_good');
        //当goods_id 存在时 查询所对应的商品信息
         $data['goods_id'] = $this->input->get('goods_id', true);
        $where = array('goods_id' => $data['goods_id'], 'is_del' => 0);
        $data['list'] = $this->Car_goods_model->get_goods_detail($where);
//        echo "<pre>";
//       print_r($data['list']);die;
       
        $this->load->view('usadmin/car/car_goods_add', $this->_set_common($data));
    }

    //车购商品列表
    public function car_art($id='') {
        if(empty($id)){
            echo "请先上传基础信息";die;
        }
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['menu_list'] = $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
        $data['url']=$this->_url($id);
        $data['type']=$this->input->get('type',true);
        $where=array('goods_type'=>$data['type'],'page_id'=>$id,'is_del'=>0);
        $data['list']=$this->Car_goods_model->get_goods_list($where);
        $data['car_goods_add']=  base_url('cg/car_purchase/car_goods_add').'/'.$id.'/?type='.$data['type'];
        foreach ($data['list'] as $k=>$v){
        switch ($v['goods_type']){
        case 'car':
            $data['list'][$k]['goods_types']='用车';
            break;
         case 'play':
            $data['list'][$k]['goods_types']='游玩';
            break;
         case 'hotel':
            $data['list'][$k]['goods_types']='酒店';
            break;
         case 'car_g':
            $data['list'][$k]['goods_types']='车购';
            break;
         case 'local':
            $data['list'][$k]['goods_types']='当地特色';
            break;
         case 'other':
            $data['list'][$k]['goods_types']='图文';
            break;
           case 'stroke':
            $data['list'][$k]['goods_types']='其他';
            break;
         default:
                FALSE;
        }
        }
        $data['car_del']=base_url('cg/car_purchase/car_del');//车购商品页面删除链接
        $data['upload_del']=base_url('cg/car_purchase/car_goods_add');//车购商品编辑页面
        $data['page_id']=$id;
        $this->load->view('usadmin/car/car_art', $this->_set_common($data));
    }
   // 车购商品删除
   public function car_del($goods_id=''){
       if(!$goods_id){
           echo "操作错误";die;
       }
       $type=$this->input->get('type',TRUE);
       $where=array('goods_id'=>$goods_id,'is_del'=>'0');
       $page_id=$this->Car_goods_model->get_goods_detail($where);
       if(empty($page_id)){
           echo '参数错误';die;
       }
       $data['goods_id']=$goods_id;
       $data['is_del']=1;
       $res=$this->Car_goods_model->save_goods_info($data);
       if($res){
            echo "<script>alert('删除成功');location.href='" . base_url("cg/car_purchase/car_art").'/'.$page_id['page_id'].'?type='.$type."';</script>";   
       }
   }
    //车购订单列表
    public function car_goods_order() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['menu_list'] = $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
         $cg['business_id'] = $business_account['business_id'];
         $where=array('business_id'=>$cg['business_id'],'is_del'=>0);
           $data['modify']=$this->Car_modify_model->get_modify_list($where);
           foreach($data['modify'] as $k=>$v){
                //拿取导游名字
             $where=array('user_id'=>$v['guide_id'],'is_del'=>0);
             $guide=$this->Page_user_model->get_user_detail($where);
             $data['modify'][$k]['guide_name']=$guide['user_name'];  
           }
         
           $group_name=$this->input->get('group_name',true);
           if(!empty($group_name)){
               $where=array('group_id'=>$group_name,'is_del'=>0);
               $data['list']=$this->Car_order_model->get_order_list($where);
             foreach( $data['list'] as $k=>$v){
             $data['list'][$k]['order_list']=  json_decode($v['order_list'],TRUE);//订单详情
           if(time()-$v['add_time']>=86400 && $v['order_state']==0){
         $dd['id']=$v['id'];
         $dd['order_state']=9;
         $this->Car_order_model->save_order_info($dd);
        }
             switch ($v['order_state']){
             case 0:
                 $data['list'][$k]['state']="未支付";
                 break;
              case 1:
                 $data['list'][$k]['state']="已支付";
                 break;
               case 7:
                 $data['list'][$k]['state']="已发货";
                 break;
              case 8:
                 $data['list'][$k]['state']="订单完成";
                 break;
              case 9:
                 $data['list'][$k]['state']="订单过期";
                 break;
             }
             
         }  
           }
              $data['goods_id']=$this->input->get('goods_id',true);
           if(!empty($data['goods_id'])){
               $where=array('id'=>$data['goods_id']);
                $goods_list=$this->Car_order_model->get_order_detail($where);
                $data['goods_list']=  json_decode($goods_list['order_list'],true);
           }

//    echo "<pre>";
//   print_r($data['list'][0]);die;
        $this->load->view('usadmin/car/order', $this->_set_common($data));
    }
    //订单发货界面
    public function  order_operation($id){
      
         $group=$this->input->get('group_name',true);
         $data['id']=$id;
        $data['order_state']=7;
       $res= $this->Car_order_model->save_order_info($data);
       if($res){
          echo "<script language=javascript>alert('发货成功');location.href='" . base_url("cg/car_purchase/car_goods_order?group_name=") .$group. "';</script>";
       }
    }
    
    //车购基础信息上传编辑
    public function seve_car_user() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $access = $this->_is_access();
        $cg['id'] = $this->input->post('id', true);
        $cg['business_id'] =$this->input->post('business_id', true);
        $cg['title_name'] = $this->input->post('title_name', true);
        $cg['name'] = $this->input->post('uploader', true);
        $cg['share_image'] = $this->input->post('share_image', true);
        if (isset($_FILES['image_top']) && $_FILES['image_top']['error'] == 0) {
            if ($cg['id']) {
                $cg['top_image'] = $this->upload_image('image_top', 'H5image/' . $cg['id'], $cg['id'] . 'image_top');
            } else {
                $cg['top_image'] = $this->upload_image('image_top', 'H5image', 'image_top');
            }
        } else {
            $cg['top_image'] = $this->input->post('image1', TRUE);
        }
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
            if ($cg['id']) {
                $cg['cover_image'] = $this->upload_image('cover_image', 'H5image/' . $cg['id'], $cg['id'] . 'cover_image');
            } else {
                $cg['cover_image'] = $this->upload_image('cover_image', 'H5image', 'cover_image');
            }
        } else {
            $cg['cover_image'] = $this->input->post('image2', TRUE);
        }
        if (isset($_FILES['share_image']) && $_FILES['share_image']['error'] == 0) {
            if ($cg['id']) {
                $cg['share_image'] = $this->upload_image('share_image', 'H5image/' . $cg['id'], $cg['id'] . 'share_image');
            } else {
                $cg['share_image'] = $this->upload_image('share_image', 'H5image', 'share_image');
            }
        } else {
            $cg['share_image'] = $this->input->post('image3', TRUE);
        }
        if (isset($_FILES['image_wx']) && $_FILES['image_wx']['error'] == 0) {
            if ($cg['id']) {
                $image_wx = $this->upload_image('image_wx', 'H5image/' . $cg['id'], $cg['id'] . 'image_wx');
            } else {
                $image_wx = $this->upload_image('image_wx', 'H5image', 'image_wx');
            }
        } else {
            $image_wx = $this->input->post('image4', TRUE);
        }
        $kf_type = $this->input->post('kf_type', TRUE);
        if ($kf_type == '微信') {
            $kf_data = array(
                'wx' => array(
                    'name' => '微信',
                    'intro' => $image_wx
                )
            );
        } elseif ($kf_type == '手机') {
            $kf_data = array(
                'mobile' => array(
                    'name' => '手机',
                    'intro' => $user_mobile
                )
            );
        } elseif ($kf_type == 'QQ') {
            $kf_data = array(
                'qq' => array(
                    'name' => 'QQ',
                    'intro' => $user_mobile
                )
            );
        }
        $cg['kf_image'] = json_encode($kf_data);
        $cg['add_time'] = time();

        $res = $this->Car_page_model->save_user_info($cg);
        if ($res) {
            if ($cg['id']) {
                echo "<script>alert('编辑成功');location.href='" . base_url("cg/car_purchase/car_art") . '/' . $res . "';</script>";
            } else {
                echo "<script>alert('添加成功 ');location.href='" . base_url("cg/car_purchase/car_art") . '/' . $res . "';</script>";
            }
        }
    }
// 车购商品上传,编辑
    public function seve_good() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $access = $this->_is_access();
        $data['menu_list'] = $this->_get_menu();
        $cg['goods_id'] = $this->input->post('goods_id', TRUE);
        if ($cg['goods_id']) {
            $cg['page_id'] = $this->input->post('page_id', TRUE);
            $cg['goods_type'] = $this->input->post('types', TRUE);
        } else {
            $cg['page_id'] = $this->input->post('page_id', TRUE);
            $cg['goods_type'] = $this->input->post('type', TRUE);
        }
        if (!$cg['page_id']) {
            echo "操作错误";
            die;
        }
        $cg['goods_name'] = $this->input->post('goods_name', true);
        $cg['business_id'] = $business_account['business_id'];
        $cg['price'] = $this->input->post('price', true);
        $cg['selling'] = $this->input->post('selling', true);
        $cg['goods_details'] = $this->input->post('goods_details', true);
        $cg['add_time'] = time();
        if (isset($_FILES['goods_top']) && $_FILES['goods_top']['error'] == 0) {
            if ($cg['id']) {
                $cg['goods_top'] = $this->upload_image('goods_top', 'Carimage/' . $cg['id'], $cg['id'] . 'goods_top');
            } else {
                $cg['goods_top'] = $this->upload_image('goods_top', 'Carimage', 'goods_top');
            }
        } else {
            $cg['goods_top'] = $this->input->post('image1', TRUE);
        }
        if (isset($_FILES['goods_spec']) && $_FILES['goods_spec']['error'] == 0) {
            if ($cg['id']) {
                $cg['goods_spec'] = $this->upload_image('goods_spec', 'Carimage/' . $cg['id'], $cg['id'] . 'goods_spec');
            } else {
                $cg['goods_spec'] = $this->upload_image('goods_spec', 'Carimage', 'goods_spec');
            }
        } else {
            $cg['goods_spec'] = $this->input->post('image2', TRUE);
        }
        if (isset($_FILES['goods_share']) && $_FILES['goods_share']['error'] == 0) {
            if ($cg['id']) {
             $cg['goods_share'] = $this->upload_image('goods_share', 'Carimage/' . $cg['id'], $cg['id'] . 'goods_share');
            } else {
             $cg['goods_share'] = $this->upload_image('goods_share', 'Carimage', 'goods_share');
            }
        } else {
            $cg['goods_share'] = $this->input->post('image3', TRUE);
        }
        $res = $this->Car_goods_model->save_goods_info($cg);
        if ($res) {
            if ($cg['goods_id']) {
                echo "<script>alert('编辑成功');location.href='" . base_url("cg/car_purchase/car_art") . '/' . $cg['page_id'] . '?type=' . $cg['goods_type'] . "';</script>";
                die;
            } else {
                echo "<script language=javascript>alert('添加成功');location.href='" . base_url("cg/car_purchase/car_art") . '/' . $cg['page_id'] . '?type=' . $cg['goods_type'] . "';</script>";
                die;
            }
        }
    }

// 车购订单生成页面
    public function car_order() {
      $data = $this->_get_urls();
        $business_account = $this->_get_auth();
   $access = $this->_is_access();
       $data['menu_list'] = $this->_get_menu();
        //生成车购订单号
        $cg['order_sn'] = 'CG' . date('Ymd', time()).  time() . rand('100000', '999999');
     //   print_r($cg);die;
        $cg['order_type'] = 1;
        $cg['business_id'] = $this->Car_session->set_session('busioness_id');
        $cg['guide_id'] = $this->Car_session->set_session('guide_id');
        $cg['group_id'] = $this->Car_session->set_session('group_id');
        $cg['order_name'] = $this->input->post('order_name', TRUE);
        $cg['price'] = $this->input->post('price', true);
        $cg['buyers_name'] = $this->input->post('buyers_name', TRUE); 
        $cg['buyers_mobile'] = $this->input->post('buyers_mobile', TRUE);
        $cg['order_list'] = $this->input->post('order_list', TRUE);
        $cg['order_state'] = 0;
        $cg['add_time'] = time();
        $res=$this->Car_page_model->save_order_info($cg);
        if ($res) {
            echo "<script>alert('提交成功')</script>";
        }
    }
// 订单查询
    public  function order_list(){
        $mobile=$this->input->get('mobile',true);//获取用户输入查询的手机号码
        $where=array('buyers_mobile'=>$mobile,'is_del'=>0);
        $res=$this->Car_order_model->get_order_list($where);
        if($res){
         foreach($res as $k=>$v){
        $data[$k]['order_sn']=$v['order_sn']; // 订单号
        $data[$k]['order_name']=$v['order_name']; //订单名称
        $data[$k]['price']=$v['price'];// 订单价格
        $data[$k]['order_list']=  json_decode($v['order_list'],true); //订单详细信息s ps  : 商品内容
      
        if(time()-$v['add_time']>=86400 && $v['order_state']==0){
         $data[$k]['order_state']=9; // 订单过期
         $dd['id']=$v['id'];
         $dd['order_state']=9;
         $this->Car_order_model->save_order_info($dd);
        }else{
          $data[$k]['order_state']=$v['order_state'];  
        }
        $data[$k]['add_time']=$v['add_time'];//订单下达时间 ，订单支付 ， 过期时间 
        $data['errorcode'] = 200;
        $data['msg'] = "成功";
        return $this->ajax_return($data); 
        }
        }else{
            $data['errorcode'] = 400;
            $data['msg'] = "失败";
            return $this->ajax_return($data);  
            
        }
        
    }
   //订单删除
   public  function car_goods_del($id){
       $group=$this->input->get('group_name',true);
       $data['id']=$id;
       $data['is_del']=1;
       $res= $this->Car_order_model->save_order_info($data);
       if($res){
          echo "<script language=javascript>alert('删除成功');location.href='" . base_url("cg/car_purchase/car_goods_order?group_name=") .$group. "';</script>";
       }
   }
   // 页面编辑导航栏通用链接
   public function _url($page_id=''){
       $data=array(
           'page_url'=>base_url('cg/car_purchase/car_page_info').'/'.$page_id,
           'product_url'=> base_url('cg/car_purchase/car_art').'/'.$page_id,
        //   'dt_url'=>base_url('cg/car_purchase/car_page_info').'/'.$page_id,
        //   'top_url'=>base_url('cg/car_purchase/car_page_info').'/'.$page_id,
           
       );
       return $data;; 
   }
   //单图片处理 
    private function upload_image($filename, $fileurl, $key = 'time') {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/' . $fileurl)) {
            if (!mkdir('./public/images/' . $fileurl)) {
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        switch ($file['type']) {
            case 'image/jpeg':
                $br = '.jpg';
                break;
            case 'image/png':
                $br = '.png';
                break;
            case 'image/gif':
                $br = '.gif';
                break;
            default:
                $br = FALSE;
                break;
        }

        if ($br) {
            if ($key == 'time') {
                $key = md5(rand(1, 99999) . time());
            }else{
               $key = md5($key); 
            }
            $pic_url = "./public/images/" . $fileurl . "/" . $key . $br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            return "/public/images/" . $fileurl . "/" . $key . $br;
        }
    }
    //多图上传
    private function muti_upload_image($filename) {
        $file = $_FILES[$filename];
        $new_url = array();
        foreach ($file['error'] as $k => $v) {
            if ($v == 0) {
                switch ($file['type'][$k]) {
                    case 'image/jpeg':
                        $br = '.jpg';
                        break;
                    case 'image/png':
                        $br = '.png';
                        break;
                    case 'image/gif':
                        $br = '.gif';
                        break;
                    default:
                        $br = FALSE;
                }

                if ($br) {
                    $key = md5(rand(1, 99999) . time());
                    $pic_url = "./public/images/H5image/" . $key . $br;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[] = "/public/images/H5image/" . $key . $br;
                }
            }
        }
        return $new_url;
    }

    //文件上传
    private function upload_files($filename, $fileurl, $key = 'time') {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/travel/' . $fileurl)) {
            if (!mkdir('./public/travel/' . $fileurl)) {
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        $info = pathinfo($file['name']);
        $br = '.' . $info['extension'];
        if (!in_array($br, array('.doc', '.docx', '.pdf'))) {
            return '';
        }

        if ($key == 'time') {
            $key = md5(rand(1, 99999) . time());
        }
        $pic_url = "./public/travel/" . $fileurl . "/" . $key . $br;
        move_uploaded_file($file['tmp_name'], $pic_url);
        return "/public/travel/" . $fileurl . "/" . $key . $br;
    }

    //分页数据
    private function _pagination($query, $per_page, $url_data) {
        $this->load->library('pagination');
        if (!is_numeric($query)) {
            $query = strtolower($query);
            $query = preg_replace("/(?<=^select)[\s\S]*?(?=from)/", " 1 ", $query);
            $query = preg_replace("/order by [\s\S]*/", "", $query);
            $query = preg_replace("/limit [\s\S]*/", "", $query);

            $query = $this->db->query("select count(1) total from ($query) tab");
            $row = $query->row_array();
        } else {
            $row['total'] = $query;
        }

        unset($url_data['per_page']);
        if (empty($url_data)) {
            $base_url = site_url($this->uri->uri_string());
        } else {
            $base_url = site_url($this->uri->uri_string() . '?' . http_build_query($url_data));
        }

        $this->pagination->usadmin_page(array('base_url' => $base_url, 'per_page' => $per_page, 'total_rows' => $row['total']));

        $link = $this->pagination->create_links();

        if (!empty($link)) {
            $link = $this->pagination->total_tag_open . $link . $this->pagination->total_tag_close;
        }

        return array('total' => $row['total'], 'link' => $link);
    }

    private function _check_city_name($page_id, $city_name) {
        if (!$city_name) {
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        if (empty($city_list)) {
            return false;
        }
        return true;
    }

    private function _check_package_name($page_id, $city_name, $package_name) {
        if (!$package_name) {
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'package_name' => $package_name
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        if (empty($package_list)) {
            return false;
        }
        return true;
    }

    private function _get_city_list_by_page($page_id) {
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'package_id asc',
            'group_by' => 'city_name'
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        return $city_list;
    }

    private function _get_package_list_by_city($page_id, $city_name) {
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'order_by' => 'package_id asc'
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        return $package_list;
    }

    private function _get_new_day_info($day_id, $page_id = false, $package_id = false) {
        $where = array(
            'day_id' => $day_id
        );
        $day_data = $this->Day_info_model->get_day_info_detail($where);
        $new_day_info = $day_data;
        if ($page_id) {
            $new_day_info['page_id'] = $page_id;
        }
        if ($package_id) {
            $new_day_info['day_package_id'] = $package_id;
        }
        $new_day_info['add_time'] = time();
        unset($new_day_info['day_id']);
        unset($new_day_info['update_time']);
        return $new_day_info;
    }

    //获取页面通用链接
    private function _get_urls() {
        $data['login_out_url'] = base_url('usadmin/business/login_out');
        $data['pwd_edit_url'] = base_url('usadmin/package_tour/pwd_edit');
        $data['change_business_url'] = base_url('usadmin/business/change_business');
        $data['package_tour_url'] = base_url('usadmin/package_tour/index');
        $data['free_tour_url'] = base_url('usadmin/free_tour/index');
       $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $data['role_list_url'] = base_url('usadmin/package_role/role_list');
        $data['user_list_url'] = base_url('usadmin/package_role/user_list');
        
        return $data;
    }
    // 左侧列表菜单
    private function _get_menu() {
        $data['menu']=array(
           'H5页面'=>array(
               '0'=>array(
                     'url'=>base_url('usadmin/package_tour/index'),
                     'title'=>'跟团游',
               ),
              '1'=>array(
                  'url'=>base_url('usadmin/package_tour/page_monitor_index'),
                  'title'=>'自由行',
              ) 
           ),    
        );
         
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        if(in_array('5001',$data['access_list'])){
         $data['menu']['订单列表'][0]= array(
                   'url'=>base_url('usadmin/Package_user_list'),
                  'title'=>'订单列表',  
              );
        }
        if(in_array('1004',$data['access_list'])){
         
             $data['menu']['权限管理'][0]= array(
                   'url'=>base_url('usadmin/package_role/role_list'),
                  'title'=>'职位管理',   
              );
        }
             if(in_array('3001',$data['access_list'])){
      
              $data['menu']['权限管理'][1]= array(
                   'url'=>base_url('usadmin/package_role/user_list'),
                  'title'=>'销售员管理',  
              );
        }
          if(in_array('3002',$data['access_list'])){
         $data['menu']['权限管理'][2]= array(
                   'url'=>base_url('usadmin/package_role/admin_list'),
                  'title'=>'后台用户管理',  
             );
        }
                         if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][0]= array(
                   'url'=>base_url('cg/car_purchase/index'),
                  'title'=>'页面管理',  
             );
        }
                   if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][1]= array(
                   'url'=>base_url('cg/car_purchase/car_page_info'),
                  'title'=>'导游管理',  
             );
        }
             if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][2]= array(
                'url'=>base_url('cg/car_purchase/car_goods_order'),
                  'title'=>'订单管理',  
             );
        }


        return $data['menu'];
    }

    //校验登录
    private function _get_auth() {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if (!$business_account_id) {
            redirect(base_url('usadmin/business/login'));
        }
        $where = array(
            'business_account_id' => $business_account_id
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if (empty($business_account)) {
            redirect(base_url('usadmin/business/login'));
        }
        $business_id = $this->my_session->get_session('business_id', SESSION_KEY_PRE);
        if (!$business_id) {
            redirect(base_url('usadmin/business/login'));
        }
        $business_account['business_id'] = $business_id;
        $where = array(
            'business_id' => $business_id
        );
        $business_info = $this->Business_info_model->get_business_info_detail($where);
        $business_account['business_name'] = $business_info['business_name'];
        $business_account['template_name'] = $business_info['template_name'];
        $business_account['share_name'] = $business_info['share_name'];
        return $business_account;
    }

    //获取所有后台商户
    private function _get_business_all($type = 1) {
        $where = array(
            'business_type' => $type
        );
        $business_all = $this->Business_info_model->get_business_info_list($where);
        return $business_all;
    }

    //获取商户
    private function _get_business_detail($business_id) {
        $data['business_id'] = $business_id;
        $business_all = $this->Business_info_model->get_business_info_detail($data);
        return $business_all;
    }

    private function _delete_cache($page_id) {
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $cache_key = US_PACKAGE_TOUR_CACHE_KEY . $page_id;
        $this->cache->delete($cache_key);
    }

    //获取通用头部左侧菜单
    private function _set_common($data) {
        $data['header'] = $this->load->view('usadmin/common/header', $data, true);
        $data['menu'] = $this->load->view('usadmin/common/menu', $data, true);
        //$data['show_count_code'] = $this->show_count_code();
        $data['footer'] = $this->load->view('usadmin/common/footer', $data, true);
        return $data;
    }

    //时间控件
    private function _timex() {
        $time = strtotime(date('Y', time()) . '-' . date('n', time()) . '-1');
        $timeend = strtotime('+11 month', $time);
        $data['date']['cal'][] = array(
            'year' => date('Y', $time),
            'month' => date('n', $time),
            'month_cn' => $this->common->get_month_cn(date('n', $time)),
            'week_first' => date("w", mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
            'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time)))),
            'all_days' => date('t', $time),
            'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
        );

        while (date('Y', $time) < date('Y', $timeend) OR date('n', $time) < date('n', $timeend)) {
            $data['date']['cal'][] = array(
                'year' => date('Y', strtotime('+1 month', $time)),
                'month' => date('n', strtotime('+1 month', $time)),
                'month_cn' => $this->common->get_month_cn(date('n', strtotime('+1 month', $time))),
                'week_first' => date("w", mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time)))),
                'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))),
                'all_days' => date('t', strtotime('+1 month', $time)),
                'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))
            );
            $time = strtotime('+1 month', $time);
        }

        return $data['date']['cal'];
    }
    // 跟团游导航栏菜单
    private function _tour($page_id){
 
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]= array(
                'url'=>base_url('usadmin/package_tour/page_edit/'.$page_id),
                'title'=>'基础内容',
            );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
             'url'=>base_url('usadmin/package_tour/page_trip/'.$page_id),
              'title'=>'产品编辑',   
             );
        }
        if(in_array('2004',$data['access_list'])){
         $data['tour'][2]=array(
              'url'=>  base_url('usadmin/package_tour/page_price/'.$page_id),
              'title'=>'导航编辑'
          );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array(
              'url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑'
          );    
        }

        return $data['tour'];
    }
  //自由行导航栏菜单
    
    private function _free($page_id){
       
         //头图编辑-2002  基础内容-2003   价格上传-2004    行程上传/产品编辑-2005   导航编辑-2006
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]=array(
         'url'=>base_url('usadmin/package_tour/free_page_edit/'.$page_id),
          'title'=>'基础内容',    
        );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
                 'url'=>base_url('usadmin/package_tour/article/'.$page_id),
              'title'=>'产品编辑',   
            );
        }
        if(in_array('2006',$data['access_list'])){
         $data['tour'][2]=array(
             'url'=>  base_url('usadmin/package_tour/addmenu/'.$page_id),
              'title'=>'导航编辑'  
         );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array('url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑');    
        }
        
        return $data['tour'];
    }
   public function log($list = array()) {
        $file = 'zweb/log/' . date('Y-m-d', time()) . '_log.txt'; //要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
        $time = date('Y-m-d H:i:s', time());
        $content = $time . '  :' . implode(' ', $list) . "\n";
        file_put_contents($file, $content, FILE_APPEND); // 这个函数支持版本(PHP 5) 
        file_get_contents($file); // 这个函数支持版本(PHP 4 >= 4.3.0, PHP 5) 
    }

    public function text() {
        $file_path = 'zweb/log/' . date('Y-m-d', time()) . '_log.txt';
        if (file_exists($file_path)) {
            $file_arr = file($file_path);
            for ($i = 0; $i < count($file_arr); $i++) {//逐行读取文件内容
                echo $file_arr[$i] . "<br />";
            }
        }
    }

// 取用户权限数据
    private function _is_access($role = '') {
        $business_account = $this->_get_auth();
        $where = array(
            'role_id' => $business_account['role_id'],
            'is_del' => 0,
        );

        $access_list = $this->Page_role_model->get_role_detail($where);
        $access_list = json_decode($access_list['access_list'], true);

        if (in_array($role, $access_list)) {

            return array('access' => true, 'access_list' => $access_list);
        } else {
            return array('access' => false, 'access_list' => $access_list);
        }
    }

//    public function arrays(){
////        $data['page_id']=74;
////        $h5_video=array(
////          '0'=>array(
////              'video'=>'http://image.etjourney.com/us/111.mp4',
////              'image'=>'http://image.etjourney.com/video_image/111.png',  
////          ), 
////            '1'=>array(
////               'video'=>'http://image.etjourney.com/us/222.mp4',
////              'image'=>'http://image.etjourney.com/video_image/222.png',   
////          ) , 
////                '2'=>array(
////             'video'=>'http://image.etjourney.com/us/333.mp4',
////              'image'=>'http://image.etjourney.com/video_image/333.png',  
////          ) ,
////    
////    
////);
////        $data['data_video']=  json_encode($h5_video);
////    $this->Page_info_model->save_page_info($data);                
////    }
//     $data['url']=  base_url('usadmin/package_tour/xiuxiu');   
//   $this->load->view('usadmin/page_monitor/xiuxiu',$data);
//}
//public function xiuxiu(){
//    $data['page_id']=120;
//     if (isset($_FILES['Filedata']) && $_FILES['Filedata']['error'] == 0) {
//            $data['h5_image'] = $this->upload_image('Filedata', 'H5image');
//        }
//       $xiuxiu= $this->Page_info_model->save_page_info($data);
//    if($xiuxiu){
//        return $this->ajax_return(上传成功);
//}
//
//
//}
//public  function tianqi(){
// header("Location: http://www.sojson.com/open/api/weather/json.shtml?city=上海"); 
//die;
//
//   
//
//
//}

}
