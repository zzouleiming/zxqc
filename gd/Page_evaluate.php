
<?php

/**
 * Created by PhpStorm.
 * User: zouleiming
 * Date: 2018/5/02
 * Time: 14:24
 */

class Page_evaluate extends MY_Controller {

  public function __construct() {
    parent::__construct();
       $this->load->model('tl/Page_user_model');
       $this->load->model('tl/Page_group_model');
       $this->load->model('us/Page_info_model');  
       $this->load->model('us/Package_info_model');
       $this->load->model('us/Class_type_model'); 
       $this->load->model('tl/Page_role_model');
       $this->load->model('pl/Pl_grade_model');
       $this->load->model('pl/Pl_visitor_model');
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->model('Business_info_model');
        $this->load->model('Count_model');
        $this->load->model('Business_account_model');
        $this->load->library('MY_Session');
        $this->load->helper('cookie');
        //   $this->load->library('Redis');
    }

//保存联系人信息
    public function contact(){ 
      $data=$this->input->post();
      if($data['name'] || $data['mobile']){
     
       // 先查询表中是否已经存在该用户
        $where=array('mobile'=>$data['mobile']);
        $res=$this->Pl_visitor_model->get_visitor_detail($where);

        if($res){
     //     $t=$this->Pl_visitor_model->save_visitor_info($data);
        $data['id']=$res['id'];
        $data['add_time']=time();
        $this->Pl_visitor_model->save_visitor_info($data);
          $result['errcode'] = 200;
          $result['msg'] = "联系人信息获取成功";
          $result['data'] = $res['id'];
          return $this->ajax_return($result);
        }else{
        	$data['add_time']=time();
           $t=$this->Pl_visitor_model->save_visitor_info($data);
           if($t){
          $result['errcode'] = 200;
          $result['msg'] = "联系人信息保存成功";
          $result['data'] = $t;
          return $this->ajax_return($result);
           }else{
         $result['errcode'] = 400;
          $result['msg'] = "联系人信息保存失败";
          return $this->ajax_return($result);
           }
        }

      }else{
         $result['errcode'] = 400;
          $result['msg'] = "缺少参数P";
          return $this->ajax_return($result);
      }
    }

    public function evaluate_add(){
     $data=$this->input->post();
     if(!$data['vid']){
       $result['errcode'] = 400;
      $result['msg'] = "缺少参数V";
      return $this->ajax_return($result);  
     }

   $vname=$this->Pl_visitor_model->get_visitor_detail(array('id'=>$data['vid']));
    // 获取页面busines_id
    $b=$this->Page_info_model->get_page_info_detail(array('page_id'=>$data['page_id'],'is_show'=>0));
 
     if($data){
     	$where=array('class'=>$data['class'],'title'=>$data['title'],'page_id'=>$data['page_id'],'vid'=>$data['vid'],'day '=>$data['day']);
     	$es=$this->Pl_grade_model->get_grade_detail($where);
     	if($es){
        $data['id']=$es['id'];
        $data['add_time']=time();
        $data['business_id']=$b['business_id'];
        $data['ip']=$_SERVER["REMOTE_ADDR"];
        $res=$this->Pl_grade_model->save_grade_info($data);
          if($res){

                if($data['star']==1){
            $s=$b['page_title'].'用户'.$vname['name'].'-'.'更新了一条'.$data['star'].'星差评！'.'id'.$data['page_id'];
           $this->_sendmail($s);
           }/**else{
               $s=$b['page_title'].'用户'.$vname['name'].'-'.'更新了一条'.$data['star'].'星好评！'.'id'.$data['page_id'];
           $this->_sendmail($s);
           }  **/
          $result['errcode'] = 200;
          $result['msg'] = "评价更新成功";
          $result['data'] = $res;
          return $this->ajax_return($result);
      }else{
          $result['errcode'] = 400;
          $result['msg'] = "评价更新失败";
          return $this->ajax_return($result);
      }
     	}else{
        $data['add_time']=time();
        $data['business_id']=$b['business_id'];
        $data['ip']=$_SERVER["REMOTE_ADDR"];
        $res=$this->Pl_grade_model->save_grade_info($data);
            if($res){
            if($data['star']==1){
            $s=$b['page_title'].'用户'.$vname['name'].'-'.'提交了一条'.$data['star'].'星差评！'.'id'.$data['page_id'];
            $this->_sendmail($s);
            }/**else{
                 $s=$b['page_title'].'用户'.$vname['name'].'-'.'提交了一条'.$data['star'].'星好评！'.'id'.$data['page_id'];
            $this->_sendmail($s);
            }  **/ 
          $result['errcode'] = 200;
          $result['msg'] = "评价成功";
          $result['data'] = $res;
          return $this->ajax_return($result);
      }else{
          $result['errcode'] = 400;
          $result['msg'] = "评价失败";
          return $this->ajax_return($result);
      }
     
    }  
    
  }else{
      $result['errcode'] = 400;
      $result['msg'] = "缺少参数P";
      return $this->ajax_return($result);
  }

}
  public function evaluate_list(){
    $page_id=$this->input->post('page_id',true);
    $day=$this->input->post('day',true);
      $vid=$this->input->post('vid',true);
    $res=$this->Pl_grade_model->get_grade_list(array('page_id'=>$page_id,'day'=>$day,'vid'=>$vid,'is_show'=>1 ));
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

         private function _sendmail($message) {
        $this->load->library('Email'); //加载CI的email类
        $this->load->config('email_config');
        $config = config_item('email_config');
        $this->email->initialize($config);
        $this->email->from('3341684452@qq.com', '坐享其成'); //发件人
        $this->email->subject('投诉建议');
        $this->email->to('806191111@qq.com');
        $this->email->message($message);
        $this->email->send();
    }


}

