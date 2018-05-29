<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/3/30
 * Time: 14:00
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Shopadmin_t extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('common');

        $this->load->model('Goodsforcar_model');
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->library('image_lib');
    }


    //商户登录界面
    public function login()
    {
        $data=[];
        $data['act_login']=base_url('shopadmin_t/act_login');
        $data['index']=base_url('shopadmin_t');
        if(isset($_SESSION['business_id'])){
            redirect('/shopadmin_t');
        }else{
            $this->load->view('shopadmin/login',$data);
        }
    }
    //登录验证
    public function act_login()
    {

        $captcha=strtoupper($this->input->post('code',true));
        $time=time();
        /*临时*/
        unset($_SESSION['code']);
        $account =  $this->input->post('user',true);
        $password   =  $this->input->post('pwd',true);
        if(empty($account) OR empty($account))
        {
            //非法登录
            echo 2;
        }
        else
        {

            $shop_info=$this->Goodsforcar_model->get_shop_one(array('business_account'=>$account));
            if(count($shop_info)>0)
            {
                if($shop_info['password']==md5($password.$shop_info['salt']))
                {
                    $_SESSION['business_id']=$shop_info['business_id'];
                    $this->Goodsforcar_model->update_one(array('business_id'=>$shop_info['business_id']),array('login_time'=>time()),'wx_shop');
                    echo 5;
                }else{
                    echo 4;
                }
            }
            else
            {
                echo 3;
            }


        }
        exit();
        /**/


        if(!isset($_SESSION['code']) OR $captcha!=$_SESSION['code'] OR ($time-$_SESSION['time'])>360)
        {
            unset($_SESSION['code']);
            //验证码错误或者过期
            echo 1;
        }
        else
        {
            unset($_SESSION['code']);
            $account =  $this->input->post('user',true);
            $password   =  $this->input->post('pwd',true);
            if(empty($account) OR empty($account))
            {
                //非法登录
                echo 2;
            }
            else
            {

                $shop_info=$this->Goodsforcar_model->get_shop_one(array('business_account'=>$account));
                if(count($shop_info)>0)
                {
                    if($shop_info['password']==md5($password.$shop_info['salt']))
                    {
                        $_SESSION['business_id']=$shop_info['business_id'];
                        $this->Goodsforcar_model->update_one(array('business_id'=>$shop_info['business_id']),array('login_time'=>time()),'wx_shop');
                         echo 5;
                    }else{
                        echo 4;
                    }
                }
                else
                {
                        echo 3;
                }

            }
        }
    }


    //验证码
    public function get_cpa()
    {
        $this->load->library('captcha');
        $code = $this->captcha->getCaptcha();
        $_SESSION['code']=strtoupper($code);
        $_SESSION['time']=time();
        $this->captcha->showImg();
    }

    //结构页 frame
    public function index()
    {
        $data=[];
        $this->get_auth();
        $data['main']=base_url('shopadmin_t/to_do');
        $data['top']=base_url('shopadmin_t/top');
        $this->load->view('shopadmin/index',$data);

    }


//简单获取侧边链接
    public function get_left_url()
    {
        return array(
            'to_do'=>base_url('shopadmin_t/to_do'),
            'did'=>base_url('shopadmin_t/did'),
            'account'=>base_url('shopadmin_t/account'),
            'goods_add'=>base_url('shopadmin_t/goods_add'),
            'goods_list'=>base_url('shopadmin_t/goods_list'),

            'pwd_edit'=>base_url('shopadmin_t/pwd_edit'),
            'login_out'=>base_url('shopadmin_t/login_out'),

        );
    }

    //密码修改
    public function edit_pwd()
    {
        $password_old=$this->input->post('password_old');
        $password_new=$this->input->post('password_new');
        $info=$this->Goodsforcar_model->get_shop_one(array('business_id'=>$_SESSION['business_id']));
        if(md5($password_old.$info['salt'])==$info['password'])
        {
            $salt=rand(1,9999);
            $password=md5($password_new.$salt);
            $this->Goodsforcar_model->update_one(array('business_id'=>$_SESSION['business_id']),array('password'=>$password,'salt'=>$salt),'wx_shop');
            unset($_SESSION['business_id']);
            redirect('/shopadmin_t');
        }else{
            redirect('/shopadmin_t');
        }




    }

    //退出登录
    public function login_out()
    {
        unset($_SESSION['business_id']);
        redirect('/shopadmin_t');
    }

    //修改密码
    public function pwd_edit()
    {
        $data=[];
        //$this->get_auth();
        $data['shop_info']=$this->Goodsforcar_model->get_shop_one(array('business_id'=>$_SESSION['business_id']));
        $data['url']=base_url('shopadmin_t/edit_pwd');
        //$data['shop_info']=$this->Goodsforcar_model->get_shop_one(array('business_id'=>1));

        $this->load->view('shopadmin/pwd_edit',$data);
    }




    //main 页面  待处理
    public function to_do($page=1)
    {
        $data=$this->get_left_url();
        $this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));
        $trip_id=$this->input->get('trip_id',TRUE);

        $where="is_show=1 AND business_id= $_SESSION[business_id] AND todo=0 AND order_status IN (1,2,3)";


        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%' ";
        }

        $data['date']=$date;
        $data['trip_id']=$trip_id;

        $data['did_url']=base_url('shopadmin_t/did_url');

        $page=intval($page);
        if(!$page)
        {
            $page=1;
        }
        $page_num =1000;
        $data['now_page'] = $page;

        $data['search_url']=base_url('shopadmin_t/to_do');

        $count = $this->Goodsforcar_model->get_order_goods_all_count($where,'wx_order_info');

        $data['count']=$count;
        $data['max_page'] = ceil($count/$page_num);

        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;

        $data['order_info']=$this->Goodsforcar_model->get_order_goods_all($where,$page_num,$start);
        $data['new_order_info']=[];
        foreach($data['order_info'] as $k=>$v)
        {
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_name'];
            $data['new_order_info'][$v['trip_id']]['date']=date('Y-m-d',$v['add_time']);

            if(isset($data['new_order_info'][$v['trip_id']]['all_amount']))
            {
                $data['new_order_info'][$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $data['new_order_info'][$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

            $data['new_order_info'][$v['trip_id']]['show_detail']=base_url("shopadmin_t/show_detail?trip_id=$v[trip_id]");
            $data['new_order_info'][$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($data['new_order_info']);



        unset($data['order_info']);
        //echo $this->db->last_query();
       // echo '<pre>';print_r($data['new_order_info']);exit();
        //print_r($data['order_info']);exit();
        $data['page']=$page;
      //  echo $this->db->last_query();
        $this->load->view('shopadmin/main_t',$data);
    }

    //main 页面  已处理
    public function did($page=1)
    {

       // print_r($_GET);exit();
        $data=$this->get_left_url();
        $this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));
        $trip_id=$this->input->get('trip_id',TRUE);

        $where="is_show=1 AND business_id= $_SESSION[business_id] AND todo=1 AND order_status IN (1,2,3)";


        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%' ";
        }

        $data['date']=$date;
        $data['trip_id']=$trip_id;
        $data['sub']=base_url('shopadmin_t/did');
        $page=intval($page);
        if(!$page)
        {
            $page=1;
        }
        $page_num =1000;
        $data['now_page'] = $page;

        $data['search_url']=base_url('shopadmin_t/did');

        $count = $this->Goodsforcar_model->get_order_goods_all_count($where,'wx_order_info');

        $data['count']=$count;
        $data['max_page'] = ceil($count/$page_num);

        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;

        $data['order_info']=$this->Goodsforcar_model->get_order_goods_all($where,$page_num,$start);
        $data['new_order_info']=[];
        foreach($data['order_info'] as $k=>$v)
        {
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_name'];
            $data['new_order_info'][$v['trip_id']]['date']=date('Y-m-d',$v['add_time']);

            if(isset($data['new_order_info'][$v['trip_id']]['all_amount']))
            {
                $data['new_order_info'][$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $data['new_order_info'][$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

           // $temp_trip=$this->common->url_encode($v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['show_detail']=base_url("shopadmin_t/guide_detail?trip_id=$v[trip_id]");

            $data['new_order_info'][$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($data['new_order_info']);



        unset($data['order_info']);
        //echo '<pre>';print_r($data['new_order_info']);exit();
        //print_r($data['order_info']);exit();
        $data['page']=$page;
        $this->load->view('shopadmin/order',$data);
    }


    //全部订单
    public function all($page=1)
    {
        $data=$this->get_left_url();
        //$this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));
        $trip_id=$this->input->get('trip_id',TRUE);

        $where="is_show=1 AND  order_status IN (1,2,3)";


        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%' ";
        }

        $data['date']=$date;
        $data['trip_id']=$trip_id;
        $page=intval($page);
        if(!$page)
        {
            $page=1;
        }
        $page_num =1000;
        $data['now_page'] = $page;

        $data['search_url']=base_url('shopadmin_t/did');

        $count = $this->Goodsforcar_model->get_order_goods_all_count($where,'wx_order_info');

        $data['count']=$count;
        $data['max_page'] = ceil($count/$page_num);

        if($page>$data['max_page'])
        {
            //return false;
            $page=1;
        }

        $start = ($page-1)*$page_num;

        $data['order_info']=$this->Goodsforcar_model->get_order_goods_all($where,$page_num,$start);
        $data['new_order_info']=[];
        foreach($data['order_info'] as $k=>$v)
        {
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_name'];

            if(isset($data['new_order_info'][$v['trip_id']]['all_amount']))
            {
                $data['new_order_info'][$v['trip_id']]['all_amount']+=$v['goods_sum'];
            }else{
                $data['new_order_info'][$v['trip_id']]['all_amount']=$v['goods_sum'];
            }

            // $temp_trip=$this->common->url_encode($v['trip_id']);
            $data['new_order_info'][$v['trip_id']]['show_detail']=base_url("shopadmin_t/guide_detail?trip_id=$v[trip_id]");

            $data['new_order_info'][$v['trip_id']]['val'][]=$v;

        }
        $data['new_order_info']=array_values($data['new_order_info']);



        unset($data['order_info']);
        //echo '<pre>';print_r($data['new_order_info']);exit();
        //print_r($data['order_info']);exit();
        $data['page']=$page;
        $this->load->view('shopadmin/order',$data);
    }

    //main 页面  对账单
    public function account()
    {
        $data=$this->get_left_url();
        $this->get_auth();


        $date=$this->input->get('date',TRUE);
        $trip_id=$this->input->get('trip_id',TRUE);

        $data['new_order_info']=[];
      //  $data['order_info']=$this->Goodsforcar_model->account_list(0,$_SESSION['business_id'],0,0,1);
        $data['order_info']=$this->Goodsforcar_model->get_account_infO($_SESSION['business_id'],$trip_id,$date);
        foreach($data['order_info'] as $k=>$v){
            //  $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            //$data['new_order_info'][$v['trip_id']]['cash_money']=0;
            //$data['new_order_info'][$v['trip_id']]['wx_money']=0;
            if(!isset($data['new_order_info'][$v['trip_id']]['cash_money']))
            {
                $data['new_order_info'][$v['trip_id']]['cash_money']=0;
            }
            if(!isset($data['new_order_info'][$v['trip_id']]['wx_money']))
            {
                $data['new_order_info'][$v['trip_id']]['wx_money']=0;
            }
            if($v['pay_id']==4)
            {
                $data['new_order_info'][$v['trip_id']]['cash_money']=$v['sum_money'];
            }else{
                $data['new_order_info'][$v['trip_id']]['wx_money']=$v['sum_money'];
            }
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['user_id_sell']=$v['user_id_sell'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$this->Goodsforcar_model->get_user_name($v['user_id_sell']);
        }
        $data['new_order_info']=array_values($data['new_order_info']);
        unset($data['order_info']);
        // echo '<pre>';print_r($data['new_order_info']);
         //echo '<pre>';print_r($data['order_info']); exit();

        $this->load->view('shopadmin/account',$data);
    }


    //show detail
    public function show_detail()
    {

        $data=$this->get_left_url();
        $this->get_auth();
        $date=strtotime($this->input->get('date',TRUE));

        $trip_id=$this->input->get('trip_id',TRUE);

        $where="is_show=1 AND business_id=$_SESSION[business_id]  AND order_status IN (1,2,3)";

        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%'  ";
        }


        $data['date']=$date;
        $data['trip_id']=$trip_id;

        $data['did_url']=base_url('shopadmin_t/show');

        $data['order_info']=$this->Goodsforcar_model->get_order_goods_all($where,0,0);

        $data['new_order_info']=[];
        foreach($data['order_info'] as $k=>$v)
        {
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_name'];



            $data['new_order_info'][$v['trip_id']]['val'][]=$v;

        }
       // echo '<pre>';print_r($data['new_order_info']);exit();
        $this->load->view('shopadmin/show',$data);
    }


    public function did_url()
    {
        if(!isset($_SESSION['business_id']))
        {
            return FALSE;
        }
        $trip_id=$this->input->post('trip_id',TRUE);

        return   $this->Goodsforcar_model->update_one(array('trip_id'=>$trip_id,'business_id'=>$_SESSION['business_id']),array('todo'=>'1'),'wx_order_info');

    }

    //导游订单明细
    public function guide_detail()
    {
        $data=$this->get_left_url();
        $this->get_auth();

        $date=strtotime($this->input->get('date',TRUE));


        $trip_id=$this->input->get('trip_id',TRUE);
        $data['trip_id']=$trip_id;
        $order_status=$this->input->get('order_status',TRUE);
        if(!$order_status)
        {
            $order_status=1;
        }
        $data['order_status']=$order_status;
        $where="is_show=1 AND business_id=$_SESSION[business_id]  AND order_status =$order_status";

        if($date)
        {
            $date_end=$date+84600;
            $where.=" AND add_time >=$date AND add_time<$date_end";
        }
        if($trip_id)
        {
            $where.=" AND trip_id LIKE '%$trip_id%'  ";
        }


        $data['order_info']=$this->Goodsforcar_model->get_order_all($where,$page_num=0,$start=0);
        foreach($data['order_info'] as $k=>$v){
            $data['new_order_info'][$v['trip_id']]['trip_id']=$v['trip_id'];
            $data['new_order_info'][$v['trip_id']]['guide_name']=$v['user_id_sell_name'];
            unset($v['add_time']);
         //   unset($v['mobile']);
            unset($v['goods_all_num']);
            unset($v['commont']);


            $data['new_order_info'][$v['trip_id']]['val'][]=$v;
        }
        $data['new_order_info']=array_values($data['new_order_info']);
        unset($data['order_info']);

        $data['cash_url']=base_url("shopadmin_t/guide_detail?trip_id=$trip_id&order_status=2");
        $data['wx_url']=base_url("shopadmin_t/guide_detail?trip_id=$trip_id&order_status=1");
        $data['succ_url']=base_url("shopadmin_t/guide_detail?trip_id=$trip_id&order_status=3");
        //echo'<pre>';print_r($data['new_order_info']);exit();
        $this->load->view('shopadmin/guide_order',$data);
    }







    //top
    public function top()
    {
        $data=$this->get_left_url();
        $this->get_auth();
        $data['wxinfo']=$this->Goodsforcar_model->get_shop_one($where="business_id=$_SESSION[business_id]");
        $this->load->view('shopadmin/top',$data);
    }

    //无session 定向登录
    public function get_auth()
    {
        if(!isset($_SESSION['business_id']))
        {
            redirect('shopadmin_t/login');
        }
    }

    /*
    *地接社添加商品页
    */
    public function goods_add()
    {
        //$this->auth_or_no();

        $goods_id=$this->input->get('goods_id',TRUE);
        $data=[];
        // echo '<pre>';print_r($data);exit();
        $data['sub_url']="/shopadmin_t/goods_insert";
        if($goods_id)
        {
            $data['sub_url']="/shopadmin_t/goods_sub";
            $data['info']=$this->User_model->get_select_one('*',array('goods_id'=>$goods_id),'wx_new_goods');
            $data['info']['image_r']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'7','isdelete'=>0),'v_images');
            $data['info']['image_r']=isset($data['info']['image_r']['url'])?$data['info']['image_r']['url']:$data['info']['image_r']['url'];

            $data['info']['image_s']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'8','isdelete'=>0),'v_images');
            $data['info']['image_s']=isset($data['info']['image_s']['url'])?$data['info']['image_s']['url']:$data['info']['image_s']['url'];

            $data['info']['image_c']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'9','isdelete'=>0),'v_images');
            $data['info']['image_c']=isset($data['info']['image_c']['url'])?$data['info']['image_c']['url']:'';
        }
        $this->load->view('newadmin/car_goods_add',$data);
    }

    /*
    *地接社添加商品提交
    */
    public function goods_insert()
    {
        //echo'<pre>';print_r($_POST);

        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['ori_price']=$this->input->post('ori_price',TRUE);
        $data['oori_price']=$this->input->post('oori_price',TRUE);
        $data['is_show']=$this->input->post('is_show',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $data['addtime']=time();
        $goods_id=$this->User_model->user_insert('wx_new_goods',$data);
        if($_FILES['image_r']['error']==0 && $_FILES['image_s']['error']==0)
        {

            $image_r=$this->upload_image('image_r', 'car');
            $image_s=$this->upload_image('image_s', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'7','url'=>$image_r,'createdAt'=>time()));
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'8','url'=>$image_s,'createdAt'=>time()));

        }
        if($_FILES['image_c']['error']==0 )
        {
            $image_c=$this->upload_image('image_c', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'9','url'=>$image_c,'createdAt'=>time()));

        }

        return redirect("/shopadmin_t/goods_add?goods_id=$goods_id");
    }

    /*
    *地接社修改商品提交
    */
    public function goods_sub()
    {
       // echo'<pre>';print_r($_POST);
        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['ori_price']=$this->input->post('ori_price',TRUE);
        $data['oori_price']=$this->input->post('oori_price',TRUE);
        $data['is_show']=$this->input->post('is_show',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $data['addtime']=time();

        $goods_id=$this->input->post('goods_id',TRUE);
        if($_FILES['image_r']['error']==0)
        {
            $image_r=$this->upload_image('image_r', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'7'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'7','url'=>$image_r,'createdAt'=>time()));

        }
        if($_FILES['image_s']['error']==0)
        {
            $image_s=$this->upload_image('image_s', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'8'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'8','url'=>$image_s,'createdAt'=>time()));
        }

        if($_FILES['image_c']['error']==0 )
        {
            $image_c=$this->upload_image('image_c', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'9'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'9','url'=>$image_c,'createdAt'=>time()));

        }
        $this->User_model->update_one(array('goods_id'=>$goods_id),$data,'wx_new_goods');
        return redirect("/shopadmin_t/goods_add?goods_id=$goods_id");
    }

    /*
    *地接社修改商品列表
    */
    public function goods_list($page=1)
    {
        $where='1=1';
        $is_show=$this->input->get('is_show');
        if($is_show)
        {
            $where.=" AND is_show=2";
        }else{
            $where.=" AND  is_show =1";
        }
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $is_off= $data['is_off']= $this->input->get('is_off',true);
   
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'wx_new_goods');
        $data['max_page'] = ceil($count['count']/$page_num);
        //print_r($data['max_page']);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

        $data['list']=$this->User_model->get_goods_lists($where,'wx_new_goods',$order_title='displayorder',$order='ASC',$start,$page_num);
         
          foreach($data['list'] as $k=>$v)
        {
            $data['list'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['list'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['list'][$k]["edit_url"]="/shopadmin_t/goods_add?goods_id=$v[goods_id]";
        }
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['is_show']=$is_show ? $is_show : 1;
     
       // echo "<pre>";
      // print_r($data);
      //  echo "</pre>";
        $this->load->view('newadmin/car_goods_list',$data);
    }
}