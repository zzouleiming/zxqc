<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
	public function __construct()
	{
    	parent::__construct();
    	$this->load->helper('cookie');
    	//session

    	$this->load->library('session');
		$this->load->helper('url');
    	$this->lang->load('log', 'english');
    	$this->lang->load('common', 'english');
		$this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    	//$this->load->library('priv');
    	//权限
    	$this->load->library('priv');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
    	$this->load->model('User_model');
    	$this->load->model('User_Api_model');
    	$this->load->model('Admin_model');
		$this->load->library('image_lib');
		$this->load->library('common');
  	}


	//根据userid 获取用户的语种信息
	public function new_lan_bydb($user_id){
		$rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
		$lang = $rs['lan'];
		switch ($lang) {
			case 'zh-cn' :
				$this->lang->load('jt', 'english');
				break;
			case 'zh-CN' :
				$this->lang->load('jt', 'english');
				break;
			case 'zh-tw' :
				$this->lang->load('ft', 'english');
				break;
			case 'zh-TW' :
				$this->lang->load('ft', 'english');
				break;
			case 'ja-jp' :
				$this->lang->load('jp', 'english');
				break;
			case 'ja-JP' :
				$this->lang->load('jp', 'english');
				break;
			case 'ko-kr' :
				$this->lang->load('hy', 'english');
				break;
			case 'ko-KR' :
				$this->lang->load('hy', 'english');
				break;
			case 'th-th' :
				$this->lang->load('th', 'english');
				break;
			case 'th-TH' :
				$this->lang->load('th', 'english');
				break;
			default:
				$this->lang->load('eng', 'english');
				break;
		}

	}



//根据HTTP_ACCEPT_LANGUAGE 获取用户的语种信息
 public function new_lan_byweb(){
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $lang = $matches[1];
    switch ($lang) {
      case 'zh-cn' :
        $this->lang->load('jt', 'english');
        break;
      case 'zh-CN' :
        $this->lang->load('jt', 'english');
        break;
      case 'zh-tw' :
        $this->lang->load('ft', 'english');
        break;
      case 'zh-TW' :
        $this->lang->load('ft', 'english');
        break;
      case 'ja-jp' :
        $this->lang->load('jp', 'english');
        break;
      case 'ja-JP' :
        $this->lang->load('jp', 'english');
        break;
      case 'ko-kr' :
        $this->lang->load('hy', 'english');
        break;
      case 'th-th' :
        $this->lang->load('th', 'english');
        break;
      case 'th-TH' :
        $this->lang->load('th', 'english');
        break;
      case 'ko-KR' :
        $this->lang->load('hy', 'english');
        break;
      default:
        $this->lang->load('eng', 'english');
        break;
    }

  }


	/**
	 * 
	 * [admin_header 头部]
	 * @return [type] [description]
	 */
	public function admin_header()
  	{
		if(empty($_SESSION['admin_id'])){
			return false;
		}
    	//$this->load->view('Home/header',$data);
		$this->load->view('Home/header');
  	}

  	/**
  	 * [left 导航栏]
  	 * @return [type] [description]
  	 */
	public function  left()
	{
		if(empty($_SESSION['admin_id'])){
			return false;
		}
		$rs=$this->User_model->get_count("is_display ='0'", 'v_report');
		$data['report_num']=$rs['count'];
        $this->load->view('admin/left',$data);
  	}

	//获取举报数
	public function is_new_report(){
		$rs=$this->User_model->get_count("is_display ='0'", 'v_report');
		echo $rs['count'];

	}

	//删除管理员
	public function admin_del($admin_id){
		$this->User_model->update_one(array("admin_id"=>$admin_id),array('status'=>'1'),'v_admin_user');
		redirect(base_url('admin/admin_list'));
	}
  	/**
  	 * [main 内容]
  	 * @return [type] [description]
  	 */
	public function main()
 	{
		if(empty($_SESSION['admin_id'])){
			return false;
		}
 		$this->load->view('admin/main');
 	}
	/**
	 * [orderlist 订单列表]
	 * @return [type] [description]
	 */
	public function order_list(){
		if(empty($_SESSION['admin_id'])){
			return false;
		}
		$this->load->view('admin/order_info_list');
	}
	/*
	 * [admin_list 管理员列表]
	 * @return [type] [description]
	 */
	public function admin_list($page =1)
	{
		if(empty($_SESSION['admin_id'])){
			return false;
		}
		//$this->priv->admin_priv('admin_list');

		$data['title'] = trim($this->input->get('title',true));
		$data['time1']=strtotime($this->input->get('time1',true));
		$data['time2']=strtotime($this->input->get('time2',true));
		$data['type']=trim($this->input->get('type',true));
		//echo "<pre>";print_r($data);exit();
		$where=' 1=1';
		if($data['time1']){
			$where.=" AND add_time >=$data[time1]";
		}
		if($data['time2']){
			$data['time2']+=86400;
			$where.="  AND add_time <=$data[time2]";
		}

		if($data['title'])
		{
			$where.= " AND admin_name LIKE '%$data[title]%' ";
		}
		$where.="  AND  status='0'";

	    $page_num =100;
	    $data['now_page'] = $page;
	    $count = $this->User_model->get_count($where,'v_admin_user');
	    $data['max_page'] = ceil($count['count']/$page_num);
	    if($page>$data['max_page'])
	    {
	      $page=1;
	    }
	    $start = ($page-1)*$page_num;

		$data['list'] = $this->User_model->get_select_all('admin_id,admin_name,add_time,login_time,role_name',$where,'admin_id','ASC','v_admin_user',1,'v_admin_role',"v_admin_user.role_id=v_admin_role.role_id",false,1,$start,$page_num);
		//echo $this->db->last_query();
		//echo "<pre>";print_r($data);exit();
		$data['time2']=strtotime($this->input->get('time2',true));
		$this->load->view('newadmin/admin_list',$data);
	}

	//密码修改页面
	public function password_edit_adv(){
		if(isset($_SESSION['admin_id'])){
			$admin_id=$_SESSION['admin_id'];
			$where=array('admin_id'=>$admin_id);
			$data=$this->User_model->get_select_one($select='admin_name',$where,'v_admin_user');
			unset($_COOKIE);
			$this->load->view('admin_auth/admin_password_edit',$data);
			
		}else{
			return false;
		}
	}

	//密码修改提交
	public function password_edit_sub(){
		if(isset($_SESSION['admin_id'])){
			$admin_id=$_SESSION['admin_id'];
			$where=array('admin_id'=>$admin_id);
			$old_pass=$this->input->post('password_old',true);
			$new_pass=$this->input->post('password_new',true);
			$data=$this->User_model->get_select_one('password,salt',$where,'v_admin_user');
			if($data['password']==md5(md5($old_pass).$data['salt'])){
				$new_pass=md5(md5($new_pass).$data['salt']);
				$this->User_model->update_one($where,array('password'=>$new_pass),'v_admin_user');
				unset($_SESSION['admin_id']);
				redirect(base_url('admin/index'));
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}

//登录校验

	public function act_login()
	{
		$admin_name =  $this->input->post('username',true);
		$password   =  $this->input->post('password',true);
		if(empty($admin_name))
		{
			echo '登录名称不能为空';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}
		if(empty($password))
		{
			echo '密码不能为空';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}
		$data = $this->Admin_model->check_admin_new($admin_name,$password);
		if($data)
		{
			$_SESSION['admin_id']=$data['admin_id'];
			$_SESSION['admin_name']=$data['admin_name'];
			$data1['admin_name'] = $data['admin_name'];
			$data1['admin_id'] = $data['admin_id'];
			$data1['role_id'] = $data['role_id'];
			//$data1['action_list'] = $data->action_list;

			$this->put_admin_log("管理员登录");
			echo '<meta http-equiv="refresh" content="0; url=/admin/index">';die;
		}
		else
		{
			echo '登录失败';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}
	}

	/**
	 * [allot 权限列表]
	 * @param  string $admin_id [description]
	 * @return [type]           [description]
	 */
	public function allot($admin_id='')
	{
		$this->priv->admin_priv('allot_priv');
		if(empty($admin_id))
		{
			echo  '用户id为空！';
			echo '<meta http-equiv="refresh" content="1; url=/admin/admin_list">';
		}
		/* 获得该管理员的权限 */
		$priv_str = $this->Admin_model->action_list($admin_id);
	}

	/**
	 * [logout 退出登录]
	 * @return [type] [description]
	 */
	public function logout()
	{
		unset($_SESSION['admin_id']);

		echo '<meta http-equiv="refresh" content="1; url=/admin/login">';
	}

	/**
	 * [log_info 单个用户日志列表]
	 * @param  string  $user_id [description]
	 * @param  integer $page    [description]
	 * @return [type]           [description]
	 */
	public function log_info($user_id='',$page=1)
	{
		if(isset($_SESSION['admin_id'])){
			$data['user_id'] = $user_id;
			if(empty($data['user_id']))
			{
				$where = 1;
			}
			else
			{
				$where = 'user_id=' . $data['user_id'];
			}
			$page_num =10;
			$data['now_page'] = $page;
			$count = $this->User_model->get_count($where,'v_admin_log');
			$data['max_page'] = ceil($count['count']/$page_num);
			if($page>$data['max_page'])
			{
				$page=1;
			}
			$start = ($page-1)*$page_num;
			$data['log_list'] = '';
			$data['log_list'] = $this->Admin_model->log_list($where);
			if(!empty($data['log_list']))
			{
				foreach ($data['log_list'] as $key => $value) {
					$admin_name = $this->Admin_model->user_info('v_admin_user','admin_id='.$value['user_id']);
					$data['log_list'][$key]['admin_name'] = $admin_name['admin_name'];
				}
			}
			$this->load->view('/admin/admin_logs',$data);
		}else{
			echo '登录超时';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}

	}
/*
 * 后台身份信息提交
 */
	public function guide_admin(){

		$data['user_id']=$this->input->get('user_id',true);
		if(!$data['user_id']){
			$data['user_id']=0;
		}
		$this->load->view('admin_auth/legalize_admin',$data);
	}


	//后台导游信息提交
	public function guide_admin_sub(){
		$user_id=$this->input->post('user_id',true);
		$rs=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),'v_users');
		$user_name=$rs['user_name'];
		$range=$this->input->post('range',true);

		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_num=$this->input->post('card_num',true);
		$id_view_style=$this->input->post('work',true);
		$id_view_num=$this->input->post('work_num',true);
		$id_view_status='1';
		$data=array(
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_view_style'=>$id_view_style,
			'id_view_num'=>$id_view_num,
			'id_view_status'=>$id_view_status,
			'id_auth_time'=>time(),
			'is_temp'=>'1',
			'id_range'=>$range
		);

		if($_FILES['file2']['error']==0){
			$id_view_image=$this->upload_image('file2',$user_id,'id_guide_num');
			$data['id_view_image']=$id_view_image;
			$id_view_image_thumb=$this->thumb($id_view_image,$user_id,'id_guide_num');
			$data['id_view_image_thumb']=$id_view_image_thumb;
		}else{
			$row=$this->User_model->get_select_one('id_view_image,id_view_image_thumb',array('user_id'=>$user_id),'v_auth_views',1);
			$data['id_view_image']=$row['id_view_image'];
			$data['id_view_image_thumb']=$row['id_view_image_thumb'];
		}
		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_guide');
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_guide');
			$data['id_image_thumb']=$id_image_thumb;
			$data['id_image']=$id_image;
		}else{
			$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('user_id'=>$user_id),'v_auth_views',1);
			$data['id_image']=$row['id_image'];
			$data['id_image_thumb']=$row['id_image_thumb'];
		}

		$where=array('user_id'=>$user_id,'is_temp'=>'1');
		$row=$this->User_model->get_count($where,'v_auth_views');
		//  echo "<pre>";print_r($data);exit();
		$this->put_admin_log("后台游客信息提交 $user_id");
		if($row['count']==0){
			if($this->User_model->user_insert('v_auth_views',$data)){
				//echo $this->db->last_query();
				redirect(base_url("admin/guide_admin"));
			}else{return false;}
		}else{
			if($this->User_model->update_one($where,$data,'v_auth_views')){
				redirect(base_url("admin/guide_admin"));
			}else{
				return false;
			}
		}
	}

//后台当地人信息 录入页面
	public function local_admin(){

		$data['user_id']=$this->input->get('user_id',true);
		if(!$data['user_id']){
			$data['user_id']=0;
		}
		$this->load->view('admin_auth/locals_admin',$data);
	}
	//后台当地人信息 录入操作
	public function local_admin_sub(){

		$user_id=$this->input->post('user_id',true);
		$rs=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),'v_users');
		$user_name=$rs['user_name'];
		$range=$this->input->post('range',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_num=$this->input->post('card_num',true);
		$id_local_status='1';
		$data=array(
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'id_range'=>$range,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_local_status'=>$id_local_status,
			'id_auth_time'=>time(),
			'is_temp'=>'1'
		);
		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_locals');
			$data['id_image']=$id_image;
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_locals');
			$data['id_image_thumb']=$id_image_thumb;
		}else{
			$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('user_id'=>$user_id),'v_auth_locals',1);
			$data['id_image']=$row['id_image'];
			$data['id_image_thumb']=$row['id_image_thumb'];
		}
		//echo "<pre>";print_r($data);exit();
		$where=array('user_id'=>$user_id,'is_temp'=>'1');
		$row=$this->User_model->get_count($where, 'v_auth_locals');
		//  echo "<pre>";print_r($row);exit();
		$this->put_admin_log("后台地陪信息提交 $user_id");
		if($row['count']==0){
			if($this->User_model->user_insert('v_auth_locals',$data)){

				redirect(base_url("admin/local_admin"));
			}else{return false;}
		}else{
			if($this->User_model->update_one($where,$data,'v_auth_locals')){
				redirect(base_url("admin/local_admin"));
			}else{
				return false;
			}
		}
	}

	//当地人信息修改页面
	public function local_info_edit(){
		$local_id=$this->input->get('local_id',true);
		$data['info']=$this->User_model->get_select_one('*',array('local_id'=>$local_id),'v_auth_locals');
		$this->load->view('admin_auth/locals_admin',$data);
	}

	//后台当地人信息修改操作
	public function local_admin_edit(){
		$local_id=$this->input->post('local_id',true);
		$user_id=$this->input->post('user_id',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_range=$this->input->post('range',true);
		$id_num=$this->input->post('card_num',true);
		$data=array(
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_range'=>$id_range,
			'id_auth_time'=>time(),

		);
		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_locals');
			$data['id_image']=$id_image;
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_locals');
			$data['id_image_thumb']=$id_image_thumb;
		}else{
			$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('local_id'=>$local_id),'v_auth_locals',1);
			$data['id_image']=$row['id_image'];
			$data['id_image_thumb']=$row['id_image_thumb'];
		}

		$where=array('local_id'=>$local_id);
		//echo "<pre>";print_r($data);exit();
		if($this->User_model->update_one($where,$data,'v_auth_locals')){
			$this->put_admin_log("后台地陪信息提交 $user_id");
			redirect(base_url("user/local_list"));

		}
	}

	//后台导游信息修改界面
	public function guide_info_edit(){
		$view_id=$this->input->get('view_id',true);
		$data['info']=$this->User_model->get_select_one('*',array('view_id'=>$view_id),'v_auth_views');

		$this->load->view('admin_auth/legalize_admin',$data);
	}
//导游信息修改操作
	public function guide_admin_edit(){
		$view_id=$this->input->post('view_id',true);
		$user_id=$this->input->post('user_id',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_view_style=$this->input->post('work',true);
		$id_view_num=$this->input->post('work_num',true);
		$id_num=$this->input->post('card_num',true);
		$id_range=$this->input->post('range',true);
		$data=array(
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_auth_time'=>time(),
			'id_view_style'=>$id_view_style,
			'id_view_num'=>$id_view_num,
			'id_range'=>$id_range,
		);

		if($_FILES['file2']['error']==0){
			$id_view_image=$this->upload_image('file2',$user_id,'id_guide_num');
			$data['id_view_image']=$id_view_image;
			$id_view_image_thumb=$this->thumb($id_view_image,$user_id,'id_guide_num');
			$data['id_view_image_thumb']=$id_view_image_thumb;
		}else{
			$row=$this->User_model->get_select_one('id_view_image,id_view_image_thumb',array('view_id'=>$view_id),'v_auth_views',1);
			$data['id_view_image']=$row['id_view_image'];
			$data['id_view_image_thumb']=$row['id_view_image_thumb'];
		}
		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_guide');
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_guide');
			$data['id_image_thumb']=$id_image_thumb;
			$data['id_image']=$id_image;
		}else{
			$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('view_id'=>$view_id),'v_auth_views',1);
			$data['id_image']=$row['id_image'];
			$data['id_image_thumb']=$row['id_image_thumb'];
		}
		$where=array('view_id'=>$view_id);
		//echo "<pre>";print_r($data);exit();
		if($this->User_model->update_one($where,$data,'v_auth_views')){
			$this->put_admin_log("后台导游信息提交 $user_id");
			redirect(base_url("user/guide_list"));

		}
	}
//司机信息修改页面
	public function driver_info_edit(){
		$driver_id=$this->input->get('driver_id',true);
		$data['info']=$this->User_model->get_select_one('*',array('driver_id'=>$driver_id),'v_auth_drivers');

		$this->load->view('admin_auth/driver_admin',$data);
	}
	//司机信息修改操作
	public function driver_admin_edit(){
		$driver_id=$this->input->post('driver_id',true);
		$user_id=$this->input->post('user_id',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_range=$this->input->post('range',true);
		$id_style=$this->input->post('card',true);
		$id_num=$this->input->post('card_num',true);
		$id_driver=$this->input->post('drive_num',true);
		$id_car_num=$this->input->post('travel_num',true);
		$id_car_style=$this->input->post('car_style',true);

		$data=array(
			'auth_name'=>$auth_name,
			'id_range'=>$id_range,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_driver'=>$id_driver,
			'id_car_num'=>$id_car_num,
			'id_car_style'=>$id_car_style,
			'id_auth_time'=>time(),
		);
		if(isset($_FILES['file1'])){
			if($_FILES['file1']['error']==0){
				$id_image=$this->upload_image('file1',$user_id,'id_driver');
				$data['id_image']=$id_image;
				$id_image_thumb=$this->thumb($id_image,$user_id,'id_driver');
				$data['id_image_thumb']=$id_image_thumb;
			}else{
				$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('driver_id'=>$driver_id),'v_auth_drivers',1);
				$data['id_image']=$row['id_image'];
				$data['id_image_thumb']=$row['id_image_thumb'];
			}
		}
		if(isset($_FILES['file2'])){
			if($_FILES['file2']['error']==0){
				$id_driver_image=$this->upload_image('file2',$user_id,'id_num_driver');
				$data['id_driver_image']=$id_driver_image;
				$id_driver_image_thumb=$this->thumb($id_driver_image,$user_id,'id_num_driver');
				$data['id_driver_image_thumb']=$id_driver_image_thumb;
			}else{
				$row=$this->User_model-> get_select_one('id_driver_image,id_driver_image_thumb',array('driver_id'=>$driver_id),'v_auth_drivers',1);
				$data['id_driver_image']=$row['id_driver_image'];
				$data['id_driver_image_thumb']=$row['id_driver_image_thumb'];
			}
		}
		if(isset($_FILES['file3'])){
			if($_FILES['file3']['error']==0){
				$id_car_num_image=$this->upload_image('file3',$user_id,'id_car_driver');
				$data['id_car_num_image']=$id_car_num_image;
				$id_car_num_image_thumb=$this->thumb($id_car_num_image,$user_id,'id_car_driver');
				$data['id_car_num_image_thumb']=$id_car_num_image_thumb;
			}else{
				$row= $this->User_model-> get_select_one('id_car_num_image,id_car_num_image_thumb',array('driver_id'=>$driver_id),'v_auth_drivers',1);
				$data['id_car_num_image']=$row['id_car_num_image'];
				$data['id_car_num_image_thumb']=$row['id_car_num_image_thumb'];
			}
		}
		if(isset($_FILES['file4'])){
			if($_FILES['file4']['error']==0){
				$id_car_image=$this->upload_image('file4',$user_id,'id_car_image');
				$data['id_car_image']=$id_car_image;
				$id_car_image_thumb=$this->thumb($id_car_image,$user_id,'id_car_image');
				$data['id_car_image_thumb']=$id_car_image_thumb;
			}else{
				$row= $this->User_model-> get_select_one('id_car_image,id_car_image_thumb',array('driver_id'=>$driver_id),'v_auth_drivers',1);
				$data['id_car_image']=$row['id_car_image'];
				$data['id_car_image_thumb']=$row['id_car_image_thumb'];
			}
		}

			if($this->User_model->update_one(array('driver_id'=>$driver_id),$data,'v_auth_drivers')){
				$this->put_admin_log("后台司机信息提交 $user_id");
				redirect(base_url("newadmin/drivers_list"));

		}
	}
    //后台填写认证商户信息
	public function business_admin(){
		$data['user_id']=$this->input->get('user_id',true);
		if(!$data['user_id']){
			$data['user_id']=0;
		}

		$this->load->view('admin_auth/business_admin',$data);
	}


    public function photo_admin(){
        $data['user_id']=$this->input->get('user_id',true);

        $rs=$this->User_model->get_select_one('is_carmer',array('user_id'=>$data['user_id']),'v_users');
        if(!$data['user_id']){
            $data['user_id']=0;
        }

        $this->load->view('admin_auth/photo_admin',$data);
    }
	public function business_info_edit(){
		$business_id=$this->input->get('business_id',true);
		$data['info']=$this->User_model->get_select_one('*',array('business_id'=>$business_id),'v_auth_business');

		$this->load->view('admin_auth/business_admin',$data);
	}

	public function business_admin_edit(){
		$business_id=$this->input->post('business_id',true);
		$user_id=$this->input->post('user_id',true);
		$shop_name=$this->input->post('shop_name',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_num=$this->input->post('card_num',true);

		$data=array(
			'user_id'=>$user_id,
			'shop_name'=>$shop_name,
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_auth_time'=>time(),

		);

		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_image_bussiness');
			$data['id_image']=$id_image;
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_image_bussiness');
			$data['id_image_thumb']=$id_image_thumb;
		}else{
			$row=$this->User_model->get_select_one('id_image,id_image_thumb',array('business_id'=>$business_id),'v_auth_business',1);
			$data['id_image_thumb']=$row['id_image_thumb'];
			$data['id_image']=$row['id_image'];
		}
		//echo "<pre>";print_r($data);exit();
		if($_FILES['file2']['error']==0){
			$id_business_image=$this->upload_image('file2',$user_id,'bussiness_image_bussiness');
			$data['id_business_image']=$id_business_image;
			$id_business_image_thumb=$this->thumb($id_business_image,$user_id,'id_image_bussiness');
			$data['id_business_image_thumb']=$id_business_image_thumb;
		}else{
			$row=$this->User_model->get_select_one('id_business_image,id_business_image_thumb',array('business_id'=>$business_id),'v_auth_business',1);

			echo $this->db->last_query();
			$data['id_business_image']=$row['id_business_image'];
			$data['id_business_image_thumb']=$row['id_business_image_thumb'];
		}
		$where=array('business_id'=>$business_id);
		//echo "<pre>";print_r($data);exit();
		$this->put_admin_log("后台商户信息提交 $user_id");
		if($this->User_model->update_one($where,$data,'v_auth_business')){
				redirect(base_url("newadmin/business_list"));

		}
	}


	public function business_admin_sub(){

			$user_id=$this->input->post('user_id',true);
			$rs=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),'v_users');
			$user_name=$rs['user_name'];
			$shop_name=$this->input->post('shop_name',true);
			$auth_name=$this->input->post('user',true);
			$mobile=$this->input->post('contact',true);
			$auth_wechat=$this->input->post('weixin',true);
			$id_style=$this->input->post('card',true);
			$id_num=$this->input->post('card_num',true);
			$id_business_status='1';
			$data=array(
				'user_id'=>$user_id,
				'user_name'=>$user_name,
				'shop_name'=>$shop_name,
				'auth_name'=>$auth_name,
				'mobile'=>$mobile,
				'auth_wechat'=>$auth_wechat,
				'id_style'=>$id_style,
				'id_num'=>$id_num,
				'id_business_status'=>$id_business_status,
				'id_auth_time'=>time(),
				'is_temp'=>'1'
			);

			if($_FILES['file1']['error']==0){
				$id_image=$this->upload_image('file1',$user_id,'id_image_bussiness');
				$data['id_image']=$id_image;
				$id_image_thumb=$this->thumb($id_image,$user_id,'id_image_bussiness');
				$data['id_image_thumb']=$id_image_thumb;
			}else{
				$row=$this->User_model->get_select_one('id_image,id_image_thumb',array('user_id'=>$user_id),'v_auth_business',1);
				$data['id_image_thumb']=$row['id_image_thumb'];
				$data['id_image']=$row['id_image'];
			}
		//echo "<pre>";print_r($data);exit();
			if($_FILES['file2']['error']==0){
				$id_business_image=$this->upload_image('file2',$user_id,'bussiness_image_bussiness');
				$data['id_business_image']=$id_business_image;
				$id_business_image_thumb=$this->thumb($id_business_image,$user_id,'id_image_bussiness');
				$data['id_business_image_thumb']=$id_business_image_thumb;
			}else{
				$row=$this->User_model->get_select_one('id_business_image,id_business_image_thumb',array('user_id'=>$user_id),'v_auth_business',1);
				$data['id_business_image']=$row['id_business_image'];
				$data['id_business_image_thumb']=$row['id_business_image_thumb'];
			}
			$where=array('user_id'=>$user_id,'is_temp'=>'1');
			$row=$this->User_model->get_count($where,'v_auth_business');
			$this->put_admin_log("后台商户信息提交 $user_id");
			if($row['count']==0){
				if($this->User_model->user_insert('v_auth_business',$data)){
					//echo $this->db->last_query();
					redirect(base_url("admin/business_admin"));
				}else{return false;}
			}else{
				if($this->User_model->update_one($where,$data,'v_auth_business')){
					redirect(base_url("admin/business_admin"));
				}else{
					return false;
				}
			}

	}


	public function driver_admin(){
		$data['user_id']=$this->input->get('user_id',true);
		if(!$data['user_id']){
			$data['user_id']=0;
		}

		$this->load->view('admin_auth/driver_admin',$data);
	}
	public function driver_admin_sub(){

		$user_id=$this->input->post('user_id',true);
		$rs=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),'v_users');
		$user_name=$rs['user_name'];
		$range=$this->input->post('range',true);
		$auth_name=$this->input->post('user',true);
		$mobile=$this->input->post('contact',true);
		$auth_wechat=$this->input->post('weixin',true);
		$id_style=$this->input->post('card',true);
		$id_num=$this->input->post('card_num',true);
		$id_driver=$this->input->post('drive_num',true);
		$id_car_num=$this->input->post('travel_num',true);
		$id_car_style=$this->input->post('car_style',true);
		$id_driver_status='1';
		$data=array(
			'user_id'=>$user_id,
			'user_name'=>$user_name,
			'auth_name'=>$auth_name,
			'mobile'=>$mobile,
			'auth_wechat'=>$auth_wechat,
			'id_style'=>$id_style,
			'id_num'=>$id_num,
			'id_driver'=>$id_driver,
			'id_car_num'=>$id_car_num,
			'id_car_style'=>$id_car_style,
			'id_driver_status'=>$id_driver_status,
			'id_auth_time'=>time(),
			'is_temp'=>'1',
			'id_range'=>$range
		);
		if($_FILES['file1']['error']==0){
			$id_image=$this->upload_image('file1',$user_id,'id_driver');
			$data['id_image']=$id_image;
			$id_image_thumb=$this->thumb($id_image,$user_id,'id_driver');
			$data['id_image_thumb']=$id_image_thumb;
		}else{
			$row=$this->User_model-> get_select_one('id_image,id_image_thumb',array('user_id'=>$user_id),'v_auth_drivers',1);
			$data['id_image']=$row['id_image'];
			$data['id_image_thumb']=$row['id_image_thumb'];
		}
		if($_FILES['file2']['error']==0){
			$id_driver_image=$this->upload_image('file2',$user_id,'id_num_driver');
			$data['id_driver_image']=$id_driver_image;
			$id_driver_image_thumb=$this->thumb($id_driver_image,$user_id,'id_num_driver');
			$data['id_driver_image_thumb']=$id_driver_image_thumb;
		}else{
			$row=$this->User_model-> get_select_one('id_driver_image,id_driver_image_thumb',array('user_id'=>$user_id),'v_auth_drivers',1);
			$data['id_driver_image']=$row['id_driver_image'];
			$data['id_driver_image_thumb']=$row['id_driver_image_thumb'];
		}
		if($_FILES['file3']['error']==0){
			$id_car_num_image=$this->upload_image('file3',$user_id,'id_car_driver');
			$data['id_car_num_image']=$id_car_num_image;
			$id_car_num_image_thumb=$this->thumb($id_car_num_image,$user_id,'id_car_driver');
			$data['id_car_num_image_thumb']=$id_car_num_image_thumb;
		}else{
			$row= $this->User_model-> get_select_one('id_car_num_image,id_car_num_image_thumb',array('user_id'=>$user_id),'v_auth_drivers',1);
			$data['id_car_num_image']=$row['id_car_num_image'];
			$data['id_car_num_image_thumb']=$row['id_car_num_image_thumb'];
		}
		if($_FILES['file4']['error']==0){
			$id_car_image=$this->upload_image('file4',$user_id,'id_car_image');
			$data['id_car_image']=$id_car_image;
			$id_car_image_thumb=$this->thumb($id_car_image,$user_id,'id_car_image');
			$data['id_car_image_thumb']=$id_car_image_thumb;
		}else{
			$row= $this->User_model-> get_select_one('id_car_image,id_car_image_thumb',array('user_id'=>$user_id),'v_auth_drivers',1);
			$data['id_car_image']=$row['id_car_image'];
			$data['id_car_image_thumb']=$row['id_car_image_thumb'];
		}

		$where=array('user_id'=>$user_id,'is_temp'=>'1');
		$row=$this->User_model->get_count($where, 'v_auth_drivers');
		//  echo "<pre>";print_r($row);exit();
		$this->put_admin_log("后台司机信息提交 $user_id");
		if($row['count']==0){
			if($id=$this->User_model->user_insert('v_auth_drivers',$data)){
				redirect(base_url("admin/driver_info_edit?driver_id=$id"));
			}else{return false;}
		}else{
			if($this->User_model->update_one($where,$data,'v_auth_drivers')){
				redirect(base_url("admin/driver_admin"));
			}else{
				return false;
			}
		}
	}

	public function batch_drop()
	{
		//检测权限
		$this->priv->admin_priv('logs_drop');
		foreach ($_REQUEST['checkboxes'] AS $key => $id)
        {
            $result = $this->Admin_model->log_del($id);
        }
        if ($result)
        {
            $this->admin_log('', 'remove', 'adminlog');
            echo '删除成功';
            echo '<meta http-equiv="refresh" content="1; url=/admin/log_info">';
        }
	}

	public function admin_log($sn='',$action='',$content='')
	{
		 $log_info = $this->lang->line($action) . $this->lang->line($content) .': '. addslashes($sn);
	    	$logs= array(
	    			'log_time' => time(),
	    			'user_id'  => $_SESSION['admin_id'],
	    			'log_info' => $log_info,
	    			'ip_address'=> $this->priv->real_ip()
	    	);
	    	$this->Admin_model->add_logs($logs);
	}

	public function put_admin_log($log_info){
		$admin_id= $_SESSION['admin_id'];
		$admin_name=$this->User_model->get_select_one($select='admin_name',array('admin_id'=>$admin_id),$table='v_admin_user');
		$log_info=$log_info .';管理员 '.$admin_name['admin_name'].'操作';
		$logs= array(
			'log_time' => time(),
			'user_id'  => $_SESSION['admin_id'],
			'log_info' => $log_info,
			'ip_address'=> $this->priv->real_ip()
		);
		$this->User_model->user_insert('v_admin_log',$logs);
		// $this->Admin_model->add_logs($logs);
	}
	/*
	 * 商户活动申请记录
	 */


	public function index_list($page=1)
	{
		if(isset($_SESSION['admin_id'])){
			$this->User_model->ban_user_del_all();
			$data['title'] = trim($this->input->get('title'));
			if($data['title'])
			{
				$where = " title LIKE '%$data[title]%' AND is_off=0 ";
			}
			else
			{
				//$where='1=1';
				$where  = " is_off=0 ";
			}
			$page_num =100;
			$data['now_page'] = $page;
			$count = $this->User_model->get_count($where,'v_video');
			$data['max_page'] = ceil($count['count']/$page_num);
			if($page>$data['max_page'])
			{
				$page=1;
			}
			$start = ($page-1)*$page_num;
			$data['list'] = $this->User_model->video_list($where,$start,$page_num,'v_video');
			$order_count=$this->User_model->get_count("display_order<30000", 'v_video');
			$data['order']=$order_count['count'];
			//echo "<pre>";print_r($data);exit();
			$this->load->view('admin/index_list',$data);
		}else{
			echo '登录超时';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}

	}



	//解禁操作
	public function ban_user_del(){
		$ban_id=$this->input->get('ban_id',true);
		$user_id=$this->input->get('user_id',true);
		$this->User_model->update_one(array('ban_id'=>$ban_id),array('statue'=>'1'),'v_ban_user');
		$this->new_lan_bydb($user_id);
		$info=$this->lang->line('sys_jf');
		$this->push_sys($user_id,$info);

		redirect('admin/ban_user_list');
	}


	//删除视频
	public function video_list_del(){
		$video_id=$this->input->get('video_id',true);
		$this->User_model->update_one(array('video_id'=>$video_id),array('is_off'=>4),'v_video');
		$type=$this->input->get('type',true);
		if($type==1){
			redirect(base_url('admin/index_list'));
		}elseif($type==3){
			redirect(base_url('admin/act_and_video'));
		}else{
			redirect(base_url('admin/ban_video_list'));
		}

	}


	//违规禁播视频
	public function ban_video_list($page=1){
		if(isset($_SESSION['admin_id'])){
			$data['title'] = trim($this->input->get('title'));
			if($data['title'])
			{
				$where = " title LIKE '%$data[title]%' AND is_off=3 ";
			}
			else
			{
				//$where='1=1';
				$where  = " is_off=3 ";
			}
			$page_num =100;
			$data['now_page'] = $page;
			$count = $this->User_model->get_count($where,'v_video');
			$data['max_page'] = ceil($count['count']/$page_num);
			if($page>$data['max_page'])
			{
				$page=1;
			}
			$start = ($page-1)*$page_num;

			$data['list'] = $this->User_model->video_list($where,$start,$page_num,'v_video','start_time');
			$order_count=$this->User_model->get_count("display_order<30000", 'v_video');
			$data['order']=$order_count['count'];
			if($data['list']>0){
				foreach($data['list'] as $k=>$v){
								$re=$this->User_model->get_select_one('*',"user_id=$v[user_id] AND statue='0'",'v_ban_user');
								if($re!=0){
									$data['list'][$k]['ban_id']=$re['ban_id'];
									$re['ban_in_time']=date('Y-m-d H:i:s',$re['ban_in_time']);
									$data['list'][$k]['ban_in_time']=$re['ban_in_time'];
									$re['ban_out_time']=date('Y-m-d H:i:s',$re['ban_out_time']);
									$data['list'][$k]['ban_out_time']=$re['ban_out_time'];
									$data['list'][$k]['is_show']=$re['is_show'];
									$data['list'][$k]['is_socket']=$re['is_socket'];
									$data['list'][$k]['is_letter']=$re['is_letter'];
								}else{
									$data['list'][$k]['ban_id']=0;
								}
							}
			}
			
			//echo "<pre>";print_r($data);exit();
			$this->load->view('admin/ban_video_list',$data);
		}else{
			echo '登录超时';
			echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
		}

	}
	/*
	 * 100年大礼包
	 * 3153600000
	 */
	/*public function ban_ever(){
		$user_id=$this->input->post_get('user_id',true);
		$video_id=$this->input->post_get('video_id',true);
		$row=$this->User_model->get_count(array('user_id'=>$user_id,'statue'=>'0'), 'v_ban_user');
		$rs=$this->User_model->get_select_one('video_name,start_time',array("video_id"=>$video_id),'v_video');
		$time=time()-$rs['start_time'];
		if($time<60){
			echo 1;
		}else{
			if($row['count']==0){
				$time=time()+3153600000;
				$data=array(
					'user_id'=>$user_id,
					'ban_in_time'=>time(),
					'ban_out_time'=>$time,
					'is_show'=>'1',
					'is_socket'=>'1',
					'is_letter'=>'1'
				);
				$this->User_model->user_insert('v_ban_user',$data);
				$this->User_model->update_one(array("video_id"=>$video_id),array('is_off'=>3,'display_order'=>30000),'v_video');


				$url=$this->getstopurl($rs['video_name']);
				$this->https_request($url);


				$info='抱歉，您已被系统永久封禁,不可发言，不可开播，不可私信,如有疑问，请联系客服!';
				$this->push_sys($user_id,$info);
				echo 0;
			}else{
				echo 2;
			}
		}

	}*/

	public function re_del(){
		$r_id=$this->input->get('r_id',true);
		$this->User_model->update_one("r_id=$r_id",array('is_display'=>'1'),'v_report');
		redirect(base_url('admin/repoter_list'));
	}


	public function size_up($name){
		$file_size=$_FILES[$name]['size'];
		if ($file_size>51200){
			echo "请让图片小于50k";
			exit();
		}
	}





/*
 * 增加封禁时间
 */
	public function ban_show_plus(){
		$user_id=$this->input->post('user_id',true);
		$val=$this->input->post('val',true);
		if($val==0){
			return;
		}elseif($val==1){
			$ban_time=86400;
		}elseif($val==2){
			$ban_time=604800;
		}elseif($val==3){
			$ban_time=2592000;
		}elseif($val==4){
			$ban_time=31536000;
		}elseif($val==5){
			$ban_time=3153600000;
		}
		$this->User_model->amount_update('ban_out_time',"ban_out_time+$ban_time",array('user_id'=>$user_id,'statue'=>'0'),'v_ban_user');
		$rs=$this->User_model->get_select_one('ban_out_time,is_show,is_socket,is_letter',array("user_id"=>$user_id,'statue'=>'0'),'v_ban_user');
		$time=date('Y年m月d日 H:i:s',$rs['ban_out_time']);
		$this->new_lan_bydb($user_id);
		$str_arr=$str_au=array();
		if($rs['is_show']==1){
			$str_arr[]=$this->lang->line('sys_s1');
			$str_au[]=$this->lang->line('sys_s2');
		}
		if($rs['is_socket']==1){
			$str_arr[]=$this->lang->line('sys_s3');
			$str_au[]=$this->lang->line('sys_s4');
		}
		if($rs['is_letter']==1){
			$str_arr[]=$this->lang->line('sys_s5');
			$str_au[]=$this->lang->line('sys_s6');
		}
		$str=implode(',',$str_arr);
		$str1=implode(',',$str_au);
		//$info="很抱歉，您已被{$str}，{$time}前不能{$str1}";
		$info=$this->lang->line('sys_s7').$str.','.$time.$this->lang->line('sys_s8').$str1;
		//$info="抱歉，您已被系统封禁到{$time},期间{$str}如有疑问，请联系客服!";
		$this->push_sys($user_id,$info);

		echo $val;
	}
	public function ban_do(){
		$user_id=$this->input->post('user_id',true);
		$type=$this->input->post('type',true);
		$rs=$this->User_model->get_select_one('ban_out_time,is_show,is_socket,is_letter',array("user_id"=>$user_id,'statue'=>'0'),'v_ban_user');
		$time=date('Y年m月d日 H:i:s',$rs['ban_out_time']);
		if($type==1){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_show'=>'1'),'v_ban_user');
			$this->put_admin_log("禁播用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$time.','.$this->lang->line('sys_x2');
			//$info="抱歉，您已被系统封禁到{$time},期间禁止播放如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);

			echo 1;

		}elseif($type==2){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_socket'=>'1'),'v_ban_user');
			$this->put_admin_log("禁言用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$time.','.$this->lang->line('sys_x3');
			//$info="抱歉，您已被系统封禁到{$time},期间禁止发言如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);
			echo 1;

		}elseif($type==3){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_letter'=>'1'),'v_ban_user');
			$this->put_admin_log("禁私信用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$time.','.$this->lang->line('sys_x4');
		//	$info="抱歉，您已被系统封禁到{$time},期间禁止私信如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);
			echo 1;

		}
	}

	public function ban_do_new(){
		$user_id=$this->input->post('user_id',true);
		$type=$this->input->post('type',true);
		$time=time()+86400;
		$ban_time=date('Y年m月d日 H:i:s',$time);
		if($type==1){

			$data=array('user_id'=>$user_id,
				'ban_in_time'=>time(),
				'ban_out_time'=>$time,
				'is_show'=>'1');
			$this->User_model-> user_insert('v_ban_user',$data);
			$this->put_admin_log("禁播用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$ban_time.','.$this->lang->line('sys_x2');
			//$info="抱歉，您已被系统封禁到{$ban_time},期间禁止播放如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);
			echo 1;

		}elseif($type==2){
			$data=array('user_id'=>$user_id,
				'ban_in_time'=>time(),
				'ban_out_time'=>$time,
				'is_socket'=>'1');
			$this->User_model->user_insert('v_ban_user',$data);
			$this->put_admin_log("禁言用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$ban_time.','.$this->lang->line('sys_x3');
			//$info="抱歉，您已被系统封禁到{$ban_time},期间禁止发言如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);
			echo 1;

		}elseif($type==3){
			$data=array('user_id'=>$user_id,
				'ban_in_time'=>time(),
				'ban_out_time'=>$time,
				'is_letter'=>'1');
			$this->User_model-> user_insert('v_ban_user',$data);
			$this->put_admin_log("禁私信用户 $user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_x1').$ban_time.','.$this->lang->line('sys_x4');

		//	$info="抱歉，您已被系统封禁到{$ban_time},期间禁止私信如有疑问，请联系客服!";
			$this->push_sys($user_id,$info);
			echo 1;

		}
	}
	public function ban_do_no(){
		$user_id=$this->input->post('user_id',true);
		$type=$this->input->post('type',true);
		if($type==1){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_show'=>'0'),'v_ban_user');
			$this->put_admin_log("解除禁播 用户：$user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_z1');
			$this->push_sys($user_id,$info);
			echo 1;

		}elseif($type==2){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_socket'=>'0'),'v_ban_user');
			$this->put_admin_log("解除禁言 用户：$user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_z1');
			$this->push_sys($user_id,$info);
			echo 1;

		}elseif($type==3){
			$this->User_model->update_one(array("user_id"=>$user_id,'statue'=>'0'),array('is_letter'=>'0'),'v_ban_user');
			$this->put_admin_log("解除禁私信 用户：$user_id");
			$this->new_lan_bydb($user_id);
			$info=$this->lang->line('sys_z1');
			$this->push_sys($user_id,$info);
			echo 1;

		}
	}



//友盟推送
	public function pushinfo($data,$info)
	{
		$token1=$token2= $sep = $type_1 = $type_2 = '';
		for($i=0;$i<count($data);$i++)
		{
			if($data[$i]['type'] == '1')
			{

				$token1 .= $sep.$data[$i]['device_id'];
				$sep =',';
				$type_1 = 1;

			}
			elseif($data[$i]['type'] == '2')
			{

				$token2 .= $sep.$data[$i]['device_id'];
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
			$aps['alert'] =  $info ;
			$aps['sound'] = '';
			$aps['content-available'] = 1;
			// $aps['video_info'] = $video_info;
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
			$payload['body'] = array('text'=>$info,'sound'=>'');
			$params['payload'] = $payload;
			$params['device_tokens']= $token2 ;
			$post_body = json_encode($params);
			$sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
			$array = $this->http_post_data($url . $sign, $post_body);
		}
	}

	public function http_post_data($url, $data_string) {
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

	public function video_del($video_id = '',$user_id= '',$video_name='')
	{
		$this->User_model->user_del('v_users',array('status' => 2),$user_id);
		//$this->User_Api_model->gag_del(array('video_id'=>$video_id),'v_video');
		$this->User_Api_model->update_video_zombie($video_id,'v_users');
		$url = $this->getstopurl($video_name);
		//echo $url.'<br />';
		$fh = $this->https_request($url);
		//var_dump(json_decode($fh));
		//$fh=file_get_contents('http://42.121.193.231:8080/control/drop/publisher?app=hls&name='.$video_name);
		echo '删除成功';
    	echo '<meta http-equiv="refresh" content="1; url=/Admin/index_list">';
	}

	public function send_push() {
        $this->load->view('admin/send_push');   
    }
	/*
	 * 系统消息发送
	 */
	public function push_sys($user_id,$info){
		$data=array(
			'pm_type'=>0,
			'user_id'=>$user_id,
			'message'=>$info,
			'is_new'=>1,
			'add_time'=>time()
		);
		$this->User_model->user_insert('v_prompt',$data);
	}

	public function getlan($user_id){
		$rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),$table='v_users');
		return $rs['lan'];
	}

    public function config_list()
    {
		$data['list'] = $this->Admin_model->config_list();
		$this->load->view('admin/config_list',$data);
    }

    public function config_info($id = 0)
    {
    	if(empty($id))
	    {
	      $this->load->view('admin/config_info');
	    }
	    else
	    {
	    	$data['list'] = $this->Admin_model->config_info($id);
	      	$this->load->view('admin/config_info',$data);
	    }
    }
	/*
 * 缩略图生成类
 */
	public function thumb($url,$key1,$key2='time'){
		if (!file_exists('./public/images/thumb/'.$key1))
		{
			if (!mkdir('./public/images/thumb/'. $key1,0777))
			{
				return false;
			}
		}

		$arr['image_library'] = 'gd2';
		$arr['source_image'] = $url;
		$arr['maintain_ratio'] = TRUE;
		$type=pathinfo($url,PATHINFO_EXTENSION);
		if($key2=='time'){
			$key2=time();
		}
		$arr['new_image']='./public/images/thumb/'.$key1.'/'.$key2.'.'.$type;
		$arr['width']     = 520;
		$arr['height']   = 300;

		$this->image_lib->initialize($arr);

		if($this->image_lib->resize()){
			return  $arr['new_image'];
		}



	}

    public function config_update()
    {
    	$code = trim($this->input->post('code'));
    	$values = trim($this->input->post('values'));
    	$id = trim($this->input->post('id'));
    	$info = array('value' => $values);
    	$data = $this->Admin_model->config_update($info,$id);
    	if($data)
    	{
    		echo '修改成功';
    		echo '<meta http-equiv="refresh" content="1; url=/Admin/config_list">';

    	}
    	else
    	{
    		echo '修改失败、没有改动';
    		echo '<meta http-equiv="refresh" content="1; url=/Admin/config_info/'.$id.'">';
    	}
    }

	public function getstopurl($video_name){
		$para['Action']='ForbidLiveStream';
		$para['DomainName']='video.etjourney.com';
		$para['AppName']='etjourney';
		$para['StreamName']=$video_name;
		$para['LiveStreamType']='publisher';
		$para['Format']='JSON';
		$para['Version']='2014-11-11';
		$para['AccessKeyId']='vNXHrUOlKeC7uHL9';
		$para['SignatureMethod']='HMAC-SHA1';
		$para['Timestamp']=substr(gmdate(DATE_ATOM,time()),0,19).'Z';
		$para['SignatureVersion']='1.0';
		$para['SignatureNonce']=$this->getRandChar(16);
		//$key = 'DlDvHRZ6Gv9f1IFRR4UCncC8Q9cLSu'.'&';
		$key = 'ThXh2tCuLabUhfWP3043znCBm0Vr07'.'&';
		$String = urlencode($this->formatParaMap($para));
		$String = 'GET'.'&%2F&'.$String;
		$para['Signature'] = $this->getSignature($String,$key);
		$String = $this->formatParaMap($para,1);
		$url = 'https://cdn.aliyuncs.com/?'.$String;
		
		return $url;
	}
	
	function getRandChar($length){
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;
		
		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];
		}
		return $str;
	}
/**
 * 	作用：格式化参数，签名过程需要使用
 */
function formatParaMap($paraMap,$type=0)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
				if($type || (($k=="Timestamp" || $k=="StartTime" || $k=="EndTime") && !$type))
				{
					$v = urlencode($v);
				}
				$buff .= $k . "=" . $v . "&";
		}
		$reqPar='';
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
function getSignature($str, $key) {  
    $signature = "";  
    if (function_exists('hash_hmac')) { 
        $signature = base64_encode(hash_hmac("sha1", $str, $key, true));  
    } else {  
        $blocksize = 64;  
        $hashfunc = 'sha1';  
        if (strlen($key) > $blocksize) {  
            $key = pack('H*', $hashfunc($key));  
        }  
        $key = str_pad($key, $blocksize, chr(0x00));  
        $ipad = str_repeat(chr(0x36), $blocksize);  
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack(
                'H*', $hashfunc(
                        ($key ^ $opad) . pack(
                                'H*', $hashfunc(
                                        ($key ^ $ipad) . $str  
                                )
                        )
                )
        );
        $signature = base64_encode($hmac);  
    }
    return $signature;
   }

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
	public function upload_image($filename,$fileurl,$key='time')
	{
		/* 如果目标目录不存在，则创建它 */
		if (!file_exists('./public/images/'.$fileurl))
		{
			if (!mkdir('./public/images/'. $fileurl))
			{
				return false;
			}
		}

		return $this->shangchuan($filename,$fileurl,$key);
	}

	public function shangchuan($filename,$fileurl,$key='time')
	{
		$file = $_FILES[$filename];
		switch ($file['type'])
		{
			case 'image/jpeg':
				$br = '.jpg';break;
			case 'image/png':
				$br = '.png';break;
			case 'image/gif':
				$br = '.gif';break;
			default:
				$br = false;break;
		}
		if($br)
		{
			if($key=='time'){
				$key =time();
			}

			$pic_url="./public/images/".$fileurl."/".$key.$br;
			move_uploaded_file($file['tmp_name'], $pic_url);
			return $pic_url;
		}
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
	

    //个人中心分享
    public function user_info_share()
	{

		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		set_time_limit(0);
		if(empty($user_id))
		{
            return false;
		}
		if($user_id<0)
		{
			return false;
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
						//$list[$key]['video_url'] = $this->get_rec($value['video_name'],$value['push_type']);
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

            $rs=$this->User_model->get_select_one('business_id,discount,logo_image,business_name,business_country,star_num,tag',array('user_id'=>$user_id,'is_show'=>'1'),'v_wx_business');
            if($rs!='0'){
                $data['shop']['business_id']=$rs['business_id'];
                $data['shop']['discount']=$rs['discount'];
                $data['shop']['en_dis']=(string)((1000-$rs['discount']*100)/10).'%';
                $data['shop']['image']=$rs['logo_image'];
                $data['shop']['business_name']=$rs['business_name'];


                $data['shop']['business_country']=$rs['business_country'];
				$data['shop']['star_num']=$rs['star_num'];
				 $data['shop']['tag']=$rs['tag'];
	
                $where="business_id= $rs[business_id] AND is_show='1' AND special >'2'";
                $data['shop']['goods_num']=$this->User_model->get_count($where,'v_activity_children');
                $data['shop']['goods_num']= $data['shop']['goods_num']['count'];

            }else{
                $data['shop']=array('business_id'=>'0');
            }
          //  sort($data['video_list']);
			//$this->data_back($data, '0x000');  //返回数据
			
		$this->load->view('api/personal_share',$data);
		}
	}

 }