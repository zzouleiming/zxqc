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

class Package_user_list extends MY_Controller {

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
        $this->load->model('us/Cate_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
        $this->load->model('tl/Page_access_model');
        $this->load->model('tl/Page_group_model');
        $this->load->model('tl/Page_role_model');
        $this->load->model('tl/Page_user_model');
        $this->load->model('tl/Page_register_model');
        $this->load->model('tl/Page_order_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('tl/Page_u_user_page_model');
        $this->load->helper('url');
        $this->load->helper('common');
        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');
    }


    public function index() {
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
         $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
            $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        $where=array('business_id'=>$data['business_account']['business_id'],'limit' => 15,'is_del'=>0,'order_by'=>'order_id desc' );
          if (!empty($data['param']['keyword'])) {
            $where['like'] = array('order_name' => $data['param']['keyword']);
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
       $data['list']=$this->Page_order_model->get_order_list($where);  
       $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
             
       foreach ($data['list'] as $k=>$v){

       $where=array('user_id'=>$v['seller_list'],'is_del'=>0);
       $xx =$this->Page_user_model->get_user_detail($where);//拿取销售员信息
      // echo "--\n". $this->db->last_query();
       $data['list'][$k]['xx_name']=$xx['user_mobile']?$xx['user_mobile']:$v['seller_list'];
       
       $where=array('user_id'=>$v['seller_list'],'u_page_id'=>$v['page_id'],'is_del'=>0,'order_by'=>'id asc');
       $names=array();
       $names=$this->Page_u_user_page_model->get_u_user_page_detail($where);
      // echo  "==\n". $this->db->last_query();
           $where=array('user_id'=>$names['user_id'],'is_del'=>0);
           $name_xx=$this->Page_user_model->get_user_list($where);
               $data['list'][$k]['xs_name']=$name_xx;
               switch ($v['state']){
                   case 0:
                   $data['list'][$k]['zt']='未支付';
                    $data['list'][$k]['color']='red';
                       break;
                      case 1:
                   $data['list'][$k]['zt']='已支付';
                    $data['list'][$k]['color']='green';
                       break;
                      case 2:
                   $data['list'][$k]['zt']='客服未确认';
                   $data['list'][$k]['color']='orange';
                       break;
                      case 8:
                   $data['list'][$k]['zt']='处理完成';
                    $data['list'][$k]['color']='gray';
                           break;
                          case 9:
                   $data['list'][$k]['zt']='订单过期';
                   $data['list'][$k]['color']='gray';
                       break;   
                       
                   
               }
               if($v['pay_type']=='ALIPAY_WAP'){
                    $data['list'][$k]['fs']='支付宝';
               }else if($v['pay_type']=='WECHAT_SUB'){
                    $data['list'][$k]['fs']='微信';   
               }
    
               
      
         }
        $data['order_del'] = base_url('usadmin/Package_user_list/order_del');
        //订单编辑
        $data['order_detail_url'] = base_url('usadmin/package_user_list/order_detail');
        //用户表单
        $data['form_url'] = base_url('usadmin/package_user_list/form_list');
        // 客服确认订单
        $data['kf_url'] = base_url('usadmin/package_user_list/kf_ok');
        // 订单完成
        $data['kf_order_url'] = base_url('usadmin/package_user_list/kf_order_ok');
         //搜索链接
        $data['kword']=  base_url('usadmin/package_user_list/index');
      
        $this->load->view('usadmin/page_user_list/order', $this->_set_common($data));
    }
 //分销订单提交
 public function order_add() {
//      echo "<pre>";
//      print_r($_POST);die;
        $data = $this->_get_urls();
        $data['order_sn'] = 'TL' . date('Ymd', time()) . rand(100000, 999999);
        $data['good_name'] = $this->input->post('good_name', TRUE);
        $data['good_list'] = json_encode($this->input->post('good_list', TRUE));
        $data['good_list']=$this->input->post('good_list',TRUE);  
        $data['seller_list'] = $this->input->post('seller_list', TRUE);
        $data['doc_list'] = json_encode($this->input->post('doc_list', TRUE));
        $data['order_cid']=$this->input->post('cId',TRUE);
        $data['order_price'] = $this->input->post('order_price', TRUE);
        $data['order_remark'] = $this->input->post('order_remark', TRUE);
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['order_name'] = $this->input->post('order_name', true);
        $data['order_mobile'] = $this->input->post('order_mobile', TRUE);
        $data['business_id']=$this->input->post('business_id', TRUE);
        $data['add_time'] = time();
//        echo "<pre>";
//        print_r($data);die;
        $res = $this->Page_order_model->save_order_info($data);
        $url=  base_url('usadmin/package_user_list/ok');
        if ($res) {
           echo "<script language=javascript>alert('提交成功');location.href='$url';</script>";     
            die;
        }
    }
//订单删除
 public function order_del($order_id){
     $data['order_id']=$order_id;
     $data['is_del']=1;
      $res = $this->Page_order_model->save_order_info($data);
    //echo  $this->db->last_query();die;
      $url=  base_url('usadmin/Package_user_list');
      if($res){
         echo "<script language=javascript>alert('删除成功');location.href='$url';</script>";     
            die;  
      }
     
 }
// 订单编辑 
    public function order_detail($order_id=''){
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();  
        $where=array('order_id'=>$order_id,'is_del'=>0);
        $data['list']=$this->Page_order_model->get_order_detail($where);
           // 用户编辑
           	$data['good_list']=json_decode($data['list']['good_list'],TRUE);
             $data['user_update_url']=base_url('usadmin/package_user_list/user_update');
       foreach($data['good_list'] as $k=>$v){
             $data['good_list'][$k]['names']="\n".'【'.$v['columnName'].'】'."\n".$v['itemName'].'('.$v['itemUnit'].'='.$v['allPrice'].'元) '."\n".'--------------------------------------------------------------';
       }

       
     $this->load->view('usadmin/page_user_list/order_detail',$this->_set_common($data));   
   
}
//用户表单信息编辑
    public function form_list($order_sn = '') {
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list'] = $this->_get_menu();
        $where = array('order_sn' => $order_sn, 'is_del' => 0);
        $data['list'] = $this->Form_info_model->get_info_detail($where);
        if (empty($data['list'])) {
            echo"<script>alert('该用户未填写表单信息！');history.go(-1);</script>";
        }
        $this->load->view('usadmin/page_user_list/order_form', $this->_set_common($data));
    }

    // 客服操作
    public function kf_ok($order_id = '') {
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list'] = $this->_get_menu();
        $kf['order_id'] = $order_id;
        $kf['state'] = 0;
        $url = base_url('usadmin/package_user_list');
        $res = $this->Page_order_model->save_order_info($kf);
        //   echo   $this->db->last_query();die;
        if ($res) {
            echo "<script language=javascript>alert('确认成功');location.href='$url';</script>";
        }
    }

    // 客服确认订单
    public function kf_order_ok($order_id=''){
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();  
        $kf['order_id']=$order_id;
        $kf['state']=8;
        $url=  base_url('usadmin/package_user_list');
         $res=$this->Page_order_model->save_order_info($kf);
         $where=array('order_id'=>$kf['order_id']);
         $order_sn=$this->Page_order_model->get_order_detail($where);
        if($res){
         echo   "<script language=javascript>alert('订单完成');location.href='$url';</script>";       
          $smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => $order_sn['order_sn'], //接受短信的用户手机号码
                'tpl_id' => '61439', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#ordersn#=' . $order_sn['order_sn']  . '' //您设置的模板变量，根据实际情况修改
            );
            $content = $this->_sendsms($smsConf, 1); //请求发送短信
        }
  }
  //用户价格备注修改编辑
    public function user_update() {
        $data['order_id'] = $this->input->post('order_id');
        $data['order_remark'] = $this->input->post('order_remark');
        $data['cost_price'] = $this->input->post('cost_price');
        $res = $this->Page_order_model->save_order_info($data);
        $url = base_url('usadmin/package_user_list');
        if ($res) {
            echo "<script language=javascript>alert('编辑成功');location.href='$url';</script>";
        }
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
  // echo base_url();die;
      //  echo $query;die;
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
          
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
      if(in_array('5003',$data['access_list'])){
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
        } 
         
   
                if(in_array('5001',$data['access_list'])||in_array('5002',$data['access_list'])||in_array('5004',$data['access_list'])){
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
                       if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][0]= array(
                'url'=>'http://new.cnzz.com/v1/login.php?siteid=1258510548" target="_blank"',
                  'title'=>'cszz统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][1]= array(
                'url'=>' https://tongji.baidu.com/web/24998121/visit/toppage?siteId=11585653" target="_blank"',
                  'title'=>'百度统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][2]= array(
                'url'=>'https://portal.qiniu.com/cdn/refresh-prefetch" target="_blank"',
                  'title'=>'刷新缓存',  
             );
        }


        return $data['menu'];
    }
   //ok
   public  function ok(){
       $this->load->view('home/user/ok');
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
              'title'=>'行程编辑',   
             );
        }
        if(in_array('2004',$data['access_list'])){
         $data['tour'][2]=array(
              'url'=>  base_url('usadmin/package_tour/page_price/'.$page_id),
              'title'=>'价格上传'
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
   public  function log($list= array()){
    $file  ='zweb/log/'.date('Y-m-d',time()).'_log.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
    $time=  date('Y-m-d H:i:s',  time());
   $content = $time.'  :'.implode(' ',$list)."\n";

  file_put_contents($file, $content,FILE_APPEND);// 这个函数支持版本(PHP 5) 
     file_get_contents($file); // 这个函数支持版本(PHP 4 >= 4.3.0, PHP 5) 
 
        
    }
 public  function text(){  
$file_path = 'zweb/log/'.date('Y-m-d',time()).'_log.txt';
if(file_exists($file_path)){
$file_arr = file($file_path);
for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容
echo $file_arr[$i]."<br />";
}
}


    }

    
// 取用户权限数据
  private function _is_access($role=''){
       $business_account = $this->_get_auth();
       $where=array(
            'role_id'=>$business_account['role_id'],
            'is_del'=>0,
        );
 
      $access_list = $this->Page_role_model->get_role_detail($where);
      $access_list = json_decode($access_list['access_list'], true);

      if(in_array($role,$access_list)){ 
          
         return array('access'=>true , 'access_list' =>$access_list );
      }else{
         return array('access'=>false , 'access_list' =>$access_list );
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
    public  function redis(){
  
   $redis = new Redis();
   $redis->connect('127.0.0.1', 6379);
   echo "Connection to server sucessfully";
         //查看服务是否运行
   echo "Server is running: " . $redis->ping();
    }
   private function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    } 
    private  function mobile(){
        $arr = array(
    130,131,132,133,134,135,136,137,138,139,
    144,147,
    150,151,152,153,155,156,157,158,159,
    176,177,178,
    180,181,182,183,184,185,186,187,188,189,
);
for($i = 0; $i < 1; $i++) {
    $tmp= $arr[array_rand($arr)].' '.mt_rand(1000,9999).' '.mt_rand(1000,9999);
}
return $tmp;
    }
        private function _sendsms($params = false, $ispost = 0) {
        $url = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
