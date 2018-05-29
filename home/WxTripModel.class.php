<?php

namespace Addons\WxTrip\Model;
use Think\Model;

/**
 * WxTrip模型
 */
class WxTripSeveModel extends Model{
//protected $tableName = 'addonspg';
//protected $tablePrefix = 'wp_'; 
	// 自定义标签类型增加方法
    function type_set($data){
	$User = M("Wxtrip_tag"); // 实例化User对象
// 根据表单提交的POST数据创建数据对象
        if(!empty($data['id'])){
        return	$User->where("id=$id")->save($data);
        }else{
        return	 $User->add($data);
        }

}
// 查询标签类型名称
function type_lable(){
	//$user= M("wxtrip_lable");
   $data['is_del']=0;
return M ( 'Wxtrip_lable' )->where ( $data)->select ();
}

// 查询页面标签类容
function type_list(){

return  M ( 'Wxtrip_tag' )->select();
}
// 查询所有目的地
function  address(){

	   $data['is_del']=0;
return M ( 'Wxtrip_destination' )->where ( $data)->select ();
}
// 页面上传save
function save_page($data=''){
	$data['keywords'] = "1";
	$data['data'] = "123";
	print_r($data);
	print_r(M("Wxtrip_cms")->add($data));die;
return M("wxtrip_cms")->add($data);
}
}
