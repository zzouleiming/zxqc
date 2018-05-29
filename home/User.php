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

class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
        $this->load->model('tl/Page_u_user_page_model');
        $this->load->model('tl/Page_access_model');
        $this->load->model('tl/Page_group_model');
        $this->load->model('tl/Page_role_model');
        $this->load->model('tl/Page_user_model');
        $this->load->model('tl/Page_order_model');
        $this->load->model('tl/Page_collection_model');
        $this->load->model('tl/Page_register_model');
        $this->load->model('us/Class_type_model');
         $this->load->model('us/Page_old_model');
        $this->load->helper('url');
        $this->load->helper('common');
        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');
    }

////员工注册页面
    public function register($business_id = '') {
        if ($business_id == '') {
            $business_id == '67396';
        }
        $data['save'] = base_url('home/user/save?type=register');
        $data['business_id'] = $business_id;
        $mo = $this->input->get('m', true);
        if (strstr($mo, "-")) {
            $r = explode('-', $mo);
            $data['m'] = $r[0];
            $data['u'] = $r[1];
            $data['p'] = $r[2];
        }
        $data['title'] = "注册";
		$where = array('business_id' => $business_id);     
		$banme = $this->Business_info_model->get_business_info_detail($where);
		$data['business_name'] = $banme['business_name'];
        $where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['group_list'] = $this->Page_group_model->get_group_list($where);
        $where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['role_list'] = $this->Page_role_model->get_role_list($where);
		$user['title'] = $data['title'];
		$data['head'] = $this->_head($user);
        $this->load->view('home/user/register', $data);
    }

// 编辑
    public function user_edit($user_id = '') {
        $user = $this->_is_login();
        $access = $this->_is_access('4001');
        //没有权限，跳转到个人信息页面
        if ($user_id && !$access['access']) {
            $url = base_url('home/user/user_edit/');
            header("Location: $url");
        }
        // $user_id 无参数，取session
        $user_id = $user_id ? $user_id : $user['user_id'];
        //有权限，$user_id 赋值,无权限取session
        $user_id = $access['access'] ? $user_id : $user['user_id'];
        //$user_id参数和session相同，编辑个人信息
        $data['title'] = ($user_id == $user['user_id']) ? "个人信息" : "用户信息";
        $data['save'] = base_url('home/user/save'); //ps:编辑表单提交路径，要带user_id


        $where = array('user_id' => $user_id, 'is_del' => 0);
        $data['list'] = $this->Page_register_model->get_register_detail($where);
        $data['business_id'] = $this->_is_login("business_id");
        $where = array('business_id' => $data['business_id']);     
		$banme = $this->Business_info_model->get_business_info_detail($where);
		$data['business_name'] = $banme['business_name']; 
		$where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['group_list'] = $this->Page_group_model->get_group_list($where);
        $where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['role_list'] = $this->Page_role_model->get_role_list($where);
        $user['title'] = $data['title'];
        $data['user_id'] = $user_id;
        $data['m'] = $data['list']['user_mobile'];
        $data['wx'] = $data['list']['user_wx'];
        $data['head'] = $this->_head($user);
        $this->load->view('home/user/register', $data);
    }

//登录
    public function login() {
        $data['save'] = base_url('home/user/sign');
		$user['title'] = "产品管理助手";
		$data['head'] = $this->_head($user);
        $data['title'] = "登录";
        $this->load->view('home/user/register', $data);
    }

//添加
    public function user_add() {
        $data['business_id'] = $this->_is_login("business_id");
        $access = $this->_is_access('4001');
        if (!$access['access']) {
            echo '非法访问。';
            exit;
        }
        $data['save'] = base_url('home/user/save');
        $data['title'] = "添加用户";
        $where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['group_list'] = $this->Page_group_model->get_group_list($where);
        $where = array('business_id' => $data['business_id'], 'is_del' => 0);
        $data['role_list'] = $this->Page_role_model->get_role_list($where);
        $user['title'] = $data['title'];
        $data['head'] = $this->_head($user);
        $this->load->view('home/user/register', $data);
    }

// 注册表单提交
    public function save() {
        $data['user_id'] = $this->input->post('user_id', true); //user_id 存在 执行update 否则insert
        $data['group_id'] = $this->input->post('group_id', true);
        $data['business_id'] = $this->input->post('business_id', true);
        $data['user_wx'] = $this->input->post('user_wx', true);
        $register=$this->input->get('type',TRUE);
        $business_id = $data['business_id'];
        if (empty($data['business_id'])) {
            echo '商户不能为空';
            die;
        }
        if ($data['user_id']) {
            $user_id = $this->_is_login("user_id");
            $access = $this->_is_access('4001');
            if ($user_id != $data['user_id'] & !$access['access']) {
                echo '非法访问。。';
                exit;
            }
            $data['user_id'] = $access['access'] ? $data['user_id'] : $user_id;
        }
        //赋值 销售员
        $where = array(
            'business_id' => $data['business_id'],
            'role_name' => '销售员',
        );
        $role_id = $this->Page_role_model->get_role_detail($where);
//     $data['role_id']=$this->input->post('role_id',TRUE);
//     if(empty($data['role_id'])){
//         $data['role_id']=0;
//     }
        if ($role_id['role_id']) {

            $data['role_id'] = $role_id['role_id'];
        } else {

            $data['role_id'] = 0;
        }
        $data['user_name'] = $this->input->post('user_name', TRUE);
        $data['user_mobile'] = $this->input->post('user_mobile', TRUE);
        if (empty($data['user_id'])) {
            $where = array('user_mobile' => $data['user_mobile']);
            $res = $this->Page_user_model->get_user_detail($where);
            if ($res) {
                echo "<script language=javascript>alert('该用户已被注册');history.back();</script>";
                die;
            }
        }

        $user_pwd = $this->input->post('user_pwd', TRUE);
        if ($user_pwd) {
            $data['user_pwd'] = md5($user_pwd . '1314');
        }
     //   echo $user_pwd;die;
        if($register=='register'){
            if($user_pwd==''){
             echo '<script type="text/javascript">alert("密码不能为空");history.back();</script>';
                die;    
            }
        }
        $data['user_email'] = $this->input->post('user_email', TRUE);
        $data['user_access'] = $this->input->post('user_access', true);
        $data['user_remark'] = $this->input->post('user_remark', true);
        $data['add_time'] = time();
        $res = $this->Page_register_model->save_register_info($data);
        if ($res) {
            $type=$this->input->get('type',true);
            if(!$type){
            if ($user_pwd && $data['user_id']) {
                //用户信息编辑，密码修改成功
                echo '<script type="text/javascript">alert("密码修改成功,请重新登录");location.href="' . base_url('home/user/login') . '";</script>';
                die;
            } else {
                //用户信息，修改成功
                echo '<script type="text/javascript">alert("用户信息修改成功");location.href="' . base_url('home/user/user_edit/' . $data['user_id']) . '";</script>';
                die;
            }
            }
            $datas['u_user_id'] = $this->input->post('u', TRUE);
            if ($datas['u_user_id']) {
                $datas['user_id'] = $res;
                $datas['u_page_id'] = $this->input->post('p', true);
                $datas['add_time'] = time();
                $red = $this->Page_u_user_page_model->save_u_user_page_info($datas);
            }
            $where = array(
                'user_id' => $res,
            );
            $rec = $this->Page_register_model->get_register_detail($where);
            unset($res['add_time']);
            unset($res['update_time']);
            unset($res['is_del']);
            $this->_user_business($rec);
            setcookie('user_id', $res['user_id'], time() + 31536000);
            setcookie('user_mobile', $res['user_mobile'], time() + 31536000);
            setcookie('user_pwd', $res['user_pwd'], time() + 31536000);
            echo '<script type="text/javascript">location.href="' . base_url('home/user/page_list') . '";</script>';
        }
    }

    //登录表单提交
    public function sign() {
        $user_mobile = $this->input->post('user_mobile', TRUE);
        $pwd = md5($this->input->post('user_pwd', TRUE) . '1314');
        $where = array(
            'user_mobile' => $user_mobile,
            'user_pwd' => $pwd,
        );
        $res = $this->Page_register_model->get_register_detail($where);
      
        if (!$res) {
            echo '<script type="text/javascript">alert("登录失败，用户名或密码错误");history.back();</script>';
            die;
        }
        if ($res['is_del'] == 1) {
            echo '<script type="text/javascript">alert("此帐号已被禁用！");history.back();</script>';
            die;
        }
        if ($res['is_del'] == '0') {
            unset($res['add_time']);
            unset($res['update_time']);
            unset($res['is_del']);
            $this->_user_business($res);
            setcookie('user_id', $res['user_id'], time() + 31536000);
            setcookie('user_mobile', $res['user_mobile'], time() + 31536000);
            setcookie('user_pwd', $res['user_pwd'], time() + 31536000);
            $role=$this->Page_role_model->get_role_detail(array('role_id'=>$res['role_id'],'is_del'=>0));

          if(strpos($role['access_list'],'3003')){
            echo '<script type="text/javascript">location.href="' . base_url('cg/car_home/group_list') . '";</script>';
          }else{
           echo '<script type="text/javascript">location.href="' . base_url('home/user/col_list') . '";</script>'; 
          }
            
        }
    }

// 员工管理   ps:管理员权限
    public function user_list($business_id = "") {
        $user = $this->_is_login();
        $access = $this->_is_access('4001');
        $data['access_list'] = $access['access_list'];
        if ($this->_role($user['role_id'])) { //坐享其成管理员
            $business_id = $business_id ? $business_id : 0;
        } else {      //商户管理员 --
            $business_id = $user['business_id'];
        }
        if ($business_id >= 1) {
            $where = array('is_del' => 0, 'business_id' => $business_id, 'order_by' => 'user_id desc');
        } else {
            $where = array('is_del' => 0, 'order_by' => 'user_id desc');
        }
        $data['list'] = $this->Page_register_model->get_register_list($where);
        foreach ($data['list'] as $k => $v) {
            $where = array('group_id' => $v['group_id'], 'is_del' => 0);
            $group = $this->Page_group_model->get_group_detail($where);
            if ($group) {
                $data['list'][$k]['group'] = $group['group_name'];
            } else {
                $data['list'][$k]['group'] = '默认部门';
            }
            $where = array('role_id' => $v['role_id'], 'is_del' => 0);
            $role = $this->Page_role_model->get_role_detail($where);
            if ($role) {
                $data['list'][$k]['role'] = $role['role_name'];
            } else {
                $data['list'][$k]['role'] = '初始权限';
            }
        }
        if (!$data['list']) {
            echo "未找到对应员工";
            die;
        } else {
            $data['title'] = "员工管理";
            $user['title'] = $data['title'];
            $data['head'] = $this->_head($user);
            $this->load->view('home/user/userlist', $data);
        }
    }

    // 员工信息编辑页面
    public function staff_edit($user_id) {
        $where = array('user_id' => $user_id, 'is_del' => 0);
        $data['list'] = $this->page_register_model->get_register_detail($where);
        $data['staff_edit_url'] = base_url('home/user/add_register'); //ps:编辑表单提交路径，要带user_id
        $this->load->view('usadmin/home/staff_edit', $data);
    }

// 判断发送给销售的账户是否存在
    public function xx_user() {
        $user = $this->_is_login();
        $business_id = $user['business_id'];
        $m = $this->input->post('m', true); //手机号
        $u = $this->input->post('u', true); // 上线ID
        $p = $this->input->post('p', true); // 发送页面ID
        $where = array('user_id' => $u); // 上线id判断姓名
        $res = $this->Page_user_model->get_user_detail($where);
        $salername = $res['user_name']; //姓名
        $where = array('page_id' => $p);
        $res = $this->Page_info_model->get_page_info_detail($where);
        $title = $res['short_title'];
        $where = array('user_mobile' => $m);
        $res = $this->Page_user_model->get_user_detail($where);
        if ($res['is_del'] == 1) {
            $result['code'] = 1;
            $result['msg'] = "此账号已被禁用";
            return $this->ajax_return($result);
        }
        if ($res) {
            $data['user_id'] = $res['user_id'];
            $data['u_user_id'] = $u;
            $data['u_page_id'] = $p;
            $where = array('user_id' => $data['user_id'], 'u_page_id' => $data['u_page_id'], 'is_del' => 0); //判断是否已经转发过该页面  如果有转发则不能再发送
            $parent = $this->Page_u_user_page_model->get_u_user_page_detail($where);
            if ($parent) {
                $result['code'] = 1;
                $result['msg'] = "该用户已拥有此页面，请勿重复转发";  //转发失败
                return $this->ajax_return($result);
            }
            $where = array('u_user_id' => $data['user_id'], 'user_id' => $data['u_user_id'], 'u_page_id' => $data['u_page_id'], 'is_del' => 0);  // 判断是否父子级互相转发 // 子级不能转发给父级已有的页面。转发流程 1-2， 2-3， 3-4 ****———— 如4--3失败
            $parent = $this->Page_u_user_page_model->get_u_user_page_detail($where);
            if ($parent['is_level'] > 1) {
                $result['code'] = 1;
                $result['msg'] = "该用户已拥有此页面，请勿重复转发";
                return $this->ajax_return($result);
            } else {
                $where = array('u_page_id' => $data['u_page_id'], 'u_user_id' => $data['user_id'], 'is_del' => 0);   //判断是否 转发给最父级以上， 转发流程 1-2， 2-3， 3-4 ****———— 如4--2， 4-1 失败
                $parent = $this->Page_u_user_page_model->get_u_user_page_detail($where);
                if ($parent) {
                    $result['code'] = 1;
                    $result['msg'] = "该用户已拥有此页面，请勿重复转发";  //转发失败
                    return $this->ajax_return($result);
                }
            }
            $where = array('user_id' => $data['user_id'], 'u_user_id' => $data['u_user_id'], 'u_page_id' => $data['u_page_id'], 'is_del' => 0); // 判断是否重复转发页面给子级。 相同页面 只能转发一个子级用户一次。
            $rds = $this->Page_u_user_page_model->get_u_user_page_detail($where);
            if ($rds) {
                $result['code'] = 1;
                $result['msg'] = "已转发过页面，请勿重复转发";
                return $this->ajax_return($result);
            }
            // 第一级子分类
            $data['is_level'] = +1;  // 判断是否是首次转发 如首次转发 等级1
            //下一级子页面
            $where = array('user_id' => $data['u_user_id'], 'u_page_id' => $data['u_page_id'], 'is_del' => 0);
            $level = $this->Page_u_user_page_model->get_u_user_page_detail($where);
            if ($level) {
                $data['is_level'] = $level['is_level'] + 1;  // 多次流转转发 N+1
            }
            $data['add_time'] = time();
            $seller = $this->Page_u_user_page_model->save_u_user_page_info($data);
            //转发成功
            $smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => $m, //接受短信的用户手机号码
                'tpl_id' => '61475', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#salername#=' . $salername . '&#title#=' . $title . '&#names#=' . 'http://api.etjourney.com/home/user/login' . '' //您设置的模板变量，根据实际情况修改
            );

            //      $content = $this->_sendsms($smsConf, 1); //请求发送短信

            $result['code'] = 0;
            $result['seller'] = $seller;
            $result['msg'] = "已短信通知手机:" . $m; //转发成功
            return $this->ajax_return($result);
        } else {
            //同行非会员
            $result['msg'] = base_url("home/user/register/" . "?m=$m-$u-$p");
            $smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => $m, //接受短信的用户手机号码
                'tpl_id' => '61475', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#salername#=' . $salername . '&#title#=<' . $title . '>&#names#=' . $result['msg'] //您设置的模板变量，根据实际情况修改
            );
            $content = $this->_sendsms($smsConf, 1); //请求发送短信
            $result['code'] = 2;
            //      $result['msg'] = "发送失败";

            $result['msg'] = '<img src="' . base_url('H5info_temp_zyx/qrcode_info?name=') . $result['msg'] . '"><p><br>
			已短信通知:' . $m . '，对方注册后即可拥有该页面。<br>
			<span class="f14" style="color:#999;">若对方长时间未收到短信，请长按保存或转发二维码(注册链接)给同行注册</span></p>';
            return $this->ajax_return($result);
        }
    }

//订单提交
    public function order_add() {
        $data['order_sn'] = 'TL' . date('Ymd', time()) . rand(100000, 999999);
        $data['good_name'] = $this->input->post('good_name', TRUE);
        $data['good_list'] = json_encode($this->input->post('good_list', TRUE));
        $data['good_list'] = $this->input->post('good_list', TRUE);
        $data['seller_list'] = $this->input->post('seller_list', TRUE);
        $data['doc_list'] = json_encode($this->input->post('doc_list', TRUE));
        $data['order_cid'] = $this->input->post('cId', TRUE);
        $data['order_price'] = $this->input->post('order_price', TRUE);
        $data['order_remark'] = $this->input->post('order_remark', TRUE);
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['order_name'] = $this->input->post('order_name', true);
        $data['order_mobile'] = $this->input->post('order_mobile', TRUE);
        $data['business_id'] = $this->_is_login("business_id");
        $data['add_time'] = time();
        $res = $this->Page_order_model->save_order_info($data);
        $url = base_url('usadmin/package_user_list/ok');
        if ($res) {
            echo "<script language=javascript>alert('提交成功');location.href='$url';</script>";
            die;
        }
    }
    //订单展示
    public function order_list() {
        $user = $this->_is_login();
        $type = $this->input->get('type', true);
        $data['param']['type'] = $type;
        $data['title'] = $user['title'] = "订单列表";
        $data['head'] = $this->_head($user);
        $where = "business_id=$user[business_id] AND seller_list=$user[user_id] AND is_del=0";
        $data['list'] = $this->User_model->get_select_all($select = '*', $where, 'order_id', 'ASC', 'tl_order_info');
        //   echo $this->db->last_query();die;
        foreach ($data['list'] as $k => $v) {
            $where = array('user_id' => $v['seller_list'], 'is_del' => 0);
            $xx = $this->Page_user_model->get_user_detail($where); //拿取销售员信息
            $data['list'][$k]['xx_name'] = $xx['user_mobile'];
            $where = array('user_id' => $v['seller_list'], 'u_page_id' => $v['page_id'], 'is_del' => 0, 'order_by' => 'id asc');
            $names[] = $this->Page_u_user_page_model->get_u_user_page_detail($where);
            foreach ($names as $k1 => $v1) {
                $where = array('user_id' => $v1['u_user_id'], 'is_del' => 0);
                $name_xx = $this->Page_user_model->get_user_list($where);
                $data['list'][$k]['xs_name'] = $name_xx;
            }
        }
        $this->load->view('home/user/orderlist', $data);
    }

// 商户页面展示
    public function page_list($business_id = '') {
        $user = $this->_is_login();
        $data['user_id'] = $user['user_id'];
        $type = $this->input->get('type', true);
        $data['type'] = $type;
        // 获取用户部门
        $group = $this->Page_user_model->get_user_detail(array('user_id' => $data['user_id'], 'is_del' => 0));
        $g_name = $this->Page_group_model->get_group_detail(array('group_id' => $group['id'], 'is_del' => 0));
        $data['group_name'] = $g_name['group_name'];
        $business_id = $business_id ? $business_id : $user['business_id'];
        //获取用户 Business_name
        $bname = $this->Business_info_model->get_business_info_detail(array('business_id' => $user['business_id']));
        $data['business_name'] = $bname['business_name'];
        $data['business_del'] = $bname['is_del'];
        if ($user['business_id'] == 1) {//自己是坐享其成-
            $where = array('is_show' => 0, 'business_id' => $business_id, 'is_release' => 0, 'order_by' => 'page_id desc');
            $sql = array('business_type' => 1);
            $data['business_del'] = 0;
        } elseif ($user['business_id'] == $business_id) {//自己所在商户
            $where = array('is_show' => 0, 'business_id' => $business_id, 'is_release' => 0, 'order_by' => 'page_id desc');
            $sql = array('is_del' => 0, 'business_type' => 1);
            if ($user['business_id'] == 67396) {//自己是其他商户
                $where = array('is_show' => 0, 'is_eye' => 1, 'is_release' => 0, 'order_by' => 'page_id desc');
                $sql = array('is_del' => 0, 'business_type' => 1);
            }
        } else {//同行商户
            $where = array('is_show' => 0, 'business_id' => $business_id, 'is_release' => 0, 'is_eye' => 1, 'order_by' => 'page_id desc');
            $sql = array('is_del' => 0, 'business_type' => 1);
        }
        //商户列表
        $data['blist'] = $this->Business_info_model->get_business_info_list($sql);
        foreach ($data['blist'] as $k => $v) {
            $blist[] = $v['business_id'];
            //$data['business_name'] =($user['business_id']=$v['business_id'])?$v['business_name']:'';
            $data['blist'][$k]['class'] = $this->Class_type_model->get_class_list(array('is_del' => 0, 'business_id' => $v['business_id']));
        }

        $data['title'] = "H5页面管理";
        $user['title'] = $data['title'];
        $data['user'] = $user;
        $data['head'] = $this->_head($user);
        //超级管理员    
        //旅行社管理员
        $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        //  echo "<pre>";
        //  print_r($data['param']);die;
        if (!empty($data['param']['cid'])) {
            $where['class_name'] = $data['param']['cid'];
        }
        if (!empty($data['param']['keyword'])) {
            unset($where['business_id']);
            if (is_numeric($data['param']['keyword'])) {
                $where['like'] = array('page_id' => $data['param']['keyword']);
            } else {
                $where['like'] = array('page_title' => $data['param']['keyword']);
            }
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
        if ($type == 2) {
            $where['group_id'] = $group['group_id'];
            unset($where['is_eye']);
            $data['title'] = "我的产品";
        }

        $where ['limit'] = 15;
        $where['order_by'] = 'page_id desc';
        $data['page_info_list'] = $this->Page_info_model->get_page_info_list($where);
//echo $this->db->last_query();die;
        $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        if ($data['_pagination']) {
            echo $v['class_name'];
            foreach ($data['page_info_list'] as $k => $v) {
                //不在blist中 并且 不是自己商户 并且自己不是坐享其成 过滤 查询结果
                if ($user['business_id'] != $business_id && $user['business_id'] != 1 && !in_array($v['business_id'], $blist)) {
                    break;
                }
                $where = array('page_id' => $v['page_id'], 'user_id' => $data['user_id'], 'is_del' => 0);
                $sg = $this->Page_collection_model->get_collection_detail($where);
                $data['list'][$k] = json_decode($v['image_data'], TRUE);
                $data['list'][$k]['short_title'] = $v['short_title'];
                $data['list'][$k]['page_title'] = $v['page_title'];
                $data['list'][$k]['add_time'] = date('Y-m-d', $v['add_time']);
                $data['list'][$k]['page_id'] = $v['page_id'];
                $data['list'][$k]['op_mobile'] = $v['op_mobile'];
                $data['list'][$k]['th_price'] = $v['th_price'];
                $data['list'][$k]['is_eye'] = $v['is_eye'];
                // 查询页面部门
                $where = array('group_id' => $v['group_id'], 'is_del' => 0);
                $groupname = $this->Page_group_model->get_group_detail($where);
                $data['list'][$k]['group_name'] = $groupname['group_name'];
                if ($sg) {
                    $data['list'][$k]['collection'] = 1;
                } else {
                    $data['list'][$k]['collection'] = 0;
                }
                $data['list'][$k]['id'] = $sg['id'];
                $op = $this->Page_user_model->get_user_detail(array('user_mobile' => $v['op_mobile'], 'is_del' => 0));
                if ($op) {
                    $data['list'][$k]['op_wx'] = $op['user_wx'];
                    $data['list'][$k]['op_name'] = $op['user_name'];
                }
                $where = array('business_id' => $v['business_id']);
                $bus = $this->Business_info_model->get_business_info_detail($where);
                $data['list'][$k]['business_name'] = $bus['business_name'];
                if ($v['page_type'] == 1) {
                    $data['list'][$k]['url'] = base_url('home/package_tour/view/' . $v['page_id']) . '/' . $bus['share_name'] . $v['page_id'];
                } else if ($v['page_type'] == 2) {
                    $data['list'][$k]['url'] = base_url('home/package_travel/view/' . $v['page_id']) . '/' . $bus['share_name'] . $v['page_id'];
                }
                // $data['list'][$k]['uplaoder']=$v['uplaoder'];  
            }
            //调用模版
            $data['xx_url'] = base_url('home/user/xx_user');
            // 同行页面
            $type = $this->input->get('type', true);
            $data['type'] = $type;
            if ($type == 1) {
                $data['list'] = array();
                $where = array('user_id' => $user['user_id'], 'is_del' => 0, 'limit' => 15, 'order_by' => 'id desc');
                $u_list = $this->Page_u_user_page_model->get_u_user_page_list($where);
                $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
                foreach ($u_list as $k1 => $v1) {
                    $where = array('page_id' => $v1['u_page_id'], 'is_show' => 0);
                    $data['u_list'][] = $this->Page_info_model->get_page_info_detail($where);
                    foreach ($data['u_list'] as $k => $v) {
                        $data['list'][$k] = json_decode($v['image_data'], TRUE);
                        $data['list'][$k]['short_title'] = $v['short_title'];
                        $data['list'][$k]['page_title'] = $v['page_title'];
                        $data['list'][$k]['add_time'] = date('Y-m-d', $v['add_time']);
                        $data['list'][$k]['page_id'] = $v['page_id'];
                        $data['list'][$k]['th_price'] = $v['th_price'];
                        $data['list'][$k]['is_eye'] = $v['is_eye'];
                        // 查询页面部门
                        $where = array('group_id' => $v['group_id'], 'is_del' => 0);
                        $groupname = $this->Page_group_model->get_group_detail($where);
                        $data['list'][$k]['group_name'] = $groupname['group_name'];
                        $where = array('business_id' => $v['business_id']);

                        $bus = $this->Business_info_model->get_business_info_detail($where);
                        if ($v['page_type'] == 1) { //跟团游
                            $data['list'][$k]['url'] = base_url('home/package_tour/view/' . $v['page_id']) . '/' . $bus['share_name'] . $v['page_id'];
                        } else if ($v['page_type'] == 2) { //自由行
                            $data['list'][$k]['url'] = base_url('home/package_travel/view/' . $v['page_id']) . '/' . $bus['share_name'] . $v['page_id'];
                        }
                        $data['list'][$k]['uplaoder'] = $v['uplaoder'];
                        $data['list'][$k]['business_name'] = $bus['business_name'];
                    }
                }
                foreach ($data['list'] as $k => $v) {

                    $where = array('user_id' => $user['user_id']);
                    $u_list = $this->Page_u_user_page_model->get_u_user_page_list($where);


                    //     echo $this->db->last_query();
                    foreach ($u_list as $ke1 => $va1) {


                        $where = array('user_id' => $va1['u_user_id'], 'is_del' => 0);
                        $names = $this->Page_user_model->get_user_detail($where);


                        if ($names) {
                            $data['list'][$ke1]['names'] = $names['user_name'];
                            $data['list'][$ke1]['mobile'] = $names['user_mobile'];
                            $where = array('business_id' => $names['business_id'], 'is_del' => 0);
                            $bus = $this->Business_info_model->get_business_info_detail($where);
                        }
                    }
                }
            }

            $user = $this->_is_login();
            $data['business_id'] = $business_id;
            $this->load->view('home/user/pagelist', $data);
        }
    }

    //旧页面
    public  function page_old_list($business_id = '') {
        $user = $this->_is_login();
            $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
		$data['business_id']=$user['business_id'];
        $where = array('business_id' => $user['business_id']);
        $bus = $this->Business_info_model->get_business_info_detail($where);
        $data['business_name']=$bus['business_name'];
		if($user['business_id'] == 1){//自己是坐享其成-
			$where = array('is_show' => 0, 'business_id'=>$business_id, 'limit' => 15, 'order_by' => 'page_id desc');
			$sql = array( 'business_type' => 1);
			$data['business_del'] = 0;
		}elseif($user['business_id'] == $business_id){//自己所在商户
			$where = array('is_show' => 0, 'business_id'=>$business_id, 'limit' => 15, 'order_by' => 'page_id desc');
			$sql = array('is_del' => 0, 'business_type' => 1);
		}else{//同行商户
			$where = array('is_show' => 0, 'business_id'=>$business_id, 'is_eye'=>1, 'limit' => 15, 'order_by' => 'page_id desc');
			$sql = array('is_del' => 0, 'business_type' => 1);
		}
        //商户列表
        $data['blist'] = $this->Business_info_model->get_business_info_list($sql);
        foreach ($data['blist'] as $k => $v) {
			 $data['blist'][$k]['class'] = $this->Class_type_model->get_class_list(array('is_del' => 0, 'business_id' => $v['business_id']));
        }
		if (!empty($data['param']['cid'])) {
            $where['class_name'] = $data['param']['cid'];
        }
		if (!empty($data['param']['per_page'])) {
            $where['offset'] = $data['param']['per_page'];
        }
        $data['list']=$this->Page_old_model->get_old_list($where);
		 foreach ($data['list'] as $k => $v) {
			$data['list'][$k]['share'] = '/public/'. substr($v['dir'],strrpos($v['dir'],'/')+1).'/images/share.jpg';
			$data['list'][$k]['top'] = '/public/'. substr($v['dir'],strrpos($v['dir'],'/')+1).'/images/head.jpg';
			$data['list'][$k]['short_title'] = $v['page_title'];
            $data['list'][$k]['page_title'] = $v['page_title'];
			$data['list'][$k]['business_name'] = $data['business_name'];
			$data['list'][$k]['op_name'] = '';
            $data['list'][$k]['add_time'] = date('Y-m-d', $v['add_time']);
            $data['list'][$k]['page_id'] = $v['page_id'];
            $data['list'][$k]['th_price'] = $v['th_price'];
            $data['list'][$k]['is_eye'] = $v['is_eye'];
		 }
        $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        $data['user_name']=$user['user_name'];
		$data['user_id']=$user['user_id'];
        $data['title']='旧版';
		//echo '<pre>'; print_r($data);die;
       $this->load->view('home/user/pagelist', $data);
        
    }
//切换状态
public function set_eye(){
   $data['page_id']=$this->input->post('page_id',TRUE);
    $data['is_eye']=$this->input->post('is_eye',TRUE);
    $res=$this->Page_info_model->save_page_info($data);
    if ($res) {
            $result['code'] = 200;
            $result['msg'] = "切换成功!";
            return $this->ajax_return($result);
        } else {
            $result['code'] = 400;
            $result['msg'] = "切换失败!";
            return $this->ajax_return($result);
        }
    }
//添加收藏
//
    public function get_collection() {
        $user = $this->_is_login();
        $data['user_id'] = $user['user_id']; // 取出用户id
     //   $password = $this->_password();
        if ($data['user_id']) {
            $data['page_id'] = $this->input->post('page_id'); //要收藏的页面id
            $where = array('user_id' => $data['user_id'], 'page_id' => $data['page_id'], 'is_del' => 0);
            $res = $this->Page_collection_model->get_collection_detail($where);
            if ($res) {
                $result['code'] = 400;
                $result['msg'] = "该页面已被收藏!";
                return $this->ajax_return($result);
            } else {
                $data['add_time'] = time();
                $resc = $this->Page_collection_model->save_collection_info($data);
                if ($resc) {
                    $result['code'] = 200;
                    $result['msg'] = "收藏成功!";
                    $result['id'] = $resc;
                    return $this->ajax_return($result);
                }
            }
        } else {
            $result['code'] = 200;
            $result['msg'] = "登录超时,请重新登录!";
            return $this->ajax_return($result);
        }
    }

    public function col_list() {
        $user = $this->_is_login();
        $data['user_id'] = $user['user_id']; // 取出用户id
		$data['user_name'] =$user['user_name']; 
        $data['title'] = $user['title'] = "我的收藏";
        //$data['head']=$this->_head($user);
        $password = $this->_password();
        if ($data['user_id']) {
			$bname = $this->Business_info_model->get_business_info_detail(array('business_id' => $user['business_id']));
			$data['business_name'] =$bname['business_name']; 
            $where = array('user_id' => $data['user_id'], 'is_del' => 0);
            $res = $this->Page_collection_model->get_collection_list($where);
            //遍历数组 取出page_id
         //   print_r($res);
            foreach ($res as $k => $v) {
                $where = array('page_id' => $v['page_id'],'is_eye'=>1, 'is_show' => 0);
				if( $user['business_id'] == $v['business_id']){//自己商户全部可见
				//	$where = array('page_id' => $v['page_id'], 'is_show' => 0);
				}
                $red = $this->Page_info_model->get_page_info_detail($where);
               // echo  .'-';
                if(count($red)==0){ 
                	unset($res[$k]);
                } else{              
                $res[$k] = json_decode($red['image_data'], TRUE);
                $res[$k]['short_title'] = $red['short_title'];
                 $res[$k]['page_title'] = $red['page_title'];
                $res[$k]['add_time'] = date('Y-m-d', $v['add_time']);
                $res[$k]['page_id'] = $red['page_id'];
                $res[$k]['op_mobile'] = $red['op_mobile'];
                $res[$k]['th_price'] = $red['th_price'];
                $res[$k]['collection'] = 1;
                $op = $this->Page_user_model->get_user_detail(array('user_mobile' => $red['op_mobile'], 'is_del' => 0));
                if ($op) {
                    $res[$k]['op_wx'] = $op['user_wx'];
                    $res[$k]['op_name'] = $op['user_name'];
                }
                $where = array('business_id' => $red['business_id']);
                $bus = $this->Business_info_model->get_business_info_detail($where);
                if ($red['page_type'] == 1) { //跟团游
                    $res[$k]['url'] = base_url('home/package_tour/view/' . $red['page_id']) . '/' . $bus['share_name'] . $red['page_id'];
                } else if ($v['page_type'] == 2) { //自由行
                    $res[$k]['url'] = base_url('home/package_travel/view/' . $red['page_id']) . '/' . $bus['share_name'] . $red['page_id'];
                }
                $res[$k]['uplaoder'] = $red['uplaoder'];
                $res[$k]['business_name'] = $bus['business_name'];
                if ($red['class_name'] == 0) {
                    $res[$k]['class_name'] = '无';
                } else {
                    $where = array('id' => $red['class_name'], 'is_del' => 0);
                    $type = $this->Class_type_model->get_class_detail($where);
                    $res[$k]['class_name'] = $type['class_name'];
                }
      
                }          

            }
             $data['list'] = $res;
            $this->load->view('home/user/pagelist', $data);

          
        } else {
            $url = base_url('home/user/login');
            echo "<script language=javascript> location.href='$url';</script>";
        }
    }

    public function del_collection() {
        $user = $this->_is_login();
        $user['user_id'] = $user['user_id']; // 取出用户id
        $password = $this->_password();
        if ($user['user_id']) {
            $page_id = $this->input->post('page_id', TRUE);
            $res = $this->User_model->update_one(array('page_id' => $page_id, 'user_id' => $user['user_id']), array('is_del' => '1'), $table = 'tl_collection');
            if ($res) {
                $result['code'] = 200;
                $result['msg'] = "已取消";
                $result['id'] = $res;

                return $this->ajax_return($result);
            }
        } else {
            $result['code'] = 400;
            $result['msg'] = "登录超时，请重新登录";
            return $this->ajax_return($result);
        }
    }
// 发送投诉建议邮件
    public  function set_email(){
            $user = $this->_is_login();
        $user_name = $user['user_name']; 
        $user_mobile=$user['user_mobile'];
        $content=$this->input->post('content',true);
        if(!$content){
             $result['code'] = 400;
            $result['msg'] = "请填写内容";
            return $this->ajax_return($result); 
        }
        $message='用户名：'.$user_name.'-'.'手机号码: '.$user_mobile.'建议内容:'.$content;
        $this->_sendmail($message);
         $result['code'] = 200;
            $result['msg'] = "发送成功";
            return $this->ajax_return($result);
    }
// 判断cookie 用户名密码是否正确
    private function _password() {
        $user_mobile = $_COOKIE['user_mobile'];
        $user_pwd = $_COOKIE['user_pwd'];
        $where = array('user_mobile' => $user_mobile, 'user_pwd' => $user_pwd, 'is_del' => 0);
        $res = $this->Page_user_model->get_user_detail($where);
        return $res;
    }

    // 取用户信息数据
    private function _is_login($user = "") {
        if (empty($user)) {
            $list = $_SESSION;
        } else {
            $list = $this->session->userdata($user);
        }
        if (!$list['user_id']) {
            $cookie = $this->_password();
            if (!empty($cookie)) {
                $this->session->set_userdata($cookie);
                $list = $_SESSION;
            } else {
                $url = base_url('home/user/login');
                header("Location: $url");
            }
        }

        return $list;
    }

    // 退出登录
    public function login_out() {
//       $this->session->unset_userdata($array_items);//销毁某一个
		setcookie("user_id");
		setcookie("user_mobile");
		setcookie("user_pwd");
        $res = $this->session->sess_destroy();
        $url = base_url('home/user/login');
        header("Location: $url");
    }

// 取用户权限数据
    private function _is_access($role) {
        $where = array(
            'role_id' => $_SESSION['role_id'],
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

// 用户信息存储
    private function _user_business($list) {
        $user = $this->session->set_userdata($list);
        return $user;
    }

//  //调用头部
    private function _head($list = array()) {
        $data['title'] = $list['title'];
        $data['user_name'] = $list['user_name'];
        $data['register'] = base_url('home/user/user_edit/' . $list['user_id']);
        $data['loginout'] = base_url('home/user/login_out');

        $page_head['head'] = $this->load->view('home/user/head', $data, true);

        return $page_head;
    }

//    超级管理员
    private function _role($id) {
        if ($id > 1) {
            return FALSE;
        } else {
            return true;
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
    private function _tour($page_id) {
        $data['tour'] = array(
            '0' => array(
                'url' => base_url('usadmin/package_tour/page_edit/' . $page_id),
                'title' => '基础内容',
            ),
            '1' => array(
                'url' => base_url('usadmin/package_tour/page_trip/' . $page_id),
                'title' => '行程编辑',
            ),
            '2' => array(
                'url' => base_url('usadmin/package_tour/page_price/' . $page_id),
                'title' => '价格上传'
            ),
            '3' => array(
                'url' => base_url('usadmin/page_poster/index/' . $page_id),
                'title' => '头图编辑'
            ),
        );

        return $data['tour'];
    }

    //自由行导航栏菜单

    private function _free($page_id) {
        $data['tour'] = array(
            '0' => array(
                'url' => base_url('usadmin/package_tour/free_page_edit/' . $page_id),
                'title' => '基础内容',
            ),
            '1' => array(
                'url' => base_url('usadmin/package_tour/article/' . $page_id),
                'title' => '产品编辑',
            ),
            '2' => array(
                'url' => base_url('usadmin/package_tour/addmenu/' . $page_id),
                'title' => '导航编辑'
            ),
            '3' => array(
                'url' => base_url('usadmin/page_poster/index/' . $page_id),
                'title' => '头图编辑'
            ),
        );

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

    public function arrays() {
//        $data['page_id']=74;
//        $h5_video=array(
//          '0'=>array(
//              'video'=>'http://image.etjourney.com/us/111.mp4',
//              'image'=>'http://image.etjourney.com/video_image/111.png',  
//          ), 
//            '1'=>array(
//               'video'=>'http://image.etjourney.com/us/222.mp4',
//              'image'=>'http://image.etjourney.com/video_image/222.png',   
//          ) , 
//                '2'=>array(
//             'video'=>'http://image.etjourney.com/us/333.mp4',
//              'image'=>'http://image.etjourney.com/video_image/333.png',  
//          ) ,
//    
//    
//);
//        $data['data_video']=  json_encode($h5_video);
//    $this->Page_info_model->save_page_info($data);                
//    }
        $data['url'] = base_url('usadmin/package_tour/xiuxiu');
        $this->load->view('usadmin/page_monitor/xiuxiu', $data);
    }

    public function xiuxiu() {
        $data['page_id'] = 120;
        if (isset($_FILES['Filedata']) && $_FILES['Filedata']['error'] == 0) {
            $data['h5_image'] = $this->upload_image('Filedata', 'H5image');
        }
        $xiuxiu = $this->Page_info_model->save_page_info($data);
        if ($xiuxiu) {
            return $this->ajax_return(上传成功);
        }
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
        private function _sendmail($message) {
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成'); //发件人
        $this->email->subject('来自H5：产品管理助手投诉建议');
        $this->email->to('806191111@qq.com');
        $this->email->message($message);
        $this->email->send();
    }

}
