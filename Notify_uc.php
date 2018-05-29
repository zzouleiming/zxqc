<?php
/* *
支付宝回调
 */
// 订单控制器类
class Notify_uc extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
	$this->load->model('User_Api_model');
    $this->load->model('Admin_model');
  }
  // $method参数只能是'return'或'notify'，对应URL
  public function callback ($method) {
		$id       = isset($_REQUEST['id']) ? trim($_REQUEST['id']) :'';
		$node     = isset($_REQUEST['node']) ? trim($_REQUEST['node']) :'';
		$ip       = isset($_REQUEST['ip']) ? trim($_REQUEST['ip']) :'';
		$app      = isset($_REQUEST['app']) ? trim($_REQUEST['app']) :'';
		$appname  = isset($_REQUEST['appname']) ? trim($_REQUEST['appname']) :'';
		$time     = time();
		//推流成功通知
		if($method == 'publish_start')
		{
			if($id)
			{
				$video_info = $this->User_Api_model->get_video_info(0,'v_video',0,$id);
				if($video_info)
				{
					$rtmp = $this->get_rtmp($id);
					$image = '/opt/nginx/html/zxqc/uploads/'.$video_info['video_id'].'.jpg';
					sleep(3);
					$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 1 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
					exec($exec);
				}
				//$this->User_Api_model->comment_update(array('video_name'=>$id),array('is_off'=>0),'v_video');
				file_put_contents('/opt/nginx/html/zxqc/logfile/uc_notify'.$date.'.log','[id='.$id.'][start at'.date("Y-m-d h:i:sa", time()).']'.PHP_EOL,FILE_APPEND);
			}

			echo 1;
		}
		//断流通知
		elseif($method == 'publish_stop')
		{
			if($id)
			{
				$video_info = $this->User_Api_model->get_video_info(0,'v_video',0,$id,2);
				//$myfile = fopen("logfile.txt", "a") or die("Unable to open file!");
				//$log = ' video_id= '.$video_info['video_id']."\r\n";
				//$log .= ' is_off= '.$video_info['is_off']."\r\n";
				if($video_info)
				{
					if($video_info['is_off'] == '0')
					{
					//$log .= ' is_off= '.$video_info['is_off']."\r\n";
						$is_off = $time-intval($video_info['start_time']) >= 60 ? 1 : 2;
						if($is_off ==1 && !file_exists('/opt/nginx/html/zxqc'.ltrim($video_info['image'],'.')))
						{
							$is_off = 2;
						}
						$this->User_Api_model->comment_update(array('video_name'=>$id),array('is_off'=>$is_off,'stop_time'=>$time),'v_video');
					}
					elseif($video_info['is_off'] == '3')
					{
					//$log .= ' is_off= '.$video_info['is_off']."\r\n";
						$this->User_Api_model->comment_update(array('video_name'=>$id),array('stop_time'=>$time),'v_video');
					}
					$this->User_Api_model->comment_update(array('watch'=>intval($video_info['video_id'])),array('watch'=>0),'v_users');
				}
				$where = "video_name='".$id."'";
				$socket = $this->User_Api_model->get_socket_list("*",$where,"",'',0,1,"v_socket");
				if($socket)
				{
					$this->User_Api_model->comment_update(array('video_name'=>$id),array('open_status'=>0,'video_id'=>0,'user_id'=>0,'start_time'=>0,'video_name'=>''),'v_socket');
					$cmd = "ps aux | grep '".$this->config->item('socket_server')." ".$socket[0]['socket_port']."' | grep -v grep | cut -c 9-15 | xargs kill -s 9";
					exec($cmd);
				}
					file_put_contents('/opt/nginx/html/zxqc/logfile/uc_notify'.$date.'.log','[id='.$id.'][stop at'.date("Y-m-d h:i:sa", time()).']'.PHP_EOL,FILE_APPEND);
				}
				fclose($myfile);
			echo 1;
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
					$result = 'rtmp://video.olook.tv/olook/'.$video_name.'?auth_key='.$auth_key;
				}
			}
		}
		return $result;
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
			$sign = md5('/olook/' . $video_name . '-' . $para .$this->config->item('cdn_key'));
			$result = $para.$sign;
		}
		return $result;
	}

}