<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Wxcode extends CI_Controller {
	public function __construct()
	{
    	parent::__construct();
        $this->load->helper('url');
  	}
	

    //type 微信用户身份 0普通 1 导游
    public function get_wx_code($type=0)
    {
        //车购普通版回调链接
        $redirect_uri = 'http://thai.etjourney.com/goodsforcar/index?code=';
        $url=base_url('test/get_wx_userid');
        //车购高价版回调链接
        if($type==1)
        {
            $url .= '/'.$type; 
            $redirect_uri = 'http://thai.etjourney.com/goodsforcar/index_high?code=';
        }
        //导游信息注册收集回调链接
        elseif($type==2)
        {
            $url .= '/'.$type; 
            $redirect_uri = 'http://thai.etjourney.com/caradmin/pre_index?code=';
        }
        //导游后台回调链接
        elseif($type==3)
        {
            $url .= '/'.$type; 
            $redirect_uri = 'http://thai.etjourney.com/caradmin/index?code=';
        }
        $this->load->library('Wxauth');

        if (!isset($_GET['code']))
        {
            //base_url("bussell/order_add_fromwx?act_id={$act_id}")
            //触发微信返回code码
            $url = $this->wxauth->createOauthUrlForCode_all(urlencode($url));
            //var_dump($url);exit();
            redirect($url);
        }
        else
        {
            //获取code码，以获取openid
            $code = $_GET['code'];
            //$redirect_uri = 'http://thai.etjourney.com/test/get_wx_userid?code='.$code;
            //拼接code跳转回源站
            $redirect_uri .= $code;
            redirect($redirect_uri);
            exit();
        }

    }
}
?>