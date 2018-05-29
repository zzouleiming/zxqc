<?php
/**
 * 展示页合集
 * User: xiaohei
 * Date: 2017/5/8
 * Time: 12:15
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Lvt extends CI_Controller
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
    
    //优选版，台湾环岛8天 （直飞，台北进出）
    public function twhd_yxb_8dinfo()
    {

        $data=$this->get_day_url('twhd_yxb_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='优选版，台湾环岛8天 （直飞，台北进出）';
        $data['share_desc']='优选版，台湾环岛8天 （直飞，台北进出）';
        $data['index_url']=base_url('lvt/twhd_yxb_8dinfo/SHLY-TWYXB-8D');
        $data['shareimage']="http://api.etjourney.com/public/twhd_yxb_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('twhd_yxb_8d/index',$data);
        $this->show_count();
    }
    
    //美西海岸 旧金山十七里湾 夏威夷 羚羊彩穴 三大国家公园12天-惠 
    public function mx12dhminfo()
    {

        $data=$this->get_day_url('mx12dhm');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='美西海岸 旧金山十七里湾 夏威夷 羚羊彩穴 三大国家公园12天-惠';
        $data['share_desc']='美西海岸 旧金山十七里湾 夏威夷 羚羊彩穴 三大国家公园12天-惠';
        $data['index_url']=base_url('lvt/mx12dhminfo/MY-MXHM-12D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/mx12dhm/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('mx12dhm/index',$data);
        $this->show_count();
    }
    //美西 旧金山十七里湾 秘境羚羊彩穴 三大国家公园 夏12天-纯
    public function mx12dcminfo()
    {

        $data=$this->get_day_url('mx12dcm');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='美西 旧金山十七里湾 秘境羚羊彩穴 三大国家公园 夏12天-纯';
        $data['share_desc']='美西 旧金山十七里湾 秘境羚羊彩穴 三大国家公园 夏12天-纯';
        $data['index_url']=base_url('lvt/mx12dcminfo/MY-MXCM-12D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/mx12dcm/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('mx12dcm/index',$data);
        $this->show_count();
    }
    //本州6天岚山列车.温泉美食赏枫巡礼
    public function bz6dinfo()
    {

        $data=$this->get_day_url('bz6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='本州6天岚山列车.温泉美食赏枫巡礼';
        $data['share_desc']='本州6天岚山列车.温泉美食赏枫巡礼';
        $data['index_url']=base_url('lvt/bz6dinfo/SH-BZ-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bz6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('bz6d/index',$data);
        $this->show_count();
    }

    //10-1.2【国庆半自助】6天“清近”绿野仙踪-清境、南投、大台北6日 （直飞）个签2人成行(2)
    public function lyxzinfo()
    {

        $data=$this->get_day_url('lyxz');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【国庆半自助】“清近”绿野仙踪-清境、南投、大台北5/6日 （直飞）个签2人成行';
        $data['share_desc']='【国庆半自助】“清近”绿野仙踪-清境、南投、大台北5/6日 （直飞）个签2人成行';
        $data['index_url']=base_url('lvt/lyxzinfo/SH-LYXZ');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/lyxz/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('lyxz/index',$data);
        $this->show_count();
    }
    //10-1.2【国庆半自助】6天“清近”绿野仙踪-清境、南投、大台北6日 （直飞）个签2人成行(2)
    public function lyxz2info()
    {

        $data=$this->get_day_url('lyxz');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【国庆半自助】“清近”绿野仙踪-清境、南投、大台北5/6日 （直飞）个签2人成行';
        $data['share_desc']='【国庆半自助】“清近”绿野仙踪-清境、南投、大台北5/6日 （直飞）个签2人成行';
        $data['index_url']=base_url('lvt/lyxz2info/SH-LYXZ');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/lyxz/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('lyxz/index2',$data);
        $this->show_count();
    }

    //10-2.3【国庆半自助】6天 七彩南湾深度文青之旅6日 个签2人发班
    public function qcnwinfo()
    {

        $data=$this->get_day_url('qcnw');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['share_desc']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['index_url']=base_url('lvt/qcnwinfo/SH-QCNW');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/qcnw/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('qcnw/index',$data);
        $this->show_count();
    }
    //10-2.3【国庆半自助】6天 七彩南湾深度文青之旅6日 个签2人发班
    public function qcnw2info()
    {

        $data=$this->get_day_url('qcnw');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['share_desc']='【国庆半自助】 七彩南湾深度文青之旅6/7日 个签2人发班';
        $data['index_url']=base_url('lvt/qcnw2info/SH-QCNW');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/qcnw/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('qcnw/index2',$data);
        $this->show_count();
    }

    //台湾部17年国庆自由行计划(0725）
    public function twzyxinfo()
    {

        $data=$this->get_day_url('twzyx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='台湾部17年国庆自由行计划';
        $data['share_desc']='台湾部17年国庆自由行计划';
        $data['index_url']=base_url('lvt/twzyxinfo/SH-TWZYX');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/twzyx/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('twzyx/index',$data);
        $this->show_count();
    }
    //台湾部17年国庆自由行计划(0725）
    public function twzyx2info()
    {

        $data=$this->get_day_url('twzyx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='台湾部17年国庆自由行计划';
        $data['share_desc']='台湾部17年国庆自由行计划';
        $data['index_url']=base_url('lvt/twzyx2info/SH-TWZYX');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/twzyx/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('twzyx/index2',$data);
        $this->show_count();
    }

    //0216夏一地8天6晚MU-精品半自助
    public function xwyjp6n8dinfo()
    {

        $data=$this->get_day_url('xwyjp6n8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='夏一地8天6晚MU-精品半自助';
        $data['share_desc']='夏一地8天6晚MU-精品半自助';
        $data['index_url']=base_url('lvt/xwyjp6n8dinfo/MY-XWY-6N8D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xwyjp6n8d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xwyjp6n8d/index',$data);
        $this->show_count();
    }

    //加拿大西海岸 落基山脉 温泉体验9日游
    public function jnd9d2info()
    {

        $data=$this->get_day_url('jnd9d2');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='加拿大西海岸 落基山脉 温泉体验9日游';
        $data['share_desc']='加拿大西海岸 落基山脉 温泉体验9日游';
        $data['index_url']=base_url('lvt/jnd9d2info/SH-JND-9D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jnd9d2/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jnd9d2/index',$data);
        $this->show_count();
    }

    //杜马盖地 【机 酒 5J】5晚6日行程
    public function dmgdinfo()
    {

        $data=$this->get_day_url('dmgd');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='杜马盖地 【机 酒 5J】5晚6日行程';
        $data['share_desc']='杜马盖地 【机 酒 5J】5晚6日行程';
        $data['index_url']=base_url('lvt/dmgdinfo/SH-DMGD-5N6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/dmgd/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('dmgd/index',$data);
        $this->show_count();
    }


    //0216夏一地8天6晚MU休闲半自助游
    public function xwybzz6n8dinfo()
    {

        $data=$this->get_day_url('xwybzz6n8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='夏一地8天6晚MU休闲半自助游';
        $data['share_desc']='夏一地8天6晚MU休闲半自助游';
        $data['index_url']=base_url('lvt/xwybzz6n8dinfo/MY-XWY-6N8D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xwybzz6n8d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xwybzz6n8d/index',$data);
        $this->show_count();
    }

    //泰国+新加坡+马来西亚10日游
    public function txm10dinfo()
    {

        $data=$this->get_day_url('txm10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='泰国+新加坡+马来西亚10日游';
        $data['share_desc']='泰国+新加坡+马来西亚10日游';
        $data['index_url']=base_url('lvt/txm10dinfo/GL-TXM-10D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/txm10d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('txm10d/index',$data);
        $this->show_count();
    }


    //泰国+澳门7日游
    public function tgam7dinfo()
    {

        $data=$this->get_day_url('tgam7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='泰国+澳门7日游';
        $data['share_desc']='泰国+澳门7日游';
        $data['index_url']=base_url('lvt/tgam7dinfo/GL-TGAM-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/tgam7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('tgam7d/index',$data);
        $this->show_count();
    }

    

    //清迈+清莱+曼谷7日游
    public function qmqlmg7dinfo()
    {

        $data=$this->get_day_url('qmqlmg7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈+清莱+曼谷7日游';
        $data['share_desc']='清迈+清莱+曼谷7日游';
        $data['index_url']=base_url('lvt/qmqlmg7dinfo/GL-QMQLMG-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/qmqlmg7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('qmqlmg7d/index',$data);
        $this->show_count();
    }

    //普吉+曼谷7日游
    public function pjmg7dinfo()
    {

        $data=$this->get_day_url('pjmg7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉+曼谷7日游';
        $data['share_desc']='普吉+曼谷7日游';
        $data['index_url']=base_url('lvt/pjmg7dinfo/GL-PJMG-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/pjmg7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('pjmg7d/index',$data);
        $this->show_count();
    }


    //vip大厅
    public function vipinfo()
    {

        $data=$this->get_day_url('vip');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='vip大厅';
        $data['share_desc']='vip大厅';
        $data['index_url']=base_url('lvt/vipinfo/XJ-VIP');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/vip/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('vip/index',$data);
        $this->show_count();
    }

    //【微笑吧！缅甸】图文版 曼德勒 蒲甘 仰光5飞 6晚7日游6-5(2)
    public function md7dinfo()
    {

        $data=$this->get_day_url('md7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【微笑吧！缅甸】图文版 曼德勒 蒲甘 仰光5飞 6晚7日游6-5(2)';
        $data['share_desc']='【微笑吧！缅甸】图文版 曼德勒 蒲甘 仰光5飞 6晚7日游6-5(2)';
        $data['index_url']=base_url('lvt/md7dinfo/SH-MD-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/md7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('md7d/index',$data);
        $this->show_count();
    }

    //MU  巴厘岛5晚7天经典行程（17年）
    public function bldjd5n7dinfo()
    {

        $data=$this->get_day_url('bldjd5n7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='MU  巴厘岛5晚7天经典行程（17年）';
        $data['share_desc']='MU  巴厘岛5晚7天经典行程（17年）';
        $data['index_url']=base_url('lvt/bldjd5n7dinfo/KLJQ-BLDJD-5N7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bldjd5n7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('bldjd5n7d/index',$data);
        $this->show_count();
    }

    //MU 巴厘岛行程（精致蜜月）17年01月 五晚七天
    public function bldmy5n7dinfo()
    {

        $data=$this->get_day_url('bldmy5n7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='MU 巴厘岛行程（精致蜜月）17年01月 五晚七天';
        $data['share_desc']='MU 巴厘岛行程（精致蜜月）17年01月 五晚七天';
        $data['index_url']=base_url('lvt/bldmy5n7dinfo/KLJQ-BLDMY-5N7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bldmy5n7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('bldmy5n7d/index',$data);
        $this->show_count();
    }




    //纯美-美国东西海岸 夏威夷14天夏进纽出-MU
    public function cmmg14dinfo()
    {

        $data=$this->get_day_url('cmmg14d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯美-美国东西海岸 夏威夷14天夏进纽出-MU';
        $data['share_desc']='纯美-美国东西海岸 夏威夷14天夏进纽出-MU';
        $data['index_url']=base_url('lvt/cmmg14dinfo/SH-CM-14D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/cmmg14d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('cmmg14d/index',$data);
        $this->show_count();
    }

    //2017年7-9月澳门自由行计划
    public function amzyxinfo()
    {

        $data=$this->get_day_url('amzyx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='2017年7-9月澳门自由行计划';
        $data['share_desc']='2017年7-9月澳门自由行计划';
        $data['index_url']=base_url('lvt/amzyxinfo/SH-AM-ZYX');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/amzyx/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('amzyx/index',$data);
        $this->show_count();
    }

    //本州6日赏枫 道乐蟹双城半自由行
    public function bzsfbzy6dinfo()
    {

        $data=$this->get_day_url('bzsfbzy6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='本州6日赏枫 道乐蟹双城半自由行';
        $data['share_desc']='本州6日赏枫 道乐蟹双城半自由行';
        $data['index_url']=base_url('lvt/bzsfbzy6dinfo/SH-RBSFBZY-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bzsfbzy6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('bzsfbzy6d/index',$data);
        $this->show_count();
    }


    //13穿越秘境·真正的无人区穿越（11天）
    public function wrqcy11dinfo()
    {

        $data=$this->get_day_url('wrqcy11d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越秘境·真正的无人区穿越（11天）';
        $data['share_desc']='穿越秘境·真正的无人区穿越（11天）';
        $data['index_url']=base_url('lvt/wrqcy11dinfo/HXJT-WRQCY-11D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/wrqcy11d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('wrqcy11d/index',$data);
        $this->show_count();
    }

    //13穿越秘境·真正的无人区穿越（11天）
    public function wrqcy11d2info()
    {

        $data=$this->get_day_url('wrqcy11d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越秘境·真正的无人区穿越（11天）';
        $data['share_desc']='穿越秘境·真正的无人区穿越（11天）';
        $data['index_url']=base_url('lvt/wrqcy11d2info/HXJT-WRQCY-11D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/wrqcy11d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('wrqcy11d/index2',$data);
        $this->show_count();
    }

    //9纵横天山，极致自驾之旅（4天）
    public function zhts4dinfo()
    {

        $data=$this->get_day_url('zhts4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纵横天山，极致自驾之旅（4天）';
        $data['share_desc']='纵横天山，极致自驾之旅（4天）';
        $data['index_url']=base_url('lvt/zhts4dinfo/HXJT-ZHTS-4D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/zhts4d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('zhts4d/index',$data);
        $this->show_count();
    }
    //9纵横天山，极致自驾之旅（4天）
    public function zhts4d2info()
    {

        $data=$this->get_day_url('zhts4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纵横天山，极致自驾之旅（4天）';
        $data['share_desc']='纵横天山，极致自驾之旅（4天）';
        $data['index_url']=base_url('lvt/zhts4d2info/HXJT-ZHTS-4D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/zhts4d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('zhts4d/index2',$data);
        $this->show_count();
    }



    //15醉美大北疆（五日）
    public function zmdbj5dinfo()
    {

        $data=$this->get_day_url('zmdbj5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='醉美大北疆（五日）';
        $data['share_desc']='醉美大北疆（五日）';
        $data['index_url']=base_url('lvt/zmdbj5dinfo/HXJT-ZMDBJ-5D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/zmdbj5d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('zmdbj5d/index',$data);
        $this->show_count();
    }

    //15醉美大北疆（五日）
    public function zmdbj5d2info()
    {

        $data=$this->get_day_url('zmdbj5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='醉美大北疆（五日）';
        $data['share_desc']='醉美大北疆（五日）';
        $data['index_url']=base_url('lvt/zmdbj5d2info/HXJT-ZMDBJ-5D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/zmdbj5d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('zmdbj5d/index2',$data);
        $this->show_count();
    }

    //17生命禁区阿尔金山（6天）
    public function aejs6dinfo()
    {

        $data=$this->get_day_url('aejs6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='生命禁区阿尔金山（6天）';
        $data['share_desc']='生命禁区阿尔金山（6天）';
        $data['index_url']=base_url('lvt/aejs6dinfo/HXJT-AEJS-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/aejs6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('aejs6d/index',$data);
        $this->show_count();
    }

    //17生命禁区阿尔金山（6天）
    public function aejs6d2info()
    {

        $data=$this->get_day_url('aejs6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='生命禁区阿尔金山（6天）';
        $data['share_desc']='生命禁区阿尔金山（6天）';
        $data['index_url']=base_url('lvt/aejs6d2info/HXJT-AEJS-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/aejs6d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('aejs6d/index2',$data);
        $this->show_count();
    }

    //穿越准噶尔—千里画廊（10天）
    public function cyzge10dinfo()
    {

        $data=$this->get_day_url('cyzge10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越准噶尔—千里画廊（10天）';
        $data['share_desc']='穿越准噶尔—千里画廊（10天）';
        $data['index_url']=base_url('lvt/cyzge10dinfo/HXJT-CYZGE-10D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/cyzge10d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('cyzge10d/index',$data);
        $this->show_count();
    }

    //穿越准噶尔—千里画廊（10天）
    public function cyzge10d2info()
    {

        $data=$this->get_day_url('cyzge10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越准噶尔—千里画廊（10天）';
        $data['share_desc']='穿越准噶尔—千里画廊（10天）';
        $data['index_url']=base_url('lvt/cyzge10d2info/HXJT-CYZGE-10D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/cyzge10d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('cyzge10d/index2',$data);
        $this->show_count();
    }

    //南疆深度文化游
    public function njsdwhy13dinfo()
    {

        $data=$this->get_day_url('njsdwhy13d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='南疆深度文化游';
        $data['share_desc']='南疆深度文化游';
        $data['index_url']=base_url('lvt/njsdwhy13dinfo/RH-NJSHWHY-13D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/njsdwhy13d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('njsdwhy13d/index2',$data);
        $this->show_count();
    }

    //穿越秘境——大海道（4天）
    public function cymj4dinfo()
    {

        $data=$this->get_day_url('cymj4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越秘境——大海道（4天）';
        $data['share_desc']='穿越秘境——大海道（4天）';
        $data['index_url']=base_url('lvt/cymj4dinfo/HXJT-CYMJ-4D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/cymj4d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('cymj4d/index',$data);
        $this->show_count();
    }

    //穿越秘境——大海道（4天）
    public function cymj4d2info()
    {

        $data=$this->get_day_url('cymj4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='穿越秘境——大海道（4天）';
        $data['share_desc']='穿越秘境——大海道（4天）';
        $data['index_url']=base_url('lvt/cymj4d2info/HXJT-CYMJ-4D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/cymj4d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('cymj4d/index2',$data);
        $this->show_count();
    }

    //开启冬季喀纳斯冰封自驾之旅（7天）
    public function knszj7dinfo()
    {

        $data=$this->get_day_url('knszj7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='开启冬季喀纳斯冰封自驾之旅（7天）';
        $data['share_desc']='开启冬季喀纳斯冰封自驾之旅（7天）';
        $data['index_url']=base_url('lvt/knszj7dinfo/HXJT-KNSZJ-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/knszj7d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('knszj7d/index',$data);
        $this->show_count();
    }

    //开启冬季喀纳斯冰封自驾之旅（7天）
    public function knszj7d2info()
    {

        $data=$this->get_day_url('knszj7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='开启冬季喀纳斯冰封自驾之旅（7天）';
        $data['share_desc']='开启冬季喀纳斯冰封自驾之旅（7天）';
        $data['index_url']=base_url('lvt/knszj7d2info/HXJT-KNSZJ-7D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/knszj7d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('knszj7d/index2',$data);
        $this->show_count();
    }

    //一路向北——东方小瑞士，醉氧慎入（14天）
    public function dfxrsinfo()
    {

        $data=$this->get_day_url('dfxrs');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='一路向北——东方小瑞士，醉氧慎入（14天）';
        $data['share_desc']='一路向北——东方小瑞士，醉氧慎入（14天）';
        $data['index_url']=base_url('lvt/dfxrsinfo/HXJT-DFXRS-14D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/dfxrs/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('dfxrs/index',$data);
        $this->show_count();
    }

    //一路向北——东方小瑞士，醉氧慎入（14天）
    public function dfxrs2info()
    {

        $data=$this->get_day_url('dfxrs');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='一路向北——东方小瑞士，醉氧慎入（14天）';
        $data['share_desc']='一路向北——东方小瑞士，醉氧慎入（14天）';
        $data['index_url']=base_url('lvt/dfxrs2info/HXJT-DFXRS-14D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/dfxrs/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('dfxrs/index2',$data);
        $this->show_count();
    }

    //轻奢私家团--法意深度8晚10天
    public function fy8n10dinfo()
    {

        $data=$this->get_day_url('fy8n10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='轻奢私家团--法意深度8晚10天';
        $data['share_desc']='轻奢私家团--法意深度8晚10天';
        $data['index_url']=base_url('lvt/fy8n10dinfo/JFWD-FY-8N10D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/fy8n10d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('fy8n10d/index',$data);
        $this->show_count();
    }

    //日本签证
    public function rbqzinfo()
    {

        $data=$this->get_day_url('rbqz');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='日本签证';
        $data['share_desc']='日本签证';
        $data['index_url']=base_url('lvt/rbqzinfo/SH-RBQZ');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/rbqz/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('rbqz/index',$data);
        $this->show_count();
    }


    //中老年东京体检六日之旅-完整版
    public function tj6dinfo()
    {

        $data=$this->get_day_url('tj6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='中老年东京体检六日之旅-完整版';
        $data['share_desc']='中老年东京体检六日之旅-完整版';
        $data['index_url']=base_url('lvt/tj6dinfo/ZY-RBDJ-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/tj6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('tj6d/index',$data);
        $this->show_count();
    }

    //轻奢私家团--德瑞意深度9晚12天
    public function dry9n12dinfo()
    {

        $data=$this->get_day_url('dry9n12d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='轻奢私家团--德瑞意深度9晚12天';
        $data['share_desc']='轻奢私家团--德瑞意深度9晚12天';
        $data['index_url']=base_url('lvt/dry9n12dinfo/JFWD-DRY-9N12D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/dry9n12d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('dry9n12d/index',$data);
        $this->show_count();
    }

    //本州6天美食+北陆赏枫之旅
    public function bzms6dinfo()
    {

        $data=$this->get_day_url('bzms6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='本州6天美食+北陆赏枫之旅';
        $data['share_desc']='本州6天美食+北陆赏枫之旅';
        $data['index_url']=base_url('lvt/bzms6dinfo/SH-BZMS-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/bzms6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('bzms6d/index',$data);
        $this->show_count();
    }

    //西域风情：天池、吐鲁番、喀达市、塔县天门大峡谷双飞8日游
    public function xyfqinfo()
    {

        $data=$this->get_day_url('xyfq');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='西域风情：天池、吐鲁番、喀达市、塔县天门大峡谷双飞8日游';
        $data['share_desc']='西域风情：天池、吐鲁番、喀达市、塔县天门大峡谷双飞8日游';
        $data['index_url']=base_url('lvt/xyfqinfo/RH-XYFQ-8D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xyfq/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xyfq/index',$data);
        $this->show_count();
    }

    //轻奢私家团--英国一地10晚12天
    public function qsyg12dinfo()
    {

        $data=$this->get_day_url('qsyg12d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='轻奢私家团--英国一地10晚12天';
        $data['share_desc']='轻奢私家团--英国一地10晚12天';
        $data['index_url']=base_url('lvt/qsyg12dinfo/JFWD-YG-12D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/qsyg12d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('qsyg12d/index',$data);
        $this->show_count();
    }

    //新加坡+民丹岛4晚6日游
    public function xjpmdd4n6dinfo()
    {

        $data=$this->get_day_url('xjpmdd4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='新加坡+民丹岛4晚6日游';
        $data['share_desc']='新加坡+民丹岛4晚6日游';
        $data['index_url']=base_url('lvt/xjpmdd4n6dinfo/SH-XJPMDD-4N6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/xjpmdd4n6d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('xjpmdd4n6d/index',$data);
        $this->show_count();
    }

    //9月份--铂金豪景--日本白川乡本州世界遗产极致6日（东京1日自由）
    public function rbbjhjinfo()
    {

        $data=$this->get_day_url('rbbjhj');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='9月份--铂金豪景--日本白川乡本州世界遗产极致6日（东京1日自由）';
        $data['share_desc']='9月份--铂金豪景--日本白川乡本州世界遗产极致6日（东京1日自由）';
        $data['index_url']=base_url('lvt/rbbjhjinfo/ZX-RBBJHJ-6D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/rbbjhj/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('rbbjhj/index',$data);
        $this->show_count();
    }

    //惊爆-美东西+夏威夷14天夏进纽出-MU
    public function jbmx14dinfo()
    {

        $data=$this->get_day_url('jbmx14d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='惊爆-美东西+夏威夷14天夏进纽出-MU';
        $data['share_desc']='惊爆-美东西+夏威夷14天夏进纽出-MU';
        $data['index_url']=base_url('lvt/jbmx14dinfo/MY-JBMX-14D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx14d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx14d/index',$data);
        $this->show_count();
    }

    //美国东西海岸+南峡+夏威夷14天惠美团
    public function jbmx14d2info()
    {

        $data=$this->get_day_url('jbmx14d2');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='美国东西海岸+南峡+夏威夷14天惠美团';
        $data['share_desc']='美国东西海岸+南峡+夏威夷14天惠美团';
        $data['index_url']=base_url('lvt/jbmx14d2info/MY-JBMX-14D-2');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx14d2/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx14d2/index',$data);
        $this->show_count();
    }

    //惊爆-美东西+夏威夷14天夏进纽出-MU
    public function jbmx14d3info()
    {

        $data=$this->get_day_url('jbmx14d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='惊爆-美东西+夏威夷14天夏进纽出-MU';
        $data['share_desc']='惊爆-美东西+夏威夷14天夏进纽出-MU';
        $data['index_url']=base_url('lvt/jbmx14d3info/MY-JBMX-14D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx14d/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx14d/index2',$data);
        $this->show_count();
    }

    //0420-惊爆-美西8天洛进洛出-AA--4999
    public function jbmx1info()
    {

        $data=$this->get_day_url('jbmx1');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='惊爆-美西8天洛进洛出-AA';
        $data['share_desc']='惊爆-美西8天洛进洛出-AA';
        $data['index_url']=base_url('lvt/jbmx1info/MY-JBMX-4999');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx1/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx1/index',$data);
        $this->show_count();
    }

    //0420-惊爆-美西8天洛进洛出-AA--4999
    public function jbmx3info()
    {

        $data=$this->get_day_url('jbmx1');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='惊爆-美西8天洛进洛出-AA';
        $data['share_desc']='惊爆-美西8天洛进洛出-AA';
        $data['index_url']=base_url('lvt/jbmx3info/MY-JBMX-4999');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx1/images/head2.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx1/index2',$data);
        $this->show_count();
    }

    //0420-惊爆-美西8天洛进洛出-AA--4666
    public function jbmx2info()
    {

        $data=$this->get_day_url('jbmx2');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='惊爆-美西8天洛进洛出-AA';
        $data['share_desc']='惊爆-美西8天洛进洛出-AA';
        $data['index_url']=base_url('lvt/jbmx2info/MY-JBMX-4666');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jbmx2/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jbmx2/index',$data);
        $this->show_count();
    }

    //上海一地2晚3天 当地散拼
    public function jfwdshinfo()
    {

        $data=$this->get_day_url('jfwdsh');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='上海一地2晚3天 当地散拼';
        $data['share_desc']='上海一地2晚3天 当地散拼';
        $data['index_url']=base_url('lvt/jfwdshinfo/JFWD-SH-2N3D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/jfwdsh/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('jfwdsh/index',$data);
        $this->show_count();
    }

    //1002浪漫夏威夷一地8天6晚半自助游
    public function lmxwy6n8d_1002info()
    {

        $data=$this->get_day_url('lmxwy6n8d_1002');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='1002浪漫夏威夷一地8天6晚半自助游';
        $data['share_desc']='1002浪漫夏威夷一地8天6晚半自助游';
        $data['index_url']=base_url('lst/lmxwy6n8d_1002info/MY-LMXYWY6N8D-ZF');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/lmxwy6n8d_1002/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('lmxwy6n8d_1002/index',$data);
        $this->show_count();
    }

    //1002浪漫夏威夷一地8天6晚半自助游
    public function lmxwy6n8d_1002_2info()
    {

        $data=$this->get_day_url('lmxwy6n8d_1002_2');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='1002浪漫夏威夷一地8天6晚半自助游';
        $data['share_desc']='1002浪漫夏威夷一地8天6晚半自助游';
        $data['index_url']=base_url('lst/lmxwy6n8d_1002_2info/MY-LMXYWY6N8D-ZF');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/lmxwy6n8d_1002_2/images/head.jpg";

        $this->load->view('lmxwy6n8d_1002_2/index',$data);
        $this->show_count();
    }

    //上航乐享
    public function lxinfo()
    {

        $data=$this->get_day_url('lx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='乐享普吉岛5晚7天游';
        $data['share_desc']='乐享普吉岛5晚7天游';
        $data['index_url']=base_url('lvt/lxinfo');
        $data['shareimage']=$this->shareimage_forlx;
        $data['shareimage']="http://api.etjourney.com/public/lx/img/share.png";

        $this->load->view('lx/index',$data);
        $this->show_count();
    }
 

    //纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）
    public function akmAinfo()
    {

        $data=$this->get_day_url('akmA');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）';
        $data['share_desc']='纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）';
        $data['index_url']=base_url('lvt/akmAinfo/TY-A-xiaofangyanxitiyan');
        $data['shareimage']="http://api.etjourney.com/public/akmA/images/head.jpg";
        $this->load->view('akmA/index',$data);
        $this->show_count();
    }
    //纯玩：0804澳凯墨10天CX杭州（B线：嗨翻嘉年华+鲸声有约）
    public function akmBinfo()
    {

        $data=$this->get_day_url('akmB');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯玩：0804澳凯墨10天CX杭州（B线：嗨翻嘉年华+鲸声有约）';
        $data['share_desc']='纯玩：0804澳凯墨10天CX杭州（B线：嗨翻嘉年华+鲸声有约）';
        $data['index_url']=base_url('lvt/akmBinfo/TY-B-haifanjianianhuajingshengyouyue');
        $data['shareimage']="http://api.etjourney.com/public/akmB/images/head.jpg";
        $this->load->view('akmB/index',$data);
        $this->show_count();
    }

    //芽庄半自助4晚5天 A套
    public function yz5t4wainfo()
    {

        $data=$this->get_day_url('yz5t4wa');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='芽庄半自助4晚5天 A套';
        $data['share_desc']='拥抱着夏天的东方“马尔代夫 -- 越南芽庄”';
        $data['index_url']=base_url('lvt/yz5t4wainfo/kljq-5t4wa');
        $data['shareimage']="http://api.etjourney.com/public/yz5t4wa/images/head.jpg";
        $this->load->view('yz5t4wa/index',$data);
        $this->show_count();
    }

    //芽庄半自助4晚5天 B套
    public function yz5t4wbinfo()
    {

        $data=$this->get_day_url('yz5t4wb');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='芽庄亲子半自助4晚5天 B套';
        $data['share_desc']='芽庄亲子度假之旅5日游';
        $data['index_url']=base_url('lvt/yz5t4wbinfo/kljq-5t4wb');
        $data['shareimage']="http://api.etjourney.com/public/yz5t4wb/images/head.jpg";
        $this->load->view('yz5t4wb/index',$data);
        $this->show_count();
    }

    //芽庄半自助5晚6天 A套
    public function yz6t5wainfo()
    {

        $data=$this->get_day_url('yz6t5wa');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='芽庄半自助5晚6天 A套';
        $data['share_desc']='拥抱着夏天的东方“马尔代夫 -- 越南芽庄”';
        $data['index_url']=base_url('lvt/yz6t5wainfo/KLJQ-A-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/yz6t5wa/images/head.jpg";
        $this->load->view('yz6t5wa/index',$data);
        $this->show_count();
    }

    //芽庄半自助5晚6天 B套
    public function yz6t5wbinfo()
    {

        $data=$this->get_day_url('yz6t5wb');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='芽庄亲子半自助5晚6天 B套';
        $data['share_desc']='芽庄亲子度假之旅5日游';
        $data['index_url']=base_url('lvt/yz6t5wbinfo/KLJQ-B-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/yz6t5wb/images/head.jpg";
        $this->load->view('yz6t5wb/index',$data);
        $this->show_count();
    }

    //澳新11天CX杭州(AS
    public function axhinfo()
    {

        $data=$this->get_day_url('axh');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='澳大利亚新西兰11天';
        $data['share_desc']='澳大利亚新西兰11天';
        $data['index_url']=base_url('lvt/axhinfo/TY-aodaliyaxinxilan11tian');
        $data['shareimage']="http://api.etjourney.com/public/axh/images/head.jpg";
        $this->load->view('axh/index',$data);
        $this->show_count();
    }
    //D本州·赏艺伎·享温泉伊豆深度六日游（坂东）
    public function Dbandonginfo()
    {

        $data=$this->get_day_url('Dbandong');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='TY-D本州·赏艺伎·享温泉伊豆深度六日游(大阪进东京出)';
        $data['share_desc']='本州•赏艺伎•享温泉伊豆深度六日游';
        $data['index_url']=base_url('lvt/Dbandonginfo/TY-D-BD');
        $data['shareimage']="http://api.etjourney.com/public/Dbandong/images/head.jpg";
        $this->load->view('Dbandong/index',$data);
        $this->show_count();
    }
    //TY-D本州·赏艺伎·享温泉伊豆深度六日游（东坂）
    public function Ddongbaninfo()
    {

        $data=$this->get_day_url('Ddongban');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='TY-D本州·赏艺伎·享温泉伊豆深度六日游（东京进大阪出）';
        $data['share_desc']='本州•赏艺伎•享温泉伊豆深度六日游';
        $data['index_url']=base_url('lvt/Ddongbaninfo/TY-D-DB');
        $data['shareimage']="http://api.etjourney.com/public/Ddongban/images/head.jpg";
        $this->load->view('Ddongban/index',$data);
        $this->show_count();
    }
    //清迈5天4晚超值游
    public function qingmaiinfo()
    {

        $data=$this->get_day_url('qingmai');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈5天4晚超值游';
        $data['share_desc']='清迈5天4晚超值游';
        $data['index_url']=base_url('lvt/qingmaiinfo/');
        $data['shareimage']="http://api.etjourney.com/public/qingmai/images/head.jpg";
        $this->load->view('qingmai/index',$data);
        $this->show_count();
    }
   //私想家-C夏日亲子篇• 富士摇滚音乐节
    public function sxjCinfo()
    {

        $data=$this->get_day_url('sxjC');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='私想家-C夏日亲子篇• 富士摇滚音乐节';
        $data['share_desc']='私想家-C夏日亲子篇• 富士摇滚音乐节';
        $data['index_url']=base_url('lvt/sxjCinfo/TY-C-sixiangjia');
        $data['shareimage']="http://api.etjourney.com/public/sxjC/images/head.jpg";
        $this->load->view('sxjC/index',$data);
        $this->show_count();
    }
//【斯里兰卡】UL7天6晚新魅力轻奢系列
   public function siinfo()
    {

        $data=$this->get_day_url('si');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【斯里兰卡】UL7天6晚新魅力轻奢系列';
        $data['share_desc']='【斯里兰卡】UL7天6晚新魅力轻奢系列';
        $data['index_url']=base_url('lvt/siinfo/zhongxin-sh-sililanka');
        $data['shareimage']="http://api.etjourney.com/public/si/images/head.jpg";
        $this->load->view('si/index',$data);
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
        $data['day1']=base_url("lvt/day_info/1/$type");
        $data['day2']=base_url("lvt/day_info/2/$type");
        $data['day3']=base_url("lvt/day_info/3/$type");
        $data['day4']=base_url("lvt/day_info/4/$type");
        $data['day5']=base_url("lvt/day_info/5/$type");
        $data['day6']=base_url("lvt/day_info/6/$type");
        $data['day7']=base_url("lvt/day_info/7/$type");
        $data['day8']=base_url("lvt/day_info/8/$type");
        $data['day9']=base_url("lvt/day_info/9/$type");
        $data['day10']=base_url("lvt/day_info/10/$type");
        $data['day11']=base_url("lvt/day_info/11/$type");
        $data['day12']=base_url("lvt/day_info/12/$type");
        $data['day13']=base_url("lvt/day_info/13/$type");
        $data['day14']=base_url("lvt/day_info/14/$type");
        
        $data['day15']=base_url("lvt/day_info/15/$type");
        $data['day16']=base_url("lvt/day_info/16/$type");
        $data['day17']=base_url("lvt/day_info/17/$type");
        $data['day18']=base_url("lvt/day_info/18/$type");
        $data['day19']=base_url("lvt/day_info/19/$type");
        $data['day20']=base_url("lvt/day_info/20/$type");
        $data['day21']=base_url("lvt/day_info/21/$type");
        $data['day22']=base_url("lvt/day_info/22/$type");
        $data['day23']=base_url("lvt/day_info/23/$type");
        $data['day24']=base_url("lvt/day_info/24/$type");

        $data['day25']=base_url("lvt/day_info/25/$type");

        $data['day26']=base_url("lvt/day_info/26/$type");
        $data['day27']=base_url("lvt/day_info/27/$type");
        $data['day28']=base_url("lvt/day_info/28/$type");
        $data['day29']=base_url("lvt/day_info/29/$type");
        $data['day30']=base_url("lvt/day_info/30/$type");

        $data['local']=base_url("lvt/hotel/local/$type");
        $data['local2']=base_url("lvt/hotel/local2/$type");
        $data['inter']=base_url("lvt/hotel/inter/$type");
        $data['hotel1']=base_url("lvt/hotel/hotel1/$type");
        $data['hotel2']=base_url("lvt/hotel/hotel2/$type");
        $data['hotel3']=base_url("lvt/hotel/hotel3/$type");
        $data['hotel4']=base_url("lvt/hotel/hotel4/$type");
        $data['hotel5']=base_url("lvt/hotel/hotel5/$type");

        $data['hotel6']=base_url("lvt/hotel/hotel6/$type");
        $data['hotel7']=base_url("lvt/hotel/hotel7/$type");
        $data['hotel8']=base_url("lvt/hotel/hotel8/$type");
        $data['hotel9']=base_url("lvt/hotel/hotel9/$type");

        $data['stroke1']=base_url("lvt/stroke_info/1/$type");
        $data['stroke2']=base_url("lvt/stroke_info/2/$type");
        $data['stroke3']=base_url("lvt/stroke_info/3/$type");
        $data['stroke4']=base_url("lvt/stroke_info/4/$type");
        $data['stroke5']=base_url("lvt/stroke_info/5/$type");
        $data['stroke6']=base_url("lvt/stroke_info/6/$type");
        $data['stroke7']=base_url("lvt/stroke_info/7/$type");
        $data['stroke8']=base_url("lvt/stroke_info/8/$type");
        $data['stroke9']=base_url("lvt/stroke_info/9/$type");
        $data['stroke10']=base_url("lvt/stroke_info/10/$type");
        $data['stroke11']=base_url("lvt/stroke_info/11/$type");
        $data['stroke12']=base_url("lvt/stroke_info/12/$type");
        $data['stroke13']=base_url("lvt/stroke_info/13/$type");
        return $data;
    }
  
  
    //纯享 $type cx 纯享 yx 悦享用
    public function day_info($day,$type='cx')
    {
        //$data=[];
        $data=$this->get_day_url($type);
        if(!in_array($type,array('sqbjd','cx','yx','lx','cl')))
        {
            //return false;
        }
        switch($day)
        {
            case 1:
                $this->load->view("$type/day1",$data);
                break;
            case 2:
                $this->load->view("$type/day2",$data);
                break;
            case 3:
                $this->load->view("$type/day3",$data);
                break;
            case 4:
                $this->load->view("$type/day4",$data);
                break;
            case 5:
                $this->load->view("$type/day5",$data);
                break;
            case 6:
                $this->load->view("$type/day6",$data);
                break;
            case 7:
                $this->load->view("$type/day7",$data);
                break;
            case 8:
                $this->load->view("$type/day8",$data);
                break;
            case 9:
                $this->load->view("$type/day9",$data);
                break;
            case 10:
                $this->load->view("$type/day10",$data);
                break;
            case 11:
                $this->load->view("$type/day11",$data);
                break;
            case 12:
                $this->load->view("$type/day12",$data);
                break;
            case 13:
                $this->load->view("$type/day13",$data);
                break;
            case 14:
                $this->load->view("$type/day14",$data);
                break;
            case 15:
                $this->load->view("$type/day15",$data);
                break;
            case 16:
                $this->load->view("$type/day16",$data);
                break;
            case 17:
                $this->load->view("$type/day17",$data);
                break;
            case 18:
                $this->load->view("$type/day18",$data);
                break;
            case 19:
                $this->load->view("$type/day19",$data);
                break;
            case 20:
                $this->load->view("$type/day20",$data);
                break;
            case 21:
                $this->load->view("$type/day21",$data);
                break;
            case 22:
                $this->load->view("$type/day22",$data);
                break;
            case 23:
                $this->load->view("$type/day23",$data);
                break;
            case 24:
                $this->load->view("$type/day24",$data);
                break;
            case 25:
                $this->load->view("$type/day25",$data);
                break;
            case 26:
                $this->load->view("$type/day26",$data);
                break;
            case 27:
                $this->load->view("$type/day27",$data);
                break;
            case 28:
                $this->load->view("$type/day28",$data);
                break;
            case 29:
                $this->load->view("$type/day29",$data);
                break;
            case 30:
                $this->load->view("$type/day30",$data);
                break;
            default:
                return false;
        }
        $this->show_count();

    }

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


    //悦享
    public function yxinfo()
    {
       // echo 1;exit();
        $data=$this->get_day_url('yx');
        $data['share_title']='悦享普吉岛5晚7天游';
        $data['share_desc']='悦享普吉岛5晚7天游。';
        $data['index_url']=base_url('lvt/yxinfo');
        $data['shareimage']=$this->shareimage_foryx;
        $data['shareimage']="http://api.etjourney.com/public/yx/images/yx_share.jpg";
        $data['signPackage']=$this->wx_js_para(3);


        $this->load->view('yx/index',$data);
        $this->show_count();
    }


    // 深情普吉岛
    public function sqbjdinfo()
    {
        // echo 1;exit();
        $data=$this->get_day_url('sqbjd');
        $data['share_title']='深情普吉，5晚7日舒适行。';
        $data['share_desc']='独家行程：2017全新开幕水上恐龙乐园。 五星豪华酒店。';
        $data['index_url']=base_url('lvt/sqbjdinfo');
        $data['shareimage']='/public/yazhuang/images/share.png';


        $data['signPackage']=$this->wx_js_para(3);

        $this->load->view('sqbjd/index',$data);
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