<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lst extends CI_Controller
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

  

  


    //1219-纯美-美国东西海岸+冬黄石+南峡+秘境羚羊彩穴+夏威夷15天夏进纽出-MU
        public function mg15dcminfo()
        {

            $data=$this->get_day_url('mg15dcm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='1219-纯美-美国东西海岸+冬黄石+南峡+秘境羚羊彩穴+夏威夷15天夏进纽出-MU';
            $data['share_desc']='1219-纯美-美国东西海岸+冬黄石+南峡+秘境羚羊彩穴+夏威夷15天夏进纽出-MU';
            $data['index_url']=base_url('lst/mg15dcminfo/MY-mg15dcm-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg15dcm/images/share.jpg";
            $this->load->view('mg15dcm/index',$data);
            $this->show_count();
        }

    //美国东西海岸+夏威夷+冬黄石15天惠美团
        public function mg15dhminfo()
        {

            $data=$this->get_day_url('mg15dhm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+夏威夷+冬黄石15天惠美团';
            $data['share_desc']='美国东西海岸+夏威夷+冬黄石15天惠美团';
            $data['index_url']=base_url('lst/mg15dhminfo/MY-mg15dhm-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg15dhm/images/share.jpg";
            $this->load->view('mg15dhm/index',$data);
            $this->show_count();
        }        


    //美东西12天惊爆
        public function jbmdx12dinfo()
        {

            $data=$this->get_day_url('jbmdx12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美东西12天惊爆';
            $data['share_desc']='美东西12天惊爆';
            $data['index_url']=base_url('lst/jbmdx12dinfo/MY-jbmdx12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jbmdx12d/images/share.jpg";
            $this->load->view('jbmdx12d/index',$data);
            $this->show_count();
        }        


    //美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】
        public function mg10dinfo()
        {

            $data=$this->get_day_url('mg10d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】';
            $data['share_desc']='美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】';
            $data['index_url']=base_url('lst/mg10dinfo/SH-mg10d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg10d/images/share.jpg";
            $this->load->view('mg10d/index',$data);
            $this->show_count();
        }
    //美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】
        public function mg10d1info()
        {

            $data=$this->get_day_url('mg10d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】';
            $data['share_desc']='美国迈阿密、古巴10天8晚跟团游【雪茄王国、哈瓦那老爷车、巴拉德罗海滩、爱迪生福特冬宫】';
            $data['index_url']=base_url('lst/mg10d1info/SH-mg10d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg10d/images/share.jpg";
            $this->load->view('mg10d/index1',$data);
            $this->show_count();
        }
    //【随心随行半自由行】– 个签2人发团  境外拼车
        public function twgjbc7dinfo()
        {

            $data=$this->get_day_url('twgjbc7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【随心随行半自由行】– 个签2人发团  境外拼车';
            $data['share_desc']='【随心随行半自由行】– 个签2人发团  境外拼车';
            $data['index_url']=base_url('lst/twgjbc7dinfo/SH-twgjbc7d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/twgjbc7d/images/share.jpg";
            $this->load->view('twgjbc7d/index',$data);
            $this->show_count();
        }
    //【随心随行半自由行】– 个签2人发团  境外拼车
        public function twgjbc7d1info()
        {

            $data=$this->get_day_url('twgjbc7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【随心随行半自由行】– 个签2人发团  境外拼车';
            $data['share_desc']='【随心随行半自由行】– 个签2人发团  境外拼车';
            $data['index_url']=base_url('lst/twgjbc7d1info/SH-twgjbc7d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/twgjbc7d/images/share.jpg";
            $this->load->view('twgjbc7d/index1',$data);
            $this->show_count();
        }

    //西游记【随心随行半自由行】
        public function xyjtw6dinfo()
        {

            $data=$this->get_day_url('xyjtw6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='西游记【随心随行半自由行】';
            $data['share_desc']='西游记【随心随行半自由行】';
            $data['index_url']=base_url('lst/xyjtw6dinfo/SH-xyjtw6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xyjtw6d/images/share.jpg";
            $this->load->view('xyjtw6d/index',$data);
            $this->show_count();
        }   
             
    //西游记【随心随行半自由行】
        public function xyjtw6d1info()
        {

            $data=$this->get_day_url('xyjtw6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='西游记【随心随行半自由行】';
            $data['share_desc']='西游记【随心随行半自由行】';
            $data['index_url']=base_url('lst/xyjtw6d1info/SH-xyjtw6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xyjtw6d/images/share.jpg";
            $this->load->view('xyjtw6d/index1',$data);
            $this->show_count();
        } 
    //美国东西海岸+西雅图+波士顿名校12天惊爆团
        public function mxb12dinfo()
        {

            $data=$this->get_day_url('mxb12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+西雅图+波士顿名校12天惊爆团';
            $data['share_desc']='美国东西海岸+西雅图+波士顿名校12天惊爆团';
            $data['index_url']=base_url('lst/mxb12dinfo/MY-mxb12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mxb12d/images/share.jpg";
            $this->load->view('mxb12d/index',$data);
            $this->show_count();
        }

    //皇家加勒比海洋和悦号游轮-美国东西部 南部18天游
        public function hjjlb18dinfo()
        {

            $data=$this->get_day_url('hjjlb18d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='皇家加勒比海洋和悦号游轮-美国东西部 南部18天游';
            $data['share_desc']='皇家加勒比海洋和悦号游轮-美国东西部 南部18天游';
            $data['index_url']=base_url('lst/hjjlb18dinfo/SH-hjjlb18d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/hjjlb18d/images/share.jpg";
            $this->load->view('hjjlb18d/index',$data);
            $this->show_count();
        }

    //美国东西南部+皇家加勒比游轮
        public function jlbyl18dinfo()
        {

            $data=$this->get_day_url('jlbyl18d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西南部+皇家加勒比游轮';
            $data['share_desc']='美国东西南部+皇家加勒比游轮';
            $data['index_url']=base_url('lst/jlbyl18dinfo/SH-jlbyl18d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jlbyl18d/images/share.jpg";
            $this->load->view('jlbyl18d/index',$data);
            $this->show_count();
        }

    //美国东西海岸+波士顿名校+圣地亚哥12天惠美A
        public function mbs12dinfo()
        {

            $data=$this->get_day_url('mbs12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+波士顿名校+圣地亚哥12天惠美A';
            $data['share_desc']='美国东西海岸+波士顿名校+圣地亚哥12天惠美A';
            $data['index_url']=base_url('lst/mbs12dinfo/MY-mbs12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mbs12d/images/share.jpg";
            $this->load->view('mbs12d/index',$data);
            $this->show_count();
        }

    //泰国+新加坡+马来西亚10日游
    public function txm10dinfo()
    {

        $data=$this->get_day_url('txm10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='泰国+新加坡+马来西亚10日游';
        $data['share_desc']='泰国+新加坡+马来西亚10日游';
        $data['index_url']=base_url('lst/txm10dinfo/GL-TXM-10D');
        $data['shareimage']=$this->shareimage_forlx; 
        $data['shareimage']="http://api.etjourney.com/public/txm10d/images/head.jpg";
        //echo '<pre>';print_r($data);exit();
        $this->load->view('txm10d/index',$data);
        $this->show_count();
    }


    //新加坡4晚5日纯玩休闲游
        public function xmcw5dinfo()
        {

            $data=$this->get_day_url('xmcw5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡4晚5日纯玩休闲游';
            $data['share_desc']='新加坡4晚5日纯玩休闲游';
            $data['index_url']=base_url('lst/xmcw5dinfo/gl-xmcw5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xmcw5d/images/share.jpg";
            $this->load->view('xmcw5d/index',$data);
            $this->show_count();
        }

    //双新双园亲子游---新加坡海洋馆+夜间动物园+新山乐高主题乐园
        public function xmlg5dinfo()
        {

            $data=$this->get_day_url('xmlg5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡海洋馆+夜间动物园+新山乐高主题乐园';
            $data['share_desc']='轻松度假4晚5日纯玩休闲游';
            $data['index_url']=base_url('lst/xmlg5dinfo/gl-xmlg5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xmlg5d/images/share.jpg";
            $this->load->view('xmlg5d/index',$data);
            $this->show_count();
        }

    //新加坡+马六甲+云顶+吉隆坡4晚5日
        public function xm4n5dinfo()
        {

            $data=$this->get_day_url('xm4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡+马六甲+云顶+吉隆坡4晚5日';
            $data['share_desc']='新加坡+马六甲+云顶+吉隆坡4晚5日';
            $data['index_url']=base_url('lst/xm4n5dinfo/gl-xm4n5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xm4n5d/images/share.jpg";
            $this->load->view('xm4n5d/index',$data);
            $this->show_count();
        }                

    //曼谷+芭提雅6日游 
        public function mb6dinfo()
        {

            $data=$this->get_day_url('mb6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='曼谷+芭提雅6日游 ';
            $data['share_desc']='曼谷+芭提雅6日游 ';
            $data['index_url']=base_url('lst/mb6dinfo/gl-mb6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mb6d/images/share.jpg";
            $this->load->view('mb6d/index',$data);
            $this->show_count();
        }


    //曼谷+芭提雅+大城王府7日游 
        public function mbd7dinfo()
        {

            $data=$this->get_day_url('mbd7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='曼谷+芭提雅+大城王府7日游 ';
            $data['share_desc']='曼谷+芭提雅+大城王府7日游 ';
            $data['index_url']=base_url('lst/mbd7dinfo/gl-mbd7d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mbd7d/images/share.jpg";
            $this->load->view('mbd7d/index',$data);
            $this->show_count();
        }


    //曼谷+芭提雅+清迈8天游
        public function mbq8dinfo()
        {

            $data=$this->get_day_url('mbq8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='曼谷+芭提雅+清迈8天游';
            $data['share_desc']='曼谷+芭提雅+清迈8天游';
            $data['index_url']=base_url('lst/mbq8dinfo/gl-mbq8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mbq8d/images/share.jpg";
            $this->load->view('mbq8d/index',$data);
            $this->show_count();
        }


    //曼谷+芭提雅+普吉8天游
        public function mbp8dinfo()
        {

            $data=$this->get_day_url('mbp8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='曼谷+芭提雅+普吉8天游';
            $data['share_desc']='曼谷+芭提雅+普吉8天游';
            $data['index_url']=base_url('lst/mbp8dinfo/gl-mbp8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mbp8d/images/share.jpg";
            $this->load->view('mbp8d/index',$data);
            $this->show_count();
        }


    //巴厘岛送文莱5N6D行程
        public function bldBIinfo()
        {

            $data=$this->get_day_url('bldBI');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛送文莱5N6D行程';
            $data['share_desc']='巴厘岛送文莱5N6D行程';
            $data['index_url']=base_url('lst/bldBIinfo/KLJQ-bldBI-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldBI/images/share.jpg";
            $this->load->view('bldBI/index',$data);
            $this->show_count();
        }

    //东航直飞五晚七天龙目岛+巴厘岛半自由行行程
        public function bldMUinfo()
        {

            $data=$this->get_day_url('bldMU');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东航直飞五晚七天龙目岛+巴厘岛半自由行行程';
            $data['share_desc']='东航直飞五晚七天龙目岛+巴厘岛半自由行行程';
            $data['index_url']=base_url('lst/bldMUinfo/KLJQ-bldMU-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldMU/images/share.jpg";
            $this->load->view('bldMU/index',$data);
            $this->show_count();
        }

    //奢雅风华-巴厘岛魔力爱五晚六天行程
        public function bldmlaBIinfo()
        {

            $data=$this->get_day_url('bldmlaBI');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='奢雅风华-巴厘岛魔力爱五晚六天行程';
            $data['share_desc']='奢雅风华-巴厘岛魔力爱五晚六天行程';
            $data['index_url']=base_url('lst/bldmlaBIinfo/KLJQ-bldmlaBI-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldmlaBI/images/share.jpg";
            $this->load->view('bldmlaBI/index',$data);
            $this->show_count();
        }

    //巴厘岛送文莱5N6D行程升级蓝梦
        public function bldlmBIinfo()
        {

            $data=$this->get_day_url('bldlmBI');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛送文莱5N6D行程升级蓝梦';
            $data['share_desc']='巴厘岛送文莱5N6D行程升级蓝梦';
            $data['index_url']=base_url('lst/bldlmBIinfo/KLJQ-bldlmBI-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldlmBI/images/share.jpg";
            $this->load->view('bldlmBI/index',$data);
            $this->show_count();
        }

    //特价！！！台湾环岛8日游（直飞、经济型酒店、台北进出、晚去晚回）
        public function twhy8dinfo()
        {

            $data=$this->get_day_url('twhy8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='特价！！！台湾环岛8日游（直飞、经济型酒店、台北进出、晚去晚回）';
            $data['share_desc']='特价！！！台湾环岛8日游（直飞、经济型酒店、台北进出、晚去晚回）';
            $data['index_url']=base_url('lst/twhy8dinfo/SH-twhy8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/twhy8d/images/share.jpg";
            $this->load->view('twhy8d/index',$data);
            $this->show_count();
        }

    //美国东西海岸+旧金山+夏威夷15天惊爆团
        public function mg15dXJNCinfo()
        {

            $data=$this->get_day_url('mg15dXJNC');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+旧金山+夏威夷15天惊爆团';
            $data['share_desc']='美国东西海岸+旧金山+夏威夷15天惊爆团';
            $data['index_url']=base_url('lst/mg15dXJNCinfo/MY-mg15dXJNC-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg15dXJNC/images/share.jpg";
            $this->load->view('mg15dXJNC/index',$data);
            $this->show_count();
        }

    //东京5天自由行
        public function rbjjtcinfo()
        {

            $data=$this->get_day_url('rbjjtc');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东京5天自由行';
            $data['share_desc']='东京5天自由行';
            $data['index_url']=base_url('lst/rbjjtcinfo/SH-rbjjtc-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbjjtc/images/share.jpg";
            $this->load->view('rbjjtc/index',$data);
            $this->show_count();
        }

    //梦到一片绿海，梦醒再见醉人胡杨
        public function xnj5dinfo()
        {

            $data=$this->get_day_url('xnj5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='梦到一片绿海，梦醒再见醉人胡杨';
            $data['share_desc']='梦到一片绿海，梦醒再见醉人胡杨';
            $data['index_url']=base_url('lst/xnj5dinfo/HXJT-xnj5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xnj5d/images/share.jpg";
            $this->load->view('xnj5d/index',$data);
            $this->show_count();
        }
    //梦到一片绿海，梦醒再见醉人胡杨
        public function xnj5d1info()
        {

            $data=$this->get_day_url('xnj5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='梦到一片绿海，梦醒再见醉人胡杨';
            $data['share_desc']='梦到一片绿海，梦醒再见醉人胡杨';
            $data['index_url']=base_url('lst/xnj5d1info/HXJT-xnj5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xnj5d/images/head1.jpg";
            $this->load->view('xnj5d/index1',$data);
            $this->show_count();
        }
    //天山伊犁草原花海风光深度摄影团
        public function tsyl12dinfo()
        {

            $data=$this->get_day_url('tsyl12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='天山伊犁草原花海风光深度摄影团';
            $data['share_desc']='天山伊犁草原花海风光深度摄影团';
            $data['index_url']=base_url('lst/tsyl12dinfo/HXJT-tsyl12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tsyl12d/images/share.jpg";
            $this->load->view('tsyl12d/index',$data);
            $this->show_count();
        }
    //天山伊犁草原花海风光深度摄影团
        public function tsyl12d1info()
        {

            $data=$this->get_day_url('tsyl12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='天山伊犁草原花海风光深度摄影团';
            $data['share_desc']='天山伊犁草原花海风光深度摄影团';
            $data['index_url']=base_url('lst/tsyl12d1info/HXJT-tsyl12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tsyl12d/images/head1.jpg";
            $this->load->view('tsyl12d/index1',$data);
            $this->show_count();
        }
    //罗布泊探险穿越之旅
        public function lbp9dinfo()
        {

            $data=$this->get_day_url('lbp9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='罗布泊探险穿越之旅';
            $data['share_desc']='罗布泊探险穿越之旅';
            $data['index_url']=base_url('lst/lbp9dinfo/HXJT-lbp9d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lbp9d/images/share.jpg";
            $this->load->view('lbp9d/index',$data);
            $this->show_count();
        }
    //罗布泊探险穿越之旅
        public function lbp9d1info()
        {

            $data=$this->get_day_url('lbp9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='罗布泊探险穿越之旅';
            $data['share_desc']='罗布泊探险穿越之旅';
            $data['index_url']=base_url('lst/lbp9d1info/HXJT-lbp9d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lbp9d/images/share1.jpg";
            $this->load->view('lbp9d/index1',$data);
            $this->show_count();
        }
    //库木塔格沙漠里翻沙倒浪去
        public function xly2dinfo()
        {

            $data=$this->get_day_url('xly2d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='库木塔格沙漠里翻沙倒浪去';
            $data['share_desc']='库木塔格沙漠里翻沙倒浪去';
            $data['index_url']=base_url('lst/xly2dinfo/HXJT-xly2d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xly2d/images/share.jpg";
            $this->load->view('xly2d/index',$data);
            $this->show_count();
        }
    //库木塔格沙漠里翻沙倒浪去
        public function xly2d1info()
        {

            $data=$this->get_day_url('xly2d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='库木塔格沙漠里翻沙倒浪去';
            $data['share_desc']='库木塔格沙漠里翻沙倒浪去';
            $data['index_url']=base_url('lst/xly2d1info/HXJT-xly2d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xly2d/images/head1.jpg";
            $this->load->view('xly2d/index1',$data);
            $this->show_count();
        }
    //醉美胡杨，遇见塔克拉玛干
        public function zmhy11dinfo()
        {

            $data=$this->get_day_url('zmhy11d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='醉美胡杨，遇见塔克拉玛干';
            $data['share_desc']='醉美胡杨，遇见塔克拉玛干';
            $data['index_url']=base_url('lst/zmhy11dinfo/HXJT-zmhy11d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zmhy11d/images/share.jpg";
            $this->load->view('zmhy11d/index',$data);
            $this->show_count();
        }
    //醉美胡杨，遇见塔克拉玛干
        public function zmhy11d1info()
        {

            $data=$this->get_day_url('zmhy11d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='醉美胡杨，遇见塔克拉玛干';
            $data['share_desc']='醉美胡杨，遇见塔克拉玛干';
            $data['index_url']=base_url('lst/zmhy11d1info/HXJT-zmhy11d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zmhy11d/images/head1.jpg";
            $this->load->view('zmhy11d/index1',$data);
            $this->show_count();
        }
    //新疆伊犁大环线10日游
        public function xjdhx10dinfo()
        {

            $data=$this->get_day_url('xjdhx10d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新疆伊犁大环线10日游';
            $data['share_desc']='新疆伊犁大环线10日游';
            $data['index_url']=base_url('lst/xjdhx10dinfo/HXJT-xjdhx10d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjdhx10d/images/share.jpg";
            $this->load->view('xjdhx10d/index',$data);
            $this->show_count();
        }
    //新疆伊犁大环线10日游
        public function xjdhx10d1info()
        {

            $data=$this->get_day_url('xjdhx10d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新疆伊犁大环线10日游';
            $data['share_desc']='新疆伊犁大环线10日游';
            $data['index_url']=base_url('lst/xjdhx10d1info/HXJT-xjdhx10d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjdhx10d/images/head1.jpg";
            $this->load->view('xjdhx10d/index1',$data);
            $this->show_count();
        }
    //千古之谜—小河墓地
        public function xhmd8dinfo()
        {

            $data=$this->get_day_url('xhmd8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='千古之谜—小河墓地';
            $data['share_desc']='千古之谜—小河墓地';
            $data['index_url']=base_url('lst/xhmd8dinfo/HXJT-xhmd8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xhmd8d/images/share.jpg";
            $this->load->view('xhmd8d/index',$data);
            $this->show_count();
        }
    //千古之谜—小河墓地
        public function xhmd8d1info()
        {

            $data=$this->get_day_url('xhmd8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='千古之谜—小河墓地';
            $data['share_desc']='千古之谜—小河墓地';
            $data['index_url']=base_url('lst/xhmd8d1info/HXJT-xhmd8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xhmd8d/images/share1.jpg";
            $this->load->view('xhmd8d/index1',$data);
            $this->show_count();
        }
    // 乌市周边3天穿越避暑游
        public function ws3dinfo()
        {

            $data=$this->get_day_url('ws3d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='乌市周边3天穿越避暑游';
            $data['share_desc']='乌市周边3天穿越避暑游';
            $data['index_url']=base_url('lst/ws3dinfo/HXJT-ws3d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ws3d/images/share.jpg";
            $this->load->view('ws3d/index',$data);
            $this->show_count();
        }        
    // 乌市周边3天穿越避暑游
        public function ws3d1info()
        {

            $data=$this->get_day_url('ws3d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='乌市周边3天穿越避暑游';
            $data['share_desc']='乌市周边3天穿越避暑游';
            $data['index_url']=base_url('lst/ws3d1info/HXJT-ws3d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ws3d/images/head1.jpg";
            $this->load->view('ws3d/index1',$data);
            $this->show_count();
        }  


    //  本州季节花田巡游+蟹道乐盛宴+东京1天FREE6日豪华游（4钻）--阪东线-0910
        public function bzhj6dBDinfo()
        {

            $data=$this->get_day_url('bzhj6dBD');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 本州季节花田巡游+蟹道乐盛宴+东京1天FREE6日豪华游（4钻）--阪东线-0910';
            $data['share_desc']=' 本州季节花田巡游+蟹道乐盛宴+东京1天FREE6日豪华游（4钻）--阪东线-0910';
            $data['index_url']=base_url('lst/bzhj6dBDinfo/SH-bzhj6dBD-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bzhj6dBD/images/share.jpg";
            $this->load->view('bzhj6dBD/index',$data);
            $this->show_count();
        }

    //  轻奢私家团--西班牙一地深度9晚11日
        public function xby11dinfo()
        {

            $data=$this->get_day_url('xby11d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 轻奢私家团--西班牙一地深度9晚11日';
            $data['share_desc']=' 轻奢私家团--西班牙一地深度9晚11日';
            $data['index_url']=base_url('lst/xby11dinfo/JF-xby11d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xby11d/images/share.jpg";
            $this->load->view('xby11d/index',$data);
            $this->show_count();
        }

    //  轻奢私家团--捷德法深度8晚11天
        public function jdf11dinfo()
        {

            $data=$this->get_day_url('jdf11d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 轻奢私家团--捷德法深度8晚11天';
            $data['share_desc']=' 轻奢私家团--捷德法深度8晚11天';
            $data['index_url']=base_url('lst/jdf11dinfo/JF-jdf11d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jdf11d/images/share.jpg";
            $this->load->view('jdf11d/index',$data);
            $this->show_count();
        }

    //  轻奢私家团--捷德奥深度9晚12天
        public function jda12dinfo()
        {

            $data=$this->get_day_url('jda12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 轻奢私家团--捷德奥深度9晚12天';
            $data['share_desc']=' 轻奢私家团--捷德奥深度9晚12天';
            $data['index_url']=base_url('lst/jda12dinfo/JF-jda12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jda12d/images/share.jpg";
            $this->load->view('jda12d/index',$data);
            $this->show_count();
        }

    //  喀纳斯、禾木、魔鬼城、赛里木湖。那拉提、巴音布鲁克翻越天山乔尔玛独库8日游
        public function xj8dinfo()
        {

            $data=$this->get_day_url('xj8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 喀纳斯、禾木、魔鬼城、赛里木湖。那拉提、巴音布鲁克翻越天山乔尔玛独库8日游';
            $data['share_desc']=' 喀纳斯、禾木、魔鬼城、赛里木湖。那拉提、巴音布鲁克翻越天山乔尔玛独库8日游';
            $data['index_url']=base_url('lst/xj8dinfo/RHHK-xj8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xj8d/images/share.jpg";
            $this->load->view('xj8d/index',$data);
            $this->show_count();
        }

    //  伊犁赛里木湖、东西喀拉峻、巴音布鲁克、吐鲁番天上四季8日游
        public function xjyl8dinfo()
        {

            $data=$this->get_day_url('xjyl8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 伊犁赛里木湖、东西喀拉峻、巴音布鲁克、吐鲁番天上四季8日游';
            $data['share_desc']=' 伊犁赛里木湖、东西喀拉峻、巴音布鲁克、吐鲁番天上四季8日游';
            $data['index_url']=base_url('lst/xjyl8dinfo/RHHK-xjyl8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjyl8d/images/share.jpg";
            $this->load->view('xjyl8d/index',$data);
            $this->show_count();
        }

    //  北疆准噶尔喀纳斯、禾木村、乌尔禾魔鬼城、可可托海7日游
        public function xj7dinfo()
        {

            $data=$this->get_day_url('xj7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 北疆准噶尔喀纳斯、禾木村、乌尔禾魔鬼城、可可托海7日游';
            $data['share_desc']=' 北疆准噶尔喀纳斯、禾木村、乌尔禾魔鬼城、可可托海7日游';
            $data['index_url']=base_url('lst/xj7dinfo/RHHK-xj7d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xj7d/images/share.jpg";
            $this->load->view('xj7d/index',$data);
            $this->show_count();
        }


    // 纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】
        public function cy6dinfo()
        {

            $data=$this->get_day_url('cy6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】';
            $data['share_desc']='纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】';
            $data['index_url']=base_url('lst/cy6dinfo/SH-cy6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/cy6d/images/share.jpg";
            $this->load->view('cy6d/index',$data);
            $this->show_count();
        }


    // 纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】
        public function cy6d1info()
        {

            $data=$this->get_day_url('cy6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】';
            $data['share_desc']='纯悦泰国6日游【无购物/4晚网评5星/升一晚艾美或万豪或希尔顿】';
            $data['index_url']=base_url('lst/cy6d1info/SH-cy6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/cy6d/images/share1.jpg";
            $this->load->view('cy6d/index1',$data);
            $this->show_count();
        }

    // 舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】
        public function sx6dinfo()
        {

            $data=$this->get_day_url('sx6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】';
            $data['share_desc']='舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】';
            $data['index_url']=base_url('lst/sx6dinfo/SH-sx6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sx6d/images/head.jpg";
            $this->load->view('sx6d/index',$data);
            $this->show_count();
        }

    // 舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】
        public function sx6d1info()
        {

            $data=$this->get_day_url('sx6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】';
            $data['share_desc']='舒心泰国6日游【自费600/全程5星/升一晚希尔顿或艾美/赠送旅拍照】';
            $data['index_url']=base_url('lst/sx6d1info/SH-sx6d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sx6d/images/head1.jpg";
            $this->load->view('sx6d/index1',$data);
            $this->show_count();
        }

    // 夏威夷自由行7天6晚
        public function xwyzyxinfo()
        {

            $data=$this->get_day_url('xwyzyx');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='夏威夷自由行7天6晚';
            $data['share_desc']='夏威夷自由行7天6晚';
            $data['index_url']=base_url('lst/xwyzyxinfo/MY-XWY-7T6W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xwyzyx/images/share.jpg";
            $this->load->view('xwyzyx/index',$data);
            $this->show_count();
        }

        // 夏威夷自由行6天5晚
        public function xwyzyx6t5winfo()
        {

            $data=$this->get_day_url('xwyzyx6t5w');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='夏威夷自由行6天5晚';
            $data['share_desc']='夏威夷自由行6天5晚';
            $data['index_url']=base_url('lst/xwyzyx6t5winfo/MY-XWY-6T5W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xwyzyx/images/share.jpg";
            $this->load->view('xwyzyx6t5w/index',$data);
            $this->show_count();
        }
        // 夏威夷自由行5天4晚
        public function xwyzyx5t4winfo()
        {

            $data=$this->get_day_url('xwyzyx5t4w');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='夏威夷自由行5天4晚';
            $data['share_desc']='夏威夷自由行5天4晚';
            $data['index_url']=base_url('lst/xwyzyx5t4winfo/MY-XWY-5T4W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xwyzyx/images/share.jpg";
            $this->load->view('xwyzyx5t4w/index',$data);
            $this->show_count();
        }
        // 夏威夷自由行4天3晚
        public function xwyzyx4t3winfo()
        {

            $data=$this->get_day_url('xwyzyx4t3w');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='夏威夷自由行4天3晚';
            $data['share_desc']='夏威夷自由行4天3晚';
            $data['index_url']=base_url('lst/xwyzyx4t3winfo/MY-XWY-4T3W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xwyzyx/images/share.jpg";
            $this->load->view('xwyzyx4t3w/index',$data);
            $this->show_count();
        }
        // 夏威夷自由行3天2晚
        public function xwyzyx3t2winfo()
        {

            $data=$this->get_day_url('xwyzyx3t2w');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='夏威夷自由行3天2晚';
            $data['share_desc']='夏威夷自由行3天2晚';
            $data['index_url']=base_url('lst/xwyzyx3t2winfo/MY-XWY-3T2W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xwyzyx/images/share.jpg";
            $this->load->view('xwyzyx3t2w/index',$data);
            $this->show_count();
        }




        // 旗舰普吉--------皇帝岛海豚岛双岛之旅8日
        public function qjpjhhsd8dinfo()
        {

            $data=$this->get_day_url('qjpjhhsd8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='旗舰普吉--皇帝岛海豚岛双岛之旅8日';
            $data['share_desc']='旗舰普吉--皇帝岛海豚岛双岛之旅8日';
            $data['index_url']=base_url('lst/qjpjhhsd8dinfo/HY-QJPJHHSD8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qjpjhhsd8d/images/share.jpg";
            $this->load->view('qjpjhhsd8d/index',$data);
            $this->show_count();
        }



  //【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】
   public function twxy6dinfo()
   {

       $data=$this->get_day_url('twxy6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】';
       $data['share_desc']='【纯玩】- 优选版 台湾西游记6天 （直飞）  【北进高出】';
       $data['index_url']=base_url('lst/twxy6dinfo/SH-twxy6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/twxy6d/images/head.jpg";

       $this->load->view('twxy6d/index',$data);
       $this->show_count();
   }

  //加拿大东西海岸+落基山脉+露易丝湖费尔蒙城堡酒店12天体验之旅
   public function jnd12d0420info()
   {

       $data=$this->get_day_url('jnd12d0420');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='加拿大东西海岸+落基山脉+露易丝湖费尔蒙城堡酒店12天体验之旅';
       $data['share_desc']='加拿大东西海岸+落基山脉+露易丝湖费尔蒙城堡酒店12天体验之旅';
       $data['index_url']=base_url('lst/jnd12d0420info/SH-jnd12d0420-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/jnd12d0420/images/head.jpg";

       $this->load->view('jnd12d0420/index',$data);
       $this->show_count();
   }   

  //美东西大瀑布+南峡+羚羊秘境+旧金山+夏威夷15天纯美团
   public function chmg0913info()
   {

       $data=$this->get_day_url('chmg0913');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='美东西大瀑布+南峡+羚羊秘境+旧金山+夏威夷15天纯美团';
       $data['share_desc']='美东西大瀑布+南峡+羚羊秘境+旧金山+夏威夷15天纯美团';
       $data['index_url']=base_url('lst/chmg0913info/MY-chmg0913-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/chmg0913/images/head.jpg";

       $this->load->view('chmg0913/index',$data);
       $this->show_count();
   }

  //菲律宾——纯长滩四晚六天自由行
   public function flb_ct4n6dinfo()
   {

       $data=$this->get_day_url('flb_ct4n6d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='菲律宾——纯长滩四晚六天自由行';
       $data['share_desc']='菲律宾——纯长滩四晚六天自由行';
       $data['index_url']=base_url('lst/flb_ct4n6dinfo/KLJQ-flb_ct4n6d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/flb_ct4n6d/images/head.jpg";

       $this->load->view('flb_ct4n6d/index',$data);
       $this->show_count();
   }
   //中日国交45周年纪念 青少年友好交流事业 
   public function djyx7dinfo()
   {

       $data=$this->get_day_url('djyx7d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='中日国交45周年纪念 青少年友好交流事业 ）';
       $data['share_desc']='中日国交45周年纪念 青少年友好交流事业 ）';
       $data['index_url']=base_url('lst/djyx7dinfo/ZY-djyx7d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/djyx7d/images/head.jpg";

       $this->load->view('djyx7d/index',$data);
       $this->show_count();
   }

  //枫花雪岳-加拿大东西海岸落基山脉12天赏枫之旅（四星，蒙进温出）【加航直飞，品质酒店，美食，露易丝湖，班夫，枫叶美景】
   public function fhxyjnd12dinfo()
   {

       $data=$this->get_day_url('fhxyjnd12d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='枫花雪岳-加拿大东西海岸落基山脉12天赏枫之旅（四星，蒙进温出）【加航直飞，品质酒店，美食，露易丝湖，班夫，枫叶美景】';
       $data['share_desc']='枫花雪岳-加拿大东西海岸落基山脉12天赏枫之旅（四星，蒙进温出）【加航直飞，品质酒店，美食，露易丝湖，班夫，枫叶美景】';
       $data['index_url']=base_url('lst/fhxyjnd12dinfo/SH-fhxyjnd12d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/fhxyjnd12d/images/head.jpg";

       $this->load->view('fhxyjnd12d/index',$data);
       $this->show_count();
   }

       //七彩南湾深度文青之旅7天 半自由行（2晚高雄、2晚垦丁、2晚台北）（每周二， 四发班）
     public function qctw7dinfo()
     {

         $data=$this->get_day_url('qctw7d');
         $data['signPackage']=$this->wx_js_para(3);
         $data['share_title']='七彩南湾深度文青之旅7天 半自由行（2晚高雄、2晚垦丁、2晚台北）（每周二， 四发班）';
         $data['share_desc']='七彩南湾深度文青之旅7天 半自由行（2晚高雄、2晚垦丁、2晚台北）（每周二， 四发班）';
         $data['index_url']=base_url('lst/qctw7dinfo/SH-qctw7d-ZF');
         $data['shareimage']=$this->shareimage_forlx;
         $data['shareimage']="http://api.etjourney.com/public/qctw7d/images/head.jpg";

         $this->load->view('qctw7d/index',$data);
         $this->show_count();
     }

       //高雄、台北、垦丁、花莲7天 半自由行（每周二发班）
   public function tw7dinfo()
   {

       $data=$this->get_day_url('tw7d');
       $data['signPackage']=$this->wx_js_para(3);
       $data['share_title']='高雄、台北、垦丁、花莲7天 半自由行（每周二发班）';
       $data['share_desc']='高雄、台北、垦丁、花莲7天 半自由行（每周二发班）';
       $data['index_url']=base_url('lst/tw7dinfo/SH-tw7d-ZF');
       $data['shareimage']=$this->shareimage_forlx;
       $data['shareimage']="http://api.etjourney.com/public/tw7d/images/head.jpg";

       $this->load->view('tw7d/index',$data);
       $this->show_count();
   }

       //0917美国东西海岸大瀑布+2整天黄石15天纯美团/惠美团
       public function chmg15dinfo()
       {

           $data=$this->get_day_url('chmg15d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='0917美国东西海岸大瀑布+2整天黄石15天纯美团/惠美团';
           $data['share_desc']='0917美国东西海岸大瀑布+2整天黄石15天纯美团/惠美团';
           $data['index_url']=base_url('lst/chmg15dinfo/MY-chmg15d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/mg15dLJNC/images/head.jpg";

           $this->load->view('chmg15d/index',$data);
           $this->show_count();
       }

       // 美国西海岸+羚羊峡谷+纪念碑山谷+拱门国家公园+峡谷地国家公园+布莱斯峡谷国家公园13天探秘之旅【超值特价，线路独一无二】
       public function mg13dTJinfo()
       {

           $data=$this->get_day_url('mg13dTJ');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国西海岸+羚羊峡谷+纪念碑山谷+拱门国家公园+峡谷地国家公园+布莱斯峡谷国家公园13天探秘之旅【超值特价，线路独一无二】';
           $data['share_desc']='美国西海岸+羚羊峡谷+纪念碑山谷+拱门国家公园+峡谷地国家公园+布莱斯峡谷国家公园13天探秘之旅【超值特价，线路独一无二】';
           $data['index_url']=base_url('lst/mg13dTJinfo/SH-mg13dTJ-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/mg13dTJ/images/head.jpg";

           $this->load->view('mg13dTJ/index',$data);
           $this->show_count();
       }


       // 美国西海岸+旧金山+黄石公园+羚羊峡谷+马蹄湾+布莱斯峡谷+“天空之镜”13天【国航往返直飞】
       public function mg13dGHinfo()
       {

           $data=$this->get_day_url('mg13dGH');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国西海岸+旧金山+黄石公园+羚羊峡谷+马蹄湾+布莱斯峡谷+“天空之镜”13天【国航往返直飞】';
           $data['share_desc']='美国西海岸+旧金山+黄石公园+羚羊峡谷+马蹄湾+布莱斯峡谷+“天空之镜”13天【国航往返直飞】';
           $data['index_url']=base_url('lst/mg13dGHinfo/SH-mg13dGH-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/mg13dGH/images/head.jpg";

           $this->load->view('mg13dGH/index',$data);
           $this->show_count();
       }

       // 魅力狮城·新加坡一地半自由行4晚6日
       public function xjp4n6dinfo()
       {

           $data=$this->get_day_url('xjp4n6d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='魅力狮城·新加坡一地半自由行4晚6日';
           $data['share_desc']='魅力狮城·新加坡一地半自由行4晚6日';
           $data['index_url']=base_url('lst/xjp4n6dinfo/ZX-xjp4n6d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xjp4n6d/images/head.jpg";

           $this->load->view('xjp4n6d/index',$data);
           $this->show_count();
       }

       // 加拿大西海岸+缤纷极光10日游A团【加航直飞，品质酒店，温哥华，极光，品冰酒】
       public function jnd10dJGinfo()
       {

           $data=$this->get_day_url('jnd10dJG');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='加拿大西海岸+缤纷极光10日游A团【加航直飞，品质酒店，温哥华，极光，品冰酒】';
           $data['share_desc']='加拿大西海岸+缤纷极光10日游A团【加航直飞，品质酒店，温哥华，极光，品冰酒】';
           $data['index_url']=base_url('lst/jnd10dJGinfo/SH-jnd10dJG-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/jnd10dJG/images/head.jpg";

           $this->load->view('jnd10dJG/index',$data);
           $this->show_count();
       }


       // 精品小团  东京
       public function rbbcdjinfo()
       {

           $data=$this->get_day_url('rbbcdj');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='精品小团  东京';
           $data['share_desc']='精品小团  东京';
           $data['index_url']=base_url('lst/rbbcdjinfo/RB-JPXT-DJ');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/rbbcdj/images/head.jpg";

           $this->load->view('rbbcdj/index',$data);
           $this->show_count();
       }

       // 精品小团  大阪
       public function rbbcdbinfo()
       {

           $data=$this->get_day_url('rbbcdb');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='精品小团  大阪';
           $data['share_desc']='精品小团  大阪';
           $data['index_url']=base_url('lst/rbbcdbinfo/RB-JPXT-DB');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/rbbcdb/images/head.jpg";

           $this->load->view('rbbcdb/index',$data);
           $this->show_count();
       }


       // 美国西海岸+旧金山+黄石+大提顿+羚羊峡谷+马蹄湾+布莱斯峡谷13天【美联航往返，美景，摄影，达人专线】
       public function shmg13dinfo()
       {

           $data=$this->get_day_url('shmg13d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国西海岸+旧金山+黄石+大提顿+羚羊峡谷+马蹄湾+布莱斯峡谷13天【美联航往返，美景，摄影，达人专线】';
           $data['share_desc']='美国西海岸+旧金山+黄石+大提顿+羚羊峡谷+马蹄湾+布莱斯峡谷13天【美联航往返，美景，摄影，达人专线】';
           $data['index_url']=base_url('lst/shmg13dinfo/SH-MG-13D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/shmg13d/images/head.jpg";

           $this->load->view('shmg13d/index',$data);
           $this->show_count();
       }



    // 仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）
       public function sxhhy8dinfo()
       {

           $data=$this->get_day_url('sxhhy8d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/sxhhy8dinfo/RHHK-sxhhy8d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/sxhhy8d/images/share.jpg";

           $this->load->view('sxhhy8d/index',$data);
           $this->show_count();
       }
    // 仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）
       public function sxhhy8d1info()
       {

           $data=$this->get_day_url('sxhhy8d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/sxhhy8d1info/RHHK-sxhhy8d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/sxhhy8d/images/share.jpg";

           $this->load->view('sxhhy8d/index1',$data);
           $this->show_count();
       }

    // 仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）
       public function sxhhysxinfo()
       {

           $data=$this->get_day_url('sxhhysx');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='神仙的后花园喀纳斯+可可托海+禾木村+魔鬼城8日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/sxhhysxinfo/RHHK-sxhhysx-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/sxhhysx/images/head.jpg";

           $this->load->view('sxhhysx/index',$data);
           $this->show_count();
       }

       // 经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）
       public function xjjd9dinfo()
       {

           $data=$this->get_day_url('xjjd9d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
           $data['share_desc']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
           $data['index_url']=base_url('lst/xjjd9dinfo/RHHK-xjjd9d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xjjd9d/images/head1.jpg";

           $this->load->view('xjjd9d/index',$data);
           $this->show_count();
       }
       // 东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城7日深度游小包团（奔驰商务车+含丝路秀）
       public function dj7dinfo()
       {

           $data=$this->get_day_url('dj7d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城7日深度游小包团（奔驰商务车+含丝路秀）';
           $data['share_desc']='东疆畅玩天池+江布拉克+沙漠公园+火焰山+葡萄沟+交河故城7日深度游小包团（奔驰商务车+含丝路秀）';
           $data['index_url']=base_url('lst/dj7dinfo/RHHK-dj7d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/dj7d/images/head.jpg";

           $this->load->view('dj7d/index',$data);
           $this->show_count();
       }
       // 经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）
       public function xjjd9d1info()
       {

           $data=$this->get_day_url('xjjd9d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
           $data['share_desc']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
           $data['index_url']=base_url('lst/xjjd9d1info/RHHK-xjjd9d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xjjd9d/images/head.jpg";

           $this->load->view('xjjd9d/index1',$data);
           $this->show_count();
       }

       // 贯穿南北疆+喀纳斯+伊犁+风情喀什+和田+沙漠公路深度18日游（奔驰商务车，含丝路秀）
       public function gc18dinfo()
       {

           $data=$this->get_day_url('gc18d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='贯穿南北疆+喀纳斯+伊犁+风情喀什+和田+沙漠公路深度18日游（奔驰商务车，含丝路秀）';
           $data['share_desc']='贯穿南北疆+喀纳斯+伊犁+风情喀什+和田+沙漠公路深度18日游（奔驰商务车，含丝路秀）';
           $data['index_url']=base_url('lst/gc18dinfo/RHHK-gc18d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/gc18d/images/head.jpg";

           $this->load->view('gc18d/index',$data);
           $this->show_count();
       }       

       // 伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）
       public function ysdbn7dinfo()
       {

           $data=$this->get_day_url('ysdbn7d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/ysdbn7dinfo/RHHK-ysdbn7d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/ysdbn7d/images/share.jpg";

           $this->load->view('ysdbn7d/index',$data);
           $this->show_count();
       }
       // 伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）
       public function ysdbn7d1info()
       {

           $data=$this->get_day_url('ysdbn7d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/ysdbn7d1info/RHHK-ysdbn7d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/ysdbn7d/images/share.jpg";

           $this->load->view('ysdbn7d/index1',$data);
           $this->show_count();
       }
       // 伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）
       public function ysdbn7dzxinfo()
       {

           $data=$this->get_day_url('ysdbn7dzx');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/ysdbn7dzxinfo/RHHK-ysdbn7dzx-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/ysdbn7dzx/images/share.jpg";

           $this->load->view('ysdbn7dzx/index',$data);
           $this->show_count();
       }
       // 伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）
       public function ysdbn7dzx1info()
       {

           $data=$this->get_day_url('ysdbn7dzx');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['share_desc']='伊犁+赛里木湖+独库公路北段+巴音+那拉提天山南北面7日深度游小包团（奔驰商务车专享）';
           $data['index_url']=base_url('lst/ysdbn7dzx1info/RHHK-ysdbn7dzx-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/ysdbn7dzx/images/share.jpg";

           $this->load->view('ysdbn7dzx/index1',$data);
           $this->show_count();
       }
               // 赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享）
       public function xj4dinfo()
       {

           $data=$this->get_day_url('xj4d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享）';
           $data['share_desc']='赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享））';
           $data['index_url']=base_url('lst/xj4dinfo/RHHK-xj4d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xj4d/images/share.jpg";

           $this->load->view('xj4d/index',$data);
           $this->show_count();
       }
               // 赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享）
       public function xj4d1info()
       {

           $data=$this->get_day_url('xj4d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享）';
           $data['share_desc']='赛湖+那拉提草原+独库公路+巴音布鲁克4日独立成团（奔驰商务车专享））';
           $data['index_url']=base_url('lst/xj4d1info/RHHK-xj4d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xj4d/images/share.jpg";

           $this->load->view('xj4d/index1',$data);
           $this->show_count();
       }
       // 伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）
       public function xj5dinfo()
       {

           $data=$this->get_day_url('xj5d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）';
           $data['share_desc']='伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）';
           $data['index_url']=base_url('lst/xj5dinfo/RHHK-xj5d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xj5d/images/share.jpg";

           $this->load->view('xj5d/index',$data);
           $this->show_count();
       }
       // 伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）
       public function xj5d1info()
       {

           $data=$this->get_day_url('xj5d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）';
           $data['share_desc']='伊犁+巴音+那拉提+薰衣草+赛里木湖+红山大峡谷天山南北面5日品质游（奔驰商务车专享）';
           $data['index_url']=base_url('lst/xj5d1info/RHHK-xj5d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xj5d/images/share.jpg";

           $this->load->view('xj5d/index1',$data);
           $this->show_count();
       }
       // 美国东西海岸+旧金山+大瀑布+杰克逊小镇+四大国家公园+羚羊峡谷16天（一价全含，四星品质团）【美联航，旧进华出，四星品质团】
       public function shmg16dinfo()
       {

           $data=$this->get_day_url('shmg16d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国东西海岸+旧金山+大瀑布+杰克逊小镇+四大国家公园+羚羊峡谷16天（一价全含，四星品质团）【美联航，旧进华出，四星品质团】';
           $data['share_desc']='美国东西海岸+旧金山+大瀑布+杰克逊小镇+四大国家公园+羚羊峡谷16天（一价全含，四星品质团）【美联航，旧进华出，四星品质团】';
           $data['index_url']=base_url('lst/shmg16dinfo/SH-MG-16D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/shmg16d/images/head.jpg";

           $this->load->view('shmg16d/index',$data);
           $this->show_count();
       }

       // 美国西海岸+黄石+大提顿+布莱斯峡谷+西部牛仔小镇10天【美联航，特价，国庆节】
       public function shmg10dinfo()
       {

           $data=$this->get_day_url('shmg10d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国西海岸+黄石+大提顿+布莱斯峡谷+西部牛仔小镇10天【美联航，特价，国庆节】';
           $data['share_desc']='美国西海岸+黄石+大提顿+布莱斯峡谷+西部牛仔小镇10天【美联航，特价，国庆节】';
           $data['index_url']=base_url('lst/shmg10dinfo/SH-MG-10D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/shmg10d/images/head.jpg";

           $this->load->view('shmg10d/index',$data);
           $this->show_count();
       }

       // 美国西海岸+黄石+大提顿+天空之镜+布莱斯峡谷+羚羊峡谷+马蹄湾+亚利桑那陨石坑12日探秘游【美国达美航空（DL）】
       public function shmg12dinfo()
       {

           $data=$this->get_day_url('shmg12d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美国西海岸+黄石+大提顿+天空之镜+布莱斯峡谷+羚羊峡谷+马蹄湾+亚利桑那陨石坑12日探秘游【美国达美航空（DL）】';
           $data['share_desc']='美国西海岸+黄石+大提顿+天空之镜+布莱斯峡谷+羚羊峡谷+马蹄湾+亚利桑那陨石坑12日探秘游【美国达美航空（DL）】';
           $data['index_url']=base_url('lst/shmg12dinfo/SH-MG-12D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/shmg12d/images/head.jpg";

           $this->load->view('shmg12d/index',$data);
           $this->show_count();
       }


       // 东京、富士山、富士山、京都、大阪6日5晚 （阪东、1晚温泉）
       public function bzdfjd6dinfo()
       {

           $data=$this->get_day_url('bzdfjd6d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='东京、富士山、富士山、京都、大阪6日5晚 （阪东、1晚温泉）';
           $data['share_desc']='东京、富士山、富士山、京都、大阪6日5晚 （阪东、1晚温泉）';
           $data['index_url']=base_url('lst/bzdfjd6dinfo/SH-bzdfjd6d-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/bzdfjd6d/images/head.jpg";

           $this->load->view('bzdfjd6d/index',$data);
           $this->show_count();
       }

       // 德州深度7-12天半自助游
       public function dz_7_12info()
       {

           $data=$this->get_day_url('dz_7_12');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='德州深度7-12天半自助游';
           $data['share_desc']='德州深度7-12天半自助游）';
           $data['index_url']=base_url('lst/dz_7_12info/ZY-dz_7_12-5N6D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/dz_7_12/images/head.jpg";

           $this->load->view('dz_7_12/index',$data);
           $this->show_count();
       }

       // 上流社会礼仪篇（日本6天5夜）
       public function slshlypinfo()
       {

           $data=$this->get_day_url('slshlyp');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='上流社会礼仪篇（日本6天5夜）';
           $data['share_desc']='上流社会礼仪篇（日本6天5夜）';
           $data['index_url']=base_url('lst/slshlypinfo/ZY-SLSHLYP-5N6D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/slshlyp/images/head.jpg";

           $this->load->view('slshlyp/index',$data);
           $this->show_count();
       }


       // 企业主高管商务游学篇（日本7天6夜）
       public function swyxpinfo()
       {

           $data=$this->get_day_url('swyxp');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='企业主高管商务游学篇（日本7天6夜）';
           $data['share_desc']='企业主高管商务游学篇（日本7天6夜）';
           $data['index_url']=base_url('lst/swyxpinfo/ZY-SWYX-6N7D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/swyxp/images/head.jpg";

           $this->load->view('swyxp/index',$data);
           $this->show_count();
       }

       //10月15日落地拼团商务游学
       public function swyxp2info()
       {

           $data=$this->get_day_url('swyxp2');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='10月15日落地拼团商务游学';
           $data['share_desc']='10月15日落地拼团商务游学';
           $data['index_url']=base_url('lst/swyxp2info/ZY-SWYX-6N7D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/swyxp/images/head2.jpg";

           $this->load->view('swyxp2/index',$data);
           $this->show_count();
       }


       // 1020美西8天洛进洛出AA-惊爆
       public function jbmx8dinfo()
       {

           $data=$this->get_day_url('jbmx8d');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='美西8天洛进洛出AA-惊爆';
           $data['share_desc']='美西8天洛进洛出AA-惊爆';
           $data['index_url']=base_url('lst/jbmx8dinfo/MY-JBMX-8D');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/jbmx8d/images/head.jpg";

           $this->load->view('jbmx8d/index',$data);
           $this->show_count();
       }

       // 西雅图5-9天精华半自助游
       public function xyt_5_9info()
       {

           $data=$this->get_day_url('xyt_5_9');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='西雅图5-9天精华半自助游';
           $data['share_desc']='西雅图5-9天精华半自助游';
           $data['index_url']=base_url('lst/xyt_5_9info/MY-XYTSH-5_9');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/xyt_5_9/images/head.jpg";

           $this->load->view('xyt_5_9/index',$data);
           $this->show_count();
       }

       //0917A-纯美-美东西大瀑布+南峡+2整天黄石15天洛进纽出-AA
       public function mg15dLJNCinfo()
       {

           $data=$this->get_day_url('mg15dLJNC');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='0917A-纯美-美东西大瀑布+南峡+2整天黄石15天洛进纽出-AA';
           $data['share_desc']='0917A-纯美-美东西大瀑布+南峡+2整天黄石15天洛进纽出-AA';
           $data['index_url']=base_url('lst/mg15dLJNCinfo/MY-mg15dLJNC-ZF');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/mg15dLJNC/images/head.jpg";

           $this->load->view('mg15dLJNC/index',$data);
           $this->show_count();
       }

       //TY-D升级版 本州_赏艺伎_享温泉伊豆深度六日游
       public function tydsjinfo()
       {

           $data=$this->get_day_url('tydsj');
           $data['signPackage']=$this->wx_js_para(3);
           $data['share_title']='TY-D升级版 本州_赏艺伎_享温泉伊豆深度六日游';
           $data['share_desc']='TY-D升级版 本州_赏艺伎_享温泉伊豆深度六日游';
           $data['index_url']=base_url('lst/tydsjinfo/TY-D-SJ');
           $data['shareimage']=$this->shareimage_forlx;
           $data['shareimage']="http://api.etjourney.com/public/tydsj/images/head.jpg";

           $this->load->view('tydsj/index',$data);
           $this->show_count();
       }

       //1002浪漫夏威夷一地8天6晚半自助游
       public function lmxwy6n8d_1002info()
       {


           $data=$this->get_day_url2('lmxwy6n8d_1002');
           $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='1002浪漫夏威夷一地8天6晚半自助游';
            $data['share_desc']='1002浪漫夏威夷一地8天6晚半自助游';
            $data['index_url']=base_url('lst/lmxwy6n8d_1002info/MY-LMXYWY6N8D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lmxwy6n8d_1002/images/head.jpg";
          //  echo '<pre>';print_r($data);exit();
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

        //纯美-加拿大西岸+温哥华+育空白马极光9天
        public function cm9dinfo()
        {

            $data=$this->get_day_url('cm9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='纯美-加拿大西岸+温哥华+育空白马极光9天';
            $data['share_desc']='纯美-加拿大西岸+温哥华+育空白马极光9天';
            $data['index_url']=base_url('lst/cm9dinfo/MY-CM-9D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/cm9d/images/head.jpg";

            $this->load->view('cm9d/index',$data);
            $this->show_count();
        }


    //加西+惠斯勒+班芙温泉+落基山脉9天温进温出AC-品质纯玩+
        public function jx9dinfo()
        {

            $data=$this->get_day_url('jx9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='加西+惠斯勒+班芙温泉+落基山脉9天温进温出AC-品质纯玩+';
            $data['share_desc']='加西+惠斯勒+班芙温泉+落基山脉9天温进温出AC-品质纯玩+';
            $data['index_url']=base_url('lst/jx9dinfo/MY-JX-9D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jx9d/images/head.jpg";

            $this->load->view('jx9d/index',$data);
            $this->show_count();
        }


    //嗨翻普吉自由行，多条线路任你行 说是公司的行程，标题分享语都没有（标题随便天的）
        public function zxqcny2info()
        {

            $data=$this->get_day_url('zxqcny2');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='嗨翻普吉自由行，多条线路任你行';
            $data['share_desc']='嗨翻普吉自由行，多条线路任你行';
            $data['index_url']=base_url('lst/zxqcny2info/zxqc2');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zxqcny2/images/head.jpg";

            $this->load->view('zxqcny2/index',$data);
            $this->show_count();
        }

    //嗨翻普吉自由行，多条线路任你行 说是公司的行程，标题分享语都没有（标题随便天的）SH-pzsy4n5d-ZF
        public function zxqcnyinfo()
        {

            $data=$this->get_day_url('zxqcny');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='嗨翻普吉自由行，多条线路任你行';
            $data['share_desc']='嗨翻普吉自由行，多条线路任你行';
            $data['index_url']=base_url('lst/zxqcnyinfo/HY-zxqc1');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zxqcny/images/head.jpg";

            $this->load->view('zxqcny/index',$data);
            $this->show_count();
        }

        //美国东西海岸+大瀑布+黄石+大提顿+布莱斯峡谷+夏威夷17天【美联航，纽进夏出】
        public function mg17dNJXCinfo()
        {

            $data=$this->get_day_url('mg17dNJXC');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+大瀑布+黄石+大提顿+布莱斯峡谷+夏威夷17天【美联航，纽进夏出】';
            $data['share_desc']='美国东西海岸+大瀑布+黄石+大提顿+布莱斯峡谷+夏威夷17天【美联航，纽进夏出】';
            $data['index_url']=base_url('lst/mg17dNJXCinfo/SH-MG17dNJXC-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg17dNJXC/images/head.jpg";

            $this->load->view('mg17dNJXC/index',$data);
            $this->show_count();
        }

            //澳港品质双园5日
        public function pzsy4n5dinfo()
        {

            $data=$this->get_day_url('pzsy4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='澳港品质双园5日 ';
            $data['share_desc']='澳港品质双园5日 ';
            $data['index_url']=base_url('lst/pzsy4n5dinfo/SH-pzsy4n5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/pzsy4n5d/images/share.jpg";

            $this->load->view('pzsy4n5d/index',$data);
            $this->show_count();
        }

        //澳港纯玩双园5日
        public function cwsy4n5dinfo()
        {

            $data=$this->get_day_url('cwsy4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='澳港纯玩双园5日 ';
            $data['share_desc']='澳港纯玩双园5日 ';
            $data['index_url']=base_url('lst/cwsy4n5dinfo/SH-cwsy4n5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/cwsy4n5d/images/share.jpg";

            $this->load->view('cwsy4n5d/index',$data);
            $this->show_count();
        }
        //1012美东+迈阿密+奥兰多10天惊爆团
        public function md10djbtinfo()
        {

            $data=$this->get_day_url('md10djbt');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='1012美东+迈阿密+奥兰多10天惊爆团';
            $data['share_desc']='1012美东+迈阿密+奥兰多10天惊爆团';
            $data['index_url']=base_url('lst/md10djbtinfo/MY-JBT-10D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/md10djbt/images/head.jpg";

            $this->load->view('md10djbt/index',$data);
            $this->show_count();
        }

        //1016A迈阿密+奥兰多9天惊爆团
        public function mam9djbtinfo()
        {

            $data=$this->get_day_url('mam9djbt');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='1016A迈阿密+奥兰多9天惊爆团';
            $data['share_desc']='1016A迈阿密+奥兰多9天惊爆团';
            $data['index_url']=base_url('lst/mam9djbtinfo/MY-JBT-9D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mam9djbt/images/head.jpg";

            $this->load->view('mam9djbt/index',$data);
            $this->show_count();
        }

        //浪漫夏威夷一地8天6晚
        public function lmxwy6n8dinfo()
        {

            $data=$this->get_day_url('lmxwy6n8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='浪漫夏威夷一地8天6晚';
            $data['share_desc']='浪漫夏威夷一地8天6晚';
            $data['index_url']=base_url('lst/lmxwy6n8dinfo/MY-LMXYWY6N8D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lmxwy6n8d/images/head.jpg";

            $this->load->view('lmxwy6n8d/index',$data);
            $this->show_count();
        }



        //美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美
        public function mg18dcm2info()
        {

            $data=$this->get_day_url('mg18dcm2');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美';
            $data['share_desc']='美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美';
            $data['index_url']=base_url('lst/mg18dcm2info/MY-MG18DCM-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg18dcm2/images/share.jpg";

            $this->load->view('mg18dcm2/index',$data);
            $this->show_count();
        }


        //美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美 与mg18dcm2info合并为一个 已废
        public function mg18dCMinfo()
        {

            $data=$this->get_day_url('mg18dCM');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美';
            $data['share_desc']='美东西大瀑布+2整天黄石+秘境羚羊彩穴+三大国家公园18天纯美';
            $data['index_url']=base_url('lst/mg18dCMinfo/MY-MG18DCM-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg18dCM/images/share.jpg";

            $this->load->view('mg18dCM/index',$data);
            $this->show_count();
        }

        //美西+旧金山十七里湾+秘境羚羊彩穴+三大国家公园+夏12天纯美
        public function mg12dcminfo()
        {

            $data=$this->get_day_url('mg12dcm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美西+旧金山十七里湾+秘境羚羊彩穴+三大国家公园+夏12天纯美';
            $data['share_desc']='美西+旧金山十七里湾+秘境羚羊彩穴+三大国家公园+夏12天纯美';
            $data['index_url']=base_url('lst/mg12dcminfo/MY-12DCM');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg12dcm/images/share.jpg";

            $this->load->view('mg12dcm/index',$data);
            $this->show_count();
        }


        //惊爆-美国西海岸+墨西哥+双OUTLET双国10天洛进洛出
        public function jbsginfo()
        {

            $data=$this->get_day_url('jbsg');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='惊爆-美国西海岸+墨西哥+双OUTLET双国10天洛进洛出';
            $data['share_desc']='惊爆-美国西海岸+墨西哥+双OUTLET双国10天洛进洛出';
            $data['index_url']=base_url('lst/jbsginfo/MY-MX-JBSG-10D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jbsg/images/share.jpg";

            $this->load->view('jbsg/index',$data);
            $this->show_count();
        }


    //浪漫威尼斯_激情赛船节 VIP金牌法意瑞13日 FCO_CDG 意签 （LH）
        public function fyr13dinfo()
        {

            $data=$this->get_day_url('fyr13d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='浪漫威尼斯_激情赛船节 VIP金牌法意瑞13日 FCO_CDG 意签 （LH）';
            $data['share_desc']='浪漫威尼斯_激情赛船节 VIP金牌法意瑞13日 FCO_CDG 意签 （LH）';
            $data['index_url']=base_url('lst/fyr13dinfo/ZXZTYB-FYR-13D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/fyr13d/images/share.jpg";

            $this->load->view('fyr13d/index',$data);
            $this->show_count();
        }

    //情陷撒哈拉-突尼斯+摩洛哥13日（TK）
        public function tns13dinfo()
        {

            $data=$this->get_day_url('tns13d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='情陷撒哈拉-突尼斯+摩洛哥13日（TK）';
            $data['share_desc']='情陷撒哈拉-突尼斯+摩洛哥13日（TK）';
            $data['index_url']=base_url('lst/tns13dinfo/HBZX-TNS13d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tns13d/images/share.jpg";

            $this->load->view('tns13d/index',$data);
            $this->show_count();
        }

    //名师带路追逐花儿与少年的足迹纳米比亚摄影12日 （ET）
        public function nmby12dinfo()
        {

            $data=$this->get_day_url('nmby12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='名师带路追逐花儿与少年的足迹纳米比亚摄影12日 （ET）';
            $data['share_desc']='名师带路追逐花儿与少年的足迹纳米比亚摄影12日 （ET）';
            $data['index_url']=base_url('lst/nmby12dinfo/HBZX-NMBY12d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/nmby12d/images/share.jpg";

            $this->load->view('nmby12d/index',$data);
            $this->show_count();
        }

    //酷爸靓妈萌宝“象”前冲
        public function mbx8dinfo()
        {

            $data=$this->get_day_url('mbx8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='酷爸靓妈萌宝“象”前冲';
            $data['share_desc']='曼谷、芭提雅、大象岛8日亲子游';
            $data['index_url']=base_url('lst/mbx8dinfo/HY-MNX8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mbx8d/images/share.jpg";

            $this->load->view('mbx8d/index',$data);
            $this->show_count();
        }

        // 全疆门票套票签约景区名录及服务内容
        public function taopiao2info()
        {
            $data=$this->get_day_url('taopiao2');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='全疆门票套票签约景区名录及服务内容';
            $data['share_desc']='全疆门票套票签约景区名录及服务内容';
            $data['index_url']=base_url('lst/taopiao2info/KKT-TIAOPIAO-etjourney');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/taopiao2/images/head.jpg";
            $a=$this->uri->segment(4);
            // var_dump($a);die;

            $data['id']=$a;

            $data['url']=base_url("lst/kaka_dd/$a");
            $data['urls']=base_url("lst/kaka_xd/id/$a");

            //  echo "<pre>";
            //   print_r($data);
            //   echo "</pre>";
            $this->load->view('taopiao2/index',$data);
            $this->show_count();
        }
    //卡卡通页面流程

  public function kaka_dd($id){
  
  $data['id']=$id;

   $where ="user_id=$id";
   $is_off=$this->input->get('is_off',TRUE);

if($is_off==1){

  $where=" order_status=1 AND user_id=$id";
}else if($is_off==2){
$where =" order_status=0 AND user_id=$id";
}

$data['info']=$this->User_model->get_all($select='*',$where,'kk_order_info',$order_title='order_id','desc');
//echo $this->db->last_query();
foreach($data['info'] as $k=>$v){
  $data['info'][$k]['names']=json_decode($v['consignee']);
  $data['info'][$k]['tel']=json_decode($v['mobile']);
  $data['info'][$k]['card']=json_decode($v['card_id']);
/**for($i=0;$i<count($data['info'][$k]['names']);$i++){
$data['info'][$k]['xinxi'][$i]=array(
 'names'=>$data['info'][$k]['names'][$i],
 'tel'=>$data['info'][$k]['tel'][$i],
 'card'=>$data['info'][$k]['card'][$i]

  );


}**/

}
$data['url']=base_url("Api1/kaka_zfb");
//
//echo "<pre>";
//print_r($data);
//echo "</pre>";

 $this->load->view('taopiao2/my',$data);

 }


     //卡卡通下单页面、
    public function kaka_xd(){

        $data['id']=$this->uri->segment(4);
        $data['url']=base_url('kaka_list/kaka_sub');
        $a=$this->uri->segment(4);
        // var_dump($a);die;
        $data['id']=$a;
        $data['url']=base_url("kaka_list/kaka_sub/$a");
        $data['urls']=base_url("kaka_list/kaka_xd/id/$a");
        $data['urlz']=base_url("kaka_list/kaka_dd/$a");

        $this->load->view('taopiao2/buy',$data);
    }
     //卡卡通提交订单
    public function kaka_sub($id){
        $data['user_id']=$id;
        $data['money']=$this->input->post('money',TRUE);
        $data['consignee']=$this->input->post('username',TRUE);
        $data['card_id']=$this->input->post('account',TRUE);
        $data['mobile']=$this->input->post('tel');
        $sn=rand(100000,999999);
        $order=date('Ymd',time());
        $order_sn=$order.$sn;


        for($i=0;$i<count($data['consignee']);$i++){
            $sn=rand(100000,999999);
            $order=date('Ymd',time());
            $order_sn=$order.$sn;

            $datas[$i]=array(
                'user_id'=>$data['user_id'],
                'money'=>$data['money'],
                'consignee'=>$data['consignee'][$i],
                'card_id'=>$data['card_id'][$i],
                'mobile'=>$data['mobile'][$i],
                'order_sn'=>$order_sn,
                'h5_title'=>'新疆游，全疆门票',
                'add_time'=>time()

            );

        }
        //echo "<pre>";
        //print_r($datas);
        //echo "</pre>";die;
        $time=time();
        $red=$this->db->insert_batch('kk_order_info',$datas);
        if($red){
            redirect(base_url("kaka_list/kaka_zf/$id/$time"));

        }

     }

         //卡卡通支付界面
    public function kaka_zf($id,$time){
        $where ="1=1";
        if(!empty($id) && !empty($time)){
            $where = " user_id=$id AND add_time=$time";
        }
        $data['info']=$this->User_model->get_all($select='*',$where,'kk_order_info',$order_title='order_id');
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";

        $this->load->view('taopiao2/success',$data);
    }

    // 全疆门票套票签约景区名录及服务内容
    public function taopiaoinfo(){

        $data=$this->get_day_url('taopiao');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='全疆门票套票签约景区名录及服务内容';
        $data['share_desc']='全疆门票套票签约景区名录及服务内容';
        $data['index_url']=base_url('lst/taopiaoinfo/KKT-TIAOPIAO');
        $data['shareimage']=$this->shareimage_forlx;
        $data['shareimage']="http://api.etjourney.com/public/taopiao/images/head.jpg";

        $this->load->view('taopiao/index',$data);
        $this->show_count();
    }

    // 美国东西海岸+大瀑布+三大国家公园+西部牛仔小镇+羚羊峡谷探秘15天【美国航空，纽进洛出】
        public function mg15dinfo()
        {

            $data=$this->get_day_url('mg15d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+大瀑布+三大国家公园+西部牛仔小镇+羚羊峡谷探秘15天【美国航空，纽进洛出】';
            $data['share_desc']='美国东西海岸+大瀑布+三大国家公园+西部牛仔小镇+羚羊峡谷探秘15天【美国航空，纽进洛出】';
            $data['index_url']=base_url('lst/mg15dinfo/SH-MG-15D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg15d/images/head.jpg";

            $this->load->view('mg15d/index',$data);
            $this->show_count();
        }


    // 象模像YOUNG--------曼谷+芭提雅+象岛10日之旅
        public function dxd10dinfo()
        {

            $data=$this->get_day_url('dxd10d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='象模像YOUNG--------曼谷+芭提雅+象岛10日之旅';
            $data['share_desc']='象模像YOUNG--------曼谷+芭提雅+象岛10日之旅';
            $data['index_url']=base_url('lst/dxd10dinfo/HY-DXD10d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/dxd10d/images/share.jpg";

            $this->load->view('dxd10d/index',$data);
            $this->show_count();
        }


    // 加拿大西海岸+落基山脉+温泉体验9日游【露易丝湖升级入住费尔蒙城堡酒店，特色餐】
        public function jnd9dinfo()
        {

            $data=$this->get_day_url('jnd9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='加拿大西海岸+落基山脉+温泉体验9日游【露易丝湖升级入住费尔蒙城堡酒店，特色餐】';
            $data['share_desc']='加拿大西海岸+落基山脉+温泉体验9日游【露易丝湖升级入住费尔蒙城堡酒店，特色餐】';
            $data['index_url']=base_url('lst/jnd9dinfo/SH-JND9d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jnd9d/images/share.jpg";

            $this->load->view('jnd9d/index',$data);
            $this->show_count();
        }

        // 东京富士山京都大阪经典八日半自助游（名古屋进出）
        public function rbmgw8dinfo()
        {

            $data=$this->get_day_url('rbmgw8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东京富士山京都大阪经典八日半自助游（名古屋进出）';
            $data['share_desc']='东京富士山京都大阪经典八日半自助游（名古屋进出）';
            $data['index_url']=base_url('lst/rbmgw8dinfo/SH-MGWJC-8D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbmgw8d/images/head.jpg";

            $this->load->view('rbmgw8d/index',$data);
            $this->show_count();
        }


        // 美国东西海岸大瀑布+2整天黄石+旧金山+夏威夷四大国家公园18天纽进夏出MU-纯美
        public function mg18dinfo()
        {

            $data=$this->get_day_url('mg18d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸大瀑布+2整天黄石+旧金山+夏威夷四大国家公园18天纽进夏出MU-纯美';
            $data['share_desc']='美国东西海岸大瀑布+2整天黄石+旧金山+夏威夷四大国家公园18天纽进夏出MU-纯美';
            $data['index_url']=base_url('lst/mg18dinfo/MY-18D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg18d/images/head.jpg";

            $this->load->view('mg18d/index',$data);
            $this->show_count();
        }


        // 本州双古都6日游
        public function bzsgd6dinfo()
        {

            $data=$this->get_day_url('bzsgd6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='本州双古都6日游';
            $data['share_desc']='本州双古都6日游';
            $data['index_url']=base_url('lst/bzsgd6dinfo/SH-RB-6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bzsgd6d/images/head.jpg";

            $this->load->view('bzsgd6d/index',$data);
            $this->show_count();
        }

    //美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+布莱斯峡谷14天【美国航空，洛进纽出】
        public function mg14dLJNCinfo()
        {

            $data=$this->get_day_url('mg14dLJNC');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+布莱斯峡谷14天【美国航空，洛进纽出】';
            $data['share_desc']='美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+布莱斯峡谷14天【美国航空，洛进纽出】';
            $data['index_url']=base_url('lst/mg14dLJNCinfo/SH-mg14d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg14dLJNC/images/share.jpg";

            $this->load->view('mg14dLJNC/index',$data);
            $this->show_count();
        }

        // 美国东西海岸+大瀑布+布莱斯峡谷+羚羊峡谷+马蹄湾14天【美航(AA)，特价团】
        public function mg14dAAinfo()
        {

            $data=$this->get_day_url('mg14dAA');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+大瀑布+布莱斯峡谷+羚羊峡谷+马蹄湾14天【美航(AA)，特价团】';
            $data['share_desc']='美国东西海岸+大瀑布+布莱斯峡谷+羚羊峡谷+马蹄湾14天【美航(AA)，特价团】';
            $data['index_url']=base_url('lst/mg14dAAinfo/SH-mg14d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg14dAA/images/share.jpg";

            $this->load->view('mg14dAA/index',$data);
            $this->show_count();
        }

        // 美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+“天空之镜”+布莱斯峡谷15天【达美航空(DL)往返，三座国家公园】
        public function mg15dDLinfo()
        {

            $data=$this->get_day_url('mg15dDL');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+“天空之镜”+布莱斯峡谷15天【达美航空(DL)往返，三座国家公园】';
            $data['share_desc']='美国东西海岸+大瀑布+大提顿+黄石+杰克逊小镇+“天空之镜”+布莱斯峡谷15天【达美航空(DL)往返，三座国家公园】';
            $data['index_url']=base_url('lst/mg15dDLinfo/SH-mg15d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg15dDL/images/share.jpg";

            $this->load->view('mg15dDL/index',$data);
            $this->show_count();
        }

        // 西雅图+黄石奇幻8-10日盛景游
        public function xyt_8_10info()
        {

            $data=$this->get_day_url('xyt_8_10');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='西雅图+黄石奇幻8-10日盛景游';
            $data['share_desc']='西雅图+黄石奇幻8-10日盛景游';
            $data['index_url']=base_url('lst/xyt_8_10info/MY-XYTSH-8_10');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xyt_8_10/images/head.jpg";

            $this->load->view('xyt_8_10/index',$data);
            $this->show_count();
        }


        // 寰游俄罗斯---金环谢镇浪漫庄园6晚8日
        public function lmzy6n8dinfo()
        {

            $data=$this->get_day_url('lmzy6n8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='寰游俄罗斯---金环谢镇浪漫庄园6晚8日';
            $data['share_desc']='寰游俄罗斯---金环谢镇浪漫庄园6晚8日';
            $data['index_url']=base_url('lst/lmzy6n8dinfo/HY-lmzy6n8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lmzy6n8d/images/head.jpg";

            $this->load->view('lmzy6n8d/index',$data);
            $this->show_count();
        }

            // 6、（三星寰游）7月2.8.9.15.16.22.23.29.30日 三星 早集合 俄罗斯浪漫庄园6晚8日（阿斯塔纳往返三星 ）
        public function lmzy6n8d3xinfo()
        {

            $data=$this->get_day_url('lmzy6n8d3x');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 三星 早集合 俄罗斯浪漫庄园6晚8日（阿斯塔纳往返三星 ）';
            $data['share_desc']=' 三星 早集合 俄罗斯浪漫庄园6晚8日（阿斯塔纳往返三星 ）';
            $data['index_url']=base_url('lst/lmzy6n8d3xinfo/HY-lmzy6n8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lmzy6n8d3x/images/head.jpg";

            $this->load->view('lmzy6n8d3x/index',$data);
            $this->show_count();
        }

            // 新马波德申6晚8天行程（包机0527）
        public function xmbds6n8dinfo()
        {

            $data=$this->get_day_url('xmbds6n8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新马波德申6晚8天行程（包机0527）';
            $data['share_desc']='新马波德申6晚8天行程（包机0527）';
            $data['index_url']=base_url('lst/xmbds6n8dinfo/HY-xmbds6n8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xmbds6n8d/images/head.jpg";

            $this->load->view('xmbds6n8d/index',$data);
            $this->show_count();
        }

            // 亲悦双城新马亲子6晚8天行程(包机0526)
        public function xmqzy6n8dinfo()
        {

            $data=$this->get_day_url('xmqzy6n8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='亲悦双城新马亲子6晚8天行程(包机0526)';
            $data['share_desc']='亲悦双城新马亲子6晚8天行程(包机0526)';
            $data['index_url']=base_url('lst/xmqzy6n8dinfo/HY-xmqzy6n8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xmqzy6n8d/images/head.jpg";

            $this->load->view('xmqzy6n8d/index',$data);
            $this->show_count();
        }



        // 开启8月芝加哥俊杰音乐节（6天5晚）
        public function zjgyyjinfo()
        {

            $data=$this->get_day_url('zjgyyj');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='开启8月芝加哥俊杰音乐节（6天5晚）';
            $data['share_desc']='开启8月芝加哥俊杰音乐节（6天5晚）';
            $data['index_url']=base_url('lst/zjgyyjinfo/MY-ZJG-6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zjgyyj/images/head.jpg";

            $this->load->view('zjgyyj/index',$data);
            $this->show_count();
        }




        // 洛杉矶汽车展（8天7晚）
        public function lsjqczinfo()
        {

            $data=$this->get_day_url('lsjqcz');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='洛杉矶汽车展（8天7晚）';
            $data['share_desc']='洛杉矶汽车展（8天7晚）';
            $data['index_url']=base_url('lst/lsjqczinfo/MY-LSJ-8D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lsjqcz/images/head.jpg";

            $this->load->view('lsjqcz/index',$data);
            $this->show_count();
        }



        // 【主题旅游-节庆系列】2017年10月28日 墨西哥亡灵节+古巴墨西哥探索千年文明深度14日（AA）
        public function mxg14dinfo()
        {

            $data=$this->get_day_url('mxg14d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【主题旅游-节庆系列】2017年10月28日 墨西哥亡灵节+古巴墨西哥探索千年文明深度14日（AA）';
            $data['share_desc']='【主题旅游-节庆系列】2017年10月28日 墨西哥亡灵节+古巴墨西哥探索千年文明深度14日（AA）';
            $data['index_url']=base_url('lst/mxg14dinfo/ZXZTYB-MXG');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mxg14d/images/head.jpg";

            $this->load->view('mxg14d/index',$data);
            $this->show_count();
        }


        // 1016加拿大东西海岸+落基山脉12天多进温出AC
        public function jnd12dinfo()
        {

            $data=$this->get_day_url('jnd12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='1016加拿大东西海岸+落基山脉12天多进温出AC';
            $data['share_desc']='1016加拿大东西海岸+落基山脉12天多进温出AC';
            $data['index_url']=base_url('lst/jnd12dinfo/MY-JND-12D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jnd12d/images/head.jpg";

            $this->load->view('jnd12d/index',$data);
            $this->show_count();
        }

        // 【主题旅游-节庆&摄影系列】名师带路印度普什卡骆驼节摄影团15日
        public function ydsy15dinfo()
        {

            $data=$this->get_day_url('ydsy15d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【主题旅游-节庆&摄影系列】名师带路印度普什卡骆驼节摄影团15日';
            $data['share_desc']='【主题旅游-节庆&摄影系列】名师带路印度普什卡骆驼节摄影团15日';
            $data['index_url']=base_url('lst/ydsy15dinfo/ZXZTYB-YD-15D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ydsy15d/images/head.jpg";

            $this->load->view('ydsy15d/index',$data);
            $this->show_count();
        }

        // 【主题旅游-节庆系列】“番茄也疯狂”西红柿节嗨翻天 西班牙一地深度12日（LH）
        public function xby12dinfo()
        {

            $data=$this->get_day_url('xby12d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【主题旅游-节庆系列】“番茄也疯狂”西红柿节嗨翻天 西班牙一地深度12日（LH）';
            $data['share_desc']='【主题旅游-节庆系列】“番茄也疯狂”西红柿节嗨翻天 西班牙一地深度12日（LH）';
            $data['index_url']=base_url('lst/xby12dinfo/ZXZTYB-XBY-12D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xby12d/images/head.jpg";

            $this->load->view('xby12d/index',$data);
            $this->show_count();
        }


        // 寰游普吉-------- 遇见海豚岛之旅7日
        public function yjpjhtzl7dinfo()
        {

            $data=$this->get_day_url('yjpjhtzl7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='寰游普吉-------- 遇见海豚岛之旅7日';
            $data['share_desc']='寰游普吉-------- 遇见海豚岛之旅7日';
            $data['index_url']=base_url('lst/yjpjhtzl7dinfo/HY-YJPJHTZL7d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/yjpjhtzl7d/images/share.jpg";

            $this->load->view('yjpjhtzl7d/index',$data);
            $this->show_count();
        }

        // 旗舰普吉--------皇帝岛海豚岛双岛之旅9日
        public function qjpjhhsd9dinfo()
        {

            $data=$this->get_day_url('qjpjhhsd9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='旗舰普吉--------皇帝岛海豚岛双岛之旅9日';
            $data['share_desc']='旗舰普吉--------皇帝岛海豚岛双岛之旅9日';
            $data['index_url']=base_url('lst/qjpjhhsd9dinfo/HY-QJPJHHSD9d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qjpjhhsd9d/images/share.jpg";

            $this->load->view('qjpjhhsd9d/index',$data);
            $this->show_count();
        }

        //别YOYNG锡兰  ----初始斯里兰卡9日之旅
        public function sllk6n9dinfo()
        {

            $data=$this->get_day_url('sllk6n9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='别YOYNG锡兰  ----初始斯里兰卡9日之旅 ';
            $data['share_desc']='别YOYNG锡兰  ----初始斯里兰卡9日之旅 ';
            $data['index_url']=base_url('lst/sllk6n9dinfo/HY-SLLK6N9D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sllk6n9d/images/head.jpg";

            $this->load->view('sllk6n9d/index',$data);
            $this->show_count();
        }

        //旗舰锡兰 · 茶山深度  ----斯里兰卡8日之旅
        public function sllkcszl8dinfo()
        {

            $data=$this->get_day_url('sllkcszl8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='旗舰锡兰 · 茶山深度  ----斯里兰卡8日之旅';
            $data['share_desc']='旗舰锡兰 · 茶山深度  ----斯里兰卡8日之旅';
            $data['index_url']=base_url('lst/sllkcszl8dinfo/HY-SLLKCSZL8d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sllkcszl8d/images/head.jpg";

            $this->load->view('sllkcszl8d/index',$data);
            $this->show_count();
        }


        // 珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心）
        public function zzg6t5w1info()
        {

            $data=$this->get_day_url('zzg6t5w1');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心）';
            $data['share_desc']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心）';
            $data['index_url']=base_url('lst/zzg6t5w1info/MY-ZZG-6T5W1');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zzg6t5w1/images/head.jpg";

            $this->load->view('zzg6t5w1/index',$data);
            $this->show_count();
        }

        // 珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）
        public function zzg6t5w2info()
        {

            $data=$this->get_day_url('zzg6t5w2');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）';
            $data['share_desc']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）';
            $data['index_url']=base_url('lst/zzg6t5w2info/MY-ZZG-6T5W2');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zzg6t5w2/images/head.jpg";

            $this->load->view('zzg6t5w2/index',$data);
            $this->show_count();
        }

        // 珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛一日游）
        public function zzg6t5w3info()
        {

            $data=$this->get_day_url('zzg6t5w3');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛一日游）';
            $data['share_desc']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛一日游）';
            $data['index_url']=base_url('lst/zzg6t5w3info/MY-ZZG-6T5W3');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zzg6t5w3/images/head.jpg";

            $this->load->view('zzg6t5w3/index',$data);
            $this->show_count();
        }
        // 珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+茂宜岛一日游）
        public function zzg6t5w4info()
        {

            $data=$this->get_day_url('zzg6t5w4');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+茂宜岛一日游）';
            $data['share_desc']='珍珠港 & 市区观光6天5夜当地游（含小环岛精华游+波利尼西亚文化中心+茂宜岛一日游）';
            $data['index_url']=base_url('lst/zzg6t5w4info/MY-ZZG-6T5W4');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zzg6t5w4/images/head.jpg";

            $this->load->view('zzg6t5w4/index',$data);
            $this->show_count();
        }



        // 珍珠港 & 市区观光7天6夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）
        public function zzg7t6winfo()
        {

            $data=$this->get_day_url('zzg7t6w');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光7天6夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）';
            $data['share_desc']='珍珠港 & 市区观光7天6夜当地游（含小环岛精华游+波利尼西亚文化中心+大岛&茂宜岛二日游）';
            $data['index_url']=base_url('lst/zzg7t6winfo/MY-ZZG-7T6W');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zzg7t6w/images/head.jpg";

            $this->load->view('zzg7t6w/index',$data);
            $this->show_count();
        }

        // 大阪 奈良 京都当地5日游
        public function rbdd5dinfo()
        {

            $data=$this->get_day_url('rbdd5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 大阪 奈良 京都当地5日游';
            $data['share_desc']=' 大阪 奈良 京都当地5日游';
            $data['index_url']=base_url('lst/rbdd5dinfo/Easywin-rbdd5d-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbdd5d/images/head.jpg";

            $this->load->view('rbdd5d/index',$data);
            $this->show_count();
        }

        // 珍珠港 & 市区观光3天2夜当地游
        public function my_zzg2n3dinfo()
        {

            $data=$this->get_day_url('my_zzg2n3d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光3天2夜当地游';
            $data['share_desc']=' 珍珠港 & 市区观光3天2夜当地游';
            $data['index_url']=base_url('lst/my_zzg2n3dinfo/MY-ZZG-2N3D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg2n3d/images/share.jpg";

            $this->load->view('my_zzg2n3d/index',$data);
            $this->show_count();
        }

        // 珍珠港 & 市区观光4天3夜当地游（含波利尼西亚文化中心）
        public function my_zzg3n4d_blnxyinfo()
        {

            $data=$this->get_day_url('my_zzg3n4d_blnxy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='珍珠港 & 市区观光4天3夜当地游（含波利尼西亚文化中心）';
            $data['share_desc']='珍珠港 & 市区观光4天3夜当地游（含波利尼西亚文化中心）';
            $data['index_url']=base_url('lst/my_zzg3n4d_blnxyinfo/MY-ZZGblnxy-3N4D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg3n4d_blnxy/images/share.jpg";

            $this->load->view('my_zzg3n4d_blnxy/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光4天3夜当地游（含小环岛精华游）
        public function my_zzg3n4d_xhdinfo()
        {

            $data=$this->get_day_url('my_zzg3n4d_xhd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光4天3夜当地游（含小环岛精华游）';
            $data['share_desc']=' 珍珠港 & 市区观光4天3夜当地游（含小环岛精华游）';
            $data['index_url']=base_url('lst/my_zzg3n4d_xhdinfo/MY-ZZGxhd-3N4D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg3n4d_xhd/images/share.jpg";

            $this->load->view('my_zzg3n4d_xhd/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光5天4夜当地游（含波利尼西亚文化中心）
        public function my_zzg4n5d_blnxyinfo()
        {

            $data=$this->get_day_url('my_zzg4n5d_blnxy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光5天4夜当地游（含波利尼西亚文化中心）';
            $data['share_desc']=' 珍珠港 & 市区观光5天4夜当地游（含波利尼西亚文化中心）';
            $data['index_url']=base_url('lst/my_zzg4n5d_blnxyinfo/MY-ZZGblnxy-4N5D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg4n5d_blnxy/images/share.jpg";

            $this->load->view('my_zzg4n5d_blnxy/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游）
        public function my_zzg4n5d_xhdinfo()
        {

            $data=$this->get_day_url('my_zzg4n5d_xhd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游）';
            $data['share_desc']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游）';
            $data['index_url']=base_url('lst/my_zzg4n5d_xhdinfo/MY-ZZGxhd-4N5D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg4n5d_xhd/images/share.jpg";

            $this->load->view('my_zzg4n5d_xhd/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+波利尼西亚文化中心）
        public function my_zzg4n5d_xhd_blnxyinfo()
        {

            $data=$this->get_day_url('my_zzg4n5d_xhd_blnxy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+波利尼西亚文化中心）';
            $data['share_desc']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+波利尼西亚文化中心）';
            $data['index_url']=base_url('lst/my_zzg4n5d_xhd_blnxyinfo/MY-ZZGxhdblnxy-4N5D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg4n5d_xhd_blnxy/images/share.jpg";

            $this->load->view('my_zzg4n5d_xhd_blnxy/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+茂宜岛一日游）
        public function my_zzg4n5d_xhd_mydinfo()
        {

            $data=$this->get_day_url('my_zzg4n5d_xhd_myd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+茂宜岛一日游）';
            $data['share_desc']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+茂宜岛一日游）';
            $data['index_url']=base_url('lst/my_zzg4n5d_xhd_mydinfo/MY-ZZGxhdmyd-4N5D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg4n5d_xhd_myd/images/share.jpg";

            $this->load->view('my_zzg4n5d_xhd_myd/index',$data);
            $this->show_count();
        }

            // 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+大岛一日游）
        public function my_zzg4n5d_xhd_ddinfo()
        {

            $data=$this->get_day_url('my_zzg4n5d_xhd_dd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+大岛一日游）';
            $data['share_desc']=' 珍珠港 & 市区观光5天4夜当地游（含小环岛精华游+大岛一日游）';
            $data['index_url']=base_url('lst/my_zzg4n5d_xhd_ddinfo/MY-ZZGxhddd-4N5D-ZF');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/my_zzg4n5d_xhd_dd/images/share.jpg";

            $this->load->view('my_zzg4n5d_xhd_dd/index',$data);
            $this->show_count();
        }
        // 东航沙巴5晚7天半自由行
        public function dhsb5n7dinfo()
        {

            $data=$this->get_day_url('dhsb5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 东航沙巴5晚7天半自由行';
            $data['share_desc']=' 东航沙巴5晚7天半自由行';
            $data['index_url']=base_url('lst/dhsb5n7dinfo/KLJQ-DHSB-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/dhsb5n7d/images/share.jpg";

            $this->load->view('dhsb5n7d/index',$data);
            $this->show_count();
        }

        // 东航沙巴4晚6天半自由行
        public function dhsb4n6dinfo()
        {

            $data=$this->get_day_url('dhsb4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 东航沙巴4晚6天半自由行';
            $data['share_desc']=' 东航沙巴4晚6天半自由行';
            $data['index_url']=base_url('lst/dhsb4n6dinfo/KLJQ-DHSB-4N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/dhsb4n6d/images/share.jpg";

            $this->load->view('dhsb4n6d/index',$data);
            $this->show_count();
        }

        // 马航沙巴4N6D半自由行
        public function mhsb4n6dinfo()
        {

            $data=$this->get_day_url('mhsb4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 马航沙巴4N6D半自由行';
            $data['share_desc']=' 马航沙巴4N6D半自由行';
            $data['index_url']=base_url('lst/mhsb4n6dinfo/KLJQ-MHSB-4N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mhsb4n6d/images/share.jpg";

            $this->load->view('mhsb4n6d/index',$data);
            $this->show_count();
        }

        // 马航沙巴3N5D半自由行
        public function mhsb3n5dinfo()
        {

            $data=$this->get_day_url('mhsb3n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 马航沙巴3N5D半自由行';
            $data['share_desc']=' 马航沙巴3N5D半自由行';
            $data['index_url']=base_url('lst/mhsb3n5dinfo/KLJQ-MHSB-3N5D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mhsb3n5d/images/share.jpg";

            $this->load->view('mhsb3n5d/index',$data);
            $this->show_count();
        }

        // 马航沙巴仙本娜5N7D自由行
        public function sbxbn5n7dinfo()
        {

            $data=$this->get_day_url('sbxbn5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 航沙巴仙本娜5N7D自由行';
            $data['share_desc']=' 航沙巴仙本娜5N7D自由行';
            $data['index_url']=base_url('lst/sbxbn5n7dinfo/KLJQ-MHSBXBN-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sbxbn5n7d/images/share.jpg";

            $this->load->view('sbxbn5n7d/index',$data);
            $this->show_count();
        }

        // 马航沙巴仙本那斗湖3N5D半自由行
        public function medf3n5dinfo()
        {

            $data=$this->get_day_url('medf3n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 马航沙巴仙本那斗湖3N5D半自由行';
            $data['share_desc']=' 马航沙巴仙本那斗湖3N5D半自由行';
            $data['index_url']=base_url('lst/medf3n5dinfo/KLJQ-MEDF-3N5D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/medf3n5d/images/share.jpg";

            $this->load->view('medf3n5d/index',$data);
            $this->show_count();
        }

        // 西班牙摄影团
        public function xbysytinfo()
        {

            $data=$this->get_day_url('xbysyt');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 西班牙摄影团';
            $data['share_desc']=' 西班牙摄影团';
            $data['index_url']=base_url('lst/xbysytinfo/ZXZTYB-XBY');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xbysyt/images/share.jpg";

            $this->load->view('xbysyt/index',$data);
            $this->show_count();
        }

        // 惊爆-美西8天洛进洛出-UA+
        public function jbmxinfo()
        {

            $data=$this->get_day_url('jbmx');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 惊爆-美西8天洛进洛出-UA+';
            $data['share_desc']=' 惊爆-美西8天洛进洛出-UA+';
            $data['index_url']=base_url('lst/jbmxinfo/MY-LJLC-8D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jbmx/images/share.jpg";

            $this->load->view('jbmx/index',$data);
            $this->show_count();
        }

        // 本州花田巡游+蟹道乐盛宴+东京1天FREE 6日亲子轻奢游
        public function bzqsy6dinfo()
        {

            $data=$this->get_day_url('bzqsy6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 本州花田巡游+蟹道乐盛宴+东京1天FREE 6日亲子轻奢游';
            $data['share_desc']=' 本州花田巡游+蟹道乐盛宴+东京1天FREE 6日亲子轻奢游';
            $data['index_url']=base_url('lst/bzqsy6dinfo/SH-rbbzqsy-6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bzqsy6d/images/share.jpg";

            $this->load->view('bzqsy6d/index',$data);
            $this->show_count();
        }

        //0921美国东西海岸+夏威夷+2整天黄石+墨西哥+加拿大东西+落基山脉班芙国家公园深度26天夏进温出MU-纯美
        public function mg26dinfo()
        {

            $data=$this->get_day_url('mg26d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='美国东西海岸+夏威夷+2整天黄石+墨西哥+加拿大东西+落基山脉班芙国家公园深度26天夏进温出MU-纯美';
            $data['share_desc']='美国东西海岸+夏威夷+2整天黄石+墨西哥+加拿大东西+落基山脉班芙国家公园深度26天夏进温出MU-纯美';
            $data['index_url']=base_url('lst/mg26dinfo/MY-26D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mg26d/images/head.jpg";

            $this->load->view('mg26d/index',$data);
            $this->show_count();
        }

    //4晚5天 新加坡自由行
        public function xinjiapoziyouxinginfo()
        {

            $data=$this->get_day_url('xinjiapoziyouxing');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='4晚5天 新加坡自由行';
            $data['share_desc']='4晚5天 新加坡自由行';
            $data['index_url']=base_url('lst/xinjiapoziyouxinginfo/KLJQ-xijiapo_ziyou-4n5d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xinjiapoziyouxing/images/head.jpg";

            $this->load->view('xinjiapoziyouxing/index',$data);
            $this->show_count();
        }


        //新加坡5晚6天  --品质半自由行
        public function xinjiapo5n6dinfo()
        {

            $data=$this->get_day_url('xinjiapo5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡5晚6天  --品质半自由行';
            $data['share_desc']='新加坡5晚6天  --品质半自由行';
            $data['index_url']=base_url('lst/xinjiapo5n6dinfo/KLJQ-xijiapo_banziyou-5n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xinjiapo5n6d/images/head.jpg";

            $this->load->view('xinjiapo5n6d/index',$data);
            $this->show_count();
        }

        //7月_8月_北京出发_CA_鼎级和风_本州伊豆半岛双温泉美食7天行程
        public function rbbzydinfo()
        {

            $data=$this->get_day_url('rbbzyd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='鼎级和风_本州伊豆半岛双温泉美食7天行程';
            $data['share_desc']='鼎级和风 本州伊豆半岛双温泉美食7日';
            $data['index_url']=base_url('lst/rbbzydinfo/ZXHB-RBBZYD');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbbzyd/images/head.jpg";

            $this->load->view('rbbzyd/index',$data);
            $this->show_count();
        }


        //7月16日7月20日7月24日7月28日东京箱根动感四园亲子体验6日（JL020对JL869)
        public function rbdgsyinfo()
        {

            $data=$this->get_day_url('rbdgsy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东京箱根动感四园亲子体验6日';
            $data['share_desc']='东京箱根动感四园亲子体验6日';
            $data['index_url']=base_url('lst/rbdgsyinfo/ZXHB-RBDGSY');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbdgsy/images/head.jpg";

            $this->load->view('rbdgsy/index',$data);
            $this->show_count();
        }

        //8月04日_8月18日_8月26日_JL_东京箱根悦享三园亲子5日(JL020对JL869)
        public function rbyxsyinfo()
        {

            $data=$this->get_day_url('rbyxsy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东京箱根悦享三园亲子5日';
            $data['share_desc']='东京箱根悦享三园亲子5日';
            $data['index_url']=base_url('lst/rbyxsyinfo/ZXHB-RBYXSY');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbyxsy/images/head.jpg";

            $this->load->view('rbyxsy/index',$data);
            $this->show_count();
        }

        //北海道青森东京轻奢7日JL(7月23日、8月15日、9月13日)
        public function rbbhdqsinfo()
        {

            $data=$this->get_day_url('rbbhdqs');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='北海道青森东京轻奢7日JL';
            $data['share_desc']='北海道青森东京轻奢7日JL';
            $data['index_url']=base_url('lst/rbbhdqsinfo/ZXHB-RBBHDQS');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbbhdqs/images/head.jpg";

            $this->load->view('rbbhdqs/index',$data);
            $this->show_count();
        }

        //浪漫双城 北海道东京亲子赏花6日游JL(7月21日、26日、8月4日、9日、20日、26日)
        public function rbbhdqzinfo()
        {

            $data=$this->get_day_url('rbbhdqz');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='浪漫双城 北海道东京亲子赏花6日游';
            $data['share_desc']='浪漫双城 北海道东京亲子赏花6日游';
            $data['index_url']=base_url('lst/rbbhdqzinfo/ZXHB-RBBHDQZ');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbbhdqz/images/head.jpg";

            $this->load->view('rbbhdqz/index',$data);
            $this->show_count();
        }

        //优品-蓝梦加勒比巴厘岛5晚7天
        public function ypbldinfo()
        {

            $data=$this->get_day_url('ypbld');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='优品-蓝梦加勒比巴厘岛5晚7天';
            $data['share_desc']='优品-蓝梦加勒比巴厘岛5晚7天';
            $data['index_url']=base_url('lst/ypbldinfo/ZXHB-YP-LMBLD');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ypbld/images/head.jpg";

            $this->load->view('ypbld/index',$data);
            $this->show_count();
        }

        //臻品-万豪巴厘岛5晚7天
        public function zpwhbldinfo()
        {

            $data=$this->get_day_url('zpwhbld');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='臻品-万豪巴厘岛5晚7天';
            $data['share_desc']='臻品-万豪巴厘岛5晚7天';
            $data['index_url']=base_url('lst/zpwhbldinfo/ZXHB-ZP-WHBLD');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zpwhbld/images/head.jpg";

            $this->load->view('zpwhbld/index',$data);
            $this->show_count();
        }

        //尊品-巴厘岛金蓝双享5晚7天
        public function zpjlbldinfo()
        {

            $data=$this->get_day_url('zpjlbld');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='尊品-巴厘岛金蓝双享5晚7天';
            $data['share_desc']='尊品-巴厘岛金蓝双享5晚7天';
            $data['index_url']=base_url('lst/zpjlbldinfo/ZXHB-ZP-JLBLD');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zpjlbld/images/head.jpg";

            $this->load->view('zpjlbld/index',$data);
            $this->show_count();
        }

    //清迈&清莱&金三角 · “喜”从天降 · 品质放心游
        public function xicongtianjiang4n6dinfo()
        {

            $data=$this->get_day_url('xicongtianjiang4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='清迈&清莱&金三角 · “喜”从天降 · 品质放心游';
            $data['share_desc']='清迈&清莱&金三角 · “喜”从天降 · 品质放心游';
            $data['index_url']=base_url('lst/xicongtianjiang4n6dinfo/HT-qingmai-4N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xicongtianjiang4n6d/images/share.jpg";

            $this->load->view('xicongtianjiang4n6d/index',$data);
            $this->show_count();
        }
        //金蓝巴厘岛4N6行程-直飞
        public function jlbld4n6dinfo()
        {

            $data=$this->get_day_url('jlbld4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='金蓝巴厘岛4N6行程-直飞';
            $data['share_desc']='金蓝巴厘岛4N6行程-直飞';
            $data['index_url']=base_url('lst/jlbld4n6dinfo/ZXHB-JLBLD-4N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jlbld4n6d/images/head.jpg";

            $this->load->view('jlbld4n6d/index',$data);
            $this->show_count();
        }


        //金蓝巴厘岛5N7行程-直飞
        public function jlbld5n7dinfo()
        {

            $data=$this->get_day_url('jlbld5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='金蓝巴厘岛5N7行程-直飞';
            $data['share_desc']='金蓝巴厘岛5N7行程-直飞';
            $data['index_url']=base_url('lst/jlbld5n7dinfo/ZXHB-JLBLD-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jlbld5n7d/images/head.jpg";

            $this->load->view('jlbld5n7d/index',$data);
            $this->show_count();
        }

        //臻品万豪·巴厘岛4N6行程-往返直飞
        public function zpwhbld4n6dinfo()
        {

            $data=$this->get_day_url('zpwhbld4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='臻品万豪·巴厘岛4N6行程-往返直飞';
            $data['share_desc']='臻品万豪·巴厘岛4N6行程-往返直飞';
            $data['index_url']=base_url('lst/zpwhbld4n6dinfo/ZXHB-ZPWHBLD-4N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zpwhbld4n6d/images/head.jpg";

            $this->load->view('zpwhbld4n6d/index',$data);
            $this->show_count();
        }

        //沙巴汶莱五晚六天经典纯玩游
        public function wlsb5n6dinfo()
        {

            $data=$this->get_day_url('wlsb5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='沙巴汶莱五晚六天经典纯玩游';
            $data['share_desc']='沙巴汶莱五晚六天经典纯玩游';
            $data['index_url']=base_url('lst/wlsb5n6dinfo/WLSB-5N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/wlsb5n6d/images/head.jpg";

            $this->load->view('wlsb5n6d/index',$data);
            $this->show_count();
        }

        //沙巴汶莱四晚五天品质纯玩游
        public function wlsb4n5dinfo()
        {

            $data=$this->get_day_url('wlsb4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='沙巴汶莱四晚五天品质纯玩游';
            $data['share_desc']='沙巴汶莱四晚五天品质纯玩游';
            $data['index_url']=base_url('lst/wlsb4n5dinfo/WLSB-4N5D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/wlsb4n5d/images/head.jpg";

            $this->load->view('wlsb4n5d/index',$data);
            $this->show_count();
        }

        // TFFML1706-001马来风情五天之旅
        public function tffmlxyinfo()
        {

            $data=$this->get_day_url('tffmlxy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFFML1706-001马来风情五天之旅';
            $data['share_desc']='TFFML1706-001马来风情五天之旅';
            $data['index_url']=base_url('lst/tffmlxyinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffmlxy/images/head.jpg";

            $this->load->view('tffmlxy/index',$data);
            $this->show_count();
        }

        // TFFXJP1706-003狮城探秘四天之旅
        public function tffxjp4dinfo()
        {

            $data=$this->get_day_url('tffxjp4d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFFXJP1706-003狮城探秘四天之旅';
            $data['share_desc']='TFFXJP1706-003狮城探秘四天之旅';
            $data['index_url']=base_url('lst/tffxjp4dinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffxjp4d/images/head.jpg";

            $this->load->view('tffxjp4d/index',$data);
            $this->show_count();
        }

        // TFFXJP1706-001及002新加坡亲子5天之旅
        public function tffxjpinfo()
        {

            $data=$this->get_day_url('tffxjp');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFFXJP1706-001及002新加坡亲子5天之旅';
            $data['share_desc']='TFFXJP1706-001及002新加坡亲子5天之旅';
            $data['index_url']=base_url('lst/tffxjpinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffxjp/images/head.jpg";

            $this->load->view('tffxjp/index',$data);
            $this->show_count();
        }

        // // TFFXJP1706-002直客四星新加坡5天小包团
        // public function tffxjp2info()
        // {

        //     $data=$this->get_day_url('tffxjp2');
        //     $data['signPackage']=$this->wx_js_para(3);
        //     $data['share_title']='TFFXJP1706-002直客四星新加坡5天小包团';
        //     $data['share_desc']='TFFXJP1706-002直客四星新加坡5天小包团';
        //     $data['index_url']=base_url('lst/tffxjp2info/easy-win');
        //     $data['shareimage']=$this->shareimage_forlx;
        //     $data['shareimage']="http://api.etjourney.com/public/tffxjp2/images/head.jpg";

        //     $this->load->view('tffxjp2/index',$data);
        //     $this->show_count();
        // }


        // TFF-ASUJ1706-1激情澳洲大堡礁8天尊享品质之旅（纯玩）-QF半自由行
        public function tffazdbjinfo()
        {

            $data=$this->get_day_url('tffazdbj');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFF-ASUJ1706-1激情澳洲大堡礁8天尊享品质之旅（纯玩）-QF半自由行';
            $data['share_desc']='TFF-ASUJ1706-1激情澳洲大堡礁8天尊享品质之旅（纯玩）-QF半自由行';
            $data['index_url']=base_url('lst/tffazdbjinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffazdbj/images/head.jpg";

            $this->load->view('tffazdbj/index',$data);
            $this->show_count();
        }

        // TFF-AUSD1716-2澳洲大洋路10天全景动感之旅（纯玩）半自由行
        public function tffazdylinfo()
        {

            $data=$this->get_day_url('tffazdyl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFF-AUSD1716-2澳洲大洋路10天全景动感之旅（纯玩）半自由行';
            $data['share_desc']='TFF-AUSD1716-2澳洲大洋路10天全景动感之旅（纯玩）半自由行';
            $data['index_url']=base_url('lst/tffazdylinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffazdyl/images/head.jpg";

            $this->load->view('tffazdyl/index',$data);
            $this->show_count();
        }

        // TFF-NEWZ1716-3新西兰南北岛美景9天纯净之旅半自由行
        public function tffxxlinfo()
        {

            $data=$this->get_day_url('tffxxl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TFF-NEWZ1716-3新西兰南北岛美景9天纯净之旅半自由行';
            $data['share_desc']='TFF-NEWZ1716-3新西兰南北岛美景9天纯净之旅半自由行';
            $data['index_url']=base_url('lst/tffxxlinfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tffxxl/images/head.jpg";

            $this->load->view('tffxxl/index',$data);
            $this->show_count();
        }

        // 文莱美里姆鲁热带雨林7晚8天亲子游（每周一发团）
        public function wlmlml7n8dinfo()
        {

            $data=$this->get_day_url('wlmlml7n8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='文莱美里姆鲁热带雨林7晚8天亲子游（每周一发团）';
            $data['share_desc']='文莱美里姆鲁热带雨林7晚8天亲子游（每周一发团）';
            $data['index_url']=base_url('lst/wlmlml7n8dinfo/KLJQ-WLSB-7N8D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/wlmlml7n8d/images/head.jpg";

            $this->load->view('wlmlml7n8d/index',$data);
            $this->show_count();
        }

        // 槟城自由行
        public function binchengziyouxinginfo()
        {

            $data=$this->get_day_url('binchengziyouxing');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='槟城自由行';
            $data['share_desc']='槟城自由行';
            $data['index_url']=base_url('lst/binchengziyouxinginfo/KLJQ-BC-ziyouxing');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/binchengziyouxing/images/share.jpg";

            $this->load->view('binchengziyouxing/index',$data);
            $this->show_count();
        }


        // 文莱纯玩深度游5N6D行程
        public function wl5n6dinfo()
        {

            $data=$this->get_day_url('wl5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='文莱纯玩深度游5N6D行程';
            $data['share_desc']='文莱纯玩深度游5N6D行程';
            $data['index_url']=base_url('lst/wl5n6dinfo/KLJQ-WL-5N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/wl5n6d/images/share.jpg";

            $this->load->view('wl5n6d/index',$data);
            $this->show_count();
        }

        // 文莱纯玩深度游4N5D行程
        public function wl4n5dinfo()
        {

            $data=$this->get_day_url('wl4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='文莱纯玩深度游4N5D行程';
            $data['share_desc']='文莱纯玩深度游4N5D行程';
            $data['index_url']=base_url('lst/wl4n5dinfo/KLJQ-WL-4N5D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/wl4n5d/images/share.jpg";

            $this->load->view('wl4n5d/index',$data);
            $this->show_count();
        }

        // 伊犁天山环线深度8天
        public function xjyili8dinfo()
        {

            $data=$this->get_day_url('xjyili8d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='伊犁天山环线深度8天';
            $data['share_desc']='伊犁天山环线深度8天';
            $data['index_url']=base_url('lst/xjyili8dinfo/RHHK-xjyili8d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjyili8d/images/head.jpg";

            $this->load->view('xjyili8d/index',$data);
            $this->show_count();
        }

        // 经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）
        public function xjdahuanxian9dinfo()
        {

            $data=$this->get_day_url('xjdahuanxian9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
            $data['share_desc']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
            $data['index_url']=base_url('lst/xjdahuanxian9dinfo/RHHK-xjdahuanxian9d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjdahuanxian9d/images/head1.jpg";

            $this->load->view('xjdahuanxian9d/index',$data);
            $this->show_count();
        }
        // 经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）
        public function xjdahuanxian9d1info()
        {

            $data=$this->get_day_url('xjdahuanxian9d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
            $data['share_desc']='经典喀纳斯、独库公路穿越天山、伊犁大环线9日深度游（奔驰商务车专享）';
            $data['index_url']=base_url('lst/xjdahuanxian9d1info/RHHK-xjdahuanxian9d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjdahuanxian9d/images/head.jpg";

            $this->load->view('xjdahuanxian9d/index1',$data);
            $this->show_count();
        }
        //大阪 奈良 京都 白川乡 飞弹高山 富士山 东京 7日之旅文档

        public function dabaninfo()
        {

            $data=$this->get_day_url('daban');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='大阪 奈良 京都 白川乡 飞弹高山 富士山 东京 7日之旅文档';
            $data['share_desc']='大阪 奈良 京都 白川乡 飞弹高山 富士山 东京 7日之旅文档';
            $data['index_url']=base_url('lst/dabaninfo/easy-win');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/daban/images/head.jpg";

            $this->load->view('daban/index',$data);
            $this->show_count();
        }

        //TY-冲绳 碧海蓝天与琉球美食文化4日游

        public function tyd4info()
        {

            $data=$this->get_day_url('tyd4');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-冲绳 碧海蓝天与琉球美食文化4日游';
            $data['share_desc']='TY-冲绳 碧海蓝天与琉球美食文化4日游';
            $data['index_url']=base_url('lst/tyd4info/TY-CS4');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tyd4/images/head.jpg";

            $this->load->view('tyd4/index',$data);
            $this->show_count();
        }


        //TY-冲绳 碧海蓝天与琉球美食文化5日游

        public function tyd5info()
        {

            $data=$this->get_day_url('tyd5');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-冲绳 碧海蓝天与琉球美食文化5日游';
            $data['share_desc']='TY-冲绳 碧海蓝天与琉球美食文化5日游';
            $data['index_url']=base_url('lst/tyd5info/TY-CS5');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tyd5/images/head.jpg";

            $this->load->view('tyd5/index',$data);
            $this->show_count();
        }


        //私想家-D文化交流篇• 暑期探索夏令营7日

        public function sxjdinfo()
        {

            $data=$this->get_day_url('sxjd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='私想家-D文化交流篇• 暑期探索夏令营7日';
            $data['share_desc']='私想家-D文化交流篇• 暑期探索夏令营7日';
            $data['index_url']=base_url('lst/sxjdinfo/TY-D-SXJ');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sxjd/images/head.jpg";

            $this->load->view('sxjd/index',$data);
            $this->show_count();
        }

        //普吉美食风帆 · 休闲之旅 · 行程升级版
        public function msff5n6dinfo()
        {

            $data=$this->get_day_url('msff5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉美食风帆 · 休闲之旅 · 行程升级版';
            $data['share_desc']='普吉美食风帆 · 休闲之旅 · 行程升级版';
            $data['index_url']=base_url('lst/msff5n6dinfo/HT-MSFF-5N6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/msff5n6d/images/share.jpg";

            $this->load->view('msff5n6d/index',$data);
            $this->show_count();
        }

        //普吉美食风帆 · 休闲之旅 · 行程升级版
        public function msff5n7dinfo()
        {

            $data=$this->get_day_url('msff5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉美食风帆 · 休闲之旅 · 行程升级版';
            $data['share_desc']='普吉美食风帆 · 休闲之旅 · 行程升级版';
            $data['index_url']=base_url('lst/msff5n7dinfo/HT-MSFF-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/msff5n7d/images/share.jpg";

            $this->load->view('msff5n7d/index',$data);
            $this->show_count();
        }

        //【新印象】邂逅九州 出海观海豚 人气牧场 双温泉5日

        public function xyxxhinfo()
        {

            $data=$this->get_day_url('xyxxh');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【新印象】邂逅九州 出海观海豚 人气牧场 双温泉5日';
            $data['share_desc']='【新印象】邂逅九州 出海观海豚 人气牧场 双温泉5日';
            $data['index_url']=base_url('lst/xyxxhinfo/ZX-SH-XH');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xyxxh/images/head.jpg";

            $this->load->view('xyxxh/index',$data);
            $this->show_count();
        }

        //【新印象】松阪牛 湖上富士山 双古都本州6日（东京一天FREE）

        public function xyxsbinfo()
        {

            $data=$this->get_day_url('xyxsb');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【新印象】松阪牛 湖上富士山 双古都本州6日（东京一天FREE）';
            $data['share_desc']='【新印象】松阪牛 湖上富士山 双古都本州6日（东京一天FREE）';
            $data['index_url']=base_url('lst/xyxsbinfo/ZX-SH-SB');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xyxsb/images/head.jpg";

            $this->load->view('xyxsb/index',$data);
            $this->show_count();
        }

        //日本--寻梦

        public function rbxminfo()
        {

            $data=$this->get_day_url('rbxm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='寻梦鹤雅 北海道深度全景赏花7日';
            $data['share_desc']='寻梦鹤雅 北海道深度全景赏花7日';
            $data['index_url']=base_url('lst/rbxminfo/ZXHB-xunmeng');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbxm/images/head.jpg";

            $this->load->view('rbxm/index',$data);
            $this->show_count();
        }

        //日本--动感乐园

        public function rbdglyinfo()
        {

            $data=$this->get_day_url('rbdgly');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='动感乐园 哈利波特迪士尼富士急三丽鸥奢享7日';
            $data['share_desc']='动感乐园 哈利波特迪士尼富士急三丽鸥奢享7日';
            $data['index_url']=base_url('lst/rbdglyinfo/ZXHB-dongganleyuan');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbdgly/images/head.jpg";

            $this->load->view('rbdgly/index',$data);
            $this->show_count();
        }

        //日本--亲子乐园

        public function rbqzlyinfo()
        {

            $data=$this->get_day_url('rbqzly');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='亲子乐园 哈利波特迪士尼富士急三丽鸥舒享7日';
            $data['share_desc']='亲子乐园 哈利波特迪士尼富士急三丽鸥舒享7日';
            $data['index_url']=base_url('lst/rbqzlyinfo/ZXHB-qinzileyuan');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/rbqzly/images/head.jpg";

            $this->load->view('rbqzly/index',$data);
            $this->show_count();
        }

        //尊享  俄罗斯6晚8日

        public function eluosiinfo()
        {

            $data=$this->get_day_url('eluosi');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='尊享 俄罗斯6晚8日';
            $data['share_desc']='尊享 俄罗斯6晚8日';
            $data['index_url']=base_url('lst/eluosiinfo/ZXHB-HUZX');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/eluosi/images/head.jpg";

            $this->load->view('eluosi/index',$data);
            $this->show_count();
        }

        //童趣乐高-新加坡新山亲子半自助游5晚6日

        public function xjptqlginfo()
        {

            $data=$this->get_day_url('xjptqlg');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='童趣乐高-新加坡新山亲子半自助游5晚6日';
            $data['share_desc']='童趣乐高-新加坡新山亲子半自助游5晚6日';
            $data['index_url']=base_url('lst/xjptqlginfo/ZXHB-TQLG');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjptqlg/images/head.jpg";

            $this->load->view('xjptqlg/index',$data);
            $this->show_count();
        }

        //马航六天五晚特价行程 17年6-8月
        public function mahang5n6dinfo()
        {

            $data=$this->get_day_url('mahang5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛5晚6天特价游';
            $data['share_desc']='巴厘岛5晚6天特价游';
            $data['index_url']=base_url('lst/mahang5n6dinfo/KLJQ-mahang5n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/mahang5n6d/images/share.jpg";

            $this->load->view('mahang5n6d/index',$data);
            $this->show_count();
        }

    //新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH
        public function xinjiapocw5n6dMH389info()
        {

            $data=$this->get_day_url('xinjiapocw5n6dMH389');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH';
            $data['share_desc']='新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH';
            $data['index_url']=base_url('lst/xinjiapocw5n6dMH389info/KLJQ-XJP-5N6D-MH389');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xinjiapocw5n6dMH389/images/head.jpg";

            $this->load->view('xinjiapocw5n6dMH389/index',$data);
            $this->show_count();
        }

    //新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH387
        public function xinjiapocw5n6dMH387info()
        {

            $data=$this->get_day_url('xinjiapocw5n6dMH387');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH387';
            $data['share_desc']='新加坡2晚+吉隆坡+云顶+波德申（5晚6天纯玩）MH387';
            $data['index_url']=base_url('lst/xinjiapocw5n6dMH387info/KLJQ-XJP-5N6D-MH387');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xinjiapocw5n6dMH387/images/head.jpg";

            $this->load->view('xinjiapocw5n6dMH387/index',$data);
            $this->show_count();
        }

        //巴厘岛送文莱5N6D行程(周三出发)
        public function bld5n6d3info()
        {

            $data=$this->get_day_url('bld5n6d3');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛送文莱5N6D行程(周三出发)';
            $data['share_desc']='巴厘岛送文莱5N6D行程(周三出发)';
            $data['index_url']=base_url('lst/bld5n6d3info/KLJQ-BLD-5N6D3');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bld5n6d3/images/share.jpg";

            $this->load->view('bld5n6d3/index',$data);
            $this->show_count();
        }

        //巴厘岛送文莱5N6D行程(周五出发)
        public function bld5n6d5info()
        {

            $data=$this->get_day_url('bld5n6d5');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛送文莱5N6D行程(周五出发)';
            $data['share_desc']='巴厘岛送文莱5N6D行程(周五出发)';
            $data['index_url']=base_url('lst/bld5n6d5info/KLJQ-BLD-5N6D5');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bld5n6d5/images/share.jpg";

            $this->load->view('bld5n6d5/index',$data);
            $this->show_count();
        }

        //童趣海洋·亲子新加坡一地5晚6日   7.24

        public function xjp5n6d3info()
        {

            $data=$this->get_day_url('xjp5n6d3');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='童趣海洋·亲子新加坡一地5晚6日';
            $data['share_desc']='童趣海洋·亲子新加坡一地5晚6日--7.24';
            $data['index_url']=base_url('lst/xjp5n6d3info/ZXHB-XJP-5N6D-724');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjp5n6d3/images/share.jpg";

            $this->load->view('xjp5n6d3/index',$data);
            $this->show_count();
        }

        //童趣海洋·亲子新加坡一地5晚6日   7-8月

        public function xjp5n6dinfo()
        {

            $data=$this->get_day_url('xjp5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='童趣海洋·亲子新加坡一地5晚6日';
            $data['share_desc']='童趣海洋·亲子新加坡一地5晚6日--7-8月';
            $data['index_url']=base_url('lst/xjp5n6dinfo/ZXHB-XJP-5N6D-78');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xjp5n6d/images/share.jpg";

            $this->load->view('xjp5n6d/index',$data);
            $this->show_count();
        }

        //巴厘岛5晚7天行程

        public function bld5n7dinfo()
        {

            $data=$this->get_day_url('bld5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='巴厘岛5晚7天行程';
            $data['share_desc']='5晚7天 东航直飞巴厘岛';
            $data['index_url']=base_url('lst/bld5n7dinfo/KLJQ-BLD-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bld5n7d/images/share.jpg";

            $this->load->view('bld5n7d/index',$data);
            $this->show_count();
        }

    //东航直飞五晚七天经典行程

        public function dhzf5n7d3info()
        {

            $data=$this->get_day_url('dhzf5n7d3');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='东航直飞五晚七天经典行程';
            $data['share_desc']='东航直飞五晚七天经典行程';
            $data['index_url']=base_url('lst/dhzf5n7d3info/KLJQ-DHZF-5N7D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/dhzf5n7d3/images/share.jpg";

            $this->load->view('dhzf5n7d3/index',$data);
            $this->show_count();
        }

    //奢雅风华-巴厘岛魔力爱五晚六天行程 (每周三出发)

        public function bldwl3info()
        {

            $data=$this->get_day_url('bldwl3');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='奢雅风华-巴厘岛魔力爱五晚六天行程 (每周三出发)';
            $data['share_desc']='奢雅风华-巴厘岛魔力爱五晚六天行程 (每周三出发)';
            $data['index_url']=base_url('lst/bldwl3info/KLJQ-bldwl3');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldwl3/images/share.jpg";

            $this->load->view('bldwl3/index',$data);
            $this->show_count();
        }


    //菲律宾3晚5天

        public function changtan3n5dinfo()
        {

            $data=$this->get_day_url('changtan3n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='菲律宾--纯长滩三晚五日游';
            $data['share_desc']='菲律宾--纯长滩三晚五日游';
            $data['index_url']=base_url('lst/changtan3n5dinfo/KLJQ-changtan3n5d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/changtan3n5d/images/head.jpg";

            $this->load->view('changtan3n5d/index',$data);
            $this->show_count();
        }

    //菲律宾4晚6天

        public function changtan4n6dinfo()
        {

            $data=$this->get_day_url('changtan4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='菲律宾--纯长滩四晚六日游';
            $data['share_desc']='菲律宾--纯长滩四晚六日游';
            $data['index_url']=base_url('lst/changtan4n6dinfo/KLJQ-changtan4n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/changtan4n6d/images/head.jpg";

            $this->load->view('changtan4n6d/index',$data);
            $this->show_count();
        }

    //菲律宾4晚6天

        public function changtan5n7dinfo()
        {

            $data=$this->get_day_url('changtan5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='菲律宾--纯长滩五晚七日游';
            $data['share_desc']='菲律宾--纯长滩五晚七日游';
            $data['index_url']=base_url('lst/changtan5n7dinfo/KLJQ-changtan5n7d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/changtan5n7d/images/head.jpg";

            $this->load->view('changtan5n7d/index',$data);
            $this->show_count();
        }

        //奢雅风华-巴厘岛魔力爱五晚六天行程 (每周五出发)

        public function bldwl5info()
        {

            $data=$this->get_day_url('bldwl5');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='奢雅风华-巴厘岛魔力爱五晚六天行程 (每周五出发)';
            $data['share_desc']='奢雅风华-巴厘岛魔力爱五晚六天行程 (每周五出发)';
            $data['index_url']=base_url('lst/bldwl5info/KLJQ-bldwl5');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bldwl5/images/share.jpg";

            $this->load->view('bldwl5/index',$data);
            $this->show_count();
        }


        //普吉·海豚之恋·暑期亲子游·行程升级版-5N7D
        public function htzl5n7dinfo()
        {

            $data=$this->get_day_url('htzl5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉·海豚之恋·暑期亲子游·行程升级版-5N7D';
            $data['share_desc']='普吉·海豚之恋·暑期亲子游·行程升级版-5N7D';
            $data['index_url']=base_url('lst/htzl5n7dinfo/HT-PJ-HTZL');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/htzl5n7d/images/head.jpg";

            $this->load->view('htzl5n7d/index',$data);
            $this->show_count();
        }

        //上航乐享
        public function lxinfo()
        {

            $data=$this->get_day_url('lx');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='乐享普吉岛5晚7天游';
            $data['share_desc']='乐享普吉岛5晚7天游';
            $data['index_url']=base_url('lst/lxinfo');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/lx/img/share.png";

            $this->load->view('lx/index',$data);
            $this->show_count();
        }

        //沙巴 曼谷5晚6天（3 2 ） - 广告
        public function baman5n6dinfo()
        {

            $data=$this->get_day_url('baman5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='沙巴+曼谷5晚6天 （跟团游）';
            $data['share_desc']='沙巴+曼谷5晚6天 （跟团游）';
            $data['index_url']=base_url('lst/baman5n6dinfo/guolv-baman5n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/baman5n6d/images/head.jpg";

            $this->load->view('baman5n6d/index',$data);
            $this->show_count();
        }

        //沙巴 曼谷5晚6天（精致小团2-6人 ）--广告
        public function baman5n6dxiaoinfo()
        {

            $data=$this->get_day_url('baman5n6dxiao');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='沙巴+曼谷5晚6天 （2-6人精致小团）';
            $data['share_desc']='沙巴+曼谷5晚6天 （2-6人精致小团）';
            $data['index_url']=base_url('lst/baman5n6dxiaoinfo/guolv-baman5n6dxiao');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/baman5n6dxiao/images/head.jpg";

            $this->load->view('baman5n6dxiao/index',$data);
            $this->show_count();
        }

        //沙巴4晚6天（2-6人精致小团 每周五出发）
        public function baman4n6dxiaoinfo()
        {

            $data=$this->get_day_url('baman4n6dxiao');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='沙巴4晚6天（2-6人精致小团 每周五出发）';
            $data['share_desc']='沙巴4晚6天（2-6人精致小团 每周五出发）';
            $data['index_url']=base_url('lst/baman4n6dxiaoinfo/guolv-baman4n6dxiao');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/baman4n6dxiao/images/head.jpg";

            $this->load->view('baman4n6dxiao/index',$data);
            $this->show_count();
        }

        // 沙巴4晚6天半自由行 （每周五出发 赠送接送机）
        public function baman4n6dinfo()
        {

            $data=$this->get_day_url('baman4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 沙巴4晚6天半自由行 （每周五出发 赠送接送机） ';
            $data['share_desc']=' 沙巴4晚6天半自由行 （每周五出发 赠送接送机） ';
            $data['index_url']=base_url('lst/baman4n6dinfo/guolv-baman4n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/baman4n6d/images/head.jpg";

            $this->load->view('baman4n6d/index',$data);
            $this->show_count();
        }

        //普吉·尊尚纯玩·行程美化版 4N5D
        public function zscw4n5dinfo()
        {

            $data=$this->get_day_url('zscw4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['share_desc']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['index_url']=base_url('lst/zscw4n5dinfo/HT-zscw4n5d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zscw4n5d/images/share.jpg";

            $this->load->view('zscw4n5d/index',$data);
            $this->show_count();
        }

        //普吉·尊尚纯玩·行程美化版 4N6D
        public function zscw4n6dinfo()
        {

            $data=$this->get_day_url('zscw4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['share_desc']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['index_url']=base_url('lst/zscw4n6dinfo/HT-zscw4n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zscw4n6d/images/share.jpg";

            $this->load->view('zscw4n6d/index',$data);
            $this->show_count();
        }

        //普吉·尊尚纯玩·行程美化版 5N6D
        public function zscw5n6dinfo()
        {

            $data=$this->get_day_url('zscw5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['share_desc']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['index_url']=base_url('lst/zscw5n6dinfo/HT-zscw5n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zscw5n6d/images/share.jpg";

            $this->load->view('zscw5n6d/index',$data);
            $this->show_count();
        }

        //普吉·尊尚纯玩·行程美化版 5N7D
        public function zscw5n7dinfo()
        {

            $data=$this->get_day_url('zscw5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['share_desc']='高端普吉 · 尊尚纯玩 · 行程美化版';
            $data['index_url']=base_url('lst/zscw5n7dinfo/HT-zscw5n7d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/zscw5n7d/images/share.jpg";

            $this->load->view('zscw5n7d/index',$data);
            $this->show_count();
        }

        //三生三世 · 游普吉 · 4N5D-改-3.2
        public function ssss4n5dinfo()
        {

            $data=$this->get_day_url('ssss4n5d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='三生三世 · 游普吉 · 行程升级版';
            $data['share_desc']='三生三世 · 游普吉 · 行程升级版';
            $data['index_url']=base_url('lst/ssss4n5dinfo/HT-ssss4n5d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ssss4n5d/images/head.jpg";

            $this->load->view('ssss4n5d/index',$data);
            $this->show_count();
        }

        //三生三世 · 游普吉 · 4N6D-改-3.2
        public function ssss4n6dinfo()
        {

            $data=$this->get_day_url('ssss4n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='三生三世 · 游普吉 · 行程升级版';
            $data['share_desc']='三生三世 · 游普吉 · 行程升级版';
            $data['index_url']=base_url('lst/ssss4n6dinfo/HT-ssss4n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ssss4n6d/images/head.jpg";

            $this->load->view('ssss4n6d/index',$data);
            $this->show_count();
        }

        //三生三世 · 游普吉 · 5N6D-改-3.2
        public function ssss5n6dinfo()
        {

            $data=$this->get_day_url('ssss5n6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='三生三世 · 游普吉 · 行程升级版';
            $data['share_desc']='三生三世 · 游普吉 · 行程升级版';
            $data['index_url']=base_url('lst/ssss5n6dinfo/HT-ssss5n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ssss5n6d/images/head.jpg";

            $this->load->view('ssss5n6d/index',$data);
            $this->show_count();
        }

        //三生三世 · 游普吉 · 5N7D-改-3.2
        public function ssss5n7dinfo()
        {

            $data=$this->get_day_url('ssss5n7d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='三生三世 · 游普吉 · 行程升级版';
            $data['share_desc']='三生三世 · 游普吉 · 行程升级版';
            $data['index_url']=base_url('lst/ssss5n7dinfo/HT-ssss5n7d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/ssss5n7d/images/head.jpg";

            $this->load->view('ssss5n7d/index',$data);
            $this->show_count();
        }

    //探访蓝色土耳其浪漫之旅10日
        public function tuerqiinfo()
        {

            $data=$this->get_day_url('tuerqi');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='探访蓝色土耳其浪漫之旅10日';
            $data['share_desc']='探访蓝色土耳其浪漫之旅10日';
            $data['index_url']=base_url('lst/tuerqiinfo/ZXHB-ZDF-TUERQI');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tuerqi/images/head.jpg";

            $this->load->view('tuerqi/index',$data);
            $this->show_count();
        }

        //清迈精品休闲游
        public function qmjpxxinfo()
        {

            $data=$this->get_day_url('qmjpxx');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='清迈5天4晚纯玩精品悠闲游';
            $data['share_desc']='清迈5天4晚纯玩精品悠闲游';
            $data['index_url']=base_url('lst/qmjpxxinfo/KLJQ-jingpinxiuxian');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qmjpxx/images/head.jpg";

            $this->load->view('qmjpxx/index',$data);
            $this->show_count();
        }


        //清迈乐享双城游
        public function qmlxscinfo()
        {

            $data=$this->get_day_url('qmlxsc');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='清迈清莱5天4晚乐享双城游';
            $data['share_desc']='清迈清莱5天4晚乐享双城游';
            $data['index_url']=base_url('lst/qmlxscinfo/HLJQ');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qmlxsc/images/head.jpg";

            $this->load->view('qmlxsc/index',$data);
            $this->show_count();
        }

        //越南岘港3晚4天新享游
        public function xiangangxxyinfo()
        {

            $data=$this->get_day_url('xiangangxxy');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港3晚4天新享游';
            $data['share_desc']='越南岘港3晚4天新享游';
            $data['index_url']=base_url('lst/xiangangxxyinfo/KLJQ-xg3w4dxxy');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xiangangxxy/images/head.jpg";

            $this->load->view('xiangangxxy/index',$data);
            $this->show_count();
        }

        //越南岘港4晚5天休闲游
        public function xiuxianyou4w5tinfo()
        {

            $data=$this->get_day_url('xiuxianyou4w5t');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港4晚5天休闲游';
            $data['share_desc']='越南岘港4晚5天休闲游';
            $data['index_url']=base_url('lst/xiuxianyou4w5tinfo/KLJQ-xiuxianyou4w5t');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xiuxianyou4w5t/images/head.jpg";

            $this->load->view('xiuxianyou4w5t/index',$data);
            $this->show_count();
        }

        //越南岘港4晚5天悦享游
        public function yuexiangyou4w5tinfo()
        {

            $data=$this->get_day_url('yuexiangyou4w5t');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港4晚5天悦享游';
            $data['share_desc']='越南岘港4晚5天悦享游';
            $data['index_url']=base_url('lst/yuexiangyou4w5tinfo/KLJQ-yuexiangyou4w5t');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/yuexiangyou4w5t/images/share.jpg";

            $this->load->view('yuexiangyou4w5t/index',$data);
            $this->show_count();
        }

        //越南岘港4晚5天新享游
        public function xinxiangyou4w5tinfo()
        {

            $data=$this->get_day_url('xinxiangyou4w5t');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港4晚5天新享游';
            $data['share_desc']='越南岘港4晚5天新享游';
            $data['index_url']=base_url('lst/xinxiangyou4w5tinfo/KLJQ-xinxiangyou4w5t');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xinxiangyou4w5t/images/share.jpg";

            $this->load->view('xinxiangyou4w5t/index',$data);
            $this->show_count();
        }

                                //越南岘港3晚4天悦享游
        public function xianganginfo()
        {

            $data=$this->get_day_url('xiangang');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港3晚4天悦享游';
            $data['share_desc']='越南岘港3晚4天悦享游';
            $data['index_url']=base_url('lst/xianganginfo/KLJQ-xg3w4dyxy');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xiangang/images/head.jpg";

            $this->load->view('xiangang/index',$data);
            $this->show_count();
        }


                            //越南岘港3晚4天休闲游
        public function xianxianginfo()
        {

            $data=$this->get_day_url('xianxiang');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='越南岘港3晚4天休闲游 ';
            $data['share_desc']='越南岘港3晚4天休闲游 ';
            $data['index_url']=base_url('lst/xianxianginfo/KLJQ-xx3w4dxxy');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xianxiang/images/head.jpg";

            $this->load->view('xianxiang/index',$data);
            $this->show_count();
        }


                        //清迈清城之恋四晚六天之旅
        public function qmqczlinfo()
        {

            $data=$this->get_day_url('qmqczl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='清迈-清城の恋-品质放心游  ';
            $data['share_desc']='清迈-清城の恋-品质放心游 ';
            $data['index_url']=base_url('lst/qmqczlinfo/HT-qmqc4w6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qmqczl/images/head.jpg";

            $this->load->view('qmqczl/index',$data);
            $this->show_count();
        }

        //TY-E 思想家  休闲避暑
        public function sxjeinfo()
        {

            $data=$this->get_day_url('sxje');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【富士山登顶】暑期亲子游特别活动';
            $data['share_desc']='和孩子一起同心协力，征服日本第一高峰——富士山看日出，赏星空，留下一生的回忆 ！';
            $data['index_url']=base_url('lst/sxjeinfo/TY-E-sixiangjia');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/tye/images/head.jpg";

            $this->load->view('sxje/index',$data);
            $this->show_count();
        }

                        //清迈清莱-双城の恋-调整版升级版-4.4
        public function qmqlsczlinfo()
        {

            $data=$this->get_day_url('qmqlsczl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='清迈&清莱 · 双城の恋 · 品质放心游升级版 ';
            $data['share_desc']='清迈&清莱 · 双城の恋 · 品质放心游升级版';
            $data['index_url']=base_url('lst/qmqlsczlinfo/HT-shuangcheng4n6d');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/qmqlsczl/images/head.jpg";

            $this->load->view('qmqlsczl/index',$data);
            $this->show_count();
        }

                    //0919澳洲海豚岛+企鹅岛+塔斯马尼亚+新西兰南北岛17天CA
        public function aozhou0919info()
        {

            $data=$this->get_day_url('aozhou0919');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='澳大利亚海豚岛+企鹅岛+塔斯马尼亚+新西兰17天 ';
            $data['share_desc']='澳大利亚海豚岛+企鹅岛+塔斯马尼亚+新西兰17天 ';
            $data['index_url']=base_url('lst/aozhou0919info/tuyi-aozhou0919');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/aozhou0919/images/head.jpg";

            $this->load->view('aozhou0919/index',$data);
            $this->show_count();
        }


                     //0807澳洲海豚岛+企鹅岛+塔斯马尼亚+新西兰南北岛17天CA
        public function aozhou0807info()
        {

            $data=$this->get_day_url('aozhou0807');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='0807澳洲海豚岛+企鹅岛+塔斯马尼亚+新西兰南北岛17天CA ';
            $data['share_desc']='0807澳洲海豚岛+企鹅岛+塔斯马尼亚+新西兰南北岛17天CA ';
            $data['index_url']=base_url('lst/aozhou0807info/tuyi-aozhou0807');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/aozhou0807/images/head.jpg";

            $this->load->view('aozhou0807/index',$data);
            $this->show_count();
        }

                //斯里兰卡8天6晚遗产之旅自组7-8月行程(1)
        public function sllkyczlinfo()
        {

            $data=$this->get_day_url('sllkyczl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='温情斯里兰卡---8日遗产之旅 ';
            $data['share_desc']='温情斯里兰卡---8日遗产之旅 ';
            $data['index_url']=base_url('lst/sllkyczlinfo/gangzhonglv-sllkyichanzhilv');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sllkyczl/images/head.jpg";

            $this->load->view('sllkyczl/index',$data);
            $this->show_count();
        }
        ////斯里兰卡9天6晚世界尽头之旅自组7-8月行程
        public function sllkjtzlinfo()
        {

            $data=$this->get_day_url('sllkjtzl');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='温情斯里兰卡---9日世界尽头之旅';
            $data['share_desc']='温情斯里兰卡---9日世界尽头之旅';
            $data['index_url']=base_url('lst/sllkjtzlinfo/gangzhonglv-sllkshijiejintouzhilv');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sllkjtzl/images/head.jpg";

            $this->load->view('sllkjtzl/index',$data);
            $this->show_count();
        }
        //私想家-B北海道周末度假篇•高尔夫4日游
        public function sxjbinfo()
        {

            $data=$this->get_day_url('sxjb');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='思想家-B 北海道周末度假篇·高尔夫4日游';
            $data['share_desc']='富良野·登别·洞爷湖 ·札幌';
            $data['index_url']=base_url('lst/sxjbinfo/TY-B-sixiangjia');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/sxjb/images/head.jpg";

            $this->load->view('sxjb/index',$data);
            $this->show_count();
        }

            //纯玩：0829 澳凯墨10天MU上海（C线：直升机翱翔）
        public function cwc0829info()
        {

            $data=$this->get_day_url('cwc0829');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='CW澳大利亚凯恩斯墨尔本全景10天 ';
            $data['share_desc']='CW澳大利亚凯恩斯墨尔本全景10天 ';
            $data['index_url']=base_url('lst/cwc0829info/tuyi-zhishengjiaoxiangC');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/cwc0829/images/head.jpg";

            $this->load->view('cwc0829/index',$data);
            $this->show_count();
        }

        //TY-E 本州·富士山登顶亲子6日游
        public function bzfsstinfo()
        {

            $data=$this->get_day_url('bzfsst');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【富士山登顶】暑期亲子游特别活动';
            $data['share_desc']='和孩子一起同心协力，征服日本第一高峰——富士山看日出，赏星空，留下一生的回忆 ！';
            $data['index_url']=base_url('lst/bzfsstinfo/TY-E-fushishandengding');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bzfsst/images/head.jpg";

            $this->load->view('bzfsst/index',$data);
            $this->show_count();
        }

            //纯玩：0811澳凯墨10天MU上海（B线：嗨翻嘉年华+鲸声有约）
        public function chunwaninfo()
        {

            $data=$this->get_day_url('chunwan');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='纯玩：0811澳凯墨10天MU上海（B线：嗨翻嘉年华+鲸声有约）';
            $data['share_desc']='CW嗨翻嘉年华+鲸声有约';
            $data['index_url']=base_url('lst/chunwaninfo/tuyi-haifanjianianhuaB');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/chunwan/images/head.jpg";

            $this->load->view('chunwan/index',$data);
            $this->show_count();
        }

                //进店：0811 澳凯墨10天MU上海
        public function jindian2info()
        {

            $data=$this->get_day_url('jindian2');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='JD澳大利亚凯恩斯墨尔本全景10天';
            $data['share_desc']='JD澳大利10天（ 布里斯班、黄金海岸、凯恩斯、墨尔本、悉尼）';
            $data['index_url']=base_url('lst/jindian2info/tuyi-jindian0811');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jindian2/images/head.jpg";

            $this->load->view('jindian2/index',$data);
            $this->show_count();
        }

                //进店：0801 澳凯墨10天MU上海
        public function jindian1info()
        {

            $data=$this->get_day_url('jindian1');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='JD澳大利亚凯恩斯墨尔本全景10天';
            $data['share_desc']='JD澳大利10天（ 布里斯班、黄金海岸、凯恩斯、墨尔本、悉尼）';
            $data['index_url']=base_url('lst/jindian1info/tuyi-jindian0801');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/jindian1/images/head.jpg";

            $this->load->view('jindian1/index',$data);
            $this->show_count();
        }

            //北海道新魅力
        public function bhdxmlinfo()
        {

            $data=$this->get_day_url('bhdxml');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='北海道轻奢6日双温泉';
            $data['share_desc']='浪漫地标小樽+函馆百万夜景+伊达忍者秀';
            $data['index_url']=base_url('lst/bhdxmlinfo/zhongxin-beihaidaoxinmeili');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bhdxml/images/head.jpg";

            $this->load->view('bhdxml/index',$data);
            $this->show_count();
        }


        //日本北海道
        public function bhdinfo()
        {

            $data=$this->get_day_url('bhd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='北海道吉日出行超值6日双温泉';
            $data['share_desc']='吉祥航空，上海札幌直飞';
            $data['index_url']=base_url('lst/bhdinfo/zhongxin-beihaidao');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/bhd/images/share.jpg";

            $this->load->view('bhd/index',$data);
            $this->show_count();
        }
        //新加坡、马来西亚
        public function xminfo()
        {

            $data=$this->get_day_url('xm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡+马六甲+云顶+吉隆坡4晚6天';
            $data['share_desc']='新马缤纷4晚6日游';
            $data['index_url']=base_url('lst/xminfo/guolv-xinma');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xm/images/share.jpg";

            $this->load->view('xm/index',$data);
            $this->show_count();
        }

        //新加坡+马来西亚6日游
        public function xm6dinfo()
        {

            $data=$this->get_day_url('xm6d');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新加坡+马来西亚6日游';
            $data['share_desc']='新加坡+马来西亚6日游';
            $data['index_url']=base_url('lst/xm6dinfo/GL-XM-6D');
            $data['shareimage']=$this->shareimage_forlx;
            $data['shareimage']="http://api.etjourney.com/public/xm6d/images/share.jpg";

            $this->load->view('xm6d/index',$data);
            $this->show_count();
        }


        //纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）
        public function akmAinfo()
        {

            $data=$this->get_day_url('akmA');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）';
            $data['share_desc']='纯玩：0801 澳凯墨10天MU上海（A线：消防演习体验）';
            $data['index_url']=base_url('lst/akmAinfo/TY-A-xiaofangyanxitiyan');
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
            $data['index_url']=base_url('lst/akmBinfo/TY-B-haifanjianianhuajingshengyouyue');
            $data['shareimage']="http://api.etjourney.com/public/akmB/images/head.jpg";
            $this->load->view('akmB/index',$data);
            $this->show_count();
        }
        //澳新11天CX杭州(AS
        public function axhinfo()
        {

            $data=$this->get_day_url('axh');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='澳大利亚新西兰11天';
            $data['share_desc']='澳大利亚新西兰11天';
            $data['index_url']=base_url('lst/axhinfo/TY-aodaliyaxinxilan11tian');
            $data['shareimage']="http://api.etjourney.com/public/axh/images/head.jpg";
            $this->load->view('axh/index',$data);
            $this->show_count();
        }
        //TY-A本州·亲子宝贝成长夏令营六日游（坂东）
        public function bandonginfo()
        {

            $data=$this->get_day_url('bandong');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-A本州·亲子宝贝成长夏令营六日游(大阪进东京出)';
            $data['share_desc']='小小漫画家·富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/bandonginfo/TY-A-BD');
            $data['shareimage']="http://api.etjourney.com/public/bandong/images/head.jpg";
            $this->load->view('bandong/index',$data);
            $this->show_count();
        }
        public function bandonginfo1()
        {

            $data=$this->get_day_url('bandong');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-A本州·亲子宝贝成长夏令营六日游(大阪进东京出)';
            $data['share_desc']='小小漫画家·富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/bandonginfo/TY-A-BD');
            $data['shareimage']="http://api.etjourney.com/public/bandong/images/head.jpg";
            $this->load->view('bandong/index1',$data);
            $this->show_count();
        }
        //TY-A本州·亲子宝贝成长夏令营六日游（东坂）
        public function dongbaninfo()
        {

            $data=$this->get_day_url('dongban');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-A本州·亲子宝贝成长夏令营六日游（东京进大阪出）';
            $data['share_desc']='小小漫画家·富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/dongbaninfo/TY-A-DB');
            $data['shareimage']="http://api.etjourney.com/public/dongban/images/head.jpg";
            $this->load->view('dongban/index',$data);
            $this->show_count();
        }
        //普吉皇帝珊瑚岛泳池别墅半自助5晚7天游
        public function phsinfo()
        {

            $data=$this->get_day_url('phs');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉皇帝珊瑚岛泳池别墅半自助5晚7天游';
            $data['share_desc']='无自费无购物2人成行';
            $data['index_url']=base_url('lst/phsinfo/shanghang-pujihuangdishanhu');
            $data['shareimage']="http://api.etjourney.com/public/phs/images/head.jpg";
            $this->load->view('phs/index',$data);
            $this->show_count();
        }
        //普吉经典两次出海半自助5晚7天游
        public function pjjdinfo()
        {

            $data=$this->get_day_url('pjjd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉经典两次出海半自助5晚7天游';
            $data['share_desc']='无自费无购物2人成行';
            $data['index_url']=base_url('lst/pjjdinfo/shanghang-pujijingdian');
            $data['shareimage']="http://api.etjourney.com/public/pjjd/images/share.jpg";
            $this->load->view('pjjd/index',$data);
            $this->show_count();
        }
        //TY-B-亲子宝贝成长夏令营
        public function tybbandonginfo()
        {

            $data=$this->get_day_url('tybbandong');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-B本州·亲子宝贝成长夏令营六日游（大阪进东京出）';
            $data['share_desc']='富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/tybbandonginfo/TY-B-BD');
            $data['shareimage']="http://api.etjourney.com/public/tyb-bandong/images/share.jpg";
            $this->load->view('tybbandong/index',$data);
            $this->show_count();
        }
        //TY-B-亲子宝贝成长夏令营
        public function tybdongbaninfo()
        {

            $data=$this->get_day_url('tybdongban');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-B本州·亲子宝贝成长夏令营六日游（东京进大阪出）';
            $data['share_desc']='富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/tybdongbaninfo/TY-B-DB');
            $data['shareimage']="http://api.etjourney.com/public/tyb-dongban/images/head.jpg";
            $this->load->view('tybdongban/index',$data);
            $this->show_count();
        }
        //TY-C-亲子宝贝成长夏令营
        public function tycbandonginfo()
        {

            $data=$this->get_day_url('tycbandong');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-C本州·亲子宝贝成长夏令营六日游（大阪进东京出）';
            $data['share_desc']='富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/tycbandonginfo/TY-C-BD');
            $data['shareimage']="http://api.etjourney.com/public/tyc-bandong/images/share.jpg";
            $this->load->view('tycbandong/index',$data);
            $this->show_count();
        }
        //TY-C-亲子宝贝成长夏令营
        public function tycdongbaninfo()
        {

            $data=$this->get_day_url('tycdongban');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='TY-C本州·亲子宝贝成长夏令营六日游（东京进大阪出）';
            $data['share_desc']='富士山下户外探险、探秘自然科学、感受异国人文之旅';
            $data['index_url']=base_url('lst/tycdongbaninfo/TY-C-DB');
            $data['shareimage']="http://api.etjourney.com/public/tyc-dongban/images/head.jpg";
            $this->load->view('tycdongban/index',$data);
            $this->show_count();
        }
        //纯玩：0908澳凯墨10天MU上海
        public function cwcinfo()
        {

            $data=$this->get_day_url('cwc');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='CW-澳大利亚凯恩斯墨尔本全景10天（C线：直升机翱翔）';
            $data['share_desc']='澳大利亚凯恩斯墨尔本全景10天';
            $data['index_url']=base_url('lst/cwcinfo/TY-C-aokaimo');
            $data['shareimage']="http://api.etjourney.com/public/cwc/images/head.jpg";
            $this->load->view('cwc/index',$data);
            $this->show_count();
        }
        //购物：110新西兰南北岛8天MU上海
        public function xxlnbdinfo()
        {

            $data=$this->get_day_url('xxlnbd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='新西兰南北岛8天';
            $data['share_desc']='奥克兰、罗托鲁瓦、皇后镇、基督城、库克山国家公园';
            $data['index_url']=base_url('lst/xxlnbdinfo/TY-xinxilannanbeidao');
            $data['shareimage']="http://api.etjourney.com/public/xxlnbd/images/head.jpg";
            $this->load->view('xxlnbd/index',$data);
            $this->show_count();
        }
        //私想家-A 热海东京度假篇•海钓休闲慢旅行
        public function sxjainfo()
        {

            $data=$this->get_day_url('sxja');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='思想家-A 热海东京度假篇·海钓休闲慢旅行';
            $data['share_desc']='天气似乎热起来了，有没有想去海边玩的冲动。太阳镜，帐篷，躺椅，各种发泡类美酒，和冰块准备起来！';
            $data['index_url']=base_url('lst/sxjainfo/TY-A-sixiangjia');
            $data['shareimage']="http://api.etjourney.com/public/sxja/images/head.jpg";
            $this->load->view('sxja/index',$data);
            $this->show_count();
        }
        //泰新马8晚10天
        public function txminfo()
        {

            $data=$this->get_day_url('txm');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='嗨翻泰新马，泰航直飞8晚10天';
            $data['share_desc']='升级五星酒店、赠送签证和保险 ';
            $data['index_url']=base_url('lst/txminfo/zhongxin-taixinma');
            $data['shareimage']="http://api.etjourney.com/public/txm/images/head.jpg";
            $this->load->view('txm/index',$data);
            $this->show_count();
        }
        //曼谷+芭提雅+大城王府5晚7天
        public function mbcinfo()
        {

            $data=$this->get_day_url('mbc');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='品泰  曼谷 芭提雅 大城7日品质游  ';
            $data['share_desc']='全程泰国航空，国五标准，探索泰国古文明发源地 ';
            $data['index_url']=base_url('lst/mbcinfo/zhongxin-dacheng');
            $data['shareimage']="http://api.etjourney.com/public/mbc/images/head.jpg";
            $this->load->view('mbc/index',$data);
            $this->show_count();
        }
        //曼巴普
        public function manbapuinfo()
        {

            $data=$this->get_day_url('manbapu');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='曼巴普舒心8日游';
            $data['share_desc']='豪华客机往返，五“心”体验';
            $data['index_url']=base_url('lst/manbapuinfo');
            $data['shareimage']="http://api.etjourney.com/public/manbapu/images/head.jpg";
            $this->load->view('manbapu/index',$data);
            $this->show_count();
        }
        //北海道-旭川
        public function xuchuaninfo()
        {

            $data=$this->get_day_url('xuchuan');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='【新魅力】春之北海道轻奢6日游';
            $data['share_desc']='一价全包，五“心”体验';
            $data['index_url']=base_url('lst/xuchuaninfo');
            $data['shareimage']="http://api.etjourney.com/public/xuchuan/images/share.jpg";
            $this->load->view('xuchuan/index',$data);
            $this->show_count();
        }
        //富士南线
        public function fsnxinfo()
        {

            $data=$this->get_day_url('fsnx');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']=' 【新魅力】富士南线之私人温泉+艺妓螃蟹宴+浪漫薰衣草本州6日游';
            $data['share_desc']='免费wifi、赠送20万旅游意外险、迪斯尼门票';
            $data['index_url']=base_url('lst/fsnxinfo');
            $data['shareimage']="http://api.etjourney.com/public/fsnx/images/share.jpg";

            $this->load->view('fsnx/index',$data);
            $this->show_count();
        }


        //曼巴沙
        public function mbsinfo()
        {

            $data=$this->get_day_url('mbs');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='经典曼芭莎享乐之旅';
            $data['share_desc']='包山包海、包岛包船让您一次体验，消费真实度假旅游让您感受物有所值。';
            $data['index_url']=base_url('lst/mbsinfo');
            $data['shareimage']="http://src.etjourney.com/public/mbs/images/share.jpg";

            $this->load->view('mbs/index',$data);
            $this->show_count();
        }

       //蜜月
        public function myinfo()
        {

            $data=$this->get_day_url('my');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='普吉岛 蜜月半自助游';
            $data['share_desc']='异国风光，蜜月回忆，惊爆无限';
            $data['index_url']=base_url('lst/myinfo');
             $data['shareimage']="http://api.etjourney.com/public/my/images/share.jpg";

            $this->load->view('my/index',$data);
            $this->show_count();
        }


        //藏地
        public function zdinfo()
        {

            $data=$this->get_day_url('zd');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='藏地360度全景游';
            $data['share_desc']='0购物0自费，赠送特别体验和超级大礼包';
            $data['index_url']=base_url('lst/zdinfo');
            $data['shareimage']="http://api.etjourney.com/public/zd/images/share.jpg";

            $this->load->view('zd/index',$data);
            $this->show_count();
        }


        //臻爱普吉
        public function zapjinfo()
        {

            $data=$this->get_day_url('zapj');
            $data['signPackage']=$this->wx_js_para(3);
            $data['share_title']='臻爱普吉上海直飞6天4晚';
            $data['share_desc']='精彩项目全包，全程绝无自费品质保证';
            $data['index_url']=base_url('lst/zapjinfo');
            $data['shareimage']="http://api.etjourney.com/public/zapj/img/zapj_share.jpg";

            $this->load->view('zapj/index',$data);
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

            $data['login']=base_url("lst/login/$type");
            $data['register']=base_url("lst/register/$type");
            $data['ask']=base_url("lst/ask/$type");
            $data['buy']=base_url("lst/buy/$type");
            $data['my']=base_url("lst/my/$type");
            $data['success']=base_url("lst/success/$type");


            $data['day1']=base_url("lst/day_info/1/$type");
            $data['day2']=base_url("lst/day_info/2/$type");
            $data['day3']=base_url("lst/day_info/3/$type");
            $data['day4']=base_url("lst/day_info/4/$type");
            $data['day5']=base_url("lst/day_info/5/$type");
            $data['day6']=base_url("lst/day_info/6/$type");
            $data['day7']=base_url("lst/day_info/7/$type");
            $data['day8']=base_url("lst/day_info/8/$type");
            $data['day9']=base_url("lst/day_info/9/$type");
            $data['day10']=base_url("lst/day_info/10/$type");
            $data['day11']=base_url("lst/day_info/11/$type");
            $data['day12']=base_url("lst/day_info/12/$type");
            $data['day13']=base_url("lst/day_info/13/$type");
            $data['day14']=base_url("lst/day_info/14/$type");
            $data['day15']=base_url("lst/day_info/15/$type");
            $data['day16']=base_url("lst/day_info/16/$type");
            $data['day17']=base_url("lst/day_info/17/$type");
            $data['day18']=base_url("lst/day_info/18/$type");
            $data['day19']=base_url("lst/day_info/19/$type");
            $data['day20']=base_url("lst/day_info/20/$type");
            $data['day21']=base_url("lst/day_info/21/$type");
            $data['day22']=base_url("lst/day_info/22/$type");
            $data['day23']=base_url("lst/day_info/23/$type");
            $data['day24']=base_url("lst/day_info/24/$type");
            $data['day25']=base_url("lst/day_info/25/$type");
            $data['day26']=base_url("lst/day_info/26/$type");
            $data['day27']=base_url("lst/day_info/27/$type");
            $data['day28']=base_url("lst/day_info/28/$type");
            $data['day29']=base_url("lst/day_info/29/$type");
            $data['day30']=base_url("lst/day_info/30/$type");
            $data['day31']=base_url("lst/day_info/31/$type");
            $data['day32']=base_url("lst/day_info/32/$type");
            $data['day33']=base_url("lst/day_info/33/$type");
            $data['day62']=base_url("lst/day_info/62/$type");
            $data['day72']=base_url("lst/day_info/72/$type");
            $data['day73']=base_url("lst/day_info/73/$type");
            $data['day82']=base_url("lst/day_info/82/$type");
            $data['day83']=base_url("lst/day_info/83/$type");
            $data['day92']=base_url("lst/day_info/92/$type");

            $data['day01']=base_url("lst/day_info/01/$type");
            $data['day017']=base_url("lst/day_info/017/$type");


            $data['local']=base_url("lst/hotel/local/$type");
            $data['local2']=base_url("lst/hotel/local2/$type");
            $data['inter']=base_url("lst/hotel/inter/$type");
            $data['hotel1']=base_url("lst/hotel/hotel1/$type");
            $data['hotel2']=base_url("lst/hotel/hotel2/$type");
            $data['hotel3']=base_url("lst/hotel/hotel3/$type");
            $data['hotel4']=base_url("lst/hotel/hotel4/$type");
            $data['hotel5']=base_url("lst/hotel/hotel5/$type");
            return $data;
        }

    public function get_day_url2($type)
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
        $data['local']=base_url("lvt/hotel/local/$type");
        $data['local2']=base_url("lvt/hotel/local2/$type");
        $data['inter']=base_url("lvt/hotel/inter/$type");
        $data['hotel1']=base_url("lvt/hotel/hotel1/$type");
        $data['hotel2']=base_url("lvt/hotel/hotel2/$type");
        $data['hotel3']=base_url("lvt/hotel/hotel3/$type");
        $data['hotel4']=base_url("lvt/hotel/hotel4/$type");
        $data['hotel5']=base_url("lvt/hotel/hotel5/$type");
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
    //西域
    public function xyinfo()
    {
        $data=$this->get_day_url('xy');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='西域游';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        $data['index_url']=base_url('lst/xyinfo');
        $data['shareimage']="http://api.etjourney.com/public/xy/img/xy_share.png";
//xy_share.png
        $this->load->view('xy/index',$data);
        $this->show_count();
    }

    //纯享
    public function cxinfo()
    {
        $data=$this->get_day_url('cx');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯享普吉岛4晚6天游';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        $data['index_url']=base_url('lst/cxinfo');
        $data['shareimage']="http://api.etjourney.com/public/cx/images/cx_share.jpg";

        $this->load->view('cx/index',$data);
        $this->show_count();

    }
   //帆普归臻
    public function fanpuinfo()
    {
        $data=$this->get_day_url('fanpu');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉新印象，吉祥航空上海直飞6天4夜';
        $data['share_desc']='特别赠送3588元超值大礼包';
        $data['index_url']=base_url('lst/fanpuinfo');

        $data['shareimage']="http://src.etjourney.com/public/fanpu/images/share.jpg";

        $this->load->view('fanpu/index',$data);
        $this->show_count();

    }
    //游轮
    public function youluninfo()
    {
        $data=$this->get_day_url('youlun');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='诺唯真游轮喜悦号—— 海上头等舱  2017年夏季首航';
        $data['share_desc']='喜悦号“游轮教父”王力宏';
        $data['index_url']=base_url('lst/youluninfo/tuniu-xiyuehao');

        $data['shareimage']="http://src.etjourney.com/public/youlun/images/share.jpg";

        $this->load->view('youlun/index',$data);
        $this->show_count();

    }
       //简享团
    public function jxtinfo()
    {
        $data=$this->get_day_url('jxt');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='简享普吉岛5晚7天游';
        $data['share_desc']='酒店住宿随意选，当地特色项目随意搭，定制自己的特色行程。';
        $data['index_url']=base_url('lst/jxtinfo');

        $data['shareimage']="http://api.etjourney.com/public/jxt/images/share.jpg";

        $this->load->view('jxt/index',$data);
        $this->show_count();

    }
    //芽庄
    public function yazhuanginfo()
    {
        $data=$this->get_day_url('yazhuang');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='越南芽庄一地8天7晚';
        $data['share_desc']='海清沙白、椰林婆娑、人少景美！';
        $data['index_url']=base_url('lst/yazhuanginfo');
        $data['shareimage']='http://api.etjourney.com/public/yazhuang/images/share.jpg';

        $this->load->view('yazhuang/index',$data);
        $this->show_count();

    }
    //南洋风情自由行fit
    public function nanyanginfo()
    {

        $data=$this->get_day_url('nanyang');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='嗨翻普吉自由行，多条线路任你行';
        $data['share_desc']='凡购任一产品，凭2本护照送一个品牌乳胶枕';
        $data['index_url']=base_url('lst/nanyanginfo');
        $data['shareimage']="http://api.etjourney.com/public/nanyang/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('nanyang/index',$data);
        $this->show_count();
    }
    //自由行
    public function ziyouxinginfo()
    {

        $data=$this->get_day_url('ziyouxing');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='嗨翻普吉自由行，多条线路任你行';
        $data['share_desc']='凡购任一产品，凭2本护照送一个品牌乳胶枕';
        $data['index_url']=base_url('lst/ziyouxinginfo');
        $data['shareimage']="http://api.etjourney.com/public/ziyouxing/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('ziyouxing/index',$data);
        $this->show_count();
    }
    //二销
    public function erxiaoinfo()
    {

        $data=$this->get_day_url('erxiao');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='二销';
        $data['share_desc']='二销';
        $data['index_url']=base_url('lst/erxiaoinfo');
        $data['shareimage']="http://api.etjourney.com/public/erxiao/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('erxiao/index',$data);
        $this->show_count();
    }

    //优选版，台湾环岛8天 （直飞，台北进出）
    public function twhd_yxb_8dinfo()
    {

        $data=$this->get_day_url('twhd_yxb_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='优选版，台湾环岛8天 （直飞，台北进出）';
        $data['share_desc']='优选版，台湾环岛8天 （直飞，台北进出）';
        $data['index_url']=base_url('lst/twhd_yxb_8dinfo/SHLY-TWYXB-8D');
        $data['shareimage']="http://api.etjourney.com/public/twhd_yxb_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('twhd_yxb_8d/index',$data);
        $this->show_count();
    }
    
    //浪漫夏威夷8天6晚精品半自助游
    public function zy_xiaweiyi8d6ninfo()
    {

        $data=$this->get_day_url('zy_xiaweiyi8d6n');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='浪漫夏威夷8天6晚精品半自助游';
        $data['share_desc']='浪漫夏威夷8天6晚精品半自助游';
        $data['index_url']=base_url('lst/zy_xiaweiyi8d6ninfo/MY-XWY-8D6N');
        $data['shareimage']="http://api.etjourney.com/public/zy_xiaweiyi8d6n/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_xiaweiyi8d6n/index',$data);
        $this->show_count();
    }
    //台湾优选版环岛8日游（直飞、四星+五星酒店、台北进出、晚去晚回）
    public function twyxbhd_8dinfo()
    {

        $data=$this->get_day_url('twyxbhd_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='台湾优选版环岛8日游（直飞、四星+五星酒店、台北进出、晚去晚回）';
        $data['share_desc']='台湾优选版环岛8日游（直飞、四星+五星酒店、台北进出、晚去晚回）';
        $data['index_url']=base_url('lst/twyxbhd_8dinfo/SHLY-TWYXB-8D');
        $data['shareimage']="http://api.etjourney.com/public/twyxbhd_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('twyxbhd_8d/index',$data);
        $this->show_count();
    }
    //巴厘岛送文莱5N6D行程(纯玩升级版)
    public function bldswl5n6dinfo()
    {

        $data=$this->get_day_url('bldswl5n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='巴厘岛送文莱5N6D行程(纯玩升级版)';
        $data['share_desc']='巴厘岛送文莱5N6D行程(纯玩升级版)';
        $data['index_url']=base_url('lst/bldswl5n6dinfo/KLJQ-SH-BLDWL-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/bldswl5n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('bldswl5n6d/index',$data);
        $this->show_count();
    }
    //【纯玩】台湾秋季版环岛8日游（直飞、四星+五星酒店、台北进出）
    public function cw_twqjbhd_8dinfo()
    {

        $data=$this->get_day_url('cw_twqjbhd_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【纯玩】台湾秋季版环岛8日游（直飞、四星+五星酒店、台北进出）';
        $data['share_desc']='【纯玩】台湾秋季版环岛8日游（直飞、四星+五星酒店、台北进出）';
        $data['index_url']=base_url('lst/cw_twqjbhd_8dinfo/SHLY-CW-TWQZB-8D');
        $data['shareimage']="http://api.etjourney.com/public/cw_twqjbhd_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('cw_twqjbhd_8d/index',$data);
        $this->show_count();
    }
    //普吉岛自由行-上航国旅
    public function zy_puji_5n7dinfo()
    {

        $data=$this->get_day_url('zy_puji_5n7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉岛自由行-上航国旅';
        $data['share_desc']='普吉岛自由行-上航国旅';
        $data['index_url']=base_url('lst/zy_puji_5n7dinfo/SH-5N7D');
        $data['shareimage']="http://api.etjourney.com/public/zy_puji_5n7d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_puji_5n7d/index',$data);
        $this->show_count();
    }
    //欧洲境外用车
    public function ouzhou_jwycinfo()
    {

        $data=$this->get_day_url('ouzhou_jwyc');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='欧洲境外用车';
        $data['share_desc']='欧洲境外用车';
        $data['index_url']=base_url('lst/ouzhou_jwycinfo/JFGL');
        $data['shareimage']="http://api.etjourney.com/public/ouzhou_jwyc/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('ouzhou_jwyc/index',$data);
        $this->show_count();
    }
    //阿尔金山探险12日游
    public function aerjinshantanxian_12dinfo()
    {

        $data=$this->get_day_url('aerjinshantanxian_12d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='阿尔金山探险12日游';
        $data['share_desc']='阿尔金山探险12日游';
        $data['index_url']=base_url('lst/aerjinshantanxian_12dinfo/RHHK-AEJSTX-WLMQ-12D');
        $data['shareimage']="http://api.etjourney.com/public/aerjinshantanxian_12d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('aerjinshantanxian_12d/index',$data);
        $this->show_count();
    }
    //探寻“地球之耳”越野8日游
    public function txdqzeyy_8dinfo()
    {

        $data=$this->get_day_url('txdqzeyy_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='探寻“地球之耳”越野8日游';
        $data['share_desc']='探寻“地球之耳”越野8日游';
        $data['index_url']=base_url('lst/txdqzeyy_8dinfo/RHHK-TXDQZEYY-QGGD-9D');
        $data['shareimage']="http://api.etjourney.com/public/txdqzeyy_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('txdqzeyy_8d/index',$data);
        $this->show_count();
    }
    //天池、那拉提、库车大峡谷、罗布人村寨、博斯腾湖、吐鲁番9日游
    public function xingzoutianshan_9dinfo()
    {

        $data=$this->get_day_url('xingzoutianshan_9d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='天池、那拉提、库车大峡谷、罗布人村寨、博斯腾湖、吐鲁番9日游';
        $data['share_desc']='天池、那拉提、库车大峡谷、罗布人村寨、博斯腾湖、吐鲁番9日游';
        $data['index_url']=base_url('lst/xingzoutianshan_9dinfo/RHHK-XZTS-QGGD-9D');
        $data['shareimage']="http://api.etjourney.com/public/xingzoutianshan_9d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('xingzoutianshan_9d/index',$data);
        $this->show_count();
    }
    
    //清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】
    public function qingmaiqinglai_4n6d1info()
    {

        $data=$this->get_day_url('qingmaiqinglai_4n6d1');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】';
        $data['share_desc']='清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】';
        $data['index_url']=base_url('lst/qingmaiqinglai_4n6d1info/SH-QMQL-SH-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/qingmaiqinglai_4n6d1/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('qingmaiqinglai_4n6d1/index',$data);
        $this->show_count();
    }
    //清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】
    public function qingmaiqinglai_4n6dinfo()
    {

        $data=$this->get_day_url('qingmaiqinglai_4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】';
        $data['share_desc']='清迈清莱4晚6日游（纯玩泰航转机）【黑白庙/纯玩无购物/一天自由活动】';
        $data['index_url']=base_url('lst/qingmaiqinglai_4n6dinfo/SH-QMQL-SH-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/qingmaiqinglai_4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('qingmaiqinglai_4n6d/index',$data);
        $this->show_count();
    }
    //新马波德申4晚6日游
    public function xmbds_4n6dinfo()
    {

        $data=$this->get_day_url('xinmabodeshen_4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='新马波德申4晚6日游';
        $data['share_desc']='新马波德申4晚6日游';
        $data['index_url']=base_url('lst/xmbds_4n6dinfo/SH-XMBDS-SH-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/xmbds_4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('xmbds_4n6d/index',$data);
        $this->show_count();
    }
    //新马波德申4晚6日游
    public function xinmabodeshen_4n6dinfo()
    {

        $data=$this->get_day_url('xinmabodeshen_4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='新马波德申4晚6日游';
        $data['share_desc']='新马波德申4晚6日游';
        $data['index_url']=base_url('lst/xinmabodeshen_4n6dinfo/SH-XMBDS-SH-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/xinmabodeshen_4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('xinmabodeshen_4n6d/index',$data);
        $this->show_count();
    }    
    //意大利南部7-10天行程含酒店
    public function zy_yidali_nb_10dinfo()
    {

        $data=$this->get_day_url('zy_yidali_nb_10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='意大利南部7-10天行程含酒店 ';
        $data['share_desc']='意大利南部7-10天行程含酒店';
        $data['index_url']=base_url('lst/zy_yidali_nb_10dinfo/JFGL-YDL-SH-7D11D');
        $data['shareimage']="http://api.etjourney.com/public/zy_yidali_nb_10d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_yidali_nb_10d/index',$data);
        $this->show_count();
    }
    //意大利音乐艺术之旅
    public function zy_yidali_yyyszl_11dinfo()
    {

        $data=$this->get_day_url('zy_yidali_yyyszl_11d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='意大利音乐艺术之旅  ';
        $data['share_desc']='意大利音乐艺术之旅 ';
        $data['index_url']=base_url('lst/zy_yidali_yyyszl_11dinfo/JFGL-YDL-SH-11D');
        $data['shareimage']="http://api.etjourney.com/public/zy_yidali_yyyszl_11d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_yidali_yyyszl_11d/index',$data);
        $this->show_count();
    }
    //【度假】香港4日自由行
    public function zy_xianggang4dinfo()
    {

        $data=$this->get_day_url('zy_xianggang4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【度假】香港4日自由行    ';
        $data['share_desc']='【度假】香港4日自由行    ';
        $data['index_url']=base_url('lst/zy_xianggang4dinfo/SHLY-DJXG-4D3N');
        $data['shareimage']="http://api.etjourney.com/public/zy_xianggang4d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_xianggang4d/index',$data);
        $this->show_count();
    }

    //【纯玩】台湾夏季版环岛8日游（直飞、四星+五星酒店、台北进出）
    public function taiwan_xjbhd_cw_8dinfo()
    {

        $data=$this->get_day_url('taiwan_xjbhd_cw_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='【纯玩】台湾夏季版环岛8日游（直飞、四星+五星酒店、台北进出）';
        $data['share_desc']='【纯玩】台湾夏季版环岛8日游（直飞、四星+五星酒店、台北进出）';
        $data['index_url']=base_url('lst/taiwan_xjbhd_cw_8dinfo/SHLY-TAIWAN-8D');
        $data['shareimage']="http://api.etjourney.com/public/taiwan_xjbhd_cw_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('taiwan_xjbhd_cw_8d/index',$data);
        $this->show_count();
    }

    //澳港品质5日游
    public function gangao_pz_5dinfo()
    {

        $data=$this->get_day_url('gangao_pz_5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='澳港品质5日游';
        $data['share_desc']='澳港品质5日游';
        $data['index_url']=base_url('lst/gangao_pz_5dinfo/SHGATB-GANAO-5D');
        $data['shareimage']="http://api.etjourney.com/public/gangao_pz_5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('gangao_pz_5d/index',$data);
        $this->show_count();
    }
    //澳港5日奥特莱斯游
    public function ganao_atls_5dinfo()
    {

        $data=$this->get_day_url('ganao_atls_5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='澳港5日奥特莱斯游';
        $data['share_desc']='澳港5日奥特莱斯游';
        $data['index_url']=base_url('lst/ganao_atls_5dinfo/SHGATB-GANAO-5D');
        $data['shareimage']="http://api.etjourney.com/public/ganao_atls_5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('ganao_atls_5d/index',$data);
        $this->show_count();
    }

    //净YOUNG苏梅岛--半自由2+2浪漫8日之旅
    public function jingyoung_msd_8dinfo()
    {

        $data=$this->get_day_url('jingyoung_msd_8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='净YOUNG苏梅岛--半自由2+2浪漫8日之旅';
        $data['share_desc']='净YOUNG苏梅岛--半自由2+2浪漫8日之旅';
        $data['index_url']=base_url('lst/jingyoung_msd_8dinfo/HY-WLMQ-MSD-8D');
        $data['shareimage']="http://api.etjourney.com/public/jingyoung_msd_8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('jingyoung_msd_8d/index',$data);
        $this->show_count();
    }
    //”里昂灯光节+圣诞集市“法国一地深度12日

    public function faguo_liang_dgjs12dinfo()
    {

        $data=$this->get_day_url('faguo_liang_dgjs12d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='”里昂灯光节+圣诞集市“法国一地深度12日';
        $data['share_desc']='”里昂灯光节+圣诞集市“法国一地深度12日';
        $data['index_url']=base_url('lst/faguo_liang_dgjs12dinfo/ZXZTYB-FG-12D');
        $data['shareimage']="http://api.etjourney.com/public/faguo_liang_dgjs12d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('faguo_liang_dgjs12d/index',$data);
        $this->show_count();
    }
    //暑假马航出发巴厘岛7天5晚自由行

    public function zy_balidao5d7ninfo()
    {

        $data=$this->get_day_url('zy_balidao5d7n');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='巴厘岛5晚7天自由行';
        $data['share_desc']='巴厘岛5晚7天自由行';
        $data['index_url']=base_url('lst/zy_balidao5d7ninfo/KLJQ-SH-BLD-5N7D');
        $data['shareimage']="http://api.etjourney.com/public/zy_balidao5d7n/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_balidao5d7n/index',$data);
        $this->show_count();
    }
    //普吉赠送一晚吉隆坡六日游

    public function puji_jlp5n6dinfo()
    {

        $data=$this->get_day_url('puji_jlp5n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉赠送一晚吉隆坡六日游';
        $data['share_desc']='普吉赠送一晚吉隆坡六日游';
        $data['index_url']=base_url('lst/puji_jlp5n6dinfo/KLJQ-SH-PJ-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/puji_jlp5n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('puji_jlp5n6d/index',$data);
        $this->show_count();
    }

    //“环球视野•快乐少年”军事精英营（八日）
    public function junshixly8dinfo()
    {

        $data=$this->get_day_url('junshixly8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='“环球视野•快乐少年”军事精英营（八日）';
        $data['share_desc']='“环球视野•快乐少年”军事精英营（八日）';
        $data['index_url']=base_url('lst/junshixly8dinfo/HQSY-JSYLY-8D');
        $data['shareimage']="http://api.etjourney.com/public/junshixly8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('junshixly8d/index',$data);
        $this->show_count();
    }
    //“环球视野•快乐少年”军事精英营（十五日）
    public function junshixly15dinfo()
    {

        $data=$this->get_day_url('junshixly15d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='“环球视野•快乐少年”军事精英营（十五日）';
        $data['share_desc']='“环球视野•快乐少年”军事精英营（十五日）';
        $data['index_url']=base_url('lst/junshixly15dinfo/HQSY-JSYLY-15D');
        $data['shareimage']="http://api.etjourney.com/public/junshixly15d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('junshixly15d/index',$data);
        $this->show_count();
    }

    //迈阿密+奥兰多(四大主题乐园+肯尼迪航天中心)9天欢乐游之旅纯美
    public function chunmei_mam_ald_9dinfo()
    {

        $data=$this->get_day_url('chunmei_mam_ald_9d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='迈阿密+奥兰多(四大主题乐园+肯尼迪航天中心)9天欢乐游之旅纯美';
        $data['share_desc']='迈阿密+奥兰多(四大主题乐园+肯尼迪航天中心)9天欢乐游之旅纯美';
        $data['index_url']=base_url('lst/chunmei_mam_ald_9dinfo/MY-MAMALD-9D');
        $data['shareimage']="http://api.etjourney.com/public/chunmei_mam_ald_9d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('chunmei_mam_ald_9d/index',$data);
        $this->show_count();
    }
    //美西深度+17英里+南峡 10天纯美
    public function meixisd_17yl_nx_10dinfo()
    {

        $data=$this->get_day_url('meixisd_17yl_nx_10d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='美西深度+17英里+南峡 10天纯美';
        $data['share_desc']='美西深度+17英里+南峡 10天纯美';
        $data['index_url']=base_url('lst/meixisd_17yl_nx_10dinfo/MY-MXSDSQYLNX-10D');
        $data['shareimage']="http://api.etjourney.com/public/meixisd_17yl_nx_10d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('meixisd_17yl_nx_10d/index',$data);
        $this->show_count();
    }
    //（四星旗舰高铁）俄罗斯璀璨金环+拉多加湖6晚8日
    public function eluosilccjh_gt6n8dinfo()
    {

        $data=$this->get_day_url('eluosilccjh_gt6n8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='（四星旗舰高铁）俄罗斯璀璨金环+拉多加湖6晚8日';
        $data['share_desc']='（四星旗舰高铁）俄罗斯璀璨金环+拉多加湖6晚8日';
        $data['index_url']=base_url('lst/eluosilccjh_gt6n8dinfo/SXHY-ELS-GT-6N8D');
        $data['shareimage']="http://api.etjourney.com/public/eluosilccjh_gt6n8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('eluosilccjh_gt6n8d/index',$data);
        $this->show_count();
    }
    //（四星旗舰卧铺）俄罗斯璀璨金环+拉多加湖6晚8日
    public function eluosilccjh_wp6n8dinfo()
    {

        $data=$this->get_day_url('eluosilccjh_wp6n8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='（四星旗舰卧铺）俄罗斯璀璨金环+拉多加湖6晚8日';
        $data['share_desc']='（四星旗舰卧铺）俄罗斯璀璨金环+拉多加湖6晚8日';
        $data['index_url']=base_url('lst/eluosilccjh_wp6n8dinfo/SXHY-ELS-WP-6N8D');
        $data['shareimage']="http://api.etjourney.com/public/eluosilccjh_wp6n8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('eluosilccjh_wp6n8d/index',$data);
        $this->show_count();
    }
    //俄罗斯浪漫庄园6晚8日
    public function eluosilmzy6n8dinfo()
    {

        $data=$this->get_day_url('eluosilmzy6n8d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='俄罗斯浪漫庄园6晚8日';
        $data['share_desc']='俄罗斯浪漫庄园6晚8日';
        $data['index_url']=base_url('lst/eluosilmzy6n8dinfo/SXHY-ELS-6N8D');
        $data['shareimage']="http://api.etjourney.com/public/eluosilmzy6n8d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('eluosilmzy6n8d/index',$data);
        $this->show_count();
    }
    //AA巴西、阿根廷、智利、秘鲁（含2国伊瓜苏、大冰川、火地岛、马丘比丘）南美全境19天深度游
    public function aabxagtzlml19dinfo()
    {

        $data=$this->get_day_url('aabxagtzlml19d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='AA巴西、阿根廷、智利、秘鲁（含2国伊瓜苏、大冰川、火地岛、马丘比丘）南美全境19天深度游';
        $data['share_desc']='AA巴西、阿根廷、智利、秘鲁（含2国伊瓜苏、大冰川、火地岛、马丘比丘）南美全境19天深度游';
        $data['index_url']=base_url('lst/aabxagtzlml19dinfo/SHMZB-NM-BXAGTZLML-19D');
        $data['shareimage']="http://api.etjourney.com/public/aabxagtzlml19d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('aabxagtzlml19d/index',$data);
        $this->show_count();
    }
    //旗舰巴厘岛-超级星光钻石版超级星光钻石版 纯玩无购物 四晚五天
    public function balidaozhuanshi4n5dinfo()
    {

        $data=$this->get_day_url('balidaozhuanshi4n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='旗舰巴厘岛-超级星光钻石版';
        $data['share_desc']='纯玩无购物 四晚五天';
        $data['index_url']=base_url('lst/balidaozhuanshi4n5dinfo/HY-SH-BLD-4N5D');
        $data['shareimage']="http://api.etjourney.com/public/balidaozhuanshi4n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('balidaozhuanshi4n5d/index',$data);
        $this->show_count();
    }

    //沙巴一地4晚6天（FM上海航空直飞）

    public function zy_shaba4n6dinfo()
    {

        $data=$this->get_day_url('zy_shaba4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='沙巴一地4晚6天（FM上海航空直飞）';
        $data['share_desc']='沙巴一地4晚6天（FM上海航空直飞）';
        $data['index_url']=base_url('lst/zy_shaba4n6dinfo/SHDNY-SH-SB-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_shaba4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_shaba4n6d/index',$data);
        $this->show_count();
    }

    //沙巴一地【自由行】5晚7天（FM上海航空直飞）

    public function zy_shaba5n7dinfo()
    {

        $data=$this->get_day_url('zy_shaba5n7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='沙巴一地5晚7天（FM上海航空直飞）';
        $data['share_desc']='沙巴一地5晚7天（FM上海航空直飞）';
        $data['index_url']=base_url('lst/zy_shaba5n7dinfo/SHDNY-SH-SB-5N7D');
        $data['shareimage']="http://api.etjourney.com/public/zy_shaba5n7d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_shaba5n7d/index',$data);
        $this->show_count();
    }
    //上海—长滩岛 直飞 4晚5日机票+酒店  （周一晚上 机场集合）

    public function zy_changtan2p4n5dinfo()
    {

        $data=$this->get_day_url('zy_changtan2p4n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='上海—长滩岛 直飞 4晚5日自由行';
        $data['share_desc']='机票+酒店';
        $data['index_url']=base_url('lst/zy_changtan2p4n5dinfo/SHDNY-SH-CT-4N5D');
        $data['shareimage']="http://api.etjourney.com/public/zy_changtan2p4n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_changtan2p4n5d/index',$data);
        $this->show_count();
    }
    //上海—长滩岛 直飞 5晚6日机票+酒店  （周一晚上 机场集合）

    public function zy_changtan2p5n6dinfo()
    {

        $data=$this->get_day_url('zy_changtan2p5n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='上海—长滩岛 直飞 5晚6日自由行';
        $data['share_desc']='机票+酒店';
        $data['index_url']=base_url('lst/zy_changtan2p5n6dinfo/SHDNY-SH-CT-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_changtan2p5n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_changtan2p5n6d/index',$data);
        $this->show_count();
    }
    //普吉赠送一晚吉隆坡六日游自由行

    public function zy_pujijlp5n6dinfo()
    {

        $data=$this->get_day_url('zy_pujijlp5n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉赠送一晚吉隆坡六日游自由行';
        $data['share_desc']='普吉赠送一晚吉隆坡六日游自由行';
        $data['index_url']=base_url('lst/zy_pujijlp5n6dinfo/KLJQ-SH-PJ-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_pujijlp5n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_pujijlp5n6d/index',$data);
        $this->show_count();
    }
    //百城签证
    public function zy_baichenginfo()
    {

        $data=$this->get_day_url('zy_baicheng');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='百城签证';
        $data['share_desc']='百城签证';
        $data['index_url']=base_url('lst/zy_baichenginfo');
        $data['shareimage']="http://api.etjourney.com/public/zy_baicheng/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_baicheng/index',$data);
        $this->show_count();
    }
    //岘港自由行3晚4天
    public function zy_xiangan3n4dinfo()
    {

        $data=$this->get_day_url('zy_xiangan3n4d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='岘港自由行3晚4天';
        $data['share_desc']='岘港自由行3晚4天 ';
        $data['index_url']=base_url('lst/zy_xiangan3n4dinfo/KLJQ-SH-XG-3N4D');
        $data['shareimage']="http://api.etjourney.com/public/zy_xiangan3n4d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_xiangan3n4d/index',$data);
        $this->show_count();
    }
    //岘港自由行4晚5天
    public function zy_xiangan4n5dinfo()
    {

        $data=$this->get_day_url('zy_xiangan4n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='岘港自由行4晚5天';
        $data['share_desc']='岘港自由行4晚5天';
        $data['index_url']=base_url('lst/zy_xiangan4n5dinfo/KLJQ-SH-XG-4N5D');
        $data['shareimage']="http://api.etjourney.com/public/zy_xiangan4n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_xiangan4n5d/index',$data);
        $this->show_count();
    }
    //长滩3晚5天（自由行）
    public function zy_changtan3n5dinfo()
    {

        $data=$this->get_day_url('zy_changtan3n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='长滩3晚5天（自由行）';
        $data['share_desc']='长滩3晚5天（自由行）';
        $data['index_url']=base_url('lst/zy_changtan3n5dinfo/KLJQ-SH-CT-3N5D');
        $data['shareimage']="http://api.etjourney.com/public/zy_changtan3n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_changtan3n5d/index',$data);
        $this->show_count();
    }
    //长滩4晚6天（自由行）
    public function zy_changtan4n6dinfo()
    {

        $data=$this->get_day_url('zy_changtan4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='长滩4晚6天（自由行）';
        $data['share_desc']='长滩4晚6天（自由行）';
        $data['index_url']=base_url('lst/zy_changtan4n6dinfo/KLJQ-SH-CT-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_changtan4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_changtan4n6d/index',$data);
        $this->show_count();
    }

    //纯长滩3晚5天（自由行）
    public function zy_c_changtan3n5dinfo()
    {

        $data=$this->get_day_url('zy_c_changtan3n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯长滩3晚5天（自由行）';
        $data['share_desc']='纯长滩3晚5天（自由行）';
        $data['index_url']=base_url('lst/zy_c_changtan3n5dinfo/KLJQ-SH-CT-3N5D');
        $data['shareimage']="http://api.etjourney.com/public/zy_c_changtan3n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_c_changtan3n5d/index',$data);
        $this->show_count();
    }
    //纯长滩4晚6天（自由行）
    public function zy_c_changtan4n6dinfo()
    {

        $data=$this->get_day_url('zy_c_changtan4n6d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯长滩4晚6天（自由行）';
        $data['share_desc']='纯长滩4晚6天（自由行）';
        $data['index_url']=base_url('lst/zy_c_changtan4n6dinfo/KLJQ-SH-CT-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_c_changtan4n6d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_c_changtan4n6d/index',$data);
        $this->show_count();
    }
    //纯长滩5晚7天（自由行）
    public function zy_c_changtan5n7dinfo()
    {

        $data=$this->get_day_url('zy_c_changtan5n7d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯长滩5晚7天（自由行）';
        $data['share_desc']='纯长滩5晚7天（自由行）';
        $data['index_url']=base_url('lst/zy_c_changtan5n7dinfo/KLJQ-SH-CT-5N7D');
        $data['shareimage']="http://api.etjourney.com/public/zy_c_changtan5n7d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_c_changtan5n7d/index',$data);
        $this->show_count();
    }
    //清迈自由行（四晚五天）
    public function zy_qingmai4n5dinfo()
    {

        $data=$this->get_day_url('zy_qingmai4n5d');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈自由行（四晚五天）';
        $data['share_desc']='清迈自由行（四晚五天）';
        $data['index_url']=base_url('lst/zy_qingmai4n5dinfo/KLJQ-QM-4N5D');
        $data['shareimage']="http://api.etjourney.com/public/zy_qingmai4n5d/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_qingmai4n5d/index',$data);
        $this->show_count();
    }
    //上海-兰卡威3晚吉隆坡1晚，4晚6天自由行
    public function zy_klwjlpinfo()
    {

        $data=$this->get_day_url('zy_klwjlp');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='上海-兰卡威3晚吉隆坡1晚，4晚6天自由行';
        $data['share_desc']='上海-兰卡威3晚吉隆坡1晚，4晚6天自由行';
        $data['index_url']=base_url('lst/zy_klwjlpinfo/ZS-LKW-JLP-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/zy_klwjlp/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('zy_klwjlp/index',$data);
        $this->show_count();
    }
    //纯玩：0824 新西兰南北岛11天MU上海
    public function cwxxlinfo()
    {

        $data=$this->get_day_url('cwxxl');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='CW-【魔戒冰雪奇缘】新西兰南北岛11天';
        $data['share_desc']='CW-【魔戒冰雪奇缘】新西兰南北岛11天';
        $data['index_url']=base_url('lst/cwxxlinfo/tuyi-0824');
        $data['shareimage']="http://api.etjourney.com/public/cwxxl/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('cwxxl/index',$data);
        $this->show_count();
    }
    //澳大利亚凯恩斯墨尔本全景+新西兰12天
    public function aokiamoxininfo()
    {

        $data=$this->get_day_url('aokiamoxin');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='澳大利亚凯恩斯墨尔本全景+新西兰12天';
        $data['share_desc']='澳大利亚凯恩斯墨尔本全景+新西兰12天';
        $data['index_url']=base_url('lst/aokiamoxininfo/tuyi-0922');
        $data['shareimage']="http://api.etjourney.com/public/aokiamoxin/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('aokiamoxin/index',$data);
        $this->show_count();
    }
    //私人定制游清迈清新泰北4日游
    public function qmqxtbinfo()
    {

        $data=$this->get_day_url('qmqxtb');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='私人定制•游•清迈清新泰北4日游';
        $data['share_desc']='私人定制•游•清迈清新泰北4日游';
        $data['index_url']=base_url('lst/qmqxtbinfo/tuyi-0922');
        $data['shareimage']="http://api.etjourney.com/public/qmqxtb/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('qmqxtb/index',$data);
        $this->show_count();
    }
    //纯玩畅游普吉
    public function cwcypjinfo()
    {

        $data=$this->get_day_url('cwcypj');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='纯玩畅游普吉';
        $data['share_desc']='私人帆船游艇\食尚盛宴\奢华酒店';
        $data['index_url']=base_url('lst/cwcypjinfo');
        $data['shareimage']="http://api.etjourney.com/public/cwcypj/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('cwcypj/index',$data);
        $this->show_count();
    }
    //澳大利亚海豚岛+企鹅岛+塔斯马尼亚+新西兰17天
    public function aozhou0721info()
    {

        $data=$this->get_day_url('aozhou0721');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='澳大利亚海豚岛+企鹅岛+塔斯马尼亚+新西兰17天';
        $data['share_desc']='澳大利亚海豚岛+企鹅岛+塔斯马尼亚+新西兰17天';
        $data['index_url']=base_url('lst/aozhou0721info');
        $data['shareimage']="http://api.etjourney.com/public/aozhou0721/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('aozhou0721/index',$data);
        $this->show_count();
    }
    //圆梦椿山庄日本舒享6日
    public function chunshanzhuanginfo()
    {

        $data=$this->get_day_url('chunshanzhuang');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='圆梦椿山庄日本舒享6日';
        $data['share_desc']='圆梦椿山庄日本舒享6日';
        $data['index_url']=base_url('lst/chunshanzhuanginfo/zhongxinhuabei-chunshanzhuang');
        $data['shareimage']="http://api.etjourney.com/public/chunshanzhuang/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('chunshanzhuang/index',$data);
        $this->show_count();
    }
    //普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版/四晚六天 （上海/南京/无锡往返）
    public function haitun46info()
    {

        $data=$this->get_day_url('haitun46');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版';
        $data['share_desc']='四晚六天 （上海/南京/无锡往返）';
        $data['index_url']=base_url('lst/haitun46info/HT-PJ-HTZL-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/haitun46/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('haitun46/index',$data);
        $this->show_count();
    }

     //普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版/五晚七天 （上海/南京/无锡往返）
    public function haitun57info()
    {

        $data=$this->get_day_url('haitun57');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版';
        $data['share_desc']='五晚七天 （上海/南京/无锡往返）';
        $data['index_url']=base_url('lst/haitun57info/HT-PJ-HTZL-5N7D');
        $data['shareimage']="http://api.etjourney.com/public/haitun57/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('haitun57/index',$data);
        $this->show_count();
    }

    //普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版/五晚六天 （上海/南京/无锡往返）
    public function haitun56info()
    {

        $data=$this->get_day_url('haitun56');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='普吉 • 海豚之恋 • 暑期亲子游 • 行程升级版';
        $data['share_desc']='五晚六天 （上海/南京/无锡往返）';
        $data['index_url']=base_url('lst/haitun56info/HT-PJ-HTZL-5N6D');
        $data['shareimage']="http://api.etjourney.com/public/haitun56/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('haitun56/index',$data);
        $this->show_count();
    }
    //清迈-清心の旅-优惠休闲游四晚六天 （上海口岸）
    public function qingmai_qxzlinfo()
    {

        $data=$this->get_day_url('qingmai_qxzl');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈-清心の旅-优惠休闲游';
        $data['share_desc']='四晚六天 （上海口岸）';
        $data['index_url']=base_url('lst/qingmai_qxzlinfo/HT-QM-QXZL-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/qingmai_qxzl/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('qingmai_qxzl/index',$data);
        $this->show_count();
    }
    //清迈 • 尊尚纯玩 • 行程升级版品质休闲游 四晚六天 （上海口岸）
    public function qingmai_zscwinfo()
    {

        $data=$this->get_day_url('qingmai_zscw');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='清迈 • 尊尚纯玩 • 行程升级版';
        $data['share_desc']='品质休闲游 四晚六天 （上海口岸）';
        $data['index_url']=base_url('lst/qingmai_zscwinfo/HT-QM-ZSCW-4N6D');
        $data['shareimage']="http://api.etjourney.com/public/qingmai_zscw/images/share.jpg";
        $data['test']=isset($_GET['test']) ? $_GET['test'] : '';

        $this->load->view('qingmai_zscw/index',$data);
        $this->show_count();
    }
    //纯蓝普吉
    public function clinfo()
    {
        $data=$this->get_day_url('cl');
        $data['signPackage']=$this->wx_js_para(3);
        $data['share_title']='优美.纯蓝普吉5晚7天游';
        $data['share_desc']='豪华五星住宿';
        $data['index_url']=base_url('lst/clinfo');
        $data['shareimage']=$this->shareimage_forcl;

        $this->load->view('cl/index',$data);
        $this->show_count();

    }
    //纯享 $type cx 纯享 yx 悦享用
    public function day_info($day,$type='cx')
    {
        $data=[];

            $this->load->view("$type/day{$day}",$data);
            $this->show_count();

    }
    //注册
    public function register($type)
    {
        $data=[];

            $this->load->view("$type/register",$data);
            $this->show_count();

    }

    //登录
    public function login($type)
    {
        $data=[];

            $this->load->view("$type/login",$data);
            $this->show_count();

    }

    //咨询
    public function ask($type)
    {
        $data=[];

            $this->load->view("$type/ask",$data);
            $this->show_count();

    }

    //购买
    public function buy($type)
    {
        $data=[];

            $this->load->view("$type/buy",$data);
            $this->show_count();

    }

    //登录
    public function my($type)
    {
        $data=[];

            $this->load->view("$type/my",$data);
            $this->show_count();

    }

    //登录
    public function success($type)
    {
        $data=[];

            $this->load->view("$type/success",$data);
            $this->show_count();

    }




    //悦享
    public function yxinfo()
    {
       // echo 1;exit();
        $data=$this->get_day_url('yx');
        $data['share_title']='悦享普吉岛5晚7天游';
        $data['share_desc']='悦享普吉岛5晚7天游。';
        $data['index_url']=base_url('lst/yxinfo');
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
        $data['index_url']=base_url('lst/sqbjdinfo');
        $data['shareimage']='/public/yazhuang/images/share.png';


        $data['signPackage']=$this->wx_js_para(3);

        $this->load->view('sqbjd/index',$data);
        $this->show_count();
    }


        //$type local inter   $for cx yx
    public function hotel($type,$for)
    {
        $data=[];
        if(!in_array($type,array('local','inter','login','ask','buy')) OR !in_array($for,array('cx','yx','lx')))
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