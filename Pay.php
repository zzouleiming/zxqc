<?php

defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL);

class Pay extends My_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     * 
     */

    private $_yipay_url = 'http://pay.zhangzhongzhifu.com/sdkServer/thirdpays/pay/';
    private $_yipay_log_path = APPPATH . 'logs/yipay';

    public function __construct() {
        parent::__construct();

        $this->load->model('Order_info_model');
        $this->load->model('cg/Car_order_model');
        $this->load->model('cg/Car_modify_model');
        $this->load->model('tl/Page_order_model');
        $this->load->model('Business_info_model');
        $this->load->model('User_Api_model');
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->library('MY_Session');
     //   include_once("/opt/nginx/html/zxqc/api/application/third_party/wxpay_v3/lib/WxPay.JsApiPay.php");
     //    include_once("/opt/nginx/html/zxqc/api/application/third_party/wxpay_ht/lib/WxPay.JsApiPay.php");
    }

    /*     * *支付宝支付* */

    public function alipay() {
        $order_sn = $this->input->post('order_sn', TRUE);
        if (!$order_sn) {
            $order_sn = $this->uri->segment(3);
        }

        //处理订单
        if (preg_match('/^TP\d+$/', $order_sn, $match)) {
            //二销订单
            $where = "order_sn='{$order_sn}'";
            $info = $this->User_model->get_select_one($select = '*', $where, 'us_order');
            $data['order_title'] = $info['order_title'];
            $data['order_price'] = $info['pay_price'];
        } else {
            //其他订单
            $where = "order_sn='{$order_sn}'";
            $info = $this->User_model->get_select_one($select = '*', $where, 'v_h5_order');
            $data['order_title'] = $info['h5_title'];
            $data['order_price'] = $info['order_amount'];
        }

        $data['order_sn'] = $order_sn;
        $data['url'] = base_url('pay/alipay1');
        $this->load->view('pay/alipay/index', $data);
    }

    public function alipay1() {
        $this->load->view('pay/alipay/alipayapi', $data);
    }

    /*     * *支付宝支付回调* */

    public function alipay_notify() {
        $order_sn = $this->input->get('out_trade_no', TRUE);
        $pay_sn = $this->input->get('trade_no', TRUE);
        $trade_status = $this->input->get('trade_status', TRUE);

        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
            //处理订单
            if (preg_match('/^TP\d+$/', $order_sn, $match)) {
                //二销订单
                $where = "order_sn='{$order_sn}'";
                $data['pay_status'] = '1';
                $this->User_model->update_one($where, $data, 'us_order');

                $info = $this->User_model->get_select_one('*', $where, 'us_order');
                $data['url'] = base_url("tpin/home") . '/' . $info['page_id'];
                $this->_sendemail($info);
            } else {
                //其他订单
                $where = "order_sn='{$order_sn}'";
                $data['zf_state'] = '1';
                $this->User_model->update_one($where, $data, 'v_h5_order');

                $info = $this->User_model->get_select_one('*', $where, 'v_h5_order');
                $data['url'] = base_url("tpin/home") . '/' . $info['h5_id'];
            }
        }

        $this->load->view('pay/alipay/success', $data);
    }

// 支付宝支付 通用方法
    public function alipay_xjrh() {
        $order_sn = $this->input->post('order_sn', TRUE);
        if (empty($order_sn)) {
            echo '订单号不能为空';
            die;
        }
        $where = array(
            'order_sn' => $order_sn,
        );
        $res = $this->Page_order_model->get_order_detail($where);
        if (!$res) {
            echo '订单不存在';
            die;
        }
        $where = array('business_id' => $res['business_id']);
        $business_key = $this->Business_info_model->get_business_info_detail($where);
        if ($business_key) {

            $this->session->set_userdata($business_key);
        }
        $data['list'] = $res;
        $data['order_sn'] = $order_sn;
        $data['url'] = base_url('pay/alipayht');
        $this->load->view('payht/alipay/index', $data);
    }

    public function alipayht() {
        $this->load->view('payht/alipay/alipayapi', $data);
    }

    //支付宝支付通用 页面回调
    public function alipay_success() {
        $order_sn = $this->input->get('out_trade_no', TRUE);
        $pay_sn = $this->input->get('trade_no', TRUE);
        $trade_status = $this->input->get('trade_status', TRUE);

        if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {

            $where = "order_sn='{$order_sn}'";
            $data['state'] = '1';
            $data['pay_type'] = 'ALIPAY_WAP';
            $res = $this->User_model->update_one($where, $data, 'tl_order_info');
            $where = array('order_sn' => $order_sn);
            $info = $this->Page_order_model->get_order_detail($where);
              // 查询要发送短信的手机号码
            $mobile=$this->Business_info_model->get_business_info_detail(array('business_id'=>$info['business_id'],'is_del'=>0));
            if ($res) {
                $data['url'] = base_url("home/package_travel/view") . '/' . $info['page_id'];
            }
          
          if($mobile['mobile']){
          	$smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => $mobile['mobile'], //接受短信的用户手机号码
                'tpl_id' => '61438', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#ordersn#=' . $info['order_sn'] . '&#username#=' . $info['order_name'] . '&#mobile#=' . $info['order_mobile'] . '' //您设置的模板变量，根据实际情况修改
            );
            $content = $this->_sendsms($smsConf, 1); //请求发送短信
          }
       /**     
            **/
        }
        $this->load->view('payht/wxjs_pay_success', $data);
    }

    // 微信支付 通用方法
    public function wxht_pay($order_sn = '') {
    	
    	  $where = array(
            'order_sn' => $order_sn,
        );
        $res = $this->Page_order_model->get_order_detail($where);
 
        if (!$res) {
            echo '订单不存在';
            die;
        }
        
        $where = array('business_id' => $res['business_id']);
        $business_key = $this->Business_info_model->get_business_info_detail($where);
        $wx_bus=$business_key['wx_bus'];
       
        require_once APPPATH . 'third_party/wxpay_'.$wx_bus.'/lib/WxPay.Api.php';
        require_once APPPATH . 'third_party/wxpay_'.$wx_bus.'/lib/WxPay.JsApiPay.php';
        $open_id = $this->_wx_get_openid();

        //  $order_sn = 'FT20180110164942028954';
        $where = array(
            'order_sn' => $order_sn,
        );
        $res = $this->Page_order_model->get_order_detail($where);

        if (!$res) {
            echo '订单不存在';
            die;
        }
        $wx_order_data = array(
            'body' => $res['good_name'], //商品描述
            'attach' => '', //附加数据
            'total_fee' => $res['cost_price'] * 100, //商品金额（单位为分）
            'goods_tag' => '', //订单优惠标记
            'open_id' => $open_id,
            'order_sn' => $order_sn
        );

        $data['wxjs_pay_params'] = $this->_htwx_order($wx_order_data);

        $data['order_sn'] = $wx_order_data['order_sn'];
        $data['order_title'] = $wx_order_data['body'];
        $data['order_price'] = $wx_order_data['total_fee'] / 100;
        $this->load->view('payht/wxjs_pay', $data);
    }

    //微信支付回调
    public function wxht_pay_success($order_sn = '') {
        $where = "order_sn='{$order_sn}'";
        $data['state'] = '1';
        $data['pay_type'] = 'WECHAT_SUB';
        $res = $this->User_model->update_one($where, $data, 'tl_order_info');
        $where = array('order_sn' => $order_sn);
        $info = $this->Page_order_model->get_order_detail($where);
             // 查询要发送短信的手机号码
            $mobile=$this->Business_info_model->get_business_info_detail(array('business_id'=>$info['business_id'],'is_del'=>0));
     if ($res) {
     	
     		  $data['url'] = base_url("home/package_travel/view") . '/' . $info['page_id'];
     		  if($info['sendsms']<1){
            if($mobile['mobile']){
                      $smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => $mobile['mobile'], //接受短信的用户手机号码
                'tpl_id' => '61438', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#ordersn#=' . $info['order_sn'] . '&#username#=' . $info['order_name'] . '&#mobile#=' . $info['order_mobile'] . '' //您设置的模板变量，根据实际情况修改
            );
            $content = $this->_sendsms($smsConf, 1); //请求发送短信
     	}
          
            $t['order_id']=$info['order_id'];
            $t['sendsms']=$info['sendsms']+1;
            $this->Page_order_model->save_order_info($t);
         //   echo $this->db->last_query();die;
            }
    /**          $smsConf = array(
                'key' => 'd0a9e0f8dc2052809cde1cc93f913632', //您申请的APPKEY
                'mobile' => '17621274981', //接受短信的用户手机号码
                'tpl_id' => '61438', //您申请的短信模板ID，根据实际情况修改
                'tpl_value' => '#ordersn#=' . $info['order_sn'] . '&#username#=' . $info['order_name'] . '&#mobile#=' . $info['order_mobile'] . '' //您设置的模板变量，根据实际情况修改
            );
            $content = $this->_sendsms($smsConf, 1); //请求发送短信
            **/
        }


        $this->load->view('payht/wxjs_pay_success', $data);
    }

    public function wxjs_pay() {
        $order_sn = $this->input->post('order_sn', TRUE);
        if (!$order_sn) {
            $order_sn = $this->uri->segment(3);
        }
        $datas['consignee'] = $this->input->post('tel', TRUE);
        $datas['mobile'] = $this->input->post('username', TRUE);
        $data['xj_zyx'] = $this->input->post('xj_zyx', TRUE);
        $datas['card'] = $this->input->post('cId', TRUE);
        $datas['goTime'] = $this->input->post('goTime', TRUE);
        $datas['goFly'] = $this->input->post('goFly', TRUE);
        $datas['comeTime'] = $this->input->post('comeTime', TRUE);
        $datas['comeFly'] = $this->input->post('comeFly', TRUE);

        $this->User_model->update_one(array('order_sn' => $order_sn), $datas, 'v_h5_order');

        Header("Location: " . base_url('pay/wxjs_pay1') . '/' . $order_sn);
        exit;
    }

    public function wxjs_pay1() {
        exit('暂不支持微信支付，请在第三方浏览器打开用支付宝支付');
        require_once APPPATH . 'third_party/wxpay_v3/lib/WxPay.Api.php';
        require_once APPPATH . 'third_party/wxpay_v3/lib/WxPay.JsApiPay.php';

        $open_id = $this->_wx_get_openid();
        $order_sn = $this->input->post('order_sn', TRUE);
        if (!$order_sn) {
            $order_sn = $this->uri->segment(3);
        }

        //处理订单
        if (preg_match('/^TP\d+$/', $order_sn, $match)) {
            //二销订单
            $wx_order_data = $this->_tpin_wxorder_data($order_sn, $open_id);
        } else {
            //其他订单
            $wx_order_data = $this->_h5_wxorder_data($order_sn, $open_id);
        }

        $data['wxjs_pay_params'] = $this->_wx_order($wx_order_data);
        $data['order_sn'] = $wx_order_data['order_sn'];
        $data['order_title'] = $wx_order_data['body'];
        $data['order_price'] = $wx_order_data['total_fee'] / 100;
        $this->load->view('pay/wxjs_pay', $data);
    }

    public function wxjs_pay_success() {
        $order_sn = $this->uri->segment(3);

        //处理订单
        if (preg_match('/^TP\d+$/', $order_sn, $match)) {
            //二销订单
            $where = "order_sn='{$order_sn}'";
            $data['info'] = $this->User_model->get_select_one('*', $where, 'us_order');
            $data['url'] = base_url('tpin/home/') . '/' . $data['info']['page_id'];
        } else {
            //其他订单
            $where = "order_sn='{$order_sn}'";
            $data['info'] = $this->User_model->get_select_one('*', $where, 'v_h5_order');
            $data['url'] = base_url("h5show/index") . '/' . $data['info']['h5_id'];
        }

        $this->load->view('pay/wxjs_pay_success', $data);
    }

    public function wxjs_pay_notify() {
        $order_sn = $this->uri->segment(3);

        require_once APPPATH . 'third_party/wxpay_v3/lib/WxPay.Api.php';

        //获取通知的数据
        $xml = file_get_contents('php://input');
        file_put_contents(APPPATH . 'logs/wxpay.log', $xml . "\r\n\r\n", FILE_APPEND);
        //如果返回成功则验证签名
        try {
            $result = WxPayResults::Init($xml);
        } catch (WxPayException $e) {
            file_put_contents(APPPATH . 'logs/wxpay.log', $e->errorMessage() . "\r\n\r\n", FILE_APPEND);
            return false;
        }

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            //处理订单
            if (preg_match('/^TP\d+$/', $order_sn, $match)) {
                //二销订单
                $where = "order_sn='{$order_sn}'";
                $data['pay_status'] = '1';
                $this->User_model->update_one($where, $data, 'us_order');
                $order_info = $this->User_model->get_select_one('*', $where, 'us_order');
                $this->_sendemail($order_info);
            } else {
                //其他订单
                $where = "order_sn='{$order_sn}'";
                $data['zf_state'] = '1';
                $this->User_model->update_one($where, $data, 'v_h5_order');
            }
        }
    }

    public function yipay() {
        $transp = $this->input->post('transp', true);
        $service = $this->input->post('service', true);
        switch ($service) {
            case 'WECHAT_SUB':
            case 'WECHAT_WAP':
                exit('暂不支持微信支付，请在第三方浏览器打开用支付宝支付');
            case 'UNION_H5':
            case 'ALIPAY_WAP':
                $this->_yipay_web($transp, $service);

                break;
            default:
                exit('非法访问');
        }
    }

//新车购页面支付
    public function cgpay() {
        $order_sn = $this->input->post('order_sn', true);
        $service = $this->input->post('service', true);
        //  echo "<pre>";
        //   print_r($service);die;ALIPAY_WAP
        switch ($service) {
            // echo 111;
            case 'WECHAT_SUB':
                $this->_cgpay_web($order_sn, $service);
                break;
            case 'WECHAT_WAP':
            // exit('暂不支持微信支付，请在第三方浏览器打开用支付宝支付');


            case 'UNION_H5':
            case 'ALIPAY_WAP':

                $this->_cgpay_web($order_sn, $service);

                break;
            default:
                exit('非法访问');
        }
    }

    // 自由行支付
    public function tlpay() {
        $order_sn = $this->input->post('order_sn', true);
        $service = $this->input->post('service', true);
        //   print_r($service);die;ALIPAY_WAP
        switch ($service) {
            // echo 111;
            case 'WECHAT_SUB':
                $this->_tlpay_web($order_sn, $service);
                break;
            case 'WECHAT_WAP':
            // exit('暂不支持微信支付，请在第三方浏览器打开用支付宝支付');


            case 'UNION_H5':
            case 'ALIPAY_WAP':

                $this->_tlpay_web($order_sn, $service);

                break;
            default:
                exit('非法访问');
        }
    }

    public function yipay_notify() {
        $posts = $this->input->post();

        $log = "易付返回报文: " . json_encode($posts) . "\r\n";
        $this->_yipay_log($log);

        if ($this->_yipay_sign_check($posts) && $posts['status'] == 'success') {
            switch ($posts['paychannel']) {
                case 'WECHAT_SUB':
                case 'ALIPAY_WAP':
                case 'UNION_H5':
                case 'WECHAT_WAP':
                default:
                    if (!$this->_yipay_web_notify($posts)) {
                        return false;
                    }
                    break;
            }
        } else {
            $log = "发生错误: 签名校验失败或支付失败\r\n";
            $this->_yipay_log($log);
            return false;
        }

        $log = "响应结束\r\n\r\n";
        $this->_yipay_log($log);
        echo 'success';
        return true;
    }

    private function _yipay_sign_check($data) {
        list($order_sn, $service) = explode('|||', $data['orderid']);
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Order_info_model->order_business_detail($where);

        ksort($data);
        $sign = $data['sign'];
        unset($data['sign']);
        $data['key'] = $order_info['key'];
        $sign_str = urldecode(http_build_query($data));
        return $sign == md5($sign_str) ? true : false;
    }

    private function _yipay_web_notify($data) {
        list($order_sn, $service) = explode('|||', $data['orderid']);
        $where = array(
            'a.order_sn' => $order_sn,
            'b.is_master' => 1
        );
        $order_info = $this->Order_info_model->order_user_detail($where);
        if (empty($order_info)) {
            $log = "发生错误: 未找到相应的订单信息\r\n";
            $this->_yipay_log($log);
            return false;
        }

        $order['order_id'] = $order_info['order_id'];
        $order['pay_status'] = 1;
        $order['pay_type'] = $data['paychannel'];
        $order['pay_time'] = strtotime($data['time']);
        $order['pay_sn'] = $data['pdorderid'];
        $ret = $this->Order_info_model->save_order_info($order);
        if (!$ret) {
            $log = "发生错误: 更新订单信息失败\r\n";
            $this->_yipay_log($log);
            return false;
        }
        $log = "更新订单信息成功\r\n";
        $this->_yipay_log($log);

        $order_data = json_decode($order_info['order_data'], true);
        $send_mail = $order_data['send_mail'];
        $template_type = $order_data['template_type'];
        if ($send_mail) {
            $this->_page_sendemail($order_info['order_id'], $template_type);
        }
        return true;
    }

    public function free_tour_callback() {

        $gets = $this->input->get();
        list($order_sn, $service) = explode('|||', $gets['transp']);
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Order_info_model->get_order_info_detail($where);
        if (empty($order_info)) {
            exit("非法访问");
        }
        $data['order_info'] = $order_info;
        $order_data = json_decode($order_info['order_data'], true);
        $data['index_url'] = $order_data['index_url'];
        $data['service'] = $service;
        $data['pay_url'] = base_url('pay/yipay');

        $transp_data = array($order_sn);
        $data['transp'] = implode('|||', $transp_data);

        if ($gets['success']) {
            $this->load->view('pay/free_tour_ok', $data);
        } else {
            $this->load->view('pay/free_tour_cancel', $data);
        }
    }

    private function _yipay_web($transp, $service) {
        list($order_sn) = explode('|||', $transp);
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Order_info_model->order_business_detail($where);
        if (empty($order_info)) {
            exit('非法访问');
        }
        if ($order_info['pay_status'] == 1) {
            exit('订单已支付，请勿重复支付');
        }

        $appid = $order_info['appid'];
        $money = $order_info['pay_price'] * 100;
        //$money = 1;
        $key = $order_info['key'];
        $transp = implode('|||', array($order_sn, $service));
        $sign = md5($appid . $service . $money . $transp . $key);

        $pay_params = array(
            'api' => 0,
            'appid' => $appid,
            'money' => $money,
            'openid' => str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT),
            'productName' => urlencode($order_info['order_title']),
            'traderName' => urldecode($order_info['business_name']),
            'transp' => $transp,
            'sign' => $sign,
            'callbackUrl' => $this->_yipay_web_callbackurl($order_info)
        );

        $pay_url = $this->_yipay_url . $service . '?' . http_build_query($pay_params);
        redirect($pay_url);
    }

//新车购

    private function _cgpay_web($order_sn, $service) {

        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Car_order_model->car_business_detail($where);
        if (empty($order_info)) {
            // echo 111;
            exit('非法访问');
        }
        if ($order_info['pay_status'] == 1) {
            exit('订单已支付，请勿重复支付');
        }

        $appid = $order_info['appid'];
        $money = $order_info['price'] * 100;
        //$money = 1;
        $key = $order_info['key'];
        $transp = $order_sn;
        $sign = md5($appid . $service . $money . $order_sn . $key);

        $pay_params = array(
            'api' => 0,
            'appid' => $appid,
            'money' => $money,
            'openid' => str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT),
            'productName' => urlencode($order_info['order_name']),
            'traderName' => urldecode($order_info['business_name']),
            'transp' => $transp,
            'sign' => $sign,
            'callbackUrl' => $this->_carpay_web_callbackurl($order_info)
        );

        $pay_url = $this->_yipay_url . $service . '?' . http_build_query($pay_params);
        redirect($pay_url);
    }

    //自由行支付

    private function _tlpay_web($order_sn, $service = '') {
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Page_order_model->car_business_detail($where);


        if (empty($order_info)) {
            // echo 111;
            exit('非法访问');
        }
        if ($order_info['state'] == 1) {
            exit('订单已支付，请勿重复支付');
        }

        $appid = $order_info['appid'];
        $money = $order_info['cost_price'] * 100;

        //$money = 1;
        $key = $order_info['key'];
        $transp = $order_sn;
        $sign = md5($appid . $service . $money . $order_sn . $key);

        $pay_params = array(
            'api' => 0,
            'appid' => $appid,
            'money' => $money,
            'openid' => str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT),
            'productName' => urlencode($order_info['good_name']),
            'traderName' => urldecode($order_info['business_id']),
            'transp' => $transp,
            'sign' => $sign,
            'callbackUrl' => $this->_tlpay_web_callbackurl($order_info)
        );

        $pay_url = $this->_yipay_url . $service . '?' . http_build_query($pay_params);
        redirect($pay_url);
    }

    private function _yipay_web_callbackurl($order_info) {
        $order_type = $order_info['order_type'];
        $order_sn = $order_info['order_sn'];
        switch ($order_type) {
            case 1: //车购
                return base_url('pay/free_tour_callback');
                break;
            default:
            case 2: //跟团游
            case 3: //自由行


                break;
        }
    }

    // 自由行回调
    private function _tlpay_web_callbackurl($order_info) {
        $order_type = $order_info['order_type'];
        $order_sn = $order_info['order_sn'];
        //  echo "<pre>";
        //   print_r($order_info);die;
        switch ($order_type) {
            case 1: //新车购
                return base_url('pay/car_tour_callback');
                break;
            case 2: //跟团游
            case 3: //自由行
                return base_url('pay/tl_tour_callback');
                break;
            default:

                break;
        }
    }

    //新车购页面回调
    private function _carpay_web_callbackurl($order_info) {
        $order_type = $order_info['order_type'];
        $order_sn = $order_info['order_sn'];
        //  echo "<pre>";
        //   print_r($order_info);die;
        switch ($order_type) {
            case 1: //新车购
                return base_url('pay/car_tour_callback');
                break;
            case 2: //跟团游
            case 3: //自由行

            default:

                break;
        }
    }

    public function car_tour_callback() {

        $gets = $this->input->get();

        $order_sn = $gets['transp'];
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Car_order_model->get_order_detail($where);
        //    $this->db->last_query();die;
        if (empty($order_info)) {
            exit("非法访问");
        }
        $where = array(
            'group_id' => $order_info['group_id'],
        );
        $modify = $this->Car_modify_model->get_modify_detail($where);

        $data['order_info']['pay_price'] = $order_info['price'];
        $data['service'] = $order_info['pay_type'];
        $data['pay_url'] = base_url('pay/cgpay');


        $data['transp'] = $order_sn;

        $data['index_url'] = base_url('cg/car_home/index') . '/' . $order_info['page_id'] . '?g=' . $order_info['guide_id'] . '&b=' . $order_info['business_id'] . '&r=' . $order_info['group_id'] . '&m=' . $modify['id'];
        if ($gets['success']) {
            $cg['id'] = $order_info['id'];
            $cg['order_state'] = 1;
            $cg['add_time'] = time();
            $this->Car_order_model->save_order_info($cg);
            $this->load->view('pay/free_tour_ok', $data);
        } else {
            $this->load->view('pay/free_tour_cancel', $data);
        }
    }

//自由行 回调
    public function tl_tour_callback() {

        $gets = $this->input->get();

        $order_sn = $gets['transp'];
        $where = array(
            'order_sn' => $order_sn
        );
        $order_info = $this->Page_order_model->get_order_detail($where);
        //    $this->db->last_query();die;
        if (empty($order_info)) {
            exit("非法访问");
        }


        $data['order_info']['pay_price'] = $order_info['price'];
        $data['service'] = $order_info['pay_type'];
        $data['pay_url'] = base_url('pay/tlpay');


        $data['transp'] = $order_sn;

        $data['index_url'] = base_url('home/package_travel/view') . '/' . $order_info['page_id'];
        if ($gets['success']) {
            $cg['order_id'] = $order_info['order_id'];
            $cg['state'] = 1;
            $cg['pay_time'] = time();
            $this->Page_order_model->save_order_info($cg);
            $this->_tlsendmail($order_sn);
            $this->load->view('pay/free_tour_ok', $data);
        } else {
            $this->load->view('pay/free_tour_cancel', $data);
        }
    }

    private function _yipay_log($log) {
        $log_file = $this->_yipay_log_path . '/' . date('Y-m-d');
        @file_put_contents($log_file, $log, FILE_APPEND);
    }

    private function _tpin_wxorder_data($order_sn, $open_id) {
        $where = "order_sn='{$order_sn}'";
        $data = $this->User_model->get_select_one($select = '*', $where, 'us_order');
        $wx_order_data = array(
            'body' => $data['order_title'], //商品描述
            'attach' => '', //附加数据
            'total_fee' => $data['pay_price'] * 100, //商品金额（单位为分）
            'goods_tag' => '', //订单优惠标记
            'open_id' => $open_id,
            'order_sn' => $order_sn
        );
        return $wx_order_data;
    }

    private function _h5_wxorder_data($order_sn, $open_id) {
        $where = "order_sn='{$order_sn}'";
        $data = $this->User_model->get_select_one($select = '*', $where, 'v_h5_order');
        $wx_order_data = array(
            'body' => $data['h5_title'], //商品描述
            'attach' => '', //附加数据
            'total_fee' => $data['order_amount'] * 100, //商品金额（单位为分）
            'goods_tag' => '', //订单优惠标记
            'open_id' => $open_id,
            'order_sn' => $order_sn
        );
        return $wx_order_data;
    }

    private function _wx_get_openid() {
        //①、获取用户openid
        $tools = new JsApiPay();
        return $tools->GetOpenid();
    }

    //微信支付下单
    private function _wx_order($data) {
        $tools = new JsApiPay();
        $notify_url = base_url('pay/wxjs_pay_notify') . '/' . $data['order_sn'];
        //①、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetAttach($data['attach']);
        $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
        $input->SetTotal_fee($data['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($data['goods_tag']);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($data['open_id']);
        $order = WxPayApi::unifiedOrder($input);

        return $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
    }

    //合途在线微信支付下单
    private function _htwx_order($data) {
        $tools = new JsApiPay();

        $notify_url = base_url('pay/wxht_pay_success') . '/' . $data['order_sn'];
        //①、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetAttach($data['attach']);
        $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
        $input->SetTotal_fee($data['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($data['goods_tag']);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($data['open_id']);
        $order = WxPayApi::unifiedOrder($input);

        return $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
    }

    private function _page_sendemail($order_id, $template_type) {
        $where = array(
            'a.order_id' => $order_id,
            'b.is_master' => 1
        );
        $order_info = $this->Order_info_model->order_user_detail($where);
        $order_data = json_decode($order_info['order_data'], true);
        $order_data['order_title'] = $order_info['order_title'];
        $order_data['pay_status'] = $order_info['pay_status'];

        $user = array(
            'user_mobile' => $order_info['user_mobile'],
            'user_name' => $order_info['user_name'],
            'user_fname' => '',
            'user_lname' => '',
            'user_wx' => $order_info['user_wx'],
            'user_cardid' => $order_info['user_cardid'],
            'tj_name' => $order_info['tj_name'],
            'comname' => $order_info['comname'],
            'image' => $order_info['image']
        );
        $mail['data'] = $order_data;
        $mail['user'] = $user;
        if ($template_type) {
            $mail_message = $this->load->view('home/order/free_tour_email_' . $template_type, $mail, true);
        } else {
            $mail_message = $this->load->view('home/order/free_tour_email', $mail, true);
        }
        $mail_title = $order_data['order_title'];
        $this->_sendmail($mail_title, $mail_message);
    }

    private function _sendmail($title, $message) {
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成'); //发件人
        $this->email->subject('来自H5：收到一笔已付款订单！');
        $this->email->to('850522655@qq.com,806191111@qq.com,2168399838@qq.com');
        $this->email->message($message);
        $this->email->send();
    }

    private function _tlsendmail($order_sn) {
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成'); //发件人
        $this->email->subject('你有一笔 已付款订单，请登录后台确认！');
        $this->email->to('850522655@qq.com,806191111@qq.com');
        $this->email->message('订单号' . $order_sn . '【后台链接】' . base_url('usadmin/package_tour'));
        $this->email->send();
    }

    //短信发送

    /**
     * 请求接口返回内容
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    private function _sendsms($params = false, $ispost = 0) {
        $url = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

}
