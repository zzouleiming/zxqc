<?php
/**
 * 商户商品买卖
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Bussell extends CI_Controller {

    public $et=FALSE;
    public $wx=FALSE;
    public  $user_id=0;
  public function __construct()
  {
    parent::__construct();
    ini_set('php_mbstring','1');
    $this->load->model('User_model');
    $this->load->model('Order_model');
    $this->load->model('Camer_model');
    $this->load->library('common');
    $this->load->library('session');
    //$this->load->library('imagecrop');
    $this->load->helper('url');
    $this->load->library('image_lib');
    // $this->load->driver('cache');
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
      $this->get_et();
      $this->get_wx();
  }







    public function get_et()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false)
        {
            $this->et=FALSE;
        }
        else
        {
            $this->et=TRUE;
        }
    }

    public function get_wx()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE)
        {
            $this->wx=FALSE;
        }
        else
        {
            $this->wx=TRUE;
        }
    }



  //唯一产品入库
  public function order_todb()
  {
    //echo'<pre>';print_r($_SESSION);
    if(isset( $_SESSION['fly']) &&  isset($_SESSION['hotel']))
    {
      $order_id=$this->input->post('order_id',TRUE);

      if((int)$order_id>0)
      {
        $rs=$this->User_model->get_select_one('goods_amount,order_sn,user_id_buy_fromwx,appId,nonceStr,timeStamp,signType,package,paySign',array('order_id'=>$order_id),'v_order_info');
        if($rs['appId']!='0')
        {
          $arr_get=array(
              'appId'=>$rs['appId'],
              'nonceStr'=>$rs['nonceStr'],
              'timeStamp'=>$rs['timeStamp'],
              'signType'=>$rs['signType'],
              'package'=>$rs['package'],
              'paySign'=>$rs['paySign'],
          );
          $arr=array('order_id'=>$order_id,'json'=>json_encode(json_encode($arr_get)));
          echo json_encode($arr);

        }
      }
      else
      {
        $cn_name=$this->input->post('cn_name',TURE);
        $cn_mobile=$this->input->post('cn_mobile',TURE);
        $weixin=$this->input->post('weixin',TURE);
        $app_commont=$this->input->post('app_commont',TURE);

        $order_sn=$this->get_order_sn();

        if($this->wx==TRUE)
        {
          $user_id=$this->get_wx_userid(base_url('wygoods'));
          $user_id_buy_name=$this->User_model->get_username($user_id,'v_wx_users');

          $order_info=array(
              'user_id_buy_fromwx'=>$user_id,
              'user_id_buy_name_fromwx'=>$user_id_buy_name,
              'from'=>'12',
              'user_id_sell'=>1,
              'user_id_sell_name'=>'坐享其成',
              'order_sn'=>$order_sn,
              'goods_amount'=>500,
              'order_amount'=>500,
              'goods_all_num' =>'1',
              'add_time'=>time(),
              'order_status'=>'0',
              'address'=>$weixin,
              'commont'=>$app_commont,
              'consignee'=>$cn_name,
              'mobile'=>$cn_mobile,
          );
          //fly : 0  特定转机     1  10日转机  2 4N6D直飞  3 10日直飞
          //hotel: 0当地五星酒店 1 国际五星酒店
          switch($_SESSION['fly'])
          {
            case 0:
              $fly='特定转机';
              break;
            case 1:
              $fly='10日转机';
              break;
            case 2:
              $fly='4N6D直飞';
              break;
            default:
              $fly='10日直飞';
          }

          switch($_SESSION['hotel'])
          {
            case 0:
              $hotel='当地五星';
              break;
            default:
              $hotel='国际五星';
          }
          $order_goods=array(
              'goods_id'=>2751,
              'goods_name'=>$fly.'、'.$hotel.'普吉岛旅游',
              'goods_number'=>1,
              'goods_sum'=>1,
              'goods_price'=>500,
          );

          $order_addition=array(
              'cn_name'=>$cn_name,
              'cn_mobile'=>$cn_mobile,
              'cn_hotel'=>$fly,
              'en_hotel'=>$hotel,
              'weixin'=>$weixin,
              'add_time'=>time(),
              'commont'=>$_SESSION['allprice'],
              'type'=>'1'
          );
          //echo '<pre>';print_r($order_info);print_r($order_goods);print_r($order_addition);exit();
          $order_id=$this->Order_model->order_insert($order_info,$order_goods,$order_addition);

          // exit();
          $_SESSION['order_info']=$order_info;
          $_SESSION['order_goods']=$order_goods;
          include_once("./application/third_party/wxpay/WxPay.php");
          $fee=floatval($_SESSION['order_info']['goods_amount'])*100;
          if(isset($_SESSION['openidfromwx']))
          {
            $openid=$_SESSION['openidfromwx'];
          }
          else
          {
            $url=base_url("wygoods");
            $this->get_wx_userid($url);
            $openid=$_SESSION['openidfromwx'];
          }

          $unifiedOrder = new UnifiedOrder_pub();
          $jsApi = new JsApi_pub();
          $unifiedOrder->setParameter("openid","$openid");//商品描述
          $unifiedOrder->setParameter("body","特价产品");//商品描述

          $out_trade_no =$_SESSION['order_info']['order_sn'];
          $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
          $unifiedOrder->setParameter("total_fee","$fee");//总金额
          $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
          $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
          //  echo $out_trade_no;exit();
          $prepay_id = $unifiedOrder->getPrepayId();
          // echo $prepay_id;exit();
          $jsApi->setPrepayId($prepay_id);
          $jsApiParameters = $jsApi->getParameters();
          $_SESSION['jsApiParameters']=$jsApiParameters;

          $prr_temp=json_decode($jsApiParameters,TRUE);
          $parm=array(
              'appId'=>$prr_temp['appId'],
              'nonceStr'=>$prr_temp['nonceStr'],
              'timeStamp'=>$prr_temp['timeStamp'],
              'signType'=>$prr_temp['signType'],
              'package'=>$prr_temp['package'],
              'paySign'=>$prr_temp['paySign'],
          );
          $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
          $jsApiParameters=json_encode($jsApiParameters);
          $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
          echo json_encode($arr);


        }else{
          echo -2;
        }

      }
    }
    else
    {
      echo -1;
    }
  }




  //app店铺列表
  public function app_shop_list($page=1)
  {
    //$data['title']=$this->input->post('title');
    //  echo $data['title'];exit();

    $user_id=$this->input->get('user_id',true);
    $lat=0;
    $lng=0;
    if(isset($_SESSION['lat'])){
      $lat=$_SESSION['lat'];
    }
    if(isset($_SESSION['lng'])){
      $lng=$_SESSION['lng'];
    }
    // return array('minLat'=>$minLat,'maxLat'=>$maxLat,'minLng'=>$minLng,'maxLng'=>$maxLng,);
    $arr=$this->getAround($lat,$lng,'500000000000');
    if($lng===0 || $lat===0){
      $data['signPackage']=$this->wx_js_para(3);
      $data['list']=array();

      $page_num =10;
      $data['now_page'] = $page;
      $count = $this->User_model->get_count("is_show = '1'",'v_wx_business');
      $data['max_page'] = ceil($count['count']/$page_num);
      if($page>$data['max_page'])
      {
        $page=1;
      }
      $start = ($page-1)*$page_num;
      $data['list']=$this->User_model->get_select_more($select='business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,address',
          "is_show = '1'",$start,$page_num,$order_title='business_id',$order='ASC',$table='v_wx_business');
      foreach($data['list'] as $k=>$v){
        $tag_arr=explode(',',$v['tag']);
        $data['list'][$k]['tag_arr']=$tag_arr;
      }
    }else{
      $data['signPackage']=false;
      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng] AND is_show = '1' AND lng !=''";
      $data['title']=$this->input->post_get('title',true);
      if($data['title']){
        $where.="  AND (business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%')";
      }


      $page_num =10;
      $data['now_page'] = $page;
      $count = $this->User_model->get_count($where,'v_wx_business');
      $data['max_page'] = ceil($count['count']/$page_num);
      if($page>$data['max_page'])
      {
        $page=1;
      }
      $start = ($page-1)*$page_num;

      //$where="1=1";
      //ABS((lat-$lat)*111+(lng-$lng)*111*COS($lat)) as order1
      $select="address,business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,lat,lng,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";
      $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
      //echo $this->db->last_query();exit();
      foreach($data['list'] as $k=>$v)
      {
        // $data['list'][$k]['distance']=$this->GetDistance($lat, $lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
        $tag_arr=explode(',',$v['tag']);
        $str='';
        $data['list'][$k]['tag_arr']=array();
        foreach($tag_arr as $k1=>$v1){
          $str.=$v1;
          $data['list'][$k]['tag_arr'][]=$v1;
          if(mb_strlen($str)>14){
            break;
          }
        }
      }
      // $data['list']=$this-> multi_array_sort($data['list'],'distance',$sort=SORT_ASC);
    }
    //  echo $this->db->last_query();
    // echo "<pre>";print_r($data);exit();
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    //echo "<pre>";print_r($data);exit();
    $this->load->view('bussell/wx_list',$data);
  }



  //wx 店铺列表
  public function wx_shop_list($page=1)
  {
    //$data['title']=$this->input->post('title');
  //  echo $data['title'];exit();
    $url=base_url("bussell/wx_shop_list");
   // $user_id=$data['user_id']=$this->get_wx_userid($url);
    //$user_id=-1;
//    $lat=0;
//    $lng=0;
//    if(isset($_SESSION['lat'])){
//      $lat=$_SESSION['lat'];
//    }
//    if(isset($_SESSION['lng'])){
//      $lng=$_SESSION['lng'];
//    }
    // return array('minLat'=>$minLat,'maxLat'=>$maxLat,'minLng'=>$minLng,'maxLng'=>$maxLng,);
 //   $arr=$this->getAround($lat,$lng,'500000000000');
//    if($lng===0 || $lat===0)
//    {
   //   $data['signPackage']=$this->wx_js_para(3);
      $data['signPackage']=false;
      $data['list']=array();

      $page_num =10;
      $data['now_page'] = $page;
      $count = $this->User_model->get_count("is_show = '1'",'v_wx_business');
      $data['max_page'] = ceil($count['count']/$page_num);
      if($page>$data['max_page'])
      {
        $page=1;
      }
      $start = ($page-1)*$page_num;
      $data['list']=$this->User_model->get_select_more($select='business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,address',
          "is_show = '1'",$start,$page_num,$order_title='business_id',$order='ASC',$table='v_wx_business');
      foreach($data['list'] as $k=>$v){
          if(mb_strlen($v['business_name'])>25){
              $data['list'][$k]['business_name']=mb_substr($v['business_name'],0,25).'...';
          }
        $tag_arr=explode(',',$v['tag']);
        $data['list'][$k]['tag_arr']=$tag_arr;
      }
   // }else{
//      $data['signPackage']=false;
//      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng] AND is_show = '1' AND lng !=''";
//      $data['title']=$this->input->post_get('title',true);
//      if($data['title']){
//        $where.="  AND (business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%')";
//      }
//
//
//      $page_num =10;
//      $data['now_page'] = $page;
//      $count = $this->User_model->get_count($where,'v_wx_business');
//      $data['max_page'] = ceil($count['count']/$page_num);
//      if($page>$data['max_page'])
//      {
//        $page=1;
//      }
//      $start = ($page-1)*$page_num;
//
//      //$where="1=1";
//      //ABS((lat-$lat)*111+(lng-$lng)*111*COS($lat)) as order1
//      $select="address,business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,lat,lng,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";
//      $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,$order_title="distance",$order='ASC',$table='v_wx_business');
//      //echo $this->db->last_query();exit();
//      foreach($data['list'] as $k=>$v)
//      {
//       // $data['list'][$k]['distance']=$this->GetDistance($lat, $lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
//          if(mb_strlen($v['business_name'])>25){
//              $data['list'][$k]['business_name']=mb_substr($v['business_name'],0,25).'...';
//          }
//        $tag_arr=explode(',',$v['tag']);
//        $str='';
//        $data['list'][$k]['tag_arr']=array();
//        foreach($tag_arr as $k1=>$v1){
//          $str.=$v1;
//          $data['list'][$k]['tag_arr'][]=$v1;
//          if(mb_strlen($str)>14){
//            break;
//          }
//        }
//      }
     // $data['list']=$this-> multi_array_sort($data['list'],'distance',$sort=SORT_ASC);
 //   }
 //  echo $this->db->last_query();
  // echo "<pre>";print_r($data);exit();
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    //echo "<pre>";print_r($data);exit();
    $this->load->view('bussell/wx_list',$data);
  }


  public function wxshop_list_iscron(){

    $user_id=$data['user_id']=1;
    $lat=0;
    $lng=0;
    if(isset($_SESSION['lat'])){
      $lat=$_SESSION['lat'];
    }
    if(isset($_SESSION['lng'])){
      $lng=$_SESSION['lng'];
    }
    // return array('minLat'=>$minLat,'maxLat'=>$maxLat,'minLng'=>$minLng,'maxLng'=>$maxLng,);
    $arr=$this->getAround($lat,$lng,'50000000000');
    if($lng===0 || $lat===0){
      $data['signPackage']=$this->wx_js_para(3);
      $data['list']=array();

      $data['list']=$this->User_model->get_select_more($select='business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,address',
          "is_show = '1'",$start=0,$page_num=10,$order_title='business_id',$order='ASC',$table='v_wx_business');

      foreach($data['list'] as $k=>$v){
        $tag_arr=explode(',',$v['tag']);
        $data['list'][$k]['tag_arr']=$tag_arr;
      }
      if($this->input->get('test')){
        echo "<pre>";print_r( $data['list']);exit();
      }
    }else{
      $data['signPackage']=false;
      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng] AND is_show = '1'";
      $data['title']=$this->input->post('title',true);
      if($data['title']){
        $where.="  AND (business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%')";
      }
      //$where="1=1";
      $data['list']=$this->User_model->get_select_more($select='address,business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,lat,lng',
          $where,$start=0,$page_num=1,$order_title='business_id',$order='ASC',$table='v_wx_business');

      $str=$len='';
      foreach($data['list'] as $k=>$v){
        $data['list'][$k]['tag_arr']=array();
        $data['list'][$k]['distance']=$this->GetDistance($lat, $lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
        //$len=mb_strlen($v['tag']);
        $tag_arr=explode(',',$v['tag']);
        foreach($tag_arr as $k1=>$v1){
          $str.=$v1;
          $data['list'][$k]['tag_arr'][]=$v1;
          if(mb_strlen($str)>14){
              break;
          }
        }
       // $data['list'][$k]['tag_arr']=$tag_arr;

      }
      $data['list']=$this-> multi_array_sort($data['list'],'distance',$sort=SORT_ASC);
    }
    //  echo $this->db->last_query();
    // echo "<pre>";print_r($data);exit();

    //echo json_encode($data['list']);
    $this->load->view('bussell/wx_list_iscroll',$data);
  }

  public function wxshop_list_iscron_ajax(){
    $page=$this->input->post('page',true);
    if(!$page){
      $page=1;
    }
    $page_num =1;
    $start = ($page-1)*$page_num;

//echo $start;exit();
    $user_id=$data['user_id']=1;
    $lat=0;
    $lng=0;
    if(isset($_SESSION['lat'])){
      $lat=$_SESSION['lat'];
    }
    if(isset($_SESSION['lng'])){
      $lng=$_SESSION['lng'];
    }
    // return array('minLat'=>$minLat,'maxLat'=>$maxLat,'minLng'=>$minLng,'maxLng'=>$maxLng,);
    $arr=$this->getAround($lat,$lng,'50000000000');
    if($lng===0 || $lat===0){
      $data['signPackage']=$this->wx_js_para(3);
      $data['list']=array();

      $data['list']=$this->User_model->get_select_more($select='business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,address',
          "is_show = '1'",$start,$page_num,$order_title='business_id',$order='ASC',$table='v_wx_business');
      foreach($data['list'] as $k=>$v){
        $tag_arr=explode(',',$v['tag']);
        $data['list'][$k]['tag_arr']=$tag_arr;
      }
    }else{
      $data['signPackage']=false;
      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng] AND is_show = '1'";
      $data['title']=$this->input->post('title',true);
      if($data['title']){
        $where.="  AND (business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%')";
      }
      //$where="1=1";
      $data['list']=$this->User_model->get_select_more($select='address,business_id,logo_image_thumb as logo_image,business_name,star_num,discount,tag,lat,lng',
          $where,$start,$page_num,$order_title='business_id',$order='ASC',$table='v_wx_business');
      foreach($data['list'] as $k=>$v){

        $data['list'][$k]['distance']=$this->GetDistance($lat, $lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
        $tag_arr=explode(',',$v['tag']);
        $data['list'][$k]['tag_arr']=$tag_arr;
      }
      $data['list']=$this-> multi_array_sort($data['list'],'distance',$sort=SORT_ASC);
    }

    echo json_encode($data['list']);
   // $this->load->view('bussell/wx_list_iscroll',$data);
  }

  //获取实际人民币
    public function get_true_money()
  {
    $money=$this->input->post_get('money',true);
    echo  json_encode(array('money'=>sprintf("%.2f", $this->common->get_true_money($money))));


  }

  public function get_true_curr(){
    echo $this->common->get_true_curr('CNY');
  }

  //获取微信端用户地理位置
  public function receive_location(){
    $_SESSION['lat']=$this->input->post('latitude',true);
    $_SESSION['lng']=$this->input->post('longitude',true);
    echo 1;
  }
  //wx 端商铺详情
  public function bussinfo(){
    $business_id=$this->input->get('business_id',true);
    $url=base_url("bussell/bussinfo?business_id=$business_id");
    $user_id=$data['user_id']=$this->get_wx_userid($url);
    $data=$this->User_model->get_select_one('business_id,logo_image_thumb as image,tag,
    business_name,star_num,discount,business_info,business_tel,business_address',
        array('business_id'=>$business_id),'v_wx_business');

    $tag_arr=explode(',',$data['tag']);
    if(mb_strlen($data['business_name'])>13){
      $data['business_name']=mb_substr($data['business_name'],0,13).'…';
    }
    //$data['tag_arr']=$tag_arr;
    $str='';
    foreach($tag_arr as $k1=>$v1){
      $str.=$v1;
      $data['tag_arr'][]=$v1;
      if(mb_strlen($str)>14){
        break;
      }
    }
    if($data['business_info']==''){
      $data['business_info']=$data['tag'];
    }
    if(isset($_SESSION['pra'])){
      $pra=array_unique($_SESSION['pra']);
      $pra=array_values($pra);
      //组成点赞过的视频id json数据 便于前台js遍历
      $data['pra']=json_encode($pra);
    }

    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.image as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
    $left_title='v_video.user_id=v_users.user_id';
    $left_table='v_users';
    $data['list']=$this->User_model->get_act_video_all($select,"business_id=$business_id  AND is_off<2 ",'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start=0,$page_num=100);
    if(!empty($data['list'])){
      foreach($data['list'] as $k => $v){
        if($v['is_off']==1)
        {
          if($v['push_type']==0)
          {
            $data['list'][$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
          }
          else
          {
            $data['list'][$k]['path']=$this->config->item('record_uc_url').$v['video_name'].'.m3u8';
          }
          $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
        }
        else
        {
          $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
      }

    }
    if(!$data['list'])
    {
      $data['list']=0;
    }

    if($this->input->get('test'))
    {
      echo '<pre>';print_r($data);exit();
    }
    $this->load->view('bussell/wxshop_info',$data);
  }

  //app 端商铺详情
  public function business_info_app()
  {

    $business_id=$this->input->get('business_id',true);

    $data['h5']=FALSE;
   //print_r($_COOKIE);exit();
    $per_user_id=$this->user_id_and_open_id();
        //echo $per_user_id;exit();
    if(!$per_user_id)
    {
      if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
      {
        $per_user_id=-1;
        $data['h5']=TRUE;
        $data['down']=$this->down;
      }
      else
      {
        redirect(base_url("bussell/bussinfo?business_id={$business_id}"));
      }
    }


    if(!$business_id)
    {
     return false;
    }
    $data=$this->User_model->get_select_one('business_id,logo_image_thumb as image,tag,user_id,business_name,star_num,discount,business_info,business_tel,business_address',
        array('business_id'=>$business_id),'v_wx_business');
    if(mb_strlen($data['business_name'])>13){
      $data['business_name']=mb_substr($data['business_name'],0,13).'…';
    }
    $tag_arr=explode(',',$data['tag']);
    //$data['tag_arr']=$tag_arr;
    if($data['business_info']==''){
      $data['business_info']=$data['tag'];
    }
    $str='';
    if($data['user_id']==$per_user_id)
    {
      $data['edit']=TRUE;
    }
    else
    {
      $data['edit']=FALSE;
    }
    foreach($tag_arr as $k1=>$v1)
    {
      $str.=$v1;
      $data['tag_arr'][]=$v1;
      if(mb_strlen($str)>14){
        break;
      }
    }

    if(isset($_SESSION['pra']))
    {
      $pra=array_unique($_SESSION['pra']);
      $pra=array_values($pra);
      //组成点赞过的视频id json数据 便于前台js遍历
      $data['pra']=json_encode($pra);
    }
    if(mb_strlen($data['business_name'])>14){
      $data['business_name']=mb_substr($data['business_name'],0,14).'……';
    }
    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.image as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
    $left_title='v_video.user_id=v_users.user_id';
    $left_table='v_users';
      $where_temp="business_id=$business_id  AND is_off<2 ";
      //$where_temp='is_off<2';
    $data['list']=$this->User_model->get_act_video_all($select,$where_temp,'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start=0,$page_num=10);
    if(!empty($data['list']))
    {
      foreach($data['list'] as $k => $v){
        if($v['is_off']==1)
        {
          if($v['push_type']==0)
          {
            $data['list'][$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
          }
          else
          {
            $data['list'][$k]['path']=$this->config->item('record_uc_url').$v['video_name'].'.m3u8';
          }
         // $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
            $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
            /*
             * 'olook://onvideo.toapp?guankan_zb&videoinfo<'.$v['videoinfo']?>
             */
            $data['list'][$k]['lb_url']='olook://onvideo.toapp?guankan_lb&videoinfo<'. $data['list'][$k]['videoinfo'];
            if(stristr($_SERVER['HTTP_USER_AGENT'],'android')){
                $data['list'][$k]['lb_url']='olook://videoinfo_lb<'. $data['list'][$k]['videoinfo'];
            }
           // $data['list'][$k]['lb_url']='olook://videoinfo_lb<'. $data['list'][$k]['videoinfo'];
          $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$v['video_id']}";

          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
        }
        else
        {

          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
          //  $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
            $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);

            $data['list'][$k]['video_url']='olook://onvideo.toapp?guankan_zb&videoinfo<'. $data['list'][$k]['videoinfo'];
            if(stristr($_SERVER['HTTP_USER_AGENT'],'android')){
                $data['list'][$k]['video_url']='olook://videoinfo_zb<'. $data['list'][$k]['videoinfo'];
            }
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
      }

    }
    if(!$data['list'])
    {
      $data['list']=0;
    }

    if($this->input->get('test'))
    {
      echo '<pre>';print_r($data);exit();
    }
    $data['share']['share_url']=base_url("bussell/business_info_app?business_id=$business_id");
    $data['share']['title']=$data['business_name'];
    $data['share']['image']=$data['image'];
    $data['share']['desc']="坐享其成上的一个商铺。";
    $data['json_share']=json_encode($data['share']);

      $data['share_out']='olook://shareinfo<'.$data['json_share'];

      if((!isset($_SERVER['HTTP_REFERER']) OR is_null($_SERVER['HTTP_REFERER'])) AND $this->et==TRUE)
      {
          $data['back_url']='olook://identify.toapp>menu';
      }else{
          $data['back_url']="javascript:history.go(-1)";

      }



    $data['is_fav']=FALSE;
    $where="shop_id=$data[business_id] AND user_id=$per_user_id AND type='2'";
    $fav=$this->User_model->get_select_one('act_id',$where,'v_favorite');
    if($fav!==0)
    {
      $data['is_fav']=TRUE;
    }
      //print_r($_SESSION);exit();
    $this->load->view('bussell/shop_info_app',$data);
  }
//app端优惠买单界面
  public function app_shop_buy()
  {

    $business_id=$this->input->get('business_id');


    if(!$business_id)
    {
      return false;
    }
    $_SESSION['business_id']=$business_id;
    $data=$this->User_model->get_select_one('business_name,discount,discount_cri,business_id,currency,currency_name,user_id',array('business_id'=>$business_id),'v_wx_business');
    if(mb_strlen($data['business_name'])>13){
      $data['business_name']=mb_substr($data['business_name'],0,13).'…';
    }
    if($this->input->get('call',TRUE)){
      $data['back_url']='olook://identify.toapp>menu';
    }else{
      $data['back_url']='javascript:void(0);';
    }
    $data['custer_user']='-1';
    if(isset($_SESSION['user_id'])){
      $data['custer_user']=$_SESSION['user_id'];
    }
    $data['no_buy']=FALSE;
    if($data['custet_user']==$data['user_id']){
      $data['no_buy']=TRUE;
    }
    switch ($data['currency'])
    {
      case 'CNY':
        $data['cur_tag']='¥';
        break;
      case 'THB':
        $data['cur_tag']='฿';
        break;
      case 'KER':
        $data['cur_tag']='₩';
        break;
      case 'JPY':
        $data['cur_tag']='¥';
        break;
      case 'AUD':
        $data['cur_tag']='$';
        break;
      case 'TWD':
        $data['cur_tag']='NT$';
        break;
      case 'USD':
        $data['cur_tag']='$';
        break;
      default:
        $data['cur_tag']='¥';
    }
    if($data['currency']!='CNY'){
      $data['true_curr']=$this->common->get_true_curr($data['currency'],'CNY');
    }else{
      $data['true_curr']=1;
    }

      $this->load->view('bussell/app_shopbuy',$data);
  }

  public function i_notice()
  {
    $this->load->view('bussell/explanation');
  }

  //微信端优惠买单界面
  public function wx_shop_buy()
  {
    /*if(!isset($_SESSION['user_id'])){
      redirect('bussell/wx_shop_list');
    }*/
    $url=base_url("bussell/wx_shop_list");
    $user_id=$data['user_id']=$this->get_wx_userid($url);
    $business_id=$this->input->get('business_id');
    if(!$business_id)
    {
      return false;
    }
    $_SESSION['business_id']=$business_id;
    $data=$this->User_model->get_select_one('business_name,discount,discount_cri,business_id,currency,currency_name',array('business_id'=>$business_id),'v_wx_business');

    if(mb_strlen($data['business_name'])>13){
      $data['business_name']=mb_substr($data['business_name'],0,13).'…';
    }
    switch ($data['currency'])
    {
      case 'CNY':
            $data['cur_tag']='¥';
            break;
      case 'THB':
            $data['cur_tag']='฿';
            break;
      case 'KER':
           $data['cur_tag']='₩';
           break;
      case 'JPY':
          $data['cur_tag']='¥';
           break;
      case 'AUD':
          $data['cur_tag']='$';
          break;
      case 'TWD':
        $data['cur_tag']='NT$';
        break;
      case 'USD':
            $data['cur_tag']='$';
            break;
      default:
            $data['cur_tag']='¥';
    }
    if($data['currency']!='CNY'){
      //print_r($data['currency']);
      $data['true_curr']=$this->common->get_true_curr($data['currency'],'CNY');
    }else{
      $data['true_curr']=1;
    }

    $this->load->view('bussell/wx_shopbuy',$data);
  }
//app  优惠订单生成页面
  public function app_shop_order_sub()
  {

      $data['user_id']=$this->user_id_and_open_id();
      if(! $data['user_id'])
      {
          echo -1;
      }
      else
      {
          $business_id=trim($this->input->post('business_id',TRUE));
          $discount_price=trim($this->input->post('discount_price',TRUE));
          $rmb=trim($this->input->post('rmb',TRUE));

          $nodiscount_price=trim($this->input->post('nodiscount_price',TRUE));
          if(!$nodiscount_price){
              $nodiscount_price=0;
          }
          $discount_price_curr=trim($this->input->post('discount_price_curr',TRUE));
          $nodiscount_price_curr=trim($this->input->post('nodiscount_price_curr',TRUE));
          if(!$nodiscount_price_curr){
              $nodiscount_price_curr=0;
          }
          if(!$discount_price_curr){
              $discount_price_curr=0;
          }
          if(!$discount_price){
              $discount_price=0;
          }
          if(!$nodiscount_price){
              $nodiscount_price=0;
          }
          if($discount_price<0 || $nodiscount_price<0)
          {
              return false;
          }
          if($discount_price==0 && $nodiscount_price==0){
              return false;
          }

          $rs=$this->User_model->get_select_one('p_user_id,user_id,business_name,discount,currency',array('business_id'=>$business_id),'v_wx_business');
          if($rs['user_id']==0){
              $rs['user_id']=$rs['p_user_id'];
          }
          $user_info=$this->User_model->get_select_one('user_name',array('user_id'=>$data['user_id']),'v_users');

          $user_id_buy_name=$user_info['user_name'];
          $user_id_sell_name=$rs['business_name'];
          $order_sn=$this->get_order_sn();
          $order_amount=$discount_price+$nodiscount_price;


          $order_amount_curr=$discount_price_curr+$nodiscount_price_curr;
          $goods_amount_curr=$nodiscount_price_curr*$rs['discount']/10+$nodiscount_price_curr;
          //  $order_amount=sprintf("%.2f",$order_amount)*100;
          //  $order_amount=sprintf("%.2f",$order_amount);
          $goods_amount=$rmb;

          $data=array(
              'order_sn'=>$order_sn,
              'user_id_buy'=>$data['user_id'],
              'user_id_buy_name'=>$user_id_buy_name,
              'user_id_sell'=>$rs['user_id'],
              'user_id_sell_name'=>$user_id_sell_name,
              'add_time'=>time(),
              'goods_amount'=>$goods_amount,
              'order_amount'=>$order_amount,
              'discount'=>$rs['discount'],
              'discount_money'=>$discount_price,
              'nodiscount_money'=>$nodiscount_price,
              'goods_amount_curr'=>$goods_amount_curr,
              'order_amount_curr'=>$order_amount_curr,
              'discount_money_curr'=>$discount_price_curr,
              'nodiscount_money_curr'=>$nodiscount_price_curr,
              'currency'=>$rs['currency'],
              'goods_all_num'=>1,
              'from'=>'3',
              'business_id'=>$business_id
          );

          $order_id=$this->User_model->user_insert($table='v_order_info',$data);

          $json=array();
          $json['order_id']=$order_id;
          $json['user_id_buy']=$data['user_id_buy'];

          //$json['prod']=base_url("bussell/pay_succeed?type=1&order_id={$order_id}");
          $json['prod']=base_url("bussell/business_info_app?business_id={$business_id}");
          //  $json['prod']=1;


          if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
          {
              $json['number']=1;
              $json['order_sn']=$order_sn;
              $json['ship_url']=base_url('order/order_list_unshipbuy');
          }
          else
          {
              $json['num']=1;
          }
          $json['amount']=$goods_amount;
          $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
          $json['productName']='优惠买单';

          $json=json_encode($json);

          echo $json;
      }




  }

//wx 优惠订单生成页面
  public function wx_shop_order_sub()
  {
    $url=base_url("bussell/wx_shop_list");
    $user_id_buy_fromwx=$data['user_id']=$this->get_wx_userid($url);


    $business_id=trim($this->input->post('business_id',TRUE));
    $discount_price=trim($this->input->post('discount_price',TRUE));
    $rmb=trim($this->input->post('rmb',TRUE));

    $nodiscount_price=trim($this->input->post('nodiscount_price',TRUE));
    if(!$nodiscount_price){
      $nodiscount_price=0;
    }
    $discount_price_curr=trim($this->input->post('discount_price_curr',TRUE));
    $nodiscount_price_curr=trim($this->input->post('nodiscount_price_curr',TRUE));
    if(!$nodiscount_price_curr){
      $nodiscount_price_curr=0;
    }
    if(!$discount_price_curr){
      $discount_price_curr=0;
    }
    if(!$discount_price){
      $discount_price=0;
    }
    if(!$nodiscount_price){
      $nodiscount_price=0;
    }
    if($discount_price<0 || $nodiscount_price<0)
    {
      return false;
    }
    if($discount_price==0 && $nodiscount_price==0){
      return false;
    }
    $rs=$this->User_model->get_select_one('p_user_id,user_id,business_name,discount,currency',array('business_id'=>$business_id),'v_wx_business');
      if($rs['user_id']==0){
          $rs['user_id']=$rs['p_user_id'];
      }
    $user_id_buy_name_fromwx=$this->User_model->get_username($user_id_buy_fromwx,$table='v_wx_users');
    $user_id_sell_name=$rs['business_name'];
    $order_sn=$this->get_order_sn();
    $order_amount=$discount_price+$nodiscount_price;
    //$goods_amount=$discount_price*$rs['discount']/10+$nodiscount_price;

    $order_amount_curr=$discount_price_curr+$nodiscount_price_curr;
    $goods_amount_curr=$nodiscount_price_curr*$rs['discount']/10+$nodiscount_price_curr;
   // $order_amount=sprintf("%.2f",$order_amount)*100;
    $goods_amount=$rmb;

    $data=array(
        'order_sn'=>$order_sn,
        'user_id_buy_fromwx'=>$user_id_buy_fromwx,
        'user_id_buy_name_fromwx'=>$user_id_buy_name_fromwx,
        'user_id_sell'=>$rs['user_id'],
        'user_id_sell_name'=>$user_id_sell_name,
        'add_time'=>time(),
        'goods_amount'=>$goods_amount,
        'order_amount'=>$order_amount,
        'discount'=>$rs['discount'],
        'discount_money'=>$discount_price,
        'nodiscount_money'=>$nodiscount_price,
        'goods_amount_curr'=>$goods_amount_curr,
        'order_amount_curr'=>$order_amount_curr,
        'discount_money_curr'=>$discount_price_curr,
        'nodiscount_money_curr'=>$nodiscount_price_curr,
        'currency'=>$rs['currency'],
        'from'=>'2',
        'business_id'=>$business_id,
    );
   //print_r(floatval($goods_amount)*100);exit();
    $order_id=$this->User_model->user_insert($table='v_order_info',$data);
    include_once("./application/third_party/wxpay/WxPay.php");
    $fee=floatval($goods_amount)*100;
    $unifiedOrder = new UnifiedOrder_pub();
    $jsApi = new JsApi_pub();

    if(isset($_SESSION['openidfromwx']))
    {
      $openid=$_SESSION['openidfromwx'];
    }
    else
    {
      $url=base_url("bussell/wx_shop_list");
      $this->get_wx_userid($url);
      $openid=$_SESSION['openidfromwx'];
    }


    $unifiedOrder->setParameter("openid","$openid");//商品描述
    $unifiedOrder->setParameter("body","优惠买单");//商品描述

    $out_trade_no =$order_sn;
    $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
    $unifiedOrder->setParameter("total_fee","$fee");//总金额
    $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
    //  echo $out_trade_no;exit();
    $prepay_id = $unifiedOrder->getPrepayId();
   // echo $prepay_id;exit();
    $jsApi->setPrepayId($prepay_id);
    $jsApiParameters = $jsApi->getParameters();
    $_SESSION['jsApiParameters']=$jsApiParameters;

    $prr_temp=json_decode($jsApiParameters,TRUE);
    $parm=array(
        'appId'=>$prr_temp['appId'],
        'nonceStr'=>$prr_temp['nonceStr'],
        'timeStamp'=>$prr_temp['timeStamp'],
        'signType'=>$prr_temp['signType'],
        'package'=>$prr_temp['package'],
        'paySign'=>$prr_temp['paySign']
    );
    $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
    $jsApiParameters=json_encode($jsApiParameters);
    $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
    echo json_encode($arr);
  }

//app端 商户活动列表
  public function bus_list_app($act_id=59)
  {

    $data['act_id']=$act_id;
    $data['down']=$this->down;
    $rs=$this->User_model->get_select_one($select='title',array('act_id'=>$act_id),'v_activity_father');
    $data['banner_title']=$rs['title'];
    $data['no_act']=TRUE;
    $data['act_me']=array();
    $data['user_id']='-1';
    $data['canapply']=TRUE;
    if(isset($_COOKIE['user_id']))
    {
      $data['user_id']=$_SESSION['user_id']=$_COOKIE['user_id'];
    }
    elseif(isset($_COOKIE['olook']))
    {
      $arr_olook=explode('-',$_COOKIE['olook']);
      $data['user_id']=$_SESSION['user_id']=$arr_olook[0];
    }
    elseif(isset($_SESSION['user_id']))
    {
      $data['user_id']=$_SESSION['user_id'];
    }
//测试
    //echo '<pre>';print_r($_SESSION);exit();
    //$data['user_id']=0;
    $where="user_id=$data[user_id] AND special='1' AND act_status<'3'";
    $canapply=$this->User_model->get_select_one('act_id',$where,'v_activity_father');
    if($data['user_id']==1077){
    //echo '<pre>';print_r($canapply);exit();
    }
    if($canapply!==0){
      $data['canapply']=FALSE;
    }
    if($data['user_id']==2011){
      //print_r($canapply);exit();
    }
    $canapply=$this->User_model->get_select_one('user_id',array('user_id'=>$data['user_id'],'is_merchant'=>'1'),'v_users');
    if($canapply===0){
      $data['canapply']=FALSE;
    }
    $where="special  ='1' AND is_show ='1' AND pid='$act_id' AND act_status='2' AND is_temp='0'";
    if($data['user_id']==1960){
    // return false;
      //$where="special  ='1' AND act_status='2' AND pid='$act_id' AND is_temp='0'";
    }
    $data['shop_list']=$this->User_model->get_select_all(
        $select='act_id,pid,user_id,cor_name,title,logo_image,is_show,is_set,displayorder,special',
        $where,'displayorder','ASC','v_activity_father');
    if($data['shop_list']===false){
      $data['shop_list']=array();
    }
    // echo "<pre>";print_r($data);exit();
    if(!empty($data['shop_list']))
    {
      //$data['shop_list']['children_list']=array();
      foreach($data['shop_list'] as $k=>$v)
      {
        $data['shop_list'][$k]['children_list']=$this->User_model->get_select_all('title,act_id,banner_image',
            "is_show='1' AND pid= '$v[act_id]' AND is_temp='0' AND act_status='2' ",'displayorder','ASC','v_activity_children');
        //echo $this->db->last_query();

        if($data['shop_list'][$k]['children_list']===false)
        {
          unset($data['shop_list'][$k]);
          continue;
        }
        else
        {
          $data['shop_list'][$k]['first_act_id']= $data['shop_list'][$k]['children_list'][0]['act_id'];
          $data['shop_list'][$k]['first_banner']= $data['shop_list'][$k]['children_list'][0]['banner_image'];
          $data['shop_list'][$k]['first_title']= $data['shop_list'][$k]['children_list'][0]['title'];
        }
        if($v['user_id']==$data['user_id'])
        {
          $data['no_act']=FALSE;
          $data['act_me'][$k]=$data['shop_list'][$k];
          unset($data['shop_list'][$k]);
        }
      }
    }
    if($this->input->get('test'))
    {
      echo "<pre>";print_r($data);//exit();
      $this->output->enable_profiler(TRUE);
    }
    //
    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney') && stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {

      $this->load->view('bussell/bus_activity_an',$data);

    }
    elseif(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney') && stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
    {
      $this->load->view('bussell/bus_activity_ios',$data);
    }
    else
    {
      $this->load->view('bussell/bus_activity_h5',$data);
      //$this->load->view('bussell/bus_activity_ios',$data);
    }
  }


  //订单页面获取支付信息
  public function get_pay_info()
  {
    $order_sn=trim($this->input->post_get('order_sn',true));
    $rs=$this->User_model->get_select_one('goods_amount,user_id_buy_fromwx,appId,nonceStr,timeStamp,signType,package,paySign',array('order_sn'=>$order_sn),'v_order_info');


    // $row=$this->User_model->get_select_one('openid',array('user_id'=>$rs['user_id_buy_fromwx']),'v_wx_users');

    $jsApiParameters['appId']=$rs['appId'];
    $jsApiParameters['nonceStr']=$rs['nonceStr'];
    $jsApiParameters['timeStamp']=$rs['timeStamp'];
    $jsApiParameters['signType']=$rs['signType'];
    $jsApiParameters['package']=$rs['package'];
    $jsApiParameters['paySign']=$rs['paySign'];
    //$jsApiParameters1=json_decode($_SESSION['jsApiParameters'],true) ;
    echo json_encode($jsApiParameters);
    //echo json_encode($jsApiParameters),'<br>',json_encode($jsApiParameters1);

  }

  //商户列表
  public function bus_list_wx($act_id=65)
  {
    $url=base_url("bussell/bus_list_wx");
    $data['user_id']=$this->get_wx_userid($url);
    $row=$this->User_model->get_select_all($select='act_id',array('pid'=>'0','special'=>'1'),$order_title='displayorder',$order='ASC',$table='v_activity_father');
    $row_act_id=array();
    if(is_array($row))
    {
      foreach($row as $k=>$v)
      {
        $row_act_id[]=$v['act_id'];
      }
    }
    $row_act_id=implode(',',$row_act_id);
    //echo $row_act_id;exit();
  //  $data['user_id']='0';
    $_SESSION['zid']=$data['act_id']=$act_id;
    $data['down']=$this->down;
    $rs=$this->User_model->get_select_one($select='title',array('act_id'=>$act_id),'v_activity_father');
    $data['banner_title']=$rs['title'];
    $data['no_act']=TRUE;
    $data['act_me']=array();
    $data['shop_list']=$this->User_model->get_select_all(
        $select='act_id,pid,user_id,cor_name,title,logo_image,is_show,is_set,displayorder,special',
        "special  ='1' AND is_show ='1' AND (pid='$act_id' OR pid IN ($row_act_id)) AND act_status='2' AND is_temp='0'",'displayorder','ASC','v_activity_father');

    if($data['shop_list']===false)
    {
      $data['shop_list']=array();
    }
    if(!empty($data['shop_list']))
    {
      //$data['shop_list']['children_list']=array();
      foreach($data['shop_list'] as $k=>$v){
        $data['shop_list'][$k]['children_list']=$this->User_model->get_select_all('title,act_id,banner_image',
            "is_show='1' AND pid= '$v[act_id]' AND is_temp='0' AND act_status='2' ",'displayorder','ASC','v_activity_children');
        //echo $this->db->last_query();
        if($data['shop_list'][$k]['children_list']===false)
        {
          unset($data['shop_list'][$k]);
          continue;
        }
        else
        {
          $data['shop_list'][$k]['first_act_id']= $data['shop_list'][$k]['children_list'][0]['act_id'];
          $data['shop_list'][$k]['first_banner']= $data['shop_list'][$k]['children_list'][0]['banner_image'];
          $data['shop_list'][$k]['first_title']= $data['shop_list'][$k]['children_list'][0]['title'];
        }

      }
    }
    if($this->input->get('test'))
    {
      echo "<pre>";print_r($data);exit();
    }
    //
    // $this->load->view('bussell/bus_activity_ios',$data);
    $this->load->view('bussell/bus_activity',$data);

  }



//商户申请官方后台//废弃
  public function children_activity_log1($gran_id='59',$page=1)
  {
    $data['gran_id']=$gran_id;
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $data['type']=trim($this->input->get('type',true));
    $act_status= $data['act_status']= $this->input->get('act_status',true);
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if(!$act_status)
    {
      $act_status= $data['act_status']='2';
      $data['is_show']=$is_show='1';
    }

    $where="v_activity_father.special='1' AND v_activity_father.pid='$gran_id'";
    if($act_status ==2)
    {
      $where.=" AND v_activity_children.is_temp ='0'";
    }
    if($data['time1'])
    {
      $where.=" AND v_activity_children.act_time_adv >=$data[time1]";
    }
    if($data['time2'])
    {
      $data['time2']+=86400;
      $where.="  AND v_activity_children.act_time_adv <=$data[time2]";
    }
    if($data['title'])
    {
      if($data['type']==3)
      {
        $where.= " AND v_activity_father.cor_name LIKE '%$data[title]%' ";
      }
      elseif($data['type']==4)
      {
        $where.= " AND v_activity_children.title LIKE '%$data[title]%'";
      }
    }
    else
    {
      $data['type']=0;
    }
    if($is_show)
    {
      $where.=" AND v_activity_children.is_show='$is_show'";
    }
    $where.="  AND v_activity_children.act_status= '$act_status'";
    $page_num =100;
    $data['now_page'] = $page;

    $count = $this->User_model->get_temp_activity_count($where);
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list']=$this->User_model->get_temp_activity($where,$start,$page_num);

    if($act_status==9)
    {
      $rs=$this->User_model->get_select_all($select='pid',
          $where='1=1',$order_title='act_id',
          $order='ASC',$table='v_activity_children');
      $p_arr=array();
      foreach($rs as $k=>$v){
        $p_arr[]=$v['pid'];
      }
      $p_arr=array_unique($p_arr);
      $data['list']=$this->User_model->get_xian_activity(array('act_id',$p_arr),array('special'=>'1','pid>'=>'0'));
      // echo $this->db->last_query();
    }
    // $data['time2']=strtotime($this->input->get('time2'));
    $data['list_now']=array();
    foreach($data['list'] as $k=>$v){
      $father_act_id=$v['father_act_id'];

      if($act_status!=9)
      {
        $temp_arr=array('children_act_id'=>$v['children_act_id'],'title'=>$v['title']);
      }
      else
      {
        $temp_arr=array();
      }
      $data['list_now'][$father_act_id]['cor_name']=$v['cor_name'];
      $data['list_now'][$father_act_id]['logo_image']=$v['logo_image'];
      $data['list_now'][$father_act_id]['father_act_id']=$father_act_id;
      $data['list_now'][$father_act_id]['children_list'][]=$temp_arr;
    }
    if($this->input->get('test'))
    {
      echo "<pre>";print_r($data);exit();
    }
    if($act_status==9){
      // echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/bus_activity_home',$data);
  }


  //商户活动    （废弃）
  public function business_home($gran_id='65',$page=1)
  {
    $data['gran_id']=$gran_id;
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $data['type']=trim($this->input->get('type',true));
    $act_status= $data['act_status']= $this->input->get('act_status',true);
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if(!$act_status)
    {
      $act_status= $data['act_status']='2';
      $data['is_show']=$is_show='1';
    }
    //echo $is_show;
    $where=" special='1' AND pid='$gran_id'";
    if($act_status ==2 && $is_show=='1')
    {
      $where.=" AND is_temp ='0'";
    }
    if($data['time1'])
    {
      $where.=" AND add_time >=$data[time1]";
    }
    if($data['time2'])
    {
      $data['time2']+=86400;
      $where.="  AND add_time <=$data[time2]";
    }
    if($data['title'])
    {
      $where.= " AND cor_name LIKE '%$data[title]%' ";
    }
    else
    {
      $data['type']=0;
    }
    if($is_show)
    {
      $where.=" AND is_show='$is_show'";
    }
    $where.="  AND act_status= '$act_status'";
    $page_num =100;
    $data['now_page'] = $page;

    $count = $this->User_model->get_count($where, $table='v_activity_father');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list']=$this->User_model->get_select_more($select='*', $where, $start, $page_num, 'displayorder', $order='ASC',
        $table='v_activity_father');

    if($act_status==9)
    {
      $rs=$this->User_model->get_select_all($select='pid',
          $where='1=1',$order_title='act_id',
          $order='ASC',$table='v_activity_children');
      $p_arr=array();
      foreach($rs as $k=>$v){
        $p_arr[]=$v['pid'];
      }
      $p_arr=array_unique($p_arr);
      $data['list']=$this->User_model->get_xian_activity(array('act_id',$p_arr),array('special'=>'1','pid>'=>'0'));
      // echo $this->db->last_query();
    }
    $data['time2']=strtotime($this->input->get('time2'));
    if($data['list']===0)
    {
      $data['list']=array();
    }
    if($this->input->get('test')){
      echo $this->db->last_query();
      echo "<pre>";print_r($data);exit();
    }
    if($act_status==9){
      // echo "<pre>";print_r($data);exit();

    }
    $this->load->view('bussell/business_home',$data);
  }
  //商户子活动（废弃）
  public function business_children($page=1){
    $data['pid']=$pid=$this->input->get('pid',true);
    $data['pinfo']=$this->User_model->get_select_one('*',array('act_id'=>$pid),'v_activity_father');
    $data['count_url']=$this->count_url;
    $data['title'] = trim($this->input->get('title',true));
    $data['time1']=strtotime($this->input->get('time1',true));
    $data['time2']=strtotime($this->input->get('time2',true));
    $data['type']=trim($this->input->get('type',true));
    $act_status= $data['act_status']= $this->input->get('act_status',true);
    $is_show= $data['is_show']= $this->input->get('is_show',true);
    if(!$act_status)
    {
      $act_status= $data['act_status']='2';
      $data['is_show']=$is_show='1';
    }

    $where="pid=$pid";
    if($act_status ==2)
    {
      $where.=" AND is_temp ='0'";
    }
    if($data['time1'])
    {
      $where.=" AND add_time >=$data[time1]";
    }
    if($data['time2'])
    {
      $data['time2']+=86400;
      $where.="  AND add_time <=$data[time2]";
    }
    if($data['title'])
    {
      $where.= " AND title LIKE '%$data[title]%'";
    }
    else
    {
      $data['type']=0;
    }
    if($is_show)
    {
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
    $data['list']=$this->User_model->get_select_more($select='*', $where, $start, $page_num, 'displayorder', $order='ASC',
        $table='v_activity_children');
    // $data['list']=$this->User_model->get_temp_activity($where,$start,$page_num);

    if($act_status==9)
    {
      $rs=$this->User_model->get_select_all($select='pid',
          $where='1=1',$order_title='act_id',
          $order='ASC',$table='v_activity_children');
      $p_arr=array();
      foreach($rs as $k=>$v){
        $p_arr[]=$v['pid'];
      }
      $p_arr=array_unique($p_arr);
      $data['list']=$this->User_model->get_xian_activity(array('act_id',$p_arr),array('special'=>'1','pid>'=>'0'));
      // echo $this->db->last_query();
    }
    $data['time2']=strtotime($this->input->get('time2'));
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    if($act_status==9){
      // echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/business_children',$data);
  }

  //子活动排序提交（废弃）
  public function children_order_sub()
  {
    $act_id=$this->input->post('act_id',true);
    $order=$this->input->post('order',true);
    $this->User_model->update_one(array('act_id'=>$act_id),array('displayorder'=>$order),'v_activity_children');
   echo 1;
  }

  //上架商户子活动（废弃）
  public function up_activity($act_id)
  {
    // $act_id=$this->input->get('act_id',true);
    $pid=$this->input->get('pid',true);
    //$where,$data=array(),$table='v_activity')
    $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'1'),'v_activity_children');
    redirect(base_url("bussell/business_children?pid={$pid}"));
  }
  //下架活动（废弃）
  public function down_activity($act_id)
  {
    //$act_id=$this->input->get('act_id',true);
    $pid=$this->input->get('pid',true);
    $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_activity_children');
    // echo $this->db->last_query();exit();
    redirect(base_url("bussell/business_children?pid={$pid}"));
  }

  //商户申请企业编辑（废弃）
  public function cor_edit()
  {
    $data['act_id']=$act_id=$this->input->get('act_id',true);
    $data['list']=$this->User_model->get_select_one($select='user_id,cor_name,mobile,email,logo_image,act_status,is_show,is_temp,act_status,users',array('act_id'=>$act_id),$table='v_activity_father');
    // $data=array();
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/cor_edit',$data);
  }

//（废弃）
  public function cor_add()
  {
    $data['pid']=$pid=$this->input->get('act_id',true);
    //$data['list']=array();
    // $data=array();
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/cor_edit',$data);
  }

  //（废弃）
  public function cor_insert()
  {

    $cor_name=trim($this->input->post('cor_name',TRUE));
    $mobile=trim($this->input->post('mobile',TRUE));
    $email=trim($this->input->post('email',TRUE));
    $pid=trim($this->input->post('pid',TRUE));
    $users=trim($this->input->post('users',TRUE));
    $user_id=trim($this->input->post('user_id',TRUE));
    $data=array('cor_name'=>$cor_name,'mobile'=>$mobile,'email'=>$email,'pid'=>$pid,'user_id'=>$user_id,'users'=>$users);

    if($_FILES['logo']['error']==0)
    {
      $logo_image=$this->upload_image('logo','bus_act');
      $data['logo_image']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
    }
    $data['special']='1';
    $data['act_status']='1';
    $act_id=$this->User_model->user_insert($table='v_activity_father',$data);
    $this->put_admin_log("添加企业{$act_id}");

    // if($this->input->get('test')){
    // print_r($data);
    redirect(base_url("bussell/cor_edit?act_id={$act_id}"));
  }
//（废弃）
  public function cor_sub()
  {
    // $this->size_validate('logo',40960,2);
    $cor_name=trim($this->input->post('cor_name',TRUE));
    $mobile=trim($this->input->post('mobile',TRUE));
    $email=trim($this->input->post('email',TRUE));
    $act_id=trim($this->input->post('act_id',TRUE));
    $user_id=trim($this->input->post('user_id',TRUE));
    $users=trim($this->input->post('users',TRUE));
    $data=array('cor_name'=>$cor_name,'mobile'=>$mobile,'email'=>$email,'user_id'=>$user_id,'users'=>$users);
    $row=$this->User_model->get_select_one('logo_image,user_id',array('act_id'=>$act_id),'v_activity_father');
    if($_FILES['logo']['error']==0)
    {
      $logo_image=$this->upload_image('logo','bus_act'.$act_id);
      $logo_image=$this->imagecropper($logo_image,$key1='logo',$key2='time',$target_width='100',$target_height='100');
      $data['logo_image']=$logo_image;
    }
    else
    {
      $data['logo_image']=$row['logo_image'];
    }
    $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
    $this->put_admin_log("修改商户活动企业信息{$act_id}");
    redirect(base_url("bussell/cor_edit?act_id={$act_id}"));
  }
  //活动删除（废弃）
  public function act_del($act_id)
  {
    $pid=$this->input->get('pid',true);
    //business_children
    $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'4'),$table='v_activity_children');
    redirect(base_url("bussell/business_children?pid={$pid}"));
  }

  //企业删除（废弃）
  public function cor_del()
  {
    $act_id=$this->input->get('act_id',true);
    $type=$this->input->get('type',true);
    $act_status=$this->input->get('act_status',true);
    $zid=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_father');
    $zid=$zid['pid'];
    if($type==1)
    {
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_activity_father');
      $this->put_admin_log("撤下企业{$act_id}");
    }
    elseif($type==2)
    {
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'0','act_status'=>'4'),$table='v_activity_father');
      $this->put_admin_log("删除企业{$act_id}");
    }
    redirect(base_url("bussell/business_home/$zid"));
  }
//cor_ok or cor_no（废弃）
  public function cor_ok_no()
  {
    $act_id=$this->input->post_get('act_id',true);
    $val=$this->input->post_get('val',true);
    if($val==1)
    {
      //更改当前企业信息，可见，审核通过
      $data=array('is_show'=>'1','act_status'=>'2');
      $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
      //取出该条记录需要信息
      $rs=$this->User_model->get_select_one('pid,user_id,cor_name,mobile,email,title,logo_image,is_show,act_status,displayorder,add_time,is_set,special,users',
          array('act_id'=>$act_id),'v_activity_father');

      //查询同名id下是否有正式信息
      $row=$this->User_model->get_select_one('act_vf_id',array('act_tf_id'=>$act_id),'v_temp_real');
      if($row===0)
      {
        $act_vf_id=$this->User_model->user_insert($table='v_activity_father',$rs);
        $this->User_model->user_insert($table='v_temp_real',array('act_vf_id'=>$act_vf_id,'act_tf_id'=>$act_id));
        $this->User_model->update_one(array('pid'=>$act_id),array('pid'=>$act_vf_id),$table='v_activity_children');

      }
      else
      {
        $act_vf_id=$row['act_vf_id'];
        $this->User_model->update_one(array('act_id'=>$act_vf_id),$rs,$table='v_activity_father');
        $this->User_model->update_one(array('pid'=>$act_id),array('pid'=>$act_vf_id),$table='v_activity_children');
      }
      echo 1;
    }
    else
    {
      //审核不通过，即该条记录不显示，审核状态不通过
      $data=array('is_show'=>'0','act_status'=>'3','is_temp'=>'1');
      $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_father');
      echo 1;
    }
  }

  //后台商户子活动审核通过（废弃）
  public function bus_ok_no()
  {
    $act_id=$this->input->get('act_id',true);
    $val=$this->input->get('val',true);
    //审核通过
    if($val==1)
    {
      //抽取当前记录信息
      $data=$this->User_model->get_select_one('user_id,pid,banner_image,add_time,users,is_show,content_text,content,day_list,request,displayorder,title',
          array('act_id'=>$act_id),'v_activity_children');
      //如果当前记录为正式记录，即执行正式记录操作
      $rs=$this->User_model->get_select_one('act_vc_id',array('act_tc_id'=>$act_id),'v_temp_real');
      if($rs===0)
      {
        $data['is_temp']='0';
        $data['act_status']='2';
        $act_vc_id= $this->User_model->user_insert($table='v_activity_children',$data);
        $this->User_model->user_insert($table='v_temp_real',array('act_tc_id'=>$act_id,'act_vc_id'=>$act_vc_id));
        $good=$this->User_model->get_select_one('low,goods_name,goods_number,shop_price,add_time,is_show,dateto,pricehas,priceno,pricecom',array('act_id'=>$act_id),'v_goods');
        $good['act_id']=$act_vc_id;
        $this->User_model->user_insert($table='v_goods',$good);
      }
      else
      {
        $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'2'),$table='v_activity_children');
        if($data['displayorder']==1)
        {
          //除正式记录外，其余记录更新
          $act_vc_id=$rs['act_vc_id'];
          $this->User_model->update_one("pid=$data[pid] AND act_id != $act_vc_id",array('displayorder'=>'99'),$table='v_activity_children');
        }


        $data['is_temp']='0';
        $data['act_status']='2';

        $this->User_model->update_one(array('act_id'=>$rs['act_vc_id']),$data,$table='v_activity_children');


        $good=$this->User_model->get_select_one('low,goods_name,goods_number,shop_price,add_time,is_show,dateto,pricehas,priceno,pricecom',array('act_id'=>$act_id),'v_goods');
        // echo "<pre>";print_r($good);
        $count=$this->User_model->get_count(array('act_id'=>$rs['act_vc_id']), $table='v_goods');
        // echo "<pre>";print_r($count);
        if($count['count']==0)
        {
          $good['act_id']=$rs['act_vc_id'];
          // echo "<pre>";print_r($good);
          if($good!==0)
          {
            $this->User_model->user_insert($table='v_goods',$good);
          }

        }
        else
        {
          if($good!==0)
          {
            $this->User_model->update_one(array('act_id'=>$rs['act_vc_id']),$good,$table='v_goods');
          }
          else
          {
            $this->User_model->update_one(array('act_id'=>$rs['act_vc_id']),array('is_show'=>'2'),$table='v_goods');
          }
        }
      }

      echo '1';
      //审核不通过
    }
    else
    {
      //当前记录状态更新
      $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'3'),$table='v_activity_children');
      echo '1';
    }
  }
  //编辑商户提交的商户子活动页面（废弃）
  public function bus_children_activity_edit($act_id)
  {
    $data['act_id']=$act_id;
    $data['info']=$this->User_model->get_select_one('act_id,user_id,pid,users,
    user_name,title,banner_image,content_text,content,is_show,displayorder,start_time,end_time,day_list,request',
        array('act_id'=>$data['act_id']),'v_activity_children');
    if(trim($data['info']['content_text'])=='')
    {
      $data['info']['content_text']=$data['info']['content'];
    }
    $data['pid']=$data['info']['pid'];

    $data['day']=json_decode($data['info']['day_list'],true);

    $data['goods']=$this->User_model->get_select_all('goods_id,goods_name,goods_number,dateto,pricehas,priceno,pricecom,is_show,shop_price,act_id,low',array('act_id'=>$data['act_id']),'goods_id', 'ASC','v_goods');
    if($this->input->get('test')){
      echo $this->db->last_query();

      echo "<pre>";print_r($data);
      exit();
    }
    $this->load->view('bussell/bus_children_activity_edit',$data);
  }
//（废弃）
  public function bus_children_activity_add()
  {
    $data['pid']=$act_id=$this->input->get('pid',true);
    $rs=$this->User_model->get_select_one('pid',array('act_id'=>$act_id),'v_activity_father');
    $data['zid']=$rs['pid'];
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/bus_children_activity_edit',$data);
  }
//后台商户子活动修改提交（废弃）
  public function bus_children_activity_sub()
  {
    // $this->size_validate('banner',61440,2);
    $data['act_id']=$act_id=$this->input->post('act_id',true);

    $rs=$this->User_model->get_select_one('pid,is_temp',array('act_id'=>$data['act_id']),'v_activity_children');

    $rs2=$this->User_model->get_select_one('user_id',array('act_id'=>$rs['pid']),'v_activity_father');
    $user_id=$rs2['user_id'];
    $data['title']=$this->input->post('title',true);


    $data['content_text']=$this->input->post('content_text',true);
    $data['content_text'] = str_replace("\n","<br>", $data['content_text']);
    //  $data['content']=trim($this->input->post('content',false));
    $data['content']= $data['content_text'];

    $data['users']=$this->input->post('users',true);

    $day=$this->input->post('day',true);
    $new_day=$day;
    foreach($day as $k=>$v){
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);
    $data['day_list']=json_encode($new_day);

    $ed_check=trim($this->input->post('ed_check',true));
    $add_time=time();

    //活动上下架
    $up_down_act=$this->input->post('up_down_act',true);
    //商品上下架
    $up_down_goods=$this->input->post('up_down_goods',true);
    if($this->input->post('top'))
    {
      $disorder=1;
    }
    else
    {
      $disorder=99;
    }
    if($up_down_act==0)
    {
      $data['is_show']='2';
    }
    elseif($up_down_act==1)
    {
      $data['is_show']='1';
    }
    if($disorder==1)
    {
      $data['displayorder']='1';
    }
    if($_FILES['banner']['error']==0)
    {
      $banner=$this->upload_image('banner',$user_id.'banner');
      //crop_outci($source_path,$key1='test',$key2='time',$target_width='700',$target_height='300')
      $banner_image=$this->imagecropper($banner,'banner','time',$width='700',$height='300');
      $banner_product=$this->imagecropper($banner,'banner_product','time',$width='100',$height='100');
      $banner_hot=$this->imagecropper($banner,'banner_hot','time',$width='341',$height='230');
      //$banner_image=$this->thumb($banner_image,'banner',$user_id.'banner',$width='702',$height='300');
      $data['banner_image']=$banner_image;
      $data['banner_product']=$banner_product;
      $data['banner_hot']=$banner_hot;
    }


    $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
    $this->put_admin_log("修改活动信息{$act_id}");
    $where="pid=$rs[pid] AND act_id!=$act_id";
    $this->User_model->update_one($where,array('displayorder'=>'99'),$table='v_activity_children');
    $goods_number=$this->input->post('goods_number',true);
    $shop_price=$this->input->post('shop_price',true);

    $dateto=$this->input->post('dateto',true);
    $pricehas=$this->input->post('pricehas',true);
    $priceno=$this->input->post('priceno',true);
    $pricecom=$this->input->post('pricecom',true);



    $low=$this->input->post('low',true);
    if($low!=1)
    {
      $low=0;
    }
    $data=array(
        'goods_name'=> $data['title'],
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
    if($up_down_goods==0)
    {
      $data['is_show']='2';
    }
    elseif($up_down_goods==1)
    {
      $data['is_show']='1';
    }
    if($ed_check==1 && $goods_number>0 && $shop_price>0)
    {
      //$count1=$this->User_model->get_count(array('act_id'=>$act_id,''),'v_goods');
      $count=$this->User_model->get_count(array('act_id'=>$act_id),'v_goods');
      if($count['count']==0)
      {
        $this->User_model->user_insert('v_goods',$data);
      }
      elseif($count['count']>0)
      {
        $this->User_model->update_top_one(array('act_id'=>$act_id),$data,'v_goods');
      }
    }
    if(!$ed_check)
    {
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_goods');
    }
    redirect(base_url("bussell/bus_children_activity_edit/{$act_id}"));
  }

  //后台商户活动增加提交（废弃）
  public function bus_children_activity_insert()
  {
    // $this->size_validate('banner',61440,2);

    $data['pid']=$pid=$this->input->post('pid',true);

    $rs=$this->User_model->get_select_one('user_id,pid',array('act_id'=>$data['pid']),'v_activity_father');
    $data['user_id']=$user_id=$rs['user_id'];
    $zid=$rs['pid'];
    $data['title']=$this->input->post('title',true);

    $data['content_text']=$this->input->post('content_text',true);
    $data['content_text'] = str_replace("\n","<br>", $data['content_text']);
    // $data['content']=$this->input->post('content',true);
    $data['content']= $data['content_text'];

    $data['users']=$this->input->post('users',true);

    $day=$this->input->post('day',true);
    $new_day=$day;
    foreach($day as $k=>$v){
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);

    $data['day_list']=json_encode($new_day);

    $ed_check=trim($this->input->post('ed_check',true));
    $add_time=time();

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
      $data['is_show']='2';
    }elseif($up_down_act==1){
      $data['is_show']='1';
    }
    if($disorder==1)
    {
      $data['displayorder']='1';
    }

    if($_FILES['banner']['error']==0)
    {
      $banner=$this->upload_image('banner',$user_id.'banner');
      //crop_outci($source_path,$key1='test',$key2='time',$target_width='700',$target_height='300')
      $banner_image=$this->imagecropper($banner,'banner','time',$width='700',$height='300');
      $banner_product=$this->imagecropper($banner,'banner_product','time',$width='100',$height='100');
      $banner_hot=$this->imagecropper($banner,'banner_hot','time',$width='341',$height='230');
      //$banner_image=$this->thumb($banner_image,'banner',$user_id.'banner',$width='702',$height='300');
      $data['banner_image']=$banner_image;
      $data['banner_product']=$banner_product;
      $data['banner_hot']=$banner_hot;
    }
    else
    {
      $data['banner_hot']=$data['banner_product']= $data['banner_image']="./public/newadmin/images/logo.png";
    }


    $data['act_status']='1';
    $data['is_temp']='1';
    $act_id=$this->User_model->user_insert($table='v_activity_children',$data);

    $this->put_admin_log("添加商户活动{$act_id}");
    $goods_number=trim($this->input->post('goods_number',true));
    $shop_price=$this->input->post('shop_price',true);
    $low=$this->input->post('low',true);
    if($low!=1){
      $low=0;
    }

    $dateto=$this->input->post('dateto',true);
    $pricehas=$this->input->post('pricehas',true);
    $priceno=$this->input->post('priceno',true);
    $pricecom=$this->input->post('pricecom',true);

    $data=array(
        'goods_name'=> $data['title'],
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
    redirect(base_url("bussell/bus_children_activity_edit/{$act_id}"));
  }



//app端 商户子活动申请 or 编辑 （废弃）
  public function bus_children_add_info(){

    $data['all']=$this->input->get('all',true);
    //既zid祖父id
    $data['bus_id']=$this->input->get('bus_id',true);
    $data['pid']=$this->input->get('pid',true);
    $data['act_id']=$this->input->get('act_id',true);
    $_SESSION['act_id']= $data['act_id'];
    $data['type']=$this->input->get('type',true);
    //$data=array();
    if($data['type']=='edit'){
      $data['ban_title']='编辑活动';
      if($data['all']=='all'){
        $data['info']=$this->User_model->get_select_one(
            'act_id,user_id,pid,title,act_image,poster_image,is_show,displayorder,start_time,end_time,logo_image,banner_image,content_text',
            array('act_id'=>$data['act_id']),'v_activity_father');
      }else{
        $data['info']=$this->User_model->get_select_one(
            'act_id,user_id,pid,title,banner_image,content_text,is_show,displayorder,start_time,end_time,day_list,content',
            array('act_id'=>$data['act_id']),'v_activity_children');
        if(trim($data['info']['content_text'])==''){
          $data['info']['content_text']=trim($data['info']['content']);
        }else

        $data['day']=json_decode($data['info']['day_list'],true);
        $data['day']=array_filter( $data['day']);
        $data['goods']=$this->User_model->get_select_all('goods_id,goods_name,goods_number,is_show,shop_price,act_id,pricehas,priceno,pricecom,dateto',
            array('act_id'=>$data['act_id']),'goods_id', 'ASC','v_goods');
        if($data['goods']!==FALSE){
          $data['hasgoods']=TRUE;
        }
      }
    }else{
      if($data['all']=='all'){
        $data['ban_title']='申请活动';
      }else{
        $data['ban_title']='添加活动';
      }

    }
    $user_id=$this->user_id_and_open_id();
    if($user_id==1960){
      //echo "<pre>";print_r($data);
    }
    if($this->input->get('test')){
      echo "<pre>";print_r($data);exit();
    }
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    $this->load->view('bussell/add_bus_acti',$data);
  }
//商户活动申请 （废弃）
  public function bus_children_info_insert()
  {
    $user_id=$this->user_id_and_open_id();
    if(!$user_id){
      return FALSE;
    }
    $noceruser=$this->get_nocer();
    // $user_id=$_SESSION['user_id']=1;

    //  echo $user_id;exit();
    $all=trim($this->input->post('all',true));
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

    $day=$this->input->post('day',true);
    $new_day=$day;
    foreach($day as $k=>$v){
      if(stristr($v,'请填写行程描述'))
      {
        unset($new_day[$k]);
      }
    }
    $new_day=array_values($new_day);
    $day=json_encode($new_day);
    $content=trim($this->input->post('content_text',true));
    $ed_check=trim($this->input->post('ed_check',true));

    //$banner_image=$this->upload_image('banner',$user_id.'banner');
   // $banner_image= $this->imagecropper($banner_image,'banner','time',$width='700',$height='300');

    $banner=$this->upload_image('banner',$user_id.'banner');
    //crop_outci($source_path,$key1='test',$key2='time',$target_width='700',$target_height='300')
    $banner_image=$this->imagecropper($banner,'banner','time',$width='700',$height='300');
    $banner_product=$this->imagecropper($banner,'banner_product','time',$width='100',$height='100');
    $banner_hot=$this->imagecropper($banner,'banner_hot','time',$width='341',$height='230');

    $add_time=time();
    if($all==1)
    {
      // $this->size_validate('logo',40960);
      //插入企业信息
      $logo_image=$this->upload_image('logo',$user_id.'logo');
      $data_bus['logo_image']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
      //$data_bus['logo_image']=$logo_image=$this->thumb($logo_image,'logo','time',$width='100',$height='100');

      $data_bus['cor_name']=trim($this->input->post('cor_name',true));
      $data_bus['user_id']=$user_id;
      $data_bus['pid']=$zid;
      $data_bus['act_status']='1';
      //  $data_bus['user_name']=$user_name;
      //$data_bus['title']=trim($this->input->post('title',true));
      $data_bus['special']='1';
      $data_bus['is_temp']='1';
      if(in_array($user_id,$noceruser)){
        $data_bus['is_temp']='0';
        $data_bus['act_status']='2';
        $data_bus['is_show']='1';
      }
      $data_bus['email']=trim($this->input->post('email',true));
      $data_bus['mobile']=trim($this->input->post('mobile',true));
      $data_bus['add_time']=$add_time;

      $pid= $this->User_model->user_insert('v_activity_father',$data_bus);

    }
    $data=array(
        'title'=>$title,
        'user_id'=>$user_id,
        'pid'=>$pid,
        'day_list'=>$day,
        'content_text'=>$content,
        'banner_image'=>$banner_image,
        'banner_product'=>$banner_product,
        'banner_hot'=>$banner_hot,
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
    if(in_array($user_id,$noceruser)){
      $data['is_temp']='0';
      $data['act_status']='2';

    }
    if($user_id==1077){
     // echo '<pre>';print_r($noceruser);exit();
    }
    //
    $act_id= $this->User_model->user_insert('v_activity_children',$data);


    $goods_number=trim($this->input->post('goods_number',true));
    $shop_price=trim($this->input->post('shop_price',true));
    $low=$this->input->post('low',true);

    $dateto=trim($this->input->post('dateto',true));
    $pricehas=trim($this->input->post('pricehas',true));
    $priceno=trim($this->input->post('priceno',true));
    $pricecom=trim($this->input->post('pricecom',true));

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
    redirect(base_url("bussell/bus_list_app/{$zid}"));

  }

  //商户活动下架（废弃）
  public function bus_children_info_sub(){

    if(!$this->input->get('test')){
      $user_id=$this->user_id_and_open_id();
      if(!$user_id){
        return FALSE;
      }
    }else{
      $user_id=$_SESSION['user_id']=1;
    }
    if(isset($_SESSION['act_id'])){
      $act_id=$_SESSION['act_id'];
    }else{
      return false;
    }

    $rs=$this->User_model->get_select_one('pid',"act_id=$act_id ",'v_activity_children');
    $pid=$rs['pid'];
    $rs2=$this->User_model->get_select_one('pid',"act_id=$pid ",'v_activity_father');
    $zid=$rs2['pid'];
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
    }
    if($up_down_goods==0){
      $good_is_show='2';
      $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_goods');
    }else{
      $good_is_show='1';
    }
    if($disorder==1){
      $this->User_model->update_one(array('act_id'=>$act_id),array('displayorder'=>'1'),$table='v_activity_children');
      $where="pid=$pid AND act_id!=$act_id";
      $this->User_model->update_one($where,array('displayorder'=>'99'),$table='v_activity_children');
    }

    $title=trim($this->input->post('title',true));
    $content=trim($this->input->post('content',true));



    $ed_check=trim($this->input->post('ed_check',true));
    $day=$this->input->post('day',true);
    if($user_id==1960){
      //echo '<pre>';print_r($day);exit();
    }
    $new_day=$day;
    foreach($day as $k=>$v){
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
        'act_status'=>'1',
        'is_temp'=>'1',
      // 'type'=>'father',
        'displayorder'=>$disorder,
        'is_show'=>$act_is_show,
        'add_time'=>$add_time,
    );
    if($_FILES['banner']['error']==0)
    {

      $banner=$this->upload_image('banner',$user_id.'banner');
      //crop_outci($source_path,$key1='test',$key2='time',$target_width='700',$target_height='300')
      $banner_image=$this->imagecropper($banner,'banner','time',$width='700',$height='300');
      $banner_product=$this->imagecropper($banner,'banner_product','time',$width='100',$height='100');
      $banner_hot=$this->imagecropper($banner,'banner_hot','time',$width='341',$height='230');
      //$banner_image=$this->thumb($banner_image,'banner',$user_id.'banner',$width='702',$height='300');
      $data['banner_image']=$banner_image;
      $data['banner_product']=$banner_product;
      $data['banner_hot']=$banner_hot;
    }
    else
    {
      $row=$this->User_model->get_select_one('banner_image',array('act_id'=>$act_id),'v_activity_children');
      $data['banner_image']=$row['banner_image'];
    }

    $goods_number=$this->input->post('goods_number',true);
    $shop_price=$this->input->post('shop_price',true);
    $low=$this->input->post('low',true);


    $dateto=$this->input->post('dateto',true);
    $pricehas=$this->input->post('pricehas',true);
    $priceno=$this->input->post('priceno',true);
    $pricecom=$this->input->post('pricecom',true);


    if($low!=1){
      $low=0;
    }
    $no_cerusers=$this->get_nocer();
    if(in_array($user_id,$no_cerusers)){
      $data['is_temp']='0';
      $data['act_status']='2';
      $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
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

      if($ed_check==1 && intval($goods_number)>0 && floatval($shop_price)>0){
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
      redirect(base_url("bussell/bus_list_app/{$zid}"));
    }
    else
    {
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
      $data=array(
          'goods_name'=>$title,
          'goods_number'=>$goods_number,
          'shop_price'=>$shop_price,
          'is_show'=>$good_is_show,
          'act_id'=>$act_tc_id,
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
        $count=$this->User_model->get_count(array('act_id'=>$act_tc_id),'v_goods');
        if($count['count']==0){
          //未查到，插入商品信息
          $this->User_model->user_insert('v_goods',$data);
        }else{
          //查到，直接更新商品信息
          $this->User_model->update_one(array('act_id'=>$act_tc_id),$data,'v_goods');
        }
      }
      if(!$ed_check){
        //未选取，下架商品
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_goods');
      }
      redirect(base_url("bussell/bus_list_app/{$zid}"));
    }


  }

//后台商户子活动细节展示（废弃）
  public function bus_son_show($act_id,$page=1){

    $data['count_url']=$this->count_url;
    if($act_id==0){
      return false;
    }
    $data['menu']='0';
    $data['per_user_id']=-1;
    $data['down']="#";
    $data['app_session']=session_id();
    if(isset($_COOKIE['user_id'])){
      $data['per_user_id']=$_COOKIE['user_id'];
    }
    if(isset($_COOKIE['olook'])){
      $arr_olook=explode('-',$_COOKIE['olook']);
      $data['menu']=$arr_olook[3];
      $data['per_user_id']=$arr_olook[0];

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
    $select="act_id,pid,title,act_image,start_time,end_time,content,banner_image as poster_image,request,act_status";
    $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');
    $sharetitle= $data['activity']['title'];
    $data['act_id']=$act_id;
    $data['activity']['act_id']='p'.$act_id;
    $select="v_video.video_id,v_video.address,v_video.video_name,
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

    if(is_array($data['goods']>0)){
      $goods_id=$data['goods']['goods_id'];
    }else{
      $goods_id=-1;
    }
    if($data['goods']['low']==1){
      $data['goods']['shop_price'].='起';
    }
    $where="goods_id=$goods_id AND order_status > '0'";
    $data['goods_buy']=$this->User_model->get_order_count($where);
    // echo $this->db->last_query();
    $data['goods_buy']=$data['goods_buy']['count']+rand(5,20);


    $act_rs=$this->User_model->get_select_one('user_id,users,day_list,pid',"act_id=$act_id",'v_activity_children');
    $pid=$act_rs['pid'];
    $act_rs_father=$this->User_model->get_select_one('user_id,users,pid',"act_id=$pid",'v_activity_father');
    $arr_users=explode(',',$act_rs_father['users']);
    $new_day=$data['day']=json_decode($act_rs['day_list'],true);

    foreach($data['day'] as $k=>$v){
      if($v==''){
        unset($new_day[$k]);
      }
    }
    $data['day']=$new_day;
    if($act_rs_father['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users)){
      $data['act_add']=TRUE;
    }else{
      $data['act_add']=FALSE;
    } //OR $this->input->get('admin')
    if($act_rs_father['user_id']==$data['per_user_id'] ){
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
    $data['share']['share_url']=base_url("activity/bus_son_show/$act_id");
    $data['share']['title']=$data['activity']['title'];
    $data['share']['image']=$data['activity']['banner_image'];
    $data['share']['desc']="坐享其成上的一个精彩活动{$sharetitle}快来一起High。";
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
    $this->load->view('bussell/bus_detail_show',$data);
    //$this->load->view('bussell/bus_detail',$data);
  }


//商户子活动细节（废弃）

  public function bus_children_detail_app($act_id,$page=1)
  {
   $this->get_crop_for_video();
    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false)
    {
      $et=FALSE;
    }
    else
    {
      $et=TRUE;
    }
    $data['count_url']=$this->count_url;
    $data['down']=$this->down;
    if($act_id==0)
    {
      return false;
    }
    $data['menu']='0';
    $data['per_user_id']=0;
    $data['down']="#";
    $data['app_session']=session_id();

    if(isset($_COOKIE['user_id']))
    {
      $_SESSION['user_id']=$data['per_user_id']=$_COOKIE['user_id'];
    }
    elseif(isset($_COOKIE['olook']))
    {
      $arr_olook=explode('-',$_COOKIE['olook']);
      $data['menu']=$arr_olook[3];
      $_SESSION['user_id']=$data['per_user_id']=$arr_olook[0];
    }
    elseif(isset($_SESSION['user_id']))
    {
      $data['per_user_id']=$_SESSION['user_id'];
    }
    if($data['per_user_id']==1077){
     redirect(base_url("myshop/products_detail_new?act_id=404"));
    }
    //echo '<pre>';print_r($data);exit();
    $share_user_id=$this->input->get('share_user_id',true);
    if(!$share_user_id){
      $share_user_id=$data['per_user_id'];
    }

    if(isset($_COOKIE['menu'])){
      $data['menu']=$_COOKIE['menu'];
      unset($_COOKIE['menu']);
    }
    if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no')
    {
      $location=$_COOKIE['location'];
      $location=explode(',',$location);
      $w=$location[0];$j=$location[1];
      $arr_city_country=$this->get_city_country($w,$j);
      unset($_COOKIE['location']);
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
    $count=$this->User_model->get_act_video_count("act_shop_id=$act_id  AND is_off<2 ", 'v_video');
    $data['count']= $count['count'];
    $page_num =10;
    $data['now_page'] = $page;
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page']) {$page=1;}
    $start = ($page-1)*$page_num;
    $select="act_id,pid,title,act_image,start_time,end_time,content,content_text,banner_image as poster_image,banner_image,request";
    $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');

    $pid=$data['activity']['pid'];
    $zid=$this->User_model->get_select_one('pid',"act_id=$pid",'v_activity_father');
    $data['zid']=$zid['pid'];
    if(mb_strlen($data['activity']['title'])>11){
      $data['activity']['title']=mb_substr($data['activity']['title'],0,11).'……';
    }
    $sharetitle= $data['activity']['title'];
    $data['act_id']=$act_id;
    $data['activity']['act_id']='p'.$act_id;
    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.imageforh5 as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
    $left_title='v_video.user_id=v_users.user_id';
    $left_table='v_users';
    $where="act_shop_id='$act_id'  AND is_off<2 ";

    $data['list']=$this->User_model->get_act_video_all($select,$where,'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start,$page_num);
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

          if($et===FALSE)
          {
            $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          }
          $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
        }
        else
        {
          if($et===FALSE)
          {
            $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
          }
          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
        }
        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
      }
    }
    if(!$data['list'])
    {
      $data['list']=0;
    }
    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price,low,dateto,pricehas,priceno,pricecom',
        "act_id=$act_id AND is_show='1'",'v_goods');

    if(is_array($data['goods']))
    {
      $goods_id=$data['goods']['goods_id'];
    }
    else
    {
      $goods_id=-1;
    }
    if($data['goods']['low']==1)
    {
      $data['goods']['shop_price'].='起';
    }
    $where="goods_id=$goods_id AND order_status > '0'";
    $data['goods_buy']=$this->User_model->get_order_count($where);
    $data['goods_buy']=$data['goods_buy']['count']+rand(5,20);

    $act_rs=$this->User_model->get_select_one('user_id,users,day_list,pid',"act_id=$act_id",'v_activity_children');
    $pid=$act_rs['pid'];
    $act_rs_father=$this->User_model->get_select_one('user_id,users,pid',"act_id=$pid",'v_activity_father');
    $arr_users=explode(',',$act_rs_father['users']);
    $data['day']=json_decode($act_rs['day_list'],true);
    foreach($data['day'] as $k=>$v)
    {
      if($v=='')
      {
        unset($data['day'][$k]);
      }
    }

    if($act_rs_father['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users))
    {
      $data['act_add']=TRUE;
    }
    else
    {
      $data['act_add']=FALSE;
    }
// OR $this->input->get('admin')
    if($act_rs_father['user_id']==$data['per_user_id'])
    {
      $data['edit']=TRUE;
    }
    else
    {
      $data['edit']=FALSE;
    }
    if($data['per_user_id']==0)
    {
      $data['edit']=FALSE;$data['act_add']=FALSE;
    }
    if($arr_city_country>0)
    {
      $data['liver_info']['country']=$arr_city_country['0'];
      $data['liver_info']['city']=$arr_city_country['1'];
      $data['liver_info']['act_id']='0';
      $data['liver_info']['act_shop_id']=$act_id;
      $data['liver_info']=json_encode($data['liver_info']);
    }
    else
    {
      $data['liver_info']='0';
    }
    $data['share']['share_url']=base_url("bussell/bus_children_detail_app/$act_id?share_user_id={$share_user_id}");
    $data['share']['title']=$data['activity']['title'];
    $data['share']['image']=$data['activity']['banner_image'];
    $data['share']['desc']="坐享其成上的一个精彩活动{{$sharetitle}}快来一起High。";
    $data['json_share']=json_encode($data['share']);
    $type=$this->get_lan_user();
    if(in_array($type,$this->long_css)){
      $data['type']='en';
    }
    $this->new_lan_byweb();
    if($et===TRUE)
    {
      if($this->input->get('menu',true))
      {
        $data['menu']='1';
      }
      else
      {
        $data['menu']='menu';
      }
      if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
      {
        //if($data['per_user_id']==1077){ echo '<pre>';var_dump($data);exit();}

        $this->load->view('bussell/bus_children_detail_ios',$data);
      }
      else
      {
        $this->load->view('bussell/bus_children_detail_an',$data);
      }
    }
    else
    {
      $data['edit']=FALSE;
      if($this->input->get('test')){
        echo"<pre>";print_r($data);
        $this->output->enable_profiler(TRUE);
      }
      $data['link_url']=TRUE;
      if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
      {
        $data['thdown']=$this->down;
        $this->load->view('bussell/bus_children_detail_demo',$data);
      }
      else
      {
        $data['thdown']=$this->down;
        $data['buy_url']=base_url("bussell/order_add_web?act_id=$act_id&share_user_id=$share_user_id");
        $this->load->view('bussell/bus_children_detail_wx',$data);
      }

    }
    //$this->load->view('bussell/bus_detail',$data);

  }


//订单填写页面
  public function order_add_app()
  {

    if(!$this->input->get('test'))
    {
      if(isset($_SESSION['user_id']))
      {
      $user_id=$_SESSION['user_id'];
      }
      else
      {
        echo '非法链接';
      }
    }
    else
    {
      $user_id=$_SESSION['user_id']=1;
    }
    $data['discount']=$this->input->get('discount');
    $act_id=$data['act_id']=$this->input->get('act_id',true);
    $data['tip']=$this->input->get('tip',true);
    $data['json']=$this->input->get('json',true);
    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price',
        "act_id=$act_id AND is_show='1'",'v_goods');
    // $data['goods']['shop_price']=500;
    $data['address']=$this->User_model->get_select_one('consignee,address,mobile',"user_id=$user_id",'v_user_address');
    if($this->input->get('test')){
      // echo "<pre>";print_r($data);exit();
    }
    $this->load->view('bussell/prod',$data);
  }

  //订单填写页面
  public function order_add_web()
  {
    $act_id=$data['act_id']=$this->input->get('act_id',true);
    $share_user_id=$data['share_user_id']=$this->input->get('share_user_id',true);
      $_SESSION['share_user_id']=$share_user_id;
    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
      redirect("bussell/order_add_fromwx?act_id=$act_id&share_user_id=$share_user_id");
    }
    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price', "act_id=$act_id AND is_show='1'",'v_goods');
    $data['address']=$this->User_model->get_select_one('consignee,address,mobile',"user_id=-1",'v_user_address');
      $data['back_url']="jacascript:history.go(-1)";
    $this->load->view('bussell/prod_down',$data);
  }
//微信订单提交界面
  public function order_add_fromwx()
  {
    $act_id=$data['act_id']=$this->input->get('act_id',true);
    $share_user_id=$data['share_user_id']=$this->input->get('share_user_id',true);
    if(!$share_user_id){
        $data['share_user_id']=$share_user_id=$_SESSION['share_user_id'];
    }
    include_once("./application/third_party/wxpay/WxPay.php");
    $url=base_url("bussell/order_add_fromwx?act_id=$act_id&share_user_id=$share_user_id");
    // echo $url;
    $user_id=$data['user_id']=$this->get_wx_userid($url);
    //$user_id=1;
    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price',
        "act_id=$act_id AND is_show='1'",'v_goods');
    $data['address']=$this->User_model->get_select_one('consignee,address,mobile',"user_id=$user_id",'v_wx_user_address');
    $this->load->view('bussell/prod_wx',$data);
    // $unifiedOrder = new UnifiedOrder_pub();
  }

  public function order_add_web_new()
  {
    $act_id=$data['act_id']=$this->input->get('act_id',true);
      $share_user_id=$data['share_user_id']=$this->input->get('share_user_id',true);
    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
      redirect("bussell/order_add_fromwx?act_id=$act_id&share_user_id=$share_user_id");
    }

    $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price',
        "act_id=$act_id",'v_goods');
    $data['address']=$this->User_model->get_select_one('consignee,address,mobile',"user_id=-1",'v_user_address');
    $this->load->view('bussell/prod_down',$data);
  }
//产品类筛选界面
//  public function order_add_fromwx_new()
//  {
//
//    $act_id=$data['act_id']=$this->input->get('act_id',true);
//    $data['share_user_id']=$this->input->get('share_user_id',true);
//    if(!$data['share_user_id'])
//    {
//      $data['share_user_id']=0;
//    }
//   // $act_id=$data['act_id']=233;
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//      include_once("./application/third_party/wxpay/WxPay.php");
//      $url=base_url("bussell/order_add_fromwx_new?act_id={$act_id}&share_user_id= $data[share_user_id]");
//      $wx_user_id=$data['user_id']=$this->get_wx_userid($url);
//        $sys='WX';
//    }else{
//        if(isset($_SESSION['user_id']))
//        {
//           // $user_id=$data['user_id']=$_SESSION['user_id'];
//        }
//        else
//        {
//          //  return false;
//        }
//        $sys='APP';
//    }
//
//    //$user_id_buy=1;
//    $row=$this->User_model->get_products_info(array('act_id'=>$act_id));
//
//
//
//
//    $date_time=array();
//
//    foreach($row as $k=>$v)
//    {
//
////            if($v['attr_type']==1 AND $v['attr_val']<time()){continue;}
//      $data['goods']['goods_name']=$v['goods_name'];
//      $data['goods']['goods_id']=$v['goods_id'];
//      $data['goods']['goods_number']=$v['goods_number'];
//      $data['goods']['shop_price']=$v['shop_price'];
//      if($v['attr_type']==1)
//      {
//
//        $data['date']['attr_name']=$v['attr_name'];
//        $data['date']['attr_type']=$v['attr_type'];
//
//        $data['date']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['date']['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['date']['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $date_time[]=$v['attr_val'];
//        $data['date']['attr_list'][$k]['attr_val']=date('Y-m-d',$v['attr_val']);
//        $data['date']['attr_list'][$k]['attr_wek']=date('w',$v['attr_val']);//"0" (星期日) 至 "6" (星期六)
//        $data['date']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//        $data['date']['attr_val_arr'][$v['goods_attr_id']]=date('Y-n-j',$v['attr_val']);
//        $data['date']['attr_price_arr'][$v['goods_attr_id']]=$v['attr_price'];
//
//        $data['date']['start_year']=date('Y',  $date_time[0]);
//        $data['date']['start_month']=date('n', $date_time[0]);
//        $data['date']['end_year']=date('Y', end($date_time));
//        $data['date']['end_month']=date('n', end($date_time));
//       // $time=$date_time[0];
//        $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');
//      }
//      elseif($v['attr_type']==2)
//      {
//        $data['attr'][$v['attr_id']]['attr_name']=$v['attr_name'];
//        $data['attr'][$v['attr_id']]['attr_type']=$v['attr_type'];
//        $data['attr'][$v['attr_id']]['attr_id']=$v['attr_id'];
//
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//        $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
//      }else
//      {
//        $data['check']['attr_name']=$v['attr_name'];
//        $data['check']['attr_type']=$v['attr_type'];
//        $data['check']['attr_id']=$v['attr_id'];
//
//        $data['check']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['check']['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['check']['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $data['check']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//        $data['check']['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//        $data['check']['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
//        $data['check']['attr_list']=array_values( $data['check']['attr_list']);
//      }
//
//    }
//    if(isset($time))
//    {
//      $data['date']['cal'][]=array(
//          'year'=>date('Y',$time),
//          'month'=>date('n',$time),
//          'month_cn'=>$this->get_month_cn(date('n',$time)),
//          'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//          'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
//          'all_days'=>date('t',$time),
//          'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//      );
//
////
//      while(date('Y',$time)<date('Y',end($date_time)) OR date('n',$time)<date('n',end($date_time)))
//      {
//        $data['date']['cal'][] =array(
//            'year'=>date('Y',strtotime('+1 month', $time)),
//            'month'=>date('n',strtotime('+1 month', $time)),
//            'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//            'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//            'all_days'=>date('t',strtotime('+1 month', $time)),
//            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//        );
//        $time= strtotime('+1 month', $time);
//      }
//
//      if(count($data['date']['cal'])==1)
//      {
//        $data['date']['cal'][]=array(
//            'year'=>date('Y',strtotime('+1 month', $time)),
//            'month'=>date('n',strtotime('+1 month', $time)),
//            'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//            'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//            'all_days'=>date('t',strtotime('+1 month', $time)),
//            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//        );
//      }
//    }else{
//      $data['date']['cal']=array();
//    }
//
//    if(isset($data['attr'])){
//      $data['attr']=array_values($data['attr']);
//    }else{
//      $data['attr']=array();
//    }
//
//    $data['act_id']=$act_id;
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
//    {
//      $data['show_title']=TRUE;
//      //echo '<pre>';print_r($data);exit();
//     //
//
//    }
//      $data['share_user_id']=$this->input->get('share_user_id',true);
//      if($this->input->get('test')){
//          echo '<pre>';print_r($data);exit();
//      }
//    $this->load->view('bussell/wx_stroke',$data);
//  }


    //旅拍产品筛选界面
    public function trip_order_index()
    {

        $ts_id=$data['ts_id']=$this->input->get('ts_id',true);
        if(!$ts_id){
            return false;
        }
        $data['share_user_id']=$this->input->get('share_user_id',true);
        if(!$data['share_user_id'])
        {
            $data['share_user_id']=0;
        }
        // $act_id=$data['act_id']=233;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            include_once("./application/third_party/wxpay/WxPay.php");
            $url=base_url("bussell/trip_order_index?ts_id={$ts_id}&share_user_id= $data[share_user_id]");
            $user_id=$data['user_id']=$this->get_wx_userid($url);
        }


        $data['ts_product']=$this->Camer_model->get_pho_product_detail($ts_id);

        $time=time();
        $end_time=$time+15552000;
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

//
        while(date('Y',$time)<date('Y',$end_time) OR date('n',$time)<date('n',$end_time))
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


        $data['ts_id']=$ts_id;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
        {
            $data['show_title']=TRUE;
            //echo '<pre>';print_r($data);exit();
            //
        }
        $data['ts_ajax_url']=base_url('bussell/get_camer_ajax');
        $data['call_url']="javascript:history.go(-1)";
        //$data['call_url']="javascript:history.go(-1)";

        //$data['share_user_id']=$this->input->get('share_user_id',true);
        if($this->input->get('test')){
            echo '<pre>';print_r($data);exit();
        }
        $data['sub_url']=base_url("bussell/trip_order_inert?debug=1");
        $this->load->view('bussell/trip_order_index_view',$data);
    }
  //获取摄影师列表 废弃
    public function get_camer_ajax()
    {

        $data=array(
            "date"=>$this->input->post_get("date"),
            "ts_id"=>$this->input->post_get("ts_id"),
        );
        $in_date=array('order_date'=>$data['date'],'ts_id'=>$data['ts_id']);
        $count=$this->User_model->get_goods_number_by_date($in_date);
        $ts_data=$this->User_model->get_pho_product_detail(array("ts_id"=>$data['ts_id']));
        // $count=$this->Shop_model->get_goods_number_by_date($order_date);
        $can_buy=TRUE;
        if($ts_data["goods_number"]<=$count)
        {

            $can_buy=FALSE;
        }
        if($can_buy==TRUE)
        {
            echo 1;
        }else{
            echo -1;
        }


    }

    //随机获取摄影师 废弃
    public function get_random_camer($date,$ts_id)
    {
        $data=array(
            "date"=>$date,
            "ts_id"=>$ts_id,
        );




        $rs=$this->Camer_model->get_camer_for_ts($data);
        $num=rand(0,count($rs)-1);
        if($this->input->get('test'))
        {
           echo '<pre>';print_r($rs);
        }
        else
        {
            return $rs[$num];
        }

        // print_r($rs[$num]);
    }


//旅拍订单处理

    public function trip_order_inert()
    {


        $ts_id=$this->input->post('ts_id',TRUE);

        //活动时间
        $date_id=$this->input->post('date_id',TRUE);



        $pho_id=0;
        if(!$ts_id OR !$date_id )
        {
            redirect(base_url("bussell/trip_order_index?ts_id=$ts_id"));
        }

        $in_date=array('order_date'=>$date_id,'ts_id'=>$ts_id);
        $count=$this->User_model->get_goods_number_by_date($in_date);
        $ts_data=$this->User_model->get_pho_product_detail(array("ts_id"=>$ts_id));
        // $count=$this->Shop_model->get_goods_number_by_date($order_date);
        $can_buy=TRUE;
        if($ts_data["goods_number"]<=$count)
        {

            $can_buy=FALSE;
        }

        if($can_buy==FALSE)
        {
            echo '当天无库存！';
            header("Refresh:3;url=/bussell/trip_order_index?ts_id=$ts_id");
          //  redirect(base_url("bussell/trip_order_index?ts_id=$ts_id"));
        }
        $share_user_id=$this->input->post('share_user_id',TRUE);



        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
            $sys='APP';
        }elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $sys='WX';
        }else{
            $sys='H5';
        }

  //      $sys='APP';$_SESSION['user_id']=$user_id_buy=$data['user_id']=1077;
        if($sys=='APP')
        {
            if(isset($_SESSION['user_id']))
            {
                $user_id_buy=$data['user_id']=$_SESSION['user_id'];
            }
            else
            {
                return false;
            }
        }
        elseif($sys=='WX')
        {
            include_once("./application/third_party/wxpay/WxPay.php");
            $url=base_url("bussell/trip_order_index?ts_id={$ts_id}");
            $user_id_buy=$data['user_id']=$this->get_wx_userid($url);
        }else{
            redirect(base_url("bussell/trip_order_index?ts_id=$ts_id"));
        }


        $ts_data=$this->Camer_model->get_pho_product_detail(array("ts_id"=>$ts_id));

        if(stristr($ts_data['banner_product'], 'http')===false)
        {
            $ts_data['banner_product'] = $this->config->item('base_url'). ltrim($ts_data['banner_product'],'.');
        }


        $ts_data['camer_id']=$pho_id;
        $ts_data['date']=$date_id;
        $check_json=json_encode($ts_data);


        $amount=$ts_data['ori_price'];



        if($sys=='WX')
        {
            $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_wx_users');
        }
        elseif($sys=='APP')
        {
            $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_users');
        }

        $order_sn=$this->get_order_sn();

        if($sys=='WX')
        {
            $order_arr=array(
                'user_id_buy_fromwx'=>$user_id_buy,
                'user_id_buy_name_fromwx'=>$user_id_buy_name,
                'from'=>'8',
                'front_amount'=>$ts_data['front_price'],
                'user_id_sell'=>$ts_data['user_id'],
                'user_id_sell_name'=>$ts_data['user_name'],
                'order_sn'=>$order_sn,
                'goods_amount'=>$amount,
                'order_amount'=>$amount,
                'goods_all_num' =>'1',
                'add_time'=>time(),
                'share_user_id'=>$share_user_id,
                'order_status'=>'0',
                'date_id'=>$date_id
            );
        }
        elseif($sys=='APP')
        {
            $order_arr=array(
                'user_id_buy'=>$user_id_buy,
                'user_id_buy_name'=>$user_id_buy_name,
                'from'=>'6',
                'user_id_sell'=>$ts_data['user_id'],
                'user_id_sell_name'=>$ts_data['user_name'],
                'order_sn'=>$order_sn,
                'goods_amount'=>$amount,
                'order_amount'=>$amount,
                'front_amount'=>$ts_data['front_price'],
                'goods_all_num' =>'1',
                'add_time'=>time(),
                'order_status'=>'0',
                'date'=>$date_id
            );
        }else{
            return false;
        }


      //  $order_id=$this->User_model->user_insert('v_order_info',$order_arr);
        //$this->User_model->user_insert('v_order_act_ts',array('order_id'=>$order_id,'ts_id'=>$ts_id,'add_time'=>time()));


        $order_goods_arr=array(

            'goods_id'=>$ts_data['goods_id'],
            'goods_name'=>$ts_data['title'],
            'goods_number'=>'1',
            'goods_sum'=>$ts_data['ori_price'],
            'goods_price'=>$ts_data['ori_price'],
            'goods_attr_id'=>$check_json,
            'ts_id'=>$ts_id,
            'oori_price'=>$ts_data['oori_price'],
            'camer_id'=>$pho_id,
            'camer_date'=>$date_id,
            'photo_time'=>$ts_data['photo_time'],

        );



      //  $this->User_model->user_insert('v_order_goods', $goods_order_arr);

        $data['title']=$ts_data['title'];
        $data['goods_name']=$ts_data['title'];
        $data['front_price']=$ts_data['front_price'];
        $data['date']=$date_id;
      //  $data['pho_name']=$pho_name;
        $data['pho_name']='';
        $data['amount']=$amount;
        $data['banner_product']=$ts_data['banner_product'];
        $data['num']='1';
        //$data['attr_arr']=$this-> multi_array_sort($check_arr,'attr_type',$sort=SORT_ASC);
    //    $data['order_id']=$order_id;
        $data['sys']=$sys;
        $data['user_id']=$user_id_buy;
        $data['ts_id']=$ts_id;
        $_SESSION['data']=$data;
        $_SESSION['order_info']=$order_arr;
        $_SESSION['order_goods']=$order_goods_arr;
//if($user_id_buy==1077){print_r($_SESSION);exit();}
       // echo '<pre>';print_r($_SESSION);exit();


        redirect(base_url('bussell/trip_order_supply'));
    }
    //旅拍产品订单信息补充
    public function trip_order_supply()
    {
        if(isset($_SESSION['data']) && isset($_SESSION['order_info']) && isset($_SESSION['order_goods']))
        {
            if($_SESSION['order_info']['user_id_buy']==1077)
            {
               //echo '<pre>';print_r($_SESSION);exit();
            }
            $data=$_SESSION['data'];
            if($data['front_price']=='0')
            {
                $data['has_front']=false;
            }else{
                $data['has_front']=true;
            }

            if($data['sys']=='WX')
            {
                $data['sub_url']=base_url('bussell/trip_order_supply_insert?debug=1');
                $where="user_id=$data[user_id] AND is_show='1' AND type='2' ";
                $data['addition']=$this->User_model->get_all($select='name as cn_name,name_py as cn_py,mobile as cn_mobile',$where,$table='v_user_address',$order_title='addtime',$order='DESC');
                if($data['user_id']==1){
                    //echo '<pre>';print_r($data);exit();
                }

            }
            elseif($data['sys']=='APP')
            {
                $data['sub_url']=base_url('bussell/trip_order_supply_insert?debug=1');
                $data['pay_url']="olook://payorder.toapp>fukuan&order_info<";
                $where="user_id=$data[user_id] AND is_show='1' AND type='1' ";
                $data['address']=$this->User_model->get_select_one('address',$where,'v_user_address');
                $data['address']=$data['address']['address'];
                $data['call_back']=base_url("bussell/products?act_id=$data[act_id]");
                $data['call_back']="javascript:void(0);";
                if($data['user_id']=1077){
                    //  echo '<pre>';print_r($data);//exit();
                }


            }
            $this->load->view('bussell/trip_supply',$data);
        }
        else
        {

            $data['title']='测试';
            $data['goods_name']='测试';
            $data['amount']=200;
            $data['banner_product']="http://api.etjourney.com//public/images/crop/banner_hot/1478682332.png";
            $data['num']='2';
            $data['attr_arr']=array();
            $data['order_id']=1;
            $data['sys']='APP';
            $data['user_id']=1077;
            $data['act_id']=208;

            $data['sub_url']="javascript:void(0);";
            $data['pay_url']="javascript:void(0);";

            $data['call_back']="javascript:void(0);";


            $data['front_price']=10;
            $data['date']='2011-1-1';
            $data['pho_name']='tom';
            $data['amount']=20;
       //     $data['banner_product']=$row['act_image'];
            $data['num']='1';

            $data['order_id']=12345;
            $data['sys']='APP';
            $data['user_id']=1;
            $data['ts_id']=3;
           // echo '<pre>';print_r($data);
            $this->load->view('bussell/trip_supply',$data);


           // redirect(base_url("bussell/trip_order_index"));
        }
    }
    //旅拍订单补充信息插入
    public function trip_order_supply_insert()
    {
        if(isset($_SESSION['order_info']) && isset($_SESSION['order_goods']))
        {
           $order_id=$this->input->post('order_id',TRUE);

            $sys=$this->input->post('sys',TRUE);
            $is_front=$this->input->post('is_front',TRUE);

            $order_addition=array(
                'cn_name'=>trim($this->input->post('cn_name',TRUE)),
                'en_oth_name'=>trim($this->input->post('en_oth_name',TRUE)),
                'en_first_name'=>trim($this->input->post('en_first_name',TRUE)),
                'cn_mobile'=>trim($this->input->post('cn_mobile',TRUE)),
                'weixin'=>trim($this->input->post('weixin',TRUE)),
                'mail'=>trim($this->input->post('mail',TRUE)),
                'en_hotel'=>trim($this->input->post('en_hotel',TRUE)),
                'en_hotel_address'=>trim($this->input->post('en_hotel_address',TRUE)),
                'passport_image'=>trim($this->input->post('passport',TRUE)),
                'add_time'=>time(),
                'type'=>'1'
            );
            $_SESSION['order_info']['commont']=trim($this->input->post('app_commont',TRUE));
            $_SESSION['order_info']['address']=trim($this->input->post('app_address',TRUE));
            $_SESSION['order_info']['consignee']=trim($this->input->post('cn_name',TRUE));
            $_SESSION['order_info']['mobile']=trim($this->input->post('cn_mobile',TRUE));
            if(!$order_id)
            {
               // $is_free=$this->Camer_model->camer_is_free($_SESSION['order_goods']['camer_date'],$_SESSION['order_goods']['camer_id'],$_SESSION['order_goods']['photo_time']);

                $in_date=array('order_date'=>$_SESSION['order_goods']['camer_date'],'ts_id'=>$_SESSION['order_goods']['ts_id']);
                $count=$this->User_model->get_goods_number_by_date($in_date);
                $ts_data=$this->User_model->get_pho_product_detail(array("ts_id"=>$_SESSION['order_goods']['ts_id']));
                // $count=$this->Shop_model->get_goods_number_by_date($order_date);
                $can_buy=TRUE;
                if($ts_data["goods_number"]<=$count)
                {
                    //echo '当天无库存！';
                    $can_buy=FALSE;
                }

                if($can_buy)
                {
                    //  echo '<pre>'; print_r($_SESSION);print_r($order_addition); exit();
                    $order_id=$this->Camer_model->ts_order_insert($_SESSION['order_info'],$_SESSION['order_goods'],$order_addition);
                    //   redirect("pay/alipay/$order_id");
                    if($sys=='WX')
                    {

                        include_once("./application/third_party/wxpay/WxPay.php");
                        if($is_front=='1'){
                            $fee=floatval($_SESSION['order_info']['front_amount'])*100;
                        }else{
                            $fee=floatval($_SESSION['order_info']['goods_amount'])*100;
                        }

                        //var_dump($fee);exit();
                        if(isset($_SESSION['openidfromwx']))
                        {
                            $openid=$_SESSION['openidfromwx'];
                        }
                        else
                        {
                            $url=base_url("bussell/trip_order_supply");
                            $this->get_wx_userid($url);
                            $openid=$_SESSION['openidfromwx'];
                        }
                        $unifiedOrder = new UnifiedOrder_pub();
                        $jsApi = new JsApi_pub();
                        $unifiedOrder->setParameter("openid","$openid");//商品描述
                        $unifiedOrder->setParameter("body","旅拍产品");//商品描述

                        $out_trade_no =$_SESSION['order_info']['order_sn'];
                        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
                        $unifiedOrder->setParameter("total_fee","$fee");//总金额
                        $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
                        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
                        //  echo $out_trade_no;exit();
                        $prepay_id = $unifiedOrder->getPrepayId();
                        // echo $prepay_id;exit();
                        $jsApi->setPrepayId($prepay_id);
                        $jsApiParameters = $jsApi->getParameters();
                        $_SESSION['jsApiParameters']=$jsApiParameters;

                        $prr_temp=json_decode($jsApiParameters,TRUE);
                        if($is_front=='1')
                        {

                            $parm=array(
                                'appId'=>$prr_temp['appId'],
                                'nonceStr'=>$prr_temp['nonceStr'],
                                'timeStamp'=>$prr_temp['timeStamp'],
                                'signType'=>$prr_temp['signType'],
                                'package'=>$prr_temp['package'],
                                'paySign'=>$prr_temp['paySign'],
                                'order_status'=>'0'
                            );
                        }else{

                            $parm=array(
                                'appId'=>$prr_temp['appId'],
                                'nonceStr'=>$prr_temp['nonceStr'],
                                'timeStamp'=>$prr_temp['timeStamp'],
                                'signType'=>$prr_temp['signType'],
                                'package'=>$prr_temp['package'],
                                'paySign'=>$prr_temp['paySign'],
                                'order_status'=>'0',
                                'from'=>'9',
                            );
                        }

                        $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
                        $jsApiParameters=json_encode($jsApiParameters);
                        $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
                        echo json_encode($arr);
                    }
                    elseif($sys=='APP')
                    {
                        if($is_front)
                        {
                            $order_update=array('order_status'=>'0');
                        }
                        else{
                            $order_update=array('order_status'=>'0','from'=>'7');
                        }
                        if($_SESSION['order_info']['front_amount']==$_SESSION['order_info']['goods_amount'])
                        {
                            $order_update=array('order_status'=>'0','from'=>'7');
                        }
                        $this->User_model->update_one(array('order_id'=>$order_id),$order_update,$table='v_order_info');


                        $json=array();
                        $json['order_id']=$order_id;
                        $json['user_id_buy']=$_SESSION['order_info']['user_id_buy'];
                        $json['prod']='1';
                        if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                            $json['number']=1;
                            $json['order_sn']=$_SESSION['order_info']['order_sn'];
                            $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
                        }else{
                            $json['num']=1;
                        }
                        if($is_front=='1'){
                            //$fee=floatval($rs['front_amount'])*100;
                            $json['amount']=$_SESSION['order_info']['front_amount'];
                        }else{
                            //  $fee=floatval($rs['goods_amount'])*100;
                            $json['amount']=$_SESSION['order_info']['goods_amount'];
                        }

                        //  $json['amount']=$rs['goods_amount'];
                        $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                        $json['productName']='旅拍';
                        echo json_encode($json);
                    }
                }else{
                    // $ts_id=$_SESSION['order_goods']['ts_id'];
                    //redirect(base_url("myshop/trip_detail?ts_id=$ts_id"));
                    echo '-1';
                }
            }else{

                $json=array();
                $json['order_id']=$order_id;
                $json['user_id_buy']=$_SESSION['order_info']['user_id_buy'];
                $json['prod']='1';
                if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                    $json['number']=1;
                    $json['order_sn']=$_SESSION['order_info']['order_sn'];
                    $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
                }else{
                    $json['num']=1;
                }
                if($is_front=='1'){
                    //$fee=floatval($rs['front_amount'])*100;
                    $json['amount']=$_SESSION['order_info']['front_amount'];
                }else{
                    //  $fee=floatval($rs['goods_amount'])*100;
                    $json['amount']=$_SESSION['order_info']['goods_amount'];
                }

                //  $json['amount']=$rs['goods_amount'];
                $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                $json['productName']='旅拍';
                echo json_encode($json);
            }




        }else{
            echo '订单过期,重新下单';
            exit();
        }
    }



   //筛选产品测试
//    public function ptest()
//    {
//        $act_id=$data['act_id']=$this->input->get('act_id',true);
//        $data['share_user_id']=$this->input->get('share_user_id',true);
//        if(!$data['share_user_id'])
//        {
//            $data['share_user_id']=0;
//        }
//        // $act_id=$data['act_id']=233;
//        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//            include_once("./application/third_party/wxpay/WxPay.php");
//            $url=base_url("bussell/order_add_fromwx_new?act_id={$act_id}&share_user_id= $data[share_user_id]");
//            $user_id=$data['user_id']=$this->get_wx_userid($url);
//        }
//
//        //$user_id_buy=1;
//        $row=$this->User_model->get_products_info(array('act_id'=>$act_id));
//
//        $data=array();
//        $date_time=array();
//
//        foreach($row as $k=>$v)
//        {
//
////            if($v['attr_type']==1 AND $v['attr_val']<time()){continue;}
//            $data['goods']['goods_name']=$v['goods_name'];
//            $data['goods']['goods_id']=$v['goods_id'];
//            $data['goods']['goods_number']=$v['goods_number'];
//            $data['goods']['shop_price']=$v['shop_price'];
//            if($v['attr_type']==1)
//            {
//
//                $data['date']['attr_name']=$v['attr_name'];
//                $data['date']['attr_type']=$v['attr_type'];
//
//                $data['date']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//                $data['date']['attr_list'][$k]['attr_id']=$v['attr_id'];
//                $data['date']['attr_list'][$k]['attr_val']=$v['attr_val'];
//                $date_time[]=$v['attr_val'];
//                $data['date']['attr_list'][$k]['attr_val']=date('Y-m-d',$v['attr_val']);
//                $data['date']['attr_list'][$k]['attr_wek']=date('w',$v['attr_val']);//"0" (星期日) 至 "6" (星期六)
//                $data['date']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//                $data['date']['attr_val_arr'][$v['goods_attr_id']]=date('Y-n-j',$v['attr_val']);
//                $data['date']['attr_price_arr'][$v['goods_attr_id']]=$v['attr_price'];
//
//                $data['date']['start_year']=date('Y',  $date_time[0]);
//                $data['date']['start_month']=date('n', $date_time[0]);
//                $data['date']['end_year']=date('Y', end($date_time));
//                $data['date']['end_month']=date('n', end($date_time));
//                // $time=$date_time[0];
//                $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');
//            }
//            elseif($v['attr_type']==2)
//            {
//                $data['attr'][$v['attr_id']]['attr_name']=$v['attr_name'];
//                $data['attr'][$v['attr_id']]['attr_type']=$v['attr_type'];
//                $data['attr'][$v['attr_id']]['attr_id']=$v['attr_id'];
//
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_id']=$v['attr_id'];
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_val']=$v['attr_val'];
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//                $data['attr'][$v['attr_id']]['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
//            }else
//            {
//                $data['check']['attr_name']=$v['attr_name'];
//                $data['check']['attr_type']=$v['attr_type'];
//                $data['check']['attr_id']=$v['attr_id'];
//
//                $data['check']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//                $data['check']['attr_list'][$k]['attr_id']=$v['attr_id'];
//                $data['check']['attr_list'][$k]['attr_val']=$v['attr_val'];
//                $data['check']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//                $data['check']['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//                $data['check']['attr_list'][$k]['supply_info_two']=$v['supply_info_two'];
//                $data['check']['attr_list']=array_values( $data['check']['attr_list']);
//            }
//
//        }
//        if(isset($time))
//        {
//            $data['date']['cal'][]=array(
//                'year'=>date('Y',$time),
//                'month'=>date('n',$time),
//                'month_cn'=>$this->get_month_cn(date('n',$time)),
//                'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
//                'all_days'=>date('t',$time),
//                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//            );
//
////
//            while(date('Y',$time)<date('Y',end($date_time)) OR date('n',$time)<date('n',end($date_time)))
//            {
//                $data['date']['cal'][] =array(
//                    'year'=>date('Y',strtotime('+1 month', $time)),
//                    'month'=>date('n',strtotime('+1 month', $time)),
//                    'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//                    'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//                    'all_days'=>date('t',strtotime('+1 month', $time)),
//                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//                );
//                $time= strtotime('+1 month', $time);
//            }
//
//            if(count($data['date']['cal'])==1)
//            {
//                $data['date']['cal'][]=array(
//                    'year'=>date('Y',strtotime('+1 month', $time)),
//                    'month'=>date('n',strtotime('+1 month', $time)),
//                    'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//                    'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//                    'all_days'=>date('t',strtotime('+1 month', $time)),
//                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//                );
//            }
//        }else{
//            $data['date']['cal']=array();
//        }
//
//        if(isset($data['attr'])){
//            $data['attr']=array_values($data['attr']);
//        }else{
//            $data['attr']=array();
//        }
//
//        $data['act_id']=$act_id;
//        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
//        {
//            $data['show_title']=TRUE;
//            //echo '<pre>';print_r($data);exit();
//            //
//
//        }
//        $data['share_user_id']=$this->input->get('share_user_id',true);
//        if($this->input->get('test')){
//            echo '<pre>';print_r($data);exit();
//        }
//        $this->load->view('bussell/wx_stroke_test',$data);
//    }
    //旅拍产品筛选界面
//   public function trip_order_choose()
//   {
//       $ts_id=$this->input->get_post('ts_id',TRUE);
//       $select="v_ts.ts_id,v_ts.attr_json,v_ts.photo_time,v_ts.user_id";
//       $where="v_ts.ts_id=$ts_id AND v_goods.is_show='1'";
//       $data=$this->User_model->get_one($select,$where,'v_ts','v_goods','ts_id','ts_id');
//       $data['attr_json']=json_decode($data['attr_json'],TRUE);
//       $data['date']=array();
//
//       foreach($data['attr_json'] as $k=>$v)
//       {
//
//           $data['date'][]=$k;
//           // $data['date_val'][$k]=$v;
//           foreach($v['radio'] as $k2=>$v2)
//           {
//               $name=$this->User_model->get_select_one('user_name,photo_starttime,photo_endtime',array('user_id'=>$v2['attr_value']),'v_users');
//               if( (strtotime($k) >=$name['photo_starttime'] AND strtotime($k)<=$name['photo_endtime'])  OR ( strtotime($k)+$data['photo_time']>=$name['photo_starttime'] AND strtotime($k)+$data['photo_time']<=$name['photo_endtime'])){
//                   // echo '<pre>';var_dump( strtotime($data['date'][$k2]));
//                   //echo '<br>';
//                   continue;
//               }
//               $data['date_val'][$k]['radio'][]=array('user_id'=>$v2['attr_value'],'user_name'=>$name['user_name'],'price'=>$v2['attr_price'],'url'=>'https://www.baidu.com/');
//               $data['date_val'][$k]['str_price']=$v['st_price'];
//
//           }
//       }
//       foreach($data['date'] as $k=>$v)
//       {
//           if(count($data['date_val'][$v]['radio'])==0)
//           {
//               unset($data['date'][$k]);
//           }elseif(strtotime($v)<=time()+172800)
//           {
//               unset($data['date'][$k]);
//           }
//
//           //if($v)
//       }
//       $data['date']=array_values($data['date']);
//       unset($data['attr_json']);
//   }

//日期决定选项
//  public function product_order_add()
//  {
//    $act_id=$data['act_id']=$this->input->get('act_id',true);
//    $data['share_user_id']=$this->input->get('share_user_id',true);
//    if(!$data['share_user_id'])
//    {
//      $data['share_user_id']=0;
//    }
//   //  $act_id=$data['act_id']=363;
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//      include_once("./application/third_party/wxpay/WxPay.php");
//      $url=base_url("bussell/product_order_add?act_id={$act_id}&share_user_id= $data[share_user_id]");
//      $user_id=$data['user_id']=$this->get_wx_userid($url);
//    }
//
//    //$user_id_buy=1;
//    $row=$this->User_model->get_products_info(array('act_id'=>$act_id));
//    if($this->input->get('detail')){
//     echo '<pre>';print_r($row);
//    }
//    $date_time=$data=array();
//    $data['radio_name']=array();
//    foreach($row as $k=>$v)
//    {
//      $data['goods']['goods_id']=$v['goods_id'];
//      $data['goods']['goods_name']=$v['goods_name'];
//      $data['goods']['goods_number']=$v['goods_name'];
//
//      if($v['attr_type']==1)
//      {
//        $data['date']['attr_name']=$v['attr_name'];
//        $data['date']['attr_type']=$v['attr_type'];
//
//        $data['date']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['date']['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['date']['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $date_time[]=$v['attr_val'];
//        $data['date']['attr_list'][$k]['attr_val']=date('Y-m-d',$v['attr_val']);
//        $data['date']['attr_list'][$k]['attr_wek']=date('w',$v['attr_val']);//"0" (星期日) 至 "6" (星期六)
//        $data['date']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//        $data['date']['attr_val_arr'][$v['goods_attr_id']]=date('Y-n-j',$v['attr_val']);
//        $data['date']['attr_price_arr'][$v['goods_attr_id']]=$v['attr_price'];
//
//        $data['date']['start_year']=date('Y',  $date_time[0]);
//        $data['date']['start_month']=date('n', $date_time[0]);
//        $data['date']['end_year']=date('Y', end($date_time));
//        $data['date']['end_month']=date('n', end($date_time));
//        // $time=$date_time[0];
//        $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');
//      }
//      elseif($v['attr_type']==2)
//      {
//        $data['radio_name'][]=$v['attr_name'];
//        $data['attr'][$v['pid']]['pid']=$v['pid'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_name']=$v['attr_name'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_type']=$v['attr_type'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_id']=$v['attr_id'];
//
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_price']=$v['attr_price'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//        $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['pid']=$v['pid'];
//        $data['attr'][$v['pid']]['val_list']=array_values($data['attr'][$v['pid']]['list']);
//      }else{
//        $data['check']['attr_name']=$v['attr_name'];
//        $data['check']['attr_type']=$v['attr_type'];
//        $data['check']['attr_id']=$v['attr_id'];
//
//        $data['check']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
//        $data['check']['attr_list'][$k]['attr_id']=$v['attr_id'];
//        $data['check']['attr_list'][$k]['pid']=$v['pid'];
//        $data['check']['attr_list'][$k]['attr_val']=$v['attr_val'];
//        $data['check']['attr_list'][$k]['attr_price']=$v['attr_price'];
//
//        $data['check']['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
//
//       // $data['check'][$v['pid']]['attr_list']=array_values($data['check']['attr_list']);
//      }
//    }
//    if(isset($data['radio_name'])){
//      $data['radio_name']=array_values(array_unique($data['radio_name']));
//    }
//
//    $date_time=array();
//    $data['share_user_id']=$this->input->get('share_user_id',true);
//    if(!$data['share_user_id'])
//    {
//      $data['share_user_id']=0;
//    }
//    if(isset($time))
//    {
//      $data['date']['cal'][]=array(
//          'year'=>date('Y',$time),
//          'month'=>date('n',$time),
//          'month_cn'=>$this->get_month_cn(date('n',$time)),
//          'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//          'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
//          'all_days'=>date('t',$time),
//          'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
//      );
//
////
//      while(date('Y',$time)<date('Y',end($date_time)) OR date('n',$time)<date('n',end($date_time)))
//      {
//        $data['date']['cal'][] =array(
//            'year'=>date('Y',strtotime('+1 month', $time)),
//            'month'=>date('n',strtotime('+1 month', $time)),
//            'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//            'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//            'all_days'=>date('t',strtotime('+1 month', $time)),
//            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//        );
//        $time= strtotime('+1 month', $time);
//      }
//
//      if(count($data['date']['cal'])==1)
//      {
//        $data['date']['cal'][]=array(
//            'year'=>date('Y',strtotime('+1 month', $time)),
//            'month'=>date('n',strtotime('+1 month', $time)),
//            'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//            'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//            'all_days'=>date('t',strtotime('+1 month', $time)),
//            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
//        );
//      }
//    }else{
//      $data['date']['cal']=array();
//    }
//
//    if(!isset($data['attr'])){
//      $data['attr']=array();
//    }else{
//      //$data['attr']=array_values($data['attr']);
//    }
//
//    $data['act_id']=$act_id;
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
//    {
//      $data['show_title']=TRUE;
//      //echo '<pre>';print_r($data);
//      //
//    }
//    if($this->input->get('detail')){
//      echo '<pre>';print_r($data);exit();
//    }
//  //
//    $this->load->view('bussell/product_order_add_view',$data);
//  }



//产品类筛选界面 type0  2016 1222
    public function trip_proudcts_screen_one()
    {

        $act_id=$data['act_id']=$this->input->get('act_id',true);
        if(!$act_id)
        {
            return false;
        }
        $sell_info=$this->User_model->get_trip_products_detail($act_id);
        if($sell_info['type']=='1')
        {
            redirect(base_url("bussell/trip_proudcts_screen_two?act_id=$act_id"));
        }
        $data['share_user_id']=$this->input->get('share_user_id',true);
        if(!$data['share_user_id'])
        {
            $data['share_user_id']=0;
        }
        // $act_id=$data['act_id']=233;
        $data['wx']=false;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            include_once("./application/third_party/wxpay/WxPay.php");
            $url=base_url("bussell/trip_proudcts_screen_one?act_id={$act_id}&share_user_id= $data[share_user_id]");
            $wx_user_id=$data['user_id']=$this->get_wx_userid($url);
            $sys='WX';
            $data['wx']=true;
        }

        //$user_id_buy=1;
        $row=$this->User_model->get_products_info(array('act_id'=>$act_id,'v_goods.is_show'=>'1'));
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
                // $time=$date_time[0];
                $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');
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
        if(isset($time))
        {
            $data['date']['cal'][]=array(
                'year'=>date('Y',$time),
                'month'=>date('n',$time),
                'month_cn'=>$this->get_month_cn(date('n',$time)),
                'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
                'all_days'=>date('t',$time),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            );

//
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

            if(count($data['date']['cal'])==1)
            {
                $data['date']['cal'][]=array(
                    'year'=>date('Y',strtotime('+1 month', $time)),
                    'month'=>date('n',strtotime('+1 month', $time)),
                    'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
                    'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                    'all_days'=>date('t',strtotime('+1 month', $time)),
                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
                );
            }
        }else{
            $data['date']['cal']=array();
        }

        if(isset($data['attr'])){
            $data['attr']=array_values($data['attr']);
        }else{
            $data['attr']=array();
        }

        $data['act_id']=$act_id;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
        {
            $data['show_title']=TRUE;

        }
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['sub_url']=base_url("bussell/order_arrange");
        if($this->input->get('test')){
            echo '<pre>';print_r($data);exit();
        }

        $this->load->view('bussell/tp_one_view',$data);
    }

    //产品类筛选界面 type1  2016 1222
    public function trip_proudcts_screen_two()
    {

        $act_id=$data['act_id']=$this->input->get('act_id',true);
        if(!$act_id)
        {
            return false;
        }
        $sell_info=$this->User_model->get_trip_products_detail($act_id);
        if($sell_info['type']=='0')
        {
            redirect(base_url("bussell/trip_proudcts_screen_one?act_id=$act_id"));
        }
        $data['share_user_id']=$this->input->get('share_user_id',true);
        if(!$data['share_user_id'])
        {
            $data['share_user_id']=0;
        }
        //  $act_id=$data['act_id']=363;
      $data['wx']=FALSE;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            include_once("./application/third_party/wxpay/WxPay.php");
            $url=base_url("bussell/trip_proudcts_screen_two?act_id={$act_id}&share_user_id= $data[share_user_id]");
            $user_id=$data['user_id']=$this->get_wx_userid($url);
          $data['wx']=TRUE;
        }

        //$user_id_buy=1;
       // $row=$this->User_model->get_products_info(array('act_id'=>$act_id));
        $row=$this->User_model->get_products_info(array('act_id'=>$act_id,'v_goods.is_show'=>'1'));
        if($this->input->get('detail')){
            echo '<pre>';print_r($row);
        }
        $date_time=$data=array();
        $data['radio_name']=array();
        foreach($row as $k=>$v)
        {
            $data['goods']['goods_id']=$v['goods_id'];
            $data['goods']['goods_name']=$v['goods_name'];
            $data['goods']['goods_number']=$v['goods_name'];

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
                // $time=$date_time[0];
                $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');
            }
            elseif($v['attr_type']==2)
            {
                $data['radio_name'][]=$v['attr_name'];
                $data['attr'][$v['pid']]['pid']=$v['pid'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_name']=$v['attr_name'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_type']=$v['attr_type'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_id']=$v['attr_id'];

                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_id']=$v['attr_id'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_val']=$v['attr_val'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['attr_price']=$v['attr_price'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];
                $data['attr'][$v['pid']]['list'][$v['attr_id']]['attr_list'][$k]['pid']=$v['pid'];
                $data['attr'][$v['pid']]['val_list']=array_values($data['attr'][$v['pid']]['list']);
            }else{
                $data['check']['attr_name']=$v['attr_name'];
                $data['check']['attr_type']=$v['attr_type'];
                $data['check']['attr_id']=$v['attr_id'];

                $data['check']['attr_list'][$k]['goods_attr_id']=$v['goods_attr_id'];
                $data['check']['attr_list'][$k]['attr_id']=$v['attr_id'];
                $data['check']['attr_list'][$k]['pid']=$v['pid'];
                $data['check']['attr_list'][$k]['attr_val']=$v['attr_val'];
                $data['check']['attr_list'][$k]['attr_price']=$v['attr_price'];

                $data['check']['attr_list'][$k]['supply_info_one']=$v['supply_info_one'];

                // $data['check'][$v['pid']]['attr_list']=array_values($data['check']['attr_list']);
            }
        }
        if(isset($data['radio_name'])){
            $data['radio_name']=array_values(array_unique($data['radio_name']));
        }

        $date_time=array();
        $data['share_user_id']=$this->input->get('share_user_id',true);
        if(!$data['share_user_id'])
        {
            $data['share_user_id']=0;
        }
        if(isset($time))
        {
            $data['date']['cal'][]=array(
                'year'=>date('Y',$time),
                'month'=>date('n',$time),
                'month_cn'=>$this->get_month_cn(date('n',$time)),
                'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
                'all_days'=>date('t',$time),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            );

//
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

            if(count($data['date']['cal'])==1)
            {
                $data['date']['cal'][]=array(
                    'year'=>date('Y',strtotime('+1 month', $time)),
                    'month'=>date('n',strtotime('+1 month', $time)),
                    'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
                    'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                    'all_days'=>date('t',strtotime('+1 month', $time)),
                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
                );
            }
        }else{
            $data['date']['cal']=array();
        }

        if(!isset($data['attr'])){
            $data['attr']=array();
        }

        $data['act_id']=$act_id;
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
        {
            $data['show_title']=TRUE;

        }
        if($this->input->get('detail')){
            echo '<pre>';print_r($data);exit();
        }
        $data['sub_url']=base_url("bussell/order_arrange");
        $this->load->view('bussell/product_order_add_view',$data);
    }

  //旅游筛选产品数据整理
    public function order_arrange()
    {
        $act_id=$this->input->post('act_id',TRUE);
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
            $sys='APP';
        }elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $sys='WX';
        }else{
            $sys='H5';
        }
       //  $sys="APP";$_SESSION['user_id']=1077;
        if($sys=='APP')
        {
            if(isset($_SESSION['user_id']))
            {
                $user_id_buy=$data['user_id']=$_SESSION['user_id'];
            }
            else
            {
                return false;
            }
        }
        elseif($sys=='WX')
        {
            include_once("./application/third_party/wxpay/WxPay.php");
            $url=base_url("bussell/order_arrange?act_id={$act_id}");
            $user_id_buy=$data['user_id']=$this->get_wx_userid($url);
        }else{
            redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        $sell_info=$this->User_model->get_trip_products_detail($act_id);
        if(stristr($sell_info['banner_product'], 'http')==false)
        {
            $sell_info['banner_product'] = $this->config->item('base_url')  . ltrim($sell_info['banner_product'],'.');
        }

        $share_user_id=$this->input->post('share_user_id',TRUE);

        $date_id=$this->input->post('date_id',TRUE);
        $temp_rs=$this->User_model->get_select_one('attr_val',array('goods_attr_id'=>$date_id),'v_goods_attr');

        $radio_id=$this->input->post('radio_id',TRUE);

        $check_id=$this->input->post('check_id',TRUE);
        $check_num=$this->input->post('check_num',TRUE);

        $check_val=$this->input->post('check_val',TRUE);
        $check_price=$this->input->post('check_price',TRUE);
        $check_name=$this->input->post('check_name',TRUE);

        $check_arr=array();
        $check_all_num=0;
        foreach($check_id as $k=>$v)
        {
            if($check_num[$k]>0)
            {
                $check_arr[]=array(
                    'goods_attr_id'=>$v,
                    'attr_num'=>$check_num[$k],
                    'attr_val'=>$check_val[$k],
                    'attr_price'=>$check_price[$k],
                    'attr_name'=>$check_name,
                    'attr_type'=>'3'
                );
                $check_all_num+=$check_num[$k];
            }
        }
        $radio_id[]=$date_id;
        $radio_id=implode(',',$radio_id);
        $where="v_goods_attr.goods_attr_id IN ($radio_id) AND v_goods_attr.is_show='1'";
        $rs=$this->User_model->get_select_all('v_attr.attr_id,v_attr.attr_name,v_goods_attr.attr_val,v_goods_attr.attr_price,v_goods_attr.goods_attr_id,v_attr.attr_type',
            $where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
        foreach($rs as $k=>$v)
        {
            $check_arr[]=array(
                'goods_attr_id'=>$rs[$k]['goods_attr_id'],
                'attr_num'=>$check_all_num,
                'attr_val'=>$rs[$k]['attr_val'],
                'attr_price'=>$rs[$k]['attr_price'],
                'attr_name'=>$rs[$k]['attr_name'],
                'attr_type'=>$rs[$k]['attr_type']
            );
        }

        $goods_name=$sell_info['title'];
        $oori_price=$sell_info['oori_price'];

        $user_id_sell=$sell_info['user_id'];
        $user_id_sell_name=$sell_info['user_name'];


        if($sys=='WX')
        {
            $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_wx_users');
        }
        else
        {
            $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_users');
        }

        $order_sn=$this->get_order_sn();

        $goods_id=$sell_info['goods_id'];

        $amount=$this->input->post('amount',TRUE);

        if($sys=='WX')
        {
            $order_arr=array(
                'user_id_buy_fromwx'=>$user_id_buy,
                'user_id_buy_name_fromwx'=>$user_id_buy_name,
                'from'=>'4',
                'user_id_sell'=>$user_id_sell,
                'user_id_sell_name'=>$user_id_sell_name,
                'order_sn'=>$order_sn,
                'goods_amount'=>$amount,
                'order_amount'=>$amount,
                'goods_all_num' =>'1',
                'add_time'=>time(),
                'share_user_id'=>$share_user_id,
                'order_status'=>'0',
                'date'=>date('Y-m-d',$temp_rs['attr_val'])
            );
        }
        else
        {
            $order_arr=array(
                'user_id_buy'=>$user_id_buy,
                'user_id_buy_name'=>$user_id_buy_name,
                'from'=>'5',
                'user_id_sell'=>$user_id_sell,
                'user_id_sell_name'=>$user_id_sell_name,
                'order_sn'=>$order_sn,
                'goods_amount'=>$amount,
                'order_amount'=>$amount,
                'goods_all_num' =>'1',
                'add_time'=>time(),
                'order_status'=>'0',
                'date'=>date('Y-m-d',$temp_rs['attr_val'])
            );
        }

        $_SESSION['order_info']=$order_arr;

        $goods_attr_arr=json_encode($check_arr);

        $goods_order_arr=array(
            'goods_id'=>$goods_id,
            'goods_name'=>$goods_name,
            'goods_number'=>$check_all_num,
            'goods_sum'=>$amount,
            'goods_price'=>$amount,
            'goods_attr_id'=>$goods_attr_arr,
            'act_id'=>$act_id,
            'oori_price'=>$oori_price,

        );
        $_SESSION['order_goods']=$goods_order_arr;

     //   $this->User_model->user_insert('v_order_goods', $goods_order_arr);
        $data['range_name']=$sell_info['name'];
        $data['title']=$goods_name;
        $data['amount']=$amount;
        $data['banner_product']=$sell_info['banner_product'];
        $data['num']=$check_all_num;
        $data['attr_arr']=$this-> multi_array_sort($check_arr,'attr_type',$sort=SORT_ASC);
        $data['sys']=$sys;
        $data['user_id']=$user_id_buy;
        $data['act_id']=$act_id;
        $_SESSION['data']=$data;
        //echo '<pre>';print_r($_SESSION);exit();
//if($user_id_buy==1077){print_r($_SESSION);exit();}
        redirect(base_url('bussell/order_addition'));
    }



  //订单补充信息
    public function order_addition()
    {


        if(isset($_SESSION['data']))
        {
            $data=$_SESSION['data'];
            if($data['sys']=='WX')
            {

                $data['show_title']=FALSE;
               // $data['sub_url']=base_url('bussell/gopay_com_sub_wx');
                $where="user_id=$data[user_id] AND is_show='1' AND type='2' ";
                $data['addition']=$this->User_model->get_all($select='name as cn_name,name_py as cn_py,mobile as cn_mobile',$where,$table='v_user_address',$order_title='addtime',$order='DESC');
                if($data['user_id']==1){

                    //   echo '<pre>';print_r($data);exit();
                }
                // $this->load->view('bussell/wx_gopay',$data);
            }
            elseif($data['sys']=='APP')
            {

                $data['show_title']=TRUE;
               // $data['sub_url']=base_url('bussell/gopay_com_sub_app');
                $data['pay_url']="olook://payorder.toapp>fukuan&order_info<";
                $where="user_id=$data[user_id] AND is_show='1' AND type='1' ";
                $data['call_back']=base_url("bussell/products_new?act_id=$data[act_id]");
                $data['call_back']="javascript:void(0);";
                $data['call_url']="javascript:void(0);";
                if($data['user_id']=1092){
                 //   echo '<pre>';print_r($data);//exit();
                }

                ///$data['addition']=$this->User_model->get_all($select='name as cn_name,name_py as cn_py,mobile as cn_mobile',$where,$table='v_user_address',$order_title='addtime',$order='DESC');
                //$this->load->view('bussell/app_gopay',$data);
            }
            $data['sub_url']=base_url('bussell/trip_order_put');
            $this->load->view('bussell/order_addition_view',$data);
        }
        else
        {
            echo '订单过期，请重新下单！';
        }
    }

   //旅游 筛选类订单提交
    public function trip_order_put()
    {
        if(!isset($_SESSION['order_info']) OR !isset($_SESSION['order_goods']) )
        {
            //return false;
          echo -1;exit();
        }
        $order_id=$this->input->post('order_id',TRUE);
        if((int)$order_id>0)
        {
            if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney'))
            {
                $json=array();
                $json['order_id']=$order_id;
                $json['user_id_buy']=$_SESSION['order_info']['user_id_buy'];
                $json['prod']='1';
                if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                    $json['number']='1';
                    $json['order_sn']=$_SESSION['order_info']['order_sn'];
                    $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
                }else{
                    $json['num']='1';
                }
                $json['amount']=$_SESSION['order_info']['goods_amount'];
                $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                $json['productName']='旅游产品';
                echo json_encode($json);
                exit();
            }
            if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
            {
                $rs=$this->User_model->get_select_one('goods_amount,order_sn,user_id_buy_fromwx,appId,nonceStr,timeStamp,signType,package,paySign',array('order_id'=>$order_id),'v_order_info');
                if($rs['appId']!='0')
                {
                    $arr_get=array(
                        'appId'=>$rs['appId'],
                        'nonceStr'=>$rs['nonceStr'],
                        'timeStamp'=>$rs['timeStamp'],
                        'signType'=>$rs['signType'],
                        'package'=>$rs['package'],
                        'paySign'=>$rs['paySign'],
                    );
                    $arr=array('order_id'=>$order_id,'json'=>json_encode(json_encode($arr_get)));
                    echo json_encode($arr);

                }
            }

        }
        $order_addition[]=array(
            'cn_name'=>trim($this->input->post('cn_name',TRUE)),
            'en_oth_name'=>trim($this->input->post('en_oth_name',TRUE)),
            'en_first_name'=>trim($this->input->post('en_first_name',TRUE)),
            'cn_mobile'=>trim($this->input->post('cn_mobile',TRUE)),
            'weixin'=>trim($this->input->post('weixin',TRUE)),
            'mail'=>trim($this->input->post('mail',TRUE)),
            'en_hotel'=>trim($this->input->post('en_hotel',TRUE)),
            'en_hotel_address'=>trim($this->input->post('en_hotel_address',TRUE)),
            'commont'=>trim($this->input->post('commont',TRUE)),
            'passport'=>trim($this->input->post('passport',TRUE)),
            'add_time'=>time(),
            'type'=>'1'
        );

        $_SESSION['order_info']['commont']=trim($this->input->post('commont',TRUE));
        $_SESSION['order_info']['address']=trim($this->input->post('en_hotel_address',TRUE));
        $_SESSION['order_info']['consignee']=trim($this->input->post('cn_name',TRUE));
        $_SESSION['order_info']['mobile']=trim($this->input->post('cn_mobile',TRUE));


        $cx_name_arr=$this->input->post('cx_name',TRUE);
        $cx_enname_arr=$this->input->post('cx_enname',TRUE);
        $cx_en_firstname_arr=$this->input->post('cx_en_firstname',TRUE);
        $cx_cn_mobile_arr=$this->input->post('cx_cn_mobile',TRUE);
        $cx_passport=$this->input->post('cx_passport',TRUE);



        if(is_array($cx_name_arr))
        {
            foreach($cx_name_arr as $k=>$v)
            {
                $order_addition[]=array(
                    'cn_name'=>$cx_name_arr[$k],
                    'en_oth_name'=>$cx_enname_arr[$k],
                    'en_first_name'=>$cx_en_firstname_arr[$k],
                    'cn_mobile'=>$cx_cn_mobile_arr[$k],
                    'weixin'=>'',
                    'mail'=>'',
                    'en_hotel'=>'',
                    'en_hotel_address'=>'',
                    'commont'=>'',
                    'add_time'=>time(),
                    'passport'=>$cx_passport[$k],
                    'type'=>'2'
                );
            }
        }

        $order_commont=trim($this->input->post('order_commont',TRUE));
        $_SESSION['order_info']['commont']=$order_commont;


        //echo '<pre>';print_r($_SESSION);print_r($order_addition);exit();
        $order_id= $this->User_model->trip_insert($_SESSION['order_info'],$_SESSION['order_goods'],$order_addition);

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney'))
        {

            $json=array();
            $json['order_id']=$order_id;
            $json['user_id_buy']=$_SESSION['order_info']['user_id_buy'];
            $json['prod']='1';
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                $json['number']='1';
                $json['order_sn']=$_SESSION['order_info']['order_sn'];
                $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
            }else{
                $json['num']='1';
            }
            $json['amount']=$_SESSION['order_info']['goods_amount'];
            $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
            $json['productName']='旅游产品';
            echo json_encode($json);

        }
        elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
        {

            include_once("./application/third_party/wxpay/WxPay.php");
            $fee=floatval($_SESSION['order_info']['goods_amount'])*100;
            if(isset($_SESSION['openidfromwx']))
            {
                $openid=$_SESSION['openidfromwx'];
            }
            else
            {
                $url=base_url("bussell/trip_order_put");
                $this->get_wx_userid($url);
                $openid=$_SESSION['openidfromwx'];
            }

            $unifiedOrder = new UnifiedOrder_pub();
            $jsApi = new JsApi_pub();
            $unifiedOrder->setParameter("openid","$openid");//商品描述
            $unifiedOrder->setParameter("body","特价产品");//商品描述

            $out_trade_no =$_SESSION['order_info']['order_sn'];
            $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
            $unifiedOrder->setParameter("total_fee","$fee");//总金额
            $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
            $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
            //  echo $out_trade_no;exit();
            $prepay_id = $unifiedOrder->getPrepayId();
            // echo $prepay_id;exit();
            $jsApi->setPrepayId($prepay_id);
            $jsApiParameters = $jsApi->getParameters();
            $_SESSION['jsApiParameters']=$jsApiParameters;

            $prr_temp=json_decode($jsApiParameters,TRUE);
            $parm=array(
                'appId'=>$prr_temp['appId'],
                'nonceStr'=>$prr_temp['nonceStr'],
                'timeStamp'=>$prr_temp['timeStamp'],
                'signType'=>$prr_temp['signType'],
                'package'=>$prr_temp['package'],
                'paySign'=>$prr_temp['paySign'],
            );
            $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
            $jsApiParameters=json_encode($jsApiParameters);
            $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
            echo json_encode($arr);

        }
        else
        {
            return false;
        }




    }






//  public function wx_gopay_products()
//  {
//    $act_id=$this->input->post('act_id',TRUE);
//
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
//      $sys='APP';
//    }elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//      $sys='WX';
//    }else{
//      $sys='H5';
//    }
//
//
//    if($sys=='APP')
//    {
//      if(isset($_SESSION['user_id']))
//      {
//        $user_id_buy=$data['user_id']=$_SESSION['user_id'];
//      }
//      else
//      {
//        return false;
//      }
//    }
//    elseif($sys=='WX')
//    {
//      include_once("./application/third_party/wxpay/WxPay.php");
//      $url=base_url("bussell/order_add_fromwx_new?act_id={$act_id}");
//      $user_id_buy=$data['user_id']=$this->get_wx_userid($url);
//    }else{
//      redirect(base_url('bussell/order_add_fromwx_new'));
//    }
//
//
//
//
//
//
//    $act_rs=$this->User_model->get_select_one('banner_product',array('act_id'=>$act_id),'v_activity_children');
//
//    $share_user_id=$this->input->post('share_user_id',TRUE);
//
//    $date_id=$this->input->post('date_id',TRUE);
//
//    $radio_id=$this->input->post('radio_id',TRUE);
//
//    $check_id=$this->input->post('check_id',TRUE);
//    $check_num=$this->input->post('check_num',TRUE);
//
//    $check_val=$this->input->post('check_val',TRUE);
//    $check_price=$this->input->post('check_price',TRUE);
//    $check_name=$this->input->post('check_name',TRUE);
//
//    $check_arr=array();
//    $check_all_num=0;
//    foreach($check_id as $k=>$v)
//    {
//      if($check_num[$k]>0)
//      {
//        $check_arr[]=array(
//            'goods_attr_id'=>$v,
//            'attr_num'=>$check_num[$k],
//            'attr_val'=>$check_val[$k],
//            'attr_price'=>$check_price[$k],
//            'attr_name'=>$check_name,
//            'attr_type'=>'3'
//        );
//        $check_all_num+=$check_num[$k];
//      }
//    }
//    $radio_id[]=$date_id;
//    $radio_id=implode(',',$radio_id);
//    $where="v_goods_attr.goods_attr_id IN ($radio_id) AND v_goods_attr.is_show='1'";
//    $rs=$this->User_model->get_select_all('v_attr.attr_id,v_attr.attr_name,v_goods_attr.attr_val,v_goods_attr.attr_price,v_goods_attr.goods_attr_id,v_attr.attr_type',
//        $where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
//    foreach($rs as $k=>$v)
//    {
//      $check_arr[]=array(
//          'goods_attr_id'=>$rs[$k]['goods_attr_id'],
//          'attr_num'=>$check_all_num,
//          'attr_val'=>$rs[$k]['attr_val'],
//          'attr_price'=>$rs[$k]['attr_price'],
//          'attr_name'=>$rs[$k]['attr_name'],
//          'attr_type'=>$rs[$k]['attr_type']
//      );
//    }
//   // echo '<pre>';print_r($check_arr);print_r($rs);exit();
//    //get_one($select,$where,$table,$left_table,$title1,$title2)
//    $row=$this->User_model->get_one('v_activity_children.user_id,v_goods.oori_price,v_goods.goods_id,v_activity_children.title as goods_name,v_goods.shop_price',array('v_activity_children.act_id'=>$act_id,'v_goods.is_show'=>'1'),'v_activity_children','v_goods','act_id','act_id');
//    $user_id_sell=$row['user_id'];
//    $goods_name=$row['goods_name'];
//    $oori_price=$row['oori_price'];
//
//    if($sys=='WX')
//    {
//      $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_wx_users');
//    }
//    elseif($sys=='APP')
//    {
//      $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_users');
//    }
//
//    $user_id_sell_name=$this->User_model->get_username($user_id_sell);
//    $order_sn=$this->get_order_sn();
//
//    $goods_id=$row['goods_id'];
//
//    $amount=$this->input->post('amount',TRUE);
//
//    if($sys=='WX')
//    {
//      $order_arr=array(
//          'user_id_buy_fromwx'=>$user_id_buy,
//          'user_id_buy_name_fromwx'=>$user_id_buy_name,
//          'from'=>'4',
//          'act_id'=>$act_id,
//          'user_id_sell'=>$user_id_sell,
//          'user_id_sell_name'=>$user_id_sell_name,
//          'order_sn'=>$order_sn,
//          'goods_amount'=>$amount,
//          'order_amount'=>$amount,
//          'goods_all_num' =>$check_all_num,
//          'add_time'=>time(),
//          'share_user_id'=>$share_user_id,
//          'order_status'=>'-1'
//      );
//    }
//    elseif($sys=='APP')
//    {
//      $order_arr=array(
//          'user_id_buy'=>$user_id_buy,
//          'user_id_buy_name'=>$user_id_buy_name,
//          'from'=>'5',
//          'act_id'=>$act_id,
//          'user_id_sell'=>$user_id_sell,
//          'user_id_sell_name'=>$user_id_sell_name,
//          'order_sn'=>$order_sn,
//          'goods_amount'=>$amount,
//          'order_amount'=>$amount,
//          'goods_all_num' =>$check_all_num,
//          'add_time'=>time(),
//          'order_status'=>'-1'
//      );
//    }
//
//
//    $order_id=$this->User_model->user_insert('v_order_info',$order_arr);
////    $this->User_model->user_insert('v_order_act_ts',array('order_id'=>$order_id,'act_id'=>$act_id,'add_time'=>time()));
//
//      $goods_attr_arr=json_encode($check_arr);
//
//    $goods_order_arr=array(
//      'order_id'=>$order_id,
//      'goods_id'=>$goods_id,
//      'goods_name'=>$goods_name,
//      'goods_number'=>$check_all_num,
//      'goods_sum'=>$amount,
//      'goods_price'=>$row['shop_price'],
//      'goods_attr_id'=>$goods_attr_arr,
//      'act_id'=>$act_id,
//      'oori_price'=>$oori_price,
//
//    );
//
//    $this->User_model->user_insert('v_order_goods', $goods_order_arr);
//
//    $data['title']=$goods_name;
//    $data['amount']=$amount;
//    $data['banner_product']=$act_rs['banner_product'];
//    $data['num']=$check_all_num;
//    $data['attr_arr']=$this-> multi_array_sort($check_arr,'attr_type',$sort=SORT_ASC);
//    $data['order_id']=$order_id;
//    $data['sys']=$sys;
//    $data['user_id']=$user_id_buy;
//    $data['act_id']=$act_id;
//    $_SESSION['data']=$data;
////if($user_id_buy==1077){print_r($_SESSION);exit();}
//    redirect(base_url('bussell/gopay_com'));
//
//  }

  //创建特价产品订单  日期决定选项
//  public function order_products_insert()
//  {
//
//    //echo '<pre>';print_r($_POST);exit();
//
//    $act_id=$this->input->post('act_id',TRUE);
//
//    if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
//      $sys='APP';
//    }elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//      $sys='WX';
//    }else{
//      $sys='H5';
//    }
//
//
//    if($sys=='APP')
//    {
//      if(isset($_SESSION['user_id']))
//      {
//        $user_id_buy=$data['user_id']=$_SESSION['user_id'];
//      }
//      else
//      {
//        return false;
//      }
//    }
//    elseif($sys=='WX')
//    {
//      include_once("./application/third_party/wxpay/WxPay.php");
//      $url=base_url("bussell/order_products_insert?act_id={$act_id}");
//      $user_id_buy=$data['user_id']=$this->get_wx_userid($url);
//    }else{
//      //$user_id_buy=1077;
//      redirect(base_url('bussell/order_products_insert'));
//    }
//
//
//
//    $act_rs=$this->User_model->get_select_one('banner_product',array('act_id'=>$act_id),'v_activity_children');
//
//    $share_user_id=$this->input->post('share_user_id',TRUE);
//
//    $date_id=$this->input->post('date_id',TRUE);
//
//    $radio_id=$this->input->post('radio_id',TRUE);
//
//    $check_id=$this->input->post('check_id',TRUE);
//    $check_num=$this->input->post('check_num',TRUE);
//
//    $check_val=$this->input->post('check_val',TRUE);
//    $check_price=$this->input->post('check_price',TRUE);
//    $check_name=$this->input->post('check_name',TRUE);
//
//    $check_arr=array();
//    $check_all_num=0;
//    foreach($check_id as $k=>$v)
//    {
//      if($check_num[$k]>0)
//      {
//        $check_arr[]=array(
//            'goods_attr_id'=>$v,
//            'attr_num'=>$check_num[$k],
//            'attr_val'=>$check_val[$k],
//            'attr_price'=>$check_price[$k],
//            'attr_name'=>$check_name,
//            'attr_type'=>'3'
//        );
//        $check_all_num+=$check_num[$k];
//      }
//    }
//    $radio_id[]=$date_id;
//    $radio_id=implode(',',$radio_id);
//    $where="v_goods_attr.goods_attr_id IN ($radio_id) AND v_goods_attr.is_show='1'";
//    $rs=$this->User_model->get_select_all('v_attr.attr_id,v_attr.attr_name,v_goods_attr.attr_val,v_goods_attr.attr_price,v_goods_attr.goods_attr_id,v_attr.attr_type',
//        $where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
//    foreach($rs as $k=>$v)
//    {
//      $check_arr[]=array(
//          'goods_attr_id'=>$rs[$k]['goods_attr_id'],
//          'attr_num'=>$check_all_num,
//          'attr_val'=>$rs[$k]['attr_val'],
//          'attr_price'=>$rs[$k]['attr_price'],
//          'attr_name'=>$rs[$k]['attr_name'],
//          'attr_type'=>$rs[$k]['attr_type']
//      );
//    }
//    // echo '<pre>';print_r($check_arr);print_r($rs);exit();
//    //get_one($select,$where,$table,$left_table,$title1,$title2)
//    $row=$this->User_model->get_one('v_activity_children.user_id,v_goods.oori_price,v_goods.goods_id,v_activity_children.title as goods_name,v_goods.shop_price',array('v_activity_children.act_id'=>$act_id,'v_goods.is_show'=>'1'),'v_activity_children','v_goods','act_id','act_id');
//    $user_id_sell=$row['user_id'];
//    $goods_name=$row['goods_name'];
//    $oori_price=$row['oori_price'];
//
//    if($sys=='WX')
//    {
//      $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_wx_users');
//    }
//    elseif($sys=='APP')
//    {
//      $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_users');
//    }
//
//    $user_id_sell_name=$this->User_model->get_username($user_id_sell);
//    $order_sn=$this->get_order_sn();
//
//    $goods_id=$row['goods_id'];
//
//    $amount=$this->input->post('amount',TRUE);
//
//    if($sys=='WX')
//    {
//      $order_arr=array(
//          'user_id_buy_fromwx'=>$user_id_buy,
//          'user_id_buy_name_fromwx'=>$user_id_buy_name,
//          'from'=>'4',
//          'user_id_sell'=>$user_id_sell,
//          'user_id_sell_name'=>$user_id_sell_name,
//          'order_sn'=>$order_sn,
//          'goods_amount'=>$amount,
//          'order_amount'=>$amount,
//          'goods_all_num' =>$check_all_num,
//          'add_time'=>time(),
//          'share_user_id'=>$share_user_id,
//          'order_status'=>'-1'
//      );
//    }
//    elseif($sys=='APP')
//    {
//      $order_arr=array(
//          'user_id_buy'=>$user_id_buy,
//          'user_id_buy_name'=>$user_id_buy_name,
//          'from'=>'5',
//          'user_id_sell'=>$user_id_sell,
//          'user_id_sell_name'=>$user_id_sell_name,
//          'order_sn'=>$order_sn,
//          'goods_amount'=>$amount,
//          'order_amount'=>$amount,
//          'goods_all_num' =>$check_all_num,
//          'add_time'=>time(),
//          'order_status'=>'-1'
//      );
//    }
//
//
//    $order_id=$this->User_model->user_insert('v_order_info',$order_arr);
//    $goods_attr_arr=json_encode($check_arr);
//
//    $goods_order_arr=array(
//        'order_id'=>$order_id,
//        'goods_id'=>$goods_id,
//        'goods_name'=>$goods_name,
//        'goods_number'=>$check_all_num,
//        'goods_sum'=>$amount,
//        'goods_price'=>$row['shop_price'],
//        'goods_attr_id'=>$goods_attr_arr,
//        'act_id'=>$act_id,
//        'oori_price'=>$oori_price,
//
//    );
//
//    $this->User_model->user_insert('v_order_goods', $goods_order_arr);
//
//    $data['title']=$goods_name;
//    $data['amount']=$amount;
//    $data['banner_product']=$act_rs['banner_product'];
//    $data['num']=$check_all_num;
//    $data['attr_arr']=$this-> multi_array_sort($check_arr,'attr_type',$sort=SORT_ASC);
//    $data['order_id']=$order_id;
//    $data['sys']=$sys;
//    $data['user_id']=$user_id_buy;
//    $data['act_id']=$act_id;
//      $data['call_url']=base64_encode(base_url("bussell/order_add_fromwx_new?act_id=$act_id"));
//    $_SESSION['data']=$data;
//
//    redirect(base_url('bussell/gopay_com'));
//
//  }
/*
 *
 */
//  public function gopay_com()
//  {
//    if(isset($_SESSION['data']))
//    {
//      $data=$_SESSION['data'];
//
//        $act_rs=$this->User_model->get_select_one('range_name',array('act_id'=> $data['act_id']),'v_activity_children');
//        $data['show_info']=FALSE;
//        if($act_rs['range_name']=='普吉岛' AND ($data['user_id']==1 OR $data['user_id']==1077))
//        {
//            $data['show_info']=TRUE;
//          // var_dump($data['show_info']);
//        }
//
//
//
//      if($data['sys']=='WX')
//      {
//
//          $where_show_info=array('v_benefit_log.wx_user_id'=>$data['user_id'],'v_benefit_log.benefit_id'=>'1');
//          $show_info=$this->User_model->get_one('v_benefit_log.log_id',$where_show_info,'v_benefit_log','v_benefit','benefit_id','benefit_id');
//          if(count($show_info)!='0'){
//              $data['show_info']=FALSE;
//          }
//
//          $data['show_title']=FALSE;
//          $data['sub_url']=base_url('bussell/gopay_com_sub_wx');
//        $where="user_id=$data[user_id] AND is_show='1' AND type='2' ";
//        $data['addition']=$this->User_model->get_all($select='name as cn_name,name_py as cn_py,mobile as cn_mobile',$where,$table='v_user_address',$order_title='addtime',$order='DESC');
//          if($data['user_id']==1){
//
//           //   echo '<pre>';print_r($data);exit();
//          }
//       // $this->load->view('bussell/wx_gopay',$data);
//      }
//      elseif($data['sys']=='APP')
//      {
//          $where_show_info=array('v_benefit_log.user_id'=>$data['user_id'],'v_benefit_log.benefit_id'=>'1');
//          $show_info=$this->User_model->get_one('v_benefit_log.log_id',$where_show_info,'v_benefit_log','v_benefit','benefit_id','benefit_id');
//          if(count($show_info)!='0'){
//              $data['show_info']=FALSE;
//          }
//
//        $data['show_title']=TRUE;
//        $data['sub_url']=base_url('bussell/gopay_com_sub_app');
//        $data['pay_url']="olook://payorder.toapp>fukuan&order_info<";
//        $where="user_id=$data[user_id] AND is_show='1' AND type='1' ";
//          $data['call_back']=base_url("bussell/products_new?act_id=$data[act_id]");
//          $data['call_back']="javascript:void(0);";
//          if($data['user_id']=1077){
//           // var_dump( $data['show_info']); echo '<pre>';print_r($data);//exit();
//          }
//
//        ///$data['addition']=$this->User_model->get_all($select='name as cn_name,name_py as cn_py,mobile as cn_mobile',$where,$table='v_user_address',$order_title='addtime',$order='DESC');
//        //$this->load->view('bussell/app_gopay',$data);
//      }
//        $this->load->view('bussell/app_gopay',$data);
//    }
//    else
//    {
//      echo '订单过期，请重新下单！';
//    }
//  }



  //点单补充提交 联系人，出行人
//  public function gopay_com_sub_app()
// {
//   $order_id=$this->input->post('order_id',TRUE);
//     $is_benefit=$this->input->post('is_benefit',TRUE);
//
//     if($is_benefit)
//     {
//         $passport=$this->input->post('passport',TRUE);
//         $this->User_model->order_benefit_update($order_id,$passport);
//     }
//   $cx_name=$this->input->post('cx_name',TRUE);
//   $cx_py=$this->input->post('cx_py',TRUE);
//
//   $cx_cntel=$this->input->post('cx_cntel',TRUE);
//   $cx_otel=$this->input->post('cx_otel',TRUE);
//   //  print_r($cx_cntel);
//   // exit();
//   $cx=array();
//   if(is_array($cx_name))
//   {
//     foreach($cx_name as $k=>$v)
//     {
//       $cx[$k]['cn_name']=$cx_name[$k];
//       $cx[$k]['cn_py']=$cx_py[$k];
//       $cx[$k]['cn_mobile']=$cx_cntel[$k];
//       $cx[$k]['oth_mobile']=$cx_otel[$k];
//
//       $cx[$k]['order_id']=$order_id;
//       $cx[$k]['add_time']=time();
//       $cx[$k]['type']='2';
//
//       $cx[$k]['weixin']='';
//       $cx[$k]['mail']='';
//       $cx[$k]['cn_hotel']='';
//       $cx[$k]['en_hotel']='';
//       $cx[$k]['cn_hotel_address']='';
//       $cx[$k]['en_hotel_address']='';
//
//     }
//   }
//   $lx['cn_name']=$lx_name=$this->input->post('lx_name',TRUE);
//   $lx['cn_py']=$lx_py=$this->input->post('lx_py',TRUE);
//   $lx['cn_mobile']=$lx_cntel=$this->input->post('lx_cntel',TRUE);
//   $lx['oth_mobile']= $lx_other_tel=$this->input->post('lx_other_tel',TRUE);
//
//   $lx['order_id']=$order_id;
//   $lx['add_time']=time();
//   $lx['type']='1';
//
//   $lx['weixin']=$lx_wx=$this->input->post('lx_wx',TRUE);
//   $lx['mail']=$lx_mail=$this->input->post('lx_mail',TRUE);
//   $lx['cn_hotel']=$lx_hotel_cn=$this->input->post('lx_hotel_cn',TRUE);
//   $lx['en_hotel']= $lx_hotel_eng=$this->input->post('lx_hotel_eng',TRUE);
//   $lx['cn_hotel_address']=$lx_address_cn=$this->input->post('lx_address_cn',TRUE);
//   $lx['en_hotel_address']=$lx_address_eng=$this->input->post('lx_address_eng',TRUE);
//   $cx[]=$lx;
//
//
//   $jj['cn_name']=$this->input->post('jj_name',TRUE);
//   $jj['cn_py']=$this->input->post('jj_py',TRUE);
//   $jj['cn_mobile']=$this->input->post('jj_cntel',TRUE);
//   $jj['oth_mobile']=$this->input->post('jj_other_tel',TRUE);
//
//   $jj['order_id']=$order_id;
//   $jj['add_time']=time();
//   $jj['type']='3';
//   $jj['weixin']='';
//   $jj['mail']='';
//   $jj['cn_hotel']='';
//   $jj['en_hotel']='';
//   $jj['cn_hotel_address']='';
//   $jj['en_hotel_address']='';
//
//
//   $cx[]=$jj;
//  // echo '<pre>';print_r($cx);exit();
//   $rs=$this->User_model->get_count(array('order_id'=>$order_id),'v_order_addition');
//   if($rs['count']==0){
//     $this->User_model->user_insert('v_order_addition',$cx,2);
//   }else{
//     $this->User_model->del(array('order_id'=>$order_id),'v_order_addition');
//     $this->User_model->user_insert('v_order_addition',$cx,2);
//   }
//
//
// //  $this->User_model->user_insert('v_order_addition',$cx,$one='2');
//   $rs=$this->User_model->get_select_one('user_id_buy,goods_amount,order_sn,user_id_buy,goods_all_num,order_sn',array('order_id'=>$order_id),'v_order_info');
//
//   $this->User_model->update_one(array('type'=>'1',"user_id=>$rs[user_id_buy]"),array('is_show'=>'2'),$table='v_user_address');
//   $this->User_model-> user_insert($table='v_user_address',array('type'=>'1',"user_id"=>$rs['user_id_buy'],"name"=>$lx_name,"name_py"=>$lx_py,'addtime'=>time(),'mobile'=>$lx_cntel),$one='1');
//
//   $count=$this->User_model->get_count(array('order_id'=>$order_id),'v_order_addition');
//   if($count['count']==0){
//     $this->User_model->user_insert('v_order_addition',$cx,2);
//   }else{
//     $this->User_model->del(array('order_id'=>$order_id),'v_order_addition');
//     $this->User_model->user_insert('v_order_addition',$cx,2);
//   }
//
//  $this->User_model->update_one(array('order_id'=>$order_id),array('order_status'=>'0'),$table='v_order_info');
//
//
//   $json=array();
//   $json['order_id']=$order_id;
//   $json['user_id_buy']=$rs['user_id_buy'];
//   $json['prod']='1';
//   if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
//     $json['number']=$rs['goods_all_num'];
//     $json['order_sn']=$rs['order_sn'];
//     $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
//   }else{
//     $json['num']=$rs['goods_all_num'];
//   }
//   $json['amount']=$rs['goods_amount'];
//   $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
//   $json['productName']='特价';
//   echo json_encode($json);
//
// }



//  public function gopay_com_sub_wx()
//  {
//    $order_id=$this->input->post('order_id',TRUE);
//      $is_benefit=$this->input->post('is_benefit',TRUE);
//      if($is_benefit)
//      {
//          $passport=$this->input->post('passport',TRUE);
//          $this->User_model->order_benefit_update($order_id,$passport);
//      }
//
//
//    $cx_name=$this->input->post('cx_name',TRUE);
//    $cx_py=$this->input->post('cx_py',TRUE);
//   // echo '<pre>';print_r($_POST);exit();
//
//
//
//    $cx_cntel=$this->input->post('cx_cntel',TRUE);
//    $cx_otel=$this->input->post('cx_otel',TRUE);
//  //  print_r($cx_cntel);
//   // exit();
//    $cx=array();
//    if(is_array($cx_name))
//    {
//      foreach($cx_name as $k=>$v)
//      {
//        $cx[$k]['cn_name']=$cx_name[$k];
//        $cx[$k]['cn_py']=$cx_py[$k];
//        $cx[$k]['cn_mobile']=$cx_cntel[$k];
//        $cx[$k]['oth_mobile']=$cx_otel[$k];
//
//        $cx[$k]['order_id']=$order_id;
//        $cx[$k]['add_time']=time();
//        $cx[$k]['type']='2';
//
//        $cx[$k]['weixin']='';
//        $cx[$k]['mail']='';
//        $cx[$k]['cn_hotel']='';
//        $cx[$k]['en_hotel']='';
//        $cx[$k]['cn_hotel_address']='';
//        $cx[$k]['en_hotel_address']='';
//
//      }
//    }
//
//
//    $lx['cn_name']=$lx_name=$this->input->post('lx_name',TRUE);
//    $lx['cn_py']=$lx_py=$this->input->post('lx_py',TRUE);
//    $lx['cn_mobile']=$lx_cntel=$this->input->post('lx_cntel',TRUE);
//    $lx['oth_mobile']= $lx_other_tel=$this->input->post('lx_other_tel',TRUE);
//
//    $lx['order_id']=$order_id;
//    $lx['add_time']=time();
//    $lx['type']='1';
//
//    $lx['weixin']=$lx_wx=$this->input->post('lx_wx',TRUE);
//    $lx['mail']=$lx_mail=$this->input->post('lx_mail',TRUE);
//    $lx['cn_hotel']=$lx_hotel_cn=$this->input->post('lx_hotel_cn',TRUE);
//    $lx['en_hotel']= $lx_hotel_eng=$this->input->post('lx_hotel_eng',TRUE);
//    $lx['cn_hotel_address']=$lx_address_cn=$this->input->post('lx_address_cn',TRUE);
//    $lx['en_hotel_address']=$lx_address_eng=$this->input->post('lx_address_eng',TRUE);
//    $cx[]=$lx;
//
//
//    $jj['cn_name']=$this->input->post('jj_name',TRUE);
//    $jj['cn_py']=$this->input->post('jj_py',TRUE);
//    $jj['cn_mobile']=$this->input->post('jj_cntel',TRUE);
//    $jj['oth_mobile']=$this->input->post('jj_other_tel',TRUE);
//
//    $jj['order_id']=$order_id;
//    $jj['add_time']=time();
//    $jj['type']='3';
//    $jj['weixin']='';
//    $jj['mail']='';
//    $jj['cn_hotel']='';
//    $jj['en_hotel']='';
//    $jj['cn_hotel_address']='';
//    $jj['en_hotel_address']='';
//
//
//    $cx[]=$jj;
//    //echo '<pre>';print_r($cx);exit();
//    $rs=$this->User_model->get_count(array('order_id'=>$order_id),'v_order_addition');
//    if($rs['count']==0){
//      $this->User_model->user_insert('v_order_addition',$cx,2);
//    }else{
//      $this->User_model->del(array('order_id'=>$order_id),'v_order_addition');
//      $this->User_model->user_insert('v_order_addition',$cx,2);
//    }
//
//
//   //var_dump($info);
//    include_once("./application/third_party/wxpay/WxPay.php");
//    $rs=$this->User_model->get_select_one('goods_amount,order_sn,user_id_buy_fromwx,appId,nonceStr,timeStamp,signType,package,paySign',array('order_id'=>$order_id),'v_order_info');
//      if($rs['appId']!='0')
//      {
//          $arr_get=array(
//              'appId'=>$rs['appId'],
//              'nonceStr'=>$rs['nonceStr'],
//              'timeStamp'=>$rs['timeStamp'],
//              'signType'=>$rs['signType'],
//              'package'=>$rs['package'],
//              'paySign'=>$rs['paySign'],
//          );
//          $arr=array('order_id'=>$order_id,'json'=>json_encode(json_encode($arr_get)));
//          echo json_encode($arr);
//
//      }
//      else
//      {
//          $this->User_model->update_one(array('type'=>'2',"user_id=>$rs[user_id_buy_fromwx]"),array('is_show'=>'2'),$table='v_user_address');
//          $this->User_model->user_insert($table='v_user_address',array('type'=>'2',"user_id"=>$rs['user_id_buy_fromwx'],"name"=>$lx_name,"name_py"=>$lx_py,'addtime'=>time(),'mobile'=>$lx_cntel),$one='1');
//
//          //print_r($rs);exit();
//          $fee=floatval($rs['goods_amount'])*100;
//
//          if(isset($_SESSION['openidfromwx']))
//          {
//              $openid=$_SESSION['openidfromwx'];
//          }
//          else
//          {
//              $url=base_url("bussell/gopay_com");
//              $this->get_wx_userid($url);
//              $openid=$_SESSION['openidfromwx'];
//          }
//          $unifiedOrder = new UnifiedOrder_pub();
//          $jsApi = new JsApi_pub();
//          $unifiedOrder->setParameter("openid","$openid");//商品描述
//          $unifiedOrder->setParameter("body","特价产品");//商品描述
//
//          $out_trade_no =$rs['order_sn'];
//          $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
//          $unifiedOrder->setParameter("total_fee","$fee");//总金额
//          $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
//          $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
//          //  echo $out_trade_no;exit();
//          $prepay_id = $unifiedOrder->getPrepayId();
//          // echo $prepay_id;exit();
//          $jsApi->setPrepayId($prepay_id);
//          $jsApiParameters = $jsApi->getParameters();
//          $_SESSION['jsApiParameters']=$jsApiParameters;
//
//          $prr_temp=json_decode($jsApiParameters,TRUE);
//          $parm=array(
//              'appId'=>$prr_temp['appId'],
//              'nonceStr'=>$prr_temp['nonceStr'],
//              'timeStamp'=>$prr_temp['timeStamp'],
//              'signType'=>$prr_temp['signType'],
//              'package'=>$prr_temp['package'],
//              'paySign'=>$prr_temp['paySign'],
//              'order_status'=>'0'
//          );
//          $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
//          $jsApiParameters=json_encode($jsApiParameters);
//          $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
//          echo json_encode($arr);
//      }
//
//
//
//  }




  public function get_attr_price(){
    $goods_attr_id=$this->input->post('goods_attr_id',TRUE);
    $row=$this->User_model->get_select_one('attr_price',array('goods_attr_id'=>$goods_attr_id),'v_goods_attr');
    echo $row['attr_price'];
  }
//获取中文月份
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

  //获取微信授权
  public function get_wx_userid($url)
  {
    include_once("./application/third_party/wxpay/WxPay.php");
    $jsApi = new JsApi_pub();
    if(isset($_SESSION['wx_user_id']) AND isset($_SESSION['openidfromwx']))
    {
      return $_SESSION['wx_user_id'];
    }
    else
    {
      if (!isset($_GET['code']))
      {
        //base_url("bussell/order_add_fromwx?act_id={$act_id}")
        //触发微信返回code码
        $url = $jsApi->createOauthUrlForCode_all($url);
        Header("Location: $url");
      }
      else
      {
        //获取code码，以获取openid
        $code = $_GET['code'];
        $jsApi->setCode($code);
        //$openid = $jsApi->getOpenId();
        $wxuserinfo = $jsApi->wxuserinfo($code);
        //echo "<pre>";print_r($wxuserinfo);die;
        $openid=$wxuserinfo['openid'];
        $user_name=$wxuserinfo['nickname'];
        $sex=$wxuserinfo['sex'];
        if($sex==1)
        {
          $sex_et='0';
        }
        elseif($sex==2)
        {
          $sex_et='1';
        }
        else
        {
          $sex_et='2';
        }
        $lan=$wxuserinfo['language'];
        $address=$wxuserinfo['city'];
        $image=$wxuserinfo['headimgurl'];
        $num=strripos($image,'/');
        //$numall=strlen($str);
        $image=substr($image,0,$num);
        $wxinfo=$this->User_model->get_select_one($select='openid,user_id',array('openid'=>$openid),'v_wx_users');
        if($wxinfo){
          $_SESSION['openidfromwx']=$wxinfo['openid'];
          $_SESSION['wx_user_id']=$wxinfo['user_id'];
          return $wxinfo['user_id'];
        }else{
          $datauser=array(
              'openid'=>$openid,
              'register_time'=>time(),
              'regist_type'=>'7',
              'user_name'=>$user_name,
              'sex'=>$sex_et,
              'lan'=>$lan,
              'address'=>$address,
              'image'=>$image.'/96'

          );
          $_SESSION['openidfromwx']=$openid;
          $_SESSION['wx_user_id']=$this->User_model->user_insert($table='v_wx_users',$datauser);
          return $_SESSION['wx_user_id'];
        }
      }
    }

  }


//微信订单确认
  public function wx_confirm()
  {
    $data['order_id']=$this->input->get('order_id');
    //$data['order_id']=$_SESSION['order_id'];
    $data['order_info']=$this->Order_model->order_list_home(array('order_id'=>$data['order_id']));
    $url=base_url("bussell/wx_shop_list");
    $user_id=$data['user_id']=$this->get_wx_userid($url);
    $wxinfo=$this->User_model->get_select_one('appId,nonceStr,timeStamp,signType,package,paySign',array('order_id'=>$data['order_id']),'v_order_info');

    $data['json']=array(
        'appId'=>$wxinfo['appId'],
        'nonceStr'=>$wxinfo['nonceStr'],
        'timeStamp'=>$wxinfo['timeStamp'],
        'signType'=>$wxinfo['signType'],
        'package'=>$wxinfo['package'],
        'paySign'=>$wxinfo['paySign'],
    );
    $data['json']=json_encode($data['json']);
  //  echo'<pre>'; print_r($data);exit();
    $this->load->view('bussell/wxpayjs',$data);
  }
  //wx 支付成功页面
  public function pay_succeed()
  {
    $type=$this->input->get('type',true);
    if($type==1)
    {
      $order_id=$this->input->get('order_id',true);
      $data=$this->User_model->get_select_one($select='user_id_sell_name,goods_amount,order_amount,order_sn,pay_time',
          array('order_id'=>$order_id),'v_order_info');


    }
    elseif($type==2)
    {
        $order_id=$this->input->get('order_id',true);
        $data=$this->User_model->get_select_one($select='user_id_sell_name,goods_amount,order_amount,order_sn,pay_time,front_amount,from',
            array('order_id'=>$order_id),'v_order_info');
        if($data['from']=='8'){
            $data['goods_amount']=$data['front_amount'];
        }
//        $arr=$this->User_model->get_select_one('goods_attr_id',array('order_id'=>$order_id),'v_order_goods');
//        $arr=json_decode($arr['goods_attr_id'],TRUE);
//        $user_updae=array(
//            'photo_starttime'=>strtotime($arr['date']),
//            'photo_endtime'=>strtotime($arr['date'])+$arr['photo_time'],
//        );
//        $this->User_model->update_one(array('user_id'=>$arr['pho_id']),$user_updae,'v_users');

    }

    $this->load->view('bussell/wx_pay_succeed',$data);
  }
  //wx 支付失败页面
  public function pay_fail()
  {
   
    $type=$this->input->get('type',true);
      $call_url=$this->input->get('call_url',true);
      $data['call_url']='javascript:void(0);';
    if($type==1)
    {
      $order_id=$this->input->get('order_id',true);
      $data=$this->User_model->get_select_one($select='user_id_sell_name,goods_amount,order_amount,order_sn,add_time',
          array('order_id'=>$order_id),'v_order_info');
      $data['json']=json_encode($_SESSION['jsApiParameters']);
      $data['order_id']=$order_id;
      // echo $data['json'];exit();
        $data['call_url']=base64_decode($call_url);
    }
    else
    {
      $data=array();
    }
    unset($_SESSION);
    $this->load->view('bussell/wx_pay_fail',$data);
  }





  //订单更新
  public function order_update_wx(){
    include_once("./application/third_party/wxpay/WxPay.php");
    $order_id=trim($this->input->post_get('order_id'));
    $order_sn_from=trim($this->input->post_get('order_sn',true));
    $rs=$this->User_model->get_select_one('order_sn',array('order_id'=>$order_id),'v_order_info');
    if($rs>0){
      $order_sn=$rs['order_sn'];
      if($order_sn_from==$order_sn)
      {
        $obj=new OrderQuery_pub();
        //$rsxml=$obj->createXmlfororder($order_sn);
        $obj->setParameter("out_trade_no",$order_sn);
        $rsxml=$obj->createXml();
        $rsxml=$obj->returnorderxml($rsxml);
        $arrob=$obj->xmlToArray($rsxml);
        //echo '<pre>';print_r($arrob);exit();
        if(isset($arrob['trade_state']) && $arrob['trade_state']=='SUCCESS')
        {
          $data=array(
              'order_status'=>'1',
              'pay_time'=>time(),
              'pay_id'=>2,
          );
          if($this->User_model->update_one(array('order_id'=>$order_id),$data,$table='v_order_info'))
          {
            echo 1;
          }
          else
          {
            echo 2;
          }
        }
      }
      else
      {
        echo 3;
      }
    }
    else
    {
      echo 2;
    }

  }

  //订单信息提交
  public function order_sub(){
    $user_id_buy=$_SESSION['user_id'];
    $address=trim($this->input->post('address',true));
    $mobile=trim($this->input->post('mobile',true));
    $consignee=trim($this->input->post('consignee',true));
    $commont=trim($this->input->post('commont',true));

    $act_id=trim($this->input->post('act_id',true));

    $user_id_sell=$this->User_model->get_select_one($select='user_id',array('act_id'=>$act_id),'v_activity_children');
    $user_id_sell=$user_id_sell['user_id'];
    $address_arr=array(
        'user_id'=>$user_id_buy,
        'address'=>$address,
        'mobile'=>$mobile,
        'consignee'=>$consignee,
    );

    $user_id_buy_name=$this->User_model->get_username($user_id_buy);
    $user_id_sell_name=$this->User_model->get_username($user_id_sell);
    $order_sn=$this->get_order_sn();

    $goods_id=$this->input->post('goods_id',true);
    $goods_number=$this->newpost('goods_number');
    //$goods_number=$this->input->post('goods_number',true);

    $goods_id=explode(',',$goods_id);
    $goods_id=array_filter($goods_id);
    $goods_number=explode(',',$goods_number);
    $goods_number=array_filter($goods_number);
    $amount=0;
    $goods_arr=array();
      $oori_price=0;
    foreach($goods_id as $k=>$v){
      $temp=$this->User_model->get_goodsinfo($v,$act_id);
      if(!$temp){
        echo 1;
        exit();
      }
      elseif($temp['goods_number']<$goods_number[$k])
      {
        echo 2;
        exit();
      }
      else
      {
          $oori_price=$temp['oori_price'];
        $amount +=$goods_number[$k]*$temp['shop_price'];
      }
    }
    $goods_all_number=0;
    foreach($goods_number as $k=>$v){
      $goods_all_number+=$v;
    }
    $order_arr=array(
        'user_id_buy'=>$user_id_buy,
        'user_id_buy_name'=>$user_id_buy_name,
        'user_id_sell'=>$user_id_sell,
        'user_id_sell_name'=>$user_id_sell_name,
        'order_sn'=>$order_sn,
        'consignee'=>$consignee,
        'address'=>$address,
        'mobile'=>$mobile,
        'commont'=>$commont,
        'goods_amount'=>$amount,
        'order_amount'=>$amount,
        'goods_all_num' =>$goods_all_number,
        'add_time'=>time()
    );

    foreach($goods_id as $k=>$v)
    {
      $temp=$this->User_model->get_goodsinfo($v,$act_id);
      $order_goods  =  array(
          'goods_id' =>$v,
          'goods_name' =>$temp['goods_name'],
          'goods_number' =>$goods_number[$k],
          'goods_price' =>$temp['shop_price'],
          'act_id' =>$act_id,
          'oori_price' =>$oori_price,
          'goods_sum'   =>$goods_number[$k]*$temp['shop_price']
      );
      $goods_arr[]=$order_goods;
    }
    $rs=$this->User_model->trans_order_sub($order_arr,$goods_arr,'v_order_info','v_order_goods');

    $prod=base_url("bussell/bus_children_detail_app/{$act_id}?menu=1");;
    if($rs>0)
    {
      $json=$this->Order_model->order_list_home(array('order_id'=>$rs),$start=0,$page_num=5,$prod);
      $json=$json[0]['json'];
      //$strphone="olook://payorder.toapp?fukuan&order_info<'.$json";
      //$stran="olook://payorder.toapp>fukuan&order_info<'.$json";
      if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
      {
        $json=array(3,$json);
        echo json_encode($json);
        //redirect("bussell/order_add_app?act_id={$act_id}&tip=3&json={$json}");
        //redirect("$strphone");

      }
      else
      {
        $json=array(4,$json);
        echo json_encode($json);
        //redirect("bussell/order_add_app?act_id={$act_id}&tip=4&json={$json}");
        //redirect("$stran");
      }
    }else{
      echo 2;
    }
  }
    /*//微信提交普通商品订单
     * 2016 12 06
     */


  public function order_sub_wx(){
    include_once("./application/third_party/wxpay/WxPay.php");
    $act_id=trim($this->input->post('act_id',true));
    $share_user_id=trim($this->input->post('share_user_id',true));

    $url=base_url("bussell/order_add_fromwx?act_id={$act_id}&share_user_id=$share_user_id");
    $user_id_buy=$data['user_id']=$this->get_wx_userid($url);

    $address=trim($this->input->post('address',true));
    $mobile=trim($this->input->post('mobile',true));
    $consignee=trim($this->input->post('consignee',true));
    $commont=trim($this->input->post('commont',true));



    $user_id_sell=$this->User_model->get_select_one($select='user_id',array('act_id'=>$act_id),'v_activity_children');
    $user_id_sell=$user_id_sell['user_id'];
    $address_arr=array(
        'user_id'=>$user_id_buy,
        'address'=>$address,
        'mobile'=>$mobile,
        'consignee'=>$consignee,
    );
    $count=$this->User_model->get_count(array('user_id'=>$user_id_buy),'v_wx_user_address');
    if($count['count']>0)
    {
      $this->User_model->update_one(array('user_id'=>$user_id_buy),$address_arr,'v_wx_user_address');
    }
    else
    {
      $this->User_model->user_insert($table='v_wx_user_address',$address_arr);
    }
    $user_id_buy_name=$this->User_model->get_username($user_id_buy,'v_wx_users');
    $user_id_sell_name=$this->User_model->get_username($user_id_sell);
    $order_sn=$this->get_order_sn();

    $goods_id=$this->input->post('goods_id',true);
    $goods_number=$this->newpost('goods_number');
   // $goods_number=$this->input->post('goods_number',true);

    $goods_id=explode(',',$goods_id);
    $goods_id=array_filter($goods_id);
    $goods_number=explode(',',$goods_number);
    $goods_number=array_filter($goods_number);
    $amount=0;
    $goods_arr=array();
      $oori_price=0;
    foreach($goods_id as $k=>$v)
    {
      $temp=$this->User_model->get_goodsinfo($v,$act_id);
      if(!$temp)
      {
        echo 1;
        exit();
      }
      elseif($temp['goods_number']<$goods_number[$k])
      {
        echo 2;
        exit();
      }
      else
      {
        $amount +=$goods_number[$k]*$temp['shop_price'];
          $oori_price=$temp['oori_price'];
      }
    }
    $goods_all_number=0;
    foreach($goods_number as $k=>$v){
      $goods_all_number+=$v;
    }
    $order_arr=array(
        'user_id_buy_fromwx'=>$user_id_buy,
        'user_id_buy_name_fromwx'=>$user_id_buy_name,
        'from'=>'1',
        'user_id_sell'=>$user_id_sell,
        'user_id_sell_name'=>$user_id_sell_name,
        'order_sn'=>$order_sn,
        'consignee'=>$consignee,
        'address'=>$address,
        'mobile'=>$mobile,
        'commont'=>$commont,
        'goods_amount'=>$amount,
        'order_amount'=>$amount,
        'goods_all_num' =>$goods_all_number,
        'add_time'=>time(),
        'share_user_id'=>$share_user_id
    );

    foreach($goods_id as $k=>$v){
      $temp=$this->User_model->get_goodsinfo($v,$act_id);
      $order_goods  =  array(
          'goods_id' =>$v,
          'goods_name' =>$temp['goods_name'],
          'goods_number' =>$goods_number[$k],
          'goods_price' =>$temp['shop_price'],
          'goods_sum'   =>$goods_number[$k]*$temp['shop_price'],
          'act_id'=>$act_id,
          'oori_price' =>$oori_price,
      );
      $goods_arr[]=$order_goods;
    }
    $rs=$this->User_model->trans_order_sub($order_arr,$goods_arr,'v_order_info','v_order_goods');
    if($rs>0)
    {
      $order_info=$this->User_model->get_select_one($select='order_sn,goods_amount',array('order_id'=>$rs),$table='v_order_info');
      $fee=floatval($order_info['goods_amount'])*100;
      // print_r($order_info);die;

      $unifiedOrder = new UnifiedOrder_pub();
      $jsApi = new JsApi_pub();

      $openid=$_SESSION['openidfromwx'];

      $unifiedOrder->setParameter("openid","$openid");//商品描述
      $unifiedOrder->setParameter("body","商品付款");//商品描述

      $out_trade_no = $order_info['order_sn'];
      $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
      $unifiedOrder->setParameter("total_fee","$fee");//总金额
      $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
      $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
      //  echo $out_trade_no;exit();
      $prepay_id = $unifiedOrder->getPrepayId();

      $jsApi->setPrepayId($prepay_id);

      $jsApiParameters = $jsApi->getParameters();
      $prr_temp=json_decode($jsApiParameters,TRUE);
      $parm=array(
          'appId'=>$prr_temp['appId'],
          'nonceStr'=>$prr_temp['nonceStr'],
          'timeStamp'=>$prr_temp['timeStamp'],
          'signType'=>$prr_temp['signType'],
          'package'=>$prr_temp['package'],
          'paySign'=>$prr_temp['paySign']
      );
      $this->User_model->update_one(array('order_id'=>$rs),$parm,$table='v_order_info');
      $_SESSION['jsApiParameters']=$jsApiParameters;
      $_SESSION['order_sn']=$order_info['order_sn'];
      $_SESSION['order_id']=$rs;
     //$json=array($rs,$order_info['order_sn'],$jsApiParameters);

      echo $rs;
    }else{
      echo 2;
    }
  }
//官方用微信商铺后台
  public function wx_shop_list_admin($page=1)
  {

   // $where="(is_show='1' OR is_show='2')";
      $is_show=$this->input->get('is_show',TRUE);
      if(!$is_show){
          $is_show='2';
      }
      $where="  (is_show=$is_show)";
      $data['is_show']=$is_show;
    $data['title']=$title=trim($this->input->get('title',true));
    if($title)
    {
      $where="  business_name LIKE '%$title%' OR tag  LIKE '%$title%'";
    }
    $page_num =80;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_wx_business');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list']=$this->User_model->get_select_more($select='business_id,user_id,business_name,star_num,discount,tag,logo_image_thumb AS logo_image,is_show',
        $where,$start,$page_num,$order_title='business_name',$order='ASC',$table='v_wx_business');
    if($data['list']!==0)
    {
      foreach($data['list'] as $k=>$v)
      {
        $data['list'][$k]['tag_arr']=explode(',',$v['tag']);
      }
    }

    if($this->input->get('test',true)){
      echo '<pre>';print_r($data);exit();
    }
    $this->load->view('bussell/shop_list_admin',$data);
  }




  //官方后台加商铺
  public function add_wx_shop()
  {

    $this->load->view('bussell/add_shop');
  }
  //修改微信店铺
  public function edit_wx_shop()
  {
    $business_id=$this->input->get('business_id',true);
    $data=$this->User_model->get_select_one('business_id,user_id,business_name,star_num,tag,lng,lat,discount,currency,is_apply,p_user_id,
    business_info,business_address,business_tel,logo_image_thumb AS image',array('business_id'=>$business_id),'v_wx_business');
    if($this->input->get('test')){
      echo '<pre>';print_r($data);exit();
    }
    $this->load->view('bussell/add_shop',$data);
  }

//官方后台插入商铺数据
  public function shop_insert()
  {
    $data['user_id']=trim($this->input->post('user_id',true));
    $data['currency']=trim($this->input->post('currency',true));

    switch ($data['currency'])
    {
      case 'THB':
        $data['currency_name']='泰铢';
        break;
      case 'USD':
        $data['currency_name']='美元';
        break;
      default:
        $data['currency_name']='人民币';
        $data['currency']='CNY';

    }

    $data['business_name']=trim($this->input->post('business_name',true));
   // $data['star_num']=trim($this->input->post('star',true));
    $tag=trim($this->input->post('tag',true));
    $data['tag']=str_replace('，',',',$tag);
    $location=trim($this->input->post('location',true));
    $location=str_replace('，',',',$location);
    $location= explode(',',$location);
    $data['lng']=$location[1];
    $data['lat']=$location[0];
    $data['discount']=$this->input->post('discount',true);
    $data['business_info']=$this->input->post('business_info',true);
    $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

    $data['business_address']=$this->input->post('address',true);
    $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

    $data['business_tel']=$this->input->post('business_tel',true);

    $data['is_apply']=$this->input->post('is_apply',TRUE);
    $logo_image=$this->upload_image('file1', $data['user_id']);

    $data['logo_image']=$logo_image;
    // thumb($url,$key1,$key2='time',$width='702',$height='300')

    $data['logo_image_thumb']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
    $business_id=$this->User_model->user_insert($table='v_wx_business',$data);
    $this->put_admin_log("插入商铺数据{$business_id}");

    redirect('bussell/wx_shop_list_admin');
  }
//商铺修改信息提交
  public function shop_sub()
  {
    $data['user_id']=trim($this->input->post('user_id',true));
    $data['currency']=trim($this->input->post('currency',true));
    switch ($data['currency'])
    {
      case 'THB':
        $data['currency_name']='泰铢';
          break;
      case 'USD':
        $data['currency_name']='美元';
        break;
      default:
        $data['currency_name']='人民币';
        $data['currency']='CNY';

    }


    $business_id=trim($this->input->post('business_id',true));
    $data['business_name']=trim($this->input->post('business_name',true));
   // $data['star_num']=trim($this->input->post('star',true));
    $tag=trim($this->input->post('tag',true));
    $data['tag']=str_replace('，',',',$tag);
    $location=trim($this->input->post('location',true));
    $location=str_replace('，',',',$location);
    $location= explode(',',$location);
    $data['lng']=$location[1];
    $data['lat']=$location[0];
    $data['discount']=$this->input->post('discount',true);
    $data['business_info']=$this->input->post('business_info',true);
    $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

    $data['business_address']=$this->input->post('address',true);
    $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

    $data['business_tel']=$this->input->post('business_tel',true);
    $data['is_apply']=$this->input->post('is_apply',TRUE);

    if(isset($_FILES['file1']))
    {
      if($_FILES['file1']['error']==0){
        $logo_image=$this->upload_image('file1', $data['user_id']);
        $data['logo_image']=$logo_image;
        $data['logo_image_thumb']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
      }
    }
    //echo '<pre>';echo $business_id;print_r($data);
    $this->User_model->update_one(array('business_id'=>$business_id),$data,$table='v_wx_business');
    //echo $this->db->last_query();
      $this->put_admin_log("商铺信息修改{$business_id}");
    redirect("bussell/show_shop_admin?business_id=$business_id");
  }

  //下架店铺
  public function down_shop()
  {
    $business_id=$this->input->post('business_id',true);


    $this->User_model->update_one(array('business_id'=>$business_id),array('is_show'=>'2','displayorder'=>9200),$table='v_wx_business');
      $user_id=$this->User_model->get_select_one('user_id',array('business_id'=>$business_id),'v_wx_business');
      $user_id=$user_id['user_id'];
    $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>'0'),$table='v_users');

    $this->put_admin_log("下架店铺{$business_id}");
    echo 1;
  }
  //删除店铺
  public function del_shop()
  {
    $business_id=$this->input->post('business_id',true);
    $this->User_model->update_one(array('business_id'=>$business_id),array('is_show'=>'3','displayorder'=>9300),$table='v_wx_business');
    //$this->User_model->del(array('business_id'=>$business_id),$table='v_wx_business');
      $user_id=$this->User_model->get_select_one('user_id',array('business_id'=>$business_id),'v_wx_business');
      $user_id=$user_id['user_id'];
    //  $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>'0'),$table='v_users');
    $this->put_admin_log("删除店铺{$business_id}");
    echo 1;
  }

//官方后台微信店铺修改
  public function show_shop_admin()
  {
    $business_id=$this->input->get('business_id',true);
      if(!$business_id){
          return false;
      }
    $data['business']=$this->User_model->get_select_one('business_id,user_id,business_name,lat,lng,is_show,
    star_num,discount,tag,business_tel,logo_image_thumb AS logo_image,business_info,business_address',array('business_id'=>$business_id),'v_wx_business');
    $data['business']['tag_arr']=explode(',', $data['business']['tag']);
    //$data=$this->input->get_select_one();
    $lat= $data['business']['lat'];
    $lng= $data['business']['lng'];
    //getAround($lat,$lon,$raidus)
    $location=$this->getAround($lat,$lng,'400000');

    $data['shopper_video']=$this->User_model->get_wx_bus_video_by_user_id($data['business']['user_id']);
    foreach($data['shopper_video'] as $k=>$v){
      if($v['is_off']==0)
      {
        $data['shopper_video'][$k]['path']= $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
      }
      else
      {
        $data['shopper_video'][$k]['path']="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
      }

    }
    $arr_len=count($data['shopper_video']);
    if($arr_len>=10)
    {
      $data['video_nearby']=array();
    }
    else
    {
      $num=10-$arr_len;
      $data['video_nearby']=$this->User_model->get_wx_bus_video_by_location($location,$start=0,$num);

      foreach($data['video_nearby'] as $k=>$v)
      {
        if($v['is_off']==0)
        {
          $data['video_nearby'][$k]['path']= $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
        }
        else
        {
          $data['video_nearby'][$k]['path']="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
        }
      }
    }
    if($this->input->get('test')){
      //echo $this->db->last_query();
      echo '<pre>';print_r($data);exit;
    }

    //
    if(isset($_SESSION['admin_id']))
    {
      $this->load->view('bussell/show_shop',$data);
    }
    else
    {
      $this->load->view('bussell/show_shop_foruser',$data);
    }

  }

  //wx 店铺显示
  public function wx_shop_show()
  {
    $business_id=$this->input->post('business_id');
    $this->User_model->update_one(array('business_id'=>$business_id),array('is_show'=>'1'),$table='v_wx_business');

      $user_id=$this->User_model->get_select_one('user_id',array('business_id'=>$business_id),'v_wx_business');
      $user_id=$user_id['user_id'];
      $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>$business_id),$table='v_users');
    echo 1;
  }
  //验证图片大小
  public function size_validate($name,$size,$home='1')
  {
    $file_size=$_FILES[$name]['size'];
    if ($file_size>$size){
      if($home==1){
        return false;
      }else{
        echo '图片太大';
      }

    }
  }

  //验证
  public function user_id_and_open_id()
  {
    if(isset($_COOKIE['user_id']))
    {
      $user_id=$_SESSION['user_id']=$_COOKIE['user_id'];
      $where=array('user_id'=>$user_id);
      $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
      if(isset($_COOKIE['openid']))
      {
        $str=$row['openid'];
        $str=strtoupper(md5('ET'.$str));
        if($str==$_COOKIE['openid'])
        {
          $_SESSION['openid']=$_COOKIE['openid'];
          return $user_id;
        }
        else
        {
          return FALSE;
        }
      }
      else
      {
        return FALSE;
      }
    }
    elseif(isset($_COOKIE['olook']))
    {
      //print_r($_COOKIE);exit();
       $striso=$_COOKIE['olook'];
      $arrolook=explode('-',$striso);
      $user_id=$arrolook[0];
      $openid=$arrolook[1];
      $where=array('user_id'=>$user_id);
      $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
      $str=$row['openid'];
      $str=strtoupper(md5('ET'.$str));
      if($str==$openid)
      {
        $_SESSION['user_id']=$user_id;
        $_SESSION['openid']=$openid;
       // echo $user_id;exit();
        return $user_id;
      }
      else
      {
        return FALSE;
      }
    }
    elseif(isset($_SESSION['openid']))
    {
      // print_r($_SESSION['openid']);
      $user_id=$_SESSION['user_id'];
      $where=array('user_id'=>$user_id);
      $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
      $str=$row['openid'];
      $str=strtoupper(md5('ET'.$str));
      if($str==$_SESSION['openid'])
      {
        return $user_id;
      }
      else
      {
        return FALSE;
      }
    }
    else
    {
      return FALSE;
    }
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
//./public/images/1265/id_driver.jpg


//$source_path='./public/images/bus_act99/1468231068.jpg',$key1='test',$key2='time',$target_width='700',$target_height='300'
  function imagecropper($source_path='./tmp/logo100.png',$key1='test',$key2='time',$target_width='50', $target_height='50')
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
        }elseif($this->config->item('rtmp_flg') == 3){
          $result = $this->config->item('rtmp_uc_url').$video_name;
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

  //log
  public function put_admin_log($log_info)
  {
    if(isset($_SESSION['admin_id'])){
      $admin_id= $_SESSION['admin_id'];
    }else{
      redirect(baser_url("newadmin/login"));
    }
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
//  public function get_map_info()
//  {
//
//    //$time1=microtime();
//    set_time_limit(0);
//    $select='v_video.video_id,v_video.user_id,
//      	location,v_users.user_name,v_users.image as user_image,all_address,v_video.start_time,push_type,
//      	views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
//      		socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
//    $data=$this->User_model->get_select_all($select,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id",$sum=false,$L=0, $start=0,$page_num=100000);
//    //echo '<pre>';print_r($data);exit();
//    if($data===FALSE){
//      $this->data_back(array(),'0X000','success' );
//    }else{
//      if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
//        foreach($data as $k=>$v){
//          $data[$k]['level']=$this->get_level($v['credits']);
//          if($v['is_off']==1){
//            if($v['push_type']==0){
//              $data[$k]['path'] = $this->config->item('record_url').$v['video_name'].'.m3u8';
//            }else{
//              $data[$k]['path'] = $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
//            }
//            //$data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
//            $data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//            $data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
//          }else{
//            $data[$k]['path']=$this->get_rtmp($v['video_name']);
//            $data[$k]['share_replay_path'] ="";
//            $data[$k]['video_dec']='测试描述'.$v['title'];
//          }
//        }
//      }else{
//        foreach($data as $k=>$v){
//          $data[$k]['level']=$this->get_level($v['credits']);
//          if($v['is_off']==1){
//            $data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
//            $data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//            $data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
//          }else{
//            $data[$k]['path']=$this->get_rtmp($v['video_name']);
//            $data[$k]['share_replay_path'] ="";
//            $data[$k]['video_dec']='测试描述'.$v['title'];
//          }
//        }
//      }
//     // $this->benchmark->mark('another_mark_end');
//
//     //print_r(json_encode($data));
//      //echo '<hr>';
//    //  $time2=microtime();
//      //echo $time2-$time1;
//      //echo $this->benchmark->elapsed_time();
//      $this->data_back($data,'0X000','success');
//    }
//  }
//  public function get_map_shop()
//  {
//    $user_id=$this->input->get_post('user_id',TRUE);
//    $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
//    if($location && $user_id)
//    {
//      $arr=explode(',',$location);
//      $lat=$arr[0];
//      $lng=$arr[1];
//      $arr=$this->getAround($lat,$lng,'50000');
//      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng]  AND lat !=''";
//
//      $where.=" AND is_show = '1'";
//      $select="business_info,is_apply,type,address,business_id,logo_image_thumb AS logo_image,logo_image_thumb AS image,business_name,business_name as title,star_num,discount,tag,lat,lng,lat AS latitude,lng AS longitude,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";
//
//      $list=$this->User_model->get_select_more($select,$where,0,50,$order_title="distance",$order='DESC',$table='v_wx_business');
//
//      if($list==0){
//        $list=array();
//      }
//      foreach($list as $k=>$v){
//        $list[$k]['shop_url']=base_url('bussell/business_info_app?business_id=').$v['business_id'];
//        $list[$k]['api_url']=base_url('api/get_app_business_info?');
//        if($list[$k]['business_info']==''){
//          $list[$k]['business_info']=$list[$k]['tag'];
//        }
//      }
//      $temp_text=$this->User_model->get_select_one('temp_text',array('user_id'=>$user_id),'v_users');
//      $temp_text=json_decode($temp_text['temp_text'],TRUE);
//      echo '<pre>';
//      print_r($temp_text);
//      if(!is_array($temp_text))
//      {
//        $temp_text=array();
//      }
//      foreach($list as $k=>$v)
//      {
//        if(in_array($v['business_id'],$temp_text))
//        {
//          unset($list[$k]);
//        }
//        else
//        {
//          $temp_text[]=$v['business_id'];
//        }
//      }
//      $list=array_values($list);
//      $temp_text=json_encode($temp_text);
//      $this->User_model->update_one(array('user_id'=>$user_id),array('temp_text'=>$temp_text),$table='v_users');
//      $this->data_back($list);
//    }else{
//      $this->data_back("参数为空", '0x011','fail');
//    }
//  }


////似乎废弃
//  public function get_map_video_info(){
//    $video_id=$this->input->get_post('video_id',true);
//    //$video_id='4021';
//    if(!$video_id){
//      $this->data_back('参数为空','0X011','fail');
//    }else{
//      $where=array('video_id'=>$video_id);
//      $row=$this->User_model->get_select_one('views,praise,video_name,user_id,title,location,image,is_off,socket_info',$where,'v_video');
//      $user_id=$row['user_id'];
//      $where=array('user_id'=>$user_id);
//      $data=$this->User_model->get_select_one('user_name,user_id,credits,image as user_image,sex,is_guide,is_attendant,is_driver,is_merchant,auth',$where,'v_users');
//
//      //$data=$this->User_model->get_select_one('user_name,user_id,image as user_image',$where,'v_users');
//      $data['level']=$this->get_level($data['credits']);
//      $data['video_id']=$video_id;
//      $data['title']=$row['title'];
//      $data['location']=$row['location'];
//      $data['image']=$row['image'];
//      $data['is_off']=$row['is_off'];
//      $data['socket_info']=$row['socket_info'];
//
//      if($row['is_off']==1){
//        $data['path']=$this->config->item('record_url').$row['video_name'].'.m3u8';
//        $data['views']=$row['views'];
//        $data['praise']=$row['praise'];
//      }else{
//        $data['path']=$this->get_rtmp($row['video_name']);
//      }
//
//      $this->data_back($data,'0X000','success');
//
//    }
//  }

  /**
   * 计算两组经纬度坐标 之间的距离
   * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
   * return m or km
   *
   *
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
 	/**
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




  public function get_nocer($act_id='65'){
    $data=$this->User_model->get_select_all($select='user_id',array('act_id'=>$act_id),'user_id','ASC','v_nocer_users');
    $arr=array();
    foreach($data as $k=>$v){
      $arr[]=$v['user_id'];
    }
    $arr=array_unique($arr);
    return $arr;
   // echo '<pre>';
   // print_r($arr);
  }

  public function get_crop_for_video(){
    set_time_limit(0);
    $data=$this->User_model->get_select_all($select='video_id',
        $where=" (imageforh5 IS NULL OR imageforh5 ='') AND is_off=1  ",$order_title='start_time',$order='ASC',$table='v_video');
    if($this->input->get('test')){
      echo $this->db->last_query();
      var_dump($data) ;
    }

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

  function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
    if(is_array($multi_array)){
      foreach ($multi_array as $row_array){
        if(is_array($row_array)){
          $key_array[] = $row_array[$sort_key];
        }else{
          return false;
        }
      }
    }else{
      return false;
    }
    array_multisort($key_array,$sort,$multi_array);
    return $multi_array;
  }



  public function newpost($parm,$t=TRUE){
    return trim($this->input->post_get($parm,$t));
  }




  public function get_auth_downcountry(){
    $arr=$this->User_model->get_select_all($select='name',"is_down='1'",$order_title='id',
        $order='ASC',$table='v_location');
    $newarr=array();
    if($arr!==false){
      foreach($arr as $k=>$v){
        $newarr[]=$v['name'];
      }
    }
    $this->data_back($newarr);
  }




  public function get_auth_downpart(){
    $arr=array('亚洲','欧洲','非洲','美洲','大洋洲','其他');
    $this->data_back($arr);
  }

  function get_rec($video_name,$push_type)
  {
    $result = '';
    if($video_name)
    {
      if($push_type == '0')
      {
        $result = $this->config->item('record_url').$video_name.'.m3u8';
      }
      elseif($push_type == '1')
      {
        $result = $this->config->item('record_uc_url').$video_name.'.m3u8';
      }
    }
    return $result;
  }

//  public function act_parted_in()
//  {
//    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
//    $data=$this->User_model->get_parted_act_new($user_id);
//    foreach($data as $k=>$v)
//    {
//      $data[$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data[$k]['content'])) ;
//      if(isset($v['banner_image']))
//      {
//        if(stristr($v['banner_image'], 'http')===false)
//        {
//         $data[$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'./');
//        }
//      }
//
//      if($v['1']==1)
//      {
//        $data[$k]['act_url']=base_url("bussell/bus_children_detail_app/{$v['act_id']}");
//      }
//      else
//      {
//        $data[$k]['act_url']=base_url("user/activiy_app_detail/{$v['act_id']}");
//      }
//      unset($data[$k]['1']);
//    }
//    $data=$this->common->unique($data);
//    $data=array_values($data);
//    $this->data_back($data);
//  }

//  public function act_applied()
//  {
//    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
//    if(!$user_id){
//      $this->data_back('参数为空','0X011','fail');
//    }
//    $new_data=array();
//    $where_father="special='0' AND (is_show='1' OR is_show ='2') AND user_id=$user_id";
//    $data['father']=$this->User_model->get_select_all('act_id,title,content,act_image',$where_father,'start_time','DESC','v_activity_father');
//    if( $data['father']===false){
//      $data['father']=array();
//    }
//    foreach( $data['father'] as $k=>$v){
//      $data['father'][$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data['father'][$k]['content'])) ;
//      $data['father'][$k]['act_url']=base_url("user/activiy_app_detail/{$v['act_id']}");
//      $new_data[]=$data['father'][$k];
//    }
//
//    $where_child="act_status='2' AND user_id=$user_id AND is_temp='0' AND special='0'";
//    $data['child']=$this->User_model->get_select_all('act_id,title,content_text as content',$where_child,'start_time','DESC','v_activity_children');
//    if( $data['child']===false){
//      $data['child']=array();
//    }
//    foreach( $data['child'] as $k=>$v){
//      $data['child'][$k]['content'] =trim(preg_replace("/<[^>]*>/i",'',$data['child'][$k]['content'])) ;
//      $data['child'][$k]['act_url']=base_url("bussell/bus_children_detail_app/{$v['act_id']}");
//      $new_data[]=$data['child'][$k];
//    }
//
//    //$data=array_unique($data);
//    echo '<pre>';
//    print_r($new_data);
//    $this->data_back($new_data);
//  }



//  public function auth_search(){
//    $data=array();
//    $where='1=1';
//    $auth_type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'auth';
//    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
//    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
//    $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
//    $auth_range = isset($_REQUEST['auth_range'])?$_REQUEST['auth_range']:0;
//    $title  = trim($this->input->get_post('title',true));
//    switch ($auth_type)
//    {
//      //导游
//      case 'guide':
//        $auth_type = 'is_guide';
//        break;
//      //地陪
//      case 'attendant':
//        $auth_type = 'is_attendant';
//        break;
//      //司机
//      case 'driver':
//        $auth_type = 'is_driver';
//        break;
//      //商户
//      case 'merchant':
//        $auth_type = 'is_merchant';
//        break;
//      case 'auth':
//        $auth_type = 'auth';
//        break;
//      default:
//        $auth_type = 'auth';
//        break;
//    }
//    if($page > 1)
//    {
//      $page = $page - 1;
//      $page_num = 10;
//      $user_hidden = true;
//    }
//    else
//    {
//      $user_hidden = false;
//      $page_num = 2;
//    }
//
//    $start = ($page-1)*$page_num;
//    if($title)
//    {
//      $where=" AND v_users.user_name LIKE '%$title%' AND v_users.auth='1'  ";
//      $data['userlist']=$this->User_model->get_auth_list($where,$user_id);
//    }
//    else
//    {
//      if($auth_range!='all'){
//        $where.="  AND v_users.$auth_type='1' AND ( u1.id_prange = '$auth_range' OR u2.id_prange = '$auth_range' OR u3.id_range = '$auth_range')";
//        $wherev=$where." AND is_off < '2'";
//        $data['videolist']=$this->User_model->get_video_list($wherev,$start,$page_num,$table='v_users');
//
//      }
//      else
//      {
//        $where.="  AND v_users.$auth_type='1' ";
//        $wherev=$where." AND is_off < '2'";
//        $data['videolist']=$this->User_model->get_video_list($wherev,$start,$page_num,$table='v_users');
//      }
//
//      foreach($data['videolist'] as $k=>$v)
//      {
//
//        if($v['is_off'] == '1')
//        {
//          $data['videolist'][$k]['rtmp'] = $this->get_rec($v['video_name'],$v['push_type']);
//        }
//        else
//        {
//          $data['videolist'][$k]['rtmp'] = $this->get_rtmp($v['video_name']);
//        }
//        if(stristr($v['v_image'], 'http'))
//        {
//          $data['videolist'][$k]['image'] = $v['v_image'];
//        }
//        else
//        {
//          $data['videolist'][$k]['image'] = $this->config->item('base_url') . ltrim($v['v_image'],'.');
//        }
//        $distance = "";
//        if($location && $v['location'])
//        {
//          $lct1 = explode(",",$location);
//          $lct2 = explode(",",$v['location']);
//          $lat1 = $lct1[0];
//          $lng1 = $lct1[1];
//          $lat2 = $lct2[0];
//          $lng2 = $lct2[1];
//          $distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
//        }
//
//        $data['videolist'][$k]['distance'] = strval($distance);
//        $data['videolist'][$k]['ipinfo'] = $v['address'];
//        $data['videolist'][$k]['video_type'] = strval($v['is_off']);
//        $data['videolist'][$k]['share_replay_path'] =  "http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$v['video_id']}";
//        $data['videolist'][$k]['video_dec']=$v['user_name'].'在'.$v['address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
//      }
//
//      if($user_hidden===false){
//        $data['userlist']=$this->User_model->get_auth_list($where,$user_id);
//      }
//    }
//
//    $this->data_back($data);
//  }


//  public function get_favorite()
//  {
//    $type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '1';
//    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
//    $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '1,1';
//    if(!$location OR !$type OR !$user_id)
//    {
//      $this->data_back('参数为空','0X011','fail');
//    }
//    $where="v_favorite.user_id=$user_id AND v_favorite.type='$type'";
//    if($type==1)
//    {
//      $where .="  AND v_goods.is_show='1'";
//      $select="v_favorite.user_id,v_favorite.act_id,v_activity_children.banner_product as banner_image,v_activity_children.title,v_activity_children.tag,v_goods.shop_price,v_goods.goods_id";
//      $data['list']=$this->User_model->get_favoreite($select,$where,'v_favorite');
//      foreach($data['list'] as $k=>$v)
//      {
//        $where="goods_id=$v[goods_id] AND order_status = '3'";
//        $data['list'][$k]['goods_buy']=$this->User_model->get_order_count($where);
//        $data['list'][$k]['goods_buy']=$data['list'][$k]['goods_buy']['count'];
//        $data['list'][$k]['product_url']=base_url("myshop/products_detail?act_id=$v[act_id]");
//
//      }
//    }
//    else
//    {
//      $where.=" AND v_wx_business.is_show='1'";
//      $select="v_wx_business.star_num,v_wx_business.tag,v_wx_business.logo_image_thumb,v_wx_business.lng,v_wx_business.lat,v_wx_business.discount,v_wx_business.business_name,v_wx_business.business_id,v_wx_business.address";
//      $data['list']=$this->User_model->get_select_all($select,$where,'displayorder','ASC','v_favorite',$left=1,'v_wx_business',"v_favorite.shop_id=v_wx_business.business_id ");
//      $me_location=explode(',',$location);
//      $me_lat=$me_location[0];
//      $me_lng=$me_location[1];
//      if($data['list']===false)
//      {
//        $data['list']=array();
//      }
//      foreach($data['list'] as $k=>$v)
//      {
//        $data['list'][$k]['distance']=$this->GetDistance($me_lat, $me_lng, $v['lat'], $v['lng'], $len_type = 1, $decimal = 2);
//        $data['list'][$k]['shop_url']=base_url("bussell/business_info_app?business_id={$v['business_id']}");
//      }
//
//    }
//    $this->data_back($data);
//  }

//  public function appmap_getbus()
//  {
//    $location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
//    $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'bus';
//    if($location)
//    {
//      $arr=explode(',',$location);
//      $lat=$arr[0];
//      $lng=$arr[1];
//      $arr=$this->getAround($lat,$lng,'120000');
//      $where="lat > $arr[minLat] AND lat < $arr[maxLat] AND lng > $arr[minLng] AND lng < $arr[maxLng]  AND lat !=''";
//      if($type=='bus')
//      {
//        $where='1=1';
//        $data['title']=$this->input->post_get('title',true);
//        if($data['title']){
//          $where.="  AND (business_name LIKE '%$data[title]%' OR  tag  LIKE '%$data[title]%')";
//        }
//        $where.=" AND is_show = '1'";
//        $select="is_apply,type,address,business_id,logo_image_thumb AS logo_image,logo_image_thumb AS image,business_name,business_name as title,star_num,discount,tag,lat,lng,lat AS latitude,lng AS longitude,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(lat*PI()/180)*POW(SIN(($lng*PI()/180-lng*PI()/180)/2),2)))*1000) AS distance  ";
//        $list=$this->User_model->get_select_more($select,$where,0,20,$order_title="type",$order='DESC',$table='v_wx_business');
//        foreach($list as $k=>$v){
//          $list[$k]['shop_url']=base_url('bussell/business_info_app?business_id=').$v['business_id'];
//          $list[$k]['api_url']=base_url('api/get_app_business_info?');
//        }
//      }
//      elseif($type=='video')
//      {
//
//        $select='v_video.video_id,v_video.user_id,v_video.start_time,location,v_users.user_name,v_users.image as avatar,
//			all_address,push_type,views,v_video.praise,video_name,title,location,v_video.image as image,v_users.pre_sign,
//			title,is_off,socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
//        $where.="  AND is_off<'2'";
//        $list=$this->User_model->get_select_all($select,$where,'v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
//
//        if($list===FALSE)
//        {
//          $this->data_back('数据为空','0X015','fail');
//        }
//        else
//        {
//          foreach ($list as $k => $v)
//          {
//            $list[$k]['level'] = $this->get_level($v['credits']);
//            if ($v['is_off'] == 1)
//            {
//              $list[$k]['path'] = $this->get_rec($v['video_name'], $v['push_type']);
//              $list[$k]['share_replay_path'] = "http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//              $list[$k]['video_dec'] = $v['user_name'] . '在' . $v['all_address'] . '的精彩直播' . $v['title'] . ',世界那么大赶快来看看!';
//            }
//            else
//            {
//              $list[$k]['path'] = $this->get_rtmp($v['video_name']);
//              $list[$k]['share_replay_path'] = "";
//              $list[$k]['video_dec'] = '测试描述' . $v['title'];
//            }
//          }
//        }
//      }
//      elseif($type=='products')
//      {
//        $where='1=1';
//        $select="v_activity_children.act_id,v_activity_children.user_id,v_activity_children.banner_product as banner_image,v_activity_children.title,v_wx_business.business_id,ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-v_wx_business.lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(v_wx_business.lat*PI()/180)*POW(SIN(($lng*PI()/180-v_wx_business.lng*PI()/180)/2),2)))*1000) AS distance  ";
//        $where.="         AND v_activity_children.special='2'
//						 AND v_activity_children.act_status='2'
//						 AND v_activity_children.is_temp='0'
//						 AND v_activity_children.is_show='1'
//						 AND v_activity_children.user_id!=0
//						  AND  v_wx_business.business_id!=''
//						 AND  v_wx_business.is_show = '1'
//						 ";
//        $list=$this->User_model->get_select_all($select,$where,"distance",'ASC','v_activity_children',1,'v_wx_business',"v_activity_children.user_id=v_wx_business.user_id ");
//        if(is_array($list))
//        {
//          $new_list=array();
//          $del=array();
//          foreach($list as $k=>$v)
//          {
//            $rs=$this->User_model->get_select_one('goods_id,shop_price',array('act_id'=>$v['act_id']),'v_goods');
//            if($rs==0)
//            {
//              $del[]=$k;
//              continue;
//            }
//            $where="goods_id=$rs[goods_id] AND order_status = '3'";
//            $list[$k]['goods_buy']=$this->User_model->get_order_count($where);
//            $list[$k]['goods_buy']=$list[$k]['goods_buy']['count']+rand(5,20);
//            $list[$k]['shop_price']=$rs['shop_price'];
//            $list[$k]['product_url']=base_url("myshop/products_detail?act_id=$v[act_id]");
//            $new_list[$v["act_id"]]=$list[$k];
//          }
//
//          $list=array_values($new_list);
//        }
//      }
//      else
//      {
//        $this->data_back("参数为空", '0x011','fail');
//      }
//      if(is_array($list) && count($list)>0)
//      {
//        $this->data_back($list);
//      }
//      else
//      {
//        $this->data_back("参数为空", '0x011','fail');
//      }
//
//
//    }
//    else
//    {
//      $this->data_back("参数为空", '0x011','fail');
//    }
//  }
//  public function get_app_business_info()
//  {
//
//    $business_id=$this->input->get('business_id',true);
//    $data['shop']=$this->User_model->get_select_one('business_name,business_info,business_country,business_id,tag,star_num,discount,logo_image_thumb as image,address,user_id',array('business_id'=>$business_id),'v_wx_business');
//    if($data['shop']===0)
//    {
//      $data['shop']=array();
//    }
//    else
//    {
//      $data['shop']['tag_arr']=explode(',',$data['shop']['tag']);
//      $data['shop']['new_arr']=array();
//      $tag_arr=explode(',',$data['shop']['tag_arr']);
//      //$data['tag_arr']=$tag_arr;
//      $str='';
//      foreach($tag_arr as $k1=>$v1)
//      {
//        $str.=$v1;
//        $data['shop']['new_arr'][]=$v1;
//        if(mb_strlen($str)>14){
//          break;
//        }
//      }
//    }
//    $user_id=$data['shop']['user_id'];
//
//    $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.tag,v_activity_children.banner_product as banner_image,v_goods.shop_price,v_goods.low,,v_goods.goods_id";
//    $data['products']=$this->User_model->get_select_all($select,array('v_activity_children.user_id'=>$user_id,'special'=>'2','v_activity_children.is_show'=>'1','v_activity_children.act_status'=>'2','v_goods.shop_price >'=>'0'),'v_activity_children.act_id','ASC',
//        'v_activity_children',1,'v_goods',"v_goods.act_id=v_activity_children.act_id  AND v_goods.is_show='1'");
//
//    if($data['products']===false)
//    {
//      $data['products']=array();
//    }
//
//
//
//    $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
//    v_video.image as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
//    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
//    $left_title='v_video.user_id=v_users.user_id';
//    $left_table='v_users';
//    //business_id=$business_id
//    $data['list']=$this->User_model->get_act_video_all($select,"v_video.user_id=1210  AND is_off<2 ",'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start=0,$page_num=100);
//    if(!empty($data['list']))
//    {
//      foreach($data['list'] as $k => $v){
//        if($v['is_off']==1)
//        {
//          if($v['push_type']==0)
//          {
//            $data['list'][$k]['path']=$this->config->item('record_url').$v['video_name'].'.m3u8';
//          }
//          else
//          {
//            $data['list'][$k]['path']=$this->config->item('record_uc_url').$v['video_name'].'.m3u8';
//          }
//         // $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//
//            $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
//            $data['list'][$k]['lb_url']='olook://videoinfo_lb<'. $data['list'][$k]['videoinfo'];
//
//
//          $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$v['video_id']}";
//
//          $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
//        }
//        else
//        {
//         // $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
//
//          $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
//            $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
//            $data['list'][$k]['video_url']='olook://videoinfo_zb<'. $data['list'][$k]['videoinfo'];
//        }
//        $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
//      }
//
//    }
//    if(!$data['list'])
//    {
//      $data['list']=array();
//    }
//    $data['share']['share_url']=base_url("bussell/business_info_app?business_id=$business_id");
//    $data['share']['title']=$data['business_name'];
//    $data['share']['image']=$data['image'];
//    $data['share']['desc']="坐享其成上的一个商铺。";
//    $data['json_share']=json_encode($data['share']);
//    $data['call_url']=$this->input->get('call_url');
//    if(!$data['call_url']){
//      $data['call_url']='olook://identify.toapp>menu';
//    }
//    $this->data_back($data);
//  }


//图片下载
  function  downImage($url,$new_name,$filename="") {
    if($url=="") return false;

    if($filename=="") {
      $ext=strrchr($url,".");
      if($ext!=".gif" && $ext!=".jpg" && $ext!=".png" && $ext!="jpeg") return false;
      $filename="./public/images/shop/".$new_name.'_'.time().$ext;
    }

    ob_start();
    //make file that output from url goes to buffer
    readfile($url);
    //file_get_contents($url);  这个方法不行的！！！只能用readfile
    $img = ob_get_contents();
    ob_end_clean();

    $fp=@fopen($filename, "a");//append
    fwrite($fp,$img);

    fclose($fp);

    return $filename;
    //echo $filename;
  }


    
}