<?php
/**
 * 阿里云回调
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Notify_oss extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
	$this->load->model('User_Api_model');
    $this->load->model('Admin_model');
  }

  /**
   * [回调操作]
   * @param  
   * @return 
   */
	public function index()
	{
		$date = date("Y-m-d");
		$log_path = '/opt/nginx/html/zxqc/logfile/oss'.$date.'.log';
		
		$json = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
		if($json)
		{
			$data = json_decode($json,true);
			if($data)
			{
				$domain = $data['domain'];
				$app = $data['app'];
				$stream = $data['stream'];
				$start_time = $data['start_time'];
				$stop_time = $data['stop_time'];
				$uri = $data['uri'];
				file_put_contents($log_path,'[domain='.$domain.'][app='.$app.'][stream='.$stream.'][uri='.$uri.'][start_time='.$start_time.'][stop_time='.$stop_time.']'.PHP_EOL,FILE_APPEND);
				$arr = explode("/",$uri);
				$detail = str_replace('.m3u8','',$arr[2]);
				file_put_contents($log_path,'detail='.$detail.PHP_EOL,FILE_APPEND);
				$video_name = $stream;
				$video_name_r = $stream.'/'.$detail;
				file_put_contents($log_path,'video_name_r='.$video_name_r.PHP_EOL,FILE_APPEND);
				//获取视频信息
				$video_info = $this->User_Api_model->get_video_info(0,'v_video',0,$video_name,1);
				if($video_info)
				{
					file_put_contents($log_path,'video exists!'.PHP_EOL,FILE_APPEND);
					$set['video_name'] = $video_name_r;
					if($start_time && $stop_time && $stop_time-$start_time<40 && $video_info['is_off'] == '1')
					{
						$set['is_off'] = '2';
					}
					$this->User_Api_model->comment_update(array('video_name'=>$video_name),$set,'v_video');
				}
				echo 'SUCCESS';
			}
			else
			{
				file_put_contents($log_path,'data is empty!'.PHP_EOL,FILE_APPEND);
				echo 'FAIL';
			}
		}
		else
		{
			file_put_contents($log_path,'json is empty!'.PHP_EOL,FILE_APPEND);
			echo 'FAIL';
		}
	//echo $app;
		//推流成功通知
		/*if($domain == 'publish')
		{
			if($id)
			{
				$video_info = $this->User_Api_model->get_video_info(0,'v_video',0,$id);
				if($video_info)
				{
					$rtmp = $this->get_rtmp($id);
					$image = '/opt/nginx/html/live-camera/uploads/'.$video_info['video_id'].'.jpg';
					sleep(3);
					$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 1 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
					exec($exec);
				}
				//$this->User_Api_model->comment_update(array('video_name'=>$id),array('is_off'=>0),'v_video');
				$myfile = fopen("logfile.txt", "a") or die("Unable to open file!");
					$log = $id." is publish at ".date("Y-m-d h:i:sa", $time)."\r\n";
					fwrite($myfile, $log);
				fclose($myfile);
			}

			echo 1;
		}
		//断流通知
		elseif($domain == 'publish_done')
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
					$myfile = fopen("logfile.txt", "a") or die("Unable to open file!");
					$log .= $id.' is publish_done at '.date("Y-m-d h:i:sa", time())."\r\n";
					fwrite($myfile, $log);
				}
				fclose($myfile);
			echo 1;
		}*/
	}
	

}