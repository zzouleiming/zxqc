<?php
/**
 * 商户商品买卖
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Imagetest extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    ini_set('php_mbstring','1');
    $this->load->model('User_model');
    $this->load->model('Order_model');
    $this->load->library('common');
    $this->load->library('session');
    $this->load->library('imagecrop');
    $this->load->helper('url');
    $this->load->library('image_lib');
    // $this->load->driver('cache');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
  }
 
 



  //图像处理方法
  public function upload_image($filename,$fileurl,$key='time')
  {
    if (!file_exists('./public/images/'.$fileurl))
    {
      if (!mkdir('./public/images/'. $fileurl))
      {
        return FALSE;
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
        $br = FALSE;break;
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
  //缩略图
  public function thumb($url='./public/images/2510/id_guide.jpg',$key1='test',$key2='time',$width='700',$height='300'){
    if (!file_exists('./public/images/thumb/'.$key1))
    {
      if (!mkdir('./public/images/thumb/'. $key1,0777))
      {
        return FALSE;
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
    $arr['width']     = $width;
   /// $arr['height']   = $height;

    $this->image_lib->initialize($arr);

    if($this->image_lib->resize()){
      return  $arr['new_image'];
      //echo $arr['new_image'];
    }

  }
/*
 * /**
* Author : smallchicken
* Time   : 2009年6月8日16:46:05
* mode 1 : 强制裁剪，生成图片严格按照需要，不足放大，超过裁剪，图片始终铺满
* mode 2 : 和1类似，但不足的时候 不放大 会产生补白，可以用png消除。
* mode 3 : 只缩放，不裁剪，保留全部图片信息，会产生补白，
* mode 4 : 只缩放，不裁剪，保留全部图片信息，生成图片大小为最终缩放后的图片有效信息的实际大小，不产生补白
* 默认补白为白色，如果要使补白成透明像素，请使用SaveAlpha()方法代替SaveImage()方法
*
* 调用方法：
*
* $ic=new ImageCrop('old.jpg','afterCrop.jpg');
* $ic->Crop(120,80,2);
* $ic->SaveImage();
*        //$ic->SaveAlpha();将补白变成透明像素保存
* $ic->destory();
*
*
*/


  //裁剪图
  public function crop($url='./public/images/1265/id_driver.jpg',$key1='test',$key2='time1',$targetw='700',$targeth='300'){

      $type=pathinfo($url,PATHINFO_EXTENSION);
      $destsrc='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;

    list($src_w,$src_h)=getimagesize($url); // 获取原图尺寸
    $dst_scale = $targeth/$targetw; //目标图像长宽比
    $src_scale = $src_h/$src_w; // 原图长宽比
    if($src_scale>=$dst_scale)
    {
// 过高
      $w = intval($src_w);
      $h = intval($dst_scale*$w);
      $x = 0;
      $y = ($src_h - $h)/2;
    }
    else
    {
// 过宽
      $h = intval($src_h);
      $w = intval($h/$dst_scale);
      $x = ($src_w - $w)/2;
      $y = 0;
    }
// 剪裁
    $source=imagecreatefromjpeg($url);
    $croped=imagecreatetruecolor($w, $h);
    imagecopy($croped,$source,0,0,$x,$y,$src_w,$src_h);
// 缩放
    $scale = $targetw/$w;
    $target = imagecreatetruecolor($targetw, $targeth);
    $final_w = intval($w*$scale);
    $final_h = intval($h*$scale);
    imagecopyresampled($target,$croped,0,0,0,0,$final_w,$final_h,$w,$h);
// 保存
    //$timestamp = time();
    imagejpeg($target, $destsrc);
    echo $destsrc;
    imagedestroy($target);
  }


  public function crop_outci($source_path,$key1='test',$key2='time',$target_width='700',$target_height='300'){
    if (!file_exists('./public/images/crop/'.$key1))
    {
      if (!mkdir('./public/images/crop/'. $key1,0777))
      {
        echo 'false';
        //return FALSE;
      }
    }
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

    switch ($source_mime)
    {
      case 'image/gif':
        $source_image = imagecreatefromgif($source_path);
        break;

      case 'image/jpeg':
        $source_image = imagecreatefromjpeg($source_path);
        break;

      case 'image/png':
        $source_image = imagecreatefrompng($source_path);
        break;

      default:
        return false;
        break;
    }

    $target_image  = imagecreatetruecolor($target_width, $target_height);
    $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

    // 裁剪
    imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
    // 缩放
    imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
    //echo $target_image;
    //header('Content-Type: image/jpeg');
    if($key2=='time'){
      $key2=time();
    }
    $type=pathinfo($source_path,PATHINFO_EXTENSION);
    $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
    imagejpeg($target_image,$new_image);

    imagedestroy($source_image);
    imagedestroy($target_image);
    imagedestroy($cropped_image);
    return $new_image;
  }
  /**
   * 得到新订单号
   * @return  string
   */
  public function get_order_sn()
  {
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
  }
  public function get_lan_user(){
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $lang = $matches[1];
    return $lang;
  }
  public function get_lan_bydb($user_id){
    $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
    // echo $rs['lan'];
    return $rs['lan'];
  }
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
      case 'ko-KR' :
        $this->lang->load('hy', 'english');
        break;
      default:
        $this->lang->load('eng', 'english');
        break;
    }

  }
  /*
 * 获取流地址
 */
  public function get_rtmp($video_name)
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
          $result = 'rtmp://video.etjourney.com/etjourney/'.$video_name.'?auth_key='.$auth_key;
        }
      }
    }
    return $result;
  }
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

  public function get_city_country($dimension,$longitude){
    $position = $this->geocoder($dimension,$longitude);
    $position = json_decode($position,TRUE);
    if($position){
      $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
      $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
      $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
      if($position['status']==0 && empty($country)){
        return array('no','no');
      }else{
        return array($country,$city);
      }
    }else{
      return array('no','no');
    }
  }
  function geocoder($dimension, $longitude)
  {
    $url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
    $result = file_get_contents($url);
    $result = substr($result,29);
    $result = substr($result, 0, -1);
    if($this->input->get('test1')){
      echo $result;
    }
    return $result;
  }

  public function put_admin_log($log_info){
    $admin_id= $_SESSION['admin_id'];
    $admin_name=$this->User_model->get_select_one($select='admin_name',array('admin_id'=>$admin_id),$table='v_admin_user');
    $log_info=$log_info .';管理员 '.$admin_name['admin_name'].'操作';
    $logs= array(
        'log_time' => time(),
        'user_id'  => $_SESSION['admin_id'],
        'log_info' => $log_info,
        'ip_address'=> $this->real_ip()
    );
    $this->User_model->user_insert('v_admin_log',$logs);
    // $this->Admin_model->add_logs($logs);
  }

  public function real_ip()
  {
    static $realip = NULL;

    if ($realip !== NULL)
    {
      return $realip;
    }

    if (isset($_SERVER))
    {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

        /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
        foreach ($arr AS $ip)
        {
          $ip = trim($ip);

          if ($ip != 'unknown')
          {
            $realip = $ip;

            break;
          }
        }
      }
      elseif (isset($_SERVER['HTTP_CLIENT_IP']))
      {
        $realip = $_SERVER['HTTP_CLIENT_IP'];
      }
      else
      {
        if (isset($_SERVER['REMOTE_ADDR']))
        {
          $realip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
          $realip = '0.0.0.0';
        }
      }
    }
    else
    {
      if (getenv('HTTP_X_FORWARDED_FOR'))
      {
        $realip = getenv('HTTP_X_FORWARDED_FOR');
      }
      elseif (getenv('HTTP_CLIENT_IP'))
      {
        $realip = getenv('HTTP_CLIENT_IP');
      }
      else
      {
        $realip = getenv('REMOTE_ADDR');
      }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
  }
  public function get_map_info(){
    $select='v_video.video_id,v_video.user_id,
      location,v_users.user_name,v_users.image as user_image,
      views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
    $data=$this->User_model->get_select_all($select,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
    //echo '<pre>';print_r($data);exit();
    if($data===FALSE){
      $this->data_back(array('result'=>'fail'));
    }else{
      foreach($data as $k=>$v){
        $data[$k]['level']=$this->get_level($v['credits']);
        if($v['is_off']==1){
          $data[$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
        }else{
          $data[$k]['path']=$this->get_rtmp($v['video_name']);
        }
      }
      $this->data_back($data,'0X000','success');
    }
  }

  public function get_map_video_info(){
    $video_id=$this->input->get_post('video_id',true);
    //$video_id='4021';
    if(!$video_id){
      $this->data_back('参数为空','0X011','fail');
    }else{
      $where=array('video_id'=>$video_id);
      $row=$this->User_model->get_select_one('views,praise,video_name,user_id,title,location,image,is_off,socket_info',$where,'v_video');
      $user_id=$row['user_id'];
      $where=array('user_id'=>$user_id);
      $data=$this->User_model->get_select_one('user_name,user_id,credits,image as user_image,sex,is_guide,is_attendant,is_driver,is_merchant,auth',$where,'v_users');

      //$data=$this->User_model->get_select_one('user_name,user_id,image as user_image',$where,'v_users');
      $data['level']=$this->get_level($data['credits']);
      $data['video_id']=$video_id;
      $data['title']=$row['title'];
      $data['location']=$row['location'];
      $data['image']=$row['image'];
      $data['is_off']=$row['is_off'];
      $data['socket_info']=$row['socket_info'];

      if($row['is_off']==1){
        $data['path']=$this->config->item('record_url').$row['video_name'].'.m3u8';
        $data['views']=$row['views'];
        $data['praise']=$row['praise'];
      }else{
        $data['path']=$this->get_rtmp($row['video_name']);
      }

      $this->data_back($data,'0X000','success');

    }
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
  //微信接口调用
  public function wx_js_para($wx_id,$url='')
  {
    $where=array('wx_id'=>$wx_id);
    $result=$this->User_model->get_select_one('app_id,app_secret',$where,'wx_acctoken_info');
    //echo $this->db->last_query();
    //echo "<pre>";print_r($result);exit();
    if($result)
    {
      $appid     = $result['app_id'];
      $secret = $result['app_secret'];
    }else{
      return false;
    }
    $timestamp = time();
    $wxnonceStr = $this->createNonceStr();
    $wxticket =  $this->wx_get_js_ticket($appid,$secret);
    if(empty($url))
    {
      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

      $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    $wxOri = "jsapi_ticket=$wxticket&noncestr=$wxnonceStr&timestamp=$timestamp&url=$url";;
    $signature = sha1($wxOri);
    $para = array(
        'appid'      => $result['app_id'],
        'timestamp'  => $timestamp,
        'wxnonceStr' => $wxnonceStr,
        'signature'  => $signature
    );

    return $para;
  }

  public function wx_get_js_ticket($appid,$secret)
  {
    $ticket = "";
    $time = time() - 7000;
    $where=array('app_id'=>$appid);
    $ticket_info=$this->User_model->get_select_one('jsapi_ticket,jsapi_time',$where,'wx_acctoken_info');

    if(!empty($ticket_info['jsapi_ticket']) && $ticket_info['jsapi_time'] > $time){
      $ticket = $ticket_info['jsapi_ticket'];
    }else{
      $token = $this->get_actoken($appid,$secret);
      $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
      $jsapi_ticket = file_get_contents($url);
      $jsapi_ticket = json_decode($jsapi_ticket, true);
      $ticket = $jsapi_ticket['ticket'];
      $jsapi_time = time();

      $data=array(
          'jsapi_ticket'=>$ticket,
          'jsapi_time'=>$jsapi_time,
      );
      $this->User_model->update_one($where,$data,'wx_acctoken_info');
    }
    return $ticket;
  }

  public function createNonceStr($length = 16)
  {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function get_actoken($appid,$secret)
  {
    $token = "";
    $where=array('app_id'=>$appid);
    $token_info=$this->User_model->get_select_one('access_token,access_time',$where,'wx_acctoken_info');
    if(!empty($token_info)){
      $time = time() - 7000;
      if($token_info['access_time'] > $time && !empty($token_info['access_token'])){
        $token = $token_info['access_token'];
      }else{
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        $acc_token = file_get_contents($url);
        $acc_token = json_decode($acc_token, true);
        $token = $acc_token['access_token'];
        $acc_time = time();
        $data=array(
            'access_token'=>$token,
            'access_time'=>$acc_time,
        );
        $this->User_model->update_one($where,$data,'wx_acctoken_info');
      }
    }else{
      $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
      $acc_token = file_get_contents($url);
      $acc_token = json_decode($acc_token, true);
      $token = $acc_token['access_token'];
      //$acc_time = time();
      //$GLOBALS['db']->query("INSERT INTO wx_acc_token SET access_token='$token', access_time='$acc_time' ");
    }
    return $token;
  }

  public function data_back($info, $msg = '', $result = 'success')
  {
    $data_arr = array('result'=>$result, 'msg'=>$msg, 'info'=>$info);
    die(json_encode($data_arr));
  }
  public 	/**
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
  public function get_test(){
    // $time1=time();
    $select='v_video.video_id,v_video.user_id,
      location,v_users.user_name,v_users.image as user_image,
      views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
    $data=$this->User_model->get_select_all($select,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
    //echo '<pre>';print_r($data);exit();
    if($data===FALSE){
      $this->data_back(array('result'=>'fail'));
    }else{
      foreach($data as $k=>$v){
        $data[$k]['level']=$this->get_level($v['credits']);
        if($v['is_off']==1){
          $data[$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
        }else{
          $data[$k]['path']=$this->get_rtmp($v['video_name']);
        }
      }
      $this->data_back($data,'0X000','success');
    }
  }
}