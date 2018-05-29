<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Business_info_model');
        $this->load->model('Order_info_model');
        $this->load->model('Order_user_model');
        $this->load->helper('url');
        $this->load->library('common');
    }

    //自由行订单
    public function free_tour_confirm(){
        $order_data = $this->input->post('data', true);
        $template_type = $this->input->post('template_type', true);
        $send_mail = $this->input->post('send_mail', true);
        $index_url = $this->input->post('index_url', true);

        $order_data = json_decode($order_data, true);
        foreach($order_data['car'] as $key => $val){
            $choose_price = $val['price'];
            foreach($val['choose'] as $k => $v){
                $choose_price *= $v['num'];
            }
            $order_data['car'][$key]['day'] = $val['day'] ? $val['day'] : date('Y-m-d',time());
            $order_data['car'][$key]['choose_price'] = $choose_price;
        }
        $data['car'] = $order_data['car'];
        foreach($order_data['hotel'] as $key => $val){
            $choose_price = $val['price'];
            foreach($val['choose'] as $k => $v){
                $choose_price *= $v['num'];
            }
            $order_data['hotel'][$key]['day'] = $val['day'] ? $val['day'] : date('Y-m-d',time());
            $order_data['hotel'][$key]['choose_price'] = $choose_price;
        }
        $data['hotel'] = $order_data['hotel'];
        foreach($order_data['storke'] as $key => $val){
            $choose_price = $val['price'];
            foreach($val['choose'] as $k => $v){
                $choose_price *= $v['num'];
            }
            $order_data['storke'][$key]['day'] = $val['day'] ? $val['day'] : date('Y-m-d',time());
            $order_data['storke'][$key]['choose_price'] = $choose_price;
        }
        $data['storke'] = $order_data['storke'];
        foreach($order_data['air'] as $key => $val){
            $choose_price = $val['price'];
            foreach($val['choose'] as $k => $v){
                $choose_price *= $v['num'];
            }
            $order_data['air'][$key]['day'] = $val['day'] ? $val['day'] : date('Y-m-d',time());
            $order_data['air'][$key]['choose_price'] = $choose_price;
        }
        $data['air'] = $order_data['air'];
        $order_data['send_mail'] = $send_mail ? $send_mail : 0;
        $order_data['template_type'] = $template_type ? $template_type : '';
        $order_data['index_url'] = $index_url;
        $data['order_data'] = base64_encode(json_encode($order_data));

        $data['page_id'] = $this->input->post('page_id', true);
        $business_id = $this->input->post('business_id', true);
        $where = array(
            'business_id' => $business_id
        );
        $business_info = $this->Business_info_model->get_business_info_detail($where);
        if(empty($business_info)){
            exit("商户信息获取失败");
        }
        $data['business_id'] = $business_id;
        $data['all_price'] = $this->input->post('allprice', true);
        $data['order_title'] = $this->input->post('title',true);
        $data['order_pay_url'] = base_url('home/order/free_tour_pay');
        if($template_type){
            $this->load->view('home/order/free_tour_confirm_'.$template_type, $data);
        }else{
            $this->load->view('home/order/free_tour_confirm', $data);
        }   
    }

    public function free_tour_pay(){
        $order['page_id'] = $this->input->post('page_id', true);
        $business_id = $this->input->post('business_id', true);
        $where = array(
            'business_id' => $business_id
        );
        $business_info = $this->Business_info_model->get_business_info_detail($where);
        if(empty($business_info)){
            exit("商户信息获取失败");
        }
        $order['business_id'] = $business_id;
        $order_data = $this->input->post('order_data', true);
        $order_data = base64_decode($order_data);
        $order['order_data'] = $order_data;
        $order['order_type'] = 3; //
        $order['order_title'] = $this->input->post('order_title', true);
        $order['order_sn'] = $this->_get_order_sn('FT');
        $order['pay_price'] = $this->input->post('all_price', true);
        $order['add_time'] = time();
        $order_id = $this->Order_info_model->save_order_info($order);
        if(!$order_id){
            exit('提交订单失败');
        }

        $user = array(
            'order_id' => $order_id,
            'user_mobile' => $this->input->post('tel', true),
            'user_name' => $this->input->post('username', true),
            'user_fname' => '',
            'user_lname' => '',
            'user_wx' => '',
            'user_cardid' => $this->input->post('cardId',true),
            'tj_name' => $this->input->post('tjName',TRUE),
            'comname' => $this->input->post('comName',true)  ,
            'is_master' => 1 
        );
        if (isset($_FILES['image_hz']) && $_FILES['image_hz']['error'] == 0) {
            $user['image'] = $this->upload_image('image_hz', 'H5image');
        }
        $this->Order_user_model->save_order_user($user);

        $order_data = json_decode($order_data, true);
        $template_type = $order_data['template_type'];
        $send_mail = $order_data['send_mail'];
        if($send_mail){
            $this->_page_sendemail($order_id, $template_type);
        }

        redirect(base_url('home/order/pay').'/'.$order['order_sn']);
    }

    public function pay($order_sn){
        $where = array(
            'order_sn' => $order_sn
        );
        $order = $this->Order_info_model->get_order_info_detail($where);
        if(empty($order)){
            exit("支付失败");
        }
        if($order['order_status'] != 0){
            exit("订单已关闭");
        }
        if($order['pay_status'] == 1){
            exit("订单已支付");
        }
        $data['pay_url'] = base_url('pay/yipay');
        $data['order_sn'] = $order['order_sn'];
        $data['order_price'] = $order['pay_price'];
        $data['order_title'] = $order['order_title'];
        $data['is_wx'] = $this->_is_wx();
        $transp_data = array($order['order_sn']);
        $data['transp'] = implode('|||', $transp_data);
        
        $this->load->view('home/order/pay', $data);
    }
    //寰宇 订单查询
    public  function search(){
        $tel = $this->input->get('tel',TRUE);
        $type = $this->input->get('type', TRUE);
        $business_id = $this->input->get('business_id',TRUE);
        $where = array(
            'b.user_mobile'=>$tel,
            'a.business_id'=>$business_id,   
            'order_by' => 'a.order_id desc'
        );
        if($type){
            $where['order_type'] = $type;
        }
         
        $data['user_order']=$this->Order_info_model->search_order_info_list($where);
        
        if(!$data['user_order']){
           echo "<script language=javascript>alert('暂无此用户信息');history.back();</script>";die;      
        }
        foreach($data['user_order'] as $k=>$v){
            $data['user_order'][$k]['data']=  json_decode($v['order_data'],TRUE);
        }
        $this->load->view('home/order/search',$data);
    }

    //获取order_sn
    private function _get_order_sn($pre='')
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return $pre.date('YmdHis') . str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
    }

    private function _is_wx()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') === FALSE){
            return FALSE;
        } else {
            return TRUE;
        }
    }
      //单图片处理 
    private function upload_image($filename, $fileurl, $key='time')
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl)){
            if (!mkdir('./public/images/'. $fileurl)){
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        switch ($file['type']){
            case 'image/jpeg':
                $br = '.jpg';break;
            case 'image/png':
                $br = '.png';break;
            case 'image/gif':
                $br = '.gif';break;
            default:
                $br = FALSE;break;
        }

        if($br){
            if($key=='time'){
                $key = md5(rand(1,99999).time());
            }
            $pic_url = "./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            return "/public/images/".$fileurl."/".$key.$br;
        }
    }
    private function  _page_sendemail($order_id, $template_type){
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
        if($template_type){
            $mail_message = $this->load->view('home/order/free_tour_email_'.$template_type, $mail, true);
        }else{
            $mail_message = $this->load->view('home/order/free_tour_email', $mail, true);
        }
        $mail_title = $order_data['order_title'];
        $this->_sendmail($mail_title, $mail_message);
    }
     
    private function _sendmail($title, $message){
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成');//发件人
        $this->email->subject('来自H5：收到一笔未付款订单！');
        $this->email->to('850522655@qq.com,806191111@qq.com,2168399838@qq.com');
        $this->email->message($message);
        $this->email->send();
        
    }
}