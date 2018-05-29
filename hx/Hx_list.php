<?php

/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Hx_list extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('hx/Sh_save');
        $this->load->helper('url');
         $this->load->model('User_model');
        $this->load->model('hx/Hx_channel_model');
        $this->load->model('hx/Hx_coupon_class_model');
        $this->load->model('hx/Hx_coupon_data_model');
        $this->load->model('hx/Hx_coupon_info_model');
        $this->load->model('hx/Hx_coupon_type_model');
        $this->load->model('hx/Hx_machine_model');
        $this->load->model('hx/Hx_manager_model');
        $this->load->model('hx/Hx_order_model');
        $this->load->model('hx/Hx_count_model');
        $this->load->model('hx/Hx_security_info_model');
        $this->load->model('hx/Hx_security_code_model');
        $this->load->model('Business_info_model');
        $this->load->library('MY_Session');
   
    }

    public  function index_list(){
        $business_id=$_SESSION['business_id'];
        if($business_id){
          $result['errcode'] = 200;
           $result['msg'] = "成功";
           $result['data']=$business_id;
          return $this->ajax_return($result);      
        }else{
             $result['errcode'] = 403;
           $result['msg'] = "无权限";
          return $this->ajax_return($result);   
        }
         
    }
    //优惠券 新增 编辑
    public function save_coupon() {
        $data['coupon_name'] = $this->input->post('coupon_name', true); // 优惠券名字 
        $data['coupon_type'] = $this->input->post('coupon_type', true); // 优惠券类型 
        $data['coupon_image'] = $this->input->post('coupon_image', true); // 折扣
        $data['discount'] = $this->input->post('discount', true);
        $data['business_id'] = $_SESSION['business_id']; // 商户ID
        $data['cut_amount'] = $this->input->post('cut_amount', true); // 满多少
        $data['cut_value'] = $this->input->post('cut_value', true); // 减多少
		 $data['details'] = $this->input->post('details', true); // 详细信息
        $data['start_time'] = strtotime($this->input->post('start_time', true)); // 开始有效期
        $data['end_time'] = strtotime($this->input->post('end_time', true)); // 有效期结束时间
        $data['add_time'] = time(); // 优惠券添加时间
        $data['coupon_info_id'] = $this->input->post('coupon_info_id', true); ///  优惠券ID  如果存在 则 执行update  否则 insects
        $res = $this->Hx_coupon_info_model->save_coupon_info_info($data);
        if ($res) {
            if (empty($data['coupon_info_id'])) {
                $result['errcode'] = 200;
                $result['msg'] = "增加成功";
                return $this->ajax_return($result);
            } else {
                $result['errcode'] = 200;
                $result['msg'] = "编辑成功";
                return $this->ajax_return($result);
            }
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "操作失败";
            return $this->ajax_return($result);
        }
    }

    // 商户优惠券列表
    public function coupon_list() {
        $business_id = $_SESSION['business_id'];
        $where = array('business_id' => $business_id, 'is_del' => 0);
        $res = $this->Hx_coupon_info_model->get_coupon_info_list($where);
      
        foreach($res as $k=>$v){
             $business=$this->Business_info_model->get_business_info_detail($where=array('business_id'=>$v['business_id'],'is_del'=>0));
            $res[$k]['business_name']=$business['business_name'];   
            $res[$k]['start_time']=date('Y-m-d ',$v['start_time']);
            $res[$k]['end_time']=date('Y-m-d',$v['end_time']);
            $type=$this->Hx_coupon_type_model->get_coupon_type_detail(array('coupon_type_id'=>$v['coupon_type'],'is_del'=>0));
            $res[$k]['coupon_type_name']=$type['type_name'];
        }
        if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }

    // 优惠券码领取
    public function coupon_code() {
        $data['channel_tag'] = $this->input->get('tag', true); // 渠道标识
        $data['coupon_info_id'] = $this->input->get('cid', TRUE); // 优惠券 id
        $data['coupon_code'] = rand(1000, 9999) . "-" . rand(1000, 9999) . "-" . rand(1000, 9999);
        $data['add_time'] = time();
        $res = $this->Hx_coupon_data_model->save_coupon_info($data);
        if ($res) {
            $sum=$this->Hx_coupon_info_model->get_coupon_info_detail(array('coupon_info_id'=>$data['coupon_info_id'],'is_del'=>0));
            $dat['get_amount']=$sum['get_amount']+1;
            $dat['coupon_info_id']=$data['coupon_info_id'];
            $this->Hx_coupon_info_model->save_coupon_info_info($dat);
            $parameter=array(
                'coupon_code'=>$data['coupon_code'],
                'coupon_name'=>$sum['coupon_name'],
                'coupon_image'=>$sum['coupon_image'],
                'start_time'=>$sum['start_time'],
                'end_time'=>$sum['end_time']
            ); // 响应参数
            // 统计记录领取次数
           $count['count_type']=$data['coupon_info_id'];
           $count['business_id']=$sum['business_id'];
           $count['coupon_time']=date('Ymd',  time());
           $where=array('count_type'=>$count['count_type'],'business_id'=>$count['business_id'],'coupon_time'=>$count['coupon_time'],'is_del'=>0);
           $count_list=$this->Hx_count_model->get_detail($where);
           $count['id']=$count_list['id'];
           $count['receive_sum']=$count_list['receive_sum']+1;
           $this->Hx_count_model->save_info($count);
         //=============================================================================  
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $parameter;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }

//优惠券码列表
    public function coupon_code_list($id = '') {
        $coupon_info_id = $this->input->get('id', TRUE); // 优惠券 id  
	$coupon_info_id = $id?$id:$coupon_info_id;
        $status=$this->input->get('status',TRUE);
        $code=$this->input->get('code',TRUE);
        if(empty($coupon_info_id)){
           $where = array( 'is_del' => 0);  
        }else{
           $where = array('coupon_info_id' => $coupon_info_id, 'is_del' => 0);  
        }
        if($status!=""){
            $where['coupon_status']=$status;
        }
        if($code){
           $where['coupon_code']=$code; 
        }
        $res = $this->Hx_coupon_data_model->get_coupon_list($where);
        foreach($res as $k=>$v){
       $coupon_name=$this->Hx_coupon_info_model->get_coupon_info_detail(array('coupon_info_id'=>$v['coupon_info_id'],'is_del'=>0));
       $res[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
       $res[$k]['use_time']=$v['use_time']?date('Y-m-d H:i:s',$v['use_time']):'未使用';
       
       $res[$k]['coupon_name']=$coupon_name['coupon_name'];
      $order_name= $this->Hx_order_model->get_order_detail(array('coupon_id'=>$v['id']));
      $res[$k]['order_name']=$order_name['order_name'];
       
        }
        if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }
// 订单列表
    public function order_list() {
        $business_id = $_SESSION['business_id'];
        $where = array('business_id' => $business_id, 'is_del' => 0);
        $order_sn=$this->input->get('order_sn',true);
        if($order_sn){
            $where['order_sn']=$order_sn;
        }
        $res = $this->Hx_order_model->get_order_list($where);
        foreach ($res as $k => $v) {
            $res[$k]['add_time'] = date('Y-m-d', $v['add_time']);
            $business = $this->Business_info_model->get_business_info_detail($where = array('business_id' => $business_id, 'is_del' => 0));
            $res[$k]['business_name'] = $business['business_name'];
            $code = $this->Hx_coupon_data_model->get_coupon_detail($where = array('id' => $v['coupon_id']));
            $res[$k]['code'] = $code['coupon_code'];
            $res[$k]['channel_tag'] = $code['channel_tag'];
        }
        if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }
// 优惠券核销请求
    public  function get_coupon(){
        $code=$this->input->get('code',true);
        $where=array('coupon_code'=>$code,'coupon_status'=>0,'is_del'=>0);
        $use=$this->Hx_coupon_data_model->get_coupon_detail($where);
        if($use){
           $where=array('coupon_info_id'=>$use['coupon_info_id'],'is_del'=>0);
           $res=$this->Hx_coupon_info_model->get_coupon_info_detail($where);
            if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
        } 
    }
   // 优惠券核销回调
        public  function use_coupon(){
        $code=$this->input->get('code',true);
        $where=array('coupon_code'=>$code,'coupon_status'=>0,'is_del'=>0);
        $use=$this->Hx_coupon_data_model->get_coupon_detail($where);
        if($use){
          $this->db->trans_begin();
          $data['id']=$use['id'];
          $data['coupon_status']=1;
          $data['use_time']=  time();
          $this->Hx_coupon_data_model->save_coupon_info($data);
          $sum=$this->Hx_coupon_info_model->get_coupon_info_detail(array('coupon_info_id'=>$use['coupon_info_id'],'is_del'=>0));
          $dat['use_amount']=$sum['use_amount']+1;
          $dat['coupon_info_id']=$use['coupon_info_id'];
         $this->Hx_coupon_info_model->save_coupon_info_info($dat);
         $hx['business_id']=$sum['business_id']; //商户id
         $hx['coupon_id']=$use['id'];// 优惠券码主键ID
         $hx['order_sn']=$this->input->get('order_sn',true);
         $hx['price_before']=$this->input->get('price_before',true);
         $hx['price_after']=$this->input->get('price_after',true);
         $hx['status']=1;
         $hx['add_time']=  time();
         $hx['order_name']=$this->input->get('order_name',true);
         $hx['order_details']=$this->input->get('order_details',true);
         $order=$this->Hx_order_model->save_order_info($hx);
         if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if($order){
                  // 统计记录核销次数
           $count['count_type']=$use['coupon_info_id'];
           $count['business_id']=$sum['business_id'];
           $count['coupon_time']=date('Ymd',  time());
           $where=array('count_type'=>$count['count_type'],'business_id'=>$count['business_id'],'coupon_time'=>$count['coupon_time'],'is_del'=>0);
           $count_list=$this->Hx_count_model->get_detail($where);
           $count['id']=$count_list['id'];
           $count['use_sum']=$count_list['use_sum']+1;
           $this->Hx_count_model->save_info($count);
         //=============================================================================  
                
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $order;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
        } else{
             $result['errcode'] = 400;
            $result['msg'] = "优惠券已使用";
            return $this->ajax_return($result); 
        }
    }
    
    // 登录
    public function login() {
        $manager_name = $this->input->post('u_name', true);
        $manager_pwd = $this->input->post('u_pwd', true);
        $manager_pwd = md5('zxqc001' . $manager_pwd);
        $where = array('manager_name' => $manager_name, 'manager_pwd' => $manager_pwd, 'is_del' => 0);
        $res = $this->Hx_manager_model->get_manager_detail($where);
        if ($res) {
            unset($res['add_time']);
            unset($res['manager_pwd']);
            $this->_user_business($res);
            $result['errcode'] = 200;
            $result['msg'] = "登录成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "登录失败";
            return $this->ajax_return($result);
        }
    }

// 注册
    public function register() {
        $data['manager_name'] = $this->input->post('u_name', true);
        $manager_pwd = $this->input->post('u_pwd', true);
        $data['manager_pwd'] = md5('zxqc001' . $manager_pwd);
        if(empty($data['manager_name'])){
           $result['errcode'] = 400;
            $result['msg'] = "注册失败,用户名不能为空";
            return $this->ajax_return($result);  
        }
        $where=array('manager_name'=>$data['manager_name']);
        $names = $this->Hx_manager_model->get_manager_detail($where);
        if($names){
          $result['errcode'] = 400;
            $result['msg'] = "注册失败,用户名已存在";
            return $this->ajax_return($result);     
        }
         if(empty($manager_pwd)){
           $result['errcode'] = 400;
            $result['msg'] = "注册失败,密码不能为空";
            return $this->ajax_return($result);  
        }
        $data['business_id'] = 67388;
        $data['add_time'] = time();
        $res = $this->Hx_manager_model->save_manager_info($data);
        if ($res) {
            $where = array('manager_id' => $res, 'is_del' => 0);
            $res = $this->Hx_manager_model->get_manager_detail($where);
            unset($res['add_time']);
            unset($res['manager_pwd']);
            $this->_user_business($res);
            $result['errcode'] = 200;
            $result['msg'] = "注册成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "注册失败";
            return $this->ajax_return($result);
        }
    }
 // 优惠券类型
 public  function coupon_type(){
     $where=array('is_del'=>0);
     $res=$this->Hx_coupon_type_model->get_coupon_type_list($where);
     if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
 }
 //优惠券详情
 public function  coupon_info(){
     $id=$this->input->get('id',true);
     $where=array('coupon_info_id'=>$id,'is_del'=>0);
     $res=$this->Hx_coupon_info_model->get_coupon_info_detail($where);
     $res['start_time']=date('Y-m-d',($res['start_time']?$res['start_time']: time()));
     $res['end_time']=date('Y-m-d',($res['end_time']?$res['end_time']:strtotime('+1year')));
     $res['add_time']=date('Y-m-d H:i:s',($res['add_time']?$res['add_time']:time()));
       if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
     
     
 }
 //渠道列表
 public  function channel_list(){
     $res=$this->Hx_channel_model->get_channel_list(array('is_del'=>0));
            if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
       
 }
 // 统计前一天的优惠券领取和使用情况
    public function yesterday_sum() {
        $business_id = $_SESSION['business_id'];
        if (empty($business_id)) {
            $business_id = 67388;
        }
     
            $coupon_time = date('Ymd', time() - 86400);
       
            $where = array('business_id' => $business_id, 'coupon_time' => $coupon_time, 'is_del' => 0);
            $res = $this->Hx_count_model->get_count_list($where);
            if ($res) {
                foreach ($res as $k => $v) {
                    $where = array('coupon_info_id' => $v['count_type'], 'is_del' => 0);
                    $type_name = $this->Hx_coupon_info_model->get_coupon_info_detail($where);
                    if ($type_name) {
                        $res[$k]['type_name'] = $type_name['coupon_name'];
                    }
                }
            }
       

        if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }

    // 计算任意一天领取优惠券的总数量
     public function total_sum($coupon_time=''){
        if($coupon_time){
         $sum=   $this->User_model->sum('receive_sum','use_sum',$coupon_time);

          $res['receive_sum']=$sum['sum1']?$sum['sum1']:0;
          $res['use_sum']= $sum['sum2']?$sum['sum2']:0;   
             if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
        }else{
            echo "时间参数不能为空";die;
        } 
     }
     public  function seven_sum(){
         for($i=6;$i>=0;$i--){
             $coupon_time = date('Ymd',time()-86400*$i);   
             
                $sum=  $this->User_model->sum('receive_sum','use_sum',$coupon_time);
          $res['receive_sum'][] = intval($sum['sum1']?$sum['sum1']:0);
          $res['use_sum'][] = intval($sum['sum2']?$sum['sum2']:0);   
           
         }
        if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res ;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     } 
     }
   // 查询渠道发券数量
   public  function channel_sum(){
       $business_id=$_SESSION['business_id'];
       if(empty($business_id)){
           $business_id=67388;
       }
       $channel=$this->Hx_channel_model->get_channel_list(array('business_id'=>$business_id,'is_del'=>0));
      foreach($channel as $k=>$v){
        $channel_tag1=$this->User_model->table_join('mc',$business_id,$v['channel_tag'],0);
        $channel_tag2=$this->User_model->table_join('my',$business_id,$v['channel_tag'],1);
        $data[]=array(
              'tag'=>$v['channel_tag'],
              'mc'=>$channel_tag1['mc'],
              'sy'=>$channel_tag2['my'],
              'name'=>$v['channel_name']  
            );
          

      }
      if($data){
             $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $data ;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
      }

   
   }
   // 用户信息存储
    private function _user_business($list) {
        $user = $this->session->set_userdata($list);
        return $user;
    }
        // 退出登录
    public function login_out() {
//       $this->session->unset_userdata($array_items);//销毁某一
        $res = $this->session->sess_destroy();
         $result['errcode'] = 200;
		 $result['data'] = date('Y-m-d H:i:s',time());
         $result['msg'] = "退出成功";
         return $this->ajax_return($result); 
        
    }
   // 用户信息
   public function user_info(){
       $data['user_name']=$_SESSION['user_name'];
       $data['business_id']=$_SESSION['business_id'];
       $business=$this->Business_info_model->get_business_info_detail($where=array('business_id'=> $data['business_id'],'is_del'=>0));
       $data['business_name']=$business['business_name'];   
                  if($data){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $data;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
     
       
   }
// 上航假期可见状态修改
    public function sh($business_id = '') {
        if (empty($business_id)) {
            $data['business_id'] = 67400;
        } else {
            $data['business_id'] = $business_id;
        }

        $data['is_eye'] = 1;
        $res = $this->Sh_save->save_info($data);
        if ($res) {
            echo "状态修改成功";
        }
    }
 


 //优惠券详情
 public function  security_info(){
     $id=$this->input->get('id',true);
	 if(!$id){
		  $result['errcode'] = 200;
           $result['msg'] = "新增";
            $result['data'] = 1;
            return $this->ajax_return($result); 
	 }
     $where=array('id'=>$id,'is_del'=>0);
     $res=$this->Hx_security_info_model->get_security_info_detail($where);
       if($res){
           $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result); 
     }else{
         $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result); 
     }
 }

 // 增加或者修改防伪码
  public function  save_security(){
       $data['business_id']=$_SESSION['business_id']; 
       if(empty($data['business_id'])){
           $data['business_id']=1;
       }
      $data['id']=$this->input->post('id',TRUE);
      $data['coupon_name']=$this->input->post('coupon_name',true); 
      $data['company_name']=$this->input->post('company_name',true); 
      $data['product_name']=$this->input->post('product_name',true); 
      $data['details']=$this->input->post('details',true); 
      $data['add_time']=  time(); 
      if(empty($data['id'])){
          $res=$this->Hx_security_info_model->save_security_info($data);
          if($res){
             $result['errcode'] = 200;
            $result['msg'] = "增加成功";
            $result['data'] = $res;
            return $this->ajax_return($result);    
          }else{
             $result['errcode'] = 400;
            $result['msg'] = "增加失败";
            return $this->ajax_return($result);     
          }
      }else{
          $res=$this->Hx_security_info_model->save_security_info($data);
             if($res){
             $result['errcode'] = 200;
            $result['msg'] = "编辑成功";
            $result['data'] = $res;
            return $this->ajax_return($result);    
          }else{
             $result['errcode'] = 400;
            $result['msg'] = "编辑失败";
            return $this->ajax_return($result);     
          }
      }
        
    }
    // 防伪码种类列表
    public  function security_list(){
       $data['business_id']=$_SESSION['business_id']; 
       if(empty($data['business_id'])){
           $data['business_id']=1;
       } 
      $res=$this->Hx_security_info_model->get_security_info_list(array('business_id'=>$data['business_id'],'is_del'=>0));
      foreach($res as $k=>$v){
          $res[$k]['add_time']=date('Y-m-d，H:i:s',$v['add_time']);
      }
     if($res){
             $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);    
          }else{
             $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);     
          } 
    }
    // 防伪码列表
    public function security_code_list() {
        $data['business_id'] = $_SESSION['business_id'];
        $security_info_id = $this->input->get('id', TRUE);
        $status=$this->input->get('status',TRUE);
        $code=$this->input->get('code',TRUE);
        if(empty($coupon_info_id)){
           $where = array( 'is_del' => 0);  
        }else{
           $where = array('security_id' => $security_info_id, 'is_del' => 0);  
        }
        if($status){
            $where['status']=$status;
        }
        if($code){
           $where['security_code']=$code; 
        }
        $res = $this->Hx_security_code_model->get_security_code_list($where);
        foreach ($res as $k => $v) {
            $security_name = $this->Hx_security_info_model->get_security_info_detail(array('id' => $v['security_info_id'], 'is_del' => 0));
            $res[$k]['coupon_name'] = $security_name['coupon_name'];
            $res[$k]['company_name'] = $security_name['company_name'];
            $res[$k]['product_name'] = $security_name['product_name'];
            $res[$k]['details'] = $security_name['details'];
            $res[$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $res[$k]['check_time'] = $v['check_time']?date('Y-m-d H:i:s', $v['check_time']):'';
        }
        if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }
    // 生成防伪码
    public  function create_code(){
          $data['business_id'] = $_SESSION['business_id'];
		
        $data['security_info_id']=$this->input->get('id',true);
        $i=$this->input->get('i',true);
        if(empty($i)){
            $i=1;
        }
        for($u=0;$u<$i;$u++){
        $data['security_code']=  rand(1000,9999).rand(1000,9999).rand(1000,9999);
        $data['add_time']=  time();
        $res=$this->Hx_security_code_model->save_security_code_info($data);
        }
              if ($res) {
            $result['errcode'] = 200;
            $result['msg'] = "成功";
            $result['data'] = $res;
            return $this->ajax_return($result);
        } else {
            $result['errcode'] = 400;
            $result['msg'] = "失败";
            return $this->ajax_return($result);
        }
    }
    // 查询防伪码真假
    public function security_verify(){   
        $code=$this->input->post('code',true);
       $res = $this->Hx_security_code_model->get_security_code_detail(array('security_code' => $code, 'is_del' => 0));    
        if($res){
			
            if($res['security_status']==0){
            $data['id']=$res['id'];
            $data['check_time']=  time();   
            $data['security_status']=1;
            $this->Hx_security_code_model->save_security_code_info($data);
            }
          
            $security_name = $this->Hx_security_info_model->get_security_info_detail(array('id' => $res['security_info_id'], 'is_del' => 0));
            $res['coupon_name'] = $security_name['coupon_name'];
            $res['company_name'] = $security_name['company_name'];
            $res['product_name'] = $security_name['product_name'];
            $res['details'] = $security_name['details'];
            $res['add_time'] = date('Y-m-d H:i:s', $security_name['add_time']);
            $res['check_time'] = $security_name['check_time']?date('Y-m-d H:i:s', $security_name['check_time']):date('Y-m-d H:i:s', time()); 
			
            $result['errcode'] = 200;
            $result['msg'] = ($res['security_status']==0)?"正品，请放心使用":"防伪码 已被查询";
            $result['data'] = $res;
            return $this->ajax_return($result);
            
        }else{
             $result['errcode'] = 400;
            $result['msg'] = "没有查询到该产品";
            return $this->ajax_return($result); 
        }
    }

}
