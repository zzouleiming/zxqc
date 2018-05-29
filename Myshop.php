<?php
/**
 * app个人商城类
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Myshop extends CI_Controller
{

    public $et=FALSE;
    public $wx=FALSE;
    public $visitor=FALSE;
    public  $user_id=0;
    public function __construct()
    {
        parent::__construct();
        ini_set('php_mbstring','1');
        $this->load->model('User_model');
        $this->load->model('Order_model');
        $this->load->library('common');
        $this->load->library('session');
        //$this->load->library('imagecrop');
        $this->load->helper('url');
        $this->load->library('image_lib');
        // $this->load->driver('cache');
        $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
        $this->tologin="olook://tologin";
        $this->east_country=array("越南","老挝","柬埔寨","缅甸","泰国","马来西亚","新加坡","印度尼西亚","菲律宾","文莱","东帝汶");
        $this->get_et();
        $this->get_wx();
        $this->get_visitor();
        $this->user_id=$this->user_id_and_open_id();
        $this->share_url='olook://shareinfo<';
        $this->touser_url="olook://touser<";
    }


    public function get_visitor()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'visitor')===false)
        {
            $this->visitor=FALSE;
        }
        else
        {
            $this->visitor=TRUE;
        }
    }

    public function get_et()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false)
        {
            $this->et=FALSE;
        }
        else
        {
            $this->et=TRUE;
        }
    }

    public function get_wx()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE)
        {
            $this->wx=FALSE;
        }
        else
        {
            $this->wx=TRUE;
        }
    }

    //个人商城页面

    public function my_mall_list()
    {
        $user_id=$this->input->get('user_id',true);
        $data['wx']=$this->wx;
        $data['et']=$this->et;


        // echo $per_user_id;
        if($data['et']===TRUE OR $this->visitor==TRUE)
        {
            $per_user_id= $this->user_id;
            $data['to_user']=$this->touser_url.$user_id;

        }else{
            $data['to_user']="javascript:void(0)";
            $per_user_id='-1';
        }
        if($data['et']===TRUE)
        {
            if(isset($_COOKIE['olook']))
            {
                $arrolook=explode('-',$_COOKIE['olook']);
                $location=explode(',',$arrolook[2]);
                $lat=$location[0];$lng=$location[1];
            }
            elseif(isset($_COOKIE['location']))
            {
                $location=explode(',',$_COOKIE['location']);
                $lat=$location[0];$lng=$location[1];
            }
            else
            {
                $lat=	31.05;
                $lng=	121.76;
            }
        }else{

        }
        if($user_id==$per_user_id)
        {
            $data['part']='me';
        }
        else
        {
            $data['part']='other';
        }
        //echo $per_user_id;
        // $data['part']='me';    $per_user_id=1077;
        $user_info=$this->User_model->get_select_one('user_name,auth,is_merchant',array('user_id'=>$user_id),'v_users');
        $data['is_bus']=$user_info['auth'];
        $data['is_bus']=$user_info['is_merchant'];
        $data['user_name']=$user_info['user_name'];
        if($data['part']=='me')
        {
            $data['user_name']='我';
            $data['products_list_name']='我的产品';
        }else{
            $data['products_list_name']='Ta的其他产品';
        }
        $data['shop']=$this->User_model->get_select_one('business_name,business_info,business_country,business_id,tag,star_num,discount,logo_image_thumb as image,address',array('user_id'=>$user_id,'is_show'=>'1'),'v_wx_business');

//        $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.tag,v_activity_children.order_sell AS goods_buy,
//    v_activity_children.banner_product as banner_image,
//    v_goods.ori_price,v_goods.shop_price,v_goods.low,v_goods.goods_id,v_activity_children.special";
//        $data['products']=$this->User_model->get_select_all($select,array('v_activity_children.user_id'=>$user_id,'special'=>'3','v_activity_children.is_show'=>'1','v_goods.goods_id>'=>'0'),'v_activity_children.act_id','ASC',
//            'v_activity_children',1,'v_goods',"v_goods.act_id=v_activity_children.act_id AND v_goods.is_show='1'");

        if($user_info['is_merchant']==1)
        {
            $data['products']=$this->User_model->get_products(-1,'3',100,$start=0,'all'," AND v_activity_children.user_id=$user_id");
        }else{
            $data['products']=$this->User_model->get_ts_products("AND  v_ts.user_id=$user_id");
        }
        if($this->input->get('test'))
        {
            echo '<pre>';
            echo $this->db->last_query();
            print_r($data);exit();
        }

        if(count($data['products'])==0)
        {
//            $where_new="range_name LIKE '%普吉%'  AND v_activity_children.is_show='1' AND v_activity_children.act_status='2' AND v_goods.goods_id>0";
//            $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.tag,v_activity_children.banner_product as banner_image,
//            v_activity_children.order_sell AS goods_buy,
//      v_goods.ori_price,v_goods.shop_price,v_goods.low,v_goods.goods_id,v_activity_children.special";
//            $data['products']=$this->User_model->get_select_all($select,$where_new,'v_activity_children.add_time','DESC',
//                'v_activity_children',1,'v_goods',"v_goods.act_id=v_activity_children.act_id AND v_goods.is_show='1'",
//                $sum=false,$L=1, 0,5);

            $data['products']=$this->User_model->get_special_line($range_id=0,$page_num=5,$start=0,$order='all',$seats='0');
            if($data['products']===false){
                $data['products']=array();
            }
            $data['products_list_name']='热销产品';
        }


        if($data['shop']===0)
        {
            $data['shop']=array();
        }
        else
        {
            $data['shop']['tag_arr']=explode(',',$data['shop']['tag']);
        }

        if($data['part']=='other')
        {
            $where=array('is_show'=>'1');
            $select="user_id,address,business_id,logo_image_thumb as image,business_name,star_num,discount,tag,lat,lng";
            $data['other_shops']=$this->User_model->get_select_more($select,$where,0,4,"business_id",'ASC',$table='v_wx_business');
            if($data['other_shops']!==0)
            {
                foreach($data['other_shops'] as $k=>$v)
                {
                    if(mb_strlen($v['business_name'])>5)
                    {
                        $data['other_shops'][$k]['business_name']=mb_substr($v['business_name'],0,5).'...';
                    }
                    $str='';
                    $tag=explode(',',$v['tag']);
                    foreach($tag as $k1=>$v1)
                    {
                        $str.=$v1;
                        $data['other_shops'][$k]['tag_arr'][]=$v1;
                        if(mb_strlen($str)>1)
                        {
                            break;
                        }
                    }
                    if($str=='')
                    {
                        // $data['other_shops'][$k]['str']=$str;
                        $data['other_shops'][$k]['tag_arr']=array('特价');
                    }
                }
            }
        }
//        foreach($data['products'] as $k=>$v)
//        {
//
//            $temp_arr=explode(',',$v['tag']);
//            $data['products'][$k]['tag_arr']=$temp_arr;
//        }

        $data['share']['share_url']=base_url("myshop/my_mall_list?user_id=$user_id&share_user_id=$per_user_id");
        //$data['share']['title']=$data['products']['title'];
        $data['share']['title']= $data['user_name']."个人商城";
        //$data['share']['image']=$data['products']['banner_image'];
        $data['share']['image']=isset($data['shop']['image'])?$data['shop']['image']:base_url('public/myshop/images/create.png');
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";
        $data['json_share']=json_encode($data['share']);
        $data['share_url']=$this->share_url.$data['json_share'];


        // if(history.length==0){ }
        if(!isset($_SERVER['HTTP_REFERER']) OR is_null($_SERVER['HTTP_REFERER']))
        {
            $data['back_url']='olook://identify.toapp>menu';
        }else{
            $data['back_url']="javascript:history.go(-1)";
        }

        $data['call_url']="javascript:history.go(-1)";
        $data['share_user_id']=$this->input->get_post('share_user_id',TRUE);
        //$data['business_info']=base_url('bussell/bussinfo');
        if($data['part']=='me')
        {
            if(count($data['shop'])==0)
            {
                $this->load->view('myshop/my_shop_no',$data);
            }
            else
            {
                $this->load->view('myshop/my_shop',$data);
            }
        }
        else
        {

            $this->load->view('myshop/ta_shop',$data);
        }
    }


    //个人产品页面
    public function personal_products_list()
    {


        $personal_id=$this->input->get('user_id',TRUE);
        if(!$personal_id){
            $personal_id=-1;
            //$personal_id=3706;

        }
        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        $data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        $data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }
        if($this->input->get('nosepc'))
        {
            $where=" v_activity_children.is_show='1' AND v_activity_children.act_status='2'  AND v_goods.goods_id IS NOT NULL AND ((v_activity_father.act_status='2' AND v_activity_father.is_show='1') OR v_activity_children.special='2')";
            //$where2=" v_activity_children.is_show='1' AND v_activity_children.act_status='2'  AND v_goods.goods_id IS NOT NULL";
        }else{
            //(1210,2273,1862)
            $where=" v_activity_children.special='3' AND v_activity_children.is_show='1' AND v_activity_children.act_status='2' AND v_goods.goods_id > 0 AND  v_activity_children.user_id='$personal_id'";
        }


        $page_num =10;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_products_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.banner_product as banner_image,v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,v_goods.ori_price,v_goods.oori_price";


        if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
        {
            $data['back_url']='olook://identify.toapp>menu';
        }
        else
        {
            $data['back_url']='olook://identify.toapp?menu';
        }
        if($page==1)
        {
            $data['products']=$this->User_model->get_products_list_all($select,$where,$start,$call_page_num);
            foreach($data['products'] as $k=>$v)
            {
                $where="goods_id=$v[goods_id] AND order_status = '3'";
                if($v['shop_price']=='0.00')
                {
                    $data['products'][$k]['shop_price']=$v['ori_price'];
                }
                if($v['oori_price']=='0.00')
                {
                    $data['products'][$k]['oori_price']=$v['ori_price']+100;
                }
                if($v['ori_price']!='0.00'){
                    $data['products'][$k]['shop_price']=$v['ori_price'];
                }
                $data['products'][$k]['goods_buy']=$this->User_model->get_order_count($where);
                $data['products'][$k]['goods_buy']= $data['products'][$k]['goods_buy']['count'];
                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                if(stristr($v['banner_hot'], 'http')===false)
                {
                    $data['products'][$k]['banner_hot'] = $this->config->item('base_url'). ltrim($v['banner_hot'],'.');
                }

            }

            $data['share']['share_url']=base_url("myshop/personal_products_list?share_user_id={$data['share_user_id']}&user_id=$personal_id");
            $data['share']['title']='特价产品';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="坐享其成上的特价产品。";
            $data['json_share']=json_encode($data['share']);
            if($count['count']>=4)
            {
                for($i=1;$i<=4;$i++)
                {
                    $data['hot'][]=array_shift($data['products']);
                }
            }
            else
            {
                $data['hot']=$data['products'];
                $data['products']=array();
            }
            /*for($i=1;$i<=5;$i++){
              $data['products'][]=$data['hot'][1];
            }*/
            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{

                $data['call_url']=base_url("myshop/personal_products_list?user_id=$personal_id");
                $data['call_url']=$this->common->url_encode($data['call_url']);
                if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
                {
                    $url=base_url("myshop/personal_products_list?share_user_id={$data['share_user_id']}&user_id=$personal_id");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    if($user_id==1){
                        //echo '<pre>';print_r($data);
                    }
                    $data['call_url']="javascript:history.go(-1)";
                }
                // $data['call_url']=base_url("myshop/products_list");

                $this->load->view('myshop/products_list',$data);
            }

        }
        else
        {
            $data['products']=$this->User_model->get_products_list_all($select,$where,$start,$page_num);
            foreach($data['products'] as $k=>$v)
            {
                $where="goods_id=$v[goods_id] AND order_status = '3'";
                $data['products'][$k]['goods_buy']=$this->User_model->get_order_count($where);
                $data['products'][$k]['goods_buy']= $data['products'][$k]['goods_buy']['count'];
                if($v['shop_price']=='0.00')
                {
                    $data['products'][$k]['shop_price']=$v['ori_price'];
                }
                if($v['ori_price']!='0'){
                    $data['products'][$k]['shop_price']=$v['ori_price'];
                }
                if($v['oori_price']=='0')
                {
                    $data['products'][$k]['oori_price']=number_format($v['ori_price']+101,0,'.','');
                }
                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }

            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }
    }
//特价线路 special=4
    public function special_line()
    {
        $lat=$lng=0;

        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
        {
            $per_user_id=$this->user_id_and_open_id();


        }
        else
        {
            $url=base_url("myshop/special_line");
            $per_user_id=$this->get_wx_userid($url);

        }

        $data['order']=$this->input->get_post('order',true);
        $ajax=$this->input->get_post('ajax',true);

        $data['range']=$this->input->get_post('range',true);
        if(!$data['range']){$data['range']=0;}
        $all_act_id_arr=$this->User_model->get_select_all('act_id',array('is_show'=>'1','range_id'=>$data['range']),'act_id','ASC','v_act_range');
        $act_arr=array('0');
        if(is_array($all_act_id_arr)){
            foreach($all_act_id_arr as $k=>$v)
            {
                $act_arr[]="'".$v['act_id']."'";
                //  $temp[]="'".$v['act_id']."'";
            }
        }
        $act_arr=array_values($act_arr);
        $act_str=implode(',',$act_arr);

        if(!$data['order'])
        {
            $data['order']=2;
        }

        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        //$data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        //$data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }
        $where="v_activity_children.special='4' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'  ";
        //   $where="v_activity_children.special='2'  AND v_activity_children.is_show='1' AND v_goods.is_show = '1'";
        //  $where='1=1';
        if($data['range'] AND count($act_arr)>=1)
        {
            $where.="  AND v_activity_children.act_id IN ($act_str)";
        }
        $page_num =8;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_special_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell as goods_buy,v_activity_children.banner_product ,
        v_activity_children.banner_hot  AS image,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,
        v_goods.ori_price,v_goods.oori_price ";


        $data['back_url']='olook://identify.toapp>menu';
        if(is_null($_SERVER['HTTP_REFERER']))
        {
            $data['back_url']='olook://identify.toapp>menu';
        }else{
            $data['back_url']="javascript:histroy.go(-1)";
        }

        if($page==1)
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            // echo $this->db->last_query();
            foreach($data['products'] as $k=>$v)
            {

                // $data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                if(mb_strlen($v['title'])>11){
                    $data['products'][$k]['title']=mb_substr($v['title'],0,11).'……';
                }

                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>12){
                        break;
                    }
                }
            }

            $data['share']['share_url']=base_url("myshop/special_line?share_user_id={$data['share_user_id']}");
            $data['share']['title']='特价产品';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="坐享其成上的特价产品。";
            $data['json_share']=json_encode($data['share']);
            $data['share_url']='olook://shareinfo<'.$data['json_share'];
            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{
                $data['call_url']=base64_encode(base_url("myshop/special_line?lat=$lat&lng=$lng&order=$data[order]&range=$data[range]"));

                if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
                {
                    $url=base_url("myshop/special_line?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    if($user_id==1){
                        //echo '<pre>';print_r($data);
                    }
                    $data['call_url']="javascript:history.go(-1)";
                }
                // $data['call_url']=base_url("myshop/products_list");
                if($ajax)
                {
                    echo json_encode($data['products']);
                }
                else
                {
                    $this->load->view('myshop/special_products_view',$data);
                }

            }

        }
        else
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            foreach($data['products'] as $k=>$v)
            {

                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>12){
                        break;
                    }
                }

            }

            echo json_encode($data['products']);
        }
    }

    //特殊线路筛选条件选择页面
    public function special_select()
    {
        $data['name']=$this->input->get('name',TRUE);
        $data['order']=$this->input->get('order',TRUE);
        $data['range']=$this->input->get('range',TRUE);
        if(!$data['order']){
            $data['order']=1;
        }


        switch($data['order']){
            case 1:
                $data['order_name']='距离';
                break;
            case 2:
                $data['order_name']='销量';
                break;
            case 3:
                $data['order_name']='价格最低';
                break;
            case 4:
                $data['order_name']='价格最高';
                break;
        }


        $this->load->view('myshop/special_select',$data);
    }

    //
    //交通 special=6
    public function traffic_products()
    {
        $lat=$lng=0;

        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
        {
            $per_user_id=$this->user_id_and_open_id();
            if($per_user_id){
                if(isset($_SESSION['lat'])){
                    $lat=$_SESSION['lat'];
                }else{
                    $lat=$this->input->get('lat',TRUE);
                    if($lat)
                    {
                        $_SESSION['lat']=$lat;
                    }
                }

                if(isset($_SESSION['lng'])){
                    $lat=$_SESSION['lng'];
                }else{
                    $lng=$this->input->get('lng',TRUE);
                    if($lng)
                    {
                        $_SESSION['lng']=$lng;
                    }
                }

            }
        }
        else
        {
            $url=base_url("myshop/traffic_products");
            $per_user_id=$this->get_wx_userid($url);
            if(isset($_SESSION['lat'])){
                $lat=$_SESSION['lat'];
            }
            if(isset($_SESSION['lng'])){
                $lng=$_SESSION['lng'];
            }
        }


        $data['order']=$this->input->get_post('order',true);


        $data['range']=$this->input->get_post('range',true);
        if(!$data['range']){$data['range']=0;}
        $all_act_id_arr=$this->User_model->get_select_all('act_id',array('is_show'=>'1','range_id'=>$data['range']),'act_id','ASC','v_act_range');
        $act_arr=array('0');
        if(is_array($all_act_id_arr)){
            foreach($all_act_id_arr as $k=>$v)
            {
                $act_arr[]="'".$v['act_id']."'";
                //  $temp[]="'".$v['act_id']."'";
            }
        }
        $act_arr=array_values($act_arr);
        $act_str=implode(',',$act_arr);


        if(!$data['order'])
        {
            $data['order']=2;
        }
        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        //$data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        //$data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }
        $where="v_activity_children.special='6' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1' ";

        if($data['xtype'])
        {
            $where.="  AND xtype = '$data[xtype]'";
        }

        if($data['range'] AND count($act_arr)>=1)
        {
            $where.="  AND v_activity_children.act_id IN ($act_str)";
        }
        //   $where="v_activity_children.special='2' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'  ";

        $page_num =8;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_special_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell as goods_buy,v_activity_children.banner_product AS image,
        v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,
        v_goods.ori_price,v_goods.oori_price,
        ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($lat*PI()/180-v_wx_business.lat*PI()/180)/2),2)+COS($lat*PI()/180)*COS(v_wx_business.lat*PI()/180)*POW(SIN(($lng*PI()/180-v_wx_business.lng*PI()/180)/2),2)))*1000) AS distance ";


        $data['back_url']='olook://identify.toapp>menu';
        if($page==1)
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);

            foreach($data['products'] as $k=>$v)
            {

                //  $data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                if(mb_strlen($v['title'])>11){
                    $data['products'][$k]['title']=mb_substr($v['title'],0,11).'……';
                }
                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>12){
                        break;
                    }
                }
            }

            $data['share']['share_url']=base_url("myshop/traffic_products?share_user_id={$data['share_user_id']}");
            $data['share']['title']='特价产品';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="坐享其成上的特价产品。";
            $data['json_share']=json_encode($data['share']);
            $data['share_url']='olook://shareinfo<'.$data['json_share'];
            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{
                $data['call_url']=base64_encode(base_url("myshop/traffic_products?lat=$lat&lng=$lng&order=$data[order]&range=$data[range]"));
                // $data['call_url']=base64_encode(base_url("myshop/local_products?lat=$lat&lng=$lng&xtype=$data[xtype]&order=$data[order]&range=$data[range]"));
                if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
                {
                    $url=base_url("myshop/traffic_products?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    if($user_id==1){
                        //echo '<pre>';print_r($data);
                    }
                    $data['call_url']="javascript:history.go(-1)";
                }
                // $data['call_url']=base_url("myshop/products_list");

                $this->load->view('myshop/traffic_products_view',$data);
            }

        }
        else
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            foreach($data['products'] as $k=>$v)
            {

                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,mb_strlen($data['products'][$k]['tag'])-1);
                    }
                }


            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }
    }



    //当地项目 special=5
    //order 1距离 2销量 3价格低 4价格高
    //xtype 1潜水2看秀3spa4丛林飞跃5水上活动6当地特色
    public function local_products()
    {


        $data['order']=$this->input->get_post('order',true);
        $range=$data['range']=$this->input->get_post('range',true);
        $all_act_id_arr=$this->User_model->get_select_all('act_id',array('is_show'=>'1','range_id'=>$range),'act_id','ASC','v_act_range');
        $act_arr=array('0');
        if(is_array($all_act_id_arr)){
            foreach($all_act_id_arr as $k=>$v)
            {
                $act_arr[]="'".$v['act_id']."'";
                //  $temp[]="'".$v['act_id']."'";
            }
        }
        $act_arr=array_values($act_arr);
        $act_str=implode(',',$act_arr);


        $data['xtype']=$this->input->get_post('xtype',true);
        if(! $data['xtype']){
            $data['xtype']=0;
            $data['xtype_name']='全部分类';
        }
        elseif( $data['xtype']=='1')
        {
            $data['xtype_name']='出海';
        }
        elseif( $data['xtype']=='2')
        {
            $data['xtype_name']='看秀';
        }
        elseif( $data['xtype']=='3')
        {
            $data['xtype_name']='SPA';
        }
        elseif( $data['xtype']=='4')
        {
            $data['xtype_name']='丛林飞跃';
        }
        elseif( $data['xtype']=='5')
        {
            $data['xtype_name']='当地特色';
        }
        elseif( $data['xtype']=='6')
        {
            $data['xtype_name']='水上活动';
        }

        $data['xtype_arr']=array(
            array(
                'xtype'=>'1',
                'xtype_name'=>'出海',
                'url'=>base_url("myshop/local_products_choosed?xtype=1&range=$range")
            ),
            array(
                'xtype'=>'2',
                'xtype_name'=>'看秀',
                'url'=>base_url("myshop/local_products_choosed?xtype=2&range=$range")
            ),
            array(
                'xtype'=>'3',
                'xtype_name'=>'SPA',
                'url'=>base_url("myshop/local_products_choosed?xtype=3&range=$range")
            ),
            array(
                'xtype'=>'4',
                'xtype_name'=>'丛林飞跃',
                'url'=>base_url("myshop/local_products_choosed?xtype=4&range=$range")
            ),
            array(
                'xtype'=>'5',
                'xtype_name'=>'水上活动',
                'url'=>base_url("myshop/local_products_choosed?xtype=5&range=$range")
            ),
            array(
                'xtype'=>'6',
                'xtype_name'=>'当地特色',
                'url'=>base_url("myshop/local_products_choosed?xtype=6&range=$range")
            ),
        );

        if(!$data['order'])
        {
            $data['order']=2;
        }
        switch($data['order']){
            case 1:
                $data['order_name']='距离';
                break;
            case 2:
                $data['order_name']='销量';
                break;
            case 3:
                $data['order_name']='价格最低';
                break;
            case 4:
                $data['order_name']='价格最高';
                break;
        }
        $page=$this->input->post('page',true);
        if(!$page)
        {
            $page=1;
        }
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        //$data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        //$data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');
        if($this->et===FALSE)
        {
            $data['share_noshow']=TRUE;
        }

        $where="v_activity_children.special='5' AND v_activity_children.is_show='1' AND v_goods.is_show = '1'  ";
        //  $where="v_activity_children.special='2' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'";
        if($data['xtype'])
        {
            $where.="  AND xtype = '$data[xtype]'";
        }

        if($range AND count($act_arr)>=1)
        {

            $where.="  AND v_activity_children.act_id IN ($act_str)";
        }
        //print_r($act_str);exit();
        $page_num =7;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_special_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell as goods_buy,v_activity_children.banner_product AS image,
        v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,
        v_goods.ori_price,v_goods.oori_price ";



        if($page==1)
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            // echo $this->db->last_query();
            foreach($data['products'] as $k=>$v)
            {

                //$data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;

                if(mb_strlen($v['title'])>11){
                    $data['products'][$k]['title']=mb_substr($v['title'],0,11).'……';
                }


                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>14){
                        break;
                    }
                }


            }

            $data['share']['share_url']=base_url("myshop/local_products?share_user_id={$data['share_user_id']}");
            $data['share']['title']='特价产品';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="坐享其成上的特价产品。";
            $data['json_share']=json_encode($data['share']);
            $data['share_url']='olook://shareinfo<'.$data['json_share'];

            $data['type_select']=base_url('myshop/local_select?name=fenlei&xtype=').$data['xtype'].'&order='.$data['order'].'&range='.$data['range'];
            $data['order_select']=base_url('myshop/local_select?name=paixu&order=').$data['xtype'].'&order='.$data['order'].'&range='.$data['range'];

            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{

                if($this->wx=='TRUE')
                {
                    $url=base_url("myshop/local_products?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    $data['call_url']="javascript:history.go(-1)";
                    $data['call_url']="javascript:void(0);";
                }
                else
                {
                    $data['call_url']='olook://identify.toapp';
                }

                $this->load->view('myshop/local_products_view',$data);
            }

        }
        else
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            foreach($data['products'] as $k=>$v)
            {

                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }

                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,strlen($data['products'][$k]['tag'])-1);
                    }
                }
            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }
    }





//当地项目选择后列表
    public function local_products_choosed()
    {
        $lat=$lng=0;



        $data['xtype']=$this->input->get_post('xtype',true);
        $data['ajax']=$this->input->get_post('ajax',true);
        if(! $data['xtype']){
            $data['xtype']=0;
            $data['xtype_name']='全部分类';
        }
        elseif( $data['xtype']=='1')
        {
            $data['xtype_name']='出海';
        }
        elseif( $data['xtype']=='2')
        {
            $data['xtype_name']='看秀';
        }
        elseif( $data['xtype']=='3')
        {
            $data['xtype_name']='SPA';
        }
        elseif( $data['xtype']=='4')
        {
            $data['xtype_name']='丛林飞跃';
        }
        elseif( $data['xtype']=='5')
        {
            $data['xtype_name']='水上活动';
        }
        elseif( $data['xtype']=='6')
        {
            $data['xtype_name']='当地特色';
        }


        $data['order']=$this->input->get_post('order',true);


        $data['range']=$this->input->get_post('range',true);
        $all_act_id_arr=$this->User_model->get_select_all('act_id',array('is_show'=>'1','range_id'=>$data['range']),'act_id','ASC','v_act_range');
        $act_arr=array('0');
        if(is_array($all_act_id_arr)){
            foreach($all_act_id_arr as $k=>$v)
            {
                $act_arr[]="'".$v['act_id']."'";
                //  $temp[]="'".$v['act_id']."'";
            }
        }
        $act_arr=array_values($act_arr);
        $act_str=implode(',',$act_arr);


        if(!$data['order'])
        {
            $data['order']=2;
        }

        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        //$data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        //$data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }
        $where="v_activity_children.special='5' AND v_activity_children.is_show='1' AND v_activity_children.act_status='2' AND v_goods.is_show = '1'  ";
        //$where="v_activity_children.special='2' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'";
        if($data['xtype'])
        {
            $where.="  AND xtype = '$data[xtype]'";
        }

        if($data['range'] AND count($act_arr)>=1)
        {
            $where.="  AND v_activity_children.act_id IN ($act_str)";
        }
        //  $where="v_activity_children.special='2' AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'";

        $page_num =7;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_special_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell as goods_buy,v_activity_children.banner_product AS image,
        v_activity_children.banner_hot,v_activity_children.hot,v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,
        v_goods.ori_price,v_goods.oori_price";
        if($page==1)
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            //echo $this->db->last_query();
            foreach($data['products'] as $k=>$v)
            {

                //$data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                //  $data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                if(mb_strlen($v['title'])>11){
                    $data['products'][$k]['title']=mb_substr($v['title'],0,11).'……';
                }

                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>14){
                        break;
                    }
                }

                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,mb_strlen($data['products'][$k]['tag'])-1);
                    }
                }

            }

            $data['share']['share_url']=base_url("myshop/local_products_choosed?share_user_id=$data[share_user_id]&range=$data[range]&xtype=$data[xtype]");
            $data['share']['title']=$data['range'].'当地项目';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="当地项目。";
            $data['json_share']=json_encode($data['share']);
            $data['share_url']='olook://shareinfo<'.$data['json_share'];


            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{
                if($this->wx==TRUE)
                {

                    $url=base_url("myshop/local_products?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);

                }

                $data['call_url']="javascript:history.go(-1)";


                if($data['ajax'])
                {
                    echo json_encode($data['products']);
                }
                else
                {
                    $this->load->view('myshop/local_products_choosed',$data);
                }
            }

        }
        else
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            foreach($data['products'] as $k=>$v)
            {

                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,mb_strlen($data['products'][$k]['tag'])-1);
                    }
                }

            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }



    }



//当地项目筛选条件选择页面
    public function local_select()
    {
        $data['name']=$this->input->get('name',TRUE);

        if(isset($_SESSION['lat'])){
            $data['lat']=$_SESSION['lat'];
        }else{
            $data['lat']=0;
        }

        if(isset($_SESSION['lng'])){
            $data['lng']=$_SESSION['lng'];
        }else{
            $data['lng']=0;
        }


        $data['order']=$this->input->get('order',TRUE);
        $data['range']=$this->input->get('range',TRUE);
        if(!$data['order']){
            $data['order']=1;
        }
        $data['xtype']=$this->input->get('xtype',TRUE);
        if(!$data['xtype']){
            $data['xtype']=0;
        }

        switch($data['order']){
            case 1:
                $data['order_name']='距离';
                break;
            case 2:
                $data['order_name']='销量';
                break;
            case 3:
                $data['order_name']='价格最低';
                break;
            case 4:
                $data['order_name']='价格最高';
                break;
        }

        switch($data['xtype']){
            case 1:
                $data['xtype_name']='潜水';
                break;
            case 2:
                $data['xtype_name']='看秀';
                break;
            case 3:
                $data['xtype_name']='spa';
                break;
            case 4:
                $data['xtype_name']='丛林飞跃';
                break;
            case 5:
                $data['xtype_name']='水上活动';
                break;
            case 6:
                $data['xtype_name']='当地特色';
                break;
            default:
                $data['xtype_name']='全部分类';
        }

        $this->load->view('myshop/local_select',$data);
    }


//特价产品页面
    public function products_list()
    {
        $data['discount_type']=$this->input->get('discount_type',true);
        $data['range']=$this->input->get_post('range',true);
        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        $data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        $data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false){
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }

        //$where="v_activity_children.special IN ('2','4','5','6') AND v_activity_children.is_show='1' AND v_activity_children.act_status='2' AND v_goods.is_show='1'";

        $where="v_activity_children.special IN ('2','4','5','6') AND v_activity_children.is_show='1'  AND v_goods.is_show='1'";

        if($data['discount_type'])
        {
            $where.="  AND discount_type = '$data[discount_type]'";
        }

        if($data['range'])
        {
            $where.="  AND range = '$data[range]'";
        }
        $page_num =10;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_products_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.banner_product as banner_image,v_activity_children.banner_hot,
    v_activity_children.hot,v_activity_children.tag,v_activity_children.order_sell AS goods_buy,
    v_goods.goods_id,v_goods.low,v_goods.shop_price,v_goods.ori_price,v_goods.oori_price";


        //olook://identify.toapp
        $data['back_url']="javascript:void(0)";
        if($page==1)
        {
            $data['products']=$this->User_model->get_products_list_all($select,$where,$start,$call_page_num);
            foreach($data['products'] as $k=>$v)
            {

                $data['products'][$k]['shop_price']=$v['ori_price'];

                // $data['products'][$k]['goods_buy']= $data['products'][$k]['goods_buy']['count']+$v['goods_id']%7;
                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                if(stristr($v['banner_hot'], 'http')===false)
                {
                    $data['products'][$k]['banner_hot'] = $this->config->item('base_url'). ltrim($v['banner_hot'],'.');
                }

            }

            $data['share']['share_url']=base_url("myshop/products_list?share_user_id={$data['share_user_id']}");
            $data['share']['title']='特价产品';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="坐享其成上的特价产品。";
            $data['json_share']=json_encode($data['share']);
            if($count['count']>=4)
            {
                for($i=1;$i<=4;$i++)
                {
                    $data['hot'][]=array_shift($data['products']);
                }
            }
            else
            {
                $data['hot']=$data['products'];
                $data['products']=array();
            }
            /*for($i=1;$i<=5;$i++){
              $data['products'][]=$data['hot'][1];
            }*/
            if($this->input->get('test')){
                echo '<pre>';
                print_r($data);exit();


            }else{
                $data['call_url']=1;
                if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
                {
                    $url=base_url("myshop/products_list?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    if($user_id==1){
                        //echo '<pre>';print_r($data);
                    }
                    $data['call_url']="javascript:history.go(-1)";
                }
                // $data['call_url']=base_url("myshop/products_list");

                $this->load->view('myshop/products_list_new',$data);
            }

        }
        else
        {
            $data['products']=$this->User_model->get_products_list_all($select,$where,$start,$page_num);
            foreach($data['products'] as $k=>$v)
            {
                //  $data['products'][$k]['goods_buy']= $data['products'][$k]['goods_buy']['count']+$goods_id%7;

                $data['products'][$k]['shop_price']=$v['ori_price'];

                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }

            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }
    }
    //特价产品增加
    public function products_add()
    {

        $data['ban_title']='特价产品添加';
        $this->load->view('myshop/products_add',$data);
    }

    public function products_edit()
    {
        $act_id=$this->input->get('act_id',true);
        $data['ban_title']='特价产品编辑';
        $data['info']=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_children');
        $data['day']=json_decode( $data['info']['day_list'],true);

        $data['goods']=$this->User_model->get_select_one('shop_price,dateto,pricehas,priceno,goods_number,pricecom',array('act_id'=>$act_id),'v_goods');
        $this->load->view('myshop/products_add',$data);
    }

    //app 内 产品添加
    public function products_insert_app()
    {
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            //$user_id=1077;
            return FALSE;
        }
        //活动上下架
        $up_down_act=$this->input->post('up_down_act',true);
        //商品上下架
        if($this->input->post('top')){
            $disorder=1;
        }else{
            $disorder=99;
        }
        // $this->size_validate('banner',61440);
        $title=trim($this->input->post('title',true));
        $day=$this->input->post('day',true);
        $new_day=$day;
        foreach($day as $k=>$v){
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $day=json_encode($new_day);
        $content=trim($this->input->post('content',true));
        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $user_id.'banner');
            $banner_image=$this->imagecropper($banner,'banner','time',$width='700',$height='300');
        }else{
            $banner_image='';
        }


        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $user_id.'banner_hot');
            $banner_hot= $this->imagecropper($banner_hot,'banner_hot','time',$width='341',$height='240');
        }else{
            $banner_hot='';
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $user_id.'banner_product');
            $banner_product= $this->imagecropper($banner_product,'banner_product','time',$width='100',$height='100');
        }else{
            $banner_product='';
        }

        $add_time=time();

        $data=array(
            'title'=>$title,
            'user_id'=>$user_id,
            'pid'=>0,
            'day_list'=>$day,
            'content_text'=>$content,
            'banner_image'=>$banner_image,
            'banner_product'=>$banner_product,
            'banner_hot'=>$banner_hot,
            'act_status'=>'2',
            'displayorder'=>$disorder,
            'is_temp'=>'0',
            'add_time'=>$add_time,
            'special'=>'3'
        );

        if($user_id==1077){
            // echo '<pre>';print_r($noceruser);exit();
        }
        //
        $act_id= $this->User_model->user_insert('v_activity_children',$data);


        $goods_number=trim($this->input->post('goods_number',true));
        $shop_price=trim($this->input->post('shop_price',true));
        $low=$this->input->post('low',true);

        $dateto=trim($this->input->post('dateto',true));
        $pricehas=trim($this->input->post('pricehas',true));
        $priceno=trim($this->input->post('priceno',true));
        $pricecom=trim($this->input->post('pricecom',true));
        if($low!=1){
            $low=0;
        }
        $data=array(
            'goods_name'=>$title,
            'goods_number'=>$goods_number,
            'shop_price'=>$shop_price,
            'act_id'=>$act_id,
            'add_time'=>$add_time,
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'is_show'=>'1',

        );
        if($goods_number>0 && $shop_price>0){
            $this->User_model->user_insert('v_goods',$data);
        }
        redirect(base_url("myshop/country_list?is_show={$up_down_act}&act_id={$act_id}"));
    }

    //国家列表
    public function country_list()
    {
        $data['count_url']=$this->count_url;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id;
        $data['is_show']=$this->input->get('is_show',true);
        $data['act_id']=$this->input->get('act_id',true);
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="(name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%' or name_jp LIKE '%$keyword%' or name_hy LIKE '%$keyword%' )AND level=2 " ;
            $data['sou']=1;
        }else{
            $where=array('level'=>2);
        }

        $type=$this->get_lan_user();
        if($user_id==0){
            $type='en';
        }
        if($type=='zh-cn' OR $type=='zh-CN'){
            $select='id,name,name_pinyin,name_en';
            $order='name_pinyin';
            $this->lang->load('jt', 'english');
        }elseif($type=='zh-TW' OR $type=='zh-tw'){
            $select='id,name_pinyin,name_ft as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ja-jp' OR $type=='ja-JP'){
            $select='id,name_pinyin,name_jp as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ko-kr' OR $type=='ko-KR'){
            $select='id,name_pinyin,name_hy as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }else{
            $data['type']='en';
            $select='id,name_pinyin,name_en as name';
            $order='name_en';
            $this->lang->load('eng', 'english');
        }
        $data['list']=$this->User_model->get_city($select,$where,$order);

        $this->load->view('myshop/country_list',$data);
    }

//app 内 城市列表
    public function city_list()
    {
        $data['count_url']=$this->count_url;
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $data['user_id']=$user_id=$_SESSION['user_id'];
        $pid=$this->input->get('pid',true);
        $data['is_show']=$this->input->get('is_show',true);
        $data['act_id']=$this->input->get('act_id',true);

        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="(name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%' or name_jp LIKE '%$keyword%' or name_hy LIKE '%$keyword%' ) AND level=3 " ;
            $data['sou']=1;
        }else{
            $where=array('pid'=>$pid);
        }
        $type=$this->get_lan_user();
        if($user_id==0){
            $type='en';
        }
        if($type=='zh-cn' OR $type=='zh-CN'){
            $select='id,name,name_pinyin,name_en';
            $order='name_pinyin';
            $this->lang->load('jt', 'english');
        }elseif($type=='zh-TW' OR $type=='zh-tw'){
            $select='id,name_pinyin,name_ft as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ja-jp' OR $type=='ja-JP'){
            $select='id,name_pinyin,name_jp as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }elseif($type=='ko-kr' OR $type=='ko-KR'){
            $select='id,name_pinyin,name_hy as name';
            $order='name_pinyin';
            $this->lang->load('ft', 'english');
        }else{
            $data['type']='en';
            $select='id,name_pinyin,name_en as name';
            $order='name_en';
            $this->lang->load('eng', 'english');
        }
        $data['list']=$this->User_model->get_city($select,$where,$order);
        if(empty($data['list'])){
            $data['list']=$this->User_model->get_city($select,array('id'=>$pid ),$order);
        }
        $this->load->view('myshop/city_list',$data);
    }


    /*
   * 城市提交
     *
     * 目的地
  0未匹配
  1海岛(删除)
  2港澳台
  3日韩
  4东南亚
  5欧美
  6其他
   */
    public function city_sub()
    {
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){return false;}
        $data['user_id']=$user_id;
        $id=$this->input->get('id',true);
        $is_show=$this->input->get('is_show',true);
        $act_id=$this->input->get('act_id',true);
        $row=$this->User_model->get_select_one('name,pid',array('id'=>$id),'v_location');

        $pid=$row['pid'];
        $row2=$this->User_model->get_select_one('name,pid',array('id'=>$pid),'v_location');
        $zid=$row2['pid'];
        $row3=$this->User_model->get_select_one('name,pid',array('id'=>$zid),'v_location');
        if(in_array($row2['name'],$this->east_country))
        {
            $range='4';
        }
        elseif($row['name']=='香港' OR $row['name']=='澳门' OR $row['name']=='台湾')
        {
            $range='2';
        }
        elseif($row2['name']=='日本' OR $row2['name']=='韩国')
        {
            $range='3';
        }
        elseif($row3['name']=='欧洲' OR $row3['name']=='北美')
        {
            $range='5';
        }else
        {
            $range='6';
        }

        $table='v_activity_children';
        $data=array(
            'range_name'=>$row['name'],
            'is_show'=>$is_show,
            'range'=>$range
        );
        $where=array('act_id'=>$act_id);
        $this->User_model->update_one($where,$data,$table);
        redirect(base_url("myshop/my_mall_list?user_id={$user_id}"));
    }

    //app 内产品提交
    public function products_sub_app()
    {
        if(!$this->input->get('test'))
        {
            $user_id=$this->user_id_and_open_id();
            if(!$user_id){
                return FALSE;
            }
        }
        else
        {
            $user_id=$_SESSION['user_id']=1;
        }
        $act_id=$this->input->get('act_id',true);
        //活动上下架
        $up_down_act=$this->input->post('up_down_act',true);
        //商品上下架
        if($this->input->post('top')){
            $disorder=1;
        }else{
            $disorder=99;
        }
        if($up_down_act==0)
        {
            $act_is_show='2';
            $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_activity_children');
        }
        else
        {
            $act_is_show='1';
        }
        if($disorder==1)
        {
            $this->User_model->update_one(array('act_id'=>$act_id),array('displayorder'=>'1'),$table='v_activity_children');
            $where="user_id=$user_id AND special='2'";
            $this->User_model->update_one($where,array('displayorder'=>'99'),$table='v_activity_children');
        }

        $title=trim($this->input->post('title',true));
        $content=trim($this->input->post('content',true));



        $day=$this->input->post('day',true);
        if($user_id==1077){
            //echo '<pre>';print_r($day);exit();
        }
        $new_day=$day;
        foreach($day as $k=>$v){
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $day=json_encode($new_day);
        $add_time=time();

        $data=array(
            'title'=>$title,
            'user_id'=>$user_id,
            'day_list'=>$day,
            'content_text'=>$content,
            'displayorder'=>$disorder,
            'is_show'=>$act_is_show,
            'add_time'=>$add_time,
        );
        if($_FILES['banner']['error']==0)
        {
            $banner_image_ori=$this->upload_image('banner',$user_id.'banner');
            $banner_image= $this->imagecropper($banner_image_ori,'banner','time',$width='700',$height='300');
            $banner_product= $this->imagecropper($banner_image_ori,'banner_product','time',$width='100',$height='100');
            $banner_hot= $this->imagecropper($banner_image_ori,'banner_hot','time',$width='341',$height='240');
            $data['banner_image']=$banner_image;
            $data['banner_product']=$banner_product;
            $data['banner_hot']=$banner_hot;
        }


        $goods_number=$this->input->post('goods_number',true);
        $shop_price=$this->input->post('shop_price',true);
        $low=$this->input->post('low',true);


        $dateto=$this->input->post('dateto',true);
        $pricehas=$this->input->post('pricehas',true);
        $priceno=$this->input->post('priceno',true);
        $pricecom=$this->input->post('pricecom',true);
        if($low!=1){
            $low=0;
        }
        //echo '<pre>';print_r($data);exit();
        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
        $data=array(
            'goods_name'=>$title,
            'goods_number'=>$goods_number,
            'shop_price'=>$shop_price,
            'add_time'=>$add_time,
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
        );

        if( intval($goods_number)>0 && floatval($shop_price)>0)
        {
            $this->User_model->update_one(array('act_id'=>$act_id),$data,'v_goods');
        }

        redirect(base_url("myshop/my_mall_list?user_id=$user_id"));

    }



    //app 商铺增加
    public function app_shop_add()
    {
        $data['back_url']='olook://identify.toapp';
        $user_id=$this->user_id_and_open_id();

        if($user_id)
        {
            $rs=$this->User_model->get_select_one('business_id',array('user_id'=>$user_id),'v_auth_business');
            if($rs=='0')
            {
                //redirect($data['back_url']);
                $this->app_show_info('商户认证信息不全',$url=$data['back_url']);
            }
            else
            {
                $business_info=$this->User_model->get_select_one('business_id,is_show',array('user_id'=>$user_id),'v_wx_business');
                if($business_info!=0)
                {
                    if($business_info['is_show']==2)
                    {
                        $this->app_show_info('店铺审核中',$url=$data['back_url']);

                    }else{
                        redirect("/myshop/app_shop_edit");
                    }
                }else{
                    $this->load->view('myshop/add_shop',$data);
                }

            }


        }else{
            redirect($data['back_url']);
        }
        //print_r($_SESSION);

    }

    //app 审核提示页面
    public function app_show_info($content='审核中',$url)
    {
        $data['content']=$content;
        $data['url']=$url;
        $this->load->view('myshop/app_show_info',$data);
        header("Refresh:3;url=$url");

    }

    //app 商铺增加
    public function app_shop_edit()
    {
        $user_id=$this->user_id_and_open_id();
        //$user_id=1077;
        $data=$this->User_model->get_select_one('*,logo_image_thumb as image',array('user_id'=>$user_id),'v_wx_business');
        $data['back_url']='olook://identify.toapp';

        $this->load->view('myshop/add_shop',$data);
    }



    public function shop_insert()
    {
        $user_id=$this->user_id_and_open_id();

        if($user_id===false){
            return FALSE;
        }
        if($user_id==1077)
        {
            // echo '<pre>';print_r($_POST);exit();
        }
        $data['user_id']=$user_id;
        $data['business_name']=trim($this->input->post('business_name',true));
        $data['star_num']=trim($this->input->post('star_num',true));
        $tag=trim($this->input->post('tag',true));
        $data['tag']=str_replace('，',',',$tag);


        if(isset($_SESSION['location']) AND $_SESSION['location']!='no,no')
        {
            $location=$_SESSION['location'];
        }
        else
        {
            $location="31.41,121.48";
        }

        $location= explode(',',$location);
        $data['lng']=$location[1];
        $data['lat']=$location[0];
        $arr_country=$this->common->get_city_country($data['lat'],$data['lng'],$this->config->item('baidu_map_url'),$this->config->item('baidu_key'));
        //echo $arr_country[0];echo '<br>';
        if($arr_country[0]=='中国' OR $arr_country[0]=='China')
        {
            $data['currency_name']='人民币';
            $data['currency']='CNY';
        }
        elseif($arr_country[0]=='泰国' OR $arr_country[0]=='Thailand')
        {
            $data['currency_name']='泰铢';
            $data['currency']='THB';
        }else{
            $data['currency_name']='美元';
            $data['currency']='USD';
        }
        $data['address']=$arr_country[1];
        $data['type']=trim($this->input->post('type',true));
        $data['is_show']='2';
        // $data['discount']=$this->input->post('discount',true);

        $data['discount']=trim($this->input->post('discount',true));
        if($data['discount']>10 OR $data['discount']<=0){
            $data['discount']=10;
        }
        $data['business_info']=$this->input->post('business_info',true);
        $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

        $data['business_address']=$this->input->post('business_address',true);
        $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

        $data['business_tel']=$this->input->post('business_tel',true);

        $logo_image=$this->upload_image('file1', $data['user_id']);

        $data['logo_image']=$logo_image;
        // thumb($url,$key1,$key2='time',$width='702',$height='300')

        $data['logo_image_thumb']=$logo_image=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
        // echo '<pre>';print_r($data);exit();
        $rs=$this->User_model->get_select_one('business_id',array('user_id'=>$user_id),'v_wx_business');
        if($rs==0)
        {
            $business_id= $this->User_model->user_insert($table='v_wx_business',$data);
            $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>$business_id),'v_users');


        }else{
            $this->User_model->update_one(array('business_id'=>$rs['business_id']),$data,'v_wx_business');
            $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>$rs['business_id']),'v_users');
        }

        $data['back_url']='olook://identify.toapp';
        redirect($data['back_url']);
    }
    //二维码图片生成  wx
    public function show_qrcode($business_id=1)
    {

        $this->load->library('common');
        $url=base_url("bussell/bussinfo?business_id=$business_id");
        $this->common->get_qrcode($url);

    }

    //二维码图片生成  app
    public function show_qrcode_for_app($business_id=1)
    {
        //http://api.etjourney.com/bussell/business_info_app?business_id=28914
        $this->load->library('common');
        $url=base_url("bussell/business_info_app?business_id=$business_id");
        $this->common->get_qrcode($url);

    }


    public function shop_sub()
    {
        $user_id=$this->user_id_and_open_id();
        if($user_id===false){
            return FALSE;
        }
        $business_id=$this->input->post('business_id',true);
        //  $data['business_name']=trim($this->input->post('business_name',true));
        // $data['star_num']=trim($this->input->post('star_num',true));
        $tag=trim($this->input->post('tag',true));
        $data['tag']=str_replace('，',',',$tag);


        if(isset($_SESSION['location']) AND $_SESSION['location']!='no,no')
        {
            $location=$_SESSION['location'];
        }
        else
        {
            $location="31.41,121.48";
        }

        $location= explode(',',$location);
        $data['lng']=$location[1];
        $data['lat']=$location[0];
        $arr_country=$this->common->get_city_country($data['lat'],$data['lng'],$this->config->item('baidu_map_url'),$this->config->item('baidu_key'));
        //echo $arr_country[0];echo '<br>';
        if($arr_country[0]=='中国')
        {
            $data['currency_name']='人民币';
            $data['currency']='CNY';
        }elseif($arr_country[0]=='泰国')
        {
            $data['currency_name']='泰铢';
            $data['currency']='THB';
        }else{
            $data['currency_name']='美元';
            $data['currency']='USD';
        }
        $data['address']=$arr_country[1];
        $data['type']=trim($this->input->post('type',true));
        $data['is_show']='2';
        // $data['discount']=$this->input->post('discount',true);

        $data['discount']=trim($this->input->post('discount',true));
        if($data['discount']>10 OR $data['discount']<=0){
            $data['discount']=10;
        }
        $data['business_info']=$this->input->post('business_info',true);
        $data['business_info'] = str_replace("\n","<br>", $data['business_info']);

        $data['business_address']=$this->input->post('business_address',true);
        $data['business_address'] = str_replace("\n","<br>", $data['business_address']);

        $data['business_tel']=$this->input->post('business_tel',true);


        if($_FILES['file1']['error']==0)
        {
            $logo_image=$this->upload_image('file1', $data['user_id']);
            $data['logo_image']=$logo_image;
            $data['logo_image_thumb']=$this->imagecropper($logo_image,'logo','time',$width='100',$height='100');
        }


        $this->User_model->update_one(array('business_id'=>$business_id),$data,$table='v_wx_business');
        $this->User_model->update_one(array('user_id'=>$user_id),array('for_business_id'=>$business_id),'v_users');
        redirect("myshop/my_mall_list?user_id=$user_id");
    }

    //个人特价细节
    public function personal_products_detail($act_id,$page=1)
    {
        $this->get_crop_for_video();

        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false)
        {
            $et=FALSE;
        }
        else
        {
            $et=TRUE;
        }
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        if($act_id==0)
        {
            return false;
        }
        $data['menu']='0';
        $data['per_user_id']=0;
        $data['down']="#";
        $data['app_session']=session_id();

        if(isset($_COOKIE['user_id']))
        {
            $_SESSION['user_id']=$data['per_user_id']=$_COOKIE['user_id'];
        }
        elseif(isset($_COOKIE['olook']))
        {
            $arr_olook=explode('-',$_COOKIE['olook']);
            $data['menu']=$arr_olook[3];
            $_SESSION['user_id']=$data['per_user_id']=$arr_olook[0];
        }
        elseif(isset($_SESSION['user_id']))
        {
            $data['per_user_id']=$_SESSION['user_id'];
        }
        if($data['user_id']=1077){
            // redirect(base_url("bussell/business_info_app?business_id=5"));
        }
        //echo '<pre>';print_r($_SESSION);print_r($_COOKIE);exit();
        $share_user_id=$this->input->get('share_user_id',true);
        if(!$share_user_id){
            $share_user_id=$data['per_user_id'];
        }

        if(isset($_COOKIE['menu'])){
            $data['menu']=$_COOKIE['menu'];
            unset($_COOKIE['menu']);
        }
        if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no')
        {
            $location=$_COOKIE['location'];
            $location=explode(',',$location);
            $w=$location[0];$j=$location[1];
            $arr_city_country=$this->get_city_country($w,$j);
            unset($_COOKIE['location']);
        }
        else
        {
            $arr_city_country=0;
        }

        if(isset($_SESSION['pra']))
        {
            $pra=array_unique($_SESSION['pra']);
            $pra=array_values($pra);
            //组成点赞过的视频id json数据 便于前台js遍历
            $data['pra']=json_encode($pra);
        }
        $count=$this->User_model->get_act_video_count("act_shop_id=$act_id  AND is_off<2 ", 'v_video');
        $data['count']= $count['count'];
        $page_num =10;
        $data['now_page'] = $page;
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page']) {$page=1;}
        $start = ($page-1)*$page_num;
        $select="act_id,user_id,users,pid,title,act_image,start_time,end_time,content,content_text,banner_image as poster_image,banner_image,request";
        $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');

        //$pid=$data['activity']['pid'];
        //$zid=$this->User_model->get_select_one('pid',"act_id=$pid",'v_activity_father');
        //$data['zid']=$zid['pid'];
        if(mb_strlen($data['activity']['title'])>11){
            $data['activity']['title']=mb_substr($data['activity']['title'],0,11).'……';
        }
        $sharetitle= $data['activity']['title'];
        $data['act_id']=$act_id;
        $data['activity']['act_id']='p'.$act_id;
        $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
    v_video.imageforh5 as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,
    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
        $left_title='v_video.user_id=v_users.user_id';
        $left_table='v_users';
        $where="act_shop_id='$act_id'  AND is_off<2 ";

        $data['list']=$this->User_model->get_act_video_all($select,$where,'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start,$page_num);
        if(!empty($data['list']))
        {
            foreach($data['list'] as $k => $v){
                if($v['is_off']==1)
                {
                    if($v['push_type']==0)
                    {
                        $data['list'][$k]['path']= $this->config->item('record_url').$v['video_name'].'.m3u8';
                    }
                    else
                    {
                        $data['list'][$k]['path']= $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
                    }

                    if($et===FALSE)
                    {
                        $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
                    }
                    $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
                    $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
                }
                else
                {
                    if($et===FALSE)
                    {
                        $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
                    }
                    $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
                }
                $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
            }
        }
        if(!$data['list'])
        {
            $data['list']=0;
        }
        $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price,low,dateto,pricehas,priceno,pricecom',
            "act_id=$act_id AND is_show='1'",'v_goods');

        if(is_array($data['goods']))
        {
            $goods_id=$data['goods']['goods_id'];
        }
        else
        {
            $goods_id=-1;
        }
        if($data['goods']['low']==1)
        {
            $data['goods']['shop_price'].='起';
        }
        $where="goods_id=$goods_id AND order_status > '0'";
        $data['goods_buy']=$this->User_model->get_order_count($where);
        $data['goods_buy']=$data['goods_buy']['count']+$goods_id%7;
        $data['goods_buy']=$data['goods_buy']['count'];

        //$act_rs=$this->User_model->get_select_one('user_id,users,day_list,pid',"act_id=$act_id",'v_activity_children');
        // $pid=$act_rs['pid'];
        // $act_rs_father=$this->User_model->get_select_one('user_id,users,pid',"act_id=$pid",'v_activity_father');
        $arr_users=explode(',', $data['activity']['users']);
        $data['day']=json_decode( $data['activity']['day_list'],true);
        foreach($data['day'] as $k=>$v)
        {
            if($v=='')
            {
                unset($data['day'][$k]);
            }
        }

        if( $data['activity']['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users))
        {
            $data['act_add']=TRUE;
        }
        else
        {
            $data['act_add']=FALSE;
        }
// OR $this->input->get('admin')
        if( $data['activity']['user_id']==$data['per_user_id'])
        {
            $data['edit']=TRUE;
        }
        else
        {
            $data['edit']=FALSE;
        }
        if($data['per_user_id']==0)
        {
            $data['edit']=FALSE;$data['act_add']=FALSE;
        }
        if($arr_city_country>0)
        {
            $data['liver_info']['country']=$arr_city_country['0'];
            $data['liver_info']['city']=$arr_city_country['1'];
            $data['liver_info']['act_id']='0';
            $data['liver_info']['act_shop_id']=$act_id;
            $data['liver_info']=json_encode($data['liver_info']);
        }
        else
        {
            $data['liver_info']='0';
        }
        $data['share']['share_url']=base_url("myshop/personal_products_detail/$act_id?share_user_id={$share_user_id}");
        $data['share']['title']=$data['activity']['title'];
        $data['share']['image']=$data['activity']['banner_image'];
        $data['share']['desc']="坐享其成上的一个精彩活动{{$sharetitle}}快来一起High。";
        $data['json_share']=json_encode($data['share']);
        $type=$this->get_lan_user();
        if(in_array($type,$this->long_css)){
            $data['type']='en';
        }
        $this->new_lan_byweb();
        if($this->input->get('test')){
            echo"<pre>";print_r($data);
            $this->output->enable_profiler(TRUE);
        }
        if($et===TRUE)
        {
            if($this->input->get('menu',true))
            {
                $data['menu']='1';
            }
            else
            {
                $data['menu']='menu';
            }
            if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone'))
            {
                //if($data['per_user_id']==1077){ echo '<pre>';var_dump($data);exit();}

                $this->load->view('bussell/bus_children_detail_ios',$data);
            }
            else
            {
                $this->load->view('bussell/bus_children_detail_an',$data);
            }
        }
        else
        {
            $data['edit']=FALSE;

            $data['link_url']=TRUE;
            if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
            {
                $data['thdown']=$this->down;
                $this->load->view('bussell/bus_children_detail_demo',$data);
            }
            else
            {
                $data['thdown']=$this->down;
                $data['buy_url']=base_url("bussell/order_add_web?act_id=$act_id&share_user_id=$share_user_id");
                $this->load->view('bussell/bus_children_detail_wx',$data);
            }

        }
    }


////特价商品细节
//    public function products_detail($page=1)
//    {
//        $this->get_crop_for_video();
//        $share_id=$this->input->get('share_user_id',true);
//        if(!$share_id){
//            $share_id='0';
//        }
//        $act_id=$this->input->get('act_id',true);
//        if(!$act_id)
//        {
//            return false;
//        }
//        $_SESSION['call_page']=$this->input->get('call_page',true);
//        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===false)
//        {
//            $data['et']=$et=FALSE;
//            // $data['et']=$et=TRUE;
//        }
//        else
//        {
//            $data['et']=$et=TRUE;
//        }
//        $data['call_url']=$this->input->get('call_url',true);
//        //echo $data['call_url'];exit();
//        if(!$data['call_url'])
//        {
//            if($et===TRUE){
//                $data['call_url']='olook://identify.toapp>menu';
//            }else{
//                $data['call_url']=base_url("myshop/products_list?share_user_id=$share_id");
//            }
//
//        }
//        $data['count_url']=$this->count_url;
//        $data['down']=$this->down;
//        $data['menu']='0';
//        $data['per_user_id']=0;
//        $data['down']="#";
//        $data['app_session']=session_id();
//        $data['is_fav']=FALSE;
//
//
//        if(isset($_COOKIE['user_id']))
//        {
//            $_SESSION['user_id']=$data['per_user_id']=$_COOKIE['user_id'];
//        }
//        elseif(isset($_COOKIE['olook']))
//        {
//            $arr_olook=explode('-',$_COOKIE['olook']);
//            $data['menu']=$arr_olook[3];
//            $_SESSION['user_id']=$data['per_user_id']=$arr_olook[0];
//        }
//        elseif(isset($_SESSION['user_id']))
//        {
//            $data['per_user_id']=$_SESSION['user_id'];
//        }
//
//
//        //echo '<pre>';print_r($_SESSION);print_r($_COOKIE);exit();
//        if(isset($_COOKIE['menu']))
//        {
//            $data['menu']=$_COOKIE['menu'];
//            unset($_COOKIE['menu']);
//        }
//        if(isset($_COOKIE['location'])  && $_COOKIE['location']!='no,no')
//        {
//            $location=$_COOKIE['location'];
//            $location=explode(',',$location);
//            $w=$location[0];$j=$location[1];
//            $arr_city_country=$this->get_city_country($w,$j);
//            unset($_COOKIE['location']);
//        }
//        else
//        {
//            $arr_city_country=0;
//        }
//        if(isset($_SESSION['pra']))
//        {
//            $pra=array_unique($_SESSION['pra']);
//            $pra=array_values($pra);
//            //组成点赞过的视频id json数据 便于前台js遍历
//            $data['pra']=json_encode($pra);
//        }
//        $count=$this->User_model->get_act_video_count("act_shop_id=$act_id  AND is_off<2 ", 'v_video');
//        $data['count']= $count['count'];
//        $page_num =10;
//        $data['now_page'] = $page;
//        $data['max_page'] = ceil($count['count']/$page_num);
//        if($page>$data['max_page']) {$page=1;}
//        $start = ($page-1)*$page_num;
//        $select="act_id,pid,title,act_image,start_time,end_time,content,content_text,banner_image as poster_image,banner_image,request,range,range_name,type,special";
//        $data['activity']=$this->User_model->get_select_one($select,"act_id='$act_id'",'v_activity_children');
//        $range= $data['activity']['range'];
//        $data['show_info']=FALSE;
//        if($data['activity']['range_name']=='普吉岛')
//        {
//            $data['show_info']=TRUE;
//        }
//        $data['activity']['ori_title']=$data['activity']['title'];
//        if(mb_strlen($data['activity']['title'])>10){
//            $data['activity']['title']=mb_substr($data['activity']['title'],0,10).'...';
//        }
//        $data['more_act']=$this->User_model->get_select_all('v_activity_children.banner_image,v_activity_children.act_id,v_goods.shop_price,v_goods.low,v_goods.ori_price', "range=$range AND v_activity_children.act_id!=$act_id",
//            'act_id','ASC',$table='v_activity_children',$left=1,'v_goods',$left_title="v_goods.act_id=v_activity_children.act_id",$sum=false,1, 0,5);
//        if($data['more_act']!==false)
//        {
//            $data['more_act']=array();
//        }
//        $sharetitle= $data['activity']['title'];
//        $data['act_id']=$act_id;
//        $data['activity']['act_id']='p'.$act_id;
//        $select="v_video.video_id,v_video.address,v_video.video_name,v_video.push_type,
//    v_video.imageforh5 as image,v_video.praise as praise,title,v_video.user_id,v_users.image as avatar,v_video.all_address,
//    views,act_id,user_name,sex,pre_sign,start_time,v_users.image as user_image,is_off,v_video.location,v_video.socket_info";
//        $left_title='v_video.user_id=v_users.user_id';
//        $left_table='v_users';
//        $where="act_shop_id='$act_id'  AND is_off<2 ";
//
//        $data['list']=$this->User_model->get_act_video_all($select,$where,'start_time', 'DESC','v_video',1,$left_table,$left_title,false,1,$start,$page_num);
//        if(!empty($data['list']))
//        {
//            foreach($data['list'] as $k => $v)
//            {
//                if($v['is_off']==1)
//                {
//                    if($v['push_type']==0)
//                    {
//                        $data['list'][$k]['path']= $this->config->item('record_url').$v['video_name'].'.m3u8';
//                    }
//                    else
//                    {
//                        $data['list'][$k]['path']= $this->config->item('record_uc_url').$v['video_name'].'.m3u8';
//                    }
//
//                    if($et===FALSE)
//                    {
//                        $data['list'][$k]['lb_url'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//                    }
//                    $data['list'][$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
//                    $data['list'][$k]['video_dec'] =$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
//                }
//                else
//                {
//                    if($et===FALSE)
//                    {
//                        $data['list'][$k]['video_url'] = $this->config->item('base_url').'index.php/index/share?type=video&user_id='.$v['user_id'];
//                    }
//                    $data['list'][$k]['path']=$this->get_rtmp($v['video_name']);
//                }
//                $data['list'][$k]['videoinfo']=json_encode($data['list'][$k]);
//
//                $data['list'][$k]['app_lb']='olook://onvideo.toapp?guankan_lb&videoinfo<'. $data['list'][$k]['videoinfo'];
//                $data['list'][$k]['app_zb']='olook://onvideo.toapp?guankan_zb&videoinfo<'. $data['list'][$k]['videoinfo'];
//
//                if(stristr($_SERVER['HTTP_USER_AGENT'],'android') OR $this->input->get('from_home'))
//                {
//                    $data['list'][$k]['app_lb']='olook://videoinfo_lb<'. $data['list'][$k]['videoinfo'];
//                    $data['list'][$k]['app_zb']='olook://videoinfo_zb<'. $data['list'][$k]['videoinfo'];
//                }
//            }
//        }
//        if(!$data['list'])
//        {
//            $data['list']=0;
//        }
//
//        $data['goods']=$this->User_model->get_select_one('goods_id,goods_name,goods_number,shop_price,low,dateto,pricehas,priceno,pricecom,ori_price,oori_price',
//            "act_id=$act_id AND is_show='1'",'v_goods');
//
//        if(is_array($data['goods']))
//        {
//            $goods_id=$data['goods']['goods_id'];
//        }
//        else
//        {
//            $goods_id=-1;
//        }
//        if($data['goods']['shop_price']=='0.00')
//        {
//            $data['goods']['shop_price']=$data['goods']['ori_price'].'起';
//        }else{
//            $data['goods']['shop_price'].='起';
//        }
//
//        if($data['goods']['oori_price']=='0.00'){
//            $data['goods']['oori_price']=$data['goods']['ori_price']+100;
//            // $data['goods']['oori_price']=$data['goods']['ori_price']+100;
//        }
//        if($data['goods']['ori_price']!='0.00'){
//            $data['goods']['shop_price']=$data['goods']['ori_price'].'起';
//        }
//
//        $where="goods_id=$goods_id AND order_status > '0'";
//        $data['goods_buy']=$this->User_model->get_order_count($where);
//        $data['goods_buy']=$data['goods_buy']['count']+$goods_id%7;
//        //$data['goods_buy']=$data['goods_buy']['count'];
//
//        $act_rs=$this->User_model->get_select_one('user_id,users,day_list,pid',"act_id=$act_id",'v_activity_children');
//        //$pid=$act_rs['pid'];
//        // $act_rs_father=$this->User_model->get_select_one('user_id,users,pid',"act_id=$pid",'v_activity_father');
//        $arr_users=explode(',',$act_rs['users']);
//        $data['day']=json_decode($act_rs['day_list'],true);
//        foreach($data['day'] as $k=>$v)
//        {
//            if($v=='')
//            {
//                unset($data['day'][$k]);
//            }
//        }
//        if($act_rs['user_id']==$data['per_user_id'] OR in_array($data['per_user_id'],$arr_users))
//        {
//            $data['act_add']=TRUE;
//        }
//        else
//        {
//            $data['act_add']=FALSE;
//        }
//
//        if($act_rs['user_id']==$data['per_user_id'] )
//        {
//            $data['edit']=TRUE;
//        }
//        else
//        {
//            $data['edit']=FALSE;
//        }
//        if($data['per_user_id']==0)
//        {
//            $data['edit']=FALSE;$data['act_add']=FALSE;
//        }
//        if($arr_city_country>0)
//        {
//            $data['liver_info']['country']=$arr_city_country['0'];
//            $data['liver_info']['city']=$arr_city_country['1'];
//            $data['liver_info']['act_id']='0';
//            $data['liver_info']['act_shop_id']=$act_id;
//            $data['liver_info']=json_encode($data['liver_info']);
//        }
//        else
//        {
//            $data['liver_info']='0';
//        }
//
//        $data['share']['share_url']=base_url("myshop/products_detail?act_id=$act_id&share_user_id=$data[per_user_id]");
//        $data['share']['title']=$data['activity']['title'];
//        $data['share']['image']=$data['activity']['banner_image'];
//        $data['share']['desc']="坐享其成上的特价产品{{$sharetitle}}欢迎选购。";
//
//        $data['json_share']=json_encode($data['share']);
//        if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
//        {
//            $data['share_out']="olook://shareinfo<".$data['json_share'];
//        }
//        else
//        {
//            $data['share_out']="olook://toshare.toapp>toshare&shareinfo<".$data['json_share'];
//        }
//
//        $type=$this->get_lan_user();
//        if(in_array($type,$this->long_css))
//        {
//            $data['type']='en';
//        }
//        $this->new_lan_byweb();
//        $where="act_id=$act_id AND user_id=$data[per_user_id] AND type='1'";
//        $fav=$this->User_model->get_select_one('act_id',$where,'v_favorite');
//
//        if($fav!==0)
//        {
//            $data['is_fav']=TRUE;
//        }
//
//        if($et===TRUE)
//        {
//            if($this->input->get('menu',true))
//            {
//                $data['menu']='1';
//            }
//            else
//            {
//                $data['menu']='menu';
//            }
//            if($data['activity']['type']==0)
//            {
//                $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;
//            }
//            elseif($data['activity']['type']==1)
//            {
//                $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;
//            }
//
//            if($data['activity']['special']==3)
//            {
//                $data['buy_url']=base_url('bussell/order_add_app?act_id=').$act_id.'&share_user_id='.$share_id;
//                //$data['buy_url']=base_url("bussell/order_add_web?act_id=$act_id&share_user_id=$share_user_id");
//
//            }
//
//        }
//        else
//        {
//            $data['edit']=FALSE;
//            $data['link_url']=TRUE;
//            if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false)
//            {
//                $data['buy_url']=$this->down;
//            }
//            else
//            {
//                $data['no_title']=TRUE;
//                $data['thdown']=$this->down;
//                if($data['activity']['type']==0)
//                {
//                    $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;
//                }
//                elseif($data['activity']['type']==1)
//                {
//                    $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;
//                }
//
//                if($data['activity']['special']==3)
//                {
//                    //$data['buy_url']=base_url('bussell/order_add_app?act_id=').$act_id.'&share_user_id='.$share_id;
//                    $data['buy_url']=base_url("bussell/order_add_web?act_id=$act_id&share_user_id=$share_id");
//
//                }
//            }
//        }
//        if($this->input->get('detail'))
//        {
//            echo '<pre>';print_r($data);exit();
//        }
//        if($data['per_user_id']==4013){
//            // echo '<pre>';print_r($data);exit();
//        }
//        $this->load->view('myshop/products_detail',$data);
//    }


    //加入收藏
    public function put_favorite()
    {
        $data['user_id']=$this->user_id_and_open_id();
        //print_r($_SESSION);exit();
        if(!$data['user_id'])
        {
            return false;
        }
        else
        {

            $data['addtime']=time();

            $data['act_id']=$this->input->post('act_id',true);
            $data['shop_id']=$this->input->post('shop_id',true);
            $data['ts_id']=$this->input->post('ts_id',true);
            //echo t['act_id'];exit();

            if($data['act_id'])
            {
                $rs=$this->User_model->get_select_one('act_id',array('user_id'=>$data['user_id'],'type'=>'1','act_id'=> $data['act_id']),'v_favorite');
                if($rs==0)
                {
                    $data_arr=array(
                        'user_id'=>$data['user_id'],
                        'addtime'=>$data['addtime'],
                        'act_id'=>$data['act_id'],
                        'type'=>'1'
                    );
                    $this->User_model->user_insert($table='v_favorite',$data_arr);


                }
            }

            if( $data['shop_id'])
            {
                $rs=$this->User_model->get_select_one('shop_id',array('user_id'=>$data['user_id'],'type'=>'2','shop_id'=> $data['shop_id']),'v_favorite');
                if($rs==0)
                {
                    $data_arr=array(
                        'user_id'=>$data['user_id'],
                        'addtime'=>$data['addtime'],
                        'shop_id'=>$data['shop_id'],
                        'type'=>'2'
                    );
                    $this->User_model->user_insert($table='v_favorite',$data_arr);


                }
            }


            if( $data['ts_id'])
            {
                $rs=$this->User_model->get_select_one('ts_id',array('user_id'=>$data['user_id'],'type'=>'2','ts_id'=> $data['ts_id']),'v_favorite');
                if($rs==0)
                {
                    $data_arr=array(
                        'user_id'=>$data['user_id'],
                        'addtime'=>$data['addtime'],
                        'ts_id'=>$data['ts_id'],
                        'type'=>'3'
                    );
                    $this->User_model->user_insert($table='v_favorite',$data_arr);


                }
            }



        }
        echo 1;
    }
    //取消收藏
    public function out_favorite()
    {
        $user_id=$this->user_id_and_open_id();
        if(!$user_id){
            return false;
        }
        $act_id=$this->input->post('act_id',true);
        $shop_id=$this->input->post('shop_id',true);
        $ts_id=$this->input->post('ts_id',true);

        if($act_id)
        {
            $where=array('user_id'=>$user_id,'act_id'=>$act_id);

        }
        elseif($shop_id)
        {
            $where=array('user_id'=>$user_id,'shop_id'=>$shop_id);
        }
        elseif($ts_id)
        {
            $where=array('user_id'=>$user_id,'ts_id'=>$ts_id);
        }
        else
        {
            return false;
        }

        $this->User_model->update_one($where,array('type'=>'4'),$table='v_favorite');
        echo 1;
    }


// 首页热门产品
    public function hot_products()
    {

        //redirect("http://api.etjourney.com/index.php/index/share?type=video&user_id=2025");
        if($_SESSION['user_id']==1077)
        {

        }
        $data['xtype']=$this->input->get_post('xtype',true);
        $data['ajax']=$this->input->get_post('ajax',true);
        if(! $data['xtype']){
            $data['xtype']=0;
            $data['xtype_name']='热门产品';
        }



        $data['order']=$this->input->get_post('order',true);


        //  $data['range']=$this->input->get_post('range',true);
        if(!$data['order'])
        {
            $data['order']=2;
        }

        $page=$this->input->post('page',true);
        // echo $page;
        $data['share_user_id']=$this->input->get('share_user_id',true);
        $data['user_id']=$this->user_id_and_open_id();
        if(! $data['share_user_id']){
            $data['share_user_id']= $data['user_id'];
        }

        if(isset($_SESSION['call_page'])){
            $data['call_page']=$_SESSION['call_page'];
        }else{
            $data['call_page']=1;
        }
        unset($_SESSION['call_page']);
        //$data['discount_type_name']=array('自由行','跟团游','酒店','美食','机票');
        //$data['range_name']=array('港澳台','日韩','东南亚','欧美','其他');

        if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $data['share_noshow']=FALSE;

        }else{
            $data['share_noshow']=TRUE;
        }
        if(!$page)
        {
            $page=1;
        }
        $where="v_activity_children.special='5' AND v_activity_children.is_show='1' AND v_activity_children.act_status='2' AND v_goods.is_show = '1' AND v_wx_business.is_show ='1' ";
        $where="v_activity_children.special IN ('2','4','5','6') AND v_activity_children.is_show='1'  AND v_goods.is_show = '1'";

        $page_num =9;
        $call_page_num=$page_num*$data['call_page'];
        // echo $call_page_num;
        $data['now_page'] = $page;
        $count = $this->User_model->get_special_count($where);
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;
        // echo $page;
        $select="v_activity_children.star_num,v_activity_children.act_id,v_wx_business.business_id,
        v_activity_children.title,v_activity_children.xtype,v_activity_children.order_sell AS goods_buy,
        v_activity_children.banner_product ,
        v_activity_children.banner_hot,
        v_activity_children.banner_image,
        v_activity_children.tag,v_goods.goods_id,v_goods.low,v_goods.shop_price,
        v_goods.ori_price,v_goods.oori_price,1 AS distance ";


        $data['back_url']='olook://identify.toapp>menu';

        if($page==1)
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            // echo $this->db->last_query();
            foreach($data['products'] as $k=>$v)
            {

                //$data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                //  $data['products'][$k]['goods_buy']=$data['products'][$k]['goods_buy']+$v['goods_id']%7;
                if(mb_strlen($v['title'])>11){
                    $data['products'][$k]['title']=mb_substr($v['title'],0,11).'……';
                }
                if (stristr($v['banner_product'], 'http') === false)
                {
                    $data['products'][$k]['banner_product'] = $this->config->item('base_url') . ltrim($v['banner_product'], '.');
                }
                if (stristr($v['banner_hot'], 'http') === false)
                {
                    $data['products'][$k]['banner_hot'] = $this->config->item('base_url') . ltrim($v['banner_hot'], '.');
                }
                $tag_arr=explode(',',$v['tag']);
                $str='';
                foreach($tag_arr as $k1=>$v1)
                {
                    $str.=$v1;
                    $data['products'][$k]['tag_arr'][]=$v1;
                    if(mb_strlen($str)>14){
                        break;
                    }
                }

                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,mb_strlen($data['products'][$k]['tag'])-1);
                    }
                }

            }

            $data['share']['share_url']=base_url("myshop/hot_products?share_user_id=$data[share_user_id]&range=$data[range]&xtype=$data[xtype]");
            $data['share']['title']=$data['range'].'当地项目';
            $data['share']['image']='http://api.etjourney.com/public/newadmin/images/logo.png';
            $data['share']['desc']="当地项目。";
            $data['json_share']=json_encode($data['share']);
            $data['share_url']='olook://shareinfo<'.$data['json_share'];
            if(!$data['ajax'])
            {
                if($count['count']>=4)
                {
                    for($i=1;$i<=4;$i++)
                    {
                        $data['hot'][]=array_shift($data['products']);
                    }
                }
                else
                {
                    $data['hot']=$data['products'];
                    $data['products']=array();
                }

            }


            if( 1==2)
            {
                echo '<pre>';var_dump($data['share_noshow']);print_r($_SERVER['HTTP_USER_AGENT']);print_r($data);exit();


            }else{
                // $data['call_url']=base64_encode(base_url("myshop/local_products?lat=$lat&lng=$lng&xtype=$data[xtype]&order=$data[order]&range=$data[range]"));
                $data['call_url']=base_url("myshop/hot_products?range=$data[range]");
                if(stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger'))
                {
                    $url=base_url("myshop/hot_products?share_user_id={$data['share_user_id']}");
                    $user_id=$data['user_id']=$this->get_wx_userid($url);
                    if($user_id==1){
                        //echo '<pre>';print_r($data);
                    }
                    $data['call_url']="javascript:history.go(-1)";
                }
                // $data['call_url']=base_url("myshop/products_list");


                if($data['ajax'])
                {
                    echo json_encode($data['products']);
                }
                else
                {
                    $this->load->view('myshop/hot_products_view',$data);
                }
            }

        }
        else
        {
            $data['products']=$this->User_model->get_special($select,$where,$start,$call_page_num,$data['order']);
            foreach($data['products'] as $k=>$v)
            {

                if(stristr($v['banner_product'], 'http')===false)
                {
                    $data['products'][$k]['banner_product'] = $this->config->item('base_url'). ltrim($v['banner_product'],'.');
                }
                if(stristr($v['banner_image'], 'http')===false)
                {
                    $data['products'][$k]['banner_image'] = $this->config->item('base_url'). ltrim($v['banner_image'],'.');
                }
                if(mb_strlen($v['tag'])>12)
                {
                    $data['products'][$k]['tag']=mb_substr($v['tag'],0,12);
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,11);
                    }

                }else{
                    if(mb_substr($data['products'][$k]['tag'],-1)==',')
                    {
                        $data['products'][$k]['tag']=mb_substr($v['tag'],0,mb_strlen($data['products'][$k]['tag'])-1);
                    }
                }

            }
            /* for($i=1;$i<=10;$i++){
               $data['products'][]=$data['products'][0];
             }*/
            echo json_encode($data['products']);
        }



    }





    public function get_crop_for_video()
    {
        set_time_limit(0);
        $data=$this->User_model->get_select_all($select='video_id',
            $where=" (imageforh5 IS NULL OR imageforh5 ='') AND is_off=1  ",$order_title='start_time',$order='ASC',$table='v_video');
        if($this->input->get('test')){
            echo $this->db->last_query();
            var_dump($data) ;
        }

        if($data!==false)
        {
            foreach($data as $k=>$v)
            {
                $url="./uploads/".$v['video_id'].".jpg";
                $new_imag=$this->crop_for_video($url,$v['video_id']);
                $dataimage=array('imageforh5'=>$new_imag);
                $this->User_model->update_one(array('video_id'=>$v['video_id']),$dataimage,$table='v_video');
            }
        }

    }


    public function get_lan_user()
    {
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        return $lang;
    }


    public function new_lan_byweb()
    {
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        switch ($lang)
        {
            case 'zh-cn' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-CN' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-tw' :
                $this->lang->load('ft', 'english');
                break;
            case 'zh-TW' :
                $this->lang->load('ft', 'english');
                break;
            case 'ja-jp' :
                $this->lang->load('jp', 'english');
                break;
            case 'ja-JP' :
                $this->lang->load('jp', 'english');
                break;
            case 'ko-kr' :
                $this->lang->load('hy', 'english');
                break;
            case 'ko-KR' :
                $this->lang->load('hy', 'english');
                break;
            default:
                $this->lang->load('eng', 'english');
                break;
        }

    }


    public function get_rtmp($video_name)
    {
        $result = '';
        $flg=$this->config->item('rtmp_flg');
        if($video_name)
        {
            if(stristr($video_name,'rtmp://'))
            {
                $result = $video_name;
            }
            else
            {
                if($flg== 0)
                {
                    $result = 'rtmp://42.121.193.231/hls/'.$video_name;
                }
                elseif($flg== 1)
                {
                    $auth_key = $this->get_auth($video_name);
                    $result = 'rtmp://video.etjourney.com/etjourney/'.$video_name.'?auth_key='.$auth_key;
                }elseif($flg== 3){
                    $result = $this->config->item('rtmp_uc_url').$video_name;
                }
            }
        }
        return $result;
    }





    public function get_city_country($dimension,$longitude)
    {
        $position = $this->geocoder($dimension,$longitude);
        $position = json_decode($position,TRUE);
        if($position)
        {
            $country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
            $province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
            $city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
            if($position['status']==0 && empty($country))
            {
                return array('no','no');
            }
            else
            {
                return array($country,$city);
            }
        }
        else
        {
            return array('no','no');

        }
    }
    function geocoder($dimension, $longitude)
    {
        $url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
        $result = file_get_contents($url);
        $result = substr($result,29);
        $result = substr($result, 0, -1);
        if($this->input->get('test1'))
        {
            echo $result;
        }
        return $result;
    }




    public function crop_for_video($source_path='./uploads/5311.jpg',$key2='time',$target_width='400', $target_height='400')
    {

        $source_info   = getimagesize($source_path);
        $source_width  = $source_info[0];
        $source_height = $source_info[1];
        $source_mime   = $source_info['mime'];
        $source_ratio  = $source_height / $source_width;
        $target_ratio  = $target_height / $target_width;

        // 源图过高
        if ($source_ratio > $target_ratio)
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        }
        // 源图过宽
        elseif ($source_ratio < $target_ratio)
        {
            $cropped_width  = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        }
        // 源图适中
        else
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }

        if($source_mime=='image/jpeg'){
            $source_image = imagecreatefromjpeg($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./vimagecrop/'.$key2.'.'.$type;
            imagejpeg($target_image,$new_image);


            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            return $new_image;
        }
    }



    //图像处理方法
    public function upload_image($filename,$fileurl,$key='time')
    {
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl))
            {
                return FALSE;
            }
        }

        return $this->shangchuan($filename,$fileurl,$key);
    }

    public function shangchuan($filename,$fileurl,$key='time')
    {
        $file = $_FILES[$filename];
        switch ($file['type'])
        {
            case 'image/jpeg':
                $br = '.jpg';break;
            case 'image/png':
                $br = '.png';break;
            case 'image/gif':
                $br = '.gif';break;
            default:
                $br = FALSE;break;
        }
        if($br)
        {
            if($key=='time'){
                $key =time();
            }

            $pic_url="./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            return $pic_url;
        }
    }
    public function imagecropper($source_path='./public/images/1265/id_driver.jpg',$key1='test',$key2='time',$target_width='100', $target_height='100')
    {
        $source_info   = getimagesize($source_path);
        $source_width  = $source_info[0];
        $source_height = $source_info[1];
        $source_mime   = $source_info['mime'];
        $source_ratio  = $source_height / $source_width;
        $target_ratio  = $target_height / $target_width;

        // 源图过高
        if ($source_ratio > $target_ratio)
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        }
        // 源图过宽
        elseif ($source_ratio < $target_ratio)
        {
            $cropped_width  = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        }
        // 源图适中
        else
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }

        if($source_mime=='image/jpeg'){
            $source_image = imagecreatefromjpeg($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagejpeg($target_image,$new_image);


            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            return $new_image;
        }elseif($source_mime=='image/png'){
            $source_image = imagecreatefrompng($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

            $alpha = imagecolorallocatealpha($target_image, 0, 0, 0, 127);
            imagefill($target_image, 0, 0, $alpha);
            $alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
            imagefill($cropped_image, 0, 0, $alpha);
            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagesavealpha($target_image, true);
            imagepng($target_image,$new_image);

            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            return $new_image;
        }else{
            $source_image = imagecreatefromgif($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagegif($target_image,$new_image);
            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            return $new_image;
        }
    }


    /*
     * 验证user_id 并获取
     */
    public function user_id_and_open_id()
    {

//      if(isset($_SESSION['user_id']))
//      {
//          return $_SESSION['user_id'];
//
//      }
//      else
        if(isset($_COOKIE['user_id']))
        {
            $user_id=$_COOKIE['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            if(isset($_COOKIE['openid']))
            {
                $str=$row['openid'];
                $str=strtoupper(md5('ET'.$str));
                if($str==$_COOKIE['openid'])
                {
                    $_SESSION['openid']=$_COOKIE['openid'];
                    $_SESSION['user_id']=  $user_id;
                    if(isset($_COOKIE['location']))
                    {
                        $_SESSION['location']=$_COOKIE['location'];
                    }
                    return $user_id;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }
        elseif(isset($_COOKIE['olook']))
        {
            $striso=$_COOKIE['olook'];
            $arrolook=explode('-',$striso);
            $user_id=$arrolook[0];
            $openid=$arrolook[1];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            $str=$row['openid'];
            $str=strtoupper(md5('ET'.$str));
            if($str==$openid)
            {
                $_SESSION['user_id']=$user_id;
                $_SESSION['openid']=$openid;
                $_SESSION['location']=$arrolook[2];
                return $user_id;
            }
            else
            {
                return 0;
            }
        }
        elseif(isset($_SESSION['user_id']))
        {
            return $_SESSION['user_id'];
        }
        else
        {
            return 0;
        }
    }

    public function get_wx_userid($url)
    {
        include_once("./application/third_party/wxpay/WxPay.php");
        $jsApi = new JsApi_pub();
        if(isset($_SESSION['wx_user_id']) AND isset($_SESSION['openidfromwx']))
        {
            return $_SESSION['wx_user_id'];
        }
        else
        {
            if (!isset($_GET['code']))
            {
                //base_url("bussell/order_add_fromwx?act_id={$act_id}")
                //触发微信返回code码
                $url = $jsApi->createOauthUrlForCode_all($url);
                Header("Location: $url");
            }
            else
            {
                //获取code码，以获取openid
                $code = $_GET['code'];
                $jsApi->setCode($code);
                //$openid = $jsApi->getOpenId();
                $wxuserinfo = $jsApi->wxuserinfo($code);
                //echo "<pre>";print_r($wxuserinfo);die;
                $openid=$wxuserinfo['openid'];
                $user_name=$wxuserinfo['nickname'];
                $sex=$wxuserinfo['sex'];
                if($sex==1)
                {
                    $sex_et='0';
                }
                elseif($sex==2)
                {
                    $sex_et='1';
                }
                else
                {
                    $sex_et='2';
                }
                $lan=$wxuserinfo['language'];
                $address=$wxuserinfo['city'];
                $image=$wxuserinfo['headimgurl'];
                $num=strripos($image,'/');
                //$numall=strlen($str);
                $image=substr($image,0,$num);
                $wxinfo=$this->User_model->get_select_one($select='openid,user_id',array('openid'=>$openid),'v_wx_users');
                if($wxinfo){
                    $_SESSION['openidfromwx']=$wxinfo['openid'];
                    $_SESSION['wx_user_id']=$wxinfo['user_id'];
                    return $wxinfo['user_id'];
                }else{
                    $datauser=array(
                        'openid'=>$openid,
                        'register_time'=>time(),
                        'regist_type'=>'7',
                        'user_name'=>$user_name,
                        'sex'=>$sex_et,
                        'lan'=>$lan,
                        'address'=>$address,
                        'image'=>$image.'/96'

                    );
                    $_SESSION['openidfromwx']=$openid;
                    $_SESSION['wx_user_id']=$this->User_model->user_insert($table='v_wx_users',$datauser);
                    return $_SESSION['wx_user_id'];
                }
            }
        }

    }


    public function tran_baidu($query, $from, $to)
    {
        // $query='hello';$from='en';$to='zh';
        $rs=$this->baidu_translate($query, $from, $to);
        //var_dump($rs);
        return $rs;
    }


    function baidu_translate($query, $from, $to)
    {
        $res = '';
        $args = array(
            'q' => $query,
            'appid' => $this->config->item('baidu_trans_id'),
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,

        );
        $args['sign'] = $this->buildSign($query, $this->config->item('baidu_trans_id'), $args['salt'], $this->config->item('baidu_trans_key'));
        $ret = $this->call($this->config->item('baidu_trans_url'), $args);
        $ret = json_decode($ret, true);
        if($ret)
        {
            if($ret['trans_result'])
            {
                $res = $ret['trans_result']['0']['dst'];
            }
        }
        return $res;
    }

//加密
    function buildSign($query, $appID, $salt, $secKey)
    {/*{{{*/
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }/*}}}*/

//发起网络请求
    function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array())
    {/*{{{*/
        $ret = false;
        $i = 0;
        while($ret === false)
        {
            if($i > 1)
                break;
            if($i > 0)
            {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }/*}}}*/

    function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
    {/*{{{*/
        $ch = curl_init();
        if($method == "post")
        {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        else
        {
            $data = $this->convert($args);
            if($data)
            {
                if(stripos($url, "?") > 0)
                {
                    $url .= "&$data";
                }
                else
                {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($withCookie)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }/*}}}*/

    function convert(&$args)
    {/*{{{*/
        $data = '';
        if (is_array($args))
        {
            foreach ($args as $key=>$val)
            {
                if (is_array($val))
                {
                    foreach ($val as $k=>$v)
                    {
                        $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                    }
                }
                else
                {
                    $data .="$key=".rawurlencode($val)."&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }/*}}}*/


    //活动须知公用头部
    public function show_head($title_up,$call_back,$title_down,$show_share=FALSE,$share_info=0)
    {
        if($this->et===TRUE OR $this->visitor==TRUE)
        {
            $data['show_head']=TRUE;
        }else{
            $data['show_head']=FALSE;
        }
        $data['title_up']=$title_up;
        $data['call_back']=$call_back;
        $data['title_down']=$title_down;
        $data['show_share']=$show_share;
        $data['share_out']=$share_info;
        $this->load->view('send/common_head',$data);
    }

    //商品细节
    public function products_detail_new($page=1)
    {
        // $this->et=true;

        // echo '<pre>';print_r($_SERVER['HTTP_USER_AGENT']);exit();
        $data['HTTP_USER_AGENT']=$_SERVER['HTTP_USER_AGENT'];

        $this->get_crop_for_video();
        $share_id=$this->input->get('share_user_id',true);
        if(!$share_id){
            $share_id='0';
        }
        $_SESSION['share_user_id']=$share_id;
        $act_id=$this->input->get('act_id',true);
        if(!$act_id)
        {
            return false;
        }
        $_SESSION['call_page']=$this->input->get('call_page',true);

        if($this->et===FALSE)
        {
            $data['et']=FALSE;
        }else
        {
            $data['et']=TRUE;
        }

        //var_dump($_SERVER['HTTP_USER_AGENT']);exit();

        if($this->input->get('menu',true))
        {
            if($this->et===TRUE OR $this->visitor===TRUE)
            {
                $data['call_url']='olook://identify.toapp';
            }

        }
        else
        {
            $data['call_url']="javascript:history.go(-1)";
        }

        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $data['menu']='0';
        $data['per_user_id']=0;
        $data['down']="#";
        $data['app_session']=session_id();
        $data['is_fav']=FALSE;
        if($this->et===TRUE)
        {
            if(isset($_COOKIE['user_id']))
            {
                $_SESSION['user_id']=$data['per_user_id']=$_COOKIE['user_id'];
            }
            elseif(isset($_COOKIE['olook']))
            {
                $arr_olook=explode('-',$_COOKIE['olook']);
                $data['menu']=$arr_olook[3];
                $_SESSION['user_id']=$data['per_user_id']=$arr_olook[0];
            }
            elseif(isset($_SESSION['user_id']))
            {
                $data['per_user_id']=$_SESSION['user_id'];
            }
        }



        if( $data['per_user_id']==1077 OR $data['per_user_id']==2025 OR $data['per_user_id']==1031)
        {
            //redirect("/products");
        }
        //echo '<pre>';print_r($_SESSION);print_r($_COOKIE);exit();
        if(isset($_COOKIE['menu']))
        {
            $data['menu']=$_COOKIE['menu'];
            unset($_COOKIE['menu']);
        }


        $select="v_activity_children.act_id,v_activity_children.title,v_activity_children.banner_image AS image,v_activity_children.range_name,v_activity_children.banner_product,v_activity_children.type,
        v_activity_children.star_num,v_activity_children.tag,v_activity_children.content,v_activity_children.content_text,v_activity_children.special,v_activity_children.business_id,v_activity_children.is_show,
        v_activity_children.order_sell,v_goods.attention_list,v_goods.ori_price,v_goods.oori_price,v_goods.shop_price";

        $data['products']=$this->User_model->get_one($select,"v_activity_children.act_id=$act_id AND v_goods.is_show='1'",'v_activity_children','v_goods',"act_id","act_id");
        if($data['products']['is_show']!='1')
        {
            echo '此商品已下架!';
            header("Refresh:2;url=$data[call_url]");
            exit();
        }
        if(stristr($data['products']['image'], 'http')===false){
            $data['products']['image'] = $this->config->item('base_url'). ltrim($data['products']['image'],'.');
        }
        if(stristr($data['products']['banner_product'], 'http')===false){
            $data['products']['banner_product'] = $this->config->item('base_url'). ltrim($data['products']['banner_product'],'.');
        }
        $data['show_info']=FALSE;
        if($data['products']['range_name']=='普吉岛')
        {
            $data['show_info']=TRUE;
        }
        $sharetitle=$data['products']['title'];
        $data['comment']=$this->User_model->get_select_all('v_users.user_id,v_users.user_name,v_users.sex,v_users.image,v_order_info.evaluate,v_order_info.confirm_time,v_order_info.star_num',"v_order_info.act_id=$act_id AND v_order_info.order_status ='3'",'v_order_info.order_id','ASC','v_order_info',1,'v_users',"v_users.user_id=v_order_info.user_id_buy");
        if($data['comment']==false)
        {
            $data['comment']=array();

            $data['comment_no']='暂无';
        }
        $star_arr=$this->User_model->get_select_all('star_num',"act_id=$act_id AND order_status='3'",$order_title='order_id',$order='ASC',$table='v_order_info');

        if($star_arr!==FALSE)
        {

            $star_num=0;
            foreach($star_arr as $k=>$v)
            {
                $star_num+=$v['star_num'];
            }

            $star_num=$star_num/count($star_arr);
            $data['products']['star_num']=round($star_num);
        }

        $range=$data['products']['range_name'];
        $special=$data['products']['special'];
        $data['rec_products']=$this->User_model->get_select_all($select,"v_activity_children.is_show='1'  AND v_goods.is_show='1' AND v_activity_children.act_id!=$act_id AND range_name='$range'  AND special='$special'",'act_id','ASC','v_activity_children',1,'v_goods',"v_goods.act_id=v_activity_children.act_id");
        if( $data['rec_products']==false){
            $data['rec_products']=array();
        }


        $tag_arr=explode(',',$data['products']['tag']);
        $str='';
        foreach($tag_arr as $k1=>$v1)
        {
            $str.=$v1;
            $data['tag_arr'][]=$v1;
            if(mb_strlen($str)>14){
                break;
            }
        }

        foreach($data['rec_products'] as $k=>$v)
        {
            $data['rec_products'][$k]['url']=base_url("myshop/products_detail_new?act_id=$v[act_id]&share_user_id=$data[per_user_id]");

            $tag_arr=explode(',',$v['tag']);
            $str='';
            foreach($tag_arr as $k1=>$v1)
            {
                $str.=$v1;
                $data['rec_products'][$k]['tag_arr'][]=$v1;
                if(mb_strlen($str)>14){
                    break;
                }
            }
        }
        $data['share']['share_url']=base_url("myshop/products_detail_new?act_id={$act_id}&share_user_id=$data[per_user_id]");
        $data['share']['title']=$sharetitle;
        $data['share']['image']=$data['products']['image'];
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']='olook://shareinfo<'.$data['json_share'];


        $where="act_id=$act_id AND user_id=$data[per_user_id] AND type='1'";
        $fav=$this->User_model->get_select_one('act_id',$where,'v_favorite');

        if($fav!==0)
        {
            $data['is_fav']=TRUE;
        }


        if($this->et===TRUE)
        {

            if($special==7 OR $special==3)
            {
                $data['buy_url']=base_url("bussell/order_add_app?act_id=$act_id&share_user_id=$share_id");
            }
            else
            {

                $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;

            }

        }
        else
        {
            $data['shop_url']="javascript:void(0)";
            $data['edit']=FALSE;
            $data['link_url']=TRUE;
            if($this->wx==false)
            {
                if( $this->visitor===TRUE)
                {
                    $data['buy_url']=$this->tologin;
                }else{
                    $data['buy_url']=$this->down;
                }


            }
            else
            {
                if($special==7 OR $special==3){
                    $data['buy_url']=base_url("bussell/order_add_fromwx?act_id=$act_id&share_user_id=$share_id");

                }else{

                    $data['buy_url']=base_url('bussell/trip_proudcts_screen_one?act_id=').$act_id.'&share_user_id='.$share_id;
                }
            }
        }
        if($this->input->get('detail'))
        {// var_dump($data['is_fav']);
            echo '<pre>';print_r($data);exit();

        }
        if($data['per_user_id']==2025){
            //echo '<pre>';print_r($data);exit();
        }
        $business_id=$data['products']['business_id'];
        $data['zx']=$this->User_model->get_select_one('business_tel',"business_id=$business_id ",'v_wx_business');
        $data['zx']='tel:13'.$data['zx']['business_tel'];
        $this->show_head($data['products']['title'], $data['call_url'],$data['products']['title'],$show_share=TRUE,$data['share_out']);
        $data['nobuy']=$this->input->get('nobuy',true);
        if($data['nobuy'])
        {
            $data['rec_products']=array();
        }
        $this->load->view('myshop/product_detail_new',$data);
    }
//ts pro
    public function trip_detail()
    {
        $this->get_crop_for_video();
        $share_id=$this->input->get('share_user_id',true);
        if(!$share_id){
            $share_id='0';
        }
        $ts_id=$this->input->get('ts_id',true);
        if(!$ts_id)
        {
            return false;
        }
        $_SESSION['call_page']=$this->input->get('call_page',true);

        $et= $data['et']=$this->et;
        $menu=$this->input->get('menu');
        if($menu)
        {
            if($et===TRUE OR $this->visitor===TRUE){
                $data['call_url']='olook://identify.toapp>menu';
            }else{
                $data['call_url']="javascript:history.go(-1)";
            }

        }else{
            $data['call_url']="javascript:history.go(-1)";
        }
        $data['count_url']=$this->count_url;
        $data['down']=$this->down;
        $data['menu']='0';
        $data['per_user_id']=0;
        $data['down']="#";
        $data['app_session']=session_id();
        $data['is_fav']=FALSE;

        $data['per_user_id']=$this->user_id;

        if(isset($_COOKIE['menu']))
        {
            $data['menu']=$_COOKIE['menu'];
            unset($_COOKIE['menu']);
        }
        $ts_id=$this->input->get_post('ts_id',TRUE);
        $data['has_front']=FALSE;
        $select="v_ts.ts_id,v_ts.user_id,v_ts.banner_image,v_ts.banner_product AS act_image,v_ts.range_name,v_ts.tag,v_ts.star_num,v_ts.business_id,
        v_ts.order_sell,v_ts.content,v_ts.title,v_ts.title,v_ts.hotelstars,v_ts.hoteldays,v_ts.flight,v_ts.flighttickets,v_goods.ori_price,v_goods.oori_price,v_goods.front_price,v_ts.attr_json,v_goods.attention_list";
        $where="v_ts.ts_id=$ts_id AND v_goods.is_show='1'";
        $data['products']=$this->User_model->get_one($select,$where,'v_ts','v_goods','ts_id','ts_id');
        if($data['products']['front_price']>0)
        {
            $data['has_front']=TRUE;
        }
        if(stristr($data['products']['banner_image'], 'http')===false)
        {
            $data['products']['banner_image'] = $this->config->item('base_url'). ltrim($data['products']['banner_image'],'./');
        }
        if(stristr($data['products']['act_image'], 'http')===false)
        {
            $data['products']['act_image'] = $this->config->item('base_url'). ltrim($data['products']['act_image'],'./');
        }

        $range=$data['products']['range_name'];
        $sharetitle=$data['products']['title'];
        $data['rec_products']=$this->User_model->get_select_all($select,"v_ts.is_show='1'  AND v_goods.is_show='1' AND range_name='$range'  AND v_ts.ts_id !=$ts_id",'ts_id','ASC','v_ts',1,'v_goods',"v_goods.ts_id=v_ts.ts_id");
        if( $data['rec_products']==false){
            $data['rec_products']=array();
        }

        $star_arr=$this->User_model->get_select_all($select='star_num',"ts_id=$ts_id AND order_status='3'",$order_title='order_id',$order='ASC',$table='v_order_info');

        if($star_arr!==FALSE)
        {

            $star_num=0;
            foreach($star_arr as $k=>$v)
            {
                $star_num+=$v['star_num'];
            }

            $star_num=$star_num/count($star_arr);
            $data['products']['star_num']=round($star_num);
        }

        $tag_arr=explode(',',$data['products']['tag']);
        $str='';
        foreach($tag_arr as $k1=>$v1)
        {
            $str.=$v1;
            $data['tag_arr'][]=$v1;
            if(mb_strlen($str)>14){
                break;
            }
        }

        foreach($data['rec_products'] as $k=>$v)
        {
            $data['rec_products'][$k]['url']=base_url("myshop/trip_detail?ts_id=$v[ts_id]&share_user_id=$data[per_user_id]");

            $tag_arr=explode(',',$v['tag']);
            $str='';
            foreach($tag_arr as $k1=>$v1)
            {
                $str.=$v1;
                $data['rec_products'][$k]['tag_arr'][]=$v1;
                if(mb_strlen($str)>14){
                    break;
                }
            }
            if(mb_strlen($v['title'])>14){
                $data['rec_products'][$k]['title']=mb_substr($v['title'],0,14);
            }

        }
        $data['comment']=$this->User_model->get_select_all('v_users.user_id,v_users.user_name,v_users.sex,v_users.image,v_order_info.evaluate,v_order_info.confirm_time,v_order_info.star_num',"v_order_info.ts_id=$ts_id AND v_order_info.order_status ='3'",'v_order_info.order_id','ASC','v_order_info',1,'v_users',"v_users.user_id=v_order_info.user_id_buy");
        if($data['comment']==false)
        {
            $data['comment']=array();

            $data['comment_no']='暂无';
        }


        $data['share']['share_url']=base_url("myshop/trip_detail?ts_id=$ts_id&share_user_id=$data[per_user_id]");
        $data['share']['title']=$sharetitle;
        $data['share']['image']=$data['products']['act_image'];
        $data['share']['desc']="坐享其成上的特价产品{{$sharetitle}}欢迎选购。";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']='olook://shareinfo<'.$data['json_share'];

        $where="ts_id=$ts_id AND user_id=$data[per_user_id] AND type='3'";
        $fav=$this->User_model->get_select_one('ts_id',$where,'v_favorite');

        if($fav!==0)
        {
            $data['is_fav']=TRUE;
        }
        $data['show_title']=TRUE;
        $user_id=$data['products']['user_id'];
        $shop_info=$this->User_model->get_select_one('business_name',array('user_id'=>$user_id,'is_show'=>'1'),'v_wx_business');
        $data['business_name']=$shop_info['business_name'];
        if($et===TRUE)
        {

            $data['buy_url']=base_url('bussell/trip_order_index?ts_id=').$ts_id.'&share_user_id='.$share_id;

            $data['shop_url']=base_url("myshop/my_mall_list?user_id=$user_id");
        }
        else
        {
            $data['edit']=FALSE;
            $data['link_url']=TRUE;
            $data['shop_url']="javascript:void(0)";
            if($this->wx==false)
            {
                // $data['buy_url']=$this->down;


                if( $this->visitor===TRUE)
                {
                    $data['buy_url']=$this->tologin;
                    $data['shop_url']=base_url("myshop/my_mall_list?user_id=$user_id");
                }else{
                    $data['buy_url']=$this->down;

                }
            }
            else
            {
                $data['buy_url']=base_url("bussell/trip_order_index?ts_id=$ts_id&share_user_id=$share_id");
                $data['show_title']=FALSE;

            }
        }
        if($this->input->get('test'))
        {
            echo '<pre>';print_r($data);exit();
        }
        if($data['per_user_id']==4013){
        }

        $data['zx']='javascript:void(0)';

        $data['nobuy']=$this->input->get('nobuy',true);
        if($data['nobuy'])
        {
            $data['rec_products']=array();
        }
        $this->load->view('myshop/trip_detail_view',$data);
    }
}