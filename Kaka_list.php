<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class Kaka_list extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->library('common');
    }

        //增加页面
    public function index()
    {      
        $time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend= strtotime('+12 month', $time);
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

// 'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend))
        {
            $data['date']['cal'][] =array(
                'year'=>date('Y',strtotime('+1 month', $time)),
                'month'=>date('n',strtotime('+1 month', $time)),
                'month_cn'=>$this->common->get_month_cn(date('n',strtotime('+1 month', $time))),
                'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                'all_days'=>date('t',strtotime('+1 month', $time)),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
            );
            $time= strtotime('+1 month', $time);
        }

        $data['sub_url']=base_url('h5info/to_insert');

        $this->load->view('h5/index',$data);
    }
//卡卡通管理
    public function kaka_list($page=1){       
       $where="1=1";
       $names=$this->input->get('name',TRUE);
       $type=$this->input->get('type',TRUE);
       $is_off=$this->input->get('is_off',TRUE);
       $data['is_off']=$is_off;
          if($is_off==0){
          $where=" order_status =1"; 
         if($type==1){
           $where="  order_status in (1,2) AND consignee like '%$names%'  ";   
       }else if($type==2){
           $where="order_status in (1,2) AND mobile like '%$names%' ";
           
       }else{
           
           $where=" order_status in (1,2)  AND card_id like '%$names%' ";
       } 
           
       }else if($is_off==1){
           $where=" order_status=0";
           if($type==1){
           $where="  order_status=0 AND consignee like '%$names%'  ";   
       }else if($type==2){
           $where="  order_status=0 AND mobile like '%$names%' ";
           
       }else{
           
           $where="  order_status=0 AND card_id like '%$names%' ";
       }
           
       }
     //  $data['info']=$this->User_model->get_select_all($select='*',$where,'order_id','ASC','kk_order_info');
       $page_num =15;
        $data['now_page'] = $page;

        $count = $this->User_model->get_counts($where,'kk_order_info');
    
        $data['max_page'] = ceil($count['count']/$page_num);
      
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
         
        $data['info']=$this->User_model->get_goods_lists($where,'kk_order_info',$order_title='order_id',$order='ASC',$start,$page_num);
      foreach($data['info'] as $k=>$v){
           if($v['add_time']>0){
            $data['info'][$k]['ks_time']=date('Y-m-d H:i:s',$v['add_time']);
           }
           if($v['confirm_time']>0){
            $data['info'][$k]['jh_time']=date('Y-m-d H:i;s',$v['confirm_time']);       
           }
            }
        $this->load->view('kaka/kaka_list',$data);
    }

  //卡卡通账号
    public function kaka_info($page=1){
        $where="1=1";
        $type=$this->input->get('type',TRUE);
        $names=$this->input->get('name',TRUE);
        $is_off=$this->input->get('is_off',TRUE);
        $data['is_off']=$is_off;
          if($type==1){
            $where =" ka_id like '%$names%'";  
              
          } 
         if($is_off==0){
            $where = "ka_status=1"; 
               if($type==1){
            $where ="  ka_status=1 AND ka_id like '%$names%'";  
              
          } 
         }else if($is_off==1){
             
            $where ="ka_status=0"; 
              if($type==1){
            $where =" ka_status=0 AND ka_id like '%$names%'";  
              
          } 
         } 
          $page_num =15;
        $data['now_page'] = $page;

        $count = $this->User_model->get_counts($where,'kk_card_info');
    
        $data['max_page'] = ceil($count['count']/$page_num);
      
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
             
        $data['info']=$this->User_model->get_goods_lists($where,'kk_card_info',$order_title='id',$order='ASC',$start,$page_num);
        $this->load->view('kaka/kaka_info',$data);
    }
//测试卡卡通订单提交
    public function  ceshi_kk(){
        
        $this->load->view('kaka/write');
    }
 
 public function kk_insert(){
    
      $data['consignee']=$this->input->post('uName',TRUE);
      $data['mobile']=$this->input->post('uTel',TRUE);
      $data['card_id']=$this->input->post('card',TRUE);
      $data['cf_date']=$this->input->post('uDate',TRUE);
      $data['address']=$this->input->post('lx',TRUE);
      $data['commont']=$this->input->post('bz',TRUE);
      $data['order_amount']=$this->input->post('money',TRUE);
     $data['add_time']=  time();
      $data['order_status']=1;
      $data['from']=$this->input->post('from',TRUE);
      $red=$this->User_model->user_insert('kk_order_info',$data);
      if($red){
          echo "订单插入成功";
      }
 }
 //卡卡通卡号激活接口测试
	public function kakatest()
	{  
                    $id=$this->input->post('id',TRUE);
                   $where="order_id=$id";
                   $red=$this->User_model->kaka_jihuo($select='*',$where);
//                   echo $this->db->last_query();
//                   print_r($red);
//                   return $red;
                   $wheres=" ka_status='0'  LIMIT 1;";
                $kaka=$this->User_model->kaka_kahao($select='*',$wheres);
                  $url = 'http://acc.xjkakatong.com/index.php/agent/do/index.html';
                   $data=array(
                       'token'=>'dd23d4d54d81ed07cc7b37dd0c6bde48',
                       'bizcode'=>'001',
                       'card_id'=>$red[0]['card_id'],
                       'name'=>$red[0]['consignee'],
                       'ka_id'=>$kaka[0]['kaka_id'],
                       'ka_password'=>$kaka[0]['ka_password'],
                       'mobile'=>$red[0]['mobile']
                   );
          //       print_r($data);
           //       return $data;
                   
                   
		
//		$data=array(
//				'token' => 'dd23d4d54d81ed07cc7b37dd0c6bde48',
//				'bizcode' => '001',
//				'card_id' => '342422198107286320',
//				'name' => '张三',
//				'ka_id' => '6688160311128508',
//				'ka_password' => '859807',
//				'mobile' => ''
//			);
		//echo '<pre>';
		//print_r($data);
		$ch = curl_init();  
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       // $res = curl_exec($ch);  

        ob_start();  
        curl_exec($ch);  
        $return_content = ob_get_contents();  
        ob_end_clean();
        curl_close($ch);

     //   echo 'res1=';
    //    print_r($return_content);
     $res2 = json_decode($return_content);
    //    echo 'res2=';
      
        if($res2->code==0){
       $datas=array(
             'ka_status'=>1  
            );
            $datat['order_status']=2;
        $this->User_model->update_one(array('id'=>$kaka[0]['id']),$datas,'kk_card_info');
        $this->User_model->update_one(array('order_id'=>$red[0]['order_id']),$datat,'kk_order_info');
    
        echo $res2->msg;
        }else{
         $msg=$res2->msg;       
         echo $msg;            

        }
	}
  //卡卡通用户注册
  //卡卡通用户注册
   public function kaka_zc(){
    $data['user_name']=$this->input->post('username',true);
    $dataname=$data['user_name'];
    $data['password']=$this->input->post('password2',true);
    $data['regist_type']=8;
    $where=" user_name='".$dataname."'";
    $row=$this->User_model->get_select_one($select='*' , $where,'v_users');
   
    if ($row) {
    echo "<script> alert('注册失败');parent.location.href='http://api.etjourney.com/lst/register/taopiao2'; </script>"; 

    }else{
          $red=$this->User_model->user_insert('v_users',$data);
         $datas['id']=$red;
       //  $this->load->view("kaka_list/kaka_ym/id/$red");
         redirect(base_url("lst/taopiao2info/id/$red"));

 
 
     }


   }


   //卡卡通登录
public function kaka_dl(){


  $data['user_name']=$this->input->post('username',TRUE);
  $data['password']=$this->input->post('password',TRUE);
  $dataname=$data['user_name'];
  $datapswd=$data['password'];
   $where=" user_name='".$dataname."'   AND  password='".$datapswd."'";
  //echo $where; die;
   $temp=$this->User_model->get_select_one('*',$where,'v_users');
 $id=$temp['user_id'];
   if($temp){
     redirect(base_url("lst/taopiao2info/id/$id"));

   }else{
    echo "<script> alert('用户名或密码错误');parent.location.href='http://api.etjourney.com/lst/login/taopiao2'; </script>"; 
   }
}
//卡卡通页面
public function kaka_ym(){
   $a=$this->uri->segment(4);
  // var_dump($a);die;
  $data['id']=$a;
  $data['url']=base_url("kaka_list/kaka_dd/$a");
  $data['urls']=base_url("kaka_list/kaka_xd/id/$a");
 // echo "<pre>";
 // print_r($data);
 // echo "</pre>";

  $this->load->view('taopiao2/index',$data);
}
 //卡卡通订单页面
 public function kaka_dd($id){
  
  $data['id']=$id;

   $where ="user_id=$id";
   $is_off=$this->input->get('is_off',TRUE);

if($is_off==1){

  $where=" order_status=1 AND user_id=$id";
}else if($is_off==2){
$where =" order_status=0 AND user_id=$id";
}

$data['info']=$this->User_model->get_all($select='*',$where,'kk_order_info',$order_title='order_id');

//echo $this->db->last_query();
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
  //echo "<pre>";
 // print_r($_POST);
 // echo "</pre>";die;
 $data['user_id']=$id;
 $consignee=$this->input->post('username',TRUE);
 $data['money']=json_encode($this->input->post('money',TRUE));
 $data['consignee']=json_encode($this->input->post('username',TRUE));
 $data['card_id']=json_encode($this->input->post('account',TRUE));
 $data['mobile']=json_encode($this->input->post('tel'));
$sn=rand(100000,999999);
$order=date('Ymd',time());
$data['order_sn']=$order.$sn;
$i= count($consignee);


$data['z_money']=$data['money']*$i;
$data['kaka_sum']=$i;

echo "<pre>";
print_r($data);
echo "</pre>";die;
 //$time=time();
   $red=$this->db->insert_batch('kk_order_info',$data);
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
   //修改页面
    public function edit_index($id)
    {
        if(!$id)
        {
            return false;
        }
        $h5_info = $this->User_model->get_select_one('*',array('h5_id'=>$id),'v_h5_info');

        $data['h5_title']=$h5_info['h5_title'];
        $data['h5_id']=$h5_info['h5_id'];
        $data['sta_price']=$h5_info['sta_price'];
        $data['share_desc']=$h5_info['share_desc'];
        $data['video_src']=$h5_info['video_src'];
        $data['uploader']=$h5_info['uploader'];
        $data['url_type']=$h5_info['url_type'];
        $data['youlun_id']=$h5_info['youlun_id'];
          if($h5_info['date']!='')
        {
            $data['date_choose']=json_decode($h5_info['date'],TRUE);
            if(array_key_exists('destination', $data['date_choose'][0]))
            {
                 $data['is_destination']=1;
            }
        
        }


        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);


        $data['hotel_info']=$this->User_model->get_hotel_info($id);

        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);
        $data['shareimage']=$this->User_model->get_image(17,$id);

       //echo '<pre>';print_r($data);exit();

        $time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend= strtotime('+12 month', $time);
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

// 'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend))
        {
            $data['date']['cal'][] =array(
                'year'=>date('Y',strtotime('+1 month', $time)),
                'month'=>date('n',strtotime('+1 month', $time)),
                'month_cn'=>$this->common->get_month_cn(date('n',strtotime('+1 month', $time))),
                'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                'all_days'=>date('t',strtotime('+1 month', $time)),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
            );
            $time= strtotime('+1 month', $time);
        }

        $data['sub_url']=base_url('h5info/to_sub');

//echo '<pre>';
//print_r($date['choose']);
//echo "</pre>";
//exit;
        $this->load->view('h5/edit_index',$data);
    }

 


    //游轮修改页面
    public function youlun_inset($id)
    {
        if(!$id)
        {
            return false;
        }
        $h5_info = $this->User_model->get_select_one('*',array('h5_id'=>$id),'v_h5_info');   
        $data['h5_title']=$h5_info['h5_title'];
        $data['h5_id']=$h5_info['h5_id'];
        $data['sta_price']=$h5_info['sta_price'];
        $data['share_desc']=$h5_info['share_desc'];
        $data['video_src']=$h5_info['video_src'];
        $data['uploader']=$h5_info['uploader'];
        $data['url_type']=$h5_info['url_type'];
        $data['youlun_id']=$h5_info['youlun_id'];
        if($h5_info['date']!='')
        {
            $data['date_choose']=json_decode($h5_info['date'],TRUE);
        }


        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);


        $data['hotel_info']=$this->User_model->get_hotel_info($id);

        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);
        $data['shareimage']=$this->User_model->get_image(17,$id);

       //echo '<pre>';print_r($data);exit();

        $time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend= strtotime('+12 month', $time);
        $data['date']['cal'][]=array(
            'year'=>date('Y',$time),
            'month'=>date('n',$time),
            'month_cn'=>$this->common->get_month_cn(date('n',$time)),
            'week_first'=>date("w",mktime(0,0,0,date('n',$time),1,date('Y',$time))),
            'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',$time),1,date('Y',$time)))),
            'all_days'=>date('t',$time),
            'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        );

// 'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',$time),1,date('Y',$time))),
        while(date('Y',$time)<date('Y',$timeend) OR date('n',$time)<date('n',$timeend))
        {
            $data['date']['cal'][] =array(
                'year'=>date('Y',strtotime('+1 month', $time)),
                'month'=>date('n',strtotime('+1 month', $time)),
                'month_cn'=>$this->common->get_month_cn(date('n',strtotime('+1 month', $time))),
                'week_first'=>date("w",mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time)))),
                'week_fin'=>date("w",strtotime('+1 month -1 day', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))),
                'all_days'=>date('t',strtotime('+1 month', $time)),
                'time'=>strtotime('+1 month -1 day +23 hour', mktime(0,0,0,date('n',strtotime('+1 month', $time)),1,date('Y',strtotime('+1 month', $time))))
            );
            $time= strtotime('+1 month', $time);
        }

        $data['sub_url']=base_url('h5info_yl/to_sub');
          $data['youlun_url']=base_url('h5info_yl/to_youlun');
         $youlun=array();
         $youlun = $this->User_model->get_select_one('*',array('h5_id'=>$id,'is_show'=>1),'v_h5_youlun');
         $data['cabin']=  json_decode($youlun['cabin_info']);
         $data['home']=  json_decode($youlun['home_details']);
      //toecho '<pre>';print_r($data['cabin']);
        $this->load->view('h5/youlun_inset',$data);
    }

    //h5页面列表
    public function h5list()
    {
        $where="is_show=1";
        $data['info']=$this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
        // echo '<pre>';
        //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['edit_url']=base_url("h5info_temp/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show_temp/index_s/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info_temp/del_h5/$v[h5_id]");
            $data['info'][$k]['day_url']=base_url("h5info_trip_yl/trip_list/$v[h5_id]");
 
            //$data['info'][$k]['del_url']=base_url("h5info/fly_del?fly_id=$v[fly_id]");
        }
        $this->load->view('h5/h5list',$data);
    }
      public function h5list_s()
    {
        $where="is_show=1 AND type_id=3";
        $data['info']=$this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
        // echo '<pre>';
        //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['edit_url']=base_url("h5info_temp/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show_temp/index_s/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info_temp/del_h5/$v[h5_id]");
            $data['info'][$k]['day_url']=base_url("h5info_trip_yl/trip_list/$v[h5_id]");
             $data['info'][$k]['car_url']=base_url("h5info_trip_yl/car_temp/$v[h5_id]");
 
            //$data['info'][$k]['del_url']=base_url("h5info/fly_del?fly_id=$v[fly_id]");
        }
        $this->load->view('h5/h5list_temp',$data);
    }
    //自由行2
       public function h5list_s2()
    {
        $where="is_show=1 AND type_id=4";
        $data['info']=$this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
        // echo '<pre>';
        //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['edit_url']=base_url("h5info_temp/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show_temp/zyx2/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info_temp/del_h5/$v[h5_id]");
            $data['info'][$k]['day_url']=base_url("h5info_trip_yl/trip_list/$v[h5_id]");
             $data['info'][$k]['car_url']=base_url("h5info_trip_yl/car_temp/$v[h5_id]");
              $data['info'][$k]['hotel_url']=base_url("h5info_trip_yl/hotel_temp/$v[h5_id]");
               $data['info'][$k]['taocan_url']=base_url("h5info_trip_yl/taocan_temp/$v[h5_id]");
                $data['info'][$k]['hangban_url']=base_url("h5info_trip_yl/hangban_temp/$v[h5_id]");
 
            //$data['info'][$k]['del_url']=base_url("h5info/fly_del?fly_id=$v[fly_id]");
        }
        $this->load->view('h5/h5list_temp_2',$data);
    }

    //删除
    public function del_h5($id)
    {
        $this->User_model->update_one(array('h5_id'=>$id),array('is_show'=>'2'),$table='v_h5_info');
      return  redirect($_SERVER['HTTP_REFERER']);
    }

    /*主数据插入*/
    public function to_insert()
    {
        set_time_limit(0);
        $data['h5_title']=$this->input->post('h5_title',TRUE);
        $data['uploader']=$this->input->post('uploader',TRUE);
        $data['share_desc']=$this->input->post('share_desc',TRUE);
        $data['sta_price']=$this->input->post('sta_price',TRUE);
        $data['url_type']=$this->input->post('url_type',TRUE);
        $video_src=$this->input->post('video_src',TRUE);
        if($video_src)
        {
            $data['video_src']=$video_src;
        }


        //日期信息
        $date_val=$this->input->post('date_val',TRUE);
        $date_price=$this->input->post('date_price',TRUE);



        //购买须知
        $know_title_cn=$this->input->post('know_title_cn',TRUE);
        $know_title_en=$this->input->post('know_title_en',TRUE);
        $know_content_bf=$this->input->post('know_content_bf',TRUE);
        $know_content_text=$this->input->post('know_content_text',FALSE);
        if(count($know_title_cn)>0)
        {
            foreach($know_title_cn as $k=>$v)
            {
                $data['sign_to_know'][$k]=array('cn'=>$v,'en'=>$know_title_en[$k],'bf'=>$know_content_bf[$k],'info'=>explode('**',$know_content_text[$k]));
            }
            $data['sign_to_know']=json_encode($data['sign_to_know']);
        }




        $hotel_easy_title=$this->input->post('hotel_easy_title',TRUE);
        $hotel_detail_title=$this->input->post('hotel_detail_title',TRUE);
        $hotel_desc=$this->input->post('hotel_desc',TRUE);
        $hotel_introduce=$this->input->post('hotel_introduce',TRUE);
        $hotel_tips=$this->input->post('hotel_tips',TRUE);

       // $data['hotel_info']=json_encode($hotel_info);
        if(count($date_val)>0)
        {
            $new_date=[];
            foreach($date_val as $k=>$v)
            {
                $new_date[$v]=$date_price[$k];
            }
            $data['date']=json_encode($new_date);
        }

       // print_r($data);exit();

        $h5_id=$this->User_model->user_insert('v_h5_info',$data);
        if(isset($_FILES['image1']) && isset($_FILES['image2']) && isset($_FILES['image3']) && $_FILES['image1']['error']==0 && $_FILES['image2']['error']==0 &&$_FILES['image3']['error']==0)
        {
            $head_image=$this->upload_image('image1','H5image');
            $index_image=$this->upload_image('image2','H5image');
            $share_image=$this->upload_image('image3','H5image');

            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>11,'url'=>$index_image));
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>17,'url'=>$share_image));
        }


        $new_file=[];
        foreach($_FILES as $k=>$v)
        {
            if(stristr($k,'hotel_img'))
            {
                $new_file[]=$_FILES[$k];
            }
        }
        $new_file= array_values($new_file);
        foreach($new_file as $k=>$v)
        {
            $nwe_file_temp['hotel_img'.($k+1)]=$v;
        }

        foreach($hotel_easy_title as $k=>$v)
        {
            if($v=='')
            {
                continue;
            }
            $hotel_info=array('hotel_easy_title'=>$v,
                'hotel_detail_title'=>$hotel_detail_title[$k],
                'hotel_desc'=>$hotel_desc[$k],
                'hotel_introduce'=>json_encode(explode('**',$hotel_introduce[$k])),
                'hotel_tips'=>$hotel_tips[$k],
                'h5_id'=>$h5_id
            );

            $hotel_id=$this->User_model->user_insert('v_h5_hotel',$hotel_info);
            $file_name='hotel_img'.($k+1);

        //    $new_url=$this->more_pic_upload($file_name);
            $new_url=$this->new_more_upload($nwe_file_temp[$file_name]);
            foreach($new_url as $k1=>$v1)
            {
                $this->User_model->user_insert('v_images',array('link_id'=>$hotel_id,'type'=>12,'url'=>$v1));
            }

        }

        redirect(base_url("h5info/h5list"));

    }

    //主页面数据修改
    public function to_sub()
    {

        set_time_limit(0);
        $h5_id=$this->input->post('h5_id',TRUE);
      // echo '<pre>';print_r($_POST);exit();


        $data['h5_title']=$this->input->post('h5_title',TRUE);
        $data['share_desc']=$this->input->post('share_desc',TRUE);
        $data['sta_price']=$this->input->post('sta_price',TRUE);
        $data['url_type']=$this->input->post('url_type',TRUE);
        $video_src=$this->input->post('video_src',TRUE);
        if($video_src)
        {
            $data['video_src']=$video_src;
        }

        //日期信息
        $date_val=$this->input->post('date_val',TRUE);
        $date_price=$this->input->post('date_price',TRUE);



        //购买须知
        $know_title_cn=$this->input->post('know_title_cn',TRUE);
        $know_title_en=$this->input->post('know_title_en',TRUE);
        $know_content_bf=$this->input->post('know_content_bf',TRUE);
        $know_content_text=$this->input->post('know_content_text',FALSE);
        if(count($know_title_cn)>0)
        {
            foreach($know_title_cn as $k=>$v)
            {
                $data['sign_to_know'][$k]=array('cn'=>$v,'en'=>$know_title_en[$k],'bf'=>$know_content_bf[$k],'info'=>explode('**',$know_content_text[$k]));
            }
            $data['sign_to_know']=json_encode($data['sign_to_know']);
        }


        $hotel_easy_title=$this->input->post('hotel_easy_title',TRUE);
        $hotel_detail_title=$this->input->post('hotel_detail_title',TRUE);
        $hotel_desc=$this->input->post('hotel_desc',TRUE);
        $hotel_introduce=$this->input->post('hotel_introduce',TRUE);
        $hotel_tips=$this->input->post('hotel_tips',TRUE);


        // $data['hotel_info']=json_encode($hotel_info);
        if(count($date_val)>0)
        {
            $new_date=[];
            foreach($date_val as $k=>$v)
            {
                $new_date[$v]=$date_price[$k];
            }
            $data['date']=json_encode($new_date);
        }

        $this->User_model->update_one(array('h5_id'=>$h5_id),$data,'v_h5_info');

        if(isset($_FILES['image1']) && $_FILES['image1']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>10),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image1','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        if(isset($_FILES['image2']) && $_FILES['image2']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>11),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image2','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>11,'url'=>$head_image));
        }
        if(isset($_FILES['image3']) && $_FILES['image3']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>17),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image3','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>17,'url'=>$head_image));
        }


        $del_image_id=$this->input->post('del_image_id',TRUE);
        $del_hotel_id=$this->input->post('hotel_id',TRUE);
        if($del_image_id)
        {
            $del_image_id=implode(',',array_filter(explode(',',$del_image_id)));

            $this->User_model->update_one("id IN ($del_image_id)",array('isdelete'=>1),'v_images');
        }
        if($del_hotel_id)
        {
            $del_hotel_id=implode(',',array_filter(explode(',',$del_hotel_id)));
            $this->User_model->update_one("id IN ($del_hotel_id)",array('is_show'=>2),'v_h5_hotel');
        }
        //$image_all= $this->User_model->get_image($type,$link_id,$num=0)
        $id_arr=[];
        $temp_arr=$this->User_model->get_select_more('*',array('h5_id'=>$h5_id,'is_show'=>1),0,20,'id','ASC','v_h5_hotel');
        foreach($temp_arr as $k=>$v)
        {
            $id_arr[]=$v['id'];
        }
       //echo '<pre>';print_r($temp_arr);
        $new_file=[];
        foreach($_FILES as $k=>$v)
        {
            if(stristr($k,'hotel_img'))
            {
                $new_file[]=$_FILES[$k];
            }
        }
        $new_file= array_values($new_file);
        foreach($new_file as $k=>$v)
        {
            $nwe_file_temp['hotel_img'.($k+1)]=$v;
        }
      //  $this->User_model->update_one(array('h5_id'=>$h5_id),array('is_show'=>'2'),'v_h5_hotel');
        foreach($hotel_easy_title as $k=>$v)
        {
            if($v=='')
            {
                continue;
            }
            $hotel_info=array('hotel_easy_title'=>$v,
                'hotel_detail_title'=>$hotel_detail_title[$k],
                'hotel_desc'=>$hotel_desc[$k],
                'hotel_introduce'=>json_encode(explode('**',$hotel_introduce[$k])),
                'hotel_tips'=>$hotel_tips[$k],
                'h5_id'=>$h5_id,

            );

            if(isset($id_arr[$k]))
            {
                $hotel_id=$id_arr[$k];
                $this->User_model->update_one(array('id'=>$id_arr[$k]),$hotel_info,'v_h5_hotel');
            }else{
                $hotel_id=$this->User_model->user_insert('v_h5_hotel',$hotel_info);
            }

            $file_name='hotel_img'.($k+1);
            $new_url=$this->new_more_upload($nwe_file_temp[$file_name]);
            foreach($new_url as $k1=>$v1)
            {
                $this->User_model->user_insert('v_images',array('link_id'=>$hotel_id,'type'=>12,'url'=>$v1));
            }

        }
        redirect(base_url("h5info/edit_index/$h5_id"));

    }
    //游轮信息修改
    public function to_youlun()
    {

        set_time_limit(0);
        $h5_id=$this->input->post('h5_id',TRUE);
      // echo '<pre>';print_r($_POST);exit();


        $data['h5_title']=$this->input->post('h5_title',TRUE);
        $data['share_desc']=$this->input->post('share_desc',TRUE);
        $data['sta_price']=$this->input->post('sta_price',TRUE);
        $data['url_type']=$this->input->post('url_type',TRUE);
        $video_src=$this->input->post('video_src',TRUE);
        if($video_src)
        {
            $data['video_src']=$video_src;
        }

        //日期信息
        $date_val=$this->input->post('date_val',TRUE);
        $date_price=$this->input->post('date_price',TRUE);



        //购买须知
        $know_title_cn=$this->input->post('know_title_cn',TRUE);
        $know_title_en=$this->input->post('know_title_en',TRUE);
        $know_content_bf=$this->input->post('know_content_bf',TRUE);
        $know_content_text=$this->input->post('know_content_text',FALSE);
        if(count($know_title_cn)>0)
        {
            foreach($know_title_cn as $k=>$v)
            {
                $data['sign_to_know'][$k]=array('cn'=>$v,'en'=>$know_title_en[$k],'bf'=>$know_content_bf[$k],'info'=>explode('**',$know_content_text[$k]));
            }
            $data['sign_to_know']=json_encode($data['sign_to_know']);
        }


        $hotel_easy_title=$this->input->post('hotel_easy_title',TRUE);
        $hotel_detail_title=$this->input->post('hotel_detail_title',TRUE);
        $hotel_desc=$this->input->post('hotel_desc',TRUE);
        $hotel_introduce=$this->input->post('hotel_introduce',TRUE);
        $hotel_tips=$this->input->post('hotel_tips',TRUE);


        // $data['hotel_info']=json_encode($hotel_info);
        if(count($date_val)>0)
        {
            $new_date=[];
            foreach($date_val as $k=>$v)
            {
                $new_date[$v]=$date_price[$k];
            }
            $data['date']=json_encode($new_date);
        }

        $this->User_model->update_one(array('h5_id'=>$h5_id),$data,'v_h5_info');

        if(isset($_FILES['image1']) && $_FILES['image1']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>10),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image1','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        if(isset($_FILES['image2']) && $_FILES['image2']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>11),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image2','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>11,'url'=>$head_image));
        }
        if(isset($_FILES['image3']) && $_FILES['image3']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>17),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image3','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>17,'url'=>$head_image));
        }


        $del_image_id=$this->input->post('del_image_id',TRUE);
        $del_hotel_id=$this->input->post('hotel_id',TRUE);
        if($del_image_id)
        {
            $del_image_id=explode(',',$del_image_id);

      //      $this->User_model->update_one("id IN ($del_image_id)",array('isdelete'=>1),'v_images');
        }   
        if($del_hotel_id)
        {
            $del_hotel_id=implode(',',array_filter(explode(',',$del_hotel_id)));
            $this->User_model->update_one("id IN ($del_hotel_id)",array('is_show'=>2),'v_h5_hotel');
        }
        //$image_all= $this->User_model->get_image($type,$link_id,$num=0)
        $id_arr=[];
        $temp_arr=$this->User_model->get_select_more('*',array('h5_id'=>$h5_id,'is_show'=>1),0,20,'id','ASC','v_h5_hotel');
        foreach($temp_arr as $k=>$v)
        {
            $id_arr[]=$v['id'];
        }
       //echo '<pre>';print_r($temp_arr);
        $new_file=[];
        foreach($_FILES as $k=>$v)
        {
            if(stristr($k,'hotel_img'))
            {
                $new_file[]=$_FILES[$k];
            }
        }
        $new_file= array_values($new_file);
        foreach($new_file as $k=>$v)
        {
            $nwe_file_temp['hotel_img'.($k+1)]=$v;
        }
      //  $this->User_model->update_one(array('h5_id'=>$h5_id),array('is_show'=>'2'),'v_h5_hotel');
    $hotel_easy_title=$this->input->post('hotel_easy_title',TRUE);
        foreach($hotel_easy_title as $k=>$v)
        {
            if($v=='')
            {
                continue;
            }
            $hotel_info=array('hotel_easy_title'=>$v,
                'hotel_detail_title'=>$hotel_detail_title[$k],
                'hotel_desc'=>$hotel_desc[$k],
                'hotel_introduce'=>json_encode(explode('**',$hotel_introduce[$k])),
                'hotel_tips'=>$hotel_tips[$k],
                'h5_id'=>$h5_id
            );
        }
       $view_info = array();
         
         //echo "<pre>";
           $youlun_easy_title=$this->input->post('youlun_easy_title',TRUE);
           $where = ' h5_id='.$h5_id;
          $res = $this->User_model->get_select_one($select='*',$where,'v_h5_youlun');
        $cabin_info = json_decode($res['cabin_info']);
         $home_details = json_decode($res['home_details']);
       // print_r($cabin_info );
        if(count($hotel_easy_title)>=1)
        {
            for($i=0;$i<=count($hotel_easy_title);$i++)
            {
                $view_file_name='hotel_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                $hotel_img = $this->merge_image($cabin_info[$i]->img->view_image,$del_image_id,$new_url);
           //   var_dump($hotel_img);
      

                $view_info[] = array(
                            //    'view_name' => $view_name[$i],
                        //        'view_intro' => str_replace('**','<br>',$view_intro[$i]),
                                'view_image' => $hotel_img
                );
            }
            
         //print_r($view_info);die;
        }
        //print_r($del_image_id);
        //print_r($view_info);die;
           $view_infos = array();
         
        if(count($youlun_easy_title)>=1)
        {
            for($i=0;$i<count($youlun_easy_title);$i++)
            {
                $view_file_name='hotel_imges'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
               
            
          $view_image = $this->merge_image($home_details->youlun_img[$i]->view_image,$del_image_id,$new_url);
//                 print_r($view_image);
//               exit;
                $view_infos[] = array(
                        //        'view_name' => $view_name[$i],
                         //       'view_intro' => str_replace('**','<br>',$view_intro[$i]),
                                'view_image' => $view_image
                );
            }
        }
       
            $names=array();
        $cabin=array();
        $names['cc']=$this->input->post('hotel_easy_title',TRUE); 
        $hotel_easy_title =$this->input->post('hotel_easy_title',TRUE);   
        $hotel_detail_title =$this->input->post('hotel_detail_title',TRUE);  
        $hotel_desc =$this->input->post('hotel_desc',TRUE);  
        $hotel_introduce =$this->input->post('hotel_introduce',TRUE);  
        $hotel_tips =$this->input->post('hotel_tips',TRUE);  
        $taocan_name =$this->input->post('taocan_name',TRUE);  
        $taocan_sum =$this->input->post('taocan_sum',TRUE);  
        $taocan_gg =$this->input->post('taocan_gg',TRUE);  
        $taocan_money=$this->input->post('taocan_money',TRUE); 
        $img =$view_info;         
           for($i=0;$i<=count($names['cc']);$i++){
               $cabin[$i] = array(
                                'hotel_easy_title' =>  $hotel_easy_title[$i],
                                'hotel_detail_title' =>$hotel_detail_title[$i],
                                'hotel_desc' => $hotel_desc[$i],
                                'hotel_introduce' => $hotel_introduce[$i],
                                 'hotel_tips' => $hotel_tips[$i],
                                'taocan_name' => $taocan_name[$i],
                                 'taocan_sum' => $taocan_sum[$i],
                                'taocan_gg' => $taocan_gg[$i],
                                'taocan_money' => $taocan_money[$i],
                                'img'=>$img[$i]
                    
                );     
                        
                  }
        $home_details=array();           
    
//        $cabin['hotel_easy_title'] =$this->input->post('hotel_easy_title',TRUE);   
//        $cabin['hotel_detail_title'] =$this->input->post('hotel_detail_title',TRUE);  
//        $cabin['hotel_desc'] =$this->input->post('hotel_desc',TRUE);  
//        $cabin['hotel_introduce'] =$this->input->post('hotel_introduce',TRUE);  
//        $cabin['hotel_tips'] =$this->input->post('hotel_tips',TRUE);  
//        $cabin['img'] =$view_info;  
        $home_details['youlun_easy_title'] =$this->input->post('youlun_easy_title',TRUE);   
        $home_details['youlun_detail_title'] =$this->input->post('youlun_detail_title',TRUE);  
        $home_details['youlun_desc'] =$this->input->post('youlun_desc',TRUE);  
        $home_details['youlun_introduce'] =$this->input->post('youlun_introduce',TRUE);  
        $home_details['youlun_tips'] =$this->input->post('youlun_tips',TRUE);       
        $home_details['youlun_img'] =$view_infos;   
        $cabin_xinxi=json_encode($cabin);
        $home_xinxi=  json_encode($home_details);
        $updatas=array();
        $updatas['cabin_info']=$cabin_xinxi;
        $updatas['home_details']=$home_xinxi;
         $this->User_model->update_one(array('h5_id'=>$h5_id),$updatas,'v_h5_youlun');
//  echo $this->db->last_query();
//       echo "<pre>";
//  print_r($cabin);
//     echo "</pre>";
//   echo "<pre>";
//    print_r($cabin);
//   echo "</pre>";


            if(isset($id_arr[$k]))
            {
                $hotel_id=$id_arr[$k];
                $this->User_model->update_one(array('id'=>$id_arr[$k]),$hotel_info,'v_h5_hotel');
          //     echo $this->db->last_query();  



            }else{
                $hotel_id=$this->User_model->user_insert('v_h5_hotel',$hotel_info);
                
 //echo $this->db->last_query();
           }

           $file_name='hotel_img'.($k+1);
          $new_url=$this->new_more_upload($nwe_file_temp[$file_name]);
           foreach($new_url as $k1=>$v1)
          {
              $this->User_model->user_insert('v_images',array('link_id'=>$hotel_id,'type'=>12,'url'=>$v1));
       // echo $this->db->last_query();
    
              }

     
        redirect(base_url("h5info_yl/youlun_inset/$h5_id"));
    }
//不确定图片上传


    public function new_more_upload($file)
    {
        foreach($file['error'] as $k=>$v)
        {
            if($v==0)
            {
                switch ($file['type'][$k])
                {
                    case 'image/jpeg':
                        $br = '.jpg';break;
                    case 'image/png':
                        $br = '.png';break;
                    case 'image/gif':
                        $br = '.gif';break;
                    default:
                        $br = FALSE;
                }

                if($br)
                {

                    $key =md5(rand(1,99999).time());


                    $pic_url="./public/images/H5image/".$key.$br;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[]="/public/images/H5image/".$key.$br;

                }
            }
        }
        return $new_url;
    }

    public function more_pic_upload($filename)
    {
        $file = $_FILES[$filename];
        $new_url=[];
        foreach($file['error'] as $k=>$v)
        {
            if($v==0)
            {
                switch ($file['type'][$k])
                {
                    case 'image/jpeg':
                        $br = '.jpg';break;
                    case 'image/png':
                        $br = '.png';break;
                    case 'image/gif':
                        $br = '.gif';break;
                    default:
                        $br = FALSE;
                }

                if($br)
                {

                        $key =md5(rand(1,99999).time());


                    $pic_url="./public/images/H5image/".$key.$br;
                    move_uploaded_file($file['tmp_name'][$k], $pic_url);
                    $new_url[]="/public/images/H5image/".$key.$br;

                }
            }
        }
        return $new_url;

    }

    public function upload_image($filename,$fileurl,$key='time')
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl))
            {
                return FALSE;
            }
        }

        return $this->shangchuan($filename,$fileurl,$key);
    }

    public function shangchuan($filename,$fileurl,$key='time')
    {
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
                $key =md5(rand(1,99999).time());
            }

            $pic_url="./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            $new_url="/public/images/".$fileurl."/".$key.$br;
            return $new_url;
        }
    }



    //航班



    //后台列表
    public function fly_list($page=1)
    {   
      
        $where='1=1';
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $is_off= $data['is_off']= $this->input->get('is_off',true);
   
     if(!$is_off){
            $is_off=0;
        }
        $wheres=' 1=1 ';

     $where.=' AND type>0';
        if($data['time1']){
            $wheres.=' AND add_time >=$data["time1"]';
        }
        if($data['time2']){
            $data['time2']+=86400;
            $wheres.='  AND add_time <=$data["time2"]';
        }

        if($data['title'])
        {
           
        }else{
            $data['type']=0;
        }
        if($is_off==0){
            $wheres.="  AND is_off= $is_off";
        }elseif($is_off==1){
            $wheres.="  AND is_off > 0";
        }


        $page_num =14;
        $data['now_page'] = $page;
        $count = $this->User_model->get_counts($where,'v_h5_fly');
       // $count = $this->db->count_all('v_h5_fly');
       // echo $count;
        $data['max_page'] = ceil($count/$page_num);
       // print_r($data['max_page']);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

      //  $data['info']=$this->Goodsforcar_model->get_goods_lists($where,'v_wx_users',$start,$page_num);
        $data['info']=$this->User_model->get_goods_lists($where,'v_h5_fly',$order_title='fly_id',$order='ASC',$start,$page_num);
      
        print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['url']=base_url("h5info/fly_temp?fly_id=$v[fly_id]");
            $data['info'][$k]['del_url']=base_url("h5info/fly_del?fly_id=$v[fly_id]");
        }
        $this->load->view('h5/temp_fly_list',$data);
    }
    //航班删除
    public function fly_del()
    {
        $data['fly_id']=$this->input->get('fly_id',TRUE);
        $this->User_model->update_one(array('fly_id'=>$data['fly_id']),array('type'=>0),'v_h5_fly');

        redirect(base_url('H5info/fly_list'));
    }

    //接口数据
    public function get_flyinfo_ajax()
    {
        $title=$this->input->post_get('title',TRUE);
        $start_place=$this->input->post_get('start_place',TRUE);
        $where="temp_title LIKE '%$title%'  AND fly_start_place  LIKE '%$start_place%' AND type>0";
        $data=$this->User_model->get_select_all($select='*',$where,$order_title='temp_title',$order='ASC',$table='v_h5_fly');
        foreach($data as $k=>$v)
        {
            $data[$k]['time_start']=date('H:i:s',$v['fly_start_time']);
            $data[$k]['date_start']=date('Y-m-d',$v['fly_start_time']);
            $data[$k]['time_end']=date('H:i:s',$v['fly_end_time']);
            $data[$k]['date_end']=date('Y-m-d',$v['fly_end_time']);
            $data[$k]['last_time']=$this->return_hm($data[$k]['fly_end_time']-$data[$k]['fly_start_time']);
           // $data[$k]['last_time_detail']=$data[$k]['fly_end_time']-$data[$k]['fly_start_time'];
           // $data[$k]['last_time_detail']=$data[$k]['fly_end_time']-$data[$k]['fly_start_time'];
            unset( $data[$k]['fly_start_time']);
            unset( $data[$k]['fly_end_time']);
            unset( $data[$k]['day_id']);
            $temp=$this->User_model->get_select_one('*',array('pid'=>$v['fly_id']),'v_h5_fly');
            if($temp)
            {
                $temp['time_start']=date('H:i:s',$temp['fly_start_time']);
                $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                $temp['time_end']=date('H:i:s',$temp['fly_end_time']);
                $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                unset( $temp['fly_start_time']);
                unset( $temp['fly_end_time']);
                unset( $temp['day_id']);
                $data[]=$temp;

                $temp=$this->User_model->get_select_one('*',array('pid'=>$temp['fly_id']),'v_h5_fly');
                if($temp)
                {
                    $temp['time_start']=date('H:i:s',$temp['fly_start_time']);
                    $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                    $temp['time_end']=date('H:i:s',$temp['fly_end_time']);
                    $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                    $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                    unset( $temp['fly_start_time']);
                    unset( $temp['fly_end_time']);
                    unset( $temp['day_id']);
                    $data[]=$temp;

                    $temp=$this->User_model->get_select_one('*',array('pid'=>$temp['fly_id']),'v_h5_fly');
                    if($temp)
                    {
                        $temp['time_start']=date('H:i:s',$temp['fly_start_time']);
                        $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                        $temp['time_end']=date('H:i:s',$temp['fly_end_time']);
                        $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                        $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                        unset( $temp['fly_start_time']);
                        unset( $temp['fly_end_time']);
                        unset( $temp['day_id']);
                        $data[]=$temp;
                    }
                }

            }
        }
        //echo '<pre>';print_r($data);exit();
        echo json_encode($data);
    }

    //接口数据_修改版
    public function get_newflyinfo_ajax()
    {
        $title=$this->input->post_get('title',TRUE);
        $start_place=$this->input->post_get('start_place',TRUE);
        $date_start=$this->input->post_get('date_start',TRUE);
        $where="temp_title LIKE '%$title%'  AND fly_start_place  LIKE '%$start_place%' AND type>0";
        if($date_start)
        {
            $start_time = strtotime($date_start);
            $end_time = $start_time + 86400;
            $where .= " AND fly_start_time<$end_time AND fly_start_time>=$start_time AND pid=0";
        }
        $data=$this->User_model->get_select_all($select='*',$where,$order_title='temp_title',$order='ASC',$table='v_h5_fly');
        $result = array();
        $pid_list = array();
        if($data)
        {
            foreach($data as $k=>$v)
            {
                $tmp_data=[];
                if(in_array($v['fly_id'], $pid_list))
                {
                    continue;
                }
                $data[$k]['time_start']=date('H:i',$v['fly_start_time']);
                $data[$k]['date_start']=date('Y-m-d',$v['fly_start_time']);
                $data[$k]['time_end']=date('H:i',$v['fly_end_time']);
                $data[$k]['date_end']=date('Y-m-d',$v['fly_end_time']);
                $data[$k]['last_time']=$this->return_hm($data[$k]['fly_end_time']-$data[$k]['fly_start_time']);
               // $data[$k]['last_time_detail']=$data[$k]['fly_end_time']-$data[$k]['fly_start_time'];
               // $data[$k]['last_time_detail']=$data[$k]['fly_end_time']-$data[$k]['fly_start_time'];
                unset( $data[$k]['fly_start_time']);
                unset( $data[$k]['fly_end_time']);
                unset( $data[$k]['day_id']);
                $tmp_data[]=$data[$k];
                $temp=$this->User_model->get_select_one('*',array('pid'=>$v['fly_id']),'v_h5_fly');
                if($temp)
                {
                    $temp['time_start']=date('H:i',$temp['fly_start_time']);
                    $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                    $temp['time_end']=date('H:i',$temp['fly_end_time']);
                    $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                    $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                    unset( $temp['fly_start_time']);
                    unset( $temp['fly_end_time']);
                    unset( $temp['day_id']);
                    $tmp_data[]=$temp;
                    $pid_list[]= $temp['fly_id'];
    
                    $temp=$this->User_model->get_select_one('*',array('pid'=>$temp['fly_id']),'v_h5_fly');
                    if($temp)
                    {
                        $temp['time_start']=date('H:i',$temp['fly_start_time']);
                        $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                        $temp['time_end']=date('H:i',$temp['fly_end_time']);
                        $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                        $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                        unset( $temp['fly_start_time']);
                        unset( $temp['fly_end_time']);
                        unset( $temp['day_id']);
                        $tmp_data[]=$temp;
                        $pid_list[]= $temp['fly_id'];
    
                        $temp=$this->User_model->get_select_one('*',array('pid'=>$temp['fly_id']),'v_h5_fly');
                        if($temp)
                        {
                            $temp['time_start']=date('H:i',$temp['fly_start_time']);
                            $temp['date_start']=date('Y-m-d',$temp['fly_start_time']);
                            $temp['time_end']=date('H:i',$temp['fly_end_time']);
                            $temp['date_end']=date('Y-m-d',$temp['fly_end_time']);
                            $temp['last_time']=$this->return_hm($temp['fly_end_time']-$temp['fly_start_time']);
                            unset( $temp['fly_start_time']);
                            unset( $temp['fly_end_time']);
                            unset( $temp['day_id']);
                            $tmp_data[]=$temp;
                            $pid_list[]= $temp['fly_id'];
                        }
                    }
                }
                $result[] = $tmp_data;
            }
        }
        else
        {
            $result = false;
        }
        //echo '<pre>';print_r($data);exit();
        echo json_encode($result);
    }


    public function fly_temp()
    {
       $data['fly_id']=$this->input->get('fly_id',TRUE);
        if($data['fly_id'])
        {
            $data=$this->User_model->get_select_one('*',array('fly_id'=>$data['fly_id']),'v_h5_fly');
            $data['fly_start_time']=$this->return_datetime_time($data['fly_start_time']);
            $data['fly_end_time']=$this->return_datetime_time($data['fly_end_time']);
            $data['sub']=base_url('H5info/fly_temp_sub');
            $this->load->view('h5/fly_temp_edit',$data);
        }
        else
        {
            $data['fly_id']=0;
            $data['sub']=base_url('H5info/fly_temp_insert');
            $this->load->view('h5/fly_temp',$data);
        }


    }


    public function fly_temp_insert()
    {
       //echo '<pre>';print_r($_POST);
        $is_z=$this->input->post('is_z',TRUE);
        $data['temp_title']=$this->input->post('temp_title',TRUE);
        $data['fly_price']=$this->input->post('fly_price',TRUE);
        $data['commont']=$this->input->post('commont',TRUE);

        $fly_sn=$this->input->post('fly_sn',TRUE);
        $fly_name=$this->input->post('fly_name',TRUE);
        $place=$this->input->post('place',TRUE);
        $time=$this->input->post('time',TRUE);

        if(!$is_z)
        {
             //无中转 go
            $data['fly_sn']=$fly_sn[0];
            $data['type']=1;
            $data['fly_name']=$fly_name[0];
            $data['fly_start_place']=$place[0];
            $data['fly_end_place']=$place[2];
            $data['fly_start_time']=$this->return_unix_time($time[0]);
            $data['fly_end_time']=$this->return_unix_time($time[1]);
            $pid=$this->User_model->user_insert('v_h5_fly',$data);

            //无中转 back
            $data['fly_sn']=$fly_sn[2];
            $data['pid']=$pid;
            $data['type']=2;
            $data['fly_name']=$fly_name[2];
            $data['fly_start_place']=$place[2];
            $data['fly_end_place']=$place[1];
            $data['fly_start_time']=$this->return_unix_time($time[4]);
            $data['fly_end_time']=$this->return_unix_time($time[5]);
            $this->User_model->user_insert('v_h5_fly',$data);
            redirect(base_url('H5info/fly_list'));

        }
        else
        {
            // go中转
            $data['fly_sn']=$fly_sn[0];
            $data['type']=1;
            $data['fly_name']=$fly_name[0];
            $data['fly_start_place']=$place[0];
            $data['fly_end_place']=$place[1];
            $data['fly_start_time']=$this->return_unix_time($time[0]);
            $data['fly_end_time']=$this->return_unix_time($time[1]);
            $data['is_z']=1;
            $pid=$this->User_model->user_insert('v_h5_fly',$data);


            //中转go
            $data['fly_sn']=$fly_sn[1];
            $data['pid']=$pid;
            $data['type']=1;
            $data['fly_name']=$fly_name[1];
            $data['fly_start_place']=$place[1];
            $data['fly_end_place']=$place[2];
            $data['fly_start_time']=$this->return_unix_time($time[2]);
            $data['fly_end_time']=$this->return_unix_time($time[3]);
            $data['is_z']=1;
            $pid=$this->User_model->user_insert('v_h5_fly',$data);


            //back中转
            $data['fly_sn']=$fly_sn[2];
            $data['pid']=$pid;
            $data['type']=2;
            $data['fly_name']=$fly_name[2];
            $data['fly_start_place']=$place[2];
            $data['fly_end_place']=$place[1];
            $data['fly_start_time']=$this->return_unix_time($time[4]);
            $data['fly_end_time']=$this->return_unix_time($time[5]);
            $data['is_z']=1;
            $pid=$this->User_model->user_insert('v_h5_fly',$data);


            $data['fly_sn']=$fly_sn[3];
            $data['pid']=$pid;
            $data['type']=2;
            $data['fly_name']=$fly_name[3];
            $data['fly_start_place']=$place[1];
            $data['fly_end_place']=$place[0];
            $data['fly_start_time']=$this->return_unix_time($time[6]);
            $data['fly_end_time']=$this->return_unix_time($time[7]);
            $pid=$this->User_model->user_insert('v_h5_fly',$data);
            $data['is_z']=1;
            redirect(base_url('H5info/fly_list'));
            //中转back
        }

    }

    public function fly_temp_sub()
    {
        //echo '<pre>';print_r($_POST);

        $data['fly_id']=$this->input->post('fly_id',TRUE);

        $data['temp_title']=$this->input->post('temp_title',TRUE);
        $data['fly_price']=$this->input->post('fly_price',TRUE);
        $data['commont']=$this->input->post('commont',TRUE);

        $data['fly_sn']=$this->input->post('fly_sn',TRUE);
        $data['fly_name']=$this->input->post('fly_name',TRUE);
        $data['fly_start_place']=$this->input->post('fly_start_place',TRUE);
        $data['fly_end_place']=$this->input->post('fly_end_place',TRUE);
        $data['fly_start_time']=$this->return_unix_time($this->input->post('fly_start_time',TRUE));
        $data['fly_end_time']=$this->return_unix_time($this->input->post('fly_end_time',TRUE));
        $this->User_model->update_one(array('fly_id'=>$data['fly_id']),$data,'v_h5_fly');
        return redirect(base_url('H5info/fly_list'));

    }

    //返回 datetime-local 的 unix时间

    public function return_unix_time($datetime_local='2017-05-04T15:35')
    {

        return strtotime(substr($datetime_local,0,10).''.substr($datetime_local,11));
    }

    //返回 unix时间 的 datetime-local

    public function return_datetime_time($time='1493861700')
    {

        $time= date('Y-m-d H:i:s',$time);
        return substr($time,0,10).'T'.substr($time,11);
      //  return strtotime(substr($datetime_local,0,10).''.substr($datetime_local,11));
    }


    //输入unix 返回 1h5m

    public function return_hm($time='21600')
    {

        $h_num=intval($time/3600);
        $m_num=intval(($time-$h_num*3600)/60);
        return $h_num.'小时'.$m_num.'分';
    }
    
    //入库图片地址编辑
    function merge_image($old_image,$del_image,$new_image)
    {
        $result = $res = array();
        if($old_image && $del_image)
        {
            foreach ($old_image as $key => $value) {
               if(!in_array($value, $del_image))
               {
                    $res[] = $value;
               }
            }
        }
        else
        {
            $res = !empty($old_image) ? $old_image : array();
        }
        $result = array_merge($res,$new_image);
        return  $result;
    }
}