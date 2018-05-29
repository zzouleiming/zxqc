<?php
/**
 * [Addviews  直播添加ROBOT观众]
 * 服务端定时执行
 */
	 
defined('BASEPATH') OR exit('No direct script access allowed');

class Addviews extends My_Controller {
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('User_Api_model');
	    $this->load->library('common');
	}

	public function index()
	{
		/*获取请求来源IP*/
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
		}
		else{
		$cip = "无法获取！";
		}
		//echo $cip;die;
		if($cip != '114.55.65.195')
		{
			echo 'access denied!';
			die;
		}
		$batch = isset($_REQUEST['batch']) ? intval(trim($_REQUEST['batch'])) : 0;
		$add_view = '';
		$add_comm = '';
		$add_follow = '';
		$time = time();
		$time_1 = $time-10;
		//echo $time_1;
		//$cip ='42.121.5.3';
		/*获取当前直播列表*/
		$video_list = $this->User_Api_model->comment_select('video_id,user_id,video_name,start_time,socket_info'," is_off=0 AND start_time<$time_1 AND types='1'",'','video_id DESC','','','v_video',1);
		//print_r($video_list);die;
		if($video_list)
		{
			foreach($video_list as $video)
			{
				/*SOCKET信息*/
				$socket = explode(':',$video['socket_info']);
				if($socket)
				{
					$ip   = $socket[0];
					$port = $socket[1];
				}
				//echo 'video='.$video['video_id'].'<br />';
				//echo 'ip='.$ip.'<br />';
				//echo 'port='.$port.'<br />';
				$time_2 = $time - intval($video['start_time']);
				/*robot加入直播间规则*/
				$limit = 1;
				if($time_2 > 20 && $time_2 < 60)
				{
					/*直播前一分钟加入10人*/
					if(in_array($batch,array(1,3,5,7,9,11,13,15,17,19)))
					{
						$add_view = '1';
						$watch_num = 4;
						$limit = 3;
					}
				}elseif($time_2 < 180){
					/*直播第2,3分钟每分钟加入6人*/
					if(in_array($batch,array(2,3,5,6,7,10,14,18,20)))
					{
						$add_view = '1';
						//$watch_num = rand(8,12);
						$watch_num = 5;
						$limit = 2;
					}
				}elseif($time_2 < 300){
					/*直播第3分钟后每分钟加入4人*/
					if(in_array($batch,array(2,3,5,10,15,20)))
					{
						$add_view = '1';
						$watch_num = rand(2,10);
						$limit = 2;
						//$watch_num = 2;
					}
				}else{
					/*直播第3分钟后每分钟加入4人*/
					if(in_array($batch,array(5,10,15,20)))
					{
						$add_view = '1';
						$watch_num = rand(1,4);
						//$watch_num = 1;
					}
				}
				/*robot随机评论规则*/
				if(in_array($batch,array(4,8,12,16)))
				{
					$add_comm = '1';
				}
				/*robot关注主播*/
				$minute = ceil($time_2/60);
				if((in_array($minute,array(3,7,11,15,19,23,27,30)) && $batch==1) || $video['user_id']==1734 || $video['user_id']==1474)
				//if(in_array($batch,array(3,7,11,15,19,20)))
				{
					$add_follow = '1';
				}
				//echo 'add_view='.$add_view.'<br />';
				//echo 'add_comm='.$add_comm.'<br />';
				/*robot加入直播间*/
				if($add_view)
				{
					//echo 'add view start'.'<br />';
					/*获取当前可用robot用户*/
					$res = $this->User_Api_model->comment_select(' user_id,user_name,image,auth,rand() as rd '," groupid=4 ",'',' watch,rd ',0,$limit,'v_users');
					if($res)
					{
						//echo '<pre>';
						//var_dump($res);
						foreach($res as $v)
						{
							$message = array();
							$user_id = intval($v['user_id']);
							if(stristr($v['image'], 'http'))
							{
								$image = $v['image'];
							}
							else
							{
								$image = $this->config->item('base_url'). ltrim($v['image'],'.');
							}
							/*进入直播间提示信息*/
							$message[] = array('user_id'=>$v['user_id'],'user_name'=>$v['user_name'],'image'=>$image,'content'=>'来了','auth'=>$v['auth']);
							//echo 'user_list='.$user_list.'<br />';
							/*发送socket信息*/
							$this->send_msg($message,$ip,$port);
							//echo '<pre>';
							//var_dump($message);
							/*更新robot正在观看直播信息*/
							$this->User_Api_model->comment_update(" user_id=$user_id ",array('watch'=>$video['video_id']),'v_users');
							usleep(2000000);
						}
						//if(stristr($res[0]['image'], 'http'))
						//{
						//	$image = $res[0]['image'];
						//}
						//else
						//{
						//	$image = $this->config->item('base_url'). ltrim($res[0]['image'],'.');
						//}
						/*进入直播间提示信息*/
						//$message[] = array('user_id'=>$res[0]['user_id'],'user_name'=>$res[0]['user_name'],'image'=>$image,'content'=>'来了','auth'=>$res[0]['auth']);
						/*发送socket信息*/
						//$this->send_msg($message,$ip,$port);
					}
					/*更新当前直播观看数信息*/
					$this->User_Api_model->update_watchs(array('video_id'=>$video['video_id']),'v_video',$watch_num);
				}
				/*robot发表随机评论*/
				if($add_comm)
				{
					/*获取随机robot用户*/
					$user = $this->User_Api_model->comment_select(' user_id,user_name,image,auth '," watch=$video[video_id] AND groupid=4 ",'',' RAND() ',0,1,'v_users');
					/*获取随机评论语*/
					$comment = $this->User_Api_model->comment_select(' comment '," 1 ",'',' RAND() ',0,1,'v_rand_comment');
					/*拼接socket消息并发送*/
					$message = array();
					if($user && $comment)
					{
						$image = $this->config->item('base_url'). ltrim($user[0]['image'],'.');
						$message[] = array('user_id'=>$user[0]['user_id'],'user_name'=>$user[0]['user_name'],'image'=>$image,'content'=>$comment[0]['comment'],'auth'=>$user[0]['auth']);
						$this->send_msg($message,$ip,$port);
						//echo '<pre>';
						//var_dump($message);
					}
				}
				/*robot关注主播*/
				if($add_follow)
				{
					/*获取当前可用robot用户*/
					$fans = $this->User_Api_model->comment_select(' user_id,user_name,image,auth '," watch=$video[video_id] AND groupid=4 ",'',' RAND() ',0,1,'v_users');
					if($fans)
					{
						if(stristr($fans[0]['image'], 'http'))
						{
							$image = $fans[0]['image'];
						}
						else
						{
							$image = $this->config->item('base_url'). ltrim($fans[0]['image'],'.');
						}
						$param = array(
								'user_id'  => $fans[0]['user_id'],
								'fans_id'  => $video['user_id']
							);
						$count = $this->User_Api_model->count_all($param,'v_follow');
						if(empty($count))
						{
							$param['dateline'] = $time;
							$this->User_Api_model->gag_add($param,'v_follow');
							$follow[] =array('user_id'=>$fans[0]['user_id'],'user_name'=>$fans[0]['user_name'],'image'=>$image,'content'=>$fans[0]['user_name'].'关注了主播','auth'=>$fans[0]['auth']);
							$this->send_msg($follow,$ip,$port);
						}
					}
				}
			}
		}
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
			foreach($message as $v)
			{
				$in = array(
					'user_id'=>$v['user_id'],
					'user_name'=>$v['user_name'],
					'type'     =>'comment',
					'content'  =>$v['content'],
					'avatar'   =>$v['image'],
					'auth'     =>$v['auth']
					);
				$in = json_encode($in)."\n";
				@socket_write($socket, $in, strlen($in));
			}
			@socket_close($socket);
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
}


?>
