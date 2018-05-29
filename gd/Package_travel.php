<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('US_PACKAGE_TOUR_CACHE_KEY', 'us_package_travel_');
define('US_PACKAGE_TOUR_CACHE_TIME', 24*60*60);
define('DEFAULT_CITY_NAME', '__DEFAULT__');
define('DEFAULT_PACKAGE_NAME', '__DEFAULT__');
define('HTTP_OK', 200);
define('HTTP_ERROR', 400);
define('DEFAULT_RS_NAMESPACE', 'normal_normal');
class Package_travel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('us/Page_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('us/Pro_cate_model');
        $this->load->model('us/Pro_navigation_model');
        $this->load->model('H5_Day_info_model');
        $this->load->model('H5_Fly_info_model');
        $this->load->model('us/Pro_info_model');
         $this->load->model('tl/Page_user_model');
        $this->load->model('Wx_account_model');
       $this->load->model('us/Package_hotel_info_model');

        $this->load->helper('url');
        $this->load->library('common');
    }

    public function index()
    {
     
        $filename = $this->uri->segment(3);
     
        $data['signPackage'] = $this->Wx_account_model->wx_js_para(3);
        if(preg_match('/^uzai-(.+)$/', $filename, $match)){
            $this->load->view('package_travel/uzai/'.$match[1], $data);
        }else{
         
            $this->load->view('package_travel/normal/'.$filename, $data);
        }
        $this->show_count(); 
    }

    public function view($page_id){
        $data['page_id'] = $page_id;
        $data['signPackage'] = $this->Wx_account_model->wx_js_para(3);
		$data['seller'] = $this->input->get('s', true);
		$data['seller'] = $data['seller']?$data['seller']:$this->input->get('u', true);	//分销用u传递销售员。
        $where= array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        $is_pay = $page_info['is_pay'] ? $page_info['is_pay'] : '';
		$data['business_id'] = $page_info['business_id'];
		$data['share_title'] = $page_info['short_title'];
		$data['index_url'] =  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     $data['resource'] = $this->_load_resource($page_info['template_type'].'_'.$page_info['style_type']);
      $aaa=  $this->load->view('home/package_travel/'.$page_info['template_type'].'/index', $data);
        $this->show_count();
    }

    public function view_data($page_id){
       
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

        $no_cache = $this->input->get('no_cache', true);
        if(!$no_cache){
            $cache_key = US_PACKAGE_TOUR_CACHE_KEY.$page_id;
            if($result = $this->cache->get($cache_key)){
                return $this->ajax_return(json_decode($result, true));
            }
        }

        $data = array();


      $page_info = $this->_page_info($page_id);
        $data['share'] = array(
            'title' => $page_info['page_title'],
            'link' => base_url('home/package_travel/view').$page_info['share_url'],
            'image' => $page_info['share_image'],
            'desc' => $page_info['share_desc']
        );
      
        $data['service'] = $page_info['kf_data'];
        $u = $this->input->get('u', true);
        if (!empty($u)) {
            $kf = $this->kefu($u);
            $data['service']['user']['mobile'] = $kf['user_mobile'];
            $data['service']['user']['wx'] = $kf['user_wx'];
            $data['service']['user']['user_name'] = $kf['user_name'];
        }

        $data['data'] = array();
        $data['data']['page_info'] = array(
            'page_id' => $page_info['page_id'],
            'title' => $page_info['page_title'],
            'head_image' =>$page_info['h5_image']?$page_info['h5_image']:$page_info['top_image'],
            'spec_image' => $page_info['spec_image'],
            'travel_file' => $page_info['fileupload'],
            'inst_content' => $page_info['inst_data'],
            'h5_vodie' => $page_info['h5_vodie'],
			'is_pay' => $page_info['is_pay'],
            'is_evaluate'=> $page_info['is_evaluate'],
			'unit' => $page_info['unit']?$page_info['unit']:'￥',
            'data_video' => json_decode($page_info['data_video']),
            'min_price' => $package_list['min_price'],
            'style'=>$page_info['template_type'].'/'.$page_info['style_typle']
        );
        $data['package_list']= $this->pro_navigation($page_id);
        $data['errorcode'] = HTTP_OK;
        $data['modified'] = time();

        $this->cache->save($cache_key, json_encode($data), US_PACKAGE_TOUR_CACHE_TIME);
        $this->ajax_return($data);
    }

    public function show($page_id){

        $data['page_info'] = $this->_page_info($page_id);
        $data['page_info']['share_url'] = base_url('home/package_tour/show').$data['page_info']['share_url'];

        $data['day_data'] = $this->_day_data($page_id); 
        $data['order_url']=base_url('home/package_tour/order_add');    
        $data['signPackage'] = $this->Wx_account_model->wx_js_para(3);

        $is_pay = $data['page_info']['is_pay'] ? $data['page_info']['is_pay'] : '';
        $this->load->view('package_tour/'.$data['page_info']['template_type'].'/index'.$is_pay, $data);
        $this->show_count();
    }
    
    public  function order_add(){
        $data['order_title']=$this->input->post('title',true);
        $data['company_id']=$this->input->post('company_id',true);
        $data['price']=$this->input->post('price',true);
        $data['godate']=$this->input->post('godate',true);
        $this->load->view('package_tour/uzai/order',$data);    
    }

    private function _page_info($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_show' => 0
        );
        $page_info = $this->Page_info_model->get_page_info_detail($where);
        $image_data = json_decode($page_info['image_data'], true);
        $page_info['top_image'] = isset($image_data['top']) ? $image_data['top'] : '';
        $page_info['share_image'] = isset($image_data['share']) ? base_url($image_data['share']) : '';
        $page_info['spec_image'] = isset($image_data['spec']) ? $image_data['spec'] : '';
        $page_info['fileupload'] = isset($image_data['fileupload']) ? $image_data['fileupload'] : '';
        $page_info['day_top_image'] = isset($image_data['day']) ? $image_data['day'] : '';
        $page_info['share_url'] = '/'.$page_info['page_id'].'/'.$page_info['share_url'];

        $page_info['kf_data'] = json_decode($page_info['kf_data'], true); 

        if(empty($page_info['date'])){
            $page_info['date_choose'] = array();
            $page_info['min_price'] = 0;
            $page_info['cal'] = array();
            return $page_info;
        }

        $page_info['price']=json_decode($page_info['date'],TRUE);
        $page_info['date_choose'] = $page_info['price']['date'];
        $page_info['min_price'] = min(array_values($page_info['date_choose']));
        $choose_date = array_keys($page_info['date_choose']);
        foreach($choose_date as $key => $val){
            $choose_date[$key] = strtotime($val);
        }
        $max_date = max($choose_date);
        if($max_date + 24*60*60 < time()){
            $page_info['date_choose'] = array();
            $page_info['min_price'] = 0;
            $page_info['cal'] = array();
            return $page_info;
        }
        $min_data = min($choose_date);
        if($min_data + 24*60*60 < time()){
            $min_data = time();
        }

        $max_year = date('Y', $max_date);
        $max_month = date('n', $max_date);
        $timeend_month = ($max_year - date('Y', $min_data))*12 + $max_month-date('n', $min_data);
        
        $time = strtotime(date('Y',$min_data).'-'.date('n',$min_data).'-1');
        $timeend = strtotime('+'.$timeend_month.' month', $time);
        $page_info['cal'][]=array(
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
            $page_info['cal'][] =array(
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
        return $page_info;
    }

    private function _package_list($page_id){
        $result = array(
            'min_price' => 0,
            'list' => array()
        );

        $package_list = array();
        $min_price = array();
        $city_list = $this->_get_city_list_by_page($page_id);
        foreach($city_list as $key => $val){
            $package_list[$key]['city'] = $val['city_name'];
            $packages = $this->_get_package_list_by_city($page_id, $val['city_name']);
            foreach($packages as $key1 => $val1){
                $package_list[$key]['package'][$key1]['id'] = $val1['package_id'];
                $package_list[$key]['package'][$key1]['name'] = $val1['package_name'];
                $package_list[$key]['package'][$key1]['map'] = $val1['package_map'] ? $val1['package_map'] : '';
                $cal_price = $this->_cal_price($val1['date_price']);
                $package_list[$key]['package'][$key1]['price'] = $cal_price;
                if(!empty($cal_price['min_price'])){
                    $min_price[] = $cal_price['min_price'];
                }
                $trips = $this->_get_trip_list_by_package($val1['package_id']);
                foreach($trips as $key2 => $val2){
                    $package_list[$key]['package'][$key1]['trip'][$key2]['title'] = $val2['day_title'];
                    $package_list[$key]['package'][$key1]['trip'][$key2]['image'] = $val2['day_image'] ? $val2['day_image'] : '';
                    $package_list[$key]['package'][$key1]['trip'][$key2]['view'] = json_decode($val2['day_view'], true);
                    $package_list[$key]['package'][$key1]['trip'][$key2]['content'] = $val2['day_content'];
                    $package_list[$key]['package'][$key1]['trip'][$key2]['fly'] = json_decode($val2['day_fly_data'], true);
                }
            }
        }
        if(!empty($min_price)){
            $result['min_price'] = min($min_price);
        }
        $result['list'] = $package_list;
        return $result;
    }

    private function _get_city_list_by_page($page_id){
        $where = array(
            'page_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'package_id asc',
            'group_by' => 'city_name'
        );
        $city_list = $this->Package_info_model->get_package_list($where);
        if(count($city_list) > 1){
            foreach($city_list as $key => $val){
                if($val['city_name'] == DEFAULT_CITY_NAME){
                    unset($city_list[$key]);
                }
            }
            $city_list = array_values($city_list);
        }
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
        if(count($package_list) > 1){
            foreach($package_list as $key => $val){
                if($val['package_name'] == DEFAULT_CITY_NAME){
                    unset($package_list[$key]);
                }
            }
            $package_list = array_values($package_list);
        }
        return $package_list;
    }

    private function _cal_price($date_price){
        $cal_price = array();
        if(empty($date_price)){
            return $cal_price;
        }
        $date_price = json_decode($date_price, true);
        if(!$date_price || empty($date_price)){
            return $cal_price;
        }
        $cal_price['remarks'] = $date_price['remarks'];

        $date_choose = $date_price['date'];
        if(empty($date_choose)){
            return $cal_price;
        }

        $today = strtotime(date('Y-m-d', time()));
        $later_date_choose = array();
        foreach($date_choose as $key => $val){
            $month = date('Y-n', strtotime($key));
            if(strtotime($key) >= $today AND !empty($val)){
                $later_date_choose[$month][] = $val;
            }
        }
        $later_min_price = array();
        foreach($later_date_choose as $key => $val){
            $later_min_price[$key] = min($val);
        }
        $cal_price['min_price'] = min(array_values($later_min_price));
        $choose_date = array_keys($date_choose);
        foreach($choose_date as $key => $val){
            $choose_date[$key] = strtotime($val);
        }
        $min_data = min($choose_date);
        if($min_data + 24*60*60 < time()){
            $min_data = time();
        }

        $max_date = max($choose_date);
        if($max_date + 24*60*60 < time()){
            return $cal_price;
        }
        $max_year = date('Y', $max_date);
        $max_month = date('n', $max_date);
        $timeend_month = ($max_year - date('Y', $min_data))*12 + $max_month-date('n', $min_data);

        $time = strtotime(date('Y',$min_data).'-'.date('n',$min_data).'-1');
        $timeend = strtotime('+'.$timeend_month.' month', $time);
        $cal_info[] = array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend)){
            $cal_info[] =array(
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
        $cal = array();
        foreach($cal_info as $key => $val){
            $cal[$key]['year'] = $val['year'];
            $cal[$key]['month'] = $val['month'];
            $cal[$key]['month_cn'] = $val['month_cn'];
            $year_month = $val['year'].'-'.$val['month'];
            $cal[$key]['min_price'] = $later_min_price[$year_month];
            for($i=0; $i<$val['week_first']; $i++){
                $cal[$key]['item'][] = array('' => '');
            }
            for($i=1; $i<=$val['all_days']; $i++){
                $ymi = $val['year'].'-'.$val['month'].'-'.$i;
                if((strtotime($ymi)>=$today) AND (array_key_exists($ymi, $date_choose))){
                    $cal[$key]['item'][] = array($ymi => $date_choose[$ymi]);
                }else{
                    $cal[$key]['item'][] = array($ymi => '');
                }
            }
        }
        $cal_price['cal'] = $cal;
        return $cal_price;
    }



    private function _day_data($page_id){
        $where = array(
            'monitor_id' => $page_id,
            'is_del' => 0,
            'order_by' => 'day_order asc, day_id asc'
        );
        $day_data = $this->H5_Day_info_model->get_day_info_list($where);
        foreach($day_data as $key => $val){
            $day_data[$key]['day_count'] = json_decode($val['day_count'], true);
            $day_data[$key]['line_html'] = $this->_line_html($day_data[$key]['day_count']['line']);
            $day_data[$key]['fly_data'] = $this->_fly_data($val['day_id']);
        }
        return $day_data;
    }

    private function _fly_data($day_id){
        $where = array(
            'day_id' => $day_id
        );
        $fly_data = $this->H5_Fly_info_model->get_fly_info_list($where);
        return $fly_data;
    }

    private function _line_html($line_arr){
        if(empty($line_arr)){
            return '';
        }
        $line_html = '';
        $length = count($line_arr);
        $mid = 0;
        $class = '';
        switch($length){
            case 2:
                $class = 'two';
                break;
            case 3:
                $class = 'three';
                break;
            case 4:
            case 5:
            case 6:
                $class = 'three';
                $mid = 3;
                break;
            case 7:
            case 8:
                $class = 'four';
                $mid = 4;
                break;
            case 9:
            case 10:
                $class = 'five';
                $mid = 5;
                break;
        }
        $line_html .= '<ul class="step '.$class.'">';
        if(!$mid){
            for($i=0; $i<$length;$i++){ //2或3个
                if($i == 0){
                    $line_html .= '<li class="first star"><span>'.$line_arr[$i].'</span><p></p></li>';
                }elseif($i == $length-1){
                    $line_html .= '<li class="end"><span>'.$line_arr[$i].'</span><p></p></li>';
                }else{
                    $line_html .= '<li><span>'.$line_arr[$i].'</span><p></p></li>';
                }
            }
        }else{
            for($i=0; $i<$mid;$i++){ //遍历上一行
                if($i == 0){
                    $line_html .= '<li class="first star"><span>'.$line_arr[$i].'</span><p></p></li>';
                }elseif($i == $mid-1){
                    $line_html .= '<li class="end rightline"><span>'.$line_arr[$i].'</span><p></p></li>';
                }else{
                    $line_html .= '<li><span>'.$line_arr[$i].'</span><p></p></li>';
                }
            }
            if($length - $mid == 1){ //4个遍历下一行
                for($i=0; $i<$mid;$i++){
                    if($i == $mid-1){
                        $line_html .= '<li class="only"><p></p><span>'.$line_arr[$i].'</span></li>';
                    }else{
                        $line_html .= '<li></li>';
                    }
                }
            }else{ // 5,6,7,8,9,10遍历下一行
                for($i=$mid*2-1; $i>=$mid;$i--){
                    if($i == $mid){
                        $line_html .= '<li class="end"><p></p><span>'.$line_arr[$i].'</span></li>';
                    }elseif($i == $length -1){
                        $line_html .= '<li class="last"><p></p><span>'.$line_arr[$i].'</span></li>';
                    }else{
                        if(!isset($line_arr[$i])){
                            $line_html .= '<li></li>';
                        }else{
                            $line_html .= '<li><p></p><span>'.$line_arr[$i].'</span></li>';
                        }
                    }
                }
            }
        }

        $line_html .= '</ul>';
        return $line_html;
    }

    private function _load_resource($namespace){
        $this->load->config('resource');
        $config = config_item('resource');
        if(!$namespace){
            $namespace = DEFAULT_RS_NAMESPACE;
        }
        if(!isset($config[$namespace])){
            $namespace = DEFAULT_RS_NAMESPACE;
        }
        $rs = $config[$namespace];
        $resource = array();
        $css = '';
        $js = '';
        foreach($rs['css'] as $key => $val){
            $css .= '<link rel="stylesheet" href="'.$val.'?'.time().'">';
        }
        foreach($rs['js'] as $key => $val){
            $js .= '<script src="'.$val.'?'.time().'"></script>';
        }
        $resource['css'] = $css;
        $resource['js'] = $js;
        return $resource;
    }
    
    private function _img_replace($content){
        $pattern = '/<img([^>]*?)src="([^\"]*?)"/i';
        $img = '<img${1}src="'.base_url('home/common/image_resize').'?imgUrl=${2}" class="lazy" data-original="${2}"';
        return preg_replace($pattern, $img, $content);
    }

    private function print_t($param){
        echo '<pre>';
        print_r($param);
        echo '</pre>';
    }  
   //一级栏目
   private function pro_navigation($page_id){
       $where=array('page_id'=>$page_id,'is_del'=>0);
       $res=$this->Pro_navigation_model->get_navigation_detail($where);
       $menu=  json_decode($res['pro_info'],true);
         foreach($menu as $k=>$v){
             $package_list[$k]['menu']=$v['name'];
              foreach($v['type'] as $k1=>$v1){
                 
                   $package_list[$k]['column'][$k1]['name']=$v1['name'];
                    foreach( $v1['list'] as $k2=>$v2){
                        $package_list[$k]['column'][$k1]['package_list'][$k2]['id']=$v2;
                         $where=array('hotel_package_id'=>$v2,'is_del'=>0);
                         $package=$this->Package_hotel_info_model->get_package_detail($where);
                         if($package){
                                $package_list[$k]['column'][$k1]['package_list'][$k2]['name']=$package['package_name'];
                            $where=array('package_id'=>$v2,'is_del'=>0,'order_by' => 'order_bay asc,id asc');
                            $items=$this->Pro_info_model->get_pro_list($where);

                            $item =[];
                           foreach( $items as $k3=>$v3){
                               $item['id'] = $v3['id'];
                               $item["pro_title"] = $v3["pro_title"]?$v3["pro_title"]:$v3["pro_name"];
                               $item["type"] =$v3["cate_id"];
                               $item["cate_name"] =$this->_type_name($v3["cate_id"]);
							   $item["model_id"] =$v3["model_id"];
                               $item["image"] = $v3["pro_image"];
                               $item["pro_space"] = array_filter(json_decode($v3["pro_space"],true));
                               $item["pro_unit"] = $v3["pro_unit"];
                               $item["pro_sum"] = $v3["pro_sum"];
                               $item["url"] = $v3["url"];
                               $item["pro_luggage"] = $v3["pro_luggage"];
                               $item["pro_name"] = $v3["pro_name"];
                               $item["pro_eng_name"] = $v3["pro_eng_name"];
                               $item["pro_grade"] = $v3["pro_grade"];
							   $item["form_type"] = $v3["form_type"];
                               $item["price"] = json_decode($v3["pro_price_date"],TRUE);
                               $item["min_price"] = $this->get_min_price($item["price"]);
                               $item['price_money']=$v3['price_money'];
                               $item["remarks"] = $v3["pro_remarks"];
                               $item["view"] = json_decode($v3["pro_text"],TRUE);
                               $item["content"] = $v3["pro_details"];
                               $item["fly"] = $this->_get_fly(json_decode($v3["pro_info"],true));
                               $package_list[$k]['column'][$k1]['package_list'][$k2]['items'][]=$item;
                           }
                           if(!$item){
                               $package_list[$k]['column'][$k1]['package_list'][$k2]['items']=[];
                           }
							 
			}else{
                             unset($package_list[$k]['column'][$k1]['package_list'][$k2]);				 
			}
                    }
                     $package_list[$k]['column'][$k1]['package_list'] = $package_list[$k]['column'][$k1]['package_list']?array_merge($package_list[$k]['column'][$k1]['package_list']):[];
             }
        }//一级菜单
        return  $package_list;
            

   }
   private function  get_min_price($price){
        foreach($price['date'] as $k=>$v){
          $intcal[]=  intval($v);  
        }
       $min=min($intcal);
       $max=max($intcal);
       $time=date('Y-m-d',  time());
       return $min==$max?$min:$min.'起';
       
   }
    private function _get_fly($fly){
        
                foreach($fly as $k => $v){
                    $fly[$k]['fly_start_time'] = date('H:i', $v['fly_start_time']);
                    $fly[$k]['fly_end_time'] = date('H:i', $v['fly_end_time']);
                    $start_time = $v['fly_start_time'];
                    $end_time = $v['fly_end_time'] + $v['day_x']*(24*60*60) + $v['time_x']*(60*60);
                    $cost_time = $end_time - $start_time;
                    $fly_cost_time = '';
                    $d = intval($cost_time/(24*60*60));
                    $fly_cost_time .= $d > 0 ? $d.'天' : '';
                    $cost_time -= $d*(24*60*60);
                    $h = intval($cost_time/(60*60));
                    $fly_cost_time .= $fly_cost_time == '' ? ($h > 0 ? $h.'小时' : '') : $h.'小时';
                    $cost_time -= $h*(60*60);
                    $m = intval($cost_time/60);
                    $fly_cost_time .= $m.'分钟';
                    $fly[$k]['fly_cost_time'] = $fly_cost_time;
                }
            
        return $fly?$fly:[];
    }
    public function price($package_id){
        $type=$this->input->get('type',TRUE);
        $where=array('package_id'=>$package_id,'is_del'=>0);
        $cal_price=$this->Pro_info_model->get_pro_detail($where);
        $date_price = json_decode($cal_price['pro_price_date'], true);
        if(!$date_price || empty($date_price)){
            return $cal_price;
        }
        $date_choose = $date_price['date'];
        if(empty($date_choose)){
            return $cal_price;
        }

        $today = strtotime(date('Y-m-d', time()));
        $later_date_choose = array();
        foreach($date_choose as $key => $val){
            $month = date('Y-n', strtotime($key));
            if(strtotime($key) >= $today AND !empty($val)){
                $later_date_choose[$month][] = $val;
            }
        }
      
        $later_min_price = array();
        foreach($later_date_choose as $key => $val){
            $later_min_price[$key] = min($val);
        }
        $cal_price['min_price'] = min(array_values($later_min_price));
        $choose_date = array_keys($date_choose);
        foreach($choose_date as $key => $val){
            $choose_date[$key] = strtotime($val);
        }
        $min_data = min($choose_date);
        if($min_data + 24*60*60 < time()){
            $min_data = time();
        }

        $max_date = max($choose_date);
        if($max_date + 24*60*60 < time()){
            return $cal_price;
        }
        $max_year = date('Y', $max_date);
        $max_month = date('n', $max_date);
        $timeend_month = ($max_year - date('Y', $min_data))*12 + $max_month-date('n', $min_data);

        $time = strtotime(date('Y',$min_data).'-'.date('n',$min_data).'-1');
        $timeend = strtotime('+'.$timeend_month.' month', $time);
        $cal_info[] = array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend)){
            $cal_info[] =array(
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
         
        $cal = array();
        foreach($cal_info as $key => $val){
            $cal[$key]['year'] = $val['year'];
            $cal[$key]['month'] = $val['month'];
            $cal[$key]['month_cn'] = $val['month_cn'];
            $year_month = $val['year'].'-'.$val['month'];
            $cal[$key]['min_price'] = $later_min_price[$year_month];
            for($i=0; $i<$val['week_first']; $i++){
                $cal[$key]['item'][] = array('' => '');
            }
            for($i=1; $i<=$val['all_days']; $i++){
                $ymi = $val['year'].'-'.$val['month'].'-'.$i;
                if($type){
                     if((strtotime($ymi)>=$today) AND (array_key_exists($ymi, $date_choose))){
                    $cal[$key]['item'][] = array($ymi => $date_choose[$ymi]);
                }else{
               
                    $cal[$key]['item'][] = array($ymi => '');
                }  
                }else{
                      $cal[$key]['item'][] = array($ymi => $date_choose[$ymi]);
                }
             
            }
        }
       
        $cal_price['cal'] = $cal;
        return json_encode($cal_price['cal']); 
        
    }
    private function _type_name($data){
        $where=array('type'=>$data);
     $cate_name= $this->Pro_cate_model->get_pro_detail($where);
       return $cate_name['cate_name']; 
    }
      public function price_type($id){
        $type=$this->input->get('type',TRUE);
        $where=array('id'=>$id,'is_del'=>0);
        $cal_price=$this->Pro_info_model->get_pro_detail($where);
        $date_price = json_decode($cal_price['pro_price_date'], true);
        if(!$date_price || empty($date_price)){
            return $cal_price;
        }
        $date_choose = $date_price['date'];
        if(empty($date_choose)){
            return $cal_price;
        }

        $today = strtotime(date('Y-m-d', time()));
        $later_date_choose = array();
        foreach($date_choose as $key => $val){
            $month = date('Y-n', strtotime($key));
            if(strtotime($key) >= $today AND !empty($val)){
                $later_date_choose[$month][] = $val;
            }
        }
      
        $later_min_price = array();
        foreach($later_date_choose as $key => $val){
            $later_min_price[$key] = min($val);
        }
        $cal_price['min_price'] = min(array_values($later_min_price));
        $choose_date = array_keys($date_choose);
        foreach($choose_date as $key => $val){
            $choose_date[$key] = strtotime($val);
        }
        $min_data = min($choose_date);
        if($min_data + 24*60*60 < time()){
            $min_data = time();
        }

        $max_date = max($choose_date);
        if($max_date + 24*60*60 < time()){
            return $cal_price;
        }
        $max_year = date('Y', $max_date);
        $max_month = date('n', $max_date);
        $timeend_month = ($max_year - date('Y', $min_data))*12 + $max_month-date('n', $min_data);

        $time = strtotime(date('Y',$min_data).'-'.date('n',$min_data).'-1');
        $timeend = strtotime('+'.$timeend_month.' month', $time);
        $cal_info[] = array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend)){
            $cal_info[] =array(
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
         
        $cal = array();
        foreach($cal_info as $key => $val){
            $cal[$key]['year'] = $val['year'];
            $cal[$key]['month'] = $val['month'];
            $cal[$key]['month_cn'] = $val['month_cn'];
            $year_month = $val['year'].'-'.$val['month'];
            $cal[$key]['min_price'] = $later_min_price[$year_month];
            for($i=0; $i<$val['week_first']; $i++){
                $cal[$key]['item'][] = array('' => '');
            }
            for($i=1; $i<=$val['all_days']; $i++){
                $ymi = $val['year'].'-'.$val['month'].'-'.$i;
                if($type){
                     if((strtotime($ymi)>=$today) AND (array_key_exists($ymi, $date_choose))){
                    $cal[$key]['item'][] = array($ymi => $date_choose[$ymi]);
                }else{
               
                    $cal[$key]['item'][] = array($ymi => '');
                }  
                }else{
                      $cal[$key]['item'][] = array($ymi => $date_choose[$ymi]?$date_choose[$ymi]:'');
                }
             
            }
        }
       
        $cal_price['cal'] = $cal;
        $cal_price['cal']['length']=count($cal_price['cal']);    
        if($cal_price['cal']){
            $cal_price['cal']['code']=200;
    
          return $this->ajax_return($cal_price['cal']);
        }else{
            $cal_price['cal']['code']=404;
           return $this->ajax_return($cal_price['cal']);
        }
       
       
        
    }
    private  function kefu($u=''){
        $where=array('user_id'=>$u,'is_del'=>0);
        $kf_list=$this->Page_user_model->get_user_detail($where);
        return $kf_list;
        
    }
}
