<?php
/**
 * 2017 0109
 * zxf
 * 新产品类
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Products extends CI_Controller
{



    public $user_id=0;
    public $can_buy=TRUE;
    public $visitor=FALSE;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Order_model');
        $this->load->library('session');
        $this->load->helper('url');
       // $this->load->library('image_lib');
    //    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
      //  $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        //$this->east_country=array("越南","老挝","柬埔寨","缅甸","泰国","马来西亚","新加坡","印度尼西亚","菲律宾","文莱","东帝汶");
        $this->share_url='olook://shareinfo<';
        $this->tologin="olook://tologin";
        $this->get_visitor();
        $this->service_price=150;
        $this->et=FAlSE;
        $this->get_et();
        $this->user_id=$this->user_id_and_open_id();
        $this->share_pic="http://api.etjourney.com/tmp/share.jpg";
        $this->test_prise();

    }

    public function show_count()
    {
        $data['count_url']= $this->count_url;
        $this->load->view('products/show_count',$data);
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
    public function test_prise()
    {
        $user_id_arr=array(5059,1077);
        if(in_array($this->user_id,$user_id_arr))
        {
            $this->service_price=0.01;
        }
    }
    //普吉三日游 详情介绍
    public function goods_desc_add()
    {
        $id=$this->input->get('id',TRUE);
        if($id)
        {
            $data['info']=$this->User_model->get_select_one('*',array('id'=>$id),'v_goods_desc');
        }
        $data['goods']=$this->User_model->get_select_more($select='goods_id,goods_name',"goods_id IN (2657,2658,2659)",0,10,'goods_id','ASC',$table='v_goods');
        $data['sub_url']='/products/goods_adesc_insert';
       $this->load->view('products/desc_add',$data);
        $this->show_count();
    }

    public function goods_desc_edit()
    {
        $id=$this->input->get('id',TRUE);
        $data['info']=$this->User_model->get_select_one('*',array('id'=>$id),'v_goods_desc');
        $data['goods']=$this->User_model->get_select_more($select='goods_id,goods_name',"goods_id IN (2657,2658,2659)",0,10,'goods_id','ASC',$table='v_goods');
        $data['sub_url']='/products/goods_adesc_insert';
       // $this->load->view('products/desc_add',$data);
    }

    public function goods_adesc_insert()
    {
        $data['title']=$this->input->post('title',TRUE);
        $data['goods_id']=$this->input->post('goods_id',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $id=$this->input->post('id',TRUE);
        if($id)
        {
            $this->User_model-> update_one(array('id'=>$id),$data,$table='v_goods_desc');

        }else{
            $id=$this->User_model->user_insert('v_goods_desc',$data);
        }
        redirect("products/goods_desc_add?id=$id");

    }

    public function judge_can_buy($goods_id=2657)
    {
        $user_id=$this->user_id;
        $where="v_order_goods.goods_id =$goods_id AND v_order_info.user_id_buy=$user_id AND v_order_info.order_status IN ('0','1','2','3')";
        $num=$this->User_model->get_order_count($where);
      //  echo $this->db->last_query();
        $num=$num['count'];
        if($num>=1)
        {
            $this->can_buy=FALSE;
        }else{
            $this->can_buy=TRUE;
        }

        //$this->can_buy=TRUE;

    }

    public function get_et()
    {
        if(stristr($_SERVER['HTTP_USER_AGENT'],'etjourney')===FALSE)
        {
            $this->et=FALSE;
        }
        else
        {
            $this->et=TRUE;
        }
      //  $this->et=TRUE;
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
        $this->load->view('products/common_head',$data);
    }


    //首页  线路一2657  线路二2658  线路三2659  接送服务 2660
    public function index()
    {


    //    $data['num']=$this->User_model->get_count();
        $data['detail1']="/products/detail?line_id=1&id=4";
        $data['detail2']="/products/detail?line_id=1&id=5";
        $data['detail3']="/products/detail?line_id=1&id=6";

        $data['detail4']="/products/detail?line_id=2&id=7";
        $data['detail5']="/products/detail?line_id=2&id=8";
        $data['detail6']="/products/detail?line_id=2&id=9";
        $data['detail7']="/products/detail?line_id=2&id=10";

        $data['detail8']="/products/detail?line_id=3&id=1";
        $data['detail9']="/products/detail?line_id=3&id=2";
        $data['detail10']="/products/detail?line_id=3&id=3";
      //  $data['detail10']="/products/detail?line_id=3&id=10";
        if($this->et===TRUE)
        {
            $data['sub_url']="/products/choose";
            $data['num']=50;
            $data['call_back']='olook://identify.toapp';
            $data['show_head']=TRUE;
        }else{
            $data['call_back']='javascript:void(0)';
            $data['sub_url']='javascript:void(0)';
          //  $data['sub_url']="/products/choose";
            $data['show_head']=FALSE;
        }
        if($this->visitor==TRUE)
        {
            $data['down']=$this->tologin;
            $data['call_back']='olook://identify.toapp';
        }else{
            $data['down']=$this->down;
        }

        $data['share']['share_url']=base_url("products");
        $data['share']['title']='普吉岛0元嗨三天';


        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="只要你来，我就免费！活动周期一个月,三条线路全免费！";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];
        $where="goods_id IN (2657,2658,2659)";
        $data['num']=$this->User_model->get_count($where, $table='v_order_goods');
        $data['num']=  $data['num']['count'];
        $data['notice']="/products/notice";


        $data['path_one']="/products/path_one";
        $data['path_two']="/products/path_two";
        $data['path_three']="/products/path_three";

        $this->show_head('普吉岛0元嗨三天',$data['call_back'],'普吉岛0元嗨三天',$show_share=TRUE,$data['share_out']);
        $this->load->view('products/index_2',$data);
        $this->show_count();
    }
    /**
    获取录播地址信息
     **/

    function get_rec($video_name,$push_type)
    {
        $result = '';
        if($video_name)
        {
            if($push_type == '0')
            {
                $result = $this->config->item('record_url').$video_name.'.m3u8';
            }
            elseif($push_type == '1')
            {
                $result = $this->config->item('record_uc_url').$video_name.'.m3u8';
            }
        }
        return $result;
    }
    //详情页面
    public function detail()
    {

        $data=array();

        $line=$this->input->get('line_id',TRUE);
        $id=intval($this->input->get('id',TRUE));
        if($line==2)
        {
            $data['line']='二';
            $data['line_id']=2;
        }elseif($line==3)
        {
            $data['line']='三';
            $data['line_id']=3;
        }else{
            $data['line']='一';
            $data['line_id']=1;
        }


      //  $data['traffic']=$this->User_model->get_traffic_products(2336,2);
     //   $data['traffic']=$this->User_model->get_local_products(2336,200);



        $data['call_back']="javascript:history.go(-1)";

    //    $data['special_line']=$this->User_model->get_special_line(2336,5);
        //echo $this->db->last_query();
        $data['more_line']=base_url("myshop/special_line");

        $data['share']['share_url']=base_url("products/detail?line_id=$line&id=$id");
        $data['share']['title']='普吉岛0元嗨三天';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];

        if($this->et===TRUE)
        {
            $data['sub_url']=base_url("products/choose?line_id=$data[line_id]");
            if(stristr($_SERVER['HTTP_USER_AGENT'],'android'))
            {
                $data['app']='android';
            }
            else
            {
                $data['app']='iphone';
            }

        }else{
            if($this->visitor==TRUE)
            {
                $data['sub_url']=$this->tologin;
            }else{
                $data['sub_url']=$this->down;
            }
          //  $data['sub_url']=$this->down;
            $data['app']='h5';
        }
      //  $data['app']='android';


        $this->load->view('products/detail_head',$data);
        switch($id)
        {
            case 1:
                //动物园
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7181'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if($data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7181);
                }
                $this->show_head('景点详情',$data['call_back'],'动物园',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(453,505,473,523,499,478));
                $this->load->view('products/detail1',$data);
                break;
            case 2:
                //皇家免税店
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7160'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7160);
                }
                $this->show_head('景点详情',$data['call_back'],'皇家免税店',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(453,505,473,523,499,478));
                $this->load->view('products/detail2',$data);
                break;
            case 3:
                ///中央百货
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'6907'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(6907);
                }
                $this->show_head('景点详情',$data['call_back'],'中央百货',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(453,505,473,523,499,478));
                $this->load->view('products/detail3',$data);
                break;
            case 4:
                //查龙寺
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7163'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7163);
                }
                $this->show_head('景点详情',$data['call_back'],'查龙寺',$show_share=TRUE,$data['share_out']);

                $data['traffic']=$this->User_model-> get_choose_products(array(500,518,520,512,511,521));
                $this->load->view('products/detail4',$data);
                break;
            case 5:
                //香蕉海滩
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7186'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7186);
                }
                $this->show_head('景点详情',$data['call_back'],'香蕉岛',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(500,518,520,512,511,521));
                $this->load->view('products/detail5',$data);
                break;
            case 6:
                //东方spa
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7170'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7170);
                }
                $this->show_head('景点详情',$data['call_back'],'泰式SPA',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(500,518,520,512,511,521));
                $this->load->view('products/detail6',$data);
                break;
            case 7:
                ///天空漫步
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7201'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7201);
                }
                $this->show_head('景点详情',$data['call_back'],'天空漫步',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(498,506,485,512,511,454));
                $this->load->view('products/detail7',$data);
                break;
            case 8:
                //沙发里四合一
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7166'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7166);
                }
                $this->show_head('景点详情',$data['call_back'],'沙发里乐园',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(498,506,485,512,511,454));

                $this->load->view('products/detail8',$data);
                break;
            case 9:
                //神仙半岛
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7188'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7188);
                }
                $this->show_head('景点详情',$data['call_back'],'神仙半岛',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(498,506,485,512,511,454));

                $this->load->view('products/detail9',$data);
                break;
            default:
                //挪威海鲜市场
                $video_info=$this->User_model->get_select_one('video_name,video_id,push_type,image',array('video_id'=>'7180'),'v_video');
                $data['poster_image']=$video_info['image'];
                $data['video_url']=$this->get_rec($video_info['video_name'],$video_info['push_type']);
                if( $data['app']=='android')
                {
                    $data['video_url']='olook://lookvideo.toapp>guankan_lb&videoinfo<'.$this->get_video_info(7180);
                }
                $this->show_head('景点详情',$data['call_back'],'拉威海鲜市场',$show_share=TRUE,$data['share_out']);
                $data['traffic']=$this->User_model-> get_choose_products(array(498,506,485,512,511,454));

                $this->load->view('products/detail10',$data);
        }
//        $this->load->view('products/attractionDetails',$data);
        if($this->input->get('test'))
        {
            echo '<pre>';print_r($data);
        }

        $this->load->view('products/detail_footer',$data);
        $this->show_count();
    }


    public function get_video_info($video_id){

        $where=array('video_id'=>$video_id);
        $row=$this->User_model->get_select_one('views,praise,video_name,user_id,title,location,image,is_off,socket_info,push_type',$where,'v_video');
        $user_id=$row['user_id'];
        $where=array('user_id'=>$user_id);
        $data=$this->User_model->get_select_one('user_name,user_id,credits,image as user_image,sex,is_guide,is_attendant,is_driver,is_merchant,auth',$where,'v_users');

        //$data=$this->User_model->get_select_one('user_name,user_id,image as user_image',$where,'v_users');
        $this->load->library('common');
        $data['level']=$this->common->get_level($data['credits']);
        $data['video_id']=$video_id;
        $data['title']=$row['title'];
        $data['location']=$row['location'];
        $data['image']=$row['image'];
        $data['is_off']=$row['is_off'];
        $data['socket_info']=$row['socket_info'];
        $data['path']=$this->get_rec($row['video_name'],$row['push_type']);
        return json_encode($data);

    }

    /*
   * id
   * type id  1 goods_id
   */
    public function show_qrcode($id,$type_id=1)
    {
        $arr=array('id'=>$id,'type'=>$type_id);
        $arr_json=json_encode($arr);
        $this->load->library('common');
        $arr_json=$this->common->url_encode($arr_json);
        $this->get_qrcode($arr_json);
    }

    //活动须知
    public function  notice()
    {
        $data['call_back']="javascript:history.go(-1)";
        $data['pro_notice']="/products/pro_notice";
        $data['buy_notice']="/products/buy_notice";
        $data['book_notice']="/products/book_notice";
        $data['server_notice']="/products/server_notice";
        $data['tel_notice']="/products/tel_notice";

        $this->show_head('活动须知',$data['call_back'],'活动须知');

        $this->load->view('products/notice',$data);
        $this->show_count();
    }
    //产品概括
    public function pro_notice()
    {

        $data['call_back']="javascript:history.go(-1)";

        $this->show_head('产品概括',$data['call_back'],'产品概括');
        $this->load->view('products/pro_notice',$data);
        $this->show_count();
    }
    //如何购买
    public function buy_notice()
    {
        $data['call_back']="javascript:history.go(-1)";

        $this->show_head('如何购买',$data['call_back'],'如何购买');
        $this->load->view('products/buy_notice',$data);
        $this->show_count();
    }
    //预定须知
    public function book_notice()
    {
        $data['call_back']="javascript:history.go(-1)";


        $this->show_head('预定须知',$data['call_back'],'预定须知');
        $this->load->view('products/book_notice',$data);
        $this->show_count();
    }
    //服务条款
    public function server_notice()
    {
        $data['call_back']="javascript:history.go(-1)";


        $this->show_head('服务条款',$data['call_back'],'服务条款');
        $this->load->view('products/server_notice',$data);
        $this->show_count();
    }
    //联系客服
    public function tel_notice()
    {
        $data['call_back']="javascript:history.go(-1)";

        $this->show_head('联系客服',$data['call_back'],'联系客服');
        $this->load->view('products/tel_notice',$data);
        $this->show_count();
    }

    //2.4~3.3  购买页面

    public function choose()
    {
        $line=$this->input->post_get('line_id',TRUE);
        $data=array();
        if($line==2)
        {
            $data['line']='二';
            $data['line_id']=2;
            $goods_id=2658;
            $data['path_url']="/products/path_two";

        }
        elseif($line==3)
        {
            $data['line']='三';
            $data['line_id']=3;
            $goods_id=2659;
            $data['path_url']="/products/path_three";

        }else{
            $data['line']='一';
            $data['line_id']=1;
            $goods_id=2657;
            $data['path_url']="/products/path_one";

        }


        $this->judge_can_buy($goods_id);
        $data['sub_url']="/products/order_sub";
        $data['can_buy']=$this->can_buy;
        if($this->can_buy==false OR $this->et==false)
        {
            $data['sub_url']="javascript:void(0)";
            $data['can_buy']=false;
        }


        $data['share_user_id']=1;



        //产品选择日期
        $data['time_start']=$time_start=strtotime("2017-3-1");
        $data['time_end']= $time_end=strtotime("2017-3-31");
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time_start),
            'month'=>date('n',$time_start),
            'month_cn'=>$this->get_month_cn(date('n',$time_start)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start)))),
            'all_days'=>date('t',$time_start),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time_start),1,date('Y',$time_start))),
        );

//        $data['date']['cal'][]=array(
//            'year'=>date('Y',$time_end),
//            'month'=>date('n',$time_end),
//            'month_cn'=>$this->get_month_cn(date('n',$time_end)),
//            'week_first'=>date("w",mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end))),
//            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end)))),
//            'all_days'=>date('t',$time_end),
//            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time_end),1,date('Y',$time_end))),
//        );

        $data['call_back']="javascript:history.go(-1)";

        $data['agreement']="/products/server_notice";
        $this->load->view('products/choose',$data);
        $this->show_count();
    }


    //订单数据整理     //首页  线路一2657  线路二2658  线路三2659  接送服务 2660
    public function order_sub()
    {
      //  echo '<pre>';print_r($_POST);

       // $traffic=$this->input->post('traffic',TRUE);
        $traffic=1;
        $address=$this->input->post('address',TRUE);
        $line_id=$this->input->post('line_id',TRUE);
        if($line_id==1)
        {
            $goods_id=2657;
        }
        elseif($line_id==2)
        {
            $goods_id=2658;
        }
        elseif($line_id==3)
        {
            $goods_id=2659;
        }else{
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->judge_can_buy($goods_id);
        if($this->can_buy===false)
        {
            echo '-1';

            exit();
        }
        $goods_info=$this->User_model->get_select_one('goods_name',array('goods_id'=>$goods_id),'v_goods');

        $safe=$this->input->post('safe',TRUE);
        $date_id=$this->input->post('date_id',TRUE);
        //echo '<pre>';print_r($_POST);exit();
        if($safe!=1)
        {
            return false;
        }

        if($this->user_id!=0)
        {
            $user_id=$this->user_id;
        }else{
            return false;
        }
     //   echo $user_id;exit();
        $user_id_buy_name=$this->User_model->get_username($user_id,'v_users');
        $order_sn=$this->get_order_sn();
        $line_order_info=array(
            'user_id_buy'=>$user_id,
            'user_id_buy_name'=>$user_id_buy_name,
            'from'=>'11',
            'user_id_sell'=>1,
            'user_id_sell_name'=>'坐享其成',
            'order_sn'=>$order_sn,
            'goods_amount'=>'0',
            'address'=>$address,
            'order_amount'=>'0',
            'goods_all_num' =>'1',
            'add_time'=>time(),
            'order_status'=>'0',
            'date'=>$date_id,

        );
        $_SESSION['line_order_info']=$line_order_info;

        $line_order_goods=array(
            'goods_id'=>$goods_id,
            'goods_name'=>$goods_info['goods_name'],
            'goods_number'=>1,
            'goods_sum'=>1,
            'goods_price'=>0,
        );
        $_SESSION['line_order_goods']=$line_order_goods;
        $_SESSION['traffic']=0;
        if($traffic)
        {
            $_SESSION['traffic']=1;
            $order_sn=$this->get_order_sn();
            $line_order_info=array(
                'user_id_buy'=>$user_id,
                'user_id_buy_name'=>$user_id_buy_name,
                'from'=>'11',
                'user_id_sell'=>1,
                'user_id_sell_name'=>'坐享其成',
                'order_sn'=>$order_sn,
                'goods_amount'=> $this->service_price,
                'address'=>$address,
                'order_amount'=> $this->service_price,
                'goods_all_num' =>'1',
                'add_time'=>time(),
                'order_status'=>'0',
                'date'=>$date_id,

            );
            $_SESSION['traffic_order_info']=$line_order_info;

            $line_order_goods=array(
                'goods_id'=>2660,
                'goods_name'=>$goods_info['goods_name'].'接送服务',
                'goods_number'=>1,
                'goods_sum'=>1,
                'goods_price'=> $this->service_price,
            );
            $_SESSION['traffic_order_goods']=$line_order_goods;

        }
        echo 1;
        //redirect("/products/order_addition");
    }
//订单补充信息填写
    public function order_addition()
    {
        $data=array();
        if($this->can_buy===false)
        {
            echo '你已购买过该产品';
            header("Refresh:3;url=/products");
            exit();
        }

        $data['agreement']="javascript:void(0)";
        if(isset($_SESSION['line_order_info']) && isset($_SESSION['line_order_goods']) && isset($_SESSION['traffic']) )
        {
            $data['goods_name']=$_SESSION['line_order_goods']['goods_name'];
            $data['date']=$_SESSION['line_order_info']['date'];
//            if($_SESSION['traffic']==1)
//            {
//                $data['amount']= $this->service_price;
//
//            }else{
//                $data['amount']=0;
//            }

            $data['amount']= $this->service_price;
            $data['call_back']="javascript:history.go(-1)";
            $data['sub_url']="/products/order_to_db";
            $data['pay_url']="olook://payorder.toapp>fukuan&order_info<";
            $data['pay_finish']="/products/pay_finish";
            $data['time_ajax']="/products/time_ajax";
             $data['agreement']="/products/server_notice";
            $this->load->view('products/gopay',$data);
        }else{
            redirect("/products");
        }

    }


    //订单入库
    public function order_to_db()
    {


        if(isset($_SESSION['line_order_info']) && isset($_SESSION['line_order_goods']) && isset($_SESSION['traffic']) )
        {
            $order_id=$this->input->post('order_id',true);
            if($order_id==0)
            {
                $goods_id=$_SESSION['line_order_goods']['goods_id'];
                $this->judge_can_buy($goods_id);
                if($this->can_buy===false)
                {
                    echo '勿重复提交';
                    header("Refresh:3;url=/products");
                    exit();
                }

                $order_addition=array(
                    'cn_name'=>trim($this->input->post('cn_name',TRUE)),
                    'en_oth_name'=>trim($this->input->post('en_oth_name',TRUE)),
                    'en_first_name'=>trim($this->input->post('en_first_name',TRUE)),
                    'cn_mobile'=>trim($this->input->post('cn_mobile',TRUE)),
                    'weixin'=>trim($this->input->post('weixin',TRUE)),
                    'mail'=>trim($this->input->post('mail',TRUE)),
                    'en_hotel'=>trim($this->input->post('en_hotel',TRUE)),
                    'en_hotel_address'=>$_SESSION['line_order_info']['address'],
                    'passport_image'=>trim($this->input->post('passport',TRUE)),
                    'add_time'=>time(),
                    'type'=>'1'
                );

                $_SESSION['line_order_info']['commont']=trim($this->input->post('app_commont',TRUE));
               // $_SESSION['line_order_info']['address']=trim($this->input->post('en_hotel_address',TRUE));
                $_SESSION['line_order_info']['consignee']=trim($this->input->post('cn_name',TRUE));
                $_SESSION['line_order_info']['mobile']=trim($this->input->post('cn_mobile',TRUE));
                $line_order_id=$this->Order_model->order_insert($_SESSION['line_order_info'],$_SESSION['line_order_goods'],$order_addition);
                if($_SESSION['traffic']==1 && isset($_SESSION['traffic_order_info']) && isset($_SESSION['traffic_order_goods']))
                {
                    $_SESSION['traffic_order_info']['pid']=$line_order_id;
                    $_SESSION['traffic_order_info']['commont']=trim($this->input->post('app_commont',TRUE));
                  //  $_SESSION['traffic_order_info']['address']=trim($this->input->post('en_hotel_address',TRUE));
                    $_SESSION['traffic_order_info']['consignee']=trim($this->input->post('cn_name',TRUE));
                    $_SESSION['traffic_order_info']['mobile']=trim($this->input->post('cn_mobile',TRUE));


                    $order_id=$this->Order_model->order_insert($_SESSION['traffic_order_info'],$_SESSION['traffic_order_goods'],$order_addition);


                    $json=array();
                    $json['order_id']=$order_id;
                    $json['to_buy']=1;
                    $json['user_id_buy']=$_SESSION['traffic_order_info']['user_id_buy'];
                    $json['prod']='1';
                    if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                        $json['number']=1;
                        $json['order_sn']=$_SESSION['traffic_order_info']['order_sn'];
                        $json['ship_url']=base_url('ordernew/order_list_unshipbuy');
                    }else{
                        $json['num']=1;
                    }
                    $json['amount']=$this->service_price;
                    $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                    $json['productName']='三日普吉';
                    echo json_encode($json);
                }else{
                    $json['order_id']=$line_order_id;
                    $json['to_buy']=0;
                    echo json_encode($json);
                }
            }else{
               if($_SESSION['traffic']==1)
               {

                   $json=array();
                   $json['order_id']=$order_id;
                   $json['to_buy']=1;
                   $json['user_id_buy']=$_SESSION['traffic_order_info']['user_id_buy'];
                   $json['prod']=base_url('products/pay_finish');
                   if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
                       $json['number']=1;
                       $json['order_sn']=$_SESSION['traffic_order_info']['order_sn'];
                       $json['ship_url']=base_url('products/pay_finish');
                   }else{
                       $json['num']=1;
                   }
                   $json['amount']=$this->service_price;
                   $json['notifyURL']='http://api.etjourney.com/index.php/notify_alipay/callback/notify';
                   $json['productName']='三日普吉';
                   echo json_encode($json);
               }else{
                   $json['order_id']=$order_id;
                   $json['to_buy']=0;
                   echo json_encode($json);
               }
            }

        }else{
            redirect("/products");
        }
    }

    public function time_ajax()
    {
        $order_id=$this->input->post('order_id',true);
        if($order_id)
        {
            $user_id=$this->user_id;
            $data=$this->Order_model->get_order_detail($order_id,$user_id);
            if($data['order_status']=='1')
            {
                echo 1;
            }else{
                echo 0;
            }

        }else{
            echo 0;
        }

    }




    public function pay_finish()
    {
        $order_id=$this->input->get('order_id',true);
      //  $this->user_id=952;
        if($this->user_id!=0)
        {
            $user_id=$this->user_id;
        }else{
            return false;
        }

        $data=$this->Order_model->get_order_detail($order_id,$user_id);
       // echo $data['goods'][0]['goods_name'];
        $data['line_name']=$data['goods'][0]['goods_name'];
        $data['call_back']="javascript:history.go(-1)";
        $data['call_back']='/products';
        $data['detail_url']="/products/order_detail?order_id=$order_id";

        if($data['pid']!=0)
        {
            $data_temp=$this->Order_model->get_order_detail($data['pid'],$user_id);
            $data['line_name']=$data_temp['goods'][0]['goods_name'];
            $data['detail_url']="/ordernew/order_list_unshipbuy";
        }
        $data['to_url']= $data['detail_url'];
        $data['sec']=500;
        if($this->input->get('test'))
        {
            echo '<pre>';print_r($data);
        }

        $this->load->view('products/pay_finished',$data);
        $this->show_count();
    }


    public function order_detail()
    {

        $order_id=$this->input->get('order_id',true);
        if($this->user_id!=0)
        {
            $user_id=$this->user_id;
        }else{
           return false;
        }
        $data=$this->Order_model->get_order_detail($order_id,$user_id);
        if(count($data)<=0){
         //   return false;
        }
        if($data['order_status']==0)
        {
            $data['order_name']='未支付';
        }
        elseif($data['order_status']==1 OR $data['order_status']==2 OR $data['order_status']==3)
        {
            $data['order_name']='已支付';
        }else{
            $data['order_name']='已关闭';
        }
        $data['call_back']="javascript:history.go(-1)";

        $this->load->view('products/dingdan_detail',$data);
        $this->show_count();
    }


    public function path_one()
    {
        $data=array();
        $data['call_back']="javascript:history.go(-1)";


        if($this->et===TRUE OR $this->visitor==TRUE)
        {
            $data['show_head']=TRUE;
        }else{
            $data['show_head']=FALSE;
        }

        $data['share']['share_url']=base_url("products/path_one");
        $data['share']['title']='普吉三日免费游';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];

        $this->load->view('products/path_one',$data);
        $this->show_count();
    }


    public function path_two()
    {
        $data=array();
        $data['call_back']="javascript:history.go(-1)";

        if($this->et===TRUE OR $this->visitor==TRUE)
        {
            $data['show_head']=TRUE;
        }else{
            $data['show_head']=FALSE;
        }
        $data['share']['share_url']=base_url("products/path_two");
        $data['share']['title']='普吉三日免费游';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];

        $this->load->view('products/path_two',$data);
        $this->show_count();
    }

    public function path_three()
    {
        $data=array();
        $data['call_back']="javascript:history.go(-1)";

        if($this->et===TRUE OR $this->visitor==TRUE)
        {
            $data['show_head']=TRUE;
        }else{
            $data['show_head']=FALSE;
        }
        $data['share']['share_url']=base_url("products/path_three");
        $data['share']['title']='普吉三日免费游';
        $data['share']['image']=$this->share_pic;
        $data['share']['desc']="一站式旅游服务平台，为您精挑细选，让你坐享其成";

        $data['json_share']=json_encode($data['share']);
        $data['share_out']=  $this->share_url.$data['json_share'];


        $this->load->view('products/path_three',$data);
        $this->show_count();
    }



    //月份转换中文
    public function get_month_cn($month)
    {
        switch ($month)
        {
            case 1:
                return '一';
            case 2:
                return '二';
            case 3:
                return '三';
            case 4:
                return '四';
            case 5:
                return '五';
            case 6:
                return '六';
            case 7:
                return '七';
            case 8:
                return '八';
            case 9:
                return '九';
            case 10:
                return '十';
            case 11:
                return '十一';
            default:
                return '十二';
        }
    }

    public function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    public function sql_do()
    {

       $data=array(
           'goods_name'=>'坐享其成接送服务',
           'add_time'=>time(),
           'is_show'=>'1',
           'shop_price'=> $this->service_price,
           'ori_price'=> $this->service_price,
       );
       // $this->User_model->user_insert($table='v_goods',$data);
    }

    public function get_qrcode($value='李鹏远',$has_logo=0)
    {

        include_once './phpqrcode.php';
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 16;//生成图片大小
            //生成二维码图片
        QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = './tmp/logo'.$has_logo.'.png';//准备好的logo图片
        $QR = 'qrcode.png';//已经生成的原始二维码图
        if($has_logo)
        {
            if ($logo !== FALSE) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width/$logo_qr_width;
                $logo_qr_height = $logo_height/$scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新组合图片并调整大小
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
            }
        }else{
            $QR = imagecreatefromstring(file_get_contents($QR));
        }

//输出图片

        //imagepng($QR, 'helloweba.png');
        Header("Content-type: image/png");
        ImagePng($QR);

    }

    //验证
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






//decbin 12 bindec 20
    public function test()
    {
        $str= getcwd() ;
        $str="/opt/nginx/html/zxqc_git/zxqc/pc/application";
       $rs=$this->my_scandir($str);
        echo '<pre>';
       // print_r($rs);
    }


    function my_scandir($dir)
    {
        $files=array();
        if(is_dir($dir)){
            if($handle=opendir($dir))
            {
                while(($file=readdir($handle))!==false)
                {
                    if($file!='.' && $file!="..")
                    {
                        if(is_dir($dir."/".$file)){
                            $files[$file]=$this->my_scandir($dir."/".$file);
                        }else{
                            $files[]=$dir."/".$file;
                        }
                    }
                }
            }
        }
        closedir($handle);
        return $files;
    }



}