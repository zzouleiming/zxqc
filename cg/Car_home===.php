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

class Car_home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cg/Car_goods_model');
		$this->load->model('cg/Car_modify_model');
        $this->load->model('cg/Car_page_model');
		$this->load->model('cg/Car_order_model');
		$this->load->model('cg/Car_session');
        $this->load->model('User_model');
		
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
    
   //车购前端显示页面
   public  function index($id=''){
	$guide_id=$this->input->get('g',TRUE);
        if(empty($guide_id)){
            echo "缺少参数g";die;
        }
        $business_id=$this->input->get('b',true);
        if(empty($business_id)){
       echo '缺少参数';die;
         }
        $group_id=$this->input->get('r',true);
        if(empty($group_id)){
            echo "缺少参数r";die;
        }
		$modify_id=$this->input->get('m',true);
        if(empty($modify_id)){
            echo "缺少参数m";die;
        }
        $where=array('business_id'=>$business_id);
        $res=$this->Business_info_model->get_business_info_detail($where); //验证business_id 是否正确
        if(empty($res)){
            echo "参数非法b";die;
        }
        $where=array('user_id'=>$guide_id,'is_del'=>0);
        $res=$this->Page_user_model->get_user_detail($where);  // 验证导游id是否正确
        if(empty($res)){
            echo '参数非法g';die;
        }
        $is_son=array(
            'guide_id'=>$guide_id,
            'business_id'=>$business_id,
            'group_id'=>$group_id,
			'modify_id'=>$modify_id	
        );
        $this->Car_session->set_session($is_son);
        $data=[];
        //服务器url
        $data['car_add']=base_url('cg/car_home/add_cart');
        $data['car_de']=base_url('cg/car_home/de_cart');
        $data['to_cart']=base_url('cg/car_home/car_index');
        $data['to_order']=base_url('wygoods/wx_order_list');

		$data['page_id']=$id;
        $this->load->view('home/car/index',$data);

   }


      //wx 商品 详情页面
    public function goods_detail($goods_id = '')
    {
        $data['business_id'] = $this->Car_session->get_session('business_id');
        $data['guide_id'] = $this->Car_session->get_session('guide_id');
		$data['group_id'] = $this->Car_session->get_session('group_id');
		$data['page_id'] = $this->input->post('page_id', true);

        $data['car_add']=base_url('cg/car_home/add_cart');
        $data['order_in_session']=base_url('cg/car_home/order_in_session');
        $data['to_order']=base_url('wygoods/wx_order_list');
        $data['sub_url']=base_url("wygoods/order_add?type=$this->one");
		
		$data['goods_id']=$goods_id;
        $this->load->view('home/car/detail',$data);
        $this->show_count();
    }
	
	
// 车购订单生成页面
    public function car_order() {
 //       $data = $this->_get_urls();
 //       $business_account = $this->_get_auth();
//        $access = $this->_is_access();
        //生成车购订单号
        $cg['order_sn'] = 'CG' . date('YmdHis', time()) . rand('1000', '9999');
		$cg['page_id'] = $this->input->post('page_id', TRUE);
        $cg['order_type'] = 1;
        $cg['business_id'] = $this->Car_session->get_session('business_id');
        $cg['guide_id'] = $this->Car_session->get_session('guide_id');
		$cg['group_id'] = $this->Car_session->get_session('group_id');
        $cg['order_name'] = $this->input->post('order_name', TRUE);
        $cg['price'] = $this->input->post('price', true);
        $cg['buyers_name'] = $this->input->post('buyers_name', TRUE);
        $cg['buyers_mobile'] = $this->input->post('buyers_mobile', TRUE);
        $cg['order_list'] = $this->input->post('order_list', TRUE);
        $cg['order_state'] = 0;
        $cg['add_time'] = time();
        $res=$this->Car_order_model->save_order_info($cg);
        if ($res) {
		   $cg['id'] = $res;
		   $cg['order_list'] = json_decode($cg['order_list'] , true);
		   $cg['add_time'] = date('Y-m-d H:i', $cg['add_time']);
           $return['errorcode'] = 200;
		   $return['order'] = $cg;
		   $return['id'] = $res;
           $return['msg'] = "成功";
           return $this->ajax_return($return); 
		 }else{
			$return['errorcode'] = 400;
			$return['msg'] = "失败";
			return $this->ajax_return($return); 
		 }
    }
   
	public function car_modify() {

        $data['business_id'] = $this->Car_session->get_session('business_id');
        $data['guide_id'] = $this->Car_session->get_session('guide_id');
		$data['group_id'] = $this->input->post('group_id', true);
		$data['page_id'] = $this->input->post('page_id', true);
        $data['goods_list'] = $this->input->post('goods_list', true);
        $data['add_time'] = time();
        if (empty($data['group_id'])) {
            echo "请填写团号";
            die;
        }
        $where = array('group_id' => $data['group_id'], 'is_del' => 0);
        $modify = $this->Car_modify_model->get_modify_detail($where);
        if ($modify) {
            echo '此团号已经存在，请勿重复填写';
            die;
        }

        $res = $this->Car_modify_model->save_modify_info($data);
        if ($res) {
          $return['errorcode'] = 200;
		  $return['id'] = $res;
         $return['msg'] = "成功";
         return $this->ajax_return($return); 
         
     }else{
            $return['errorcode'] = 400;
           $return['msg'] = "失败";
            return $this->ajax_return($return); 
     }
       
    }

	public function del_price() {
        $data['id'] = $this->input->post('id', true);
		
        if (empty($data['id'])) {
            echo "参数错误id";
            die;
        }
		$data['is_del'] = 1;
        $res = $this->Car_modify_model->save_modify_info($data);
        if ($res) {
          $return['errorcode'] = 200;
		  $return['id'] = $res;
         $return['msg'] = "成功";
         return $this->ajax_return($return); 
         
     }else{
            $return['errorcode'] = 400;
           $return['msg'] = "失败";
            return $this->ajax_return($return); 
     }
       
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
// 导游后台
 public  function group_list(){
 
     $this->load->view('home/car/modify_index');
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
	
}