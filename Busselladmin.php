<?php
/**
 * 商户操作后台
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Busselladmin extends CI_Controller {
  public function __construct()
  {
    parent::__construct();

    return false;
    ini_set('php_mbstring','1');
    $this->load->model('User_model');
    $this->load->model('Order_model');
    $this->load->library('common');
    $this->load->library('session');
    $this->load->helper('url');
    $this->load->library('image_lib');
    // $this->load->driver('cache');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
  }

  //商户登录界面
  public function login()
  {
    if(isset($_SESSION['bus_user_id'])){
      redirect('Busselladmin/index');
    }else{
      $data['down']="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
      $this->load->view('busselladmin/login',$data);
    }

  }
  //退出登录
  public function logout(){
    unset($_SESSION['bus_user_id']);
    redirect('Busselladmin/login');
  }

  //验证码
  public function get_cpa()
  {
    $this->load->library('captcha');
    $code = $this->captcha->getCaptcha();
    $_SESSION['code']=strtoupper($code);
    $_SESSION['time']=time();
    $this->captcha->showImg();
  }
  //
  public function act_login()
  {

    $captcha=strtoupper($this->input->post('code',true));
    //echo 'ca:'.$captcha,'sess:'.$_SESSION['code'];exit();
    $time=time();
    if(!isset($_SESSION['code']) OR $captcha!=$_SESSION['code'] OR ($time-$_SESSION['time'])>360){
      unset($_SESSION['code']);
      //验证码错误或者过期
      echo 1;
      //die(1);
    }else{
      unset($_SESSION['code']);
      $account =  $this->input->post('user',true);
      $password   =  $this->input->post('pwd',true);
      if(empty($account) OR empty($account))
      {
        //非法登录
        echo 2;
        //echo '<meta http-equiv="refresh" content="1; url=/Busselladmin/login?tip=2">';die;
      }else{
        $rs=$this->User_model->get_select_one('user_id,password',array('account'=>$account,'is_merchant'=>'1'),'v_users');
        if($rs!==0){
          if($rs['password']==md5($password)){
            $_SESSION['bus_user_id']=$rs['user_id'];
            if($this->User_model->update_one(array('user_id'=>$_SESSION['bus_user_id']),array('login_time'=>time()),$table='v_users')){
             //登录成功
              echo 5;
            }else{
              echo 4;
            }
          }else{
            //密码错误
            echo 4;
          }
        }else{
          //用户不存在或者您并未通过app商户认证
          echo 3;
        }
      }
    }
  }
  public function index(){
    if(isset($_SESSION['bus_user_id'])){
      $zarr=$this->User_model->get_select_one('act_id',array('user_id'=>$_SESSION['bus_user_id'],'special'=>'1','pid'=>0),'v_activity_father');
      $zid=$zarr['act_id'];
      $this->load->view('busselladmin/index');
    }else
    {
      redirect(base_url('busselladmin/login'));
    }

  }

  public function top(){
    if(empty($_SESSION['bus_user_id']))
    {
      redirect(base_url('busselladmin/login'));
    }else{
      $user_id=$_SESSION['bus_user_id'];
      $data=$this->User_model-> get_select_one('user_name as admin_name',array('user_id'=>$user_id),$table='v_users');
      $this->load->view('busselladmin/top',$data);
    }

  }
  public function  left()
  {
    $data['count_url']=$this->count_url;
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
      //$data['title_arr3']=$this->User_model->get_select_all('*',"is_show='1' AND is_set='0' AND special='1' AND pid='0' ",'displayorder', 'ASC','v_activity_father');
      $data['title_arr3']=$this->User_model->get_select_all('*',"act_id=65 ",'displayorder', 'ASC','v_activity_father');
      $data['wx_shop']=0;
      $data['order']=0;
      if($user_id==1 || $user_id==928){
        $data['wx_shop']=1;
       // $data['order']=1;
      }
      //print_r($data);exit();
      $this->load->view('busselladmin/left',$data);
    }else{
      redirect(base_url('busselladmin/login'));
    }
  }
  public function main($page=1)
  {
    if(isset($_SESSION['bus_user_id'])){

      $this->load->view('busselladmin/main');
      // $this->load->view('newadmin/report',$data);
    }else{
      redirect(base_url('busselladmin/login'));
    }
  }
  public function password_edit_adv(){
    if(isset($_SESSION['bus_user_id'])){

      $user_id=$_SESSION['bus_user_id'];
      $where=array('user_id'=>$user_id);
      $data=$this->User_model->get_select_one($select='account',$where,'v_users');
      unset($_COOKIE);
      $this->load->view('busselladmin/password_edit',$data);
    }else{
      return false;
    }
  }
  public function password_edit_sub(){
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];

      $where=array('user_id'=>$user_id);
      $old_pass=$this->input->post('password_old',true);
      $new_pass=$this->input->post('password_new',true);
      $data=$this->User_model->get_select_one('password',$where,'v_users');
      if($data['password']==md5($old_pass)){
        $new_pass=md5($new_pass);
        $this->User_model->update_one($where,array('password'=>$new_pass),'v_users');
        unset($_SESSION['bus_user_id']);
        redirect(base_url('busselladmin/index'));
      }else{
        echo '旧密码错误';
      }
    }else{
      return false;
    }
  }

public function activity_all_add(){
  if(isset($_SESSION['zid'])){
    $zid=$_SESSION['zid'];
  }else{
    unset($_SESSION['bus_user_id']);
    redirect(base_url('busselladmin/login'));
  }
  $data['all']=TRUE;
  $data['htitle']='活动申请';
  //$this->load->view('busselladmin/acti_apply',$data);
  $this->load->view('busselladmin/acti_apply_all',$data);

}

public function add_activity(){
  $pid=$this->input->get('pid',true);
  $data['info']['pid']=$pid;
  $data['htitle']='添加活动';
  $data['hasgoods']=TRUE;
  $this->load->view('busselladmin/acti_apply',$data);
}



  public function edit_activity($act_id){
    $data['htitle']='活动添加';
    $data['edit']=TRUE;

    $data['info']=$this->User_model->get_select_one(
        'act_id,user_id,pid,user_name,title,banner_image,content_text,is_show,displayorder,start_time,end_time,day_list',
        array('act_id'=>$act_id),'v_activity_children');
    $data['info']['content_text']=trim($data['info']['content_text']);
    $data['info']['content_text']=str_replace("<br>","\n", $data['info']['content_text']);;
    $data['day']=json_decode($data['info']['day_list'],true);
    $data['day']=array_filter( $data['day']);
    foreach( $data['day'] as $k=>$v)
    {
      $data['day'][$k]=  str_replace("<br>","\n", $v);;
    }

    $data['goods']=$this->User_model->get_select_all('goods_id,goods_name,goods_number,is_show,shop_price,act_id,low,pricehas,priceno,pricecom,dateto',
        array('act_id'=>$act_id),'goods_id', 'ASC','v_goods');
    if($data['goods']!==FALSE){
      $data['hasgoods']=TRUE;
    }
    foreach( $data['goods'] as $k=>$v)
    {
      $data['goods'][$k]['pricehas'] = str_replace("<br>","\n", $v['pricehas']);
      $data['goods'][$k]['priceno'] = str_replace("<br>","\n", $v['priceno']);
      $data['goods'][$k]['pricecom'] = str_replace("<br>","\n", $v['pricecom']);
      $data['goods'][$k]['dateto'] = str_replace("<br>","\n", $v['dateto']);
    }
    if($this->input->get('test')){
      echo '<pre>';
      print_r($data);exit();
    }
    if($this->input->get('fwb'))
    {
      $this->load->view('busselladmin/acti_apply_new',$data);
    }
    else
    {
      $this->load->view('busselladmin/acti_apply',$data);
    }

  }

public function easy_sodetail($act_id){
  $data['htitle']='活动添加';
  $data['edit']=TRUE;
  $data['no_sub']=TRUE;
  $data['info']=$this->User_model->get_select_one(
      'act_id,user_id,pid,user_name,title,banner_image,content_text,is_show,displayorder,start_time,end_time,day_list',
      array('act_id'=>$act_id),'v_activity_children');
  $data['info']['content_text']=trim( $data['info']['content_text']);
  $data['day']=json_decode($data['info']['day_list'],true);
  $data['day']=array_filter( $data['day']);
  $data['goods']=$this->User_model->get_select_all('goods_id,goods_name,goods_number,is_show,shop_price,act_id',
      array('act_id'=>$act_id),'goods_id', 'ASC','v_goods');
  if($data['goods']!==FALSE){
    $data['hasgoods']=TRUE;
  }
  if($this->input->get('test')){
    echo '<pre>';
    print_r($data);
    exit();
  }
  $this->load->view('busselladmin/acti_apply',$data);
}

//企业+活动申请
  public function activity_insert_all(){
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
    }else{
      redirect('busselladmin/login');
    }
    if(isset($_SESSION['zid'])){
      $zid=$_SESSION['zid'];
    }else{
      unset($_SESSION['bus_user_id']);
      redirect(base_url('busselladmin/login'));
    }

    $data_bus['cor_name']=trim($this->input->post('cor_name',true));
    $data_bus['user_id']=$user_id;
    $data_bus['pid']=$zid;
    $data_bus['special']='1';
    $data_bus['is_temp']='1';
    $data_bus['act_status']='1';
    $data_bus['email']=trim($this->input->post('email',true));
    $data_bus['mobile']=trim($this->input->post('mobile',true));
    $data_bus['add_time']=time();
    if($_FILES['logo']['error']==0){
      $logo_image=$this->upload_image('logo',$user_id.'logo');
      $data_bus['logo_image']= $this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
    }else{
      $data_bus['logo_image']='/public/newadmin/images/logo.png';
    }

    $no_cerusers=$this->get_nocer();
    if(in_array($user_id,$no_cerusers)){
      $data_bus['is_temp']='0';
      $data_bus['act_status']='2';
      $data_bus['is_show']='1';
      $pid= $this->User_model->user_insert('v_activity_father',$data_bus);
    }else{
      $pid= $this->User_model->user_insert('v_activity_father',$data_bus);
    }
    $up_down_act=$this->input->post('up_down_act',true);
    if($up_down_act==0){
      $data['is_show']='2';
    }elseif($up_down_act==1){
      $data['is_show']='1';
    }
    $up_down_goods=$this->input->post('up_down_goods',true);

    if($this->input->post('top')){
      $disorder=1;
    }else{
      $disorder=99;
    }
    $title=trim($this->input->post('title',true));
    $start_time=trim($this->input->post('start_time',true));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',true));
    $end_time=strtotime($end_time);
    $day=$this->input->post('day',true);
    $new_day=$day;
    foreach($day as $k=>$v){
      $new_day[$k] = str_replace("\n","<br>", $v);
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);
    $day=json_encode($new_day);

    $content=trim($this->input->post('content',true));
 //   $content = str_replace("'","‘", $content);
    $content = str_replace("\n","<br>", $content);

    $content = str_replace("script","", $content);
    $content = str_replace("onclick","", $content);

    $request=trim($this->input->post('request',true));
    $ed_check=trim($this->input->post('ed_check',true));
    $banner_image=$this->upload_image('banner',$user_id.'banner');
    $banner_image= $this->imagecropper($banner_image,'banner','time',$width='700',$height='300');
    $data=array(
        'title'=>$title,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'day_list'=>$day,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content_text'=>$content,
        'content'=>$content,
        'request'=>$request,
        'banner_image'=>$banner_image,
        'act_status'=>'1',
        'displayorder'=>$disorder,
        'is_temp'=>'1',
        'add_time'=>time(),
    );
    if($up_down_goods==0){
      $data['is_show']='2';
    }elseif($up_down_goods==1){
      $data['is_show']='1';
    }
    if(in_array($user_id,$no_cerusers)){
      $data['is_temp']='0';
      $data['act_status']='2';
    }

    $act_id= $this->User_model->user_insert('v_activity_children',$data);

    $goods_number=trim($this->input->post('goods_number',true));
    $shop_price=trim($this->input->post('shop_price',true));

    $dateto=$this->input->post('dateto',true);
    $dateto = str_replace("\n","<br>", $dateto);

    $pricehas=$this->input->post('pricehas',true);
    $pricehas = str_replace("\n","<br>", $pricehas);

    $priceno=$this->input->post('priceno',true);
    $priceno = str_replace("\n","<br>", $priceno);

    $pricecom=$this->input->post('pricecom',true);
    $pricecom = str_replace("\n","<br>", $pricecom);

    $low=$this->input->post('low',true);
    if($low!=1){
      $low=0;
    }
    $data=array(
        'goods_name'=>$title,
        'goods_number'=>$goods_number,
        'shop_price'=>$shop_price,
        'act_id'=>$act_id,
        'add_time'=>time(),
        'low'=>$low,
        'dateto'=>$dateto,
        'pricehas'=>$pricehas,
        'priceno'=>$priceno,
        'pricecom'=>$pricecom,

    );
    if($up_down_goods==0){
      $data['is_show']='2';
    }elseif($up_down_goods==1){
      $data['is_show']='1';
    }
    if($ed_check==1 && $goods_number>0 && $shop_price>0){
      $this->User_model->user_insert('v_goods',$data);
    }
    redirect(base_url("busselladmin/children_activity_log/{$zid}?act_status=9"));
  }

  //1.上传企业信息
  public function act_insert_all(){
    if(isset($_SESSION['bus_user_id']))
    {
      $user_id=$_SESSION['bus_user_id'];
    }
    else
    {
      redirect('busselladmin/login');
    }
    if(isset($_SESSION['zid']))
    {
      $zid=$_SESSION['zid'];
    }
    else
    {
      unset($_SESSION['bus_user_id']);
      redirect(base_url('busselladmin/login'));
    }

    $where="user_id=$user_id AND act_status <4 AND special='1'";
    $rsc=$this->User_model->get_select_one('act_id',$where,'v_activity_father');
    if($rsc!==0){
      redirect(base_url("busselladmin/cor_see?act_id=$rsc[act_id]"));
     // return false;
    }
    $data_bus['cor_name']=trim($this->input->post('cor_name',true));
    $data_bus['user_id']=$user_id;
    $data_bus['pid']=$zid;
    $data_bus['special']='1';
    $data_bus['is_temp']='1';
    $data_bus['act_status']='1';
    $data_bus['email']=trim($this->input->post('email',true));
    $data_bus['mobile']=trim($this->input->post('mobile',true));
    $data_bus['add_time']=time();


    if($_FILES['logo']['error']==0){
      $logo_image=$this->upload_image('logo',$user_id.'logo');
      $data_bus['logo_image']= $this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
    }else{
      $data_bus['logo_image']="./public/newadmin/images/logo.png";
    }

    $no_cerusers=$this->get_nocer();
    if(in_array($user_id,$no_cerusers)){
      $data_bus['is_temp']='0';
      $data_bus['act_status']='2';
      $data_bus['is_show']='1';
      $pid= $this->User_model->user_insert('v_activity_father',$data_bus);
    }else{
      $pid= $this->User_model->user_insert('v_activity_father',$data_bus);
    }

    redirect(base_url("busselladmin/add_activity/{$pid}"));
  }

  public function temp($act_id){
    redirect(base_url("busselladmin/edit_activity/{$act_id}"));
   // redirect(base_url("busselladmin/add_activity/{$pid}"));
  }
//企业活动申请
  public function activity_insert(){
   if(isset($_SESSION['bus_user_id'])){
     $user_id=$_SESSION['bus_user_id'];
   }else{
     redirect('busselladmin/login');
   }
    if(isset($_SESSION['zid'])){
      $zid=$_SESSION['zid'];
    }else{
      unset($_SESSION['bus_user_id']);
      redirect(base_url('busselladmin/login'));
    }
    //活动上下架
    $up_down_act=$this->input->post('up_down_act',true);
    //商品上下架
    $up_down_goods=$this->input->post('up_down_goods',true);
    if($this->input->post('top')){
      $disorder=1;
    }else{
      $disorder=99;
    }
    // $this->size_validate('banner',61440);
    $title=trim($this->input->post('title',true));

    $pid=trim($this->input->post('pid',true));
    $zid=trim($this->input->post('bus_id',true));
    if(!$zid){
      $rsz=$this->User_model->get_select_one('pid',array('act_id'=>$zid),'v_activity_father');
      $zid=$rsz['pid'];
    }
    $start_time=trim($this->input->post('start_time',true));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',true));
    $end_time=strtotime($end_time);

    $day=$this->input->post('day',true);
    $new_day=$day;
    foreach($day as $k=>$v){
      $new_day[$k] = str_replace("\n","<br>", $v);
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);
    $day=json_encode($new_day);
    $content=trim($this->input->post('content',true));
    $content = str_replace("\n","<br>", $content);
    $content = str_replace("script","", $content);
    $content = str_replace("onclick","", $content);
    $request=trim($this->input->post('request',true));
    $ed_check=trim($this->input->post('ed_check',true));

    if($_FILES['banner']['error']==0)
    {
      $banner_image=$this->upload_image('banner',$user_id.'banner');
      $banner_image= $this->imagecropper($banner_image,'banner','time',$width='700',$height='300');
    }
    else
    {
      $banner_image="/public/newadmin/images/logo.png";
    }

    $add_time=time();
    $data=array(
        'title'=>$title,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'day_list'=>$day,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content_text'=>$content,
        'content'=>$content,
        'request'=>$request,
        'banner_image'=>$banner_image,
        'act_status'=>'1',
        'displayorder'=>$disorder,
        'is_temp'=>'1',
        'add_time'=>$add_time,
    );
    if($up_down_act==0){
      $data['is_show']='2';
    }elseif($up_down_act==1){
      $data['is_show']='1';
    }
    //
    //echo "<pre>";print_r($data);exit();
    $noceruser=$this->get_nocer();
    if(in_array($user_id,$noceruser)){
      $data['is_temp']='0';
      $data['act_status']='2';

    }
    $act_id= $this->User_model->user_insert('v_activity_children',$data);


    $goods_number=$this->input->post('goods_number',true);
    $shop_price=$this->input->post('shop_price',true);

    $dateto=$this->input->post('dateto',true);
    $pricehas=$this->input->post('pricehas',true);
    $priceno=$this->input->post('priceno',true);
    $pricecom=$this->input->post('pricecom',true);

    $low=$this->input->post('low',true);
    if($low!=1){
      $low=0;
    }
    $data=array(
        'goods_name'=>$title,
        'goods_number'=>$goods_number,
        'shop_price'=>$shop_price,
        'act_id'=>$act_id,
        'add_time'=>$add_time,
        'low'=>$low,
        'dateto'=>$dateto,
        'pricehas'=>$pricehas,
        'priceno'=>$priceno,
        'pricecom'=>$pricecom,

    );
    if($up_down_goods==0){
      $data['is_show']='2';
    }elseif($up_down_goods==1){
      $data['is_show']='1';
    }
    if($ed_check==1 && $goods_number>0 && $shop_price>0){
      $this->User_model->user_insert('v_goods',$data);
    }
    redirect(base_url("busselladmin/edit_activity/{$act_id}"));
  }





  //简易效果展示
  public function easy_detail($act_id,$page=1){
    $data['count_url']=$this->count_url;
    $data['down']=$this->down;
    if($act_id==0){
      return false;
    }
    $data['menu']='0';
    $data['per_user_id']=-1;
    $data['down']="#";
    $data['app_session']=session_id();
    $arr_city_country=0;
    if(isset($_SESSION['bus_user_id'])){
      $data['per_user_id']=$_SESSION['bus_user_id'];
    }else{
      redirect('busselladmin/login');
    }
 //   echo 1;
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
    $select="act_id,pid,title,act_image,start_time,end_time,content,banner_image as poster_image,banner_image,request";
    $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');
    $pid=$data['activity']['pid'];
    $zid=$this->User_model->get_select_one('pid',"act_id=$pid",'v_activity_father');
    $data['zid']=$zid['pid'];

    $sharetitle= $data['activity']['title'];
    $data['act_id']=$act_id;
    $data['activity']['act_id']='p'.$act_id;
    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.image as image,v_video.praise as praise,title,v_video.user_id,
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
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
      }
    }
    if(!$data['list']){
      $data['list']=0;
    }
    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price,low,pricehas,priceno,pricecom,dateto',
        "act_id=$act_id AND is_show='1'",'v_goods');
    if(is_array($data['goods'])){
      $goods_id=$data['goods']['goods_id'];
    }else{
      $goods_id=0;
    }

    if($data['goods']['low']==1){
      $data['goods']['shop_price'].='起';
    }

    $where="goods_id=$goods_id AND order_status > '0'";
    $data['goods_buy']=$this->User_model->get_order_count($where);
    $data['goods_buy']=$data['goods_buy']['count'];


    $act_rs=$this->User_model->get_select_one('user_id,users,day_list,pid',"act_id=$act_id",'v_activity_children');
    $pid=$act_rs['pid'];
    $act_rs_father=$this->User_model->get_select_one('user_id,users,pid',"act_id=$pid",'v_activity_father');
    $arr_users=explode(',',$act_rs_father['users']);
    $data['day']=json_decode($act_rs['day_list'],true);
    foreach($data['day'] as $k=>$v){
      if($v==''){
       // echo $k;
        unset($data['day'][$k]);
      }
    }

    // echo"<pre>";var_dump($data['day']);exit();

    if($act_rs_father['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users)){
      $data['act_add']=TRUE;
    }else{
      $data['act_add']=FALSE;
    }
    if($act_rs_father['user_id']==$data['per_user_id'] OR $this->input->get('admin') ){
      $data['edit']=TRUE;
    }else{
      $data['edit']=FALSE;
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
    $data['share']['share_url']=base_url("bussell/bus_children_detail_app/$act_id");
    $data['share']['title']=$data['activity']['title'];
    $data['share']['image']=$data['activity']['banner_image'];
    $data['share']['desc']="坐享其成上的一个精彩活动{{$sharetitle}}快来一起High。";
    $data['json_share']=json_encode($data['share']);
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if($this->input->get('test')){
      echo "<pre>";
      print_r($data);
      exit();
    }
    $this->load->view('busselladmin/easy_detail',$data);
  }

  //商户后台活动资料修改后台提交
  public function activity_sub(){

    $no_cerusers=$this->get_nocer();
    $act_id=$this->input->post('act_id',TRUE);

    $rs=$this->User_model->get_select_one('pid,is_temp',array('act_id'=>$act_id),'v_activity_children');
    if($rs['is_temp']==1){
      $now_temp=1;
    }else{
      $now_temp=0;
    }
    $user_id=$this->validate($rs['pid']);
    if($user_id==1){
    //  echo '<pre>';print_r($_POST);exit();
    }
  //  echo $act_id,',',$user_id;exit();
    if($user_id===false){
      return false;
    }
    $pid=$rs['pid'];
    //  echo $user_id;exit();
    //活动上下架
    $up_down_act=$this->input->post('up_down_act',true);
    //商品上下架
    $up_down_goods=$this->input->post('up_down_goods',true);
    if($this->input->post('top')){
      $disorder=1;
    }else{
      $disorder=99;
    }
    if($up_down_act==0){
      $act_is_show='2';
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_activity_children');
    }else{
      $act_is_show='1';
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'1'),$table='v_activity_children');
    }
    if($up_down_goods==0){
      $good_is_show='2';
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_goods');
    }else{
      $good_is_show='1';
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'1'),$table='v_goods');
    }
    if($disorder==1){
      $this->User_model->update_one(array('act_id'=>$act_id),array('displayorder'=>'1'),$table='v_activity_children');
      $where="pid=$pid AND act_id!=$act_id";
      $this->User_model->update_one($where,array('displayorder'=>'99'),$table='v_activity_children');
    }

    $title=trim($this->input->post('title',true));
    $content=trim($this->input->post('content',false));
    $content = str_replace("\n","<br>", $content);
    $content = str_replace("script","", $content);
    $content = str_replace("onclick","", $content);


    $ed_check=trim($this->input->post('ed_check',true));

    $day=$this->input->post('day',true);

    $new_day=$day;
    foreach($day as $k=>$v){
      $new_day[$k] = str_replace("\n","<br>", $v);
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);
    $day=json_encode($new_day);

    $add_time=time();

    $data=array(
        'title'=>$title,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'day_list'=>$day,


        'content_text'=>$content,
         'content'=>$content,

        'act_status'=>'1',
        'is_temp'=>'1',
      // 'type'=>'father',
        'displayorder'=>$disorder,
        'is_show'=>$act_is_show,
        'add_time'=>$add_time,
    );
    if($_FILES['banner']['error']==0){
      $banner_image=$this->upload_image('banner',$user_id.'banner');
      $banner_image= $this->imagecropper($banner_image,'banner','time',$width='700',$height='300');

      //$banner_image=$this->upload_image('banner',$user_id.'banner');
      $data['banner_image']=$banner_image;
    }else{
      $row=$this->User_model->get_select_one('banner_image',array('act_id'=>$act_id),'v_activity_children');
      $data['banner_image']=$row['banner_image'];
    }
    if($user_id==1){
     // echo '<pre>';print_r($data);echo '<br>';print_r($ed_check);
      // exit();
    }
    if(in_array($user_id,$no_cerusers)){
      $data['act_status']='2';
      $data['is_temp']='0';

      $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');

      $goods_number=$this->input->post('goods_number',true);
      $shop_price=$this->input->post('shop_price',true);




      $dateto=$this->input->post('dateto',true);
      $dateto = str_replace("\n","<br>", $dateto);

      $pricehas=$this->input->post('pricehas',true);
      $pricehas = str_replace("\n","<br>", $pricehas);

      $priceno=$this->input->post('priceno',true);
      $priceno = str_replace("\n","<br>", $priceno);

      $pricecom=$this->input->post('pricecom',true);
      $pricecom = str_replace("\n","<br>", $pricecom);


      $low=$this->input->post('low',true);
      if($low!=1){
        $low=0;
      }
      $data=array(
          'goods_name'=>$title,
          'goods_number'=>$goods_number,
          'shop_price'=>$shop_price,
          'is_show'=>$good_is_show,
          'act_id'=>$act_id,
          'add_time'=>$add_time,
          'low'=>$low,
          'dateto'=>$dateto,
          'pricehas'=>$pricehas,
          'priceno'=>$priceno,
          'pricecom'=>$pricecom,
      );
      if($user_id==1){
      // echo '<pre>';print_r($data);echo '<br>';print_r($ed_check);exit();
      }
      //如果选择商品上架，同时数目，价格大于0，则操作商品表信息
      if($ed_check==1 && intval($goods_number)>0 && floatval($shop_price)>0){
        //查询商品表内是否有绑定该正式活动的商品
        $count=$this->User_model->get_count(array('act_id'=>$act_id),'v_goods');
        echo '<br>';
        print_r($count);
        if($count['count']==0){
          //未查到，插入商品信息
          $this->User_model->user_insert('v_goods',$data);
        }else{
          //查到，直接更新商品信息
          print_r($data);
          print_r($act_id);
        //  exit();
          $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_goods');
        }
      }
      if(!$ed_check){
        //未选取，下架商品
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_goods');
      }

    }else{
      if($now_temp==1){
        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
      }else{
        //活动临时记录与正式记录关联表，
        $count=$this->User_model->get_count(array('act_vc_id'=>$act_id), $table='v_temp_real');
        //没有关联信息
        if($count['count']==0){
          //插入一条临时记录，获取临时记录act_id,同时将修改活动的id鱼产生的id同时插入关联表
          $act_tc_id= $this->User_model->user_insert('v_activity_children',$data);
          $this->User_model->user_insert('v_temp_real',array('act_vc_id'=>$act_id,'act_tc_id'=>$act_tc_id));
        }else{
          //存在关联信息，抽出临时记录表id，同时提交的字段更新至临时记录
          $act_tc_id=$this->User_model->get_select_one('act_tc_id',array('act_vc_id'=>$act_id),'v_temp_real');
          $act_tc_id=$act_tc_id['act_tc_id'];
          $this->User_model->update_one(array('act_id'=>$act_tc_id),$data,$table='v_activity_children');
        }
      }

      $goods_number=$this->input->post('goods_number',true);
      $shop_price=$this->input->post('shop_price',true);

      $dateto=$this->input->post('dateto',true);
      $pricehas=$this->input->post('pricehas',true);
      $priceno=$this->input->post('priceno',true);
      $pricecom=$this->input->post('pricecom',true);

      $low=$this->input->post('low',true);
      if($low!=1){
        $low=0;
      }
      $data=array(
          'goods_name'=>$title,
          'goods_number'=>$goods_number,
          'shop_price'=>$shop_price,
          'is_show'=>$good_is_show,
          'act_id'=>$act_tc_id,
          'goods_status'=>'1',
          'add_time'=>$add_time,
          'low'=>$low,
          'dateto'=>$dateto,
          'pricehas'=>$pricehas,
          'priceno'=>$priceno,
          'pricecom'=>$pricecom,
      );
      //如果选择商品上架，同时数目，价格大于0，则操作商品表信息
      if($ed_check==1 && intval($goods_number)>0 && floatval($shop_price)>0){
        //查询商品表内是否有绑定该正式活动的商品
        $count=$this->User_model->get_count(array('act_id'=>$act_id),'v_goods');
        if($count['count']==0){
          //未查到，插入商品信息
          $this->User_model->user_insert('v_goods',$data);
        }else{
          //查到，直接更新商品信息
          $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_goods');
        }
      }
      if(!$ed_check){
        //未选取，下架商品
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_goods');
      }
    }

    redirect(base_url("busselladmin/children_activity_log/{$_SESSION['zid']}"));
  }

  public function cor_see(){
    $act_id=$this->input->get('act_id');
    $user_id=$this->validate($act_id);
    if(!$user_id){
      return false;
    }
    $data['list']=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_father');
    $data['see']=TRUE;
    $this->load->view('busselladmin/cor_edit',$data);
  }


//商户申请官方后台
  public function children_activity_log($gran_id,$page=1){
    if(!isset($_SESSION['bus_user_id'])){
      redirect(base_url('busselladmin/login'));
    }else{
      $user_id=$_SESSION['bus_user_id'];
    }


    $zid=$_SESSION['zid']=$data['gran_id']=$gran_id;
    $zarr=$this->User_model->get_select_one('title',array('act_id'=>$gran_id),'v_activity_father');
    $data['ztitle']=$zarr['title'];
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $data['type']=trim($this->input->get('type',true));
    $is_show=$data['is_show']=trim($this->input->get('is_show',true));
    $act_status= $data['act_status']= $this->input->get('act_status',true);
    if(!$act_status){
      $act_status= $data['act_status']='2';
      $data['is_show']=$is_show='1';
    }
    $where="user_id=$user_id AND pid=$zid AND act_status<'4'";
    $data['pact']=$this->User_model->get_select_one('cor_name,logo_image,act_id,act_status,is_show,is_temp',$where,'v_activity_father');


    if($data['pact']===0){
     $data['canapply']=TRUE;
    }else{
     $data['canapply']=FALSE;
    }

    $pid=$data['pact']['act_id'];
    $where=" pid='$pid' AND pid!='0' ";
    if($act_status ==2){
      $where.=" AND is_temp ='0'";
    }
    if($data['time1']){
      $where.=" AND add_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND add_time <=$data[time2]";
    }
    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%'";

    }else{
      $data['type']=0;
    }
    if($is_show){
      $where.=" AND is_show='$is_show'";
    }
    $where.="  AND act_status= '$act_status'";
    $page_num =100;
    $data['now_page'] = $page;

    $count = $this->User_model->get_count($where,'v_activity_children');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list']=$this->User_model->get_select_more($select='title,act_id', $where, $start, $page_num, 'displayorder', $order='ASC',
        $table='v_activity_children');
    if($data['list']===0){
      $data['list']=array();
    }
    if($data['act_status']==9){
      $data['pact']=$this->User_model->get_select_one('cor_name,logo_image,act_id,act_status,is_show,is_temp',array('user_id'=>$user_id,'act_status'=>'1'),$table='v_activity_father');
      $data['list']=array();

    }
    if($this->input->get('test')){
      echo '<pre>';print_r($data);
    }
    $data['time2']=strtotime($this->input->get('time2'));

    $this->load->view('busselladmin/children_activity_log',$data);
  }

  //上架活动
  public function up_activity($act_id){
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_children');
    $user_id=$this->validate($rs['pid']);
    if(!$user_id){
      return false;
    }
    $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'1'),$table='v_activity_children');
    redirect(base_url("busselladmin/children_activity_log/{$_SESSION['zid']}"));
  }
  //下架活动
  public function down_activity($act_id){
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_children');
    $user_id=$this->validate($rs['pid']);
   // echo $user_id;exit();
    if(!$user_id){
      return false;
    }
    $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_activity_children');
    redirect(base_url("busselladmin/children_activity_log/{$_SESSION['zid']}"));
  }
  public function delact($act_id)
  {
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_children');
    $user_id=$this->validate($rs['pid']);
    // echo $user_id;exit();
    if(!$user_id){
      return false;
    }
    $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'4'),$table='v_activity_children');
    redirect(base_url("busselladmin/children_activity_log/{$_SESSION['zid']}"));
  }

  public function cor_edit(){
    $data['act_id']=$act_id=$this->input->get('act_id',true);
    $data['list']=$this->User_model->get_select_one($select='user_id,cor_name,mobile,email,logo_image,act_status',array('act_id'=>$act_id),$table='v_activity_father');
    // $data=array();
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    $this->load->view('busselladmin/cor_edit',$data);
  }
  //用户用后台企业提交
  public function cor_sub(){
    // $this->size_validate('logo',40960,2);
    $cor_name=trim($this->input->post('cor_name',TRUE));
    $mobile=trim($this->input->post('mobile',TRUE));
    $email=trim($this->input->post('email',TRUE));
    $act_id=trim($this->input->post('act_id',TRUE));
   // $user_id=trim($this->input->post('user_id',TRUE));
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
    }else{
      return false;
    }
    $no_cerusers=$this->get_nocer();


    $data=array('cor_name'=>$cor_name,'mobile'=>$mobile,'email'=>$email,'act_status'=>'1','user_id'=>$user_id);

    $row=$this->User_model->get_select_one('logo_image,user_id,is_temp,pid',array('act_id'=>$act_id),'v_activity_father');
    if($_FILES['logo']['error']==0){
      $logo_image=$this->upload_image('logo','bus_act'.$act_id,'logo');
      $logo_image=$this->thumb($logo_image,'logo',$act_id.'logo',$width='100',$height='100');
      $data['logo_image']=$logo_image;
    }else{
      $data['logo_image']=$row['logo_image'];
      //$data['id_view_image_thumb']=$row['id_view_image_thumb'];
    }
    if(in_array($user_id,$no_cerusers)){
      $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
    }else{
      //若该条记录为临时记录，则直接修改
      if($row['is_temp']==1)
      {
        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
      }
      else
      {
        //若该条记录为正式记录,则检查是否存在临时记录
        $tmarr=$this->User_model->get_select_one('act_tf_id',array('act_vf_id'=>$act_id),'v_temp_real');
        if($tmarr!==0){
          $act_tf_id=$tmarr['act_tf_id'];
          $this->User_model->update_one(array('act_id'=>$act_tf_id),$data,$table='v_activity_father');
        }else{
          $data['is_temp']='1';
          $data['pid']=$row['pid'];
          $data['special']='1';
          $data['act_status']='1';
          $data['add_time']=time();
          $act_tf_id=$this->User_model->user_insert($table='v_activity_father',$data);
          $this->User_model->user_insert($table='v_temp_real',array('act_tf_id'=>$act_tf_id,'act_vf_id'=>$act_id));
        }

      }
    }
    redirect(base_url("busselladmin/children_activity_log/$_SESSION[zid]"));
  }



  public function cor_del(){
    $act_id=$this->input->get('act_id',true);
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
    }else{
      redirect(base_url("busselladmin/login"));
    }
    $rs=$this->User_model->get_select_one('pid,user_id',array('act_id'=>$act_id),'v_activity_father');
    $pid=$rs['pid'];

    if($rs['user_id']!=$user_id){
      unset($_SESSION['bus_user_id']);
      redirect(base_url("busselladmin/login"));
    }
    $act_status=$this->input->get('act_status',true);
    $is_show=$this->input->get('is_show',true);
    $type=$this->input->get('type',true);
    if($type==1){
      $data=array('is_show'=>'1');
    }else{
      $data=array('is_show'=>'2');
    }
    $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
    redirect(base_url("busselladmin/children_activity_log/{$pid}?act_status={$act_status}&is_show={$is_show}"));
  }


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

  //裁剪图方法
  function imagecropper($source_path='./public/images/1265/id_driver.jpg',$key1='test',$key2='time',$target_width='100', $target_height='100')
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


  function imagecropper_test($source_path='./public/images/1265/id_driver.jpg',$key1='test',$key2='time',$target_width='100', $target_height='100')
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

    // $white = imagecolorallocate($target_image,255,255,255);
    //  imagefilledrectangle($target_image,0,0,$target_width,$target_height,$white);
    // imagecolortransparent($target_image,$white);


    // header('Content-Type: image/jpeg');
    $type=pathinfo($source_path,PATHINFO_EXTENSION);
    if($key2=='time'){
      $key2=time();
    }
    $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
    imagejpeg($target_image,$new_image);


    imagedestroy($source_image);
    imagedestroy($target_image);
    imagedestroy($cropped_image);
    echo $new_image;
  }



  //缩略图
  public function thumb($url,$key1,$key2='time',$width='702',$height='300'){
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
    $arr['height']   = $height;

    $this->image_lib->initialize($arr);

    if($this->image_lib->resize()){
      return  $arr['new_image'];
    }
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
  public function get_lan_user(){
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $lang = $matches[1];
    return $lang;
  }
  public function get_user_id(){
    if(isset($_SESSION['bus_user_id']))
    {
      return $_SESSION['bus_user_id'];
    }else{
      redirect('busselladmin/login');
    }
  }

  public function validate($act_id){
    if(isset($_SESSION['bus_user_id']))
    {
      $user_id=$_SESSION['bus_user_id'];
    }else{
      redirect('busselladmin/login');
    }
    $rs=$this->User_model->get_select_one('user_id',array('act_id'=>$act_id),'v_activity_father');
   //echo $rs['user_id'];exit();
    if($rs['user_id']!=$user_id){
      return false;
    }else{
      return $user_id;
    }
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

    list($src_w,$src_h)=getimagesize($source_path); // 获取原图尺寸
    $dst_scale = $target_height/$target_width; //目标图像长宽比
    $src_scale = $src_h/$src_w; // 原图长宽比
    if($src_scale>=$dst_scale)
    {
      $w = intval($src_w);
      $h = intval($dst_scale*$w);
      $x = 0;
      $y = ($src_h - $h)/2;
    }
    else
    {
      $h = intval($src_h);
      $w = intval($h/$dst_scale);
      $x = ($src_w - $w)/2;
      $y = 0;
    }
    $source=imagecreatefromjpeg($source_path);
    $croped=imagecreatetruecolor($w, $h);
    imagecopy($croped,$source,0,0,$x,$y,$src_w,$src_h);
    $scale = $target_width/$w;
    $target = imagecreatetruecolor($target_width, $target_height);
    $final_w = intval($w*$scale);
    $final_h = intval($h*$scale);
    imagecopyresampled($target,$croped,0,0,0,0,$final_w,$final_h,$w,$h);

    if($key2=='time'){
      $key2=time();
    }
    $type=pathinfo($source_path,PATHINFO_EXTENSION);
    $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
    //$timestamp = time();
    imagejpeg($target, $new_image);

    imagedestroy($target);

    return $new_image;

  }

  public function wx_shop_list_for_user($page=1){
    $user_id=$this->get_user_id();
    $where=" user_id=$user_id AND (is_show='1' OR is_show='2')";
    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_wx_business');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list']=$this->User_model->get_select_more($select='business_id,user_id,business_name,star_num,discount,tag,logo_image_thumb AS logo_image,is_show',
        $where,$start,$page_num,$order_title='displayorder',$order='ASC',$table='v_wx_business');
    if($data['list']!==0){
      foreach($data['list'] as $k=>$v){
        $data['list'][$k]['tag_arr']=explode(',',$v['tag']);
      }
      if(count($data['list'])>=1){
        $data['noadd']='1';
      }
    }
    if($this->input->get('test',true)){
      echo '<pre>';print_r($data);exit();
    }
    $data['for_user']=1;

    $this->load->view('bussell/shop_list_admin_foruser',$data);
  }
//商户自用后台添加商铺
  public function add_wx_shop(){
    $data['for_user']=1;
    $user_id=$this->get_user_id();
    $row=$this->User_model->get_select_one('business_id',array('user_id'=>$user_id),'v_wx_business');
    if($row!==0){
      return false;
    }

    $this->load->view('bussell/add_shop_foruser',$data);
  }
  public function edit_wx_shop(){
    $business_id=$this->input->get('business_id',true);
    $data=$this->User_model->get_select_one('business_id,user_id,business_name,star_num,tag,lng,lat,discount,
    business_info,business_address,business_tel,logo_image_thumb AS image',array('business_id'=>$business_id),'v_wx_business');

    $this->load->view('bussell/add_shop_foruser',$data);
  }
  public function shop_sub(){
    $data['user_id']=$this->get_user_id();

    $business_id=trim($this->input->post('business_id',true));
    $row=$this->User_model->get_select_one('logo_image,logo_image_thumb,user_id',array('business_id'=>$business_id),'v_wx_business');
    if($row['user_id']!=$data['user_id']){
      return false;
    }
    $data['business_name']=trim($this->input->post('business_name',true));
    $data['star_num']=trim($this->input->post('star',true));
    $tag=trim($this->input->post('tag',true));
    $data['tag']=str_replace('，',',',$tag);
    $data['discount']=trim($this->input->post('discount',true));
    if($data['discount']>10 OR $data['discount']<=0){
      $data['discount']=10;
    }

    $data['business_info']=$this->input->post('business_info',true);
    $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

    $data['business_address']=$this->input->post('address',true);
    $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

    $data['business_tel']=$this->input->post('business_tel',true);


    if(isset($_FILES['file1'])){
      if($_FILES['file1']['error']==0){
        $logo_image=$this->upload_image('file1', $data['user_id']);
        $data['logo_image']=$logo_image;
        //imagecropper($source_path='./public/images/1265/id_driver.jpg',$key1='test',$key2='time',$target_width='100', $target_height='100')
        $data['logo_image_thumb']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');

      }else{

        $data['logo_image']=$row['logo_image'];
        $data['logo_image_thumb']=$row['logo_image_thumb'];
      }
    }
    $this->User_model->update_one(array('business_id'=>$business_id),$data,$table='v_wx_business');
    redirect("busselladmin/wx_shop_list_for_user");
  }

  public function shop_insert(){
    $data['user_id']=$this->get_user_id();
    $data['business_name']=trim($this->input->post('business_name',true));
    $data['star_num']=trim($this->input->post('star',true));
    $tag=trim($this->input->post('tag',true));
    $data['tag']=str_replace('，',',',$tag);
    $location=trim($this->input->post('location',true));
    $location=str_replace('，',',',$location);
    $location= explode(',',$location);
    $data['lng']=$location[1];
    $data['lat']=$location[0];
   // $data['discount']=$this->input->post('discount',true);

    $data['discount']=trim($this->input->post('discount',true));
    if($data['discount']>10 OR $data['discount']<=0){
      $data['discount']=10;
    }
    $data['business_info']=$this->input->post('business_info',true);
    $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

    $data['business_address']=$this->input->post('address',true);
    $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

    $data['business_tel']=$this->input->post('business_tel',true);

    $logo_image=$this->upload_image('file1', $data['user_id']);

    $data['logo_image']=$logo_image;
    // thumb($url,$key1,$key2='time',$width='702',$height='300')

    $data['logo_image_thumb']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
    $this->User_model->user_insert($table='v_wx_business',$data);
    redirect("busselladmin/wx_shop_list_for_user");
  }


  public function show_shop_admin(){
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
    }else{
      redirect(base_url('busselladmin/login'));
    }


    //$business_id=$this->input->get('business_id',true);
    $data['business']=$this->User_model->get_select_one('business_id,user_id,business_name,lat,lng,is_show,
    star_num,discount,tag,business_tel,logo_image_thumb AS logo_image,business_info,business_address',array('user_id'=>$user_id),'v_wx_business');
    $data['business']['tag_arr']=explode(',', $data['business']['tag']);
    //$data=$this->input->get_select_one();
    $lat= $data['business']['lat'];
    $lng= $data['business']['lng'];
    //getAround($lat,$lon,$raidus)
    $location=$this->getAround($lat,$lng,'400000');

    $data['shopper_video']=$this->User_model->get_wx_bus_video_by_user_id($data['business']['user_id']);
    foreach($data['shopper_video'] as $k=>$v){
      if($v['is_off']==0){
        $data['shopper_video'][$k]['path']= $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
      }else{
        $data['shopper_video'][$k]['path']="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
      }

    }
    $arr_len=count($data['shopper_video']);
    if($arr_len>=10){
      $data['video_nearby']=array();
    }else{
      $num=10-$arr_len;
      $data['video_nearby']=$this->User_model->get_wx_bus_video_by_location($location,$start=0,$num);

      foreach($data['video_nearby'] as $k=>$v){
        if($v['is_off']==0){
          $data['video_nearby'][$k]['path']= $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
        }else{
          $data['video_nearby'][$k]['path']="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
        }
      }
    }
    if($this->input->get('test')){
      //echo $this->db->last_query();
      echo '<pre>';print_r($data);exit;
    }
    $this->load->view('bussell/show_shop_foruser',$data);
  }
  public function get_nocer(){
    $data=$this->User_model->get_select_all($select='user_id',
        $where='1=1',$order_title='user_id',
        $order='ASC',$table='v_nocer_users');
    $arr=array();
    foreach($data as $k=>$v){
      $arr[]=$v['user_id'];
    }
    $arr=array_unique($arr);
    return $arr;
    // echo '<pre>';
    // print_r($arr);
  }

public function print_nocer(){
  $data=$this->User_model->get_select_all($select='user_id',
      $where='1=1',$order_title='user_id',
      $order='ASC',$table='v_nocer_users');
  $arr=array();
  foreach($data as $k=>$v){
    $arr[]=$v['user_id'];
  }
  $arr=array_unique($arr);
  print_r($arr);
}
  //下架店铺
  public function down_shop(){
    $business_id=$this->input->post('business_id',true);
    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
      $rs=$this->User_model->get_select_one('user_id',array('business_id'=>$business_id),'v_wx_business');
      if($user_id==$rs['user_id']){
        $this->User_model->update_one(array('business_id'=>$business_id),array('is_show'=>'2','displayorder'=>9200),$table='v_wx_business');
        echo 1;
      }
    }else{
      return false;
    }
  }

  public function wx_shop_show(){
    $business_id=$this->input->post('business_id');

    if(isset($_SESSION['bus_user_id'])){
      $user_id=$_SESSION['bus_user_id'];
      $rs=$this->User_model->get_select_one('user_id',array('business_id'=>$business_id),'v_wx_business');
      if($user_id==$rs['user_id']){
        $this->User_model->update_one(array('business_id'=>$business_id),array('is_show'=>'1','displayorder'=>9200),$table='v_wx_business');
        echo 1;
      }
    }else{
      return false;
    }
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
}