<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lxgj extends CI_Controller {
	
	public function __construct()
	  {
	    parent::__construct();
		ini_set('php_mbstring','1');
        $this->table='country';
		$this->table='phone';
        $this->load->model('Lxgj_model');
		$this->load->model('User_model');

        $this->load->library('common');
        $this->load->library('session');
        $this->load->helper('url');
       // $this->load->driver('cache');
	   $this->lang->load('log', 'english');
        $this->lang->load('common', 'english');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->web_url="http://new.cnzz.com/v1/login.php?siteid=1258510548";
        $this->down_count='http://mobile.umeng.com/apps ';
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');

          $this->load->library('image_lib');
        
    }
	
	
	//显示条目
	
	/* public function index(){
		$data['phones']=$this->Lxgj_model->get_news();
		$this->load->view('lxgj/phonelist_view', $data);
	}
	public function view($slug=null){
		$data['phones_item']=$this->Lxgj_model->get_news($slug);
		if(empty($data['phones_item'])){show_404();}
		$this->load->view('lxgj/view',$data);
	} */
	
	/*  public function sayhello($name,$name2){
 8         echo $name,",Hello CI to ",$name2;
 9     }
	 */
	//旅行工具主页


	public function lxgj(){
		$this->load->view('lxgj/lxgj');
	}
	//旅行工具主页
	public function security(){
		$data['share']['share_url']=base_url('lxgj/security');
    $data['share']['title']="出境旅游安全须知";
    $data['share']['image']='http://api.etjourney.com/public/lxgj/images/fh.jpg';
    $data['share']['desc']="出境旅游安全须知";

    $data['json_share']=json_encode($data['share']);
	

		if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $data['share_out']="olook://shareinfo<".$data['json_share'];
    }
    else
    {
      $data['share_out']="olook://toshare.toapp>toshare&shareinfo<".$data['json_share'];
    }
		$data['share_out']="olook://shareinfo<".$data['json_share'];
		$this->load->view('lxgj/security',$data);
	}
	//紧急电话列表
	public function phonelist(){
		
		 $this->load->model('Lxgj_model');
        //var_dump($this->Lxgj_model->phone_list("亚洲"));
		$row['yazhou']=$this->Lxgj_model->phone_list("亚洲");
		$row['dayang']=$this->Lxgj_model->phone_list("大洋洲");
		$row['ouzhou']=$this->Lxgj_model->phone_list("欧洲");
		$row['beimei']=$this->Lxgj_model->phone_list("北美洲");
		$row['nanmei']=$this->Lxgj_model->phone_list("南美洲");
		$row['feizhou']=$this->Lxgj_model->phone_list("非洲");
		$row['nanji']=$this->Lxgj_model->phone_list("南极洲");
		//print_r($row['yazhou']);
		//print_r($row['yazhou']['0']);
		//$row['yazhou']=$this->phone_list('zhou=');
		//print_r($row);
		$this->load->view('lxgj/phonelist_view',$row);
		//$this->load->view('lxgj/pplist_view',$row);
		
	}
	public function pphonelist(){
		
		 $this->load->model('Lxgj_model');
        //var_dump($this->Lxgj_model->phone_list("亚洲"));
		$row['yazhou']=$this->Lxgj_model->phone_list("亚洲");
		$row['dayang']=$this->Lxgj_model->phone_list("大洋洲");
		$row['ouzhou']=$this->Lxgj_model->phone_list("欧洲");
		$row['beimei']=$this->Lxgj_model->phone_list("北美洲");
		$row['nanmei']=$this->Lxgj_model->phone_list("南美洲");
		$row['feizhou']=$this->Lxgj_model->phone_list("非洲");
		$row['nanji']=$this->Lxgj_model->phone_list("南极洲");
		//print_r($row['yazhou']);
		//print_r($row['yazhou']['0']);
		//$row['yazhou']=$this->phone_list('zhou=');
		//print_r($row);
		$this->load->view('lxgj/pphonelist_view',$row);
		//$this->load->view('lxgj/pplist_view',$row);
		
	}
	public function pplist(){
		
		 $this->load->model('Lxgj_model');
        //var_dump($this->Lxgj_model->phone_list("亚洲"));
		$row['yazhou']=$this->Lxgj_model->phone_list("亚洲");
		$row['dayang']=$this->Lxgj_model->phone_list("大洋洲");
		$row['ouzhou']=$this->Lxgj_model->phone_list("欧洲");
		$row['beimei']=$this->Lxgj_model->phone_list("北美洲");
		$row['nanmei']=$this->Lxgj_model->phone_list("南美洲");
		$row['feizhou']=$this->Lxgj_model->phone_list("非洲");
		$row['nanji']=$this->Lxgj_model->phone_list("南极洲");
		//print_r($row['yazhou']);
		//print_r($row['yazhou']['0']);
		//$row['yazhou']=$this->phone_list('zhou=');
		//print_r($row);
		$this->load->view('lxgj/pplist_view',$row);
		//$this->load->view('lxgj/pplist_view',$row);
		
	}
	//紧急电话单页
	public function phone($id){
		//$name=$this->input->get();
		$this->load->model('Lxgj_model');
		$data['detail']=$this->Lxgj_model->phone($id);
		//print_r($data);


$data['share']['share_url']=base_url('lxgj/phone/'.$id);
    $data['share']['title']=$data['detail']['name'];
    $data['share']['image']='http://api.etjourney.com/public/lxgj/images/fh.jpg';
    $data['share']['desc']=$data['detail']['name']."的紧急电话";

    $data['json_share']=json_encode($data['share']);
		if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $data['share_out']="olook://shareinfo<".$data['json_share'];
    }
    else
    {
      $data['share_out']="olook://toshare.toapp>toshare&shareinfo<".$data['json_share'];
    }

        var_dump($data['detail']['name']);

		$data['share_out']="olook://shareinfo<".$data['json_share'];

		$this->load->view('lxgj/embassy/phone_view',$data);
	}
	
	//大使馆列表
	public function embassylist(){
		$this->load->model('Lxgj_model');
		$elist['lista']=$this->Lxgj_model->embassy_list("1");
		$elist['listb']=$this->Lxgj_model->embassy_list("2");
		$elist['listc']=$this->Lxgj_model->embassy_list("3");
		$elist['listd']=$this->Lxgj_model->embassy_list("4");
		$elist['liste']=$this->Lxgj_model->embassy_list("5");
		$elist['listf']=$this->Lxgj_model->embassy_list("6");
		$elist['listg']=$this->Lxgj_model->embassy_list("7");
		
		//var_dump($elist);
		$this->load->view('lxgj/embassylist_view',$elist);
	}
	//大使馆单页
	public function embassy($id){
		//$id=htmlspecialchars(REQUEST[$id]);
		$this->load->model('Lxgj_model');
		$data['content']=$this->Lxgj_model->embassy($id);
		//echo'hvciqwpehf';

		$data['share']['share_url']=base_url('lxgj/embassy/'.$id);
    $data['share']['title']=$data['content']->name;
    $data['share']['image']='http://api.etjourney.com/public/lxgj/images/fh.jpg';
    $data['share']['desc']=$data['content']['name']."的大使馆信息";
//var_dump($data['share']);
    $data['json_share']=json_encode($data['share']);
		if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
    {
      $data['share_out']="olook://shareinfo<".$data['json_share'];
    }
    else
    {
      $data['share_out']="olook://toshare.toapp>toshare&shareinfo<".$data['json_share'];
    }
    
    	$data['share_out']="olook://shareinfo<".$data['json_share'];
		$this->load->view('lxgj/embassy/embassy_view',$data);
	}
	//搜索列表
	public function sousou(){
		$this->load->model('Lxgj_model');
		$name=$this->input->post('sel');
		
			if($name==null||$name==""){
			$sousou['detail']=$this->Lxgj_model->sou_list();

		}else{ 
			$sousou['detail']=$this->Lxgj_model->sousou($name);

		}
		
		
		//echo $name.'<hr/>';
		
		//var_dump('ghivbh');
		//
		//print_r($sousou['detail']);
		$this->load->view('lxgj/embassy/ss_view',$sousou);
	}
	
	public function sousouem(){
		$this->load->model('Lxgj_model');
		$name=$this->input->post('sel');
		if($name==null||$name==""){
			$sousou['detail']=$this->Lxgj_model->souem_list();

		}else{ 
			$sousou['detail']=$this->Lxgj_model->sousouem($name);

		}
		/* $this->load->library('Translate','','myfanyi');
		$sousou['ename']=$this->myfanyi->exec($sousou['detail'],'auto','en'); */
		
		$this->load->view('lxgj/embassy/ssem_view',$sousou);
	}
	//翻译
 public function fanyi(){
	$this->load->model('Lxgj_model');
	$this->load->library('Translate','','myfanyi');
	//print_r($aa);
	$asel='auto';
	$bsel=$this->input->post('bsel');
	$atxt=$this->input->post('atxt');
	//echo $atxt.$asel.$bsel;
	$fanyi['text']=$this->myfanyi->exec($atxt,$asel,$bsel);
	$fanyi['atxt']=$atxt;
	//echo $fanyi.'<hr/>';
	$this->load->view('lxgj/fanyi_view',$fanyi);
	} 
	
public function quanlist()
{
	$data['info']=$this->User_model->get_select_all($select='*',$where="is_show='1'",$order_title='addtime',$order='ASC',$table='v_dis_code');
	if($data['info']===FALSE)
	{
		$data['info']=array();
	}
	$this->load->view('lxgj/youhuiq',$data);
}


//摄影师作品集
//摄影作品添加页面
public function pho_insert_show(){
	
	
	$this->load->view('myshop/pho_add_from_admin');

}



//摄影师作品集
public function photoes_detail(){
	$user_id=trim($this->input->get_post('user_id',TRUE));
	$call_url=base64_decode(trim($this->input->get_post('call_url',TRUE)));
    $user_per_id=$this->user_id_and_open_id();
    if(!$user_per_id){
        //echo '<pre>';print_r($_COOKIE);
        return false;
    }
    // if($user_id ==$user_per_id)
    // {
    //     //打开的是我的摄影集
    //     $data['picture']=$this->Lxgj_model->photoes('user_id');
    // }else{
    //     //打开的是TA的摄影集
    //     $data['picture']=$this->Lxgj_model->photoes('user_id');
    // }
    $where=array('user_id'=>$user_id);

	 //$data['picture']=$this->Lxgj_model->photoes('20161114');
	 $data['picture']=$this->Lxgj_model->photoes('user_id');
    if($call_url){
        $data['call_url']=$call_url;
    }else{
        $data['call_url']='olook://identify.toapp>menu';
    }

     //print_r($data);
	 $this->load->view('myshop/photos_detail',$data);

}

//获取手机user_id
    public function user_id_and_open_id(){
        if(isset($_COOKIE['user_id'])){
            $user_id=$_SESSION['user_id']=$_COOKIE['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            if(isset($_COOKIE['openid'])){
                $str=$row['openid'];
                $str=strtoupper(md5('ET'.$str));
                if($str==$_COOKIE['openid']){
                    $_SESSION['openid']=$_COOKIE['openid'];
                    return $user_id;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }elseif(isset($_COOKIE['olook'])){
            $striso=$_COOKIE['olook'];
            $arrolook=explode('-',$striso);
            $user_id=$arrolook[0];
            $openid=$arrolook[1];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            $str=$row['openid'];
            $str=strtoupper(md5('ET'.$str));
            if($str==$openid){
                $_SESSION['user_id']=$user_id;
                $_SESSION['openid']=$openid;
                return $user_id;
            }else{
                return false;
            }
        }elseif(isset($_SESSION['openid'])){
            $user_id=$_SESSION['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            $str=$row['openid'];
            $str=strtoupper(md5('ET'.$str));
            if($str==$_SESSION['openid']){
                return $user_id;
            }else{
                return false;
            }
        }elseif(isset($_SESSION['user_id'])){
            return $_SESSION['user_id'];
        }else{
            return false;
        }
    }


}
