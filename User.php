 <?php
/**
 * 用户
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->table = 'v_users';
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
    $this->load->model('User_model');
    $this->load->model('Admin_model');
    $this->load->helper('url');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    $this->load->library('session');
    $this->load->helper('cookie');
    $this->load->library('image_lib');
    $this->load->library('common');

  }



  /*
   *提现后台审核
   */
  public function cash_log_list($page=1){

    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',TRUE));
    $data['time1']=strtotime($this->input->get('time1',TRUE));
    $data['time2']=strtotime($this->input->get('time2',TRUE));
    $data['type']=trim($this->input->get('type'));
    $cash_status= $data['cash_status']= $this->input->get('cash_status',TRUE);
    if(!$cash_status){
      $cash_status='1';
    }
    $where="cash_in_out='2'";

    if($data['time1']){
      $where.=" AND cash_adv_time >=$data[time1]";
    }
    if($data['time2']){
      $data['time2']+=86400;
      $where.="  AND cash_adv_time <=$data[time2]";
    }

    if($data['title'])
    {
      $where.= " AND user_name LIKE '%$data[title]%' OR user_id LIKE '%$data[title]%'";
    }else{
      $data['type']=0;
    }
    $where.="  AND cash_status= '$cash_status'";


    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_cash_count($where,'v_cash_log');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $select='log_id,v_cash_log.user_id as user_id,cash_num,cash_time,cash_adv_time,cash_status,cash_in_out,
    user_name,cash_style,card_no,bank_name,bank_name_ext,bank_user_name,alipay_id,
alipay_user_name,wechat,amount,canamount';
    $data['list']=$this->User_model->cash_log($select, $where, $start, $page_num, 'cash_adv_time', 'ASC');
    $data['time2']=strtotime($this->input->get('time2'));
    //echo "<pre>";print_r($data);exit();
    $this->load->view('newadmin/cash_log',$data);
  }
  //提现log 删除
  public function cash_log_del(){
    $cash_status=$this->input->get('cash_status',TRUE);
    $log_id=$this->input->get('log_id',TRUE);
    $this->User_model->update_one(array('log_id'=>$log_id),$data=array('cash_status'=>'4'),$table='v_cash_log');
    redirect(base_url("user/cash_log_list?cash_status=$cash_status"));
  }
  /*
   * 提现审核
   *
   */
  public function cash_ok_no(){
    $mes=$this->input->post('mes',TRUE);
    $user_id=$this->input->post('user_id',TRUE);
    $log_id=$this->input->post('log_id',TRUE);
    $cash_num=$this->input->post('cash_num',TRUE);
    if($mes==2){
      $data1=array(
          'cash_status'=>'2',
          'cash_time'=>time(),
      );
      $where1=array('log_id'=>$log_id);
      $where2=array('user_id'=>$user_id);
      $row=$this->User_model->get_select_one('amount',$where2);
      //echo $row['amount'];exit();
      $amount=$row['amount']-$cash_num;
      $data2=array('amount'=>$amount);
      $this->User_model->update_trans_cash_log_and_amount($where1,$data1,'v_cash_log',$where2,$data2,'v_auth_users');
     // $lan=$this->getlan($user_id);

      $this->put_admin_log("批准提现 提现编号：$log_id 提现金额：$cash_num 提现用户id：$user_id");
      $this->new_lan_bydb($user_id);
      $info=$this->lang->line('sys_txs');
      $this->push_sys($user_id,$info);
      echo 2;
    }elseif($mes==3){
      $data=array(
          'cash_status'=>'3',
          'cash_time'=>time(),
      );
      $where=array('log_id'=>$log_id);
      if($this->User_model->update_one($where,$data,'v_cash_log')){
        $this->User_model->amount_update('canamount','canamount+'.$cash_num,array('user_id'=>$user_id),'v_auth_users');
        $this->put_admin_log("否决提现 提现编号：$log_id 提现金额：$cash_num 提现用户id：$user_id");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_txf');
        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }

  /*
   * 前台活动展示
   */
  public function activity_app(){
    $data['count_url']=$this->count_url;

    if(!$this->input->get('image')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){

       //echo '<pre>';
       // print_r($_SERVER);
       // print_r($_COOKIE);exit();
        return FALSE;
      }
      $data['user_id']=$user_id;
    }else{
      $data['user_id']=$user_id=1;
    }

    //$select='id,image,title';
   // $data['shop_list']=$this->User_model->get_select_all($select,array('STATUS'=>'1'),'displayorder','ASC','v_activity_terms');
    $select='act_id,act_image,title,image,is_set,special';
   // $data['list']=$this->User_model->get_select_all($select,array('is_show'=>'1','pid'=>'0','is_temp'=>'0','special'=>'0'),'displayorder','ASC','v_activity_father');
    //if($user_id==2011 || $user_id=='1889' || $user_id=='1744' ||$user_id=='1085' ||$user_id=='2025' ||$user_id=='1077'||$user_id=='1742'||$user_id=='1020'|| $user_id=='1093'|| $user_id=='1032' || $user_id=='1026' || $user_id=='1055' ){
    //$data['list']=$this->User_model->get_select_all($select,array('pid'=>'0','is_show'=>'1','is_temp'=>'0'),'displayorder','ASC','v_activity_father');
   // }
    $data['list']=$this->User_model->get_select_all($select,array('pid'=>'0','is_show'=>'1','is_temp'=>'0'),'displayorder','ASC','v_activity_father');

//    $data['shop_list']=$this->User_model->get_select_all($select,array('is_show'=>'1'),'displayorder','ASC','v_activity_bus');

//
    if($this->input->get('image')){
      //echo "<pre>";print_r($data);exit();
    }

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auth/activity_an',$data);
    }else{

      //print_r($_SERVER['HTTP_USER_AGENT']);exit();
      $this->load->view('auth/activity',$data);
    }
  }

 /* public function activity_bus_app(){
    $data['count_url']=$this->count_url;
   $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $data['user_id']=$user_id;
    $rs=$this->User_model->get_select_one('is_merchant',array('user_id'=>$user_id),'v_users');
    $data['is_bus']=$rs['is_merchant'];
    $select='act_id,act_image,title';
    $data['list']=$this->User_model->get_select_all($select,array('is_show'=>'1'),'displayorder','ASC','v_activity_bus');

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
     if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auth/bus_activity',$data);
    }else{
      $this->load->view('auth/bus_activity_ios',$data);
    }


  }*/
  /*
   * 点赞一次
   */
  public function activity_pra_on(){
    $video_id=$this->input->post('video_id',TRUE);
    //点赞者id
    //$user_id=$this->input->post('user_id',TRUE);
    $_SESSION['pra'][]=$video_id;
    $where=array('video_id'=>$video_id);
    $this->User_model->praise_plus($where);
    echo 1;
  }

  public function bus_activity_pra_on(){
    $video_id=$this->input->post('video_id',TRUE);
    //点赞者id
    //$user_id=$this->input->post('user_id',TRUE);
    $_SESSION['pra'][]=$video_id;
    $where=array('video_id'=>$video_id);
    $this->User_model->praise_plus($where);
    echo 1;
  }
/*
删除通过审核的身份
 */
  public function auth_del($table,$user_id){
    //$user_id=$this->input->get('user_id',TRUE);
    $where=array('user_id'=>$user_id,'is_temp'=>'0');
    $this->User_model->del($where,$table);
    $where=array('user_id'=>$user_id);
    if($table=='v_auth_views'){
      $this->User_model->update_one($where,array('is_guide'=>'0'),'v_users');
      $count=  $this->User_model->get_count("user_id=$user_id AND is_guide='0' AND is_driver='0' AND is_attendant='0' AND is_merchant ='0'", 'v_users');
      if($count['count']==1){
        $this->User_model->update_one($where,array('auth'=>'0'),'v_users');
      }
      $this->put_admin_log("删除导游 用户id{$user_id}");
      redirect(base_url('newadmin/guide_list'));

    }elseif($table=='v_auth_drivers'){
      $this->User_model->update_one($where,array('is_driver'=>'0'),'v_users');
      $count=  $this->User_model->get_count("user_id=$user_id AND is_guide='0' AND is_driver='0' AND is_attendant='0' AND is_merchant ='0'", 'v_users');
      if($count['count']==1){
        $this->User_model->update_one($where,array('auth'=>'0'),'v_users');
      }
      $this->put_admin_log("删除司机 用户id{$user_id}");
      redirect(base_url('newadmin/drivers_list'));

    }elseif($table=='v_auth_locals'){
      $this->User_model->update_one($where,array('is_attendant'=>'0'),'v_users');
      $count=  $this->User_model->get_count("user_id=$user_id AND is_guide='0' AND is_driver='0' AND is_attendant='0' AND is_merchant ='0'", 'v_users');
      if($count['count']==1){
        $this->User_model->update_one($where,array('auth'=>'0'),'v_users');
      }
      $this->put_admin_log("删除地陪 用户id{$user_id}");
      redirect(base_url('newadmin/local_list'));

    }elseif($table=='v_auth_business'){
      $this->User_model->update_one($where,array('is_merchant'=>'0'),'v_users');
      $count=  $this->User_model->get_count("user_id=$user_id AND is_guide='0' AND is_driver='0' AND is_attendant='0' AND is_merchant ='0'", 'v_users');
      if($count['count']==1){
        $this->User_model->update_one($where,array('auth'=>'0'),'v_users');
      }
      $this->put_admin_log("删除商户 用户id{$user_id}");
      redirect(base_url('newadmin/business_list'));

    }
  }
/*
删除身份认证申请
 */
  public function temp_auth_del($table,$user_id){
   // $user_id=$this->input->get('user_id',TRUE);
    $where=array('user_id'=>$user_id,'is_temp'=>'1');
    $this->User_model->del($where,$table);

    if($table=='v_auth_views'){
      $this->put_admin_log("删除导游申请 用户id{$user_id}");
      redirect(base_url('newadmin/guide_list'));
    }elseif($table=='v_auth_drivers'){
      $this->put_admin_log("删除司机申请 用户id{$user_id}");
      redirect(base_url('newadmin/driver_list'));
    }elseif($table=='v_auth_locals'){
      $this->put_admin_log("删除地陪申请 用户id{$user_id}");
      redirect(base_url('newadmin/locals_list'));
    }elseif($table=='v_auth_business'){
      $this->put_admin_log("删除商户申请 用户id{$user_id}");
      redirect(base_url('newadmin/business_list'));
    }

  }
//app 用户收支记录
  public function cash_history(){
   $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $data['user_id']=$user_id;
    $where="v_cash_log.user_id='$user_id' AND v_cash_log.cash_status='2'";
    $data['list']=$this->User_model->get_select_all('v_cash_log.cash_num,v_cash_log.cash_in_out,v_cash_log.cash_time,v_auth_users.cash_style',
       $where,'cash_time','DESC', 'v_cash_log','1',
       'v_auth_users','v_cash_log.user_id=v_auth_users.user_id');
     //$this->p($data);
    //echo  $this->db->last_query();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('account/history',$data);
  }
  /*
   * 后台收支记录
   */
  public function cash_history_admin(){
    $data['user_id']=$user_id=$this->input->get('user_id',TRUE);
    $where="v_cash_log.user_id='$user_id' AND v_cash_log.cash_status='2'";
    $data['list']=$this->User_model->get_select_all('v_cash_log.cash_num,v_cash_log.cash_in_out,v_cash_log.cash_time,v_auth_users.cash_style',
        $where,'cash_time','DESC', 'v_cash_log','1',
        'v_auth_users','v_cash_log.user_id=v_auth_users.user_id');
    //$this->p($data);
    //echo  $this->db->last_query();
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('account/history',$data);
  }

   /*
    *
    */
  public function cash_edit()
  {
    $user_id=$this->user_id_and_open_id();
  if(!$user_id){
    return FALSE;
  }
    $data['user_id']=$user_id;
    $where="v_cash_log.user_id='$user_id' AND v_cash_log.cash_status='2'";
    $data['list']=$this->User_model->get_select_all('v_cash_log.log_id,v_cash_log.cash_num,v_cash_log.cash_in_out,v_cash_log.cash_time,v_auth_users.cash_style',
        $where,'cash_time','DESC', 'v_cash_log','1',
        'v_auth_users','v_cash_log.user_id=v_auth_users.user_id');
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('account/edit',$data);
  }

  public function cash_app_del(){
    $logarr=$this->input->post('log_id');
    $logarr=explode(',',$logarr);
    foreach($logarr as $k=>$v){
      $this->User_model->update_one(array('log_id'=>$v),array('cash_status'=>'4'),'v_cash_log');
    }

    echo 1;
  }
  /*
   * 申请页面展现
   */

  public function activiy_app_adv(){

    $data['count_url']=$this->count_url;
    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
      $user_id=1;
    }

    $_SESSION['user_id']=$user_id;
    $where=array('user_id'=>$user_id);
    $data=$this->User_model->get_select_one('user_name,user_id',$where,'v_users');
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone') ){
      $this->load->view('auth/apply_activity_ios',$data);
    }else{
      $this->load->view('auth/apply_activity',$data);
    }

  }
  /*
   * 活动细节展现，点赞只可一次限制
   */
  public function activiy_app_detail($act_id,$page=1,$sess=0){
    $data['count_url']=$this->count_url;
    $this->get_crop_for_video();
    if($act_id==0){
      return FALSE;
    }
    $act_count=$this->User_model->get_count("act_id=$act_id AND is_show='1'", 'v_activity_father');
    if($act_count['count']==0){
     // return FALSE;
    }

   $data['menu']='0';
   $data['per_user_id']=0;
    $data['down']="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
    $data['app_session']=$sess;
    if(isset($_COOKIE['olook'])){
      $arr_olook=explode('-',$_COOKIE['olook']);
      $data['menu']=$arr_olook[3];
      $data['per_user_id']=$arr_olook[0];

    }
    if(isset($_COOKIE['user_id'])){
      $data['per_user_id']=$_COOKIE['user_id'];

    }
    if(isset($_COOKIE['menu'])){
      $data['menu']=$_COOKIE['menu'];
      unset($_COOKIE['menu']);
    }

    $share_user_id=$this->input->get('share_user_id',true);
    if(!$share_user_id){
      $share_user_id=$data['per_user_id'];
    }
    $data['bool_live']=FALSE;
    $temp_data=$this->User_model->get_select_one('is_merchant',"user_id=$data[per_user_id]",'v_users');
    if($temp_data['is_merchant']==1){
      $data['bool_live']=TRUE;
    }else{
      if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no'){
        $location=$_COOKIE['location'];
        $location=explode(',',$location);
        $w=$location[0];$j=$location[1];
        $data['bool_live']=$this->can_live($w,$j);
       // unset($_COOKIE['location']);
      }else{
        $arr_city_country=0;
      }
      if(isset($_COOKIE['olook'])){
        $iosarr=explode('-',$_COOKIE['olook']);
        if(isset($iosarr[2])){
          if($iosarr[2]!='no,no'){
            $str=$iosarr[2];
            $location=explode(',',$str);
            $w=$location[0];$j=$location[1];

            $data['bool_live']=$this->can_live($w,$j);
          }
        }
      }
    }
    if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no')
    {
      $location=$_COOKIE['location'];
      $location=explode(',',$location);
      $w=$location[0];$j=$location[1];
      $arr_city_country=$this->get_city_country($w,$j);
    }
    else
    {
      $arr_city_country=0;
    }

    if(isset($_SESSION['pra']))
    {
      $pra=array_unique($_SESSION['pra']);
      $pra=array_values($pra);
      //组成点赞过的视频id json数据 便于前台js遍历
      $data['pra']=json_encode($pra);
    }
    $count=$this->User_model->get_act_video_count("act_id=$act_id AND is_off<2 ", 'v_video');

    $data['count']= $count['count'];
    $page_num =10;
    $data['now_page'] = $page;
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page']) {$page=1;}
    $start = ($page-1)*$page_num;

    $select="act_id,title,act_image,image,start_time,end_time,content,poster_image,request";
    $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_father');
   if(mb_strlen($data['activity']['title'])>11){
      $data['activity']['title']=mb_substr($data['activity']['title'],0,11).'……';
    }
    $data['act_id']=$act_id;
    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.imageforh5 as image,v_video.praise as praise,title,v_video.push_type,
    v_video.user_id,views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,v_users.image as avatar,is_off,v_video.location,v_video.socket_info";
    $left_title='v_video.user_id=v_users.user_id';
    $left_table='v_users';
    $data['list']=$this->User_model->get_act_video_all($select,"act_id='$act_id' AND is_off<2  ",'start_time', 'DESC','v_video',1,$left_table,$left_title,FALSE,1,$start,$page_num);
    if(!empty($data['list']))
    {
      foreach($data['list'] as $k => $v){
        if($v['is_off']==1)
        {

          if($v['push_type']==0)
          {
            $data['list'][$k]['path']= $this->config->item('record_url').$v['video_name'].'.m3u8';
          }
          else
          {
            $data['list'][$k]['path']= $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
          }

          //h5 用
          if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===FALSE)
          {
              $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          }

          $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
        }
        else
        {
          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
          //h5用
          if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===FALSE)
          {
            $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
          }
          $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
      }
    }

    if(!$data['list'])
    {
      $data['list']=0;
    }
      if($arr_city_country!=0)
      {
        $data['liver_info']['country']=$arr_city_country['0'];
        $data['liver_info']['city']=$arr_city_country['1'];
        $data['liver_info']['act_id']=$act_id;
        $data['liver_info']['act_shop_id']='0';
        $data['liver_info']=json_encode($data['liver_info']);

      }
      else
      {
        $data['liver_info']='0';
      }
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css))
    {
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $data['share']['share_url']=base_url("user/activiy_app_detail/$act_id?share_user_id=$share_user_id");
    $data['share']['title']=$data['activity']['title'];
    $data['share']['image']=$data['activity']['act_image'];
    $sharetitle=$data['activity']['title'];
    $data['share']['desc']="坐享其成上的一个精彩活动{{$sharetitle}}快来一起High。";
    $data['json_share']=json_encode($data['share']);
    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney'))
    {
      if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
      {
       // if( $data['per_user_id']==2011){
        //$data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
          //$data['activity']['content']=str_ireplace("olooknewweb://api.etjourney","http://api.etjourney",$data['activity']['content']);
        //$data['activity']['content']=str_ireplace("https","olooknewwebs",$data['activity']['content']);
         // $this->load->view('auth/activity_detail_test',$data);
       // }else{
        $this->load->view('auth/activity_detail_ios',$data);
       // }

      }
      else
      {
        if(!$this->input->get('menu',TRUE))
        {
          $data['menu']='menu';
        }
        //if($data['per_user_id']==1744){ echo '<pre>';print_r($data);exit();}
        //$data['activity']['content']=str_ireplace("http","olooknewweb",$data['activity']['content']);
        //$data['activity']['content']=str_ireplace("olooknewweb://api.etjourney","http://api.etjourney",$data['activity']['content']);
        //$data['activity']['content']=str_ireplace("https","olooknewwebs",$data['activity']['content']);
        $this->load->view('auth/activity_detail',$data);
      }
    }
    else
    {
      $data['signPackage']=$this->wx_js_para(1);
      $data['link_url']=TRUE;
      if($this->input->get('test'))
      {
       echo "<pre>";print_r($data);exit();
      }

      $this->load->view('auth/activity_detail_h5',$data);
    }


  }

  public function activity_del_one(){
    $act_id=$this->input->get('act_id');
    $where=array('act_id'=>$act_id);
    $this->User_model->del($where,'v_activity_father');
    $this->put_admin_log("官方活动删除 活动id{$act_id}");
 redirect(base_url('newadmin/activity_list'));
  }
  /*
   * 活动申请提交地址
   *
   */
  public function activity_adv_sub(){
    $user_id=trim($this->input->post('user_id',TRUE));
    $user_name=trim($this->input->post('user_name',TRUE));
    $title=trim($this->input->post('title',TRUE));
    $start_time=trim($this->input->post('start_time',TRUE));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',TRUE));
    $end_time=strtotime($end_time);
    $content=trim($this->input->post('content',TRUE));
    $mobile=trim($this->input->post('tel',TRUE));
    $email=trim($this->input->post('email',TRUE));
    $type=$this->get_lan_user();
    $act_time_adv=time();
    $data=array(
        'user_id'=>$user_id,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'mobile'=>$mobile,
        'email'=>$email,
        'act_status'=>'1',
        'lan'=>$type,
        'act_time_adv'=>$act_time_adv,
    );
    $this->User_model->user_insert('v_activity_temp',$data);
    redirect(base_url("user/activity_app"));
  }
  public function activity_adv_sub_ios(){
    $user_id=trim($this->input->post('user_id',TRUE));
    $user_name=trim($this->input->post('user_name',TRUE));
    $title=trim($this->input->post('title',TRUE));
    $start_time=trim($this->input->post('start_time',TRUE));
    $start_time=strtotime($start_time);
    $end_time=trim($this->input->post('end_time',TRUE));
    $end_time=strtotime($end_time);
    $content=trim($this->input->post('content',TRUE));
    $mobile=trim($this->input->post('tel',TRUE));
    $email=trim($this->input->post('email',TRUE));
    $type=$this->get_lan_user();
    $act_time_adv=time();
    $data=array(
        'user_id'=>$user_id,
        'user_name'=>$user_name,
        'title'=>$title,
        'start_time'=>$start_time,
        'end_time'=>$end_time,
        'content'=>$content,
        'mobile'=>$mobile,
        'email'=>$email,
        'act_status'=>'1',
        'lan'=>$type,
        'act_time_adv'=>$act_time_adv,
    );
    if( $this->User_model->user_insert('v_activity_temp',$data)){
      echo 1;
    }else{
      echo 2;
    }


  }

public function test_sql($act_id){
  $select="v_video.video_id,v_video.address,v_video.video_name,v_video.image as image,v_video.praise as praise,title,
    v_video.user_id,views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
  $left_title='v_video.user_id=v_users.user_id';
  $left_table='v_users';
  $data['count']=$this->User_model->get_act_video_count("act_id='$act_id' AND is_off<=2 AND (v_video.stop_time - v_video.start_time >60) ",'v_video');
  $data['list']=$this->User_model->get_act_video_all($select,"act_id='$act_id' AND is_off<=2 AND (v_video.stop_time - v_video.start_time >60) ",'start_time', 'DESC','v_video',1,$left_table,$left_title,FALSE);
  echo "<pre>";
  print_r($data);
  exit();

}
  /*
   * 后台活动添加页面
   */

  public function activity_add_adv(){


    $this->load->view('activity/activity_add_adv');
  }
  public function size_up($name){
    $file_size=$_FILES[$name]['size'];
    if ($file_size>51200){
      echo "请让图片小于50k";
      exit();
    }
  }
  /*
   * 后台活动添加操作
   */

  public function activity_add(){
    $this->size_up('act_image');
    $this->size_up('poster_image');
    $title=$this->input->post('title',TRUE);
    $start_time=$this->input->post('start_time',TRUE);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',TRUE);
    $end_time=strtotime($end_time);
    $content=$this->input->post('content',false);
    $request=$this->input->post('request',TRUE);
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
    redirect(base_url('newadmin/activity_list'));
  }

  /*
   * 后台活动修改
   */
  public function activity_edit_adv(){
    $act_id=$this->input->get('act_id',TRUE);
    $where=array('act_id'=>$act_id);
    $data=$this->User_model->get_select_one('act_id,title,act_image,poster_image,start_time,end_time,content,is_show,request',$where,'v_activity_father');
   // echo "<pre>";print_r($data);exit();
    $this->load->view('activity/activity_add_adv',$data);
  }

  /*
   * 后台提交
   */
  public function activity_sub(){
    $act_id=$this->input->post('act_id',TRUE);
    $where=array('act_id'=>$act_id);
    $title=$this->input->post('title',TRUE);
    $start_time=$this->input->post('start_time',TRUE);
    $start_time=strtotime($start_time);
    $end_time=$this->input->post('end_time',TRUE);
    $end_time=strtotime($end_time);
    $request=$this->input->post('request',TRUE);
    $content=$this->input->post('content',FALSE);
    $this->size_up('act_image');
  $this->size_up('poster_image');

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
    redirect(base_url("user/activity_edit_adv?act_id={$act_id}"));
  }

  /*
   * 活动申请记录
   */
  public function activity_log($page=1){
    $data['title'] =$this->input->post_get('title',TRUE);
    if($data['title'])
    {
      $where = "title LIKE '%$data[title]%' OR user_id LIKE '%$data[title]%' OR user_name LIKE '%$data[title]%'";
    }
    else
    {
      $where  = "1 = 1" ;
    }
    $page_num =10;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_activity_temp');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $select='act_id,user_id,user_name,title,start_time,end_time,content,mobile,email,act_status,act_time_adv';
    $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'act_time_adv','DESC','v_activity_temp');
    $this->load->view('activity/activity_log',$data);
  }
  /*
   * 商户活动展示
   */
  public function bus_activity_home(){
    $select='act_id,act_image,title';
    $data['list']=$this->User_model->get_select_all($select,array('is_show'=>'1'),'start_time','DESC','v_activity_bus');
    //echo "<pre>";print_r($data);exit();
    $this->load->view('activity/activity_home',$data);

  }

  /*
   * 活动后台展示
   */

  public function activity_home(){
    $select='act_id,act_image,title';
    $data['list']=$this->User_model->get_select_all($select,array('is_show'=>'1'),'start_time','DESC');
    //echo "<pre>";print_r($data);exit();
    $this->load->view('activity/activity_home',$data);
  }
  public function bus_in(){

   // $this->load->view('admin/bus_in',$data);
  }


 

  /*
   * 活动详情
   */
  public function activity_info()
  {
    $act_id=$this->input->get('act_id',TRUE);
    $select='act_id,title,start_time,end_time,content,is_show,poster_image,request';
    $where=array('act_id'=>$act_id);
    $data['activity']=$this->User_model->get_select_one($select,$where,$table='v_activity');
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('activity/activity_info',$data);
  }


  /*
   *
   * 后台发布撤销官方活动
   */
  public function activity_ok_no()
  {
    $mes=$this->input->post('mes',TRUE);
    $act_id=$this->input->post('act_id',TRUE);
    if($mes==1){
      $data=array(
          'is_show'=>'1',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity')){
        echo 1;
      }
    }elseif($mes==2){
      $data=array(
          'is_show'=>'2',
          'act_time'=>time()
      );
      $where=array('act_id'=>$act_id);
      if($this->User_model->update_one($where,$data,'v_activity')){
        echo 2;
      }
    }
  }
  /*
   * 活动图片和活动宣传图片添加
   */

  public function activity_image_add(){
    $data['act_id']=$this->input->get('act_id',TRUE);
    $data=$this->User_model->get_select_one('act_image,poster_image,act_id',"act_id=$data[act_id]",'v_activity');
    $this->load->view('user/image_add',$data);
  }

  public function act_image_add(){
    $act_id=$this->input->post('hidden',TRUE);
    $where=array('act_id'=>$act_id);
      $this->size_up('activity1');
        $this->size_up('activity2');
    $act_image=$this->upload_image('activity1',$act_id);
    $poster_image=$this->upload_image('activity2',$act_id);
    $data=array(
        'act_image'=>$act_image,
        'poster_image'=>$poster_image

    );
    $this->User_model->update_one($where,$data);
    echo $this->db->last_query();


  }
  

/*
 * 个人活动申请记录删除
 */

  public function activity_log_del(){
    $act_id=$this->input->get('act_id',TRUE);
    $where=array('act_id'=>$act_id);
    $this->User_model->del($where,'v_activity_temp');
    $this->put_admin_log("活动申请删除 活动id{$act_id}");
    redirect(base_url('user/activity_log'));
  }
  /*
   * 导游认证
   */

  public function guide_auth(){
    $data['count_url']=$this->count_url;
    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
     // return FALSE;
      $user_id=1;
      $_SESSION['user_id']= $user_id;
      $data['type']='en';
    }
    $data['user_id']=$user_id;
    $row=$this->User_model->get_select_one('user_name,is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['user_name']=$row['user_name'];
    $data['is_guide']=$row['is_guide'];
    $data['is_attendant']=$row['is_attendant'];
    $data['is_driver']=$row['is_driver'];
    $data['is_merchant']=$row['is_merchant'];
    $data['info']=$this->User_model->get_select_one('auth_name,mobile,auth_wechat,id_style,id_num,
    id_image_thumb,id_view_status,id_view_style,id_view_num,id_view_image_thumb ',
        array('user_id'=>$user_id),'v_auth_views','1');
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    if($user_id==2946){
      echo $type;
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auther/legalize_an',$data);
    }else{
      $this->load->view('auther/legalize',$data);
    }

/*    if($user_id==1022){
      $this->load->view('auther/legalize_an',$data);
    }else{
      $this->load->view('auth/legalize',$data);
    }*/

  }


  public function driver_auth(){
    $data['count_url']=$this->count_url;
  if(!$this->input->get('test')){
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
  }else{
    $user_id=1;
    $type=1;
  }

    $data['user_id']=$user_id;
    $row=$this->User_model->get_select_one('user_name,is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['user_name']=$row['user_name'];
    $data['is_guide']=$row['is_guide'];
    $data['is_attendant']=$row['is_attendant'];
    $data['is_driver']=$row['is_driver'];
    $data['is_merchant']=$row['is_merchant'];
    $data['info']=$this->User_model->get_select_one('auth_name,mobile,auth_wechat,id_style,
    id_num,id_driver_status,id_car_num, id_driver,id_car_style,
    id_image_thumb,id_driver_image_thumb ,id_car_image_thumb ,id_car_num_image_thumb',
        array('user_id'=>$user_id), 'v_auth_drivers','1');

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auther/driver_an',$data);
    }else{
      $this->load->view('auther/driver',$data);
    }
   /* if($user_id==1022){
      $this->load->view('auther/driver_an',$data);
    }else{
      $this->load->view('auth/driver',$data);
    }*/
  }

  public function locals_auth(){
    $data['count_url']=$this->count_url;
    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
      $user_id=1022;
      $_SESSION['user_id']= $user_id;
    }

    $data['user_id']=$user_id;
    $row=$this->User_model->get_select_one('user_name,is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['user_name']=$row['user_name'];
    $data['is_guide']=$row['is_guide'];
    $data['is_attendant']=$row['is_attendant'];
    $data['is_driver']=$row['is_driver'];
    $data['is_merchant']=$row['is_merchant'];
    $data['info']=$this->User_model->get_select_one('auth_name,mobile,auth_wechat,id_style,id_num,id_image_thumb,
    id_local_status',array('user_id'=>$user_id),'v_auth_locals','1');
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();

    if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auther/locals_an',$data);
    }else{
      $this->load->view('auther/locals',$data);
    }
    /*  if($user_id==1022){
        $this->load->view('auther/locals_an',$data);
      }else{
        $this->load->view('auth/locals',$data);
      }*/
  }

  public function business_auth(){
    $data['count_url']=$this->count_url;

    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
      $user_id=1022;
      $type=1;
    }

    $data['user_id']=$user_id;
    $row=$this->User_model->get_select_one('user_name,is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['user_name']=$row['user_name'];
    $data['is_guide']=$row['is_guide'];
    $data['is_attendant']=$row['is_attendant'];
    $data['is_driver']=$row['is_driver'];
    $data['is_merchant']=$row['is_merchant'];
    $data['info']=$this->User_model->get_select_one('auth_name,mobile,auth_wechat,id_style,id_num,id_image_thumb ,shop_name,
id_business_status,id_business_image_thumb ',array('user_id'=>$user_id),'v_auth_business','1');

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $this->load->view('auther/business_an',$data);
    }else{
      $this->load->view('auther/business',$data);
    }

   /* if($user_id==1022){
      $this->load->view('auther/business_an',$data);
    }else{
      $this->load->view('auther/business',$data);
    }*/
   // $this->load->view('auth/business',$data);
  }

  public function notice(){

    $data['count_url']=$this->count_url;
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $data['user_id']=$user_id;
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('auth/notice',$data);
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


  public function new_lan_bydb($user_id){
    $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
    $lang = $rs['lan'];
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
  public function get_lan_bydb($user_id){
    $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
    return $rs['lan'];
  }
  public function lantest(){
    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    //echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $lang = $matches[1];
   echo $lang;
  }

  public function test_3(){
    echo $this->thumb('./public/images/913/id_driver.jpg',913,'id_driver');
    echo "<br>";
    echo $this->thumb('./public/images/913/id_num_driver.jpg',913,'id_num_driver');
    echo "<br>";
    echo  $this->thumb('./public/images/913/id_car_driver.jpg',913,'id_car_driver');
    echo "<br>";
    echo $this->thumb('./public/images/913/id_car_image.jpg',913,'id_car_image');
  }
  public function href(){

    $this->load->view('video/href');
  }
  /*
   *app提现审核
   */
  public function user_pay_adv(){
    $data['count_url']=$this->count_url;
   $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      //$user_id=1032;
     return FALSE;
    }
    $data['user_id']=$user_id;

    $row=$this->User_model->get_username_and_image_by_userid($user_id);
    $data['user_name']=$row['user_name'];
    $where=array('user_id'=>$user_id);
    $row=$this->User_model->get_select_one('auth_status,amount,canamount',$where);
    if($row!=0){
      $data['amount']=$row['amount'];
      $data['canamount']=$row['canamount'];
      $data['auth_status']=$row['auth_status'];
    }else{
      $data['amount']=0;
      $data['canamount']=0;

    }
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if($this->input->get('test')){
      echo '<pre>';print_r($data);
  }
    $this->load->view('account/account',$data);


  }
  public function account_type(){
    $data['count_url']=$this->count_url;
     $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      //$user_id=1;
      return FALSE;
    }
    $data['user_id']=$user_id;
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('account/account_type',$data);
  }
  /*
   * app 提现审核资料提交
   */
  public function withdraw_info_sub($style){
    $data['count_url']=$this->count_url;
    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
      $user_id=1;
    }

//
    $where=array('user_id'=>$user_id);
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if($style==1){
      $select='cashed,alipay_id,alipay_user_name,user_id,auth_status';
      $data=$this->User_model->get_select_one($select,$where);
      if($data){
        if($data['auth_status']==2){
          redirect(base_url("user/user_pay_adv"));
        }
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/alipayinfo',$data);
      }else{
        $data=array();
        $data['noiden']=1;
        $data['user_id']=$user_id;
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/alipayinfo',$data);
        //return FALSE;
      }
      //echo"<pre>"; print_r($data);exit();
    }elseif($style==2){
      $select='cashed,wechat,user_id,auth_status';
      $data=$this->User_model->get_select_one($select,$where);
      if($data){
        if($data['auth_status']==2){ redirect(base_url("user/user_pay_adv"));}
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/weixininfo',$data);
      }else{
        $data=array();
        $data['noiden']=1;
        $data['user_id']=$user_id;
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/weixininfo',$data);
      }
    }elseif($style==3){
      $select='cashed,,user_id,card_no,bank_name,bank_name_ext,bank_user_name,auth_status';
      $data=$this->User_model->get_select_one($select,$where);
      if($data){
        if($data['auth_status']==2){ redirect(base_url("user/user_pay_adv"));}
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/bankinfo',$data);
      }else{
        $data=array();
        $data['noiden']=1;
        $data['user_id']=$user_id;
        if($type=='en'){
          $data['type']='en';
        }
        $this->load->view('account/bankinfo',$data);
      }
    }
  }
  public function dowithdraw_info_sub(){
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      //$user_id=1;
      return FALSE;
    }
    $data['user_id']=$user_id;
    $cash_style=$this->input->post('cash_style');
    $where=array('user_id'=>$user_id);
    if($cash_style==1){
      $alipay_id=trim($this->input->post('alipay',TRUE));
      $alipay_user_name=trim($this->input->post('alipay_name',TRUE));
      $data=array(
          'cashed'=>'1',
          'alipay_id'=>$alipay_id,
          'alipay_user_name'=>$alipay_user_name,
          'auth_money_time'=>time(),
          'cash_style'=>$cash_style,
          'auth_status'=>'1'
      );
      // echo"<pre>"; print_r($data);exit();
      $this->User_model->update_one($where,$data,$table='v_auth_users');
      redirect(base_url("user/user_pay_adv"));
    }elseif($cash_style==2){
      $wechat=trim($this->input->post('weixin',TRUE));
      $data=array(
          'cashed'=>'1',
          'auth_money_time'=>time(),
          'cash_style'=>$cash_style,
          'auth_status'=>'1',
          'wechat'=>$wechat
      );
      $this->User_model->update_one($where,$data,$table='v_auth_users');
      redirect(base_url("user/user_pay_adv"));

    }elseif($cash_style==3){
      /*
       * 'cashed,,user_id,card_no,bank_name,bank_name_ext,bank_user_name
       */
      $card_no=trim($this->input->post('card',TRUE));
      $bank_name=trim($this->input->post('bank',TRUE));
      $bank_name_ext=trim($this->input->post('bank_name',TRUE));
      $bank_user_name=trim($this->input->post('account',TRUE));
      $data=array(
          'cashed'=>'1',
          'auth_money_time'=>time(),
          'cash_style'=>$cash_style,
          'auth_status'=>'1',
          'card_no'=>$card_no,
          'bank_name'=>$bank_name,
          'bank_name_ext'=>$bank_name_ext,
          'bank_user_name'=>$bank_user_name,
      );
      $this->User_model->update_one($where,$data,$table='v_auth_users');
      redirect(base_url("user/user_pay_adv"));

    }
  }


  public function creates_app(){
    $data['count_url']=$this->count_url;
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $data['user_id']=$user_id;
    $where=array('user_id'=>$user_id);
    $data=$this->User_model->get_select_one('credits',$where,$table='v_users');

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('user/integral',$data);
  }
  /*
   * 等级
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

  /**
   *  根据坐标点获取地理位置信息（百度接口）
   **/
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


  public function get_city($dimension, $longitude){
    $position = $this->geocoder($dimension,$longitude);
    $position = json_decode($position,TRUE);
    if($position){
      $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
      $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
      $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
      if($position['status']==0 && empty($country)){
        return array('未知');
      }else{
        return array($city);
      }
    }else{
      return array('未知');
    }
  }
/*
 *
 */
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
  public function can_live($dimension,$longitude)
  {
    $position = $this->geocoder($dimension,$longitude);
    $position = json_decode($position,TRUE);
    if($position){
    $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
    $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
    $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
    if($position['status']==0 && empty($country))
    {
       return TRUE;
    }
    else
    {
        $data['country'] = $country;
        $data['city']    = $city;
      if($this->input->get('test')){
           echo'<pre>'; print_r($country) ;exit();
        }
        if($country != '中国'){
          return TRUE;
        }else{
          if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
          {
            return TRUE;
          }else{
            return FALSE;
          }
        }
      }
    }else{
      return FALSE;
    }

  }


  public function can_live_js(){

    $dimension=$this->input->post('arr0',TRUE);
   // echo   $dimension;
    $longitude=$this->input->post('arr1',TRUE);
    $position = $this->geocoder($dimension,$longitude);
    $position = json_decode($position,TRUE);
    if($position){
      $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
      $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
      $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
      if($position['status']==0 && empty($country)){
       echo 1;
      }else{
        $data['country'] = $country;
        $data['city']    = $city;
        //echo $country;
        if($country != '中国'){

          echo 1;
        }else{
          if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
          {
            echo 1;
          }else{
            echo 0;
          }
        }
      }
    }else{
      echo 0;
    }

  }
  /*
   *地图——直播
   */
  public function map_live(){
    if($this->input->get('test')){
      $_COOKIE['video_id']='649';
    }
    $data['count_url']=$this->count_url;
    $temp=array();
    if(isset($_COOKIE['user_id'])){
      $data['user_id']=$_COOKIE['user_id'];
    }
    if(isset($_COOKIE['location'])){
      if($_COOKIE['location']!='no,no'){
        $str=$_COOKIE['location'];
        $data["me_location"]=explode(',',$str);
        if($data["me_location"][0]!=0 && $data["me_location"][1]!=0){
          $_SESSION['me_location']=$data["me_location"];
        }else{
          $data["me_location"]=array(12.93,100.87);
        }
     //   unset($_COOKIE['location']);
      }
    }else{
      $data["me_location"]=array(12.93,100.87);
    }
    if(isset($_COOKIE['olook'])){
      $iosarr=explode('-',$_COOKIE['olook']);
      $data['user_id']=$iosarr[0];
      if(isset($iosarr[2])){
        if($iosarr[2]!='no,no'){
          $str=$iosarr[2];
          $_SESSION['me_location']=$data["me_location"]=explode(',',$str);
        }
      }
      if(isset($iosarr[3])){
        if($iosarr[3]!='no' && $iosarr[3]!='menu'){
          $_COOKIE['video_id']=$iosarr[3];
        }
      }
    //  unset($_COOKIE['olook']);
    }
   // $cc=$this->get_city_country($data["me_location"][0], $data["me_location"][1]);
   // $country=$cc[0];

    $ip = $this->common->real_ip();
    $country = $this->common->GetIpCountrynew($ip);
    if(isset($data['user_id'])){
      if($data['user_id']==1027 ){
     // echo $country;exit();
      }
    }
    if($country=='中国' ||$country=='China' ||$country=='中國'){
      $_SESSION['country']=$data['country']=0;
    }else{
      $_SESSION['country']=$data['country']=1;
    }

    $data['list_video_id']=$data['list_location']= $data['list_user_name']=$data['list_image']=$data['level']=array();
    $select='v_video.video_id,v_video.user_id,location,v_users.user_name,v_users.image';

    $rs=$this->User_model->get_select_all($select,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
   //      $_COOKIE['video_id']='5476';
    foreach($rs as $k=>$v){
      if(isset($_COOKIE['video_id'])){
        if($_COOKIE['video_id']==$v['video_id']){
          $arr=explode(',',$v['location']);
          $data['live_location_w']=$arr[0];
          $data['live_location_j']=$arr[1];
          if($data['live_location_w']==0 && $data['live_location_j']==0){
            $data['no_live_location']=1;
            continue;
          }
          $data['live_id']=$v['video_id'];
          $data['live_name']=$v['user_name'];

          $temp['user_id']=$v['user_id'];
          if(stristr($v['user_name'],"'")){
            $v['user_name']=str_replace("'","’",$v['user_name']);
          }
          if(stristr($v['user_name'],"\\")){
            $v['user_name']=str_replace("\\","\\\\",$v['user_name']);
          }
          $temp['user_name']=$v['user_name'];
          $temp['image']=$v['image'];
          $temp['video_id']=$v['video_id'];
          $temp['location']=$v['location'];
          continue;
        }
      }
      $data['list_user_id'][]=trim($v['user_id']);
      $data['list_location'][]=trim($v['location']);
      if(stristr($v['user_name'],"'")){
        $v['user_name']=str_replace("'","’",$v['user_name']);
      }
      if(stristr($v['user_name'],"\\")){
        $v['user_name']=str_replace("\\","\\\\",$v['user_name']);
      }
      $data['list_user_name'][]=trim($v['user_name']);
      $data['list_image'][]=trim($v['image']);
      $data['list_video_id'][]=trim($v['video_id']);

    }
    if(isset($_COOKIE['video_id'])){
      /*array_unshift( $data['list_user_id'],$temp['user_id']);
      array_unshift( $data['list_user_name'],$temp['user_name']);
      array_unshift( $data['list_location'],$temp['location']);
      array_unshift( $data['list_image'],$temp['image']);
      array_unshift( $data['list_video_id'],$temp['video_id']);*/
      if(isset($temp['user_id'])){
        $data['list_user_id'][]=$temp['user_id'];
        $data['list_user_name'][]=$temp['user_name'];
        $data['list_location'][]=$temp['location'];
        $data['list_image'][]=$temp['image'];
        $data['list_video_id'][]=$temp['video_id'];
      }

      unset($_COOKIE['video_id']);
    }

   //$this->p($data);
    $data['list_user_id']=json_encode($data['list_user_id']);
    $data['list_location']=json_encode($data['list_location']);
    $data['list_user_name']=json_encode($data['list_user_name']);
    $data['list_image']=json_encode($data['list_image']);
    $data['list_video_id']=json_encode($data['list_video_id']);

    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')  )
    {
      $this->load->view('map/map',$data);

    }else{

      $this->load->view('map/mapan',$data);
    }

  }

/*
   * 苹果策略
   */

  public function appys(){

    $this->load->view('auth/appys');
  }

  public function driver_info(){
    $data['count_url']=$this->count_url;
    $driver_id=$this->input->get('driver_id',TRUE);
    $where=array('driver_id'=>$driver_id);
    $select="user_id,user_name,auth_name,mobile,auth_wechat,
      id_style,id_num,id_image,id_range,
      id_driver,id_driver_image,id_car_num,id_car_num_image,id_car_style,id_car_image,
      id_driver_status,id_auth_time";
    $data=$this->User_model->get_select_one($select,$where,'v_auth_drivers');
    $user_id=$data['user_id'];
    $data['is_auth']=$this->User_model->get_select_one('is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['city']=$data['id_range'];
    $this->load->view('user/dirver_info',$data);

  }
/*
 * 推送
 */
  public function pushinfo($data,$info)
  {
    $token1=$token2= $sep = $type_1 = $type_2 = '';
    for($i=0;$i<count($data);$i++)
    {
      if($data[$i]['type'] == '1')
      {

        $token1 .= $sep.$data[$i]['device_id'];
        $sep =',';
        $type_1 = 1;

      }
      elseif($data[$i]['type'] == '2')
      {

        $token2 .= $sep.$data[$i]['device_id'];
        $sep =',';
        $type_2 = 2;
      }
    }
    //$token1 = '37499ba4d41b50a6811ba2aabcef53b2a54958793b8b1d90d86af8b533ef6d88,571ce7ae1aea82c982b5869559b95adb393307e3f4322e7beb50aff09558b728,9ddfa0de3772b41c75f295a83453a07f0d305e2677da7b5b9dfbb74024f627cb,efe3d3e9d536143f53825b39db1080bcbd9c59b3a0e96a39d8f9bacd65d9bc97';
    $url = "http://msg.umeng.com/api/send?sign=";
    $urlForSign = 'http://msg.umeng.com/api/send';
    $params['timestamp'] = time();
    $params['type'] = 'listcast';
    if($type_1 == 1)
    {
      $app_master_secret = $this->config->item('app_master_secret_ios');
      $params['appkey'] = $this->config->item('youmeng_apikey_ios');
      $aps['alert'] =  $info ;
      $aps['sound'] = '';
      $aps['content-available'] = 1;
      // $aps['video_info'] = $video_info;
      $payload['aps'] = $aps;
      $params['production_mode'] = FALSE;
      $params['payload'] = $payload;
      $params['device_tokens'] = $token1 ;
      $post_body = json_encode($params);
      $sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
      $array = $this->http_post_data($url . $sign, $post_body);
    }
    if($type_2 == 2){
      $app_master_secret = $this->config->item('app_master_secret_android');
      $params['appkey'] = $this->config->item('youmeng_apikey_android');
      $payload['display_type'] = "notification";
      $payload['body'] = array('text'=>$info,'sound'=>'');
      $params['payload'] = $payload;
      $params['device_tokens']= $token2 ;
      $post_body = json_encode($params);
      $sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
      $array = $this->http_post_data($url . $sign, $post_body);
    }
  }

  public function http_post_data($url, $data_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string))
    );
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();

    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return array($return_code, $return_content);
  }
  /*
   * 审核操作
   */
  public function driver_ok_no(){
    $mes=$this->input->post_get('mes',TRUE);
    $user_id=$this->input->post_get('user_id',TRUE);
    $user_name=$this->input->post_get('user_name',TRUE);
    if($mes==2){
        $where=array(
          'user_id'=>$user_id,
      );
        $data=array(
            'id_driver_status'=>'2',
            'id_auth_time'=>time(),
        );

        $this->User_model->update_one($where,$data,'v_auth_drivers');
        $this->User_model->update_one(array('user_id'=>$user_id,),array('is_driver'=>'1','auth'=>'1'),'v_users');
        $idor=$this->User_model->get_count(array('user_id'=>$user_id),'v_auth_users');
        if($idor['count']==0){
            $this->User_model->user_insert('v_auth_users',array('user_id'=>$user_id,'user_name'=>$user_name));
        }
        $this->put_admin_log("司机审核通过 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzs');
        $this->push_sys($user_id,$info);
        echo 2;

    }elseif($mes==3){
      $where=array(
          'user_id'=>$user_id,
      );
      $data=array(
          'id_driver_status'=>'3',
          'id_auth_time'=>time(),
      );
      if($this->User_model->update_one($where,$data,'v_auth_drivers')){
        $this->put_admin_log("司机审核否决 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzf');
        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }

  public function local_info(){
    $data['count_url']=$this->count_url;
    $local_id=$this->input->get('local_id',TRUE);
    $where=array('local_id'=>$local_id);
    $select="user_id,user_name,auth_name,mobile,auth_wechat,id_style,id_num,id_image,,id_auth_time,id_range,id_local_status";
    $data=$this->User_model->get_select_one($select,$where,'v_auth_locals');
    $user_id=$data['user_id'];
    $data['is_auth']=$this->User_model->get_select_one('is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $data['city']=$data['id_range'];
    $this->load->view('user/local_info',$data);
  }

  /*
   * 当地人审核操作
   */
  public function local_ok_no(){
    $mes=$this->input->post_get('mes',TRUE);
    $user_id=$this->input->post_get('user_id',TRUE);
    $user_name=$this->input->post_get('user_name',TRUE);
    if($mes==2){
        $data=array(
            'id_local_status'=>'2',
            'id_auth_time'=>time(),);
        $where=array(
            'user_id'=>$user_id,
        );
          $this->User_model->update_one($where,$data,'v_auth_locals');
          $this->User_model->update_one(array('user_id'=>$user_id,),array('is_attendant'=>'1','auth'=>'1'),'v_users');
          $idor=$this->User_model->get_count(array('user_id'=>$user_id),'v_auth_users');
          if($idor['count']==0){
              $this->User_model->user_insert('v_auth_users',array('user_id'=>$user_id,'user_name'=>$user_name));
          }

          $this->put_admin_log("地陪审核通过 用户id{$user_id}");
          $this->new_lan_bydb($user_id);
          $info=$this->lang->line('sys_rzs');
          $this->push_sys($user_id,$info);
          echo 2;

    }elseif($mes==3){
      $where=array(
          'user_id'=>$user_id,
      );
      $data=array(
          'id_local_status'=>'3',
          'id_auth_time'=>time(),
      );
      if($this->User_model->update_one($where,$data,'v_auth_locals')){
        $this->put_admin_log("地陪审核否决 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzf');

        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }

  /*
   * 后台商户信息详情
   */
  public function business_info(){
    $data['count_url']=$this->count_url;
    $business_id=$this->input->get('business_id',TRUE);
    $where=array('business_id'=>$business_id);
    $select="user_id,user_name,auth_name,mobile,auth_wechat,shop_name,
      id_style,id_num,id_image,id_business_image,id_range,
      id_business_status,id_auth_time";
    $data=$this->User_model->get_select_one($select,$where,'v_auth_business');
    $user_id=$data['user_id'];
    $data['is_auth']=$this->User_model->get_select_one('is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
      $data['city']=$data['id_range'];
    $this->load->view('user/business_info',$data);

  }



  /*
   * 后台审核导游详细
   */
  public function guide_info(){
    $data['count_url']=$this->count_url;
    $view_id=$this->input->get('view_id',TRUE);
    $where=array('view_id'=>$view_id);
    $select="user_id,user_name,auth_name,mobile,auth_wechat,
      id_style,id_num,id_image,
      id_view_style,id_view_num,id_view_image,id_range,
      id_view_status,id_auth_time";
    $data=$this->User_model->get_select_one($select,$where,'v_auth_views');
    $user_id=$data['user_id'];
    $data['is_auth']=$this->User_model->get_select_one('is_guide,is_attendant,is_driver,is_merchant',array('user_id'=>$user_id),'v_users');
    $idarr=$data['id_range'];
    $data['city']=explode(',',$idarr);
    $this->load->view('user/guide_info',$data);
  }

  /*
   * 提现审核信息
   */
  public function withdraw_info(){
    $data['count_url']=$this->count_url;
    $user_id=$this->input->get('user_id',TRUE);
    $where=array('user_id'=>$user_id);
    $select="user_id,user_name,
      id_money_image,
      card_no,bank_name,bank_user_name,
      auth_money_time,
      alipay_id,alipay_user_name,wechat,
      auth_status";
    $data=$this->User_model->get_select_one($select,$where);
    //echo "<pre>";echo $this->db->last_query();var_dump($data);exit();
    //print_r($data);
    // $data['user_id']=$user_id;
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('user/withdraw_info',$data);


  }
  /*
   * 提现审核通过不通过按钮
   */
  public function withdraw_ok_no(){
    $mes=$this->input->post_get('mes',TRUE);
    $user_id=$this->input->post_get('user_id',TRUE);
    if($mes==2){
      $data=array(
          'auth_status'=>'2'
      );
      //update_one(array('user_id'=>$user_id),$data,'v_auth_users')
      if($this->User_model->update_one(array('user_id'=>$user_id),$data,'v_auth_users')){
        //echo $this->db->last_query();
        $this->put_admin_log("账户审核通过 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_zhs');
        $this->push_sys($user_id,$info);
        echo 2;
      }

    }elseif($mes==3){
      $data=array(
          'auth_status'=>'3'
      );
      if($this->User_model->user_update('v_auth_users',$data, $user_id)){
        $this->put_admin_log("账户审核否决 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_zhf');
        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }
  /*
   * 商户审核
   */
  public function business_ok_no(){
    $mes=$this->input->post_get('mes',TRUE);
    $user_id=$this->input->post_get('user_id',TRUE);
    $user_name=$this->input->post_get('user_name',TRUE);
    if($mes==2)
    {
      $where=array('user_id'=>$user_id);
        $data=array(
            'id_business_status'=>'2',
            'id_auth_time'=>time()
        );
        $pho_info=$this->User_model->get_select_one('is_pho',array('user_id'=>$user_id),'v_auth_business');
        if($pho_info['is_pho']==1)
        {
          $user_update=array('is_merchant'=>'1','auth'=>'1','is_trip_merchant'=>'1');
        }else{
            $user_update=array('is_merchant'=>'1','auth'=>'1','is_trip_merchant'=>'0');
        }
        $this->User_model->update_one($where,$data,'v_auth_business');
        $this->User_model->update_one(array('user_id'=>$user_id),$user_update,'v_users');
          $idor=$this->User_model->get_count(array('user_id'=>$user_id),'v_auth_users');
         if($idor['count']==0){
             $this->User_model->user_insert('v_auth_users',array('user_id'=>$user_id,'user_name'=>$user_name));
         }
          $this->put_admin_log("商户审核通过 用户id{$user_id}");
          $this->new_lan_bydb($user_id);
          $info=$this->lang->line('sys_rzs');
          $this->push_sys($user_id,$info);
          echo 2;

    }elseif($mes==3){
      $where=array(
          'user_id'=>$user_id,
      );
      $data=array(
          'id_business_status'=>'3',
          'id_auth_time'=>time()
      );
      if($this->User_model->update_one($where,$data,'v_auth_business')){
       // $this->User_model->update_one(array('user_id'=>$user_id),array('is_merchant'=>'0','auth'=>'0'),'v_users');
        $this->put_admin_log("商户审核否决 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzf');
        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }
  /*
   * 导游审核确定
   * update_one($where,$data=array(),$table='v_auth_views')
     get_select_one($select='*',$where='1=1',$table='v_auth_views')
    get_count($where='', $table='')
   */
  public function guide_ok_no(){
    $mes=$this->input->post('mes',TRUE);
    $user_id=$this->input->post('user_id',TRUE);
    $user_name=$this->input->post_get('user_name',TRUE);
    if($mes==2)
    {
      $where=array('user_id'=>$user_id);
        $data=array(
            'id_view_status'=>'2',
            'id_auth_time'=>time()
        );
        $this->User_model->update_one($where,$data,'v_auth_views');
        $this->User_model->update_one(array('user_id'=>$user_id,),array('is_guide'=>'1','auth'=>'1'),'v_users');
        $idor=$this->User_model->get_count(array('user_id'=>$user_id),'v_auth_users');
        if($idor['count']==0){
            $this->User_model->user_insert('v_auth_users',array('user_id'=>$user_id,'user_name'=>$user_name));
        }

        $this->put_admin_log("导游审核批准 用户id{$user_id}");

        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzs');
        $this->push_sys($user_id,$info);
        echo 2;

    }
    elseif($mes==3)
    {
      $where=array(
          'user_id'=>$user_id,
      );
      $data=array(
          'id_view_status'=>'3',
          'id_auth_time'=>time()
      );
      if($this->User_model->update_one($where,$data,'v_auth_views'))
      {
//          $rs=$this->get_auth_arr($user_id);
//          if(isset($rs['is_attendant']) AND $rs['is_attendant']=='1')
//          {
//              $this->User_model->update_one(array('user_id'=>$user_id),array('is_guide'=>'0'),'v_users');
//          }else{
//              $this->User_model->update_one(array('user_id'=>$user_id),array('is_guide'=>'0','auth'=>'0'),'v_users');
//          }

        $this->put_admin_log("导游审核否决 用户id{$user_id}");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_rzf');
        $this->push_sys($user_id,$info);
        echo 3;
      }
    }
  }

  /*
  public function image_plus(){

    $r1=$this->User_model->get_select_one('image','user_id=1','v_users');
    $r2=$this->User_model->get_select_one('image','user_id=2','v_users');
    $path_1 =$r1['image'];
    $path_2 =$r2['image'];
    $image_1 = imagecreatefromjpeg($path_1);
    $image_2 = imagecreatefromjpeg($path_2);
//创建一个和人物图片一样大小的真彩色画布（ps：只有这样才能保证后面copy装备图片的时候不会失真）
    $image_3 = imageCreateTRUEcolor(imagesx($image_1),imagesy($image_1));
//为真彩色画布创建白色背景，再设置为透明
    $color = imagecolorallocate($image_3, 255, 255, 255);
    imagefill($image_3, 0, 0, $color);
    imageColorTransparent($image_3, $color);
//首先将人物画布采样copy到真彩色画布中，不会失真
    imagecopyresampled($image_3,$image_1,0,0,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
//再将装备图片copy到已经具有人物图像的真彩色画布中，同样也不会失真
    imagecopymerge($image_3,$image_2, 0,320,0,0,imagesx($image_2),imagesy($image_2), 100);
//将画布保存到指定的gif文件
    imagejpeg($image_3, "./public/images/test.jpg");
  }*/
     //
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
 * 提现审核信息删除
 */
  public function withdraw_del()
  {
    $user_id=$this->input->get('user_id',TRUE);
    $this->User_model->del(array('user_id'=>$user_id),'v_auth_users');
    $this->User_model->del(array('user_id'=>$user_id),'v_auth_drivers');
    $this->User_model->del(array('user_id'=>$user_id),'v_auth_locals');
    $this->User_model->del(array('user_id'=>$user_id),'v_auth_views');
    $this->User_model->del(array('user_id'=>$user_id),'v_auth_business');
    $count=  $this->User_model->get_count("user_id=$user_id AND is_guide='0' AND is_driver='0' AND is_attendant='0' AND is_merchant ='0'", 'v_users');
    if($count['count']==1){
      $this->User_model->update_one(array('user_id'=>$user_id),array('auth'=>'0'),'v_users');
    }
    redirect(base_url('newadmin/withdraw_list'));
   // return FALSE;
  }
  /*
   * app提现
   */
  public function user_account()
  {
    $data['count_url']=$this->count_url;
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $data['user_id']=$user_id;
    $cash_num=$this->input->post('cash_num',TRUE);
    $select='canamount,amount';
    $where=array('user_id'=>$user_id);
    $row=$this->User_model->get_select_one($select,$where);
    if($cash_num<=$row['canamount'] && $cash_num>=0.01 && $cash_num<=$row['amount']){
      $data=array(
          'user_id'=>$user_id,
          'cash_num'=>$cash_num,
          'cash_in_out'=>'2',
          'cash_adv_time'=>time(),
          'cash_status'=>'1'
      );

      $cash=$row['canamount']-$cash_num;
      $cash_arr=array(
          'canamount'=>$cash
      );
      $this->User_model->user_insert($table='v_cash_log',$data);
      $this->User_model->update_one($where,$cash_arr,'v_auth_users');
      redirect(base_url("user/user_pay_adv"));
    }else{
      return FALSE;
    }
  }
  /*
   * push system
   */
public function push_sys($user_id,$info)
{
    $data=array(
      'pm_type'=>0,
        'user_id'=>$user_id,
        'message'=>$info,
        'is_new'=>1,
        'add_time'=>time()
    );
    $this->User_model->user_insert('v_prompt',$data);
}
  /**
   * [user_info 用户详情]
   * @param  string $user_id [用户id]
   * @return [type]     [description]
   */
  public function user_info($user_id='')
  {
    $data['count_url']=$this->count_url;
    /* if(empty($user_id))
     {
         echo '非法操作，用户id 为空';
         echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';
     }*/
    if(empty($user_id))
    {
      $this->load->view('user/user_info');
    }
    else
    {
      $data['info'] = $this->Admin_model->user_info($this->table,' user_id=' . $user_id);
      $this->load->view('user/user_info',$data);
    }
  }


  /**
   * [user_edit 编辑用户]
   * @param  string $user_id [用户id]
   * @return [type]          [description]
   */
  public function user_edit()
  {
    //修改
    $user_id=$this->input->post('user_id');
    $images = $this->upload_image("image",$user_id);
    if($images)
    {
      $data['image'] = $images;
    }
    if(!empty($user_id))
    {
      $data = array(
          'user_name' => $this->input->post('user_name')
      );
      if($images)
      {
        $data['image'] = $images;
      }
      $num=$this->User_model->user_update($this->table,$data,$user_id);
      if($num)
      {
        echo '修改成功';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_info/'.$user_id.'">';die;
      }
      else
      {
        echo '修改失败';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_info/'.$user_id.'">';die;
      }
    }
    else
    {
      //添加
      $data = array(
          'user_name'     => $this->input->post('user_name'),'image'=> $images
      );
      $num=$this->User_model->user_insert($this->table,$data);
      if($num)
      {
        echo '添加成功';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';die;
      }
      else
      {
        echo '添加失败';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';die;
      }
    }
  }
  /*
   *获取user_id
   */
  public function user_id_and_open_id()
  {
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
          return FALSE;
        }
      }else{
        return FALSE;
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
        return FALSE;
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
        return FALSE;
      }
    }else{
      return FALSE;
    }
  }
  /**
   * [user_del 删除或者封杀]
   * @param  string $user_id [用户id ]
   * @param  string $type    [类型    2  封杀  3  删除]
   * @return [type]          [description]
   */
  public function user_del($user_id='', $type='')
  {

    if($type == 2)
    {
      $data = array('kill_time' => time());
    }
    $data['status']=  $type;
    $num=$this->User_model->user_del($this->table,$data,$user_id);
    if($num)
    {
      if($type == 2)
      {
        echo '封杀成功';
      }elseif($type == 3)
      {
        echo '删除成功';
      }
      echo '<meta http-equiv="refresh" content="1; url=/newadmin/user_list">';
    }
    else
    {
      if($type == 2)
      {
        echo '封杀失败';
      }elseif($type == 3)
      {
        echo '删除失败';
      }
      echo '<meta http-equiv="refresh" content="1; url=/newadmin/user_list">';
    }
  }

public function user_normal($user_id='0'){
  $this->User_model->update_one(array('user_id'=>$user_id),array('status'=>'0'),'v_users');
   echo '<meta http-equiv="refresh" content="1; url=/newadmin/user_list">';
}
  /*
   * 缩略图生成
   */
  public function thumb($url,$key1,$key2='time'){
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
    $arr['width']     = 520;
    $arr['height']   = 300;

    $this->image_lib->initialize($arr);

    if($this->image_lib->resize())
    {
      return  $arr['new_image'];
    }


  }
  /**
   * [image 图片上传]
   * @param  [type] $filename [description]
   * @param  [type] $fileurl  [description]
   * @return [type]           [description]
   */
  public function upload_image($filename,$fileurl,$key='time')
  {
    /* 如果目标目录不存在，则创建它 */
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

  /*
	微信
	 */
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
        $acc_token = json_decode($acc_token, TRUE);
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
      $acc_token = json_decode($acc_token, TRUE);
      $token = $acc_token['access_token'];
      //$acc_time = time();
      //$GLOBALS['db']->query("INSERT INTO wx_acc_token SET access_token='$token', access_time='$acc_time' ");
    }
    return $token;
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
      return FALSE;
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

  public function wx_get_js_ticket($appid,$secret){
    $ticket = "";
    $time = time() - 7000;
    $where=array('app_id'=>$appid);
    $ticket_info=$this->User_model->get_select_one('jsapi_ticket,jsapi_time',$where,'wx_acctoken_info');

    if(!empty($ticket_info['jsapi_ticket']) && $ticket_info['jsapi_time'] > $time)
    {
      $ticket = $ticket_info['jsapi_ticket'];
    }
    else
    {
      $token = $this->get_actoken($appid,$secret);
      $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
      $jsapi_ticket = file_get_contents($url);
      $jsapi_ticket = json_decode($jsapi_ticket, TRUE);
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
  public function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function getlan($user_id){
    $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),$table='v_users');
    return $rs['lan'];
  }

  //部分操作log

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
  /**
   * 获得用户的真实IP地址
   *
   * @access  public
   * @return  string
   */
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

  public function get_crop_for_video(){
    set_time_limit(0);
    $data=$this->User_model->get_select_all($select='video_id',
        $where=" (imageforh5 IS NULL OR imageforh5 ='') AND is_off=1  ",$order_title='start_time',$order='ASC',$table='v_video');
    if($data!==false){
      foreach($data as $k=>$v){
        $url="./uploads/".$v['video_id'].".jpg";
        $new_imag=$this->crop_for_video($url,$v['video_id']);
        $dataimage=array('imageforh5'=>$new_imag);
        $this->User_model->update_one(array('video_id'=>$v['video_id']),$dataimage,$table='v_video');
      }
    }

  }

  public function crop_for_video($source_path='./uploads/5311.jpg',$key2='time',$target_width='100', $target_height='100')
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
}