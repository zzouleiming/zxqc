<?php
/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/8/1
 * Time: 14:00
 */
defined('BASEPATH') OR exit('No direct script access allowed');
define('SESSION_KEY_PRE', 'tp');
class Admin extends CI_Controller
{

    public function __construct()
    {   
        parent::__construct();

        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');

        $this->load->model('Page_info_model');
        $this->load->model('Car_info_model');
        $this->load->model('Car_type_model');
        $this->load->model('Hotel_info_model');
        $this->load->model('Line_info_model');
        $this->load->model('Problem_info_model');
        $this->load->model('Order_info_model');
        $this->load->model('Order_user_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');

        $this->load->model('User_model');

        $this->load->helper('url');
        $this->load->helper('common');
      
    }

    //商户登录界面
    public function login()
    {
        $data['act_login'] = base_url('tpin/admin/do_login');
        $data['index'] = base_url('tpin/admin');
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if($business_account_id){
            redirect('/tpin/admin');
        }else{
            $this->load->view('tpin/admin/login', $data);
        }
    }
    
    //登录验证
    public function do_login()
    {
        $result = array();
        $code = $this->my_session->get_session('code', SESSION_KEY_PRE);
        $this->my_session->del_session('code', SESSION_KEY_PRE);
        
        $captcha = strtoupper($this->input->post('code', true));
        $account = $this->input->post('user', true);
        $password = $this->input->post('pwd', true);
//        echo "<pre>";
//        echo "$account";
//        echo $password;
//        echo "</pre>";die;
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
        $business_account = $this->Business_account_model->get_business_account_detail($where);
 
        if(!$business_account || $business_account['password'] != md5($password.$business_account['salt'])){
            $result['code'] = 3;
            $result['msg'] = "用户不存在或者密码错误";
            echo json_encode($result);
            exit;
        }
        
        $this->my_session->set_session('business_account_id', $business_account['business_account_id'], SESSION_KEY_PRE);
        $business_account_data = array(
            'business_account_id' => $business_account['business_account_id'],
            'login_time' => time()
        );
        $this->Business_account_model->save_business_account($business_account_data);
        $result['code'] = 5;
        $result['msg'] = "登录成功";
        echo json_encode($result);
        exit;
    }
 
    //退出登录
    public function login_out()
    {
        $this->my_session->del_session('business_account_id', SESSION_KEY_PRE);
        redirect(base_url('tpin/admin/login'));
    }
    
    //验证码
    public function get_cpa()
    {
        $this->load->library('captcha');
        $code = $this->captcha->getCaptcha();
        $this->my_session->set_session('code', strtoupper($code), SESSION_KEY_PRE);
        $this->captcha->showImg();
    }

    //结构页 frame
    public function index()
    {
        $business_account = $this->get_auth();
        $data['main'] = base_url('tpin/admin/main');
        $data['top'] = base_url('tpin/admin/top');
        $this->load->view('tpin/admin/index',$data);

    }
    
    //页面头部
    public function top()
    {
        $data = $this->get_left_url();
        $data['business_account'] = $this->get_auth();

        $this->load->view('tpin/admin/top', $data);
    }

    //修改密码
    public function pwd_edit()
    {
        $data = $this->get_left_url();
        $data['business_account'] = $this->get_auth();
        $data['url'] = base_url('tpin/admin/do_pwd_edit');
        
        $this->load->view('tpin/admin/pwd_edit', $data);
    }
    
    //密码修改
    public function do_pwd_edit()
    {
        $business_account = $this->get_auth();
        
        $password_old = $this->input->post('password_old', TRUE);
        $password_new = $this->input->post('password_new', TRUE);

        if(md5($password_old.$business_account['salt']) == $business_account['password']){
            $salt = rand(1,9999);
            $password = md5($password_new.$salt);
            $business_account_data = array(
                'business_account_id' => $business_account['business_account_id'],
                'password' => $password,
                'salt' => $salt
            );
            $this->Business_account_model->save_business_account($business_account_data);
            $this->my_session->del_session('business_account_id', SESSION_KEY_PRE);            
        }
        redirect(base_url('tpin/admin/login'));
    }  

    //首页
    public function main()
    {
        $business_account = $this->get_auth();
        $data = $this->get_left_url();

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        
        $business_id = $business_account['business_id'];
        $where = array('business_id' => $business_id, 'is_show' => 0, 'limit' => 20, 'offset' => 0,'order_by' => 'page_id desc');
        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
        
        if(!empty($data['param']['per_total'])){
            $where['limit'] = $data['param']['per_total'];
        }

        $page_info_list = $this->Page_info_model->get_page_info_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        foreach($page_info_list as $key => $val){
            $page_info_list[$key]['page_prev_url'] = base_url('tpin/home').'/'.$val['page_id'].'?'.$val['share_url'];
            $page_info_list[$key]['page_edit_url'] = base_url('tpin/admin/page_edit').'/'.$val['page_id'];
            $page_info_list[$key]['page_goods_url'] = base_url('tpin/admin/page_goods').'/'.$val['page_id'];
            $page_info_list[$key]['page_del_url'] = base_url('tpin/admin/page_del').'/'.$val['page_id'];
        }
        $data['page_info_list'] = $page_info_list;
        $data['page_add_url'] = base_url('tpin/admin/page_add'); 
        $data['role_id']=$business_account['role_id'];  
        if($data['role_id']==1){
        $this->load->view('tpin/admin/main',$data);
        }elseif ($data['role_id']==2) {
            $this->order();
        }
        
    }

    //页面增加
    public function page_add()
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['do_page_add_url'] = base_url('tpin/admin/do_page_add');
        $this->load->view('tpin/admin/page_add',$data);
    }

    //二销主模板信息上传
    public  function do_page_add()
    {
        $business_account = $this->get_auth();
        $data = $this->get_left_url();
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['share_url'] = $this->input->post('share_url', TRUE);
        $data['business_id'] = $business_account['business_id'];
        $data['add_time'] = time();

        $image1 = $this->upload_image('image1','H5image');
        $image3 = $this->upload_image('image3','H5image');
        $img = array(
            'top'=>$image1,
            'share'=>$image3
        );
        $data['image_data']=  json_encode($img);

        $res = $this->Page_info_model->save_page_info($data);
        if($res){    
            redirect(base_url("tpin/admin/main"));
        }
        
    }

    //二销主页编辑页面
    public function page_edit($page_id)
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $where = array(
            'page_id' => $page_id
        );
        $data['page_info'] = $this->Page_info_model->get_page_info_detail($where);
        $data['do_page_edit_url'] = base_url('tpin/admin/do_page_edit');
        $data['page_info']['img'] = json_decode($data['page_info']['image_data']);
        $this->load->view('tpin/admin/page_edit', $data); 
    }


    //二销主页面编辑
    public  function do_page_edit()
    {
        $this->get_auth();
        $data = $this->get_left_url();
        
        $page_data['page_id'] = $this->input->post('page_id',TRUE);
        $page_data['uploader'] = $this->input->post('uploader',TRUE);
        $page_data['page_title'] = $this->input->post('h5_title',TRUE);
        $page_data['share_desc'] = $this->input->post('share_desc',TRUE);
        $page_data['share_url'] = $this->input->post('url_type',TRUE);
        $page_data['update_time'] = time();

        $where = array('page_id' => $page_data['page_id']);
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        if(!$page_info){
            echo "页面信息未找到";
        }

        $img = array();
        if(!empty($page_info['image_data'])){
            $img = json_decode($page_info['image_data'], TRUE);
        }
        if(isset($_FILES['image1']) && $_FILES['image1']['error']==0){
            $img['top'] = $this->upload_image('image1','H5image');
        }
        if(isset($_FILES['image3']) && $_FILES['image3']['error']==0){
            $img['share'] = $this->upload_image('image3','H5image');
        }
        $page_data['image_data'] = json_encode($img);
        $this->Page_info_model->save_page_info($page_data);
        redirect(base_url("tpin/admin/main")); 
    }

    //二销页面删除 
    public function page_del($page_id)
    {
        $this->get_auth();
        $page_data['page_id'] = $page_id;
        $page_data['is_show'] = 1;
        $this->Page_info_model->save_page_info($page_data);  
        redirect(base_url("tpin/admin/main"));
    }

    //二销商品管理
    public  function page_goods($page_id)
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );

        $data['page_car_url'] = base_url('tpin/admin/page_goods').'/'.$page_id;
        $data['page_line_url'] = base_url('tpin/admin/page_line').'/'.$page_id;
        $data['page_rest_url'] = base_url('tpin/admin/page_rest').'/'.$page_id;
        $data['page_kf_url'] = base_url('tpin/admin/page_kf').'/'.$page_id;
        $data['page_problem_url'] = base_url('tpin/admin/page_problem').'/'.$page_id;
        $data['page_car_add_url'] = base_url('tpin/admin/page_car_add').'/'.$page_id;
        $car_type_list_temp = $this->Car_type_model->get_car_type_list();
        $car_type_list = array();
        foreach($car_type_list_temp as $key => $val){
            $car_type_list[$val['car_type_id']] = $val['car_type_name'];
        }
        $car_info_list = $this->Car_info_model->get_car_info_list($where);
        foreach($car_info_list as $key => $val){
            $car_info_list[$key]['page_car_edit_url'] = base_url('tpin/admin/page_car_edit').'/'.$val['Id'];
            $car_info_list[$key]['page_car_del_url'] = base_url('tpin/admin/page_car_del');
            $car_info_list[$key]['car_type_name'] = $car_type_list[$val['type_id']];
        }
        $data['car_info_list'] = $car_info_list;
        $this->load->view('tpin/admin/page_car',$data);
    }
   //常见问题管理页面
    public function page_problem($page_id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['page_car_url'] = base_url('tpin/admin/page_goods').'/'.$page_id;
        $data['page_line_url'] = base_url('tpin/admin/page_line').'/'.$page_id;
        $data['page_rest_url'] = base_url('tpin/admin/page_rest').'/'.$page_id;
        $data['page_kf_url'] = base_url('tpin/admin/page_kf').'/'.$page_id;
        $data['page_problem_url'] = base_url('tpin/admin/page_problem').'/'.$page_id;

        $where = array('page_id' => $page_id, 'is_del' => 0, 'order_by' => 'is_sort asc, id asc');
        $data['info'] = $this->Problem_info_model->get_problem_info_list($where);
        $data['problem_url'] = base_url('tpin/admin/page_problem_add') . '/' . $page_id;
        $data['update_url'] = base_url('tpin/admin/page_problem_update');
        $data['edit_url'] = base_url('tpin/admin/page_problem_inset');
        $this->load->view('tpin/admin/page_problem', $data);
    }

    //问题编辑
    public function page_problem_inset($id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $where = array('id' => $id);
        $data['inset_url'] = base_url('tpin/admin/page_problem_modify') . '/' . $id;
        $data['info'] = $this->Problem_info_model->get_problem_info_detail($where);
        $this->load->view('tpin/admin/page_problem_inset', $data);
    }

    //问题编辑提交
    public function page_problem_modify($id) {
        $data['id'] = $id;
        $data['page_problem_name'] = $this->input->post('problem_name', TRUE);
        $data['page_problem_counet'] = $this->input->post('problem_content', TRUE);
        $data['is_sort'] = $this->input->post('is_sort', TRUE);
        $res = $this->Problem_info_model->save_problem_info($data);
        if ($res) {
            echo "<script language=javascript>alert('修改成功');history.back();</script>";
        }
    }

    //问题删除
    public function page_problem_update($id) {

        $data = array(
            'is_del' => "1",
        );
        $red = $this->db->update('tp_page_problem', $data, array('id' => $id));
        if ($red) {

            echo "<script language=javascript>alert('删除成功');history.back();</script>";
        }
    }

    //问题增加
    public function page_problem_add($page_id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['service_url'] = base_url('tpin/admin/page_problem_edit') . '/' . $page_id;
        $this->load->view('tpin/admin/page_problem_add', $data);
    }

    //问题insert
    public function page_problem_edit($page_id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['page_id'] = $page_id;
        $data['page_problem_name'] = $this->input->post('problem_name', TRUE);
        $data['page_problem_counet'] = $this->input->post('problem_content', TRUE);
        $res = $this->Problem_info_model->save_problem_info($data);
        if ($res) {

            redirect(base_url("tpin/admin/page_problem/" . $page_id));
        }
    }

    //客服管理页面
    public function page_kf($page_id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $where = array('page_id' => $page_id);
        $data['page_car_url'] = base_url('tpin/admin/page_goods').'/'.$page_id;
        $data['page_line_url'] = base_url('tpin/admin/page_line').'/'.$page_id;
        $data['page_rest_url'] = base_url('tpin/admin/page_rest').'/'.$page_id;
        $data['page_kf_url'] = base_url('tpin/admin/page_kf').'/'.$page_id;
        $data['page_problem_url'] = base_url('tpin/admin/page_problem').'/'.$page_id;

        $data['page_id'] = $page_id;
        $data['info'] = $this->Page_info_model->get_page_info_detail($where);
        $data['kf_url'] = base_url('tpin/admin/page_kf_add') . '/' . $page_id;
        $data['update_url'] = base_url('tpin/admin/page_kf_update') . '/' . $page_id;
        $kf_data = json_decode($data['info']['kf_data'], true);
        $data['mobile'] = isset($kf_data['mobile']) ? $kf_data['mobile'] : array();
        $data['wx'] = isset($kf_data['wx']) ? $kf_data['wx'] : array();
        $this->load->view('tpin/admin/page_kf', $data);
    }

    //客服增加
    public function page_kf_add($page_id) {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['service_url'] = base_url('tpin/admin/page_kf_edit') . '/' . $page_id;
        $this->load->view('tpin/admin/page_kf_add', $data);
    }

    //客服insert
    public function page_kf_edit($page_id) {
        $kf_name = $this->input->post('kf_name', TRUE);
        $kf_intro = $this->input->post('kf_intro', TRUE);
        $wx_name = $this->input->post('wx_name', TRUE);
        $wx_intro = $this->input->post('wx_intro', TRUE);
        $img = $this->muti_upload_image('wx_qrcode');

        $data['page_id'] = $page_id;
        $kf_data = array();
        for ($i = 0; $i < count($kf_name); $i++) {
            $kf_data['mobile'][$i] = array(
                'name' => $kf_name[$i],
                'intro' => $kf_intro[$i],
            );
        }
        for ($a = 0; $a < count($wx_name); $a++) {
            $kf_data['wx'][$a] = array(
                'name' => $wx_name[$a],
                'intro' => $wx_intro[$a],
                'qrcode' => $img[$a]
            );
        }
        $data['kf_data'] = json_encode($kf_data);
        $red = $this->Page_info_model->save_page_info($data);
        if ($red) {
            redirect(base_url("tpin/admin/page_kf/" . $page_id));
        }
    }

    // kefushangchu
    public function page_kf_update($page_id) {
        $data = array(
            'user_mobile' => "",
            'user_wx' => ""
        );
        $red = $this->db->update('tp_page_info', $data, array('page_id' => $page_id));
        if ($red) {

            echo "<script language=javascript>alert('删除成功');history.back();</script>";
        }
    }

    //二销页面车辆添加
    public function page_car_add($page_id)
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['page_id'] = $page_id;
        $data['car_type_list'] = $this->Car_type_model->get_car_type_list();
        $data['do_page_car_add_url'] = base_url('tpin/admin/do_page_car_add');    
        $this->load->view('tpin/admin/page_car_add',$data);
    }

    //二销车辆信息上传
    public function do_page_car_add() {
        $this->get_auth();
        $data = $this->get_left_url();
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['car_name'] = $this->input->post('car_name', TRUE);
        $data['car_sum'] = $this->input->post('car_sum', TRUE);
        $data['car_lgguge'] = $this->input->post('car_lgg', TRUE);
        $data['car_date'] = $this->input->post('car_date', TRUE);
        $data['car_money'] = $this->input->post('car_money', TRUE);
        $data['type_id'] = $this->input->post('car_lx', TRUE);
        $data['car_bz'] = $this->input->post('car_bz', TRUE);
        $data['spot_name'] = $this->input->post('car_dd', TRUE);
        if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
            $data['car_image'] = $this->upload_image('car_image', 'H5image');
        }
        $res = $this->Car_info_model->save_car_info($data);
        if ($res) {
            redirect(base_url("tpin/admin/page_goods/" . $data['page_id']));
        }
    }

    //二销车辆修改
    public function page_car_edit($id)
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $where = array(
            'Id'=>$id
        );
        $data['info'] = $this->Car_info_model->get_car_info_detail($where);
        $data['car_type_list'] = $this->Car_type_model->get_car_type_list();
        $data['do_page_car_edit_url'] = base_url('tpin/admin/do_page_car_edit');
        $data['id'] = $id;
        $this->load->view('tpin/admin/page_car_edit',$data);
    }

    //二销车辆修改页面
    public  function do_page_car_edit()
    {
        $this->get_auth();
        $data = $this->get_left_url();
        $id = $this->input->post('Id',TRUE);
        $where = array(
            'Id' => $id
        );
        $car_info = $this->Car_info_model->get_car_info_detail($where);
      
        if(!$car_info){
            echo "页面信息未找到";
        }

        $data['Id'] = $id; 
        $data['car_name'] = $this->input->post('car_name',TRUE);
        $data['car_sum'] = $this->input->post('car_sun',TRUE);
        $data['car_lgguge']=$this->input->post('car_lgg',TRUE);
        $data['car_date']=$this->input->post('car_date',TRUE);
        $data['car_money']=$this->input->post('car_money',TRUE);
        $data['type_id']=$this->input->post('car_lx',TRUE);
        $data['spot_name']=$this->input->post('car_dd',TRUE);
        $data['car_bz']=$this->input->post('car_bz',TRUE);
        
        if(isset($_FILES['car_image']) && $_FILES['car_image']['error']==0){
            $data['car_image'] = $this->upload_image('car_image','H5image');
        } 
        $this->Car_info_model->save_car_info($data);
        redirect(base_url("tpin/admin/page_goods/".$car_info['page_id']));  

    }  

    //二销车辆信息删除
    public function page_car_del($id)
    {
        $this->get_auth();
        $car_data['Id'] = $id;
        $car_data['is_show'] = 1;
        $res= $this->Car_info_model->save($car_data);
        $car_info = $this->Car_info_model->get_car_info_detail(array('Id' => $id));  
        redirect(base_url("tpin/admin/page_goods").'/'.$car_info['page_id']);
   }


    //二销线路列表
    public function page_line($page_id)
    {  
        $this->get_auth();
        $data = $this->get_left_url();

        $data['page_car_url'] = base_url('tpin/admin/page_goods').'/'.$page_id;
        $data['page_line_url'] = base_url('tpin/admin/page_line').'/'.$page_id;
        $data['page_rest_url'] = base_url('tpin/admin/page_rest').'/'.$page_id;
        $data['page_kf_url'] = base_url('tpin/admin/page_kf').'/'.$page_id;
        $data['page_problem_url'] = base_url('tpin/admin/page_problem').'/'.$page_id;

        $where = array(
            'page_id' => $page_id,
            'is_del' => 0
        );
        $data['page_line_add_url'] = base_url('tpin/admin/page_line_add').'/'.$page_id;
        $line_info_list = $this->Line_info_model->get_line_info_list($where);
        foreach($line_info_list as $key => $val){
            $line_info_list[$key]['page_line_edit_url'] = base_url('tpin/admin/page_line_edit').'/'.$val['line_id'];
            $line_info_list[$key]['page_line_del_url'] = base_url('tpin/admin/page_line_del').'/'.$val['line_id'];
        }
        $data['line_info_list'] = $line_info_list;
       

        $this->load->view('tpin/admin/page_line',$data);
    }

    //二销 线路增加模板
    public  function page_line_add($page_id)
    { 
        $this->get_auth();
         $data = $this->get_left_url();
        $data['page_id'] = $page_id;
        $data['do_page_line_add_url'] = base_url('tpin/admin/do_page_line_add');
        $this->load->view('tpin/admin/page_line_add',$data);
    }

    //二销线路增加 
    public function do_page_line_add(){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['line_title'] = $this->input->post('line_title', TRUE);
        $data['line_place'] = $this->input->post('line_place', TRUE);
        $data['line_name'] = $this->input->post('line_name', TRUE);
        $data['line_ename'] = $this->input->post('line_ename', TRUE);
        $data['price_p'] = $this->input->post('price_p', TRUE);
        $data['price_c'] = $this->input->post('price_c', TRUE);
        $data['cost_price'] = $this->input->post('cost_price', TRUE); 
        $data['line_detail_url'] =  $this->input->post('line_detail_url', TRUE);
        $data['line_detail'] =  $this->input->post('line_detail', TRUE); 
        $data['image'] = $this->upload_image('image', 'H5image');
        $data['add_time'] = time();
        $res = $this->Line_info_model->save_line_info($data);
        if($res){
            redirect(base_url("tpin/admin/page_line/".$data['page_id']));     
        }
    }

    //二销线路编辑
    public  function page_line_edit($line_id){
        $this->get_auth();
        $data = $this->get_left_url();
        
        $where = array(
            'line_id' => $line_id
        );
        $line_info = $this->Line_info_model->get_line_info_detail($where);

        $data['line_info'] = $line_info;
        $data['line_id'] = $line_id;
        $data['page_id'] = $line_info['page_id'];
        $data['do_page_line_edit_url'] = base_url('tpin/admin/do_page_line_edit');
        $this->load->view('tpin/admin/page_line_edit',$data);
    }

    //二销编辑代码
    public  function do_page_line_edit(){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['line_id'] = $this->input->post('line_id', TRUE);
        $where = array(
            'line_id' => $data['line_id']
        );
        $line_info = $this->Line_info_model->get_line_info_detail($where);
        if(!$line_info){
            echo "页面信息未找到";
        }

        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['line_title'] = $this->input->post('line_title', TRUE);
        $data['line_place'] = $this->input->post('line_place', TRUE);
        $data['line_name'] = $this->input->post('line_name', TRUE);
        $data['line_ename'] = $this->input->post('line_ename', TRUE);
        $data['price_p'] = $this->input->post('price_p', TRUE);
        $data['price_c'] = $this->input->post('price_c', TRUE);
        $data['cost_price'] = $this->input->post('cost_price', TRUE); 
        $data['line_detail_url'] =  $this->input->post('line_detail_url', TRUE);
        $data['line_detail'] =  $this->input->post('line_detail', TRUE); 
        if(isset($_FILES['image']) && $_FILES['image']['error']==0){
            $data['image'] = $this->upload_image('image','H5image');
        } 
        
        $this->Line_info_model->save_line_info($data);
        redirect(base_url("tpin/admin/page_line/".$data['page_id']));
    }

    //二销线路删除
    public  function page_line_del($line_id){
        $this->get_auth();
        $data = $this->get_left_url();

        $data = array(
            'line_id' => $line_id,
            'is_del' => 1
        );
        $this->Line_info_model->save_line_info($data);
        $line_info = $this->Line_info_model->get_line_info_detail(array('line_id' => $line_id));
        redirect(base_url("tpin/admin/page_line/".$line_info['page_id']));
    }

    //二销餐厅列表
    public  function page_rest($page_id){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['page_car_url'] = base_url('tpin/admin/page_goods').'/'.$page_id;
        $data['page_line_url'] = base_url('tpin/admin/page_line').'/'.$page_id;
        $data['page_rest_url'] = base_url('tpin/admin/page_rest').'/'.$page_id;
        $data['page_kf_url'] = base_url('tpin/admin/page_kf').'/'.$page_id;
        $data['page_problem_url'] = base_url('tpin/admin/page_problem').'/'.$page_id;


        $where = array(
            'page_id' => $page_id,
            'is_show' => 1
        );
        $data['page_rest_add_url'] = base_url('tpin/admin/page_rest_add').'/'.$page_id;
        $rest_info_list = $this->Hotel_info_model->get_hotel_info_list($where);
        foreach($rest_info_list as $key=>$val){
            $rest_info_list[$key]['hotel_image']=  json_decode($val['hotel_image']);
            $rest_info_list[$key]['page_rest_edit_url'] = base_url('tpin/admin/page_rest_edit').'/'.$val['id'];
            $rest_info_list[$key]['page_rest_del_url'] = base_url('tpin/admin/page_rest_del').'/'.$val['id'];
        }
        $data['rest_info_list'] = $rest_info_list;
        $this->load->view('tpin/admin/page_rest',$data);   
    }

    //二销餐厅上传界面
    public  function page_rest_add($page_id){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['page_id']=$page_id;         
        $data['do_page_rest_add_url']=base_url('tpin/admin/do_page_rest_add'); 
        $this->load->view('tpin/admin/page_rest_add',$data);
    }

    //二销餐厅上传代码
    public  function do_page_rest_add(){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['hotel_easy_title'] = $this->input->post('hotel_name', TRUE);
        $data['hotel_detail_title'] = $this->input->post('hotel_name1', TRUE);
        $data['is_sort'] = $this->input->post('is_sort', TRUE);
        $data['hotel_introduce'] = $this->input->post('hotel_content', TRUE);

        $data['qf_id'] = 1;
        $img = $this->muti_upload_image('hotel_img1');
        $data['hotel_image']=  json_encode(array('view_image' => $img));  

        $res = $this->Hotel_info_model->save_hotel_info($data);
        if($res){
            redirect(base_url("tpin/admin/page_rest/".$data['page_id']));     
        } 
    }

    //二销餐厅修改
    public  function page_rest_edit($id){
        $this->get_auth();
        $data = $this->get_left_url();

        $where = array(
            'id'=>$id
        );
        $data['info'] = $this->Hotel_info_model->get_hotel_info_detail($where);
        $data['info']['hotel_image'] =  json_decode($data['info']['hotel_image'], true);
        $data['do_page_rest_edit_url'] = base_url('tpin/admin/do_page_rest_edit');
        $this->load->view('tpin/admin/page_rest_edit',$data);  
    }

    //二销餐厅界面修改代码
    public  function do_page_rest_edit(){

        $this->get_auth();
        $data = $this->get_left_url();

        $data['id'] = $this->input->post('id', TRUE);
        $where = array(
            'id' => $data['id']
        );
        $rest_info = $this->Hotel_info_model->get_hotel_info_detail($where);
        if(!$dining_info){
            echo "页面信息未找到";
        }
        $page_id = $this->input->post('page_id',TRUE);
        $old_img = $this->input->post('old_img', TRUE);
     
        $data['hotel_easy_title'] = $this->input->post('hotel_name', TRUE);
        $data['hotel_detail_title'] = $this->input->post('hotel_name1', TRUE);
        $data['is_sort'] = $this->input->post('is_sort', TRUE);
        $data['hotel_introduce'] = $this->input->post('hotel_content', TRUE);
    
        $old_img_arr = array();
        if($old_img){
            $old_img_arr = explode(',', $old_img);
        }
        $upload_img = $this->muti_upload_image('hotel_img1');
       
        if(!empty($old_img_arr)){
            $upload_img = array_merge($old_img_arr, $upload_img);
        }
        $data['hotel_image']=  json_encode(array('view_image' => $upload_img));  
    
        $this->Hotel_info_model->save_hotel_info($data);
        redirect(base_url("tpin/admin/page_rest/".$page_id));
    }

    // 二销餐厅删除
    public function page_rest_del($id){
        $this->get_auth();
        $data = $this->get_left_url();

        $data['id'] = $id;
        $data['is_show'] = 2;
        $this->Hotel_info_model->save_hotel_info($data);  
        $rest_info = $this->Hotel_info_model->get_hotel_info_detail(array('id' => $id));
        redirect(base_url("tpin/admin/page_rest/".$rest_info['page_id']));
    }

    // 订单管理首页
    public function order() {
        $business_account = $this->get_auth();
        $data = $this->get_left_url();

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }

        $business_id = $business_account['business_id'];
        $where = array(
            'a.business_id' => $business_id, 'b.is_master' => 1, 'limit' => 20, 'offset' => 0,'order_by' => 'a.order_id desc'
        );
        if(!empty($data['param']['name'])){
            $where['b.user_name'] = $data['param']['name'];
        }

        if (!isset($data['param']['pay_status']) || $data['param']['pay_status'] != 1){
            $data['param']['pay_status'] = 0;
        }else{
            $data['param']['pay_status'] = 1;
        }
        $where['pay_status'] = $data['param']['pay_status'];

        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
        
        if(!empty($data['param']['per_total'])){
            $where['limit'] = $data['param']['per_total'];
        }
        $order_info_list = $this->Order_info_model->search_order_info_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']); 
        $data['order_info_list'] = $order_info_list;
        $data['detail_url'] = base_url('tpin/admin/order_detail');
        $data['userdata_url'] = base_url('tpin/admin/user_data');
        $data['url'] = base_url('tpin/admin/order');
        $data['excel_url'] = base_url('tpin/admin/order_export/pay');
        $data['excel2_url'] = base_url('tpin/admin/order_export/unpay'); 
        $data['travelers_url'] = base_url('tpin/admin/travelers'); 
        foreach($order_info_list as $k=>$v){
        $data['order_info_list'][$k]['user_data']=  json_decode($v['user_data']);
            
        }
       $data['role_id']=$business_account['role_id'];  
        $this->load->view('tpin/admin/order', $data);
    }
     //直通车页面订单展示
         public function ztc_order() {

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }


        $where = array(
             'b.is_master' => 1,'a.order_type'=>3, 'limit' => 20, 'offset' => 0,'order_by' => 'a.order_id desc'
        );
        if(!empty($data['param']['name'])){
            $where['b.user_name'] = $data['param']['name'];
        }

        if (!isset($data['param']['pay_status']) || $data['param']['pay_status'] != 1){
            $data['param']['pay_status'] = 0;
        }else{
            $data['param']['pay_status'] = 1;
        }
        $where['pay_status'] = $data['param']['pay_status'];

        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
        
        if(!empty($data['param']['per_total'])){
            $where['limit'] = $data['param']['per_total'];
        }
        $order_info_list = $this->Order_info_model->search_order_info_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']); 
        $data['order_info_list'] = $order_info_list;
        $data['detail_url'] = base_url('tpin/admin/ztc_order_detail');
        $data['userdata_url'] = base_url('tpin/admin/user_data');
        $data['url'] = base_url('tpin/admin/ztc_order');
        $data['excel_url'] = base_url('tpin/admin/order_export/pay');
        $data['excel2_url'] = base_url('tpin/admin/order_export/unpay'); 
        $data['travelers_url'] = base_url('tpin/admin/travelers'); 
        foreach($order_info_list as $k=>$v){
        $data['order_info_list'][$k]['user_data']=  json_decode($v['user_data']);
            
        }
        $this->load->view('tpin/admin/order_ztc', $data);
    }
    //新疆直通车
      public function ztc_order_detail($order_id) {
        $this->get_auth();
        $data = $this->get_left_url();

        $where = array(
            'a.order_id' => $order_id,
            'b.is_master' => 1
        );

        $data['info'] = $this->Order_info_model->order_user_detail($where);
        $data['info']['xiaq'] = json_decode($data['info']['order_data']);
      
        foreach ($data['info']['xiaq']->car as $k => $v) {
            $data['car'] =  '【类型】:' . $v->name . '【价格】:' . $v->price;
        }
        foreach ($data['info']['xiaq']->hotel as $k1 => $v1) {
            $data['stroke'][$k1] =  '【酒店名字】:'.$v1->name . '【价格】:' . $v1->price .'元'.'【备注】:'.$v1->type ;
        }
//          echo "<pre>";
//        print_r($data);
        $data['sub_detail_url'] = base_url('tpin/admin/sub_detail');
        $this->load->view('tpin/admin/ztc_order_detail', $data);
    }


    //订单详情
    public function order_detail($order_id) {
        $this->get_auth();
        $data = $this->get_left_url();
            
        $where = array(
            'a.order_id' => $order_id,
            'b.is_master' => 1
        );

        $data['info'] = $this->Order_info_model->order_user_detail($where);
        $data['info']['pdf']=  json_decode($data['info']['order_pdf']);        
        $data['xiaq'] = json_decode($data['info']['order_data'],TRUE);
         foreach($data['xiaq']['car'] as $k=>$v){
             $data['car'][$k] = '类型:'.'【'. $v['type'].'】,' . '名称:' . $v['name'] .','.'数量:'.$v['nun'].','.'价格:'.$v['price'].','. '时间:' . $v['day']."\n\n";
             
         }
        foreach($data['xiaq']['stroke'] as $k=>$v){
         $data['stroke'][$k] = '类型:'.'【'. $v['type'] .'】,'. '名称:' .$v['name'] .','.'数量:'.$v['num'].','.'价格:'.$v['price'].','. '时间:' . $v['day']."\n\n";
        
      }   
        if($data['info']['is_handle']==1){
            $data_view['is_handle']=$data['info']['is_handle'];
        } else if($data['info']['is_handle']!=3){
          
               $data_view['is_handle']=2; 
            
            
               
        }   
   
       $data_view['order_id']=$order_id;
       $this->Order_info_model->save_order_info($data_view);
        $data['sub_detail_url'] = base_url('tpin/admin/sub_detail');
         $data['url'] = base_url('tpin/admin/order');
       $business_account = $this->get_auth();
//       $data['role_id']=$business_account['role_id']; 
//       echo "<pre>";
//      print_r($data);die;
        $this->load->view('tpin/admin/order_detail', $data);
    }

    public function user_data($order_id) {
        $this->get_auth();
        $data = $this->get_left_url();

        $where = array(
            'order_id' => $order_id,
        );

        $data['info'] = $this->Order_info_model->get_order_info_detail($where);
        $data['order_desc'] = $data['info']['order_desc'];
        $data['order_id'] = $order_id;
        $data['info'] = json_decode($data['info']['extra_data']);
        $data['info_t'] = $this->Order_user_model->get_order_user_list($where);

        $data['sub_data_update_url'] = base_url('tpin/admin/sub_data_updata');
            $business_account = $this->get_auth();
       $data['role_id']=$business_account['role_id'];  
        $this->load->view('tpin/admin/user_data', $data);
    }

    public function sub_data_updata() {
        $this->get_auth();
        $data = $this->get_left_url();
        $order_id=$this->input->post('order_id', TRUE);
        $datav['order_id'] =$order_id; 
        $datav['user_name'] = $this->input->post('user_names', TRUE);
        $datav['user_fname'] = $this->input->post('user_fnames', true);
        $datav['user_lname'] = $this->input->post('user_lnaems', true);
        $datav['user_mobile'] = $this->input->post('user_mobiles', true);
        $datav['user_wx'] = $this->input->post('user_wxs', true);
        $datav['user_cardid'] = $this->input->post('user_cardids', true);
        $en_name = $this->input->post('en_name', true);
        $cn_name = $this->input->post('cn_name', true);
        $no = $this->input->post('no', true);
        $come_time = $this->input->post('come_time', true);
        $come_place = $this->input->post('come_place', true);
       $li=array('order_id'=>$order_id,'is_master'=>0);
        $list=$this->Order_user_model->get_order_user_list($li);
       if(!empty($list)){
         $wheres = 'order_id=' . $order_id . ' AND is_master=0';
        $this->User_model->del($wheres,'us_order_user');
       $datas['user_name'] = $this->input->post('user_name', TRUE);
        $datas['user_fname'] = $this->input->post('user_fname', true);
        $datas['user_lname'] = $this->input->post('user_lname', true);
        $datas['user_cardid'] = $this->input->post('user_cardid', true);
        $datas['user_mobile'] = $this->input->post('user_mobile', true);
         foreach($datas['user_name'] as $k=>$v){
            $view[$k] = array(
                'order_id' => $datav['order_id'],
                'user_name' => $datas['user_name'][$k],
                'user_fname' => $datas['user_fname'][$k],
                'user_lname' => $datas['user_lname'][$k],
                'user_cardid' => $datas['user_cardid'][$k],
                'user_mobile' => $datas['user_mobile'][$k],
                'is_master' => 0
            );
       
        }

      $this->db->insert_batch('us_order_user',$view);
           
       }
     
   
        $hotel = array(
            'cn_name' => $cn_name,
            'en_name' => $en_name
        );
        $fly = array(
            'no' => $no,
            'come_time' => $come_time,
            'come_place' => $come_place
        );
        $come = array(
            'hotel' => $hotel,
            'fly' => $fly
        );
        $list_t['extra_data'] = json_encode($come);
        $list_t['order_id'] = $datav['order_id'];
        $list_t['order_desc']=$this->input->post('order_desc',TRUE);
        $where = 'order_id=' . $order_id . ' AND is_master=1';
       
        $this->User_model->update_one($where, $datav, 'us_order_user');
        
        $this->Order_info_model->save_order_info($list_t);
         echo "<script language=javascript>alert('保存成功');location.href='" .base_url("tpin/admin/order/") . "';</script>";
    }
    //出行人信息增加
    public  function travelers($order_id){
        $this->get_auth();
        $data = $this->get_left_url();
        $data['order_id']=$order_id;
        $data['travelers_url']=  base_url('tpin/admin/travelers_add');
        $business_account = $this->get_auth();
       $data['role_id']=$business_account['role_id'];  
        $this->load->view('tpin/admin/travelers', $data);
    }
    public  function travelers_add(){
        $datas['user_name'] = $this->input->post('user_name', TRUE);
        $datas['user_fname'] = $this->input->post('user_fname', true);
        $datas['user_lname'] = $this->input->post('user_lname', true);
        $datas['user_cardid'] = $this->input->post('user_cardid', true);
        $datas['user_mobile'] = $this->input->post('user_mobile', true);
        $datas['order_id']=$this->input->post('order_id',true);
        $datas['is_master'] =0;
         $this-> Order_user_model->save_order_user($datas);  
              echo "<script language=javascript>alert('保存成功');location.href='" .base_url("tpin/admin/order/") . "';</script>";
            
        
    }
   //修改价格
   public function order_price(){
       $data['pay_price']=$this->input->post('price',TRUE);
       $data['order_id']=$this->input->post('order_id',TRUE);
       $where=array('order_id'=>$data['order_id']);
       $order_id=$this->Order_info_model->get_order_info_detail($where);
       if(empty($data['order_id'])){
             $result['code'] = 2;
             $result['msg'] = "用户id不能为空";
             return $result;
       }
       if(empty($order_id)){
              $result['code'] = 3;
             $result['msg'] = "用户不存在";
             return $result;
       }
       $price=$this->Order_info_model->save_order_info($data);
       if($price){
             $result['code'] = 1;
             $result['msg'] = "价格修改成功";
             return $result; 
       }
       
   }
   //修改处理状态
   public  function sub_handle(){
      $data['is_handle']=$this->input->post('handle',TRUE);
      $data['order_id']=$this->input->post('order_id',true);
      $res=$this->Order_info_model->save_order_info($data);  
    if($res){
        $result['code']==1;
        $result['msg']=='处理成功';
        return $result;
        
    }
   }
   // 订单详情 增加备注
    public function sub_detail(){
  //          echo "<pre>";
  //          print_r($_POST);die;
   
        $this->get_auth();
        $data = $this->get_left_url();
        $data['order_id'] = $this->input->post('order_id',TRUE);
        $data['order_desc'] = $this->input->post('order_bz',TRUE);
        $data['pay_price']=$this->input->post('pay_price',TRUE);
        $pdf= base64_decode($this->input->post('ems',TRUE));
        $data_pdf= json_decode($pdf);
     //  echo "<pre>";
     //  print_r($data);die;
//        print_r($_FILES['fileupload']['error'][0]);die;
      
           $order_pdf =array_filter($this-> files_upload_image('fileupload')); 

       //    print_r($order_pdf);die;
           if(!empty($order_pdf)){
            array_push($data_pdf,$order_pdf);
           }
          $result = [];
          array_walk_recursive($data_pdf, function($value) use (&$result) {
         array_push($result, $value);
         });

             $data['order_pdf']= json_encode($result);   
             if(!empty($data['order_pdf'])){
                $data['is_handle']=3; 
             }
             

       

        $res= $this->Order_info_model->save_order_info($data);  
          if($res){
          echo "<script language=javascript>alert('保存成功');location.href='" .base_url("tpin/admin/order/") . "';</script>";
    }
        
    }
   // 订单详情 pdf删除
    public function sub_detail_pdf($order_id){

        $this->get_auth();
        $data = $this->get_left_url();
        
        $where=array('order_id'=>$order_id,'order_pdf'=>NULL);
      //  $list=$this->Order_info_model->get_order_info_detail($where);
     
        $res= $this->Order_info_model->save_order_info($where);  
    if($res){
          echo "<script language=javascript>alert('删除成功');location.href='" .base_url("tpin/admin/order/") . "';</script>";
    }
        
    }
        
    //无session 定向登录
    public function get_auth()
    {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if(!$business_account_id){
            redirect(base_url('tpin/admin/login'));
        }
        $where = array(
            'business_account_id' => $business_account_id
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if(empty($business_account)){
            redirect(base_url('tpin/admin/login'));
        }
        return $business_account;
    }

    //简单获取侧边链接
    public function get_left_url() {
        return array(
            'main' => base_url('tpin/admin/main'),
            'page' => base_url('tpin/admin/page'),
            'order' => base_url('tpin/admin/order'),
            'service' => base_url('tpin/admin/service'),
            'pwd_edit' => base_url('tpin/admin/pwd_edit'),
            'login_out' => base_url('tpin/admin/login_out'),
        );
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

    //订单导出 (未付款)
    public function order_export($status) {
        $business_account = $this->get_auth();
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        $business_id = $business_account['business_id'];
        $pay_status = $status == 'unpay' ? 0 : 1;
        $order = array('a.business_id' => $business_id, 'a.pay_status' => $pay_status);
        $data['info'] = $this->Order_info_model->search_order_info_list($order);

        $resultPHPExcel = new PHPExcel();
        $resultPHPExcel->getActiveSheet()->getDefaultColumnDimension('A1')->setWidth(20);
        $resultPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $resultPHPExcel->getActiveSheet()->setCellValue('B1', '用户姓名');
        $resultPHPExcel->getActiveSheet()->setCellValue('C1', '手机号码');
        $resultPHPExcel->getActiveSheet()->setCellValue('D1', '订单号');
        $resultPHPExcel->getActiveSheet()->setCellValue('E1', '下单时间');
        $resultPHPExcel->getActiveSheet()->setCellValue('F1', '订单金额');
        $resultPHPExcel->getActiveSheet()->setCellValue('G1', '支付状态');
        foreach ($data['info'] as $k => $v) {
            $i = $k+2;
            $resultPHPExcel->getActiveSheet()->setCellValue('A' . $i, $v['order_id']);
            $resultPHPExcel->getActiveSheet()->setCellValue('B' . $i, $v['user_name']);
            $resultPHPExcel->getActiveSheet()->setCellValue('C' . $i, $v['user_mobile']);
            $resultPHPExcel->getActiveSheet()->setCellValue('D' . $i, $v['order_sn']);
            $resultPHPExcel->getActiveSheet()->setCellValue('E' . $i, date('Y-m-d H:i:s', $v['add_time']));
            $resultPHPExcel->getActiveSheet()->setCellValue('F' . $i, $v['pay_price']);
            $pay_status_str = $v['pay_status'] == 0 ? '未支付' : '已支付';
            $resultPHPExcel->getActiveSheet()->setCellValue('G' . $i, $pay_status_str);
        }
        $outputFileName = "二销订单页面.xls";
        $xlsWriter = new PHPExcel_Writer_Excel5($resultPHPExcel);
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outputFileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
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

    //多图上传
    private function muti_upload_image($filename) {
        $file = $_FILES[$filename];

        $new_url = array();
        foreach ($file['error'] as $k => $v) {
            if ($v == 0) {
                switch ($file['type'][$k]) {
                    case 'image/jpeg':
                        $br = '.jpg';
                        break;
                    case 'image/png':
                        $br = '.png';
                        break;
                    case 'image/gif':
                        $br = '.gif';
                        break;
                     case 'application/pdf':
                        $br = '.pdf';
                        break;
                    default:
                        $br = FALSE;
                }

                if ($br) {
                    $key = md5(rand(1, 99999) . time());
                    $pic_url = "./public/images/H5image/" . $key . $br;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[] = "/public/images/H5image/" . $key . $br;
                }else{
                    $new_url[] = '';
                }
            }else{
                $new_url[] = '';
            }
        }
        return $new_url;
    }
    //多文件上传
        
    private function files_upload_image($filename) {
        $file = $_FILES[$filename];

        $new_url = array();
        foreach ($file['error'] as $k => $v) {
            if ($v == 0) {
                switch ($file['type'][$k]) {
                    case 'image/jpeg':
                        $br = '.jpg';
                        break;
                    case 'image/png':
                        $br = '.png';
                        break;
                    case 'image/gif':
                        $br = '.gif';
                        break;
                     case 'application/pdf':
                        $br = '.pdf';
                        break;
                    default:
                        $br = FALSE;
                }

                if ($br) {
                   
                     $pic_url = "./public/images/H5image/" . $file['name'][$k] ;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[] = "/public/images/H5image/" . $file['name'][$k];
              
                   
                }else{
                    $new_url[] = '';
                }
            }else{
                $new_url[] = '';
            }
        }
        return $new_url;
    }
     //文件上传
     private function upload_files($filename, $fileurl, $key='time')
    {
        /* 如果目标目录不存在，则创建它 */
        if(!file_exists('./public/travel/'.$fileurl)){
            if(!mkdir('./public/travel/'. $fileurl)){
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        $info = pathinfo($file['name']);
        $br = '.'.$info['extension'];
        if(!in_array($br, array('.doc', '.docx', '.pdf'))){
            return '';
        }

        if($key=='time'){
            $key = md5(rand(1,99999).time());
        }
        $pic_url = "./public/travel/".$fileurl."/".$key.$br;
        move_uploaded_file($file['tmp_name'], $pic_url);
        return  "/public/travel/".$fileurl."/".$key.$br;
    }
}