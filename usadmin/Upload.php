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

    //上传图片
    public function upload() {
        
		$fileName = $_GET["act"];
		$this->_uploader($fileName);
       
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
                        $name = "$md5.0x{$rawImageWidth}x{$rawImageHeight}{$extension}";
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
	

    // 取用户信息数据
  private function _is_login(){
      $list=$_SESSION;    
    if(empty($list)){
    $url=base_url('home/user/login');
    header("Location: $url");
    }
    return $list;
  }
	
// 取用户权限数据
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