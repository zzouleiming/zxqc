<?php
/**
 * [Checksocket  SOCKET服务进程维护]
 * 服务端定时执行
 */
	 
defined('BASEPATH') OR exit('No direct script access allowed');

class Checksocket extends My_Controller {
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('User_Api_model');
	    $this->load->library('common');
	}

	public function check()
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
		$time = time() - 30;
		$online = array();
		$socket = array();
		//$cip ='42.121.5.3';
		/*获取当前直播列表*/
		$video_list = $this->User_Api_model->comment_select('video_id,video_name,start_time,socket_info'," is_off=0 AND types='1'",'','video_id DESC','','','v_video',1);
		//print_r($video_list);die;
		if($video_list)
		{
			foreach($video_list as $video)
			{
				$socket = explode(':',$video['socket_info']);
				if($socket)
				{
					$port = $socket[1];
					if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$port."' | grep -v grep"))
					{
						/*重新开启当前socket服务*/
						exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$port. ' > /dev/null 2>&1 &');
					}
				}
				/*SOCKET信息*/
				$online[] = $video['video_id'];
			}
		}
		/*获取当前socket列表*/
		$socket_list = $this->User_Api_model->comment_select('id,socket_port,open_status,video_id'," open_status=1 AND start_time<$time ",'',' start_time ','','','v_socket',1);
		if($socket_list)
		{
			foreach($socket_list as $v)
			{
				if(!in_array($v['video_id'],$online))
				{
					/*当前socket所属直播已结束*/
					if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$v['socket_port']."' | grep -v grep"))
					{
						$open_status = 0;
					}
					else
					{
						$open_status = 1;
					}
					echo 'open_status='.$open_status;
					/*更新socket信息*/
					$this->User_Api_model->comment_update(array('id'=>$v['id']),array('open_status'=>$open_status,'video_id'=>0,'user_id'=>0,'start_time'=>0,'video_name'=>''),'v_socket');
				}
			}
		}
	}
	
	public function reset()
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
		$time = time() - 180;
		/*获取当前直播列表*/
		$video_list = $this->User_Api_model->comment_select('video_id,video_name,start_time,socket_info'," is_off=0 AND types='1' AND start_time < $time ",'','video_id DESC','','','v_video',1);
		if($video_list)
		{
			foreach($video_list as $video)
			{
				$socket = explode(':',$video['socket_info']);
				if($socket)
				{
					$port = $socket[1];
					/*结束当前socket服务*/
					exec("ps aux | grep '".$this->config->item('socket_server')." ".$port."' | grep -v grep | cut -c 9-15 | xargs kill -s 9");
					/*重新开启当前socket服务*/
					exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$port. ' > /dev/null 2>&1 &');
					//echo 'port='.$port;
				}
			}
		}
	}
}


?>
