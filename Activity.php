<?php
/**
 * 用户
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Activity extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->model('User_model');
    $this->load->helper('url');
    $this->load->library('session');
    $this->load->helper('cookie');
    $this->load->library('image_lib');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
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
      case 'th-th' :
        $this->lang->load('th', 'english');
        break;
      case 'th-TH' :
        $this->lang->load('th', 'english');
        break;
      case 'ko-KR' :
        $this->lang->load('hy', 'english');
        break;
      default:
        $this->lang->load('eng', 'english');
        break;
    }

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

  public function activity_term_add(){

    $this->load->view('newadmin/activity_term_add');
  }

  public function size_up($name){
    $file_size=$_FILES[$name]['size'];
    if ($file_size>51200){
      echo "请让图片小于50k";
      exit();
    }
  }

/*
 * 活动集增加
 */

  public function activity_term_insert(){
    $title=$this->input->post('title',true);
      $this->size_up('file1');
    $image=$this->upload_image('file1','act_terms');
    $displayorder=$this->input->post('displayorder',true);
    $data=array(
      'title'=>$title,
      'add_time'=>time(),
      'image'=>$image,
       'displayorder'=>$displayorder
    );
    $id=$this->User_model->user_insert('v_activity_terms',$data);
    $this->put_admin_log("活动集增加 $id");
    redirect(base_url('activity/activity_term_add'));

  }
/*
 * 活动集修改
 */
  public function activity_term_edit(){
    $id=$this->input->get('id',true);
   // echo $id;exit();
    $this->size_up('file1');
    $title=$this->input->post('title',true);
    $displayorder=$this->input->post('displayorder',true);
    $data=array(
        'title'=>$title,
        'displayorder'=>$displayorder
    );
    if($_FILES['file1']['error']==0){
      $image=$this->upload_image('file1','act_terms');
      $data['image']=$image;
    }else{
      $row=$this->User_model->get_select_one('image',array('id'=>$id),'v_activity_terms');
      $data['image']=$row['image'];
    }
      //print_r($data);exit();
     $this->User_model->update_one(array('id'=>$id),$data,'v_activity_terms');
    $this->put_admin_log("活动集修改 $id");
    redirect(base_url("activity/activity_term_list_edit?id=$id"));

  }


  /*
   * 活动集顺序提交
   */
  public function act_order(){
    $act_id= $this->input->post('act_id',true);
    $order= $this->input->post('order',true);
    $where=array(
        'id'=>$act_id
    );
    $order=array(
        'displayorder'=>$order
    );
    if($this->User_model->update_one($where,$order,'v_activity_terms')){

      $this->put_admin_log("活动集顺序；值 $order ；操作活动编号$act_id");
      echo 1;
    }
  }

  public function bus_act_order(){
    $act_id= $this->input->post('act_id',true);
    $order= $this->input->post('order',true);
    $where=array(
        'act_id'=>$act_id
    );
    $order=array(
        'displayorder'=>$order
    );
    if($this->User_model->update_one($where,$order,'v_activity_son')){
      $this->put_admin_log("子活动顺序；值 $order ；操作活动编号$act_id");
      echo 1;
    }
  }

  public function children_act_order(){
    $act_id= $this->input->post_get('act_id',true);
    $order= $this->input->post_get('order',true);
    $table= $this->input->post_get('table',true);
    if(!$table){
      $table='v_activity_children';
    }

    $where=array(
        'act_id'=>$act_id
    );
    $arr=array(
        'displayorder'=>$order
    );
    if($this->User_model->update_one($where,$arr,$table))
    {

      $this->put_admin_log("子活动顺序；值 $order ；操作活动编号$act_id");
      echo 1;
    }
  }
  public function ej_act_order(){
    $act_id= $this->input->post('act_id',true);
    $order= $this->input->post('order',true);
    $where=array(
        'act_id'=>$act_id
    );
    $order=array(
        'displayorder'=>$order
    );
    if($this->User_model->update_one($where,$order,'v_activity')){
      $this->put_admin_log("官方活动顺序；值 $order ；操作活动编号$act_id");
      echo 1;
    }
  }

  /*
   * 后台活动集列表
   */
  public function activity_term_list(){
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if($is_show==null){
      $is_show='1';
      $data['is_show']='1';
    }
    $where=' 1=1 ';


    if($data['time1']){
      $where.=" AND add_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND add_time <=$data[time2]";
    }

    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%' ";
    }
    $where.="  AND status= '$is_show'";

    $data['list']=$this->User_model->get_select_all('*',$where,'displayorder','ASC','v_activity_terms');
    $this->load->view('newadmin/activity_terms',$data);
  }



  public function father_order(){
    $act_id= $this->input->post('act_id',true);
    $order= $this->input->post('order',true);
    $where=array(
        'act_id'=>$act_id
    );
    $order=array(
        'displayorder'=>$order
    );
    if($this->User_model->update_one($where,$order,'v_activity_father')){
      echo 1;
    }
  }
  public function activity_fatherself_add(){

    $this->load->view('activity/activity_fatherself_add');
  }
  public function activity_fatherself_edit(){
    $act_id=$this->input->get('act_id',true);
    $where=array('act_id'=>$act_id);
    $data=$this->User_model->get_select_one('act_id,title,act_image,poster_image,start_time,end_time,content,is_show,request',$where,'v_activity_father');
    // echo "<pre>";print_r($data);exit();
    $this->load->view('activity/activity_fatherself_add',$data);
  }
  public function activity_fatherself_insert(){
    $this->size_up('act_image');
    $this->size_up('poster_image');
    $title=$this->input->post('title',true);
    $start_time=$this->input->post('start_time',true);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $content=$this->input->post('content',false);
    $request=$this->input->post('request',true);
    $act_image=$this->upload_image('act_image','activity_act');
    $poster_image=$this->upload_image('poster_image','activity_poster');
    $add_time=time();
    $data=array(
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'request'=>$request,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
        'is_show'=>'0',
        'add_time'=>$add_time,
        'is_set'=>'1'
    );

    //print_r()
    $this->User_model->user_insert('v_activity_father',$data);
    redirect(base_url('activity/activity_father_list'));
  }

  /*
   * 后台提交
   */
  public function activity_fatherself_sub(){
    $this->size_up('act_image');
    $this->size_up('poster_image');
    $act_id=$this->input->post('act_id',true);
    $where=array('act_id'=>$act_id);
    $title=$this->input->post('title',true);
    $start_time=$this->input->post('start_time',true);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $request=$this->input->post('request',true);
    $content=$this->input->post('content',false);
    if($_FILES['act_image']['error']==0){
      $act_image=$this->upload_image('act_image','activity_act',$act_id);
    }else{
      $row=$this->User_model->get_select_one('act_image',array('act_id'=>$act_id),'v_activity_father');
      $act_image=$row['act_image'];
    }

    if($_FILES['poster_image']['error']==0){
      $poster_image=$this->upload_image('poster_image','activity_poster',$act_id);
    }else{
      $row=$this->User_model->get_select_one('poster_image',array('act_id'=>$act_id),'v_activity_father');
      $poster_image=$row['poster_image'];
    }
    $data=array(
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'request'=>$request,
        'content'=>$content,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
    );
    //echo "<pre>";print_r($data);exit();
    $this->User_model-> update_one($where,$data,'v_activity_father');
    redirect(base_url("activity/activity_fatherself_edit?act_id={$act_id}"));
  }

  public function activity_father_list($page=1){
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if($is_show==null){
      $is_show='1';
      $data['is_show']='1';
    }
    $where=" pid= '0' AND is_temp='0' ";


    if($data['time1']){
      $where.=" AND start_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND end_time <=$data[time2]";
    }

    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%' ";
    }
    $where.="  AND is_show= '$is_show'";


    // echo $where;exit();
    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_activity_father');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $select='*';
    $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'displayorder','ASC','v_activity_father');
    $data['time2']=strtotime($this->input->get('time2'));
    $this->load->view('newadmin/activity_father_list',$data);


   // $this->load->view('activity/activity_father_list',$data);
  }

  public function activity_father_list_del(){
    $id=$this->input->get('id',true);
    $where=array('act_id'=>$id);
    if( $this->User_model->update_one($where,array('is_show'=>'2','displayorder'=>99),'v_activity_father')){
      redirect(base_url("activity/activity_father_list"));
    }
  }
  public function activity_father_showto3(){
    $id=$this->input->get('id',true);
    $where=array('act_id'=>$id);
    if( $this->User_model->update_one($where,array('is_show'=>'3','displayorder'=>99),'v_activity_father')){
      redirect(base_url("activity/activity_father_list"));
    }
  }


  public function activity_father_add(){

      $this->load->view('newadmin/activity_father_add');

  }
  public function activity_father_insert(){
    $title=$this->input->post('title',true);
    $this->size_up('file1');
    $image=$this->upload_image('file1','act_terms');
    $displayorder=$this->input->post('displayorder',true);
    $special=$this->input->post('special',true);
    $data=array(
        'title'=>$title,
        'add_time'=>time(),
        'image'=>$image,
        'displayorder'=>$displayorder,
        'special'=>$special
    );
    $this->User_model->user_insert('v_activity_father',$data);
    redirect(base_url('activity/activity_father_add'));
  }

  public function activity_father_edit(){
    $act_id=$this->input->post('id',true);
    // echo $id;exit();
    $this->size_up('file1');
    $title=$this->input->post('title',true);
    $special=$this->input->post('special',true);
    $displayorder=$this->input->post('displayorder',true);
    $data=array(
        'title'=>$title,
        'displayorder'=>$displayorder,
        'special'=>$special
    );
    if($_FILES['file1']['error']==0){
      $image=$this->upload_image('file1','act_terms');
      $data['image']=$image;
    }else{
      $row=$this->User_model->get_select_one('image',array('act_id'=>$act_id),'v_activity_father');
      $data['image']=$row['image'];
    }
 //  echo '<pre>';print_r($data);//exit();
    $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_activity_father');
    //echo $this->db->last_query();
    redirect(base_url("activity/activity_father_list"));

  }
  public function activity_father_edit_adv(){
    $id=$this->input->get('id',true);
    $data=$this->User_model->get_select_one('special,act_id,title,displayorder,image',array('act_id'=>$id),'v_activity_father');
    //echo '<pre>';print_r($data);
    $this->load->view('newadmin/activity_father_add',$data);
  }

  /*public function activity_fatherself_edit_adv(){
    $act_id=$this->input->get('act_id',true);
    $where=array('act_id'=>$act_id);
    $data=$this->User_model->get_select_one('act_id,title,act_image,poster_image,start_time,end_time,content,is_show,request',$where,'v_activity_father');
    // echo "<pre>";print_r($data);exit();
    $this->load->view('activity/activity_add_adv',$data);
  }*/


/*
   * 后台详情展示
   */
  public function activity_fatherself_info(){
   // $act_id=$this->input->get('act_id',true);
    $act_id=$this->input->get('act_id',true);
    $select='act_id,title,start_time,end_time,content,is_show,poster_image,request';
    $where=array('act_id'=>$act_id);
    $data['activity']=$this->User_model->get_select_one($select,$where,$table='v_activity_father');
    $this->load->view('activity/activity_fatherself_info',$data);
  }

/*
 *活动集修改
 */
  public function activity_term_list_edit(){
    $id=$this->input->get('id',true);
    $data=$this->User_model->get_select_one('*',array('id'=>$id),'v_activity_terms');

    $this->load->view('activity/activity_term_add',$data);
  }
/*
 * 后台活动集子活动表
 */
  public function activity_children_list($page=1){
    $data['count_url']=$this->count_url;
    $data['pid']=$pid=$this->input->get('id',true);
    $rs=$this->User_model->get_select_one('title',array('act_id'=>$pid),'v_activity_father');
    $data['p_title']=$rs['title'];
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if($is_show==null){
      $is_show='1';
      $data['is_show']='1';
    }
    $where=" pid=$pid AND is_temp='0' ";
    if($data['time1']){
      $where.=" AND start_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND end_time <=$data[time2]";
    }

    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%' ";
    }
    $where.="  AND is_show= '$is_show'";


    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_activity_children');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    //echo  $this->db->last_query();
    //  print_r($data);exit();
    $data['list']=$this->User_model->get_select_more('*',$where,$start,$page_num,'displayorder','ASC','v_activity_children');
    if($data['list']>0){
      foreach($data['list'] as $k=>$v){
        if($v['user_id']==0){
          $data['list'][$k]['shop_name']='暂无';
          continue;
        }else{
          $where=array('user_id'=>$v['user_id']);
          $rs=$this->User_model->get_select_one('shop_name',$where,'v_auth_business');
          $data['list'][$k]['shop_name']= $rs['shop_name'];
        }
      }
    }
    //$this->load->view('newadmin/bus_activity_manage',$data);
    $this->load->view('activity/children_activity_manage',$data);

  }

  public function activity_fatherself_add_adv(){


    $this->load->view('activity/activity_fatherself_add_adv');
  }



  public function activity_term_son($page=1){
    $data['count_url']=$this->count_url;
    $data['pid']=$pid=$this->input->get('id',true);
    $rs=$this->User_model->get_select_one('title',array('id'=>$pid),'v_activity_terms');
    $data['p_title']=$rs['title'];

    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if($is_show==null){
      $is_show='1';
      $data['is_show']='1';
    }
    $where=" pid=$pid ";


    if($data['time1']){
      $where.=" AND start_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND end_time <=$data[time2]";
    }

    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%' ";
    }
    $where.="  AND is_show= '$is_show'";


    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_activity_son');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    //echo  $this->db->last_query();
  //  print_r($data);exit();

    $data['list']=$this->User_model->get_select_more('*',$where,$start,$page_num,'displayorder','ASC','v_activity_son');
   if($data['list']>0){
     foreach($data['list'] as $k=>$v){
       if($v['user_id']==0){
         $data['list'][$k]['shop_name']='暂无';
        continue;
       }else{
         $where=array('user_id'=>$v['user_id']);
         $rs=$this->User_model->get_select_one('shop_name',$where,'v_auth_business');
         $data['list'][$k]['shop_name']= $rs['shop_name'];
       }
     }
   }
    $this->load->view('newadmin/bus_activity_manage',$data);

  }



  /*
   * 子活动申请记录删除
   */
  public function bus_activity_log_del(){
    $act_id=$this->input->get('act_id',true);
    $where=array('act_id'=>$act_id);
    $this->User_model->del($where,'v_activity_children_temp');
    $this->put_admin_log("子活动删除  $act_id");
    redirect(base_url('activity/bus_activity_log'));
  }
  /*
  * 后台商户活动修改
  */
  public function bus_activity_edit_adv(){
    $act_id=$this->input->get('act_id',true);
    $where=array('act_id'=>$act_id);
    $data=$this->User_model->get_select_one('*',$where,'v_activity_son');
    // echo "<pre>";print_r($data);exit();
    $this->load->view('activity/bus_activity_add_adv',$data);
  }

  public function children_activity_edit_adv(){
    $act_id=$this->input->get('act_id',true);
    $where=array('act_id'=>$act_id);
    $data=$this->User_model->get_select_one('*',$where,'v_activity_children');
    //$data['content']=str_ireplace("?","❤",$data['content']);
    // echo "<pre>";print_r($data);exit();
    $this->load->view('activity/children_activity_add_adv',$data);
  }

  /*
   * 后台子活动列表
   */
  public function son_show(){
    /*$user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return false;
    }*/
    $pid=$this->input->get('pid',true);
    $data['user_id']=$user_id=0;
    $rs_p=$this->User_model->get_select_one('title',array('act_id'=>$pid),'v_activity_father');
    $data['p_title']=$rs_p['title'];
    $rs=$this->User_model->get_select_one('is_merchant',array('user_id'=>$user_id),'v_users');
    $data['is_bus']=$rs['is_merchant'];
    $select='act_id,act_image,title';
    $where=array(
        'is_show'=>'1',
        'pid'=>$pid

    );
    $data['list']=$this->User_model->get_select_all($select,$where,'displayorder','ASC','v_activity_children');
    $data['pid']=$pid;
    //echo "<pre>";print_r($data);exit();
      $this->load->view('auth/bus_activity',$data);
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
 * app 子活动列表
 */
  public function activity_app_son($pid){
    if($this->input->get('special')){
redirect(base_url("bussell/bus_list_app/{$pid}"));
    }
    $data['count_url']=$this->count_url;
    if($pid==0){
      return false;
    }
    $act_count=$this->User_model->get_count("act_id=$pid AND is_show='1' AND is_temp='0' ", 'v_activity_father');

    if($act_count['count']==0){
     // return false;
    }
    $data['user_id']=$user_id=0;
    if(isset($_COOKIE['olook'])){
      $arr_olook=explode('-',$_COOKIE['olook']);
      $user_id=$data['user_id']=$arr_olook[0];
    }
    if(isset($_COOKIE['user_id'])){
      $user_id=$data['user_id']=$_COOKIE['user_id'];
    }
    if(isset($_SESSION['user_id'])){
      $user_id=$data['user_id']=$_SESSION['user_id'];
    }

   /* $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return false;
    }
    $data['user_id']=$user_id;*/
    $prs=$this->User_model->get_select_one('title',array('act_id'=>$pid),'v_activity_father');
    $data['p_title']=$prs['title'];
    $rs=$this->User_model->get_select_one('is_merchant',array('user_id'=>$user_id),'v_users');
    $data['is_bus']=$rs['is_merchant'];
    $select='act_id,act_image,title';
    $where=array(
        'is_show'=>'1',
        'pid'=>$pid,
        'is_temp'=>'0'

    );
    $data['list']=$this->User_model->get_select_all($select,$where,'displayorder','ASC','v_activity_children');
    $data['pid']=$pid;
    //echo "<pre>";print_r($data);exit();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $menu=$this->input->get('menu');

    if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
    {
      $this->load->view('auth/bus_activity_ios',$data);

    }else{
      $this->load->view('auth/bus_activity',$data);
    }
  }
  public function geocoder($dimension, $longitude)
  {
    $url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
    $result = file_get_contents($url);
    $result = substr($result,29);
    $result = substr($result, 0, -1);

    return $result;
  }

  public function get_city_country($dimension,$longitude){
    $position = $this->geocoder($dimension,$longitude);
    $position = json_decode($position,true);
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
  public function act_relation(){
      $type=$this->input->post('type',true);
      $video_id=$this->input->post('video_id',true);
      $act_id=$this->input->post('act_id',true);

      if($type==1){
        $this->User_model->update_one(array('video_id'=>$video_id),array('act_id'=>$act_id),'v_video');
        $this->put_admin_log("官方活动关联操作；视频{$video_id} 活动{$act_id} ");
        echo 1;
      }else{
        $this->User_model->update_one(array('video_id'=>$video_id),array('act_shop_id'=>$act_id),'v_video');
        $this->put_admin_log("子活动关联操作；视频{$video_id} 活动{$act_id} ");
        echo 1;
      }
  }

  public function change_title(){
    $video_id=$this->input->post_get('video_id',true);
    $title=$this->input->post_get('title',true);
   //echo $title;
    $this->User_model->update_one(array('video_id'=>$video_id),array('title'=>$title),'v_video');
    echo 1;
  }
  /*
   * 子活动细节
   */

  public function activiy_son_detail($act_id,$page=1){
    $data['count_url']=$this->count_url;
    if($act_id==0){
      return false;
    }

    $data['menu']='0';
    $data['per_user_id']=0;
    $data['down']="#";
    $data['app_session']=session_id();
    if(isset($_COOKIE['user_id'])){
      $data['per_user_id']=$_SESSION['user_id'];
    }
    if(isset($_COOKIE['olook'])){
      $arr_olook=explode('-',$_COOKIE['olook']);
      $data['menu']=$arr_olook[3];
      $data['per_user_id']=$arr_olook[0];
      if($data['per_user_id']==2025){
       // print_r($_COOKIE['olook']);exit();
      }
      unset($_COOKIE['olook']);
    }
    if(isset($_COOKIE['menu'])){
      $data['menu']=$_COOKIE['menu'];
      unset($_COOKIE['menu']);
    }
    if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no'){
      $location=$_COOKIE['location'];
      $location=explode(',',$location);
      $w=$location[0];$j=$location[1];
      $arr_city_country=$this->get_city_country($w,$j);
      unset($_COOKIE['location']);
    }else{
      $arr_city_country=0;
    }
    if(isset($_SESSION['user_id'])){
      $data['per_user_id']=$_SESSION['user_id'];
    }
    if(isset($_SESSION['pra'])){
      $pra=array_unique($_SESSION['pra']);
      $pra=array_values($pra);
      //组成点赞过的视频id json数据 便于前台js遍历
      $data['pra']=json_encode($pra);
    }
    $count=$this->User_model->get_act_video_count("act_shop_id=$act_id  AND is_off<2 ", 'v_video');
    $data['count']= $count['count'];
    $page_num =10;
    $data['now_page'] = $page;
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page']) {$page=1;}
    $start = ($page-1)*$page_num;
    $select="act_id,pid,title,act_image,start_time,end_time,content,poster_image,request";
    $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');
    $sharetitle= $data['activity']['title'];
    $data['act_id']=$act_id;
    $data['activity']['act_id']='p'.$act_id;
    $select="v_video.video_id,v_video.address,v_video.video_name,
    v_video.image as image,v_video.praise as praise,title,v_video.user_id,v_video.type,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
    $left_title='v_video.user_id=v_users.user_id';
    $left_table='v_users';
    $data['list']=$this->User_model->get_act_video_all($select,"act_shop_id='$act_id'  AND is_off<2 ",'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start,$page_num);
    if(!empty($data['list'])){
      foreach($data['list'] as $k => $v){
        if($v['is_off']==1){
          if($v['push_type']==0){
            $data['list'][$k]['path']= $this->config->item('record_url').$v['video_name'].'.m3u8';
          }else{
            $data['list'][$k]['path']= $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
          }
          if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
              $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          }
        }else{
          if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
          }
          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
        //  $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
        // echo "<pre>";var_dump( $data['list'][$k]['videoinfo']);
      }
    }
    if(!$data['list']){
      $data['list']=0;
    }
    $act_rs=$this->User_model->get_select_one('user_id,users',"act_id=$act_id",'v_activity_children');
    $arr_users=explode(',',$act_rs['users']);

    if($act_rs['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users)){
      $data['act_add']=TRUE;
    }else{
      $data['act_add']=FALSE;
    }
    if($arr_city_country>0){
      $data['liver_info']['country']=$arr_city_country['0'];
      $data['liver_info']['city']=$arr_city_country['1'];
      $data['liver_info']['act_id']='0';
      $data['liver_info']['act_shop_id']=$act_id;
      $data['liver_info']=json_encode($data['liver_info']);
    }else{
      $data['liver_info']='0';
    }
   /* if( $data['per_user_id']==975){
      echo "<pre>";print_r( $data['liver_info']);exit();
    }*/

    $data['share']['share_url']=base_url("activity/activiy_son_detail/$act_id");
    $data['share']['title']=$data['activity']['title'];
    $data['share']['image']=$data['activity']['act_image'];
    $data['share']['desc']="坐享其成上的一个精彩活动{{$sharetitle}}快来一起High。";
    $data['json_share']=json_encode($data['share']);

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if($this->input->get('echo')){
      echo "<pre>";
      print_r($data);
      exit();
    }
    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
      if($this->input->get('menu',true)){
        $data['menu']='1';
      }else{
        $data['menu']='menu';
      }
      if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
      {
        $data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
        //$data['activity']['content']=str_ireplace("https","olooknewwebs",$data['activity']['content']);
        $data['activity']['content']=str_ireplace("olooknewweb://api.etjourney","http://api.etjourney",$data['activity']['content']);
        $this->load->view('auth/bus_detail_ios',$data);
      }else{
        $data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
        $data['activity']['content']=str_ireplace("olooknewweb://api.etjourney","http://api.etjourney",$data['activity']['content']);
        //$data['activity']['content']=str_ireplace("https","olooknewwebs",$data['activity']['content']);
       if($data['per_user_id']==1744){
       // echo "<pre>";print_r($data);
       }
        $this->load->view('auth/bus_detail',$data);
      }
    }else{
      //$data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
      //$data['activity']['content']=str_ireplace("https","olooknewwebs",$data['activity']['content']);
      //$data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
      //$data['activity']['content']=str_ireplace("olooknewweb://api","http://api",$data['activity']['content']);

      $this->load->view('auth/bus_detail_h5',$data);
    }

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
        elseif($this->config->item('rtmp_flg') == 1)
        {
          $auth_key = $this->get_auth($video_name,$type);
          if($type)
          {
            $result = 'http://video.etjourney.com/etjourney/'.$video_name.$type.'?auth_key='.$auth_key;
          }else{
            $result = 'rtmp://video.etjourney.com/etjourney/'.$video_name.'?auth_key='.$auth_key;
          }
        }
      }
    }
    return $result;
  }

  /*
   *  后台活动总预览
   */


public function show_home(){
$data['count_url']=$this->count_url;
  $data['user_id']=$user_id=0;

  //$select='act_id as id,image,title';
  //$data['shop_list']=$this->User_model->get_select_all($select,array('is_show'=>'1'),'displayorder','ASC','v_activity_father');
  $select='act_id,act_image,title,image,is_set,special';
  $data['list']=$this->User_model->get_select_all($select,array('pid'=>'0','is_show'=>'1','is_temp'=>'0'),'displayorder','ASC','v_activity_father');
    $this->load->view('auth/activity_an',$data);

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

  /*
   * app子活动提交
   */
  public function activity_son_sub_app(){
    $user_id=trim($this->input->post('user_id',true));
    $pid=trim($this->input->post('pid',true));
    $user_name=trim($this->input->post('user_name',true));
    $title=trim($this->input->post('title',true));
    $start_time=trim($this->input->post('start_time',true));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',true));
    $end_time=strtotime($end_time);
    $content=trim($this->input->post('content',true));
    $mobile=trim($this->input->post('tel',true));
    $email=trim($this->input->post('email',true));
    $act_time_adv=time();
    $data=array(
        'user_id'=>$user_id,
        'pid'=>$pid,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'mobile'=>$mobile,
        'email'=>$email,
        'act_status'=>'1',
        'act_time_adv'=>$act_time_adv,
    );
    $this->User_model->user_insert('v_activity_children_temp',$data);
    redirect(base_url("activity/activity_app_son/{$pid}"));
  }
  public function activity_son_sub_app_ios(){
    $user_id=trim($this->input->post('user_id',true));
    $pid=trim($this->input->post('pid',true));
    $user_name=trim($this->input->post('user_name',true));
    $title=trim($this->input->post('title',true));
    $start_time=trim($this->input->post('start_time',true));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',true));
    $end_time=strtotime($end_time);
    $content=trim($this->input->post('content',true));
    $mobile=trim($this->input->post('tel',true));
    $email=trim($this->input->post('email',true));
    $act_time_adv=time();
    $data=array(
        'user_id'=>$user_id,
        'pid'=>$pid,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'mobile'=>$mobile,
        'email'=>$email,
        'act_status'=>'1',
        'act_time_adv'=>$act_time_adv,
    );
    if($this->User_model->user_insert('v_activity_children_temp',$data)){
      echo 1;
    }else{
      echo 2;
    }

  //  redirect(base_url("activity/activity_app_son/{$pid}"));
  }


  /*
     * 商户活动申请
     */
  public function activity_son_adv($pid){
    $data['count_url']=$this->count_url;
    $user_id=$_SESSION['user_id'];
    $where=array('user_id'=>$user_id);
    $data=$this->User_model->get_select_one('user_name,user_id',$where,'v_users');
    $data['pid']=$pid;


    //echo "<pre>";print_r($data);exit();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
    {
      $this->load->view('auth/app_bus_activity_ios',$data);
    }else{
      $this->load->view('auth/app_bus_activity',$data);
    }
  }
/*
 * 官方活动发布和撤下
 */
  public function activity_father_ok_no(){
    $mes=$this->input->post('mes',true);
    $act_id=$this->input->post('act_id',true);
    if($mes==1){
      $data=array(
          'is_show'=>'1',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_father')){
        $this->put_admin_log("官方活动发布 $act_id");
        echo 1;
      }
    }elseif($mes==2){
      $data=array(
          'is_show'=>'2',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_father')){
        $this->put_admin_log("官方活动撤下 $act_id");
        echo 2;
      }
    }
  }
  /*
   * 活动集发布
   */
  public function activity_father_put(){
    $id=$this->input->get('id',true);
    $where=array('act_id'=>$id);
    $data=array('is_show'=>'1');
    $this->User_model->update_one($where,$data,$table='v_activity_father');
    $this->put_admin_log("活动集发布 $id");
    redirect(base_url('activity/activity_father_list'));
  }
  /*
   * 活动集发布
   */
  public function activity_terms_put(){
    $id=$this->input->get('id',true);
    $where=array('id'=>$id);
    $data=array('STATUS'=>'1');
    $this->User_model->update_one($where,$data,$table='v_activity_terms');
    $this->put_admin_log("活动集发布 $id");
    redirect(base_url('activity/activity_term_list'));
  }


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
  /*
   * 后台活动集删除
   */
  public function activity_term_list_del(){
    $id=$this->input->get('id',true);
    $where=array('id'=>$id);
   if( $this->User_model->update_one($where,array('STATUS'=>'3','displayorder'=>99),'v_activity_terms')){
     $this->put_admin_log("活动集删除 $id");
     redirect(base_url("activity/activity_term_list"));
   }
  }

/*
 *后台子活动编辑提交
 */
  public function bus_activity_sub(){
     $this->size_up('act_image');
   $this->size_up('poster_image');
    $act_id=$this->input->post('act_id',true);
    $where=array('act_id'=>$act_id);
    $title=$this->input->post('title',true);
    $users=$this->input->post('users',true);
    $start_time=$this->input->post('start_time',true);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $request=$this->input->post('request',true);
    $content=$this->input->post('content',false);
    $user_id=$this->input->post('user_id',true);
 
    $row=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),$table='v_users');
    if($row>0){
      $user_name=$row['user_name'];
    }else{
      $user_name='暂无';
    }
    if($_FILES['act_image']['error']==0){
      $act_image=$this->upload_image('act_image','activity_act_bus','time');
    }else{
      $row=$this->User_model->get_select_one('act_image',array('act_id'=>$act_id),'v_activity_son');
      $act_image=$row['act_image'];
    }

    if($_FILES['poster_image']['error']==0){
      $poster_image=$this->upload_image('poster_image','activity_poster_bus','time');
    }else{
      $row=$this->User_model->get_select_one('poster_image',array('act_id'=>$act_id),'v_activity_son');
      $poster_image=$row['poster_image'];
    }


    $data=array(
        'user_id'=>$user_id,
        'users'=>$users,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'request'=>$request,
        'content'=>$content,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
    );
   // echo "<pre>";print_r($data);exit();
    $this->User_model-> update_one($where,$data,'v_activity_son');
    $this->put_admin_log("子活动编辑 $act_id");
    redirect(base_url("activity/bus_activity_edit_adv?act_id={$act_id}"));
  }

  public function children_activity_sub(){
    $this->size_up('act_image');
    $this->size_up('poster_image');
    $act_id=$this->input->post('act_id',true);
    $where=array('act_id'=>$act_id);
    $title=$this->input->post('title',true);
    $start_time=$this->input->post('start_time',true);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $request=$this->input->post('request',true);
    $content=$this->input->post('content',false);
    $user_id=$this->input->post('user_id',true);
    $users=$this->input->post('users',true);
    $row=$this->User_model->get_select_one('user_name',array('user_id'=>$user_id),$table='v_users');
    if($row>0){
      $user_name=$row['user_name'];
    }else{
      $user_name='暂无';
    }
    if($_FILES['act_image']['error']==0){
      $act_image=$this->upload_image('act_image','activity_act_bus','time');
    }else{
      $row=$this->User_model->get_select_one('act_image',array('act_id'=>$act_id),'v_activity_children');
      $act_image=$row['act_image'];
    }
    if($_FILES['poster_image']['error']==0){
      $poster_image=$this->upload_image('poster_image','activity_poster_bus','time');
    }else{
      $row=$this->User_model->get_select_one('poster_image',array('act_id'=>$act_id),'v_activity_children');
      $poster_image=$row['poster_image'];
    }
    $data=array(
        'user_id'=>$user_id,
      'users'=>$users,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'request'=>$request,
        'content'=>$content,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
    );
    //echo "<pre>";print_r($data);exit();
    $this->User_model-> update_one($where,$data,'v_activity_children');
    $this->put_admin_log("子活动编辑 $act_id");
    redirect(base_url("activity/children_activity_edit_adv?act_id={$act_id}"));
  }
public function show_root()
{
    echo $_SERVER['DOCUMENT_ROOT'];
}


  /*
   * 后台子活动添加提交
   */
  public function bus_activity_add_sub(){
    $title=$this->input->post('title',true);
    $start_time=$this->input->post('start_time',true);
    $user_id=$this->input->post_get('user_id',true);
     $this->size_up('act_image');     
     $this->size_up('poster_image');
    if(!$user_id){
      $user_id=0;
    }
    $pid=$this->input->post('pid',true);
    if($user_id !=0){
      $rs=$this->User_model->get_select_one('user_name',"user_id=$user_id AND is_temp='0' ",'v_auth_business');
      $user_name=$rs['user_name'];
    }else{
      $user_name='暂无';
    }
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $content=$this->input->post('content',true);
    $request=$this->input->post('request',true);
    $users=$this->input->post('users',true);
    $act_image=$this->upload_image('act_image','bus_activity_act');
    $poster_image=$this->upload_image('poster_image','bus_activity_poster');
    $add_time=time();
    $data=array(
        'title'=>$title,
        'users'=>$users,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'user_name'=>$user_name,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'request'=>$request,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
        'is_show'=>'0',
        'add_time'=>$add_time,
    );
   $act_id= $this->User_model->user_insert('v_activity_son',$data);
    $this->put_admin_log("子活动添加 $act_id");
    redirect(base_url("activity/activity_term_son?id={$pid}"));
  }

  public function children_activity_add_sub(){
    $title=$this->input->post('title',true);
    $start_time=$this->input->post('start_time',true);
    $user_id=$this->input->post_get('user_id',true);
    $this->size_up('act_image');
    $this->size_up('poster_image');
    if(!$user_id){
      $user_id=0;
    }
    $pid=$this->input->post('pid',true);
    if($user_id !=0){
      $rs=$this->User_model->get_select_one('user_name',"user_id=$user_id AND is_temp='0' ",'v_auth_business');
      $user_name=$rs['user_name'];
    }else{
      $user_name='暂无';
    }
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',true);
    $end_time=strtotime($end_time);
    $content=$this->input->post('content',true);
    $request=$this->input->post('request',true);
    $users=$this->input->post('users',true);
    $act_image=$this->upload_image('act_image','bus_activity_act');
    $poster_image=$this->upload_image('poster_image','bus_activity_poster');
    $add_time=time();
    $data=array(
        'title'=>$title,
        'users'=>$users,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'user_name'=>$user_name,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'request'=>$request,
        'act_image'=>$act_image,
        'poster_image'=>$poster_image,
        'is_show'=>'0',
        'add_time'=>$add_time,
    );
    $act_id= $this->User_model->user_insert('v_activity_children',$data);
    $this->put_admin_log("子活动添加 $act_id");
    redirect(base_url("activity/activity_term_son?id={$pid}"));
  }
  /*
    * 后台发布撤销商户活动
    */
  public function bus_activity_ok_no(){
    $mes=$this->input->post('mes',true);
    $act_id=$this->input->post('act_id',true);
    if($mes==1){
      $data=array(
          'is_show'=>'1',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_son')){
        $this->put_admin_log("发布商户活动 $act_id");
        echo 1;
      }
    }elseif($mes==2){
      $data=array(
          'is_show'=>'2',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_son')){
        $this->put_admin_log("撤销商户活动 $act_id");
        echo 2;
      }
    }
  }

  public function children_activity_ok_no(){
    $mes=$this->input->post('mes',true);
    $act_id=$this->input->post('act_id',true);
    if($mes==1){
      $data=array(
          'is_show'=>'1',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_children')){
        $this->put_admin_log("发布商户活动 $act_id");
        echo 1;
      }
    }elseif($mes==2){
      $data=array(
          'is_show'=>'2',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity_children')){
        $this->put_admin_log("撤销商户活动 $act_id");
        echo 2;
      }
    }
  }
/*
 * 后台子活动删除
 */
  public function bus_activity_del_one(){
    $act_id=$this->input->get('act_id');
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_son');
    $pid=$rs['pid'];
    $data=array(
        'is_show'=>'3',
        'displayorder'=>99
        );
    $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_activity_son');
    $this->put_admin_log("子活动删除 $act_id");
    redirect(base_url("activity/activity_term_son?id={$pid}"));
  }
  public function children_activity_del_one(){
    $act_id=$this->input->get('act_id');
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_children');
    $pid=$rs['pid'];
    $data=array(
        'is_show'=>'3',
        'displayorder'=>99
    );
    $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_activity_children');
    $this->put_admin_log("子活动删除 $act_id");
    redirect(base_url("activity/activity_children_list?id={$pid}"));
  }
  /*
   * 商户活动详情
   */
  public function bus_activity_info(){
    $data['count_url']=$this->count_url;
    $act_id=$this->input->get('act_id',true);
    $select='*';
    $where=array('act_id'=>$act_id);
    $data['activity']=$this->User_model->get_select_one($select,$where,$table='v_activity_son');

    //echo "<pre>";print_r($data);exit();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('activity/bus_activity_info',$data);
  }

  public function children_activity_info(){
    $data['count_url']=$this->count_url;
    $act_id=$this->input->get('act_id',true);
    $select='*';
    $where=array('act_id'=>$act_id);
    $data['activity']=$this->User_model->get_select_one($select,$where,$table='v_activity_children');

    //echo "<pre>";print_r($data);exit();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('activity/children_activity_info',$data);
  }

  /**
   * [image 图片上传]
   * @param  [type] $filename [description]
   * @param  [type] $fileurl  [description]
   * @return [type]           [description]
   */
  /*
   * 子活动增加界面
   */
  public function bus_activity_add(){
    $data['user_id']=$this->input->get('user_id',true);
    if(!$data['user_id']){
      $data['user_id']=0;
    }
    $data['pid']=$this->input->get('pid',true);
    $this->load->view('activity/bus_activity_add_adv',$data);
  }

  public function children_activity_add(){
    $data['user_id']=$this->input->get('user_id',true);
    if(!$data['user_id']){
      $data['user_id']=0;
    }
    $data['pid']=$this->input->get('pid',true);
    $this->load->view('activity/children_activity_add_adv',$data);
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


  //
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
}