
<?php

/**
 * Created by PhpStorm.
 * User: xuzhiqiang
 * Date: 2017/9/05
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require 'upload.php';
class Gd_list extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('gd/Gd_chat_model');
		$this->load->model('gd/Gd_file_model');
		$this->load->model('gd/Gd_order_model');
		$this->load->model('gd/Gd_topic_model');
		$this->load->model('tl/Page_user_model');
		$this->load->model('tl/Page_group_model');
		$this->load->model('us/Page_info_model');
        $this->load->model('pl/Pl_grade_model');
        $this->load->model('pl/Pl_visitor_model');
         $this->load->model('pl/Pl_cs_model');
        $this->load->model('us/Page_evaluate_model');
        $this->load->model('us/Page_complain_model');
		$this->load->model('us/Package_info_model');
		$this->load->model('us/Class_type_model');
         $this->load->model('us/Day_info_model');
		$this->load->model('tl/Page_role_model');
		$this->load->helper('url');
		$this->load->model('User_model');
		$this->load->model('Business_info_model');
		$this->load->model('Count_model');
		$this->load->model('Business_account_model');
		$this->load->library('MY_Session');

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
       if($data['gd_service']=='修改'){
        $data['is_modify']=1;

       }
        
     //   echo "<pre>";
     //   print_r($data);die;

        $data['add_time'] = time();
        $data['group_id']=$_SESSION['group_id'];

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
	
	private function mtime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
    //工单列表
    public  function gd_list(){
		$ctime[0] = $this->mtime();
    	$user_id=$_SESSION['user_id'];
    	$user_id=$user_id?$user_id:1;
    	$business_id=$_SESSION['business_id'];
		$is_type=$_SESSION['is_type'];
        if(!$business_id){
            $result['errcode'] = 400;
            $result['msg'] = "您还未登入";
            return $this->ajax_return($result);
            }
    	$status=$this->input->get('status',true);
	    $access_list=$this->_role($_SESSION['role_id']);
    	if($business_id==1){
$ctime[1] = $this->mtime() - $ctime[0];
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
     $ct=array('is_show'=>1,'order_by'=>'id asc');
     
    $t=$this->input->get('page',true);  
    if($t=='0'){
    $where['is_modify']=0;
    $ct['is_modify']=0;
    }elseif($t=='1'){
     $where['is_modify']=1;   
     $ct['is_modify']=1;   
    }
	$ctime[2] = $this->mtime() -$ctime[0];
	$res=$this->Gd_order_model->get_order_list($where);
	$ctime[3] = $this->mtime() -$ctime[0];
    $this->db->where('is_status <=', 1);  
    $status=$this->Gd_order_model->get_order_detail($ct);
   // echo "<pre>";
   // print_r($status);die;
$ctime[4] = $this->mtime() -$ctime[0];
    	foreach ($res as $k=>$v){
			
             $where=array('gid'=>$v['id'],'is_reply'=>($is_type == 2?0:1) ,'is_show'=>1,'order_by'=>'id desc');
           $jl=$this->Gd_chat_model->get_chat_detail($where);
           $res[$k]['is_news']=0;
           	if($jl){
                if($jl['is_read']==0){
                    //1. 回复 + 未读   ==红   \     2
                    // 2. 消息 + 未读   ==\    红    1
                    $res[$k]['is_news']= ($jl['is_reply']==1)?2:1; 
                }
           }
           $ctime[5][$k] = $this->mtime() -$ctime[0];
    		$u=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id'],'is_del'=>0));
            $group_name=$this->Page_group_model->get_group_detail(array('group_id'=>$u['group_id'],'is_del'=>0));
    		$bus=$this->Business_info_model->get_business_info_detail(array('business_id'=>$v['business_id']));
    		$res[$k]['business_name']=$bus['business_name'];
			$res[$k]['user_name']=$u['user_name'];
            $res[$k]['group_name']=$group_name?$group_name['group_name']:'默认部门';
			
    		$res[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		$res[$k]['plan_time']=date('Y-m-d ',$v['plan_time'])?date('Y-m-d ',$v['plan_time']):'';
    		$res[$k]['update_time']=date('Y-m-d ',$v['update_time'])?date('Y-m-d',$v['update_time']):'';
    		$res[$k]['start_time']=date('Y-m-d ',$v['start_time'])?date('Y-m-d ',$v['start_time']):'';
    		$res[$k]['queue']=$v['id']-$status['id'];
			/*
    		$bus=$this->Business_info_model->get_business_info_detail(array('business_id'=>$v['business_id']));
    		$res[$k]['business_name']=$bus['business_name'];
            //客户名字 职位
            $user=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id']));
            $res[$k]['user_name']=$user['user_name'];
            //设计师
            $sj=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v['gd_designer']));
            $res[$k]['sj_name']=$sj['business_account'];
            $h5=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v['gd_engineer']));
            $res[$k]['h5_name']=$h5['business_account'];
			*/
  /*  		if(!empty($v['update_time'])){
    			$time=time();
    		//echo $time;die;
    		if((date('w') == 2) || (date('w') == 3)|| (date('w') == 4)|| (date('w') == 5)){
    			if($v['is_status']==3){
    			if(($time-$v['update_time'])>=172800){
    				
    				$data['id']=$v['id'];
    				$data['is_status']=4;
    				$this->Gd_order_model->save_order_info($data);
    			}
    			}
    		}
    		}*/
    		$res[$k]['d_sum']=0;
    		$res[$k]['s_sum']=0;
    		if($v['page_id'] && $v['is_status']==2 && $v['gd_complete']<=100){
    		
    				$page=$this->Page_info_model->get_page_info_detail(array('page_id'=>$v['page_id']));
    				$image_data=json_decode($page['image_data'],true);
    				$sum=0;
    				if($image_data['top']){
    			     $sum +=4;
    				} if($image_data['spec']){
    				 $sum +=4;
    				}if($image_data['share']){
    		           $sum +=4;
    				} if($page['synthesis_image']){
    		           $sum +=4;
    				}
    				if($page['h5_home']){
                       $sum +=4;
    				}
    				$res[$k]['s_sum']=$sum;
    			  // $this->_save_complete($v['page_id'],$sum);//完成度   
                  //计算日程完成度
    			$d=5;
                if($v['gd_day']>0){
                $day=$v['gd_day'];//工单提交日程
                $wc=$this->complete_sum($day);
                $z_count=$this->Day_info_model->get_day_info_list(array('page_id'=>$v['page_id'],'is_del'=>0));
                $d=$d+count($z_count)*$wc;
                
                	$res[$k]['d_sum']=$d;
                }
                
                  $this->_save_complete($v['id'],$sum+$d);//完成度    
    			
    		}
         
         

    	}
   $ctime[6] = $this->mtime() -$ctime[0];

    	if($res){
    		$result['errcode'] = 200;
    		$result['msg'] = "成功";
    		$result['data'] = $res;
			$result['ctime'] = $ctime;
            $result['count'] = $count;
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

         if($gd['gd_service']=='OTA长图' && $gd['gd_service']=='修改'){
         	$data['id']=$gd['id'];
         	$data['page_id']=0;
            $data['is_status'] = 2; //改写工单状态为以接单 默认为1以接单
         	$ota=$this->Gd_order_model->save_order_info($data);
            if($ota){
            	$result['errcode'] = 200;
            	$result['msg'] = "操作成功";
            	$result['data'] = $ota;
            	return $this->ajax_return($result);
            }

         }else{
        $bus=$this->Business_info_model->get_business_info_detail(array('business_id'=>$gd['business_id']));
        $page['uploader'] = $user['business_account']; //上传者
        $page['business_id'] = $gd['business_id']; //商户
        $page['page_type'] = $gd['gd_template']=='fit'?2:1;  
                //默认跟团游
        $page['group_id']=$gd['group_id'];
        $page['template_type'] =$gd['gd_template']=='fit'?'normal':$gd['gd_template'];              //模版类型
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

            //	$this->_save_complete($gd_info['page_id'],'5');//完成度
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

// 图片上传
    public  function upload($fileName) {
    	$file_post = $_FILES[$fileName];
    	$file_ary = array();
    	$file_count = count($file_post['name']);
       $user_id=$_SESSION['user_id'];//用户id
    $business_account_id=$_SESSION['business_account_id'];
    $is_type=$_SESSION['is_type'];
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
    			$where['user_id'] = $is_type=='1'?$user_id:$business_account_id;
    			$where['add_time'] = time();
    			$where['is_show'] = 1;	
    			
    			$res[]=$this->Gd_file_model->save_file_info($where);
				$name[] =$v['name'];
    		}		
    		
    		$result['errcode'] = 200;
    		$result['msg'] = "上传成功";
    		$result['data']['id'] = $res;
    		$result['data']['name'] = $name;
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
    $is_type=$_SESSION['is_type'];
    //查询页面的gd_id是否存在
    $page=$this->Gd_order_model->get_order_detail(array('id' => $gid, 'is_show' => 1));
    if($page){
    	/**如页面存在 则发送信息*/
    	$data=$this->input->post();
    	$data['user_id']=$is_type=='1'?$user_id:$business_account_id;
    	if($is_type==2){
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
              //      $f['user_id']=$is_type=='1'?$user_id:$business_account_id; // 取file 表 user_id
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
  //设计师
	$sj=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$page['gd_designer']));
	$ns[$k]['sj_name']=$sj['business_account'];
	$h5=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$page['gd_engineer']));
	$ns[$k]['h5_name']=$h5['business_account'];
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
public function gd_info($id=''){ 
	$id = $id?$id:$this->input->get('id',true);
	/**查询页面基本信息*/
	$page=$this->Gd_order_model->get_order_detail(array('id'=>$id,'is_show'=>1));
	$is_type=$_SESSION['is_type'];
	if($is_type==1){
      if($page['business_id']!=$_SESSION['business_id']){
		echo '参数错误';die;
	 }

	}
	 
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
  	  $page['add_time']=$page['add_time']?date('Y-m-d H:i',$page['add_time']):'';
  	  $page['update_time']=$page['update_time']?date('Y-m-d ',$page['update_time']):'';
  	  $page['plan_time']=$page['plan_time']?date('Y-m-d ',$page['plan_time']):'';
  	  $page['start_time']=$page['start_time']?date('Y-m-d ',$page['start_time']):'';
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
     if($image_data){
     	  $image=json_decode($image_data['image_data'],true);
    $page['page_info'][0]['url']=$image['spec'];
     $page['page_info'][0]['title']=$image['spec']?'特色长图':'';
     $type=$image_data['page_type']=='1'?'package_tour':'package_travel';
     $page['page_info'][1]['url']=base_url('/home/'.$type.'/view/'.$page['page_id'].'/'.$image_data['share_url']);
     $page['page_info'][1]['title']='html5 页面预览';
     $page['page_info'][2]['url']=base_url('usadmin/page_poster/create_poster/'.$page['page_id'].'?type=4');
     $page['page_info'][2]['title']=$page['gd_logo']?'带logo头图.jpg':'';
     $page['page_info'][3]['url']=base_url('usadmin/page_poster/create_poster/'.$page['page_id'].'?type=0');
     $page['page_info'][3]['title']='不带logo头图.jpg';
     }
   
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
					   $user_name = $user['user_name'];
  	    	  		}else{
  	    	  			$bus=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v['user_id'],'status'=>0));
						$user_name = $bus['business_account'];
  	    	  		}
           $page['news'][$k]['user_name']= $user_name;
           $page['news'][$k]['user_id']= $v['user_id'];
           $page['news'][$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $files=$this->Gd_file_model->get_file_list(array('mod'=>2,'aid'=>$v['id'],'is_show'=>1));
           foreach($files as $k1=>$v1){
          $page['news'][$k]['profile'][$k1]['dir']=$v1['dir'];
          $page['news'][$k]['profile'][$k1]['title']=$v1['title']; 
          $page['news'][$k]['profile'][$k1]['ext']=$v1['ext']; 
           $page['news'][$k]['profile'][$k1]['id']=$v1['id']; 
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
                //    $f['user_id']=$is_type=='1'?$user_id:$business_account_id; // 取file 表 user_id
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

    public function read_news(){
    $data['id']=$this->input->post('id',true);
    $data['is_read']=1;
    $res=$this->Gd_chat_model->save_chat_info($data);
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
        $res['is_type']=1;//客户登陆
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
          $res['is_type']=2;//坐享其成登陆
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
    $data['is_confirm']=$this->input->post('status',true);
    if($data['is_confirm']==3){
     $data['is_status']=3;  
    }
 	
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
 // 修改工单状态
 public function set_status(){
   $data['id']=$this->input->post('id',true);
   $data['is_status']=$this->input->post('status',true);
   $data['update_time']=time();
   $res=$this->Gd_order_model->save_order_info($data);
	if($res){
	$result['errcode'] = 200;
  		$result['msg'] = "状态修改成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
	}else{
		$result['errcode'] =400;
  		$result['msg'] = "状态修改失败";
  		return $this->ajax_return($result);
	}
 }

 // 修改pageID
 public function set_page(){
   $data['id']=$this->input->post('id',true);
   $data['page_id']=$this->input->post('pid',true);
      $res=$this->Gd_order_model->save_order_info($data);
	if($res){
	$result['errcode'] = 200;
  		$result['msg'] = "page_id修改成功";
  		$result['data'] = $res;
  		return $this->ajax_return($result);
	}else{
		$result['errcode'] =400;
  		$result['msg'] = "修改失败";
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
 //$business_id=67380;
 if($business_id==1){
 $where=array('is_show'=>0,'is_release'=>0,'order_by'=>'page_id desc');
 }else{
    $where=array('business_id'=>$business_id,'is_show'=>0,'is_release'=>0,'order_by'=>'page_id desc');
 }
 $is_eval=$this->input->get('status',true);
 if($is_eval){
    $where['is_evaluate']=1;
 }
  $page_size=200;
 $page=$this->input->get('page',true);
 $page=$page?$page:1;
 $this->db->limit($page_size,($page-1)*$page_size);
 $res=$this->Page_info_model->get_page_info_list($where);
 //echo $this->db->last_query();die;
 $data=array();
 foreach($res as $k=>$v){
    $sz=$this->Package_info_model->get_package_detail(array('page_id'=>$v['page_id'],'is_del'=>0));
 //  echo $this->db->last_query();
//echo "<pre>";
//print_r($sz);
    $p=json_decode($sz['date_price'],true);
   $max = max($p['date']);
 $min = min($p['date']);
    if ($max == $min) {
        $res[$k]['price'] = $min;
            } else {
                $res[$k]['price'] = $min . '<i>起</i>';
            }
 $res[$k]['page_title']=$v['page_title'];
 $business=$this->Business_info_model->get_business_info_detail(array('business_id'=>$v['business_id']));
 $res[$k]['business_name']=$business['business_name'];
 $res[$k]['page_type']=$v['page_type']=='1'?'跟团游':'自由行';
 $res[$k]['image']=json_decode($v['image_data'],true);
 $u=$v['page_type']=='1'?'package_tour':'package_travel';
 $res[$k]['url']=base_url('home/'.$u.'/view/'.$v['page_id'].'/'.$v['share_url']);
 $res[$k]['uploader']=$v['uploader'];
 $class_name=$this->Class_type_model->get_class_detail(array('id'=>$v['class_name'],'business_id'=>$v['business_id'],'is_del'=>0));

  $group_name=$this->Page_group_model->get_group_detail(array('group_id'=>$v['group_id'],'is_del'=>0));
 $res[$k]['group_name']=$group_name?$group_name['group_name']:'无部门';
 $res[$k]['h5_home']=json_decode($v['h5_home'],true);
 $res[$k]['add_time']=date('Y-m-d ',$v['add_time']);
 $res[$k]['qrcode'] = "/H5info_temp_zyx/qrcode_infos?name=".base64_encode($res[$k]['url']);
$res[$k]['image_code']=base_url('usadmin/page_poster/create_poster/'.$v['page_id'].'?type=4');

$res[$k]['not_code']=base_url('usadmin/page_poster/create_poster/'.$v['page_id'].'?type=0');
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
        return $this->ajax_return($result);
 }else{
        $result['errcode'] = 400;
        $result['msg'] = "失败";
        return $this->ajax_return($result);
 }
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

  public function news_list(){
    $time=$this->input->post('t',true);
	 $is_type=$_SESSION['is_type'];
     $user_id= ($is_type==1)?$_SESSION['user_id']:$_SESSION['business_account_id'];//用户id
   $time=$time?strtotime($time):time();
   if($is_type==1){
   	$name=$this->Page_user_model->get_user_detail(array('user_id'=>$user_id,'is_del'=>0));
	//当前用户
   	$user_name=$name['user_name'];
   }else{
   	$name=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$user_id,'status'=>0));
   	$user_name=$name['business_account'];
   }
   $where=array( 'order_by'=>'id desc' ,'is_show'=>1);
   $this->db->where('add_time >', $time);
  // print_r($where);
   $res=$this->Gd_chat_model->get_chat_list($where);
//echo $this->db->last_query();die;
	$rlt =array();
   foreach($res as $k=>$v){
	  //消息收件人，
	$to_id=$this->Gd_order_model->get_order_detail(array('id'=>$v['gid']));
		//收件人不是当前用户，删除
	if($is_type ==1){
		if($v['is_reply']==0){unset($res[$k]);continue;}
		if($to_id['user_id']!=$user_id){unset($res[$k]);continue;}
		$sender_name=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v['user_id'],'status'=>0));
		$res[$k]['sender_name']=$sender_name['business_account'];
	}else{
		if($v['is_reply']==1){unset($res[$k]);continue;}
		if($_SESSION['role_id']==13 && $to_id['gd_designer']!=$user_id){unset($res[$k]);continue;}
		if($_SESSION['role_id']==12 && $to_id['gd_engineer']!=$user_id){unset($res[$k]);continue;}
		$sender_name=$this->Page_user_model->get_user_detail(array('user_id'=>$v['user_id'],'is_del'=>0));
	    $res[$k]['sender_name']=$sender_name['user_name'];
	}
	$res[$k]['page_id']=$to_id['page_id'];
   	$res[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);

   	$rlt[] = $res[$k];
   }
  if($res){
  		$result['errcode'] = 200;
  		$result['msg'] = "成功";
  		$result['data'] = $rlt;
  		return $this->ajax_return($result);
  }else{
  		$result['errcode'] = 200;
  		$result['msg'] = "无新消息";
  		$result['data'] = 0;
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
  	if(empty($data['user_name']) && empty($data['business_id'])){
  					$result['errcode'] = 400;
  		$result['msg'] = "失败";
  		return $this->ajax_return($result);
  	}
  	$business = $this->Business_info_model->get_business_info_detail($where = array('business_id' => $data['business_id']));
  	$business_name = $this->Business_account_model->get_business_account_detail($where = array('business_account_id' => $business_account_id, 'status' => 0));
  	$data['user_name'] = (!$data['user_name'] && $business_name['business_account'])?$business_name['business_account'] :$data['user_name'] ;
  		$data['user_id'] = (!$data['user_id'] && $business_name['business_account_id'])?$business_name['business_account_id']: $data['user_id'];
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

    // 设计师与H5上传 统计页面
  public function count_list(){
   $s=$this->input->get('status',true);
   $p=$this->input->get('page',true);
   $s=$s?strtotime($s):strtotime(date('Y-m'));
   $p=$p?strtotime($p):time();
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
        $data[0][$k2]['OTA'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','OTA长图'));
        $data[0][$k2]['H5'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','海报+H5'));
        $data[0][$k2]['MDY'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','修改'));
        $data[0][$k2]['d_sum']= $data[0][$k2]['OTA'] + $data[0][$k2]['H5'];
    }
  }
	$data[0] = array_values($data[0]);
   foreach(array_values(array_unique($en)) as $k3=>$v3){
    if($v3){
        $name=$this->Business_account_model->get_business_account_detail(array('business_account_id'=>$v3));
        $data[1][$k3]['d_name']=$name['business_account'];
        $data[1][$k3]['OTA']=0;
        $data[1][$k3]['H5']=count($this->Gd_order_model->get_page_list($s,$p,'',$v3,'海报+H5'));
        $data[0][$k3]['MDY'] =count($this->Gd_order_model->get_page_list($s,$p,$v2,'','修改'));
        $data[1][$k3]['d_sum']= $data[1][$k3]['OTA'] + $data[1][$k3]['H5'] + $data[1][$k3]['MDY'];
    }
  }
	$data[1] = array_values($data[1]);
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
   $s=$s?strtotime($s):strtotime(date('Y-m'));
    $p=$p?strtotime($p):time();
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
     $business_id=$business_id?$business_id:$_SESSION['business_id'];
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
  public function count(){
    for($i=0;$i<=5;$i++){
       $user_id=$_SESSION['user_id'];
        $user_id=$user_id?$user_id:1;
        $business_id=$_SESSION['business_id'];
      //  $business_id=67380;
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
           
                $where['is_status']=$i;
          


        $res=$this->Gd_order_model->get_order_list($where);
        $count[$i]=count($res);
    }
        $where=array('is_read'=>0,'is_show'=>1,'order_by'=>'id desc');
       $s=$this->Gd_chat_model->get_chat_list($where);
       foreach($s as $k=>$v){
        $kk[$k]=$v['gid'];

       }
     
     //  $uiq=assoc_unique($s,'gid');
       $count[6]=count(array_unique($kk));
        $result['errcode'] = '200';
        $result['msg'] = "成功";
        $result['data']=$count;
        return $this->ajax_return($result);
  }

public function group_change(){
    $page_id=$this->input->post('pid',true);
  //  print_r($page_id);die;
    $group_id=$this->input->post('gid',true);
    $pid=count($page_id);
    for($i=0;$i<$pid;$i++){
        $data['page_id']=$page_id[$i];
        $data['group_id']=$group_id;
       $res=$this->Page_info_model->save_page_info($data);
    //   echo $this->db->last_query();die;
    }
    if($res){
       $result['errcode'] = '200';
        $result['msg'] = "分配成功";
        $result['data']=$res;
        return $this->ajax_return($result);  
    }else{
         $result['errcode'] = '400';
        $result['msg'] = "失败";
        return $this->ajax_return($result);
    }
}
// 评价查询
  public function evaluate_list(){
    $page_id=$this->input->post('page_id',true);
    if($page_id){
         $res=$this->Page_evaluate_model->get_evaluate_list(array('page_id'=>$page_id,'is_show'=>0,'order_by'=>'id desc'));
     }else{
         $res=$this->Page_evaluate_model->get_evaluate_list(array('is_show'=>0,'order_by'=>'id desc'));
     }
   
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
    public function eval_list(){
    $is_type=$_SESSION['is_type'];
    $access_list=$this->_role($_SESSION['role_id']);
    $page_id=$this->input->get('id',true);
    $class=$this->input->get('page',true);
    $star=$this->input->get('status',true);
    if($is_type==1){
        if(strpos($access_list,'7001')){
          $where=array('is_show'=>1,'business_id'=>$_SESSION['business_id']);
        }else{
            die;
        }
    }else{
        $where=array('is_show'=>1); 
    }
    if($page_id){
        $where['page_id']=$page_id;
    }
    if($star){
        $where['star']=$star;
    }
    if($class){
        $where['class']=$class;
    }
    $where['order_by']='star asc, id desc';
 /**   $page_id=$this->input->post('page_id',true);
    if($page_id){
    $where=array('page_id'=>$page_id,'is_show'=>1);
    } else{
    $where=array('is_show'=>1);
    }**/
    $res=$this->Pl_grade_model->get_grade_list($where);
    foreach($res as $k=>$v){
         $vs=$this->Pl_visitor_model->get_visitor_detail(array('id'=>$v['vid']));
         $res[$k]['vname']=$vs['name'];
          $res[$k]['vmobile']=$vs['mobile'];
        $res[$k]['vgroup']=$vs['group_num'];
         $res[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
    }
    if($res){
         $result['errcode'] = 200;
          $result['msg'] = "评论获取成功";
          $result['data'] = $res;
          return $this->ajax_return($result);  
    }else{
         $result['errcode'] = 400;
          $result['msg'] = "评论获取失败";
          return $this->ajax_return($result);
    }

   }
    public function complain_list(){
    $page_id=$this->input->post('page_id',true);
    if($page_id){
         $res=$this->Page_complain_model->get_complain_list(array('page_id'=>$page_id,'is_show'=>0,'order_by'=>'id desc'));
     }else{
         $res=$this->Page_complain_model->get_complain_list(array('is_show'=>0,'order_by'=>'id desc'));
     }
   
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
  public function star_count(){
 // echo "<pre>";
 // print_r($res);die;  
    $page_id=$this->input->get('id',true);
 $class=array('导游','领队','用车','酒店','用餐','景点'); 


   $data=array();
  for($i=0;$i<count($class);$i++){
   for($j=1;$j<=5;$j++){
      $data[$i]['class']= $class[$i];  
    //   $data[$i]['class']= $class[$i];  
      $data[$i]['star'.$j]=count($this->Pl_grade_model->star_count($page_id,$class[$i],$j));
    
    }
  
  }
    $result['errcode'] = 200;
          $result['msg'] = "成功";
          $result['data'] = $data;
          return $this->ajax_return($result);
  
  }

  public function gr_list(){
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
    //   $tts[$k1][$k2]['average']= round($v2['sum']/(array_sum($v2)-$v2['sum']),2);    
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
//下载文件  
public function download($id="0"){
	$file=$this->Gd_file_model->get_file_detail(array('id'=>$id,'is_show'=>1));
	if($file){
		$filename = '/opt/nginx/html/zxqc/api'.$file['dir'];
		$out_filename = str_replace(array(',','&',"'",),'-',$file['title']);
if(! file_exists($filename)){
	echo '不存在文件，' . $filename;

} else {
	if(is_file($filename) && is_readable($filename)){
	$file = fopen($filename, "r");
	header('Accept-Ranges: bytes');
	header('Accept-Length: ' . filesize($filename));
	header('Content-Transfer-Encoding: binary');
	header('Content-type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $out_filename);
	header('Content-Type: application/octet-stream; name=' . $out_filename);
	ob_clean();   
    flush(); 
	echo fread($file, filesize($filename));
	fclose($file);
	}
	exit;
	}
  }
  echo '已删除文件。';
}
   private  function _save_complete($id,$sum){
 
	//$order=$this->Gd_order_model->get_order_detail(array('page_id'=>$page_id,'is_show'=>1));
	if($order['gd_complete']<=100 ){
	//$data['gd_complete']=0;	
	$data['id']=$id;
	$data['gd_complete']=$sum>100?100:$sum;
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
private function complete_sum($day=''){
 //获取完成度多少
    $t=80;//day总数80%
   $return=round(80/$day); 
   return $return;
}
}
