<?php
/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
define('SESSION_KEY_PRE', 'us');
class Business extends MY_Controller
{
	public function __construct() {
        parent::__construct();

        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
        $this->load->model('Business_account_role_model');

        $this->load->helper('url');

        $this->load->library('MY_Session');
    }

    //登录界面
    public function login() {
    	$data['do_login_url'] = base_url('usadmin/business/do_login');
        $data['captcha_url'] = base_url('usadmin/business/get_captcha');
        $data['index_url'] = base_url('usadmin/package_tour');
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if( $business_account_id ){
            redirect(base_url('usadmin/package_tour'));
        }else{
            $this->load->view('usadmin/business/login', $data);
        }
    }

    //登录验证
    public function do_login() {
 
        $result = array();
        $code = $this->my_session->get_session('code', SESSION_KEY_PRE);
        $this->my_session->del_session('code', SESSION_KEY_PRE);
   
        $captcha = strtoupper($this->input->post('code', true));
        $account = $this->input->post('user', true);
        $password = $this->input->post('pwd', true);      
        if( empty($captcha) or empty($account) or empty($password) ) {
            //非法登录
            $result['code'] = 2;
            $result['msg'] = "非法登录";
            return $this->ajax_return($result);
        }
        $where = array(
            'business_account '=> $account
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if( !$business_account || $business_account['password'] != md5($password.$business_account['salt']) ){
            $result['code'] = 3;
            $result['msg'] = "用户不存在或者密码错误";
            return $this->ajax_return($result);
        }
        if( $business_account['status'] == 1){
            $result['code'] = 4;
            $result['msg'] = "用户被禁止登录";
            return $this->ajax_return($result);
        }
        if( !$code or $captcha!=$code ) {
            //验证码错误或者过期
            $result['code'] = 1;
            $result['msg'] = "验证码错误或者过期";
            return $this->ajax_return($result);
        }
        $this->my_session->set_session('business_account_id', $business_account['business_account_id'], SESSION_KEY_PRE);
        $this->my_session->set_session('business_id', $business_account['business_id'], SESSION_KEY_PRE);
        $this->my_session->set_session('business_account', $business_account['business_account'], SESSION_KEY_PRE);
        $this->my_session->set_session('role_id', $business_account['role_id'], SESSION_KEY_PRE);

        $business_account_data = array(
            'business_account_id' => $business_account['business_account_id'],
            'login_time' => time()
        );
        $this->Business_account_model->save_business_account($business_account_data);
        $result['code'] = 5;
        $result['msg'] = "登录成功";
        return $this->ajax_return($result);
    }

    //退出登录
    public function login_out() {
        $this->my_session->del_session('business_account_id', SESSION_KEY_PRE);
        $this->my_session->del_session('business_id', SESSION_KEY_PRE);
        $this->my_session->del_session('business_account', SESSION_KEY_PRE);
        $this->my_session->del_session('role_id', SESSION_KEY_PRE);
        redirect(base_url('usadmin/business/login'));
    }

    //修改密码
    public function pwd_edit() {
        $data['business_account'] = $this->_get_auth();
        $data['url'] = base_url('usadmin/business/do_pwd_edit');
        
        $this->load->view('usadmin/business/pwd_edit', $data);
    }
    
    //密码修改
    public function do_pwd_edit() {
        $business_account = $this->_get_auth();
        
        $password_old = $this->input->post('password_old', TRUE);
        $password_new = $this->input->post('password_new', TRUE);

        if( md5($password_old.$business_account['salt']) == $business_account['password'] ){
            $salt = rand(1,9999);
            $password = md5($password_new.$salt);
            $business_account_data = array(
                'business_account_id' => $business_account['business_account_id'],
                'password' => $password,
                'salt' => $salt
            );
            $this->Business_account_model->save_business_account($business_account_data);
            $this->my_session->del_session('business_account_id', SESSION_KEY_PRE);
        }
        redirect(base_url('usadmin/business/login'));
    }
    
    //切换旅行社
    public function change_business(){
        $result = array();
        $business_id = $this->input->post('business_id', true);
        if(!$business_id){
            $result['code'] = 1;
            $result['msg'] = "发生错误，操作失败";
            return $this->ajax_return($result);
        }
        $business_info = $this->Business_info_model->get_business_info_detail($where);
        if(empty($business_info)){
            $result['code'] = 1;
            $result['msg'] = "发生错误，操作失败";
            return $this->ajax_return($result);
        }
        $this->my_session->set_session('business_id', $business_id, SESSION_KEY_PRE);
        $result['code'] = 0;
        $result['msg'] = "操作成功";
        return $this->ajax_return($result);
    }

    //验证码
    public function get_captcha() {
        $this->load->library('captcha');
        $code = $this->captcha->getCaptcha();
        $this->my_session->set_session('code', strtoupper($code), SESSION_KEY_PRE);
        $this->captcha->showImg();
    }

    //获取所有后台商户
    private  function _get_business_all($type=1){
        $where = array(
            'business_type' => $type
        );
        $business_all = $this->Business_info_model->get_business_info_list($where);
        return $business_all;
    }

    //校验登录
    private function _get_auth() {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if(!$business_account_id){
            redirect(base_url('usadmin/business/login'));
        }
        $where = array(
            'business_account_id' => $business_account_id
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if(empty($business_account)){
            redirect(base_url('usadmin/business/login'));
        }
        $business_id = $this->my_session->get_session('business_id', SESSION_KEY_PRE);
        if(!$business_id){
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

    //检查权限
    private function _check_role(){
        $role_id = $this->my_session->get_session('role_id', SESSION_KEY_PRE);
        $where = array(
            'role_id' => $role_id
        );
        $business_account_role = $this->Business_account_role_model->get_business_account_role_detail($where);
        if($business_account_role['role_rules'] == 'all'){
            return true;
        }
        $roles = explode(',', $business_account_role['role_rules']);
        $router_method = $this->router->directory.$this->router->class.'/'.$this->router->method;
        if(in_array($router_method, $roles)){
            return true;
        }
        exit("没有权限");
    }
}
