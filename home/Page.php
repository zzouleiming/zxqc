<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('US_PACKAGE_TOUR_CACHE_KEY', 'us_package_tour_');
define('US_PACKAGE_TOUR_CACHE_TIME', 24*60*60);
define('DEFAULT_CITY_NAME', '__DEFAULT__');
define('DEFAULT_PACKAGE_NAME', '__DEFAULT__');
define('HTTP_OK', 200);
define('HTTP_ERROR', 400);
define('DEFAULT_RS_NAMESPACE', 'normal_normal');
class Page extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('us/Page_info_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Day_info_model');
        $this->load->model('us/Fly_info_model');
        $this->load->model('H5_Day_info_model');
        $this->load->model('H5_Fly_info_model');
        $this->load->model('tl/Page_user_model');
        $this->load->model('Wx_account_model');

        $this->load->helper('url');
        $this->load->library('common');
    }

    public function page_list($page_id='')
    {
    	print_r(-----------------);die;
      if($page_id==''){
         $page_id=$this->input->post('page_id',true);
      }
      
       $list=$this->Page_info_model->get_page_info_detail(array('page_id'=>$page_id,'is_show'=>0));
       $date=$this->Package_info_model->get_package_detail(array('page_id'=>$page_id,'is_del'=>0));
     
           $data['short_title']=$list['short_title'];
           $data['image']=  json_decode($list['image_data'],true);
           $data['inst_data']=$list['inst_data'];
           $data['type']=$list['page_type']==1?'跟团游':'自由行';
           $data['date']=json_decode($date['date_price'],true);
           $data['page_title']=$list['page_title'];
           $data['page_id']=$list['page_id'];
           $data['business_id']=$list['business_id'];
            $data['ms']=$list['page_type']==1?'package_tour':'package_travel';
        
           $data['url']='http://api.etjourney.com/home/'. $data['ms'].'/view/'.$list['page_id'].'/'.$list['share_url'];
        

        return    $this->ajax_return($data);
      
  
 
}
}