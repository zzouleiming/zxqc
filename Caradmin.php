<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * 车购导游后台
 * Date: 2017/3/29
 * Time: 14:11
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Caradmin extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('common');

        $this->load->model('Goodsforcar_model');
        $this->load->helper('url');
       // $this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
       // unset($_SESSION['guide_admin_id']);
        //单商品提交
        $this->one=md5('one+1');
        //购物车提交
        $this->all=md5('all+1');
    }

    //导游信息注册收集
    public function pre_index()
    {
        $data=[];
        $url=base_url('caradmin/pre_index');
        $guide_id=$this->get_wx_userid($url,0);
        $guide_info=$this->Goodsforcar_model->get_wxuser_info(array('user_id'=>$guide_id));
        if($guide_info['guide_id']==1)
        {
            redirect(base_url('caradmin'));
        }else{
            $data['sub']=base_url('caradmin/to_be_guider');
            $this->load->view('caradmin/register',$data);
        }

    }

    //成为导游
    public function to_be_guider()
    {
       $data['is_guide']=1;

       $data['user_name']=$this->input->get_post('user_name',TRUE);
       $data['mobile']=$this->input->get_post('mobile',TRUE);
       $data['account']=$this->input->get_post('account',TRUE);
        $this->Goodsforcar_model->update_one(array('user_id'=>$_SESSION['guide_admin_id']),$data,'v_wx_users');
        return redirect(base_url('caradmin'));
    }

    //确认发货

    public function sure_url(){
        $order_id=$this->input->post('order_id');
        if(intval($order_id))
        {
            return   $this->Goodsforcar_model->update_one(array('order_id'=>$order_id),array('order_status'=>'3'),'wx_order_info');

        }

    }
    //type 微信用户身份 0普通 1 导游
    public function get_wx_userid($url,$type=1)
    {
        $this->load->library('Wxauth');
        if(isset($_SESSION['guide_admin_id']))
        {
            return $_SESSION['guide_admin_id'];
        }
        else
        {

            if (!isset($_GET['code']))
            {
                //base_url("bussell/order_add_fromwx?act_id={$act_id}")
                //触发微信返回code码
                $url = $this->wxauth->createOauthUrlForCode_all(urlencode($url));
                Header("Location: $url");
            }
            else
            {
                //获取code码，以获取openid
                $code = $_GET['code'];
                $this->wxauth->setCode($code);
                //$openid = $jsApi->getOpenId();
                $wxuserinfo =  $this->wxauth->wxuserinfo($code);

                if(!isset($wxuserinfo['openid']))
                {
                    print_r($code);
                    echo '无token';
                    exit();
                }
                $openid=$wxuserinfo['openid'];
                $user_name=$wxuserinfo['nickname'];
                $sex=$wxuserinfo['sex'];
                $sex==1?$sex_et='0': $sex_et='1';
                $lan=$wxuserinfo['language'];
                $address=$wxuserinfo['city'];
                $image=$wxuserinfo['headimgurl'];
                $num=strripos($image,'/');
                //$numall=strlen($str);
                $image=substr($image,0,$num);
                //  $wxinfo=$this->User_model->get_select_one($select='openid,user_id',array('openid'=>$openid),'v_wx_users');
                $wxinfo=$this->Goodsforcar_model->get_wxuser_info(array('openid'=>$openid));

                if($wxinfo){
                    $_SESSION['openidfromwx']=$wxinfo['openid'];
                    $_SESSION['guide_admin_id']=$wxinfo['user_id'];
                    if($type=='1')
                    {
                        $this->Goodsforcar_model->update_one(array('openid'=>$openid,'hash_id'=>md5($openid)),array('is_guide'=>'1'),'v_wx_users');
                    }
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
                        'image'=>$image.'/96',
                        'hash_id'=>md5($openid),

                    );
                    if($type=='1')
                    {
                        $datauser['is_guide']='1';
                    }
                    $_SESSION['openidfromwx']=$openid;
                    $_SESSION['guide_admin_id']=$this->Goodsforcar_model->user_insert('v_wx_users',$datauser);

                    return $_SESSION['guide_admin_id'];
                }
            }
        }

    }

    //2 现金付款 1微信已付款 3交易完成
    public function index($type=1)
    {

        $data=[];
        $url=base_url('caradmin');
       // $guide_id=$_SESSION['guide_admin_id']=4;
     //   unset($_SESSION['guide_id']);
        $guide_id=$this->get_wx_userid($url,0);
        //var_dump($guide_id);exit();
        if(!$guide_id)
        {
            return false;
        }
        $guide_info=$this->Goodsforcar_model->get_wxuser_info(array('user_id'=>$guide_id));

        if($guide_info['is_guide']!=1)
        {

            return redirect(base_url('caradmin/pre_index'));
        }




        $data['cash_pay']=base_url("Caradmin/index/2");
        $data['wx_pay']=base_url("Caradmin/index/1");
        $data['order_end']=base_url("Caradmin/index/3");

        $data['sure_url']=base_url("Caradmin/sure_url");

        $data['account']=base_url('Caradmin/account');
        if(!in_array($type,array(1,2,3)))
        {
            $type=1;
        }

        $ajax=$this->input->post('ajax',true);
        $page=$this->input->post_get('page',true);

        if(!$page)
        {
            $page=1;
        }
        $page_num =6;
        $start = ($page-1)*$page_num;
        $data['now_page'] = $page;

        $where=" user_id_sell=$guide_id AND is_show=1 AND order_status=$type";

        $count = $this->Goodsforcar_model->get_count($where,'wx_order_info');
        $data['max_page'] = ceil($count/$page_num);
        $data['new_order_info']=[];
        $data['order_info']=$this->Goodsforcar_model->get_order_all($where,$page_num,$start);

        foreach($data['order_info'] as $k=>$v){
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            unset($v['add_time']);
            unset($v['mobile']);
            unset($v['goods_all_num']);
            unset($v['commont']);
            $v['first_goods_name']=$v['goods'][0]['goods_name'];
            $v['first_image_s']=$v['goods'][0]['image_s'];
            $v['first_goods_number']=$v['goods'][0]['goods_number'];
            $v['first_goods_sum']=$v['goods'][0]['goods_sum'];

            unset($v['goods']);
            $data['new_order_info'][$v['trip_id']]['val'][]=$v;
        }
        $data['new_order_info']=array_values($data['new_order_info']);
        //unset($data['order_info']);

      // echo '<pre>';print_r($data['order_info']);exit();
        if(!$ajax)
        {
            if($type==1)
            {
                $this->load->view('caradmin/wx_pay',$data);
            }
            elseif($type==2)
            {
                $this->load->view('caradmin/cash_pay',$data);
            }
            elseif($type==3)
            {
                $this->load->view('caradmin/order_end',$data);
            }
            else
            {
                $this->load->view('caradmin/cash_pay',$data);
            }

        }else{
            echo json_encode( $data['new_order_info']);
        }

    }


    //单订单详细信息
    public function show_detail()
    {
        $data=[];
        $data['cash_pay']=base_url("Caradmin/index/2");
        $data['wx_pay']=base_url("Caradmin/index/1");
        $data['order_end']=base_url("Caradmin/index/3");

        $data['sure_url']=base_url("Caradmin/sure_url");
        $data['account']=base_url('Caradmin/account');
        $order_id=$this->input->get('order_id',TRUE);
        $data['order_info']=$this->Goodsforcar_model->get_order_detail($order_id);
       // echo '<pre >';print_r($data['order_info']);
        $this->load->view('caradmin/show',$data);
    }

    //对账单信息
    public function account()
    {
        $data=[];
        $data['cash_pay']=base_url("Caradmin/index/2");
        $data['wx_pay']=base_url("Caradmin/index/1");
        $data['order_end']=base_url("Caradmin/index/3");

        $data['account']=base_url('Caradmin/account');

        //$where=" user_id_sell=$this->guide_id AND is_show=1 AND order_status=3 ";

        $data['new_order_info']=[];
        $data['order_info']=$this->Goodsforcar_model->account_list($_SESSION['guide_admin_id'],0);
        foreach($data['order_info'] as $k=>$v){
          //  $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            //$data['new_order_info'][$v['trip_id']]['cash_money']=0;
            //$data['new_order_info'][$v['trip_id']]['wx_money']=0;

            if($v['pay_id']==4)
            {
                $data['new_order_info'][$v['trip_id']]['cash_money']=$v['sum_money'];
            }else{
                $data['new_order_info'][$v['trip_id']]['wx_money']=$v['sum_money'];
            }
            if(!isset( $data['new_order_info'][$v['trip_id']]['cash_money']))
            {
                //$data['new_order_info'][$v['trip_id']]['cash_money']=0;
            }
            if(!isset( $data['new_order_info'][$v['trip_id']]['wx_money']))
            {
                //$data['new_order_info'][$v['trip_id']]['wx_money']=0;
            }
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
        }
        $data['new_order_info']=array_values($data['new_order_info']);
        unset($data['order_info']);
     // echo '<pre>';print_r($data['new_order_info']);
        //echo '<br>';echo '<pre>';print_r($data['order_info']);
        $this->load->view('caradmin/account',$data);
    }




    //后台导游列表
    public function guide_list($page=1)
    {  //$business_id=$_SESSION['business_id'];
      
    

      $where="business_id>0";

        $page_num =14;
        $data['now_page'] = $page;
        $count = $this->Goodsforcar_model->get_count($where,'wx_shop');
        $data['max_page'] = ceil($count/$page_num);
       // print_r($data['max_page']);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        
        $data['info']=$this->Goodsforcar_model->get_goods_lists($where,'wx_shop',$start,$page_num);
       /**   foreach($data['list'] as $k=>$v)
        {
            $data['list'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['list'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['list'][$k]["edit_url"]="/newadmin/car_goods_add?goods_id=$v[goods_id]";
        }**/
        $data['time2']=strtotime($this->input->get('time2',true));
      //      $data['info']=$this->Goodsforcar_model->get_wxuser_all('is_guide=1');
         
            foreach($data['info'] as $k=>$v)
            {
                $temp_id=$v['hash_id'];
                $data['info'][$k]['car_url']=base_url("Goodsforcar/index?guide_id=$temp_id&business_id=".$v['business_id']);
               
            }
            $this->load->view('caradmin/car_guide',$data);
            //echo '<pre>';
            //print_r($data['info']);
    
    }





}