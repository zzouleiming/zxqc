<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2016/5/12
 * Time: 10:20
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Video extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        ini_set('php_mbstring','1');
        $this->load->model('User_model');
        $this->load->library('common');
        $this->load->library('session');
        $this->load->helper('url');
       // $this->load->driver('cache');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
        $this->load->model('User_Api_model');
        $this->load->model('Admin_model');
         $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    }
    public function cash_test(){
      //  $data['list']=$this->User_model->get_select_all('*','1=1','cash_time','DESC', 'v_cash_log');

       // echo "<pre>";print_r($data);exit();
    	//echo "string1";
    	//return 'string1' ;

       var_dump('01'==1);
        var_dump(in_array('01',array(1)));
       

    }

    public function index(){
        if(isset($_SESSION['admin_id'])){
            Header("Location: http://api.etjourney.com/newadmin/index");
        }else{

            return false;
        }
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
    public function lantoweb()
    {
        $this->new_lan_byweb();
        echo $this->lang->line('sys_test');
    }

    public function phpinfo()
    {
        phpinfo();
    }
    public function get_rutp()
    {
        $video_id    = $this->input->get_post('video_id',true);
        $where = " video_id= '$video_id' AND is_off=0 ";
        $count = $this->User_model->get_count($where,'v_video');
        if($count['count']==0){
            return 0;
        }else{
            return 1;
        }
    }

    public function see_olook()
    {
        $data['count_url']=$this->count_url;
        if(isset($_COOKIE['video_id'])){
            $video_id= $_COOKIE['video_id'];
            // $video_id=$this->input->get('video_id',true);
            $data = $this->User_model->get_video_info($video_id);
            $data['url'] = $this->get_rtmp($data['video_name'],'.m3u8');
            $data['rtmp_url'] = $this->get_rtmp($data['video_name']);
            if($data['push_type']==0){
                $data['lb_url'] = $this->config->item('record_url').$data['video_name'].'.m3u8';
            }else{
                $data['lb_url'] = $this->config->item('record_uc_url').$data['video_name'].'.m3u8';
            }
            if(stristr($data['image'], 'http')===false)
            {
                $data['image'] = $this->config->item('base_url') . ltrim($data['image'],'.');
            }

             $this->load->view('video/see_olook',$data);
            // $this->load->view('video/see',$data);
        }elseif($this->input->get('video_id')){

             $video_id= $this->input->get('video_id');

            $data = $this->User_model->get_video_info($video_id);
            $data['url'] = $this->get_rtmp($data['video_name'],'.m3u8');
            $data['rtmp_url'] = $this->get_rtmp($data['video_name']);

            if($data['push_type']==0){
                $data['lb_url'] = $this->config->item('record_url').$data['video_name'].'.m3u8';
            }else{
                $data['lb_url'] = $this->config->item('record_uc_url').$data['video_name'].'.m3u8';
            }


            if(stristr($data['image'], 'http')===false)
            {
                $data['image'] = $this->config->item('base_url') . ltrim($data['image'],'.');
            }

             $this->load->view('video/see_olook',$data);
        }

        //echo "<pre>";print_r($data).exit();
      //  $this->load->view('video/see_olook',$data);
       // $this->load->view('video/see',$data);
    }

    public function see(){
        $data['count_url']=$this->count_url;
        $video_id=$this->input->get('video_id',true);
        if(!isset($_SESSION['admin_id']))
        {
            redirect("http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$video_id}");
        }
        $data = $this->User_model->get_video_info($video_id);
        $bool=$this->get_rutp($video_id);
        if($bool)
        {
            $data['url'] = $this->get_rtmp($data['video_name'],'.m3u8');
            $data['rtmp_url'] = $this->get_rtmp($data['video_name']);
        }
        else
        {
            if($data['push_type']==0){
                $data['url'] = $this->config->item('record_url').$data['video_name'].'.m3u8';
            }else{
                $data['url'] = $this->config->item('record_uc_url').$data['video_name'].'.m3u8';
            }

        }
       
//echo "<pre>"; print_r($data);
        if(stristr($data['image'], 'http')===false)
        {
            $data['image'] = $this->config->item('base_url') . ltrim($data['image'],'.');
        }

        if($this->input->get('test')){
            echo "<pre>";print_r($data);
        }

        $this->load->view('video/see',$data);
    }

    public function see_hot()
    {
        $data['count_url']=$this->count_url;
        $data['list']=$this->User_model->get_hot_video();
        //echo "<pre>";print_r($data);exit();
        foreach($data['list'] as $k=>$v){
            if(mb_strlen($v['user_name'])>=3){
            $data['list'][$k]['user_name']=mb_substr($v['user_name'],0,3).'...';
            }
        }
        $data['signPackage']=$this->wx_js_para(1);
       // echo "<pre>";print_r($data);exit();


        $this->load->view('video/hot',$data);
    }
    function get_rtmp($video_name,$type='')
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
                    if($type)
                    {
                        $result = 'http://42.121.193.231:8080/hls/'.$video_name.$type;
                    }else{
                        $result = 'rtmp://42.121.193.231/hls/'.$video_name;
                    }
                }
                elseif($this->config->item('rtmp_flg') ==2)
                {
                    $auth_key = $this->get_auth($video_name,$type);
                    if($type)
                    {
                        $result = $this->config->item('hls_uc_url').$video_name.'/playlist.m3u8'.'?auth_key='.$auth_key;
                        //$result = 'http://video.etjourney.com/etjourney/'.$video_name.$type.'?auth_key='.$auth_key;
                    }else{
                        $result = $this->config->item('rtmp_url').$video_name.'?auth_key='.$auth_key;
                    }
                }
            }
        }
        return $result;
    }
/*
 * 打印HTTP_USER_AGENT
 */
    public function see_user(){
       // echo "<pre>";
        $json=$_SERVER['HTTP_USER_AGENT'];
        $json=json_encode($json);
        //echo $json;
        die($json);
    }


    public function act_list_goods()
    {
        //$where=" v_activity_children.is_show='1' AND v_activity_children.act_status='2'  AND v_goods.goods_id IS NOT NULL AND ((v_activity_father.act_status='2' AND v_activity_father.is_show='1') OR v_activity_children.special='2')";
        $where=" v_activity_children.is_show='1' AND v_activity_children.act_status='2'  AND v_goods.goods_id IS NOT NULL AND (v_activity_father.act_status='2' AND v_activity_father.is_show='1') ";
        $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.banner_image,v_activity_children.banner_product,v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price";

        $data['list']=$this->User_model->get_products_list_all($select,$where,0,100);
        foreach($data['list'] as $k=>$v)
        {
           // $banner_product= $this->imagecropper($v['banner_image'],'banner_product','time',$width='100',$height='100');
            //$banner_hot= $this->imagecropper($v['banner_image'],'banner_hot','time',$width='341',$height='240');
            //$this->User_model-> update_one(array('act_id'=>$v['act_id']),array('banner_product'=>$banner_product,'banner_hot'=>$banner_hot),$table='v_activity_children');
        }


        echo '<pre>';
        print_r($data);
    }

    public function imagecropper($source_path='./public/images/1265/id_driver.jpg',$key1='test',$key2='time',$target_width='100', $target_height='100')
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
                $key2=microtime();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
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
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
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
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagegif($target_image,$new_image);
            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            return $new_image;
        }
    }



    public function fans_list()
    {
        $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        $page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
        $fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
        //if($fans_id){
        $fans_user_arr=array();
        $fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
        foreach($fans_user_arr_2 as $v){
            $fans_user_arr[]=$v['fans_id'];
        }
        //echo '<pre>';print_r($fans_user_arr);
        if(!$user_id)
        {
            $this->data_back("参数为空", '0x011','fail');
        }
        $select = $where = $group_by = $order_by = '';
        $page_num =10;
        //$data['now_page'] = $page;
        if($fans_id)
        {
            $where = " fans_id=$fans_id ";
        }
        else
        {
            $where = " fans_id=$user_id ";
        }
        $count = $this->User_model->get_count($where,'v_follow');
        if(empty($count['count']))
        {
            $this->data_back("没有关注者", '0x017','fail');
        }
        $start = ($page-1)*$page_num;
        $select = ' user_id ';
        $order_by = " dateline ASC ";
        $data['list'] = $this->User_Api_model->comment_select($select,
            $where,$group_by,$order_by,$start,$page_num,'v_follow');
        if(!empty($data['list']))
        {
            foreach ($data['list'] as $key => $value) {
                //$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[fans_id]");
                $user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),
                    'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',
                    $this->config->item('catch_time'));
                if(stristr($user_info['image'], 'http'))
                {
                    $data['list'][$key]['avatar'] = $user_info['image'];
                }
                else
                {
                    $data['list'][$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
                }
                $data['list'][$key]['user_name'] = $user_info['user_name'];
                if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id'])){
                    $is_follow='1';
                }else{
                    $is_follow='0';
                }

                $data['list'][$key]['follow'] = $is_follow;
                $data['list'][$key]['sex'] = $user_info['sex'];
                $data['list'][$key]['auth'] = $user_info['auth'];
                $data['list'][$key]['level'] = $this->common->get_level($user_info['credits']);
                $data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
            }
           // echo '<pre>';
           // print_r($data);exit();
            $this->data_back($data, '0x000');  //返回数据
        }
        else
        {
            $this->data_back('没有关注者', '0x017','fail');  //返回数据
        }
    }



    public function data_back($info, $msg = '', $result = 'success')
    {
        $data_arr = array('result'=>$result, 'msg'=>$msg, 'info'=>$info);
        die(json_encode($data_arr));
    }


/*
 * 后台僵尸评论列表
 */
    public function comment_list($page=1){

        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count('1=1','v_rand_comment');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page']) {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $data['list']=$this->User_model-> get_select_more('*','1=1',$start, $page_num, 'c_id','DESC','v_rand_comment');
        $this->load->view('activity/commet',$data);
    }

    public function com_add(){
        $this->load->view('activity/commet_add');
    }

    public function com_insert(){
        $com=$this->input->post('com',true);
        $this->User_model->user_insert('v_rand_comment',array('COMMENT'=>$com));
        redirect('video/comment_list');
    }
    public function com_sub(){
        $com=$this->input->post('com',true);
        $c_id=$this->input->post('c_id',true);
        $this->User_model-> update_one(array('c_id'=>$c_id),array('COMMENT'=>$com),'v_rand_comment');
        redirect('video/comment_list');
    }
    /*
     * 评论删除
     */

    public function com_del(){
        $c_id=$this->input->get('c_id',true);
        $page=$this->input->get('page',true);
        $this->User_model->del(array('c_id'=>$c_id),'v_rand_comment');
        redirect("video/comment_list/{$page}");

    }

    public function com_edit(){
        $c_id=$this->input->get('c_id',true);
        $data=$this->User_model->get_select_one($select='*',array('c_id'=>$c_id),'v_rand_comment');
        $data['edit']=1;
        $this->load->view('activity/commet_add',$data);
    }



    function get_auth($video_name,$type='')
    {
        $result = '';
        if($video_name)
        {
            $end  = intval(substr($video_name,-10)) + 86400;
            if($type)
            {
                $video_name .= $type;
            }
            $para = $end . '-0-0-';
            $sign = md5('/etjourney/' . $video_name . '-' . $para .$this->config->item('cdn_key'));
            $result = $para.$sign;
        }
        return $result;
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

    public function http_request($url,$data = null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //ssl֤�鲻����
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_USERAGENT,$this->useragent());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$this->curl_timeout);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
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

    public function bus_user_list(){
        $new_data=array();
        $data=$this->User_model->get_select_all($select='user_id,account,user_name,password',array('groupid'=>'1','is_merchant'=>'1',),$order_title='user_id', $order='ASC',$table='v_users');
        foreach($data as $k=>$v){
            $rs=$this->User_model->get_select_one('id_num',array('user_id'=>$v['user_id'],'is_temp'=>'0'),'v_auth_business');
            if($rs!==0){
                $new_data[]=$v;
               // $this->User_model->update_one(array('user_id'=>$v['user_id']),array('account'=>$rs['id_num'],'password'=>md5($rs['id_num'])),$table='v_users');
            }
            $data[$k]['id_num']=$rs['id_num'];


        }
        echo "<pre>";
        print_r($new_data);exit();
    }


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
    public function share_url(){
        $data['count_url']=$this->count_url;
        $data['list']= $this->User_model->get_select_all($select='*','1=1','id','DESC','v_share_url');
        $this->load->view('video/url',$data);
    }

    public function video_list(){
        set_time_limit(0);
        $data=$this->User_model->get_select_all($select='video_id,lat,lng',array('is_off'=>'1'),$order_title='video_id', $order='ASC',$table='v_video');
       // echo '<pre>';print_r($data);exit();
        foreach($data as $k=>$v){
            $all_address=$this->get_address($v['lat'],$v['lng']);
            $data[$k]['all_address']=$all_address;
            //$this->User_model->update_one(array('video_id'=>$v['video_id']),array('all_address'=>$all_address),$table='v_video');
        }
        //echo 1;
      echo '<pre>';print_r($data);exit();
    }


    public function get_address($dimension='31.235260', $longitude='121.373459'){
        $position = $this->geocoder($dimension,$longitude);
      // echo '<pre>';print_r($position);exit();
        $all_address='未知';
        if($position){
            $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
            $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
            $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
            $all_address=$country.' '.$city;
            //echo $all_address;
           return $all_address;
        }
    }
    function geocoder($dimension, $longitude)
    {
        $result = '';
        //$res = $this->http_post_data($this->config->item('baidu_map_url'), $param);
        $url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
        //$url = $this->config->item('baidu_map_url').'?ak=GU1rfcDjP4ZEZVZQo3UBA3jH8Q2x2RKY&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
        $result = file_get_contents($url);
        $result = substr($result,29);
        $result = substr($result, 0, -1);
        $fileType = mb_detect_encoding($result , array('UTF-8','GBK','LATIN1','BIG5'));
        if( $fileType != 'UTF-8'){
            $result = mb_convert_encoding($result ,'utf-8' , $fileType);
        }
        $result = json_decode($result,true);

        return $result;
    }
    public function url_del(){
        $id=$this->input->get('id',true);
        $this->User_model->del(array('id'=>$id),'v_share_url');
        redirect('video/share_url');
    }
    public function memtest_ori(){
        $m = new Memcache();
        $m->addServer('localhost', 11211);
        $m->set('int', 99);
        $m->increment('int');
        var_dump($m->get('int'));
    }
    public function memtest(){
        $this->load->driver('cache');
       // $this->cached->memcached->is_supported();
         $this->cache->memcached->save('foo',10);
         var_dump($this->cache->increment('foo'));
       // $this->cache->increment('foo',2);
        echo $this->cache->memcached->get('foo');
    }

    //h5  视频 次数增加
    public function h5_video_count_up($title=0,$type=0)
    {

        if($title===0)
        {
            $title=$this->input->post_get('title',true);
        }
            $src=$this->input->post_get('src',true);
        if($title)
        {
            $title_md5=md5($title);
            $m = new Memcached();

          //  $m->flush();
            $m->addServer('localhost', 11211);
            $data=$m->get($title_md5);

            if(!$data)
            {
                $info=$this->User_model->get_select_one('*',array('hash_title'=>$title_md5),'v_h5_video');
                if(!$info)
                {
                    $this->User_model->user_insert('v_h5_video',array('video_title'=>$title,'hash_title'=>$title_md5,'src'=>$src));
                    $m->set($title_md5, 0, 604800);
                    $m->set($title_md5.'time', time(), 604800);
                }else{
                    $m->set($title_md5, $info['video_count']+1, 604800);
                    $m->set($title_md5.'time', time(), 604800);
                }
            }else{
                $m->increment($title_md5,1);

                $time=$m->get($title_md5.'time');
                $timenow=time();
                $data=$m->get($title_md5);
                if(($timenow-$time)>1200)
                {
                    $this->User_model->update_one(array('hash_title'=>$title_md5),array('video_count'=>$data),'v_h5_video');
                }
            }
            if(!$type)
            {
                echo json_encode(array('title'=>$title,'count'=>intval($data)));
            }else{
                return intval($data);
            }

        }

    }

    public function get_h5_count()
    {
        $info=$this->User_model->get_select_all('*','1=1','id','ASC','v_h5_video');
        $arr=[];
        foreach($info as $k=>$v)
        {
            $arr['info'][]=array('title'=>$v['video_title'],'count'=>$v['video_count']);
        }

        echo '<pre>';print_r($arr);

    }

    public function flush_memcache()
    {
        $m = new Memcached();
        $m->addServer('localhost', 11211);
        $m->flush();
    }
    public function get_video_info(){
        $video_id=$this->input->post_get('video_id',true);
       // $this->load->driver('cache',array('adapter' => 'memcached'));
        //$this->cached->memcached->is_supported();
        $m = new Memcached();

        //$m->flush();
        $m->addServer('localhost', 11211);
      //  $m->flush();
        $data=$m->get($video_id);
        //print_r($data);exit();
        //$data=$this->cache->memcached->get($video_id);
      if(!$data){

            $data=$this->User_model->get_select_all($select='v_video.video_id,v_video.image as image,
            v_video.push_type,v_users.image as user_image,
        v_video.user_id,v_users.user_name,v_video.title,v_video.views,v_video.video_name',
                array('v_video.video_id'=>$video_id),'start_time','ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
            $data=$data[0];
          if($data['push_type']==0){
              $data['lb_url']= $this->config->item('record_url').$data['video_name'].'.m3u8';
          }else{
              $data['lb_url']= $this->config->item('record_uc_url').$data['video_name'].'.m3u8';
          }
            $data['down'] = "http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
           // $data['cache_info']=$this->cache->cache_info();
            $data['live_url']=$this->config->item('rtmp_uc_url').$data['video_name'];
            $count=intval($data['views']);
            $data=json_encode($data);
             $m->set($video_id, $data, 604800);
             $m->set($video_id.'views', $count, 604800);
            if($this->input->get('test'))
            {
                echo '<pre>';print_r($data);exit();
            }
            //$this->cache->memcached->save($video_id, $data, 86400);
            //$this->cache->memcached->save($video_id.'views', $count, 86400,false);
            $time=time();
            $m->set($video_id.'time', $time, 604800);
           // $this->cache->memcached->save($video_id.'time', $time, 86400);
        }else{
          $m->increment($video_id.'views',1);
          $time=$m->get($video_id.'time');
          $timenow=time();
          $views=$m->get($video_id.'views');
            if(($timenow-$time)>1200){
            $this->User_model->update_one(array('video_id'=>$video_id),array('views'=>$views),'v_video');
            }
          $data=json_decode($data,true);
          $data['views']=$views;
          $data=json_encode($data);
        }

        echo $data;

    }


    public function un_test(){

        $str="http://api.etjourney.com/index.php/Api/show_start?appid=appkey&timestamp=1465891272&sign=3C974080D20DCED18FA7ED21DEA6847C&user_id=1077&title=a-/%EF%BC%9A%EF%BC%8C%E3%80%82%EF%BC%9F&video_name=1077_1465891272&adv_id=0&location=31.235458%2C121.373430&act_id=0";
        echo $str;
        echo "<hr>";
        echo urldecode($str);
        echo "<hr>";
        echo rawurldecode($str);
        echo "<hr>";
        echo chr(220);
//        echo ord('？');
        var_dump(ord('？'));
    }
    public function rl(){
        $rs=$this->User_model->get_select_all($select='user_id,pre_sign','groupid=4','user_id','DESC','v_users');
        foreach($rs as $k=>$v){
           if(preg_match('/\d+：/',$v['pre_sign'],$arr)){

               // print_r($arr);echo "<br>";
                //echo $v['pre_sign'];echo "<br>";

             $temp=preg_replace('/\d+：/','',$v['pre_sign']);
             //  $this->User_model->update_one(array('user_id'=>$v['user_id']),array('pre_sign'=>$temp),'v_users');
               //echo $v['pre_sign'];echo "<br>";
        }
     }
        echo "<hr>";
        foreach($rs as $k=>$v){

            if(preg_match('/\d+./',$v['pre_sign'],$arr)){

                $temp=preg_replace('/\d+./','',$v['pre_sign']);
                //$this->User_model->update_one(array('user_id'=>$v['user_id']),array('pre_sign'=>$temp),'v_users');
                //print_r($arr);echo "<br>";
               echo $v['pre_sign'];echo "<br>";
            }
        }
       echo "<pre>";
      print_r($rs);exit();
    }





    public function video_list_test(){
        set_time_limit(0);
        $data=$this->User_model->get_select_all($select='*',
            $where=" is_temp='0' AND act_status='2'  ",$order_title='start_time',$order='ASC',$table='v_auth_drivers');
        if($data!==false){
            foreach($data as $k=>$v){
                $this->get_crop_for_video($v['video_id']);
            }
        }
    }

    public function get_crop_for_video(){
        set_time_limit(0);
        $data=$this->User_model->get_select_all($select='video_id',
            $where=" imageforh5 ='' AND is_off=1  ",$order_title='video_id',$order='ASC',$table='v_video');
        if($data!==false){
            foreach($data as $k=>$v){
                $url="./uploads/".$v['video_id'].".jpg";
                $new_imag=$this->crop_for_video($url,$v['video_id']);
                $dataimage=array('imageforh5'=>$new_imag);
                $this->User_model->update_one(array('video_id'=>$v['video_id']),$dataimage,$table='v_video');
                echo $v['video_id'].'<br>';
            }
        }else{
            echo 'no';
        }

    }

    function crop_for_video($source_path='./uploads/5311.jpg',$key2='time',$target_width='100', $target_height='100')
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
        }
    }
//




    public function cap_test(){
        $this->load->library('captcha');
        $code = $this->captcha->getCaptcha();
        $this->session->set_userdata('code', $code);
        $this->captcha->showImg();
        echo $_SESSION['code'];
    }
    public function captcha_test(){
        $this->load->helper('captcha');
        $vals = array(
            'word'      => 'Random word',
            'img_path'  => './public/images/captcha/',
            'img_url'   => 'http://api.etjourney.com/public/images/captcha/',
            'font_path' => './path/to/fonts/texb.ttf',
            'img_width' => '150',
            'img_height'    => 30,
            'expiration'    => 7200,
            'word_length'   => 8,
            'font_size' => 16,
            'img_id'    => 'Imageid',
            'pool'      => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

            // White background and border, black text and red grid
            'colors'    => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );

        $cap = create_captcha($vals);
        echo $cap['image'];
    }
    public function dophpinfo(){
        phpinfo();
    }
    public function get_cal()
    {

        $prefs['template'] = '

    {table_open}<table border="0" cellpadding="0" cellspacing="0">{/table_open}

    {heading_row_start}<tr>{/heading_row_start}

    {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
    {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
    {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

    {heading_row_end}</tr>{/heading_row_end}

    {week_row_start}<tr>{/week_row_start}
    {week_day_cell}<td>{week_day}</td>{/week_day_cell}
    {week_row_end}</tr>{/week_row_end}

    {cal_row_start}<tr>{/cal_row_start}
    {cal_cell_start}<td>{/cal_cell_start}
    {cal_cell_start_today}<td>{/cal_cell_start_today}
    {cal_cell_start_other}<td class="other-month">{/cal_cell_start_other}

    {cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
    {cal_cell_content_today}<div class="highlight"><a href="{content}">{day}</a></div>{/cal_cell_content_today}

    {cal_cell_no_content}{day}{/cal_cell_no_content}
    {cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

    {cal_cell_blank}&nbsp;{/cal_cell_blank}

    {cal_cell_other}{day}{/cal_cel_other}

    {cal_cell_end}</td>{/cal_cell_end}
    {cal_cell_end_today}</td>{/cal_cell_end_today}
    {cal_cell_end_other}</td>{/cal_cell_end_other}
    {cal_row_end}</tr>{/cal_row_end}

    {table_close}</table>{/table_close}
';


        $data = array(
            3  => 'https://www.baidu.com',
            7  => 'https://www.baidu.com',
            13 => 'https://www.baidu.com',
            26 => 'javascript:void(0)'
        );



        $this->load->library('calendar', $prefs);


      //  $this->load->library('calendar',$prefs);
        echo $this->calendar->generate(2016, 8, $data);
        echo $this->calendar->generate(2016, 8, $data);
        echo $this->calendar->generate(2016, 8, $data);
    }


    public function get_cal_new(){
       require "./application/libraries/Calendar_new.class.php";

        //echo date('Y-n-j',time());
       // echo new Calendar_new(2015,1,2016,12);
    }


    public function get_pro()
    {
        $act_id=$this->input->get('act_id',TRUE);
        $row=$this->User_model->get_products_info(array('act_id'=>$act_id));

        $data=array();
        $date_time=array();

        foreach($row as $k=>$v)
        {

//            if($v['attr_type']==1 AND $v['attr_val']<time()){continue;}
            $data['goods']['goods_name']=$v['goods_name'];
            $data['goods']['goods_id']=$v['goods_id'];
            $data['goods']['goods_number']=$v['goods_number'];
            $data['goods']['shop_price']=$v['shop_price'];
            if($v['attr_type']==1)
            {

                $data['date']['attr_name']=$v['attr_name'];
                $data['date']['attr_type']=$v['attr_type'];

                $data['date']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
                $data['date']['attr_list'][$k]['attr_id']=$v['attr_id'];
                $data['date']['attr_list'][$k]['attr_val']=$v['attr_val'];
                $date_time[]=$v['attr_val'];
                $data['date']['attr_list'][$k]['attr_val']=date('Y-m-d',$v['attr_val']);
                $data['date']['attr_list'][$k]['attr_wek']=date('w',$v['attr_val']);//"0" (星期日) 至 "6" (星期六)
                $data['date']['attr_list'][$k]['attr_price']=$v['attr_price'];

                $data['date']['attr_val_arr'][$v['goods_attr_id']]=date('Y-n-j',$v['attr_val']);
                $data['date']['attr_price_arr'][$v['goods_attr_id']]=$v['attr_price'];

                $data['date']['start_year']=date('Y',  $date_time[0]);
                $data['date']['start_month']=date('n', $date_time[0]);
                $data['date']['end_year']=date('Y', end($date_time));
                $data['date']['end_month']=date('n', end($date_time));


                $time=$date_time[0];


            }
            elseif($v['attr_type']==2)
            {
                $data['attr'][$v['attr_id']]['attr_name']=$v['attr_name'];
                $data['attr'][$v['attr_id']]['attr_type']=$v['attr_type'];
                $data['attr'][$v['attr_id']]['attr_id']=$v['attr_id'];

                $data['attr'][$v['attr_id']]['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_id']=$v['attr_id'];
                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_val']=$v['attr_val'];
                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_price']=$v['attr_price'];

                $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
                $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
            }else
            {
                $data['check']['attr_name']=$v['attr_name'];
                $data['check']['attr_type']=$v['attr_type'];
                $data['check']['attr_id']=$v['attr_id'];

                $data['check']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
                $data['check']['attr_list'][$k]['attr_id']=$v['attr_id'];
                $data['check']['attr_list'][$k]['attr_val']=$v['attr_val'];
                $data['check']['attr_list'][$k]['attr_price']=$v['attr_price'];

                $data['check']['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
                $data['check']['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
                $data['check']['attr_list']=array_values( $data['check']['attr_list']);
            }

        }

        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );


        while(date('Y',$time)<date('Y',end($date_time)) OR date('n',$time)<date('n',end($date_time)))
        {
            $data['date']['cal'][] =array(
                'year'=>date('Y',strtotime('+1 month', $time)),
                'month'=>date('n',strtotime('+1 month', $time)),
                'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
                'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                'all_days'=>date('t',strtotime('+1 month', $time)),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
            );
            $time= strtotime('+1 month', $time);
        }
        if(isset($data['attr'])){
            $data['attr']=array_values($data['attr']);
        }



        echo '<pre>';
        print_r($data);exit();

    }



    public function shop_in()
    {
       set_time_limit(0);
        $data=$this->User_model->get_all($select='name as business_name,type,country as business_country,city as address,address as business_address,lat,lng,tag,tel as business_tel',$where='1=1',$table='v_catch',$order_title='c_id',
            $order='ASC',$left=0,$left_table='',$left_title="",$sum=false,$L=0, $start=0,$page_num=10);


        foreach($data as $k=>$v)
        {
            $data[$k]['user_id']=0;
            $data[$k]['currency_name']='泰铢';
            $data[$k]['currency']='THB';
            $data[$k]['is_show']='1';
            $data[$k]['add_time']=time();
            $data[$k]['business_info']=$v['tag'];
            $data[$k]['star_num']=5;
            $data[$k]['discount']=10;
            ///$this->User_model->user_insert($table='v_wx_business',$data[$k]);
        }
        echo '<pre>';
        print_r($data);
    }




    public function get_location_con_and_city()
    {
        $title=$this->input->get_post('title',true);
        $newarr[0]['name']='热门';
        $newarr[0]['list']=array();


        $newarr[1]['name']='港澳台';
        $newarr[1]['list']=array();

        $newarr[2]['name']='亚洲';
        $newarr[2]['list']=array();

        $newarr[3]['name']='欧洲';
        $newarr[3]['list']=array();

        $newarr[4]['name']='非洲';
        $newarr[4]['list']=array();

        $newarr[5]['name']='北美';
        $newarr[5]['list']=array();

        $newarr[6]['name']='南美';
        $newarr[6]['list']=array();

        $newarr[7]['name']='大洋洲';
        $newarr[7]['list']=array();

        $newarr[8]['name']='南极洲';
        $newarr[8]['list']=array();

        $where1="is_down= '1'";
        $where2="is_show= '1'";
        if($title)
        {
            $where1.="  AND name LIKE '%$title%'";
        }
        else
        {
            $newarr[0]['list']=$this->User_model->get_select_all($select='content as name',$where2,$order_title='word_id',$order='ASC',$table='v_word');
            if($newarr[0]['list']===false)
            {
                $newarr[0]['list']=array();
            }

        }
        $arr=$this->User_model->get_select_all($select='name,level,pid,image,name_en',$where1,$order_title='id',$order='ASC',$table='v_location');
        if($arr!==false)
        {
            foreach($arr as $k=>$v)
            {
                if($v['name']=='香港'OR $v['name']=='澳门' OR $v['name']=='台湾')
                {
                    $newarr[1]['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                    );
                    continue;
                }
                if($v['level']=='2')
                {
                    if($v['pid']=='1')
                    {
                        $newarr['2']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='2')
                    {
                        $newarr['3']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='3')
                    {
                        $newarr['4']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='4')
                    {
                        $newarr['5']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='5')
                    {
                        $newarr['6']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='6')
                    {
                        $newarr['7']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($v['pid']=='7')
                    {
                        $newarr['8']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                }
                else
                {
                    $zid=$this->User_model->get_select_one('pid',array('id'=>$v['pid']),'v_location');
                    //$zid=$zid['pid'];
                    if($zid['pid']=='1')
                    {
                        $newarr['2']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='2')
                    {
                        $newarr['3']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='3')
                    {
                        $newarr['3']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='4')
                    {
                        $newarr['4']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='5')
                    {
                        $newarr['5']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='6')
                    {
                        $newarr['6']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                    elseif($zid['pid']=='7')
                    {
                        $newarr['7']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                        );
                    }
                }

            }

        }
        $this->data_back($newarr);
    }

    public function get_location_all()
    {
        $str='';
        $rs=$this->User_model->get_select_all('id,name','1=1','id', 'ASC','v_location');
        foreach($rs as $k=>$v)
        {
            $str.="<br>".$v['name'];
        }
        echo $str;
        //echo '<pre>';
       // print_r($rs);
    }

    function google_get()
    {
       // $url = "https://maps.google.cn/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=AIzaSyCZVELBoJMNpsqMnguop_EZJIWiFXErFLY";
       // $url = "http://ditu.google.com/maps/api/geocode/json?latlng=31.22,121.47&sensor=false&language=zh-CN";
       // $url = "http://ditu.google.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=false&&language=zh-CN";
        //$result = file_get_contents($url);

        //echo $result;

        $data['list']=$this->User_model->get_select_more($select='id,name','lng =-1',$start=0,$page_num=10000,'id',$order='ASC',$table='v_location');
        echo '<pre>';
        $data['count']=count($data['list']);
        print_r($data);exit();

        foreach( $data['list'] as $k=>$v)
        {
            $data['list'][$k]['name']=trim($v['name']);
        }
        $data['json']=json_encode($data['list']);
        $this->load->view('video/location',$data);
    }


    public function  put_into_db()
    {
        $id=$this->input->post('id',true);
        $lat=$this->input->post('lat',true);
        $lng=$this->input->post('lng',true);
      // echo $id.'<br>',$lat,$lng;
        $this->User_model->update_one(array('id'=>$id),array('lat'=>$lat,'lng'=>$lng),$table='v_location');
        echo 1;
    }


    public function city_all()
    {
    $str='아시아
요 크';
        $arr=explode("\n",$str);
        $arr_id=$this->User_model->get_select_all('id','1=1','id', 'ASC','v_location');
        foreach($arr as $k=>$v)
        {
            echo $k,':',$v;
            $where=array('id'=>$arr_id[$k]['id']);
           $this->User_model->update_one($where,array('name_hy'=>$v),'v_location');
        }
       // echo "<pre>";print_r($arr_id);
    }


    public function pre_in()
    {
        $str='';
        $arr=explode(';',$str);
        foreach($arr as $k=>$v)
        {
            $arr[$k]=trim($v);
        }
         $arr2=array();
        foreach($arr as $k=>$v)
        {
            $v=trim($v);
            if($v!=''){
                $arr2[]=$v;
            }
        }
        $arr=array_values($arr2);
        echo "<pre>";print_r($arr);//exit();
        $where='1=1';
        $arr_id=$this->User_model->get_select_all('id',$where,'id', 'ASC','v_location');
        echo "<pre>";print_r($arr_id);

       foreach($arr as $k=>$v)
       {
            $where=array('id'=>$arr_id[$k]['id']);
            $this->User_model->update_one($where,array('name_ft'=>$v),'v_location');
        }
    }

    public function image_head()
    {
        $str="http://wx.qlogo.cn/mmopen/OiabibA1ZJjcOABKsAR3LB84yn0CO3YsosqSNdGVIvXJ2yCYjQMuJdSWZhce0evnBmNSZicFG6eB6rxjQBB43aQTwHnd7NzoWicb/64";
        $num=strripos($str,'/');
        //$numall=strlen($str);
        $image=substr($str,0,$num);
        echo $image.'/96';
    }
    public function save_eg()
    {
        $num=0.0215489;
        echo sprintf("%.3f", $num); // 0.022
        $num=0.0215489;
        echo substr(sprintf("%.4f", $num),0,-1); // 0.021
    }

    public function get_location_list($id='0')
    {
        $rs=$this->User_model->get_all('id,pid,level,name',"pid=$id",'v_location');
        $arr=array();
        if(count($rs)>0)
        {
          foreach($rs as $k=>$v)
          {
              $rs[$k]['list']=$this->get_location_list($v['id']);
              $arr[]=$rs[$k];
          }
        }
        return $arr;
    }

    function generateTree(){
        $items=  array('0'=>'1');
        $tree = array();
        $rs=$this->User_model->get_all('id,pid,level,name',"1=1",'v_location');
        foreach($rs as $k=>$v){
            $items[]=$v;
        }
        unset($items[0]);
        foreach($items as $item){
            if(isset($items[$item['pid']])){
                $items[$item['pid']]['son'][] = &$items[$item['id']];
            }else{
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }


    public function see_location()
    {
        echo '<pre>';
        print_r($this->generateTree());
        //print_r($this->get_location_list());
    }

    public function see_money()
    {
        $this->common->get_true_money();
    }

    public function fans_list_new()
    {
        $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        $page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
        $fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;

        $fans_user_arr=array();
        $fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
        foreach($fans_user_arr_2 as $v){
            $fans_user_arr[]=$v['fans_id'];
        }
        if(!$user_id)
        {
            $this->data_back("参数为空", '0x011','fail');
        }
        $page_num =10;
        if($fans_id)
        {
            $where = " fans_id=$fans_id ";
            $count = $this->User_model->get_count($where,'v_follow');
        }
        else
        {
            $where = " fans_id=$user_id ";
            $count = $this->User_model->get_count($where,'v_follow');
            $temp_rs=$this->User_model->get_select_one('lastday_fans,lasttime_fans',array('user_id'=>$user_id),'v_users');
            $data['newfans_count']=strval($count['count']-$temp_rs['lastday_fans']);
            $datafans=array(
                'lastday_fans'=>$count['count'],
                'lasttime_fans'=>time()
            );
            $this->User_model->update_one(array('user_id'=>$user_id),$datafans,$table='v_users');
            if($data['newfans_count']>0)
            {
                $data['newfans_list'] =$this->User_model->get_select_all('v_users.user_id,v_users.image,v_users.user_name,v_users.sex,v_users.auth,v_users.credits,v_users.pre_sign',
                    $where,'dateline','DESC','v_follow',$left=1,'v_users',"v_users.user_id=v_follow.user_id",$sum=false, $L=1, 0,$data['newfans_count']);
            }
            else
            {
                $data['newfans_list']=array();
                $data['newfans_count']='0';
            }

            $where.="  AND dateline <= $temp_rs[lasttime_fans]";
        }

        if(empty($count['count']))
        {
            $this->data_back("没有关注者", '0x017','fail');
        }
        $start = ($page-1)*$page_num;

        $data['list'] = $this->User_model->get_select_all('v_users.user_id,v_users.image,v_users.user_name,v_users.sex,v_users.auth,v_users.credits,v_users.pre_sign',
            $where,'dateline','ASC','v_follow',$left=1,'v_users',"v_users.user_id=v_follow.user_id",$sum=false, $L=1, $start,$page_num);

        if($data['list']!==false)
        {
            foreach ($data['list'] as $key => $value) {

                $data['list'][$key]['avatar']= $data['list'][$key]['image'];
                if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id']))
                {
                    $is_follow='1';
                }
                else
                {
                    $is_follow='0';
                }
                $data['list'][$key]['follow'] = $is_follow;
                $data['list'][$key]['level'] = $this->common->get_level($value['credits']);
                unset($data['list'][$key]['image']);
            }
        }
        else
        {
            $data['list']=array();
        }
        if(isset($data['newfans_list']) && $data['newfans_list']!==false)
        {
            foreach ($data['newfans_list'] as $key => $value) {
                $data['newfans_list'][$key]['avatar']= $data['newfans_list'][$key]['image'];
                if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id']))
                {
                    $is_follow='1';
                }
                else
                {
                    $is_follow='0';
                }
                $data['newfans_list'][$key]['follow'] = $is_follow;
                $data['newfans_list'][$key]['level'] = strval($this->common->get_level($value['credits']));
                unset($data['newfans_list'][$key]['image']);
            }
        }
        $this->data_back($data, '0x000');
    }

    public function get_month_cn($month)
    {
        switch ($month)
        {
            case 1;
                return '一';
            case 2;
                return '二';
            case 3;
                return '三';
            case 4;
                return '四';
            case 5;
                return '五';
            case 6;
                return '六';
            case 7;
                return '七';
            case 8;
                return '八';
            case 9;
                return '九';
            case 10;
                return '十';
            case 11;
                return '十一';
            case 12;
                return '十二';
        }
    }

    public function del_cf()
    {
        $rs=$this->User_model->cf_model();
        $rs_str=array();
        foreach($rs as $k=>$v)
        {
            if(stristr(Chr(34),$v['business_name'])===FALSE){
                $rs_str[]="'".$v['business_name']."'";
            }

        }
        $rs_str2=implode(',',$rs_str);
        $where="business_name IN ($rs_str2)";
        $rs_str2=$this->User_model->get_all($select='*',$where,$table='v_wx_business',$order_title='business_name', $order='ASC',$left=0,$left_table='',$left_title="",$sum=false,$L=1, $start=0,$page_num=3000,$order2='2');
        $del_arr=$rs_str3=array();
        foreach($rs_str2 as $k=>$v){
            $rs_str3[$v['business_name']][]=$v;
        }
        foreach($rs_str3 as $k=>$v){
            $del_arr[]=$v[0];
        }
        foreach($del_arr as $k=>$v){
            //$this->User_model->del(array('business_id'=>$v['business_id']),'v_wx_business');
            //$this->User_model->update_one(array('business_id'=>$v['business_id']),array('is_show'=>'3'),$table='v_wx_business');

        }
        echo '<pre>';
        print_r($rs_str3);
    }

    public function get_down()
    {
        //echo '<pre>';
        $str=file_get_contents("http://api.etjourney.com/tmp/down_app.php");
	
       $str= htmlspecialchars($str);
        $arr3=$arr2=$arr=array();
        $pat='|data-title=&quot;(.*)target|U';
        preg_match_all($pat, $str, $arr);
        //array_pop($arr[1]);

        $pat='|down-line(.*)人下载|U';

        preg_match_all($pat, $str, $arr2);
        $pat='|icon-margin(.*)img|U';
        preg_match_all($pat, $str, $arr3);
      //  print_r($arr2[1]);
       // $arr2=  explode('>',$arr2[1]);
        //print_r($arr[1]);
       //print_r($arr2[1]);
        //print_r($arr3[1]);


        $new_arr=array();
        foreach($arr[1] as $k =>$v){

            $new_arr[$k]['name']=str_replace('&quot;','',$v);
            $arr2[1][$k]=str_replace('&quot;&gt;','',$arr2[1][$k]);
            $arr2[1][$k]=str_replace('&quot;','',$arr2[1][$k]);
            $new_arr[$k]['down']=$arr2[1][$k].'下载';

            $pat='|../(.*)&gt;|U';
            $temp=array();
            preg_match_all($pat, $arr3[1][$k],$temp);
            $temp=str_replace('&quot;','',$temp[1][0]);
            $temp2='http://android.myapp.com/'.$temp;
           // $temp2=$temp[1];
            $new_arr[$k]['down_url']=htmlspecialchars_decode($temp2);

        }
        $new_arr2=array();
        foreach($new_arr as $k=>$v)
        {
            $new_arr2[$v['name']]=$v;
        }

        //echo json_encode($new_arr);
  // echo '<pre>'; print_r($new_arr2);
        $new_arr2=array_values($new_arr2);
//    echo $srt;
        $this->data_back($new_arr2);
    }

    public function get_location_con_and_city1()
    {
        $title=$this->input->get_post('title',true);
        $newarr[0]['name']='热门';
        $newarr[0]['list']=array();


        $newarr[1]['name']='港澳台';
        $newarr[1]['list']=array();

        $newarr[2]['name']='亚洲';
        $newarr[2]['list']=array();

        $newarr[3]['name']='欧洲';
        $newarr[3]['list']=array();

        $newarr[4]['name']='非洲';
        $newarr[4]['list']=array();

        $newarr[5]['name']='北美';
        $newarr[5]['list']=array();

        $newarr[6]['name']='南美';
        $newarr[6]['list']=array();

        $newarr[7]['name']='大洋洲';
        $newarr[7]['list']=array();

        $newarr[8]['name']='南极洲';
        $newarr[8]['list']=array();

        $where1="is_down= '1'";

        if($title)
        {
            $where1.="  AND name LIKE '%$title%'";
        }
        else
        {
            $where2="  is_hot='1'";
            $newarr[0]['list']=$this->User_model->get_select_all($select='name,lat,lng,lat AS latitude,lng AS longitude ',$where2,$order_title='id',$order='ASC',$table='v_location');
            if($newarr[0]['list']===false)
            {
                $newarr[0]['list']=array();
            }


        }
        $arr=$this->User_model->get_select_all($select='name,level,pid,image,name_en,lat,lng',$where1,$order_title='id',$order='ASC',$table='v_location');
        if($arr!==false)
        {
            foreach($arr as $k=>$v)
            {
                if($v['name']=='香港'OR $v['name']=='澳门' OR $v['name']=='台湾')
                {
                    $newarr[1]['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                    continue;
                }
                if($v['level']=='2')
                {
                    if($v['pid']=='1')
                    {
                        $newarr['2']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='2')
                    {
                        $newarr['3']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='3')
                    {
                        $newarr['4']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='4')
                    {
                        $newarr['5']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='5')
                    {
                        $newarr['6']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='6')
                    {
                        $newarr['7']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($v['pid']=='7')
                    {
                        $newarr['8']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                }
                else if($v['level']=='2')
                {
                    $zid=$this->User_model->get_select_one('pid',array('id'=>$v['pid']),'v_location');
                    //$zid=$zid['pid'];
                    if($zid['pid']=='1')
                    {
                        $newarr['2']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='2')
                    {
                        $newarr['3']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='3')
                    {
                        $newarr['4']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='4')
                    {
                        $newarr['5']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='5')
                    {
                        $newarr['6']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='6')
                    {
                        $newarr['7']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                    elseif($zid['pid']=='7')
                    {
                        $newarr['8']['list'][]=array(
                            'name'=>$v['name'],
                            'name_en'=>$v['name_en'],
                            'image'=>$v['image'],
                            'lat'=>$v['lat'],
                            'lng'=>$v['lng'],
                            'latitude'=>$v['lat'],
                            'longitude'=>$v['lng'],
                        );
                    }
                }else{

                $zid=$this->User_model->get_select_one('pid',array('id'=>$v['pid']),'v_location');
                $zid=$this->User_model->get_select_one('pid',array('id'=>$zid['pid']),'v_location');
                //$zid=$zid['pid'];
                if($zid['pid']=='1')
                {
                    $newarr['2']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='2')
                {
                    $newarr['3']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='3')
                {
                    $newarr['4']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='4')
                {
                    $newarr['5']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='5')
                {
                    $newarr['6']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='6')
                {
                    $newarr['7']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
                elseif($zid['pid']=='7')
                {
                    $newarr['8']['list'][]=array(
                        'name'=>$v['name'],
                        'name_en'=>$v['name_en'],
                        'image'=>$v['image'],
                        'lat'=>$v['lat'],
                        'lng'=>$v['lng'],
                        'latitude'=>$v['lat'],
                        'longitude'=>$v['lng'],
                    );
                }
            }

            }

        }
        //if($title)
        //{
        foreach($newarr as $k=>$v)
        {
            if(count($v['list'])==0){
                unset($newarr[$k]);
            }
        }
        $newarr=array_values($newarr);
        if(count($newarr)==0)
        {
            $this->data_back('参数为空','0X011','fail');
        }

        //}

        $this->data_back($newarr);
    }


    public function set_order_sell()
    {
        $arr=$this->User_model->get_select_all($select='*',$where='1=1',$order_title='act_id',$order='ASC',$table='v_activity_children');
        foreach($arr as $k=>$v)
        {
            $this->User_model->update_one(array('act_id'=>$v['act_id']),array('order_sell'=>rand(12,120)),$table='v_activity_children');
        }
    }

    public function add_user()
    {
        $data=array('account'=>'testl','user_name'=>'testl','password'=>md5(123456));
        $this->User_model->user_insert('v_users',$data);
    }
}