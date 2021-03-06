<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Api201 extends My_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
	    parent::__construct();
	    $this->table = 'v_users';
	    $this->load->model('User_Api_model');
	    $this->load->model('User_model');
	    $this->load->model('Admin_model');
	    $this->load->library('uploadimg');
	    //$this->load->library('imagick');
	    $this->load->library('common');
		$this->load->library('image_lib');
		$this->load->library('session');
	    //$this->load->library('waf');
	    $this->load->helper(array('form', 'url'));
	    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
	//	unset($_COOKIE);
	   //签名
	    $this->sign();
	}


	/**
	 * [register 获取配置参数]
	 * @return [type] [description]
	*/
	public function get_config()
	{
		$data['banner_on'] = $this->config->item('banner_on');
		$this->data_back($data,'0X000','success');
	}
	/**
	 * [register 获取推流地址，端口号]
	 * @return [type] [description]
	*/
	public function get_publish()
	{
		$user_id  = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) :'';
		if(!$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$time = time();
		$video_name = $user_id . '_' . $time;
		$auth_key = $this->get_auth($video_name);
		$data['video_name'] = $video_name;
		if($this->config->item('rtmp_flg') == 0)
		{
			$data['publish_url'] = 'rtmp://42.121.193.231/hls/'.$video_name;
		}
		elseif($this->config->item('rtmp_flg') == 1)
		{
			$data['publish_url'] = $this->config->item('publish_url').$video_name.'?auth_key='.$auth_key.'&vhost=video.etjourney.com';
		}
		elseif($this->config->item('rtmp_flg') == 2)
		{
			$data['publish_url'] = $this->config->item('publish_uc_url').$video_name.'?record=true';
		}
		
		$this->data_back($data,'0X000','success');
		
	}
	
	/**
	 * [register 鉴权签名]
	 * @return [type] [description]
	*/
	public function get_auth($video_name,$type='')
	{
		$result = '';
		if($video_name)
		{
			if($type)
			{
				$video_name .= $type;
			}
			$end  = intval(substr($video_name,-10)) + 86400;
			$para = $end . '-0-0-';
			$sign = md5('/etjourney/' . $video_name . '-' . $para .$this->config->item('cdn_key'));
			$result = $para.$sign;
		}
		return $result;
	}

	/**
	 * [register 授权注册 --请求接口]
	 * @return [type] [description]
	*/
	public function register()
	{	
		$type       = isset($_REQUEST['type']) ? trim($_REQUEST['type']) :'';
		$data  = array();
		if(empty($type))
		{
			$this->data_back('type为空.授权失败','0X001','fail');
		}
		if($type == 'weixin' || $type == 'qq' || $type == 'twitter' || $type == 'facebook' || $type == 'weibo')
		{
			$openid     = isset($_REQUEST['openid']) ? trim($_REQUEST['openid']) :'';
			$nickname   = isset($_REQUEST['nickname']) ? trim(addslashes($_REQUEST['nickname'])) :'';
			$headimgurl = isset($_REQUEST['headimgurl']) ? trim($_REQUEST['headimgurl']) :'';
			$sex        = isset($_REQUEST['sex']) ? intval(trim($_REQUEST['sex'])) :0;
			switch ($type) {
				case 'weixin':
					$regist_type = '1';
					break;
				case 'weibo':
					$regist_type = '2';
					break;
				case 'qq':
					$regist_type = '3';
					break;
				case 'facebook':
					$regist_type = '4';
					break;
				case 'twitter':
					$regist_type = '5';
					break;
				default:
					$regist_type = '';
					break;
				}
			if(!$openid)
			{
				$this->data_back('openid.为空授权失败','0X001','fail');
			}
			$where = array('openid'  => $openid,'regist_type' => $regist_type);
			if($sex == 2 || $sex==1)
			{
				$sex1 = '1';
			}
			else{ 
				$sex1 = '0';
			}
			$count = $this->User_Api_model->count_all($where,'v_users');
			//根据IP获取地址
			$ip = $this->common->real_ip();
			$address = $this->common->GetIpLookup($ip);
			if(empty($count))
			{
				//$nickname=$this->User_Api_model->repeat_user_name($nickname);
				$param = array(
						'account'   => $nickname,
						'user_name' => $nickname,
						'openid'    => $openid,
						'register_time' => time(),
						'login_time'    => time(),
						'image'			=> $headimgurl,
						'sex'			=> $sex1,
						'address'		=> $address,
						'regist_type'	=> $regist_type
						);
				$data['user_id'] = $this->User_Api_model->insert_string($param,'v_users');
			}
			else
			{	
				$param['openid'] = $openid;
				$param['address'] = $address;
				$param['login_time'] = time();
				$data = $this->User_Api_model->login($param,'v_users','weixin');
			}
			$data['openid'] = $openid;
			$data['base_url'] = $this->config->item('base_url');
		}
		elseif($type == 'tel')
		{
			//手机号码
			$mobile     = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) :0;
			if(!$mobile)
			{
				$this->data_back('手机号不能为空','0X005','fail');
			}
			if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$mobile))
			{   
    			$this->data_back('手机号格式不对','0X003','fail');
         	}
         	$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) :0;
         	if(empty($code))
			{
				$this->data_back('验证码不能为空','0X005','fail');
			}
			//5分钟验证码过期
			$time = time() - 300;
	    	$count = $this->User_Api_model->comment_count(' COUNT(*) AS count,login_time '," user_name='$mobile' AND froms='tel' AND openid='$code'", 'v_users');
	    	$param['user_name']     = $mobile;
			if(empty($count[0]['count']))
			{
				$this->data_back('验证码不正确','0X006','fail');
			}
			else{
				if($count[0]['login_time'] < $time)
				$this->data_back('验证码已过期','0X007','fail');
		
				$param['login_time'] = time();
				$data = $this->User_Api_model->login($param,'v_users','tel');
			}
		}
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? trim($_REQUEST['sys']) :4;
			$count = $this->User_model->get_count(" user_id=$data[user_id] AND device_id='$device_id' ",'v_device');
			if(empty($count['count']))
			{
				$device = array(
				'user_id'   => $data['user_id'],
				'device_id' => $device_id,
				'type'      => $sys);
				$this->User_Api_model->insert_string($device,'v_device');
			}
			else
			{
				$this->User_Api_model->comment_update(array('user_id'=>$data['user_id'],'device_id'=>$device_id),array('login'=>0),'v_device');
			}
			
		}
		$this->data_back($data,'0X000');
	}
	
	/**
	 * [register 一般注册 --请求接口]
	 * @return [type] [description]
	*/
	public function common_register()
	{
		$user_name = isset($_REQUEST['user_name']) ? htmlspecialchars(trim(urldecode($_REQUEST['user_name']))): '';
		$password = isset($_REQUEST['password']) ? htmlspecialchars(trim(urldecode($_REQUEST['password']))) : '';
		$confirm_password = isset($_REQUEST['confirm_password']) ? htmlspecialchars(trim(urldecode($_REQUEST['confirm_password']))) : '';
		$email    = isset($_REQUEST['email']) ? htmlspecialchars(trim(urldecode($_REQUEST['email']))) : '';
		$tel = isset($_REQUEST['tel']) ? htmlspecialchars(trim(urldecode($_REQUEST['tel']))) : '';
		$language = isset($_REQUEST['language']) ? htmlspecialchars(trim(urldecode($_REQUEST['language']))) : '';
		$verification = isset($_REQUEST['verification']) ? htmlspecialchars(trim(urldecode($_REQUEST['verification']))) : '';

		$device=$_REQUEST['device_id'];
		if($device=='AmAghnl6NvxwHBgVdYyU0BlWfEsrDrrwvqzGlEQ7TRnu'){
			return false;
		}
		if(empty($user_name) || empty($password) || empty($confirm_password) || empty($tel) || empty($verification))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if($password!=$confirm_password){
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back('密码不一致，请重新输入','0X011','fail');
				}elseif($language == 'zh_HK' || $language == 'zh_TW')
				{
					$this->data_back('密碼不一致，請重新輸入','0X011','fail');
				}else{
					$this->data_back('Password is not consistent, please re-enter!','0X011','fail');
				}
			}else{
				$this->data_back('密码不一致，请重新输入','0X011','fail');
			}
		}
		$where = array('account' => $user_name);
		$count = $this->User_Api_model->count_all($where,'v_users');
		if($count)
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back('该用户名已存在','0X011','fail');
				}elseif($language == 'zh_HK' || $language == 'zh_TW')
				{
					$this->data_back('該用戶名已存在','0X011','fail');
				}else{
					$this->data_back('User name already exists,choose another user name!','0X011','fail');
				}
			}else{
				$this->data_back('该用户名已存在','0X011','fail');
			}
		}
		//检查验证码
        $verif_info = $this->User_Api_model->comment_select('id,user_id,tel,verification,expire_time'," user_id=0 AND tel='$tel' AND verif_type=2 AND is_checked=0 ",'',' id DESC ',0,1,'v_tel_verification');
        if(!$verif_info)
        {
			$this->data_back('验证码不正确','0X011','fail');
        }else{
        	if($verif_info[0]['verification'] != $verification)
        	{
				$this->data_back('验证码不正确','0X011','fail');
        	}
        	if($verif_info[0]['expire_time'] < $time)
        	{
				$this->data_back('验证码已过期','0X011','fail');
        	}
        	//更新验证码信息表
			$param_ver = array(
						'is_checked'   => 1,
						'check_time'   => $time
						);
        	$this->User_Api_model->comment_update(array('id'=>$verif_info[0]['id']),$param_ver,'v_tel_verification');
        }
		$time = time();
		//根据IP获取地址
		$ip = $this->common->real_ip();
		$address = $this->common->GetIpLookup($ip);
		$openid = md5($password.$time);
		$param = array(
				'account'   => $user_name,
				'user_name' => $user_name,
				'password'  => md5($password),
				'tel'       => $tel,
				'image'     => $this->config->item('default_avatar'),
				'openid'    => $openid,
				'register_time' => $time,
				'login_time'    => $time,
				'sex'			=> '2',
				'address'		=> $address,
				'regist_type'	=> '6'
				);
		$data['user_id'] = $this->User_Api_model->insert_string($param,'v_users');
		$data['openid'] = $openid;
		
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? trim($_REQUEST['sys']) :'4';
			if($device_id)
			{
				$count = $this->User_model->get_count(" user_id=$data[user_id] AND device_id = '$device_id' ",'v_device');
				if(empty($count['count']))
				{
					$device = array(
					'user_id'   => $data['user_id'],
					'device_id' => $device_id,
					'type'      => $sys);
					$this->User_Api_model->insert_string($device,'v_device');
				}
				else
				{
					$this->User_Api_model->comment_update(array('user_id'=>$data['user_id'],'device_id'=>$device_id),array('login'=>0),'v_device');
				}
			}
		}
		$this->data_back($data,'0X000');
	}
	
	/**
	 * [register 一般登录 --请求接口]
	 * @return [type] [description]
	*/
	public function common_login()
	{
		$user_name = isset($_REQUEST['user_name']) ? htmlspecialchars(trim(urldecode($_REQUEST['user_name']))): '';
		$password = isset($_REQUEST['password']) ? htmlspecialchars(trim(urldecode($_REQUEST['password']))) : '';
		$language = isset($_REQUEST['language']) ? htmlspecialchars(trim(urldecode($_REQUEST['language']))) : '';
		if($user_name=='灵魂引路人')
		{
			return false;
		}
		if(empty($user_name) || empty($password))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data = array();
		//$where = array('user_name' => $user_name,'password' => md5($password),'regist_type' => '6');
		$userinfo = $this->User_Api_model->comment_select('user_id,password,openid'," account='$user_name' AND regist_type='6' ",'','',0,1,'v_users');
		if(empty($userinfo))
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back('用户不存在','0X011','fail');
				}elseif($language == 'zh_HK' || $language == 'zh_TW')
				{
					$this->data_back('用戶不存在','0X011','fail');
				}else{
					$this->data_back('User is not exists!','0X011','fail');
				}
			}else{
				$this->data_back('用户不存在','0X011','fail');
			}
		}
		else
		{
			if($userinfo[0]['password'] != md5($password))
			{
				if($language)
				{
					if($language == 'zh')
					{
						$this->data_back('密码错误','0X011','fail');
					}elseif($language == 'zh_HK' || $language == 'zh_TW')
					{
						$this->data_back('密碼錯誤','0X011','fail');
					}else{
						$this->data_back('Password error!','0X011','fail');
					}
				}else{
					$this->data_back('密码错误','0X011','fail');
				}
			}
		}
		$param['user_name'] = $user_name;
		$param['password']  = md5($password);
		$param['login_time'] = time();
		$data = $this->User_Api_model->login($param,'v_users','common');
		$data['openid'] = $userinfo[0]['openid'];
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? intval($_REQUEST['sys']) :4;
			if($device_id)
			{
				$count = $this->User_model->get_count(" user_id=$data[user_id] AND device_id = '$device_id' ",'v_device');
				if(empty($count['count']))
				{
					$device = array(
					'user_id'   => $data['user_id'],
					'device_id' => $device_id,
					'type'      => strval($sys));
					$this->User_Api_model->insert_string($device,'v_device');
				}
				else
				{
					$this->User_Api_model->comment_update(array('user_id'=>$data['user_id'],'device_id'=>$device_id),array('login'=>0),'v_device');
				}
			}
			
		}
        if($data['user_id']==2025){
            //$data['user_id']=3056;
        }
		$this->data_back($data,'0X000');
		
	}
	/**
	 * [get_tel_verification 获取手机验证码]
	 * @url 
	 * @return [type] [description]
	*/
	public function get_tel_verification()
	{
		$user_name = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
		$tel   = isset($_REQUEST['tel'])   ? trim($_REQUEST['tel'])    : '';
		$act =  isset($_REQUEST['act'])   ? trim($_REQUEST['act'])     : '';
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$tel))
		{   
    		$this->data_back('请输入正确的手机号码','0X003','fail');
        }
        if(!$act)
        {
        	//密码找回用
        	if(!$user_name)
        	{
        		$this->data_back('请输入用户名','0X003','fail');
        	}
        	//查询用户名是否存在
        	$userinfo = $this->User_Api_model->comment_select('user_id,tel,password,openid'," account='$user_name' AND regist_type='6' ",'','',0,1,'v_users');
			if(empty($userinfo))
			{
				$this->data_back('该用户不存在','0X011','fail');
			}else{
				if(empty($userinfo[0]['tel']))
				{
					$this->data_back('未绑定手机','0X011','fail');
				}elseif($userinfo[0]['tel'] != $tel){
					$this->data_back('输入的手机号码与绑定手机号不一致','0X011','fail');
				}
			}
			$user_id=$userinfo[0]['user_id'];
			$verif_type = 1;
        }elseif($act == 'register'){
        	//新注册用户用
        	$user_id=0;
			$verif_type = 2;
        }

		$time = time();
        //查询验证码是否已存在
        $verif_info = $this->User_Api_model->comment_select('id,user_id,tel,verification'," user_id='$user_id' AND tel='$tel' AND verif_type='$verif_type' AND is_checked=0 AND expire_time>'$time' ",'',' id DESC ',0,1,'v_tel_verification');
        
        if($verif_info)
        {
        	if($verif_info[0]['verification'] && intval($verif_info[0]['create_time']) > $time-60)
			{
				$this->data_back('验证码申请间隔太短','0X011','fail');
			}
        }else{
     		$ch = curl_init();
			//验证码
        	$code = rand(111111,999999);
			//短信内容
			$content = '【坐享其成】您的验证码是'.$code.'，有效时间5分钟，请不要告诉他人';
			$content = urlencode($content);
			//返回值格式 1为xml，2为json；默认为1
			$tag = 2;
	    	$url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile='.$tel.'&content='.$content.'&tag='.$tag;
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
					'tel'            => $tel,
					'verification'   => $code,
					'verif_type'     => $verif_type,
					'create_time'    => $time,
					'expire_time'    => $time+300
					);
				$this->User_Api_model->insert_string($ins,'v_tel_verification');
	    	}else{
				$this->data_back('验证码发送失败，请重试','0X011','fail');
	    	}
        }
        $data['user_id'] = $user_id;
		$this->data_back($data,'0X000');
	}

	/**
	 * [password_modify 密码修改]
	 * @url 
	 * @return [type] [description]
	*/
	public function password_modify()
	{
		$password = isset($_REQUEST['password']) ? htmlspecialchars(trim(urldecode($_REQUEST['password']))) : '';
		$confirm_password = isset($_REQUEST['confirm_password']) ? htmlspecialchars(trim(urldecode($_REQUEST['confirm_password']))) : '';
		$verification = isset($_REQUEST['verification']) ? trim($_REQUEST['verification']) : '';
		$tel   = isset($_REQUEST['tel'])   ? trim($_REQUEST['tel'])     : '';
		$user_id   = isset($_REQUEST['user_id'])   ? intval($_REQUEST['user_id'])     : '';
		if(!$user_id || !$password || !$confirm_password || !$verification || !$tel)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if($password!=$confirm_password)
		{
			$this->data_back('密码不一致，请重新输入','0X011','fail');
		}
        //查询用户名是否存在
        $userinfo = $this->User_Api_model->comment_select('user_id,tel,password,openid'," user_id='$user_id' AND regist_type='6' ",'','',0,1,'v_users');
		if(empty($userinfo))
		{
			$this->data_back('该用户不存在','0X011','fail');
		}
		$time = time();
		//检查验证码
        $verif_info = $this->User_Api_model->comment_select('id,user_id,tel,verification,expire_time'," user_id='$user_id' AND tel='$tel' AND verif_type=1 AND is_checked=0 ",'',' id DESC ',0,1,'v_tel_verification');
        if(!$verif_info)
        {
			$this->data_back('验证码不正确','0X011','fail');
        }else{
        	if($verif_info[0]['verification'] != $verification)
        	{
				$this->data_back('验证码不正确','0X011','fail');
        	}
        	if($verif_info[0]['expire_time'] < $time)
        	{
				$this->data_back('验证码已过期','0X011','fail');
        	}
        	//更新验证码信息表
			$param_ver = array(
						'is_checked'   => 1,
						'check_time'   => $time
						);
        	$this->User_Api_model->comment_update(array('id'=>$verif_info[0]['id']),$param_ver,'v_tel_verification');
        	//更新用户密码
			$param_user = array(
						'password'   => md5($password)
						);
        	$this->User_Api_model->comment_update(array('user_id'=>$user_id),$param_user,'v_users');
        	$data['user_id'] = $user_id;
        	$data['openid'] = $userinfo[0]['openid'];
			$this->data_back($data,'0X000');
        }
	}

	/**
	 * [show_start 直播开始]
	 * @url http://video.dhdyz.com/index.php/Api/show_start?user_id=5&title=jaijfa&adv_id=111
	 * @return [type] [description]
	*/
	public function show_start()
	{  
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';
		$title   = isset($_REQUEST['title'])   ? trim($_REQUEST['title'])     : '';
		$adv_id = isset($_REQUEST['adv_id']) ? intval($_REQUEST['adv_id']) : '';
		$act_id = isset($_REQUEST['act_id']) ? $_REQUEST['act_id'] : 0;
		$act_shop_id = isset($_REQUEST['act_shop_id']) ? intval($_REQUEST['act_shop_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		if(stristr($act_id,'p')){
			$act_shop_id=substr($act_id,1);
			$act_id=0;
		}
		//$location=$this->input->post_get('location',true);
		//if(!$location){
		//	$this->data_back('参数为空','0X011','fail');
		//}
        //$str="/^((-?(([1-9]?[0-9])|(1[0-7][0-9]))([.])?\d{3})|(180.000))[,]((-?(([1-8]?[0-9])|)([.])?\d{3})|(90,000))$/";
        //if(!preg_match($str,$location)){
        //    $this->data_back('经纬度有误','0x030','fail');
        //}
        $video_name   = isset($_REQUEST['video_name'])   ? trim($_REQUEST['video_name'])     : '';
		if(!$user_id || !$video_name)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//$user_info = $this->User_Api_model->user_info($user_id,'v_users');
		$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$user_id.'_user_info',$this->config->item('catch_time'));
		if(!$title)
		{
			$title = $user_info['user_name'] . '在直播';
		}
		$where['user_id'] = $user_id;
		$count = $this->User_Api_model->count_all($where,'v_follow');
		$address = '';
		$lct = explode(",",$location);
		$lat = $lct[0];
		$lng = $lct[1];
		if($location)
		{
			$lct = explode(",",$location);
			$lat = $lct[0];
			$lng = $lct[1];
			$position = $this->geocoder($lat,$lng);
			if($position)
			{
				$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
				$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
				$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
				if($city)
				{
					$address = $city;
				}elseif($province){
					$address = $province;
				}elseif($country){
					$address = $country;
				}
				$all_address=$country.$city;
			}else{
				$all_address='未知';
			}
		}else{
			$all_address='未知';
		}
		$param = array(
				'user_id'     => $user_id,
				'start_time'  => time(),
				'title'       => $title,
				'ip'          => $this->common->real_ip(),
				'location'	  => $location,
				'lat'	  => $lat,
				'lng'	  => $lng,
				'address'	  => $address,
				'all_address'	  => $all_address,
				'act_id'	  => $act_id,
				'act_shop_id'	  => $act_shop_id
				//'socket_info' => '42.121.5.3:2120'			
			);
			
		$ipinfo = $this->common->GetIpLookup($param['ip']);
		//是否有未关闭直播的信息
		$video_on = $this->User_Api_model->get_video_info($user_id,'v_video');
		if($video_on)
		{
			$this->User_Api_model->comment_update(array('user_id'=>$user_id,'is_off'=>0),array('is_off'=>1,'stop_time'=>time()),'v_video');
		}
		//推流模式
		if($this->config->item('rtmp_flg') == 2)
		{
			$param['push_type'] = 1;
		}
		//获取曾经直播过的信息
		//$show_time = $this->User_Api_model->get_video_count($user_id,'v_video');
		//if($show_time['count'] == 0)
		//{
		//	$info = 'insert';
		//}
		//else
		//{
		//	$info = 'update';
		//	$param['praise'] = 0;
		//}
		$video_id = $this->User_Api_model->start_info($user_id,$param,'v_video','insert');

		//var_dump($video_id);
		if($video_id)
		{
			if($adv_id)
			{
				$this->User_Api_model->update_watch(array('adv_id'=>$adv_id),array('video_id'=>$video_id),'v_video_advance');
			}
			//更新用户当前直播ID
			$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('video_id'=>$video_id),'v_users');
			//生成缩略图
			//$video_name = $video_id.'_'.time();
			$image = $this->config->item('base_dir').'/uploads/'.$video_id.'.jpg';
			$rtmp = $this->get_rtmp($video_name);
			$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 5 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
			exec($exec);
			$param['image'] = '/uploads/'.$video_id.'.jpg';
			$param['video_name'] = $video_name;
			//获取socket地址
			$socket = $this->get_socket($video_id,$user_id,$video_name);
			if($socket)
			{
				//$socket['socket_port']=2141;
				$param['socket_info'] = $socket['socket_ip'].':'.$socket['socket_port'];
			}
			$this->User_Api_model->start_info($user_id,$param,'v_video','update');
			$data['video_id'] = "$video_id";
			$data['count']    = "$count";
			$data['title']    = $title;
			$data['socket_ip']    = $socket['socket_ip'];
			$data['socket_port']  = $socket['socket_port'];
			$data['ipinfo']    = $ipinfo;

			$follow_list = $this->User_Api_model->comment_select('user_id'," fans_id=$user_id AND status='0' ",'','','','','v_follow',1);
			if(!empty($follow_list))
			{
				$sep = $user_list = '';
				foreach ($follow_list as $key => $value)
				{
					$user_list .= $sep . $value['user_id'];
					$sep = ',';
				}
				$device_list['user_name'] = $user_info['user_name'];
				$device_list['video_info'] = $this->getvideoinfo($user_id);
				$device_list['video_info']['rtmp'] = $rtmp;
				//$device_list['list']= $this->User_Api_model->comment_select(' device_id,type',"user_id IN ($user_list) ",'','','','','v_device',1);
				$device_list['list']= $this->User_Api_model->comment_select(' d.device_id,d.type',"d.login=0 AND d.user_id IN ($user_list)  ",'','','','','v_device AS d LEFT JOIN v_users AS u ON d.user_id=u.user_id',1);
				if(!empty($device_list))
				{
					$this->pushinfo($device_list,'show_start');
				}
			}				
			
			$this->data_back($data,'0X000','success');
		}
		else
		{
			$this->data_back('当前没有视屏','0X019','fail');
		}
	}

	/**
	 * [show_stop  直播结束]
	 * @url http://video.dhdyz.com/index.php/Api/show_stop?user_id=4&video_id=8
	 * @return [type] [description]
	*/
	public function show_stop()
	{

		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : '';
		//点赞数
		$praise = isset($_REQUEST['praise']) ? intval($_REQUEST['praise']) : 0;
		//分数
		$creates = isset($_REQUEST['creates']) ? intval($_REQUEST['creates']) : 0;
		//关注总数  从show_start 获取
		$follow_count = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 0;
		$time = time();
		$where['user_id'] = $user_id;
		//清除僵尸看客
		$this->User_Api_model->gag_del($data= array('watch'=>$video_id),$table = 'v_temp_users');
		$this->User_Api_model->update_video_zombie($video_id,'v_users');
		$count = $this->User_Api_model->count_all($where,'v_follow');
		$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id,'',2);
		if(empty($video_info))
		{
			$this->data_back('视频已结束','0X010','fail');
		}
		else
		{
			if($video_info['stop_time'] && $time - intval($video_info['stop_time']) > 60)
			{
				$this->data_back('视频已结束','0X010','fail');
			}
		}
		//关闭SOCKET
		if($video_id)
		{
			$this->User_Api_model->comment_update(array('video_id'=>$video_id),array('open_status'=>0,'video_id'=>0,'user_id'=>0,'start_time'=>0,'video_name'=>''),'v_socket');
			$cmd = "ps aux | grep '".$this->config->item('socket_server')." ".substr($video_info['socket_info'],-4)."' | grep -v grep | cut -c 9-15 | xargs kill -s 9";
			exec($cmd);
		}
		$creates_count = floor((($count- $follow_count)*2 + $creates + $video_info['views'])*0.02*($time-$video_info['start_time']));
		//直播超过3分钟增加3积分
		$credits = $time-intval($video_info['start_time']) >= 180 ? 3 : 0;
		//参加活动增加积分
		if($video_info['act_id'])
		{
			$credits = ceil($credits * 1.5);
		}
		$user_data  =array(
			'praise'  => $praise,
			'creates' => $creates_count,
			'credits' => $credits);
		$this->User_Api_model->praise_creates_update($user_id,$user_data,'v_users');
		//如果有点赞  更新 用户表和日榜、月榜、年榜表
		if($creates_count)
		{
			
			$data1 = array(
				'user_id'  => $user_id,
				'score'   => $creates_count,
				'dateline' => $time
				);

			//更新日榜
			$rank_day_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m%d %H'")),'v_rank_day');
			if($rank_day_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
			}
			$data1['time'] = date('n',$time);
			$this->User_Api_model->rank_day($data1,'v_rank_day',$param);
			//更新月榜
			$rank_month_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m%d'")),'v_rank_month');
			if($rank_month_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
				//$data1['param'] = "'%Y%m%d'";

			}
			$data1['time'] = date('d',$time);
			$this->User_Api_model->rank_month($data1,'v_rank_month',$param);
			
			//更新年榜
			$rank_year_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m'")),'v_rank_year');
			if($rank_year_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
				//$data1['param'] = "'%Y%m'";
			}
			$data1['time'] = date('m',$time);
			$this->User_Api_model->rank_month($data1,'v_rank_year',$param);
		}
		$num =$time-$video_info['start_time'];
		if($num <= 0 )
		{
			$data['start_time'] = "00:00:00";
		}
		else
		{
			$hour = floor($num/3600);
			$minute = floor(($num-3600*$hour)/60);
			$second = floor((($num-3600*$hour)-60*$minute)%60);
			if($hour < 10)
			{
				$hour = '0'.$hour;
			}
			if($minute < 10)
			{
				$minute = '0'.$minute;
			}
			if($second < 10)
			{
				$second = '0'.$second;
			}
			$data['start_time'] = $hour.':'.$minute.':'.$second;

		}
		$data['praise'] = "$praise";
		$data['title'] = "$video_info[title]";
		$data['views'] = "$video_info[views]";
		//$this->User_Api_model->gag_del(array('video_id'=>$video_id),'v_video');
		if($video_info['is_off'] != '3')
		{
			//if(!@fopen($this->config->item('record_url').$video_info['video_name'].'-live.m3u8','r'))
			//{
			//	$is_off = 2;
			//}
			//else
			//{
				$is_off = $time-intval($video_info['start_time']) >= 60 ? 1 : 2;
			//}
		}
		else
		{
			$is_off = intval($video_info['is_off']);
		}
		$this->User_Api_model->comment_update(array('video_id'=>$video_id),array('stop_time'=>$time,'is_off'=>$is_off,'is_rec'=>'0','display_order'=>30000),'v_video');
		$this->data_back($data,'0X000');
	}

	/**
	 * [praise 点赞接口]
	 * @url  http://video.dhdyz.com/index.php/Api/praise?praise_id=1
	 * @return [type] [ok]
	 */
	/*public function praise()
	{
		$praise_id = isset($_REQUEST['praise_id']) ? intval($_REQUEST['praise_id']) : '';
		//插入到视屏表
		$this->User_Api_model->praise_video($praise_id,'v_video');
		$this->User_Api_model->praise_video($praise_id,'v_users');
		echo json_encode(array('info'=>'ok'));
	}*/

	
	public function rank_info()
	{	
		$i=0;
		//$data[$i] = '时榜';
		//$i++;
		$data[$i] = '日榜';
		$i++;
		$data[$i] = '周榜';
		$i++;
		$data[$i] = '月榜';
		$i++;
		$data[$i] = '年榜';
		$i++;
		//$this->data_back($data,'0X000');
		$i=0;
		$data1[$i] = 'Day';
		$i++;
		$data1[$i] = 'Weekly';
		$i++;
		$data1[$i] = 'Monthly';
		$i++;
		if(stristr($_SERVER['HTTP_USER_AGENT'],'android')){
			$data1[$i] = 'Annual';
		}else{
			$data1[$i] = 'Annualy';
		}

		
		$i=0;
		$data2[$i] = '日次';
		$i++;
		$data2[$i] = '週次';
		$i++;
		$data2[$i] = '月次';
		$i++;
		$data2[$i] = '年次';
		
		$i=0;
		$data3[$i] = '일순위';
		$i++;
		$data3[$i] = '주순위';
		$i++;
		$data3[$i] = '월순위';
		$i++;
		$data3[$i] = '년순위';
		
		$i=0;
		$data4[$i] = 'เมื่อวันที่';
		$i++;
		$data4[$i] = 'สัปดาห์';
		$i++;
		$data4[$i] = 'เดือน';
		$i++;
		$data4[$i] = 'ปี';
		$data_arr = array('result'=>'success', 'msg'=>'0X000', 'info'=>$data,'info_en'=>$data1,'info_jp'=>$data2,'info_ko'=>$data3,'info_th'=>$data4);
        die(json_encode($data_arr));
	}
	/**
	 * [rank_list 排行榜]
	 * @ [type] [ 1 时   2 日  3 周  4  月   5 年]
	 * @return [type] [description]
	 */
	public function rank_list()
	{
		$time = time();
        $h = date('H',$time);
        $d = date('d',time());

        $param = "'%Y%m%d %H'";

		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 1;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$union   = isset($_REQUEST['union']) ? intval($_REQUEST['union']) : 0;
		$page_num =10;
	    //初始化查询条件
        $select = $where = $group_by = $order_by = '';
	    if($type == 1)
	    {
	    	
	    	//时排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-3600) ";
	    	$table    = 'v_rank_day';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_time';
	    }
	    elseif($type ==2)
	    {
	    	//日排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-86400) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_day';
	    	
	    }
	    elseif($type ==3)
	    {
	    	//周排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-604800) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_week';

	    }elseif($type ==4)
	    {
	    	//月排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-2592000) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_month';


	    }elseif($type ==5)
	    {
	    	//年排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-31536000) ";
	    	$table    = 'v_rank_year';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_year';

	    }
	    if(empty($count))
	    {
	    	$this->data_back('无排行榜', '0x000');  //返回数据
	    }
	    $start = ($page-1)*$page_num;
	    $data['list'] = $this->User_Api_model->rank_day_list($select,$where,$group_by,$order_by,$start,$page_num,$table,$this->config->item('catch'),$info,$this->config->item('catch_time'));
		if(!empty($data['list']))
		{
			foreach ($data['list'] as $key => $value) {
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),'v_users');
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['image'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['image'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
				}
				//用户等级认证信息
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['auth']);
				if(empty($union))
				{
					$select = "  COUNT(*) AS count ";
					$where        = " user_id=$user_id AND fans_id=$value[user_id] ";
				    $count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
				    $data['list'][$key]['follow'] = $count_follow[0]['count'];
				}
				
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('无排行榜', '0x000');  //返回数据
		}
	}
	
	/**
	 * [user_info 获取主播信息]
	 * @url = http://video.dhdyz.com/index.php/Api/user_info?user_id=1
	 * @return [type] [description]
	*/
	public function user_info()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		if(empty($user_id))
		{

			$this->data_back("参数为空", '0x011','fail');
		}
        if($user_id==1077)
        {
        //    $user_id=1484;
        }
		if($user_id<0){
			$user_id=-$user_id;
			$temp_user_watch_by_id=$this->User_Api_model->get_temp_user_info_by_id($user_id);
			$arr=array();
			//for($i=0;$i<$count;$i++){
			$temp_arr=array(
				'user_id'=>'-'.$user_id,
				'image'=>'http://api.etjourney.com//public/images/user/temp_user.png',
				'user_name'=>'游客'.$user_id,
				'sex'=> "0",
				'pre_sign'=>'',
				'address'=>'',
				'froms'=>'',
				'register_time'=>'0',
				'login_time'=>'0',
				'creates'=>'0',
				'video_list'=>$arr,
				'watch'=>$temp_user_watch_by_id['watch'],
				'video_id'=>'0',
				'praise'=>'0','video_sum'=>'0','fan'=>'0',
				'following'=>'0','msgnew'=>0,'letter'=>'0','level'=>'1','openid'=>'',
				'credits'=>'0','checkin_time'=>'0','auth'=>'0','is_guide'=>'0','is_attendant'=>'0',
				'is_driver'=>'0','is_merchant'=>'0','checkin'=>'0','range_guide'=>'','range_attendant'=>'',
				'range_driver'=>'','range_merchant'=>''
			);
			$data=$temp_arr;
			$this->data_back($data, '0x000');
			//}
		}
		$data = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$user_id.'_user_info',$this->config->item('catch_time'));
		if(empty($data))
		{
			$this->data_back('用户信息不存在','0X015','fail');
		}
		else
		{
			$where = " fans_id=$data[user_id] ";
			$fan_count = $this->User_model->get_count($where,'v_follow');
			if($user_id==1734)
			{
			$data['fan'] = strval($fan_count['count'] + ceil((1466673763-1466481400)/6));
			}else
			{
			$data['fan'] = strval($fan_count['count']);
			}
			$follow_count = $this->User_model->get_count(" user_id=$data[user_id] ",'v_follow');
			$data['following'] = strval($follow_count['count']);
			$letter_count = $this->User_model->get_count(" from_id=$data[user_id] AND related='1' AND des_del='0' ",'v_letter');
			$new_letter = $this->User_model->get_count(" to_id=$data[user_id] AND related='0' AND new='1' AND des_del='0' ",'v_letter');
			$data['msgnew'] = $new_letter['count'];
			$data['letter'] = strval($letter_count['count']);
			$data['level'] = $this->get_level($data['credits']);
			//当日是否已签到
			if(date('Ymd',time()) == date('Ymd',intval($data['checkin_time'])))
			{
				$data['checkin'] = '1';
			}
			else
			{
				$data['checkin'] = '0';
			}
			if(stristr($data['image'], 'http')===false)
			{
				$data['image'] = $this->config->item('base_url'). ltrim($data['image'],'.').'?'.time();
			}
			//获取用户认证信息
			$range_guide = $range_attendant = $range_driver = $range_merchant = '';
			if($data['is_guide'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_views');
				if($res)
				{
					$range_guide = $res[0]['id_range'];
				}
			}
			if($data['is_attendant'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_locals');
				if($res)
				{
					$range_attendant = $res[0]['id_range'];
				}
			}
			if($data['is_driver'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_drivers');
				if($res)
				{
					$range_driver = $res[0]['id_range'];
				}
			}
			if($data['is_merchant'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_business');
				if($res)
				{
					$range_merchant = $res[0]['id_range'];
				}
			}
			$data['range_guide']     = $range_guide;
			$data['range_attendant'] = $range_attendant;
			$data['range_driver']    = $range_driver;
			$data['range_merchant']  = $range_merchant;
		}
		//获取用户直播录像
	    $where  = " user_id=$user_id AND is_off=1 ";
	    $res = $this->User_Api_model->video_list($where,0,6,'new','v_video');
		if($res)
		{
			foreach($res as $key=>$value)
			{
				$list[$key]['video_id'] = $value['video_id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['start_time'] = $value['start_time'];
				$list[$key]['stop_time'] = $value['stop_time'];
			//	$list[$key]['address'] = $value['address'];
				//if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
					$list[$key]['user_id'] = $user_id;
					$list[$key]['user_name'] = $data['user_name'] ;
					$list[$key]['auth'] = $data['auth'] ;
					$list[$key]['avatar'] = $data['image'];
				//}
				$list[$key]['views'] = $value['views'];
				$list[$key]['praise'] = $value['praise'];
				$list[$key]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
				$list[$key]['video_dec'] =$data['user_name'].'在'.$value['all_address'].'的精彩直播'.$value['title'].',世界那么大赶快来看看!';
				if($value['stop_time'] && time() - intval($value['stop_time']) < 20)
				{
					$list[$key]['image'] = $this->config->item('base_url') . '/tmp/video.jpg';
					$list[$key]['video_url'] = '';
					$list[$key]['video_exist'] = '0';
				}
				else
				{
					$list[$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					$list[$key]['video_url'] = $this->get_rec($value['video_name'],$value['push_type']);
					$list[$key]['video_exist'] = '1';
				}
				if($key==5)
				{
					$last_video = $value['video_id'];
				}
			}
			//删除超过保留期的录播视频
			//if(isset($last_video))
			//{
			//	$result = $this->User_Api_model->update_recode($last_video,$user_id);
			//}
		}
		else
		{
			$list = array();
		}
		$data['video_list'] = $list;
		$this->data_back($data, '0x000');  //返回数据
	}

	/**
	 * [avatar_upload 上传头像]
	 * @return [type] [description]
	*/
	public function avatar_upload()
	{
		//vars($_FILES['files']);
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!isset($_FILES['files']))
		{
			$this->data_back("图片为空", '0x016','fail');
		}
		$name1 = $user_id.'_avatar_big';
		$image = $this->uploadimg->upload_image($_FILES['files'],'user',$name1);
		$name2 = $user_id.time().'_avatar_small';
		$image = $this->thumb($image,$name2);
		if(empty($user_id)  || empty($image))
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		else
		{
			$param = array(
					'user_id' => $user_id,
					'image'   => $image
				);
			$row = $this->User_Api_model->avatar($param);
			//if (!empty($data)) {

				$image = $this->config->item('base_url'). ltrim($image,'.');
				$image=$image.'?'.time();
				$data_arr = array('result'=>'suucess', 'msg'=>'0x000', 'info'=>$image);
				//$data_arr['info']=$image;
				//$data_arr=json_encode($data_arr);

				//die(json_encode($data_arr));
		//	if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){

		//		die("{\"info\":\"$image\"}");
		//	}else{
				$this->data_back($image, '0x000');  //返回数据
			//}

			//}
			//else
			//{
			//	$this->data_back("异常", '0x013','fail');
			//}
		}
	}

	/**
	 * [thumb 图片压缩]
	 * @return [type] [description]
	*/
	function thumb($url,$key2='time')
	{
		$arr['image_library'] = 'gd2';
		$arr['source_image'] = $url;
		$arr['maintain_ratio'] = TRUE;
		$type=pathinfo($url,PATHINFO_EXTENSION);
		if($key2=='time'){
		$key2=time();
		}
		$arr['new_image']='./public/images/user/'.$key2.'.'.$type;
		$arr['width']     = 64;
		$arr['height']   = 64;
		$res = $this->image_lib->initialize($arr);
	
		if($this->image_lib->resize()){
			return  $arr['new_image'];
		}
  }
	/**
	 * [update_user 修改用户信息]
	 * @return [type] [description]
	*/
	public function update_user()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$user_name = isset($_REQUEST['user_name']) ? trim(htmlspecialchars($_REQUEST['user_name'])) : '';
		$pre_sign = isset($_REQUEST['pre_sign']) ? trim(htmlspecialchars($_REQUEST['pre_sign'])) : '';
		$sex = isset($_REQUEST['sex']) ? trim(htmlspecialchars($_REQUEST['sex'])) : '';
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if(!$user_name && !$sex && !isset($_REQUEST['pre_sign']))
		{
			$this->data_back('参数有误', '0x011','fail');  //返回数据
		}
		if($user_name)
		{	
			if(strlen($user_name)>30){$this->data_back('参数有误', '0x011','fail'); }

			$param['user_name'] = $user_name;
			$count = $this->User_Api_model->count_all($param,'v_users');
			if($count)
			{
				$this->data_back('用户名已经存在','0X015','fail');
			}
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		}
		if(isset($_REQUEST['pre_sign']))
		{
			$param['pre_sign'] = $pre_sign;
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		}
			if($sex == '女')
			{$param['sex'] = '1';}
			elseif($sex=='男')
			{$param['sex'] = '0';}
			//else{$param['sex'] = '2';}
			//$param['sex'] = $sex;
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		
			$this->data_back('修改成功', '0x000');  //返回数据

	}

	/**举报**/
	public function report()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$report_user_id = isset($_REQUEST['report_user_id']) ? intval($_REQUEST['report_user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$where= array('user_id'=>$user_id);
		$this->User_Api_model->reprot_video($where,'v_video');
		//if($report_user_id && $video_id)
		if($report_user_id)
		{
			$count = $this->User_Api_model->comment_count(' COUNT(*) AS count '," user_id=$user_id AND video_id=$video_id ", 'v_report');
			if(empty($count[0]['count']))
			{
				$param = array(
						'user_id' => $user_id,
						'report_user_id'    => $report_user_id,
						'video_id' => $video_id,
						'report_time'    => time()
						);
				$report_id = $this->User_Api_model->insert_string($param,'v_report');
			}
		}
		$this->data_back("举报成功", '0x000');  //返回数据
	}

	/**拉黑**/
	public function defriend_add()
	{
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		$defriend    = isset($_REQUEST['defriend'])    ? intval($_REQUEST['defriend'])  : 0;
		if(empty($user_id) || empty($defriend))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'user_id' => $user_id,
				'defriend'  => $defriend
			);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		$this->data_back("拉黑成功", '0x000'); 
		//取消对方的关注
		$this->User_Api_model->gag_del("(user_id=$user_id AND fans_id=$defriend) OR (fans_id=$user_id AND user_id=$defriend) ",'v_follow');
		//删除自己客户端私信
		$this->User_Api_model->gag_del("(to_id=$user_id AND from_id=$defriend) OR (from_id=$user_id AND to_id=$defriend) ",'v_letter');
		$param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_defriend');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("拉黑成功", '0x000');  //返回数据
		}
	}
	/**
	拉黑列表
	**/
	public function defriend_list()
	{
		$page      = isset($_REQUEST['page'])      ? intval($_REQUEST['page'])    : 1;
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		if(!$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$start = ($page-1)*$this->config->item('page_num');
		$where['user_id'] = $user_id;
		$data = $this->User_Api_model->select_string($where,'','',$start,$this->config->item('page_num'),'v_defriend');
		if(empty($data))
		{
			$this->data_back(array(),'0X014','fail');
		}
		else
		{
			foreach ($data as $key => $value) {

				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[defriend]");
				$user_info = $this->User_Api_model->user_info(" user_id=$value[defriend] ",'v_users',$this->config->item('catch'),$value['defriend'].'_user_info',$this->config->item('catch_time'));

				if(stristr($user_info['image'], 'http'))
				{
					$data1[$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data1[$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data1[$key]['user_name'] = $user_info['user_name'];
				$data1[$key]['defriend'] = $value['defriend'];
				$data1[$key]['auth'] = $user_info['auth'];
				$data1[$key]['sex'] = $user_info['sex'];
				$data1[$key]['level'] = $this->get_level($user_info['credits']);
				$data1[$key]['pre_sign'] = $user_info['pre_sign'];
			}
			$this->data_back($data1,'0X000');
		}
	}

	/**取消拉黑**/
	public function defriend_del()
	{
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		$defriend    = isset($_REQUEST['defriend'])    ? intval($_REQUEST['defriend'])  : 0;
		if(empty($user_id) || empty($defriend))
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		$param = array(
				'user_id' => $user_id,
				'defriend'  => $defriend
			);
		$data = $this->User_Api_model->gag_del($param,'v_defriend');
		$this->data_back("取消成功", '0x000');
	}
	

	/**
	 * [letter 发私信]
	 * @url http://video.dhdyz.com/index.php/Api/letter_info?to=1&from=2&content=%E6%B5%8B%E8%AF%95
	 * @return [type] [description]
	*/
	public function letter_add()
	{
		$to      = isset($_REQUEST['to'])      ? intval($_REQUEST['to'])    : 0;
		$from    = isset($_REQUEST['from'])    ? intval($_REQUEST['from'])  : 0;
		$content = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : 0;
		$time = time();

		if(!$to|| !$from || !$content || $to == 1)
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		$user_where=" user_id=$from";
		$letter_arr=$this->User_model->get_all($select='status',$user_where,$table='v_users','user_id','ASC');
		if($letter_arr[0]['status']!=0)
		{
				return false;
		}
		$param = array(
			'user_id' => $to,
			'defriend'  => $from
		);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		{
			$this->data_back("对方已将你拉黑", '0x000');
		}
		$param = array(
				'to_id'=> $to,
				'from_id'=> $from);

		$where = " (to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to) ";
		$count = $this->User_Api_model->rank_count('COUNT(*) AS count,des_del ',$where,'v_letter');

		if(empty($count[0]['count']))
		{	
			$param = array(
				'to_id'       => $to,
				'from_id'     => $from,
				'content'  => $content,
				'dateline' => $time,
				'related'  => '1'
			);
			$this->User_Api_model->letter_add($param,'v_letter');
			$param = array(
				'to_id'       => $from,
				'from_id'     => $to,
				'content'  => $content,
				'dateline' => $time,
				'new'      => '1',
				'related'  => '1'
			);
			$this->User_Api_model->letter_add($param,'v_letter');

			$param = array(
					'to_id'       => $to,
					'from_id'     => $from,
					'content'  => $content,
					'new'      => '1',
					'dateline' => $time,
					
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');
			/*$param = array(
					'from_id'       => $to,
					'to_id'     => $from,
					'content'  => $content,
					'dateline' => $time,
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');*/
		}
		else
		{	

			$where = " ((to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to)) AND related='1' ";
			$this->User_Api_model->comment_update($where,array('des_del'=>'0'),'v_letter');
			$letter_id = $this->User_Api_model->rank_count('letter_id',$where,'v_letter');
			if(empty($letter_id[0]['letter_id']))
				$this->data_back("参数不全", '0x011','fail');
			for($i=0;$i<2;$i++)
			{
				$where = array(
					'letter_id' => $letter_id[$i]['letter_id']);
				$data = array(
					'content' => $content);
				$this->User_Api_model->comment_update($where,$data,'v_letter');
			}
			$param = array(
					'to_id'       => $to,
					'from_id'     => $from,
					'content'  => $content,
					'new'      => '1',
					'dateline' => $time
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');
			/*$param = array(
					'from_id'       => $to,
					'to_id'     => $from,
					'content'  => $content,
					'dateline' => $time,
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');*/
		}
		if ($data) {
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("异常", '0x013','fail');
		}
	}

	/**
	 * [letter_list 私信列表]
	 * @return [type] [description]
	*/
	public function letter_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$start = ($page-1)*$this->config->item('page_num');
		if(empty($user_id))
		{
			$this->data_back("user_id参数为空", '0x011','fail');
		}
		//初始化数据操作
		$data =  array();
		$order_by     = array('dateline'=>'DESC');
		$where = " from_id=$user_id AND related='1' AND des_del<>'2' ";
		$data = $this->User_Api_model->comment_select('*',$where,'','dateline DESC',$start,$this->config->item('page_num'),'v_letter','',
			$this->config->item('catch'),$user_id.$start. 'letter_list',$this->config->item('catch_time'));
		if(!empty($data))
		{
			foreach ($data as $key => $value) {
				$new_letter = $this->User_model->get_count(" to_id=$user_id AND from_id=$value[to_id] AND related='0' AND new='1' ",'v_letter');
				$data[$key]['msgnew'] = $new_letter['count'];
				$user_info = $this->User_Api_model->user_info(" user_id=$value[to_id] ",'v_users',$this->config->item('catch'),$value['to_id'].'_user_info',$this->config->item('catch_time'));
				$data[$key]['from_name'] = $user_info['user_name'];
				if(stristr($user_info['image'], 'http'))
				{
					$data[$key]['from_avatar'] = $user_info['image'];
				}
				else
				{
					$data[$key]['from_avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data[$key]['sex'] = $user_info['sex'];
				$data[$key]['auth'] = $user_info['auth'];
				$data[$key]['level'] = $this->get_level($user_info['credits']);
			}
		}
		//获取系统消息
		$res = $this->User_Api_model->comment_select('pid,pm_type,message,is_new,add_time'," user_id=$user_id ",'','add_time DESC',0,1,'v_prompt');
		if($res)
		{
			$pm['letter_id'] = $res[0]['pid'];
			$pm['content'] = $res[0]['message'];
			$pm['from_id'] = strval($user_id);
			$pm['to_id'] = '1';
			$pm['dateline'] = $res[0]['add_time'];
			$new = $this->User_model->get_count(" user_id=$user_id AND is_new=1 ",'v_prompt');
			$pm['msgnew'] = $new['count'];
			$pm['from_name'] = '系统消息';
			$pm['from_avatar'] = $this->config->item('sys_icon');
			$pm['sex'] = '2';
			$pm['auth'] = '0';
			$pm['level'] = '0';
			array_unshift($data,$pm);
		}
		if(!empty($data))
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x018','fail');
		}
	}

	/**
	 * [letter_info 私信详情]
	 * @return [type] [description]
	*/
	public function letter_info()
	{
		$to   = isset($_REQUEST['to'])   ? intval($_REQUEST['to'])   : 0;
		$from = isset($_REQUEST['from']) ? intval($_REQUEST['from']) : 0;
		if(!$from)
		{
			$this->data_back("to或者from参数为空", '0x011','fail');
		}
		if(!$to || $to == 1)
		{
			$res = $this->User_Api_model->comment_select('pid,pm_type,message,is_new,add_time'," user_id=$from ",'','add_time','','','v_prompt',1);
			if(!empty($res))
			{
				$this->User_Api_model->comment_update(array('user_id'=>$from),array('is_new'=>0),'v_prompt');
				$to_user_info = $this->User_Api_model->user_info(" user_id=$from ",'v_users',$this->config->item('catch'),$from.'_user_info',$this->config->item('catch_time'));
				foreach ($res as $key => $value)
				{
					$data[$key]['letter_id'] = $value['pid'];
					$data[$key]['content'] = $value['message'];
					$data[$key]['from_id'] = '1';
					$data[$key]['to_id'] = strval($from);
					$data[$key]['rand_id'] = '0';
					$data[$key]['des_del'] = '0';
					$data[$key]['new'] = '0';
					$data[$key]['related'] = '0';
					$data[$key]['dateline'] = $value['add_time'];
					if(stristr($to_user_info['image'], 'http'))
					{
						$data[$key]['to_avatar'] = $to_user_info['image'];
					}
					else
					{
						$data[$key]['to_avatar'] = $this->config->item('base_url').ltrim($to_user_info['image'],'.');
					}
					$data[$key]['to_name'] = $to_user_info['user_name'];
					$data[$key]['from_name'] = '系统消息';
					$data[$key]['from_avatar'] = $this->config->item('sys_icon');
				}
				$this->data_back($data, '0x000');  //返回数据
			}
		}
		$where = " ((to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to)) AND related='0' ";
		//$data = $this->User_Api_model->letter_info($to,$from,'v_letter');
		$data = $this->User_Api_model->letter_info($where,'v_letter');
		//echo $this->db->last_query();
		if(!empty($data))
		{
			$this->User_Api_model->comment_update(array('to_id'=>$from,'from_id'=>$to),array('new'=>'0'),'v_letter');
			foreach ($data as $key => $value) {
				//$to_user_info = $this->Admin_model->user_info('v_users',"user_id=$value[to_id]");
				$to_user_info = $this->User_Api_model->user_info(" user_id=$value[to_id] ",'v_users',$this->config->item('catch'),$value['to_id'].'_user_info',$this->config->item('catch_time'));
				if(stristr($to_user_info['image'], 'http'))
				{
					$data[$key]['to_avatar'] = $to_user_info['image'];
				}
				else
				{
					$data[$key]['to_avatar'] = $this->config->item('base_url').ltrim($to_user_info['image'],'.');
				}
				$data[$key]['to_name'] = $to_user_info['user_name'];
				//$from_user_info = $this->Admin_model->user_info('v_users',"user_id=$value[from_id]");
				$from_user_info = $this->User_Api_model->user_info(" user_id=$value[from_id] ",'v_users',$this->config->item('catch'),$value['from_id'].'_user_info',$this->config->item('catch_time'));
				$data[$key]['from_name'] = $from_user_info['user_name'];
				if(stristr($from_user_info['image'], 'http'))
				{
					$data[$key]['from_avatar'] = $from_user_info['image'];
				}
				else
				{
					$data[$key]['from_avatar'] = $this->config->item('base_url').ltrim($from_user_info['image'],'.');
				}
				//$data[$key]['from_avatar'] = $from_user_info['image'] ?  $this->config->item('base_url').ltrim($from_user_info['image'],'.') : "";
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x018','fail');
		}
	}

	/**
	 * [letter_del 私信删除]
	 * @return [type] [description]
	*/
	public function letter_del()
	{
		$to   = isset($_REQUEST['to'])   ? intval($_REQUEST['to'])   : 0;
		$from = isset($_REQUEST['from']) ? intval($_REQUEST['from']) : 0;
		if(!$to||!$from)
		{
			$this->data_back("to或者from参数为空", '0x011','fail');
		}
		$del = $this->User_model->get_count(" from_id=$to AND to_id=$from AND related='1' AND des_del='2' ",'v_letter');
		if($del['count']==0)
		{
			$this->User_Api_model->letter_del($to,$from,'v_letter','1','1');
		}
		$this->User_Api_model->letter_del($from,$to,'v_letter','1','2');
		$data = $this->User_Api_model->letter_del($to,$from,'v_letter');
		if(empty($data))
			$this->data_back("返回数据为空、未找到私信", '0x014','fail');
		$this->data_back('删除成功', '0x000');  //返回数据
	}
	/**
	 * [gag 当前视屏下 -某用户 - 禁言]
	 * 
	 * @return [type] [description]
	*/
	public function gag_add()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['gag_id']   ? intval($_REQUEST['gag_id'])   : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'video_id' => $video_id,
				'user_id_1'  => $user_id
			);
		$count = $this->User_Api_model->count_all($param,'v_gag');
		if($count)
		{
			$this->data_back("禁言成功", '0x000'); 
		}
		$param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_gag');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("禁言成功", '0x000');  //返回数据
		}
	}

	/**
	 * [del_gag 取消禁言]
	 * @return [type] [description]
	*/
	public function gag_del()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['gag_id']   ? intval($_REQUEST['gag_id'])   : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'video_id' => $video_id,
				'user_id_1'  => $user_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_gag');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("取消禁言成功", '0x000');  //返回数据
		}
	}
	/**
	 * [del_gag 判断是否禁言]
	 * @return [type] [description]
	*/
	public function is_gag()
	{
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$gag_id   = isset($_REQUEST['gag_id']) ? intval($_REQUEST['gag_id']) : 0;
		if(!$video_id || !$gag_id)
			$this->data_back('参数不能为空','0X011','fail');
		$where['video_id']  = $video_id;
		$where['user_id_1'] = $gag_id; 
		$count = $this->User_Api_model->count_all($where, 'v_gag');
		if(empty($count))
		$this->data_back('禁言', '0x000');  //返回数据
		$this->data_back("已禁言", '0x000');

	}

	/**
	 * [fan_add 粉丝]
	 * @return [type] [description]
	*/
	public function fans_add()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!$fans_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'fans_id'  => $fans_id,
				'user_id'  => $user_id,
				'dateline' => time() 
			);
		$data = $this->User_Api_model->gag_add($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("关注成功", '0x000');  //返回数据
		}
	}

	/**
	 * [fans_list 粉丝列表]
	 * @return [type] [description]
	*/
	public function fans_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		//if($fans_id){
			$fans_user_arr=array();
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		//}else{
		//	$fans_user_arr=array();
		//}
		//echo "<pre>";print_r($fans_user_arr);
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = $where = $group_by = $order_by = '';
		$page_num =10;
		//$data['now_page'] = $page;
		if($fans_id)
		{
			$where = " fans_id=$fans_id ";
		}
		else
		{
			$where = " fans_id=$user_id ";
		}
		$count = $this->User_model->get_count($where,'v_follow');
		if(empty($count['count']))
		{
			$this->data_back("没有关注者", '0x017','fail');
		}
		$start = ($page-1)*$page_num;
		$select = ' user_id ';
		$order_by = " dateline ASC ";
		$data['list'] = $this->User_Api_model->comment_select($select,
			$where,$group_by,$order_by,$start,$page_num,'v_follow');
		if(!empty($data['list']))
		{
			foreach ($data['list'] as $key => $value) {
				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[fans_id]");
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),
					'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',
					$this->config->item('catch_time'));
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id'])){
					$is_follow='1';
				}else{
					$is_follow='0';
				}
				//if(!$fans_id){
				//	$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
				//	foreach($fans_user_arr_2 as $v){
				//		$fans_user_arr[]=$v['fans_id'];
				//	}
				//	if(in_array($value['user_id'],$fans_user_arr)){
				//		$is_follow='1';
				//	}else{
				//		$is_follow='0';
				//	}
				//}
				$data['list'][$key]['follow'] = $is_follow;
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['credits']);
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('没有关注者', '0x017','fail');  //返回数据
		}
	}
	
	/**
	 * [fan_del 取消粉]
	 * @return [type] [description]
	*/
	
	public function fans_del()
	{
		//$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 0;
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(empty($fans_id) || empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}	
		$param = array(
				'fans_id'  => $fans_id,
				'user_id'  => $user_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("取消关注成功", '0x000');  //返回数据
		}
	}
	

	/**
	 * [is_fans 判断用户是否粉主播]
	 * @return [type] [description]
	*/
	
	public function is_fans()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		//初始化数据操作
		$select = "  COUNT(*) AS count ";
		$where        = " user_id=$user_id AND fans_id=$fans_id ";
	    $count = $this->User_Api_model->comment_count($select,$where,'v_follow');
	  	if(empty($count[0]['count']))
	  	{
	  		$this->data_back('关注', '0x000');  //返回数据
	  	}
	  	else
	  	{
	  		$this->data_back("已关注", '0x000');  //返回数据
	  	}
	}

	/**
	 * [follow_add 关注]
	 * @return [type] [description]
	*/
	public function follow_add()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$language = isset($_REQUEST['language']) ? trim($_REQUEST['language']) : '';
		if(!$fans_id ||!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}elseif($fans_id<0)
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("无法关注游客", '0x011','fail');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("無法關注遊客", '0x011','fail');
				}else{
					$this->data_back("Tourists can not be followed!", '0x011','fail');
				}
			}else
			{
				$this->data_back("无法关注游客", '0x011','fail');
			}
		}elseif($fans_id == $user_id){
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("无法关注自己", '0x011','fail');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("無法關注自己", '0x011','fail');
				}else{
					$this->data_back("Can not follow yourself!", '0x011','fail');
				}
			}else
			{
				$this->data_back("无法关注自己", '0x011','fail');
			}
		}
		$param = array(
				'user_id'  => $user_id,
				'fans_id'  => $fans_id
			);
		$count = $this->User_Api_model->count_all($param,'v_follow');
	    if($count)
	    {
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("您已关注", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("您已關注", '0x000');
				}else{
					$this->data_back("You have been followed yet", '0x000');
				}
			}else
			{
				$this->data_back("您已关注", '0x000');  //返回数据
			}
	    }
	    $param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("关注成功", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("關注成功", '0x000');
				}else{
					$this->data_back("Success", '0x000');
				}
			}else
			{
				$this->data_back("关注成功", '0x000');  //返回数据
			}
		}
	}

	/**
	 * [follow_list 关注列表]
	 * @return [type] [description]
	*/
	public function follow_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		if($fans_id){
			$fans_user_arr=array();
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		}else{
			$fans_user_arr=array();
		}
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$group_by = $order_by = '';
		//$data['now_page'] = $page;
		if($fans_id)
		{
			$where = " user_id=$fans_id ";
		}
		else
		{
			$where = " user_id=$user_id ";
		}
		$count = $this->User_Api_model->count_all(array('user_id'=>$user_id),'v_follow');
		if(empty($count))
		{
			$this->data_back("没有关注者", '0x000','fail');
		}
		/* $data['max_page'] = ceil($count['count']/$page_num);
         if($page>$data['max_page'])
         {
           $page=1;
         }*/
		$start = ($page-1)*$this->config->item('page_num');
		$select = ' fans_id,status ';
		$order_by = " dateline ASC ";
		$data['list'] = $this->User_Api_model->comment_select($select,
			$where,$group_by,$order_by,$start,$this->config->item('page_num'),'v_follow');
		//echo "<pre>";
		// print_r($data);
		if($data['list'])
		{
			foreach ($data['list'] as $key => $value) {
				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[user_id]");
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['fans_id']),
					'v_users',$this->config->item('catch'),$value['fans_id'].'_user_info',$this->config->item('catch_time'));
				//print_r($user_info);
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				$data['list'][$key]['user_id']=$data['list'][$key]['fans_id'];
				unset($data['list'][$key]['fans_id']);
				if(in_array($value['fans_id'],$fans_user_arr)||$value['fans_id']==$user_id){
					$is_follow='1';
				}else{
					$is_follow='0';
				}
				if(!$fans_id){$is_follow='1';}
				$data['list'][$key]['follow'] = $is_follow;
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['credits']);
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
			}

			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('没有关注者', '0x000','fail');  //返回数据
		}
	}
	/**
	 * [follow_del 取消关注]
	 * @return [type] [description]
	*/
	public function follow_del()
	{
		//$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 0;
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$language = isset($_REQUEST['language']) ? trim($_REQUEST['language']) : '';
		if(!$fans_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}	
		$param = array(
				'user_id'  => $user_id,
				'fans_id'  => $fans_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("取消关注成功", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("取消關注成功", '0x000');
				}else{
					$this->data_back("Cancel success", '0x000');
				}
			}else
			{
				$this->data_back("取消关注成功", '0x000');  //返回数据
			}
		}
	}

	/**
	 * [is_follow 判断用户是否关注主播]
	 * @return [type] [description]
	*/
	public function is_follow()
	{
		$fans_id  = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		//初始化数据操作
		/*$select = "  COUNT(*) AS count ";
		$where        = " user_id=$fans_id AND fans_id=$user_id ";
	    $count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
	    */
        $where = array(
	    		'user_id' => $user_id,
	    		'fans_id' => $fans_id);
	    $count = $this->User_Api_model->count_all($where,'v_follow');
	    unset($where);
	    if(empty($video_id))
	    {
	    	$count_gag =0;
	    }
	    else
	    {
	    	$where = array(
	    		'video_id' => $video_id,
	    		'user_id_1' => $fans_id);

	    	 $count_gag = $this->User_Api_model->count_all($where,'v_gag');
	    }
		
	    if(empty($count) && empty($count_gag))
	    {                                                                                               
	    	$data['follow'] = "0";
	    	$data['gag']    = "0";
	    }
	    elseif(empty($count) && !empty($count_gag))
	    {
	    	$data['follow'] = "0";
	    	$data['gag']    = "1";
	    }
	    elseif(!empty($count) && empty($count_gag))
	    {	$data['follow'] = "1";
	    	$data['gag']    = "0";

	    }elseif(!empty($count) && !empty($count_gag))
	    {
	    	$data['follow'] = "1";
	    	$data['gag']    = "1";
	    }
	    $this->data_back($data,'0X000');
	}

	/**
	 * [watch_start 观看者观看]
	 * @return [type] [description]
	*/
	public function watch_start()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['user_id']  ? intval($_REQUEST['user_id'])  : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//修改用户当前观看的视屏
		$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('watch'=>$video_id),'v_users');
		//修改视屏表 观看者+1
		//$this->User_Api_model->update_views(array('video_id'=>$video_id),'v_video');
		$count = $this->User_Api_model->comment_count(" COUNT(*) AS count "," watch=$video_id ",'v_users');
		if($count && $count[0]['count'] >= 20)
		{
			$watch_num = ($user_id%4 + 1) * 5;
		}
		else
		{
			$watch_num =  1;
		}
		$this->User_Api_model->update_watchs(array('video_id'=>$video_id),'v_video',$watch_num);
		$this->data_back("正在观看", '0x000');  //返回数据
	}

	/**
	 * [watch_stop 观看者停止观看]
	 * @return [type] [description]
	*/
	public function watch_stop()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['user_id']  ? intval($_REQUEST['user_id'])  : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//修改用户当前观看的视屏
		$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('watch'=>0),'v_users');
		//更新观看人数
		$count = $this->User_Api_model->comment_count(" COUNT(*) AS count "," watch=$video_id ",'v_users');
		if($count && $count[0]['count'] >= 20)
		{
			$watch_num = -($user_id%4 + 1);
		}
		else
		{
			$watch_num = -1;
		}
		$this->User_Api_model->update_watchs(array('video_id'=>$video_id),'v_video',$watch_num);
		$this->data_back("观看结束", '0x000');  //返回数据
	}

	/**
	 * [watch_num 正在观看人数和观看过的人数]
	 * @return [type] [description]
	*/
	public function watch_num()
	{	
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!$video_id )
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id,'',2);
		//var_dump($video_info);
		$video_type = '0';
		$msg_info = '';
		if($video_info)
		{
			if($video_info['is_off'] == '0')
			{
				$image = '/opt/nginx/html/zxqc'.ltrim($video_info['image'],'.');
				if(!file_exists($image))
				{
					$rtmp = $this->get_rtmp($video_info['video_name']);
					$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 1 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
					exec($exec);
				}
			}
			elseif($video_info['is_off'] == '1' || $video_info['is_off'] == '2')
			{
				if($video_info['push_done'] == 1)
				{
					$video_type = '3';
					if($user_id == intval($video_info['user_id']))
					{
						$this->User_Api_model->comment_update(array('video_id'=>$video_info['video_id']),array('push_done'=>2),'v_video');
						$msg_info = '网络异常，您的直播已中断！';
					}
					else
					{
						$msg_info = '直播已关闭';
					}
				}
				else
				{
					$video_type = '1';
					$msg_info = '直播已关闭';
				}
			}
			elseif($video_info['is_off'] == '3')
			{
				$video_type = '2';
				$msg_info = '您已被管理员禁播！';
			}
		}
		//添加僵尸看客
		//$this->add_zombies($video_id);

		$views = $this->User_Api_model->view_num($video_id);

		$count = $this->User_Api_model->watch_num($video_id);
		$temp_user_count_and_id=$this->User_Api_model->temp_watch_num($video_id);
		foreach ($count['user_info'] as $key => $value) {
			if(stristr($value['image'], 'http')){
				$count['user_info'][$key]['image'] = $value['image'];			}
			else{
				$count['user_info'][$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
			}
		}
		for($i=0;$i<$temp_user_count_and_id['count'];$i++){
			$temp_arr=array(
				'user_id'=>'-'.$temp_user_count_and_id['temp_users_id'][$i]['temp_users_id'],
				'image'=>'http://api.etjourney.com//public/images/user/temp_user.png',
				'user_name'=>'游客'.$temp_user_count_and_id['temp_users_id'][$i]['temp_users_id'],
				'sex'=> "0"
			);
			$count['user_info'][]=$temp_arr;
		}
		//$count['count']=(string)($count['count']+$temp_user_count_and_id['count']);
		$count['count']=(string)(intval($video_info['watch_num'])+$temp_user_count_and_id['count']);
		$data = array(
			'now_watch' => $count['count'],
			'all_watch' => $views['views'],
			'user_info' => $count['user_info'],
			'video_type' => $video_type,
			'msg_info'  => $msg_info
		);
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
	 * [video_list 视屏列表]
	 * @return [type] [description]
	*/


	/**
	 * [fans_video 关注视屏列表]
	 * @return [type] [description]
	*/
	public function fans_video()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$page =    isset($_REQUEST['page'])      ? intval($_REQUEST['page'])     : 1;
		$location =    isset($_REQUEST['location'])  ? trim($_REQUEST['location'])   : '';
		$time = time()-$this->config->item('vide_list_catch_time');
		if(empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//初始化数据操作
		$select_count = "  COUNT(*) AS count ";
		//$select       = " user_id ";
		$select       = " fans_id ";
		//$where        = " fans_id=$user_id ";
		$where        = " user_id=$user_id ";
	    $group_by     = "";
	    $order_by     = " dateline ASC ";
		$page_num =10;
	    $count = $this->User_Api_model->comment_count($select_count,$where,'v_follow');
	    $count = $count[0]['count'];
		if(empty($count))
		{
			$info['video'] = $this->config->item('base_url').'public/images/app/follow_false.png';
			//$info['video'] = $this->config->item('base_url').'public/images/app/hi.jpg';
			$info['list']  = array();
			$this->data_back($info,'0X000');
		}
	    //$data['max_page'] = ceil($count/$page_num);
	    $start = ($page-1)*$page_num;
	    //所有关注的用户
		$data = $this->User_Api_model->comment_select($select,$where,$group_by,$order_by,$start,$page_num,'v_follow');
		$select   = " v.* ,f.f_id,f.fans_id,f.status,f.dateline  ";
		//$table    = " v_video AS v LEFT JOIN  v_follow  AS f ON f.user_id= v.user_id ";
		$table    = " v_video AS v LEFT JOIN  v_follow  AS f ON f.fans_id= v.user_id ";
		//$where        = " f.fans_id=$user_id ";
		$where        = " f.user_id=$user_id ";
		//if($this->config->item('record_status'))
		//{
		//	$where  .= " AND v.is_off<2 ";
		//}
		//else
		//{
			$where  .= " AND v.is_off=0 ";
		//}
		$group_by = "";
		$order_by = " is_off,video_id DESC ";
		//关注正在直播的视屏
		$data_info1 = $this->User_Api_model->comment_select($select,$where,$group_by,$order_by,$start,$page_num,$table);
		//echo $this->db->last_query();
		//var_dump($data_info1);
		if(empty($data_info1))
		{
			$data_info['video'] = $this->config->item('base_url').'public/images/app/follow_false.png';
			//$data_info['video'] = $this->config->item('base_url').'public/images/app/hi.jpg';
		}
		else
		{
			foreach ($data_info1 as $k => $v) {
				if($v['is_off'] == '1')
				{
					$data_info['video'][$k]['rtmp'] = $this->get_rec($v['video_name'],$v['push_type']);
				}
				else
				{
					$data_info['video'][$k]['rtmp'] = $this->get_rtmp($v['video_name']);
				}
				$distance = "";
				if($location && $v['location'])
				{
					$lct1 = explode(",",$location);
					$lct2 = explode(",",$v['location']);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					$lat2 = $lct2[0];
					$lng2 = $lct2[1];
					$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
				}
				if(stristr($v['image'], 'http'))
				{
					$data_info['video'][$k]['image'] = $v['image'];
				}
				else
				{
					$data_info['video'][$k]['image'] = $this->config->item('base_url') . ltrim($v['image'],'.');
				}
				if(!empty($v['address']))
				{
					$data_info['video'][$k]['ipinfo'] =  $v['address'];
				}
				elseif(!empty($v['ip']))
				{
					$data_info['video'][$k]['ipinfo'] = $this->common->GetIpLookup($v['ip']);
				}
				else
				{
					$data_info['video'][$k]['ipinfo'] =  "";
				}
				$data_info['video'][$k]['video_id'] =  $v['video_id'];
				$data_info['video'][$k]['title'] =  $v['title'];
				$data_info['video'][$k]['start_time'] =  $v['start_time'];
				$data_info['video'][$k]['report'] =  $v['report'];
				$data_info['video'][$k]['praise'] =  $v['praise'];
				$data_info['video'][$k]['user_id'] =  $v['user_id'];
				$data_info['video'][$k]['views'] =  $v['views'];
				$data_info['video'][$k]['video_name'] =  $v['video_name'];
				$data_info['video'][$k]['socket_info'] =  $v['socket_info'];
				//$user_info1 = $this->Admin_model->user_info('v_users',"user_id=$v[user_id]");
				$user_info1 = $this->User_Api_model->user_info(" user_id=$v[user_id] ",'v_users',$this->config->item('catch'),$v['user_id'].'_user_info',$this->config->item('catch_time'));
				$data_info['video'][$k]['user_name'] =  $user_info1['user_name'];
				if(stristr($user_info1['image'], 'http'))
				{
					$data_info['video'][$k]['avatar'] = $user_info1['image'];
				}
				else
				{
					$data_info['video'][$k]['avatar'] = $this->config->item('base_url')  . ltrim($user_info1['image'],'.');
				}
				$data_info['video'][$k]['sex'] = $user_info1['sex']=='2' ? '0' : $user_info1['sex'];
				$data_info['video'][$k]['auth'] = $user_info1['auth'];
				$data_info['video'][$k]['level'] = $this->get_level($user_info1['credits']);
				$data_info['video'][$k]['video_type'] = $v['is_off'];
				$data_info['video'][$k]['distance'] = strval($distance);

			}
		}
		if(empty($data))
		{
			$data_info1['list'] = '';
		}
		else
		{
			foreach ($data as $key => $value) {
				//$user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",
				$user_info = $this->User_Api_model->user_info(" user_id=$value[fans_id] ",
					'v_users',
					$this->config->item('catch'),
					//$value['user_id'].'_user_info',
					$value['fans_id'].'_user_info',
					$this->config->item('catch_time'));
				$data_info['list'][$key]['user_id']   = $user_info['user_id'];
				$data_info['list'][$key]['user_name'] = $user_info['user_name'];
				$data_info['list'][$key]['avatar']    = $user_info['image'];
				if(stristr($user_info['image'], 'http'))
				{
					$data_info['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data_info['list'][$key]['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
				}
				$data_info['list'][$key]['sex']       = $user_info['sex']=='2' ? '0' : $user_info['sex'];
				$data_info['list'][$key]['pre_sign']  = $user_info['pre_sign'];
				$data_info['list'][$key]['auth']      = $user_info['auth'];
				$data_info['list'][$key]['level']     = $this->get_level($user_info['credits']);
			}
		}
		
		$this->data_back($data_info, '0x000');
	}


	/**
		发送验证码
	**/
	public function message()
	{
		//发送号码
		$mobile     = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) :0;
		if(empty($mobile))
		{
			$this->data_back('手机号不能为空','0X002','fail');
		}
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$mobile))
		{   
			$this->data_back('手机号格式不对','0X003','fail');
     	}
     	$ch = curl_init();
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
	    $count = $this->User_Api_model->comment_count(' COUNT(*) AS count '," user_name='$mobile' AND froms='tel'", 'v_users');
    	$param['user_name']     = $mobile;
		if(empty($count[0]['count']))
		{	
			$param['user_name']     = $mobile;
			$param['froms']         = 'tel';
			$param['register_time'] = time();
			$param['login_time'] = time();
			$param['openid']       =  $code;
			//$param['image'] = $headimgurl;
			//$param['sex'] = $sex;
			$data = $this->User_Api_model->register($param,'v_users');
		}
		else
		{
			$param['user_name'] = $mobile;
			$param['froms']     = 'tel';
			$param['openid']    = $code;
	 		$param['login_time'] = time();
			$data = $this->User_Api_model->comment_update(" user_name=$param[user_name] AND froms='tel'",$param,'v_users');
		}
	}


	public function search()
	{
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$fans_user_arr=array();
		if($user_id)
		{
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		}
		//搜索用户名称
	    if($keyword)
	    {
	    	$where_video = " title LIKE '%$keyword%'";
	    	$where_user = " user_name LIKE '%$keyword%' AND status='0' ";
	    }
	    else
	    {
	      $where_video  = $where_user = " 1=1 ";
	    }

		if(!empty($type) && $type != 'all')
		{
			$where_user .= " AND is_".$type."='1' ";
		}
		$count_user = $this->User_Api_model->comment_count(' COUNT(*) AS count ',$where_user, 'v_users');
		if(!empty($count_user[0]['count']))
		{
			$data['user'] = $this->User_Api_model->comment_select("user_id,user_name,image,sex,pre_sign,credits,auth ",
				$where_user,'','',0,10,'v_users',1);
			if(!empty($data['user']))
			{
				foreach ($data['user'] as $key => $value) {
					
					if(stristr($value['image'], 'http'))
					{
						$data['user'][$key]['avatar'] = $value['image'];
					}
					else
					{
						$data['user'][$key]['avatar'] = $this->config->item('base_url') . $value['image'];
					}
					$data['user'][$key]['user_id'] = $value['user_id'];
					$data['user'][$key]['user_name'] = $value['user_name'];
					$data['user'][$key]['level'] = $this->get_level($value['credits']);
					if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id'])){
						$data['user'][$key]['is_follow'] = '1';
					}else{
						$data['user'][$key]['is_follow'] = '0';
					}
				}
			}
			else
			{
				$data['user'] = array();
			}
		}
		else
		{
			$data['user'] = array();
		}
	    $page_num =10;
	    $count = $this->User_model->get_count($where_video,'v_video');
		if(!empty($count['count']) && !$type)
	    {
			$page_num = 10;
		    $start = ($page-1)*$page_num;
		    $data['video'] = $this->User_Api_model->comment_select(' video_id,start_time,image,title,video_name,user_id,ip,views,report,praise,types,socket_info ',$where_video,'','',$start,$page_num,'v_video');
		    if(!empty($data['video']))
			{
				foreach ($data['video'] as $key => $value) {
					
					if(stristr($value['image'], 'http'))
					{
						$data['video'][$key]['image'] = $value['image'];
					}
					else
					{
						$data['video'][$key]['image'] = $this->config->item('base_url') . $value['image'];
					}
					
					$data['video'][$key]['rtmp'] = $this->get_rtmp($value['video_name']);
					//if(stristr($value['video_name'],'rtmp://'))
					//{
					//	$data['video'][$key]['rtmp'] = $value['video_name'];
					//}else
					//{
					//	if($this->config->item('rtmp_flg') == 0)
					//	{
					//		$data[$key]['rtmp'] = 'rtmp://42.121.193.231/hls/'.$value['video_name'];
					//	}
					//	elseif($this->config->item('rtmp_flg') == 1)
					//	{
					//		$auth_key = $this->get_auth($value['video_name']);
					//		$data[$key]['rtmp'] = 'rtmp://video.etjourney.com/etjourney/'.$value['video_name'].'?auth_key='.$auth_key;
					//	}
					//	$data['video'][$key]['rtmp'] = 'rtmp://42.121.193.231/hls/'.$value['video_name'];
					//}
					$user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
					$data['video'][$key]['user_name'] = $user_info['user_name'];
					$data['video'][$key]['sex'] = $user_info['sex'];
					if(stristr($user_info['image'], 'http'))
					{
						$data['video'][$key]['avatar'] = $user_info['image'];
					}
					else
					{
						$data['video'][$key]['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
					}
					if(!empty($data['video'][$key]['ip']))
					{
						$data['video'][$key]['ipinfo'] = $this->common->GetIpLookup($value['ip']);
					}
					else
					{
						$data['video'][$key]['ipinfo'] =  "";
					}
				}
			}
			else
			{
				$data['video'] = array();
			}
		}else
		{
			$data['video'] = array();
		}
		$this->data_back($data,'0X000');
	}

	/**
	增加预播
		http://video.dhdyz.com/api/advance_video?title=123&time=222&user_id=23&image=111
		title 标题、 time  时间戳    files  数组集合   图片     user_id   用户id
	**/
	public function advance_video()
	{
		$title = isset($_REQUEST['title']) ? trim(addslashes($_REQUEST['title'])) :'';
		$time  = isset($_REQUEST['time']) ? intval(trim($_REQUEST['time'])) :'';
		if(isset($_FILES['files']))
			$image = $this->uploadimg->upload_image($_FILES['files'],'advance');
		//$image = '';
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :'';

		if(empty($user_id) || empty($title) || empty($time) || empty($image))//&& !$image)
			$this->data_back('参数为空','0X011','fail');
			$data = array(
				'title'       => $title,
				'user_id'     => $user_id,
				'adv_time'    => $time,
				'image'       => $image);
		$info = $this->User_Api_model->insert_string($data,'v_video_advance');
		if(empty($info))
		{
			$this->data_back('增加预播失败','0X014','fail');
		}
		else
		{
			$this->data_back(array('adv_id'=>$info),'0X000');
		}
	}

	/**预播列表
		http://video.dhdyz.com/api/advance_list?user_id=2
	**/

	public function advance_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :'';
		$page    = isset($_REQUEST['page']) ? intval(trim($_REQUEST['page'])) :1;
		if(empty($user_id))
			$this->data_back('user_id 为空参数为空','0X011','fail');
		$start = ($page-1)*$this->config->item('page_num');
		$time = time()-86400;
		$where = "user_id=$user_id AND adv_time > $time";
		$info = $this->User_Api_model->advance_list('*',$where,'','adv_time DESC',$start,$this->config->item('page_num'),'v_video_advance');

		//echo $this->db->last_query();
		if(empty($info)) $this->data_back('您还没有预播','0X012','fail');
		foreach ($info as $key => $value) {
			if($value['adv_time']  < time())
			{	$info[$key]['stop'] = '1';
				$info[$key]['adv_time'] = '直播已结束';
			}
			else
			{
				$info[$key]['time']  = $value['adv_time'];
				$num = $value['adv_time'] - time();
				if($num > 300)
				{
					$info[$key]['click'] = '1';
				}
				else
				{
					$info[$key]['click'] = '0';
				}
				//echo $num;
				$day = floor($num/86400);
				$hour = floor(($num-86400*$day)/3600);
				//echo $num-86400*$day;
				$minute = floor(($num-86400*$day-3600*$hour)/60);
				//$second = floor((($num-86400*$day-3600*$hour)-60*$minute)%60);
				if($day < 10)
				{
					$day = '0'.$day;                                          
				}
				if($hour < 10)
				{
					$hour = '0'.$hour;
				}
				if($minute < 10)
				{
					$minute = '0'.$minute;
				}
				/*if($second < 10)
				{
					$second = '0'.$second;
				}*/

				$info[$key]['stop'] = '0';
				$info[$key]['adv_time'] = '距离直播还有'.$day.'天'.$hour.'小时'.$minute.'分钟';

			}
			if(stristr($value['image'], 'http'))
				{
					$info[$key]['image'] = $value['image'];
				}
				else
				{
					$info[$key]['image'] = !empty($value['image']) ? $this->config->item('base_url') . ltrim($value['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
				}

		}
		$this->data_back($info,'0X000');

	}

	/**
		删除预播
		http://video.dhdyz.com/api/advance_del?adv_id=33
	**/
	public function advance_del()
	{
		$adv_id = isset($_REQUEST['adv_id']) ? intval(trim($_REQUEST['adv_id'])) :'';
		if(empty($adv_id))
			$this->data_back('预播id为空','0X011','fail');
		$where = array('adv_id'=> $adv_id);
		$info = $this->User_Api_model->del($where,'v_video_advance');
		if(empty($info))
		{
			$this->data_back('异常处理','0X013','fail');
		}
		else
		{
			$this->data_back(array('adv_id'=>$adv_id),'0X000');
		}
	}


	public function share()
	{
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		switch ($type) {
			case 'time':

				break;
			case 'day':
				# code...
				break;
				case 'week':
				# code...
				break;
				case 'month':
				# code...
				break; 
				case 'year':
				break;
				case 'video':

				break;
				case 'advance':
				# code...
				break;
			default:
				# code...
				break;
		}
		$this->data_back($data['info']= '分享成功','0X000');
	}

	public function data_back($info, $msg = '', $result = 'success')
	{
		$data_arr = array('result'=>$result, 'msg'=>$msg, 'info'=>$info);
        die(json_encode($data_arr));
	}
	/******************************************************************/
	public function test()
	{
		$this->load->view('api/test');
	}

	public function push_sys()
	{	
		$time = time();
		$where = " adv_time > $time -600 ";
		//$where = " adv_time = 1458101069 ";
		$video_list = $this->User_Api_model->comment_select('user_id',$where,0,0,0,0,'v_video_advance',1);
		//var_dump($video_list);die;
		foreach ($video_list as $key => $value)
		{	$user_info = $this->User_Api_model->user_info(" user_id = $value[user_id]",'v_users');
			//给不在直播的用户推送通知
			if(empty($user_info['video_id']))
			{
				$device_list['user_name'] = $user_info['user_name'];
				$device_list['list']= $this->User_Api_model->comment_select(' device_id,type',"user_id= $value[user_id] AND login=0 ",'','','','','v_device',1);
				if(!empty($device_list))
				{
					$this->pushinfo($device_list,'advance');
				}
			}
		}
	}

	public function pushinfo($data= array(),$type1='')
	{
		$video_info = isset($data['video_info']) ? $data['video_info'] : '';
		if($type1 == 'advance')
		{
			$info = '还有5分钟就要直播了,赶快准备下把';
			$video_info['advance'] = 1;
		}
		elseif($type1 == 'show_start')
		{
			$info = '在直播 快来围观';
			$video_info['advance'] = 0;
		}
        $token1=$token2= $sep = $type_1 = $type_2 = '';
        for($i=0;$i<count($data['list']);$i++)
        {
        	if($data['list'][$i]['type'] == '1')
        	{
        		
        		$token1 .= $sep.$data['list'][$i]['device_id'];
        		$sep =',';
        		$type_1 = 1;
        		
        	}
        	elseif($data['list'][$i]['type'] == '2')
        	{

        		$token2 .= $sep.$data['list'][$i]['device_id'];
        		$sep =',';
        		$type_2 = 2;
        	}
        }
        //$token1 = '37499ba4d41b50a6811ba2aabcef53b2a54958793b8b1d90d86af8b533ef6d88,571ce7ae1aea82c982b5869559b95adb393307e3f4322e7beb50aff09558b728,9ddfa0de3772b41c75f295a83453a07f0d305e2677da7b5b9dfbb74024f627cb,efe3d3e9d536143f53825b39db1080bcbd9c59b3a0e96a39d8f9bacd65d9bc97';
	  	$url = "http://msg.umeng.com/api/send?sign=";
	  	$urlForSign = 'http://msg.umeng.com/api/send';
	  	$params['timestamp'] = time();
	  	$params['type'] = 'listcast';
    	if($type_1 == 1)
    	{
    		$app_master_secret = $this->config->item('app_master_secret_ios');
		    $params['appkey'] = $this->config->item('youmeng_apikey_ios');
	        $aps['alert'] = $data['user_name'] . $info ;
	        $aps['sound'] = '';
	        $aps['content-available'] = 1;
	        $aps['video_info'] = $video_info;
	        $payload['aps'] = $aps;
	        $params['production_mode'] = false;
	        $params['payload'] = $payload;
	        $params['device_tokens'] = $token1 ;
	        $post_body = json_encode($params);
			$sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
			$array = $this->http_post_data($url . $sign, $post_body);

    	}
    	if($type_2 == 2){
			$app_master_secret = $this->config->item('app_master_secret_android');
		    $params['appkey'] = $this->config->item('youmeng_apikey_android');
	        $payload['display_type'] = "notification";
	        $payload['body'] = array('title'=> '坐享其成','ticker'=>$data['user_name'] . '在直播','text'=> $data['user_name'] . $info,'sound'=>'','video_info'=>$video_info);
	        $params['payload'] = $payload;
	        $params['device_tokens']= $token2 ;
	        $post_body = json_encode($params);
			$sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
			$array = $this->http_post_data($url . $sign, $post_body);
    	}
	}
	/**
		关闭关注者的推送
	**/
	public function close_push()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :0;
		$close_id = isset($_REQUEST['close_id']) ? intval(trim($_REQUEST['close_id'])) :0;
		$type = isset($_REQUEST['type']) ? trim(htmlspecialchars($_REQUEST['type'])) : '';
		if($type == 'all')
		{
			//$where = array('fans_id'=>$user_id);
			$where = array('user_id'=>$user_id);
			$data  =  array('status'=>'1');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			//echo $this->db->last_query();
			$this->data_back('关闭成功','0X000');
		}
		else
		{	
			//$where = array('fans_id'=>$user_id,'user_id' => $close_id);
			$where = array('user_id'=>$user_id,'fans_id' => $close_id);
			$data  =  array('status'=>'1');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			$this->data_back('关闭成功','0X000');

		}
	}
	/**
		打开关注者的推送
	**/
	public function open_push()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :0;
		$close_id = isset($_REQUEST['close_id']) ? intval(trim($_REQUEST['close_id'])) :0;
		$type = isset($_REQUEST['type']) ? trim(htmlspecialchars($_REQUEST['type'])) : '';
		if($type == 'all')
		{
			//$where = array('fans_id'=>$user_id);
			$where = array('user_id'=>$user_id);
			$data  =  array('status'=>'0');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			if(empty($info))
			{
				$this->data_back('异常处理','0X013','fail');
			}
			else
			{
				$this->data_back('关闭成功','0X000');
			}
		}
		else
		{	
			//$where = array('fans_id'=>$user_id,'user_id' => $close_id);
			$where = array('user_id'=>$user_id,'fans_id' => $close_id);
			$data  =  array('status'=>'0');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			if(empty($info))
			{
				$this->data_back('异常处理','0X013','fail');
			}
			else
			{
				$this->data_back('关闭成功','0X000');
			}

		}
	}

	/**
		播主添加商品
	 **/
	public function add_goods()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) : 0;
		$video_id  = isset($_REQUEST['video_id']) ? intval(trim($_REQUEST['video_id'])) : 0;
		$goods_name  = isset($_REQUEST['goods_name']) ? trim($_REQUEST['goods_name']) : '';
		$price  = isset($_REQUEST['price']) ? floatval(trim($_REQUEST['price'])) : 0;
		$goods_number  = isset($_REQUEST['goods_number']) ? intval(trim($_REQUEST['goods_number'])) : 1;
		//参数验证
		if(empty($user_id) || empty($video_id) || empty($goods_name) || empty($price) || empty($goods_number))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//该用户是否已在当前直播添加过商品
		//$where = array('video_id'=>$video_id,'user_id'=>$user_id,'is_delete'=>0);
		//$count = $this->User_Api_model->count_all($where,'v_goods');
		//if($count)
		//{
			//$this->data_back('本次直播已添加过商品','0X011','fail');
		//}
		//添加商品
		$goods  =  array(
						'goods_name'  =>$goods_name,
						'shop_price'  =>$price,
						'goods_number'=>$goods_number,
						'video_id'    =>$video_id,
						'user_id'     =>$user_id,
						'add_time'    =>time()
						);
		$goods_id = $this->User_Api_model->insert_string($goods,'v_goods');
		if($goods_id)
		{
			$data['goods_id'] = strval($goods_id);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('商品添加失败！','0X020','fail');
		}
	}
	
	/**
		查看直播商品信息
	 **/
	public function video_goods()
	{
		$video_id  = isset($_REQUEST['video_id']) ? intval(trim($_REQUEST['video_id'])) : 0;
		//参数验证
		if(empty($video_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//直播是否已结束
		$video = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if(empty($video))
		{
			//$this->data_back('直播已结束','0X011','fail');
		}
		//直播内商品信息
		$select = " * ";
		$where = " video_id='$video_id' ";
		$order_by = " goods_id DESC ";
		//$goods_info = $this->User_Api_model->select_string($where,'','',0,1,'v_goods');
		$goods_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,1,'v_goods');
		if(empty($goods_info))
		{
			$this->data_back(array(),'0X014','fail');
		}
		else
		{
			foreach ($goods_info as $key => $value)
			{
				$data[$key]['goods_id']    = $value['goods_id'];
				$data[$key]['goods_name']  = $value['goods_name'];
				$data[$key]['price']       = $value['shop_price'];
				$data[$key]['goods_number']= $value['goods_number'];
				//获取已支付商品数量
				$select = " COALESCE(SUM(g.goods_number),0) AS pay_number ";
				$table  = " v_order_info o, v_order_goods g ";
				$where  = " o.order_id=g.order_id AND o.video_id=$video_id AND g.goods_id=$value[goods_id] AND o.order_status<>4 AND o.pay_status=1 ";
				$count  = $this->User_Api_model->comment_count($select,$where,$table);
				$data[$key]['pay_number'] = $count[0]['pay_number'];
			}
			$this->data_back($data, '0x000');  //返回数据
		}
	}
	/**
		提交订单
	 **/
	public function add_order()
	{
		$goods_id  = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
		$goods_number  = isset($_REQUEST['goods_number']) ? intval($_REQUEST['goods_number']) : 0;
		$video_id  = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$consignee   = isset($_REQUEST['consignee']) ? trim($_REQUEST['consignee']) : '';
		$address   = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '';
		$mobile   = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
		$note   = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';
		//参数验证
		if(empty($goods_id) || empty($goods_number) || empty($video_id) || empty($user_id) || empty($consignee) || empty($mobile) || empty($address))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//获取商品信息
		$select = " g.goods_id,g.goods_name,g.goods_number,g.shop_price,g.add_time,g.video_id,g.user_id,g.is_delete,u.user_id,u.user_name ";
		$where  = " g.goods_id='$goods_id' AND g.user_id=u.user_id ";
		$table  = " v_goods g, v_users u ";
		$goods_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,$table);
		if($goods_info)
		{
			//检查商品库存
			if($goods_info[0]['goods_number'] < $goods_number)
			{
				$this->data_back('商品库存不足','0X021','fail');
			}
			//检查商品所在直播间是否关闭
			$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
			if(empty($video_info))
			{
				//$this->data_back('商品已下架','0X022','fail');
			}
			//获取买家用户信息
			$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",
					'v_users',
					$this->config->item('catch'),
					$user_id.'_user_info',
					$this->config->item('catch_time'));
			if(empty($user_info))
			{
				$this->data_back('用户不存在','0X022','fail');
			}
			//计算订单总金额
			$amount = $goods_info[0]['shop_price'] * $goods_number;
			$order_sn = $this->get_order_sn();
			//生成订单信息
			$order  =  array(
							'order_sn'    =>$order_sn,
							'user_id_buy' =>$user_id,
							'user_id_buy_name' =>$user_info['user_name'],
							'user_id_sell' =>$goods_info[0]['user_id'],
							'user_id_sell_name' =>$goods_info[0]['user_name'],
							'consignee'   =>$consignee,
							'address'     =>$address,
							'mobile'      =>$mobile,
							'goods_amount'=>$amount,
							'order_amount'=>$amount,
							'video_id'    =>$video_id,
							'goods_all_num' =>$goods_number,
							'commont'     =>$note,
							'add_time'    =>time()
							);
			$order_id = $this->User_Api_model->insert_string($order,'v_order_info');
			if($order_id)
			{
				//生成订单商品信息
				$order_goods  =  array(
							'order_id'    =>$order_id,
							'goods_id' =>$goods_id,
							'goods_name' =>$goods_info[0]['goods_name'],
							'goods_number' =>$goods_number,
							'goods_price' =>$goods_info[0]['shop_price'],
							'goods_sum'   =>$amount
							);
				$rec_id = $this->User_Api_model->insert_string($order_goods,'v_order_goods');
				/* 更新商品库存信息 */
				$this->User_Api_model->update_goods($goods_id,$goods_number);
				//返回订单成功信息
				$data['order_id'] = $order_id;
				$data['order_info'] = $goods_info[0]['goods_name'];
				$data['order_sn'] = $order_sn;
				$data['notify_url'] = $this->config->item('alipay_notify');
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back('订单提交失败','0X022','fail');
			}
		}
		else
		{
			$this->data_back('商品已下架','0X023','fail');
		}
	}
	/**
		当地人信息
	 **/
	public function local_info()
	{
		$type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		switch ($type) {
			//导游
			case 'guide':
				$type = 'is_guide';
				break;
			//地陪
			case 'attendant':
				$type = 'is_attendant';
				break;
			//司机
			case 'driver':
				$type = 'is_driver';
				break;
			//商户
			case 'merchant':
				$type = 'is_merchant';
				break;
			default:
				$type = '';
				break;
		}
		if(!$type || !$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data['userlist'] = array();
		$data['videolist'] = array();
		//验证是否有用户
		$count = $this->User_Api_model->count_all(array($type=>'1'),'v_users');
		if(empty($count))
		{
			$this->data_back($data, '0x000');
		}
		//获取直播列表
		$select = " v.*,u.user_name,u.image as avatar,u.sex,u.auth ";
		//$where  = " v.user_id=u.user_id AND v.is_off=0 AND u.groupid='$groupid' ";
		$where  = " v.user_id=u.user_id AND u.$type='1' ";
		$table  = " v_video v, v_users u ";
		$order_by = " is_off,from_unixtime(start_time,'%Y%m%d') DESC,views DESC ";
		//是否显示录播
		if($this->config->item('record_status'))
		{
			$where  .= " AND v.is_off<2 ";
		}
		else
		{
			$where  .= " AND v.is_off=0 ";
		}
		//首页只显示2个视频，显示更多（page>1）时每页10个
		if($page > 1)
		{
			$page = $page - 1;
			$page_num = 10;
			$user_hidden = true;
		}
		else
		{
			$user_hidden = false;
			$page_num = 2;
		}
	    $start = ($page-1)*$page_num;
		$video_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,$start,$page_num,$table);
		if($video_info)
		{
			foreach($video_info as $key=>$value)
			{
				$distance = "";
				if($location && $value['location'])
				{
					$lct1 = explode(",",$location);
					$lct2 = explode(",",$value['location']);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					$lat2 = $lct2[0];
					$lng2 = $lct2[1];
					$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
				}
				if($value['is_off'] == '1')
				{
					$data['videolist'][$key]['rtmp'] = $this->get_rec($value['video_name'],$value['push_type']);
				}
				else
				{
					$data['videolist'][$key]['rtmp'] = $this->get_rtmp($value['video_name']);
				}
				if(stristr($value['image'], 'http'))
				{
					$data['videolist'][$key]['image'] = $value['image'];
				}
				else
				{
					$data['videolist'][$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
				}
				$data['videolist'][$key]['ipinfo'] = $value['address'];
				//if(!empty($value['ip']))
				//{
				//	$data['videolist'][$key]['ipinfo'] = $this->common->GetIpLookup($value['ip']);
				//}
				//else
				//{
				//	$data['videolist'][$key]['ipinfo'] =  "";
				//}
				$data['videolist'][$key]['video_id'] =  $value['video_id'];
				$data['videolist'][$key]['title'] =  $value['title'];
				$data['videolist'][$key]['start_time'] =  $value['start_time'];
				$data['videolist'][$key]['report'] =  $value['report'];
				$data['videolist'][$key]['praise'] =  $value['praise'];
				$data['videolist'][$key]['user_id'] =  $value['user_id'];
				$data['videolist'][$key]['user_name'] =  $value['user_name'];
				$data['videolist'][$key]['views'] =  $value['views'];
				$data['videolist'][$key]['video_name'] =  $value['video_name'];
				$data['videolist'][$key]['socket_info'] =  $value['socket_info'];
				$data['videolist'][$key]['share_replay_path'] =  "http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
				$data['videolist'][$key]['video_dec']=$value['user_name'].'在'.$value['address'].'的精彩直播'.$value['title'].',世界那么大赶快来看看!';


				if(stristr($value['avatar'], 'http'))
				{
					$data['videolist'][$key]['avatar'] = $value['avatar'];
				}
				else
				{
					$data['videolist'][$key]['avatar'] = $this->config->item('base_url')  . ltrim($value['avatar'],'.');
				}
				$data['videolist'][$key]['sex'] = $value['sex'] == '2' ? '0' : $value['sex'];
				$data['videolist'][$key]['auth'] = $value['auth'];
				$data['videolist'][$key]['distance'] = strval($distance);
				$data['videolist'][$key]['video_type'] = strval($value['is_off']);
			}
		}
		//首页显示用户列表
		if(!$user_hidden)
		{
			//获取热门推荐用户列表
			$select = " u.*,COALESCE(f.fans_id,0) AS is_follow ";
			$where  = " u.$type='1' ";
			$table  = " v_users AS u LEFT JOIN v_follow AS f ON u.user_id=f.fans_id  AND f.user_id='$user_id' ";
			$order_by = " displayorder,u.user_id DESC ";
			$user_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,20,$table);
			if($user_info)
			{
				foreach($user_info as $key=>$value)
				{
					$data['userlist'][$key]['user_id']   = $value['user_id'];
					$data['userlist'][$key]['user_name']   = $value['user_name'];
					$data['userlist'][$key]['avatar']    = $value['image'];
					if(stristr($value['image'], 'http'))
					{
						$data['userlist'][$key]['avatar'] = $value['image'];
					}
					else
					{
						$data['userlist'][$key]['avatar'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					}
					$data['userlist'][$key]['sex']       = $value['sex'];
					$data['userlist'][$key]['pre_sign']  = $value['pre_sign'];
					$data['userlist'][$key]['user_id']   = $value['user_id'];
					$data['userlist'][$key]['is_follow'] = empty($value['is_follow']) ? '0' : '1'; 
					$data['userlist'][$key]['auth']      = $value['auth'];
					$data['userlist'][$key]['level']     = $this->get_level($value['credits']);
				}
			}
		}
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
		添加地址信息
	 **/
	public function add_address()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$consignee   = isset($_REQUEST['consignee']) ? trim($_REQUEST['consignee']) : '';
		$mobile   = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
		$zipcode   = isset($_REQUEST['zipcode']) ? trim($_REQUEST['zipcode']) : '';
		$area   = isset($_REQUEST['area']) ? trim($_REQUEST['area']) : '';
		$address   = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '';
		$type   = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'insert';
		//参数验证
		if(empty($user_id) || empty($consignee) || empty($mobile) || empty($address))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$user_info = $this->User_Api_model->user_info(array('user_id'=>$user_id),'v_users');
		if($user_info)
		{
			$param = array(
						'user_id'   => $user_id,
						'consignee' => $consignee,
						'mobile'    => $mobile,
						'zipcode'   => $zipcode,
						'area'		=> $area,
						'address'	=> $address
						);
			//查询是否已有该用户地址信息
			$address_info = $this->User_Api_model->comment_select('address_id'," user_id=$user_id AND type='0' ",'','',0,1,'v_user_address');
			if(empty($address_info))
			{
				$data['address_id'] = $this->User_Api_model->insert_string($param,'v_user_address');
				$this->data_back('添加成功', '0x000');  //返回数据
			}
			else
			{
				$data['address_id'] = $this->User_Api_model->comment_update(array('user_id'=>$user_id),$param,'v_user_address');
				$this->data_back('修改成功', '0x000');  //返回数据
			}
		}
		else
		{
			$this->data_back('用户不存在','0X022','fail');
		}
	}
	/**
		获取地址信息
	 **/
	public function get_address()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		//参数验证
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data = $this->User_Api_model->comment_select('address_id,consignee,mobile,zipcode,area,address'," user_id=$user_id AND type='0' ",'','',0,1,'v_user_address');
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x014','fail');
		}
	}
	
	/**
		获取banner图信息
	 **/
	public function get_banner()
	{
		$data = $this->User_Api_model->comment_select('title,image_url,link_url'," status=0 ",'','displayorder',0,10,'v_banner');
		if($data)
		{
			foreach($data as $key => $value)
			{
				if(!stristr($value['image_url'], 'http'))
				{
					$data[$key]['image_url'] = $this->config->item('base_url'). ltrim($value['image_url'],'.');
				}
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x014','fail');
		}
	}
	
	/**
		翻译
	 **/
	public function translate()
	{
		$content   = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		$from  = isset($_REQUEST['from']) ? trim($_REQUEST['from']) : 'auto';
		$to   = isset($_REQUEST['to']) ? trim($_REQUEST['to']) : '';
		//参数验证
		if(empty($content) || empty($to))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if($from == $to)
		{
			$info['trans_result'] = $content;
			$this->data_back($info, '0x000');  //返回数据
		}
		switch ($to) {
			case 'ar':
				$to = 'ara';
				break;
			case 'fr':
				$to = 'fra';
				break;
			case 'ko':
				$to = 'kor';
				break;
			case 'ja':
				$to = 'jp';
				break;
			case 'es':
				$to = 'spa';
				break;
			default:
				break;
		}
		$info['trans_result'] = $content;
		$result = $this->baidu_translate($content,$from,$to);
		if($result)
		{
			$info['trans_result'] = $result;
		}
		$this->data_back($info, '0x000');  //返回数据
	}
	
	/**
		我的评价
	 **/
	public function my_evaluate()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		//参数验证
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$page_num = 200;
	    $start = ($page-1)*$page_num;
		$res = $this->User_Api_model->comment_select('user_id_buy,evaluate,star_num'," user_id_sell=$user_id AND evaluate<>'' ",'',' add_time DESC ',$start,$page_num,'v_order_info');
		if($res)
		{
			$star_sum = 0;
			$count = 0;
			foreach($res as $key=>$value)
			{
				if($value['user_id_buy'])
				{
					$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id_buy']),'v_users');
					if(stristr($user_info['image'], 'http'))
					{
						$image = $user_info['image'];
					}
					else
					{
						$image = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
					}
					$data['eva_list'][] = array(
												'user_id_buy' => $value['user_id_buy'],
												'evaluate' => $value['evaluate'],
												'star_num' => $value['star_num'],
												'user_name' => $user_info['user_name'],
												'image' => $image
										);
				}
				$star_sum = $star_sum + intval($value['star_num']);
				$count = $count + 1;
			}
			//总评分
			$data['total'] = strval(round($star_sum/$count,1));
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("暂无评价", '0x014','fail');
		}
	}
	
	/**
		签到
	 **/
	public function checkin()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		if(empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$user_info = $this->User_Api_model->comment_select(" credits,checkin_time,checkin_status "," user_id=$user_id ","","",0,1,"v_users");
		if($user_info)
		{
			$time = time();
			$credits = intval($user_info[0]['credits']);
			$checkin_status = intval($user_info[0]['checkin_status']);
			$chk_time = intval($user_info[0]['checkin_time']);
			$now_date = date("Ymd",$time);
			$chk_date = date("Ymd",$chk_time);
			$overdue = $time - $chk_time;
			if($now_date == $chk_date)
			{
				$this->data_back("您今天已经签过到了", '0x014','fail');
			}
			if($overdue > 172800)
			{
				$credits = $credits + 1;
				$checkin_status = 1;
			}
			else
			{
				if($checkin_status == 0)
				{
					$credits = $credits + 1;
					$checkin_status = 2;
				}
				elseif($checkin_status == 1)
				{
					$credits = $credits + 2;
					$checkin_status = $checkin_status + 1;
				}
				elseif($checkin_status > 1)
				{
					$credits = $credits + 3;
				}
			}
			$param  = array(
							'credits' => $credits,
							'checkin_time' => $time,
							'checkin_status' => strval($checkin_status)
						);
			$this->User_Api_model->comment_update(" user_id=$user_id ",$param,'v_users');
			$data['credits'] = strval($credits);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("用户不存在", '0x014','fail');
		}
	}
	
	/**
		我的认证
	 **/
	public function my_auth()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$language  = isset($_REQUEST['language'])  ? trim($_REQUEST['language'])  : '';
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		switch ($language) {
			case 'ja':
				$view     = 'ガイド';
				$local    = '地元ガイド';
				$driver   = '運転手';
				$business = '商戸';
				$to = 'jp';
				break;
			case 'ko':
				$view     = '가이드';
				$local    = '현지 가이드';
				$driver   = '기사';
				$business = '상점';
				$to = 'kor';
				break;
			case 'th':
				$view     = 'ไกด์ ';
				$local    = 'ไกด์ท้องถิ่น ';
				$driver   = 'คนขับรถ ';
				$business = 'ผู้ค้า';
				$to = 'th';
				break;
			case 'en':
				$view     = 'Guides';
				$local    = 'Locals';
				$driver   = 'Drivers';
				$business = 'Merchants';
				$to = 'en';
				break;
			case 'zh-Hant':
				$view     = '導遊';
				$local    = '地陪';
				$driver   = '司機';
				$business = '商戶';
				$to = 'zh';
				break;
			default:
				$view     = '认证导游';
				$local    = '认证导游';
				$driver   = '认证导游';
				$business = '认证商户';
				$to = 'zh';
				break;
			}
		$data = array();
		//认证导游信息
		$res = $this->User_Api_model->comment_select(" id_range,id_view_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_views');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$view,'range'=>$range,'verify'=>$res[0]['id_view_status']);
		}
		//认证地陪信息
		$res = $this->User_Api_model->comment_select(" id_range,id_local_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_locals');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$local,'range'=>$range,'verify'=>$res[0]['id_local_status']);
		}
		//认证司机信息
		$res = $this->User_Api_model->comment_select(" id_range,id_driver_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_drivers');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$driver,'range'=>$range,'verify'=>$res[0]['id_driver_status']);
		}
		//认证商户信息
		$res = $this->User_Api_model->comment_select(" id_range,id_business_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_business');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$business,'range'=>$range,'verify'=>$res[0]['id_business_status']);
		}
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("没有认证信息", '0x014','fail');
		}
	}
	
	/**
		删除个人录播
	 **/
	public function del_video()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$video_id  = isset($_REQUEST['video_id'])  ? intval($_REQUEST['video_id'])  : 0;
		if(!$user_id || !$video_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//查询
		$res = $this->User_Api_model->comment_select(" video_id "," video_id=$video_id AND user_id=$user_id AND is_off=1 ","","",0,1,'v_video');
		if($res)
		{
			$this->User_Api_model->comment_update(array('video_id'=>$video_id,'user_id'=>$user_id),array('is_off'=>2,'del_time'=>time()),'v_video');
			$this->data_back('删除成功', '0x000');  //返回数据
		}
		else
		{
			$this->data_back("视频不存在", '0x014','fail');
		}
	}
	
	/**
		推荐认证用户列表
	 **/
	public function recommend()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$type     = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$time     = time();
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		if($type == 'all')
		{
	    	//周排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-604800) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_week';
			if(empty($count))
			{
				$this->data_back('无排行榜', '0x000');  //返回数据
			}
			$page     = 1;
			$page_num = 20;
			$start = ($page-1)*$page_num;
			$data= $this->User_Api_model->rank_day_list($select,$where,$group_by,$order_by,$start,$page_num,$table,$this->config->item('catch'),$info,$this->config->item('catch_time'));
			if(!empty($data))
			{
				foreach ($data as $key => $value) {
					$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),'v_users');
					$data[$key]['user_name'] = $user_info['user_name'];
					if(stristr($user_info['image'], 'http'))
					{
						$data[$key]['avatar'] = $user_info['image'];
					}
					else
					{
						$data[$key]['avatar'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
					}
					//用户等级认证信息
					$data[$key]['sex'] = $user_info['sex'];
					$data[$key]['pre_sign'] = $user_info['pre_sign'];
					$data[$key]['auth'] = $user_info['auth'];
					$data[$key]['level'] = $this->get_level($user_info['auth']);
					$select = "  COUNT(*) AS count ";
					$where        = " user_id=$user_id AND fans_id=$value[user_id] ";
					$count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
					$data[$key]['is_follow'] = intval($count_follow[0]['count']) > 0 ? '1' : '0';
					
				}
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back("没有推荐", '0x014','fail');
			}
		}
		else
		{
			//获取热门推荐用户列表
			$select = " u.*,COALESCE(f.fans_id,0) AS is_follow ";
			$where  = " u.auth='1' ";
			$table  = " v_users AS u LEFT JOIN v_follow AS f ON u.user_id=f.fans_id  AND f.user_id='$user_id' ";
			$order_by = " displayorder,creates DESC,groupid ";
			if(!empty($type) && $type != 'all')
			{
				$where .= " AND u.is_".$type."='1' ";
			}
			$user_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,20,$table);
			if($user_info)
			{
				foreach($user_info as $key=>$value)
				{
					$data[$key]['user_id']   = $value['user_id'];
					$data[$key]['user_name']   = $value['user_name'];
					$data[$key]['avatar']    = $value['image'];
					if(stristr($value['image'], 'http'))
					{
						$data[$key]['avatar'] = $value['image'];
					}
					else
					{
						$data[$key]['avatar'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					}
					$data[$key]['sex']       = $value['sex'];
					$data[$key]['pre_sign']  = $value['pre_sign'];
					$data[$key]['user_id']   = $value['user_id'];
					$data[$key]['is_follow'] = empty($value['is_follow']) ? '0' : '1'; 
					$data[$key]['auth']      = $value['auth'];
					$data[$key]['level']     = $this->get_level($value['credits']);
				}
				
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back("没有推荐", '0x014','fail');
			}
		}
	}
	
	/**
		引导图广告
	 **/
	public function adv()
	{
		//获取广告链接和图片链接
		$data['image_url'] = 'http://meinv.aawap.net/dealimages/img/o/s146477t14418295491.jpg';
		$data['link_url'] = 'http://bbs.aawap.net/bbs/thread-60389-1-1.html';
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("没有广告", '0x014','fail');
		}
	}
	
	/**
		退出登录
	 **/
	public function logout()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$device_id  = isset($_REQUEST['device_id'])  ? trim($_REQUEST['device_id'])  : 0;
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$this->User_Api_model->comment_update(array('user_id'=>$user_id,'login'=>0),array('login'=>1),'v_device');
		$this->data_back("退出成功",'0x000','success');  //返回数据
	}
	
	/**
		获取地理位置信息
	 **/
	public function get_position()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$dimension  = isset($_REQUEST['dimension'])  ? trim($_REQUEST['dimension'])  : 0;
		$longitude  = isset($_REQUEST['longitude'])  ? trim($_REQUEST['longitude'])  : 0;
		if(!$user_id || !$dimension || !$longitude)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$time = time();
		$date = date("Y-m-d");
		file_put_contents('/opt/nginx/html/zxqc/logfile/getposition'.$date.'.log','[time='.$time.'][user_id='.$user_id.'][dimension='.$dimension.'][longitude='.$longitude.']'.PHP_EOL,FILE_APPEND);
		$data['country'] = '';
		$data['city'] = '';
		$data['show_status'] = '1';
		$data['msg_info'] = '';
		$data['msg_code'] = '0';
		if($dimension == $longitude && $dimension == '0.0')
		{
			$data['msg_info'] = 'Hi，境外才能开播哦！';
			$data['msg_code'] = '1';
			$this->data_back($data, '0x000');  //返回数据
		}
		$position = $this->geocoder($dimension,$longitude);
		if($position)
		{
			$address = '';
			$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
			$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
			$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
			$description = isset($position['result']['sematic_description']) ? $position['result']['sematic_description'] : '';
			if($city)
			{
				$address = $city;
			}elseif($province){
				$address = $province;
			}elseif($country){
				$address = $country;
			}elseif($description){
				$address = $description;
			}
			if($address)
			{
				if($user_id != 1861)//临时修改
				{
				$this->User_Api_model->comment_update(array('user_id'=>$user_id),array('address'=>$address),'v_users');
				}
			}
			$data['country'] = $country;
			$data['city']    = $city;
			if($position['status']==0 && empty($country))
			{
				$data['position_in']='1';
			}
			else
			{
				if($country != '中国')
				{
					$data['position_in']='1';
				}
				else
				{
					if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
					{
						$data['position_in']='1';
					}
					else
					{
						$data['position_in']='0';
					}
				}
			}
		}
		$ban = $this->User_Api_model->comment_select('user_id'," user_id=$user_id AND ban_out_time>$time AND statue='0' AND is_show='1' ",'','',0,1,'v_ban_user');
		if($ban)
		{
			$data['msg_info'] = '非常抱歉，您已被禁播！';
			$data['msg_code'] = '2';
		}
		else
		{
			$user_info = $this->User_Api_model->comment_select('user_name,auth,status,is_merchant'," user_id=$user_id AND status='0' ",'','',0,1,'v_users');
			if($user_info && $user_info[0]['status']=='0')
			{
				if($user_info[0]['is_merchant'])
				{
					$data['show_status'] = '0';
				}
				else
				{
					if($position)
					{
						if($position['status']==0 && empty($country))
						{
							$data['show_status'] = '0';
							$data['position_in']='1';
						}
						else
						{
							if($country != '中国')
							{
								$data['show_status'] = '0';
								$data['position_in']='1';
							}
							else
							{
								if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
								{
									$data['show_status'] = '0';
									$data['position_in']='1';
								}
								else
								{
									$data['msg_info'] = 'Hi，境外才能开播哦！';
									$data['msg_code'] = '1';
									$data['position_in']='0';
								}
							}
						}
					}
					else
					{
						$data['msg_info'] = 'Hi，境外才能开播哦！';
						$data['msg_code'] = '1';
					}
				}
			}
		}
		file_put_contents('/opt/nginx/html/zxqc/logfile/getposition'.$date.'.log','[country='.$data['country'].'][city='.$data['city'].']'.PHP_EOL,FILE_APPEND);
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
	获取微信支付信息
	**/
	public function get_wxpay()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$order_id  = isset($_REQUEST['order_id'])  ? intval($_REQUEST['order_id'])  : 0;
		if(empty($user_id) || empty($order_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " order_id,order_sn,user_id_buy,order_status,pay_id,pay_status,order_amount ";
		$where = " order_id=$order_id ";
		$order_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,'v_order_info');
		if($order_info)
		{
			if($order_info[0]['user_id_buy'] != $user_id || $order_info[0]['order_status'] != '0' || $order_info[0]['pay_status'] != '0')
			{
				$this->data_back("订单无法支付", '0x011','fail');
			}
		}
		else
		{
			$this->data_back("订单不存在", '0x011','fail');
		}
		$order_sn = $order_info[0]['order_sn'];
		$amount = floatval($order_info[0]['order_amount']) * 100;
		$body = '用户'.$user_id.'的订单';
		require_once("./application/third_party/wxpay/WxPayApp.php");
		$ip = $this->common->real_ip();
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		$unifiedOrder->setParameter("body",$body);//商品描述
		$unifiedOrder->setParameter("out_trade_no",$order_sn);//商户订单号 
		$unifiedOrder->setParameter("spbill_create_ip",$ip);//终端IP
		$unifiedOrder->setParameter("total_fee",$amount);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","APP");//交易类型
		
		$data['prepayid'] = $unifiedOrder->getPrepayId();
		if($data['prepayid'])
		{
			$app_pay = new Wxpay_client_pub();
			$data['appid'] = WxPayConf_pub::APPID;
			$data['partnerid'] = WxPayConf_pub::MCHID;
			$data['package'] = 'Sign=WXPay';
			$data['noncestr'] = $app_pay->createNoncestr();
			$data['timestamp'] = strval(time());
			$app_pay->setParameter("appid",$data['appid']);
			$app_pay->setParameter("partnerid",$data['partnerid']);
			$app_pay->setParameter("prepayid",$data['prepayid']);
			$app_pay->setParameter("package",$data['package']);
			$app_pay->setParameter("noncestr",$data['noncestr']);
			$app_pay->setParameter("timestamp",$data['timestamp']);
			$data['sign'] = $app_pay->getSign($app_pay->parameters);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("微信预付单号获取失败", '0x011','fail');
		}
		//echo 'prepay_id='.$prepay_id.'<br />';
		
	}
	
	/**
	获取上/下一条录播
	**/
	public function get_record()
	{
		$type = isset($_REQUEST['type']) ? $_REQUEST['type']    : 'next';
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		//echo $video_id;
		if(empty($video_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " * ";
		if($type=='next')
		{
			$where  = " video_id>$video_id AND is_off=1";
			$order = " video_id ";
		}
		else
		{
			$where  = " video_id<$video_id AND is_off=1 ";
			$order = " video_id DESC ";
		}
		$data = $this->User_Api_model->comment_select($select,$where,'',$order,0,1,'v_video');
		if($data)
		{
			$data_info = $data[0];
			$data_info['rtmp'] = $this->get_rec($data_info['video_name'],$data_info['push_type']);
			$data_info['image'] = $this->config->item('base_url') . ltrim($data_info['image'],'.');
			$user_id = intval($data_info['user_id']);
			$user_info1 = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$data_info['user_id'].'_user_info',$this->config->item('catch_time'));
			$data_info['user_name'] =  $user_info1['user_name'];
			if(stristr($user_info1['image'], 'http'))
			{
				$data_info['avatar'] = $user_info1['image'];
			}
			else
			{
				$data_info['avatar'] = $this->config->item('base_url')  . ltrim($user_info1['image'],'.');
			}
			$data_info['sex'] = $user_info1['sex']=='2' ? '0' : $user_info1['sex'];
			$data_info['auth'] = $user_info1['auth'];
			$data_info['level'] = $this->get_level($user_info1['credits']);
			$data_info['video_type'] = $data_info['is_off'];
			$this->data_back($data_info, '0x000');
		}
		else
		{
			$this->data_back("没有视频", '0x011','fail');
		}
	}
	/**
	 *  根据坐标点获取地理位置信息（百度接口）
	 **/
	function geocoder($dimension, $longitude) 
	{
		$result = '';
		//$res = $this->http_post_data($this->config->item('baidu_map_url'), $param);
		$url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
		//$url = $this->config->item('baidu_map_url').'?ak=GU1rfcDjP4ZEZVZQo3UBA3jH8Q2x2RKY&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
		$result = file_get_contents($url);
		$result = substr($result,29);
		$result = substr($result, 0, -1);
		$fileType = mb_detect_encoding($result , array('UTF-8','GBK','LATIN1','BIG5'));
		if( $fileType != 'UTF-8'){
			$result = mb_convert_encoding($result ,'utf-8' , $fileType);
		}
		$result = json_decode($result,true);

		return $result;
	}
	/**
	**/
	function http_post_data($url, $data_string) {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
            'Content-Type: application/json; charset=utf-8',  
            'Content-Length: ' . strlen($data_string))  
        );  
        ob_start();  
        curl_exec($ch);  
        $return_content = ob_get_contents();  
        ob_end_clean();  
  
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
        return array($return_code, $return_content);  
    }
	public function see_user(){
		// echo "<pre>";
		$json=$_SERVER['HTTP_USER_AGENT'];
		$data=json_encode($json);
		//echo $json;
		$this->data_back($data, '0x000');  //返回数据
	}

	public function version()
	{

		$this->data_back("2.2.0",'0x000','success');  //返回数据
	}

	public function response()
	{
		$this->data_back("反馈意见",'0x000','success');  //返回数据
	}

	public function up()
	{
		var_dump($_FILES);die;
	}


	public function com_public()
	{
	}



	public function test_url()
	{
		
		$data['url'][0]['info'] = '测试连接';
		$data['url'][0]['src']  = 'http://54101.mmb.cn/wap/Column.do?columnId=12579';
		$h =  date('H',time());
		if($h==18)
		{
			if(stristr($data['url'][0]['src'],'54101'))
			{
				$data['url'][0]['src'] = 'http://baidu.com';
			}
		}
		$this->load->view('api/api_info',$data);
		
	}

	/**
	查询排行榜位置
	**/
	public function rank_user()
	{
		$type    = isset($_REQUEST['type'])       ? intval($_REQUEST['type'])       : 1;
		$user_id = isset($_REQUEST['user_id'])    ? intval($_REQUEST['user_id'])    : '';
		$time = time();
		if($type == 1)
		{
			$where = " dateline > ($time-3600) AND user_id=$user_id ";
			$table = 'v_rank_day';
			$name = "时榜";
			$info['url'] = '/index.php/index/share?type=time';
		}
		elseif($type == 2)
		{
			$where = " dateline > ($time-86400) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "日榜";
			$info['url'] = '/index.php/index/share?type=day';
		}
		elseif($type == 3)
		{

			$where = " dateline > ($time-604800) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "周榜";
			$info['url'] = '/index.php/index/share?type=week';
		}
		elseif($type == 4)
		{
			$where = " dateline > ($time-2592000) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "月榜";
			$info['url'] = '/index.php/index/share?type=month';
		}
		elseif($type == 5)
		{
			$where = " dateline > ($time-31536000) AND user_id=$user_id ";
			$table = 'v_rank_year';
			$name = "年榜";
			$info['url'] = '/index.php/index/share?type=year';
		}
		$data = $this->User_Api_model->rank_user($where,$table);
		if(empty($data))
		{
			$info['title'] = "坐享其成红人榜最新出炉，快来围观！";
		}
		else
		{
			if($data['pm'] > 100)
			{
				$info['title'] = "坐享其成红人榜最新出炉，快来围观！";
			}
			else
			{
				
				$info['title'] = "我在坐享其成直播".$name."中获得了第".$data['pm']."名,快来围观！";
			}
		}
		$user_info = $this->User_Api_model->user_info(" user_id= $user_id",'v_users');
		if(stristr($user_info['image'], 'http'))
		{
			$info['avatar'] = $user_info['image'];
		}
		else
		{
			$info['avatar'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
		}
		$this->data_back( $info,'0x000','success');  //返回数据
	}
	
	/**
	获取视频信息
	**/
	public function defriend()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$to_user_id  = isset($_REQUEST['to_user_id']) ? intval($_REQUEST['to_user_id']) : 0;
		if(empty($user_id) || empty($to_user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$data['defriend'] = '0';
		$param = array(
			'user_id' => $to_user_id,
			'defriend'  => $user_id
		);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		{
			$data['defriend'] = '1';
		}
		$this->data_back($data, '0x000');  //返回数据
	}
	//改变语言
	public function changelan()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$lan  = $this->input->get_post('lan');

		if(empty($user_id)|| empty($lan))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		else
		{
			if(stristr($lan,'ja'))
			{
				$lan = 'ja-jp';
			}
			elseif(stristr($lan,'zh-Hans'))
			{
				$lan = 'zh-cn';
			}
			elseif(stristr($lan,'zh-Hant'))
			{
				$lan = 'zh-tw';
			}
			elseif(stristr($lan,'ko'))
			{
				$lan = 'ko-kr';
			}
			elseif(stristr($lan,'th'))
			{
				$lan = 'th-th';
			}
			else
			{
				$lan = 'en-US';
			}
			if($this->User_model->update_one(array('user_id'=>$user_id),array('lan'=>$lan),'v_users'))
			{
				$info=$lan;
				$this->data_back( $info,'0x000','success');
			}
		}

	}

	/**

	**/
	public function thrid_party()
	{
		$data[0] = 'weixin';
		$data[1] = 'weibo';
		$data[2] = 'qq';
		$data[3] = 'twitter';
		$data[4] = 'face_book';
		$this->data_back($data,'0x000','success');  //返回数据
	}


	/** 判断流是否存在**/
	public function rtmp_status()
	{
		$video_id    = isset($_REQUEST['video_id'])       ? intval($_REQUEST['video_id'])       : '';
		$where = " video_id=$video_id AND is_off=0 ";
    	$count = $this->User_model->get_count($where,'v_video');
    	if($count['count'])
    	{
    		$data = '1';
    	}
    	else
    	{
    		$data = '0';
    	}
    	$this->data_back($data,'0x000','success');  //返回数据
	}
	
	/** 添加僵尸看客**/
	public function add_zombies($video_id=0)
	{
		$limit = 0;
		$value = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if($value)
		{
			$time = time() - intval($value['start_time']);
			if($time > 25 && $time < 60)
			{
				$limit = rand(0,3);
			}elseif($time < 120){
				$limit = rand(0,2);
			}elseif($time < 300){
				$limit = rand(0,1);
			}
		}
		if($limit)
		{
			$this->User_Api_model->update_video_zombie($video_id,'v_users',$limit);
			$this->User_Api_model->update_views(array('video_id'=>$video_id),'v_video',$limit);
		}
	}
	/**
	获取视频信息
	**/
	public function getvideoinfo($user_id)
	{
		$result = array();
		$value = $this->User_Api_model->get_video_info($user_id,'v_video');
		if($value)
		{
			if($value['address'])
			{
				$result['ipinfo'] = $value['address'];
			}
			elseif(!empty($value['ip']))
			{
				$result['ipinfo'] = $this->common->GetIpLookup($value['ip']);
			}
			else
			{
				$result['ipinfo'] =  "";
			}
			
			$result['rtmp'] = $this->get_rtmp($value['video_name']);
			
			$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
	
			if(stristr($user_info['image'], 'http'))
			{
				$result['avatar'] = $user_info['image'];
			}
			else
			{
				$result['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
			}
			$result['user_name'] = $user_info['user_name'];
			$result['sex'] = $user_info['sex'];
			$result['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
			$result['user_id'] = $user_id;
			$result['video_id'] = $value['video_id'];
			$result['video_name'] = $value['video_name'];
			$result['socket_info'] = $value['socket_info'];
			$result['title'] = $value['title'];
		}
		return $result;
	}
	
	/**
	观看直播发表评论
	**/
	public function comment()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$content  = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		if(empty($user_id) || empty($video_id) || empty($content))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$user_info = $this->User_Api_model->user_info(array('user_id'=>$user_id),'v_users');
		if($user_info)
		{
			if(stristr($user_info['image'], 'http'))
			{
				$avatar = $user_info['image'];
			}
			else
			{
				$avatar = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'tmp/avatar.png';
			}
		}
		else
		{
			$this->data_back("用户不存在", '0x011','fail');
		}
		$video = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if($video)
		{
			$socket = explode(':',$video['socket_info']);
			if($socket)
			{
				$ip   = $socket[0];
				$port = $socket[1];
			}
		}
		else
		{
			$this->data_back("直播已结束", '0x011','fail');
		}
		$message =array('user_id'=>strval($user_id),'user_name'=>$user_info['user_name'],'image'=>$avatar,'content'=>$content,'auth'=>$user_info['auth']);
		$this->send_msg($message,$ip,$port);
		$this->data_back('发送成功', '0x000');
	}
	
	/**
	 * [send_msg  发送socket信息]
	 */
	function send_msg($message=array(),$ip,$port)
	{
		$service_port = intval($port);
		$address = $ip;
		if($message)
		{
			$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket < 0) 
			{
				return false;
			}
			$result = @socket_connect($socket, $address, $service_port);
			if ($result < 0)
			{
				return false;
			}
			$in = array(
				'user_id'=>$message['user_id'],
				'user_name'=>$message['user_name'],
				'type'     =>'comment',
				'content'  =>$message['content'],
				'avatar'   =>$message['image'],
				'auth'     =>$message['auth']
				);
			$in = json_encode($in)."\n";
			@socket_write($socket, $in, strlen($in));
			@socket_close($socket);
			return true;
		}
		else
		{
			return false;
		}
		
	}
	/**
	获取视频流地址信息
	**/
	function get_rtmp($video_name)
	{
		$result = '';
		if($video_name)
		{
			if(stristr($video_name,'rtmp://'))
			{
				$result = $video_name;
			}
			else
			{
				if($this->config->item('rtmp_flg') == 0)
				{
					$result = 'rtmp://42.121.193.231/hls/'.$video_name;
				}
				elseif($this->config->item('rtmp_flg') == 1)
				{
					$auth_key = $this->get_auth($video_name);
					$result = $this->config->item('rtmp_url').$video_name.'?auth_key='.$auth_key;
				}
				elseif($this->config->item('rtmp_flg') == 2)
				{
					$result = $this->config->item('rtmp_uc_url').$video_name;
				}
			}
		}
		return $result;
	}
	
	/**
	获取录播地址信息
	**/
	function get_rec($video_name,$push_type)
	{
		$result = '';
		if($video_name)
		{
			if($push_type == '0')
			{
				$result = $this->config->item('record_url').$video_name.'.m3u8';
			}
			elseif($push_type == '1')
			{
				$result = $this->config->item('record_uc_url').$video_name.'.m3u8';
			}
		}
		return $result;
	}
	/**
	获取SOCKET信息
	**/
	function get_socket($video_id,$user_id,$video_name)
	{
		$result = array();
		$result['socket_ip'] = '114.55.65.195';
		$result['socket_port'] = '2120';
		$socket_on = '';
		if($video_id)
		{
			$where = 'video_id='.$video_id;
			$res = $this->User_Api_model->get_socket_list('*',$where,'','',0,1,'v_socket');
			if($res)
			{
				$result['socket_ip'] = $res[0]['socket_ip'];
				$result['socket_port'] = $res[0]['socket_port'];
				return $result;
			}
			$where = ' video_id=0 AND user_id=0 ';
			$order = ' open_status DESC,id ';
			$socket = $this->User_Api_model->get_socket_list(' * ',$where,'',$order,0,3,'v_socket');
			if($socket)
			{
				foreach($socket as $value)
				{
					if($value['open_status'] == '1' && !$socket_on)
					{
						if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$value['socket_port']."' | grep -v grep"))
						{
							exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$value['socket_port']. ' > /dev/null 2>&1 &');
						}
						$this->User_Api_model->comment_update(array('id'=>$value['id']),array('video_id'=>$video_id,'user_id'=>$user_id,'start_time'=>time(),'video_name'=>$video_name),'v_socket');
						$result['socket_ip'] = $value['socket_ip'];
						$result['socket_port'] = $value['socket_port'];
						$socket_on = '1';
					}
					if($value['open_status'] == '0')
					{
						if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$value['socket_port']."' | grep -v grep"))
						{
							exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$value['socket_port']. ' > /dev/null 2>&1 &');
							$this->User_Api_model->comment_update(array('id'=>$value['id']),array('open_status'=>1),'v_socket');
						}
						if(!$socket_on)
						{
							$this->User_Api_model->comment_update(array('id'=>$value['id']),array('video_id'=>$video_id,'user_id'=>$user_id,'open_status'=>1,'start_time'=>time(),'video_name'=>$video_name),'v_socket');
							$result['socket_ip'] = $value['socket_ip'];
							$result['socket_port'] = $value['socket_port'];
							$socket_on = '1';
						}
						//else
						//{
						//	$this->User_Api_model->comment_update(array('id'=>$value['id']),array('open_status'=>1),'v_socket');
						//}
					}
				}
			}
		}
		
		return $result;
		
	}
	/**
	* 得到新订单号
	* @return  string
	*/
	function get_order_sn()
	{
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);
	
		return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}
	 
	/** 
	* 计算两组经纬度坐标 之间的距离 
	* params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km); 
	* return m or km 
	*/ 
	function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2) 
	{ 
		$earth_radius =6378.137;//地球半径 
		$pi=3.1415926;
		$radLat1 = $lat1 * $pi / 180.0; 
		$radLat2 = $lat2 * $pi / 180.0; 
		$a = $radLat1 - $radLat2; 
		$b = ($lng1 * $pi / 180.0) - ($lng2 * $pi / 180.0); 
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2))); 
		$s = $s * $earth_radius; 
		$s = round($s * 1000); 
		if ($len_type > 1) 
		{ 
		$s /= 1000; 
		} 
		return round($s, $decimal); 
	} 
	
	/**
	* 积分等级
	* @return  string
	*/
	function get_level($credits=0)
	{
		$level = '';
		$credits = intval($credits);
		if($credits <= 50)
		{
			$level = '1';
		}elseif($credits <= 100){
			$level = '2';
		}elseif($credits <= 500){
			$level = '3';
		}elseif($credits <= 1000){
			$level = '4';
		}elseif($credits <= 2500){
			$level = '5';
		}elseif($credits <= 5000){
			$level = '6';
		}elseif($credits <= 8000){
			$level = '7';
		}elseif($credits <= 12000){
			$level = '8';
		}elseif($credits <= 16000){
			$level = '9';
		}elseif($credits <= 20000){
			$level = '10';
		}elseif($credits <= 35000){
			$level = '11';
		}elseif($credits > 35000){
			$level = '12';
		}
		return $level;
	}
	
//翻译入口
function baidu_translate($query, $from, $to)
{
	$res = '';
    $args = array(
        'q' => $query,
        'appid' => $this->config->item('baidu_trans_id'),
        'salt' => rand(10000,99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = $this->buildSign($query, $this->config->item('baidu_trans_id'), $args['salt'], $this->config->item('baidu_trans_key'));
    $ret = $this->call($this->config->item('baidu_trans_url'), $args);
    $ret = json_decode($ret, true);
	if($ret)
	{
		if($ret['trans_result'])
		{
			$res = $ret['trans_result']['0']['dst'];
		}
	}
    return $res; 
}

//加密
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

//发起网络请求
function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array())
{/*{{{*/
    $ret = false;
    $i = 0; 
    while($ret === false) 
    {
        if($i > 1)
            break;
        if($i > 0) 
        {
            sleep(1);
        }
        $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ch = curl_init();
    if($method == "post") 
    {
        $data = $this->convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    else 
    {
        $data = $this->convert($args);
        if($data) 
        {
            if(stripos($url, "?") > 0) 
            {
                $url .= "&$data";
            }
            else 
            {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args))
    {
        foreach ($args as $key=>$val)
        {
            if (is_array($val))
            {
                foreach ($val as $k=>$v)
                {
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }
            else
            {
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/

	public function get_map_info(){
		set_time_limit(0);
		//unset($_SESSION['time']);
		//unset($_SESSION['return_arr']);
		/*if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
			$select='v_video.video_id,v_video.user_id,v_video.start_time,
      location,v_users.user_name,v_users.image as user_image,all_address,push_type,
      views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
      socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
		}else{
			$select='v_video.video_id,v_video.user_id,
      	location,v_users.user_name,v_users.image as user_image,all_address,v_video.start_time,push_type,
      	views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
      		socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
		}*/
		$select_video='v_video.video_id,v_video.user_id,
      	location,v_users.user_name,v_users.image as user_image,all_address,v_video.start_time,push_type,
      	views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
      		socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';

		$data=$this->User_model->get_select_all($select_video,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");

		//echo '<pre>';print_r($data);exit();
		if($data===FALSE){
			$this->data_back(array(),'0X000','success' );
		}else{
			if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
				foreach($data as $k=>$v){
					$data[$k]['level']=$this->get_level($v['credits']);
					if($v['is_off']==1){
						if($v['push_type']==0){
							$data[$k]['path'] = $this->config->item('record_url').$v['video_name'].'.m3u8';
						}else{
							$data[$k]['path'] = $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
						}
						//$data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
						$data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
						$data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
					}else{
						$data[$k]['path']=$this->get_rtmp($v['video_name']);
						$data[$k]['share_replay_path'] ="";
						$data[$k]['video_dec']='测试描述'.$v['title'];
					}
				}
			}else{
				foreach($data as $k=>$v){
					$data[$k]['level']=$this->get_level($v['credits']);
					if($v['is_off']==1){
						$data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
						$data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
						$data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
					}else{
						$data[$k]['path']=$this->get_rtmp($v['video_name']);
						$data[$k]['share_replay_path'] ="";
						$data[$k]['video_dec']='测试描述'.$v['title'];
					}
				}
			}
			$this->data_back($data,'0X000','success');
		}
	}

//advertisement
//0 不显示 1显示
	public function advertisement(){
		//$arr=array('time'=>2,'image'=>'http://api.etjourney.com//public/images/thumb/banner/20160713.jpg','is_show'=>'1');
		if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
			$where="type='1' AND is_show<'3'";
			$arr=$this->User_model->get_select_one('image,time,is_show',$where,'v_adver');
			//$arr['image']="http://api.etjourney.com/public/images/advertisement/wdz_ios.jpg?2";
		}else{
			//$arr['image']="http://api.etjourney.com/public/images/advertisement/wdz_andriod.jpg";
			$where="type='2' AND is_show<'3'";
			$arr=$this->User_model->get_select_one('image,time,is_show',$where,'v_adver');
		}


		if($arr===0){
			$arr=array('time'=>2,'image'=>'http://api.etjourney.com/public/images/advertisement/wdz_ios.jpg?2','is_show'=>'1');
			$this->data_back($arr);
		}else{
			$this->data_back($arr);
		}
	}

	public function get_location_con_and_city()
	{
		$title=$this->input->get_post('title',true);
		$newarr[0][0]['name']='热门目的地';
		$newarr[0][0]['list']=array();



		$where1="is_down= '1'";
		$where2="  is_hot='1'";
		if($title)
		{
			$where1=" (is_down= '1' OR is_hot='1')  AND name LIKE '%$title%'";
			//$where2.="  AND name LIKE '%$title%'";
		}
		else
		{

			$newarr[0][0]['list']=$this->User_model->get_select_all($select='name,name_en,name_pinyin,lat,lng,lat AS latitude,lng AS longitude,image ',$where2,$order_title='name_pinyin',$order='ASC',$table='v_location');
			if($newarr[0][0]['list']===false)
			{
				$newarr[0][0]['list']=array();
			}
            foreach($newarr[0][0]['list'] as $k=>$v)
            {
               // $is_rs=$this->User_model->get_select_one('act_id',"is_show='1' AND special >'2' AND range_name LIKE '%$v[name]%'",'v_activity_children');
                //if($is_rs==0 OR count($is_rs)==1){
                  //  unset($newarr[0][0]['list'][$k]);
                //}
            }


		}
		$arr=$this->User_model->get_select_all($select='name,level,pid,image,name_en,name_pinyin,lat,lng,image ',$where1,$order_title='name_pinyin',$order='ASC',$table='v_location');

		if($arr!==false)
		{

            foreach($arr as $k=>$v)
            {
                $str=strtoupper(substr($v['name_pinyin'],0,1));
                $newarr[1][$str]['name']=$str;


                //$is_rs=$this->User_model->get_select_one('act_id',"is_show='1' AND special >'2' AND range_name LIKE '%$v[name]%'",'v_activity_children');
                //if($is_rs!=0 OR count($is_rs)>1){

                    $newarr[1][$str]['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng']);
                //}

            }


		}

            $newarr[1]=array_values($newarr[1]);
            $newarr=array_merge($newarr[0],$newarr[1]);
			$newarr=array_values($newarr);
			if(count($newarr)==0)
			{
				$this->data_back('参数为空','0X011','fail');
			}

		//}
        //echo '<pre>';print_r($newarr);exit();
		$this->data_back($newarr);
	}


	//获取下拉列表国家与大洲
	public function get_auth_downcountry(){
		$arr=$this->User_model->get_select_all($select='name',"is_down='1'",$order_title='name_pinyin',
			$order='ASC',$table='v_location');
		$newarr=array();
		if($arr!==false){
			foreach($arr as $k=>$v){
				$newarr[]=$v['name'];
			}
		}
		$this->data_back($newarr);
	}

	public function get_auth_downpart(){
		$arr=array('亚洲','欧洲','非洲','北美','大洋洲','南美','其他');
		$this->data_back($arr);
	}




	public function auth_search(){
		//$this->output->enable_profiler(TRUE);
		// $this->benchmark->mark('my_mark_start');
		$data=array();
		$where='1=1';
		$auth_type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'auth';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		$auth_range = isset($_REQUEST['auth_range'])?$_REQUEST['auth_range']:0;
		$title  = trim($this->input->get_post('title',true));
		switch ($auth_type)
		{
			//导游
			case 'guide':
				$auth_type = 'is_guide';
				break;
			//地陪
			case 'attendant':
				$auth_type = 'is_attendant';
				break;
			//司机
			case 'driver':
				$auth_type = 'is_driver';
				break;
			//商户
			case 'merchant':
				$auth_type = 'is_merchant';
				break;
			case 'auth':
				$auth_type = 'auth';
				break;
			default:
				$auth_type = 'auth';
				break;
		}
		if($page > 1)
		{
			$page = $page - 1;
			$page_num = 10;
			$user_hidden = true;
		}
		else
		{
			$user_hidden = false;
			$page_num = 2;
		}

		$start = ($page-1)*$page_num;
		if($title)
		{
			$where=" AND v_users.user_name LIKE '%$title%' AND v_users.auth='1'  ";
			$data['userlist']=$this->User_model->get_auth_list($where,$user_id);
		}
		else
		{
			if($auth_range!=='0' AND $auth_range!='all'){
				$where.="  AND v_users.$auth_type='1' AND ( u1.id_prange LIKE '%$auth_range%' OR u2.id_prange LIKE '%$auth_range%' OR u3.id_range LIKE '%$auth_range%' OR u1.id_range LIKE '%$auth_range%' OR u2.id_range LIKE '%$auth_range%' )";
				$wherev=$where." AND is_off < '2'";
				$data['videolist']=$this->User_model->get_video_list($wherev,$start,$page_num,$table='v_users');

				//echo $this->db->last_query();
			}
			else
			{
				$where.="  AND v_users.$auth_type='1' ";
				$wherev=$where." AND is_off < '2'";
				$data['videolist']=$this->User_model->get_video_list($wherev,$start,$page_num,$table='v_users');
			}

			foreach($data['videolist'] as $k=>$v)
			{

				if($v['is_off'] == '1')
				{
					$data['videolist'][$k]['rtmp'] = $this->get_rec($v['video_name'],$v['push_type']);
				}
				else
				{
					$data['videolist'][$k]['rtmp'] = $this->get_rtmp($v['video_name']);
				}
				if(stristr($v['v_image'], 'http'))
				{
					$data['videolist'][$k]['image'] = $v['v_image'];
				}
				else
				{
					$data['videolist'][$k]['image'] = $this->config->item('base_url') . ltrim($v['v_image'],'.');
				}
				$distance = "";
				if($location && $v['location'])
				{
					$lct1 = explode(",",$location);
					$lct2 = explode(",",$v['location']);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					$lat2 = $lct2[0];
					$lng2 = $lct2[1];
					$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2,2);
				}

				$data['videolist'][$k]['distance'] = strval($distance);
				$data['videolist'][$k]['ipinfo'] = $v['address'];
				$data['videolist'][$k]['video_type'] = strval($v['is_off']);
				$data['videolist'][$k]['share_replay_path'] =  "http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$v['video_id']}";
				$data['videolist'][$k]['video_dec']=$v['user_name'].'在'.$v['address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
			}

			if($user_hidden===false){
				$data['userlist']=$this->User_model->get_auth_list($where,$user_id);
			}
		}

		$this->data_back($data);
	}

	//加入收藏
	public function put_favourie()
	{
		$data['user_id']=$this->input->get_post('user_id',true);
		$data['shop_id']=$this->input->get_post('business_id',true);
		if(!$data['user_id'] OR !$data['shop_id'])
		{
			$this->data_back('参数为空','0X011','fail');
		}
		else
		{
			$data['addtime']=time();
			$rs=$this->User_model->get_select_one('shop_id',array('user_id'=>$data['user_id'],'type'=>'2','shop_id'=> $data['shop_id']),'v_favorite');
			if($rs!==0)
			{
				$this->data_back('已收藏','0X011','fail');
			}else{
				$data_arr=array(
					'user_id'=>$data['user_id'],
					'addtime'=>$data['addtime'],
					'shop_id'=>$data['shop_id'],
					'type'=>'2'
				);
				$this->User_model->user_insert($table='v_favorite',$data_arr);
				$this->data_back('success','00000','success');
			}
		}
	}
//取消收藏
	public function out_favourie()
	{
		$data['user_id']=$this->input->get_post('user_id',true);
		$data['shop_id']=$this->input->get_post('business_id',true);
		if(!$data['user_id'] OR !$data['shop_id'])
		{
			$this->data_back('参数为空','0X011','fail');
		}
		else
		{
			$rs=$this->User_model->get_select_one('shop_id',array('user_id'=>$data['user_id'],'type'=>'2','shop_id'=> $data['shop_id']),'v_favorite');
			if($rs==0)
			{
				$this->data_back('未收藏','0X011','fail');
			}else{
				$where=array(
					'user_id'=>$data['user_id'],
					'shop_id'=>$data['shop_id'],
				);
				$this->User_model->update_one($where,array('type'=>'4'),$table='v_favorite');
				$this->data_back('success','00000','success');
			}
		}
	}
//是否收藏
	public function is_favourie()
	{
		$data['user_id']=$this->input->get_post('user_id',true);
		$data['shop_id']=$this->input->get_post('business_id',true);
		if(!$data['user_id'] OR !$data['shop_id'])
		{
			$this->data_back('参数为空','0X011','fail');
		}
		else
		{
			$rs=$this->User_model->get_select_one('shop_id',array('user_id'=>$data['user_id'],'type'=>'2','shop_id'=> $data['shop_id']),'v_favorite');
			if($rs==0)
			{
				$this->data_back('未收藏','00000','success');
			}else{

				$this->data_back('已收藏','00000','success');
			}
		}
	}
//内部用
	public function is_fav($user_id,$shop_id)
	{

		if(!$user_id OR !$shop_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		else
		{
			$rs=$this->User_model->get_select_one('shop_id',array('user_id'=>$user_id,'type'=>'2','shop_id'=>$shop_id),'v_favorite');
			if($rs==0)
			{
				return FALSE;
				//$this->data_back('未收藏','00000','success');
			}else{
				return TRUE;
				//$this->data_back('已收藏','00000','success');
			}
		}
	}


	public function get_favorite()
	{
		$type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '1';
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '1,1';
		if(!$location OR !$type)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$where="v_favorite.user_id=$user_id AND v_favorite.type='$type'";
		if($type==1)
		{
			$where.="  AND v_goods.is_show='1'";
			$select="v_favorite.user_id,v_favorite.act_id,v_activity_children.banner_product as banner_image,v_activity_children.title,v_activity_children.tag,v_goods.shop_price,v_goods.goods_id,v_goods.ori_price";
			$data['list']=$this->User_model->get_favoreite($select,$where,'v_favorite');
			foreach($data['list'] as $k=>$v)
			{
				if($v['shop_price']==0){
					$data['list'][$k]['shop_price']=$v['ori_price'];
				}
				$where="goods_id=$v[goods_id] AND order_status = '3'";
				$data['list'][$k]['goods_buy']=$this->User_model->get_order_count($where);
				$data['list'][$k]['goods_buy']=$data['list'][$k]['goods_buy']['count'];
				$data['list'][$k]['product_url']=base_url("myshop/products_detail?act_id=$v[act_id]");
			}
		}
		else
		{
			$where.=" AND v_wx_business.is_show='1'";
			$select="v_wx_business.star_num,v_wx_business.tag,v_wx_business.logo_image_thumb,v_wx_business.lng,v_wx_business.lat,v_wx_business.lng as longitude,v_wx_business.lat as latitude,v_wx_business.discount,v_wx_business.business_name,v_wx_business.business_id,v_wx_business.address";
			$data['list']=$this->User_model->get_select_all($select,$where,'displayorder','ASC','v_favorite',$left=1,'v_wx_business',"v_favorite.shop_id=v_wx_business.business_id ");
			$me_location=explode(',',$location);
			$me_lat=$me_location[0];
			$me_lng=$me_location[1];
			if($data['list']===false)
			{
				$data['list']=array();
			}
			foreach($data['list'] as $k=>$v)
			{
				$data['list'][$k]['distance']=$this->GetDistance($me_lat, $me_lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
				$data['list'][$k]['shop_url']=base_url("bussell/business_info_app?business_id={$v['business_id']}");
			}

		}
		$this->data_back($data);
	}

//参加过的活动
	public function act_parted_in()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$data=$this->User_model->get_parted_act_new($user_id);
		foreach($data as $k=>$v)
		{
			if(isset($v['banner_image']))
			{
				if(stristr($v['banner_image'], 'http')===false)
				{
					$data[$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
				}
			}
			$data[$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data[$k]['content'])) ;
			$data[$k]['content'] =trim(str_replace("&nbsp;",'',$data[$k]['content'])) ;
			if($v['1']==1)
			{
				$data[$k]['act_url']=base_url("bussell/bus_children_detail_app/{$v['act_id']}");
			}
			else
			{
				$data[$k]['act_url']=base_url("user/activiy_app_detail/{$v['act_id']}");
			}
			unset($data[$k]['1']);
		}
		$data=$this->common->unique($data);
		$data=array_values($data);
		$this->data_back($data);
	}
//申请过的活动
	public function act_applied()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$new_data=array();
		$where_father="special='0' AND (is_show='1' OR is_show ='2') AND user_id=$user_id";
		$data['father']=$this->User_model->get_select_all('act_id,title,content,act_image',$where_father,'start_time','DESC','v_activity_father');
		if( $data['father']===false)
		{
			$data['father']=array();
		}
		foreach( $data['father'] as $k=>$v)
		{
			$data['father'][$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data['father'][$k]['content'])) ;
			$data['father'][$k]['act_url']=base_url("user/activiy_app_detail/{$v['act_id']}");
			$data['father'][$k]['banner_image']=$v['act_image'];
			unset($data['father'][$k]['act_image']);
			$new_data[]=$data['father'][$k];
		}

		$where_child="act_status='2' AND user_id=$user_id AND is_temp='0' AND special='0'";
		$data['child']=$this->User_model->get_select_all('act_id,title,content_text as content,banner_image',$where_child,'start_time','DESC','v_activity_children');
		if( $data['child']===false)
		{
			$data['child']=array();
		}
		foreach( $data['child'] as $k=>$v)
		{
			$data['child'][$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data['child'][$k]['content'])) ;
			$data['child'][$k]['act_url']=base_url("bussell/bus_children_detail_app/{$v['act_id']}");
			$new_data[]=$data['child'][$k];
		}
		//$data=array_unique($data);
		$this->data_back($new_data);
	}

//新粉丝列表接口，user_id:调用接口的用户id fans_id 查看他人粉丝时传入的id
//
	public function fans_list_new()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$fans_user_arr=array();
		$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
		foreach($fans_user_arr_2 as $v)
		{
			$fans_user_arr[]=$v['fans_id'];
		}
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$page_num =10;
		if($fans_id)
		{
			$where = " fans_id=$fans_id ";
			$count = $this->User_model->get_count($where,'v_follow');
		}
		else
		{
			$where = " fans_id=$user_id ";
			$count = $this->User_model->get_count($where,'v_follow');
			$temp_rs=$this->User_model->get_select_one('lastday_fans,lasttime_fans',array('user_id'=>$user_id),'v_users');
			$data['newfans_count']=strval($count['count']-$temp_rs['lastday_fans']);
			$datafans=array(
				'lastday_fans'=>$count['count'],
				'lasttime_fans'=>time()
			);
			$this->User_model->update_one(array('user_id'=>$user_id),$datafans,$table='v_users');
			if($data['newfans_count']>0)
			{
				$data['newfans_list'] =$this->User_model->get_select_all('v_users.user_id,v_users.image,v_users.user_name,v_users.sex,v_users.auth,v_users.credits,v_users.pre_sign',
					$where,'dateline','DESC','v_follow',$left=1,'v_users',"v_users.user_id=v_follow.user_id",$sum=false, $L=1, 0,$data['newfans_count']);
			}
			else
			{
				$data['newfans_list']=array();
				$data['newfans_count']='0';
			}
			$where.="  AND dateline <= $temp_rs[lasttime_fans]";
		}
		if(empty($count['count']))
		{
			$this->data_back("没有关注者", '0x017','fail');
		}
		$start = ($page-1)*$page_num;
		$data['list'] = $this->User_model->get_select_all('v_users.user_id,v_users.image,v_users.user_name,v_users.sex,v_users.auth,v_users.credits,v_users.pre_sign',
			$where,'dateline','ASC','v_follow',$left=1,'v_users',"v_users.user_id=v_follow.user_id",$sum=false, $L=1, $start,$page_num);
		if($data['list']!==false)
		{
			foreach ($data['list'] as $key => $value)
			{
				$data['list'][$key]['avatar']= $data['list'][$key]['image'];
				if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id']))
				{
					$is_follow='1';
				}
				else
				{
					$is_follow='0';
				}
				$data['list'][$key]['follow'] = $is_follow;
				$data['list'][$key]['level'] = $this->common->get_level($value['credits']);
				unset($data['list'][$key]['image']);
			}
		}
		else
		{
			$data['list']=array();
		}
		if(isset($data['newfans_list']) && $data['newfans_list']!==false)
		{
			foreach ($data['newfans_list'] as $key => $value)
			{
				$data['newfans_list'][$key]['avatar']= $data['newfans_list'][$key]['image'];
				if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id']))
				{
					$is_follow='1';
				}
				else
				{
					$is_follow='0';
				}
				$data['newfans_list'][$key]['follow'] = $is_follow;
				$data['newfans_list'][$key]['level'] = strval($this->common->get_level($value['credits']));
				unset($data['newfans_list'][$key]['image']);
			}
		}
		$this->data_back($data, '0x000');
	}


	public function user_info_new()
	{

		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		set_time_limit(0);
		if(empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		if($user_id<0)
		{
			$user_id=-$user_id;
			$temp_user_watch_by_id=$this->User_Api_model->get_temp_user_info_by_id($user_id);
			$arr=array();
			//for($i=0;$i<$count;$i++){
			$temp_arr=array(
				'user_id'=>'-'.$user_id,
				'image'=>'http://api.etjourney.com//public/images/user/temp_user.png',
				'user_name'=>'游客'.$user_id,
				'sex'=> "0",
				'pre_sign'=>'',
				'address'=>'',
				'froms'=>'',
				'register_time'=>'0',
				'login_time'=>'0',
				'creates'=>'0',
				'video_list'=>$arr,
				'watch'=>$temp_user_watch_by_id['watch'],
				'video_id'=>'0',
				'praise'=>'0','video_sum'=>'0','fan'=>'0',
				'following'=>'0','msgnew'=>0,'letter'=>'0','level'=>'1','openid'=>'',
				'credits'=>'0','checkin_time'=>'0','auth'=>'0','is_guide'=>'0','is_attendant'=>'0','is_carmer'=>'0',
				'is_driver'=>'0','is_merchant'=>'0','checkin'=>'0','range_guide'=>'','range_attendant'=>'',
				'range_driver'=>'','range_merchant'=>''
			);
			$data=$temp_arr;
			$this->data_back($data, '0x000');
			//}
		}
		else
		{
			$data = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$user_id.'_user_info',$this->config->item('catch_time'));
			if(empty($data))
			{
				$this->data_back('用户信息不存在','0X015','fail');
			}
			else
			{
				$where = " fans_id=$data[user_id] ";
				$fan_count = $this->User_model->get_count($where,'v_follow');
				if($user_id==1734)
				{
					$data['fan'] = strval($fan_count['count'] + ceil((1466673763-1466481400)/6));
				}else
				{
					$data['fan'] = strval($fan_count['count']);
				}

				$follow_count = $this->User_model->get_count(" user_id=$data[user_id] ",'v_follow');
				$data['following'] = strval($follow_count['count']);
				$letter_count = $this->User_model->get_count(" from_id=$data[user_id] AND related='1' AND des_del='0' ",'v_letter');
				$new_letter = $this->User_model->get_count(" to_id=$data[user_id] AND related='0' AND new='1' AND des_del='0' ",'v_letter');
				$data['msgnew'] = $new_letter['count'];
				$data['letter'] = strval($letter_count['count']);

				$data['level'] = $this->get_level($data['credits']);
				//当日是否已签到
				if(date('Ymd',time()) == date('Ymd',intval($data['checkin_time'])))
				{
					$data['checkin'] = '1';
				}
				else
				{
					$data['checkin'] = '0';
				}
				if(stristr($data['image'], 'http')===false)
				{
					//$data['image'] = $this->config->item('base_url'). ltrim($data['image'],'.').'?'.time();
					$data['image'] = $this->config->item('base_url'). ltrim($data['image'],'.');
				}
				//获取用户认证信息
				$range_guide = $range_attendant = $range_driver = $range_merchant = '';
				if($data['is_guide'] == '1')
				{
					$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_views');
					if($res)
					{
						$range_guide = $res[0]['id_range'];
					}
				}
				if($data['is_attendant'] == '1')
				{
					$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_locals');
					if($res)
					{
						$range_attendant = $res[0]['id_range'];
					}
				}
				if($data['is_driver'] == '1')
				{
					$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_drivers');
					if($res)
					{
						$range_driver = $res[0]['id_range'];
					}
				}
				if($data['is_merchant'] == '1')
				{
					$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_business');
					if($res)
					{
						$range_merchant = $res[0]['id_range'];
					}
				}
                if($data['is_carmer'] == '1')
                {
                    $res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_camer');
                    if($res)
                    {
                        $range_carmer = $res[0]['id_range'];
                    }else{
                        $range_carmer='普吉岛';
                    }
                }
                $data['range_carmer']     = $range_carmer;
				$data['range_guide']     = $range_guide;
				$data['range_attendant'] = $range_attendant;
				$data['range_driver']    = $range_driver;
				$data['range_merchant']  = $range_merchant;
				$data['newfans_count']=$data['fan']-$data['lastday_fans'];
				if($data['newfans_count']<0){
					$data['newfans_count']=0;
				}
			}
			$where  = " user_id=$user_id AND is_off=1 ";
			$res = $this->User_Api_model->video_list($where,0,6,'new','v_video');
			if($res)
			{
				foreach($res as $key=>$value)
				{
					$list[$key]['video_id'] = $value['video_id'];
					$list[$key]['title'] = $value['title'];
					$list[$key]['start_time'] = $value['start_time'];
					$list[$key]['stop_time'] = $value['stop_time'];
					$list[$key]['address'] = $value['address'];
					//if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
					$list[$key]['user_id'] = $user_id;
					$list[$key]['user_name'] = $data['user_name'] ;
					$list[$key]['auth'] = $data['auth'] ;
					$list[$key]['avatar'] = $data['image'];
					//}
					$list[$key]['views'] = $value['views'];
					$list[$key]['praise'] = $value['praise'];
					$list[$key]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
					$list[$key]['video_dec'] =$data['user_name'].'在'.$value['all_address'].'的精彩直播'.$value['title'].',世界那么大赶快来看看!';
					if($value['stop_time'] && time() - intval($value['stop_time']) < 20)
					{
						$list[$key]['image'] = $this->config->item('base_url') . '/tmp/video.jpg';
						$list[$key]['video_url'] = '';
						$list[$key]['video_exist'] = '0';
					}
					else
					{
						$list[$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
						$list[$key]['video_url'] = $this->get_rec($value['video_name'],$value['push_type']);
						$list[$key]['video_exist'] = '1';
					}
					if($key==5)
					{
						$last_video = $value['video_id'];
					}
				}

			}
			else
			{
				$list = array();
			}
			$data['video_list'] = $list;

            $rs=$this->User_model->get_select_one('business_id,discount,logo_image,business_name',array('user_id'=>$user_id,'is_show'=>'1'),'v_wx_business');
            if($rs!='0'){
                $data['shop']['business_id']=$rs['business_id'];
                $data['shop']['discount']=$rs['discount'];
                $data['shop']['image']=$rs['logo_image'];
                $data['shop']['business_name']=$rs['business_name'];

                $where="user_id= $user_id AND is_show='1' AND special >'2'";
                $data['shop']['goods_num']=$this->User_model->get_count($where,'v_activity_children');
                $data['shop']['goods_num']= $data['shop']['goods_num']['count'];

            }else{
                $data['shop']=array('business_id'=>'0');
            }
          //  sort($data['video_list']);
			$this->data_back($data, '0x000');  //返回数据
		}

	}
//商铺坐标
	public function get_map_shop()
	{
		$user_id=$this->input->get_post('user_id',TRUE);
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		$business_id=isset($_REQUEST['business_id']) ? trim($_REQUEST['business_id']) : '0';
		if($location && $user_id)
		{
			$arr=explode(',',$location);
			$lat=$arr[0];
			$lng=$arr[1];
			$arr=$this->getAround($lat,$lng,'50000');
			$where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng]  AND lat !=''";

			$temp_text=$this->User_model->get_select_one('temp_text',array('user_id'=>$user_id),'v_users');
			$temp_text=json_decode($temp_text['temp_text'],TRUE);


			$where.=" AND is_show = '1'";

			if(!is_array($temp_text))
			{
				$temp_text=array();
			}else{
				if(!in_array($business_id,$temp_text))
				{
					$where.=" OR business_id=$business_id";
				}
			}

			$select="is_apply,type,address,business_id,logo_image_thumb AS logo_image,logo_image_thumb AS image,business_name,business_name as title,star_num,discount,tag,lat,lng,lat AS latitude,lng AS longitude,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";

			$list=$this->User_model->get_select_more($select,$where,0,50,$order_title="distance",$order='DESC',$table='v_wx_business');

			if($list==0){
				$list=array();
			}
			foreach($list as $k=>$v){
				$list[$k]['shop_url']=base_url('bussell/business_info_app?business_id=').$v['business_id'];
				$list[$k]['api_url']=base_url('api/get_app_business_info?');
				if($list[$k]['business_info']==''){
					$list[$k]['business_info']=$list[$k]['tag'];
				}
			}


			foreach($list as $k=>$v)
			{
				if(in_array($v['business_id'],$temp_text))
				{
					unset($list[$k]);
				}
				else
				{
					$temp_text[]=$v['business_id'];
				}
			}
			$list=array_values($list);
			$temp_text=json_encode($temp_text);
			$this->User_model->update_one(array('user_id'=>$user_id),array('temp_text'=>$temp_text),$table='v_users');
			$this->data_back($list);
		}else{
			$this->data_back("参数为空", '0x011','fail');
		}

		//$list=$this->User_model->get_select_more($select,$where,0,40,$order_title="distance",$order='DESC',$table='v_wx_business');

	}

	public function set_temp_text()
	{
		$user_id=$this->input->get_post('user_id',TRUE);
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$temp_text=array();
		$temp_text=json_encode($temp_text);
		if($this->User_model->update_one(array('user_id'=>$user_id),array('temp_text'=>$temp_text),$table='v_users'))
		{
			$this->data_back(array(1), '0x000','success');
		}
		else
		{
			$this->data_back(array(0), '0x001','fail');
		}

	}
//获取目的地信息
	public function appmap_getbus()
	{
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '1,1';
		$location_self = isset($_REQUEST['location_self']) ? trim($_REQUEST['location_self']) : '1,1';


		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'bus';
		$page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : '1';
		$range = isset($_REQUEST['range']) ? trim($_REQUEST['range']) : '1';
		//$range_en = isset($_REQUEST['range']) ? trim($_REQUEST['range_en']) : '';

		if($location)
		{
			//echo $location;
			$arr=explode(',',$location);
			$lat=$arr[0];
			$lng=$arr[1];
			$arr=$this->getAround($lat,$lng,'50000');


			$position1 = $this->geocoder($lat,$lng);
			$position1 = json_decode($position1,TRUE);
			$city1 = isset($position1['result']['addressComponent']['city']) ? $position1['result']['addressComponent']['city'] : '1';

			$arr_self=explode(',',$location_self);
			$lat_self=$arr_self[0];
			$lng_self=$arr_self[1];
			$arr2=$this->getAround($lat_self,$lng_self,'50000');


			$position2 = $this->geocoder($lat_self,$lng_self);
			$position2 = json_decode($position2,TRUE);
			$city2= isset($position2['result']['addressComponent']['city']) ? $position2['result']['addressComponent']['city'] : '0';

			if($city1===$city2)
			{
				$where="lat > $arr2[minLat] AND lat < $arr2[maxLat] AND lng > $arr2[minLng] AND lng < $arr2[maxLng]  AND lat !=''";
			}else{
				$where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng]  AND lat !=''";
			}
			if($type=='bus')
			{
				//$where='1=1';

				$where.=" AND is_show = '1'";

				$page_num=10;
				$start = ($page-1)*$page_num;
				$data['title']=$this->input->post_get('title',true);
				$select="is_apply,type,address,business_id,logo_image_thumb AS logo_image,logo_image_thumb AS image,business_name,business_name as title,star_num,discount,tag,lat,lng,lat AS latitude,lng AS longitude,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat_self*PI()/180-lat*PI()/180)/2),2)+COS($lat_self*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng_self*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";
				if($data['title'])
				{
					$where =" business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%' ";
					$list=$this->User_model->get_select_more($select,$where,0,40,$order_title="distance",$order='ASC',$table='v_wx_business');
				}
				else
				{
					$where1=$where." AND type='1'";
					$where2=$where." AND type='2'";
					$where3=$where." AND type='3'";
					$where4=$where." AND type='4'";
					$where5=$where." AND type='5'";
					$list1=$this->User_model->get_select_more($select,$where1,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
					if($list1==0){
						$list1=array();
					}
					$list2=$this->User_model->get_select_more($select,$where2,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
					if($list2==0){
						$list2=array();
					}
					$list3=$this->User_model->get_select_more($select,$where3,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
					if($list3==0){
						$list3=array();
					}
					$list4=$this->User_model->get_select_more($select,$where4,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
					if($list4==0){
						$list4=array();
					}
					$list5=$this->User_model->get_select_more($select,$where5,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
					if($list5==0){
						$list5=array();
					}
					$list=array_merge($list1,$list2,$list3,$list4,$list5);
				}
				foreach($list as $k=>$v)
                {
					$list[$k]['shop_url']=base_url('bussell/business_info_app?business_id=').$v['business_id'];
					//$list[$k]['shop_url']=base_url('bussell/business_info_app?business_id=2053');
					$list[$k]['api_url']=base_url('api/get_app_business_info?');
					if($list[$k]['business_info']==''){
						$list[$k]['business_info']=$list[$k]['tag'];
					}
				}
			}
			elseif($type=='video')
			{
				$page_num=5;
				$start = ($page-1)*$page_num;
				//unset($_SESSION['time']);
				//unset($_SESSION['return_arr']);
			$select="v_video.video_id,v_video.user_id,v_video.start_time,location,v_users.user_name,v_users.image as avatar,
			v_video.lat AS latitude,v_video.lng AS longitude,all_address,push_type,views,v_video.praise,video_name,title,v_video.image as image,v_users.pre_sign,
			title,is_off,socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth";
				//$row_location=$this->User_model->get_select_one('name,name_en',array('name'=>$range),'v_location');
				//$where="  (v_video.address LIKE '%$row_location[name]%' OR  v_video.address LIKE '%$row_location[name_en]%) AND is_off<'2'";
				$where.="  AND is_off<'2'";
				$data['title']=$this->input->post_get('title',true);
				if($data['title'])
                {
					$where=" v_video.title LIKE '%$data[title]%' AND is_off<'2'";
				}

				$list=$this->User_model->get_select_all($select,$where,'v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id",$sum=false,$L=1, $start,$page_num);

				if($list===FALSE)
				{
					$this->data_back(array(),'0X000','success');
				}
				else
				{
					foreach ($list as $k => $v)
					{
						$list[$k]['level'] = $this->get_level($v['credits']);

						$arr=explode(',',$v['location']);
						$lat2=$arr[0];
						$lng2=$arr[1];
						$list[$k]['distance'] = $this->GetDistance($lat_self,$lat_self, $lat2,$lng2,1);
						if ($v['is_off'] == 1)
						{
							$list[$k]['path'] = $this->get_rec($v['video_name'], $v['push_type']);
							$list[$k]['share_replay_path'] = "http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
							$list[$k]['video_dec'] = $v['user_name'] . '在' . $v['all_address'] . '的精彩直播' . $v['title'] . ',世界那么大赶快来看看!';
						}
						else
						{
							$list[$k]['path'] = $this->get_rtmp($v['video_name']);
							$list[$k]['share_replay_path'] = "";
							$list[$k]['video_dec'] = '测试描述' . $v['title'];
						}
					}
				}
			}
			elseif($type=='products')
			{
				//$where='1=1';

				$select="v_activity_children.act_id,v_activity_children.banner_product as banner_image,v_activity_children.title,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-v_wx_business.lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(v_wx_business.lat*PI()/180)*POW(SIN(($lng*PI()/180-v_wx_business.lng*PI()/180)/2),2)))*1000) AS distance  ";
				$where=" v_activity_children.special='2'
						 AND v_activity_children.act_status='2'
						 AND v_activity_children.is_temp='0'
						 AND v_activity_children.is_show='1'
						 AND v_activity_children.user_id!=0
						 AND v_activity_children.range_name LIKE '%$range%'
						 AND  v_wx_business.business_id!=''
						 AND  v_wx_business.is_show = '1'
						 ";
			 	$data['title']=$this->input->post_get('title',true);
				if($data['title']){
					$where="  v_activity_children.title LIKE '%$data[title]%'
						AND v_activity_children.special='2'
						 AND v_activity_children.act_status='2'
						 AND v_activity_children.is_temp='0'
						 AND v_activity_children.is_show='1'
						 AND v_activity_children.user_id!=0
						  AND  v_wx_business.business_id!=''
						 AND  v_wx_business.is_show = '1' ";
				}
				$list=$this->User_model->get_select_all($select,$where,"distance",'ASC','v_activity_children',1,'v_wx_business',"v_activity_children.user_id=v_wx_business.user_id ",$sum=false,$L=1, $start=0,$page_num=30);
				if(is_array($list))
				{
					$new_list=array();
					$del=array();
					foreach($list as $k=>$v)
					{
						$rs=$this->User_model->get_select_one('goods_id,shop_price,ori_price',array('act_id'=>$v['act_id']),'v_goods');
						if($rs==0)
						{
							$del[]=$k;
							continue;
						}
						$where="goods_id=$rs[goods_id] AND order_status = '3'";
						$list[$k]['goods_buy']=$this->User_model->get_order_count($where);
						$list[$k]['goods_buy']=$list[$k]['goods_buy']['count']+rand(5,20);
						if($rs['shop_price']=='0.00'){
							$list[$k]['shop_price']=$rs['ori_price'];
						}
						if($rs['ori_price']=='0.00'){
							$list[$k]['shop_price']=$rs['shop_price'];
						}
						$list[$k]['product_url']=base_url("myshop/products_detail?act_id=$v[act_id]");
						$new_list[$v["act_id"]]=$list[$k];
					}

					$list=array_values($new_list);
				}
			}
			else
			{
				$this->data_back(array(), '0x000','success');
			}
			if(isset($list) && is_array($list) && count($list)>0)
			{
				$this->data_back($list);
			}
			else
			{
				$this->data_back(array(), '0x000','success');
			}

		}
		else
		{
			$this->data_back("参数为空", '0x011','fail');
		}
	}

    public function put_device()
    {

        $data['user_id']=trim($this->input->get_post('user_id',TRUE));
        $data['type']=trim($this->input->get_post('type',TRUE));
        $data['userdevice']=trim($this->input->get_post('userdevice',TRUE));
        $data['type']=trim($this->input->get_post('type',TRUE));
        $data['phone']=trim($this->input->get_post('phone',TRUE));
        $data['lat']=trim($this->input->get_post('lat',TRUE));
        $data['lng']=trim($this->input->get_post('lng',TRUE));
        $data['addtime']=time();
        if(!$data['user_id']){
            return false;
        }
        $rs=$this->User_model->get_select_one('lat,lng',array('user_id'=>$data['user_id'],'userdevice'=>$data['userdevice']),'v_phone');
        if($rs==0)
        {
            $this->User_model->user_insert('v_phone',$data);
        }elseif(!$rs['lat'] AND !$rs['lat']){
            $this->User_model->update_one(array('user_id'=>$data['user_id'],'userdevice'=>$data['userdevice']),array('lat'=>$data['lat'],'lng'=>$data['lng']),'v_phone');
        }


    }


	//获取app内商铺信息
	public function get_app_business_info()
	{

		$business_id=$this->input->post_get('business_id',true);
		//$business_id=2053;
		$data['shop']=$this->User_model->get_select_one('business_name,user_id,business_tel,business_info,business_country,business_id,tag,star_num,discount,logo_image_thumb as image,address,user_id,is_apply',array('business_id'=>$business_id),'v_wx_business');
		if($data['shop']['business_info']==''){
			$data['shop']['business_info']=$data['shop']['tag'];
		}
		//$data['shop']['business_name']='';
		//$data['shop']['business_info']='啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦';
		if($data['shop']===0)
		{
			$data['shop']=array();
			//$data['shop']['user_id'];
			$user_id=0;
		}
		else
		{
			$data['shop']['tag_arr']=explode(',',$data['shop']['tag']);
			$data['shop']['new_arr']=array();
		//	$user_id=$data['shop']['user_id'];
			$tag_arr=explode(',',$data['shop']['tag_arr']);
			//$data['tag_arr']=$tag_arr;
			$str='';
			foreach($tag_arr as $k1=>$v1)
			{
				$str.=$v1;
				$data['shop']['new_arr'][]=$v1;
				if(mb_strlen($str)>14){
					break;
				}
			}
			$user_id=$data['shop']['user_id'];
		}

		$data['shop_url']=base_url("bussell/app_shop_buy?business_id=$business_id&call=1");
		$select="v_activity_children.act_id,v_activity_children.title,v_activity_children.tag,v_activity_children.banner_product as banner_image,v_goods.shop_price,v_goods.low,v_goods.ori_price,v_goods.goods_id";
		$data['products']=$this->User_model->get_select_all($select,array('v_activity_children.user_id'=>$user_id,'special'=>'2','v_activity_children.is_show'=>'1','v_activity_children.act_status'=>'2','v_goods.goods_id >'=>'0','v_goods.is_show'=>'1'),'v_activity_children.act_id','ASC',
			'v_activity_children',1,'v_goods',"v_goods.act_id=v_activity_children.act_id  ");


		if($data['products']===false)
		{
			$data['products']=array();
		}
        else
        {
			foreach($data['products'] as $k=>$v)
			{
				if($v['shop_price']==0){
					$data['products'][$k]['shop_price']=$v['ori_price'];
				}
				$goods_id=$v['goods_id'];
				$where="goods_id=$goods_id AND order_status = '3'";
				$data['products'][$k]['goods_buy']=$this->User_model->get_order_count($where);
				$data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']['count']+rand(5,20);
				$data['products'][$k]['product_url']=base_url("myshop/products_detail?act_id=$v[act_id]");
				$data['products'][$k]['item']='products';
			}
			//print_r($data['products']);

		} 

		$select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.image as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
		$left_title='v_video.user_id=v_users.user_id';
		$left_table='v_users';
		//business_id=$business_id
		//$where_list="business_id=$business_id  AND is_off<2";
		$where_list="v_video.user_id=$user_id  AND is_off<2";
		$data['list']=$this->User_model->get_act_video_all($select,$where_list,'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start=0,$page_num=5);
		if(!empty($data['list']))
		{
			foreach($data['list'] as $k => $v)
			{
				$data['list'][$k]['item']='video';
				if($v['is_off']==1)
				{
					if($v['push_type']==0)
					{
						$data['list'][$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
					}
					else
					{
						$data['list'][$k]['path']=$this->config->item('record_uc_url').$v['video_name'].'.m3u8';
					}
					$data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
					$data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$v['video_id']}";
					$data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
				}
				else
				{
					$data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
					$data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
				}
				$data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
			}

		}
		if(!$data['list'])
		{
			$data['list']=array();
		}
		$data['share']['share_url']=base_url("bussell/business_info_app?business_id=$business_id");
		$data['share']['title']=$data['business_name'];
		$data['share']['image']=$data['image'];
		$data['share']['desc']="坐享其成上的一个商铺。";
		$data['json_share']=json_encode($data['share']);
		$data['call_url']=$this->input->get('call_url');
		if(!$data['call_url'])
		{
			$data['call_url']='olook://identify.toapp>menu';
		}
		$this->data_back($data);
	}


//以某个经纬度为原点，返回半径范围内经纬度的范围
	public function getAround($lat,$lon,$raidus)
	{
		$PI = 3.14159265;

		$latitude = $lat;
		$longitude = $lon;

		$degree = (24901*1609)/360.0;
		$raidusMile = $raidus;

		$dpmLat = 1/$degree;
		$radiusLat = $dpmLat*$raidusMile;
		$minLat = $latitude - $radiusLat;
		$maxLat = $latitude + $radiusLat;
		$mpdLng = $degree*cos($latitude * ($PI/180));
		$dpmLng = 1 / $mpdLng;
		$radiusLng = $dpmLng*$raidusMile;
		$minLng = $longitude - $radiusLng;
		$maxLng = $longitude + $radiusLng;
		return array(
			'minLat'=>$minLat,
			'maxLat'=>$maxLat,
			'minLng'=>$minLng,
			'maxLng'=>$maxLng,
		);
	}


	public function can_live($dimension,$longitude)
	{
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '1,1';
		$arr=explode(',',$location);
		$position = $this->geocoder($dimension,$longitude);
		$position = json_decode($position,TRUE);
		if($position){
			$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
			$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
			$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
			if($position['status']==0 && empty($country)){
				return TRUE;
			}else{
				$data['country'] = $country;
				$data['city']    = $city;
				if($this->input->get('test')){
					echo'<pre>'; print_r($country) ;exit();
				}
				if($country != '中国'){
					return TRUE;
				}else{
					if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
					{
						return TRUE;
					}else{
						return FALSE;
					}
				}
			}
		}else{
			return FALSE;
		}

	}

//旅拍接口   gettype 1 套系 2商家 3摄影师
    public function trip_shoot_list()
    {
        $range_name=$this->input->post_get('range_name',TRUE);
        $user_id=$this->input->post_get('user_id',TRUE);

        if(!$range_name){
            $this->data_back("参数为空", '0x011','fail');
        }
        $title=$this->input->post_get('title',TRUE);
        $gettype=$this->input->post_get('gettype',TRUE);
        if($gettype==1){

        }
        elseif($gettype==2)
        {

        }
        elseif($gettype==3)
        {

        }else{
            $this->data_back("参数为空", '0x011','fail');
        }

    }

    public function get_index()
    {
        $user_id=trim($this->input->post_get('user_id',TRUE));
        $data['products']=$this->User_model->get_select_all($select='v_activity_children.act_id,v_activity_children.title,v_activity_children.banner_image,v_goods.ori_price,v_goods.oori_price',array('v_activity_children.is_show'=>'1','v_activity_children.special'=>'2','v_goods.is_show'=>'1','v_goods.ori_price>'=>'0','v_goods.oori_price>'=>'0'),$order_title='v_activity_children.add_time',$order='DESC',$table='v_activity_children',$left=1,
            $left_table='v_goods',$left_title="v_activity_children.act_id=v_goods.act_id",$sum=false,$L=1, $start=0,$page_num=3);
        foreach($data['products'] as $k=>$v)
        {
        $data['products'][$k]['url']=base_url("myshop/products_detail?act_id=$v[act_id]");
        }
        $select = " u.*,COALESCE(f.fans_id,0) AS is_follow ";
        $where  = " u.auth='1' ";
        $table  = " v_users AS u LEFT JOIN v_follow AS f ON u.user_id=f.fans_id  AND f.user_id='$user_id' ";
        $order_by = " displayorder,creates DESC,groupid ";
        if(!empty($type) && $type != 'all')
        {
            $where .= " AND u.is_".$type."='1' ";
        }
        $user_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,20,$table);
        if($user_info)
        {
            foreach($user_info as $key=>$value)
            {
                $data['user'][$key]['user_id']   = $value['user_id'];
                $data['user'][$key]['user_name']   = $value['user_name'];
                $data['user'][$key]['avatar']    = $value['image'];

                $data['user'][$key]['is_guide']    = $value['is_guide'];
                $data['user'][$key]['is_attendant']    = $value['is_attendant'];
                $data['user'][$key]['is_driver']    = $value['is_driver'];
                $data['user'][$key]['is_merchant']    = $value['is_merchant'];
                if(stristr($value['image'], 'http'))
                {
                    $data['user'][$key]['avatar'] = $value['image'];
                }
                else
                {
                    $data['user'][$key]['avatar'] = $this->config->item('base_url') . ltrim($value['image'],'.');
                }
                $data['user'][$key]['sex']       = $value['sex'];
                $data['user'][$key]['pre_sign']  = $value['pre_sign'];
                $data['user'][$key]['user_id']   = $value['user_id'];
                $data['user'][$key]['is_follow'] = empty($value['is_follow']) ? '0' : '1';
                $data['user'][$key]['auth']      = $value['auth'];
                $data['user'][$key]['level']     = $this->get_level($value['credits']);
            }
        }
        $where="  is_hot='1'";
        $select='name,name_en,name_pinyin,lat,lng,lat AS latitude,lng AS longitude,image ';

        $data['location']=$this->User_model->get_select_more($select,$where,$start=0,$page_num=4,'id',$order='ASC',$table='v_location');
        $this->data_back($data, '0x000');  //返回数据
    }

    public function get_range_detail()
    {
        $name=trim($this->input->get('range'));
        $name='普吉';
        $data['banner']=$this->User_model->get_select_more($select='title,banner_hot as image,act_id',$where="is_show='1'   AND range_name LIKE'%$name%'",$start=0,$page_num=5,'act_id',$order='ASC',$table='v_activity_children');
        $temp=array();
        foreach($data['banner'] as $k=>$v)
        {
            $data['banner'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]");
            $temp[]="'".$v['act_id']."'";
        }
        $temp=implode(',',$temp);
       // $data['imgae']=$this->User_model->get_select_one('image_show1,image_show2,image_show3,image_show4,image_show5',"name = '$name'",'v_location');
        $wherehot="is_show='1' AND hot='1'  AND range_name LIKE'%$name%'";
        $wherehot="act_id IN (220,221)";
        $data['hot']=$this->User_model->get_select_more($select='title,banner_hot as image,act_id',$wherehot,$start=0,$page_num=2,'act_id',$order='ASC',$table='v_activity_children');

        if($data['hot']!=0)
        {
            foreach($data['hot'] as $k=>$v)
            {
                $data['hot'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]");
                $data['hot'][$k]['name']='热推';
                $data['hot'][$k]['desc']='大家都在关注什么！';

            }
        }else{
            $data['hot']=array();
        }

        $whererec="is_show>='1' AND rec='1' AND range_name LIKE'%$name%' AND act_id NOT IN($temp) ";
        $whererec="act_id IN (208,215)";
        $data['rec']=$this->User_model->get_select_more($select='title,banner_hot as image,act_id',$whererec,$start=0,$page_num=2,'act_id',$order='ASC',$table='v_activity_children');


        if($data['rec']!=0)
        {
            foreach($data['rec'] as $k=>$v)
            {
                $data['rec'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]");
                $data['rec'][$k]['name']='特色';
                $data['rec'][$k]['desc']='当地有什么良品！';
            }


        }else{
            $data['rec']=array();
        }


        $this->data_back($data, '0x000');  //返回数据

    }

    public function video_list()
    {
        //$this->data_back(array(), '0x002','fail');
        //最新 最热视屏   默认最热
        $type = isset($_REQUEST['type']) ? $_REQUEST['type']         : 'new';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        //$data['type'] = $type;
        $time = time() - $this->config->item('vide_list_catch_time');
        $title = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
        $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
        $range = isset($_REQUEST['range']) ? trim($_REQUEST['range']) : '';
        $range_en = isset($_REQUEST['range']) ? trim($_REQUEST['range_en']) : '';
        if(!$range OR !$range_en){
            $this->data_back(array(), '0x002','fail');
        }
        if($title)
        {
            //$where = "(v.title LIKE '%$data[title]%' OR v.user_id LIKE '%$data[title]%' OR u.user_name LIKE '%$data[title]%')";
            $where = " (title LIKE '%$title%' OR user_id LIKE '%$title%') AND $time > start_time AND types=1 ";
        }
        else
        {
            $where  = " 1=1 AND $time > start_time AND types=1 AND (title LIKE '%$range%' OR all_address LIKE '%$range%' OR address  LIKE '%$range%'  OR address  LIKE '%$range_en%'  OR address  LIKE '%$range_en%' OR title  LIKE '%$range_en%')";
        }
        //是否显示录播
        if($this->config->item('record_status'))
        {
            $where  .= " AND is_off<2 ";
        }
        else
        {
            $where  .= " AND is_off=0 ";
        }
        $page_num =10;
        //$data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_video');
        //$data['max_page'] = ceil($count['count']/$page_num);
        /* if($page>$data['max_page'])
         {
           $page=1;
         }*/
        if($type == 'hot')
        {
            if($page == 1)
            {
                //$page_num  = 11;
                $page_num = 10;
            }
            else
            {
                $page_num = 10;
            }

        }
        $start = ($page-1)*$page_num;



        $video = $this->User_Api_model->video_list($where,
            $start,
            $page_num,
            $type,
            'v_video',
            0,
            $type.$start.'video_list',
            $this->config->item('catch_time'));
        $data = array();
        if(!empty($video))
        {

            foreach ($video as $key => $value) {
                //视频截图尚未生成时，列表暂不显示
                //if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')) || (!@fopen('http://oss.etjourney.com/etjourney/'.$value['video_name'].'.m3u8','r') && $value['is_off']=='1'))
                //if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')))
                if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')) || ($value['stop_time'] != '0' && $value['push_type'] == '0' && (time()-intval($value['stop_time']) < 200) && $value['is_off']=='1'))
                {
                    continue;
                }
                $info = $value;
                $distance = "";
                $lct2 = explode(",",$value['location']);
                $lat2 = $lct2[0];
                $lng2 = $lct2[1];
                if($location && $value['location'])
                {
                    $lct1 = explode(",",$location);
                    $lat1 = $lct1[0];
                    $lng1 = $lct1[1];
                    if($lat2 == '0.0' && $lng2 == '0.0')
                    {
                        $distance = '∞';
                    }
                    else
                    {
                        $distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
                    }
                }
                $info['ipinfo'] =  "";
                if(empty($value['address']))
                {
                    if($lat2 == '0.0' && $lng2 == '0.0')
                    {
                        $info['ipinfo'] = '火星?';
                    }
                    else
                    {
                        $position = $this->geocoder($lat2,$lng2);
                        if($position)
                        {
                            $address = '';
                            $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
                            $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
                            $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
                            $description = isset($position['result']['sematic_description']) ? $position['result']['sematic_description'] : '';
                            if($city)
                            {
                                $address = $city;
                            }elseif($province){
                                $address = $province;
                            }elseif($country){
                                $address = $country;
                            }elseif($description){
                                $address = $description;
                            }
                            if($address)
                            {
                                $info['ipinfo'] = $address;
                                $this->User_Api_model->comment_update(array('video_id'=>$value['video_id']),array('address'=>$address),'v_video');
                            }
                        }
                        if(!empty($value['ip']) && empty($info['ipinfo']))
                        {
                            //$info['ipinfo'] = $this->common->GetIpLookup($value['ip']);
                            $info['ipinfo'] = '火星?';
                        }
                    }

                }
                else
                {
                    $info['ipinfo'] = $value['address'];
                }
                if($value['is_off'] == '1')
                {
                    $info['rtmp'] = $this->get_rec($value['video_name'],$value['push_type']);
                }
                else
                {
                    $info['rtmp'] = $this->get_rtmp($value['video_name']);
                }
                $user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
                //$data[$key]['avatar'] = $user_info['image'];
                if(stristr($user_info['image'], 'http'))
                {
                    $info['avatar'] = $user_info['image'];
                }
                else
                {
                    $info['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
                }
                $info['user_name'] = $user_info['user_name'];
                if($user_info['sex']=='2')
                {
                    $info['sex'] = '0';
                }
                else
                {
                    $info['sex'] = $user_info['sex'];
                }
                $info['auth'] = $user_info['auth'];
                $info['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
                $info['distance'] = strval($distance);
                $info['video_type'] = strval($value['is_off']);
                $info['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
                $info['video_dec']=$info['user_name'].'在'.$info['ipinfo'].'的精彩直播'.$info['title'].',世界那么大赶快来看看!';
                $info['is_rec']=strval($value['is_rec']);
                $info['latitude']=$info['lat'];
                $info['longitude']=$info['lng'];
                $data[] = $info;

            }

            $this->data_back($data, '0x000');  //返回数据

        }
        else
        {
            $this->data_back(array(), '0x002','fail');
        }
    }


    //获取 当地特产 special=7
    //1护肤2特产3彩妆4药品
    public function get_local_native()
    {
        $lat=trim($this->input->get('lat',TRUE));
        $lng=trim($this->input->get('lng',TRUE));
        $range = isset($_REQUEST['range']) ? trim($_REQUEST['range']) : '';
        $range_en = isset($_REQUEST['range']) ? trim($_REQUEST['range_en']) : '';
        $range='普吉';
        if(!$lat){
            $lat=0;
        }
        if(!$lng){
            $lng=0;
        }

        $tctype=trim($this->input->get_post('tctype',true));
        $order=trim($this->input->get_post('order',true));
        $page=$this->input->post('page',true);

        if(!$page)
        {
            $page=1;
        }
        $where="v_activity_children.special='5' AND v_activity_children.is_show >='1' AND v_activity_children.act_status='2'  AND v_activity_children.range_name LIKE '%$range%'  AND v_goods.is_show = '1' AND v_wx_business.is_show ='1' ";

        if($tctype)
        {
            $where.="  AND tctype = '$tctype'";
        }
        $page_num =5;
      //  $call_page_num=$page_num*$page;


        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell as goods_buy,v_activity_children.banner_product AS image,
        v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.shop_price,tctype,
        v_goods.ori_price,v_goods.oori_price,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-v_wx_business.lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(v_wx_business.lat*PI()/180)*POW(SIN(($lng*PI()/180-v_wx_business.lng*PI()/180)/2),2)))*1000) AS distance ";

        $data['products']=$this->User_model->get_special($select,$where,$start,$page_num,$order);
        foreach($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]");
        }
        $this->data_back($data, '0x000');  //返回数据
    }
//order 1距离 2销量 3价格低 4价格高
//tctype 1护肤2特产3彩妆4药品
    public function get_filter_words()
    {
        $data['order']=array(
            array('name'=>'离我最近','index'=>'1'),
            array('name'=>'销量','index'=>'2'),
            array('name'=>'价格低','index'=>'3'),
            array('name'=>'价格高','index'=>'4'),
        );
        $data['tctype']=array(
            array('name'=>'全部分类','index'=>'0'),
            array('name'=>'护肤','index'=>'1'),
            array('name'=>'特产','index'=>'2'),
            array('name'=>'彩妆','index'=>'3'),
            array('name'=>'药品','index'=>'4'),
        );

        $this->data_back($data, '0x000');  //返回数据

    }


    public function get_filter_photo_words()
    {
        $data['order']=array(
            array('name'=>'离我最近','index'=>'1'),
            array('name'=>'销量','index'=>'2'),
            array('name'=>'价格低','index'=>'3'),
            array('name'=>'价格高','index'=>'4'),
        );

        //请选择<婚纱摄影 亲子旅拍 海外旅拍 闺蜜摄影 写真 摄影 全家福
        $data['type']=array(
            array('name'=>'全部分类','index'=>'0'),
            array('name'=>'婚纱摄影','index'=>'1'),
            array('name'=>'亲子旅拍','index'=>'2'),
            array('name'=>'海外旅拍','index'=>'3'),
            array('name'=>'写真摄影','index'=>'4'),
            array('name'=>'全家福','index'=>'5'),
        );

        $this->data_back($data, '0x000');  //返回数据

    }

    //旅拍 idnex 页面
    public function get_trip_detail()
    {
        $page=trim($this->input->get_post('page'));
        $type=trim($this->input->get_post('type'));
        $order=trim($this->input->get_post('order'));
        $lat=trim($this->input->get_post('lat'));
        $lng=trim($this->input->get_post('lng'));
        if(!$lat){
            $lat=0;
        }
        if(!$lng){
            $lng=0;
        }
        $page_num =10;

        if(!$page){
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $range=trim($this->input->get('range'));
        $range='普吉';

        $whereinfo="v_ts.is_show='1'   AND range_name LIKE'%$range%' AND v_goods.is_show='1' AND v_ts.ts_id>'1'";
        if($type){
            $whereinfo.=" AND v_ts.type='$type'";
        }
        $select="v_goods.ori_price,v_goods.oori_price,v_ts.range_name,v_ts.title,v_ts.banner_hot as image,v_ts.ts_id,
        ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-v_wx_business.lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(v_wx_business.lat*PI()/180)*POW(SIN(($lng*PI()/180-v_wx_business.lng*PI()/180)/2),2)))*1000) AS distance";

        $data['info']=$this->User_model->get_trip($select,$whereinfo,$start,$page_num,$order);

        if( $data['info']==false){
            $this->data_back(array(), '0x002','fail');
        }
        $this->data_back($data, '0x000');  //返回数据

    }
//lp 详情
    public function get_trip_ts()
    {
        $ts_id=$this->input->get_post('ts_id',TRUE);
        $select="v_ts.ts_id,v_ts.user_id,v_ts.banner_image,v_ts.banner_image,v_ts.banner_product,v_ts.range_name,
        v_ts.order_sell,v_ts.content,v_ts.title,v_ts.title,v_ts.hotelstars,v_ts.hoteldays,v_ts.flight,v_ts.flighttickets,v_goods.ori_price,v_goods.oori_price,v_goods.front_price,v_ts.attr_json";
        $where="v_ts.ts_id=$ts_id AND v_goods.is_show='1'";
        $data=$this->User_model->get_one($select,$where,'v_ts','v_goods','ts_id','ts_id');
        //$data['attr_arr']=json_decode($data['attr_json'],TRUE);
       // echo '<pre>';print_r($data);
        $this->data_back($data, '0x000');  //返回数据
    }
//lp 筛选
    public function get_trip_ts_detail()
    {
        $ts_id=$this->input->get_post('ts_id',TRUE);
        $ts_id=1;
        $select="v_ts.ts_id,v_ts.attr_json,v_ts.photo_time";
        $where="v_ts.ts_id=$ts_id AND v_goods.is_show='1'";
        $data=$this->User_model->get_one($select,$where,'v_ts','v_goods','ts_id','ts_id');
        $data['attr_json']=json_decode($data['attr_json'],TRUE);
        $data['date']=array();

        foreach($data['attr_json'] as $k=>$v)
        {

            $data['date'][]=$k;
           // $data['date_val'][$k]=$v;
            foreach($v['radio'] as $k2=>$v2)
            {
                $name=$this->User_model->get_select_one('user_name,photo_starttime,photo_endtime',array('user_id'=>$v2['attr_value']),'v_users');
                if( (strtotime($k) >=$name['photo_starttime'] AND strtotime($k)<=$name['photo_endtime'])  OR ( strtotime($k)+$data['photo_time']>=$name['photo_starttime'] AND strtotime($k)+$data['photo_time']<=$name['photo_endtime'])){
                   // echo '<pre>';var_dump( strtotime($data['date'][$k2]));
                    //echo '<br>';
                    continue;
                }
                $data['date_val'][$k]['radio'][]=array('user_id'=>$v2['attr_value'],'user_name'=>$name['user_name'],'price'=>$v2['attr_price'],'url'=>'https://www.baidu.com/');
                $data['date_val'][$k]['str_price']=$v['st_price'];

            }
        }
        foreach($data['date'] as $k=>$v)
        {
            if(count($data['date_val'][$v]['radio'])==0)
            {
              unset($data['date'][$k]);
            }
        }
        $data['date']=array_values($data['date']);
        unset($data['attr_json']);
        // echo '<pre>';print_r($data);
        $this->data_back($data, '0x000');  //返回数据
    }


     public function trip_order_add()
     {

     }
}