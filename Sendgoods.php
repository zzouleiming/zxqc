<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/2/28
 * Time: 16:02
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Sendgoods extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Order_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->visitor=stristr($_SERVER['HTTP_USER_AGENT'],'visitor')===FALSE?FALSE:TRUE;
        $this->et=stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===FALSE?FALSE:TRUE;
       // $this->et=TRUE;
        $this->share_url='olook://shareinfo<';
        $this->tologin="olook://tologin";
        $this->share_pic="http://api.etjourney.com/tmp/share.jpg";
    }

    //获取 微信 user_id
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


    //验证 获取app user_id
    public function user_id_and_open_id()
    {
        //return 1077;
        if(isset($_COOKIE['user_id']))
        {
            $user_id=$_SESSION['user_id']=$_COOKIE['user_id'];
            $where=array('user_id'=>$user_id);
            $row=$this->User_model->get_select_one('openid',$where,$table='v_users');
            if(isset($_COOKIE['openid']))
            {
                $str=$row['openid'];
                $str=strtoupper(md5('ET'.$str));
                if($str==$_COOKIE['openid'])
                {
                    $_SESSION['openid']=$_COOKIE['openid'];
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
            //print_r($_COOKIE);exit();
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
                // echo $user_id;exit();
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

    public function index()
    {
        $data=array();

        $data['sub_url']='/Sendgoods/yy_index';
        if($this->et==TRUE)
        {
            $data['call_back']='olook://identify.toapp';
        }else{
            $data['call_back']="javascript:history.go(-1)";
        }

        $data['share']['share_url']=base_url("/sendgoods");
        $data['share']['title']='送乳胶枕';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="送乳胶枕！";
        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];

        $this->show_head('你来买 我就送',$data['call_back'],'你来买 我就送',$show_share=TRUE,$data['share_out']);
        $this->load->view('send/pillow',$data);
    }


    public function yy_index()
    {
        $data=array();

        if($this->wx===TRUE)
        {
            $user_id=$this->get_wx_userid(base_url('/sendgoods/yy_index'));
        }
        elseif($this->et===TRUE)
        {
            $user_id=$this->user_id_and_open_id();
        }
        elseif($this->visitor===TRUE)
        {
            redirect($this->tologin);
        }
        else
        {
            redirect('/index.php/Sendgoods');
        }

        $data['sub_url']='/index.php/Sendgoods/sub';
        $data['index']="/index.php/Sendgoods/";
        $data['share']['share_url']=base_url("products");
        $data['share']['title']='普吉岛0元嗨三天';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="送乳胶枕！";
        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];
        $data['call_back']="javascript:history.go(-1)";

        $this->show_head('快速预约',$data['call_back'],'快速预约',$show_share=TRUE,$data['share_out']);
        $this->load->view('send/pillow_app',$data);
    }

    public function sub()
    {

        set_time_limit(0);

        $data['date']=$this->input->post('date',TRUE);
        $data['order_sn']=$this->input->post('order_sn',TRUE);

//        echo'<pre>';
//        print_r($data);
//        print_r($_FILES);
//        exit();
        $this->et=true;
        $user_id=0;
        if($this->wx===TRUE)
        {
            $user_id=$this->get_wx_userid('/index.php/Sendgoods/yy_index');
        }
        elseif($this->et===TRUE)
        {
            $user_id=$this->user_id_and_open_id();
        }
        elseif( $this->visitor===TRUE)
        {
           echo -1;

            //redirect($this->tologin);
        }
        else
        {
            echo -1;
            //redirect('/Sendgoods');
        }
        if($this->wx===TRUE)
        {
            $from=2;
        }else{
            $from=1;
        }
        //测试
       // $user_id=1;
        if($user_id)
        {
            if($this->wx===TRUE)
            {

                $info=$this->User_model->get_count(array('order_sn'=>$data['order_sn'],'user_id_buy_fromwx'=>$user_id,'order_status'=>'3'), $table='v_order_info');

            }
            else
            {
                $info=$this->User_model->get_count(array('order_sn'=>$data['order_sn'],'user_id_buy'=>$user_id,'order_status'=>'3'),'v_order_info');
            }
            $count=$this->User_model->get_count(array('user_id'=>$user_id,'from'=>$from),'v_send');
            if($count['count']==0)
            {
                if($_FILES['file1']['error']==0 && $_FILES['file2']['error']==0 && $_FILES['file3']['error']==0 && $_FILES['file4']['error']==0)
                {

                    if($info['count']>0)
                    {

                        $data_indb=array(
                            'user_id'=>$user_id,
                            'addtime'=>time(),
                            'is_show'=>'1',
                            'date'=>strtotime($data['date']),
                            'order_sn'=>$data['order_sn'],
                            'from'=>$from

                        );


                        $id=$this->User_model->user_insert($table='v_send',$data_indb);

                        if($id>0)
                        {


                            $key1=md5(time().rand(1,99999));
                            $pic_url=$this->upload_image('file1',$key1);
                            $data_indb=array(
                                'link_id'=>$id,
                                'user_id'=>0,
                                'type'=>3,
                                'url'=>$pic_url,
                                // 'thumb'=>$this->thumb($pic_url),
                                'createdAt'=>time(),

                            );

                            $this->User_model->user_insert($table='v_images',$data_indb);


                            $key1=md5(time().rand(1,99999));
                            $pic_url=$this->upload_image('file2',$key1);
                            //  $thumb= $this->thumb($pic_url);
                            $data_indb=array(
                                'link_id'=>$id,
                                'user_id'=>0,
                                'type'=>4,
                                'url'=>$pic_url,
                                //  'thumb'=>$this->thumb($pic_url),
                                'createdAt'=>time(),

                            );
                            $this->User_model->user_insert($table='v_images',$data_indb);

                            $key1=md5(time().rand(1,99999));
                            $pic_url=$this->upload_image('file3',$key1);
                            //  $thumb= $this->thumb($pic_url);

                            $data_indb=array(
                                'link_id'=>$id,
                                'user_id'=>0,
                                'type'=>5,
                                'url'=>$pic_url,
                                //  'thumb'=>$this->thumb($pic_url),
                                'createdAt'=>time(),

                            );
                            $this->User_model->user_insert($table='v_images',$data_indb);

                            $key1=md5(time().rand(1,99999));
                            $pic_url=$this->upload_image('file4',$key1);
                            //$thumb= $this->thumb($pic_url);


                            $data_indb=array(
                                'link_id'=>$id,
                                'user_id'=>0,
                                'type'=>6,
                                'url'=>$pic_url,
                                //'thumb'=>$this->thumb($pic_url),
                                'createdAt'=>time(),

                            );
                            $this->User_model->user_insert($table='v_images',$data_indb);
                            echo 1;
                        }

                    }
                    else
                    {
                        echo 3;
                    }
                }else{
                    echo 4;
                }
            }
            else
            {
                echo 2;
            }




        }else{
            echo -1;
        }

    }


    public function sendinfo_list($page=1)
    {

        $page_num =100;
        $count = $this->User_model->get_count("is_show=1",'v_send');

        $data['max_page'] = ceil($count['count']/$page_num);
        $start = ($page-1)*$page_num;

        $info=$this->User_model->get_select_more('*',$where='is_show=1',$start,$page_num,'id',$order='ASC',$table='v_send');
        foreach($info as $k=>$v)
        {
            $info[$k]['pass_imagea']=$this->User_model->get_select_one('url',array('type'=>'3','link_id'=>$v['id']),'v_images');
            $info[$k]['pass_imagea']=stristr($info[$k]['pass_imagea']['url'],'http')?$info[$k]['pass_imagea']['url']:$this->config->item('base_url').ltrim($info[$k]['pass_imagea']['url'], '.');

            $info[$k]['pass_imageb']=$this->User_model->get_select_one('url',array('type'=>'4','link_id'=>$v['id']),'v_images');
            $info[$k]['pass_imageb']=stristr($info[$k]['pass_imageb']['url'],'http')?$info[$k]['pass_imageb']['url']:$this->config->item('base_url').ltrim($info[$k]['pass_imageb']['url'], '.');

            $info[$k]['fly_imagea']=$this->User_model->get_select_one('url',array('type'=>'5','link_id'=>$v['id']),'v_images');
            $info[$k]['fly_imagea']=stristr($info[$k]['fly_imagea']['url'],'http')?$info[$k]['fly_imagea']['url']:$this->config->item('base_url').ltrim($info[$k]['fly_imagea']['url'], '.');

            $info[$k]['fly_imageb']=$this->User_model->get_select_one('url',array('type'=>'6','link_id'=>$v['id']),'v_images');
            $info[$k]['fly_imageb']=stristr($info[$k]['fly_imageb']['url'],'http')?$info[$k]['fly_imageb']['url']:$this->config->item('base_url').ltrim($info[$k]['fly_imageb']['url'], '.');

        }
        echo'<pre>';print_r($info);
    }



    public function del_one($id=1)
    {
        $bool=$this->User_model->update_one(array('id'=>$id),array('is_show'=>3),$table='v_send');
        if($bool>0)
        {
            echo 1;
        }else{
            echo -1;
        }

    }

    public function get_one($id=1)
    {
        $info=$this->User_model->get_select_one('*',array('id'=>$id),'v_send');

        $info['pass_imagea']=$this->User_model->get_select_one('url',array('type'=>'3','link_id'=>$id),'v_images');
        $info['pass_imagea']=stristr($info['pass_imagea']['url'],'http')?$info['pass_imagea']['url']:$this->config->item('base_url').ltrim($info['pass_imagea']['url'], '.');

        $info['pass_imageb']=$this->User_model->get_select_one('url',array('type'=>'4','link_id'=>$id),'v_images');
        $info['pass_imageb']=stristr($info['pass_imageb']['url'],'http')?$info['pass_imageb']['url']:$this->config->item('base_url').ltrim($info['pass_imageb']['url'], '.');

        $info['fly_imagea']=$this->User_model->get_select_one('url',array('type'=>'5','link_id'=>$id),'v_images');
        $info['fly_imagea']=stristr($info['fly_imagea']['url'],'http')?$info['fly_imagea']['url']:$this->config->item('base_url').ltrim($info['fly_imagea']['url'], '.');

        $info['fly_imageb']=$this->User_model->get_select_one('url',array('type'=>'6','link_id'=>$id),'v_images');
        $info['fly_imageb']=stristr($info['fly_imageb']['url'],'http')?$info['fly_imageb']['url']:$this->config->item('base_url').ltrim($info['fly_imageb']['url'], '.');

        echo '<pre>';
        print_r($info);
    }

    public function show_one($id=1)
    {
        $bool=$this->User_model->update_one(array('id'=>$id),array('is_show'=>1),$table='v_send');
        if($bool>0)
        {
            echo 1;
        }else{
            echo -1;
        }

    }

    //图像处理方法
    public function upload_image($filename,$key='time')
    {
        if (!file_exists('./public/images/sendimage'))
        {
            if (!mkdir('./public/images/sendimage'))
            {
                return FALSE;
            }
        }

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

            $pic_url="./public/images/sendimage/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            return $pic_url;
        }
    }

    public function thumb($source)
    {


        $config['image_library'] = 'gd2';
        $config['source_image'] = $source;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']     = 960;
        $config['height']   = 480;

        $this->load->library('image_lib', $config);


        if( $this->image_lib->resize())
        {
            $this->image_lib->clear();
            $case=substr($source,strrpos($source,'.'));
            print_r(substr($source,0,strrpos($source,'.'))."_thumb{$case}");
            echo '<br>';
            return substr($source,0,strrpos($source,'.'))."_thumb{$case}";
        }



        //print_r(substr($source,0,strrpos($source,'.'))."_thumb{$case}");
    }


}