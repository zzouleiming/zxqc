<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class H5info extends CI_Controller
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

//   echo '<pre>';print_r($data); die;
        $this->load->view('h5/edit_index',$data);
    }


    //h5页面列表
    public function h5list()
    {   
        $where="is_show=1 and type_id=0";
        $data['info']=$this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
        // echo '<pre>';
        //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {  if(array_key_exists('destination', $v)){
            $data['info'][$k]['edit_url']=base_url("h5info_yl/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show_yl/index/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info_yl/del_h5/$v[h5_id]");
            $data['info'][$k]['day_url']=base_url("h5info_trip_yl/trip_list/$v[h5_id]");
            //$data['info'][$k]['del_url']=base_url("h5info/fly_del?fly_id=$v[fly_id]");
        }else{
          $data['info'][$k]['edit_url']=base_url("h5info/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show/index/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info/del_h5/$v[h5_id]");
            $data['info'][$k]['day_url']=base_url("h5info_trip/trip_list/$v[h5_id]");  
            
        }
        
        
        }
        $this->load->view('h5/h5list',$data);
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
        //$date_val=$this->input->post('date_val',TRUE);
        //$date_price=$this->input->post('date_price',TRUE);
        $data_destination=$this->input->post('stc_chufa',TRUE);
     



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
//        if(count($date_val)>0)
//        {
//            $new_date=[];
//            foreach($date_val as $k=>$v)
//            {
//                $new_date[$v]=$date_price[$k];
//            }
//             }
//           $data['date']=json_encode($new_date);

           $date1=array();  
           for($i=0;$i<count($data_destination);$i++){
           if($i)
           {
               $date_val=$this->input->post('date_val'.$i,TRUE);
                $date_price=$this->input->post('date_price'.$i,TRUE);
           }else
           {
                $date_val=$this->input->post('date_val',TRUE);
                $date_price=$this->input->post('date_price',TRUE);
           }
           if(count($date_val)>0)
          {
            $new_date=[];
            foreach($date_val as $k=>$v)
            {
                $new_date[$v]=$date_price[$k];
            }
             }
           $date1[$i]=array(
                'date'=> $new_date,
                'destination'=>$data_destination[$i]
                
            );
           $data['date']=  json_encode($date1);
        }
       
//        echo "<pre>";   
//        print_r($data['date']);
//      //  print_r($data['date']);
//        echo "</pre>";
//        exit();

        $h5_id=$this->User_model->user_insert('v_h5_info',$data);
//         echo $this->db->last_query();
//         exit;
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
//        echo "<pre>";
//        print_r($_POST);
//     echo "</pre>";
//     exit;
    $data_destination=$this->input->post('stc_chufa',true);
 
       



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
//        if(count($date_val)>0)
//        {
//            $new_date=[];
//            foreach($date_val as $k=>$v)
//            {
//                $new_date[$v]=$date_price[$k];
//            }
//            $data['date']=json_encode($new_date);
//           
//        }

        if($data_destination!=""){
             $date1=array();  
           for($i=1;$i<=count($data_destination);$i++){
           if($i)
           {   
               $date_val=$this->input->post('date_val'.$i,TRUE);
                $date_price=$this->input->post('date_price'.$i,TRUE);
                  if(count($date_val)>0)
          {
            $new_date=[];
            if($date_val !=""){
            foreach($date_val as $k=>$v)
            {
                $new_date[$v]=$date_price[$k];
            }
            }
             }
           $date1[]=array(
                'date'=>  array_filter($new_date),
                'destination'=>$data_destination[$i-1]
                
            );
           $data['date']=  json_encode($date1);
           }
           }
    }else{
         $date_val=$this->input->post('date_val',TRUE);
         $date_price=$this->input->post('date_price',TRUE);
             
            $date = array();
        if(count($date_val)>0)
        {
            $new_date=[];
            foreach($date_val as $k=>$v)
            {
                if($date_price[$k])
                {
                    $new_date[$v]=$date_price[$k];
                }
            }
            $date=  $new_date;
                       //     'from'=>isset($from_place[0]) ? $from_place[0] : '',
                        
                       
        }    
           
           $data['date']=  json_encode($date);
      //     print_r($data['date']);die;
        
    }


  //          $date1=array();  
  //         for($i=1;$i<=count($data_destination);$i++){
  //         if($i)
//           {
  //             $date_val=$this->input->post('date_val'.$i,TRUE);
 //               $date_price=$this->input->post('date_price'.$i,TRUE);
  //         }else
  //         {
 //               $date_val=$this->input->post('date_val',TRUE);
  //              $date_price=$this->input->post('date_price',TRUE);
 //          }
  //         if(count($date_val)>0)
  //        {
   //         $new_date=[];
 //           if($date_val !=""){
 //           foreach($date_val as $k=>$v)
  //          {
//            }
 //           }
 //            }
   //        $date1[]=array(
  //              'date'=>  array_filter($new_date),
   //             'destination'=>$data_destination[$i-1]
     //           
     //       );
   //        $data['date']=  json_encode($date1);
    //    }
//        echo "<pre>";
//        print_r($data['date']);
//        echo "</pre>";die;
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
    {    $where='1=1';
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
      
      //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['url']=base_url("h5info/fly_temp/$v[fly_id]");
            $data['info'][$k]['del_url']=base_url("h5info/fly_del/$v[fly_id]");
        }
        $this->load->view('h5/temp_fly_list',$data);
    }
    //航班删除
    public function fly_del($id)
    {
        ;
        $this->User_model->update_one(array('fly_id'=>$id),array('type'=>0),'v_h5_fly');

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
        $h5_id=$this->input->post_get('h5_id',TRUE);
        $start_place=$this->input->post_get('start_place',TRUE);
        $date_start=$this->input->post_get('date_start',TRUE);
        if($h5_id){
          $where="h5_id=$h5_id  AND fly_start_place  LIKE '%$start_place%'  AND type>0";  
            
        }else{
        $where="temp_title LIKE '%$title%'  AND fly_start_place  LIKE '%$start_place%' AND type>0";
        }
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
//自由行2
  //接口数据_修改版
    public function get_ziyouxi2_ajax()
    {   $h5_id=$this->input->post_get('h5_id',TRUE);
        $title=$this->input->post_get('title',TRUE);
        $start_place=$this->input->post_get('start_place',TRUE);
        $date_start=$this->input->post_get('date_start',TRUE);
        $where="h5_id=$h5_id  AND type>0";
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
    //获取出发日期 地点
    public function get_startinfo_ajax()
    {
        $title=$this->input->post_get('title',TRUE);
        $h5_id=$this->input->post_get('h5_id',TRUE);
        if($h5_id)
        {
            $where="h5_id=$h5_id AND pid=0 AND type>0";
        }
        else
        {
        $where="temp_title LIKE '%$title%' AND pid=0 AND type>0";
        }
        $select_time = " distinct(from_unixtime(fly_start_time,'%Y-%m-%d')) as dates ";
        $data=$this->User_model->get_select_all($select_time,$where,$order_title='fly_start_time',$order='ASC',$table='v_h5_fly');
        $select_place = " distinct(fly_start_place) as place ";
        $data1=$this->User_model->get_select_all($select_place,$where,$order_title='fly_start_place',$order='ASC',$table='v_h5_fly');
        $date_list=$place_list=array();
        foreach ($data as  $value) {
            if($value['dates'])
            {
                $date_list[] = $value['dates'];
            }
        }
        foreach ($data1 as  $value) {
            if($value['place'])
            {
                $initials = $this->pinyin1(substr($value['place'], 0,3));
                $place_list[] = $initials.'-'.$value['place'];
            }
        }

        $result['date_list'] = $date_list;
        sort($place_list);
        $result['place_list'] = $place_list;

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

    /** 
     * @name: getfirstchar 
     * @description: 获取汉子首字母 
     * @param: string 
     * @return: mixed 
     * @author: 
     * @create: 2014-09-17 21:46:52 
    **/ 
    function getfirstchar($s0){
        $fchar = ord($s0{0});
        if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
        //$s1 = iconv("UTF-8","gb2312//IGNORE", $s0);
        // $s2 = iconv("gb2312","UTF-8//IGNORE", $s1);
        $s1 = $this->get_encoding($s0,'GB2312');
        $s2 = $this->get_encoding($s1,'UTF-8');
        if($s2 == $s0){$s = $s1;}else{$s = $s0;}  
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if($asc >= -20319 and $asc <= -20284) return "A";
        if($asc >= -20283 and $asc <= -19776) return "B";
        if($asc >= -19775 and $asc <= -19219) return "C";
        if($asc >= -19218 and $asc <= -18711) return "D";
        if($asc >= -18710 and $asc <= -18527) return "E";
        if($asc >= -18526 and $asc <= -18240) return "F";
        if($asc >= -18239 and $asc <= -17923) return "G";
        if($asc >= -17922 and $asc <= -17418) return "H";
        if($asc >= -17417 and $asc <= -16475) return "J";
        if($asc >= -16474 and $asc <= -16213) return "K";
        if($asc >= -16212 and $asc <= -15641) return "L";
        if($asc >= -15640 and $asc <= -15166) return "M";
        if($asc >= -15165 and $asc <= -14923) return "N";
        if($asc >= -14922 and $asc <= -14915) return "O";
        if($asc >= -14914 and $asc <= -14631) return "P";
        if($asc >= -14630 and $asc <= -14150) return "Q";
        if($asc >= -14149 and $asc <= -14091) return "R";
        if($asc >= -14090 and $asc <= -13319) return "S";
        if($asc >= -13318 and $asc <= -12839) return "T";
        if($asc >= -12838 and $asc <= -12557) return "W";
        if($asc >= -12556 and $asc <= -11848) return "X";
        if($asc >= -11847 and $asc <= -11056) return "Y";
        if($asc >= -11055 and $asc <= -10247) return "Z";
        return null;
    }

    /** 
     * @name: get_encoding 
     * @description: 自动检测内容编码进行转换 
     * @param: string data 
     * @param: string to  目标编码 
     * @return: string 
    **/  
    function get_encoding($data,$to){  
        $encode_arr=array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');   
        $encoded=mb_detect_encoding($data, $encode_arr);   
        $data = mb_convert_encoding($data,$to,$encoded);   
        return $data;  
    }   
    
    function pinyin1($zh){  
        $ret = "";  
        $s1 = iconv("UTF-8","gb2312", $zh);  
        $s2 = iconv("gb2312","UTF-8", $s1);  
        if($s2 == $zh){$zh = $s1;}  
        for($i = 0; $i < strlen($zh); $i++){  
            $s1 = substr($zh,$i,1);  
            $p = ord($s1);  
            if($p > 160){  
                $s2 = substr($zh,$i++,2);  
                $ret .= $this->getfirstchar($s2);  
            }else{  
                $ret .= $s1;  
            }  
        }  
        return $ret;  
    }
}