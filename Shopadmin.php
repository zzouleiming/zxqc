<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/3/30
 * Time: 14:00
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Shopadmin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('common');
        $this->load->library('image_lib');

        $this->load->model('Shop_model');
        $this->load->model('wx/Wx_order_model');
        $this->load->model('wx/Wx_goods_model');
        $this->load->model('wx/Wx_order_goods_model');
        $this->load->model('Goodsforcar_model');

        $this->load->model('User_model');

        $this->load->helper('url');
        $this->load->helper('common');
    }

    //商户登录界面
    public function login()
    {
        $data['act_login'] = base_url('shopadmin/do_login');
        $data['index'] = base_url('shopadmin');
        $business_id = $this->session->userdata('business_id');
        if($business_id){
            redirect(base_url('shopadmin'));
        }else{
            $this->load->view('shopadmin/login', $data);
        }
    }

    //登录验证
    public function do_login()
    {
        $result = array();
        $code = $this->session->userdata('code');
        $this->session->unset_userdata('code');
        
        $captcha = strtoupper($this->input->post('code', true));
        $account = $this->input->post('user', true);
        $password = $this->input->post('pwd', true);
        
        if(empty($captcha) or empty($account) or empty($password)){
            //非法登录
            $result['code'] = 2;
            $result['msg'] = "非法登录";
            echo json_encode($result);
            exit;
        }
        
        if(!$code or $captcha!=$code){
            //验证码错误或者过期
            $result['code'] = 1;
            $result['msg'] = "验证码错误或者过期";
            echo json_encode($result);
            exit;
        }
        
        $where = array(
            'business_account '=> $account
        );
        $shop_info = $this->Shop_model->get_shop_detail($where);
        if(!$shop_info || $shop_info['password'] != md5($password.$shop_info['salt'])){
            $result['code'] = 3;
            $result['msg'] = "用户不存在或者密码错误";
            echo json_encode($result);
            exit;
        }
        
        $this->session->set_userdata('business_id', $shop_info['business_id']);
        $shop_data = array(
            'business_id' => $shop_info['business_id'],
            'login_time' => time()
        );
        $this->Shop_model->save_shop($shop_data);
        $result['code'] = 5;
        $result['msg'] = "登录成功";
        echo json_encode($result);
        exit;
    }

    //退出登录
    public function login_out()
    {
        $this->session->unset_userdata('business_id');
        redirect(base_url('shopadmin/login'));
    }

    //验证码
    public function get_cpa()
    {
        $this->load->library('captcha');
        $code = $this->captcha->getCaptcha();
        $this->session->set_userdata('code', strtoupper($code));
        $this->captcha->showImg();
    }

    //结构页 frame
    public function index()
    {
        $this->get_auth();
        $data['main']=base_url('shopadmin/to_do');
        $data['top']=base_url('shopadmin/top');
        $this->load->view('shopadmin/index',$data);
    }

    //top
    public function top()
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $business_id = $this->session->userdata('business_id');
        
        $where = array(
            'business_id' => $business_id
        );
        $data['shop_info'] = $this->Shop_model->get_shop_detail($where);
        $this->load->view('shopadmin/top',$data);
    }

    //修改密码
    public function pwd_edit()
    {
        $this->get_auth();
        $business_id = $this->session->userdata('business_id');
        $where = array(
            'business_id' => $business_id
        );
        $data['shop_info'] = $this->Shop_model->get_shop_detail($where);
        $data['url'] = base_url('shopadmin/do_pwd_edit');
        
        $this->load->view('shopadmin/pwd_edit', $data);
    }

    //密码修改
    public function do_pwd_edit()
    {
        $this->get_auth();
        
        $password_old = $this->input->post('password_old', TRUE);
        $password_new = $this->input->post('password_new', TRUE);
        $business_id = $this->session->userdata('business_id');
        $where = array(
            'business_id' => $business_id
        );
        $shop_info = $this->Shop_model->get_shop_detail($where);
        if(md5($password_old.$shop_info['salt']) == $shop_info['password']){
            $salt = rand(1,9999);
            $password = md5($password_new.$salt);
            $shop_data = array(
                'business_id' => $business_id,
                'password' => $password,
                'salt' => $salt
            );
            $this->Shop_model->save_shop($shop_data);
            $this->session->unset_userdata('business_id');
        }
        redirect(base_url('shopadmin/login'));
    }

    //待发货
    public function to_do()
    {
        $this->get_auth();
        $data=$this->get_left_url();

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }

        $date = $this->input->get('date',TRUE);
        $trip_id = $this->input->get('trip_id',TRUE);
        $business_id = $this->session->userdata('business_id');

        $where = array(
            'b.business_id' => $business_id,
            'b.is_show' => 1,
            'b.todo' => 0,
            'limit' => 20,
            'offset' => 0,
            'group_by' => 'b.trip_id, a.goods_id, d.url',
            'order_by' => 'b.add_time desc',
            'where_in' => array('b.order_status' => array(1,2,3))
        );

        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
        
        if(!empty($data['param']['per_total'])){
            $where['limit'] = $data['param']['per_total'];
        }

        if(!empty($data['param']['date'])) {
            $date_start = strtotime($data['param']['date']);
            $date_end = $date_start + 84600;
            $where['b.add_time >= '] = $date_start;
            $where['b.add_time < '] = $date_end;
        }
        if(!empty($data['param']['trip_id'])){
            $where['like'] = array('b.trip_id' => $data['param']['trip_id']);
        }

        $page_num =1000;
        $order_info = $this->Wx_order_goods_model->get_trip_goods_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        
        $new_order_info = array();
        foreach($order_info as $k=>$v)
        {
            $new_order_info[$v['trip_id']]['trip_id']=$v['trip_id'];
            $new_order_info[$v['trip_id']]['date']=date('Y-m-d',$v['add_time']);
            $trip_info = explode('@', $v['trip_id']);
            $new_order_info[$v['trip_id']]['trip_name']=$trip_info[0];

            if(isset($new_order_info[$v['trip_id']]['all_amount']))
            {
                $new_order_info[$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $new_order_info[$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

            $new_order_info[$v['trip_id']]['show_detail']=base_url("shopadmin/show_detail?trip_id=$v[trip_id]");
            $new_order_info[$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($new_order_info);
        $data['do_did_url'] = base_url('shopadmin/did_url');
        $this->load->view('shopadmin/to_do',$data);
    }

    //发货
    public function did_url()
    {
        $this->get_auth();
        $business_id = $this->session->userdata('business_id');
        if(!$business_id){
            return FALSE;
        }
        $trip_id = $this->input->post('trip_id',TRUE);

        return $this->Goodsforcar_model->update_one(array('trip_id'=>$trip_id,'business_id'=>$_SESSION['business_id']),array('todo'=>'1'),'wx_order_info');
    }

    //已发货
    public function did()
    {
        $this->get_auth();
        $data=$this->get_left_url();

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }

        $date = $this->input->get('date',TRUE);
        $trip_id = $this->input->get('trip_id',TRUE);
        $business_id = $this->session->userdata('business_id');

        $where = array(
            'b.business_id' => $business_id,
            'b.is_show' => 1,
            'b.todo' => 1,
            'limit' => 20,
            'offset' => 0,
            'group_by' => 'b.trip_id, a.goods_id, d.url',
            'order_by' => 'b.add_time desc',
            'where_in' => array('b.order_status' => array(1,2,3))
        );

        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
        
        if(!empty($data['param']['per_total'])){
            $where['limit'] = $data['param']['per_total'];
        }

        if(!empty($data['param']['date'])) {
            $date_start = strtotime($data['param']['date']);
            $date_end = $date_start + 84600;
            $where['b.add_time >= '] = $date_start;
            $where['b.add_time < '] = $date_end;
        }
        if(!empty($data['param']['trip_id'])){
            $where['like'] = array('b.trip_id' => $data['param']['trip_id']);
        }

        $page_num =1000;
        $order_info = $this->Wx_order_goods_model->get_trip_goods_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        
        $new_order_info = array();
        foreach($order_info as $k=>$v)
        {
            $new_order_info[$v['trip_id']]['trip_id']=$v['trip_id'];
            $new_order_info[$v['trip_id']]['date']=date('Y-m-d',$v['add_time']);
            $trip_info = explode('@', $v['trip_id']);
            $new_order_info[$v['trip_id']]['trip_name']=$trip_info[0];

            if(isset($new_order_info[$v['trip_id']]['all_amount']))
            {
                $new_order_info[$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $new_order_info[$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

            $new_order_info[$v['trip_id']]['guide_detail']=base_url("shopadmin/guide_detail?trip_id=$v[trip_id]");
            $new_order_info[$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($new_order_info);
        $this->load->view('shopadmin/did',$data);
    }

    //查看团明细
    public function show_detail()
    {
        $this->get_auth();
        $data=$this->get_left_url();

        $trip_id = $this->input->get('trip_id',TRUE);

        $where = array(
            'b.business_id' => $this->session->userdata('business_id'),
            'b.is_show' => 1,
            'b.trip_id' => $trip_id,
            'b.todo' => 0,
            'group_by' => 'b.trip_id, a.goods_id, d.url',
            'order_by' => 'b.add_time desc',
            'where_in' => array('b.order_status' => array(1,2,3))
        );
        $order_info = $this->Wx_order_goods_model->get_trip_goods_list($where);
        $data['new_order_info'] = array();
        foreach($order_info as $k => $v){
            $data['new_order_info'][$v['trip_id']]['trip_id'] = $v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['date'] = date('Y-m-d',$v['add_time']);
            $trip_info = explode('@', $v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['trip_name'] = $trip_info[0];
            $data['new_order_info'][$v['trip_id']]['val'][] = $v;
        }
        $data['do_did_url'] = base_url('shopadmin/did_url');
        $this->load->view('shopadmin/show',$data);
    }

    //导游团明细
    public function guide_detail()
    {
        $this->get_auth();
        $data=$this->get_left_url();

        $trip_id = $this->input->get('trip_id',TRUE);
        $order_status = $this->input->get('order_status',TRUE);
        if(!$order_status){
            $order_status = 1;
        }
        $where = array(
            'is_show' => 1,
            'todo' => 1,
            'business_id' => $this->session->userdata('business_id'),
            'order_status' => $order_status,
            'trip_id' => $trip_id
        );

        $order_info = $this->Wx_order_model->get_order_info_list($where);
        $data['new_order_info'] = array();
        foreach($order_info as $k => $v){
            $data['new_order_info'][$v['trip_id']]['trip_id'] = $v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['date'] = date('Y-m-d', $v['add_time']);
            $trip_info = explode('@', $v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['trip_name'] = $trip_info[0];

            if($v['order_status']==0 ){
                $v['order_state']='未付款';
            }elseif($v['order_status']==1){
                $v['order_state']='已付款';
            }elseif($v['order_status']==2){
                $v['order_state']='现金交易';
            }elseif($v['order_status']==3){
                $v['order_state']='交易完成';
            }else{
                $v['order_state']='交易关闭';
            }

            $v['add_date'] = date('Y-m-d',$v['add_time']);
            $order_goods = $this->Wx_order_goods_model->get_order_goods_list(array('order_id' => $v['order_id']));
            foreach($order_goods as $k1 => $v1){
                $order_goods[$k1] = array_merge($v1, $this->Goodsforcar_model->get_car_goods_image($v1['goods_id']));
            }
            $v['goods'] = $order_goods;
            $data['new_order_info'][$v['trip_id']]['val'][] = $v;
        }
        
        $data['order_status'] = $order_status;
        $data['cash_url']=base_url("shopadmin/guide_detail?trip_id=$trip_id&order_status=2");
        $data['wx_url']=base_url("shopadmin/guide_detail?trip_id=$trip_id&order_status=1");
        $data['succ_url']=base_url("shopadmin/guide_detail?trip_id=$trip_id&order_status=3");
        $this->load->view('shopadmin/guide_order',$data);
    }


    //特价上架
    public function up_activity()
    {   
        $goods_id = $this->input->get('goods_id',TRUE);
        $data = array(
            'goods_id' => $goods_id,
            'is_show' => 1
        );
        $this->Wx_goods_model->save_goods($data);
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    //特价下架
    public function down_activity()
    {   
        $goods_id = $this->input->get('goods_id',TRUE);
        $data = array(
            'goods_id' => $goods_id,
            'is_show' => 2
        );
        $this->Wx_goods_model->save_goods($data);
        redirect($_SERVER['HTTP_REFERER']);
    }

    //特价删除
    public function del_activity()
    {   
        $goods_id = $this->input->get('goods_id',TRUE);
        $data = array(
            'goods_id' => $goods_id,
            'is_show' => 3
        );
        $this->Wx_goods_model->save_goods($data);
        redirect($_SERVER['HTTP_REFERER']);
    }

    //main 页面
    public function car_goods_add(){
        
          //$this->auth_or_no();

        $goods_id=$this->input->get('goods_id',TRUE);
        $data=[];
        $data=$this->get_left_url();
        $this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));
        $trip_id=$this->input->get('trip_id',TRUE);
     

        $where="is_show=1 AND business_id= $_SESSION[business_id] AND todo=0 AND order_status IN (1,2,3)";


        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%' ";
        }

        $data['date']=$date;
        $data['trip_id']=$trip_id;
      
        $data['did_url']=base_url('shopadmin/did_url');
        
        // echo '<pre>';print_r($data);exit();
        $data['sub_url']="/shopadmin/car_goods_insert";
        if($goods_id)
        {
            $data['sub_url']="/newadmin/car_goods_sub";
            $data['info']=$this->User_model->get_select_one('*',array('goods_id'=>$goods_id),'wx_new_goods');
            $data['info']['image_r']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'7','isdelete'=>0),'v_images');
            $data['info']['image_r']=isset($data['info']['image_r']['url'])?$data['info']['image_r']['url']:$data['info']['image_r']['url'];

            $data['info']['image_s']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'8','isdelete'=>0),'v_images');
            $data['info']['image_s']=isset($data['info']['image_s']['url'])?$data['info']['image_s']['url']:$data['info']['image_s']['url'];

            $data['info']['image_c']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'9','isdelete'=>0),'v_images');
            $data['info']['image_c']=isset($data['info']['image_c']['url'])?$data['info']['image_c']['url']:'';
        }
        $this->load->view('shopadmin/car_goods_add',$data);
        
        
    }

    public function car_goods_list($page=1)
    {
        $this->get_auth();
        $data=$this->get_left_url();
        $where='1=1';
        $is_show=$this->input->get('is_show');
        if($is_show)
        {
            $where.=" AND is_show=$is_show";
        }else{
            $where.=" AND  is_show =1";
        }
        $where.=" AND business_id=".$_SESSION['business_id'];
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $is_off= $data['is_off']= $this->input->get('is_off',true);
   
  /**      if(!$is_off){
            $is_off=0;
        }
        $wheres=' 1=1 ';


        if($data['time1']){
            $wheres.=' AND add_time >=$data["time1"]';
        }
        if($data['time2']){
            $data['time2']+=86400;
            $wheres.='  AND add_time <=$data["time2"]';
        }

        if($data['title'])
        {
           
        }else{
            $data['type']=0;
        }
        if($is_off==0){
            $wheres.="  AND is_off= $is_off";
        }elseif($is_off==1){
            $wheres.="  AND is_off > 0";
        }

**/
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'wx_new_goods');
        $data['max_page'] = ceil($count['count']/$page_num);
      // print_r($data['max_page']);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

     /**   $data['info']=$this->User_model->get_select_all($select='*',$where,$order_title='displayorder',$order='ASC',$table='wx_new_goods');
    
      
        //  print_r($data['info']);
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['info'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['info'][$k]["edit_url"]="/newadmin/car_goods_add?goods_id=$v[goods_id]";
        }
        $data['order_url']=base_url('newadmin/car_order');
       // print_r($data);
 
 **/
  //  $data['list'] = $this->db->query("SELECT * FROM wx_new_goods ;");

        $data['list']=$this->User_model->get_goods_lists($where,'wx_new_goods',$order_title='displayorder',$order='ASC',$start,$page_num);
         
          foreach($data['list'] as $k=>$v)
        {
            $data['list'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['list'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['list'][$k]["edit_url"]="/shopadmin/car_goods_add?goods_id=$v[goods_id]";
        }
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['is_show']=$is_show ? $is_show : 1;
     
     //   echo "<pre>";
     // print_r($data);
     //   echo "</pre>";
     $this->load->view('shopadmin/car_goods_list',$data);
    

    }
    


    //增加提交
    public function car_goods_insert()
    {
        $this->get_auth();
        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['ori_price']=$this->input->post('ori_price',TRUE);
        $data['is_show']=$this->input->post('is_show',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $data['addtime']=time();
        $data['business_id']=$_SESSION['business_id'];
       
        $goods_id=$this->User_model->user_insert('wx_new_goods',$data);
        if($_FILES['image_r']['error']==0 && $_FILES['image_s']['error']==0)
        {
            $image_r=$this->upload_image('image_r', 'car');
            $image_s=$this->upload_image('image_s', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'7','url'=>$image_r,'createdAt'=>time()));
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'8','url'=>$image_s,'createdAt'=>time()));

        }
        if($_FILES['image_c']['error']==0 )
        {
            $image_c=$this->upload_image('image_c', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'9','url'=>$image_c,'createdAt'=>time()));
        }
        redirect("/shopadmin/car_goods_add");
      
       



    }
    
    public function upload_image($filename,$fileurl,$key='time',$lt=0)
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl,0777))
            {
                return false;
            }
        }

        return $this->shangchuan($filename,$fileurl,$key,$lt);
    }
    public function shangchuan($filename,$fileurl,$key='time',$lt=0)
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
                $br = false;break;
        }
        if($br)
        {
            if($key=='time'){
                $key =md5(time().rand(1,999999));
            }

            $pic_url="./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
         //   return substr($pic_url,1);

            return ltrim($pic_url,'.');
        }
    }

    //全部订单
    public function all($page=1)
    {
        $data=$this->get_left_url();
        //$this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));
        $trip_id=$this->input->get('trip_id',TRUE);

        $where="is_show=1 AND  order_status IN (1,2,3)";


        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%' ";
        }

        $data['date']=$date;
        $data['trip_id']=$trip_id;
        $page=intval($page);
        if(!$page)
        {
            $page=1;
        }
        $page_num =1000;
        $data['now_page'] = $page;

        $data['search_url']=base_url('shopadmin/did');

        $count = $this->Goodsforcar_model->get_order_goods_all_count($where,'wx_order_info');

        $data['count']=$count;
        $data['max_page'] = ceil($count/$page_num);

        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;

        $data['order_info']=$this->Goodsforcar_model->get_order_goods_all($where,$page_num,$start);
        $data['new_order_info']=[];
        foreach($data['order_info'] as $k=>$v)
        {
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_name'];

            if(isset($data['new_order_info'][$v['trip_id']]['all_amount']))
            {
                $data['new_order_info'][$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $data['new_order_info'][$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

            // $temp_trip=$this->common->url_encode($v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['show_detail']=base_url("shopadmin/guide_detail?trip_id=$v[trip_id]");

            $data['new_order_info'][$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($data['new_order_info']);



        unset($data['order_info']);
        //echo '<pre>';print_r($data['new_order_info']);exit();
        //print_r($data['order_info']);exit();
        $data['page']=$page;
        $this->load->view('shopadmin/order',$data);
    }

    //main 页面
    public function account()
    {
        $this->get_auth();
        $data=$this->get_left_url();

        $date = $this->input->get('date',TRUE);
        $trip_id = $this->input->get('trip_id',TRUE);

        $data['new_order_info']=[];
      //  $data['order_info']=$this->Goodsforcar_model->account_list(0,$_SESSION['business_id'],0,0,1);
        $data['order_info']=$this->Goodsforcar_model->get_account_infO($_SESSION['business_id'],$trip_id,$date);
        foreach($data['order_info'] as $k=>$v){
            //  $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            //$data['new_order_info'][$v['trip_id']]['cash_money']=0;
            //$data['new_order_info'][$v['trip_id']]['wx_money']=0;
            if(!isset($data['new_order_info'][$v['trip_id']]['cash_money']))
            {
                $data['new_order_info'][$v['trip_id']]['cash_money']=0;
            }
            if(!isset($data['new_order_info'][$v['trip_id']]['wx_money']))
            {
                $data['new_order_info'][$v['trip_id']]['wx_money']=0;
            }
            if($v['pay_id']==4)
            {
                $data['new_order_info'][$v['trip_id']]['cash_money']=$v['sum_money'];
            }else{
                $data['new_order_info'][$v['trip_id']]['wx_money']=$v['sum_money'];
            }
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['date']=date('Y-m-d', $v['add_time']);
            $trip_info = explode('@', $v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['trip_name']=$trip_info[0];
            $data['new_order_info'][$v['trip_id']]['user_id_sell']=$v['user_id_sell'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$this->Goodsforcar_model->get_user_name($v['user_id_sell']);
        }
        $data['new_order_info']=array_values($data['new_order_info']);
        unset($data['order_info']);
        // echo '<pre>';print_r($data['new_order_info']);
         //echo '<pre>';print_r($data['order_info']); exit();

        $this->load->view('shopadmin/account',$data);
    }

    //分页数据
    private function _pagination($query, $per_page, $url_data) {
        $this->load->library('pagination');
        if (!is_numeric($query)) {
            $query = strtolower($query);
            $query = preg_replace("/(?<=^select)[\s\S]*?(?=from)/", " 1 ", $query);
            $query = preg_replace("/order by [\s\S]*/", "", $query);
            $query = preg_replace("/limit [\s\S]*/", "", $query);

            $query = $this->db->query("select count(1) total from ($query) tab");
            $row = $query->row_array();
        } else {
            $row['total'] = $query;
        }

        unset($url_data['per_page']);
        $base_url = site_url($this->uri->uri_string() . '?' . http_build_query($url_data));

        $this->pagination->admin_page(array('base_url' => $base_url, 'per_page' => $per_page, 'total_rows' => $row['total']));

        $link = $this->pagination->create_links();

        if (empty($link)) {
            $link = $this->pagination->total_tag;
        }

        return array('total' => $row['total'], 'link' => $link);
    }

    //无session 定向登录
    public function get_auth()
    {
        if(!$this->session->userdata('business_id')){
            redirect(base_url('shopadmin/login'));
        }
    }

    //简单获取侧边链接
    public function get_left_url()
    {
        return array(
            'to_do'=>base_url('shopadmin/to_do'),
            'did'=>base_url('shopadmin/did'),
            'account'=>base_url('shopadmin/account'),
            'car_goods_add'=>base_url('shopadmin/car_goods_add'),
            'car_goods_list'=>base_url('shopadmin/car_goods_list'),
            'pwd_edit'=>base_url('shopadmin/pwd_edit'),
            'login_out'=>base_url('shopadmin/login_out'),
        );
    }
}