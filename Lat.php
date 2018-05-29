<?php
/**
 * 展示页合集
 * User: xiaohei
 * Date: 2017/5/8
 * Time: 12:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Lat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->load->model('User_model');
        $this->load->helper('url');

        $this->shareimage_foryx='/public/gzg/images/gzg_share.jpg';
        $this->shareimage_forlx='/public/gzg/images/gzg_share.jpg';
        $this->shareimage_forcx='/public/gzg/images/gzg_share.jpg';
        $this->shareimage_forcl='http://api.etjourney.com//public/cl/images/share.jpg';

    }


    public function  show_share_count()
    {
        $data['info']=$this->User_model->get_select_all($select='*',$where='1=1',$order_title='id',$order='ASC',$table='v_h5_share');
        $new=[];
        foreach($data['info'] as  $k=>$v)
        {
            $new[$k]['分享标题']=$v['title'];
            $new[$k]['分享地址']=$v['url'];
            $new[$k]['分享次数']=$v['count'];
        }
       echo'<pre>';
        print_r($new);
    }
   
   
   
    //新加坡4晚5天半自由行
    public function xjp4n5dinfo()
    {

        $data=$this->get_day_url('xjp4n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='新加坡4晚5天半自由行';
        $data['share_desc']='新加坡4晚5天半自由行';
        $data['index_url']=base_url('lat/xjp4n5dinfo/KLJQ-XJP-4N5D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xjp4n5d/images/share.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xjp4n5d/index',$data);
        $this->show_count();
    }

    //4晚5天 新加坡自由行
    public function xjpzyxinfo()
    {

        $data=$this->get_day_url('xjpzyx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='4晚5天 新加坡自由行';
        $data['share_desc']='4晚5天 新加坡自由行';
        $data['index_url']=base_url('lat/xjpzyxinfo/KLJQ-XJP-ZYX');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xjpzyx/images/share.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xjpzyx/index',$data);
        $this->show_count();
    }
    
    //10-2.3【国庆半自助】6天 七彩南湾深度文青之旅6日 个签2人发班
    public function qcnwinfo()
    {

        $data=$this->get_day_url('qcnw');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['share_desc']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['index_url']=base_url('lat/qcnwinfo/SH-QCNW');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/qcnw/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('qcnw/index',$data);
        $this->show_count();
    }





    public function share_count()
    {
        $data['title']=$this->input->post('title',TRUE);
        $data['url']=$this->input->post('url',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['update_time']=time();

       $rs=$this->User_model->get_select_one('*',array('title'=>$data['title']),'v_h5_share');

        if($rs)
        {
            $this->User_model->amount_update("count","count+1",array('title'=>$data['title']),'v_h5_share');
        }
        else
        {

            $data['count']=1;
           // print_r($data);exit();
            //print_r($data);
           $this->User_model->user_insert('v_h5_share',$data);
        }

    }

//commo
    public function get_day_url($type)
    {
        $data['day1']=base_url("lat/day_info/1/$type");
        $data['day2']=base_url("lat/day_info/2/$type");
        $data['day3']=base_url("lat/day_info/3/$type");
        $data['day4']=base_url("lat/day_info/4/$type");
        $data['day5']=base_url("lat/day_info/5/$type");
        $data['day6']=base_url("lat/day_info/6/$type");
        $data['day7']=base_url("lat/day_info/7/$type");
        $data['day8']=base_url("lat/day_info/8/$type");
        $data['day9']=base_url("lat/day_info/9/$type");
        $data['day10']=base_url("lat/day_info/10/$type");
        $data['day11']=base_url("lat/day_info/11/$type");
        $data['day12']=base_url("lat/day_info/12/$type");
        $data['day13']=base_url("lat/day_info/13/$type");
        $data['day14']=base_url("lat/day_info/14/$type");
        
        $data['day15']=base_url("lat/day_info/15/$type");
        $data['day16']=base_url("lat/day_info/16/$type");
        $data['day17']=base_url("lat/day_info/17/$type");
        $data['day18']=base_url("lat/day_info/18/$type");
        $data['day19']=base_url("lat/day_info/19/$type");
        $data['day20']=base_url("lat/day_info/20/$type");
        $data['day21']=base_url("lat/day_info/21/$type");
        $data['day22']=base_url("lat/day_info/22/$type");
        $data['day23']=base_url("lat/day_info/23/$type");
        $data['day24']=base_url("lat/day_info/24/$type");

        $data['day25']=base_url("lat/day_info/25/$type");

        $data['day26']=base_url("lat/day_info/26/$type");
        $data['day27']=base_url("lat/day_info/27/$type");
        $data['day28']=base_url("lat/day_info/28/$type");
        $data['day29']=base_url("lat/day_info/29/$type");
        $data['day30']=base_url("lat/day_info/30/$type");

        $data['local']=base_url("lat/hotel/local/$type");
        $data['local2']=base_url("lat/hotel/local2/$type");
        $data['inter']=base_url("lat/hotel/inter/$type");
        $data['hotel1']=base_url("lat/hotel/hotel1/$type");
        $data['hotel2']=base_url("lat/hotel/hotel2/$type");
        $data['hotel3']=base_url("lat/hotel/hotel3/$type");
        $data['hotel4']=base_url("lat/hotel/hotel4/$type");
        $data['hotel5']=base_url("lat/hotel/hotel5/$type");

        $data['hotel6']=base_url("lyt/hotel/hotel6/$type");
        $data['hotel7']=base_url("lyt/hotel/hotel7/$type");
        $data['hotel8']=base_url("lyt/hotel/hotel8/$type");
        $data['hotel9']=base_url("lyt/hotel/hotel9/$type");

        $data['stroke1']=base_url("lat/stroke_info/1/$type");
        $data['stroke2']=base_url("lat/stroke_info/2/$type");
        $data['stroke3']=base_url("lat/stroke_info/3/$type");
        $data['stroke4']=base_url("lat/stroke_info/4/$type");
        $data['stroke5']=base_url("lat/stroke_info/5/$type");
        $data['stroke6']=base_url("lat/stroke_info/6/$type");
        $data['stroke7']=base_url("lat/stroke_info/7/$type");
        $data['stroke8']=base_url("lat/stroke_info/8/$type");
        $data['stroke9']=base_url("lat/stroke_info/9/$type");
        $data['stroke10']=base_url("lat/stroke_info/10/$type");
        $data['stroke11']=base_url("lat/stroke_info/11/$type");
        $data['stroke12']=base_url("lat/stroke_info/12/$type");
        $data['stroke13']=base_url("lat/stroke_info/13/$type");
        return $data;
    }
  
  
    //纯享 $type cx 纯享 yx 悦享用
    public function day_info($day,$type='cx')
    {
        $data=[];

            $this->load->view("$type/day{$day}",$data);
            $this->show_count();

    }
    // public function day_info($day,$type='cx')
    // {
    //     //$data=[];
    //     $data=$this->get_day_url($type);
    //     if(!in_array($type,array('sqbjd','cx','yx','lx','cl')))
    //     {
    //         //return false;
    //     }
    //     switch($day)
    //     {
    //         case 1:
    //             $this->load->view("$type/day1",$data);
    //             break;
    //         case 2:
    //             $this->load->view("$type/day2",$data);
    //             break;
    //         case 3:
    //             $this->load->view("$type/day3",$data);
    //             break;
    //         case 4:
    //             $this->load->view("$type/day4",$data);
    //             break;
    //         case 5:
    //             $this->load->view("$type/day5",$data);
    //             break;
    //         case 6:
    //             $this->load->view("$type/day6",$data);
    //             break;
    //         case 7:
    //             $this->load->view("$type/day7",$data);
    //             break;
    //         case 8:
    //             $this->load->view("$type/day8",$data);
    //             break;
    //         case 9:
    //             $this->load->view("$type/day9",$data);
    //             break;
    //         case 10:
    //             $this->load->view("$type/day10",$data);
    //             break;
    //         case 11:
    //             $this->load->view("$type/day11",$data);
    //             break;
    //         case 12:
    //             $this->load->view("$type/day12",$data);
    //             break;
    //         case 13:
    //             $this->load->view("$type/day13",$data);
    //             break;
    //         case 14:
    //             $this->load->view("$type/day14",$data);
    //             break;
    //         case 15:
    //             $this->load->view("$type/day15",$data);
    //             break;
    //         case 16:
    //             $this->load->view("$type/day16",$data);
    //             break;
    //         case 17:
    //             $this->load->view("$type/day17",$data);
    //             break;
    //         case 18:
    //             $this->load->view("$type/day18",$data);
    //             break;
    //         case 19:
    //             $this->load->view("$type/day19",$data);
    //             break;
    //         case 20:
    //             $this->load->view("$type/day20",$data);
    //             break;
    //         case 21:
    //             $this->load->view("$type/day21",$data);
    //             break;
    //         case 22:
    //             $this->load->view("$type/day22",$data);
    //             break;
    //         case 23:
    //             $this->load->view("$type/day23",$data);
    //             break;
    //         case 24:
    //             $this->load->view("$type/day24",$data);
    //             break;
    //         case 25:
    //             $this->load->view("$type/day25",$data);
    //             break;
    //         case 26:
    //             $this->load->view("$type/day26",$data);
    //             break;
    //         case 27:
    //             $this->load->view("$type/day27",$data);
    //             break;
    //         case 28:
    //             $this->load->view("$type/day28",$data);
    //             break;
    //         case 29:
    //             $this->load->view("$type/day29",$data);
    //             break;
    //         case 30:
    //             $this->load->view("$type/day30",$data);
    //             break;
    //         default:
    //             return false;
    //     }
    //     $this->show_count();

    // }

public function stroke_info($stroke,$type='qingmai')
    {
        $data=[];
        
        switch($stroke)
        {
            case 1:
                $this->load->view("$type/stroke1",$data);
                break;
            case 2:
                $this->load->view("$type/stroke2",$data);
                break;
            case 3:
                $this->load->view("$type/stroke3",$data);
                break;
            case 4:
                $this->load->view("$type/stroke4",$data);
                break;
            case 5:
                $this->load->view("$type/stroke5",$data);
                break;
            case 6:
                $this->load->view("$type/stroke6",$data);
                break;
            case 7:
                $this->load->view("$type/stroke7",$data);
                break;
            case 8:
                $this->load->view("$type/stroke8",$data);
                break;
            case 9:
                $this->load->view("$type/stroke9",$data);
                break;
            case 10:
                $this->load->view("$type/stroke10",$data);
                break;
            case 11:
                $this->load->view("$type/stroke11",$data);
                break;
            case 12:
                $this->load->view("$type/stroke12",$data);
                break;
            case 13:
                $this->load->view("$type/stroke13",$data);
                break;
            default:
                return false;
        }
        $this->show_count();

    }



        //$type local inter   $for cx yx
    public function hotel($type,$for)
    {
        $data=[];
        if(!in_array($type,array('local','inter')) OR !in_array($for,array('cx','yx','lx')))
        {
            //return false;
        }

        $this->load->view("$for/$type",$data);
        $this->show_count();
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
}