<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
define('SESSION_KEY_PRE', 'us');
define('TRAVEL_FILE_KEY', 'Travel schedule');

class Page_poster extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Company_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');

        $this->load->model('tl/Page_role_model');
        $this->load->helper('url');
        $this->load->helper('common');

        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');

    }
  
    //头图设计
    public function index($page_id) {
		
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        //echo "<pre>";
        $data['menu_list']= $this->_get_menu();  
        $data['business_all']=$this->_get_business_all();   
        $data['business_account'] = $business_account;
        $where = array(
            'page_id'=>$page_id
        ); 
		
       $data['info'] = $this->Page_info_model->get_page_info_detail($where); 
       $data['info']['image_data'] = json_decode($data['info']['image_data'], true); 
	   $data['poster'] = @json_decode($data['info']['synthesis_image'], true);
       $data['poster']['bg'] = (!empty($data['poster']['bg']))?$data['poster']['bg']: $data['info']['image_data']['top'];
	   $data['price'] = '0000';
        if($data['info']['page_type']==1){
          $data['url']=  $this->_tour($page_id);
        }else if($data['info']['page_type']==2){
		  $data['url']=  $this->_free($page_id);
        }
	  $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
	  $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
      
        $this->load->view('usadmin/package_tour/page_poster',$this->_set_common($data));
    }
// 保存页面上传元素
    public  function element_image(){
    $business_account = $this->_get_auth();
    $data['page_id']=$this->input->post('page_id',TRUE);
    $data['synthesis_image']= $this->input->post('data',true);
    if(empty($data['page_id'])){
          $result['code'] = 0;
          $result['msg'] = "非法登录";
          return $this->ajax_return($result);
    }
     $res = $this->Page_info_model->save_page_info($data); 
     if($res){
         $log=array(
             'name'=>'操作人:'.$business_account['business_account'],
             'id'=>'页面id:'.$data['page_id'],
             'data'=>'data:'.$data['synthesis_image'],
             'xx'=>'头图编辑',
               'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
             
         );
         $this->log($log);
           $result['code'] = 1;
          $result['msg'] = "保存成功";
          return $this->ajax_return($result);  
     }
        
    }
    //上传图片
    public function upload() {
        
		$fileName = $_GET["act"];
		$this->_uploader($fileName);
       
    }
	
	public function create_poster($page_id){ 
	$type=$this->input->get('type',TRUE);
	$m=$this->input->get('m',TRUE);
	//权限判断
	//$member =$this->_is_login();
        
        $where = array(
            'page_id'=>$page_id
        ); 
		/*
       $business_id = $_SESSION["business_id"];
	   if($business_id>0){
		   $bus['business_id'] = $business_id;
        $business_account = $this->Business_info_model->get_business_info_detail($bus);
		   $share_name 	=	$business_account['share_name'];
	   }else{
       $business_account = $this->_get_auth();
	   $share_name 	=	$business_account['share_name'];
	   }*/
       $page = $this->Page_info_model->get_page_info_detail($where); 
       $page['image_data'] = json_decode($page['image_data'], true); 

       $data['page_url'] = base_url('home/package_'.($page['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$page['share_url'].'-EWM';
	   // $type=0 时 ，生成不含user_id 头图 $type=1 生成不含二维码 h5 头图 $type=2, 生成含user_id 头图片，$type=3 json返回 带二维码头图，$type=4 返回 含IMG(LOGO) +二维码头图，
		$data['qr_url'] = "http://api.etjourney.com/H5info_temp_zyx/qrcode_infos?name=".base64_encode($data['page_url'].(($member['user_id'] && $type==2)?'?u='.$member['user_id']:''));
		if($type==5){$data['qr_url'] = "http://api.etjourney.com/H5info_temp_zyx/qrcode_infos?name=".base64_encode($data['page_url'].'?m='.$m);}
		$data['poster'] = @json_decode($page['synthesis_image'], true);
		$data['poster']['bg'] = (!empty($data['poster']['bg']))?$data['poster']['bg']:$page['image_data']['top'];

		$qr = $this->_get_QR($page_id,$data['qr_url'],($member['user_id'] && $type==2)?$member['user_id']:'');

		$src = $this->_get_poster($data['poster'], $qr, $page_id, $member,  $type);
		if(is_array($src)){
          $result['errcode'] = 200;
          $result['msg'] = "成功";
		  $result['data'] = $src;
          return $this->ajax_return($result);
		}else{
			print_r($src);
		}
    }
	
	
		/*  $poster 
		头图背景
		二维码图片
		页面id
		用户
		返回图片 或ajax
		是否更新h5头图
	 */
	private function _get_poster($poster, $qr, $page_id = 0, $member = '', $upload = 0)
	{
		$path = 'uploads/poster/';

		if (!(is_dir($path))) {
			echo '不存在目录 '.$path;
			//mkdirs($path);
		}

		$md5 = md5(json_encode(array('bg' => $poster['bg'], 'data' => $poster['data'], 'user_id' =>(($member['user_id'] && $upload==2)?$member['user_id']:''), 'version' => 1)));
		$file = $page_id.'_'.(($member['user_id'] && $upload==2)?$member['user_id']:'').'_'.$upload.'_'.$md5 . '.jpg';
                
		if (!(is_file($path . $file))) {
			set_time_limit(0);
			@ini_set('memory_limit', '256M');	
			$bg = $this->_create_image($poster['bg']);
			$w = imagesx($bg);			
			$h = imagesy($bg);			
			$pw = 750; 					//画布宽度 750
			$ph = ceil($pw/$w*$h); 		//画布高度 1334，根据源背景计算
			$ph = $ph>1500?1334:$ph;
			$target = imagecreatetruecolor($pw, $ph);		
			if(function_exists('imagecopyresampled')){  
				imagecopyresampled($target, $bg, 0, 0, 0, 0, $pw, $ph, $w, $h);  
			}else{  
				imagecopyresized($target, $bg, 0, 0, 0, 0, $pw, $ph, $w, $h); 
			}
			//imagecopy($target, $bg, 0, 0, 0, 0, 750, 1334);
			imagedestroy($bg);
			$data = $poster['data'];

			foreach ($data as $d ) {
				$d = $this->_get_real_data($d);

				if($d['type'] == 'img' && ($upload==4 ||$upload==5)) {
					// 类型等于 4 时候 返回 含有img 的 图片
					$target = $this->_merge_image($target, $d, $d['src']);
				}
				 else if ($d['type'] == 'qr' && $upload!=1) {
					$target = $this->_merge_image($target, $d, $qr);
				}
				 else if ($d['type'] == 'price') {
					$target = $this->_merge_text($target, $d, $d['txt']);
				}
				else if ($d['type'] == 'nickname') {
					$target = $this->_merge_text($target, $d, $member['nickname']);
				}

			}
			/*  用户信息 不显示
                        if($member['user_name']){
				$d =array('left'=>20,'top'=>ceil($ph-35),'width'=>120,'height'=>20,'size'=>20,'color'=>'#555');
				$target = $this->_merge_text($target, $d, $member['user_name'].'  '.$member['user_mobile']);
			}
			*/			
			//imagepng($target, $path . $file);
			imagejpeg($target, $path . $file, 85);
			//imagedestroy($target);

		}
		$img = '/'.$path . $file;
			if ($upload == 0 || $upload == 4) {
				return  '<img src="'.$img.'" >';
			}elseif ($upload == 1) {
				//2002  头图编辑权限判断
				if(!$this->_is_access('2002')['access']) return '<p style="text-align:center;color:#333;font-size:26px; margin-top:50px;">没有权限！</p>';;
			//保存图片到 page_info
                     // return $img;
                     $h5['h5_image']=$img;
                     $h5['page_id']=$page_id;
                     $this->Page_info_model->save_page_info($h5);
					 return '<p style="text-align:center;color:#333;font-size:26px; margin-top:50px;">H5头图更新成功！</p><img style="width:100%;" src="'.$img.'" >';
			}elseif($upload == 2){
				return ' <meta name="viewport" content="width=device-width, initial-scale=1"><img style="width:100%;" src="'.$img.'" >'.'<br><p style="color:#666;font-size:20px; margin-top:20px;">长按保存或转发上方的【专属头图】<br><br><small style="color:#999;">专属头图说明：<br>1.通过该头图扫码浏览或者下单（需要页面开通下单功能）的直客全部归属于您个人<br>2.页面客服信息绑定的是您自己的手机号和微信号（需要在个人中心填写微信），直客只能联系到您<br>3.同行与OP（自己的产品除外）都无法获取您渠道下的直客信息（保障您的客户信息隐私）</small></P>';
			}
		return array('img' => $img, 'qr' => $qr);
	}
	
	// 计算尺寸位置
	private function _get_real_data($data)
	{
		$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
		$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
		$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
		$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
		$data['size'] = intval(intval(str_replace('px', '', $data['size'])) * 1.5);
		//字体大小转磅值
		//$data['src'] = tomedia($data['src']);
		return $data;
	}
	
	private function _create_image($imgurl)
	{  
		if (substr($imgurl, 0, 1) == '/') {
			$imgurl = substr($imgurl, 1);
			return imagecreatefromstring(file_get_contents($imgurl));
			}
		return '';
	}	
	
	
	private function _merge_image($target, $data, $imgurl)
	{
		$img = $this->_create_image($imgurl);
		$w = imagesx($img);
		$h = imagesy($img);
		
		if(function_exists('imagecopyresampled')){  
            imagecopyresampled($target, $img, $data['left'], $data['top'], 0, 0,$data['width'], $data['height'], $w, $h);  
        }else{  
            imagecopyresized($target,$img, $data['left'], $data['top'], 0, 0,$data['width'], $data['height'],$w, $h);  
        }  
        //call_user_func_array($img_func,array($thum_handle,get_thum_name($img_name,$suffix),$quality));  
		
		
		
		
		imagedestroy($img);
		return $target;
	}

	private function _merge_text($target, $data, $text)
	{
		$font = 'public/static/poster/css/msyhb.ttf';
		$colors = $this->_hex_to_rgb($data['color']);
		$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
			$wh = 0;$ww = 0;$ws = 0;
		if($data['size']<=14){
			$wh = 2;$ww = -2;
		}elseif($data['size']<=26){
			$wh = 0;$ww = -4; $data['size'] +=1;
		}elseif($data['size']<=40){
			$wh = 2;$ww = -4;
		}elseif($data['size']<=60){
			$wh = 3;$ww = -4;
		}elseif($data['size']<=70){
			$wh = 3;$ww = -4;
		}elseif($data['size']<=80){
			$wh = 5;$ww = -4;
		}elseif($data['size']<=100){
			$wh = 5;$ww = -4;
		}elseif($data['size']<=120){
			$wh = 7;$ww = -4;
		}else{
			$wh = 8;$ww = -4;
		}
		
		imagettftext($target, $data['size'], 0, $data['left']+$ww, $data['top'] + $data['size'] + $wh, $color, $font, $text);
		return $target;
	}

	private function _hex_to_rgb($color)
	{
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}


		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		}
		 else if (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		}
		 else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array('red' => $r, 'green' => $g, 'blue' => $b);
	}

	
	private function _get_QR($page_id,$imgurl,$user_id =0){
		
		$savepath = "uploads/qr/";
		$succeed = false;
	
	set_time_limit(0);
	if (substr($savepath, -1) != '/') $savepath .= '/';
	
	if (!is_dir($savepath)) @mkdir($savepath, 0777);
	$newpath = $savepath.$page_id.'_'.$user_id.'_code.jpg';

	$data = $this->_get_url_content($imgurl);
		
	if (strlen($data) != 1984) {
		if ($data) {
			$fp = @fopen($newpath, "w");
       		@fwrite($fp, $data);
       		@fclose($fp);
			
			$succeed = true;
		}
	}

	if ($succeed) {
		return '/'.$newpath;
	} else {
		return $succeed;
	}
		
		
	}
	
	
private function _uploader($fileName) {
        $files = array();
        $files['name'] = $_FILES[$fileName]['name'];   //上传图片的原名字
        $files['info'] = ""; //和该文件上传相关的信息
        $files['size'] = $_FILES[$fileName]['size'];   //已上传文件的大小，单位为字节
        $files['type'] = $_FILES[$fileName]['type'];   //文件的 MIME 类型，需要浏览器提供该信息的支持，例如"image/gif"
        $files['success'] = false;            //这个用于标志该图片是否上传成功
        $files['path'] = '/';                //存图片路径

        //：0:表示没有发生错误
        if($_FILES[$fileName]['error']==0){
            //is_uploaded_file — 判断文件是否是通过 HTTP POST 上传的
            if(is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
                    //扩展名
                 $extension = '';
                 //strcmp — 二进制安全字符串比较 （区分大小写）
               // 如果 str1 小于 str2 返回 < 0； 如果 str1 大于 str2 返回 > 0；如果两者相等，返回 0。
                 if(strcmp($_FILES[$fileName]['type'], 'image/jpeg') == 0) {
                     $extension = '.jpg';
                  }else if(strcmp($_FILES[$fileName]['type'], 'image/png') == 0) {
                     $extension = '.png';
                  }else if(strcmp($_FILES[$fileName]['type'], 'image/gif') == 0) {
                     $extension = '.gif';
                 }else{
                     //如果type不是以上三者，我们就从图片原名称里面去截取判断去取得(处于严谨性)
                     //strrchr — 查找指定字符在字符串中的最后一次出现
                     $substr = strrchr($_FILES[$fileName]['name'], '.');
                     if(FALSE != $substr) {
                         $files['info'] = "文件类型有误";
                     }
                      //strcasecmp — 二进制安全比较字符串（不区分大小写），比较字符串是否相同
                     //如果 str1 小于 str2 返回 < 0； 如果 str1 大于 str2 返回 > 0；如果两者相等，返回 0。
                     //取得原名字的扩展名后，再通过扩展名去给type赋上对应的值
                     if(strcasecmp($substr, '.jpg') == 0 || strcasecmp($substr, '.jpeg') == 0 || strcasecmp($substr, '.jfif') == 0 || strcasecmp($substr, '.jpe') == 0 ) {
                         $files['type'] = 'image/jpeg';
                     }else if(strcasecmp($substr, '.png') == 0) {
                         $files['type'] = 'image/png';
                     } else if(strcasecmp($substr, '.gif') == 0) {
                         $files['type'] = 'image/gif';
                     }else {
                         $files['info'] = "上传的文件类型不符合";
                     }
                     $extension = $substr;//赋值扩展名

                 }

                 if(trim($files['info'])==""){
                    //对临时文件名加密，用于后面生成复杂的新文件名
                    $md5 = md5_file($_FILES[$fileName]['tmp_name']);
                    //取得图片的大小
                    //getimagesize() 函数将测定任何 GIF，JPG，PNG，SWF，SWC，PSD，TIFF，BMP，IFF，JP2，JPX，JB2，JPC，XBM
                    // 或 WBMP 图像文件的大小并返回图像的尺寸以及文件类型
                    //和一个可以用于普通 HTML 文件中 IMG 标记中的 height/width 文本字符串。
                    $imageInfo = getimagesize($_FILES[$fileName]['tmp_name']);
                    $rawImageWidth = $imageInfo[0];
                    $rawImageHeight = $imageInfo[1];
                    //设置图片上传路径，放在upload文件夹，以年月日生成文件夹分类存储，

                    $path = 'uploads/' . @date("Ymd"). '/';
                    ///确保目录可写
                    if($this->ensure_writable_dir($path)){
                        ////文件名
                        $name = $md5."_{$rawImageWidth}x{$rawImageHeight}{$extension}";
                        //加入图片文件没变化到，也就是存在，就不必重复上传了，不存在则上传
                        $ret = file_exists($path . $name) ? true : move_uploaded_file($_FILES[$fileName]['tmp_name'], $path . $name);
                        if ($ret === false) {
                            $files['info'] = "上传失败";
                        } else {
                            $files['path'] = "/uploads/" . @date("Ymd"). '/'. $name;        //存图片路径
                            $files['success'] = true;            //图片上传成功标志
                            $files['width'] = $rawImageWidth;    //图片宽度
                            $files['height'] = $rawImageHeight;    //图片高度
                            $files['info'] = "上传成功";//写入成功
                        }
                    }else{
                        $files['info'] = "目录不可写";//目录不可写
                    }
                 }

            }else{
                $files['info'] = "上传失败";//上传失败
            }

        }


        echo json_encode(array(
            'success' =>  $files['success'],
            'path' => $files['path'],
            'info'=>$files['info'],
         ));
    }


    /**
     * 判断是否可写
     * @param $dir
     * @return bool
     */
private function ensure_writable_dir($dir) {
        if(!file_exists($dir)) {
            mkdir($dir, 0766, true);
            chmod($dir, 0766);
            chmod($dir, 0777);
        }
        else if(!is_writable($dir)) {
            chmod($dir, 0766);
            chmod($dir, 0777);
            if(!is_writable($dir)) {
               return false;
            }
        }
        return true;
    }
	
    //获取页面通用链接
    private function _get_urls(){
        $data['login_out_url'] = base_url('usadmin/business/login_out');
        $data['pwd_edit_url'] = base_url('usadmin/business/pwd_edit');
        $data['change_business_url'] = base_url('usadmin/business/change_business');
        $data['package_tour_url'] = base_url('usadmin/package_tour/index');
        $data['free_tour_url'] = base_url('usadmin/free_tour/index');
       $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        return $data;
    }
	
	    //校验登录
    private function _get_auth() {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if(!$business_account_id){
			echo '<p style="text-align:center;color:#333;font-size:26px; margin-top:50px;">没有权限！</p>';exit;
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

    //获取所有后台商户
    private  function _get_business_all($type=1){
        $where = array(
            'business_type' => $type
        );
        $business_all = $this->Business_info_model->get_business_info_list($where);
        return $business_all;
    }
    // 左侧列表菜单
    private function _get_menu() {
         $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
      if(in_array('5003',$data['access_list'])){
        $data['menu']=array(
           'H5页面'=>array(
               '0'=>array(
                     'url'=>base_url('usadmin/package_tour/index'),
                     'title'=>'跟团游',
               ),
              '1'=>array(
                  'url'=>base_url('usadmin/package_tour/page_monitor_index'),
                  'title'=>'自由行',
              ) 
           ),    
        );
        } 
         
       
        if(in_array('5001',$data['access_list'])){
         $data['menu']['订单列表'][0]= array(
                   'url'=>base_url('usadmin/Package_user_list'),
                  'title'=>'订单列表',  
              );
        }
        if(in_array('1004',$data['access_list'])){
         
             $data['menu']['权限管理'][0]= array(
                   'url'=>base_url('usadmin/package_role/role_list'),
                  'title'=>'职位管理',   
              );
        }
             if(in_array('3001',$data['access_list'])){
      
              $data['menu']['权限管理'][1]= array(
                   'url'=>base_url('usadmin/package_role/user_list'),
                  'title'=>'销售员管理',  
              );
        }
          if(in_array('3002',$data['access_list'])){
         $data['menu']['权限管理'][2]= array(
                   'url'=>base_url('usadmin/package_role/admin_list'),
                  'title'=>'后台用户管理',  
             );
        }
                              if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][0]= array(
                   'url'=>base_url('cg/car_purchase/index'),
                  'title'=>'页面管理',  
             );
        }
                   if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][1]= array(
                   'url'=>base_url('cg/car_purchase/car_page_info'),
                  'title'=>'导游管理',  
             );
        }
             if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][2]= array(
                'url'=>base_url('cg/car_purchase/car_goods_order'),
                  'title'=>'订单管理',  
             );
        }
                       if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][0]= array(
                'url'=>'http://new.cnzz.com/v1/login.php?siteid=1258510548" target="_blank"',
                  'title'=>'cszz统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][1]= array(
                'url'=>' https://tongji.baidu.com/web/24998121/visit/toppage?siteId=11585653" target="_blank"',
                  'title'=>'百度统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][2]= array(
                'url'=>'https://portal.qiniu.com/cdn/refresh-prefetch" target="_blank"',
                  'title'=>'刷新缓存',  
             );
        }

        return $data['menu'];
    }

	    //获取通用头部左侧菜单
    private function _set_common($data){
        $data['header'] = $this->load->view('usadmin/common/header', $data, true);
        $data['menu'] = $this->load->view('usadmin/common/menu', $data, true);
        //$data['show_count_code'] = $this->show_count_code();
        $data['footer'] = $this->load->view('usadmin/common/footer', $data, true);
        return $data;
    }
	
 /** 获取指定URL内容 */
	private function _get_url_content($url) {
	if (empty($url)) {
    	return false;
	}
	
	if (substr($url, 0, 7) != 'http://') {
		$url = 'http://'.$url;
	}
	
	$timeout = 30;
    $data = '';

    for ($i = 0; $i < 5 && empty($data); $i++) {
		if (function_exists('curl_init')) {
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
			
        	$data = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_code != '200') {
				return false;
			}
        } elseif (function_exists('fsockopen')) {
			$params = parse_url($url);
			$host = $params['host'];
			$path = $params['path'];
			$query = $params['query'];
			$fp = @fsockopen($host, 80, $errno, $errstr, $timeout);
			if (!$fp) {
				return false;
			} else {
				$result = '';
				$out = "GET /" . $path . '?' . $query . " HTTP/1.0\r\n";
				$out .= "Host: $host\r\n";
				$out .= "Connection: Close\r\n\r\n";
				@fwrite($fp, $out);
				$http_200 = preg_match('/HTTP.*200/', @fgets($fp, 1024));
				if (!$http_200) {
					return false;
				}

				while (!@feof($fp)) {
                if ($get_info) {
                    $data .= @fread($fp, 1024);
                } else {
                    if (@fgets($fp, 1024) == "\r\n") {
                        $get_info = true;
                    }
                }
            }
            @fclose($fp);
        }
        } elseif (function_exists( 'file_get_contents')) {
			if (!get_cfg_var('allow_url_fopen')) {
				return false;
			}
            $context = stream_context_create(
				array('http' => array('timeout' => $timeout))
			);
            $data = @file_get_contents($url, false, $context);
        } else {
			return false; 
		}
	}
			
	if (!$data) {
		return false;
    } else {
        return $data;
		}
	}

    // 跟团游导航栏菜单
    private function _tour($page_id){
 
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]= array(
                'url'=>base_url('usadmin/package_tour/page_edit/'.$page_id),
                'title'=>'基础内容',
            );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
             'url'=>base_url('usadmin/package_tour/page_trip/'.$page_id),
              'title'=>'行程编辑',   
             );
        }
        if(in_array('2004',$data['access_list'])){
         $data['tour'][2]=array(
              'url'=>  base_url('usadmin/package_tour/page_price/'.$page_id),
              'title'=>'价格上传'
          );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array(
              'url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑'
          );    
        }
                 if(in_array('2003',$data['access_list'])){
         $data['tour'][4]= array(
                'url'=>base_url('usadmin/package_tour/page_home/'.$page_id),
                'title'=>'特色图上传',
            );
        }
        
        
        return $data['tour'];
    }
  //自由行导航栏菜单
    
    private function _free($page_id){
       
         //头图编辑-2002  基础内容-2003   价格上传-2004    行程上传/产品编辑-2005   导航编辑-2006
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]=array(
         'url'=>base_url('usadmin/package_tour/free_page_edit/'.$page_id),
          'title'=>'基础内容',    
        );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
                 'url'=>base_url('usadmin/package_tour/article/'.$page_id),
              'title'=>'产品编辑',   
            );
        }
        if(in_array('2006',$data['access_list'])){
         $data['tour'][2]=array(
             'url'=>  base_url('usadmin/package_tour/addmenu/'.$page_id),
              'title'=>'导航编辑'  
         );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array('url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑');    
        }
        
        return $data['tour'];
    }
    
   public  function log($list= array()){
    $file  ='zweb/log/'.date('Y-m-d',time()).'_log.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
    $time=  date('Y-m-d H:i:s',  time());
   $content = $time.'  :'.implode(' ',$list)."\n";

  file_put_contents($file, $content,FILE_APPEND);// 这个函数支持版本(PHP 5) 
     file_get_contents($file); // 这个函数支持版本(PHP 4 >= 4.3.0, PHP 5) 
 
        
    }
      //前台 用户信息数据
  private function _is_login(){
      $list=$_SESSION;    
    if(empty($list)){
    $url=base_url('home/user/login');
    header("Location: $url");
    }
    return $list;
  }
	
// 后台 取用户权限数据
  private function _is_access($role=''){
       $business_account = $this->_get_auth();
       $where=array(
            'role_id'=>$business_account['role_id'],
            'is_del'=>0,
        );
 
      $access_list = $this->Page_role_model->get_role_detail($where);
      $access_list = json_decode($access_list['access_list'], true);

      if(in_array($role,$access_list)){ 
          
         return array('access'=>true , 'access_list' =>$access_list );
      }else{
         return array('access'=>false , 'access_list' =>$access_list );
      }
  }	
}