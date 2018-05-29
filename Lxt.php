<?php
/**
 * 展示页合集
 * User: xiaohei
 * Date: 2017/5/8
 * Time: 12:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Lxt extends CI_Controller
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




  //东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）
   public function xj6dinfo()
   {

       $data=$this->get_day_url('xj6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）';
       $data['share_desc']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）';
       $data['index_url']=base_url('lxt/xj6dinfo/RHHK-xj6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/xj6d/images/share.jpg";

       $this->load->view('xj6d/index',$data);
       $this->show_count();
   } 

  //东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）
   public function xj6d1info()
   {

       $data=$this->get_day_url('xj6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）';
       $data['share_desc']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城6日深度游小包团（奔驰商务车+含丝路秀）';
       $data['index_url']=base_url('lxt/xj6d1info/RHHK-xj6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/xj6d/images/share1.jpg";

       $this->load->view('xj6d/index1',$data);
       $this->show_count();
   } 

  //2017新品呈现回首台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】
   public function hstw8dinfo()
   {

       $data=$this->get_day_url('hstw8d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='2017新品呈现回首台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】';
       $data['share_desc']='2017新品呈现回首台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】';
       $data['index_url']=base_url('lxt/hstw8dinfo/SH-hstw8d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/hstw8d/images/share.jpg";

       $this->load->view('hstw8d/index',$data);
       $this->show_count();
   } 

  //2017新品呈现初见台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】
   public function cjtw8dinfo()
   {

       $data=$this->get_day_url('cjtw8d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='2017新品呈现初见台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】';
       $data['share_desc']='2017新品呈现初见台湾-【纯玩】台湾环岛8天 （直飞）  【台北进出】';
       $data['index_url']=base_url('lxt/cjtw8dinfo/SH-cjtw8d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/cjtw8d/images/share.jpg";

       $this->load->view('cjtw8d/index',$data);
       $this->show_count();
   }       

  // 美西深度+旧金山十七里湾+秘境羚羊彩穴+南峡 10天纯美
   public function cmmx10dinfo()
   {

       $data=$this->get_day_url('cmmx10d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美西深度+旧金山十七里湾+秘境羚羊彩穴+南峡 10天纯美';
       $data['share_desc']='美西深度+旧金山十七里湾+秘境羚羊彩穴+南峡 10天纯美';
       $data['index_url']=base_url('lxt/cmmx10dinfo/MY-cmmx10d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/cmmx10d/images/share.jpg";

       $this->load->view('cmmx10d/index',$data);
       $this->show_count();
   } 

  // 美西深度+旧金山 10天惠美
   public function hmmx10dinfo()
   {

       $data=$this->get_day_url('hmmx10d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美西深度+旧金山 10天惠美';
       $data['share_desc']='美西深度+旧金山 10天惠美';
       $data['index_url']=base_url('lxt/hmmx10dinfo/MY-hmmx10d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/hmmx10d/images/share.jpg";

       $this->load->view('hmmx10d/index',$data);
       $this->show_count();
   }    
  //沙巴4晚6天半自由行
   public function sb_4n6dinfo()
   {

       $data=$this->get_day_url('sb_4n6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='沙巴4晚6天半自由行';
       $data['share_desc']='沙巴4晚6天半自由行';
       $data['index_url']=base_url('lxt/sb_4n6dinfo/KLJQ-sb_4n6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/sb_4n6d/images/share.jpg";

       $this->load->view('sb_4n6d/index',$data);
       $this->show_count();
   }   
  //神秘国度-汶莱4晚5天特色游
   public function wltsy4n5dinfo()
   {

       $data=$this->get_day_url('wltsy4n5d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='神秘国度-汶莱4晚5天特色游';
       $data['share_desc']='神秘国度-汶莱4晚5天特色游';
       $data['index_url']=base_url('lxt/wltsy4n5dinfo/KLJQ-wltsy4n5d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/wltsy4n5d/images/head.jpg";

       $this->load->view('wltsy4n5d/index',$data);
       $this->show_count();
   }   
   

  //美国东西海岸+波士顿名校12天至美
   public function mbzm12dinfo()
   {

       $data=$this->get_day_url('mbzm12d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美国东西海岸+波士顿名校12天至美';
       $data['share_desc']='美国东西海岸+波士顿名校12天至美';
       $data['index_url']=base_url('lxt/mbzm12dinfo/MY-mbzm12d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/mbzm12d/images/share.jpg";

       $this->load->view('mbzm12d/index',$data);
       $this->show_count();
   }

  //欢乐新加坡轻松度假4晚5日或6日纯玩休闲游
   public function xjpxxyinfo()
   {

       $data=$this->get_day_url('xjpxxy');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='欢乐新加坡轻松度假4晚5日或6日纯玩休闲游';
       $data['share_desc']='欢乐新加坡轻松度假4晚5日或6日纯玩休闲游';
       $data['index_url']=base_url('lxt/xjpxxyinfo/GL-xjpxxy-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/xjpxxy/images/share.jpg";

       $this->load->view('xjpxxy/index',$data);
       $this->show_count();
   }

  //旗舰俄罗斯9日
   public function lmzy9dinfo()
   {

       $data=$this->get_day_url('lmzy9d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='旗舰俄罗斯9日';
       $data['share_desc']='旗舰俄罗斯9日';
       $data['index_url']=base_url('lxt/lmzy9dinfo/HY-lmzy9d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/lmzy9d/images/head.jpg";

       $this->load->view('lmzy9d/index',$data);
       $this->show_count();
   }


  //菲律宾长滩跟团游
   public function ct_5_7info()
   {

       $data=$this->get_day_url('ct_5_7');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='菲律宾长滩跟团游';
       $data['share_desc']='菲律宾长滩跟团游';
       $data['index_url']=base_url('lxt/ct_5_7info/KLJQ-ct_5_7-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/ct_5_7/images/share.jpg";

       $this->load->view('ct_5_7/index',$data);
       $this->show_count();
   }

  //6人成行-曼谷+华欣 5晚6天游
   public function mghx6dinfo()
   {

       $data=$this->get_day_url('mghx6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='6人成行-曼谷+华欣 5晚6天游';
       $data['share_desc']='6人成行-曼谷+华欣 5晚6天游';
       $data['index_url']=base_url('lxt/mghx6dinfo/MY-mghx6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/images/H5image/031c4bd538756d10da724ea0d5d51748.jpg";
// /public/images/H5image/031c4bd538756d10da724ea0d5d51748.jpg
       $this->load->view('mghx6d/index',$data);
       $this->show_count();
   }


  //美东西12天惊爆团
   public function mdxjbt12dinfo()
   {

       $data=$this->get_day_url('mdxjbt12d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美东西12天惊爆团';
       $data['share_desc']='美东西12天惊爆团';
       $data['index_url']=base_url('lxt/mdxjbt12dinfo/MY-mdxjbt12d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/mdxjbt12d/images/share.jpg";

       $this->load->view('mdxjbt12d/index',$data);
       $this->show_count();
   }
  //美国东岸一地9天惊爆团
   public function mdjbt9dinfo()
   {

       $data=$this->get_day_url('mdjbt9d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美国东岸一地9天惊爆团';
       $data['share_desc']='美国东岸一地9天惊爆团';
       $data['index_url']=base_url('lxt/mdjbt9dinfo/MY-mdjbt9d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/mdjbt9d/images/share.jpg";

       $this->load->view('mdjbt9d/index',$data);
       $this->show_count();
   }
  //日本 关西深度6日（无自费）
   public function gx6dinfo()
   {

       $data=$this->get_day_url('gx6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='日本 关西深度6日（无自费）';
       $data['share_desc']='日本 关西深度6日（无自费）';
       $data['index_url']=base_url('lxt/gx6dinfo/KLJQ-gx6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/gx6d/images/share.jpg";

       $this->load->view('gx6d/index',$data);
       $this->show_count();
   }         
    
    //Pure100%--新西兰南北岛冰河农庄8日之旅
    public function bhnz8dinfo()
    {

        $data=$this->get_day_url('bhnz8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='Pure100%--新西兰南北岛冰河农庄8日之旅';
        $data['share_desc']='Pure100%--新西兰南北岛冰河农庄8日之旅';
        $data['index_url']=base_url('lxt/bhnz8dinfo/SH-bhnz8d-ZF');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bhnz8d/images/head.jpg";
        $this->load->view('bhnz8d/index',$data);
        $this->show_count();
    }

    //Pure100%---新西兰南北岛11日直升机农庄之旅
    public function xxlnbd_zsj_11dinfo()
    {

        $data=$this->get_day_url('xxlnbd_zsj_11d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='Pure100%---新西兰南北岛11日直升机农庄之旅';
        $data['share_desc']='Pure100%---新西兰南北岛11日直升机农庄之旅';
        $data['index_url']=base_url('lxt/xxlnbd_zsj_11dinfo/SH-xxlnbd_zsj_11d-ZF');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xxlnbd_zsj_11d/images/head.jpg";
        $this->load->view('xxlnbd_zsj_11d/index',$data);
        $this->show_count();
    }

    //经典版 台湾环岛8天 （直飞）  【台北进出，晚去晚回】
        public function hy8dinfo()
        {

            $data=$this->get_day_url('hy8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='经典版 台湾环岛8天 （直飞）  【台北进出，晚去晚回】';
            $data['share_desc']='经典版 台湾环岛8天 （直飞）  【台北进出，晚去晚回】';
            $data['index_url']=base_url('lxt/hy8dinfo/SH-hy8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/hy8d/images/share.jpg";
            $this->load->view('hy8d/index',$data);
            $this->show_count();
        }

  //【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】
   public function xy6dinfo()
   {

       $data=$this->get_day_url('xy6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】';
       $data['share_desc']='【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】';
       $data['index_url']=base_url('lxt/xy6dinfo/SH-xy6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/xy6d/images/share.jpg";

       $this->load->view('xy6d/index',$data);
       $this->show_count();
   }        

  //优选版 台湾西游记6天 （直飞）  【高进北出】
   public function yxxy6dinfo()
   {

       $data=$this->get_day_url('yxxy6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='优选版 台湾西游记6天 （直飞）  【高进北出】';
       $data['share_desc']='优选版 台湾西游记6天 （直飞）  【高进北出】';
       $data['index_url']=base_url('lxt/yxxy6dinfo/SH-yxxy6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/yxxy6d/images/share.jpg";

       $this->load->view('yxxy6d/index',$data);
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
        $data['day1']=base_url("lxt/day_info/1/$type");
        $data['day2']=base_url("lxt/day_info/2/$type");
        $data['day3']=base_url("lxt/day_info/3/$type");
        $data['day4']=base_url("lxt/day_info/4/$type");
        $data['day5']=base_url("lxt/day_info/5/$type");
        $data['day6']=base_url("lxt/day_info/6/$type");
        $data['day7']=base_url("lxt/day_info/7/$type");
        $data['day8']=base_url("lxt/day_info/8/$type");
        $data['day9']=base_url("lxt/day_info/9/$type");
        $data['day10']=base_url("lxt/day_info/10/$type");
        $data['day11']=base_url("lxt/day_info/11/$type");
        $data['day12']=base_url("lxt/day_info/12/$type");
        $data['day13']=base_url("lxt/day_info/13/$type");
        $data['day14']=base_url("lxt/day_info/14/$type");
        
        $data['day15']=base_url("lxt/day_info/15/$type");
        $data['day16']=base_url("lxt/day_info/16/$type");
        $data['day17']=base_url("lxt/day_info/17/$type");
        $data['day18']=base_url("lxt/day_info/18/$type");
        $data['day19']=base_url("lxt/day_info/19/$type");
        $data['day20']=base_url("lxt/day_info/20/$type");
        $data['day21']=base_url("lxt/day_info/21/$type");
        $data['day22']=base_url("lxt/day_info/22/$type");
        $data['day23']=base_url("lxt/day_info/23/$type");
        $data['day24']=base_url("lxt/day_info/24/$type");

        $data['day25']=base_url("lxt/day_info/25/$type");

        $data['day26']=base_url("lxt/day_info/26/$type");
        $data['day27']=base_url("lxt/day_info/27/$type");
        $data['day28']=base_url("lxt/day_info/28/$type");
        $data['day29']=base_url("lxt/day_info/29/$type");
        $data['day30']=base_url("lxt/day_info/30/$type");

        $data['day31']=base_url("lxt/day_info/31/$type");
        $data['day32']=base_url("lxt/day_info/32/$type");
        $data['day33']=base_url("lxt/day_info/33/$type");
        $data['day34']=base_url("lxt/day_info/34/$type");
        $data['day35']=base_url("lxt/day_info/35/$type");
        $data['day36']=base_url("lxt/day_info/36/$type");
        $data['day37']=base_url("lxt/day_info/37/$type");
        $data['day38']=base_url("lxt/day_info/38/$type");
        $data['day39']=base_url("lxt/day_info/39/$type");

        $data['aircraftHotel1']=base_url("lxt/hotel/aircraftHotel1/$type");
        $data['aircraftHotel2']=base_url("lxt/hotel/aircraftHotel2/$type");
        $data['aircraftHotel3']=base_url("lxt/hotel/aircraftHotel3/$type");
        $data['aircraftHotel4']=base_url("lxt/hotel/aircraftHotel4/$type");
        $data['aircraftHotel5']=base_url("lxt/hotel/aircraftHotel5/$type");

        $data['local']=base_url("lxt/hotel/local/$type");
        $data['local2']=base_url("lxt/hotel/local2/$type");
        $data['inter']=base_url("lxt/hotel/inter/$type");
        $data['hotel1']=base_url("lxt/hotel/hotel1/$type");
        $data['hotel2']=base_url("lxt/hotel/hotel2/$type");
        $data['hotel3']=base_url("lxt/hotel/hotel3/$type");
        $data['hotel4']=base_url("lxt/hotel/hotel4/$type");
        $data['hotel5']=base_url("lxt/hotel/hotel5/$type");

        $data['hotel6']=base_url("lxt/hotel/hotel6/$type");
        $data['hotel7']=base_url("lxt/hotel/hotel7/$type");
        $data['hotel8']=base_url("lxt/hotel/hotel8/$type");
        $data['hotel9']=base_url("lxt/hotel/hotel9/$type");

        $data['stroke1']=base_url("lxt/stroke_info/1/$type");
        $data['stroke2']=base_url("lxt/stroke_info/2/$type");
        $data['stroke3']=base_url("lxt/stroke_info/3/$type");
        $data['stroke4']=base_url("lxt/stroke_info/4/$type");
        $data['stroke5']=base_url("lxt/stroke_info/5/$type");
        $data['stroke6']=base_url("lxt/stroke_info/6/$type");
        $data['stroke7']=base_url("lxt/stroke_info/7/$type");
        $data['stroke8']=base_url("lxt/stroke_info/8/$type");
        $data['stroke9']=base_url("lxt/stroke_info/9/$type");
        $data['stroke10']=base_url("lxt/stroke_info/10/$type");
        $data['stroke11']=base_url("lxt/stroke_info/11/$type");
        $data['stroke12']=base_url("lxt/stroke_info/12/$type");
        $data['stroke13']=base_url("lxt/stroke_info/13/$type");
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