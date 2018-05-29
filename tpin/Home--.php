<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends MY_Controller
{
    public function __construct(){
        parent::__construct();

        $this->load->library('common');

        $this->load->model('Page_info_model');
        $this->load->model('Car_info_model');
        $this->load->model('Car_type_model');
        $this->load->model('Line_info_model');
        $this->load->model('Hotel_info_model');
        $this->load->model('Order_info_model');
        $this->load->model('Order_user_model');
        $this->load->model('Wx_account_model');
        $this->load->model('Problem_info_model');

        $this->load->library('session');

        $this->load->helper('url');
    }

    //店铺首页
    public function index($page_id){
        //页面信息
        $data['page_info'] = $this->_page_info($page_id);

        $data['signPackage'] = $this->Wx_account_model->wx_js_para(3);
        $data['car_url'] = base_url('tpin/home/car').'/'.$page_id;
        $data['play_url'] = base_url('tpin/home/play').'/'.$page_id;
        $data['food_url'] = base_url('tpin/home/food').'/'.$page_id;
        $data['kefu_url'] = base_url('tpin/home/kefu').'/'.$page_id;
        $data['order_contact_url'] = base_url('tpin/home/order_contact');
        $data['order_search_url'] = base_url('tpin/home/order_search');
        $data['page_qrcode_url'] = base_url('tpin/home/qrcode?data='.urlencode($data['page_info']['share_url']));

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/index', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/index', $data);
        } 
        $this->show_count();
    }

    public function qrcode(){
        require_once(APPPATH.'libraries/Phpqrcode.php');
        $url = urldecode($this->input->get('data', true));
        QRcode::png($url);
    }

    //用车
    public function car($page_id){
        //页面信息
        $data['page_info'] = $this->_page_info($page_id);

        //用车信息
        $data['car_info'] = $this->_car_info($page_id);

        $data['order_contact_url'] = base_url('tpin/home/order_contact');
        $data['order_search_url'] = base_url('tpin/home/order_search');
        $data['page_qrcode_url'] = base_url('tpin/home/qrcode?data='.urlencode($data['page_info']['share_url']));

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/car', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/car', $data);
        } 
        $this->show_count();
    }

    //游玩
    public function play($page_id){
        //页面信息
        $data['page_info'] = $this->_page_info($page_id);

        //游玩信息
        $data['line_info'] = $this->_line_info($page_id);

        $data['order_contact_url'] = base_url('tpin/home/order_contact');
        $data['order_search_url'] = base_url('tpin/home/order_search');
        $data['page_qrcode_url'] = base_url('tpin/home/qrcode?data='.urlencode($data['page_info']['share_url']));

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/play', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/play', $data);
        }
        $this->show_count(); 
    }

    //美食
    public function food($page_id){
        //页面信息
        $data['page_info'] = $this->_page_info($page_id);

        //美食信息
        $data['rest_info'] = $this->_rest_info($page_id);

        $data['order_contact_url'] = base_url('tpin/home/order_contact');
        $data['order_search_url'] = base_url('tpin/home/order_search');
        $data['page_qrcode_url'] = base_url('tpin/home/qrcode?data='.urlencode($data['page_info']['share_url']));

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/food', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/food', $data);
        } 
        $this->show_count();
    }

    //客服
    public function kefu($page_id){
        //页面信息
        $data['page_info'] = $this->_page_info($page_id);

        //问题信息
        $data['page_problem']=$this->_page_problem($page_id);

        $data['order_contact_url'] = base_url('tpin/home/order_contact');
        $data['order_search_url'] = base_url('tpin/home/order_search');
        $data['page_qrcode_url'] = base_url('tpin/home/qrcode?data='.urlencode($data['page_info']['share_url']));

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/kefu', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/kefu', $data);
        } 
        $this->show_count();
    }


    //行程详情
    public function line_detail($id){
        //餐厅详细信息
        $data['line_detail'] = $this->_line_detail($id);

        $where = array(
            'page_id' => $data['line_detail']['page_id'],
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);

        //展示页面
        if(!($page_info['page_type'])){
            $this->load->view('tpin/home/line_detail', $data);
        }else{
            $this->load->view('tpin/home/'.$page_info['page_type'].'/line_detail', $data);
        } 
        $this->show_count();
    } 

    //餐厅详情
    public function rest_detail($id){
        //餐厅详细信息
        $data['rest_detail'] = $this->_rest_detail($id);

        $where = array(
            'page_id' => $data['rest_detail']['page_id'],
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);

        //展示页面
        if(!($page_info['page_type'])){
            $this->load->view('tpin/home/rest_detail', $data);
        }else{
            $this->load->view('tpin/home/'.$page_info['page_type'].'/rest_detail', $data);
        } 
        $this->show_count();
    } 

    //订单查询
    public function order_search(){
        $mobile = $this->input->get('tel', true);
        $page_id = $this->input->get('page_id', true);

        $where = array(
            'a.page_id' => $page_id,
            'b.user_mobile' => $mobile,
            'a.order_status' => 0,
            'order_by' => 'a.order_id desc'
        );
        $order_list = $this->Order_info_model->search_order_info_list($where);
        if(empty($order_list)){
            exit("<script>alert('未查询到订单数据');history.go(-1)</script>");
        }

        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        $index_url = base_url('tpin/home').'/'.$page_info['page_id'].'?'.$page_info['share_url'];
        foreach($order_list as $key => $val){
            //支付地址
            $order_list[$key]['pay_url'] = base_url('home/order/pay').'/'.$val['order_sn'].'?index_url='.urlencode($index_url);
            $order_list[$key]['car_data'] = $this->_car_post_data($val['order_data']);
            $order_list[$key]['line_data'] = $this->_line_post_data($val['order_data']);
            unset($order_list[$key]['order_data']);
        }
        $data['order_list'] = $order_list; 
        foreach($order_list as $k=>$v){
            $data['order_list'][$k]['pdf']=  json_decode($v['order_pdf']);
        } 

        //展示页面
        if(!($page_info['page_type'])){
            $this->load->view('tpin/home/order_search', $data);
        }else{
            $this->load->view('tpin/home/'.$page_info['page_type'].'/order_search', $data);
        } 
        $this->show_count();
    }
    
    public function order_contact(){
        $page_id = $this->input->post('page_id', TRUE);
        $order_data = $this->input->post('data', TRUE);

        $data['order_data'] = base64_encode($order_data);
        $data['all_price'] = $this->input->post('allprice', true);
        $data['order_confirm_url'] = base_url('tpin/home/order_confirm');
        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $data['page_info'] = $this->Page_info_model->get_page_info_detail($where);

        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/order_contact', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/order_contact', $data);
        } 
    }

    public function order_confirm(){
        $page_id = $this->input->post('page_id', TRUE);
        $order_data = base64_decode($this->input->post('order_data', TRUE));

        $master_user = array(
            'user_mobile' => $this->input->post('tel', TRUE),
            'user_name' => $this->input->post('cnName', TRUE),
            'user_fname' => $this->input->post('enFirstName', TRUE),
            'user_lname' => $this->input->post('enLastName', TRUE),
            'user_wx' => $this->input->post('weixin', TRUE),
            'user_cardid' => $this->input->post('cardId', TRUE),
            'is_master' => 1
        );
        $user_data = array(
            'master_user' => $master_user,
            'traveler_user' => array()
        );

        $traveler_user_mobile = $this->input->post('telC', TRUE);
        $traveler_user_name = $this->input->post('cnNameC', TRUE);
        $traveler_user_fname = $this->input->post('enFirstNameC', TRUE);
        $traveler_user_lname = $this->input->post('enLastNameC', TRUE);
        $traveler_user_cardid = $this->input->post('cardIdC', TRUE);

        foreach($traveler_user_mobile as $key => $val){
            $user_data['traveler_user'][$key] = array(
                'user_mobile' => $traveler_user_mobile[$key],
                'user_name' => $traveler_user_name[$key],
                'user_fname' => $traveler_user_fname[$key],
                'user_lname' => $traveler_user_lname[$key],
                'user_wx' => '',
                'user_cardid' => $traveler_user_cardid[$key],
                'is_master' => 0 
            );
        }
        $user_data = json_encode($user_data);

        $extra_data = array(
            'hotel' => array(
                'cn_name' => $this->input->post('cnHotel', TRUE),
                'en_name' => $this->input->post('enHotel', TRUE)
            ),
            'fly' => array(
                'no' => $this->input->post('fly', TRUE),
                'come_time' => $this->input->post('comeTime', TRUE),
                'come_place' => $this->input->post('goTime', TRUE)
            ),
        );
        $extra_data = json_encode($extra_data);

        $data['all_price'] = $this->input->post('allprice', true);
        $data['user_data'] = base64_encode($user_data);
        $data['order_data'] = base64_encode($order_data);
        $data['extra_data'] = base64_encode($extra_data);
        $data['car_data'] = $this->_car_post_data($order_data);
        $data['line_data'] = $this->_line_post_data($order_data);
        $data['token'] = $this->build_token();
        $data['order_desc'] = $this->input->post('orderDesc', TRUE);
        $data['order_add_url'] = base_url('tpin/home/order_add');

        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $data['page_info'] = $this->Page_info_model->get_page_info_detail($where);
        //展示页面
        if(!$data['page_info']['page_type']){
            $this->load->view('tpin/home/order_confirm', $data);
        }else{
            $this->load->view('tpin/home/'.$data['page_info']['page_type'].'/order_confirm', $data);
        } 
    }

    public function order_add(){        
        $order['page_id'] = $this->input->post('page_id', true);
        $where = array(
            'page_id' => $order['page_id'],
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        if(!$page_info){
            exit('支付失败');
        }

        $order_data = $this->input->post('order_data', true);
        $order['order_data'] = base64_decode($order_data);
        $extra_data  = $this->input->post('extra_data', true);
        $order['extra_data'] = base64_decode($extra_data);
        $order['order_type'] = 1; //二销
        $order['business_id'] = $page_info['business_id'];
        $order['order_title'] = $page_info['page_title'];
        $order['order_sn'] = $this->_get_order_sn('TP');
        $price_arr = $this->_get_order_price($order['order_data']);
        $order['pay_price'] = $price_arr['pay_price'];
        $order['cost_price'] = $price_arr['cost_price'];
        $order['order_desc'] = $this->input->post('order_desc', true);
        $order['add_time'] = time();
        if($this->check_token('order_confirm')){
            $order_id = $this->Order_info_model->save_order_info($order);
            if(!$order_id){
                exit('支付失败');
            }

            $user_data = $this->input->post('user_data', true);
            $user_data = base64_decode($user_data);
            $user_data = json_decode($user_data, true);

            $user_data['master_user']['order_id'] = $order_id;
            $this->Order_user_model->save_order_user($user_data['master_user']);
            foreach($user_data['traveler_user'] as $key => $val){
                $val['order_id'] = $order_id;
                $this->Order_user_model->save_order_user($val);
            }
            $data = $order;
            $data['car_data'] = $this->_car_post_data($order['order_data']);
            $data['line_data'] = $this->_line_post_data($order['order_data']);

            $data['is_wx'] = $this->_is_wx();
            //首页地址
            $data['index_url'] = base_url('tpin/home').'/'.$page_info['page_id'].'?'.$page_info['share_url'];

            //支付地址
            $data['pay_url'] = base_url('home/order/pay').'/'.$order['order_sn'].'?index_url='.urlencode($data['index_url']);

            //订单搜索地址
            $order_user = $this->Order_info_model->order_user_detail(array('a.order_sn' => $order['order_sn'], 'b.is_master' => 1));
            $data['order_search_url'] = base_url('tpin/home/order_search?tel='.$order_user['user_mobile'].'&page_id='.$order_user['page_id']);

            $data['page_info'] = $page_info;   

            $this-> _sendemail($data);
            //展示页面
            if(!($page_info['page_type'])){
                $this->load->view('tpin/home/order_pay', $data);
            }else{
                $this->load->view('tpin/home/'.$page_info['page_type'].'/order_pay', $data);
            } 
        }else{
            redirect(base_url('tpin/home').'/'.$page_info['page_id'].'?'.$page_info['share_url']);
        }
    }

    private function _page_info($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        if(empty($page_info)){
            redirect(404);
        }
        $page_info['image_data'] = json_decode($page_info['image_data'], true);
        $page_info['kf_data'] = json_decode($page_info['kf_data'], true);
        $page_info['share_image'] = $page_info['image_data']['share'];
        $page_info['share_url'] = base_url('tpin/home').'/'.$page_info['page_id'].'?'.$page_info['share_url'];
        return $page_info;
    }   private function _car_info($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_show' => 0,
            'order_by' => 'type_id asc, Id asc'
        );
        $car_type_list_temp = $this->Car_type_model->get_car_type_list();
        $car_type_list = array();
        foreach($car_type_list_temp as $key => $val){
            $car_type_list[$val['car_type_id']] = $val['car_type_name'];
        }
        $car_info_temp = $this->Car_info_model->get_car_info_list($where);
        $car_info = array();
        foreach($car_info_temp as $key => $val){
            if(!isset($car_info[$val['type_id']])){
                $car_info[$val['type_id']]['type_name'] = $car_type_list[$val['type_id']];
            }
            $car_info[$val['type_id']]['car_list'][] = $val;
        }
        return $car_info;
    }

    private function _line_info($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0
        );
        $line_info = $this->Line_info_model->get_line_info_list($where);
        foreach($line_info as $key => $val){
            $line_info[$key]['line_detail_url'] = !empty($val['line_detail_url']) ? $val['line_detail_url'] : base_url('tpin/home/line_detail').'/'.$val['line_id'];
        }
        return $line_info;
    }

    private function _rest_info($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_show' => 1
        );
        $rest_info = $this->Hotel_info_model->get_hotel_info_list($where);
        foreach($rest_info as $key => $val){
            $hotel_image = json_decode($val['hotel_image'], true);
            $view_image = $hotel_image['view_image'];
            $rest_info[$key]['hotel_detail_url'] = base_url('tpin/home/rest_detail').'/'.$val['id'];
            $rest_info[$key]['hotel_list_image'] = $view_image[0];
            $rest_info[$key]['hotel_detail_top_image'] = $view_image[0];
            $rest_info[$key]['hotel_detail_cont_image'] = array_slice($view_image, 1);
        }
        return $rest_info;
    }
    
    private  function _page_problem($page_id){
         $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'is_sort asc, id asc'
        ); 
         $rest_detail = $this->Problem_info_model->get_problem_info_list($where); 
         return $rest_detail;
    }
    private function _rest_detail($id){
        $where = array(
            'id' => $id,
            'is_show' => 1
        );

        $rest_detail = $this->Hotel_info_model->get_hotel_info_detail($where);
        if(empty($rest_detail)){
            redirect(404);
        }

        $hotel_image = json_decode($rest_detail['hotel_image'], true);
        $view_image = $hotel_image['view_image'];
        $rest_detail['hotel_list_image'] = $view_image[0];
        $rest_detail['hotel_detail_top_image'] = $view_image[0];
        $rest_detail['hotel_detail_cont_image'] = array_slice($view_image, 1);

        return $rest_detail;
    }

    private function _line_detail($id){
        $where = array(
            'line_id' => $id,
            'is_del' => 0
        );

        $line_detail = $this->Line_info_model->get_line_info_detail($where);
        if(empty($line_detail)){
            redirect(404);
        }

        return $line_detail;
    }

    private function _car_post_data($order_data){
        $result = json_decode($order_data, true);
        if(json_last_error() !== JSON_ERROR_NONE || empty($result) || !isset($result['car'])){
            return array();
        }
        $car_data = $result['car'];
        foreach($car_data as $key => $val){
            $car_id = str_replace('car', '', $val['id']);
            $where = array(
                'Id' => $car_id,
                'is_show' => 0
            );
            $car_info = $this->Car_info_model->get_car_info_detail($where);
            $car_data[$key]['car_info'] = $car_info;
        }
        
        return $car_data;
    }

    private function _line_post_data($order_data){
        $result = json_decode($order_data, true);
        if(json_last_error() !== JSON_ERROR_NONE || empty($result) || !isset($result['stroke'])){
            return array();
        }
        $line_data = $result['stroke'];
        foreach($line_data as $key => $val){
            $line_id = str_replace('stroke', '' , $val['id']);
            $where = array(
                'line_id' => $line_id,
                'is_del' => 0
            );
            $line_info = $this->Line_info_model->get_line_info_detail($where);
            $line_data[$key]['line_info'] = $line_info;
        }
        
        return $line_data;
    }

    private function _get_order_price($order_data){
        $pirce_arr = array();
        $pay_price = 0;
        $cost_price = 0;
        $car_data = $this->_car_post_data($order_data);
        foreach($car_data as $key => $val){
            $pay_price += intval($val['car_info']['car_money']) * $val['num'];
            $cost_price += intval($val['car_info']['car_money']) * $val['num'];
        }
        $line_data = $this->_line_post_data($order_data);
        foreach($line_data as $key => $val){
            $pay_price += intval($val['line_info']['price_p']) * $val['num'];
            $cost_price += intval($val['line_info']['cost_price']) * $val['num'];
        }
        $price_arr['pay_price'] = $pay_price;
        $price_arr['cost_price'] = $cost_price;
        return $price_arr;
    }

    //获取order_sn
    private function _get_order_sn($pre='')
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return $pre.date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function _is_wx()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') === FALSE){
            return FALSE;
        } else {
            return TRUE;
        }
    }
        public function  _sendemail($order_info){
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成');//发件人
        $this->email->subject('普及旅行助手，您有新订单');
        $this->email->to('anita.hui@etjourney.com,2985917470@qq.com,liwenchang715@gmail.com,806191111@qq.com');
       //  $this->email->to('806191111@qq.com');
        $this->email->message('您有一笔新订单 请注意查收'.':订单Id:'.$order_info['order_sn'].'订单名字:'.$order_info['order_title'].':订单金额:'.$order_info['pay_price'] .'->'.'请登录后台查看' );
        $this->email->send();
    }
      
    
}
