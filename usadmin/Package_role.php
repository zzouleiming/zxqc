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

class Package_role extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('us/Page_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Company_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Package_hotel_info_model');
        $this->load->model('us/Pro_info_model');
        $this->load->model('us/Pro_navigation_model');
        $this->load->model('us/Hotel_package_model');
        $this->load->model('us/Cate_info_model');
        $this->load->model('Business_info_model');
        $this->load->model('Business_account_model');
         $this->load->model('tl/Page_access_model');
        $this->load->model('tl/Page_group_model');
        $this->load->model('tl/Page_role_model');
        $this->load->model('tl/Page_user_model');
        $this->load->model('tl/Page_register_model');
        $this->load->model('us/Page_info_model');

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
        $access = $this->_is_access();
       
       $data['menu_list']= $this->_get_menu();
        $data['access_list'] = $access['access_list'] ;
        
        $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        $data['business_all'] = $this->_get_business_all();
        $business_id = $business_account['business_id'];
        $where = array('is_show' => 0, 'page_type' => 1, 'limit' => 20, 'offset' => 0, 'order_by' => 'page_id desc');
        if (!$business_account['is_us']) {
            $where['business_id'] = $business_id;
        } else {
            if (!empty($data['param']['business_id'])) {
                $where['business_id'] = $data['param']['business_id'];
            }
        }
                 if(!in_array('2007', $data['access_list'])){
                    $where['uploader'] = $business_account['business_account']; 
                }    
//        if ($business_account['role_id'] != 1) {
//            $where['uploader'] = $business_account['business_account'];
//        }

        if (!empty($data['param']['keyword'])) {
            $where['like'] = array('page_title' => $data['param']['keyword']);
        }

        if (!empty($data['param']['upload_date'])) {
            $where['add_time >= '] = strtotime($data['param']['upload_date']);
            $where['add_time < '] = strtotime($data['param']['upload_date']) + 24 * 60 * 60;
        }

        if (!empty($data['param']['per_page'])) {
            $where['offset'] = $data['param']['per_page'];
        }
        if (!empty($data['param']['page_id'])) {
            $where['page_id'] = $data['param']['page_id'];
        }
        if (!empty($data['param']['per_total'])) {
            $where['limit'] = $data['param']['per_total'];
        }
        $data['page_info_list'] = $this->Page_info_model->get_page_info_list($where);
        $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        foreach ($data['page_info_list'] as $k => $v) {
            if (mb_strlen($v['page_title']) > SHOW_PAGE_TITLE_LENGTH) {
                $data['page_info_list'][$k]['show_page_title'] = mb_substr($v['page_title'], 0, SHOW_PAGE_TITLE_LENGTH) . '...';
            } else {
                $data['page_info_list'][$k]['show_page_title'] = $v['page_title'];
            }
            $data['page_info_list'][$k]['image_data'] = json_decode($v['image_data'], true);
            $data['page_info_list'][$k]['image_data']['top'] = $data['page_info_list'][$k]['image_data']['top'] ? $data['page_info_list'][$k]['image_data']['top'] : base_url('public/images/usadmin/no_head.jpg');
        }
             $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
        $data['page_add_url'] = base_url('usadmin/package_tour/page_add');
        $data['page_prev_url'] = base_url('home/package_tour/view');
        $data['page_del_url'] = base_url('usadmin/package_tour/page_del');
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $data['page_copy_url'] = base_url('usadmin/package_tour/page_copy');
        $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $data['business_account'] = $business_account;
        $this->load->view('usadmin/package_tour/index', $this->_set_common($data));
    }

    //自由行页面
    public function page_monitor_index() {
      
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
            
  $data['menu_list']= $this->_get_menu();
        $data['param'] = array();
        if ($this->input->get()) {
            $gets = $this->input->get();
            $data['param'] = filter_empty($gets);
        }
        $data['business_all'] = $this->_get_business_all();
        $business_id = $business_account['business_id'];
        $where = array('is_show' => 0, 'page_type' => 2, 'limit' => 20, 'offset' => 0, 'order_by' => 'page_id desc');
        if (!$business_account['is_us']) {
            $where['business_id'] = $business_id;
        } else {
            if (!empty($data['param']['business_id'])) {
                $where['business_id'] = $data['param']['business_id'];
            }
        }
                if(!in_array('2007', $data['access_list'])){
                    $where['uploader'] = $business_account['business_account']; 
                }
//        if ($business_account['role_id'] != 1) {
//            $where['uploader'] = $business_account['business_account'];
//        }

        if (!empty($data['param']['keyword'])) {
            $where['like'] = array('page_title' => $data['param']['keyword']);
        }

        if (!empty($data['param']['upload_date'])) {
            $where['add_time >= '] = strtotime($data['param']['upload_date']);
            $where['add_time < '] = strtotime($data['param']['upload_date']) + 24 * 60 * 60;
        }

        if (!empty($data['param']['per_page'])) {
            $where['offset'] = $data['param']['per_page'];
        }
        if (!empty($data['param']['page_id'])) {
            $where['page_id'] = $data['param']['page_id'];
        }
        if (!empty($data['param']['per_total'])) {
            $where['limit'] = $data['param']['per_total'];
        }
        $data['page_info_list'] = $this->Page_info_model->get_page_info_list($where);
        $data['_pagination'] = $this->_pagination($this->db->last_query(), $where['limit'], $data['param']);
        foreach ($data['page_info_list'] as $k => $v) {
            if (mb_strlen($v['page_title']) > SHOW_PAGE_TITLE_LENGTH) {
                $data['page_info_list'][$k]['show_page_title'] = mb_substr($v['page_title'], 0, SHOW_PAGE_TITLE_LENGTH) . '...';
            } else {
                $data['page_info_list'][$k]['show_page_title'] = $v['page_title'];
            }
            $data['page_info_list'][$k]['image_data'] = json_decode($v['image_data'], true);
            $data['page_info_list'][$k]['image_data']['top'] = $data['page_info_list'][$k]['image_data']['top'] ? $data['page_info_list'][$k]['image_data']['top'] : base_url('public/images/usadmin/no_head.jpg');
        }
          $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];

        $data['page_add_url'] = base_url('usadmin/package_tour/free_page_add');
        $data['page_prev_url'] = base_url('home/package_travel/view');
        $data['page_del_url'] = base_url('usadmin/package_tour/page_del');
        $data['page_edit_url'] = base_url('usadmin/package_tour/free_page_edit');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $data['page_copy_url'] = base_url('usadmin/package_tour/page_copy');
        $data['page_monitor_url'] = base_url('usadmin/package_tour/article');
        $data['business_account'] = $business_account;

        $this->load->view('usadmin/page_monitor/index', $this->_set_common($data));
    }

    public function page_price($page_id) {
      
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
          $data['menu_list']= $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
        $city_name = $this->input->get('city_name', true);
        $package_name = $this->input->get('package_name', true);
        $data['city_list'] = $this->_get_city_list_by_page($page_id);
        foreach ($data['city_list'] as $key => $val) {
            if ($val['city_name'] == DEFAULT_CITY_NAME) {
                $data['city_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            } else {
                $data['city_list'][$key]['show_city_name'] = $val['city_name'];
            }
        }
        if (!$this->_check_city_name($page_id, $city_name)) {
            $city_name = $data['city_list'][0]['city_name'];
        }
        $data['package_list'] = $this->_get_package_list_by_city($page_id, $city_name);
        foreach ($data['package_list'] as $key => $val) {
            if ($val['package_name'] == DEFAULT_PACKAGE_NAME) {
                $data['package_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            } else {
                $data['package_list'][$key]['show_package_name'] = $val['package_name'];
            }
        }
        if (!$this->_check_package_name($page_id, $city_name, $package_name)) {
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
        $time = strtotime(date('Y', time()) . '-' . date('n', time()) . '-1');
        $timeend = strtotime('+11 month', $time);
        $data['date']['cal'][] = array(
            'year' => date('Y', $time),
            'month' => date('n', $time),
            'month_cn' => $this->common->get_month_cn(date('n', $time)),
            'week_first' => date("w", mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
            'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time)))),
            'all_days' => date('t', $time),
            'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
        );

        while (date('Y', $time) < date('Y', $timeend) OR date('n', $time) < date('n', $timeend)) {
            $data['date']['cal'][] = array(
                'year' => date('Y', strtotime('+1 month', $time)),
                'month' => date('n', strtotime('+1 month', $time)),
                'month_cn' => $this->common->get_month_cn(date('n', strtotime('+1 month', $time))),
                'week_first' => date("w", mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time)))),
                'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))),
                'all_days' => date('t', strtotime('+1 month', $time)),
                'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))
            );
            $time = strtotime('+1 month', $time);
        }

        $data['page_price_update_url'] = base_url('usadmin/package_tour/page_price_upload/') . '/' . $page_id;
        $data['package_info'] = $package_info;
        $data['city_name'] = $city_name;
        $data['package_name'] = $package_name;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $date_price = json_decode($data['package_info']['date_price'], true);
        $data['remarks'] = $date_price['remarks'];
        $data['date_choose'] = $date_price ? $date_price['date'] : array();
        $data['url']=$this->_tour($page_id);
          $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
         $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
        $this->load->view('usadmin/package_tour/page_price', $this->_set_common($data));
    }

    public function page_price_upload() {
        $data['menu_list']= $this->_get_menu();
        $business_account = $this->_get_auth();
        $date_price = $this->input->post('date_price', TRUE);
        $date_val = $this->input->post('date_val', TRUE);
        $remarks = $this->input->post('prices', TRUE);
        $data['package_id'] = $this->input->post('package_id', TRUE);
        $where = array('package_id' => $data['package_id']);
        $package = $this->Package_info_model->get_package_detail($where);
        $page_id = $package['page_id'];

        if (count($date_val) >= 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                if (empty($date_val[$k]) || empty($date_price[$k])) {
                    continue;
                }
                $new_date[$v] = $date_price[$k];
            }
            $date1 = array(
                'date' => $new_date,
                'remarks' => $remarks
            );
            $data['date_price'] = json_encode($date1);
        }

        $this->_delete_cache($package['page_id']);

        $res = $this->Package_info_model->save_package_info($data);
                     $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$page_id,
                'xxx'=>'套餐id:'.$data['package_id'],         
                'operate'=>'修改or增加价格',
                 'xx'=>'跟团游',  
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],       
                
            );
             $this->log($log);
        if ($res) {
            redirect(base_url("usadmin/package_tour/page_price") . '/' . $package['page_id'] . '?city_name=' . urlencode($package['city_name']) . '&package_name=' . urlencode($package['package_name']));
        }
    }

// 编辑头图
    public function top_edit() {

        $this->load->view('usadmin/package_tour/page_poster');
    }

    //跟团游增加页面
    public function page_add() {
        $data['menu_list']= $this->_get_menu();
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();

        $data['business_all'] = $this->_get_business_all();
        $data['business_account'] = $business_account;
        $data['do_page_add_url'] = base_url('usadmin/package_tour/do_page_add');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $this->load->view('usadmin/package_tour/' . $business_account['template_name'] . '/page_add', $this->_set_common($data));
    }

    //跟团游增加
    public function do_page_add() {
        $data['menu_list']= $this->_get_menu();
        $business_account = $this->_get_auth();

        $data['business_id'] = $business_account['business_id'];
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['short_title'] = $this->input->post('short_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['inst_data'] = $this->input->post('inst_data', TRUE);
       $data['h5_vodie'] = $_POST['h5_vodie'];
        $style_type = $this->input->post('style_type', true);
        $data['style_type'] = $style_type ? $style_type : DEFAULT_TEMPLATE_TYPE;
        $kf_type = $this->input->post('kf_type', TRUE);
        $user_mobile = $this->input->post('user_mobile', TRUE);
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
        if ($kf_type == '微信') {
            $kf_data = array(
                'wx' => array(
                    'name' => '微信',
                    'intro' => $image_wx
                )
            );
        } elseif ($kf_type == '手机') {
            $kf_data = array(
                'mobile' => array(
                    'name' => '手机',
                    'intro' => $user_mobile
                )
            );
        } elseif ($kf_type == 'QQ') {
            $kf_data = array(
                'qq' => array(
                    'name' => 'QQ',
                    'intro' => $user_mobile
                )
            );
        }

        $data['kf_data'] = json_encode($kf_data);
        $data['template_type'] = $business_account['template_name'];
        $data['add_time'] = time();
        $page_id = $this->Page_info_model->save_page_info($data);
        $page_ids=$page_id;
        if ($page_id) {
            if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == 0) {
                $image_data['fileupload'] = $this->upload_files('fileupload', $page_id, TRAVEL_FILE_KEY);
            }
            $data_up = array(
                'page_id' => $page_id,
                'image_data' => json_encode($image_data),
                'share_url' => $business_account['share_name'] . $page_id
            );
            $this->Page_info_model->save_page_info($data_up);
                 
                  $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$page_id,
                'operate'=>'新增页面---跟团游',
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],            
                
            );
             $this->log($log);
            //创建默认套餐
            $data_package = array(
                'page_id' => $page_id,
                'city_name' => DEFAULT_CITY_NAME,
                'package_name' => DEFAULT_PACKAGE_NAME,
                'add_time' => time()
            );

            $page_id=$this->Package_info_model->save_package_info($data_package);
        
          
            echo "<script language=javascript>alert('保存成功');location.href='" . base_url("usadmin/package_tour/page_trip/") . '/' . $page_ids . "';</script>";
        }
    }

    //自由行增加页面

    public function free_page_add() {
        $data['menu_list']= $this->_get_menu();
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();

        $data['business_all'] = $this->_get_business_all();
        $data['business_account'] = $business_account;
        $data['do_page_add_url'] = base_url('usadmin/package_tour/free_do_page_add');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $data['do_page_add_url'] = base_url('usadmin/package_tour/free_do_page_add');
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip');
        $this->load->view('usadmin/page_monitor/page_add', $this->_set_common($data));
    }

    //自由行增加
    public function free_do_page_add() {
        $data['menu_list']= $this->_get_menu();
        $business_account = $this->_get_auth();
     
        $data['business_id'] = $business_account['business_id'];
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['short_title'] = $this->input->post('short_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['inst_data'] = $this->input->post('inst_data', TRUE);
        $data['h5_vodie'] = $_POST['h5_vodie'];
        $style_type = $this->input->post('style_type', true);
        $data['style_type'] = $style_type ? $style_type : DEFAULT_TEMPLATE_TYPE;
        $kf_type = $this->input->post('kf_type', TRUE);
        $user_mobile = $this->input->post('user_mobile', TRUE);
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
        if ($kf_type == '微信') {
            $kf_data = array(
                'wx' => array(
                    'name' => '微信',
                    'intro' => $image_wx
                )
            );
        } elseif ($kf_type == '手机') {
            $kf_data = array(
                'mobile' => array(
                    'name' => '手机',
                    'intro' => $user_mobile
                )
            );
        } elseif ($kf_type == 'QQ') {
            $kf_data = array(
                'qq' => array(
                    'name' => 'QQ',
                    'intro' => $user_mobile
                )
            );
        }

        $data['kf_data'] = json_encode($kf_data);
        $data['template_type'] = $business_account['template_name'];
        $data['add_time'] = time();
        $page_id = $this->Page_info_model->save_page_info($data);
        if ($page_id) {
            if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == 0) {
                $image_data['fileupload'] = $this->upload_files('fileupload', $page_id, TRAVEL_FILE_KEY);
            }
            $data_up = array(
                'page_id' => $page_id,
                'image_data' => json_encode($image_data),
                'share_url' => $business_account['share_name'] . $page_id
            );
            $this->Page_info_model->save_page_info($data_up);
                    $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$page_id,
                'operate'=>'新增页面---自由行',
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],            
                
            );
             $this->log($log);
//            //创建默认套餐
//            $data_package = array(
//                'page_id' => $page_id,
//                'city_name' => DEFAULT_CITY_NAME,
//                'package_name' => DEFAULT_PACKAGE_NAME,
//                'add_time' => time()
//            );
//            $this->Package_info_model->save_package_info($data_package);
            echo "<script language=javascript>alert('保存成功');location.href='" . base_url("usadmin/package_tour/article/") . '/' . $page_id . "';</script>";
        }
    }

    //自由行编辑页面
    public function free_page_edit($page_id) {
    
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
    $data['menu_list']= $this->_get_menu();
        $data['business_all'] = $this->_get_business_all();
        $data['business_account'] = $business_account;
        $where = array(
            'page_id' => $page_id
        );
        $data['info'] = $this->Page_info_model->get_page_info_detail($where);
        $data['info']['image_data'] = json_decode($data['info']['image_data'], true);
        $data['info']['kf_data'] = json_decode($data['info']['kf_data'], true);
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/article') . '/' . $page_id;
        $data['page_addmenu_url'] = base_url('usadmin/package_tour/addmenu') . '/' . $page_id;
        $data['do_page_edit_url'] = base_url('usadmin/package_tour/do_page_edit');
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;     
        $data['business_name'] = $this->_get_business_detail($data['info']['business_id']);
        $data['url']=$this->_free($page_id);
           $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
         $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
        $this->load->view('usadmin/page_monitor/page_edit', $this->_set_common($data));
    }

    //自由行产品编辑页面
    public function article($page_id) {
        
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['page_edit_url'] = base_url('usadmin/package_tour/free_page_edit') . '/' . $page_id;
        $data['page_free_url'] = base_url('usadmin/package_tour/free_add') . '/' . $page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['package_type'] = base_url('usadmin/package_tour/package_type');
        $data['package_del'] = base_url('usadmin/package_tour/hotel_package_del');
        $data['page_free_url'] = base_url('usadmin/package_tour/free_add') . '/' . $page_id;
        $data['page_addmenu_url'] = base_url('usadmin/package_tour/addmenu') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;     
        $data['page_id'] = $page_id;
        $hotel_package_id = $this->input->get('hotel_package_id', TRUE);
        if (empty($hotel_package_id)) {
            $hotel_package_id = '__DEFAULT__';
        }
        $data['hotel_package_id'] = $hotel_package_id;
        //酒店套餐显示
        $data['type'] = $this->input->get('type', TRUE);
        $data['page_free_url'] = base_url('usadmin/package_tour/free_add') . '/' . $page_id . '?type=' . $data['type'] . '&hotel_package_id=' . $hotel_package_id;
        $where = array('page_id' => $page_id, 'type' => $data['type'], 'is_del' => 0);
        $data['hotel'] = $this->Package_hotel_info_model->get_package_list($where);
        $where = array('page_id' => $page_id, 'is_del' => 0);//复制套餐信息
        $data['hotel_package'] = $this->Package_hotel_info_model->get_package_list($where);
        if ($hotel_package_id == '__DEFAULT__') {
            $where = array('cate_id' => $data['type'], 'page_id' => $page_id, 'is_del' => 0, 'order_by' => 'order_bay asc,id asc');
        } else {
            $where = array('cate_id' => $data['type'], 'package_id' => $hotel_package_id, 'page_id' => $page_id, 'is_del' => 0,'order_by' => 'order_bay asc,id asc');
        }
        $data['list'] = $this->Pro_info_model->get_pro_list($where);
        $where = array('type' => $data['type'], 'is_del' => 0);
        $cate = $this->Cate_info_model->get_cate_detail($where);
        $data['cate'] = $cate['cate_name'];
        $data['pro_edit'] = base_url('usadmin/package_tour/edit_addfly_hotel/') . '/' . $data['type'] . '/' . $page_id; //编辑页面URL 
        $data['pro_del'] = base_url('usadmin/package_tour/pro_del'); //删除URL
        foreach ($data['list'] as $k => $v) {
            $where = array('hotel_package_id' => $v['package_id'], 'page_id' => $page_id, 'is_del' => 0);
            $package_name = $this->Hotel_package_model->get_hotel_package_detail($where);
            $data['list'][$k]['package_name'] = $package_name['package_name'];
            $data['list'][$k]['price'] = json_decode($v['pro_price_date'], TRUE);
            $data['list'][$k]['min'] = min($data['list'][$k]['price']['date']);
            $data['list'][$k]['max'] = max($data['list'][$k]['price']['date']);
        }
         $data['url']=$this->_free($page_id);
           $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
         $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
//        $where=array('hotel_package_id'=>$hotel_package_id,'page_id'=>$page_id,'is_del'=>0);
//        $data['package_name']=$this->Hotel_package_model->get_hotel_package_detail($where);  
//        echo $this->db->last_query();die;
//         echo "<pre>";
//      print_r($data);die;

        $this->load->view('usadmin/page_monitor/article', $this->_set_common($data));
    }

    //自由行套餐增加
    public function package_type() {
        $data['menu_list']= $this->_get_menu();
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();    
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['type'] = $this->input->post('type', true);
        $data['package_name'] = $this->input->post('package_name', TRUE);
        $ids= $this->input->post('hotel_package_id', TRUE);
        if($ids){$data['hotel_package_id'] =$ids;}
        $data['add_time'] = time();
        $result['code'] = '0';
        $result['msg'] = "失败";
        if (empty($data['page_id'])) {
            $result['code'] = 2;
            $result['msg'] = "页面ID不能为空";
            return $this->ajax_return($result);
        }
        if (!empty($data['type'])) {
            $red = $this->Package_hotel_info_model->save_package_info($data);
            if ($red) {
                  $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$data['page_id'],
                 'hotel_package_id'=>'类别id:'.$red['hotel_package_id'],     
                'operate'=>'增加类别：模版类型：'.$data['type'].'新增类别:'.$data['package_name'],
                'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                  $this->log($log);
                $result['code'] = 1;
                $result['id'] = $red;
                 $result['msg'] = "增加成功";
                return $this->ajax_return($result);
            } else {
                $result['code'] = 0;
                $result['msg'] = "增加失败";
                return $this->ajax_return($result);
            }
        }
    }

    // 自由行套餐编辑页面
    public function edit_addfly_hotel($type, $page_id, $id) {
        $data['menu_list']= $this->_get_menu();
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['business_account'] = $business_account;
        if (empty($page_id) || empty($id)) {
            echo "操作错误";
            exit;
        }


        if (empty($type)) {
            echo "操作错误";
            exit;
        }
        $where = array('id' => $id, 'page_id' => $page_id, 'is_del' => 0);
        $data['info'] = $this->Pro_info_model->get_pro_detail($where);
        $data['info']['fly'] = json_decode($data['info']['pro_info'], true);
        $data['info']['day_view'] = json_decode($data['info']['pro_text'], TRUE);
        $data['info']['space'] = array_filter(json_decode($data['info']['pro_space']));
        $data['pro_update_url'] = base_url('usadmin/package_tour/pro_update/') . '/' . $id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/free_page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/article') . '/' . $page_id;
        $data['date']['cal'] = $this->_timex();
        $date_price = json_decode($data['info']['pro_price_date'], true);

        $data['date_choose'] = $date_price['date'] ? $date_price['date'] : array();
        $data['min'] = min($data['date_choose']);
        $data['max'] = max($data['date_choose']);
//        echo "<pre>";
//       print_r($data);die;
        if ($data['info']) {
            switch ($type) {
                case 'hotel':

                    $this->load->view('usadmin/page_monitor/addhotel_edit', $this->_set_common($data));
                    break;
                case 'car':
                    $this->load->view('usadmin/page_monitor/addcar_edit', $this->_set_common($data));
                    break;
                case 'fly':
//             echo "<pre>";
//               print_r($data);die;
                    $this->load->view('usadmin/page_monitor/addfly_edit', $this->_set_common($data));
                    break;
                case 'fly_hotel':

                    $this->load->view('usadmin/page_monitor/addfly_hotel_edit', $this->_set_common($data));
                    break;
                case 'product':
                    $this->load->view('usadmin/page_monitor/addproduct_edit', $this->_set_common($data));
                    break;
                case 'stroke':

                    $this->load->view('usadmin/page_monitor/addstroke_edit', $this->_set_common($data));
                    break;
                case 'article':
                    $this->load->view('usadmin/page_monitor/addarticle_edit', $this->_set_common($data));
                    break;
                default:
                    FALSE;
            }
        }
    }

    //自由行删除
    public function pro_del($id) {
          $data = $this->_get_urls();
        $business_account = $this->_get_auth();   
        if (!$id) {
            echo "操作错误";
            exit;
        }
        $package_id=$this->input->get('package_id',true);
        $where = array('id' => $id);
        $list = $this->Pro_info_model->get_pro_detail($where);
        if (empty($list)) {
            echo "数据不存在";
            exit;
        }

        $data['id'] = $id;
        $data['is_del'] = 1;
        $red = $this->Pro_info_model->save_pro_info($data);
        if ($red) {
                  $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$list['page_id'],
                 'package_id'=>'产品id:'.$id,     
                'operate'=>'删除产品：'.$list['pro_name'].' 所属类型:'.$package_id,
                'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                  $this->log($log);
            if($package_id=='__DEFAULT__'){
                    redirect(base_url('usadmin/package_tour/article/') . '/' . $list['page_id'] . '?type=' . $list['cate_id']);   
            }else{
                     redirect(base_url('usadmin/package_tour/article/') . '/' . $list['page_id'] . '?type=' . $list['cate_id']. '&hotel_package_id=' . $list['package_id']);  
            }
     
        }
    }

    //自由行套餐修改update
    public function pro_update($id) {
          $data = $this->_get_urls();
        $business_account = $this->_get_auth();   
        if (empty($id)) {
            echo "操作错误";
            exit;
        }
        $where = array('id' => $id, 'is_del' => 0);
        $list = $this->Pro_info_model->get_pro_detail($where);

        $data['pro_title'] = $this->input->post('title', true);
        $data['pro_name'] = $this->input->post('name', TRUE);
        $data['pro_eng_name'] = $this->input->post('eng_name', TRUE);
        $data['price_money']=$this->input->post('price_moeny',TRUE);        
        if(empty($data['pro_title']) && empty($data['pro_name'])){
              echo "非法操作";die;
         }
                

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $data['pro_image'] = $this->upload_image('image', 'H5image');
        }
        $date_price = $this->input->post('date_price', TRUE);
        $date_val = $this->input->post('date_val', TRUE);
        if (count($date_val) >= 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                if (empty($date_val[$k]) || empty($date_price[$k])) {
                    continue;
                }
                $new_date[$v] = $date_price[$k];
            }
            $date1 = array(
                'date' => $new_date,
            );
            $data['pro_price_date'] = json_encode($date1);
        }

        $data['min'] = min($date_price);
        $data['max'] = max($date_price);
        $data['pro_price'] = $data['min'];
        $data['pro_sum'] = $this->input->post('sum', true);
        $data['pro_luggage'] = $this->input->post('luggage', true);
        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach ($day_view_one as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        foreach ($day_view_two as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        $data['pro_text'] = json_encode($day_view);
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
        $fly_type = $this->input->post('fly_type', TRUE);


        $fly_data = array();
        foreach ($fly_sn as $key => $val) {
            if (!$val) {
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
                'day_x' => $day_x[$key] ? $day_x[$key] : 0,
                'fly_type'=>$fly_type[$key]
            );
            if ($fly_id[$key]) {
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            } else {
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if (!empty($ret)) {
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                } else {
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            $fly_all_data[] = $fly_data;
        }

        $data['pro_info'] = json_encode($fly_all_data);
        $data['pro_day'] = $this->input->post('day', TRUE);
        $data['pro_grade'] = $this->input->post('grade', true);
        $data['pro_remarks'] = $this->input->post('remarks', TRUE);
        $data['pro_unit'] = $this->input->post('unit', TRUE);
        $data['update_time'] = time();
        $number[] = $this->input->post('number', true);
        $share[] = $this->input->post('share', TRUE);
        if(empty($number)&&!empty($share)){
          $data['pro_space'] = json_encode($share);  
        }elseif (!empty ($number)&&empty ($share)) {
            $data['pro_space'] = json_encode($number);
        }elseif(empty ($number)&&empty ($share)){
            $data['pro_space'] ='[]';
        }else{
            $data['pro_space'] = json_encode(array_merge($number, $share));
        }
        
        $data['pro_details'] = $this->input->post('inst_data', TRUE);
        $data['id'] = $id;

        $red = $this->Pro_info_model->save_pro_info($data);
           $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$list['page_id'],
                 'hotel_package_id'=>'产品id:'.$id,     
                'operate'=>'产品编辑：编辑前标题：'.($list['pro_title']?$list['pro_title']:$list['pro_name']).' 编辑后标题:'.($data['pro_title']?$data['pro_title']:$data['pro_name']),
               'zyx'=>'自由行',
                'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                  $this->log($log);
        if ($red) {
            redirect(base_url('usadmin/package_tour/article/') . '/' . $list['page_id'] . '?type=' . $list['cate_id'] . '&hotel_package_id=' . $list['package_id']);
        }
    }

    //自由行酒店套餐删除
    public function hotel_package_del() {
          $data = $this->_get_urls();
        $business_account = $this->_get_auth();   
        $hotel_package_id = $this->input->post('hotel_package_id', TRUE);
        if ($hotel_package_id) {
            $where=array('hotel_package_id'=>$hotel_package_id,'is_del'=>0);
            $list=$this->Package_hotel_info_model->get_package_detail($where);
            if ($list['package_name']!="默认类别"){
                $page_del = array(
                    'hotel_package_id' => $hotel_package_id,
                    'is_del' => 1,
                    'update_time' => time()
                );
                $red = $this->Package_hotel_info_model->save_package_info($page_del);
                if ($red){
                       $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$list['page_id'],
                 'hotel_package_id'=>'类别id：'.$hotel_package_id,         
                'operate'=>'类别删除：'.$list['package_name'],
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                       $this->log($log);
                    $result['code'] = 1;
                    $result['msg'] = "删除成功";
                    return $this->ajax_return($result);
                } else {
                    $result['code'] = 0;
                    $result['msg'] = "删除失败";
                    return $this->ajax_return($result);
                }
            }else{$result['code'] = 3;
                $result['msg'] = "默认套餐不能删除";
                return $this->ajax_return($result);
            }
        } else {
            $result['code'] = 2;
            $result['msg'] = "套餐ID不能为空";
            return $this->ajax_return($result);
        }
    }

    //自由行套餐详情页面
    public function free_add($page_id) {
      
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
          $data['menu_list']= $this->_get_menu();
        $data['business_all'] = $this->_get_business_all();
        $data['business_account'] = $business_account;
        $data['type'] = $this->input->get('type', true);
        $data['hotel_package_id'] = $this->input->get('hotel_package_id', TRUE);
        if (empty($data['type'])) {
            echo "模板类型不能为空";
            exit;
        }
        $data['date']['cal'] = $this->_timex();
        $data['do_pro_url'] = base_url('usadmin/package_tour/do_por_add') . '/' . $page_id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/free_page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/article') . '/' . $page_id;
        $data['url']=$this->_free($page_id);
        //     $data['page_price_url'] = base_url('usadmin/package_tour/page_price').'/'.$page_id;
        switch ($data['type']) {
            case 'hotel':
                $this->load->view('usadmin/page_monitor/addhotel', $this->_set_common($data));
                break;
            case 'car':
                $this->load->view('usadmin/page_monitor/addcar', $this->_set_common($data));
                break;
            case 'fly':
                $this->load->view('usadmin/page_monitor/addfly', $this->_set_common($data));
                break;
            case 'fly_hotel':
                $this->load->view('usadmin/page_monitor/addfly_hotel', $this->_set_common($data));
                break;
            case 'product':
                $this->load->view('usadmin/page_monitor/addproduct', $this->_set_common($data));
                break;
            case 'stroke':
                $this->load->view('usadmin/page_monitor/addstroke', $this->_set_common($data));
                break;
            case 'article':
                $this->load->view('usadmin/page_monitor/addarticle', $this->_set_common($data));
                break;
            default:
                FALSE;
        }
    }
//自由行 模版内容信息增加
    public function do_por_add($page_id) {
        
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        if (!$page_id) {
            echo "非法入口";
            die;
        }
        $data['page_id'] = $page_id;
        $package_id = $this->input->post('hotel_package_id', true);
        $date_price = $this->input->post('date_price', TRUE);
        $date_val = $this->input->post('date_val', TRUE);
        if (count($date_val) >= 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                if (empty($date_val[$k]) || empty($date_price[$k])) {
                    continue;
                }
                $new_date[$v] = $date_price[$k];
            }
            $date1 = array(
                'date' => $new_date,
            );
          
            $data['pro_price_date'] = json_encode($date1);
        }
   
        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach ($day_view_one as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        foreach ($day_view_two as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        $data['pro_text'] = json_encode($day_view);
        $data['cate_id'] = $this->input->post('type', TRUE);
       
        if($package_id=='__DEFAULT__'){
             $where=array('page_id'=>$page_id,'package_name'=>'默认类别','type'=>$data['cate_id'],'is_del'=>0);
             $res=$this->Package_hotel_info_model->get_package_detail($where);
//             echo "<pre>";
//             print_r($res);die;
             if(!$res){
              $red = array(
            'page_id' => $page_id,
            'package_name' => '默认类别',
            'add_time'  => time(),
            'type' => $data['cate_id']
            ); 
          $id=$this->Package_hotel_info_model->save_package_info($red); 
         $data['package_id']=$id;
             }else{
               $data['package_id']=$res['hotel_package_id'];   
             }
       
            
  
   
        }else{
            $data['package_id']=$package_id; 
        }  
      
        $data['pro_title'] = $this->input->post('title', true);
        $data['pro_name'] = $this->input->post('name', TRUE);
        $data['pro_eng_name'] = $this->input->post('eng_name', TRUE);
        $data['price_money']=$this->input->post('price_moeny',TRUE);
        $data['pro_price'] = $this->input->post('price', TRUE);
           if(empty($data['pro_title']) && empty($data['pro_name'])){
              echo "非法操作";die;
         }
        $data['pro_price_data'] = $this->input->post('price_data', true);
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $data['pro_image'] = $this->upload_image('image', 'H5image');
        }
        $data['pro_sum'] = $this->input->post('sum', true);
        $data['pro_luggage'] = $this->input->post('luggage', true);
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
        $fly_type = $this->input->post('fly_type', TRUE);

        $fly_data = array();
        foreach ($fly_sn as $key => $val) {
            if (!$val) {
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
                'day_x' => $day_x[$key] ? $day_x[$key] : 0,
                 'fly_type' =>$fly_type[$key]
            );
            if ($fly_id[$key]) {
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            } else {
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if (!empty($ret)) {
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                } else {
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            $fly_all_data[] = $fly_data;
        }
        $pro_info = json_encode($fly_all_data);
        $data['pro_info']=($pro_info=="null")?'[]':$pro_info;
        $data['pro_day'] = $this->input->post('day', TRUE);
        $data['pro_grade'] = $this->input->post('grade', true);
        $data['pro_remarks'] = $this->input->post('remarks', TRUE);
        $data['pro_unit'] = $this->input->post('unit', TRUE);
        $data['add_time'] = time();
        $number[] = $this->input->post('number', true);
        $share[] = $this->input->post('share', TRUE);
        if(empty($number)&&!empty($share)){
          $data['pro_space'] = json_encode($share);  
        }elseif (!empty ($number)&&empty ($share)) {
            $data['pro_space'] = json_encode($number);
        }elseif(empty($number)&&empty ($share)){
            $data['pro_space'] ='[]';
        }else{
            $data['pro_space'] = json_encode(array_merge($number, $share));
        }
        $data['pro_details'] = $this->input->post('inst_data', TRUE);
        $red = $this->Pro_info_model->save_pro_info($data);
                 $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$data['page_id'],
                 'package_id'=>'产品id:'.$red['id'],     
                'ccc'=>'增加产品：'.($data['title']?$data['title']:$data['pro_name']).' :所属模版：'.$data['cate_id'].' :所属类别：'.$package_id,
                'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                  $this->log($log);
        if ($red) {
            redirect(base_url('usadmin/package_tour/article/') . '/' . $page_id . '?type=' . $data['cate_id'] . '&hotel_package_id=' . $data['package_id']);
        }
    }
    //自由行导航编辑
    public function addmenu($page_id) {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['page_edit_url'] = base_url('usadmin/package_tour/free_page_edit') . '/' . $page_id;
        $data['page_free_url'] = base_url('usadmin/package_tour/free_add') . '/' . $page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['package_type'] = base_url('usadmin/package_tour/package_type');
        $data['page_trip_url'] = base_url('usadmin/package_tour/article') . '/' . $page_id;
        $data['page_free_url'] = base_url('usadmin/package_tour/free_add') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;     
        $data['page_addmenu_url'] = base_url('usadmin/package_tour/addmenu');
        $where=array('page_id'=>$page_id,'is_del'=>0);
        $data['list']=$this->Pro_navigation_model->get_navigation_detail($where);
        $data['json_menu'] = $data['list']['pro_info']?$data['list']['pro_info']:'[]';
        $data['list']['pro_info']=  json_decode($data['list']['pro_info'],true);
        foreach($data['list']['pro_info'] as $k=>$v){
            foreach($v['type'] as $k1=>$v1){
               foreach($v1['list'] as $k2=>$v2){
                $where=array('hotel_package_id'=>$v2,'is_del'=>0);
                $package=$this->Package_hotel_info_model->get_package_detail($where);
//                if($package){
                $data['list']['pro_info'][$k]['type'][$k1]['package_name'][$k2]=$package['package_name'];
//                }else{
//                  //  unset($data['list']['pro_info'][$k]['type'][$k1]['list'][$k2]);	
//                }
               }
              // $data['list']['pro_info'][$k]['type'][$k1]['list'] = array_merge($data['list']['pro_info'][$k]['type'][$k1]['list']);  
              // $data['list']['pro_info'][$k]['type'][$k1]['package_name'] = array_merge($data['list']['pro_info'][$k]['type'][$k1]['package_name']);
            }
        }
        //$menu = $data['list']['pro_info'];
//        echo '<pre>11';
//        print_r($data['list']['pro_info']);	die();
         $where=array('page_id'=>$page_id,'is_del'=>0);   
         $data['info']=$this->Package_hotel_info_model->get_package_list($where);
          $data['post_url']=base_url('usadmin/package_tour/increase_menu');
          $data['page_id']=$page_id;
          $data['url']=$this->_free($page_id);
          $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
         $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
           $this->load->view('usadmin/page_monitor/addmenu', $this->_set_common($data)); 
  
    }
//ajax 处理 导航栏数据
    public  function increase_menu(){
            $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['pro_info']=$this->input->post('type_info',true);
        $data['page_id']=$this->input->post('page_id',TRUE);
        $data['add_time']=  time();
        $where=array('page_id'=>$data['page_id'],'is_show'=>0);
        $red=$this->Page_info_model->get_page_info_detail($where);
        if(!$red){
             $result['code'] = 0;
              $result['msg'] = "非法操作";
              return $this->ajax_return($result);
        }
      $res= $this->Pro_navigation_model->save_navigation_info($data); 
      if($res){
                  $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$data['page_id'],
                'ccc'=>'编辑导航栏',
                'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
                  $this->log($log);
            $result['code'] = 1;
              $result['msg'] = "增加成功";
              return $this->ajax_return($result);
      }
    
    }
    // 跟团游编辑页面
    public function page_edit($page_id) {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $data['business_account'] = $business_account;
        $where = array(
            'page_id' => $page_id
        );
        $data['info'] = $this->Page_info_model->get_page_info_detail($where);
        $data['info']['image_data'] = json_decode($data['info']['image_data'], true);
        $data['info']['kf_data'] = json_decode($data['info']['kf_data'], true);
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['do_page_edit_url'] = base_url('usadmin/package_tour/do_page_edit');
        $data['business_name'] = $this->_get_business_detail($data['info']['business_id']);
        $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
         $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
        $data['url']= $this->_tour($page_id);
//       echo "<pre>";
//       print_r($data);die;
        $this->load->view('usadmin/package_tour/page_edit', $this->_set_common($data));
    }
    //跟团游编辑
    public function do_page_edit() {
        $business_account = $this->_get_auth();
        $data['page_id'] = $this->input->post('page_id', TRUE);
        $data['page_title'] = $this->input->post('page_title', TRUE);
        $data['short_title'] = $this->input->post('short_title', TRUE);
        $data['share_desc'] = $this->input->post('share_desc', TRUE);
        $data['uploader'] = $this->input->post('uploader', TRUE);
        $data['inst_data'] = $_POST['inst_data'];
        $data['h5_vodie'] = $_POST['h5_vodie'];
        $kf_type = $this->input->post('kf_type', TRUE);
        $user_mobile = $this->input->post('user_mobile', TRUE);
        $style_type = $this->input->post('style_type', TRUE);
        
       
        if(empty($data['page_id'])||!is_numeric($data['page_id'])){
            echo "ID不存在";die;
        }
       if(empty($data['page_title'])) {
           echo '名称必须填写';die;
       }
        if ($style_type) {
            $data['style_type'] = $style_type;
        }
        $image_data = array();
        if (isset($_FILES['image_top']) && $_FILES['image_top']['error'] == 0) {
            $image_data['top'] = $this->upload_image('image_top', 'H5image');
        } else {
            $image_data['top'] = $this->input->post('image1', TRUE);
        }
        if (isset($_FILES['image_spec']) && $_FILES['image_spec']['error'] == 0) {
            $image_data['spec'] = $this->upload_image('image_spec', 'H5image');
        } else {
            $image_data['spec'] = $this->input->post('image2', TRUE);
        }
        if (isset($_FILES['image_share']) && $_FILES['image_share']['error'] == 0) {
            $image_data['share'] = $this->upload_image('image_share', 'H5image');
        } else {
            $image_data['share'] = $this->input->post('image3', TRUE);
        }
        if (isset($_FILES['fileupload']) && $_FILES['fileupload']['error'] == 0) {
            $image_data['fileupload'] = $this->upload_files('fileupload', $data['page_id'], TRAVEL_FILE_KEY);
        } else {
            $image_data['fileupload'] = $this->input->post('image4', TRUE);
        }
        if (isset($_FILES['image_wx']) && $_FILES['image_wx']['error'] == 0) {
            $image_wx = $this->upload_image('image_wx', 'H5image');
        } else {
            $image_wx = $this->input->post('image5', TRUE);
        }
        $data['image_data'] = json_encode($image_data);
        if ($kf_type == '微信') {
            $kf_data = array(
                'wx' => array(
                    'name' => '微信',
                    'intro' => $image_wx
                )
            );
        } elseif ($kf_type == '手机') {
            $kf_data = array(
                'mobile' => array(
                    'name' => '手机',
                    'intro' => $user_mobile
                )
            );
        } elseif ($kf_type == 'QQ') {
            $kf_data = array(
                'qq' => array(
                    'name' => 'QQ',
                    'intro' => $user_mobile
                )
            );
        }

        $data['kf_data'] = json_encode($kf_data);
        $data['update_time'] = time();
       $page_id= $this->Page_info_model->save_page_info($data);
         $this->_delete_cache($data['page_id']);
       if($page_id){
           $where=array('page_id'=>$page_id,'is_show'=>0);
            $list= $this->Page_info_model->get_page_info_detail($where);
            $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'title'=>'页面id:'.$data['page_id'],
                'operate'=>'页面编辑',
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
        
           if($list['page_type']==1){
            $log['yxlx']='跟团游';
                   $this->log($log);
                echo "<script language=javascript>alert('编辑成功');location.href='" . base_url("usadmin/package_tour/page_trip/".$data['page_id'])."';</script>"; 
           }elseif($list['page_type']==2){
               $log['yxlx']='自由行';
                   $this->log($log);
                 echo "<script language=javascript>alert('编辑成功');location.href='" . base_url("usadmin/package_tour/article/".$data['page_id'])."';</script>";
           }
       }

      

    }
    public  function pwd_edit(){
             $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $data['business_account'] = $business_account;
        $data['pwd_url']=  base_url('usadmin/package_tour/pwd_update');
        $this->load->view('usadmin/page_monitor/pwd_edit', $this->_set_common($data));  
    }
        public  function pwd_update(){
          $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $old_pwd=$this->input->post('old_pwd',true);
        $new_pwd=$this->input->post('pwd',TRUE);
        $where=array(
            'business_account_id'=>$business_account['business_account_id'],
        );
        $bus=$this->Business_account_model->get_business_account_detail($where);
        if(empty($bus)){
            $result['code'] = 0;
            $result['msg'] = "帐号不存在";
            return $this->ajax_return($result);
        }
        
        //密码加密
          $jpwd= md5($old_pwd.$bus['salt']);
          
          
          if($jpwd!=$bus['password']){
             $result['code'] = 1;
            $result['msg'] = "旧密码不正确";
            return $this->ajax_return($result);  
          }
        $data['business_account_id']=$business_account['business_account_id'];
        $data['password']= md5($new_pwd.$bus['salt']);
        $data['login_time']=  time();
        $upde=$this->Business_account_model->save_business_account($data);
        if($upde){
            $result['code'] = 2;
            $result['msg'] = "密码修改成功";
            return $this->ajax_return($result);   
        }
        
       
          $this->load->view('usadmin/page_monitor/pwd_edit', $this->_set_common($data));  
    }
    public function page_poster() {
        $data['menu_list']= $this->_get_menu();
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data = array();
        $this->load->view('usadmin/package_tour/page_poster', $this->_set_common($data));
    }
    //跟团游删除
    public function page_del() {
        
        $this->_get_auth();
        $business_account = $this->_get_auth();
        $page_id = $this->input->post('page_id', true);
        $page_data = array(
            'page_id' => $page_id,
            'is_show' => 1
        );
        //删除页面
        $res = $this->Page_info_model->save_page_info($page_data);
        $result = array();
        if (!$res) {
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
              $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                 'xx'=>'页面id:'.$page_id,
                 'shanchu'=> '   【删除页面】',
                 'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
              $this->log($log);
        $result['code'] = 0;
        $result['msg'] = "删除成功";
        return $this->ajax_return($result);
    }
    //跟团游页面复制(复制页面基本信息以及行程)
    public function page_copy() {
         $this->_get_auth();
        $business_account = $this->_get_auth();
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
        if (!$page_id) {
            $this->db->trans_rollback();
            $result['code'] = 1;
            $result['msg'] = "复制失败";
            return $this->ajax_return($result);
        }

        $where = array(
            'page_id' => $old_page_info['page_id'],
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
               $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                 'xx'=>'复制页面id:'.$old_page_info['page_id'].' 新页面id:'.$page_id,
                 'xxx'=>'跟团游复制',   
                    'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
              $this->log($log);
        $result['code'] = 0;
        $result['msg'] = "复制成功";
        return $this->ajax_return($result);
    }
  //自由行复制
  public function page_tl_copy(){
          $this->_get_auth();
          $business_account = $this->_get_auth();
        $result = array();
        $old_page_id = $this->input->post('page_id', true);
        //1. 复制page_info
        $where = array(
            'page_id' => $old_page_id
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        $page_info['add_time'] = time();
        unset($page_info['page_id']);
        unset($page_info['update_time']);
        unset($page_info['image_data']);
        unset($page_info['h5_image']);
        unset($page_info['synthesis_image']);
        $this->db->trans_start();
        $page_id = $this->Page_info_model->save_page_info($page_info);
        if (!$page_id) {
            $this->db->trans_rollback();
            $result['code'] = 1;
            $result['msg'] = "复制失败";
            return $this->ajax_return($result);
        }
        //查询-复制源-套餐数据
        $where = array(
            'page_id' =>  $old_page_id,
            'is_del' => 0
        );
        $package_data = $this->Package_hotel_info_model->get_package_list($where);
        //复制页面下的所有套餐
        foreach ($package_data as $key => $val) {
            $package_info = $val;
            $package_info['add_time'] = time();
            $package_info['page_id'] = $page_id;
            unset($package_info['hotel_package_id']);
            unset($package_info['update_time']);
            $package_id = $this->Package_hotel_info_model->save_package_info($package_info);
         
            if (!$package_id) {
                $this->db->trans_rollback();
                $result['code'] = 1;
                $result['msg'] = "复制套餐失败";
                return $this->ajax_return($result);
            }
            //查询-复制源-产品数据
            $where = array(
            'page_id' => $old_page_id,
            'package_id' => $val['hotel_package_id'],
            'is_del' => 0
             );
            $pro_info= $this->Pro_info_model->get_pro_list($where);
            foreach ($pro_info as $k => $v) {
               $new_pro_info=$v;
               $new_pro_info['page_id']=$page_id;
               $new_pro_info['package_id']=$package_id;
               unset($new_pro_info['id']);
               
              $pro_id=$this->Pro_info_model->save_pro_info($new_pro_info);
                if (!$pro_id) {
               $this->db->trans_rollback();
                    $result['code'] = 1;
                    $result['msg'] = "复制产品失败";
                    return $this->ajax_return($result);
                }
            }  
        } 

//        //查询-复制源-导航数据
//        $where = array(
//            'page_id' => $old_page_id,
//            'is_del' => 0
//        );
//        $nav_data = $this->Pro_navigation_model->get_navigation_detail($where);
//           unset($nav_data['id']);
//           $nav_data['page_id']=$page_id;
//           $nav_id = $this->Pro_navigation_model->save_navigation_info($nav_data);
//            if (!$nav_id) {
//                $this->db->trans_rollback();
//                $result['code'] = 1;
//                $result['msg'] = "复制失败";
//                return $this->ajax_return($result);
//        }
      
        $this->db->trans_complete();
             $log=array(
             'name'=>'操作人:'.$business_account['business_account'],
             'xx'=>'复制页面id:'.$old_page_id.' 新页面id:'.$page_id,
             'xxx'=>'自由行复制',   
             'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],          
              );
              $this->log($log);
        $result['code'] = 0;
        $result['msg'] = "复制成功";
        return $this->ajax_return($result);
      
  }
  //行程排序
    public function day_sort() {
        $this->_get_auth();
        $day_ids = $this->input->post('day_ids', true);
        $day_orders = $this->input->post('day_orders', true);
        foreach ($day_ids as $key => $val) {
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
    public function day_del() {
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
        if (!empty($day_ids)) {
            $this->db->trans_start();
            foreach ($day_ids as $key => $val) {
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
        if (!empty($day_ids)) {
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
   //自由行移动
   public  function pro_move(){
      $data['id']=$this->input->post('pro_ids',TRUE);
      $data['package_id']=$this->input->post('package_id',true);
     for($i=0;$i<count($data['id']);$i++){
                 foreach($data['id'] as $k=>$v){
                   $dt[$k]=array(
                      'id'=>$data['id'][$k],
                       'package_id'=>$data['package_id']
                   );
               }
               foreach($dt as $k1=>$v1){
                 $res= $this->Pro_info_model->save_pro_info($v1);
   
               }    
                $result['code'] = 1;
                 $result['msg'] = "移动成功";
                 return $this->ajax_return($result);
     }
  
   }
   //自由行复制
   public function pro_copy(){
      $this->_get_auth();
      $data['id']=$this->input->post('pro_ids',TRUE);
      $data['package_id']=$this->input->post('package_id',true);
      $where=array('hotel_package_id'=> $data['package_id'],'is_del'=>0);
      $type=$this->Package_hotel_info_model->get_package_detail($where);
           for($i=0;$i<count($data['id']);$i++){
                 foreach($data['id'] as $k=>$v){
                   $dt[$k]=array(
                      'id'=>$data['id'][$k],
                       'is_del'=>0
                     );
               }
               foreach($dt as $k1=>$v1){
                $res[$k1]=$this->Pro_info_model->get_pro_detail($v1);
               }  
               for($i=0;$i<count($res);$i++){
                   unset($res[$i]['id']);
                   unset($res[$i]['package_id']);
                   unset($res[$i]['cate_id']);
                   $res[$i]['package_id']=$data['package_id'];
                   $res[$i]['cate_id']=$type['type'];
               }
                       foreach($res as $k=>$v){
                     $red= $this->Pro_info_model->save_pro_info($v);
               }
             if($red){
                  $result['code'] = 1;
                 $result['msg'] = "复制成功";
                 return $this->ajax_return($result);
             }
     }

    }
    //排序
    public  function pro_order_bay(){
           $this->_get_auth();
        $day_ids = $this->input->post('day_ids', true);
        $day_orders = $this->input->post('day_orders', true);
        foreach ($day_ids as $key => $val) {
            $day_data = array(
                'id' => $val,
                'order_bay' => $day_orders[$key] ? $day_orders[$key] : 999
            );
       $this->Pro_info_model->save_pro_info($day_data);

        }
        $where = array(
            'id' => $day_ids[0],
            'is_del' => 0
        );
        $day_info = $this->Pro_info_model->get_pro_detail($where);

        $result['code'] = 0;
        $result['msg'] = "操作成功";
        return $this->ajax_return($result);
        
    }
    // 套餐保存
    public function package_add() {
        $this->_get_auth();
        $data['page_id'] = $this->input->post('page_id', true);
        $data['city_name'] = $this->input->post('city_name', true);
        $data['package_name'] = $this->input->post('package_name', true);
        if (!$data['city_name']) {
            $result['code'] = 1;
            $result['msg'] = "出发城市为空";
            return $this->ajax_return($result);
        }
        if (!$data['package_name']) {
            $data['package_name'] = DEFAULT_PACKAGE_NAME;
        }
        $data['add_time'] = time();

        $package_id = $this->Package_info_model->save_package_info($data);
        if ($package_id) {
            $this->_delete_cache($data['page_id']);

            $result['code'] = 0;
            $result['package_id'] = $package_id;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
        } else {
            $result['code'] = 1;
            $result['msg'] = "操作失败";
            return $this->ajax_return($result);
        }
    }

    public function package_rename() {
        $this->_get_auth();
        $business_account = $this->_get_auth();
        $package_id = $this->input->post('package_id', true);
        $where_in=array('package_id'=>$package_id,'id_del'=>0);
        $package_infos = $this->Package_info_model->get_package_detail($where);
        $package_name = $this->input->post('package_name', true);
        if ($package_id) {
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
              $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                 'xx'=>'操作城市',
                 'xxx'=>'跟团游',   
                    'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
              $this->log($log);
            $result['code'] = 0;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
        }

        $page_id = $this->input->post('page_id', true);
        $city_name = $this->input->post('city_name', true);
        $old_city_name = $this->input->post('old_city_name', true);
        if ($city_name) {
            $where = array(
                'page_id' => $page_id,
                'city_name' => $old_city_name
            );
            $data = array(
                'city_name' => $city_name
            );
            $this->Package_info_model->update($where, $data);
            $this->_delete_cache($page_id);
                $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                 'xx'=>'操作套餐',
                 'xxx'=>'跟团游',    
                    'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],        
                
            );
              $this->log($log);
            $result['code'] = 0;
            $result['msg'] = "操作成功";
            return $this->ajax_return($result);
        }
    }
    // 套餐删除
    public function package_del() {
        $this->_get_auth();
        $business_account = $this->_get_auth();
        $package_id = $this->input->post('package_id', true);
        if ($package_id) {
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
                       $log=array(
                'name'=>'操作人:'.$business_account['business_account'],
                'package'=>'套餐id'.$package_id,
                 'xx'=>'删除套餐类型',
                 'xxx'=>'跟团游',  
                    'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],               
                
            );
              $this->log($log);
            $result['code'] = 0;
            $result['msg'] = "删除成功";
            return $this->ajax_return($result);
        }

        $page_id = $this->input->post('page_id', true);
        $city_name = $this->input->post('city_name', true);
        if ($city_name) {
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
            foreach ($package_list as $key => $val) {
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
                  $log=array(
              'name'=>'操作人:'.$business_account['business_account'],
                'package'=>'出发城市:'.$city_name,
                   'id'=>'套餐id:'.$val['package_id'],   
                 'xx'=>'删除出发城市',
                 'xxx'=>'跟团游', 
                   'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],           
                
            );
              $this->log($log);
            return $this->ajax_return($result);
        }
    }
    //套餐列表
    public function package_list() {
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
        foreach ($package_list as $key => $val) {
            if ($val['package_name'] == DEFAULT_PACKAGE_NAME) {
                $package_list[$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            } else {
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
    public function package_map_upload() {
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
    public function page_trip($page_id) {
        $data = $this->_get_urls();
        $business_account = $this->_get_auth();
        $data['business_account'] = $business_account;
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $city_name = $this->input->get('city_name', true);
        $package_name = $this->input->get('package_name', true);
        $data['city_list'] = $this->_get_city_list_by_page($page_id);
        foreach ($data['city_list'] as $key => $val) {
            if ($val['city_name'] == DEFAULT_CITY_NAME) {
                $data['city_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            } else {
                $data['city_list'][$key]['show_city_name'] = $val['city_name'];
            }
        }
        if (!$this->_check_city_name($page_id, $city_name)) {
            $city_name = $data['city_list'][0]['city_name'];
        }
        $data['package_list'] = $this->_get_package_list_by_city($page_id, $city_name);
        foreach ($data['package_list'] as $key => $val) {
            if ($val['package_name'] == DEFAULT_PACKAGE_NAME) {
                $data['package_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            } else {
                $data['package_list'][$key]['show_package_name'] = $val['package_name'];
            }
        }
        if (!$this->_check_package_name($page_id, $city_name, $package_name)) {
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
        foreach ($data['trip_list'] as $key => $val) {
            if ($val['city_name'] == DEFAULT_CITY_NAME) {
                $data['trip_list'][$key]['show_city_name'] = DEFAULT_SHOW_CITY_NAME;
            } else {
                $data['trip_list'][$key]['show_city_name'] = $val['city_name'];
            }
            if ($val['package_name'] == DEFAULT_PACKAGE_NAME) {
                $data['trip_list'][$key]['show_package_name'] = DEFAULT_SHOW_PACKAGE_NAME;
            } else {
                $data['trip_list'][$key]['show_package_name'] = $val['package_name'];
            }
            $data['trip_list'][$key]['prev_url'] = '';
            $data['trip_list'][$key]['edit_url'] = base_url('usadmin/package_tour/page_trip_edit') . '/' . $val['day_id'];
            $data['trip_list'][$key]['del_url'] = base_url('usadmin/package_tour/page_trip_del') . '/' . $val['day_id'];
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
        $data['page_trip_add_url'] = base_url('usadmin/package_tour/page_trip_add') . '/' . $page_id . '/' . $package_id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['url']=$this->_tour($page_id);
        $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $data['page_prev_url'] = base_url('home/package_'.($data['info']['page_type']==1?'tour':'travel').'/view/'.$page_id).'/'.$data['info']['share_url'];
        $data['page_qr_url']   = "http://api.etjourney.com/H5info_temp_zyx/qrcode_info?name=".$data['page_prev_url'];
        $this->load->view('usadmin/package_tour/' . $business_account['template_name'] . '/page_trip', $this->_set_common($data));
    }

    //行程删除
    public function page_trip_del($day_id) {
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
        $url = base_url('usadmin/package_tour/page_trip') . '/' . $day_info['page_id'];
        if ($day_info['day_package_id']) {
            $url .= '/' . $day_info['day_package_id'];
        }
        redirect($url);
    }
    //行程修改
    public function page_trip_edit($day_id) {
        $data['menu_list']= $this->_get_menu();
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
        foreach ($data['day_info']['day_view'] as $key => $val) {
            if ($key < 5) {
                $data['day_info']['day_view_one'][] = $val;
            } else {
                $data['day_info']['day_view_two'][] = $val;
            }
        }
        $data['day_info']['day_view_one'] = array_pad($data['day_info']['day_view_one'], 5, '');
        $data['day_info']['day_view_two'] = array_reverse(array_pad($data['day_info']['day_view_two'], 5, ''));
        $data['day_info']['day_fly_data'] = json_decode($data['day_info']['day_fly_data'], true);
        $data['search_fly_url'] = base_url('usadmin/common/search_fly');
        $data['do_page_trip_edit_url'] = base_url('usadmin/package_tour/do_page_trip_edit');
        $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $data['default_day_details'] = DEFAULT_DAY_DETAILS;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit');
        $this->load->view('usadmin/package_tour/page_trip_edit', $this->_set_common($data));
    }
    //行程insert
    public function do_page_trip_edit() {
        $this->_get_auth();
        $data['business_account'] = $this->_get_auth();
         $data_day['day_id'] = $this->input->post('day_id', TRUE);
          $where = array(
            'day_id' => $data_day['day_id']
        );
        $day_infos = $this->Day_info_model->get_day_info_detail($where);
        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach ($day_view_one as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        foreach ($day_view_two as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
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
        foreach ($fly_sn as $key => $val) {
            if (!$val) {
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
            if ($fly_id[$key]) {
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            } else {
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if (!empty($ret)) {
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                } else {
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            if (!$ret) {
                $this->db->trans_rollback();
                echo "<script>alert('添加失败');history.go(-1)</script>";
                return;
            }
            if (!$fly_data['fly_id']) {
                $fly_data['fly_id'] = $ret;
                unset($fly_data['add_time']);
            } else {
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
                $log=array(
                'name'=>'操作人:'.$data['business_account']['business_account'],
                'title'=>'页面id:'. $day_info['page_id'],
               'package'=>'行程id:'.$data_day['day_id'],
               'names'=>'修改前标题:'.$day_infos['day_title'].'   修改后标题: '.$data_day['day_title'],
                'operate'=>'修改行程',
               'xyxx'=>'跟团游',
                  'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],          
                
            );
                $this->log($log);
        $url = base_url("usadmin/package_tour/page_trip") . '/' . $day_info['page_id'] . '?city_name=' . urlencode($package_info['city_name']) . '&package_name=' . urlencode($package_info['package_name']);
        redirect($url);
    }
    //行程添加页面
    public function page_trip_add($page_id, $package_id) {
        $data = $this->_get_urls();
        $data['business_account'] = $this->_get_auth();
        $data['business_all'] = $this->_get_business_all();
        $data['menu_list']= $this->_get_menu();
        $data['page_id'] = $page_id;
        $data['package_id'] = $package_id;
        $data['page_edit_url'] = base_url('usadmin/package_tour/page_edit') . '/' . $page_id;
        $data['page_trip_url'] = base_url('usadmin/package_tour/page_trip') . '/' . $page_id;
        $data['page_poster_url'] = base_url('usadmin/page_poster/index/').'/'.$page_id;
        $data['page_price_url'] = base_url('usadmin/package_tour/page_price') . '/' . $page_id;
        $data['search_fly_url'] = base_url('usadmin/common/search_fly');
        $data['do_page_trip_add_url'] = base_url('usadmin/package_tour/do_page_trip_add');
        $data['default_day_details'] = DEFAULT_DAY_DETAILS;
        $data['url']=$this->_tour($page_id);
        $this->load->view('usadmin/package_tour/page_trip_add', $this->_set_common($data));
    }

    //行程添加
    public function do_page_trip_add() {
        $this->_get_auth();
        $data['business_account'] = $this->_get_auth();
        $day_view_one = $this->input->post('day_view_one', true);
        $day_view_two = $this->input->post('day_view_two', true);
        $day_view_two = array_reverse($day_view_two);
        $day_view = array();
        foreach ($day_view_one as $key => $val) {
            if ($val) {
                $day_view[] = $val;
            }
        }
        foreach ($day_view_two as $key => $val) {
            if ($val) {
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
        foreach ($fly_sn as $key => $val) {
            if (!$val) {
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
            if ($fly_id[$key]) {
                $fly_data['fly_id'] = $fly_id[$key];
                $fly_data['update_time'] = time();
            } else {
                $where = array(
                    'fly_sn' => $fly_sn[$key],
                    'is_del' => 0
                );
                $ret = $this->Fly_info_model->get_fly_info_detail($where);
                if (!empty($ret)) {
                    $fly_data['fly_id'] = $ret['fly_id'];
                    $fly_data['update_time'] = time();
                } else {
                    $fly_data['add_time'] = time();
                }
            }
            $ret = $this->Fly_info_model->save_fly_info($fly_data);
            if (!$ret) {
                $this->db->trans_rollback();
                echo "<script>alert('添加失败');history.go(-1)</script>";
                return;
            }
            if (!$fly_data['fly_id']) {
                $fly_data['fly_id'] = $ret;
                unset($fly_data['add_time']);
            } else {
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
           $log=array(
                'name'=>'操作人:'.$data['business_account']['business_account'],
                'title'=>'页面id:'. $data_day['page_id'],
               'package'=>'套餐id:'.$data_day['day_package_id'],
               'names'=>'页面标题:'.$data_day['day_title'],
                'operate'=>'新增行程',
               'xyxx'=>'跟团游',
                 'ip'=>'ip地址:'.$_SERVER["REMOTE_ADDR"],      
                
            );
           $this->log($log);
        $url = base_url("usadmin/package_tour/page_trip") . '/' . $data_day['page_id'] . '?city_name=' . urlencode($package_info['city_name']) . '&package_name=' . urlencode($package_info['package_name']);
        redirect($url);
    }
  /***
   * ********************************************* 华丽分割线 ： ps : 权限列表 控制器 ↓
   *17/11/13 15:30
    */
// 部门展示
//    public  function group_list(){
//     $business_id=$business_account['business_id'];
//     $where=array('business_id'=>$business_id,'is_del'=>0);
//     $data['list']=$this->Page_group_model->get_group_list($where);
//     // ps：渲染模板
//     
//    }

        //角色列表
    public  function role_list(){
     $data = $this->_get_urls();
     $data['business_account'] = $this->_get_auth();
     $data['business_all'] = $this->_get_business_all();
     $business_id= $data['business_account']['business_id'];
     $data['menu_list']= $this->_get_menu();
     $where=array('business_id'=>$business_id,'is_del'=>0);   
     $data['list']=$this->Page_role_model->get_role_list($where);//ps： 查询角色
     foreach($data['list'] as $k=>$v){
         $where=array('group_id'=>$v['group_id'],'is_del'=>0);
         $group_name=$this->Page_group_model->get_group_detail($where);
         $data['list'][$k]['group_name']=$group_name['group_name'];
         $data['list'][$k]['access_list']=  json_decode($v['access_list']);
     }
     $where=array('business_id'=>$business_id,'is_del'=>0);
     $data['group_list']=$this->Page_group_model->get_group_list($where); // ：查询部门
     $where="father_id !=0 and is_del=0";
     $data['access_list']=$this->User_model->get_all('*',$where,'tl_access_info',$order_title='id'); // 查询所有权限
     $data['group_url']=  base_url('usadmin/package_role/update_group');
     $data['role_url']=  base_url('usadmin/package_role/update_role');
     $data['del_role_url']=base_url('usadmin/package_role/del_role');
     $data['role_upt']=base_url('usadmin/package_role/role_upt');
     $data['group_del']=base_url('usadmin/package_role/group_del');
//           print_r($data['list']);die;
     $this->load->view('usadmin/role/role_list', $this->_set_common($data));
    }
    // 部门删除
    public function group_del(){
       $data['group_id']=$this->input->post('group_id',true);
       $data['is_del']=1; 
       $res=$this->Page_group_model->save_group_info($data);
       if($res){
            $result['errcode'] = 200;
        $result['msg'] = "删除成功";
        $result['data'] = $res;
        return $this->ajax_return($result);
       }else{
             $result['errcode'] = 400;
        $result['msg'] = "删除失败";
       
        return $this->ajax_return($result);
       }

 
    }
   //前台帐号列表
     public  function user_list(){
  
     $data = $this->_get_urls();
     $data['business_account'] = $this->_get_auth();
     $data['business_all'] = $this->_get_business_all();
     $business_id= $data['business_account']['business_id'];
     $data['menu_list']= $this->_get_menu();  
     $where=array('business_id'=>$business_id,'is_del'=>0);   
     $data['list']=$this->Page_role_model->get_role_list($where);//ps： 查询角色
     foreach($data['list'] as $k=>$v){
         $where=array('group_id'=>$v['group_id'],'is_del'=>0);
         $group_name=$this->Page_group_model->get_group_detail($where);
         $data['list'][$k]['group_name']=$group_name['group_name'];
         $data['list'][$k]['access_list']=  json_decode($v['access_list']);
     }
     $where=array('business_id'=>$business_id,'is_del'=>0);
     $data['group_list']=$this->Page_group_model->get_group_list($where); // ：查询部门
//     $where="role_id !=0 and business_id=$business_id and status=0";
//     $data['business_list']=$this->User_model->get_all('*',$where,'us_business_account',$order_title='business_account_id'); // 查询后台账户
     $where=array('business_id'=>$business_id,'order_by' => 'user_id desc');
     $data['user_list']=$this->Page_user_model->get_user_list($where);
     foreach($data['user_list'] as $k=>$v){
         $where=array('group_id'=>$v['group_id'],'is_del'=>0);
         $group=$this->Page_group_model->get_group_detail($where);
         if($group){
           $data['user_list'][$k]['group_name']=$group['group_name'];  
         }else{
            $data['user_list'][$k]['group_name']='默认部门';  
         }
        $where=array('role_id'=>$v['role_id']);
        $role=$this->Page_role_model->get_role_detail($where);
        $data['user_list'][$k]['role_name']=$role['role_name'];
        if($role['is_del']==1){
            $data['user_list'][$k]['role_name']='初始权限';
            $data['user_list'][$k]['role_id']=0;
        }else{
         $data['user_list'][$k]['role_name']=$role['role_name'];    
        }
         
     }
     $data['group_url']=  base_url('usadmin/package_role/update_group');
     $data['role_url']=  base_url('usadmin/package_role/update_role');
     $data['del_role_url']=base_url('usadmin/package_role/del_role');
     $data['role_upt']=base_url('usadmin/package_role/role_upt');
    $data['save_url']=  base_url('usadmin/package_role/save_role');//H5页面提交权限
    $data['user_save']=base_url('usadmin/package_role/user_save');
    //删除用户
    $data['user_del']=base_url('usadmin/package_role/user_del');
    $data['business_id']=$business_id;
//    echo "<pre>";
//     print_r( $data['user_list']);die;
     $this->load->view('usadmin/role/user_list', $this->_set_common($data));
    }
 //后台帐号列表
 public  function admin_list(){
  
     $data = $this->_get_urls();
     $data['business_account'] = $this->_get_auth();
     $data['business_all'] = $this->_get_business_all();
     $data['menu_list']= $this->_get_menu();   
     $business_id= $data['business_account']['business_id'];
     $salt=$data['business_account']['salt'];
//     echo "<pre>";
//     print_r($data);die;
    //根据business_id 获取所有帐号信息
     $where=" business_id=$business_id  and salt=$salt";
    $data['business_list']=$this->User_model->get_all('*',$where,'us_business_account',$order_title='business_account_id'); // 查询后台账户
         foreach($data['business_list']as $k=>$v){
        $where=array('role_id'=>$v['role_id']);
        $role=$this->Page_role_model->get_role_detail($where);
        if($role){
            $data['business_list'][$k]['role_name']=$role['role_name'];  
       
        }else{
         $data['business_list'][$k]['role_name']='初始权限';    
        }  
     }
       $where=array('business_id'=>$business_id,'is_del'=>0);   
     $data['list']=$this->Page_role_model->get_role_list($where);//ps： 查询角色
     $where=array('business_id'=>$business_id,'is_del'=>0);
     $data['group_list']=$this->Page_group_model->get_group_list($where); // ：查询部门
      $data['admin_role']=base_url('usadmin/package_role/admin_role');
      $data['admin_del']=base_url('usadmin/package_role/admin_del');
      $data['business_save']=base_url('usadmin/package_role/business_save');
      $data['business_id']=$business_id;     
      $data['group_id']= $data['business_account']['group_id'];   
     $this->load->view('usadmin/role/admin_list' ,$this->_set_common($data));
 }
    //后台添加用户
    public  function business_save(){
     $user= $this->_get_auth();
    $data['business_account']=$this->input->post('business_account',true);
    $data['password']=md5($this->input->post('password',true).$user['salt']);
    $data['salt']=$user['salt'];
    $data['business_id']=$this->input->post('business_id',true);
    $where=array('business_id'=>$data['business_id'],'business_account'=>$data['business_account']);
    $red=$this->Business_account_model->get_business_account_detail($where);
    if($red){
        echo "<script>alert('添加失败，用户名已存在！');history.back();</script>";  die;
    }
    $data['login_time']=time();
    if($data['business_id']==1){
        $data['is_us']=1;
    }else{
        $data['is_us']=0; 
    }
    $data['role_id']=$this->input->post('role_id',TRUE);
    $res=$this->Business_account_model->save_business_account($data);
    if($res){
       echo "<script>alert('添加成功');location.href='" . base_url("usadmin/package_role/admin_list") . "';</script>";  
    }
    }
    //删除用户
   public  function user_del($user_id,$is_del){
       $data['user_id']= $user_id;
       $data['is_del']=$is_del=='0'?'1':0;
       $tst=$is_del=='0'?'禁用成功':'启用成功';
         $res=$this->Page_register_model->save_register_info($data);
     if($res){
        echo "<script>alert('$tst');location.href='" . base_url("usadmin/package_role/user_list") . "';</script>";
     }   
   }
   //前端 添加权限
    public  function save_role(){

     $user['business_account'] = $this->_get_auth();
     $data['user_id']=$this->input->post('user_id',true);
     $data['group_id'] =  $this->input->post('group_id',true);
      $data['role_id'] =  $this->input->post('role_id',true);
     
         $data['business_id']=$this->input->post('business_id',true)=='undefined'?$user['business_account']['business_id']:$this->input->post('business_id',true);
    
      //  $data['business_id']=$user['business_account']['business_id'];  
    
   
     $data['user_mobile']=$this->input->post('user_mobile',true);
     $data['user_name']=$this->input->post('user_name',true);
     $data['user_email']=$this->input->post('user_email',true);
     $data['update_time']=time();
  
  

    $res=$this->Page_register_model->save_register_info($data);
     if($res){
           $result['code'] = 1;
            $result['msg'] = "编辑成功";
            return $this->ajax_return($result);
     } 
    }
    //后台、、添加权限
       // 添加权限
    public  function admin_role(){
     $data['role_id']=$this->input->post('role_id',true);
     $data['business_account_id']=$this->input->post('business_account_id',true);  
      $data['group_id']=$this->input->post('group_id',true);
     $data['update_time']=time();
    $res=$this->Business_account_model->save_business_account($data);
     if($res){
           $result['code'] = 1;
            $result['msg'] = "编辑成功";
            return $this->ajax_return($result);
     } 
    }
   //后台页面禁言
   public  function admin_del($business_account_id,$status){
       $data['business_account_id']=$business_account_id;
       $data['status']=$status;
       $data['update_time']=time();
      $res=$this->Business_account_model->save_business_account($data);
     if($res){
         if($status==1){
         echo "<script>alert('禁用成功');location.href='" . base_url("usadmin/package_role/admin_list") . "';</script>";
     }else{
          echo "<script>alert('启用成功');location.href='" . base_url("usadmin/package_role/admin_list") . "';</script>"; 
     } 
     
         }
   }
   //添加角色
     public  function user_save(){
   $data = $this->_get_urls();
     $data['business_account'] = $this->_get_auth();
     $data['business_all'] = $this->_get_business_all();
     $data['business_id']= $data['business_account']['business_id'];
     $data['user_name'] =  $this->input->post('user_name',true);
     $data['user_mobile'] =  $this->input->post('user_mobile',true);
     $data['user_email']=$this->input->post('user_email',true);  
     $data['user_pwd']=md5($this->input->post('user_pwd',TRUE).'1314');
     $data['role_id']=0;
     $data['group_id']=$this->input->post('group_id',true);
     $data['role_id']=$this->input->post('role_id',true);
     $data['add_time']=time();
    $res=$this->Page_register_model->save_register_info($data);
     if($res){
       echo "<script>alert('添加成功');location.href='" . base_url("usadmin/package_role/user_list") . "';</script>";
     } 
    }
    // 权限管理
    //添加部门
    public  function update_group(){
       $data['business_account'] = $this->_get_auth();   
       $data['business_id']= $data['business_account']['business_id'];
       $data['group_id']=$this->input->post('group_id',true);//ps 存在的时候为修改 ， 不存在 为添加
       if(empty($data['business_id'])){
           $result['code'] =1;
            $result['msg'] = "操作失败,商户ID不能为空";
            return $this->ajax_return($result);
       }
       $where=array('business_id'=>$data['business_id']);
       $res=$this->Business_info_model->get_business_info_detail($where);
       if(!$res){
          $result['code'] = 1;
            $result['msg'] = "操作失败,商户ID不存在";
            return $this->ajax_return($result);
       }
       $data['group_name']=$this->input->post('group_name',true);
       if(empty($data['group_id'])){
            $data['add_time']=time();  
       }else{
            $data['update_time']=time();  
       }
       $res=$this->Page_group_model->save_group_info($data);
       if($res){
         $result['code'] = 0;
            $result['msg'] = "添加成功";
            return $this->ajax_return($result);
       }
    }
    // 添加职位
    public function update_role(){
      $data['role_id']= $this->input->post('role_id',TRUE); 
       $data['business_account'] = $this->_get_auth();   
        $data['business_id']= $data['business_account']['business_id'];
        if(empty($data['business_id'])){
         echo "<script>alert(操作失败， 商户ID不能为空);</script>";
          return; 
       }  
      
       $where=array('business_id'=>$data['business_id']);
       $res=$this->Business_info_model->get_business_info_detail($where);
       if(!$res){
         echo "<script>alert(操作失败，商户ID不存在);</script>";
          return; 
       }
      $data['group_id']= $this->input->post('group_id',TRUE);
       $data['role_name']=$this->input->post('role_name',true);
    
       $data['access_list']=  json_encode($this->input->post('access_id',true));  

  
      $data['role_level']=$this->input->post('role_level',TRUE);
      if(empty($data['role_id'])){
        $data['add_time']=time();  
      }else{
        $data['update_time']=time();   
      }
       $res=$this->Page_role_model->save_role_info($data);
            if($res){
        echo "<script>alert('添加成功');location.href='" . base_url("usadmin/package_role/role_list") . "';</script>";
       
       }
        
    }
    // 
    public  function role_upt(){
       $data['role_id']= $this->input->post('role_id',TRUE);  
       $data['access_list']= $this->input->post('access_id',true) ;
       $res=$this->Page_role_model->save_role_info($data);
       if($res){
          $result['code'] = 1;
          $result['msg'] = "修改成功";
          return $this->ajax_return($result);  
         
       }
       
    }
    //删除角色
    public  function del_role($role_id){
        $data['role_id']=$role_id; 
        $data['is_del']=1;
       $res=$this->Page_role_model->save_role_info($data); 
       if($res){
            echo "<script>alert('删除成功');location.href='" . base_url("usadmin/package_role/role_list") . "';</script>";
               return; 
       }
    }
    // 展示 权限列表
    public  function access_list(){
   $where='father_id !=0 AND is_del=0'; 
  $data['list']= $this->Page_access_model->get_access_list($where);
  // 页面模板调用
    }
   /**
     *17/11/13 15:30
    * **********************************************  华丽分割线 ps: 权限控制器  ↑   
    */
    //单图片处理 
    private function upload_image($filename, $fileurl, $key = 'time') {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/' . $fileurl)) {
            if (!mkdir('./public/images/' . $fileurl)) {
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        switch ($file['type']) {
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
                break;
        }

        if ($br) {
            if ($key == 'time') {
                $key = md5(rand(1, 99999) . time());
            }
            $pic_url = "./public/images/" . $fileurl . "/" . $key . $br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            return "/public/images/" . $fileurl . "/" . $key . $br;
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
    private function upload_files($filename, $fileurl, $key = 'time') {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/travel/' . $fileurl)) {
            if (!mkdir('./public/travel/' . $fileurl)) {
                return FALSE;
            }
        }

        $file = $_FILES[$filename];
        $info = pathinfo($file['name']);
        $br = '.' . $info['extension'];
        if (!in_array($br, array('.doc', '.docx', '.pdf'))) {
            return '';
        }

        if ($key == 'time') {
            $key = md5(rand(1, 99999) . time());
        }
        $pic_url = "./public/travel/" . $fileurl . "/" . $key . $br;
        move_uploaded_file($file['tmp_name'], $pic_url);
        return "/public/travel/" . $fileurl . "/" . $key . $br;
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
        if (empty($url_data)) {
            $base_url = site_url($this->uri->uri_string());
        } else {
            $base_url = site_url($this->uri->uri_string() . '?' . http_build_query($url_data));
        }

        $this->pagination->usadmin_page(array('base_url' => $base_url, 'per_page' => $per_page, 'total_rows' => $row['total']));

        $link = $this->pagination->create_links();

        if (!empty($link)) {
            $link = $this->pagination->total_tag_open . $link . $this->pagination->total_tag_close;
        }

        return array('total' => $row['total'], 'link' => $link);
    }

    private function _check_city_name($page_id, $city_name) {
        if (!$city_name) {
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        if (empty($city_list)) {
            return false;
        }
        return true;
    }

    private function _check_package_name($page_id, $city_name, $package_name) {
        if (!$package_name) {
            return false;
        }
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'package_name' => $package_name
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        if (empty($package_list)) {
            return false;
        }
        return true;
    }

    private function _get_city_list_by_page($page_id) {
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'package_id asc',
            'group_by' => 'city_name'
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        return $city_list;
    }

    private function _get_package_list_by_city($page_id, $city_name) {
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'city_name' => $city_name,
            'order_by' => 'package_id asc'
        );
        $package_list = $this->Package_info_model->get_package_list($where);
        return $package_list;
    }

    private function _get_new_day_info($day_id, $page_id = false, $package_id = false) {
        $where = array(
            'day_id' => $day_id
        );
        $day_data = $this->Day_info_model->get_day_info_detail($where);
        $new_day_info = $day_data;
        if ($page_id) {
            $new_day_info['page_id'] = $page_id;
        }
        if ($package_id) {
            $new_day_info['day_package_id'] = $package_id;
        }
        $new_day_info['add_time'] = time();
        unset($new_day_info['day_id']);
        unset($new_day_info['update_time']);
        return $new_day_info;
    }

    //获取页面通用链接
    private function _get_urls() {
        $data['login_out_url'] = base_url('usadmin/business/login_out');
        $data['pwd_edit_url'] = base_url('usadmin/package_tour/pwd_edit');
        $data['change_business_url'] = base_url('usadmin/business/change_business');
        $data['package_tour_url'] = base_url('usadmin/package_tour/index');
        $data['free_tour_url'] = base_url('usadmin/free_tour/index');
        $data['page_monitor_index_url'] = base_url('usadmin/package_tour/page_monitor_index');
        $data['role_list_url'] = base_url('usadmin/package_role/role_list');
        $data['user_list_url'] = base_url('usadmin/package_role/user_list');
        
        return $data;
    }
    // 左侧列表菜单
private function _get_menu() {
      
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
             if(in_array('5003',$data['access_list'])){
        $data['menu']=array(
           'H5页面'=>array(
               '0'=>array(
                     'url'=>base_url('usadmin/package_tour/index'),
                     'title'=>'跟团游',
               ),
              '1'=>array(
                  'url'=>base_url('usadmin/package_tour/page_monitor_index'),
                  'title'=>'自由行',
              ) 
           ),    
        );
        } 

       
         if(in_array('5001',$data['access_list'])){
         $data['menu']['订单列表'][0]= array(
                   'url'=>base_url('usadmin/Package_user_list'),
                  'title'=>'订单列表',  
              );
        }
        if(in_array('1004',$data['access_list'])){
         
             $data['menu']['权限管理'][0]= array(
                   'url'=>base_url('usadmin/package_role/role_list'),
                  'title'=>'职位管理',   
              );
        }
             if(in_array('3001',$data['access_list'])){
      
              $data['menu']['权限管理'][1]= array(
                   'url'=>base_url('usadmin/package_role/user_list'),
                  'title'=>'销售员管理',  
              );
        }
          if(in_array('3002',$data['access_list'])){
         $data['menu']['权限管理'][2]= array(
                   'url'=>base_url('usadmin/package_role/admin_list'),
                  'title'=>'后台用户管理',  
             );
        }
                               if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][0]= array(
                   'url'=>base_url('cg/car_purchase/index'),
                  'title'=>'页面管理',  
             );
        }
                   if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][1]= array(
                   'url'=>base_url('cg/car_purchase/car_page_info'),
                  'title'=>'导游管理',  
             );
        }
             if(in_array('6001',$data['access_list'])){
         $data['menu']['车购页面管理'][2]= array(
                'url'=>base_url('cg/car_purchase/car_goods_order'),
                  'title'=>'订单管理',  
             );
        }
                       if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][0]= array(
                'url'=>'http://new.cnzz.com/v1/login.php?siteid=1258510548" target="_blank"',
                  'title'=>'cszz统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][1]= array(
                'url'=>' https://tongji.baidu.com/web/24998121/visit/toppage?siteId=11585653" target="_blank"',
                  'title'=>'百度统计',  
             );
        }
                if(in_array('1006',$data['access_list'])){
         $data['menu']['统计列表'][2]= array(
                'url'=>'https://portal.qiniu.com/cdn/refresh-prefetch" target="_blank"',
                  'title'=>'刷新缓存',  
             );
        }

        return $data['menu'];
    }

    //校验登录
    private function _get_auth() {
        $business_account_id = $this->my_session->get_session('business_account_id', SESSION_KEY_PRE);
        if (!$business_account_id) {
            redirect(base_url('usadmin/business/login'));
        }
        $where = array(
            'business_account_id' => $business_account_id
        );
        $business_account = $this->Business_account_model->get_business_account_detail($where);
        if (empty($business_account)) {
            redirect(base_url('usadmin/business/login'));
        }
        $business_id = $this->my_session->get_session('business_id', SESSION_KEY_PRE);
        if (!$business_id) {
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
    private function _get_business_all($type = 1) {
        $where = array(
            'business_type' => $type
        );
        $business_all = $this->Business_info_model->get_business_info_list($where);
        return $business_all;
    }

    //获取商户
    private function _get_business_detail($business_id) {
        $data['business_id'] = $business_id;
        $business_all = $this->Business_info_model->get_business_info_detail($data);
        return $business_all;
    }

    private function _delete_cache($page_id) {
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $cache_key = US_PACKAGE_TOUR_CACHE_KEY . $page_id;
        $this->cache->delete($cache_key);
    }

    //获取通用头部左侧菜单
    private function _set_common($data) {
        $data['header'] = $this->load->view('usadmin/common/header', $data, true);
        $data['menu'] = $this->load->view('usadmin/common/menu', $data, true);
        //$data['show_count_code'] = $this->show_count_code();
        $data['footer'] = $this->load->view('usadmin/common/footer', $data, true);
        return $data;
    }

    //时间控件
    private function _timex() {
        $time = strtotime(date('Y', time()) . '-' . date('n', time()) . '-1');
        $timeend = strtotime('+11 month', $time);
        $data['date']['cal'][] = array(
            'year' => date('Y', $time),
            'month' => date('n', $time),
            'month_cn' => $this->common->get_month_cn(date('n', $time)),
            'week_first' => date("w", mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
            'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time)))),
            'all_days' => date('t', $time),
            'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', $time), 1, date('Y', $time))),
        );

        while (date('Y', $time) < date('Y', $timeend) OR date('n', $time) < date('n', $timeend)) {
            $data['date']['cal'][] = array(
                'year' => date('Y', strtotime('+1 month', $time)),
                'month' => date('n', strtotime('+1 month', $time)),
                'month_cn' => $this->common->get_month_cn(date('n', strtotime('+1 month', $time))),
                'week_first' => date("w", mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time)))),
                'week_fin' => date("w", strtotime('+1 month -1 day', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))),
                'all_days' => date('t', strtotime('+1 month', $time)),
                'time' => strtotime('+1 month -1 day +23 hour', mktime(0, 0, 0, date('n', strtotime('+1 month', $time)), 1, date('Y', strtotime('+1 month', $time))))
            );
            $time = strtotime('+1 month', $time);
        }

        return $data['date']['cal'];
    }
    // 跟团游导航栏菜单
    private function _tour($page_id){
 
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]= array(
                'url'=>base_url('usadmin/package_tour/page_edit/'.$page_id),
                'title'=>'基础内容',
            );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
             'url'=>base_url('usadmin/package_tour/page_trip/'.$page_id),
              'title'=>'行程编辑',   
             );
        }
        if(in_array('2004',$data['access_list'])){
         $data['tour'][2]=array(
              'url'=>  base_url('usadmin/package_tour/page_price/'.$page_id),
              'title'=>'价格上传'
          );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array(
              'url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑'
          );    
        }
        
        return $data['tour'];
    }
  //自由行导航栏菜单
    
    private function _free($page_id){
       
         //头图编辑-2002  基础内容-2003   价格上传-2004    行程上传/产品编辑-2005   导航编辑-2006
        $access = $this->_is_access();
        $data['access_list'] = $access['access_list'] ;
        
        if(in_array('2003',$data['access_list'])){
         $data['tour'][0]=array(
         'url'=>base_url('usadmin/package_tour/free_page_edit/'.$page_id),
          'title'=>'基础内容',    
        );
        }
        if(in_array('2005',$data['access_list'])){
            $data['tour'][1]=array(
                 'url'=>base_url('usadmin/package_tour/article/'.$page_id),
              'title'=>'产品编辑',   
            );
        }
        if(in_array('2006',$data['access_list'])){
         $data['tour'][2]=array(
             'url'=>  base_url('usadmin/package_tour/addmenu/'.$page_id),
              'title'=>'导航编辑'  
         );   
        }
        if(in_array('2002',$data['access_list'])){
         $data['tour'][3]=array('url'=>  base_url('usadmin/page_poster/index/'.$page_id),
              'title'=>'头图编辑');    
        }
        
        return $data['tour'];
    }
   public  function log($list= array()){
    $file  ='zweb/log/'.date('Y-m-d',time()).'_log.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
    $time=  date('Y-m-d H:i:s',  time());
   $content = $time.'  :'.implode(' ',$list)."\n";

  file_put_contents($file, $content,FILE_APPEND);// 这个函数支持版本(PHP 5) 
     file_get_contents($file); // 这个函数支持版本(PHP 4 >= 4.3.0, PHP 5) 
 
        
    }
 public  function text(){  
$file_path = 'zweb/log/'.date('Y-m-d',time()).'_log.txt';
if(file_exists($file_path)){
$file_arr = file($file_path);
for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容
echo $file_arr[$i]."<br />";
}
}


    }

    
// 取用户权限数据
  private function _is_access($role=''){
       $business_account = $this->_get_auth();
       $where=array(
            'role_id'=>$business_account['role_id'],
            'is_del'=>0,
        );
 
      $access_list = $this->Page_role_model->get_role_detail($where);
      $access_list = json_decode($access_list['access_list'], true);

      if(in_array($role,$access_list)){ 
          
         return array('access'=>true , 'access_list' =>$access_list );
      }else{
         return array('access'=>false , 'access_list' =>$access_list );
      }
  }
  
//    public function arrays(){
////        $data['page_id']=74;
////        $h5_video=array(
////          '0'=>array(
////              'video'=>'http://image.etjourney.com/us/111.mp4',
////              'image'=>'http://image.etjourney.com/video_image/111.png',  
////          ), 
////            '1'=>array(
////               'video'=>'http://image.etjourney.com/us/222.mp4',
////              'image'=>'http://image.etjourney.com/video_image/222.png',   
////          ) , 
////                '2'=>array(
////             'video'=>'http://image.etjourney.com/us/333.mp4',
////              'image'=>'http://image.etjourney.com/video_image/333.png',  
////          ) ,
////    
////    
////);
////        $data['data_video']=  json_encode($h5_video);
////    $this->Page_info_model->save_page_info($data);                
////    }
//     $data['url']=  base_url('usadmin/package_tour/xiuxiu');   
//   $this->load->view('usadmin/page_monitor/xiuxiu',$data);
//}
//public function xiuxiu(){
//    $data['page_id']=120;
//     if (isset($_FILES['Filedata']) && $_FILES['Filedata']['error'] == 0) {
//            $data['h5_image'] = $this->upload_image('Filedata', 'H5image');
//        }
//       $xiuxiu= $this->Page_info_model->save_page_info($data);
//    if($xiuxiu){
//        return $this->ajax_return(上传成功);
//}
//
//
//}
//public  function tianqi(){
// header("Location: http://www.sojson.com/open/api/weather/json.shtml?city=上海"); 
//die;
//
//   
//
//
//}
    public  function redis(){
  
   $redis = new Redis();
   $redis->connect('127.0.0.1', 6379);
   echo "Connection to server sucessfully";
         //查看服务是否运行
   echo "Server is running: " . $redis->ping();
    }
}
