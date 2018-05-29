<?php
/**
 * 用户
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Auther extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
        $this->load->helper('url');
        $this->count_url = 'http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->load->library('session');
        $this->load->helper('cookie');
        $this->load->library('image_lib');

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

    public function get_auth_arr($user_id)
    {
        $rs=$this->User_model->get_select_one('is_guide,is_driver,is_attendant,is_merchant',array('user_id'=>$user_id),'v_users');
        if(is_array($rs))
        {
            return $rs;
        }else{
            return array();
        }
    }
    /*
  * 司机信息提交
  */
    public function driver_info_sub(){
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $_SESSION['type']='driver';
        $user_name=trim($this->input->post('user_name',true));
        $auth_name=trim($this->input->post('user',true));
        $mobile=trim($this->input->post('contact',true));
        $auth_wechat=trim($this->input->post('weixin',true));
        $id_style=trim($this->input->post('card',true));
        $id_num=trim($this->input->post('card_num',true));
        $id_driver=trim($this->input->post('drive_num',true));
        $id_car_num=trim($this->input->post('travel_num',true));
        $id_car_style=trim($this->input->post('car_style',true));
        $lan=$this->get_lan_user();
        $id_driver_status='0';
        $data=array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'auth_name'=>$auth_name,
            'mobile'=>$mobile,
            'auth_wechat'=>$auth_wechat,
            'id_style'=>$id_style,
            'id_num'=>$id_num,
            'id_driver'=>$id_driver,
            'id_car_num'=>$id_car_num,
            'id_car_style'=>$id_car_style,
            'id_driver_status'=>$id_driver_status,
            'id_auth_time'=>time(),
            'is_temp'=>'1',
            'lan'=>$lan
        );
        if(isset($_FILES['file1'])){
            if($_FILES['file1']['error']==0){
                $id_image=$this->upload_image('file1',$user_id,'id_driver');
                $data['id_image']=$id_image;
                $id_image_thumb=$this->thumb($id_image,$user_id,'id_driver');
                $data['id_image_thumb']=$id_image_thumb;
            }
        }
        if(isset($_FILES['file2'])){
            if($_FILES['file2']['error']==0){
                $id_driver_image=$this->upload_image('file2',$user_id,'id_num_driver');
                $data['id_driver_image']=$id_driver_image;
                $id_driver_image_thumb=$this->thumb($id_driver_image,$user_id,'id_num_driver');
                $data['id_driver_image_thumb']=$id_driver_image_thumb;
            }
        }
        if(isset($_FILES['file3'])){
            if($_FILES['file3']['error']==0){
                $id_car_num_image=$this->upload_image('file3',$user_id,'id_car_driver');
                $data['id_car_num_image']=$id_car_num_image;
                $id_car_num_image_thumb=$this->thumb($id_car_num_image,$user_id,'id_car_driver');
                $data['id_car_num_image_thumb']=$id_car_num_image_thumb;
            }
        }
        if(isset($_FILES['file4'])){
            if($_FILES['file4']['error']==0){
                $id_car_image=$this->upload_image('file4',$user_id,'id_car_image');
                $data['id_car_image']=$id_car_image;
                $id_car_image_thumb=$this->thumb($id_car_image,$user_id,'id_car_image');
                $data['id_car_image_thumb']=$id_car_image_thumb;
            }
        }

        $where=array('user_id'=>$user_id);
        $row=$this->User_model->get_count($where, 'v_auth_drivers');
        //  echo "<pre>";print_r($row);exit();
        if($row['count']==0){
            if($this->User_model->user_insert('v_auth_drivers',$data)){
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("auther/country_list"));

                }

            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                   return false;
                }

            }
        }else{
            if($this->User_model->update_one($where,$data,'v_auth_drivers'))
            {
                $rs=$this->get_auth_arr($user_id);
                if(isset($rs['is_guide']) AND ($rs['is_guide']=='1' OR $rs['is_attendant']=='1'))
                {
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_driver'=>'0'),'v_users');
                }else{
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_driver'=>'0','auth'=>'0'),'v_users');
                }


                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("auther/country_list"));
                }

            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }

            }
        }
    }
    /*
     * 商户信息提交
     */
    public function business_info_sub(){
        //set_time_limit(0);
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $_SESSION['type']='business';
        $user_name=trim($this->input->post('user_name',true));
        $shop_name=trim($this->input->post('shop_name',true));
        $auth_name=trim($this->input->post('user',true));
        $mobile=trim($this->input->post('contact',true));
        $auth_wechat=trim($this->input->post('weixin',true));
        $id_style=trim($this->input->post('card',true));
        $id_num=trim($this->input->post('card_num',true));
        $id_business_status='1';
        $lan=$this->get_lan_user();
        $data=array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'shop_name'=>$shop_name,
            'auth_name'=>$auth_name,
            'mobile'=>$mobile,
            'auth_wechat'=>$auth_wechat,
            'id_style'=>$id_style,
            'id_num'=>$id_num,
            'id_business_status'=>$id_business_status,
            'id_auth_time'=>time(),
            'is_temp'=>'1',
            'lan'=>$lan
        );
        if($_FILES['file1']['error']==0){
            $id_image=$this->upload_image('file1',$user_id,'id_image_bussiness');
            $data['id_image']=$id_image;
            $id_image_thumb=$this->thumb($id_image,$user_id,'id_image_bussiness');
            $data['id_image_thumb']=$id_image_thumb;
        }
        if($_FILES['file2']['error']==0){
            $id_business_image=$this->upload_image('file2',$user_id,'bussiness_image_bussiness');
            $data['id_business_image']=$id_business_image;
            $id_business_image_thumb=$this->thumb($id_business_image,$user_id,'bussiness_image_bussiness');
            $data['id_business_image_thumb']=$id_business_image_thumb;
        }
        $where=array('user_id'=>$user_id,'is_temp'=>'1');
        $row=$this->User_model->get_count($where,'v_auth_business');
        //echo $row['count'];
        if($row['count']==0){
            if($this->User_model->user_insert('v_auth_business',$data)){
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("user/business_auth"));
                }

            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }

            }
        }else{
            if($this->User_model->update_one($where,$data,'v_auth_business')){

                $this->User_model->update_one(array('user_id'=>$user_id),array('is_merchant'=>'0','is_trip_merchant'=>'0','auth'=>'0'),'v_users');


                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("user/business_auth"));
                 }
            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }
            }
        }
    }

    /*
     * 导游信息提交
     */
    public function guide_info_sub(){
        $_SESSION['type']='guide';
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $user_name=trim($this->input->post('user_name',true));
        $auth_name=trim($this->input->post('user',true));
        $mobile=trim($this->input->post('contact',true));
        $auth_wechat=trim($this->input->post('weixin',true));
        $id_style=trim($this->input->post('card',true));
        $id_num=trim($this->input->post('card_num',true));
        $id_view_style=trim($this->input->post('work',true));
        $id_view_num=trim($this->input->post('work_num',true));
        $id_view_status='0';
        $lan=$this->get_lan_user();
        $data=array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'auth_name'=>$auth_name,
            'mobile'=>$mobile,
            'auth_wechat'=>$auth_wechat,
            'id_style'=>$id_style,
            'id_num'=>$id_num,
            'id_view_style'=>$id_view_style,
            'id_view_num'=>$id_view_num,
            'id_view_status'=>$id_view_status,
            'id_auth_time'=>time(),
            'lan'=>$lan,
            'is_temp'=>'1'
        );

        if($_FILES['file2']['error']==0){
            $id_view_image=$this->upload_image('file2',$user_id,'id_guide_num');
            $data['id_view_image']=$id_view_image;
            $id_view_image_thumb=$this->thumb($id_view_image,$user_id,'id_guide_num');
            $data['id_view_image_thumb']=$id_view_image_thumb;
        }
        if($_FILES['file1']['error']==0){
            $id_image=$this->upload_image('file1',$user_id,'id_guide');
            $id_image_thumb=$this->thumb($id_image,$user_id,'id_guide');
            $data['id_image_thumb']=$id_image_thumb;
            $data['id_image']=$id_image;
        }

        $where=array('user_id'=>$user_id,'is_temp'=>'1');
        $row=$this->User_model->get_count($where,'v_auth_views');
        //  echo "<pre>";print_r($data);exit();
        if($row['count']==0){
            if($this->User_model->user_insert('v_auth_views',$data)){
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("auther/continent_list"));
                }
            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }

            }
        }else{
            if($this->User_model->update_one($where,$data,'v_auth_views')){
                $rs=$this->get_auth_arr($user_id);
                if(isset($rs['is_driver']) AND $rs['is_driver']=='1' )
                {
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_guide'=>'0'),'v_users');
                }else{
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_guide'=>'0','auth'=>'0'),'v_users');
                }


                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("auther/continent_list"));
                }
            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }

            }
        }
    }
    /*
     *当地人信息提交
     */
    public function locals_info_sub(){
        $_SESSION['type']='local';
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        //$user_id=$this->input->post('user_id',true);
        $user_name=trim($this->input->post('user_name',true));
        $auth_name=trim($this->input->post('user',true));
        $mobile=trim($this->input->post('contact',true));
        $auth_wechat=trim($this->input->post('weixin',true));
        $id_style=trim($this->input->post('card',true));
        $id_num=trim($this->input->post('card_num',true));
        $id_local_status='0';
        $lan=$this->get_lan_user();
        $data=array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'auth_name'=>$auth_name,
            'mobile'=>$mobile,
            'auth_wechat'=>$auth_wechat,
            'id_style'=>$id_style,
            'id_num'=>$id_num,
            'id_local_status'=>$id_local_status,
            'id_auth_time'=>time(),
            'is_temp'=>'1',
            'lan'=>$lan
        );
        if($_FILES['file1']['error']==0){
            $id_image=$this->upload_image('file1',$user_id,'id_locals');
            $data['id_image']=$id_image;
            $id_image_thumb=$this->thumb($id_image,$user_id,'id_locals');
            $data['id_image_thumb']=$id_image_thumb;
        }
        $where=array('user_id'=>$user_id,'is_temp'=>'1');
        $row=$this->User_model->get_count($where, 'v_auth_locals');
        if($row['count']==0){
          if( $this->User_model->user_insert('v_auth_locals',$data)){
              if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
              {
                  echo 1;
              }else{
                  redirect(base_url("auther/country_list"));
              }
          }else{
              if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
              {
                  echo 0;
              }else{
                  return false;
              }

          }
        }else{
            if($this->User_model->update_one($where,$data,'v_auth_locals')){
                $rs=$this->get_auth_arr($user_id);
                if(isset($rs['is_driver']) AND $rs['is_driver']=='1' )
                {
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_attendant'=>'0'),'v_users');
                }else{
                    $this->User_model->update_one(array('user_id'=>$user_id),array('is_attendant'=>'0','auth'=>'0'),'v_users');
                }
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 1;
                }else{
                    redirect(base_url("auther/country_list"));
                }
            }else{
                if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
                {
                    echo 0;
                }else{
                    return false;
                }

            }
        }
    }



/*
 * 洲提交
 */
    public function continent_list(){
        $data['count_url']=$this->count_url;

        if(!$this->input->get('test')){
            $user_id=$this->user_id_and_open_id();
            if(!$user_id){
                return false;
            }
            $data['user_id']=$user_id;
        }else{
            $data['user_id']=$user_id=0;
        }
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="(name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%'  or name_jp LIKE '%$keyword%' or name_hy LIKE '%$keyword%')AND level=1 " ;
            $data['sou']=1;
        }else{
            $where=array('level'=>1);
        }

        $type=$this->get_lan_user();
        if($user_id==0){
            $type='en';
        }
        if($type=='zh-cn' OR $type=='zh-CN'){
            $select='id,name,name_pinyin,name_en';
            $order='name_pinyin';
            $this->lang->load('jt', 'english');
        }elseif($type=='zh-TW' OR $type=='zh-tw'){
            $select='id,name_pinyin,name_ft as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ja-jp' OR $type=='ja-JP'){
            $select='id,name_pinyin,name_jp as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ko-kr' OR $type=='ko-KR'){
            $select='id,name_pinyin,name_hy as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }else{
            $data['type']='en';
            $select='id,name_pinyin,name_en as name';
            $order='name_en';
            $this->lang->load('eng', 'english');
        }
        // $select='id,name,name_pinyin,name_en';
        $data['list']=$this->User_model->get_city($select,$where,$order);

        $this->load->view('auther/list_can',$data);
    }
    /*
     * 国家列表
     */
    public function country_list(){
        $data['count_url']=$this->count_url;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            if(!($this->input->get('test')))
            {
                return false;
            }
            
        }
        $data['user_id']=$user_id;
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="(name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%' or name_jp LIKE '%$keyword%' or name_hy LIKE '%$keyword%' )AND level=2 " ;
            $data['sou']=1;
        }else{
            $where=array('level'=>2);
        }

        $type=$this->get_lan_user();
        if($user_id==0){
            $type='en';
        }
        if($type=='zh-cn' OR $type=='zh-CN'){
            $select='id,name,name_pinyin,name_en';
            $order='name_pinyin';
            $this->lang->load('jt', 'english');
        }elseif($type=='zh-TW' OR $type=='zh-tw'){
            $select='id,name_pinyin,name_ft as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ja-jp' OR $type=='ja-JP'){
            $select='id,name_pinyin,name_jp as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ko-kr' OR $type=='ko-KR'){
            $select='id,name_pinyin,name_hy as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }else{
            $data['type']='en';
            $select='id,name_pinyin,name_en as name';
            $order='name_en';
            $this->lang->load('eng', 'english');
        }
        $data['list']=$this->User_model->get_city($select,$where,$order);
        $this->load->view('auther/list_country',$data);
    }
    /*
     * 城市列表
     */
    public function city_list(){
        $data['count_url']=$this->count_url;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id=$_SESSION['user_id'];
        $pid=$this->input->get('pid',true);
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="(name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%' or name_jp LIKE '%$keyword%' or name_hy LIKE '%$keyword%' ) AND level=3 " ;
            $data['sou']=1;
        }else{
            $where=array('pid'=>$pid);
        }
        $type=$this->get_lan_user();
        if($user_id==0){
            $type='en';
        }
        if($type=='zh-cn' OR $type=='zh-CN'){
            $select='id,name,name_pinyin,name_en';
            $order='name_pinyin';
            $this->lang->load('jt', 'english');
        }elseif($type=='zh-TW' OR $type=='zh-tw'){
            $select='id,name_pinyin,name_ft as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ja-jp' OR $type=='ja-JP'){
            $select='id,name_pinyin,name_jp as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ko-kr' OR $type=='ko-KR'){
            $select='id,name_pinyin,name_hy as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }else{
            $data['type']='en';
            $select='id,name_pinyin,name_en as name';
            $order='name_en';
            $this->lang->load('eng', 'english');
        }
        $data['list']=$this->User_model->get_city($select,$where,$order);
        if(empty($data['list'])){
            $data['list']=$this->User_model->get_city($select,array('id'=>$pid ),$order);
        }
        $this->load->view('auther/list_city',$data);
    }
/*
 * 城市提交
 */
    public function city_sub(){
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $id=$this->input->get('id',true);
        $row=$this->User_model->get_select_one('name,pid',array('id'=>$id),'v_location');
        $pid=$row['pid'];
        $id_range=$row['name'];
        $row2=$this->User_model->get_select_one('name',array('id'=>$pid),'v_location');
        $id_prange=$row2['name'];
        if($_SESSION['type']=='driver'){
            $table='v_auth_drivers';
            $data=array(
                'id_range'=>$id_range,
                'id_driver_status'=>'1',
                'id_prange'=>$id_prange
            );
            $where=array('user_id'=>$user_id, 'is_temp'=>'1');
            $this->User_model->update_one($where,$data,$table);
            redirect(base_url("user/driver_auth"));
        }elseif($_SESSION['type']=='local'){
            $table='v_auth_locals';
            $data=array(
                'id_range'=>$id_range,
                'id_local_status'=>'1',
                 'id_prange'=>$id_prange
            );
            $where=array('user_id'=>$user_id, 'is_temp'=>'1');
            $this->User_model->update_one($where,$data,$table);
            redirect(base_url("user/locals_auth"));
        }elseif($_SESSION['type']=='business'){
            $table='v_auth_business';
            $data=array(
                'id_range'=>$id_range,
                'id_business_status'=>'1',
                'id_prange'=>$id_prange
            );
            $where=array('user_id'=>$user_id, 'is_temp'=>'1');
            $this->User_model->update_one($where,$data,$table);
            redirect(base_url("user/business_auth"));
        }

    }
//continent 大陆提交
    public function continent_sub(){

        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        //$user_id=$this->input->get('user_id',true);
        $con_id_arr= $this->input->get('continent',true);
        $con_id_arr=explode(',',$con_id_arr);
        $len=count($con_id_arr);
        if($con_id_arr[$len-1]==''){
            array_pop($con_id_arr);
        }
        //echo "<pre>";print_r($con_id_arr);exit();
        $con_name_arr=array();
        foreach($con_id_arr as $k=>$v){
            $row=$this->User_model->get_select_one('name',array('id'=>$v),'v_location');
            $con_name_arr[]=$row['name'];
        }
        $where=array('user_id'=>$user_id,
            'is_temp'=>'1');
        $str=implode(',',$con_name_arr);
        $data=array(
            'id_range'=>$str,
            'id_view_status'=>'1',
        );
        // echo "<pre>";print_r($data);exit();
        $this->User_model->update_one($where,$data,'v_auth_views');
        redirect(base_url("user/guide_auth"));
    }

    public function get_all_city(){

        set_time_limit(0);
        $rs=$this->User_model->get_select_all($select='id,name',
            $where='1=1',$order_title='id',
            $order='ASC',$table='v_location');
       foreach($rs as $k=>$v){
           if($k%400==0){
               echo "<hr>";
           }
            echo $v['name'],"<br>";
       }
    }
   


    public function upload_image($filename,$fileurl,$key='time')
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl))
            {
                return false;
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
                $br = false;break;
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

    /*
 * 缩略图生成类
 */
    public function thumb($url,$key1,$key2='time'){
        if (!file_exists('./public/images/thumb/'.$key1))
        {
            if (!mkdir('./public/images/thumb/'. $key1,0777))
            {
                return false;
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
        $arr['width']     = 520;
        $arr['height']   = 300;

        $this->image_lib->initialize($arr);

        if($this->image_lib->resize()){
            return  $arr['new_image'];
        }
    }
    /*
 *获取user_id
 */
    public function user_id_and_open_id(){
        if(isset($_COOKIE['user_id'])){
            $user_id=$_SESSION['user_id']=$_COOKIE['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            if(isset($_COOKIE['openid'])){
                $str=$row['openid'];
                $str=strtoupper(md5('ET'.$str));
                if($str==$_COOKIE['openid']){
                    $_SESSION['openid']=$_COOKIE['openid'];
                    return $user_id;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }elseif(isset($_COOKIE['olook'])){
            $striso=$_COOKIE['olook'];
            $arrolook=explode('-',$striso);
            $user_id=$arrolook[0];
            $openid=$arrolook[1];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            $str=$row['openid'];
            $str=strtoupper(md5('ET'.$str));
            if($str==$openid){
                $_SESSION['user_id']=$user_id;
                $_SESSION['openid']=$openid;
                return $user_id;
            }else{
                return false;
            }
        }elseif(isset($_SESSION['openid'])){
            // print_r($_SESSION['openid']);
            $user_id=$_SESSION['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            $str=$row['openid'];
            $str=strtoupper(md5('ET'.$str));
            if($str==$_SESSION['openid']){
                return $user_id;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}