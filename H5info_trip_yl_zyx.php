<?php
/**
 * H5页面模板--上传行程
 * Date: 2017/4/20
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class H5info_trip_yl_zyx extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
          $this->load->library('common');
        $this->load->helper('url');
       // $this->auth();
    }


    /**
     * 权限验证
     */
    protected function auth()
    {
        if(!isset($_SESSION['admin_id']))
        {
            echo "请登录";
            redirect(base_url("newadmin/login"));die;
        }
    }
    /**
     * H5页面模板--新增行程
     */
    public function index($h5_id=0,$day_order=0)
    {
        if(!$h5_id || !$day_order)
        {
            redirect(base_url("h5info/h5list"));
        }
        $data['h5_id'] = $h5_id;
        $data['day_order'] = $day_order;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/upload');
        $this->load->view('h5/trip_index_yl',$data);
    }

//增加机场接送
    public function  car_list($h5_id){
      
      $data['h5_id']=$h5_id;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/car_upload');
       $this->load->view('h5/car_list',$data); 
    }
//机场接送
    public function  car_temp($h5_id){
      
       $where="h5_id=".$h5_id;
       $data['h5_id']=$h5_id;
      $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_car');
      $data['delete_url']=base_url("h5info_trip_yl_zyx/delete_car");
       $data['update_url']=base_url("h5info_trip_yl_zyx/update_car");
       $this->load->view('h5/car_temp',$data); 
    }
    //车辆信息删除
    public  function delete_car($id,$h5_id){
        $where='id='.$id;
     $res=$this->User_model->del($where,'v_h5_car');
//     echo $this->db->last_query();die;

     if($res=1){
        redirect(base_url("h5info_trip_yl_zyx/car_temp/$h5_id"));  
     }
        
    }
   //干果修改界面
    public  function update_ganguo($id,$h5_id){

        $where= "Id=".$id." and h5_id=".$h5_id;
        $data['info']=$this->User_model->get_select_one($select='*',$where,'v_ganguo' );
        $data['sub_url']=base_url("h5info_trip_yl_zyx/ganguo_update");
//         echo "<pre>";
//         print_r($data);
//         echo "</pre>";

        $this->load->view('h5/ganguo_update',$data);
    }
   //干果价格修改
    public  function  ganguo_update($id,$h5_id){
        $where='id='.$id;
        $data['name']=$this->input->post('name',TRUE);
        $data['money']=$this->input->post('money',TRUE);
        $data['guige']=$this->input->post('guige',TRUE);
        $data['ganguo_bz']=$this->input->post('ganguo_bz',TRUE);
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['img']=$this->upload_image('image','H5image');

            //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        $res= $this->User_model->update_one($where,$data,'v_ganguo');
        if($res){
            redirect(base_url("h5info_trip_yl_zyx/xj_ganguo/$h5_id"));

        }

    }
    //车辆信息修改界面
   public  function update_car($id,$h5_id){
       
          $where= "Id=".$id." and h5_id=".$h5_id;         
         $data['info']=$this->User_model->get_select_one($select='*',$where,'v_h5_car' );     
        $data['sub_url']=base_url("h5info_trip_yl_zyx/car_updates");
//         echo "<pre>";
//         print_r($data);
//         echo "</pre>";
       
        $this->load->view('h5/car_list_upate',$data);
    }
    //车辆信息修改详情
    public  function car_updates($id,$h5_id){
   
        $where='Id='.$id;
    $data['car_name']=$this->input->post('car_name',TRUE);
    $data['car_sum']=$this->input->post('car_sum',TRUE);
    $data['type_id']=$this->input->post('car',TRUE);
    $data['car_lgguge']=$this->input->post('car_xinli',TRUE);
    $data['car_date']=$this->input->post('car_date',TRUE);
    $data['car_money']=$this->input->post('car_money',TRUE);
     $data['car_bz']=$this->input->post('car_bz',TRUE);
      $data['car_bc']=$this->input->post('car_bc',TRUE);
    $data['xj']=$this->input->post('xj',TRUE);
  
            if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['car_image']=$this->upload_image('image','H5image');
            
        //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
         $res= $this->User_model->update_one($where,$data,'v_h5_car'); 
        if($res){
          redirect(base_url("h5info_trip_yl_zyx/car_temp/$h5_id"));     
            
        }
    }
    //自由行二酒店增加列表
    public  function hotel_temp($h5_id){
      $data['h5_id']=$h5_id;
      $where="h5_id=$h5_id AND is_show=1";
      $data['sub_url'] = base_url('h5info_trip_yl_zyx/hotel_list_2');
      $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_hotel');
      $data['delete_url']=base_url("h5info_trip_yl_zyx/delete_car_hotel");
      $data['update_url']=base_url("h5info_trip_yl_zyx/update_car_hotel");
//                    echo "<pre>";
//                    print_r($data);
//                    echo "</pre>";
      $this->load->view('h5/hotel_temp',$data);  
    }
   //测试新疆酒店
        public  function hotel_temp_xj($h5_id){
      $data['h5_id']=$h5_id;
      $where="h5_id=$h5_id AND is_show=1";
      $data['sub_url'] = base_url('h5info_trip_yl_zyx/hotel_list_3');
      $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_hotel');
      $data['delete_url']=base_url("h5info_trip_yl_zyx/delete_car_hotel");
      $data['update_url']=base_url("h5info_trip_yl_zyx/update_car_hotel2");
//                    echo "<pre>";
//                    print_r($data);
//                    echo "</pre>";
      $this->load->view('h5/hotel_temp_xj',$data);  
    }
   //自由行二酒店修改页面
   public  function update_car_hotel($id,$h5_id){
       $where= 'id='.$id;
       $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_hotel');
       foreach($data['info'] as $k=>$v){
        
           $data['img']= json_decode($v['hotel_image']);    
               
           } 
           $data['id']=$id;
           $data['h5_id']=$h5_id;
    $data['sub_url']= base_url('h5info_trip_yl_zyx/upload_hotel');
    $this->load->view('h5/hotel_list_insert',$data);   
   }
  //测试新疆
      public  function update_car_hotel2($id,$h5_id){
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

       $where= 'id='.$id;
       $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_hotel');
       foreach($data['info'] as $k=>$v){
        
           $data['img']= json_decode($v['hotel_image']);    
               
           } 
           $data['id']=$id;
           $data['h5_id']=$h5_id;
    $data['sub_url']= base_url('h5info_trip_yl_zyx/upload_hotels');
    $this->load->view('h5/hotel_list_insert_xj',$data);   
   }

   //自由行二修改页面详情
   public function upload_hotel($id,$h5_id){
        $where='id='.$id;
        //获取页面信息
         $resd = $this->User_model->get_select_one($select='*',$where,'v_h5_hotel');
        $h5_id=$this->input->post('h5_id',TRUE);
        $data['hotel_easy_title'] = $this->input->post('hotel_name', TRUE);
         $data['hotel_detail_title']= $this->input->post('hotel_name2', TRUE);
         $data['hotel_tj']= $this->input->post('hotel_tj', TRUE);
         $data['hotel_sum']= $this->input->post('hotel_sum', TRUE);
         $data['hotel_jibie']= $this->input->post('hotel_jibi', TRUE);
         $data['hotel_desc'] = $this->input->post('hotel_js', TRUE);
         $data['hotel_money']= $this->input->post('hotel_money', TRUE);
         $data['hotel_image']= $this->input->post('hotel_img1', TRUE);
        $goods_info=  json_decode($resd['hotel_image']);
     
      $del_image=explode(',',$this->input->post('del_image_id',TRUE)); 
             $view_info = array();
        if(count($data['hotel_easy_title'])>=1)
        {
            for($i=0;$i<count($data['hotel_easy_title']);$i++)
            {
                $view_file_name='hotel_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                 $view_image = $this->merge_image($goods_info->view_image,$del_image,$new_url);
         
                $view_info = array(
                               
                                'view_image' =>$view_image
                );
               
            }
        }
        $data['hotel_image']=  json_encode($view_info);
//        echo "<pre>";
//      print_r($data);
//      echo "</pre>";die;
        $res= $this->User_model->update_one($where,$data,'v_h5_hotel'); 
     //   echo $this->db->last_query();
        if($res){
           redirect(base_url("h5info_trip_yl_zyx/hotel_temp/$h5_id"));    
            
        }
   }
  //测试新疆
     public function upload_hotels($id){
        $where='id='.$id;
        //获取页面信息
         $resd = $this->User_model->get_select_one($select='*',$where,'v_h5_hotel');
        $h5_id=$this->input->post('h5_id',TRUE);
        $data['hotel_easy_title'] = $this->input->post('hotel_name', TRUE);
         $data['hotel_detail_title']= $this->input->post('hotel_name2', TRUE);
         $data['dizi_hotel']= $this->input->post('xj', TRUE);
         $data['hotel_sum']= $this->input->post('hotel_sum', TRUE);
         $data['hotel_jibie']= $this->input->post('hotel_jibi', TRUE);
         $data['hotel_desc'] = $this->input->post('hotel_js', TRUE);
         $data['hotel_money']= $this->input->post('hotel_money', TRUE);
         $data['hotel_image']= $this->input->post('hotel_img1', TRUE);
        $goods_info=  json_decode($resd['hotel_image']);
          //日期信息
        $date_val=$this->input->post('date_val',TRUE);
        $date_price=$this->input->post('date_price',TRUE);
        $date_val1=$this->input->post('date_val1',TRUE);
        $date_price1=$this->input->post('date_price1',TRUE);
           $dates = array();
             if (count($date_val) > 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                $new_date[$v] = $date_price[$k];
            }
            $date[] = array(
                'from' => isset($from_place[0]) ? $from_place[0] : '',
                'date' => $new_date
            );
        }
        if (count($date_val1) > 0) {
            $new_date = [];
            foreach ($date_val1 as $k => $v) {
                $new_date[$v] = $date_price[$k];
            }
            $date[] = array(
                'from' => isset($from_place[1]) ? $from_place[1] : '',
                'date' => $new_date
            );
            //$data['date']=json_encode($new_date);
        }
        $data['date'] = json_encode($date); 
     
      $del_image=explode(',',$this->input->post('del_image_id',TRUE)); 
             $view_info = array();
        if(count($data['hotel_easy_title'])>=1)
        {
            for($i=0;$i<count($data['hotel_easy_title']);$i++)
            {
                $view_file_name='hotel_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                 $view_image = $this->merge_image($goods_info->view_image,$del_image,$new_url);
         
                $view_info = array(
                               
                                'view_image' =>$view_image
                );
               
            }
        }
        $data['hotel_image']=  json_encode($view_info);
//        echo "<pre>";
//      print_r($data);
//      echo "</pre>";die;
        $res= $this->User_model->update_one($where,$data,'v_h5_hotel'); 
     //   echo $this->db->last_query();
        if($res){
           redirect(base_url("h5info_trip_yl_zyx/hotel_temp_xj/$h5_id"));    
            
        }
   } 
   //新疆行程
     public function trip_list_xj($h5_id)
    {
        if($h5_id)
        {
            $day_order = 0;
            $where="h5_id=".$h5_id.' AND is_del=0';
            $data['info']=$this->User_model->get_select_all($select='*',$where,'day_order','ASC','v_h5_day');
            if($data['info'])
            {
                foreach($data['info'] as $k=>$v)
                {
                    $data['info'][$k]['edit_url']=base_url("h5info_trip_yl/trip_edit/$v[h5_id]/$v[day_id]");
                    $data['info'][$k]['detail_url']=base_url("h5show_yl/show/$v[day_id]");
                    $data['info'][$k]['del_url']=base_url("h5info_trip_yl/del_day/$v[h5_id]/$v[day_id]");
                    $day_order = $v['day_order'];
                }
            }
            $day_order = intval($day_order)+1;
            $data['add_url']=base_url("h5info_trip_yl_zyx/index/$h5_id/$day_order");
            $this->load->view('h5/trip_list_youlun_xj',$data);
        }
        else
        {
            redirect(base_url("h5info/h5list"));
        }
    }
  //自由行二删除
  public function delete_car_hotel($id,$h5_id){
         $where='id='.$id;
  
       $res=$this->User_model->update_one($where,array('is_show'=>2),'v_h5_hotel');
//     echo $this->db->last_query();die;

     if($res=1){
        redirect(base_url("h5info_trip_yl_zyx/hotel_temp/$h5_id"));  
     }
      
      
  }
  //增加自由行2
    public function  hotel_list_2($h5_id){
      
        $data['h5_id']=$h5_id;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/hotel_upload'); 
       $this->load->view('h5/hotel_list_2',$data); 
    }
      //测试新疆
       public function  hotel_list_3($h5_id){
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

      
        $data['h5_id']=$h5_id;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/hotel_upload'); 
       $this->load->view('h5/hotel_list_3',$data); 
    }
    //测试新疆
    public function  hotel_upload(){
        $h5_id=$this->input->post('h5_id',TRUE);
        $hotel_easy_title = $this->input->post('hotel_name', TRUE);
        $hotel_detail_title = $this->input->post('hotel_name2', TRUE);
        $hotel_tj = $this->input->post('hotel_tj', TRUE);
        $hotel_sum = $this->input->post('hotel_sum', TRUE);
        $hotel_jibi = $this->input->post('hotel_jibi', TRUE);
        $hotel_desc = $this->input->post('hotel_js', TRUE);
        $hotel_money = $this->input->post('hotel_money', TRUE);
        $xj=$this->input->post('xj',TRUE);
           //日期信息
        $date_val=$this->input->post('date_val',TRUE);
        $date_price=$this->input->post('date_price',TRUE);
        $date_val1=$this->input->post('date_val1',TRUE);
        $date_price1=$this->input->post('date_price1',TRUE);
           $dates = array();
             if (count($date_val) > 0) {
            $new_date = [];
            foreach ($date_val as $k => $v) {
                $new_date[$v] = $date_price[$k];
            }
            $date[] = array(
                'from' => isset($from_place[0]) ? $from_place[0] : '',
                'date' => $new_date
            );
        }
        if (count($date_val1) > 0) {
            $new_date = [];
            foreach ($date_val1 as $k => $v) {
                $new_date[$v] = $date_price[$k];
            }
            $date[] = array(
                'from' => isset($from_place[1]) ? $from_place[1] : '',
                'date' => $new_date
            );
            //$data['date']=json_encode($new_date);
        }
        $datas = json_encode($date); 
      
           if(count($hotel_easy_title)>=1)
        { 
             for($i=0;$i<count($hotel_easy_title);$i++)
            {
                $view_file_name='hotel_img'.(1+$i);
                $new_url=$this->more_pic_upload($view_file_name);
           //   var_dump($hotel_img);
//                echo "<pre>";
        //        print_r($new_url);
//                echo "</pre>";die;

             $hotel_image = array(
                            //    'view_name' => $view_name[$i],
                        //        'view_intro' => str_replace('**','<br>',$view_intro[$i]),
                                'view_image' => $new_url
                );
             $data[$i]=array(
                 'h5_id'=>$h5_id,
                  'hotel_easy_title'=>$hotel_easy_title[$i],
                 'hotel_detail_title'=>$hotel_detail_title[$i],
                 'hotel_tj'=>$hotel_tj[$i],
                 'hotel_sum'=>$hotel_sum[$i],
                 'hotel_jibie'=>$hotel_jibi[$i],
                 'hotel_desc'=> $hotel_desc[$i],
                 'hotel_money'=>$hotel_money[$i],
                 'dizi_hotel'=>$xj[$i],
                'date'=>$datas,
                 'hotel_image'=> json_encode($hotel_image)
             );
          
            }
            
     
            
               $res= $this->db->insert_batch('v_h5_hotel',$data);
            if($res){
              if($xj==""){
 redirect(base_url("h5info_trip_yl_zyx/hotel_temp/$h5_id"));   
              }else{
            redirect(base_url("h5info_trip_yl_zyx/hotel_temp_xj/$h5_id"));         
              }
           
            }
        }
   
      
     
    }
//机酒操作列表
    public  function taocan_temp($id){
        $data['id']=$id;
        $data['sub_url']=base_url("h5info_trip_yl_zyx/taocan_list");
        $data['update_url']=base_url("h5info_trip_yl_zyx/taocan_update");
         $data['delete_url']=base_url("h5info_trip_yl_zyx/taocan_delete");
         $where= 'h5_id='.$id;
       $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_package');
//       echo "<pre>";
//       print_r($data);
//       echo "</pre>";
     $this->load->view('h5/taocan_temp',$data);   
    }
  //机酒套餐增加
  public  function taocan_list($id){
      $data['id']=$id;
      $data['sub_url']=base_url('h5info_trip_yl_zyx/taocan_insert');
//      echo"<pre>";
//      print_r($data);
//      echo "</pre>";
     $this->load->view('h5/taocan_list',$data); 
      
  }
  public  function taocan_insert($id){
//    echo "<pre>";
//    print_r($_FILES);die;
      
      $package_name=$this->input->post('package_name',TRUE);
      $package_contet=$this->input->post('package_contet',true);
      $package_money=$this->input->post('package_money',true);
      $package_remkres=$this->input->post('package_remkres',TRUE);
      $hotel_remkres=$this->input->post('hotel_remkres',TRUE);
       $hotel_data=$this->input->post('hotel_data',TRUE);
      if(count($package_name)>=1){
          for($i=0;$i<count($package_name);$i++){
              $view_file_name='package_img'.($i+1);
                 
           if(isset($_FILES[$view_file_name]) && $_FILES[$view_file_name]['error']==0)
        {  
        $package_image['img']=$this->upload_image($view_file_name,'H5image');
             
      }
      
             $view_file_names='hotel_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_names);
               //   $view_image = $this->merge_image($datas['xiugai_img']->view_image,$del_image,$new_url);
               // var_dump($view_file_name);die;
                $hotel_img = array(
                                'view_image' =>$new_url
      
     ); 
                $data[$i]=array(
                    'h5_id'=>$id,
                    'package_name'=>$package_name[$i],
                    'package_contet'=>$package_contet[$i],
                    'package_money'=>$package_money[$i],
                    'package_remkres'=>$package_remkres[$i],
                    'hotel_remkres'=>$hotel_remkres[$i],
                     'hotel_data'=>$hotel_data[$i],
                    'package_img'=> $package_image['img'],
                    'hotel_img'=>  json_encode($hotel_img)
                    
                    
                );
                
        }
        $res=$this->db->insert_batch('v_h5_package',$data);
                    if($res){
                     redirect(base_url("h5info_trip_yl_zyx/taocan_temp/$id"));    
                        
                    }
                    
  }
  
  
        }
     //机酒套餐删除
     public  function taocan_delete($id,$h5_id){
     $where='Id='.$id;
     $res=$this->User_model->del($where,'v_h5_package'); 
     if($res){
       redirect(base_url("h5info_trip_yl_zyx/taocan_temp/$h5_id"));   
         
     }
         
     }
    //机酒套餐修改页面列表
    public function taocan_update($id,$h5_id){
        $where='Id='.$id;
        $data['h5_id']=$h5_id;
       $data['info']=$this->User_model->get_select_all($select='*',$where,'Id','ASC','v_h5_package');
       foreach($data['info'] as $k=>$v){
           $data['info'][$k]['img']=  json_decode($v['hotel_img']);
           
       }
       $data['sub_url']=base_url('h5info_trip_yl_zyx/taocan_updates');
//       echo "<pre>";
//       print_r($data);
//       echo "</pre>";
      $this->load->view('h5/taocan_update',$data);   
    }
    //机酒套餐页面修改
    public  function taocan_updates(){
//     echo "<pre>";
//      print_r($_POST);
        $id=$this->input->post('id',TRUE);
        $h5_id=$this->input->post('h5_id',TRUE);
         $where = 'Id='.$id;
        $goods = $this->User_model->get_select_one($select='*',$where,'v_h5_package');
       $data['package_name']=$this->input->post('package_name',TRUE);
       $data['package_contet']=$this->input->post('package_contet',TRUE);
       $data['package_money']=$this->input->post('package_money',TRUE);
       $data['package_remkres']=$this->input->post('package_remkres',TRUE);
       $data['hotel_remkres']=$this->input->post('hotel_remkres',TRUE);
        $data['hotel_data']=$this->input->post('hotel_data',TRUE);
        $del_image=explode(',',$this->input->post('del_image_id',TRUE));
        
        //print_r($data);die;
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['package_img']=$this->upload_image('image','H5image');
            
        }
        $goods_info = json_decode($goods['hotel_img']);

                $view_file_name='view_img';
                $new_url=$this->more_pic_upload($view_file_name);
                $view_image = $this->merge_image($goods_info->view_image,$del_image,$new_url);
                var_dump($view_image);
                $hotel_img = array(
                                'view_image' => $view_image,             
                );
              $data['hotel_img']= json_encode($hotel_img);
        $this->User_model->update_one(array('Id'=>$id),$data,'v_h5_package');
       redirect(base_url("h5info_trip_yl_zyx/taocan_update/$id/$h5_id"));

    }
   // 自由行2 航班增加列表
    public function hangban_temp($id){
        $data['h5_id']=$id;
        $data['hb_update']=base_url('h5info_trip_yl_zyx/hb_update');
        
      $this->load->view('zyx/fly_temp',$data);  
    }
   //自由行2  航班增加详情
        public function hb_update($id)
    {
          

        $is_z=$this->input->post('is_z',TRUE);
         $bang=$this->input->post('shi',TRUE);           
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
            $data['type_bang']=$bang;
            $data['h5_id']=$id;
            $data['fly_sn']=$fly_sn[0];
            $data['type']=1;
            $data['fly_name']=$fly_name[0];
            $data['fly_start_place']=$place[0];
            $data['fly_end_place']=$place[2];
            $data['fly_start_time']=$this->return_unix_time($time[0]);
            $data['fly_end_time']=$this->return_unix_time($time[1]);
            $pid=$this->User_model->user_insert('v_h5_fly',$data);
                
            //无中转 back
            $data['type_bang']=$bang;
            $data['h5_id']=$id;
            $data['fly_sn']=$fly_sn[2];
            $data['pid']=$pid;
            $data['type']=2;
            $data['fly_name']=$fly_name[2];
            $data['fly_start_place']=$place[2];
            $data['fly_end_place']=$place[0];
            $data['fly_start_time']=$this->return_unix_time($time[4]);
            $data['fly_end_time']=$this->return_unix_time($time[5]);
            $this->User_model->user_insert('v_h5_fly',$data);
           
            redirect(base_url('h5info_trip_yl_zyx/hb_update'));

        }
        else
        { 
            // go中转
            $data['type_bang']=$bang;
            $data['h5_id']=$id;
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
            $data['type_bang']=$bang;
            $data['h5_id']=$id;
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
            $data['type_bang']=$bang;
            $data['h5_id']=$id;
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
              $data['type_bang']=$bang;      
             $data['h5_id']=$id;
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
    public function return_unix_time($datetime_local='2017-05-04T15:35')
    {

        return strtotime(substr($datetime_local,0,10).''.substr($datetime_local,11));
    }
//自由行2 行程页面详情
    public function trip_xq($id,$k){
        $where ='h5_id='.$id;
    
        $data['trip']=$this->User_model->get_select_all('*',$where,'day_fly','ASC','v_h5_day');
         foreach ($data['trip'] as $k2=>$v2){
            $data['trip'][$k2]['trip_jindian']=  json_decode($v2['day_view']);
            $data['trip'][$k2]['trip_day_main']=  json_decode($v2['day_main']);
        foreach( $data['trip'][$k2]['trip_jindian'] as $k3=>$v3){
            
            $data['trip'][$k2]['trip_jindian'][$k3]->trip_zb=  explode('**',$v3->trip_zb);
            $data['trip'][$k2]['trip_jindian'][$k3]->trip_zy=  explode('**',$v3->trip_zy);
            $data['trip'][$k2]['trip_jindian'][$k3]->trip_yd=  explode('**',$v3->trip_yd);
            $data['trip'][$k2]['trip_jindian'][$k3]->trip_ck=  explode('**',$v3->trip_ck);
        }
           
        }
        $data['trip_xq']=$data['trip'][$k2]['trip_jindian'][$k];
//       echo "<pre>";
//        print_r($data['trip_xq']);
//        echo "</pre>";
        $this->load->view('zyx/trip_xq',$data);
    }
    /**
     * h5页面行程列表
     */
    public function trip_list($h5_id)
    {
        if($h5_id)
        {
            $day_order = 0;
            $where="h5_id=".$h5_id.' AND is_del=0';
            $data['info']=$this->User_model->get_select_all($select='*',$where,'day_order','ASC','v_h5_day');
            if($data['info'])
            {
                foreach($data['info'] as $k=>$v)
                {
                    $data['info'][$k]['edit_url']=base_url("h5info_trip_yl_zyx/trip_edit/$v[h5_id]/$v[day_id]");
                    $data['info'][$k]['detail_url']=base_url("h5show_yl/show/$v[day_id]");
                    $data['info'][$k]['del_url']=base_url("h5info_trip_yl/del_day/$v[h5_id]/$v[day_id]");
                    $day_order = $v['day_order'];
                }
            }
            $day_order = intval($day_order)+1;
            $data['add_url']=base_url("h5info_trip_yl_zyx/index/$h5_id/$day_order");
            $this->load->view('h5/trip_list_youlun',$data);
        }
        else
        {
            redirect(base_url("h5info/h5list"));
        }
    }

    /**
     * h5页面行程编辑
     */
    public function trip_edit($h5_id,$day_id)
    {
        if(!$day_id || !$h5_id)
        {
            redirect(base_url("h5info/h5list"));
        }
        $data = array();
        //获取行程信息
        $where = 'day_id='.$day_id.' AND h5_id='.$h5_id.' AND is_del=0';
        $res = $this->User_model->get_select_one($select='*',$where,'v_h5_day');
        if($res)
        {
            $data['info'] = $this->get_trip_info($res);
            $data['day_order'] = $res['day_order'];
        }
        else
        {
            redirect(base_url("h5info_trip_yl/trip_list/$h5_id"));
        }
        $data['h5_id'] = $h5_id;
        $data['day_id'] = $day_id;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/update');

//        echo '<pre>';
//        print_r($data);
//        echo "</pre>";
        $this->load->view('h5/trip_edit_yl',$data);
    }

    /**
     * h5页面行程删除
     */
    public function del_day($h5_id,$day_id)
    {
        if($day_id && $h5_id)
        {
            $where = 'day_id='.$day_id.' AND h5_id='.$h5_id.' AND is_del=0';
            $res=$this->User_model->get_select_one($select='day_id',$where,'v_h5_day');
            if($res)
            {
                $this->User_model->update_one($where,array('is_del'=>1,'update_time'=>date('Y-m-d H:i:s',time())),'v_h5_day');
            }
        }
        redirect(base_url("h5info_trip_yl/trip_list/$h5_id"));
    }

    /**
     * h5页面行程展示
     */
    public function show($day_id)
    {
        if(!$day_id)
        {
            redirect(base_url("h5info/h5list"));
        }
        $data = array();
        //获取行程信息
        $where = 'day_id='.$day_id.' AND is_del=0';
        $res = $this->User_model->get_select_one($select='*',$where,'v_h5_day');
        if($res)
        {
            $data['info'] = $this->get_trip_info($res);

            $data['day_order'] = $res['day_order'];
        }
        else
        {
            redirect(base_url("h5info/h5list"));
        }
        //echo "<pre>";
        //var_dump($data);die;
        $this->load->view('h5/trip_show',$data);
    }
  /**
     * 游轮页面行程展示
     */
    public function show_youlun($day_id)
    {
        if(!$day_id)
        {
            redirect(base_url("h5info/h5list"));
        }
        $data = array();
        //获取行程信息
        $where = 'day_id='.$day_id.' AND is_del=0';
        $res = $this->User_model->get_select_one($select='*',$where,'v_h5_day');
        if($res)
        {
            $data['info'] = $this->get_trip_info($res);

            $data['day_order'] = $res['day_order'];
        }
        else
        {
            redirect(base_url("h5info/h5list"));
        }
        //echo "<pre>";
        //var_dump($data);die;
        $this->load->view('h5/trip_show',$data);
    }
    
    /**
     * 接送车辆表单提交
     */    //新疆接送
    public function  xj_car($h5_id){
       $where="h5_id=".$h5_id;
       $data['h5_id']=$h5_id;
      $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_h5_car');
      $data['delete_url']=base_url("h5info_trip_yl_zyx/delete_car");
       $data['update_url']=base_url("h5info_trip_yl_zyx/update_car");
  
       $this->load->view('h5/car_temp',$data);    
        
    }
    /**
     * 接送车辆表单提交
     */    //新疆干果表单
    public function  xj_ganguo($h5_id){
        $where="h5_id=$h5_id AND is_show='1'";
        $data['h5_id']=$h5_id;
        $data['info']=$this->User_model->get_select_all($select='*',$where,'id','ASC','v_ganguo');
        $data['delete_url']=base_url("h5info_trip_yl_zyx/delete_ganguo");
        $data['update_url']=base_url("h5info_trip_yl_zyx/update_ganguo");

        $this->load->view('h5/xj_shangpin',$data);

    }
    //干果类增加
    public  function ganguo($h5_id){

        $data['h5_id']=$h5_id;
        $data['sub_url'] = base_url('h5info_trip_yl_zyx/ganguo_upload');
        $this->load->view('h5/ganguo_list',$data);

    }
    //干果uplod
    public  function ganguo_upload(){
        $data['h5_id']=$this->input->post('h5_id',true);
        $h5_id=$data['h5_id'];
        $data['name']=$this->input->post('ganguo_name',true);
        $data['money']=$this->input->post('ganguo_money',true);
        $data['guige']=$this->input->post('ganguo_guige',true);
        $data['ganguo_bz']=$this->input->post('ganguo_bz',true);


            if(isset($_FILES[image1]) && $_FILES[image1]['error']==0)
            {
                $data['img']=$this->upload_image(image1,'H5image');

                //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
            }

        $this->User_model->user_insert('v_ganguo',$data);

        redirect(base_url("h5info_trip_yl_zyx/xj_ganguo/$h5_id"));

    }
    //干果删除

    public  function delete_ganguo($id,$h5_id){

        $where='id='.$id;

        $res=$this->User_model->update_one($where,array('is_show'=>2),'v_ganguo');
//     echo $this->db->last_query();die;

        if($res=1){
            redirect(base_url("h5info_trip_yl_zyx/xj_ganguo/$h5_id"));
        }

    }
    public function  car_upload(){
//        echo "<pre>";
//        print_r($_POST);
//        echo "</pre>";die;
  
       $h5_id=$this->input->post('h5_id',TRUE);
       $car_name=$this->input->post('car_name',TRUE);
       $type_id=$this->input->post('car',TRUE);
       $car_sum=$this->input->post('car_sum',TRUE);
       $car_lgguge=$this->input->post('car_xinli',TRUE);
        $car_date=$this->input->post('car_date',TRUE);
         $car_money=$this->input->post('car_money',TRUE);
          $car_bz=$this->input->post('car_bz',TRUE);
          $xj=$this->input->post('xj',true);
           $bc=$this->input->post('car_bc',true);
        for($i=0;$i<count($car_name);$i++ ){
               $view_file_name='image'.($i+1);
       
            if(isset($_FILES[$view_file_name]) && $_FILES[$view_file_name]['error']==0)
        {
            $car_image[]=$this->upload_image($view_file_name,'H5image');

            //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
                 $data[]=array(
                 'h5_id'=>$h5_id,
                 'car_name'=>$car_name[$i],
                 'type_id'=>$type_id[$i],
                 'car_sum'=>$car_sum[$i],
                 'car_lgguge'=>$car_lgguge[$i],
                 'car_date'=>$car_date[$i],
                 'car_money'=>$car_money[$i],
                 'car_bz'=>$car_bz[$i],
                  'xj'=>$xj[$i], 
                   'car_bc'=>$bc[$i],    
                 'car_image'=>$car_image[$i]
             
             );
         }
         
    
     
       $res= $this->db->insert_batch('v_h5_car', $data); 
       if($res){
          redirect(base_url("h5info_trip_yl_zyx/xj_car/$h5_id"));
           
       }
         
  
          
          
        
        
    }

    /**
    * 表单提交
     */
    public function upload()
            
    { 
        $trip_data['h5_id']=$this->input->post('h5_id',TRUE);
        $trip_data['day_order']=$this->input->post('day_order',TRUE);
        $fly_sn=$this->input->post('fly_sn',TRUE);
        $fly_start_time=$this->input->post('fly_start_time',TRUE);
        $fly_start_place=$this->input->post('fly_start_place',TRUE);
        $fly_end_time=$this->input->post('fly_end_time',TRUE);
        $fly_end_place=$this->input->post('fly_end_place',TRUE);

        //首页显示信息
        $main_info['day_main_title'] = $this->input->post('day_main_title',TRUE);
        $main_info['day_main_route'] = $this->input->post('day_main_route',TRUE);

        if(isset($_FILES['day_main_image']) && $_FILES['day_main_image']['error']==0)
        {
            $main_info['day_main_image']=$this->upload_image('day_main_image','H5image');
            //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        $trip_data['day_main'] = json_encode($main_info);


        //行程信息
        $trip_data['day_title'] = $this->input->post('day_title',TRUE);
        $trip_data['day_list'] = json_encode($this->input->post('day_list',TRUE));
        $trip_data['day_grade_hotel'] = $this->input->post('same_hotel',TRUE);
      
       
       $day=array(
           'youlun_end_place' =>$this->input->post('youlun_end_place',TRUE),
           'youlun_end_time' =>$this->input->post('youlun_end_time',TRUE)
           
       );
                   
        $view_name = $this->input->post('view_name',TRUE);
        $view_intro = $this->input->post('view_intro',TRUE);
        $trip_zb=$this->input->post('trip_zb',TRUE);
        $trip_zy=$this->input->post('trip_zy',TRUE);
        $trip_yd=$this->input->post('trip_yd',TRUE);
         $trip_ck=$this->input->post('trip_ck',TRUE);
        $trip_money=$this->input->post('trip_money',TRUE);

        //景点信息
        $view_info = array();

        if(count($view_name)>=1)
        {
            for($i=0;$i<count($view_name);$i++)
            {
                $view_file_name='view_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                $view_info[] = array(
                                'view_name' => $view_name[$i],
                                'view_intro' => $view_intro[$i],
                                'trip_zb'=>$trip_zb[$i],
                                'trip_zy'=>$trip_zy[$i],
                                'trip_yd'=>$trip_yd[$i],
                                'trip_ck'=>$trip_ck[$i],
                                'trip_money'=>$trip_money[$i],
                                'view_image' => $new_url
                );
            }
        }

        //酒店信息
        $hotel_info = array();
        $hotel_file_name='hotel_img';
        $hotel_img_url=$this->more_pic_upload($hotel_file_name);
        $hotel_info = array(
                        'hotel_name' => $this->input->post('hotel_name',TRUE),
                        'hotel_img'  => $hotel_img_url
            );

        $trip_data['day_hotel'] = json_encode($hotel_info);
        $trip_data['day_fly'] = count($fly_sn)>=1 ? 1 : 0;
        $trip_data['day_order'] = $this->input->post('day_order',TRUE);
        $trip_data['day_view'] = json_encode($view_info);
        $trip_data['day_tips'] = $this->input->post('tips',TRUE);
       
        $trip_data['day_place'] = json_encode($day);
       // echo '<pre>';
        //print_r($trip_data);
        //print_r($_FILES);
       // die();
        $day_id = $this->trip_insert($trip_data);

        //航班信息
        if(count($fly_sn)>=1)
        {
            $fly_data['h5_id'] = $trip_data['h5_id'];

            for($i=0;$i<count($fly_sn);$i++)
            {
                if($fly_sn[$i])
                {
                    $fly_data['fly_sn'] = $fly_sn[$i];
                    $fly_data['day_id'] = $day_id;
                    $fly_data['fly_start_time'] = strtotime(str_replace('：',':', $fly_start_time[$i]));
                    $fly_data['fly_start_place'] = $fly_start_place[$i];
                    $fly_data['fly_end_time'] = strtotime(str_replace('：',':', $fly_end_time[$i]));
                    $fly_data['fly_end_time'] = $fly_data['fly_end_time'] < $fly_data['fly_start_time'] ? $fly_data['fly_end_time']+86400 : $fly_data['fly_end_time'];
                    $fly_data['fly_end_place'] = $fly_end_place[$i];
                    $this->fly_insert($fly_data);
                }
            }
        }
        redirect(base_url("h5info_trip_yl/trip_list/$trip_data[h5_id]"));
    }

    /**
     * 表单提交
     */
    public function update()
    {
        $trip_data['h5_id']=$this->input->post('h5_id',TRUE);
        $trip_data['day_id']=$this->input->post('day_id',TRUE);

        if(!$trip_data['h5_id'] || !$trip_data['day_id'])
        {
            redirect(base_url("h5info/h5list"));
        }
        //获取行程信息
        $where = 'day_id='.$trip_data['day_id'].' AND h5_id='.$trip_data['h5_id'].' AND is_del=0';
        $res = $this->User_model->get_select_one($select='*',$where,'v_h5_day');
        if(!$res)
        {
            redirect(base_url("h5info/h5list"));
        }
        else
        {
            $trip_info = $this->get_trip_info($res);
        }

        $del_image=explode(',',$this->input->post('del_image_id',TRUE));
        $fly_id=$this->input->post('fly_id',TRUE);
        $fly_sn=$this->input->post('fly_sn',TRUE);
        $fly_start_time=$this->input->post('fly_start_time',TRUE);
        $fly_start_place=$this->input->post('fly_start_place',TRUE);
        $fly_end_time=$this->input->post('fly_end_time',TRUE);
        $fly_end_place=$this->input->post('fly_end_place',TRUE);
              ;
        //首页显示信息
        $main_info['day_main_title'] = $this->input->post('day_main_title',TRUE);
        $main_info['day_main_route'] = $this->input->post('day_main_route',TRUE);

        if(isset($_FILES['day_main_image']) && $_FILES['day_main_image']['error']==0)
        {
            $main_info['day_main_image']=$this->upload_image('day_main_image','H5image');
            //$this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        else
        {
            $main_info['day_main_image']=$trip_info['day_main']->day_main_image;
        }
        $trip_data['day_main'] = json_encode($main_info);


        //行程信息fly_end_time
        $trip_data['day_title'] = $this->input->post('day_title',TRUE);
        $trip_data['day_list'] = json_encode($this->input->post('day_list',TRUE));
        $trip_data['day_grade_hotel'] = $this->input->post('same_hotel',TRUE);
          $trip_zb=$this->input->post('trip_zb',TRUE);
         $trip_money=$this->input->post('trip_money',TRUE);
         $trip_zy=$this->input->post('trip_zy',TRUE);
         $trip_ck=$this->input->post('trip_ck',TRUE);
         $trip_yd=$this->input->post('trip_yd',true);

        $view_name = $this->input->post('view_name',TRUE);
        $view_intro =$this->input->post('view_intro',FALSE);
         $day=array(
           'youlun_end_place' =>$this->input->post('youlun_end_place',TRUE),
           'youlun_end_time' =>$this->input->post('youlun_end_time',TRUE)
           
       );
          $trip_data['day_place'] = json_encode($day);
     //  p($view_intro);die;
        //echo '<pre>';
        //var_dump($_FILES);
        //景点信息
      
        $view_info = array();
        if(count($view_name)>=1)
        {
            for($i=0;$i<count($view_name);$i++)
            {
                $view_file_name='view_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                $view_image = $this->merge_image($trip_info['day_view'][$i]->view_image,$del_image,$new_url);
                //var_dump($view_image);
                $view_info[] = array(
                                'view_name' => $view_name[$i],
                                'view_intro' => str_replace('**','<br>',$view_intro[$i]),
                                'view_image' => $view_image,
                                 'trip_zb'=>$trip_zb[$i],
                    'trip_money'=>$trip_money[$i],
                    'trip_ck'=>$trip_ck[$i],
                    'trip_yd'=>$trip_yd[$i],
                    'trip_zy'=>$trip_zy[$i]
                );
            }
        }

        //酒店信息
        $hotel_info = array();
        $hotel_file_name='hotel_img';
        $hotel_img_url=$this->more_pic_upload($hotel_file_name);
        $hotel_img = $this->merge_image($trip_info['day_hotel']->hotel_img,$del_image,$hotel_img_url);
        $hotel_info = array(
                        'hotel_name' => $this->input->post('hotel_name',TRUE),
                        'hotel_img'  => $hotel_img
            );

        $trip_data['day_hotel'] = json_encode($hotel_info);
        //$trip_data['day_fly'] = count($fly_id)>=1 && !empty($fly_sn[0]) ? 1 : 0;
        $trip_data['day_view'] = json_encode($view_info);
        $trip_data['day_tips'] = $this->input->post('tips',TRUE);
        $trip_data['update_time'] = date('Y-m-d H:i:s',time());
        
//echo '<pre>';
//var_dump($trip_data);die;
        //$day_id = $this->trip_insert($trip_data);

        //航班信息
        $trip_data['day_fly'] = 0;
        if(count($fly_id)>=1)
        {
            //$where = 'h5_id='.$trip_data['h5_id'].' AND day_id='.$trip_data['day_id'];
            //$this->User_model->del($where,'v_h5_fly');

            for($i=0;$i<count($fly_id);$i++)
            {
                $fly_data['h5_id'] = $trip_data['h5_id'];
                if($fly_sn[$i])
                { 
                    $fly_data['fly_sn'] = $fly_sn[$i];
                    $fly_data['day_id'] = $trip_data['day_id'];
                    $fly_data['fly_start_time'] = strtotime(str_replace('：',':', $fly_start_time[$i]));
                    $fly_data['fly_start_place'] = $fly_start_place[$i];
                    $fly_data['fly_end_time'] = strtotime(str_replace('：',':', $fly_end_time[$i]));
                    $fly_data['fly_end_time'] = $fly_data['fly_end_time'] < $fly_data['fly_start_time'] ? $fly_data['fly_end_time']+86400 : $fly_data['fly_end_time'];
                    $fly_data['fly_end_place'] = $fly_end_place[$i];
                    if($fly_id[$i])
                    {
                        //更新航班信息
                        $this->User_model->update_one(array('fly_id'=>$fly_id[$i]),$fly_data,'v_h5_fly');
                    }
                    else
                    {
                        //新增航班信息
                        $this->fly_insert($fly_data);
                    }
                    $trip_data['day_fly'] = 1;
                }
                else
                {
                
                    //无航班信息时删除旧记录
                    $res = $this->User_model->del(array('fly_id'=>$fly_id[$i]),'v_h5_fly');
                }
                    //var_dump($fly_data);
            }
        }
   
        //更新行程表信息
        $this->User_model->update_one(array('day_id'=>$trip_data['day_id']),$trip_data,'v_h5_day');
       
        redirect(base_url("h5info_trip_yl_zyx/trip_list/$trip_data[h5_id]"));
    }

    /**
     * 新增航班信息
     */
    function fly_insert($data)
    {
        $param = array(
                    'h5_id' => $data['h5_id'],
                    'day_id' => $data['day_id'],
                    'fly_sn' => $data['fly_sn'],
                    'fly_start_time' => $data['fly_start_time'],
                    'fly_start_place' => $data['fly_start_place'],
                    'fly_end_time' => $data['fly_end_time'],
                    'fly_end_place' => $data['fly_end_place']
            );
        $this->User_model->user_insert('v_h5_fly',$param);
    }

    /**
     * 新增行程信息
     */
    function trip_insert($data)
    {
        $param = array(
                    'h5_id' => $data['h5_id'],
                    'day_fly' => $data['day_fly'],
                    'day_title' => $data['day_title'],
                    'day_order' => $data['day_order'],
                    'day_main' => $data['day_main'],
                    'day_view' => $data['day_view'],
                    'day_hotel' => $data['day_hotel'],
                    'day_grade_hotel' => $data['day_grade_hotel'],
                    'day_list' => $data['day_list'],
                    'day_place' => $data['day_place'],
                    'day_tips' => $data['day_tips']
            );
        $result = $this->User_model->user_insert('v_h5_day',$param);
        return $result;
    }

    /**
     * 编辑获取行程信息
     */
    function get_trip_info($data)
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
         $result['place'] = json_decode($data['day_place']);
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
     * 编辑获取航班信息
     */
    function get_fly_info($h5_id,$day_id)
    {
        $result = array();
        $where = 'day_id='.$day_id.' AND h5_id='.$h5_id;
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


    //输入unix 返回 1h5m

    function return_hm($time='21600')
    {

        $h_num=intval($time/3600);
        $m_num=intval(($time-$h_num*3600)/60);
        $result = $h_num ? $h_num.'小时' : '';
        $result .= $m_num ? $m_num.'分' : '';
        return $result;
    }

    //批量图片上传
    function more_pic_upload($filename)
    {
      // print_r($filename);
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

    function upload_image($filename,$fileurl,$key='time')
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

    function shangchuan($filename,$fileurl,$key='time')
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
}