
<?php

/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require 'upload.php';
class Cs_list extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('gd/Gd_chat_model');
		$this->load->model('gd/Gd_file_model');
		$this->load->model('gd/Gd_order_model');
		$this->load->model('gd/Gd_topic_model');
		$this->load->model('tl/Page_user_model');
		$this->load->model('tl/Page_group_model');
		$this->load->model('us/Page_info_model');
		$this->load->model('us/Package_info_model');
		$this->load->model('us/Class_type_model');
        $this->load->model('us/Day_info_model');
		$this->load->model('tl/Page_role_model');
        $this->load->model('pl/Pl_grade_model');
        $this->load->model('pl/Pl_visitor_model');
        $this->load->model('pl/Pl_cs_model');
		$this->load->helper('url');
		$this->load->model('User_model');
		$this->load->model('Business_info_model');
		$this->load->model('Count_model');
		$this->load->model('Business_account_model');
		$this->load->library('MY_Session');
        //   $this->load->library('Redis');
	}

	public function gd_save() {
		// echo "<pre>";
		// print_r($_SESSION);die;
          $data = array();
        $data = $this->input->post();
        $profile=$data['profile'];
        if($data['profile']){
        	$data['gd_file']=1;
        }
        unset($data['profile']);	
        	   $data['user_id'] = $_SESSION['user_id'];
    	   if (empty($data['user_id'])) {
            $data['user_id'] = 1; //测试页面如果上传人不存在 則值为1
        }
        $data['business_id']=$_SESSION['business_id'];
        if(empty($data['business_id'])){
        	$data['business_id']=1;
        }
       
        
     //   echo "<pre>";
     //   print_r($data);die;
        $data['add_time'] = time();

        $res = $this->Gd_order_model->save_order_info($data);

        if ($res) {
        	if ($data['id']) {
        		$result['errcode'] = 200;
        		$result['msg'] = "编辑成功";
        		$result['data'] = $data['id'];
        		return $this->ajax_return($result);
        	} else {
        		
        		$file= array_filter(explode(',',$profile));
           //  echo "<pre>";
           //  print_r($file);
           //  echo "</pre>";
           //  echo count($file);

        		for($i=1;$i<=count($file);$i++){
        			$f['id']=$file[$i];
        			$f['aid']=$res;
          // echo "<pre>";
           // print_r($f);die;
        			$this->Gd_file_model->save_file_info($f);  
        		}
         //  echo "<pre>";
        //   print_r($f);die;

        		$result['errcode'] = 200;
        		$result['msg'] = "增加成功";
        		$result['data'] = $res;
        		return $this->ajax_return($result);
        	}
        } else {
        	$result['errcode'] = 400;
        	$result['msg'] = "失败";
        	return $this->ajax_return($result);
        }

    }
    public function gd_edit(){
                $result['errcode'] = 200;
        		$result['msg'] = "成功";
        		$result['data'] = 1;
        		return $this->ajax_return($result);
      
     

    }
    //工单列表
    public  function gd_list(){
            $y= '';
        
        if(is_numeric($y)==true){
         echo 111;die;
        }else{
            echo 222;die; 
        }
         echo 333;die;
    	$user_id=$_SESSION['user_id'];
    	$user_id=$user_id?$user_id:1;
    	$business_id=$_SESSION['business_id'];
    	$business_id=$business_id?$business_id:1;
    	$status=$this->input->get('status',true);
	    $access_list=$this->_role($_SESSION['role_id']);
    	if($business_id==1){
    	

    	    		if(strpos($access_list,'7001')){

    			$where=array('is_show'=>1,'order_by' => 'id desc');
    		}else if($_SESSION['role_id']==13){
    			$where=array('gd_designer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
    		}else if($_SESSION['role_id']==12){
    				$where=array('gd_engineer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
    		}else{
    			$where=array('is_show'=>1,'order_by' => 'id desc');
    		}
    	}else {
    		
           if(strpos($access_list,'7001')){
         
           		$where=array('business_id'=>$business_id,'is_show'=>1,'order_by' => 'id desc');

           }else {
           	$where=array('business_id'=>$business_id,'user_id'=>$user_id,'is_show'=>1,'order_by' => 'id desc');
           }

    	}
    	  	if($status>='0'){
    			$where['is_status']=$status;
    			}
    
    	$res=$this->Gd_order_model->get_order_list($where);
    //	echo $this->db->last_query();die;
    
    	$wheres['is_status']=0;
    	$wheres['id']='asc limit 1';
    	$status=$this->Gd_order_model->get_order_detail(array('is_status'=>0,'is_show'=>1,'order_by'=>'id asc'));
    

    	foreach ($res as $k=>$v){
             $where=array('gid'=>$v['id'],'is_show'=>1,'order_by'=>'id desc');
           $jl=$this->Gd_chat_model->get_chat_detail($where);
           $res[$k]['is_news']=0;
           	if($jl){
           		if($jl['is_reply']==0){
           		  	$res[$k]['is_news']=1;	
           		  }else{
           		  $res[$k]['is_news']=2;	
           		  }
         
           }
           
    		$u=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id'],'is_del'=>0));
            $group_name=$this->Page_group_model->get_group_detail(array('group_id'=>$u['group_id'],'is_del'=>0));

            $res[$k]['group_name']=$group_name?$group_name['group_name']:'默认部门';
    		$res[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$res[$k]['plan_time']=date('Y-m-d ',$v['plan_time'])?date('Y-m-d ',$v['plan_time']):'无';
    		$res[$k]['update_time']=date('Y-m-d ',$v['update_time'])?date('Y-m-d',$v['update_time']):'无';
    		$res[$k]['start_time']=date('Y-m-d ',$v['start_time'])?date('Y-m-d ',$v['start_time']):'无';
    		$res[$k]['queue']=$v['id']-$status['id'];
    		$bus=$this->Business_info_model->get_business_info_detail(array('business_id'=>$v['business_id']));
    		$res[$k]['business_name']=$bus['business_name'];
    		if(!empty($v['update_time'])){
    			$time=time();
    		//echo $time;die;
    			if($v['is_status']==3){
    			if(($time-$v['update_time'])>=172800){
    				
    				$data['id']=$v['id'];
    				$data['is_status']=4;
    				$this->Gd_order_model->save_order_info($data);
    			}
    			}
    		}
    		if($v['page_id']){
    			if($v['gd_complete']<100){
    				$page=$this->Page_info_model->get_page_info_detail(array('page_id'=>$v['page_id']));
    				$image_data=json_decode($page['image_data'],true);
    				if($image_data['top']){
    				$this->_save_complete($v['page_id'],'5');//完成度
    				}else if($image_data['spec']){
    				$this->_save_complete($v['page_id'],'5');//完成度
    				}else if($image_data['share']){
    				 $this->_save_complete($v['page_id'],'5');//完成度	
    				}else if($page['synthesis_image']){
    				 $this->_save_complete($v['page_id'],'5');//完成度		
    				}
    			}
                //计算日程完成度
                if($v['gd_day']>0 || $v['is_status']==2){
                $day=$v['gd_day'];//工单提交日程
                $wc=$this->complete_sum($day);
                $z_count=$this->Day_info_model->get_day_info_list(array('page_id'=>$v['page_id'],'is_del'=>0));
                for($i=0;$i<=count($z_count);$i++){
                $this->_save_complete($v['page_id'],$wc);//完成度     
                }
                }
              
    		}
    	}

    	if($res){
    		$result['errcode'] = 200;
    		$result['msg'] = "成功";
    		$result['data'] = $res;
    		return $this->ajax_return($result);  
    	}else{
    		$result['errcode'] = 400;
    		$result['msg'] = "无数据记录";
    		return $this->ajax_return($result);
    	}
    }
 
 //工单接单操作  id==工单id
    public function gd_orders() {
        $id=$this->input->post('id',true);
        $user_id = $_SESSION['business_account_id'];
        $user_id = $user_id ? $user_id : '1';
        /*         * 获取接单上传者名字
         *
         */
     
        $user = $this->Business_account_model->get_business_account_detail(array('business_account_id' => $user_id, 'is_us' => 1,'status'=>0));
        //开始接单
        /* 查询id 所属工单 并获取类容* */
        $gd = $this->Gd_order_model->get_order_detail(array('id' => $id, 'is_show' => 1));
        /*         * *取参数
         * 18.3.30 
         */
         
        $bus=$this->Business_info_model->get_business_info_detail(array('business_id'=>$gd['business_id']));
        $page['uploader'] = $user['business_account']; //上传者
        $page['business_id'] = $gd['business_id']; //商户
        $page['page_type'] = $gd['gd_template']=='fit'?2:1;  
                //默认跟团游
        $page['template_type'] = $gd['gd_template'];              //模版类型
        $page['page_title'] = $gd['gd_title']; 
        $page['class_name'] = $gd['gd_series']; 
        $page['is_releasc'] =1;              //页面标题
        $page['short_title'] = '无';           //短标题
        $page['share_desc'] = '无';           //分享标题
        $page['add_time'] = time();
        $page_info = $this->Page_info_model->save_page_info($page);
        if($page_info){
        	$package['page_id']=$page_info;
        	$package['package_name']='__DEFAULT__';
        	$package['city_name']='__DEFAULT__';
        	$package['add_time']=time();
        	$this->Package_info_model->save_package_info($package);
        }
        if ($page_info) {
        	$gd_info['id'] = $id;
        	$gd_info['page_id'] = $page_info;
            $gd_info['is_status'] = 2; //改写工单状态为以接单 默认为1以接单
            $res = $this->Gd_order_model->save_order_info($gd_info);
            $ct['page_id']=$page_info;
             $ct['share_url'] =    $bus['share_name'].$page_info;
             $this->Page_info_model->save_page_info($ct);
            if ($res) {

            	if($gd['gd_complete']<100){

            	$this->_save_complete($gd_info['page_id'],'5');//完成度
            	}
            	
            	$result['errcode'] = 200;
            	$result['msg'] = "成功";
            	$result['data'] = $gd_info['page_id'];
            	return $this->ajax_return($result);
            } else {
            	$result['errcode'] = 400;
            	$result['msg'] = "失败";
            	return $this->ajax_return($result);
            }
        }
 
    }
    //图片
    public  function gd_file(){
    	$data=$this->input->post() ;
    	$res=$this->Gd_file_model->save_file_info($data);
    	if($res){
    		$result['errcode'] = 200;
    		$result['msg'] = "成功";
    		$result['data'] =$res;
    		return $this->ajax_return($result);
    	}else{
    		$result['errcode'] = 400;
    		$result['msg'] = "失败";          
    		return $this->ajax_return($result);
    	}
    }
 // 图片列表
    private function _gd_file_list($mod='',$aid=''){
    	$where=array('mod'=>$mod,'aid'=>$aid,'is_show'=>1);
    	$res=$this->Gd_file_model->get_file_list($where);
    
    	if($res){
    		$result['errcode'] = 200;
    		$result['msg'] = "成功";
    		$result['data'] =$res;
    		return $this->ajax_return($result); 
    	}else{
    		$result['errcode'] = 400;
    		$result['msg'] = "失败";
    		return $this->ajax_return($result);
    	}

    	
    	
    }
// 上架到微商城
    public function wx_shop(){
        $page_id=$this->input->post('pid',true);
        $page_id=$page_id?$page_id:1959;

    }
// 图片上传
    public  function upload($fileName) {
    	$file_post = $_FILES[$fileName];
    	$file_ary = array();
    	$file_count = count($file_post['name']);
    	$file_keys = array_keys($file_post);
    	for ($i=0; $i<$file_count; $i++) {
    		foreach ($file_keys as $key) {
    			$file_ary[$i][$key] = $file_post[$key][$i];
    		}
    		$upload[] = _uploader($file_ary[$i]);
    	}
    	if(count($upload)>0){
    		foreach($upload as $k=>$v){
    			$where['mod'] = $fileName=='file'?1:2;
    			$where['dir'] = $v['path'];
    			$where['ext'] = $v['ext'];
    			$where['title'] = $v['name'];
    			$where['user_id'] = $_SESSION['user_id'];
    			$where['add_time'] = time();
    			$where['is_show'] = 1;	
    			
    			$res[]=$this->Gd_file_model->save_file_info($where);
    		}		
    		
    		$result['errcode'] = 200;
    		$result['msg'] = "上传成功";
    		$result['data'] = $res;
    		return $this->ajax_return($result);
    		
    	}else{
    		$result['errcode'] = 400;
    		$result['msg'] = "上传失败";
    		
    		return $this->ajax_return($result);
    	}
    }
    public function index_list() {
    	$business_id = $_SESSION['business_id'];
    	if ($business_id) {
    		$result['errcode'] = 200;
    		$result['msg'] = "成功";
    		$result['data'] = $business_id;
    		return $this->ajax_return($result);
    	} else {
    		$result['errcode'] = 403;
    		$result['msg'] = "无权限";
    		return $this->ajax_return($result);
    	}
    }
    //i消息回复
    public function news_send($gid=''){
    	/*消息发送 客户*/
    	/*取用户 账号user_id*/
    	if(!$gid){
    		$gid=$this->input->post('gid',true);
    	}
    $user_id=$_SESSION['user_id'];//用户id
    $business_account_id=$_SESSION['business_account_id'];
    //查询页面的gd_id是否存在
    $page=$this->Gd_order_model->get_order_detail(array('id' => $gid, 'is_show' => 1));
    if($page){
    	/**如页面存在 则发送信息*/
    	$data=$this->input->post();
    	$data['user_id']=$user_id?$user_id:$business_account_id;
    	if(!empty($business_account_id)){
    		$data['is_reply']=1;
    	}
    	$data['page_id']=$page_id;
    	$data['add_time']=time();
    	   $profile=$data['profile'];
        if($data['profile']){
        	$data['is_file']=1;
        }
        unset($data['profile']);	
    	$res=$this->Gd_chat_model->save_chat_info($data);
    	
    			$file= array_filter(explode(',',$profile));

        		for($i=1;$i<=count($file);$i++){
        			$f['id']=$file[$i];
        			$f['aid']=$res;
        			$f['mod']=2;  // 消息回复 附件图片为2
          // echo "<pre>";  
           // print_r($f);die;
        			$this->Gd_file_model->save_file_info($f);  
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
    }else{
    	$result['errcode'] = 400;
    	$result['msg'] = "页面不存在";
    	return $this->ajax_return($result);  
    }
}
public function gd_info($id=''){
	$id = $id?$id:$this->input->get('id',true);
	/**查询页面基本信息*/
	$page=$this->Gd_order_model->get_order_detail(array('id'=>$id,'is_show'=>1));

	if($page){
		//查询分类
		$type_name=$this->Class_type_model->get_class_detail(array('id'=>$page['gd_series'],'is_del'=>0));
	  $page['gd_series']=$type_name['class_name'];
		/**获取客户  上传人   H5设计师 客服  信息*/
  	  $kehu=$this->Page_user_model->get_user_detail(array('user_id'=>$page['user_id'],'is_del'=>0));//客户
  	  $business=$this->Business_info_model->get_business_info_detail(array('business_id'=>$kehu['business_id']));//商户

  	  if($kehu['group_id']==999 || $kehu['group_id']==0){
     $group['group_name']='默认部门';
  	  }else{
  	  	 $group=$this->Page_group_model->get_group_detail(array('group_id'=>$kehu['group_id'],'is_del'=>0));//部门

  	  }
  	 
  	  $kf=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$page['gd_kefu'],'is_us'=>1,'status'=>0));//客服
  	  $designer=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$page['gd_designer'],'is_us'=>1,'status'=>0));//设计
  	  $engineer=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$page['gd_engineer'],'is_us'=>1,'status'=>0));//H5
  	  $page['add_time']=$page['add_time']?date('Y-m-d H:i',$page['add_time']):'无';
  	  $page['update_time']=$page['update_time']?date('Y-m-d ',$page['update_time']):'无';
  	  $page['plan_time']=$page['plan_time']?date('Y-m-d ',$page['plan_time']):'无';
  	  $page['start_time']=$page['start_time']?date('Y-m-d ',$page['start_time']):'无';
  	  $page['gd_kefu']=$kf['business_account'];
  	  $page['gd_kehu']=$kehu['user_name'];
  	  $page['gd_group']=$business['business_name'] .' -> '. $group['group_name'];		//旅行社-部门
  	  $page['gd_designer']=$designer['business_account'];
  	  $page['gd_engineer']=$engineer['business_account'];
   
     $files=$this->Gd_file_model->get_file_list(array('mod'=>1,'aid'=>$page['id'],'is_show'=>1));
    foreach($files as $k=>$v){
    	$page['profile'][$k]['id']=$v['id']; 
     $page['profile'][$k]['dir']=$v['dir']; 
     $page['profile'][$k]['title']=$v['title']; 
     $page['profile'][$k]['ext']=$v['ext']; 
     $page['profile'][$k]['add_time']=date('Y-m-d H:i',$v['add_time']); 
  }   
     $image_data=$this->Page_info_model->get_page_info_detail(array('page_id'=>$page['page_id']));
 //   $page['page_info'][0]['url']=json_decode($image_data['image_data'],true);
  //   $page['page_info'][0]['title']='html5 页面预览';
     $image=json_decode($image_data['image_data'],true);
     $page['page_info'][0]['url']=$image['spec'];
     $page['page_info'][0]['title']=$image['spec']?'特色长图':'';
     $type=$image_data['page_type']=='1'?'package_tour':'package_travel';
     $page['page_info'][1]['url']=base_url('/home/'.$type.'/view/'.$page['page_id'].'/'.$image_data['share_url']);
     $page['page_info'][1]['title']='html5 页面预览';
     $page['page_info'][2]['url']=base_url('usadmin/page_poster/create_poster/'.$page['page_id'].'?type=0');
     $page['page_info'][2]['title']=$page['gd_logo']?'带logo头图.jpg':'';
     $page['page_info'][3]['url']=base_url('usadmin/page_poster/create_poster/'.$page['page_id'].'?type=4');
     $page['page_info'][3]['title']=$page['gd_logo']?'':'不带logo头图.jpg';
     $files3=$this->Gd_file_model->get_file_list(array('mod'=>3,'aid'=>$page['id'],'is_show'=>1));
    foreach($files3 as $k=>$v){
    	 $page['product'][$k]['id']=$v['id']; 
     $page['product'][$k]['dir']=$v['dir']; 
     $page['product'][$k]['title']=$v['title']; 
     $page['product'][$k]['ext']=$v['ext']; 
     $page['product'][$k]['add_time']=date('Y-m-d H:i',$v['add_time']); 
  }

// J基本信息||||v1
  	    /*获取对话信息  根据page_id 获取所有信息
  	    */
  	   
  	    $page['news']=$this->Gd_chat_model->get_chat_list(array('gid'=>$page['id'],'is_show'=>1,'order_by' => 'id desc'));
  	 
  	    if(!empty($page['news'])){
  	    	  foreach($page['news'] as $k=>$v){
  	    	  	if($v['is_reply']==0){
  	    	  		   $user=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id'],'is_del'=>0));
  	    	  		}else{
  	    	  			$bus=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v['user_id'],'status'=>0));
  	    	  		}
     
           $page['news'][$k]['user_name']=$user['user_name']?$user['user_name']:$bus['business_account'];
            $page['news'][$k]['user_id']=$user['user_id']?$user['user_id']:$bus['business_account_id'];
           $page['news'][$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $files=$this->Gd_file_model->get_file_list(array('mod'=>2,'aid'=>$v['id'],'is_show'=>1));
           foreach($files as $k1=>$v1){
      $page['news'][$k]['profile'][$k1]['dir']=$v1['dir'];
       $page['news'][$k]['profile'][$k1]['title']=$v1['title']; 
        $page['news'][$k]['profile'][$k1]['ext']=$v1['ext']; 
         $page['news'][$k]['profile'][$k1]['add_time']=date('Y-m-d H:i',$v1['add_time']); 

  }
         

  	    }

  	    }
  	  
  	    $result['errcode'] = 200;
  	    $result['msg'] = "成功";
  	    $result['data'] = $page;
  	    return $this->ajax_return($result);

  	    
  	}else{
  		$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  }
  // 分配操作
  public function distribution(){

  	$data=$this->input->post();
  	$data['start_time']=strtotime($data['start_time']);
  	$data['update_time']=time();
    $data['is_status']=1;
  	$res=$this->Gd_order_model->save_order_info($data);
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
  // 查询与分配人员信息
  public function business_account(){
        $data=array();
  	$where=array('role_id'=>12,'is_us'=>1,'status'=>0);//H5上传人员
  	$h5=$this->Business_account_model->get_business_account_list($where);
  	foreach($h5 as $k=>$v){
  $data['h5'][$k]['business_account_id']=$v['business_account_id'];
  $data['h5'][$k]['business_account']=$v['business_account'];
  	}

  	$sj=$this->Business_account_model->get_business_account_list(array('role_id'=>13,'is_us'=>1,'status'=>0));

  	 	foreach($sj as $k=>$v){
  $data['sj'][$k]['business_account_id']=$v['business_account_id'];
  $data['sj'][$k]['business_account']=$v['business_account'];
  	}
  	if($data){
  		    $result['errcode'] = 200;
  	    $result['msg'] = "成功";
  	    $result['data'] = $data;
  	    return $this->ajax_return($result);
  	}else{
  			$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  
  }
  //页面类型
  public function type_list(){
  $business_id=$_SESSION['business_id'];
  $res=$this->Class_type_model->get_class_list(array('business_id'=>$business_id,'is_del'=>0));
  if($res){
  	   $result['errcode'] = 200;
  	    $result['msg'] = "成功";
  	    $result['data'] = $res;
  	    return $this->ajax_return($result);
  	}else{
  		$result['errcode'] = 200;//没查到类型
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  }
  public function product_upload(){
  	     $data=$this->input->post();
    	   $profile=$data['profile'];
        if($data['profile']){
        	$data['is_file']=1;
        }
        unset($data['profile']);	
   
    	if($data){
    			$file= array_filter(explode(',',$profile));

        		for($i=1;$i<=count($file);$i++){
        			$f['id']=$file[$i];
        			$f['aid']=$data['gid'];
        			$f['mod']=3;  // 消息回复 附件图片为2
          // echo "<pre>";  
           // print_r($f);die;
        			$this->Gd_file_model->save_file_info($f);  
        		}
        		  $result['errcode'] = 200;
  	            $result['msg'] = "成功";
  	            $result['data'] = 1;
  	           return $this->ajax_return($result);

        	}else{
        			$result['errcode'] = 400;
  		         $result['msg'] = "失败";
        	}
    		
  }

  public function del_uoload(){
  	$data['id']=$this->input->post('id',true);
  	$data['is_show']=0;
  	$res=$this->Gd_file_model->save_file_info($data);
  	if($res){
  		  $result['errcode'] = 200;
  	            $result['msg'] = "成功";
  	            $result['data'] = $res;
  	           return $this->ajax_return($result);
  	       }else{
	          $result['errcode'] = 400;
  		         $result['msg'] = "失败";
  	       }
  }
    // 客户登录
  public function login() {
  //
  	$user_mobile = $this->input->post('u_name', true);
  	$manager_pwd = $this->input->post('u_pwd', true);
  	$user_pwd = md5($manager_pwd.'1314');
  	$where = array('user_mobile' => $user_mobile, 'user_pwd' => $user_pwd, 'is_del' => 0);
  	$res = $this->Page_user_model->get_user_detail($where);
 //	echo $this->db->last_query();die;
  	if ($res) {
  		unset($res['add_time']);
  		unset($res['manager_pwd']);
  		$this->_user_business($res);
  		$result['errcode'] = 200;
  		$result['msg'] = "登录成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
  	} else {
  		$result['errcode'] = 400;
  		$result['msg'] = "登录失败";
  		return $this->ajax_return($result);
  	}
  }
  //坐享其成登陆
  public function manage(){
  	$business_account = $this->input->post('u_name', true);
  	$password = $this->input->post('u_pwd', true);
  	$password = md5($password.'6550');
  	$where = array('business_account' => $business_account, 'password' => $password, 'status' => 0,'is_us'=>1);
  	$res = $this->Business_account_model->get_business_account_detail($where);
 //	echo $this->db->last_query();die;
  	if ($res) {
  		unset($res['add_time']);
  		unset($res['password']);
  		$this->_user_business($res);
  		$result['errcode'] = 200;
  		$result['msg'] = "登录成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
  	} else {
  		$result['errcode'] = 400;
  		$result['msg'] = "登录失败";
  		return $this->ajax_return($result);
  	}

  }
// 完成工单
 public function complete(){
 	$data['id']=$this->input->post('id',true);
	if(!$data['id']){
 		$result['errcode'] = 401;
  		$result['msg'] = "id不存在";
  		return $this->ajax_return($result);	
 	}
 	$data['is_status']=3;
 	$data['gd_complete']=100;
 	$res=$this->Gd_order_model->save_order_info($data);

 	if($res){
 		$result['errcode'] = 200;
  		$result['msg'] = "确认成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
 	}else{
 			$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
 	}


 }
//确认工单
  public function  confirm(){
 	$data['id']=$this->input->post('id',true);
 	if(!$data['id']){
 		$result['errcode'] = 401;
  		$result['msg'] = "id不存在";
  		return $this->ajax_return($result);	
 	}

 	$data['is_status']=4;
 	$res=$this->Gd_order_model->save_order_info($data);
 	if($res){
 		$result['errcode'] = 200;
  		$result['msg'] = "确认成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
 	}else{
 			$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
 	}


 }


  public function close(){
 	$data['id']=$this->input->post('id',true);
	if(!$data['id']){
 		$result['errcode'] = 401;
  		$result['msg'] = "id不存在";
  		return $this->ajax_return($result);	
 	}
 	$data['is_status']=5;
 	$res=$this->Gd_order_model->save_order_info($data);
 	if($res){
 		$result['errcode'] = 200;
  		$result['msg'] = "关闭成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
 	}else{
 			$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
 	}


 }
 
 public function page_list(){
 $business_id=$_SESSION['business_id'];	
$business_id=67380;
 if($business_id==1){
 $where=array('is_show'=>0,'order_by'=>'page_id desc');
 }else{
 	$where=array('business_id'=>$business_id,'is_show'=>0,'is_release'=>0,'order_by'=>'page_id desc');
 }
 $page_size=48;
 $page=$this->input->get('page',true);
 $page=$page?$page:1;
 $this->db->limit($page_size,($page-1)*$page_size);
 $res=$this->Page_info_model->get_page_info_list($where);
 $data=array();
 foreach($res as $k=>$v){
 $res[$k]['page_title']=$v['page_title'];
 $business=$this->Business_info_model->get_business_info_detail(array('business_id'=>$v['business_id']));
 $res[$k]['business_name']=$business['business_name'];
 $res[$k]['page_type']=$v['page_type']=='1'?'跟团游':'自由行';
 $res[$k]['image']=json_decode($v['image_data'],true);
 $u=$v['page_type']=='1'?'package_tour':'package_travel';
 $res[$k]['url']=base_url('home/'.$u.'/view/'.$v['page_id'].'/'.$v['share_url']);
 $res[$k]['uploader']=$v['uploader'];
 $class_name=$this->Class_type_model->get_class_detail(array('id'=>$v['class_name'],'business_id'=>$v['business_id'],'is_del'=>0));
 $res[$k]['class_name']=$class_name?$class_name['class_name']:'无部门';
 $res[$k]['h5_home']=json_decode($v['h5_home'],true);
  unset($res[$k]['inst_data']);
  unset($res[$k]['share_url']);
  unset($res[$k]['is_pay']);
  unset($res[$k]['image_data']);
   unset($res[$k]['kf_data']);
    unset($res[$k]['date']);
     unset($res[$k]['synthesis_image']);
      unset($res[$k]['h5_image']);
       unset($res[$k]['h5_vodie']);
        unset($res[$k]['data_video']);


 }
 if($res){

	$result['errcode'] = 200;
  		$result['msg'] = "数据读取成功";
  		$result['data'] = $res;
        $result['page'] = $data;
  		return $this->ajax_return($result);
 }else{
		$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
 }
}
    //分页数据
    private function _pagination($query, $per_page, $url_data) {
        $this->load->library('pagination');
      //  echo "<pre>";
    //   print_r($per_page);die;
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

 
// 注册
  public function register() {
  	$data['manager_name'] = $this->input->post('u_name', true);
  	$manager_pwd = $this->input->post('u_pwd', true);
  	$data['manager_pwd'] = md5('zxqc001' . $manager_pwd);
  	if (empty($data['manager_name'])) {
  		$result['errcode'] = 400;
  		$result['msg'] = "注册失败,用户名不能为空";
  		return $this->ajax_return($result);
  	}
  	$where = array('manager_name' => $data['manager_name']);
  	$names = $this->Hx_manager_model->get_manager_detail($where);
  	if ($names) {
  		$result['errcode'] = 400;
  		$result['msg'] = "注册失败,用户名已存在";
  		return $this->ajax_return($result);
  	}
  	if (empty($manager_pwd)) {
  		$result['errcode'] = 400;
  		$result['msg'] = "注册失败,密码不能为空";
  		return $this->ajax_return($result);
  	}
  	$data['business_id'] = 67388;
  	$data['add_time'] = time();
  	$res = $this->Hx_manager_model->save_manager_info($data);
  	if ($res) {
  		$where = array('manager_id' => $res, 'is_del' => 0);
  		$res = $this->Hx_manager_model->get_manager_detail($where);
  		unset($res['add_time']);
  		unset($res['manager_pwd']);
  		$this->_user_business($res);
  		$result['errcode'] = 200;
  		$result['msg'] = "注册成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
  	} else {
  		$result['errcode'] = 400;
  		$result['msg'] = "注册失败";
  		return $this->ajax_return($result);
  	}
  }

    // 用户信息存储
  private function _user_business($list) {
  	$user = $this->session->set_userdata($list);
  	return $user;
  }

    // 退出登录
  public function login_out() {
//       $this->session->unset_userdata($array_items);//销毁某一
  	$res = $this->session->sess_destroy();
  	$result['errcode'] = 200;
  	$result['data'] = date('Y-m-d H:i:s', time());
  	$result['msg'] = "退出成功";
  	return $this->ajax_return($result);
  }

    // 用户信息
  public function user_info() {
  	$data['user_name'] = $_SESSION['user_name'];
  	$data['business_id'] = $_SESSION['business_id'];
  	$business_account_id=$_SESSION['business_account_id'];
  	$group_id=$_SESSION['group_id'];
  	if(empty($data['username']) && empty($data['business_id'])){
  					$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  	$business = $this->Business_info_model->get_business_info_detail($where = array('business_id' => $data['business_id']));
  	$business_name = $this->Business_account_model->get_business_account_detail($where = array('business_account_id' => $business_account_id, 'status' => 0));
  	$data['user_name'] = $data['user_name'] ? $data['user_name']: $business_name['business_account'];
  		$data['user_id'] = $data['user_id'] ? $data['user_id']: $business_name['business_account_id'];
  	$data['business_name'] = $business['business_name'];
  	$data['role_id']=$_SESSION['role_id'];
  	  	  if($group_id==999 || $group_id==0){
     $data['group_name']='默认部门';
  	  }else{
  	  	 $group=$this->Page_group_model->get_group_detail(array('group_id'=>$group_id,'is_del'=>0));//部门
         $data['group_name']=$group['group_name'];
  	  }
  	 

  	if (!empty($data)) {
  		$result['errcode'] = 200;
  		$result['msg'] = "成功";
  		$result['data'] = $data;
  		return $this->ajax_return($result);
  	} else{
  			$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  }
   // 取 主题名称
  private function _topic($id=""){
  	if($id){
  		$where=array('id'=>$id,'is_show'=>1);
  		$list=$this->Gd_topic_model->get_topic_detail($where);
  		return $list;
  	}else{
  		$where=array('is_show'=>1);
  		$list=$this->Gd_topic_model->get_topic_list($where);
  		return $list;  
  	}
  	
  } 
  public function count(){
    for($i=0;$i<=5;$i++){
       $user_id=$_SESSION['user_id'];
        $user_id=$user_id?$user_id:1;
        $business_id=$_SESSION['business_id'];
        $business_id=67380;
        if(!$business_id){
            $result['errcode'] = 400;
            $result['msg'] = "您还未登入";
            return $this->ajax_return($result);
            }
       // $status=$this->input->get('status',true);
        $access_list=$this->_role($_SESSION['role_id']);
        if($business_id==1){
         if(strpos($access_list,'7001')){

                $where=array('is_show'=>1,'order_by' => 'id desc');
            }else if($_SESSION['role_id']==13){
                $where=array('gd_designer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
            }else if($_SESSION['role_id']==12){
                    $where=array('gd_engineer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
            }else{
                $where=array('is_show'=>1,'order_by' => 'id desc');
            }
        }else {
            
           if(strpos($access_list,'7001')){
         
                $where=array('business_id'=>$business_id,'is_show'=>1,'order_by' => 'id desc');

           }else {
            $where=array('business_id'=>$business_id,'user_id'=>$user_id,'is_show'=>1,'order_by' => 'id desc');
           }

        }
            if($status>='0'){
                $where['is_status']=$i;
                }


        $res=$this->Gd_order_model->get_order_list($where);
        $count[$i]=count($res);
    }
      
       $where=array('is_read'=>0,'is_show'=>1,'order_by'=>'id desc');
       $s=$this->Gd_chat_model->get_chat_list($where);
       foreach($s as $k=>$v){
        $kk[$k]=$v['gid'];

       }
     
     //  $uiq=assoc_unique($s,'gid');
       $count['6']=count(array_unique($kk));
   
        $result['errcode'] = '200';
        $result['msg'] = "成功";
        $result['data']=$count;
        return $this->ajax_return($result);
  }
    private function _count($type=''){
      
       $user_id=$_SESSION['user_id'];
        $user_id=$user_id?$user_id:1;
        $business_id=$_SESSION['business_id'];
        $business_id=67380;
        if(!$business_id){
            $result['errcode'] = 400;
            $result['msg'] = "您还未登入";
            return $this->ajax_return($result);
            }
       // $status=$this->input->get('status',true);
        $access_list=$this->_role($_SESSION['role_id']);
        if($business_id==1){
         if(strpos($access_list,'7001')){

                $where=array('is_show'=>1,'order_by' => 'id desc');
            }else if($_SESSION['role_id']==13){
                $where=array('gd_designer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
            }else if($_SESSION['role_id']==12){
                    $where=array('gd_engineer'=>$_SESSION['business_account_id'],'is_show'=>1,'order_by' => 'id desc');
            }else{
                $where=array('is_show'=>1,'order_by' => 'id desc');
            }
        }else {
            
           if(strpos($access_list,'7001')){
         
                $where=array('business_id'=>$business_id,'is_show'=>1,'order_by' => 'id desc');

           }else {
            $where=array('business_id'=>$business_id,'user_id'=>$user_id,'is_show'=>1,'order_by' => 'id desc');
           }

        }
            if($status>='0'){
                $where['is_status']=$type;
                }


        $res=$this->Gd_order_model->get_order_list($where);
            $wheres['is_status']=0;
        $wheres['id']='asc limit 1';
        $status=$this->Gd_order_model->get_order_detail(array('is_status'=>0,'is_show'=>1,'order_by'=>'id asc'));
    
       return count($res);

   }
  // 修改密码
  public function change_pwd(){

  	$user_mobile = $_SESSION['user_mobile'];
  	$manager_pwd = $this->input->post('u_pwd', true);
  	$data['user_pwd']=md5($this->input->post('x_pwd',true).'1314');
  	$user_pwd = md5($manager_pwd.'1314');
  	$where = array('user_mobile' => $user_mobile, 'user_pwd' => $user_pwd, 'is_del' => 0);
  	$res = $this->Page_user_model->get_user_detail($where);
  	if($res){
  		$data['user_id']=$res['user_id'];
  		$c=$this->Page_user_model->save_user_info($data);
  		if($c){
  				$result['errcode'] = 200;
  		$result['msg'] = "密码修改成功";
  		$result['data'] = $c;
  		return $this->ajax_return($result);
  		}else{
  				$result['errcode'] = 400;
  		$result['msg'] = "密码修改失败";
  		return $this->ajax_return($result);
  		}

  	}else{
  			$result['errcode'] = 400;
  		$result['msg'] = "用户名或密码不正确";
  		return $this->ajax_return($result);
  	}
  }

  public function group_list(){
     $business_id=$this->input->post('business_id',true);
     //查询部门信息
     $group=$this->Page_group_model->get_group_list(array('business_id'=>$business_id,'is_del'=>0));
     $business_name=$this->Business_info_model->get_business_info_detail(array('business_id'=>$business_id));

     foreach($group as $k=>$v){
        $user=$this->Page_user_model->get_user_detail(array('group_id'=>$v['group_id']));
        $group[$k]['user_mobile']=$user['user_mobile'];

     }
     if($group){
     			$result['errcode'] = 200;
  		$result['msg'] = "成功";
  		$result['data'] = $group;
  		$result['business_name']=$business_name['business_name'];
  		return $this->ajax_return($result);
  	}else{
  		$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		
  		return $this->ajax_return($result);
  	}

  }
// 每天 每月 提交工单查询
  public function find_time(){
    $is_type=$_SESSION['is_type'];
    $business_id=$_SESSION['business_id'];
    $business_account_id=$_SESSION['business_account_id'];
    $t=$this->input->post('t',true); // 传入时间戳类型是20180423
    if(!$t){
        $t=date('Ymd',time());
    }
    if($is_type){
        if($is_type==1){
        $sql = "SELECT * FROM `gd_order` WHERE business_id=$business_id AND date_format(FROM_UNIXTIME(add_time),'%Y%m%d')=$t";

    }else{
        if($_SESSION['role_id']==13){
         $sql = "SELECT * FROM `gd_order` WHERE gd_designer= $business_account_id AND date_format(FROM_UNIXTIME(add_time),'%Y%m%d')=$t";
            
         }else if($_SESSION['role_id']==12){
                $sql = "SELECT * FROM gd_order WHERE gd_engineer= $business_account_id AND date_format(FROM_UNIXTIME(add_time),'%Y%m%d')=$t";          
        }else{
                $sql = "SELECT * FROM gd_order WHERE  date_format(FROM_UNIXTIME(add_time),'%Y%m%d')=$t";    
        }
        
    }   
}else{
       $result['errcode'] = 400;
        $result['msg'] = "请先登录";
        return $this->ajax_return($result); 
}

//   $sql = "SELECT * FROM `gd_order` WHERE date_format(FROM_UNIXTIME(add_time),'%Y%m%d')=$t";
  $query = $this->db->query($sql);

   $row = $query->result();
if($row){
        $result['errcode'] = 200;
        $result['msg'] = "成功";
        $result['data'] = $row;
        return $this->ajax_return($result); 
}else{
       $result['errcode'] = 400;
        $result['msg'] = "失败";
        return $this->ajax_return($result); 
}

  }
  public function star_count_list(){
 // echo "<pre>";
 // print_r($res);die;  
 $class=array('导游','领队','用车','酒店','用餐','景点'); 


   $data=array();
  for($i=0;$i<count($class);$i++){
   for($j=1;$j<=5;$j++){
      $data[$i]['class']= $class[$i];  
    //   $data[$i]['class']= $class[$i];  
      $data[$i]['star'.$j]=count($this->Pl_cs_model->star_count('2055',$class[$i],$j));
      if($data[$i]['star'.$j]){
          $data[$i]['sum']+=$j*count($this->Pl_grade_model->get_grade_list(array('page_id'=>2055,'class'=>$class[$i],'star'=>$j)));

      }
 
    }
  
  }
   // echo $this->db->last_query();die;
   foreach($data as $k=>$v){
//    $data[$k][sum]=array_sum(1*$v['star1']+2*$v['star2']+3*$v['star3']+4*$v['star4']+5*$v['star5']);
 //  $data[$k]['average']= round($v['sum']/(array_sum($v)-$v['sum']),2);
    $data[$k]['average']= array_sum($v);
    

   }
   echo "<pre>";
   print_r($data);die;

    $result['errcode'] = 200;
          $result['msg'] = "成功";
          $result['data'] = $data;
          return $this->ajax_return($result);
  
  }

    public function ct_gr_list(){
     $class=array('导游','领队','用车','酒店','用餐','景点'); 
       $business_id=$_SESSION['business_id'];
       $business_id=$business_id?$business_id:1;
       if($business_id==1){
       	$data=$this->Page_info_model->get_page_info_list(array('is_evaluate'=>1,'is_show'=>0));
       }else{
       	$data=$this->Page_info_model->get_page_info_list(array('is_evaluate'=>1,'business_id'=>$business_id,'is_show'=>0));
       }
     
   //  echo "<pre>";
 //    print_r($data);die;
      /**
      $data 数组是查询数据库所有page_id 的list 
      **/

     foreach($data as $k=>$v){
        $qc[$k]=$v['page_id'];

     }

foreach($qc as  $k=>$v){  // 遍历查询page_id 
   
    
  for($i=0;$i<count($class);$i++){
   for($j=1;$j<=5;$j++){

      $tts[$k][$i]['class']= $class[$i];  
    //   $data[$i]['class']= $class[$i];  
      $t[$k][$i]['star'.$j]=count($this->Pl_cs_model->star_count($v,$class[$i],$j));
    if( $t[$k][$i]['star'.$j]){
     $t[$k][$i]['sum']+=$j*count($this->Pl_cs_model->star_count($v,$class[$i],$j));//算出各个星级的总和 
    }
     
  

    }
  
  }

}

 foreach($t as $k1=>$v1){
//    $data[$k][sum]=array_sum(1*$v['star1']+2*$v['star2']+3*$v['star3']+4*$v['star4']+5*$v['star5']);
    foreach($v1 as $k2=>$v2){
    	if($v2['sum']==0){
       $tts[$k1][$k2]['average']=0;
    	}else{
    //	 $tts[$k1][$k2]['average']= round($v2['sum']/(array_sum($v2)-$v2['sum']),2); 	
            $tts[$k1][$k2]['average']= round($v2['sum']/(array_sum($v2)-$v2['sum']),2);     
    	}
  //  取平均值 用 星级总和  除以 各个星级评价次数 得到平均值 取小数点后两位
    	   }
    	     }
 foreach($qc as  $k=>$v){
      $tts[$k]['page_id']=$v;
 }

 if($tts){

    $result['errcode'] = 200;
          $result['msg'] = "成功";
          $result['data'] = $tts;
          return $this->ajax_return($result);  
      }else{

    $result['errcode'] = 400;
          $result['msg'] = "失败";
        
          return $this->ajax_return($result);
      }

  }

  public function unread_news(){
   
$where=array('is_reply'=>0,'is_read'=>0,'is_show'=>1);
 $this->db->where('user_id !=', NULL);  
$where['order_by']='id desc';
$ns=$this->Gd_chat_model->get_chat_list($where);
foreach($ns as $k=>$v){
    $ns[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
     $name=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id']));
    $ns[$k]['user_name']=$name['user_name'];
  $page=$this->Gd_order_model->get_order_detail(array('id'=>$v['gid']));
  $ns[$k]['page_id']=$page['page_id'];
  $ns[$k]['gd_designer']=$page['gd_designer'];
  $ns[$k]['gd_engineer']=$page['gd_engineer'];
$ns[$k]['is_status']=$page['is_status'];


}
 if($ns){
     $result['errcode'] = 200;
          $result['msg'] = "成功";
          $result['data'] = $ns;
          return $this->ajax_return($result);  
 }else{
      $result['errcode'] = 400;
          $result['msg'] = "失败";
        
          return $this->ajax_return($result);
 }
  }
  // 设计师与H5上传 统计页面
  public function count_page_list(){
   $s=$this->input->get('s',true);
   $p=$this->input->get('p',true);
   $s=$s?strtotime($s):'1525104000';
    $p=$p?strtotime($p):'1526894010';

     $list=$this->Gd_order_model->get_page_list($s,$p);
   //  echo $this->db->last_query();die;
     foreach($list as $k=>$v){
     	$de[$k]=$v['gd_designer'];
     	if($v['gd_engineer']){
     	$en[$k]=$v['gd_engineer'];
     	}
     	

     }

  //echo "<pre>";
  //   echo count(array_unique($de));
   //  print_r(array_unique($en));die;
  foreach(array_values(array_unique($de)) as $k2=>$v2){
  	if($v2){
  		
  		$name=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v2));
  		$data[0][$k2]['d_name']=$name['business_account'];
        $data[0][$k2]['d_sum']=count($this->Gd_order_model->get_page_list($s,$p,$v2));
        // $data['d'][$k2]['z_sum']+=count($this->Gd_order_model->get_page_list('1525104000','1526871740',$v2));
        $templast=$this->Gd_order_model->get_page_list($s,$p,$v2);
        foreach($templast as $ks=>$vs){
        
	    $data[0][$k2]['OTA'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','OTA长图'));
		$data[0][$k2]['海报+H5'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','海报+H5'));
        }	
  		
  

  	}
  }
  foreach($data[0] as $kd=>$vd ){
  	$data[0]['z_sum']+=$vd['d_sum'];

  }
   foreach(array_values(array_unique($en)) as $k3=>$v3){
  	if($v3){
  		$name=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v3));
  		$data[1][$k3]['e_name']=$name['business_account'];
        $data[1][$k3]['e_sum']=count($this->Gd_order_model->get_page_list($s,$p,'',$v3));
  	}
  }
  foreach($data[1] as $ke=>$ve){
  	$data[1]['z_sum']+=$ve['e_sum'];

  }
 //echo "<pre>";
 //print_r($data);die;
 if($data){
     $result['errcode'] = 200;
          $result['msg'] = "数据调用成功";
          $result['data'] = $data;
          return $this->ajax_return($result);  
 }else{
      $result['errcode'] = 400;
          $result['msg'] = "数据调用失败";
        
          return $this->ajax_return($result);
 }
  }
 // 商户提交页面统计
 public function  count_business_list(){
 	$business=$_SESSION['business_id'];
 	$s=$this->input->get('s',true);
   $p=$this->input->get('p',true);
   $s=$s?strtotime($s):'1525104000';
    $p=$p?strtotime($p):'1526894010';
    $business=$business?$business:'1';
 	if($business==1){

   $list=$this->Gd_order_model->get_business_list($s,$p,'');
 foreach($list as $k=>$v){
 	$business_id[$k]=$v['business_id'];

 }
foreach(array_values(array_unique($business_id)) as $key=>$val){

 $user=$this->Gd_order_model->get_business_list($s,$p,$val);
 $bus_name=$this->Business_info_model->get_business_info_detail(array('business_id'=>$val));
	// echo $this->db->last_query();die;
 $data[$key]['name']=$bus_name['business_name'];
 $data[$key]['z_sum']=count($this->Gd_order_model->get_business_list($s,$p,$val));
// business_id 
 $user=$this->Page_user_model->get_user_list(array('business_id'=>$val));
 foreach($user as $ku=>$vu){
 	$data[$key]['user'][$ku]['user_name']=$vu['user_name'];
 	$data[$key]['user'][$ku]['user_id']=$vu['user_id'];
 	
 	$data[$key]['user'][$ku]['u_sum']=count($this->Gd_order_model->get_order_list(array('user_id'=>$vu['user_id'],'is_show'=>1)));	
 	if($data[$key]['user'][$ku]['u_sum']==0){
 		unset($data[$key]['user'][$ku]);
 	}
 }
 	}
}else{
	 $user=$this->Gd_order_model->get_business_list($s,$p,$business);
 $bus_name=$this->Business_info_model->get_business_info_detail(array('business_id'=>$business));
	// echo $this->db->last_query();die;
 $data['name']=$bus_name['business_name'];
 $data['z_sum']=count($this->Gd_order_model->get_business_list($s,$p,$business));
// business_id 
 $user=$this->Page_user_model->get_user_list(array('business_id'=>$business));
 foreach($user as $ku=>$vu){
 	$data[$key]['user'][$ku]['user_name']=$vu['user_name'];
 	$data[$key]['user'][$ku]['user_id']=$vu['user_id'];
 	
 	$t[$key]['user'][$ku]['u_sum']=count($this->Gd_order_model->get_order_list(array('user_id'=>$vu['user_id'],'is_show'=>1)));	
 	if($t[$key]['user'][$ku]['u_sum']==0){
 		unset($data[$key]['user'][$ku]);
 	}

 }
}



//echo "<pre>";
//print_r($data);die;
 if($data){
     $result['errcode'] = 200;
          $result['msg'] = "数据调用成功";
          $result['data'] = $data;
          return $this->ajax_return($result);  
 }else{
      $result['errcode'] = 400;
          $result['msg'] = "数据调用失败";
        
          return $this->ajax_return($result);
 }
 } 
   private  function _save_complete($page_id,$sum){
 
	$order=$this->Gd_order_model->get_order_detail(array('page_id'=>$page_id,'is_show'=>1));
	if($order['gd_complete']+$sum<=100){
			$data['id']=$order['id'];
	$data['gd_complete']=$order['gd_complete']+$sum;
	$data['update_time']=time();
	$res=$this->Gd_order_model->save_order_info($data);
	if($res){
		return $res;
	}else{
		return '失败';
	}
	}



}


private function _role($role_id){
$res=$this->Page_role_model->get_role_detail(array('role_id'=>$role_id,'is_del'=>0));
if($res){
	return $res['access_list'];
}
}
public function download($id="0"){
	$file=$this->Gd_file_model->get_file_detail(array('id'=>$id,'is_show'=>1));
	if($file){
		//   /opt/nginx/html/zxqc/api
		//$filename = dirname(__FILE__).'/../../..'.$file['dir'];
		$filename = '/opt/nginx/html/zxqc/api'.$file['dir'];
		$out_filename = $file['title'];
if(! file_exists($filename)){
	echo '不存在文件，' . $filename;
	exit;
} else {
	header('Accept-Ranges: bytes');
	header('Accept-Length: ' . filesize($filename));
	// It will be called
	header('Content-Transfer-Encoding: binary');
	header('Content-type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $out_filename);
	header('Content-Type: application/octet-stream; name=' . $out_filename);
	// The source is in filename
	if(is_file($filename) && is_readable($filename)){
		$file = fopen($filename, "r");
		echo fread($file, filesize($filename));
		fclose($file);
	}
	exit;
	}
  }
	echo '已删除文件。';
}
public function files(){
$file=fopen('https://zhidao.baidu.com/question/874262560361296452.html',"r");
header("Content-Type: application/octet-stream");
header("Accept-Ranges: bytes");
header("Accept-Length: ".filesize('https://zhidao.baidu.com/question/874262560361296452.html'));
header("Content-Disposition: attachment; filename=hahaha");
echo fread($file,filesize('https://zhidao.baidu.com/question/874262560361296452.html'));
fclose($file);

}
//完成度算法
private function complete_sum($day=''){
 //获取完成度多少
    $t=80;//day总数80%
   $return=round(80/$day); 
   return $return;
}
}
