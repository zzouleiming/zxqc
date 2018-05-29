<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2016/4/13
 * Time: 11:52
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Order extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Order_model');
        $this->load->model('User_model');
        $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('cookie');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
       // $this->load->model('User_Api_model');
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

    public function push_sys($user_id,$info){
        $data=array(
            'pm_type'=>0,
            'user_id'=>$user_id,
            'message'=>$info,
            'is_new'=>1,
            'add_time'=>time()
        );
        $this->User_model->user_insert('v_prompt',$data);
    }
    /*
     * 验证user_id 并获取
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
    }elseif(isset($_SESSION['user_id'])){
        return $_SESSION['user_id'];
    }else{
      return false;
    }
  }
    public function order_del($id)
    {
        $this->User_model->del(array('order_id'=>$id),'v_order_info');
        redirect(base_url('order/order_list'));
    }

    public function order_list($page =1){
          $this->Order_model->judge_timei_out_all();
          $this->Order_model->sure_sub_all();

        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $order_status= $data['order_status']= $this->input->get('order_status',true);
        if(!$order_status){
            $order_status=0;
        }
        $where="(from ='0' OR from='1') ";
        $where="1=1";


        if($data['time1']){
            $where.=" AND add_time >=$data[time1]";
        }
        if($data['time2']){
            $data['time2']+=86400;
            $where.="  AND add_time <=$data[time2]";
        }

        if($data['title'])
        {
            if($data['type']==1){
                $where.= " AND (order_id LIKE '%$data[title]%'  OR order_sn LIKE '%$data[title]%')";
            }elseif($data['type']==2){
                $where.= " AND (user_id_buy_name LIKE '%$data[title]%'  OR user_id_sell_name LIKE '%$data[title]%' )";
            }
        }else{
            $data['type']=0;
        }

            $where.="  AND order_status= '$order_status'";


            $page_num =10;
            $data['now_page'] = $page;
            $count = $this->User_model->get_count($where,'v_order_info');
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list'] = $this->Order_model->order_list($where,$start,$page_num);
       // echo "<pre>",print_r($data);exit();
        //echo $this->db->last_query();
            $this->load->view('newadmin/order_info_list',$data);

    }
    /*
     *订单快递信息提交
     */
    public function order_ship_sub(){
        $order_id=$this->input->post('order_id',true);
        $shipping_id=$this->input->post('shipping_id',true);
        $shipping_name=$this->input->post('shipping_name',true);
        $arr=array(
            'shipping_id'=>$shipping_id,
            'shipping_name'=>$shipping_name,
            'order_status'=>'2',
            'is_ship'=>'1',
            'shipping_time'=>time(),
            'receiving_time'=>time()+2592000
        );
        $where=array(
            'order_id'=>$order_id
        );
        $this->Order_model->order_update($where,$arr);


        redirect(base_url('order/order_list_unshipsell'));
        //    return false;
    }
    public function order_test(){
        $this->Order_model->order_test();
    }
    /*
     * 订单取消
     */

    /*public function order_cancle(){
        $order_id=$this->input->get('order_id',true);
        $where=array('order_id'=>$order_id);
        $arr_update=array(
            'order_status'=>'4'
        );
        $this->Order_model->order_update($where,$arr_update,$table='v_order_info');
        echo 1;

    }
    public function test_push(){
        $row[0]=array('device_id'=>'1a17897ab1bba467e9f511214b656774ba53d553c3b54adfb84431fbf7fe7aff',
            'type'=>'1'
            );
        $info='买家'.'ppp'.'取消了('.'sdqsdsd'.')的订单，如有问题可私信咨询!';
        $this->pushinfo($row,$info);
    }*/
    /*
     * 买家取消订单
     */
    public function order_cancle_buy(){
        $order_id=$this->input->post('order_id',true);
        $buyname=$this->input->post('buyname',true);
        $goodsname=$this->input->post('goodsname',true);
        $user_id_sell=$this->input->post('user_id_sell',true);
        $where=array('order_id'=>$order_id);
        $arr_update=array(
            'order_status'=>'4'
        );
        if( $this->Order_model->order_update($where,$arr_update,$table='v_order_info'))
        {
           /* $row=$this->User_model->get_select_all('device_id,type',array('user_id'=>$user_id_sell),'phone_id', 'ASC','v_device');
            if(!empty($device_list))
            {
             $info='买家'.$buyname.'取消了('.$goodsname.')的订单，如有问题可私信咨询!';
                $this->pushinfo($row,$info);
            }*/
            $rs=$this->User_model->get_select_one('user_id_buy',array('order_id'=>$order_id),'v_order_info');
            $user_id=$rs['user_id_buy'];
            $this->new_lan_bydb($user_id);
            $info=$this->lang->line('sys_buyer').$buyname.','.$this->lang->line('sys_can').$goodsname.$this->lang->line('sys_ding').','.$this->lang->line('sys_zx');
            //$info='买家'.$buyname.'取消了('.$goodsname.')的订单，如有问题可私信咨询!';
            $this->push_sys($user_id_sell,$info);
            echo 1;
        }

    }

    /*
     * 卖家取消订单
     */
    public function order_cancle_sell(){
        $order_id=$this->input->post('order_id',true);
        $sellname=$this->input->post('sellname',true);
        $goodsname=$this->input->post('goodsname',true);
        $user_id_buy=$this->input->post('user_id_buy',true);
        $where=array('order_id'=>$order_id);
        $arr_update=array(
            'order_status'=>'4'
        );
        if( $this->Order_model->order_update($where,$arr_update,$table='v_order_info')){
           /* $row=$this->User_model->get_select_all('device_id,type',array('user_id'=>$user_id_buy),'phone_id', 'ASC','v_device');
            if(!empty($row))
            {
                $info='你购买的('.$goodsname.')，卖家'.$sellname.'进行了交易关闭，如有问题可私信咨询!';
                $this->pushinfo($row,$info);
            }*/
            $rs=$this->User_model->get_select_one('user_id_sell',array('order_id'=>$order_id),'v_order_info');
            $user_id=$rs['user_id_sell'];
            $this->new_lan_bydb($user_id);
            $info=$this->lang->line('sys_ygm').$goodsname.','.$this->lang->line('sys_seller').$sellname.$this->lang->line('sys_jxgb').','.$this->lang->line('sys_zx');;
           // $info='你购买的('.$goodsname.')，卖家'.$sellname.'进行了交易关闭，如有问题可私信咨询!';
            $this->push_sys($user_id_buy,$info);
            echo 1;
        }



    }



    /*
     * 推送消息
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
            $params['production_mode'] = false;
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
/*
 *
 */
    public function pushinfo1($data= array(),$type1='')
    {
        $video_info = isset($data['video_info']) ? $data['video_info'] : '';
        if($type1 == 'advance')
        {
            $info = '还有5分钟就要直播了,赶快准备下把';
            $video_info['advance'] = 1;
        }
        elseif($type1 == 'show_start')
        {
            $info = '在直播 快来围观';
            $video_info['advance'] = 0;
        }
        $token1=$token2= $sep = $type_1 = $type_2 = '';
        for($i=0;$i<count($data['list']);$i++)
        {
            if($data['list'][$i]['type'] == '1')
            {

                $token1 .= $sep.$data['list'][$i]['device_id'];
                $sep =',';
                $type_1 = 1;

            }
            elseif($data['list'][$i]['type'] == '2')
            {

                $token2 .= $sep.$data['list'][$i]['device_id'];
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
            $aps['alert'] = $data['user_name'] . $info ;
            $aps['sound'] = '';
            $aps['content-available'] = 1;
            $aps['video_info'] = $video_info;
            $payload['aps'] = $aps;
            $params['production_mode'] = false;
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
            $payload['body'] = array('title'=> 'OLOOK','ticker'=>$data['user_name'] . '在直播','text'=> $data['user_name'] . $info,'sound'=>'','video_info'=>$video_info);
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
    /* 最大收获时间30天（2592000）
     * 距离最大收获3天（259200）以内不能延长收货时间，判断付款时间
     * return 0 距离结束时间三天以内 不能延期收货
     * return 1 延期成功
     * return 2 超过延期次数，无法继续延期
     */
    public function order_exted(){
        $order_id=$this->input->post_get('order_id',true);
        $where=array('order_id'=>$order_id);
        $judge=$this->Order_model->order_extend_judge($where);
        if($judge==0){
            echo 0;
        }elseif($judge==1){
            $this->Order_model->extend_plus($where);
            echo 1;
        }elseif($judge==2){
            echo 2;
        }
    }
    /*
     *评价提交
     */
    public function order_eval_sub(){
        $order_id=$this->input->post('order_id');
        $star_num=$this->input->post('star_num');
        $evaluate=$this->input->post('eval');
        $where=array('order_id'=>$order_id);
        $arr_update=array(
            'star_num'=>$star_num,
            'evaluate'=>$evaluate,
            'order_status'=>'3',
            'confirm_time'=>time()
        );
        $this->Order_model->order_update($where,$arr_update);
        $row=$this->User_model->get_select_one('goods_amount,user_id_sell',$where,'v_order_info');
        $cash_in=$row['goods_amount'];
        $user_id=$row['user_id_sell'];
        $data=array(
            'user_id'=>$user_id,
            'order_id'=>$order_id,
            'cash_in_out'=>'1',
            'cash_num'=>$cash_in,
            'cash_time'=>time(),
            'cash_adv_time'=>time(),
            'cash_status'=>'2',
        );
        $log_id=$this->User_model->user_insert('v_cash_log',$data);
        $fielded="canamount";
        $field="canamount+'$cash_in'";
        $where=array(
            'user_id'=>$user_id
        );
        $this->User_model->amount_update($fielded,$field,$where,'v_auth_users');
        redirect(base_url('order/order_list_unconfimbuy'));
        //echo "<pre>";print_r($_POST);exit();
    }

    public function order_info($order_id=''){

        $where=array('order_id'=>$order_id);
        $data['list'] = $this->Order_model->order_info($where);
        $data['goods'] = $this->Order_model->order_goods($where);
        //echo '<pre>';
        //print_r($data);
        $this->load->view('admin/order_info',$data);
    }
    public function order_edit(){
        $order_id=$arr['order_id']=$this->input->post('order_id',true);

        $arr['order_sn']=$this->input->post('order_sn',true);
        $arr['user_id_buy']=$this->input->post('user_id_buy',true);
        $arr['user_id_buy_name']=$this->input->post('user_id_buy_name',true);
        $arr['user_id_sell']=$this->input->post('user_id_sell',true);
        $arr['user_id_sell_name']=$this->input->post('user_id_sell_name',true);

        $arr['consignee']=$this->input->post('consignee',true);
        $arr['mobile']=$this->input->post('mobile',true);
        $arr['address']=$this->input->post('address',true);
        $arr['shipping_id']=$this->input->post('shipping_id',true);
        $arr['shipping_name']=$this->input->post('shipping_name',true);

        $arr['evaluate']=$this->input->post('evaluate',true);
        $arr['star_num']=$this->input->post('star_num',true);

        $arr['add_time']=$this->input->post('consignee',true);
        $arr['pay_time']=$this->input->post('mobile',true);
        $arr['confirm_time']=$this->input->post('address',true);
        $arr['receiving_time']=$this->input->post('shipping_id',true);
        $arr['goods_amount']=$this->input->post('goods_amount',true);
        $arr['goods_all_num']=$this->input->post('goods_all_num',true);
        $arr['order_status']=$this->input->post('status',true);
        if( !$arr['order_id'] || !$arr['user_id_buy'] || !$arr['user_id_sell']){
           // return false;
        }
        $where=array('order_id'=>$order_id);
        $this->Order_model->order_update($where,$arr);

        $arrgoods['rec_id']=$this->input->post('rec_id',true);
        $arrgoods['goods_id']=$this->input->post('goods_id',true);
        $arrgoods['goods_name']=$this->input->post('goods_name',true);
        $arrgoods['goods_price']=$this->input->post('goods_price',true);
        $arrgoods['goods_number']=$this->input->post('goods_number',true);
        $arrgoods['goods_sum']=$this->input->post('goods_sum',true);

        foreach( $arrgoods['rec_id'] as $k=>$v){
            $temp_arr=array();
            $temp_arr['goods_id']= $arrgoods['goods_id'][$k];
            $temp_arr['goods_name']= $arrgoods['goods_name'][$k];
            $temp_arr['goods_price']= $arrgoods['goods_price'][$k];
            $temp_arr['goods_number']= $arrgoods['goods_number'][$k];
            $temp_arr['goods_sum']= $arrgoods['goods_sum'][$k];
            $this->Order_model->order_update(array('rec_id'=>$v),$temp_arr,'v_order_goods');

        }
        redirect(base_url("order/order_info/$order_id"));
    }
/**
 * [order_list_unpaybuy app端订单]
 * @param  integer $page [description]
 * @return [type]        [description]
 */
    public function order_list_unpaybuy($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunpaybuy="user_id_buy=$user_id And (order_status='0' OR  order_status='4') AND from='0'";
        $page_num =5;
        if($this->input->get('test')){
            $page_num =1;
        }

        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunpaybuy,'v_order_info');
        $data['unpaybuymax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unpaybuymax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unpaybuy']=$this->Order_model->order_list_home($whereunpaybuy,$start,$page_num);
        //echo "<pre>";print_r($data);exit();

        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                $this->load->view('order/unpaybuy_ios',$data);
            }else{
                $this->load->view('order/unpaybuy',$data);
            }
        }else{
            $data['tip']=0;
            $data['tip']=$this->input->get('tip',true);
            $this->load->view('order/unpaybuy_wx',$data);
        }

        
       
    }
    public function order_list_unpaysell($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunpaysell="user_id_sell=$user_id And (order_status='0' OR  order_status='4') ";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunpaysell,'v_order_info');
        $data['unpaysellmax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unpaysellmax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unpaysell']=$this->Order_model->order_list_home($whereunpaysell,$start,$page_num);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                $this->load->view('order/unpaysell_ios',$data);
            }else{
                $this->load->view('order/unpaysell',$data);
            }
        }else{
            $this->load->view('order/unpaysell_wx',$data);
        }

     

    }

    public function order_list_unshipbuy($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunshipbuy="user_id_buy=$user_id And order_status='1' AND from='0' ";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunshipbuy,'v_order_info');
        $data['unshipbuymax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unshipbuymax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unshipbuy']=$this->Order_model->order_list_home($whereunshipbuy,$start,$page_num);
       // echo "<pre>";print_r($data);exit();
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                $this->load->view('order/unshipbuy_ios',$data);
            }else{
                $this->load->view('order/unshipbuy',$data);
            }
        }else{
            $this->load->view('order/unshipbuy_wx',$data);
        }
    }

    public function order_list_unshipsell($page=1)
    {
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        if($this->input->get('user_id')){
            $user_id=$this->input->get('user_id');
        }else{
            $user_id=$this->user_id_and_open_id();
            if(!$user_id){
                return false;
            }
        }

        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunshipsell="user_id_sell=$user_id And order_status='1' ";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunshipsell,'v_order_info');
        $data['unshipsellmax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unshipsellmax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unshipsell']=$this->Order_model->order_list_home($whereunshipsell,$start,$page_num);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('order/unshipsell_ios',$data);
        }else{
            $this->load->view('order/unshipsell',$data);
        } 
        
    }

    public function order_list_unconfimbuy($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunconfimbuy="user_id_buy=$user_id And (order_status='2' OR  order_status='3') AND from='0'";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunconfimbuy,'v_order_info');
        $data['unconfimbuymax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unconfimbuymax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unconfimbuy']=$this->Order_model->order_list_home($whereunconfimbuy,$start,$page_num);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
         if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('order/unconfimbuy_ios',$data);
        }else{
            $this->load->view('order/unconfimbuy',$data);
        } 
       
    }
    public function order_list_unconfimsell($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunconfimsell="user_id_sell=$user_id And (order_status='2' OR  order_status='3') ";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunconfimsell,'v_order_info');
        $data['unconfimsellmax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unconfimsellmax_page']) {$page=1;}
        $start = ($page-1)*$page_num;

        $data['unconfimsell']=$this->Order_model->order_list_home($whereunconfimsell,$start,$page_num);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
       if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('order/unconfimsell_ios',$data);
        }else{
            $this->load->view('order/unconfimsell',$data);
        } 
    }


    /*
     * order订单不用快递确定
     */
    public function noship(){
        $order_id=$this->input->post('order_id',true);
        $where=array('order_id'=>$order_id);
        $arr_update=array(
            'order_status'=>'2',
            'is_ship'=>'2',
            'shipping_time'=>time(),
            'shipping_id'=>'无',
            'shipping_name'=>'不用快递',
            );
        if($this->Order_model->order_update($where,$arr_update)){
            echo 1;
        }
    }



}