<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/2/27
 * Time: 17:52
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Wygoods extends CI_Controller
{
    //2751 goods id
    //4N6D直飞
    public $price_4n5d='6000';
    //     10日内直飞航班
    public $price_10zf='7200';
    //   特定转机航班
    public $price_tdzj='4400';
    //10日内转机航班
    public $price_10zj='6500';
    //2晚当五酒店基础房型
    public $price_dh='1100';
    //2晚国五酒店基础房型
    public $price_gh='1900';
    //用车
    public $price_car='360';


    //定金
    public $float_price='500';



    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Order_model');
        $this->load->library('session');
        $this->load->helper('url');

        $this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->visitor=stristr($_SERVER['HTTP_USER_AGENT'],'visitor')===FALSE?FALSE:TRUE;
        $this->et=stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===FALSE?FALSE:TRUE;
        $this->payimage='/public/wy/images/local_xiangmu01.jpg';
        $this->shareimage=base_url('/public/wy/images/shareforwx2.jpg');

        $this->shareimage_forny3200=base_url('/public/wy/images/3200.jpg');
        $this->shareimage_forny4600=base_url('/public/wy/images/4600.jpg');
        $this->shareimage_forgzg=base_url('/public/gzg/images/gzg_share.jpg');
        $this->shareimage_forht=base_url('/public/huantai/images/ht_share.jpg');
        $this->shareimage_forzt=base_url('/public/ztai/images/zt_share.jpg');
		$this->shareimage_forfit=base_url('/public/fit/images/fit_share.jpg');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';

    }

    public function show_count()
    {
        $data['count_url']= $this->count_url;
        $this->load->view('products/show_count',$data);
    }

    public function  del_wx()
    {
        unset($_SESSION['wx_user_id']);
    }
    public function get_wx_userid($url)
    {
        $url=urlencode($url);
        include_once("./application/third_party/wxpay/WxPay.php");
        $jsApi = new JsApi_pub();


        if(isset($_SESSION['wx_user_id']) AND isset($_SESSION['openidfromwx']))
        {
          // var_dump($_SESSION['wx_user_id']);
            if($_SESSION['wx_user_id']==189)
            {
                $this->float_price=0.01;
            }
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
                $sex==1?$sex_et='0': $sex_et='1';
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

/*
 *   public $price_4n5d='6000';
    public $price_10zf='7200';
    public $price_tdzj='4400';
    public $price_10zj='6500';
    public $price_dh='1100';
    public $price_gh='1900';
    public $price_car='360';
 */
    public function index()
    {

        $data=array(
            'price_4n5d'=>$this->price_4n5d,
            'price_10zf'=>$this->price_10zf,
            'price_tdzj'=>$this->price_tdzj,
            'price_10zj'=>$this->price_10zj,
            'price_dh'=>$this->price_dh,
            'price_gh'=>$this->price_gh,
            'price_car'=>$this->price_car,

        );
        //echo '<pre>';var_dump($this->wx);print_r($_SESSION);
        if($this->wx==TRUE)
        {
            $url=base_url('wygoods/index');
            $user_id=$this->get_wx_userid($url);
        }

        $data['products']=$this->User_model->get_choose_products(array(453,497,518,506,523,655));
        $data['products_num']=count( $data['products']);

        $data['local']='/wygoods/local_hotel';
        $data['local2']='/wygoods/local_hotel2';
        $data['inter']='/wygoods/inter_hotel';
        $data['sub_url']='/wygoods/pay_index';
        $data['my_order']='/wygoods/my_order_info';
        $data['in_session']='/wygoods/in_session';
        $data['index_url']=base_url('wygoods');
        $data['shareimage']=$this->shareimage;
        $data['signPackage']=$this->wx_js_para(3);
       // echo '<pre>';print_r($data);exit();
        $this->load->view('wygoods/index',$data);
        $this->show_count();

    }

    public function in_session()
    {
        $url=base_url('wygoods');
        $user_id=$this->get_wx_userid($url);
        if($user_id)
        {
            $where_count=" user_id_buy_fromwx=$user_id AND order_status IN ('0','1','2','3') AND is_show='1' AND from='12'";
            $count=$this->User_model->get_count($where_count,'v_order_info');
            if($count['count']>0)
            {
               echo 2;
            }else{
                //fly : 0  特定转机     1  10日转机  2 4N6D直飞  3 10日直飞
                $_SESSION['fly']=$this->input->post('fly',TRUE);
                //hotel: 0当地五星酒店 1 国际五星酒店
                $_SESSION['hotel']=$this->input->post('hotel',TRUE);
                $_SESSION['allprice']=$this->input->post('allprice',TRUE);
                echo 1;
            }
        }else{
            echo 3;
        }



    }

    public function  pay_index()
    {

        if($this->wx==TRUE)
        {
            $url=base_url('wygoods/pay_index');
            $user_id=$this->get_wx_userid($url);
            if($user_id)
            {
                $where_count=" user_id_buy_fromwx=$user_id AND order_status IN ('0','1','2','3') AND is_show='1' AND from='12'";
                $count=$this->User_model->get_count($where_count,'v_order_info');
                if($count['count']>0)
                {
                    return redirect('/wygoods');
                }
            }else{
                return redirect('/wygoods');
            }


        }else{
            return redirect('/wygoods');
        }

        if(isset($_SESSION['fly']) && isset( $_SESSION['hotel']))
        {
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

            $data['image']= $this->payimage;
            $data['price']=$this->float_price;

            $data['sub_url']=base_url('bussell/order_todb');
            if($this->et===TRUE)
            {
                $data['sys']='APP';
            }else{
                $data['sys']='WX';
            }

            $this->show_head('出行人填写及支付',$data['call_back'],'出行人填写及支付',$show_share=FALSE);
            $data['pay_title']=$fly.'、'.$hotel.'普吉旅游';
            $data['fly']=$fly;
            $data['hotel']=$hotel;
            $this->load->view('wygoods/gopay',$data);
            $this->show_count();
        }
    }







    public function pay_succedd(){}


    public function pay_fail(){
        echo "<pre>";print_r($this->wx_js_para(3));
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
        if(!$wxticket)
        {
            return false;
        }
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

    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
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
            if(!isset($jsapi_ticket['ticket']))
            {
                return false;
            }
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
                if(!isset($acc_token['access_token']))
                {
                   return FALSE;
                }
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

    //活动须知公用头部
    public function show_head($title_up,$call_back,$title_down,$show_share=FALSE,$share_info=0)
    {
        if($this->et===TRUE OR $this->visitor==TRUE)
        {
            $data['show_head']=TRUE;
        }else{
            $data['show_head']=FALSE;
        }
        $data['title_up']=$title_up;
        $data['call_back']=$call_back;
        $data['title_down']=$title_down;
        $data['show_share']=$show_share;
        $data['share_out']=$share_info;
        $this->load->view('send/common_head',$data);
    }

    public function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    //唯一产品入库
//    public function order_todb()
//    {
//        //echo'<pre>';print_r($_SESSION);
//        if(isset( $_SESSION['fly']) &&  isset($_SESSION['hotel']))
//        {
//            $order_id=$this->input->post('order_id',TRUE);
//
//            if((int)$order_id>0)
//            {
//                $rs=$this->User_model->get_select_one('goods_amount,order_sn,user_id_buy_fromwx,appId,nonceStr,timeStamp,signType,package,paySign',array('order_id'=>$order_id),'v_order_info');
//                if($rs['appId']!='0')
//                {
//                    $arr_get=array(
//                        'appId'=>$rs['appId'],
//                        'nonceStr'=>$rs['nonceStr'],
//                        'timeStamp'=>$rs['timeStamp'],
//                        'signType'=>$rs['signType'],
//                        'package'=>$rs['package'],
//                        'paySign'=>$rs['paySign'],
//                    );
//                    $arr=array('order_id'=>$order_id,'json'=>json_encode(json_encode($arr_get)));
//                    echo json_encode($arr);
//
//                }
//            }
//            else
//            {
//                $cn_name=$this->input->post('cn_name',TURE);
//                $cn_mobile=$this->input->post('cn_mobile',TURE);
//                $weixin=$this->input->post('weixin',TURE);
//                $app_commont=$this->input->post('app_commont',TURE);
//
//                $order_sn=$this->get_order_sn();
//
//                if($this->wx==TRUE)
//                {
//                   $user_id=$this->get_wx_userid(base_url('wygoods'));
//                    $user_id_buy_name=$this->User_model->get_username($user_id,'v_wx_users');
//
//                    $order_info=array(
//                        'user_id_buy_fromwx'=>$user_id,
//                        'user_id_buy_name_fromwx'=>$user_id_buy_name,
//                        'from'=>'12',
//                        'user_id_sell'=>1,
//                        'user_id_sell_name'=>'坐享其成',
//                        'order_sn'=>$order_sn,
//                        'goods_amount'=>$this->float_price,
//                        'order_amount'=>$this->float_price,
//                        'goods_all_num' =>'1',
//                        'add_time'=>time(),
//                        'order_status'=>'0',
//                        'address'=>$weixin,
//                        'commont'=>$app_commont,
//                        'consignee'=>$cn_name,
//                        'mobile'=>$cn_mobile,
//                    );
//                    //fly : 0  特定转机     1  10日转机  2 4N6D直飞  3 10日直飞
//                    //hotel: 0当地五星酒店 1 国际五星酒店
//                    switch($_SESSION['fly'])
//                    {
//                        case 0:
//                            $fly='特定转机';
//                            break;
//                        case 1:
//                            $fly='10日转机';
//                            break;
//                        case 2:
//                            $fly='4N6D直飞';
//                            break;
//                        default:
//                            $fly='10日直飞';
//                    }
//
//                    switch($_SESSION['hotel'])
//                    {
//                        case 0:
//                            $hotel='当地五星';
//                            break;
//                        default:
//                            $hotel='国际五星';
//                    }
//                    $order_goods=array(
//                        'goods_id'=>2751,
//                        'goods_name'=>$fly.'、'.$hotel.'普吉岛旅游',
//                        'goods_number'=>1,
//                        'goods_sum'=>1,
//                        'goods_price'=>$this->float_price,
//                    );
//
//                    $order_addition=array(
//                        'cn_name'=>$cn_name,
//                        'cn_mobile'=>$cn_mobile,
//                        'cn_hotel'=>$fly,
//                        'en_hotel'=>$hotel,
//                        'weixin'=>$weixin,
//                        'add_time'=>time(),
//                        'commont'=>$_SESSION['allprice'],
//                        'type'=>'1'
//                    );
//                   //echo '<pre>';print_r($order_info);print_r($order_goods);print_r($order_addition);exit();
//                    $order_id=$this->Order_model->order_insert($order_info,$order_goods,$order_addition);
//
//                   // exit();
//                    $_SESSION['order_info']=$order_info;
//                    $_SESSION['order_goods']=$order_goods;
//                    include_once("./application/third_party/wxpay/WxPay.php");
//                    $fee=floatval($_SESSION['order_info']['goods_amount'])*100;
//                    if(isset($_SESSION['openidfromwx']))
//                    {
//                        $openid=$_SESSION['openidfromwx'];
//                    }
//                    else
//                    {
//                        $url=base_url("wygoods");
//                        $this->get_wx_userid($url);
//                        $openid=$_SESSION['openidfromwx'];
//                    }
//
//                    $unifiedOrder = new UnifiedOrder_pub();
//                    $jsApi = new JsApi_pub();
//                    $unifiedOrder->setParameter("openid","$openid");//商品描述
//                    $unifiedOrder->setParameter("body","特价产品");//商品描述
//
//                    $out_trade_no =$_SESSION['order_info']['order_sn'];
//                    $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
//                    $unifiedOrder->setParameter("total_fee","$fee");//总金额
//                    $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
//                    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
//                    //  echo $out_trade_no;exit();
//                    $prepay_id = $unifiedOrder->getPrepayId();
//                    // echo $prepay_id;exit();
//                    $jsApi->setPrepayId($prepay_id);
//                    $jsApiParameters = $jsApi->getParameters();
//                    $_SESSION['jsApiParameters']=$jsApiParameters;
//
//                    $prr_temp=json_decode($jsApiParameters,TRUE);
//                    $parm=array(
//                        'appId'=>$prr_temp['appId'],
//                        'nonceStr'=>$prr_temp['nonceStr'],
//                        'timeStamp'=>$prr_temp['timeStamp'],
//                        'signType'=>$prr_temp['signType'],
//                        'package'=>$prr_temp['package'],
//                        'paySign'=>$prr_temp['paySign'],
//                    );
//                    $this->User_model->update_one(array('order_id'=>$order_id),$parm,$table='v_order_info');
//                    $jsApiParameters=json_encode($jsApiParameters);
//                    $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
//                    echo json_encode($arr);
//
//
//                }else{
//                    echo -2;
//                }
//
//            }
//        }
//        else
//        {
//            echo -1;
//        }
//    }


    //微信 付款页面  微信车载产品付款
    //订单提交界面
    public function order_add()
    {
        //单商品提交
        $this->one=md5('one+1');
        //购物车提交
        $this->all=md5('all+1');

        $this->load->model('Goodsforcar_model');

        $data=[];
        $type=$this->input->get('type');

        $data['my_order']=base_url('wygoods/wx_order_list');

        if($type==$this->one) //单个商品下单
        {
            $data['wx_sub_url']=base_url('goodsforcar/wx_order_in'); //微信支付
            $data['wx_sub_url_cash']=base_url('goodsforcar/wx_order_in/1'); //现金支付
            if(isset($_SESSION['goods_id']) && isset($_SESSION['num'])){
                $goods_info=$this->Goodsforcar_model->get_goods_one($_SESSION['goods_id']);
                $goods_info['num']=$_SESSION['num'];
                $data['goods_info'][]=$goods_info;
                $this->load->view('car/pay',$data);
                $this->show_count();
            }
        }
        elseif($type==$this->all)
        {
            $data['wx_sub_url']=base_url('goodsforcar/wx_order_in/0/1');
            $data['wx_sub_url_cash']=base_url('goodsforcar/wx_order_in/1/1');
            if(isset($_SESSION['wx_user_id'])){
                $data['goods_info']=$this->Goodsforcar_model->get_car_info($_SESSION['wx_user_id'],$_SESSION['business_id'],0,0);
                if(count($data['goods_info'])==0){
                    return  redirect('/goodsforcar');
                }
                $this->load->view('car/pay',$data);
                $this->show_count();
            }else{
                redirect(base_url('goodsforcar'));
            }
        }else{
            echo '提交过期！';
            sleep(3);
            return redirect()->back();
        }
    }


    public function wx_order_list()
    {
        //$_SESSION['wx_user_id']=4;
      //  echo 1;
        if(isset($_SESSION['wx_user_id']))
        {
            $this->load->model('Goodsforcar_model');

            $user_id=$_SESSION['wx_user_id'];
            $data=[];


            $ajax=$this->input->post('ajax',true);
            $page=$this->input->post('page',true);

            if(!$page)
            {
                $page=1;
            }
            $page_num =6;
            $start = ($page-1)*$page_num;
            $data['now_page'] = $page;
            $where = array(
                'user_id_buy' => $user_id,
                'is_show' => 1,
                'business_id' => $_SESSION['business_id']
            );
            
            $count = $this->Goodsforcar_model->get_count($where,'wx_order_info');
            $data['max_page'] = ceil($count/$page_num);

            $data['order_info']=$this->Goodsforcar_model->get_order_all($where,$page_num,$start,1);
            $data['pay_url']=base_url('goodsforcar/wx_order_in');
            $data['sure_url']=base_url('goodsforcar/wx_order_sure');
            $data['more_url']=base_url('wygoods/wx_order_list');
			$data['url'] = base_url('goodsforcar');
            $data['update_url']=base_url('wygoods/wx_update_list');

            if(!$ajax)
            {  
                if(!empty($data['order_info'])){
                    $this->load->view('car/order_list',$data);
                }else{
                    $this->load->view('car/order_null',$data);
                }
                $this->show_count();
            }else{
                echo json_encode($data);
            }



        }else{
            redirect(base_url('/goodsforcar'));
        }
    }



public function wx_update_list($order_id){

    $data['is_show']=2;
    $order_id=$order_id;
    $where=array(
      'order_id'=>$order_id
        );
   $red= $this->User_model->update_one($where,$data,'wx_order_info');
   if($red){
      redirect(base_url('wygoods/wx_order_list'));

   }
}

    //验证
    public function user_id_and_open_id()
    {
        //return 1077;
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
                    return 0;
                }
            }
            else
            {
                return 0;
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
                return 0;
            }
        }
        elseif(isset($_SESSION['user_id']))
        {
            return $_SESSION['user_id'];
        }
        else
        {
            return 0;
        }
    }


    public function my_order_info()
    {


        if($this->wx==false)
        {
           return $this->towxinfo();
        }
        else
        {
            $url=base_url('wygoods/my_order_info?debug=1');
            $user_id=$this->get_wx_userid($url);

            if($user_id)
            {

                $this->Order_model->judge_timei_out_wx($user_id);
                $where="a.user_id_buy_fromwx=$user_id AND a.from='12' AND a.is_show='1' AND a.order_status IN ('0','1','2','3')";
                $data=$this->Order_model->get_order_detail_where($where);
                $data['sub_url']=base_url('wygoods/order_todb');
                if(isset($data['order_id']))
                {

                    $this->load->view('wygoods/detail',$data);
                    $this->show_count();
                }
                else
                {
                    $this->load->view('wygoods/nocontent');
                    $this->show_count();
                }
            }else{
                return $this->towxinfo();
            }

        }

    }

    public function towxinfo()
    {
        $data=[];
        $this->load->view('wygoods/wx_open',$data);
    }

    public function order_detail()
    {
        $user_id=0;
        if($this->wx===TRUE)
        {
            $user_id=$this->get_wx_userid(base_url('wygoods'));
        }
        elseif($this->et===TRUE)
        {
            $user_id=$this->user_id_and_open_id();
        }

        if($user_id)
        {
            $order_id=intval(trim($this->input->get('order_id',TRUE)));
            $where="user_id_buy_fromwx=$user_id OR user_id_sell=$user_id";
            $count=$this->User_model->get_count($where,'v_order_info');
            if($count['count']>0)
            {
                $data=$this->Order_model->get_order_detail($order_id);

                $this->load->view('wygoods/dingdan_detail',$data);
            }else{
                redirect($_SERVER['HTTP_REFERER']);
            }
        }


        $this->show_count();
    }

    public function local_hotel()
    {
        $data=[];
        $this->load->view('wygoods/local_hotel',$data);
        $this->show_count();
    }

    public function local_hotel2()
    {
        $data=[];
        $this->load->view('wygoods/local_hotel2',$data);
        $this->show_count();
    }

    public function inter_hotel()
    {
        $data=[];
        $this->load->view('wygoods/inter_hotel',$data);
        $this->show_count();
    }


    //月份转换中文
    public function get_month_cn($month)
    {

        $arr=array(
            1=>'一',
            2=>'二',
            3=>'三',
            4=>'四',
            5=>'五',
            6=>'六',
            7=>'七',
            8=>'八',
            9=>'九',
            10=>'十',
            11=>'十一',
            12=>'十二',
        );
        return $arr[$month];
    }


    public function  Ny_index()
    {

        $data=[];
        $data['index_url']=base_url('wygoods/ny_index');
        //$data['index_url']="http://src.etjourney.com/wygoods/ny_index";
        $data['shareimage']=$this->shareimage_forny3200;
        $data['signPackage']=$this->wx_js_para(3);
        $data['local']='/wygoods/local_hotel';
        $data['local2']='/wygoods/local_hotel2';
        $data['inter']='/wygoods/inter_hotel';
        $data['send']='/Sendgoods';
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['products_num']=count( $data['products']);
        //$data['products']=$this->User_model->get_choose_products(array(207,224,296,403,498,453,497,518,506,523),$page_num=100,$start=0);

        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=1';
        }
        if($this->input->get('test'))
        {
            echo $this->db->last_query();
            echo'<pre>';print_r($data);exit();
        }
        $this->load->view('ny/index',$data);
        $this->show_count();
    }

    public function  Ny_index2()
    {

        $data=[];
        $data['index_url']=base_url('wygoods/ny_index2');
        //$data['index_url']="http://src.etjourney.com/wygoods/ny_index2";
        $data['shareimage']=$this->shareimage_forny4600;
        $data['signPackage']=$this->wx_js_para(3);
        $data['local']='/wygoods/local_hotel';
        $data['local2']='/wygoods/local_hotel2';
        $data['inter']='/wygoods/inter_hotel';
        $data['send']='/Sendgoods';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        $data['share_title']='普吉岛6天4晚自由行，惊爆价¥4600起每人';
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['products_num']=count( $data['products']);
        //$data['products']=$this->User_model->get_choose_products(array(207,224,296,403,498,453,497,518,506,523),$page_num=100,$start=0);

        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=1';
        }
        if($this->input->get('test'))
        {
            echo $this->db->last_query();
            echo'<pre>';print_r($data);exit();
        }
        $this->load->view('ny/index2',$data);
        $this->show_count();
    }


    public function show_gzg()
    {
        $data['signPackage']=$this->wx_js_para(3);
        $data['shareimage']=$this->shareimage_forgzg;
        $data['index_url']=base_url('wygoods/show_gzg');
        $data['index_url']="http://src.etjourney.com/wygoods/show_gzg";
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['share_title']='泰国臻选系列－臻帆双城游普吉＋甲米超低价格惊爆惊喜等你来！';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=11';
        }
        $this->load->view('gzg/index',$data);
        $this->show_count();
    }

    public function choose()
    {

        $data['sub_url']="/products/order_sub";

        //产品选择日期 3/1-4/24,5/10-5/31，9/1--9/20，
        $data['time_start']=$time_start=strtotime("2017-3-1");
        $data['time_end']= $time_end=strtotime("2017-4-24");
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time_start),
            'month'=>date('n',$time_start),
            'month_cn'=>$this->get_month_cn(date('n',$time_start)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start)))),
            'all_days'=>date('t',$time_start),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start))),
        );

        $data['date']['cal'][]=array(
            'year'=>date('Y',$time_end),
            'month'=>date('n',$time_end),
            'month_cn'=>$this->get_month_cn(date('n',$time_end)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end)))),
            'all_days'=>date('t',$time_end),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end))),
        );

        $data['call_back']="javascript:history.go(-1)";


        $this->load->view('wygoods/choose',$data);
    }





    // 环泰】
    public function show_huantai()
    {
        $data['signPackage']=$this->wx_js_para(3);
        $data['shareimage']=$this->shareimage_forht;
        $data['index_url']=base_url('wygoods/show_huantai');
        $data['index_url']="http://src.etjourney.com/wygoods/show_huantai";
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['share_title']='高端普吉--五晚七天·尊尚纯玩';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=11';
        }
        $this->load->view('huantai/index',$data);
        $this->show_count();
    }

    // 正泰】
    public function show_ztai()
    {
        $data['signPackage']=$this->wx_js_para(3);
        $data['shareimage']=$this->shareimage_forzt;
        $data['index_url']=base_url('wygoods/show_ztai');
        $data['index_url']="http://src.etjourney.com/wygoods/show_ztai";
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['share_title']='五星帝王水上乐园专享 普吉顶级豪华半自助';
        $data['share_desc']='全程海边国际五星、第二人立减1000、赠送出海写真';
        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=11';
        }
        $this->load->view('ztai/index',$data);
        $this->show_count();
    }
    public function zlocal_hotel()
    {
        $data=[];
        $this->load->view('ztai/local',$data);
        $this->show_count();
    }

    public function zinter_hotel()
    {
        $data=[];
        $this->load->view('ztai/inter',$data);
        $this->show_count();
    }
    public function day1()
    {
        $data=[];
        $this->load->view('ztai/day1',$data);
        $this->show_count();
    }
    public function day2()
    {
        $data=[];
        $this->load->view('ztai/day2',$data);
        $this->show_count();
    }
    public function day3()
    {
        $data=[];
        $this->load->view('ztai/day3',$data);
        $this->show_count();
    }
    public function day4()
    {
        $data=[];
        $this->load->view('ztai/day4',$data);
        $this->show_count();
    }
    public function day5()
    {
        $data=[];
        $this->load->view('ztai/day5',$data);
        $this->show_count();
    }
    public function day7()
    {
        $data=[];
        $this->load->view('ztai/day7',$data);
        $this->show_count();
    }
    // 港中旅
    public function gzg_local()
    {
        $data=[];
        $this->load->view('gzg/local',$data);
        $this->show_count();
    }

    public function gzg_inter()
    {
        $data=[];
        $this->load->view('gzg/inter',$data);
        $this->show_count();
    }
    public function gzg_day1()
    {
        $data=[];
        $this->load->view('gzg/day1',$data);
        $this->show_count();
    }
    public function gzg_day2()
    {
        $data=[];
        $this->load->view('gzg/day2',$data);
        $this->show_count();
    }
    public function gzg_day3()
    {
        $data=[];
        $this->load->view('gzg/day3',$data);
        $this->show_count();
    }
    public function gzg_day4()
    {
        $data=[];
        $this->load->view('gzg/day4',$data);
        $this->show_count();
    }

public function gzg_day5()
    {
        $data=[];
        $this->load->view('gzg/day5',$data);
        $this->show_count();
    }
 
    public function gzg_day7()
    {
        $data=[];
        $this->load->view('gzg/day7',$data);
        $this->show_count();
    }


    //环泰
	public function ht_local()
    {
        $data=[];
        $this->load->view('huantai/local',$data);
        $this->show_count();
    }

    public function ht_inter()
    {
        $data=[];
        $this->load->view('huantai/inter',$data);
        $this->show_count();
    }
    public function ht_day1()
    {
        $data=[];
        $this->load->view('huantai/day1',$data);
        $this->show_count();
    }
    public function ht_day2()
    {
        $data=[];
        $this->load->view('huantai/day2',$data);
        $this->show_count();
    }
    public function ht_day3()
    {
        $data=[];
        $this->load->view('huantai/day3',$data);
        $this->show_count();
    }
    public function ht_day4()
    {
        $data=[];
        $this->load->view('huantai/day4',$data);
        $this->show_count();
    }
  
    public function ht_day7()
    {
        $data=[];
        $this->load->view('huantai/day7',$data);
        $this->show_count();
    }
	
	//FIT活动
	public function show_fit()
    {
        $data['signPackage']=$this->wx_js_para(3);
        $data['shareimage']=$this->shareimage_forfit;
        $data['index_url']=base_url('wygoods/show_fit');
        $data['index_url']="http://src.etjourney.com/wygoods/show_fit";
        $data['products']=$this->User_model->get_choose_products(array(207,224,296,518,498,655),$page_num=100,$start=0);
        $data['share_title']='嗨翻普吉自由行、这条线路任你行！';
        $data['share_desc']='凡购任意产品，凭2本护照送一个品牌乳胶枕';
        foreach ($data['products'] as $k=>$v)
        {
            $data['products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]").'&nobuy=11';
        }
        $this->load->view('fit/index',$data);
        $this->show_count();
    }
    public function fit_local()
    {
        $data=[];
        $this->load->view('fit/local_hotel2',$data);
        $this->show_count();
    }

    public function fit_inter()
    {
        $data=[];
        $this->load->view('fit/local_hotel',$data);
        $this->show_count();
    }
    public function line1()
    {
        $data=[];
        $this->load->view('fit/line1',$data);
        $this->show_count();
    }
    public function line2()
    {
        $data=[];
        $this->load->view('fit/line2',$data);
        $this->show_count();
    }
    public function line3()
    {
        $data=[];
        $this->load->view('fit/line3',$data);
        $this->show_count();
    }
    public function line4()
    {
        $data=[];
        $this->load->view('fit/line4',$data);
        $this->show_count();
    }




}