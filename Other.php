<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Other extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('common');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->web_url="http://new.cnzz.com/v1/login.php?siteid=1258510548";
        $this->down_count='http://mobile.umeng.com/apps ';
        $this->load->model('User_model');
        $this->load->model('Order_model');

    }


    public function order_list($page =1)
    {
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $order_status= $data['order_status']= $this->input->get('order_status',true);
        if(!$order_status){
            $order_status=0;
        }
        $where="from ='0'";
        $where="1=1";

        if($data['time1'])
        {
            $where.=" AND add_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND add_time <=$data[time2]";
        }

        if($data['title'])
        {
            if($data['type']==1)
            {
                $where.= " AND (order_id LIKE '%$data[title]%'  OR order_sn LIKE '%$data[title]%')";
            }
            elseif($data['type']==2)
            {
                $where.= " AND (user_id_buy_name LIKE '%$data[title]%'  OR user_id_sell_name LIKE '%$data[title]%' OR user_id_buy_fromwx LIKE '%$data[title]%')";
            }
        }
        else
        {
            $data['type']=0;
        }

        $where.="  AND order_status= '$order_status' AND from='12' AND is_show='1'";


        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_order_info');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $data['list'] = $this->Order_model->get_order_list($where,$start,$page_num);
        // echo "<pre>",print_r($data);exit();
        //echo $this->db->last_query();
        $this->load->view('other/order_info_list',$data);
    }

    public function order_del($order_id)
    {
        //$order_id=$this->input->get('order_id',TRUE);
        $this->User_model->update_one(array('order_id'=>$order_id),array('is_show'=>'2'),$table='v_order_info');
        return   redirect($_SERVER['HTTP_REFERER']);

    }

    public function order_detail_for12()
    {
        $order_id=$this->input->get('order_id',TRUE);
        $data=$this->Order_model->get_order_detail($order_id);

        $this->load->view('wygoods/dingdan_detail',$data);
    }
}