<?php
/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends MY_Controller
{
    public function __construct() {
        parent::__construct();

		$this->load->model('us/Company_info_model');
        $this->load->model('us/Fly_info_model');
    }

    //航班搜索
    public function search_fly(){
        $fly_sn = trim($this->input->post('fly_sn', true));
        $where = array(
            'fly_sn' => $fly_sn,
            'is_del' => 0
        );
        $fly_data = $this->Fly_info_model->get_fly_info_detail($where);
        $result = array();
        if(!empty($fly_data)){
            $fly_data['fly_start_time'] = date('H:i', $fly_data['fly_start_time']);
            $fly_data['fly_end_time'] = date('H:i', $fly_data['fly_end_time']);
            $result['code'] = 0;
            $result['data'] = $fly_data;
            return $this->ajax_return($result);
        }
        $result['code'] = 1;
        $result['msg'] = "未找到航班信息";
        return $this->ajax_return($result);
    }

	//公司名称搜索
    public function search_company() {
        $keyword = trim($this->input->post('keyword', true));
        $where[] = "company_name like '".$keyword."%'";
        $where['limit'] = 10;
        $company_data = $this->Company_info_model->get_company_info_list($where);
        return $this->ajax_return($company_data);
    }
}