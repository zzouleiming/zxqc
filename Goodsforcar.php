<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/3/17
 * Time: 15:53
 * ps:因微信公众平台 只允许三个支付链接，故车购支付页面链接在wygoods  控制器内  order_add && order_add_high && wx_order_list
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Goodsforcar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('common');

        $this->load->model('Goodsforcar_model');
        $this->load->model('Shop_model');
        $this->load->helper('url');
        $this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';

        //单商品提交
        $this->one=md5('one+1');
        //购物车提交
        $this->all=md5('all+1');
    }

    //车购首页面
    public function index()
    {
        $business_id = $this->input->get('business_id',true);
        if(!$business_id){
            $business_id = $this->session->userdata('business_id');
        }
        if($business_id){
            $business_info = $this->Shop_model->get_shop_detail(array('business_id' => $business_id));
            if(empty($business_info)){
                exit('非法访问');
            }
            $this->session->set_userdata('business_id', $business_id);
        }else{
            exit('非法访问');
        }

     //   $url = base_url("goodsforcar/index");
    //    $this->get_wx_userid($url);

        $wx_user_id = $this->session->userdata('wx_user_id');
        $data['car_count'] = $this->Goodsforcar_model->get_car_sum($wx_user_id, $business_id, 'wx_shopping_cart');     
        if(!$data['car_count']){
            $data['car_count'] = 0;
        }

        $where = array(
            'a.is_show' => 1,
            'a.business_id' => $business_id
        );
        $data['hot_list'] = $hot_list = $this->Goodsforcar_model->get_goods_list($where,3);

        $page = $this->input->post('page',true);
        $type = $this->input->post('type',true);
        $ajax = $this->input->post('ajax',true);
        if($type){
            $where['a.type'] = $type;
        }
        $count = $this->Goodsforcar_model->get_count($where,'wx_new_goods as a');

        if(!$page){
            $page = 1;
        }
        $page_num = 6;
        $max_page = ceil($count/$page_num);
        if($page > $max_page){
            $page = 1;
        }
        $data['now_page'] = $page;
        $data['max_page'] = $max_page;

        $start = ($page-1)*$page_num;
        $data['goods_list'] = $this->Goodsforcar_model->get_goods_list($where,$page_num,$start);

        //服务器url
        $data['sub_url'] = base_url("goodsforcar/index");
        $data['car_add'] = base_url('goodsforcar/add_cart');
        $data['car_de'] = base_url('goodsforcar/de_cart');
        $data['to_cart'] = base_url('goodsforcar/car_index');
        $data['to_order'] = base_url('wygoods/wx_order_list');
        if(!$ajax){
            $this->load->view('car/index',$data);
            $this->show_count();
        }else{
            echo json_encode($data);
        }
    }

    //加入购物车 或者增加购物车数量
    public function add_cart()
    {
        $data['goods_id'] = intval($this->input->post('goods_id',TRUE));
        $data['user_id'] = $this->session->userdata('wx_user_id');
        $data['addtime'] = time();
        $data['business_id'] = $this->session->userdata('business_id');
        $sum= $data['sum'] = intval($this->input->post('num',TRUE));
        $count=$this->Goodsforcar_model->get_count(array('goods_id'=>$data['goods_id'],'user_id'=> $data['user_id'],'is_show'=>1),'wx_shopping_cart');
        if($count==0)
        {
            return $this->Goodsforcar_model->user_insert('wx_shopping_cart',$data);
        }else{
            return $this->Goodsforcar_model->amount_update('sum',"sum+$sum",array('goods_id'=>$data['goods_id'],'user_id'=> $data['user_id'],'is_show'=>1),'wx_shopping_cart');
        }
    }

    //减少购物车商品数量
    public function de_cart()
    {
        $data['goods_id'] = intval($this->input->post('goods_id',TRUE));
        $data['user_id'] = $this->session->userdata('wx_user_id');
        $sum= $data['sum'] = intval($this->input->post('num',TRUE));
        $db_sum=$this->Goodsforcar_model->get_car_one( $data['user_id'], $data['goods_id']);
        if($db_sum['sum']<=$sum)
        {
            return $this->Goodsforcar_model->del(array('goods_id'=>$data['goods_id'],'user_id'=> $data['user_id'],'is_show'=>1),'wx_shopping_cart');
        }else{
            return $this->Goodsforcar_model->amount_update('sum',"sum-$sum",array('goods_id'=>$data['goods_id'],'user_id'=> $data['user_id'],'is_show'=>1),'wx_shopping_cart');
        }
    }

    //详情页面 ajax 订单前置信息提交
    public function order_in_session()
    {
        $goods_id = intval($this->input->post('goods_id',TRUE));
        $num = intval($this->input->post('num',TRUE));
        $this->session->set_userdata('goods_id', $goods_id);
        $this->session->set_userdata('num', $num);
        echo 1;
    }

    public function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    //微信订单确认
    public function wx_order_sure()
    { 
        $order_id=$this->input->post('order_id',TRUE);
        if((int)$order_id>0)
        {
           return $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),array('order_status'=>3,'confirm_time'=>time()),$table='wx_order_info');
        }
    }
        //微信车用订单生成 默认 为生成订单后微信付款  其他则为生成订单 现金付款   0 type 普通提交 1 购物车提交    $expensive 0原价 1高价
    public function wx_order_in($pay_stytle=0,$type=0,$expensive=0)
    {

        $order_id=$this->input->post('order_id',TRUE);

        if((int)$order_id>0)
        {
            $rs=$this->Goodsforcar_model-> get_order_detail($order_id);
          
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

            }else
            {
                echo $this->pay_wx($order_id);
            }
        }
        else
        {
            if(isset($_SESSION['wx_user_id']))
            {
                $user_id=$_SESSION['wx_user_id'];
            }
            else
            {
                return false;
            }
            $order_sn=$this->get_order_sn();
            $dy=strtoupper($this->input->post('trip_id',TRUE));
            $thip_id=$dy.'@'.date('Ymd',  time());
            $business_name=$this->input->post('business_name',TRUE);
            $consignee=$this->input->post('consignee',TRUE);
            $mobile=$this->input->post('mobile',TRUE);
            $business_id=$this->session->userdata('business_id');
            $goods_id_arr=$this->input->post('goods_id',TRUE);
            $num_arr=$this->input->post('num',TRUE);
            //id 做key num 做val
            $new_arr=array_combine($goods_id_arr,$num_arr);
            $order_amount=0;
            //$order_amount=$this->Goodsforcar_model->get_goods_price_all($new_arr);
           // print_r($new_arr);
            $order_goods=[];
            foreach($goods_id_arr as $k=>$v)
            {
                if($expensive==1)
                {
                    $goods_info=$this->Goodsforcar_model->get_goods_one($v,1);
                }else{
                    $goods_info=$this->Goodsforcar_model->get_goods_one($v);
                }

                $order_amount+=$goods_info['ori_price']*$new_arr[$v];
                $order_goods[]=array(
                    'goods_id'=>$v,
                    'goods_name'=>$goods_info['goods_name'],
                    'goods_number'=>$new_arr[$v],
                    'goods_sum'=>$goods_info['ori_price']*$new_arr[$v],
                    'goods_price'=>$goods_info['ori_price'],
                );
            }

            $order_info=array(
                'order_sn'=>$order_sn,
                'user_id_buy'=>$user_id,
                'user_id_sell'=>$_SESSION['guide_id'],
                'business_id'=>$business_id,
                'trip_id'=>$thip_id,
                'order_status'=>0,
                'consignee'=>$consignee,
                'mobile'=>$mobile,
                'add_time'=>time(),
                'order_amount'=>$order_amount,

            );

               //print_r($order_info);exit();
           // print_r($order_goods);

            //购物车提交 则删除 购物车数据
            if($type){
                $type=1;
            }else{
                $type=0;
            }
   
            $order_id=$this->Goodsforcar_model->wx_order_insert($order_info,$order_goods,$type);
            if(!$pay_stytle)
            {
                echo $this->pay_wx($order_id);
            }else{
                return   $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),array('order_status'=>'2','pay_id'=>4),'wx_order_info');
            }

        }


    }

    //微信车用订单生成 默认 为生成订单后微信付款  其他则为生成订单 现金付款   0 type 普通提交 1 购物车提交    $expensive 0原价 1高价
    public function wx_order_in_xj($pay_stytle=0,$type=0,$expensive=0)
    {
       
            $order_id=$this->input->post('order_id',TRUE);
      
            // echo $appid;die;

        if((int)$order_id>0)
        {
            $rs=$this->Goodsforcar_model-> get_order_detail_xj($order_id);

            if($appid!='0')
            { $arr_get=array(
                  'appId'=>$rs['appId'],
                    'nonceStr'=>$rs['nonceStr'],
                    'timeStamp'=>$rs['timeStamp'],
                    'signType'=>'MD5',
                    'package'=>$rs['prepayid'],
                    'paySign'=>$rs['sign'],
                );
       // echo "<pre>";
      //  print_r($arr_get);
      //  echo "</pre>";die;
   //拼接微信支付URL
              $string1=$appid1.urlencode($rs['appId']).$nonceStr1.urlencode($rs['nonceStr']).$timeStamp.urlencode($rs['timeStamp']).$signType.urlencode($rs['signType']).$package.urlencode('MD5').$package1
               .urlencode($rs['prepayid']).$paySign.urlencode($rs['sign']);
               $string2=urlencode($string1);
               $string3['url']='weixin://wap/pay?'.$string2;
      
               $this->load->view('zxqcny1/cs_wx',$string3);
                $arr=array('order_id'=>$order_id,'json'=>json_encode(json_encode($arr_get)));
                echo json_encode($arr);
                

            }else
            {
                echo $this->pay_wx($order_id);
            }
        }
        else
        {

            if(isset($_SESSION['wx_user_id']) && isset($_SESSION['guide_id']))
            {
                $user_id=$_SESSION['wx_user_id'];
            }
            else
            {
                return false;
            }
            $order_sn=$this->get_order_sn();
            $thip_id=strtoupper($this->input->post('trip_id',TRUE));
            $business_name=$this->input->post('business_name',TRUE);
            $consignee=$this->input->post('consignee',TRUE);
            $mobile=$this->input->post('mobile',TRUE);
            $business=$_SESSION['business_id'];
            if($business!="" ){
            $business_id=$business;
           
            }else{
            $sql="select g.business_id as business_id  from v_wx_users as u inner join wx_guide_business as g on u.user_id=g.user_id ";
          $resd=$this->db->query($sql);
            //    $resd=$this->Goodsforcar_model->get_jion();
            if(!$resd){
                $business_id=$resd['business_id'];
               
            }
            else
            {
                $business_id=$this->Goodsforcar_model->get_trip_id($thip_id,$business_name);
            }
            }


            $goods_id_arr=$this->input->post('goods_id',TRUE);
            $num_arr=$this->input->post('num',TRUE);
            //id 做key num 做val
            $new_arr=array_combine($goods_id_arr,$num_arr);
            $order_amount=0;
            //$order_amount=$this->Goodsforcar_model->get_goods_price_all($new_arr);
           // print_r($new_arr);
            $order_goods=[];
            foreach($goods_id_arr as $k=>$v)
            {
                if($expensive==1)
                {
                    $goods_info=$this->Goodsforcar_model->get_goods_one($v,1);
                }else{
                    $goods_info=$this->Goodsforcar_model->get_goods_one($v);
                }

                $order_amount+=$goods_info['ori_price']*$new_arr[$v];
                $order_goods[]=array(
                    'goods_id'=>$v,
                    'goods_name'=>$goods_info['goods_name'],
                    'goods_number'=>$new_arr[$v],
                    'goods_sum'=>$goods_info['ori_price']*$new_arr[$v],
                    'goods_price'=>$goods_info['ori_price'],
                );
            }

            $order_info=array(
                'order_sn'=>$order_sn,
                'user_id_buy'=>$user_id,
                'user_id_sell'=>$_SESSION['guide_id'],
                'business_id'=>$business_id,
                'trip_id'=>$thip_id,
                'order_status'=>0,
                'consignee'=>$consignee,
                'mobile'=>$mobile,
                'add_time'=>time(),
                'order_amount'=>$order_amount,

            );

               //print_r($order_info);exit();
           // print_r($order_goods);

            //购物车提交 则删除 购物车数据
            if($type){
                $type=1;
            }else{
                $type=0;
            }

            $order_id=$this->Goodsforcar_model->wx_order_insert($order_info,$order_goods,$type);
            if(!$pay_stytle)
            {
                echo $this->pay_wx($order_id);
            }else{
                return   $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),array('order_status'=>'2','pay_id'=>4),'wx_order_info');
            }

        }


    }

    //返回json array('order_id'=>$order_id,'json'=>$jsApiParameters)
    public function pay_wx($order_id)
    {

        $this->load->library('Wxauth');
        $info=$this->Goodsforcar_model->get_order_detail($order_id);

        $fee=floatval($info['order_amount'])*100;

        // $_SESSION['openidfromwx']=1;
        if(isset($_SESSION['openidfromwx']))
        {
            $openid=$_SESSION['openidfromwx'];
        }
        else
        {
            return false;
        }

        $this->wxauth->setParameter("openid","$openid");//商品描述
        $this->wxauth->setParameter("body","车用订单");//商品描述

        $out_trade_no =$info['order_sn'];
        $this->wxauth->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $this->wxauth->setParameter("total_fee","$fee");//总金额
        $this->wxauth->setParameter("notify_url", $this->wxauth->NOTIFY_URL);//通知地址
        $this->wxauth->setParameter("trade_type","JSAPI");//交易类型
        //  echo $out_trade_no;exit();
        $prepay_id =  $this->wxauth->getPrepayId();
        // echo $prepay_id;exit();
        $this->wxauth->setPrepayId($prepay_id);
        $jsApiParameters = $this->wxauth->getParameters();
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
        $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),$parm,$table='wx_order_info');
        $jsApiParameters=json_encode($jsApiParameters);
        $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
        return json_encode($arr);
    }

//测试新疆支付
        //返回json array('order_id'=>$order_id,'json'=>$jsApiParameters)
    public function pay_wx_xj($order_id)
    {

        $this->load->library('Wxauth');
        $info=$this->Goodsforcar_model-> get_order_detail_xj($order_id);

        $fee=floatval($info['order_amount'])*100;

        // $_SESSION['openidfromwx']=1;
        if(isset($_SESSION['openidfromwx']))
        {
            $openid=$_SESSION['openidfromwx'];
        }
        else
        {
            return false;
        }

        $this->wxauth->setParameter("openid","$openid");//商品描述
        $this->wxauth->setParameter("body","车用订单");//商品描述

        $out_trade_no =$info['order_sn'];
        $this->wxauth->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $this->wxauth->setParameter("total_fee","$fee");//总金额
        $this->wxauth->setParameter("notify_url", $this->wxauth->NOTIFY_URL);//通知地址
        $this->wxauth->setParameter("trade_type","JSAPI");//交易类型
        //  echo $out_trade_no;exit();
        $prepay_id =  $this->wxauth->getPrepayId();
        // echo $prepay_id;exit();
        $this->wxauth->setPrepayId($prepay_id);
        $jsApiParameters = $this->wxauth->getParameters();
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
        $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),$parm,$table='wx_order_info');
        $jsApiParameters=json_encode($jsApiParameters);
        $arr=array('order_id'=>$order_id,'json'=>$jsApiParameters);
        return json_encode($arr);
    }

    //购物车展示页面
    public function car_index()
    {
        $ajax = $this->input->post('ajax',true);
        $page = $this->input->post('page',true);
        $this->get_sess();
        
        $wx_user_id = $this->session->userdata('wx_user_id');
        $count = $this->Goodsforcar_model->get_count(array('a.user_id'=>$wx_user_id,'a.is_show'=>'1'),'wx_shopping_cart as a');

        if(!$page){
            $page = 1;
        }
        $page_num = 6;
        $max_page = ceil($count/$page_num);
        if($page > $max_page){
            $page = 1;
        }
        $data['now_page'] = $page;
        $data['max_page'] = $max_page;

        $start = ($page-1)*$page_num;
        $business_id = $this->session->userdata('business_id');
        $data['car_info']=$this->Goodsforcar_model->get_car_info($wx_user_id, $business_id, $page_num, $start);

        //服务器 url
        $data['more_url'] = base_url('goodsforcar/car_index');
        $data['sub_url'] = base_url("wygoods/order_add?type=".$this->all);
        $data['car_add'] = base_url('goodsforcar/add_cart');
        $data['car_de'] = base_url('goodsforcar/de_cart');
        $data['to_order'] = base_url('wygoods/wx_order_list');
        $data['url'] = base_url('goodsforcar');
        if(!$ajax){	
            if(!empty($data['car_info'])){
                $this->load->view('car/pcar',$data);
            }else{
                $this->load->view('car/car_null',$data);
            }
            $this->show_count();
        }else{
            echo json_encode($data);
        }
    }

    public function get_sess()
    {
        if(!$this->session->userdata('wx_user_id')){
            redirect(base_url('goodsforcar'));
        }
    }

    //wx 商品 详情页面
    public function goods_detail()
    {
        $goods_id = $this->input->get('goods_id',TRUE);
        $data['goods_info']=$this->Goodsforcar_model->get_goods_one($goods_id);

        $data['car_add']=base_url('goodsforcar/add_cart');
        $data['order_in_session']=base_url('goodsforcar/order_in_session');
        $data['to_order']=base_url('wygoods/wx_order_list');
        $data['sub_url']=base_url("wygoods/order_add?type=$this->one");
        $this->load->view('car/detail',$data);
        $this->show_count();
    }

    public function guide_success()
    {

    }

    //type 微信用户身份
    public function get_wx_userid($url)
    {
        $this->load->library('Wxauth');
        if($this->session->userdata('wx_user_id') AND $this->session->userdata('openidfromwx')){
            return $this->session->userdata('wx_user_id');
        }

        $code = $this->input->get('code');
        if(!$code){
            //触发微信返回code码
            $wxauth_url = $this->wxauth->createOauthUrlForCode_all(urlencode($url));
            redirect($wxauth_url);
        }else{
            //获取code码，以获取openid
            $this->wxauth->setCode($code);
            $wxuserinfo = $this->wxauth->wxuserinfo($code);
            $openid = $wxuserinfo['openid'];
            $user_name = $wxuserinfo['nickname'];
            $sex = $wxuserinfo['sex'];
            $sex_et = $sex==1 ? '0': '1';
            $lan = $wxuserinfo['language'];
            $address = $wxuserinfo['city'];
            $image = $wxuserinfo['headimgurl'];
            $num = strripos($image,'/');
            $image = substr($image,0,$num);
            $wxinfo = $this->Goodsforcar_model->get_wxuser_info(array('openid' => $openid));
            if($wxinfo){
                $this->session->set_userdata('openidfromwx', $wxinfo['openid']);
                $this->session->set_userdata('wx_user_id', $wxinfo['user_id']);
                return $wxinfo['user_id'];
            }else{
                $datauser=array(
                    'openid' => $openid,
                    'register_time' => time(),
                    'regist_type' => '7',
                    'user_name' => $user_name,
                    'sex' => $sex_et,
                    'lan' => $lan,
                    'address' => $address,
                    'image' => $image.'/96'
                );
                $wx_user_id = $this->Goodsforcar_model->user_insert('v_wx_users',$datauser);
                $this->session->set_userdata('openidfromwx', $openid);
                $this->session->set_userdata('wx_user_id', $wx_user_id);
                return $wx_user_id;
            }
        }
    }
}