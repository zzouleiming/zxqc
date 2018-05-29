<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2016/4/13
 * Time: 11:52
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Ordernewtest extends CI_Controller{
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
    return 1077;
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

    public function order_list($page =1){
          $this->Order_model->judge_timei_out_all();
         // $this->Order_model->sure_sub_all();

        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $order_status= $data['order_status']= $this->input->get('order_status',true);
        if(!$order_status){
            $order_status=0;
        }
        $where="from ='0'";
        $where="1=1";

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
            if($data['type']==1)
            {
                $where.= " AND (order_id LIKE '%$data[title]%'  OR order_sn LIKE '%$data[title]%')";
            }
            elseif($data['type']==2)
            {
                $where.= " AND (user_id_buy_name LIKE '%$data[title]%'  OR user_id_sell_name LIKE '%$data[title]%' OR user_id_buy_fromwx LIKE '%$data[title]%')";
            }
        }
        else
        {
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

    public function discount_app_buy($page=1)
    {
        $data['on']=$this->input->get('on');
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if($user_id===false){
            if(!$this->input->get('test')){
                redirect(base_url('ordernew/order_list_unpaybuy'));
            }else{
                $user_id=1077;
            }

        }
        $this->orderto3();
        $data['user_id']=$user_id;
        $this->Order_model->judeg_time_out();
        //$this->Order_model->sure_sub_discount();
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $where="user_id_buy=$user_id  AND (from='3' OR from='2')";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_order_info');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['list']=$this->Order_model->order_list_wx($where,$start,$page_num,1,0);
        /*  $data['list']=$this->User_model->get_select_more($select='*', $where,
              $start, $page_num, 'order_id', $order='DESC', $table='v_order_info');*/
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if($this->input->get('echo')){
            echo "<pre>";print_r($data);exit();
        }
        $this->load->view('ordernew/discount_buy',$data);
    }

    public function discount_app_sell($page=1){
        $data['on']=$this->input->get('on');
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if($user_id===false){
            $user_id = 1077;
            //return false;
        }
        $this->orderto3();
        $data['user_id']=$user_id;
        $this->Order_model->judeg_time_out();
      //  $this->Order_model->sure_sub_discount();
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $where="user_id_sell=$user_id   AND (from='2' OR from='3')";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_order_info');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['list']=$this->Order_model->order_list_wx($where,$start,$page_num,1,0);
        /*  $data['list']=$this->User_model->get_select_more($select='*', $where,
              $start, $page_num, 'order_id', $order='DESC', $table='v_order_info');*/
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if($this->input->get('echo')){
            echo "<pre>";print_r($data);exit();
        }
        $this->load->view('ordernew/discount_sell',$data);
    }


    //优惠买单自动确认
    public function orderto3(){
        //$where=array('order_status'=>'1','from'=>'3');
        $where="order_status='1' AND from='3'";
        $order_arr=$this->User_model->get_select_all($select='order_id,goods_amount,user_id_sell',$where,$order_title='order_id','ASC',$table='v_order_info');
        $arr_update=array(
            'order_status'=>'3',
            'confirm_time'=>time()
        );
        if($order_arr===false){
            $order_arr=array();
        }
        foreach($order_arr as $K=>$v){
            $this->Order_model->order_update(array('order_id'=>$v['order_id']),$arr_update);
            $cash_in=$v['goods_amount'];
            $user_id=$v['user_id_sell'];

            $data=array(
                'user_id'=>$user_id,
                'order_id'=>$v['order_id'],
                'cash_in_out'=>'1',
                'cash_num'=>$cash_in,
                'cash_time'=>time(),
                'cash_adv_time'=>time(),
                'cash_status'=>'2',
            );
            $this->User_model->user_insert('v_cash_log',$data);
            $fielded="canamount";
            $field="canamount+'$cash_in'";
            $where=array(
                'user_id'=>$user_id
            );
            $this->User_model->amount_update($fielded,$field,$where,'v_auth_users');
        }
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


        redirect(base_url('ordernew/order_list_unshipsell'));
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
        if( $this->Order_model->order_update($where,$arr_update,$table='v_order_info')){
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
        $goods_id=$this->User_model->get_select_one('goods_id,goods_number',array('order_id'=>$order_id),'v_order_goods');
        $num=$goods_id['goods_number'];
        $goods_id=$goods_id['goods_id'];
        $act_id=$this->User_model->get_select_one('act_id',array('goods_id'=>$goods_id),'v_goods');
        $act_id=$act_id['act_id'];
        $this->User_model->amount_update('order_sell',"order_sell+$num",array('act_id'=>$act_id),'v_activity_children');
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
        redirect(base_url('ordernew/order_list_unconfimbuy'));
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
            return false;
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

    /*
     *  $cx[$k]['cn_name']=$cx_name[$k];
      $cx[$k]['cn_py']=$cx_py[$k];
      $cx[$k]['cn_mobile']=$cx_cntel[$k];
      $cx[$k]['oth_mobile']=$cx_otel[$k];

      $cx[$k]['order_id']=$order_id;
      $cx[$k]['add_time']=time();
      $cx[$k]['type']='2';

       $cx[$k]['weixin']='';
       $cx[$k]['mail']='';
       $cx[$k]['cn_hotel']='';
       $cx[$k]['en_hotel']='';
       $cx[$k]['cn_hotel_address']='';
       $cx[$k]['en_hotel_address']='';
     */
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

    public function put_attention()
    {
        $order_id=$this->input->post('order_id',TRUE);
        $attention=$this->input->post('text',TRUE);
        $this->User_model->update_one(array('order_id'=>$order_id),array('attention'=>$attention,'order_status'=>'2','shipping_time'=>time()),'v_order_info');
        echo 1;
    }


   public function order_detail()
   {
       if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false ){
           if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false){
               return false;
           }else{
               $url=base_url('orderwx/order_list_unpaybuy');
               $user_id_wx=$this->get_wx_userid($url);
           }
       }


       $order_id=$this->input->get('order_id',TRUE);
       $data=$this->User_model->get_select_one('cn_name,cn_py,cn_mobile,oth_mobile,weixin,mail,cn_hotel,en_hotel,cn_hotel_address,en_hotel_address',array('order_id'=>$order_id,'type'=>'1'),'v_order_addition');
       $data['order_id']=$order_id;
       $data['type']=$this->input->get('type',TRUE);
       $select="v_order_info.add_time,v_order_info.user_id_sell,v_order_info.goods_amount,v_order_info.attention,v_order_info.shipping_time,
       user_id_buy,user_id_buy_fromwx,v_order_goods.goods_name,v_order_goods.goods_price,v_order_goods.goods_attr_id,v_order_goods.goods_id";

       $rs=$this->User_model->get_one($select,array('v_order_info.order_id'=>$order_id),'v_order_info','v_order_goods','order_id','order_id');
       if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && $rs['user_id_buy_fromwx']!=$user_id_wx){
            return false;
       }
      // $rs3=$this->User_model
       $rs2=$this->User_model->get_select_one('act_id,direction,attention_list,change_goods',array('goods_id'=>$rs['goods_id']),'v_goods');
       $data['direction']=$rs2['direction'];
       $data['attention_list']=$rs2['attention_list'];
       $data['change_goods']=$rs2['change_goods'];
       $data['goods_url']=base_url("myshop/products_detail?act_id=$rs2[act_id]");
       $rs2=$this->User_model->get_select_one('banner_product as image,user_id',array('act_id'=>$rs2['act_id']),'v_activity_children');
       $data['user']=$this->User_model->get_select_one('user_name,image',array('user_id'=>$rs2['user_id']),'v_users');
       $data['user_image']=$data['user']['image'];
       $data['user_name']=$data['user']['user_name'];
     //  $data['attr']=$rs['goods_attr_id'];
       $str_arr=array();
       $check_arr=array();
       $str='';
       $str_price=0;
       $data['attr_arr']=json_decode($rs['goods_attr_id'],TRUE);
       $data['attr_arr']= $this->multi_array_sort($data['attr_arr'],'attr_type',$sort=SORT_ASC);
       $data['change_date']='';
       foreach($data['attr_arr'] as $k=>$v)
       {
           if($v['attr_type']==3){
               $check_arr[]=$v;
               unset($data[$k]);
           }else{
               $str_arr[]=$v;
           }
       }
       foreach($str_arr as $k=>$v)
       {
           if($v['attr_type']==1){
               $data['change_date']= $v['attr_val']=date('Y-m-d',intval($v['attr_val']));
               $str_price+=$v['attr_price'];
               continue;
           }else{
               $str_price+=$v['attr_price'];
               $str.=$v['attr_val'].' ';
           }
       }
       foreach($check_arr as $k=>$v)
       {
         $check_arr[$k]=$str.$v['attr_val'];
         $check_arr[$k]=array($str.$v['attr_val'],$v['attr_num'],$v['attr_num']*($v['attr_price']+$str_price+$rs['goods_price']));
       }
       $data['attention']=$rs['attention'];
       $data['goods_amount']=$rs['goods_amount'];
       $data['attr_arr']=$check_arr;
       $data['date']=date('Y-m-d H:i',$rs['add_time']);
       $data['pass']=md5($rs['user_id_sell'].$rs['add_time'].'et');
       $data['image']=$rs2['image'];
       $data['goods_name']=$rs['goods_name'];
       $count=$this->User_model-> get_count(array('order_id'=>$order_id,'type'=>'2'), $table='v_order_addition');
       $data['count']=$count['count'];

       if($this->input->get('test',TRUE))
       {
           echo '<pre>';print_r($data);exit();
       }
        $_SESSION['order_id']=$order_id;
      //
       $this->load->view('ordernew/order_detail',$data);

   }

   public function oreder_detail_bc()
   {
       if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false ){
           if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false){
               return false;
           }else{
               $url=base_url('orderwx/order_list_unpaybuy');
               $user_id_wx=$this->get_wx_userid($url);
           }
       }
       if(isset($_SESSION['order_id'])){
           $order_id= $_SESSION['order_id'];
       }else{
           echo '订单查询过期，请去详情页面重新查看';
           exit();
       }
     //  get_select_all($select='*',$where='1=1',$order_title='start_time',$order='ASC',$table='v_activity',$left=0,$left_table='',$left_title="")
       $data['info']=$this->User_model->get_select_all('type,cn_name,cn_py,cn_mobile,oth_mobile',array('order_id'=>$order_id),$order_title='type',$order='ASC','v_order_addition');


       $this->load->view('ordernew/people',$data);

   }

    public function share_order($page=1)
    {
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            //return false;
        }
        $data['user_id']=$user_id=2025;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{
            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunconfimsell="share_user_id=$user_id AND order_status IN ('-1','1','2','3','4')";
        $page_num =10;
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
       // print_r($data);exit();
        $this->load->view('ordernew/share_order',$data);
    }


    public function order_list_unpaybuy($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            if($this->input->get('test')){
                $user_id=1077;
            }else{
                return false;
            }

        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $whereunpaybuy="user_id_buy=$user_id And (order_status='0' OR  order_status='4') AND from IN ('0','1','4','5','6','7','8','9')";
        $page_num =5;
        if($this->input->get('test')){
            $page_num =1;
        }

        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunpaybuy,'v_order_info');
        $data['unpaybuymax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unpaybuymax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $data['unpaybuy']=$this->Order_model->get_order_list($whereunpaybuy,$start,$page_num);
       // echo "<pre>";print_r($data);exit();

        if($this->input->get('detail')){
            echo "<pre>";print_r($data);exit();
        }
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('ordernew/unpaybuy_ios',$data);
    }
    public function order_list_unpaysell($page=1)
    {
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
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{
            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunpaysell="user_id_sell=$user_id And (order_status='0' OR  order_status='4') AND from IN ('0','1','4','5','6','7','8','9')";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunpaysell,'v_order_info');
        $data['unpaysellmax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unpaysellmax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        //$data['unpaysell']=$this->Order_model->order_list_home($whereunpaysell,$start,$page_num);
        $data['unpaysell']=$this->Order_model->get_order_list($whereunpaysell,$start,$page_num);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        $this->load->view('ordernew/unpaysell_ios',$data);
    }

    public function order_list_unshipbuy($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            $user_id=1077;
          //  return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        //(from='0' OR from='1' OR from='4' OR from='5' OR from='6' OR from='7' OR from='6'  OR from='8')
        $whereunshipbuy="user_id_buy=$user_id And order_status='1'  AND from IN ('0','1','4','5','6','7','8','9')";
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($whereunshipbuy,'v_order_info');
        $data['unshipbuymax_page'] = ceil($count['count']/$page_num);
        if($page>$data['unshipbuymax_page']) {$page=1;}
        $start = ($page-1)*$page_num;
       // $data['unshipbuy']=$this->Order_model->order_list_home($whereunshipbuy,$start,$page_num);
        $data['unshipbuy']=$this->Order_model->get_order_list($whereunshipbuy,$start,$page_num);
       // echo "<pre>";print_r($data);exit();
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if($user_id==1077){
           if($this->input->get('test')){
               echo '<pre>';print_r($data);exit();
           }

        }

        $this->load->view('ordernew/unshipbuy_ios',$data);
        /*if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('ordernew/unshipbuy_ios',$data);
        }else{
            $this->load->view('ordernew/unshipbuy',$data);
        }*/
    }
//旅拍补全尾款
    public function order_supply_amount()
    {
        $order_id=$this->input->get_post('order_id',TRUE);
        if(!$order_id)
        {
            return false;
        }
        $is_insert=$this->User_model->get_select_one('*',array('pid'=>$order_id),'v_order_info');

        if($is_insert==0)
        {
            $data_order_info=$this->User_model->get_select_one('*',array('order_id'=>$order_id),'v_order_info');
            unset($data_order_info['order_id']);
            unset($data_order_info['order_sn']);

            if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')){
                $sys='APP';
            }elseif(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                $sys='WX';
            }else{
                $sys='H5';
            }
            if($sys=='APP')
            {
                $data_order_info['from']='7';
            }
            elseif($sys=='WX')
            {
                $data_order_info['from']='9';
            }
            else{
                return false;
            }
            $data_order_info['order_sn']=$this->get_order_sn();
            $data_order_info['order_status']='-1';
            $data_order_info['pid']=$order_id;
            $order_new_id=$this->User_model->user_insert('v_order_info',$data_order_info);

            $data_order_goods=$this->User_model->get_select_one('*',array('order_id'=>$order_id),'v_order_goods');
            unset($data_order_goods['rec_id']);
            $data_order_goods['order_id']=$order_new_id;

            $order_goods_id=$this->User_model->user_insert('v_order_goods',$data_order_goods);


            $data_order_addition=$this->User_model->get_select_all($select='*',array('order_id'=>$order_id),'addition_id',$order='ASC',$table='v_order_addition');
            foreach($data_order_addition as $k=>$v)
            {
                $data_order_addition[$k]['order_id']=$order_new_id;
                unset( $data_order_addition[$k]['addition_id']);
            }

            $this->User_model->user_insert('v_order_addition',$data_order_addition,'0');

            $json=array();
            if($sys=='APP')
            {

                $json['order_id']=$order_goods_id;
                $json['user_id_buy']=$data_order_info['user_id_buy'];
                $json['prod']='1';
                if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                    $json['number']=1;
                    $json['order_sn']=$data_order_info['order_sn'];
                    $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
                }else{
                    $json['num']=1;
                }
                $json['amount']=$data_order_info['goods_amount']-$data_order_info['front_amount'];

                //  $json['amount']=$rs['goods_amount'];
                $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                $json['productName']='特价';
                echo json_encode($json);
            }else{
                return false;
            }
        }
        else
        {


            $json['order_id']=$is_insert['order_id'];
            $json['user_id_buy']=$is_insert['user_id_buy'];
            $json['prod']='1';
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                $json['number']=1;
                $json['order_sn']=$is_insert['order_sn'];
                $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
            }else{
                $json['num']=1;
            }
            $json['amount']=$is_insert['goods_amount']-$is_insert['front_amount'];

            //  $json['amount']=$rs['goods_amount'];
            $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
            $json['productName']='特价';
            echo json_encode($json);


        }


    }

    public function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }


    public function order_list_unshipsell($page=1){
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
       $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            //$user_id=1077;
            return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunshipsell="user_id_sell=$user_id And order_status='1' AND  (from='0' OR from='1' OR from='4' OR from='5')";
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
        $this->load->view('ordernew/unshipsell_ios',$data);
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
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunconfimbuy="user_id_buy=$user_id And (order_status='2' OR  order_status='3') AND  (from='0' OR from='1' OR from='4' OR from='5')";
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
        $this->load->view('ordernew/unconfimbuy_ios',$data);
         /*if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('ordernew/unconfimbuy_ios',$data);
        }else{
            $this->load->view('ordernew/unconfimbuy',$data);
        } */
       
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
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $whereunconfimsell="user_id_sell=$user_id And (order_status='2' OR  order_status='3') AND  (from='0' OR from='1' OR from='4' OR from='5')";
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
        $this->load->view('ordernew/unconfimsell_ios',$data);
     /*  if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
            $this->load->view('ordernew/unconfimsell_ios',$data);
        }else{
            $this->load->view('ordernew/unconfimsell',$data);
        } */
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

    //旅拍订单详情
    public function order_detail_ts($order_id)
    {
        $data['down']=$this->down;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            $user_id=1077;
          //  return false;
        }
        $data['user_id']=$user_id;
        $this->Order_model->judge_timei_out($user_id);
        $this->Order_model->sure_sub($user_id);
        $row=$this->User_model->get_username_and_image_by_userid($user_id);
        if($row['is_guide']=='1')
        {
            $data['show_share']=TRUE;
        }else{

            $data['show_share']=FALSE;
            if($user_id==1077){
                $data['show_share']=TRUE;
            }
        }
        $data['user_name']=$row['user_name'];
        $data['image']=$row['image'];
        $data['order_info']=$this->Order_model->get_order_detail($order_id,$user_id);
       // echo "<pre>";print_r($data);exit();

        $goods=$this->User_model->get_select_one('ts_id,direction,attention_list,change_goods',array('goods_id'=>$data['order_info']['goods']['goods_id']),'v_goods');
        $data['goods_url']=base_url("myshop/trip_detail?ts_id=$goods[ts_id]");
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if($user_id==1077){
           if($this->input->get('test')){
               echo '<pre>';print_r($data);exit();
           }
        }
        $this->load->view('ordernew/order_detail_ts',$data);

    }

}