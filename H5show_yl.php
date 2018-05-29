<?php
/**
 * HTML模板展示页面
 * Date: 2017/4/20
 * Time: 11:05
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class H5show_yl extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('url');

    }



    /**
     * h5页面行程展示
     */
    public function show($day_id)
    {
        if(!$day_id)
        {
            redirect(base_url("h5info_yl/h5list"));
        }
      //  print_r($day_id);die;
        $data = array();
        //获取行程信息
        $where = 'day_id='.$day_id.' AND is_del=0';
        $res = $this->User_model->get_select_one($select='*',$where,'v_h5_day');

        if($res)
        {
            $data['info'] = $this->get_trip($res);

            $data['day_order'] = $res['day_order'];
        }
        else
        {
            redirect(base_url("h5info_yl/h5list"));
        }
//      echo "<pre>";
//     print_r($data);
//     
//     echo "</pre>";
//     exit;
        $this->load->view('h5/trip_show',$data);
        $this->show_count();
    }



 
    function get_trip($data)
    {
        $result = $data;
        //景点信息
        $result['day_view'] = json_decode($data['day_view']);
        //酒店信息
        $result['day_hotel'] = json_decode($data['day_hotel']);
        //同级酒店信息
        $result['day_grade_hotel'] = $data['day_grade_hotel'];
        //行程早中晚住宿
        $result['day_list'] = json_decode($data['day_list']);
        //行程首页显示信息
        $result['day_main'] = json_decode($data['day_main']);
        //行程提示
        $result['day_tips'] = ($data['day_tips']);
        //航班信息
        if($data['day_fly'])
        {
            $result['fly_info'] = $this->get_fly_info($data['h5_id'],$data['day_id']);
        }

        return $result;
    }

    /**
     * 展示首页面
     */
    public function index($id=0)
    {
        if(!is_numeric($id) || !$id)
        {
           redirect(base_url());
        }
        $data=[];
        //根据ID获取对应H5页面信息
        $h5_info = $this->User_model->get_select_one('*',array('h5_id'=>$id,'is_show'=>1),'v_h5_info');
        if(!$h5_info)
        {
            return false;
        }
        //获取航班信息
        //$fly_info = $this->User_model->get_select_one('*',array('h5_id'=>$id),'v_h5_fly');
        //获取行程详情
        $data['trip'] = $this->get_trip_info($id);
        //echo '<pre>';
        //var_dump($data['trip']);


        $data['share_title']=$h5_info['h5_title'];
        $data['share_desc']=$h5_info['share_desc'];

        $data['sta_price']=$h5_info['sta_price'];

        $data['index_url']=base_url("h5show_yl/index/$id");
        $data['index_url']= $h5_info['url_type'] ? $data['index_url'].'?type='.$h5_info['url_type'] : $data['index_url'];
        $data['shareimage']=base_url($this->User_model->get_image(17,$id));
        $data['signPackage']=$this->wx_js_para(3);

        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);

        if($h5_info['video_src']!=0)
        {
            $data['video_src']=$h5_info['video_src'];
        }


        $data['hotel_info']=$this->User_model->get_hotel_info($id);

        $data['date_choose']=json_decode($h5_info['date'],TRUE);
       
        //print_r($data['date_choose']);die;


        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);

       
         for($i=0;$i<count($data['date_choose']);$i++){
        if(is_array($data['date_choose']))
        {
//            foreach ($data['date_choose'] as $key=>$val){
//                 $date_list=array_keys($val[$i]['date']); 
//            }
            $date_list=array_keys($data['date_choose'][$i]['date']); 
            $data['date']['start_year']=date('Y', strtotime(reset($date_list)));
            $data['date']['start_month']=date('n',strtotime(reset($date_list)));
            $data['date']['start_day']=date('d',strtotime(reset($date_list)));
            $data['date']['end_year']=date('Y', strtotime(end($date_list)));
            $data['date']['end_month']=date('n', strtotime(end($date_list)));
             $data['date']['end_day']=date('d', strtotime(end($date_list)));
            // $time=$date_time[0];
            $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-'.$data['date']['start_day']);

            foreach($data['date_choose'] as $k=>$v)
            {
                $data['date_price'][date('Y-n',strtotime($k))][]=$v;
            }

          foreach($data['date_price'] as $key=>$val){
                     
               
                      }
         
            if(isset($time))
            {
               
                 
                $data['date']['cal'][]=array(
                    'year'=>date('Y',$time),
                    'month'=>date('n',$time),
                    'month_cn'=>$this->get_month_cn(date('n',$time)),
                    'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
                    'all_days'=>date('t',$time),
                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                    'min_price'=>min(($val[$i]['date'])),
                  
                    
                    
                   
                );
//            echo "<pre>";
//            print_r($val[$i]['date']);
//            echo "</pre>";die;
//            echo "</br>";
//            echo "<pre>";
//            print_r($data['date']['cal']);
//            echo "</pre>";
//            die;
//                
//                $data['sta_price_arr'][]=min($data['date_price'][date('Y-n',$time)]);
////
//                while(date('Y',$time)<date('Y',strtotime(end($date_list))) OR date('n',$time)<date('n',strtotime(end($date_list))))
//                {
//                    $data['date']['cal'][] =array(
//                        'year'=>date('Y',strtotime('+1 month', $time)),
//                        'month'=>date('n',strtotime('+1 month', $time)),
//                        'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//                        'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                        'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//                        'all_days'=>date('t',strtotime('+1 month', $time)),
//                        'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                       'min_price'=>min(($val[$i]['date'])),
//                        
//                    );
//                    $data['sta_price_arr'][]=min($data['date_price'][date('Y-n',strtotime('+1 month', $time))]);
//                    $time= strtotime('+1 month', $time);
//                    
//                }
//
//                if(count($data['date']['cal'])==1)
//                {
//                    $data['date']['cal'][]=array(
//                        'year'=>date('Y',strtotime('+1 month', $time)),
//                        'month'=>date('n',strtotime('+1 month', $time)),
//                        'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
//                        'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                        'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
//                        'all_days'=>date('t',strtotime('+1 month', $time)),
//                        'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
//                        'min_price'=>min(($val[$i]['date'])),
//                        
//                    );
//                }
//                $data['sta_price']=min($data['sta_price_arr']);
            }else{
                $data['date']['cal']=array();
            }

        }else{
            $data['date']=[];
        }
         }  


        //echo'<pre>';print_r($data['sign_to_know']);exit();
//        echo "<pre>";
//        print_r($date_choose);
//        exit;
        $this->load->view('h5/show_index',$data);
        $this->show_count();
    }


   /**
     * 游轮首页页面展示
     */

  public function youlun_index($id=0)
    {
        if(!is_numeric($id) || !$id)
        {
           redirect(base_url());
        }
        $data=[];
        //根据ID获取对应H5页面信息
        $h5_info = $this->User_model->get_select_one('*',array('h5_id'=>$id,'is_show'=>1),'v_h5_info');
        if(!$h5_info)
        {
            return false;
        }
        //获取航班信息
        //$fly_info = $this->User_model->get_select_one('*',array('h5_id'=>$id),'v_h5_fly');
        //获取行程详情
        $data['trip'] = $this->get_trip_infos($id);
        //echo '<pre>';
        //var_dump($data['trip']);


        $data['share_title']=$h5_info['h5_title'];
        $data['share_desc']=$h5_info['share_desc'];

        $data['sta_price']=$h5_info['sta_price'];

        $data['index_url']=base_url("h5show_ly/index/$id");
        $data['index_url']= $h5_info['url_type'] ? $data['index_url'].'?type='.$h5_info['url_type'] : $data['index_url'];
        $data['shareimage']=base_url($this->User_model->get_image(17,$id));
        $data['signPackage']=$this->wx_js_para(3);

        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);
      //  $where =" 'h5_id'=$id";  
      //   $sql="select  from v_h5_day where h5_id=$id";
      // $data['xingcheng']= $this->get_trip_info($id);
      // echo $this->db->last_query();
        if($h5_info['video_src']!='0')
        {
            $data['video_src']=$h5_info['video_src'];
        }



        $data['hotel_info']=$this->User_model->get_hotel_info($id);

        $data['date_choose']=json_decode($h5_info['date'],TRUE);


        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);



        if(is_array($data['date_choose']))
        {
            $date_list=array_keys($data['date_choose']);
            $data['date']['start_year']=date('Y', strtotime(reset($date_list)));
            $data['date']['start_month']=date('n',strtotime(reset($date_list)));
            $data['date']['end_year']=date('Y', strtotime(end($date_list)));
            $data['date']['end_month']=date('n', strtotime(end($date_list)));
            // $time=$date_time[0];
            $time=strtotime($data['date']['start_year'].'-'.$data['date']['start_month'].'-1');

            foreach($data['date_choose'] as $k=>$v)
            {
                $data['date_price'][date('Y-n',strtotime($k))][]=$v;
            }


            if(isset($time))
            {
                $data['date']['cal'][]=array(
                    'year'=>date('Y',$time),
                    'month'=>date('n',$time),
                    'month_cn'=>$this->get_month_cn(date('n',$time)),
                    'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                    'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
                    'all_days'=>date('t',$time),
                    'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
                    'min_price'=>min($data['date_price'][date('Y-n',$time)])
                );

                $data['sta_price_arr'][]=min($data['date_price'][date('Y-n',$time)]);
//
                while(date('Y',$time)<date('Y',strtotime(end($date_list))) OR date('n',$time)<date('n',strtotime(end($date_list))))
                {
                    $data['date']['cal'][] =array(
                        'year'=>date('Y',strtotime('+1 month', $time)),
                        'month'=>date('n',strtotime('+1 month', $time)),
                        'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
                        'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                        'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                        'all_days'=>date('t',strtotime('+1 month', $time)),
                        'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                        'min_price'=>min($data['date_price'][date('Y-n',strtotime('+1 month', $time))])
                    );
                    $data['sta_price_arr'][]=min($data['date_price'][date('Y-n',strtotime('+1 month', $time))]);
                    $time= strtotime('+1 month', $time);
                }

                if(count($data['date']['cal'])==1)
                {
                    $data['date']['cal'][]=array(
                        'year'=>date('Y',strtotime('+1 month', $time)),
                        'month'=>date('n',strtotime('+1 month', $time)),
                        'month_cn'=>$this->get_month_cn(date('n',strtotime('+1 month', $time))),
                        'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                        'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                        'all_days'=>date('t',strtotime('+1 month', $time)),
                        'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                        'min_price'=>min($data['date_price'][date('Y-n',$time)])
                    );
                }
                $data['sta_price']=min($data['sta_price_arr']);
            }else{
                $data['date']['cal']=array();
            }

        }else{
            $data['date']=[];
        }
        foreach ($data as $k=>$v){
            
            
            
        } 

        //echo "<pre>";
       
        $youlun=array();
         $youlun = $this->User_model->get_select_one('*',array('h5_id'=>$id,'is_show'=>1),'v_h5_youlun');
         $data['cabin']=  json_decode($youlun['cabin_info']);
      //   print_r($data['cabin']);
         $data['home']=  json_decode($youlun['home_details']);
         $where = array('h5_id'=>$id,'is_del'=>0);
         $data['xingcheng']= $this->User_model->get_select_all('*',$where,'day_order','ASC','v_h5_day');
          //$data['names']=  explode('**',$data['cabin']->taocan_name[0]);
          foreach($data['cabin'] as $key=>$val){
              $data['cabin'][$key]->taocan_name= explode('**',$val->taocan_name);
               $data['cabin'][$key]->taocan_sum=  explode('**',$val->taocan_sum);
                $data['cabin'][$key]->taocan_gg=  explode('**',$val->taocan_gg);
                 $data['cabin'][$key]->taocan_money=  explode('**',$val->taocan_money);
           
              
          }
       //  foreach($data['cabin'] as $k =>$v){
        //     $data['cabin']['names']=$v['taocan_name'];
      //   }
//    echo "<pre>";
//    print_r($data['home']);
//   echo "</pre>";
 //          
   //  echo'<pre>';print_r($data);exit();

        $this->load->view('youlun/index_yl',$data);
        $this->show_count();
    }
//游轮行程详情
    public function youlun_xiangqin($id=0){
       
        $data=array();
         $where = 'day_id='.$id.' AND is_del=0';
        $data = $this->User_model->get_select_one($select='*',$where,'v_h5_day');
        $data['main']=  json_decode($data['day_main']);
        $data['view']=  json_decode($data['day_view']);
        $data['hotel']=  json_decode($data['day_hotel']);
        $data['list']=  json_decode($data['day_list']);
         $data['place']=  json_decode($data['day_place']);
   
//      echo "<pre>";
//      print_r($data);
//      echo "</pre>";
         $this->load->view('youlun/day1_yl',$data); 
        
        
    }
    /**
     * 酒店详情页面
     */
    public function hotel_detail($id=0)
    {
        if(!is_numeric($id) || empty($id))
        {
           redirect(base_url());
        }
        $data=[];
        $data['hotel_info']=$this->User_model->get_hotel_one($id);
       // echo '<pre>';print_r($data);exit();

        $this->load->view('h5/hotel_detail',$data);
    }


    public function get_month_cn($month)
    {
        switch ($month)
        {
            case 1;
                return '一';
            case 2;
                return '二';
            case 3;
                return '三';
            case 4;
                return '四';
            case 5;
                return '五';
            case 6;
                return '六';
            case 7;
                return '七';
            case 8;
                return '八';
            case 9;
                return '九';
            case 10;
                return '十';
            case 11;
                return '十一';
            case 12;
                return '十二';
        }
    }

    /**
     * 行程详情页面
     */
    public function trip_detail($id=0,$day=0)
    {
        if(!is_numeric($id) || empty($id) || !is_numeric($day) || empty($day) )
        {
           redirect(base_url());
        }
        
        $this->load->view('h5/trip_detail');
    }



    //微信接口调用
    public function wx_js_para($wx_id,$url='')
    {
        $where=array('wx_id'=>$wx_id);
        $result=$this->User_model->get_select_one('app_id,app_secret',$where,'wx_acctoken_info');
        //echo $this->db->last_query();
        //echo "<pre>";print_r($result);exit();
        if($result)
        {
            $appid     = $result['app_id'];
            $secret = $result['app_secret'];
        }else{
            return false;
        }
        $timestamp = time();
        $wxnonceStr = $this->createNonceStr();
        $wxticket =  $this->wx_get_js_ticket($appid,$secret);
        if(!$wxticket)
        {
            return false;
        }
        if(empty($url))
        {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        $wxOri = "jsapi_ticket=$wxticket&noncestr=$wxnonceStr&timestamp=$timestamp&url=$url";;
        $signature = sha1($wxOri);
        $para = array(
            'appid'      => $result['app_id'],
            'timestamp'  => $timestamp,
            'wxnonceStr' => $wxnonceStr,
            'signature'  => $signature
        );

        return $para;
    }

    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public function wx_get_js_ticket($appid,$secret)
    {
        $ticket = "";
        $time = time() - 7000;
        $where=array('app_id'=>$appid);
        $ticket_info=$this->User_model->get_select_one('jsapi_ticket,jsapi_time',$where,'wx_acctoken_info');

        if(!empty($ticket_info['jsapi_ticket']) && $ticket_info['jsapi_time'] > $time){
            $ticket = $ticket_info['jsapi_ticket'];
        }else{
            $token = $this->get_actoken($appid,$secret);
            $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$token&type=jsapi";
            $jsapi_ticket = file_get_contents($url);
            $jsapi_ticket = json_decode($jsapi_ticket, true);
            if(!isset($jsapi_ticket['ticket']))
            {
                return false;
            }
            $ticket = $jsapi_ticket['ticket'];
            $jsapi_time = time();

            $data=array(
                'jsapi_ticket'=>$ticket,
                'jsapi_time'=>$jsapi_time,
            );
            $this->User_model->update_one($where,$data,'wx_acctoken_info');
        }
        return $ticket;
    }

    public function get_actoken($appid,$secret)
    {
        $token = "";
        $where=array('app_id'=>$appid);
        $token_info=$this->User_model->get_select_one('access_token,access_time',$where,'wx_acctoken_info');
        if(!empty($token_info)){
            $time = time() - 7000;
            if($token_info['access_time'] > $time && !empty($token_info['access_token'])){
                $token = $token_info['access_token'];
            }else{
                $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                $acc_token = file_get_contents($url);
                $acc_token = json_decode($acc_token, true);
                if(!isset($acc_token['access_token']))
                {
                    return FALSE;
                }
                $token = $acc_token['access_token'];
                $acc_time = time();
                $data=array(
                    'access_token'=>$token,
                    'access_time'=>$acc_time,
                );
                $this->User_model->update_one($where,$data,'wx_acctoken_info');
            }
        }else{
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $acc_token = file_get_contents($url);
            $acc_token = json_decode($acc_token, true);
            $token = $acc_token['access_token'];
            //$acc_time = time();
            //$GLOBALS['db']->query("INSERT INTO wx_acc_token SET access_token='$token', access_time='$acc_time' ");
        }
        return $token;
    }

    //获取行程详情
    function get_trip_info($id)
    {
        $result = array();
        $where = array('h5_id'=>$id,'is_del'=>0);
        $day_info = $this->User_model->get_select_all('*',$where,'day_order','ASC','v_h5_day');
        if($day_info)
        {
            foreach ($day_info as $value)
            {
                $fly_info = array();
                if($value['day_fly'])
                {
                    $fly_info = $this->get_fly_info($id,$value['day_id']);
                }
                $day_main = json_decode($value['day_main']);
                $result[] = array(
                            'day_order' =>  $value['day_order'],
                            'day_fly' =>  $value['day_fly'],
                            'day_main_title' =>  $day_main->day_main_title,
                            'day_main_route' =>  $this->get_main_route($day_main->day_main_route),
                            'day_main_image' =>  $day_main->day_main_image,
                            'day_url' =>  base_url('h5show/show/'.$value['day_id']),
                            'fly_info' =>  $fly_info
                    );
            }
        }
        return $result;
    }
       //获取游轮行程详情
    function get_trip_infos($id)
    {
        $result = array();
        $where = array('h5_id'=>$id,'is_del'=>0);
        $day_info = $this->User_model->get_select_all('*',$where,'day_order','ASC','v_h5_day');
        if($day_info)
        {
            foreach ($day_info as $value)
            {
                $fly_info = array();
                if($value['day_fly'])
                {
                    $fly_info = $this->get_fly_info($id,$value['day_id']);
                }
                $day_main = json_decode($value['day_main']);
                $result[] = array(
                            'day_order' =>  $value['day_order'],
                            'day_fly' =>  $value['day_fly'],
                            'day_main_title' =>  $day_main->day_main_title,
                            'day_main_route' =>  $this->get_main_route($day_main->day_main_route),
                            'day_main_image' =>  $day_main->day_main_image,
                            'day_url' =>  base_url('h5show_yl/youlun_xiangqin/'.$value['day_id']),
                            'fly_info' =>  $fly_info
                    );
            }
        }
        return $result;
    }

    //获取航班详情
    function get_fly_info($id,$day_id)
    {
        $result = array();
        $where = array('h5_id'=>$id,'day_id'=>$day_id);
        $result=$this->User_model->get_select_all('*',$where,'fly_id','ASC','v_h5_fly');
        if($result)
        {
            foreach ($result as $k => $v) {
                $result[$k]['fly_start_time'] = date('H:i',$v['fly_start_time']);
                $result[$k]['fly_end_time'] = date('H:i',$v['fly_end_time']);
                //飞行时间
                $times = intval($v['fly_end_time']) - intval($v['fly_start_time']);
                $result[$k]['fly_time'] = $this->return_hm($times);
                //机场信息
                $start = explode('**', $v['fly_start_place']);
                $end = explode('**', $v['fly_end_place']);
                $result[$k]['start_place'] = isset($start[0]) ? $start[0] : '';
                $result[$k]['start_airport'] = isset($start[1]) ? $start[1] : '';
                $result[$k]['end_place'] = isset($end[0]) ? $end[0] : '';
                $result[$k]['end_airport'] = isset($end[1]) ? $end[1] : '';
            }
        }
        return $result;
    }

    //获取每日行程路线
    function get_main_route($data)
    {
        $result = array();
        if($data)
        {
            $route = explode('**', str_replace('#', '<br/>',$data));
            $cnt = count($route);
            if($cnt == 0)
            {
                return $result;
            }
            if($cnt > 0 && $cnt <= 3)
            {
                $result['step'] = 'three';
            }
            elseif($cnt == 5)
            {
                $result['step'] = 'fives';
            }
            else
            {
                $result['step'] = 'four';
            }
            if($cnt<=5)
            {
                for($i=0;$i<$cnt;$i++)
                {
                    if($i==0)
                    {
                        $result['place'][$i]['class'] = 'first star';
                    }elseif($i==$cnt-1)
                    {
                        $result['place'][$i]['class'] = 'end';
                    }else
                    {
                        $result['place'][$i]['class'] = '';
                    }
                    $result['place'][$i]['p'] = 0;
                    $result['place'][$i]['value'] = $route[$i];
                }
            }else{
                for($i=0;$i<8;$i++)
                {
                    $result['place'][$i]['value'] = $i<4 ? $route[$i] : (isset($route[11-$i]) ? $route[11-$i] : '');
                    if($i==0)
                    {
                        $result['place'][$i]['class'] = 'first star';
                    }elseif($i==3)
                    {
                        $result['place'][$i]['class'] = 'end rightline';
                    }elseif($i==7)
                    {
                        $result['place'][$i]['class'] = 'end';
                    }elseif($i==12-$cnt)
                    {
                        $result['place'][$i]['class'] = 'last';
                    }else{
                        $result['place'][$i]['class'] = '';
                    }
                    $result['place'][$i]['p'] = $i<4 ? 0 : 1;
                }

            }
        }
        return $result;
    }

    function return_hm($time='21600')
    {

        $h_num=intval($time/3600);
        $m_num=intval(($time-$h_num*3600)/60);
        $result = $h_num ? $h_num.'小时' : '';
        $result .= $m_num ? $m_num.'分' : '';
        return $result;
    }
}
