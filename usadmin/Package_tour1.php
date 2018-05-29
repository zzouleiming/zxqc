<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
define('DEFAULT_DAY_DETAILS', '<p class="i_food dayTit">餐饮</p><p><br/></p><p class="i_stroke dayTit">行程</p><p><br/></p><p class="i_hotel dayTit">住宿</p><p><br/></p>');
define('US_PACKAGE_TOUR_CACHE_KEY', 'us_package_tour_');
define('SESSION_KEY_PRE', 'us');
define('TRAVEL_FILE_KEY', 'Travel schedule');
define('DEFAULT_CITY_NAME', '__DEFAULT__');
define('DEFAULT_SHOW_CITY_NAME', '无城市');
define('DEFAULT_PACKAGE_NAME', '__DEFAULT__');
define('DEFAULT_SHOW_PACKAGE_NAME', '无套餐');
define('DEFAULT_DAY_ORDER', 999);
define('DEFAULT_TEMPLATE_TYPE', 'normal');
define('SHOW_PAGE_TITLE_LENGTH', 24);
class Package_tour extends MY_Controller
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
  
    //跟团游列表
    public function index() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();

        $data['param'] = array();
        if($this->input->get()){
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        $data['business_all']=$this->_get_business_all();   
        $business_id = $business_account['business_id'];
        $where = array('is_show' => 0,'page_type'=> 1, 'limit' => 20, 'offset' => 0,'order_by' => 'page_id desc');
        if(!$business_account['is_us']){
            $where['business_id'] = $business_id;
        }else{
            if(!empty($data['param']['business_id'])){
                $where['business_id'] = $data['param']['business_id'];
            }
        }

        if($business_account['role_id'] != 1){
            $where['uploader'] = $business_account['business_account'];
        }
        
        if(!empty($data['param']['keyword'])){
            $where['like'] = array('page_title' => $data['param']['keyword']);
        }

        if(!empty($data['param']['upload_date'])){
            $where['add_time >= '] = strtotime($data['param']['upload_date']);
            $where['add_time < '] = strtotime($data['param']['upload_date'])+24*60*60;
        }

        if(!empty($data['param']['per_page'])){
            $where['offset'] = $data['param']['per_page'];
        }
          if(!empty($data['param']['page_id'])){
            $where['page_id'] = $data['param']['page_id'];
        }   
        if(!empty($data['param']['per_total'])){
           $where['limit'] = $data['param']['per_total'];
        }
        $data['page_info_list']= $this->Page_info_model->get_page_info_list($where);
        $data['_pagination']= $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        foreach($data['page_info_list'] as $k=>$v){
            if(mb_strlen($v['page_title']) > SHOW_PAGE_TITLE_LENGTH){
                $data['page_info_list'][$k]['show_page_title']= mb_substr($v['page_title'], 0, SHOW_PAGE_TITLE_LENGTH).'...';
            }else{
                $data['page_info_list'][$k]['show_page_title']= $v['page_title'];
            }
            $data['page_info_list'][$k]['image_data'] = json_decode($v['image_data'], true); 
            $data['page_info_list'][$k]['image_data']['top'] = $data['page_info_list'][$k]['image_data']['top'] ? $data['page_info_list'][$k]['image_data']['top'] : base_url('public/images/usadmin/no_head.jpg');
        }
        $data['page_add_url'] = base_url('usadmin/package_tour/page_add');
        $data['page_prev_url'] = base_url('home/package_tour/view');
        $data['page_del_url'] = base_url('usadmin/package_tour/page_del');
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $data['page_copy_url'] = base_url('usadmin/package_tour/page_copy');
        $data['page_price'] = base_url('usadmin/Page_monitor/page_price');
        $data['business_account'] = $business_account;

        $this->load->view('usadmin/package_tour/index', $this->_set_common($data));
    }
  
    public function page_price($page_id){
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
        $city_name = $this->input->get('city_name', true);
        $package_name = $this->input->get('package_name', true);
        $data['city_list'] = $this->_get_city_list_by_page($page_id);
        foreach($data['city_list'] as $key => $val){
            if($val['city_name'] == DEFAULT_CITY_NAME){
                $data['city_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            }else{
                $data['city_list'][$key]['show_city_name'] = $val['city_name'];
            }
        }
        if(!$this->_check_city_name($page_id, $city_name)){
            $city_name = $data['city_list'][0]['city_name'];
        }
        $data['package_list'] = $this->_get_package_list_by_city($page_id, $city_name);
        foreach($data['package_list'] as $key => $val){
            if($val['package_name'] == DEFAULT_PACKAGE_NAME){
                $data['package_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            }else{
                $data['package_list'][$key]['show_package_name'] = $val['package_name'];
            }
        }
        if(!$this->_check_package_name($page_id, $city_name, $package_name)){
            $package_name = $data['package_list'][0]['package_name'];
        }
        
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'package_name' => $package_name
        );
        $package_info = $this->Package_info_model->get_package_detail($where);
        $package_id = $package_info['package_id'];
        $time = strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend = strtotime('+11 month', $time);
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

        $data['page_price_update_url']=  base_url('usadmin/package_tour/page_price_upload/').'/'.$page_id;
        $data['package_info'] = $package_info;
        $data['city_name'] = $city_name;
        $data['package_name'] = $package_name;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit').'/'.$page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price').'/'.$page_id;
        $date_price = json_decode($data['package_info']['date_price'],true);
        $data['remarks'] = $date_price['remarks'];
        $data['date_choose'] =  $date_price ? $date_price['date'] : array();
        $this->load->view('usadmin/package_tour/page_price',$this->_set_common($data));
    }

    public function page_price_upload(){
        $business_account = $this->_get_auth();

        $date_price=$this->input->post('date_price',TRUE);
        $date_val=$this->input->post('date_val',TRUE);  
        $remarks=$this->input->post('prices',TRUE);
        $data['package_id']=$this->input->post('package_id',TRUE);  
        $where=array('package_id'=>$data['package_id']);
        $package=$this->Package_info_model->get_package_detail($where);
        $page_id=$package['page_id'];

        if (count($date_val) >= 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                if(empty($date_val[$k]) || empty($date_price[$k])){
                    continue;
                }
                $new_date[$v] = $date_price[$k];
            }
            $date1 = array(
                'date' => $new_date,
                'remarks'=>$remarks
            );
            $data['date_price'] = json_encode($date1);
        }

        $this->_delete_cache($package['page_id']);

        $res = $this->Package_info_model->save_package_info($data);
        if($res){
            redirect(base_url("usadmin/package_tour/page_price").'/'.$package['page_id'].'?city_name='.urlencode($package['city_name']).'&package_name='.  urlencode($package['package_name']));    
        }  
    }

    //跟团游增加页面
    public function page_add() {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        
        $data['business_all']=$this->_get_business_all();   
        $data['business_account'] = $business_account;
        $data['do_page_add_url'] = base_url('usadmin/package_tour/do_page_add');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $this->load->view('usadmin/package_tour/'.$business_account['template_name'].'/page_add',$this->_set_common($data));   
    }

    //跟团游增加
    public function do_page_add() {
        $business_account = $this->_get_auth();

        $data['business_id'] = $business_account['business_id'];
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
        $data['page_type'] = 1;
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
        $data['template_type'] = $business_account['template_name'];
        $data['add_time']=time();
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
            echo "<script language=javascript>alert('保存成功');location.href='" .base_url("usadmin/package_tour/page_trip/").'/'.$page_id . "';</script>";
        }
    }

    // 跟团游编辑页面
    public function  page_edit($page_id){
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        
        $data['business_all']=$this->_get_business_all();   
        $data['business_account'] = $business_account;
        $where = array(
            'page_id'=>$page_id
        ); 
        $data['info'] = $this->Page_info_model->get_page_info_detail($where); 
        $data['info']['image_data'] = json_decode($data['info']['image_data'], true);
        $data['info']['kf_data'] = json_decode($data['info']['kf_data'], true);
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit').'/'.$page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price').'/'.$page_id;
		$data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;
        $data['do_page_edit_url'] = base_url('usadmin/package_tour/do_page_edit');
        $data['business_name']=$this->_get_business_detail($data['info']['business_id']);
        $this->load->view('usadmin/package_tour/page_edit',$this->_set_common($data));
    }

    //跟团游编辑
    public function do_page_edit() {
        $business_account = $this->_get_auth();

        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['short_title'] = $this->input->post('short_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['inst_data'] = $this->input->post('inst_data', TRUE);
        $kf_type = $this->input->post('kf_type',TRUE);
        $user_mobile = $this->input->post('user_mobile',TRUE); 
        $style_type = $this->input->post('style_type',TRUE);
        if($style_type){
            $data['style_type'] = $style_type;
        }   
        $image_data = array();
        
        if (isset($_FILES['image_top']) && $_FILES['image_top']['error'] == 0) {
            $image_data['top'] = $this->upload_image('image_top', 'H5image');
        }else{
            $image_data['top'] = $this->input->post('image1',TRUE);
        }
        if (isset($_FILES['image_spec']) && $_FILES['image_spec']['error'] == 0) {
            $image_data['spec'] = $this->upload_image('image_spec', 'H5image');
        }else{
            $image_data['spec'] =$this->input->post('image2',TRUE);
        }
        if (isset($_FILES['image_share']) && $_FILES['image_share']['error'] == 0) {
            $image_data['share'] = $this->upload_image('image_share', 'H5image');
        }else{
            $image_data['share'] = $this->input->post('image3',TRUE);
        }
        if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == 0) {
            $image_data['fileupload'] = $this->upload_files('fileupload', $data['page_id'], TRAVEL_FILE_KEY);
        }else{
            $image_data['fileupload'] =$this->input->post('image4',TRUE);
        }
        if (isset($_FILES['image_wx']) && $_FILES['image_wx']['error'] == 0) {
            $image_wx = $this->upload_image('image_wx', 'H5image');
        }else{
            $image_wx= $this->input->post('image5',TRUE);
        }
        
        $data['image_data'] = json_encode($image_data);
    
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
          
        $data['kf_data'] = json_encode($kf_data);
        $data['update_time'] = time();
        $this->Page_info_model->save_page_info($data);
        $this->_delete_cache($data['page_id']);    
        echo "<script language=javascript>alert('编辑成功');location.href='" .base_url("usadmin/package_tour/page_trip/").'/'.$data['page_id'] . "';</script>";
    }

    public function page_poster(){
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        
        $data['business_all']=$this->_get_business_all(); 

        $data = array();
        $this->load->view('usadmin/package_tour/page_poster',$this->_set_common($data));
    }

    //跟团游删除
    public function page_del() {
        $this->_get_auth();

        $page_id = $this->input->post('page_id', true);
        $page_data = array(
            'page_id' => $page_id,
            'is_show' => 1
        );

        //删除页面
        $res = $this->Page_info_model->save_page_info($page_data);
        $result = array();
        if (!$res){
            $result['code'] = 1;
            $result['msg'] = "删除失败";
            return $this->ajax_return($result);
        }
        //删除套餐
        $where = array(
            'page_id' => $page_id
        );
        $data = array(
            'is_del' => 1
        );
        $this->Package_info_model->update($where, $data);
        //删除行程
        $where = array(
            'page_id' => $page_id
        );
        $data = array(
            'is_del' => 1
        );
        $this->Day_info_model->update($where, $data);
        
        $this->_delete_cache($page_id);

        $result['code'] = 0;
        $result['msg'] = "删除成功";
        return $this->ajax_return($result);
    }
    
    //跟团游页面复制(复制页面基本信息以及行程)
    public function page_copy(){
        $this->_get_auth();

        $result = array();
        $page_id = $this->input->post('page_id', true);
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
            $result['code'] = 1;
            $result['msg'] = "复制失败";
            return $this->ajax_return($result);
        }

        $where = array(
            'page_id' =>$old_page_info['page_id'],
            'is_del' => 0
        );

        $package_data = $this->Package_info_model->get_package_list($where);
        //复制页面下的所有套餐
        foreach ($package_data as $key => $val) {
            $new_package_info = $val;
            $new_package_info['add_time'] = time();
            $new_package_info['page_id'] = $page_id;
            unset($new_package_info['package_id']);
            unset($new_package_info['update_time']);
            $package_id = $this->Package_info_model->save_package_info($new_package_info);
            if (!$package_id) {
                $this->db->trans_rollback();
                $result['code'] = 1;
                $result['msg'] = "复制失败";
                return $this->ajax_return($result);
            }
            //复制套餐下面的所有行程
            $where = array(
                'day_package_id' => $val['package_id'],
                'is_del' => 0
            );
            $day_data = $this->Day_info_model->get_day_info_list($where); 
            foreach ($day_data as $k => $v) {
                $new_day_info = $this->_get_new_day_info($v['day_id'], $page_id, $package_id);
                $day_id = $this->Day_info_model->save_day_info($new_day_info);
                if (!$day_id) {
                    $this->db->trans_rollback();
                    $result['code'] = 1;
                    $result['msg'] = "复制失败";
                    return $this->ajax_return($result);
                }     
            }
        }
        
        $this->db->trans_complete();
        $result['code'] = 0;
        $result['msg'] = "复制成功";
        return $this->ajax_return($result);
    }

    //行程排序
    public function day_sort(){
        $this->_get_auth();

        $day_ids = $this->input->post('day_ids', true);
        $day_orders = $this->input->post('day_orders', true);
        foreach($day_ids as $key => $val){
            $day_data = array(
                'day_id' => $val,
                'day_order' => $day_orders[$key] ? $day_orders[$key] : DEFAULT_DAY_ORDER
            );
            $this->Day_info_model->save_day_info($day_data);
        }

        $where = array(
            'day_id' => $day_ids[0],
            'is_del' => 0
        );
        $day_info = $this->Day_info_model->get_day_info_detail($where);
        $this->_delete_cache($day_info['page_id']);

        $result['code'] = 0;
        $result['msg'] = "操作成功";
        return $this->ajax_return($result);
    }

    //删除行程（多个）
    public function day_del(){
        $this->_get_auth();

        $day_ids = $this->input->post('day_ids', true);
        $where = array(
            'where_in' => array('day_id' => $day_ids)
        );
        $data = array(
            'is_del' => 1
        );
        $this->Day_info_model->update($where, $data);

        $where = array(
            'day_id' => $day_ids[0]
        );
        $day_info = $this->Day_info_model->get_day_info_detail($where);
        $this->_delete_cache($day_info['page_id']);

        $result['code'] = 0;
        $result['msg'] = "操作成功";
        return $this->ajax_return($result);
    }
    
    //复制多个行程
    public function day_copy() {
        $this->_get_auth();

        $day_ids = $this->input->post('day_ids', true);
        $package_id = $this->input->post('package_id', true);
        $result = array();
        if(!empty($day_ids)){
            $this->db->trans_start();
            foreach($day_ids as $key => $val){
                $new_day_info = $this->_get_new_day_info($val, false, $package_id);
                $ret = $this->Day_info_model->save_day_info($new_day_info);
                if (!$ret) {
                    $this->db->trans_rollback();
                    $result['code'] = 1;
                    $result['msg'] = '操作失败';
                    return $this->ajax_return($result);
                }
            }

            $where = array(
                'day_id' => $day_ids[0],
                'is_del' => 0
            );
            $day_info = $this->Day_info_model->get_day_info_detail($where);
            $this->_delete_cache($day_info['page_id']);

            $this->db->trans_complete();
            $result['code'] = 0;
            $result['msg'] = '操作成功';
            return $this->ajax_return($result);
        }
    }

   // 移动多个行程
   public function day_move() {
        $this->_get_auth();

        $day_ids = $this->input->post('day_ids', TRUE);
        $package_id = $this->input->post('package_id', true);
        
        //多个移动
        if(!empty($day_ids)){
            $day_ids = $this->input->post('day_ids', true);
            $where = array(
                'where_in' => array('day_id' => $day_ids)
            );
            $data = array(
                'day_package_id' => $package_id
            );
            $this->Day_info_model->update($where, $data);

            $where = array(
                'day_id' => $day_ids[0],
                'is_del' => 0
            );
            $day_info = $this->Day_info_model->get_day_info_detail($where);
            $this->_delete_cache($day_info['page_id']);

            $result['code'] = 0;
            $result['msg'] = '操作成功';
            return $this->ajax_return($result);
        }
    }

    // 套餐保存
    public  function package_add(){
        $this->_get_auth();

        $data['page_id'] = $this->input->post('page_id', true);
        $data['city_name'] =  $this->input->post('city_name', true);
        $data['package_name'] = $this->input->post('package_name', true);
        if(!$data['city_name']){
            $result['code'] = 1;
            $result['msg'] = "出发城市为空";
            return $this->ajax_return($result);
        }
        if(!$data['package_name']){
            $data['package_name'] = DEFAULT_PACKAGE_NAME;
        }
        $data['add_time'] = time();
       
        $package_id = $this->Package_info_model->save_package_info($data);
        if($package_id){
            $this->_delete_cache($data['page_id']);

            $result['code'] = 0;
            $result['package_id'] = $package_id;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
         }else{
            $result['code'] = 1;
            $result['msg'] = "操作失败";
            return $this->ajax_return($result); 
         }
     }

     public function package_rename(){
        $this->_get_auth();

        $package_id = $this->input->post('package_id', true);
        $package_name = $this->input->post('package_name', true);
        if($package_id){
            $data = array(
                'package_id' => $package_id,
                'package_name' => $package_name
            );
            $this->Package_info_model->save_package_info($data);

            $where = array(
                'package_id' => $package_id,
                'is_del' => 0
            );
            $package_info = $this->Package_info_model->get_package_detail($where);
            $this->_delete_cache($package_info['page_id']);

            $result['code'] = 0;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
        }

        $page_id = $this->input->post('page_id', true);
        $city_name = $this->input->post('city_name', true);
        $old_city_name = $this->input->post('old_city_name', true);
        if($city_name){
            $where = array(
                'page_id' => $page_id,
                'city_name' => $old_city_name
            );
            $data = array(
                'city_name' => $city_name
            );
            $this->Package_info_model->update($where, $data);
            $this->_delete_cache($page_id);

            $result['code'] = 0;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
        }
     }
    
     // 套餐删除
     public  function package_del(){
        $this->_get_auth();

        $package_id = $this->input->post('package_id', true);
        if($package_id){
            $data['package_id'] = $this->input->post('package_id');
            $data['is_del'] = 1;
            $data['update_time'] = time();
           
            $this->Package_info_model->save_package_info($data);
            $where = array(
                'day_package_id' => $package_id
            );
            $data = array(
                'is_del' => 1
            );
            $this->Day_info_model->update($where, $data);

            $where = array(
                'package_id' => $package_id
            );
            $package_info = $this->Package_info_model->get_package_detail($where);
            $this->_delete_cache($package_info['page_id']);

            $result['code'] = 0;
            $result['msg'] = "删除成功";
            return $this->ajax_return($result);
        }

        $page_id = $this->input->post('page_id', true);
        $city_name = $this->input->post('city_name', true);
        if($city_name){
            $where = array(
                'page_id' => $page_id,
                'city_name' => $city_name
            );
            $data = array(
                'is_del' => 1
            );
            $this->Package_info_model->update($where, $data);
            $package_list = $this->Package_info_model->get_package_list($where);
            $day_package_ids = array();
            foreach($package_list as $key => $val){
                $day_package_ids[] = $val['package_id'];
            }
            $where = array(
                'where_in' => array('day_package_id', $day_package_ids)
            );
            $data = array(
                'is_del' => 1
            );
            $this->Day_info_model->update($where, $data);

            $this->_delete_cache($page_id);

            $result['code'] = 0;
            $result['msg'] = "删除成功";
            return $this->ajax_return($result);
        }
     }

    //套餐列表
    public function package_list(){
        $this->_get_auth();

        $page_id = $this->input->post('page_id', true);
        $city_name = $this->input->post('city_name', true);
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'order_by' => 'package_id asc'
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        foreach($package_list as $key => $val){
            if($val['package_name'] == DEFAULT_PACKAGE_NAME){
                $package_list[$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            }else{
                $package_list[$key]['show_package_name'] = $val['package_name'];
            }
        }
        $result = array(
            'code' => 0,
            'data' => $package_list
        );
        return $this->ajax_return($result);
    }

   //上传地图
    public function package_map_upload(){
        $this->_get_auth();

        $package_id = $this->input->post('package_id', true);
        $back_url = $this->input->post('back_url', true);

        $data['package_id'] = $package_id;
        if (isset($_FILES['package_map']) && $_FILES['package_map']['error'] == 0) {
            $data['package_map'] = $this->upload_image('package_map', 'H5image');
        }
        $this->Package_info_model->save_package_info($data);
        $where = array(
            'package_id' => $package_id,
            'is_del' => 0
        );
        $package_info = $this->Package_info_model->get_package_detail($where);
        $this->_delete_cache($package_info['page_id']);

        redirect($back_url);
    }

     //行程列表
    public function page_trip($page_id){
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();

        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
 
        $city_name = $this->input->get('city_name', true);
        $package_name = $this->input->get('package_name', true);

        $data['city_list'] = $this->_get_city_list_by_page($page_id);
        foreach($data['city_list'] as $key => $val){
            if($val['city_name'] == DEFAULT_CITY_NAME){
                $data['city_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            }else{
                $data['city_list'][$key]['show_city_name'] = $val['city_name'];
            }
        }
        if(!$this->_check_city_name($page_id, $city_name)){
            $city_name = $data['city_list'][0]['city_name'];
        }

        $data['package_list'] = $this->_get_package_list_by_city($page_id, $city_name);
        foreach($data['package_list'] as $key => $val){
            if($val['package_name'] == DEFAULT_PACKAGE_NAME){
                $data['package_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            }else{
                $data['package_list'][$key]['show_package_name'] = $val['package_name'];
            }
        }
        if(!$this->_check_package_name($page_id, $city_name, $package_name)){
            $package_name = $data['package_list'][0]['package_name'];
        }
        
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'package_name' => $package_name
        );
        $package_info = $this->Package_info_model->get_package_detail($where);
        $package_id = $package_info['package_id'];
        $where = array(
            'a.page_id' => $page_id,
            'a.is_del' => 0,
            'a.day_package_id' => $package_id,
            'order_by' => 'a.day_order asc, a.day_id asc'
        );    
        $data['trip_list'] = $this->Day_info_model->get_day_info_package_list($where);
        foreach($data['trip_list'] as $key => $val){
            if($val['city_name'] == DEFAULT_CITY_NAME){
                $data['trip_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            }else{
                $data['trip_list'][$key]['show_city_name'] = $val['city_name'];
            }
            if($val['package_name'] == DEFAULT_PACKAGE_NAME){
                $data['trip_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            }else{
                $data['trip_list'][$key]['show_package_name'] = $val['package_name'];
            }
            $data['trip_list'][$key]['prev_url'] = '';
            $data['trip_list'][$key]['edit_url'] = base_url('usadmin/package_tour/page_trip_edit').'/'.$val['day_id'];
            $data['trip_list'][$key]['del_url'] = base_url('usadmin/package_tour/page_trip_del').'/'.$val['day_id'];;
        }
        $data['page_id'] = $page_id;
        $data['city_name'] = $city_name;
        $data['package_name'] = $package_name;
        $data['package_info'] = $package_info;
        $data['day_sort_url'] = base_url('usadmin/package_tour/day_sort');
        $data['day_del_url'] = base_url('usadmin/package_tour/day_del');
        $data['day_copy_url'] = base_url('usadmin/package_tour/day_copy');
        $data['day_move_url'] = base_url('usadmin/package_tour/day_move');
        $data['package_add_url'] = base_url('usadmin/package_tour/package_add');
        $data['package_rename_url'] = base_url('usadmin/package_tour/package_rename');
        $data['package_list_url'] = base_url('usadmin/package_tour/package_list');
        $data['package_del_url'] = base_url('usadmin/package_tour/package_del');
        $data['package_map_upload_url'] = base_url('usadmin/package_tour/package_map_upload');
        $data['page_trip_add_url'] = base_url('usadmin/package_tour/page_trip_add').'/'.$page_id.'/'.$package_id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit').'/'.$page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price').'/'.$page_id;
        $this->load->view('usadmin/package_tour/'.$business_account['template_name'].'/page_trip',$this->_set_common($data));   
    }
    
    //行程删除
    public  function page_trip_del($day_id){
        $this->_get_auth();

        $data_day = array(
            'day_id' => $day_id,
            'is_del' => 1
        );
        $this->Day_info_model->save_day_info($data_day);
        $where = array(
            'day_id' => $day_id
        );
        $day_info = $this->Day_info_model->get_day_info_detail($where);

        $this->_delete_cache($day_info['page_id']);

        $url = base_url('usadmin/package_tour/page_trip').'/'.$day_info['page_id'];
        if($day_info['day_package_id']){
            $url .= '/'.$day_info['day_package_id'];
        }
        redirect($url);
    }

    //行程修改
    public function page_trip_edit($day_id){
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();

        $where = array(
            'day_id' => $day_id,
            'is_del' => 0
        );
        $data['day_info'] = $this->Day_info_model->get_day_info_detail($where);
        $data['day_info']['day_view'] = json_decode($data['day_info']['day_view'], true);
        $data['day_info']['day_view_one'] = array();
        $data['day_info']['day_view_two'] = array();
        foreach($data['day_info']['day_view'] as $key => $val){
            if($key < 5){
                $data['day_info']['day_view_one'][] = $val;
            }else{
                $data['day_info']['day_view_two'][] = $val;
            }
        }
        $data['day_info']['day_view_one'] = array_pad($data['day_info']['day_view_one'], 5, '');
        $data['day_info']['day_view_two'] = array_reverse(array_pad($data['day_info']['day_view_two'], 5, ''));
        $data['day_info']['day_fly_data'] = json_decode($data['day_info']['day_fly_data'], true);
        $data['search_fly_url'] = base_url('usadmin/common/search_fly');
        $data['do_page_trip_edit_url']=  base_url('usadmin/package_tour/do_page_trip_edit');
        $data['default_day_details'] = DEFAULT_DAY_DETAILS;
        $data['page_edit_url']=  base_url('usadmin/package_tour/page_edit');
        $this->load->view('usadmin/package_tour/page_trip_edit', $this->_set_common($data)); 
    }

    //行程insert
    public function do_page_trip_edit() {
        $this->_get_auth();

        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach($day_view_one as $key => $val){
            if($val){
                $day_view[] = $val;
            }
        }
        foreach($day_view_two as $key => $val){
            if($val){
                $day_view[] = $val;
            }
        }
        $data_day['day_id'] = $this->input->post('day_id', TRUE);
        $data_day['day_title'] = $this->input->post('day_title', TRUE);
        $data_day['day_view'] = json_encode($day_view);
        $data_day['day_content'] = $this->input->post('day_content', TRUE);
        if (isset($_FILES['day_image']) && $_FILES['day_image']['error'] == 0) {
            $data_day['day_image'] = $this->upload_image('day_image', 'H5image');
        }
        $data_day['update_time'] = time();

        $fly_id = $this->input->post('fly_id', TRUE);
        $fly_sn = $this->input->post('fly_sn', TRUE);
        $fly_company_name = $this->input->post('fly_company_name', TRUE);
        $fly_start_place = $this->input->post('fly_start_place', TRUE);
        $fly_start_airport = $this->input->post('fly_start_airport', TRUE);
        $fly_start_time = $this->input->post('fly_start_time', TRUE);
        $fly_end_place = $this->input->post('fly_end_place', TRUE);
        $fly_end_airport = $this->input->post('fly_end_airport', TRUE);
        $fly_end_time = $this->input->post('fly_end_time', TRUE);
        $time_x = $this->input->post('time_x', TRUE);
        $day_x = $this->input->post('day_x', TRUE);

        $this->db->trans_start();
        $day_fly_data = array();
        foreach($fly_sn as $key => $val){
            if(!$val){
                continue;
            }
            $fly_data = array(
                'fly_sn' => $fly_sn[$key],
                'fly_company_name' => $fly_company_name[$key],
                'fly_start_place' => $fly_start_place[$key],
                'fly_start_airport' => $fly_start_airport[$key],
                'fly_start_time' => strtotime($fly_start_time[$key]),
                'fly_end_place' => $fly_end_place[$key],
                'fly_end_airport' => $fly_end_airport[$key],
                'fly_end_time' => strtotime($fly_end_time[$key]),
                'time_x' => $time_x[$key] ? $time_x[$key] : 0,
                'day_x' => $day_x[$key] ? $day_x[$key] : 0
            );
            if($fly_id[$key]){
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            }else{
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if(!empty($ret)){
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                }else{
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            if(!$ret){
                $this->db->trans_rollback();
                echo "<script>alert('添加失败');history.go(-1)</script>";
                return;
            }
            if(!$fly_data['fly_id']){
                $fly_data['fly_id'] = $ret;
                unset($fly_data['add_time']);
            }else{
                unset($fly_data['update_time']);
            }
            $day_fly_data[] = $fly_data;
        }
        $data_day['day_fly_data'] = json_encode($day_fly_data);
        $ret = $this->Day_info_model->save_day_info($data_day);
        if (!$ret) {
            $this->db->trans_rollback();
            echo "<script>alert('添加失败');history.go(-1)</script>";
            return;
        }

        $this->db->trans_complete();

        $where = array(
            'day_id' => $data_day['day_id']
        );
        $day_info = $this->Day_info_model->get_day_info_detail($where);

        $this->_delete_cache($day_info['page_id']);

        $where = array(
            'package_id' => $day_info['day_package_id'],
            'is_del' => 0
        );
        $package_info = $this->Package_info_model->get_package_detail($where);

        $url = base_url("usadmin/package_tour/page_trip").'/'.$day_info['page_id'].'?city_name='.urlencode($package_info['city_name']).'&package_name='.urlencode($package_info['package_name']);
        redirect($url);
    }

    //行程添加页面
    public function page_trip_add($page_id, $package_id) {
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();

        $data['page_id'] = $page_id;
        $data['package_id'] = $package_id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit').'/'.$page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price').'/'.$page_id;
        $data['search_fly_url'] = base_url('usadmin/common/search_fly');
        $data['do_page_trip_add_url'] = base_url('usadmin/package_tour/do_page_trip_add');
        $data['default_day_details'] = DEFAULT_DAY_DETAILS;
        $this->load->view('usadmin/package_tour/page_trip_add', $this->_set_common($data));
    }
    //行程添加
    public function do_page_trip_add() {
        $this->_get_auth();

        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach($day_view_one as $key => $val){
            if($val){
                $day_view[] = $val;
            }
        }
        foreach($day_view_two as $key => $val){
            if($val){
                $day_view[] = $val;
            }
        }
        $data_day['page_id'] = $this->input->post('page_id', TRUE);
        $data_day['day_package_id'] = $this->input->post('package_id', TRUE);
        $data_day['day_title'] = $this->input->post('day_title', TRUE);
        $data_day['day_view'] = json_encode($day_view);
        $data_day['day_content'] = $this->input->post('day_content', TRUE);
        if (isset($_FILES['day_image']) && $_FILES['day_image']['error'] == 0) {
            $data_day['day_image'] = $this->upload_image('day_image', 'H5image');
        }
        $data_day['add_time'] = time();

        $fly_id = $this->input->post('fly_id', TRUE);
        $fly_sn = $this->input->post('fly_sn', TRUE);
        $fly_company_name = $this->input->post('fly_company_name', TRUE);
        $fly_start_place = $this->input->post('fly_start_place', TRUE);
        $fly_start_airport = $this->input->post('fly_start_airport', TRUE);
        $fly_start_time = $this->input->post('fly_start_time', TRUE);
        $fly_end_place = $this->input->post('fly_end_place', TRUE);
        $fly_end_airport = $this->input->post('fly_end_airport', TRUE);
        $fly_end_time = $this->input->post('fly_end_time', TRUE);
        $time_x = $this->input->post('time_x', TRUE);
        $day_x = $this->input->post('day_x', TRUE);

        $this->db->trans_start();
        $day_fly_data = array();
        foreach($fly_sn as $key => $val){
            if(!$val){
                continue;
            }
            $fly_data = array(
                'fly_sn' => $fly_sn[$key],
                'fly_company_name' => $fly_company_name[$key],
                'fly_start_place' => $fly_start_place[$key],
                'fly_start_airport' => $fly_start_airport[$key],
                'fly_start_time' => strtotime($fly_start_time[$key]),
                'fly_end_place' => $fly_end_place[$key],
                'fly_end_airport' => $fly_end_airport[$key],
                'fly_end_time' => strtotime($fly_end_time[$key]),
                'time_x' => $time_x[$key] ? $time_x[$key] : 0,
                'day_x' => $day_x[$key] ? $day_x[$key] : 0
            );
            if($fly_id[$key]){
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            }else{
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if(!empty($ret)){
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                }else{
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            if(!$ret){
                $this->db->trans_rollback();
                echo "<script>alert('添加失败');history.go(-1)</script>";
                return;
            }
            if(!$fly_data['fly_id']){
                $fly_data['fly_id'] = $ret;
                unset($fly_data['add_time']);
            }else{
                unset($fly_data['update_time']);
            }
            $day_fly_data[] = $fly_data;
        }
        $data_day['day_fly_data'] = json_encode($day_fly_data);
        $day_id = $this->Day_info_model->save_day_info($data_day);
        if (!$day_id) {
            $this->db->trans_rollback();
            echo "<script>alert('添加失败');history.go(-1)</script>";
            return;
        }

        $this->db->trans_complete();

        $this->_delete_cache($data_day['page_id']);

        $where = array(
            'package_id' => $data_day['day_package_id'],
            'is_del' => 0
        );
        $package_info = $this->Package_info_model->get_package_detail($where);

        $url = base_url("usadmin/package_tour/page_trip").'/'.$data_day['page_id'].'?city_name='.urlencode($package_info['city_name']).'&package_name='.urlencode($package_info['package_name']);
        redirect($url);
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
}