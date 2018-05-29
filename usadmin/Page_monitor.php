<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
define('DEAULT_DAY_DETAILS', '<p class="i_food dayTit">餐饮</p><p><br/></p><p class="i_stroke dayTit">行程</p><p><br/></p><p class="i_hotel dayTit">住宿</p><p><br/></p>');
class Page_monitor extends CI_Controller
{

    public function __construct()
    {
         parent::__construct();
        $this->load->model('User_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Company_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');

        $this->load->helper('url');
        $this->load->helper('common');

        $this->load->library('MY_Session');
        $this->load->library('common');
        $this->load->library('image_lib');
    }



    //跟团游价格列表
    public function page_price($page_id){
        $where = array(
            'page_id' => $page_id
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        if($page_info['date']!='')
        {
            $data['price']=json_decode($page_info['date'],TRUE);
            $data['date_choose']=$data['price']['date'];
        }else{
            $data['date_choose'] = array();
        }

        $time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend= strtotime('+12 month', $time);
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend))
        {
            $data['date']['cal'][] =array(
                'year'=>date('Y',strtotime('+1 month', $time)),
                'month'=>date('n',strtotime('+1 month', $time)),
                'month_cn'=>$this->common->get_month_cn(date('n',strtotime('+1 month', $time))),
                'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                'all_days'=>date('t',strtotime('+1 month', $time)),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
            );
            $time= strtotime('+1 month', $time);
        }

        $data['price_update_url']=  base_url('usadmin/Page_monitor/page_price_update/').'/'.$page_id;   
        $this->load->view('page_monitor/page_price',$data);
    }

    public function page_price_update($page_id){
        $date_val = $this->input->post('date_val', TRUE);
        $date_price = $this->input->post('date_price', TRUE);

        if (count($date_val) > 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                $new_date[$v] = $date_price[$k];
            }

            $date1 = array(
                'name' => '',
                'date' => $new_date
            );
            $data['date'] = json_encode($date1);
        }

        $res = $this->User_model->update_one(array('page_id' => $page_id), $data, 'us_page_info');
        if($res){
            redirect(base_url("usadmin/page_monitor/page_price/$page_id"));    
        }  
    }

    //跟团游删除
    public function page_del($page_id) {
        $where = array(
            'page_id' => $page_id,
            'is_show' => 1
        );
        $res = $this->Page_info_model->save_page_info($where);
        if ($res) {
            echo "<script language=javascript>alert('删除成功');location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
        }
    }

    // 跟团游编辑页面
    public function  page_edit($page_id){
        $where = array(
            'page_id'=>$page_id
        ); 
        $data['info'] = $this->Page_info_model->get_page_info_detail($where); 
        $data['info']['image_data']=  json_decode($data['info']['image_data'], true);
        $data['info']['kf_data']=  json_decode($data['info']['kf_data'], true);
        $company_info = $this->Company_info_model->get_company_info_detail(array('company_id' => $data['info']['business_id']));
        $data['info']['company_name'] = isset($company_info['company_name']) ? $company_info['company_name'] : '无';
        $data['page_edit_url']=  base_url('usadmin/Page_monitor/do_page_edit');
        $this->load->view('page_monitor/page_edit',$data);
    }

    //跟团游insert 
    public function do_page_edit() {
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['share_url'] = $this->input->post('share_url', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['template_type'] = $this->input->post('template_type', TRUE);
        $data['is_pay'] = $this->input->post('is_pay', TRUE);
        $data['update_time'] = time();

        $image_data = array();
        if (isset($_FILES['image_top']) && $_FILES['image_top']['error'] == 0) {
            $image_data['top'] = $this->upload_image('image_top', 'H5image');
        }else{
            $image_data['top'] = $this->input->post('image_top', TRUE);
        }
        if (isset($_FILES['image_spec']) && $_FILES['image_spec']['error'] == 0) {
            $image_data['spec'] = $this->upload_image('image_spec', 'H5image');
        }else{
            $image_data['spec'] = $this->input->post('image_spec', TRUE);
        }
        if (isset($_FILES['image_day']) && $_FILES['image_day']['error'] == 0) {
            $image_data['day'] = $this->upload_image('image_day', 'H5image');
        }else{
            $image_data['day'] = $this->input->post('image_day', TRUE);
        }
        if (isset($_FILES['image_share']) && $_FILES['image_share']['error'] == 0) {
            $image_data['share'] = $this->upload_image('image_share', 'H5image');
        }else{
            $image_data['share'] = $this->input->post('image_share', TRUE);
        }
        $data['image_data'] = json_encode($image_data);

        $kf_type = $this->input->post('kf_type', TRUE);
        $kf_intro = $this->input->post('kf_intro', TRUE);

        $kf_data = array();
        for ($i = 0; $i < count($kf_type); $i++) {
            if($kf_type[$i] == '1'){
                $kf_data['qq'][] = array(
                    'name' => 'QQ客服',
                    'intro' => $kf_intro[$i]
                );
            }elseif($kf_type[$i] == '3'){
                $kf_data['mobile'][] = array(
                    'name' => '电话客服',
                    'intro' => $kf_intro[$i]
                );
            }
        }

        $old_img = $this->input->post('old_img', TRUE);
        $old_img_arr = array();
        if ($old_img) {
            $old_img_arr = explode(',', $old_img);
        }
        $upload_img = $this->muti_upload_image('wx_qrcode', 'H5image');

        if (!empty($old_img_arr)) {
            $upload_img = array_merge($old_img_arr, $upload_img);
        }
        if (!empty($upload_img)) {
            $qrcode_data = array();
            foreach($upload_img as $key => $val){
                $qrcode_data[$key] = array('qrcode' => $val);
            }
            $kf_data['wx'] = $qrcode_data;
        }
        $data['kf_data'] = json_encode($kf_data);
        $data['inst_data'] = $this->input->post('hotel_content', true);
        $this->Page_info_model->save_page_info($data);
        echo "<script language=javascript>alert('编辑成功');location.href='" . base_url('usadmin/page_monitor') . "';</script>";
    }

    //自由行增加页面
    public function page_add() {
    
  
        $data['do_page_add_url'] = base_url('usadmin/page_monitor/do_page_add');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $this->load->view('usadmin/page_monitor/page_add',$data);   
    }
  //自由行增加
    public function do_page_add() {
       // $business_account = $this->_get_auth();

        $data['business_id'] = 67337;
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['short_title'] = $this->input->post('short_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['inst_data'] = $this->input->post('inst_data', TRUE);
        $style_type = $this->input->post('style_type',true);
        $data['style_type'] = $style_type ? $style_type : DEFAULT_TEMPLATE_TYPE;
        $kf_type=$this->input->post('kf_type',TRUE);   
        $user_mobile=$this->input->post('user_mobile',TRUE);   
        $image_data = array();
        if (isset($_FILES['image_top']) && $_FILES['image_top']['error'] == 0) {
            $image_data['top'] = $this->upload_image('image_top', 'H5image');
        }
        if (isset($_FILES['image_spec']) && $_FILES['image_spec']['error'] == 0) {
            $image_data['spec'] = $this->upload_image('image_spec', 'H5image');
        }
        if (isset($_FILES['image_share']) && $_FILES['image_share']['error'] == 0) {
            $image_data['share'] = $this->upload_image('image_share', 'H5image');
        }
        if (isset($_FILES['image_wx']) && $_FILES['image_wx']['error'] == 0) {
            $image_wx = $this->upload_image('image_wx', 'H5image');
        }
        $data['image_data'] = json_encode($image_data);
        $data['page_type'] = 2;
		$data['is_release'] = 1;
        if($kf_type == '微信'){
            $kf_data = array(
                'wx' => array(
                    'name'=>'微信',
                    'intro'=>$image_wx
                )
            );   
        }elseif($kf_type == '手机'){
           $kf_data = array(
                'mobile' => array(
                    'name'=>'手机',
                    'intro'=>$user_mobile
                )   
            );      
        }elseif ($kf_type == 'QQ') {
              $kf_data = array(
                'qq' => array(
                    'name'=>'QQ',
                    'intro'=>$user_mobile
                )  
            );    
        }
          
        $data['kf_data']=  json_encode($kf_data);
     // $data['template_type'] = $business_account['template_name'];
        $data['add_time']=time();
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";die;
        $page_id = $this->Page_info_model->save_page_info($data);
        if($page_id){
            if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == 0) {
                $image_data['fileupload'] = $this->upload_files('fileupload', $page_id, TRAVEL_FILE_KEY);
            }
            $data_up = array(
                'page_id' => $page_id,
                'image_data' => json_encode($image_data),
                'share_url' => $business_account['share_name'].$page_id
            );
            $this->Page_info_model->save_page_info($data_up);
            //创建默认套餐
            $data_package = array(
                'page_id' => $page_id,
                'city_name' => DEFAULT_CITY_NAME,
                'package_name' => DEFAULT_PACKAGE_NAME,
                'add_time' => time()
            );
            $this->Package_info_model->save_package_info($data_package);
            echo "<script language=javascript>alert('保存成功');location.href='" .base_url("usadmin/page_monitor/index/"). "';</script>";
        }
    }

    //跟团游页面复制
    public function page_copy($page_id){
        error_reporting(-1);
        ini_set('display_errors', 1);
        //1. 复制page_info
        $where = array(
            'page_id' => $page_id
        );
        $old_page_info = $this->Page_info_model->get_page_info_detail($where);
        $new_page_info = $old_page_info;
        $new_page_info['add_time'] = time();
        unset($new_page_info['page_id']);
        unset($new_page_info['update_time']);
        $this->db->trans_start();
        $page_id = $this->Page_info_model->save_page_info($new_page_info);
        if(!$page_id){
            $this->db->trans_rollback();
            echo "<script>alert('复制失败');location.href='".base_url('usadmin/page_monitor')."'</script>";
            return;
        }

        //2.复制行程
        $where = array(
            'monitor_id' => $old_page_info['page_id']
        );
        $day_data = $this->H5_Day_info_model->get_day_info_list($where);
        foreach($day_data as $key => $val){
            $new_day_info = $val;
            $new_day_info['add_time'] = date('Y-m-d H:i:s', time());
            $new_day_info['monitor_id'] = $page_id;
            unset($new_day_info['day_id']);
            unset($new_day_info['update_time']);
            $day_id = $this->H5_Day_info_model->save_day_info($new_day_info);
            if(!$day_id){
                $this->db->trans_rollback();
                echo "<script>alert('复制失败');location.href='".base_url('usadmin/page_monitor')."'</script>";
                return;
            }

            //复制航班
            $where = array(
                'day_id' => $val['day_id']
            );
            $fly_data = $this->H5_Fly_info_model->get_fly_info_list($where);
            foreach($fly_data as $k => $v){
                $new_fly_info = $v;
                $new_fly_info['day_id'] = $day_id;
                $new_fly_info['monitor_id'] = $page_id;
                unset($new_fly_info['fly_id']);
                $fly_id = $this->H5_Fly_info_model->save_fly_info($new_fly_info);
                if(!$fly_id){
                    $this->db->trans_rollback();
                    echo "<script>alert('复制失败');location.href='".base_url('usadmin/page_monitor')."'</script>";
                    return;
                }
            }
        }

        $this->db->trans_complete();
        echo "<script>alert('复制成功');location.href='".base_url('usadmin/page_monitor')."'</script>";
        return;
    }

  

    //行程列表
    public function page_trip($page_id){
        $data['trip_add_url']=base_url('usadmin/page_monitor/page_trip_add/').'/'.$page_id;  
        $data['trip_edit_url']=base_url('usadmin/page_monitor/page_trip_edit/');  
        $data['trip_del_url']=base_url('usadmin/page_monitor/page_trip_del/');  
        $data['trip_fly_url']=base_url('usadmin/page_monitor/page_trip_fly/').'/'.$page_id;  
        $where=array(
            'monitor_id'=>$page_id,
            'is_del'=>0,
            'order_by' => 'day_order asc,day_id asc'
        );
        $data['info']=$this->H5_Day_info_model->get_day_info_list($where);
    
        $this->load->view('page_monitor/page_trip',$data);   
    }
    public  function page_trip_fly($page_id,$day_id){
         $data['page_fly_url']=  base_url('usadmin/page_monitor/page_fly/').'/'.$page_id.'/'.$day_id;
         $data['page_fly_del_url']=base_url('usadmin/page_monitor/page_fly_del');
        $data['page_fly_edit_url']=base_url('usadmin/page_monitor/page_fly_edit');
           $where=array(
            'monitor_id'=>$page_id,
            'day_id'=>$day_id,   
            'order_by' => 'fly_id asc,day_id asc'
        );
           $data['info']=$this->H5_Fly_info_model->get_fly_info_list($where);
       $this->load->view('page_monitor/page_trip_fly',$data); 
    }
    public function page_fly_edit($fly_id){
        $data['fly_update']=base_url('usadmin/page_monitor/fly_update');
        $where=array('fly_id'=>$fly_id);
        $data['info']=$this->H5_Fly_info_model->get_fly_info_detail($where);
 
        $this->load->view('page_monitor/page_fly_insert',$data);
    }
    public function fly_update(){
       $data_c['fly_name']=$this->input->post('aviation',TRUE);
        $data_c['fly_sn']=$this->input->post('flight',TRUE);
        $data_c['fly_start_place']=$this->input->post('out_city',true);
        $data_c['fly_end_place']=$this->input->post('arrive_city',TRUE);
        $data_c['fly_start_airport']=$this->input->post('fly_start_airport',TRUE);
        $data_c['fly_end_airport']=$this->input->post('fly_end_airport',TRUE);
        $data_c['fly_start_time']=strtotime($this->input->post('out_time',true));
        $data_c['fly_end_time']=strtotime($this->input->post('arrive_time',TRUE));
        $data_c['is_next_day'] = $this->input->post('is_next_day',TRUE) || 0;
        $data_c['is_z']=$this->input->post('is_z',true);
        $data_c['time_x']=$this->input->post('flight_time',TRUE);
        $data_c['monitor_id']=$mon_id=$this->input->post('monitor_id',TRUE);
        $data_c['fly_id']=$fly_id=$this->input->post('fly_id',TRUE);
        $data_c['day_id']=$day_id=$this->input->post('day_id',TRUE);
        $fly_c_id = $this->H5_Fly_info_model->save_fly_info($data_c);
        if($fly_c_id){
       		redirect(base_url("usadmin/page_monitor/page_trip_fly/$mon_id/$day_id"));       
        }
    }

    public function page_fly_del($fly_id,$day_id,$monitor_id){
        $where="fly_id=$fly_id";
        $res=$this->User_model->del($where,'v_h5_fly');
        if($res){    
       		redirect(base_url("usadmin/page_monitor/page_trip_fly/$monitor_id/$day_id"));
        }
    }

    public  function  page_fly($page_id,$day_id){
        $data['page_fly_url']=  base_url('usadmin/page_monitor/page_fly_add/').'/'.$page_id.'/'.$day_id;
         $this->load->view('page_monitor/page_fly',$data); 
    }
      public function  page_fly_add($page_id,$day_id){
     

        $data_c['fly_name']=$this->input->post('aviation',TRUE);
        $data_c['fly_sn']=$this->input->post('flight',TRUE);
        $data_c['fly_start_place']=$this->input->post('out_city',true);
        $data_c['fly_end_place']=$this->input->post('arrive_city',TRUE);
        $data_c['fly_start_airport']=$this->input->post('fly_start_airport',TRUE);
        $data_c['fly_end_airport']=$this->input->post('fly_end_airport',TRUE);
        $data_c['fly_start_time']=strtotime($this->input->post('out_time',true));
        $data_c['fly_end_time']=strtotime($this->input->post('arrive_time',TRUE));
        $data_c['is_next_day'] = $this->input->post('is_next_day',TRUE) || 0;
        $data_c['is_z']=$this->input->post('is_z',true);
        $data_c['time_x']=$this->input->post('flight_time',TRUE);
        $data_c['monitor_id']=$page_id;
        $data_c['day_id']=$day_id;
        if(!empty($data_c['fly_name'])){
            $fly_c_id = $this->H5_Fly_info_model->save_fly_info($data_c);
            if(!$fly_c_id){
                echo "<script language=javascript>alert('添加失败');history.back(-1)</script>";
            }
        }
        redirect(base_url("usadmin/page_monitor/page_trip_fly/$page_id/$day_id"));    
    }

    //行程删除
    public  function page_trip_del($page_id,$monitor_id){
        $data['is_del']=1;
        $where="day_id=$page_id";
        $res=$this->User_model->update_one($where,$data,'v_h5_day');
        $day_id=$this->db->insert_id();
       
        if($res){
            redirect(base_url("usadmin/page_monitor/page_trip/$monitor_id"));  
        }
    }

    //行程修改
    public function page_trip_edit($page_id,$monitor_id){
        $where="day_id=$page_id";
        $data['modification_url']=  base_url('usadmin/page_monitor/do_page_trip_edit/').'/'.$page_id.'/'.$monitor_id;
        $data['default_day_details'] = DEAULT_DAY_DETAILS;
        $data['day']=$this->User_model->get_select_one('*',$where,'v_h5_day');
        $data['fly']=$this->User_model->get_select_all($select='*',$where,$order_title='fly_id',$order='ASC',$table='v_h5_fly');
        $data['day']['count']=  json_decode($data['day']['day_count']);
        $this->load->view('page_monitor/page_trip_edit',$data); 
    }

    //行程insert
    public function  do_page_trip_edit($page_id,$monitor_id){
      $cf_id=$this->input->post('cf_id',TRUE);
      $dd_id=$this->input->post('dd_id',TRUE);
      $where=array('day_id'=>$page_id);
      $data_day['day_title']=$this->input->post('title_trip',TRUE);
      $data_day['day_order']=$this->input->post('day',TRUE);    
      $page_info = $this->H5_Day_info_model->get_day_info_detail($where);
      $infot= json_decode($page_info['day_count'], TRUE);
//      echo "<pre>";
//      print_r($infot['day']);
//      echo "</pre>";die;
        if (!$page_info) {
            echo "页面信息未找到";
        }
          if (!empty($infot['day'])) {
            $day = $infot['day'];
        }
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
            $day = $this->upload_image('image1', 'H5image');
        }
//            echo "$day";die;
         $details= $this->input->post('details',TRUE);
         $line=$this->input->post('line',TRUE);
           $main=array(
            'day'=>$day,
            'details'=>$details,
            'line'=>$line
          );
        $data_day['day_count']= json_encode($main);
     
        $this->User_model->update_one(array('day_id'=>$page_id),$data_day,'v_h5_day');
     
      
         redirect(base_url("usadmin/page_monitor/page_trip/$monitor_id"));      
  }

    //行程添加页面
    public function page_trip_add($page_id){
        $data['do_page_trip_url']=base_url('usadmin/page_monitor/do_page_trip_add').'/'.$page_id;
        
        $data['default_day_details'] = DEAULT_DAY_DETAILS;

        $this->load->view('page_monitor/page_trip_add',$data);
    }

    //行程添加
    public function  do_page_trip_add($page_id){
        $data_day['day_title']=$this->input->post('day_title',TRUE);
        $details= $this->input->post('details',TRUE);
        $data_day['day_order']=$this->input->post('day_order',TRUE); 
        $data_day['monitor_id']=$page_id;
        $line=$this->input->post('line',TRUE);
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
            $day = $this->upload_image('image1', 'H5image');
        }
        $main=array(
            'day'=>$day,
            'details'=>$details,
            'line'=>$line    
        );
        $data_day['day_count'] = json_encode($main);
        $data_day['add_time'] = date('Y-m-d H:i:s', time());

        $day_id = $this->H5_Day_info_model->save_day_info($data_day);
        if($day_id){
            redirect(base_url("usadmin/page_monitor/page_trip/$page_id"));   
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
                    default:
                        $br = FALSE;
                }

                if ($br) {
                    $key = md5(rand(1, 99999) . time());
                    $pic_url = "./public/images/H5image/" . $key . $br;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[] = "/public/images/H5image/" . $key . $br;
                }
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
        if(empty($url_data)){
            $base_url = site_url($this->uri->uri_string());
        }else{
            $base_url = site_url($this->uri->uri_string() . '?' . http_build_query($url_data));
        }
        
        $this->pagination->usadmin_page(array('base_url' => $base_url, 'per_page' => $per_page, 'total_rows' => $row['total']));

        $link = $this->pagination->create_links();

        if (!empty($link)) {
            $link = $this->pagination->total_tag_open.$link.$this->pagination->total_tag_close;
        }

        return array('total' => $row['total'], 'link' => $link);
    }

    private function _check_city_name($page_id, $city_name){
        if(!$city_name){
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        if(empty($city_list)){
            return false;
        }
        return true;
    }

    private function _check_package_name($page_id, $city_name, $package_name){
        if(!$package_name){
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'package_name' => $package_name
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        if(empty($package_list)){
            return false;
        }
        return true;
    }

    private function _get_city_list_by_page($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'package_id asc',
            'group_by' => 'city_name'
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        return $city_list;
    }

    private function _get_package_list_by_city($page_id, $city_name){
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'order_by' => 'package_id asc'
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        return $package_list;
    }

    private function _get_new_day_info($day_id, $page_id = false, $package_id = false){
        $where = array(
            'day_id' => $day_id
        );
        $day_data = $this->Day_info_model->get_day_info_detail($where);
        $new_day_info = $day_data;
        if($page_id){
            $new_day_info['page_id'] = $page_id;
        }
        if($package_id){
            $new_day_info['day_package_id'] = $package_id;
        }
        $new_day_info['add_time'] = time();
        unset($new_day_info['day_id']);
        unset($new_day_info['update_time']);
        return $new_day_info;
    }

    //获取页面通用链接
    private function _get_urls(){
        $data['login_out_url'] = base_url('usadmin/business/login_out');
        $data['pwd_edit_url'] = base_url('usadmin/business/pwd_edit');
        $data['change_business_url']= base_url('usadmin/business/change_business');
        $data['package_tour_url'] = base_url('usadmin/package_tour/index');
        $data['free_tour_url'] = base_url('usadmin/free_tour/index');
        return $data;
    }

    //校验登录
    private function _get_auth() {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if(!$business_account_id){
            redirect(base_url('usadmin/business/login'));
        }
        $where = array(
            'business_account_id' => $business_account_id
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if(empty($business_account)){
            redirect(base_url('usadmin/business/login'));
        }
        $business_id = $this->my_session->get_session('business_id', SESSION_KEY_PRE);
        if(!$business_id){
            redirect(base_url('usadmin/business/login'));
        }
        $business_account['business_id'] = $business_id;
        $where = array(
            'business_id' => $business_id
        );
        $business_info = $this->Business_info_model->get_business_info_detail($where);
        $business_account['business_name'] = $business_info['business_name'];
        $business_account['template_name'] = $business_info['template_name'];
        $business_account['share_name'] = $business_info['share_name'];
        return $business_account;
    }

    //获取所有后台商户
    private  function _get_business_all($type=1){
        $where = array(
            'business_type' => $type
        );
        $business_all = $this->Business_info_model->get_business_info_list($where);
        return $business_all;
    }
    //获取商户
    private  function _get_business_detail($business_id){
          $data['business_id']=$business_id;          
        $business_all = $this->Business_info_model->get_business_info_detail($data);
        return $business_all;
    }
    private function _delete_cache($page_id){
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $cache_key = US_PACKAGE_TOUR_CACHE_KEY.$page_id;
        $this->cache->delete($cache_key);
    }

    //获取通用头部左侧菜单
    private function _set_common($data){
        $data['header'] = $this->load->view('usadmin/common/header', $data, true);
        $data['menu'] = $this->load->view('usadmin/common/menu', $data, true);
        //$data['show_count_code'] = $this->show_count_code();
        $data['footer'] = $this->load->view('usadmin/common/footer', $data, true);
        return $data;
    }
    public  function zz(){
        
        $data['list']=$this->input->get('name',true);

        $this->load->view('zz_index/zz_index',$data);
    }
}