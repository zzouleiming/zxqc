<?php
namespace Addons\WxTrip\Controller;

use Addons\WxTrip\Controller\BaseController;

class WxTripController extends BaseController{
	function _initialize() {
		$GLOBALS ['is_wap'] = 1;
		parent::_initialize ();
	}
	function config() {
		// print_r($_POST);
		$public_info = get_token_appinfo ();
		$wepUrl =  addons_url ( 'WxTrip://Wap/index', array (
				'publicid' => $public_info ['id'] 
		) );
		$normal_tips = '在微信里回复“微旅游”即可以查看效果,也可以点击：<a href="' . $wepUrl. '">预览</a>， <a id="copyLink" data-clipboard-text="' . $wepUrl . '">复制链接</a><script type="application/javascript">$.WeiPHP.initCopyBtn("copyLink");</script>';
		$this->assign ( 'normal_tips', $normal_tips );
		
		$config = D ( 'Common/AddonConfig' )->get ( MODULE_NAME );
		// dump(MODULE_NAME);
		if (IS_POST) {
			$_POST ['config'] ['background'] = implode ( ',', $_POST ['background'] );
			// $config = array_merge ( ( array ) $config, ( array ) $_POST ['config'] );
			$flag = D ( 'Common/AddonConfig' )->set ( MODULE_NAME, $_POST ['config'] );
			if ($flag !== false) {
				if ($_GET ['from'] == 'preview') {
					$url = U ( 'preview' );
				} else {
					$url = Cookie ( '__forward__' );
				}
				$this->success ( '保存成功', $url );
			} else {
				$this->error ( '400499:保存失败' );
			}
			exit ();
		}
		$this->assign ( 'data', $config );
	

		$this->display ('WxTrip/config');
	}
	
	// 添加自定义标签模版调用回写方法 @Z 03/16
	function types(){
        $user=D('WxTrip')->type_lable();
        $this->assign('data',$user);
       
		$this->display('WxTrip/types');
	}
	// 添加自定义标签 增 改 方法 @z 03/16
	function save_types(){ 
   // 实例化自定义模型类
   if(!empty($_POST)){  
    $data=$_POST['types'];
    $data['add_time']=time();
   	$user=D('WxTrip')->type_set($data);
   	 if($user!==false){
   	  	echo "上传成功";die;
   	  }else{
   	  	echo "上传失败";die;
   	  }
   }
	}

// 预加载页面上传内容
 function page_list(){
      $type_list=D('WxTrip')->type_list();

      $this->assign('lable',$type_list); // 所有标签
      $mdd=D('WxTrip')->address();
     $this->assign('mdd',$mdd); // 所有标签
      $this->display('WxTrip/page_list');

 }
 // 页面上传
function save_page(){

	$id=trim($_POST['id']);
	$data['cate_id']=$_POST['tag_id'];
	$data['destination']=$_POST['destination_id'];

	if(!empty($id)){
		$post_data['page_id']=$id;
		$url = 'http://api.etjourney.com/home/page/page_list/'.$id;	
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $file_contents = curl_exec ( $ch );
        $page = curl_exec($ch);//运行curl
        $page=json_decode($page,true);
        $data['title']=$page['short_title'];
        $data['image']=$page['image']['share'];
        $data['intro']=$page['page_title'];
        $data['data']=json_encode($page['data']);
        $data['cTime']=time();
        $data['page_id']=$page['page_id'];
        $data['business_id']=$page['business_id'];

       $page_list=D('WxTrip')->save_page($data);
       if($page_list){
       	echo "上传成功";
       } 
	}

}
	/* 预览 */
	function preview() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WxTrip://Wap/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		
		$config = get_addon_config ( 'WxTrip' );
		
		$this->assign ( 'data', $config );
		
		$this->display ();
	}
	function preview_cms() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WxTrip://Wap/lists', array (
				'publicid' => $publicid,
				'from' => 'preview' 
		) );
		$this->assign ( 'url', $url );
		
		$this->display ();
	}
	function preview_old() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WxTrip://Wap/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		$this->display ( 'Home@Addons/preview' );
	}
}
