<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Map extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('cookie');
        $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp');
        $this->load->library('image_lib');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    }

    public function get_lan_user(){
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        return $lang;
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
    public function get_lan(){
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        switch ($lang) {
            case 'zh-cn' :
                return "jt";
                break;
            case 'zh-CN' :
                return "jt";
                break;
            case 'zh-tw' :
                return "ft";
                break;
            case 'zh-TW' :
                return "ft";
                break;
            default:
                return "eng";
                break;
        }
    }
    /*
*地图——食物
*/
    public function map_food(){
        $data['count_url']=$this->count_url;
        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }else{
            $data["me_location"]=array(31.220,121.480);
        }

      //  $this->p($_COOKIE['olook']);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('map/food',$data);
    }
    /*
     *地图——景点
     */
    public function map_attractions(){
        $data['count_url']=$this->count_url;
        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }else{
            $data["me_location"]=array(31.220,121.480);
        }
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('map/attractions',$data);
    }
    /*
    *地图——休闲
    */
    public function map_leisure(){
        $data['count_url']=$this->count_url;
        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }else{
            $data["me_location"]=array(31.220,121.480);
        }
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('map/leisure',$data);
    }
    /*
      *地图——购物
      */
    public function map_shopping(){
        $data['count_url']=$this->count_url;
        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }else{
            $data["me_location"]=array(31.220,121.480);
        }
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('map/shopping',$data);
    }
    /*
     *地图酒店
     */
    public function map_hotels(){
        $data['count_url']=$this->count_url;
        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }else{
            $data["me_location"]=array(31.220,121.480);
        }
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('map/hotels',$data);
    }
    /*
      * 地图路线
       */
    public function map_line(){
        $data['count_url']=$this->count_url;
        $makid=$this->input->get('id',true);
        if(!$makid){
            return false;
        }
        $data['video_id']=$makid;
        $row=$this->User_model->get_select_one('location',array('video_id'=>$makid),$table='v_video');
        //print_r($row);exit();
        $_SESSION['mak_location']=$data['mak_location']=explode(',',$row['location']);

        if(isset( $_SESSION['me_location'])){
            $data["me_location"]=$_SESSION['me_location'];
        }


         if(isset($_SESSION['country'])){
         $data['country']=$_SESSION['country'];
        }else{
             $data['country']=1;
        }

        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
        {
            $this->load->view('map/line_ios',$data);

        }else{

            $this->load->view('map/line',$data);
        }

    }
    public function map_bus(){
        $data['count_url']=$this->count_url;
        $makid=$this->input->get('id',true);
        if(!$makid){
            return false;
        }
        $data['video_id']=$makid;
        $data['mak_location']=$_SESSION['mak_location'];
        $data["me_location"]=$_SESSION['me_location'];

         if(isset($_SESSION['country'])){
         $data['country']=$_SESSION['country'];
        }else{
             $data['country']=1;
        }

        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
        {
            $this->load->view('map/bus_ios',$data);

        }else{

            $this->load->view('map/bus',$data);
        }

    }
    public function map_driving(){
        $data['count_url']=$this->count_url;
        $makid=$this->input->get('id',true);
        if(!$makid){
            return false;
        }
         if(isset($_SESSION['country'])){
         $data['country']=$_SESSION['country'];
        }else{
             $data['country']=1;
        }
        // echo "<pre>";print_r($_SESSION);exit();
        $data['video_id']=$makid;
        $data['mak_location']=$_SESSION['mak_location'];
        $data["me_location"]=$_SESSION['me_location'];

        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
        {
            $this->load->view('map/driving_ios',$data);

        }else{

            $this->load->view('map/driving',$data);
        }

    }
    function geocoder($dimension, $longitude)
    {
        $url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
        $result = file_get_contents($url);
        $result = substr($result,29);
        $result = substr($result, 0, -1);

        return $result;
    }

    public function country_city($dimension,$longitude){
        $position = $this->geocoder($dimension,$longitude);
        $position = json_decode($position,true);
        if($position){
            $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
            $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
            $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
            if($position['status']==0 ){
                $json_arr=array($country,$province,$city);
                $str=implode(',',$json_arr);
                return $str;
            }else{
                return 'no';
            }
        }else{
            return 'no';
        }
    }



    /*
     * 地图请求资料
     */
    public function map_get_info(){
        $video_id=$this->input->post('video_id',true);
        //$video_id='4021';
        $where=array('video_id'=>$video_id);
        $row=$this->User_model->get_select_one('views,praise,video_name,user_id,title,location,image,is_off,socket_info,push_type',$where,'v_video');
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
            if($row['push_type']==0)
            {
                $data['path']= $this->config->item('record_url').$row['video_name'].'.m3u8';
            }
            else
            {
                $data['path']= $this->config->item('record_uc_url').$row['video_name'].'.m3u8';
            }
            $data['views']=$row['views'];
            $data['praise']=$row['praise'];
        }else{
            $data['path']=$this->get_rtmp($row['video_name']);
        }
        $data=json_encode($data);
        echo $data;

        // $data
    }
/*
 *
 */
    public function get_level($credits=0)
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
                }elseif($this->config->item('rtmp_flg') == 2){
                    $auth_key = $this->get_auth($video_name);
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

    public function p($a){
        echo "<pre>";
        print_r($a);
        exit();
    }

}