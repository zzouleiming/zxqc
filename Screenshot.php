<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Screenshot extends My_Controller {
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('User_Api_model');
	    $this->load->model('User_model');
	    $this->load->library('common');
	}

	public function index()
	{
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
		$time = time()-10;
		//$cip ='42.121.5.3';
		$video_list = $this->User_Api_model->comment_select('video_id,video_name,start_time,push_type'," is_off=0 AND start_time<$time AND types='1'",'','video_id DESC','','','v_video',1);
		//print_r($video_list);die;
		if($video_list)
		{
			foreach($video_list as $video)
			{
				$rtmp = $this->get_rtmp($video['video_name'],$video['push_type']);
				$image = '/opt/nginx/html/zxqc/uploads/'.$video['video_id'].'.jpg';
				//$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 5 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
				$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 5 -f image2 -r 1  '.$image;
				exec($exec,$info,$rtn);
				//$this->get_crop_for_video($video['video_id']);

				//截图失败-推流失败
				if($rtn)
				{
					if($time-intval($video['start_time']) > 20)
					{
						//关闭直播
						//$this->User_Api_model->comment_update(array('video_id'=>$video['video_id']),array('is_off'=>2,'stop_time'=>time()),'v_video');
					}
				}
			}
		}
	}
	/**
	获取视频流地址信息
	**/
	function get_rtmp($video_name,$push_type)
	{
		$result = '';
		$push_type = intval($push_type);
		if($video_name)
		{
			if(stristr($video_name,'rtmp://'))
			{
				$result = $video_name;
			}
			else
			{
				if($push_type == 0)
				{
					$auth_key = $this->get_auth($video_name);
					$result = 'rtmp://video.etjourney.com/etjourney/'.$video_name.'?auth_key='.$auth_key;
				}
				elseif($push_type == 1)
				{
					$result = $this->config->item('rtmp_uc_url').$video_name;
				}
			}
		}
		return $result;
	}
	
	/**
	 * [register 鉴权签名]
	 * @return [type] [description]
	*/
	function get_auth($video_name,$type='')
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
			$sign = md5('/etjourney/' . $video_name . '-' . $para .'etjourney2016');
			$result = $para.$sign;
		}
		return $result;
	}

	public function get_crop_for_video($video_id=5311){

		$url="./uploads/".$video_id.".jpg";
		$new_imag=$this->imagecropper($url,$video_id);
		$dataimage=array('imageforh5'=>$new_imag);
		$this->User_model->update_one(array('video_id'=>$video_id),$dataimage,$table='v_video');
		//echo $new_imag;

	}

	function imagecropper($source_path='./uploads/5311.jpg',$key2='time',$target_width='100', $target_height='100')
	{

		$source_info   = getimagesize($source_path);
		$source_width  = $source_info[0];
		$source_height = $source_info[1];
		$source_mime   = $source_info['mime'];
		$source_ratio  = $source_height / $source_width;
		$target_ratio  = $target_height / $target_width;

		// 源图过高
		if ($source_ratio > $target_ratio)
		{
			$cropped_width  = $source_width;
			$cropped_height = $source_width * $target_ratio;
			$source_x = 0;
			$source_y = ($source_height - $cropped_height) / 2;
		}
		// 源图过宽
		elseif ($source_ratio < $target_ratio)
		{
			$cropped_width  = $source_height / $target_ratio;
			$cropped_height = $source_height;
			$source_x = ($source_width - $cropped_width) / 2;
			$source_y = 0;
		}
		// 源图适中
		else
		{
			$cropped_width  = $source_width;
			$cropped_height = $source_height;
			$source_x = 0;
			$source_y = 0;
		}

		if($source_mime=='image/jpeg'){
			$source_image = imagecreatefromjpeg($source_path);
			$target_image  = imagecreatetruecolor($target_width, $target_height);
			$cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

			// 裁剪
			imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
			// 缩放
			imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

			$type=pathinfo($source_path,PATHINFO_EXTENSION);
			if($key2=='time'){
				$key2=time();
			}
			$new_image='./vimagecrop/'.$key2.'.'.$type;
			imagejpeg($target_image,$new_image);


			imagedestroy($source_image);
			imagedestroy($target_image);
			imagedestroy($cropped_image);
			 return $new_image;
		}elseif($source_mime=='image/png'){
			$source_image = imagecreatefrompng($source_path);
			$target_image  = imagecreatetruecolor($target_width, $target_height);
			$cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

			$alpha = imagecolorallocatealpha($target_image, 0, 0, 0, 127);
			imagefill($target_image, 0, 0, $alpha);
			$alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
			imagefill($cropped_image, 0, 0, $alpha);
			// 裁剪
			imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
			// 缩放
			imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
			$type=pathinfo($source_path,PATHINFO_EXTENSION);
			if($key2=='time'){
				$key2=time();
			}
			$new_image='./vimagecrop/'.$key2.'.'.$type;
			imagesavealpha($target_image, true);
			imagepng($target_image,$new_image);

			imagedestroy($source_image);
			imagedestroy($target_image);
			imagedestroy($cropped_image);
			return $new_image;
		}else{
			$source_image = imagecreatefromgif($source_path);
			$target_image  = imagecreatetruecolor($target_width, $target_height);
			$cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
			// 裁剪
			imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
			// 缩放
			imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
			$type=pathinfo($source_path,PATHINFO_EXTENSION);
			if($key2=='time'){
				$key2=time();
			}
			$new_image='./vimagecrop/'.$key2.'.'.$type;
			imagegif($target_image,$new_image);
			imagedestroy($source_image);
			imagedestroy($target_image);
			imagedestroy($cropped_image);
			return $new_image;
		}
	}


}


?>
