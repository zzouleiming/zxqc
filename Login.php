<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
	function __construct(){
		parent::__construct() ;
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('session');
    	$this->load->model('Users_model');
    	$this->load->model('Tel_verification_model');
	}
	/**
	 * Account:登录页
	 */
	public function index()
	{
		//验证是否已登录
		if($this->check_login())
		{
			redirect(base_url('buyer_index'));
		}
		$data['about_us']=base_url('buyer_index/about_us');
        $data['bus_in']=base_url('Seller_merchant/busin');
        $data['fk_url']=base_url('buyer_index/fk_log_view');
		$data['error'] = $this->session->flashdata('error');
		unset($_SESSION['error']);
		$this->load->view('login/login',$data);
		$this->load->view('footer',$data);
	}

	/**
	 * Account:s首页
	 *
	 */
	public function act_login(){
		//设置验证规则
		$this->form_validation->set_rules('username', 'username', 'trim|required');
		$this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]');
		// $this->form_validation->set_rules('captcha_code', 'captcha_code', 'trim|required');

		//验证数据合法性
		if ($this->form_validation->run() == FALSE)
        {
        	$data['message'] = "输入信息错误";
        	echo "输入信息错误";
            exit();
        }
        $username = $this->input->post('username',true);
        $password = md5($this->input->post('password',true));
        // $captcha_code = strtoupper($this->input->post('captcha_code',true));

        //验证码合法性
    	// $time=time();
    	// if(!isset($this->session->captcha_code) OR $captcha_code!=$this->session->captcha_code OR ($time-$this->session->captcha_time)>360){
     //    	$this->session->set_flashdata('error', array('position'=>'captcha_code','message'=>'验证码错误'));
     //    	redirect(base_url('login'));
     //    	exit();
    	// }
        //根据用户名查询用户信息
        $sql_search = "user_id,account,tel,user_name,password,image,address,pre_sign,register_time,login_time,sex,creates,grade,is_guide,is_attendant,
        				is_driver,is_merchant,is_trip_merchant,is_carmer,photo_starttime,photo_endtime,checkin_time,checkin_status,for_business_id";
        $sql_where =  array('account'=>$username);
        $user_info = $this->Users_model->search($sql_search,$sql_where);

        /*if(stristr($user_info[0]['image'], 'http')===false)
        {
            $user_info[0]['image'] = $this->config->item('productImgUrl') . ltrim($user_info[0]['image'],'.');
        }*/

        if(empty($user_info))
        {
        	$this->session->set_flashdata('error', array('position'=>'username','message'=>'用户名不存在'));
        	redirect(base_url('login'));
        	exit();
        }
        //验证密码
        if($user_info[0]['password'] != $password)
        {
        	$this->session->set_flashdata('error', array('position'=>'password','message'=>'密码错误'));
        	redirect(base_url('login'));
        	exit();
        }
        //设置SESSION
        unset($user_info[0]['password']);
        $shop_info=$this->Users_model->get_user_shop_info($user_info[0]['user_id']);
        if(count($shop_info)>1)
        {
            $this->session->set_userdata(array('shop_info'=>$shop_info));
        }
        $this->session->set_userdata(array('user_info'=>$user_info[0]));

        redirect(base_url('buyer_index'));
        
	}


	/**
	 * Account:退出登录
	 *
	 */
	public function logout(){
		//验证是否已登录
		if($this->check_login())
		{
            unset($_SESSION['user_info']);
            $this->session->sess_destroy();

		}
		redirect(base_url('login'));

	}

	/**
	 * Account:注册页面
	 */
	public function register(){
		//验证是否已登录
		if($this->check_login())
		{
			//redirect(base_url());
		}
		$data['about_us']=base_url('buyer_index/about_us');
        $data['bus_in']=base_url('Seller_merchant/busin');
        $data['fk_url']=base_url('buyer_index/fk_log_view');
		$data['error'] = $this->session->flashdata('error');
		unset($_SESSION['error']);
		$this->load->view('login/register',$data);
		$this->load->view('footer',$data);

	}

	/**
	 * Account:注册
	 *
	 */
	public function act_register(){
		//设置验证规则
		$this->form_validation->set_rules('username', 'username', 'trim|required');
		$this->form_validation->set_rules('pass', 'password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('pass_confirmation', 'confirmation', 'trim|required|min_length[6]');
		// $this->form_validation->set_rules('mobile', 'mobile', 'trim|required');
		// $this->form_validation->set_rules('captcha_code', 'captcha_code', 'trim|required');
		// $this->form_validation->set_rules('verification', 'verification', 'trim|required');
		//验证数据合法性
		if ($this->form_validation->run() == FALSE)
        {
        	echo "输入信息错误";
            exit();
        }
        $username = $this->input->post('username');
        $password = $this->input->post('pass_confirmation');
        $confirmation = $this->input->post('pass');
        // $mobile = $this->input->post('mobile');
        // $captcha_code = strtoupper($this->input->post('captcha_code',true));
        // $verification = $this->input->post('verification');
        //验证码合法性
    	$time=time();
    	// if(!isset($this->session->captcha_code) OR $captcha_code!=$this->session->captcha_code OR ($time-$this->session->captcha_time)>360){
     //    	$this->session->set_flashdata('error', array('position'=>'captcha_code','message'=>'验证码错误'));
     //    	redirect(base_url('login/register'));
     //    	exit();
    	// }
        //两次输入密码是否相同
        if($password != $confirmation)
        {
        	$this->session->set_flashdata('error', array('position'=>'confirmation','message'=>'两次密码不一致'));
        	redirect(base_url('login/register'));
            exit();
        }
		// //验证手机号码格式
		// if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[678]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobile))
		// {   
  //       	$this->session->set_flashdata('error', array('position'=>'mobile','message'=>'手机号码格式错误'));
  //       	redirect(base_url('login/register'));
  //       	exit();
  //       }
		// //验证手机验证码
		// $check_verif = $this->check_veif_code($mobile,$verification,2);
		// if(!$check_verif['success'])
		// {
  //       	$this->session->set_flashdata('error', array('position'=>'verification','message'=>$check_verif['msg']));
  //       	redirect(base_url('login/register'));
		// 	echo $check_verif['msg'];
  //       	exit();
		// }
        //根据用户名查询用户信息
        $sql_search = " user_id ";
        $sql_where =  array('account'=>$username);
        $user_info = $this->Users_model->search($sql_search,$sql_where);
        if($user_info)
        {
        	$this->session->set_flashdata('error', array('position'=>'username','message'=>'用户名已存在'));
        	redirect(base_url('login/register'));
            exit();
        }
        // //查询手机号是否已注册
        // $sql_search = " user_id ";
        // $sql_where =  array('tel'=>$mobile);
        // $user_info = $this->Users_model->search($sql_search,$sql_where);
        // if($user_info)
        // {
        // 	$this->session->set_flashdata('error', array('position'=>'mobile','message'=>'该手机号已注册'));
        // 	redirect(base_url('login/register'));
        //     exit();
        // }

        //验证通过生成新用户
        $time = time();
		$openid = md5($password.$time);
		$param = array(
				'account'   => $username,
				'user_name' => $username,
				'password'  => md5($password),
				'tel'       => $mobile,
				'image'     => '/tmp/avatar.png',
				'openid'    => $openid,
				'register_time' => $time,
				'login_time'    => $time,
				'sex'			=> '2',
				'regist_type'	=> '6'
				);
		$param['user_id']=$this->Users_model->add($param);
        //设置SESSION
        $sql_search = "user_id,account,tel,user_name,password,image,address,pre_sign,register_time,login_time,sex,creates,grade,is_guide,is_attendant,
        				is_driver,is_merchant,is_trip_merchant,is_carmer,photo_starttime,photo_endtime,checkin_time,checkin_status,for_business_id";
        $sql_where =  array('user_id'=>$param['user_id']);
        $user_info = $this->Users_model->search($sql_search,$sql_where);
        unset($user_info[0]['password']);
        $this->session->set_userdata(array('user_info'=>$user_info[0]));

        redirect(base_url('buyer_index'));

	}

	/**
	 * Account:手机找回密码
	 *
	 */
	public function find_pwd_by_phone(){

		$data['about_us']=base_url('buyer_index/about_us');
        $data['bus_in']=base_url('Seller_merchant/busin');
        $data['fk_url']=base_url('buyer_index/fk_log_view');
		$data['error'] = $this->session->flashdata('error');
		unset($_SESSION['error']);
		$this->load->view('login/findPwdByPhone',$data);
		$this->load->view('footer',$data);
	}

	/**
	 * Account:手机找回密码
	 *
	 */
	public function act_find_pwd_by_phone(){
		//设置验证规则
		$this->form_validation->set_rules('username', 'username', 'trim|required');
		$this->form_validation->set_rules('mobile', 'mobile', 'trim|required');
		$this->form_validation->set_rules('captcha_code', 'captcha_code', 'trim|required');
		$this->form_validation->set_rules('verification', 'verification', 'trim|required');
		//验证数据合法性
		if ($this->form_validation->run() == FALSE)
        {
        	echo "输入信息错误";
            exit();
        }
        $username = $this->input->post('username');
        $mobile = $this->input->post('mobile');
        $captcha_code = strtoupper($this->input->post('captcha_code',true));
        $verification = $this->input->post('verification');
        //验证码合法性
    	$time=time();
    	if(!isset($this->session->captcha_code) OR $captcha_code!=$this->session->captcha_code OR ($time-$this->session->captcha_time)>360){
        	$this->session->set_flashdata('error', array('position'=>'captcha_code','message'=>'验证码错误'));
        	redirect(base_url('login/find_pwd_by_phone'));
        	exit();
    	}
		//验证手机号码格式
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[678]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobile))
		{   
        	$this->session->set_flashdata('error', array('position'=>'mobile','message'=>'手机号码格式错误'));
        	redirect(base_url('login/find_pwd_by_phone'));
        	exit();
        }
		//验证手机验证码
		$check_verif = $this->check_veif_code($mobile,$verification,1);
		if(!$check_verif['success'])
		{
        	$this->session->set_flashdata('error', array('position'=>'verification','message'=>$check_verif['msg']));
        	redirect(base_url('login/find_pwd_by_phone'));
        	exit();
		}
        //根据用户名查询用户信息
        $sql_search = " user_id,tel,regist_type ";
        $sql_where =  array('account'=>$username,'regist_type'=>'6');
        $user_info = $this->Users_model->search($sql_search,$sql_where);
        if($user_info)
        {
        	//验证绑定手机号
        	if($user_info[0]['tel'] != $mobile)
        	{
        		$this->session->set_flashdata('error', array('position'=>'mobile','message'=>'手机号码与绑定的手机号码不一致'));
        		redirect(base_url('login/find_pwd_by_phone'));
           		exit();
        	}
        }else{
        	$this->session->set_flashdata('error', array('position'=>'username','message'=>'用户名不存在'));
        	redirect(base_url('login/find_pwd_by_phone'));
           	exit();
        }
        //验证通过设置修改密码相关SESSION
        $this->session->set_userdata(array('fp_name'=>$username,'fp_mobile'=>$mobile,'fp_verification'=>$verification));

        redirect(base_url('login/find_pwd'));

	}

	/**
	 * Account:找回密码
	 *
	 */
	public function find_pwd(){
		$data['title'] = "重置密码" ;
		//验证是否存在合法SESSION信息
		if(!$this->session->fp_name || !$this->session->fp_mobile || !$this->session->fp_verification)
		{
        	echo "没有修改权限";
           	exit();
		}

		$data['about_us']=base_url('buyer_index/about_us');
        $data['bus_in']=base_url('Seller_merchant/busin');
        $data['fk_url']=base_url('buyer_index/fk_log_view');
		$this->load->view('login/findPwd',$data);
		$this->load->view('footer',$data);
	}

	/**
	 * Account:找回密码
	 *
	 */
	public function act_find_pwd(){
		$time = time();
		//设置验证规则
		$this->form_validation->set_rules('pass', 'password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('pass_confirmation', 'confirmation', 'trim|required|min_length[6]');
		//验证数据合法性
		if ($this->form_validation->run() == FALSE)
        {
        	echo "输入信息错误";
            exit();
        }
        $password = $this->input->post('pass_confirmation');
        $confirmation = $this->input->post('pass');
        //两次输入密码是否相同
        if($password != $confirmation)
        {
        	echo "两次密码不一致";
            exit();
        }
		//验证是否存在合法SESSION信息
		if(!$this->session->fp_name || !$this->session->fp_mobile || !$this->session->fp_verification)
		{
        	echo "没有修改权限";
           	exit();
		}
		$username = $this->session->fp_name;
		$mobile = $this->session->fp_mobile;
		$verification = $this->session->fp_verification;
        //根据用户名查询用户信息
        $sql_search = "user_id,account,tel,user_name,password,regist_type";
        $sql_where =  array('account'=>$username,'regist_type'=>'6');
        $user_info = $this->Users_model->search($sql_search,$sql_where);
        if(empty($user_info))
        {
        	echo "用户名不存在";
        	exit();
        }
        elseif($user_info[0]['tel'] != $mobile )
        {
        	echo "手机号码与绑定的手机号码不一致";
        	exit();
        }
        //验证修改密码合法性（是否有手机验证信息）
        $verif_info = $this->Tel_verification_model->get_verif_info_by_tel($mobile,1,1);
        if($verif_info)
        {
        	if($verif_info[0]['verification'] != $verification || $verif_info[0]['expire_time'] < $time)
        	{
        		echo "修改权限过期";
           		exit();
        	}
        }else{
        	echo "没有修改权限";
           	exit();
        }
		//验证通过修改密码
		$param = array('password'=>md5($password));
		$sql_where = array('user_id'=>$user_info[0]['user_id']);
		$this->Users_model->update($param,$sql_where);
		//删除相关SESSION
		$this->session->unset_userdata(array('fp_name','fp_mobile','fp_verification'));

		redirect(base_url('login/change_success'));

	}

	/**
	 * Account:找回密码
	 *
	 */
	public function change_success(){

		$data['about_us']=base_url('buyer_index/about_us');
        $data['bus_in']=base_url('Seller_merchant/busin');
        $data['fk_url']=base_url('buyer_index/fk_log_view');
		$this->load->view('login/changeSuccess');
		$this->load->view('footer',$data);
	}
	/**
	 * User: weiwei
	 * Data：2016/11/30
	 * Account:生成验证码
	 */
	public function get_captcha(){
   		$this->load->library('captcha');
    	$captcha_code = $this->captcha->getCaptcha();
    	$this->session->set_userdata(array('captcha_code'=>strtoupper($captcha_code),'captcha_time'=>time()));
    	$this->captcha->showImg();
	}

	/**
	 * Account:获取手机验证码
	 *
	 */
	public function get_verif_code(){
        $mobile = $this->input->post('mobile');
        $type = $this->input->post('type');
		//验证手机号码格式
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[678]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobile))
		{   
        	echo "手机号码格式错误";
        	exit();
        }
        $verif_type = $type == 'register' ? 2 : 1;
		$time = time();
        //查询验证码是否已存在
        $verif_info = $this->Tel_verification_model->get_verif_info_by_tel($mobile,$verif_type);
		//die(json_encode(array('value'=>1)));
		//die(json_encode($verif_info));
        
        if($verif_info)
        {
        	if($verif_info[0]['verification'] && intval($verif_info[0]['create_time']) > $time-60)
			{
				echo '验证码申请间隔太短';
			}
        }else{
        	$res = $this->get_code($mobile,$verif_type);
        	echo '验证码发送成功';
        }

	}

	/**
	 * Account:用户协议
	 */
	public function agreement(){
		$this->load->view('agreement');
	}

	/**
	 * Account:验证登录状态
	 */
	public function check_login(){
		if(isset($this->session->user_info) && isset($this->session->user_info['user_id']))
		{
			return true;
		}
		return false;
	}

	/**
	 * Account:请求获取手机验证码
	 */
	function get_code($mobile,$type='',$user_id=0){
     	$ch = curl_init();
     	$time = time();
		//验证码
        $code = rand(111111,999999);
		//短信内容
		$content = '【坐享其成】您的验证码是'.$code.'，有效时间5分钟，请不要告诉他人';
		$content = urlencode($content);
		//返回值格式 1为xml，2为json；默认为1
		$tag = 2;
	    $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile='.$mobile.'&content='.$content.'&tag='.$tag;
		//echo $url;
	    $header = array(
	        'apikey:1ba343c8f992b5c7857afe6ac8850d2f',
	    );
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $res = curl_exec($ch);
	    $res = json_decode($res);
	    if($res->returnstatus == 'Success')
	    {
			$ins = array(
				'user_id'        => $user_id,
				'tel'            => $mobile,
				'verification'   => $code,
				'verif_type'     => $type,
				'create_time'    => $time,
				'expire_time'    => $time+300
				);
			$this->Tel_verification_model->add($ins);
			return true;
	    }else{
			return false;
	    }
	}

	/**
	 * Account:验证手机验证码
	 */
	function check_veif_code($mobile,$verification,$type=''){
		$result = array();
		$time = time();
        $result['success'] = false;
        $verif_info = $this->Tel_verification_model->get_verif_info_by_tel($mobile,$type);
        if(!$verif_info)
        {
        	$result['msg'] = "验证码不正确";
        	return $result;
        }else{
        	if($verif_info[0]['verification'] != $verification)
        	{
        		$result['msg'] = "验证码不正确";
        		return $result;
        	}
        	if($verif_info[0]['expire_time'] < $time)
        	{
        		$result['msg'] = "验证码已过期";
        		return $result;
        	}
        	//更新验证码信息表
			$sql_update = array(
						'is_checked'   => 1,
						'check_time'   => $time
						);
			$sql_where = array('id'=>$verif_info[0]['id']);
        	$this->Tel_verification_model->update($sql_update,$sql_where);
        }
        $result['success'] = true;
        return $result;
	}



}
