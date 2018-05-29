
<?php

/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */

class Page_evaluate extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('tl/Page_user_model');
		$this->load->model('tl/Page_group_model');
		$this->load->model('us/Page_info_model');
        $this->load->model('us/Page_evaluate_model');
          $this->load->model('us/Page_complain_model');
        $this->load->model('us/Package_info_model');
        $this->load->model('us/Class_type_model');
        $this->load->model('tl/Page_role_model');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->model('Business_info_model');
        $this->load->model('Count_model');
        $this->load->model('Business_account_model');
        $this->load->library('MY_Session');
        //   $this->load->library('Redis');
    }

    public function evaluate_add(){

     $data=$this->input->post();

     if($data['tourist_name'] || $data['tourist_mobile']){
        $data['add_time']=time();
        $data['ip']=$_SERVER["REMOTE_ADDR"];
        $res=$this->Page_evaluate_model->save_evaluate_info($data);
        if($res){
          $result['errcode'] = 200;
          $result['msg'] = "评价成功";
          $result['data'] = $res;
          return $this->ajax_return($result);
      }else{
          $result['errcode'] = 400;
          $result['msg'] = "评论失败";
          return $this->ajax_return($result);
      }
  }

}
  public function evaluate_list(){
    $page_id=$this->input->post('page_id',true);
    $res=$this->Page_evaluate_model->get_evaluate_list(array('page_id'=>$page_id,'is_show'=>0));
    foreach($res as $k=>$v){
           $res[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
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

  public function complain_add(){
     $data=$this->input->post();
     if($data['tourist_name'] || $data['tourist_mobile']){
        $data['add_time']=time();
        $data['ip']=$_SERVER["REMOTE_ADDR"];
        $res=$this->Page_complain_model->save_complain_info($data);
        if($res){
          $result['errcode'] = 200;
          $result['msg'] = "投诉成功";
          $result['data'] = $res;
          return $this->ajax_return($result);
      }else{
          $result['errcode'] = 400;
          $result['msg'] = "投诉失败";
          return $this->ajax_return($result);
      }
  }

  }

}
