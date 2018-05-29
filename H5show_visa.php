<?php
/**
 * HTML模板展示页面
 * Date: 2017/4/20
 * Time: 11:05
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class H5show_visa extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('url');

    }


    /**
     * 展示首页面
     */
    public function index($id=0)
    {
        //无H5_id时，显示所有签证国家
        if(!is_numeric($id) || !$id)
        {
            $data=[];
            $data['index'] = 1;
            //获取所有签证H5页面信息
            $where=" type_id=2 AND is_show=1 ";
            $res = $this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
            if(!$res)
            {
                return false;
            }
            foreach ($res as $key => $value) {
                //获取签证国家信息
                $visa = $this->User_model->get_select_one('country',array('h5_id'=>$value['h5_id'],'is_del'=>0),'v_h5_visa');
                if($visa)
                {
                    $country_image=base_url($this->User_model->get_image(18,$value['h5_id']));
                    $data['country_list'][] = array(
                                                'h5_id' => $value['h5_id'],
                                                'country' => $visa['country'],
                                                'country_image' => $country_image,
                                                'country_url' => base_url("h5show_visa/index/$value[h5_id]")
                        );
                }
            }
            //设置h5_id，获取第一个国家的特色及须知做展示用
            //（此处为临时修改，想定应该有一个总的介绍信息）
            $id = $res[0]['h5_id'];
        }
        else
        {
            $data=[];
            $data['index'] = 0;
            //根据ID获取对应H5页面信息
        }
        $h5_info = $this->User_model->get_select_one('*',array('h5_id'=>$id,'is_show'=>1),'v_h5_info');
        if(!$h5_info)
        {
            return false;
        }
        $data['h5_id'] = $id;
        //获取签证产品
        $res = $this->get_visa_info($id);
        $data['visa'] = $res['visa'];
        //获取签证办理地列表
        $data['place_list'] = $res['place_list'];
        //获取签证类型列表
        $data['type_list'] = $res['type_list'];
    
        //获取旅游产品
        $data['goods'] = $this->get_goods_info($id);
        //echo '<pre>';
        //var_dump($data['visa']);
    
        $data['share_title']=$h5_info['h5_title'];
        $data['share_desc']=$h5_info['share_desc'];
    
        $data['index_url']=base_url("h5show_visa/index/$id");
        $data['index_url']= $h5_info['url_type'] ? $data['index_url'].'?type='.$h5_info['url_type'] : $data['index_url'];
        $data['shareimage']=base_url($this->User_model->get_image(17,$id));
        $data['signPackage']=$this->wx_js_para(3);
    
        $data['sub_url'] = base_url("h5show_visa/sub_order/$id");
    
        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);
    
        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);
          
     // echo'<pre>';print_r($data);exit();
        $this->load->view('h5show/visa_index',$data);
        $this->show_count();
    }

    /**
     * 签证详情页面
     */
    public function visa_detail($id=0)
    {
        if($id)
        {
            $res = $this->User_model->get_select_one('*',array('visa_id'=>$id,'is_del'=>0),'v_h5_visa');
            if($res)
            {
                $data['info'] = $res;
                $this->load->view('h5show/visa_detail',$data);
            }
        }
    }

    /**
     * 旅游产品详情页面
     */
    public function goods_detail($id=0)
    {
        if($id)
        {
            $res = $this->User_model->get_select_one('*',array('goods_id'=>$id,'is_del'=>0),'v_h5_goods');
            if($res)
            {
                $data['info'] = $res;
                $res['goods_info'] = json_decode($res['goods_info']);
                $res['tip_info'] = json_decode($res['tip_info']);
                $res['package_info'] = json_decode($res['package_info']);

                foreach ($res['goods_info'] as $key => $value) {
                    $res['goods_info'][$key]->view_intro = explode('**', $value->view_intro);
                }
                foreach ($res['tip_info'] as $key => $value) {
                    $res['tip_info'][$key]->tip_intro = explode('**', $value->tip_intro);
                }
                foreach ($res['package_info'] as $key => $value) {
                    $res['package_info'][$key]->package_intro = explode('**', $value->package_intro);
                }
                $data['info']['goods_info'] = $res['goods_info'];
                $data['info']['tip_info'] = $res['tip_info'];
                $data['info']['package_info'] = $res['package_info'];

                $this->load->view('h5show/goods_detail',$data);
            }
        }
    }

    /**
     * 获取签证产品
     */
    function get_visa_info($id,$where='')
    {
        $res['visa'] = $res['place_list'] = $res['type_list'] = array();
        if($id)
        {
            $where=$where ? $where : "h5_id=$id AND is_del=0";
            $visa_list=$this->User_model->get_select_all($select='*',$where,'visa_id','ASC','v_h5_visa');
            if($visa_list)
            {
                foreach ($visa_list as $key => $value) {
                    $visa_list[$key]['detail_url'] = '/h5show_visa/visa_detail/'.$value['visa_id'];
                    $place_list[] = $value['place'];
                    $price_list[$value['place']] = intval($value['price']);
                    $type_list[] = $value['visa_type'];
                }
                $place_list = array_unique($place_list);
                $type_list = array_unique($type_list);
            }
            $res['visa'] = $visa_list;
            foreach ($place_list as $key => $value) {
                $res['place_list'][]=array(
                                        'place' =>$value,
                                        'price' =>$price_list[$value]
                    );
            }
            $res['type_list'] = $type_list;
        }

        return $res;
    }

    /**
     * 获取旅游产品
     */
    function get_goods_info($id)
    {
        $res = array();
        if($id)
        {
            $where="h5_id=$id AND is_del=0";
            $res=$this->User_model->get_select_all($select='*',$where,'trip_id,goods_id','ASC','v_h5_goods');
            if($res)
            {
                $trip_name_tmp = '';
                foreach ($res as $key => $value) {
                    $res[$key]['goods_info'] = json_decode($value['goods_info']);
                    $res[$key]['tip_info'] = json_decode($value['tip_info']);
                    $res[$key]['package_info'] = json_decode($value['package_info']);
                    $res[$key]['detail_url'] = '/h5show_visa/goods_detail/'.$value['goods_id'];
                    if($trip_name_tmp==$value['trip_id'])
                    {
                        $res[$key]['trip'] = '';
                    }
                    else
                    {
                        $res[$key]['trip'] = $value['trip_id'];
                        $trip_name_tmp = $value['trip_id'];
                    }
                }
            }
        }
        return $res;
    }

    /**
     * 签证详情页面
     */
    public function sub_order($id=0)
    {
        //echo '<pre>';
        //print_r($_POST);
        //die;
        $data['h5_title'] = $this->input->post('title',true);
        $data['h5_id'] = $id;
        $dats['id']=$id;
        $dats['nain']=date('Y',time());
        $dats['yue']=date('m',time());
        $dats['ri']=date('d',time());
        $dats['v_sum']=1;
     $this->User_model->user_insert('v_qz_sum',$dats);


        //$order_fly = explode(',', $this->input->post('forminpAir',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[2]) ? intval($order_fly[2]) : 0;
        //$order_fly = explode(',', $this->input->post('formFly',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        //$order_air = explode(',', $this->input->post('formAir',true));
        //$data['order_air'] = json_encode($order_air);
        //$order_amount = isset($order_air[2]) ? $order_amount+intval($order_air[2]) : $order_amount;
        //$order_car_come = explode(',', $this->input->post('formcar_come',true));
        //$data['order_car_come'] = json_encode($order_car_come);
        //$order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        //$order_car_go = explode(',', $this->input->post('formcar_go',true));
        //$data['order_car_go'] = json_encode($order_car_go);
        //$order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        //$order_hotel = explode(',', $this->input->post('formhotel',true));
        //$data['order_hotel'] = json_encode($order_hotel);
        //$order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;
        //$trip[0] = explode(',', $this->input->post('formstroke1',true));
        //$order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        //$trip[1] = explode(',', $this->input->post('formstroke2',true));
        //$order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        //$trip[2] = explode(',', $this->input->post('formstroke3',true));
        //$order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
        //$trip[3] = explode(',', $this->input->post('formstroke4',true));
        //$order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        //$trip[4] = explode(',', $this->input->post('formstroke5',true));
        //$order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;

        //旅游产品信息
        $trip = array();
        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_trip_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }
        if($trip)
        {
            $data['order_trip'] = json_encode($trip);
        }

        //签证信息
        //$res = $this->get_trip_info($this->input->post('formvisa',true));
        //$visa_id = isset($res['info'][0][2]) ? $res['info'][0][2] : '';
        //$order_amount = $order_amount + $res['amount'];
        //$data['order_visa'] = json_encode($res['info']);
        $visa_id = $this->input->post('formvisaId',true);
        $visa_price = intval($this->input->post('formvisa',true));
        $order_amount = $order_amount + $visa_price;
        $res=array($this->input->post('title',true),$visa_price,$visa_id);
        $data['order_visa'] = json_encode($res);

        $data['order_amount'] = floatval($order_amount);
        $data['order_sn'] = $this->get_order_sn();
        $data['url'] = base_url("h5show_visa/index/$id");

        //print_r($res['info']);print_r($trip);print_r($data);die;

        $order=$this->User_model->user_insert('v_h5_order',$data);
        //print_r($order);die;

        if($visa_id)
        {
            //获取签证外链
            $res = $this->User_model->get_select_one('visa_link',array('visa_id'=>$visa_id,'is_del'=>0),'v_h5_visa');
            if($res)
            {
                redirect($res['visa_link']);
            }
        }
        else
        {
            if($order)
            {
                $this->load->view('ziyouxing/ok',$data);
            }
        }

    }

    /**
     * 签证产品列表接口
     */
    public function get_visa_ajax()
    {
        $h5_id=$this->input->post_get('h5_id',TRUE);
        $place=$this->input->post_get('place',TRUE);
        $type=$this->input->post_get('type',TRUE);

        $result = array();
        //拼接检索条件
        if($h5_id)
        {
            $where = "h5_id=$h5_id AND is_del=0";
        }
        else
        {
            return json_encode($result);
        }
        if($place)
        {
            $where .= " AND place='".$place."'";
        }
        if($type)
        {
            $where .= " AND visa_type='".$type."'";
        }
        //echo $where;
        //获取签证列表
        $res=$this->get_visa_info($h5_id,$where);
        //print_r($res);

        $result = $res['visa'];

        echo json_encode($result);

    }

    /**
     * 签证产品列表接口 新
     */
    public function get_visa_ajax_new()
    {
        $h5_id=$this->input->post_get('h5_id',TRUE);

        $result = array();
        //拼接检索条件
        if($h5_id)
        {
            $where = "h5_id=$h5_id AND is_del=0";
        }
        else
        {
            return json_encode($result);
        }
        //获取签证列表
        $res=$this->get_visa_info($h5_id,$where);
        //echo '<pre>';
        //print_r($res);
        if($res['visa'])
        {
            foreach ($res['visa'] as $key => $value) {
                $result[] = array(
                            'visa_id' => $value['visa_id'],
                            'country' => $value['country'],
                            'price' => intval($value['price']),
                            'place' => $value['place'],
                            'visa_link' => $value['visa_link']
                    );
            }
        }


        echo json_encode($result);

    }


    //获取order_sn
    function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    //表单信息切割处理
    function get_trip_info($data)
    {
        $amount = 0;
        $info = array();
        $temp = explode(';', $data);
        if($temp){
            foreach ($temp as $key => $value) {
                $tmp =  explode(',',$value);
                if($tmp)
                {
                    foreach ($tmp as $k => $v) {
                        if($v)
                        {
                            $info[$k][]=$v;
                            if($key==3)
                            {
                                $amount = isset($v) ? $amount+intval($v) : $amount;
                            }
                        }
                    }
                }
            }
        }
        return array('amount'=>$amount,'info'=>$info);

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



    function return_hm($time='21600')
    {

        $h_num=intval($time/3600);
        $m_num=intval(($time-$h_num*3600)/60);
        $result = $h_num ? $h_num.'小时' : '';
        $result .= $m_num ? $m_num.'分' : '';
        return $result;
    }
}
