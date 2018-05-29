<?php
/**
 * H5页面模板--上传行程
 * Date: 2017/4/20
 * Time: 14:24
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class H5info_trip_yl extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
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
        $data['sub_url'] = base_url('h5info_trip_yl/upload');
        $this->load->view('h5/trip_index_yl',$data);
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
                    $data['info'][$k]['edit_url']=base_url("h5info_trip_yl/trip_edit/$v[h5_id]/$v[day_id]");
                    $data['info'][$k]['detail_url']=base_url("h5show_yl/show/$v[day_id]");
                    $data['info'][$k]['del_url']=base_url("h5info_trip_yl/del_day/$v[h5_id]/$v[day_id]");
                    $day_order = $v['day_order'];
                }
            }
            $day_order = intval($day_order)+1;
            $data['add_url']=base_url("h5info_trip_yl/index/$h5_id/$day_order");
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
        $data['sub_url'] = base_url('h5info_trip_yl/update');

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
         $trip_xj=$this->input->post('xj',TRUE);
      
       
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
             $trip_money1=$this->input->post('trip_money1',TRUE);

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
                                'trip_money1'=>$trip_money1[$i],
                                  'dizi'=>$trip_xj[$i],
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
         

        $view_name = $this->input->post('view_name',TRUE);
        $view_intro =$this->input->post('view_intro',FALSE);
        $trip_money =$this->input->post('trip_money',FALSE);
         $trip_money1 =$this->input->post('trip_money1',FALSE);
        $trip_ck =$this->input->post('trip_ck',FALSE);
        $trip_zb =$this->input->post('trip_zb',FALSE);
          $trip_zy =$this->input->post('trip_zy',FALSE);
            $trip_yd =$this->input->post('trip_yd',FALSE);
               $xj =$this->input->post('xj',FALSE);
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
                                'trip_money'=>$trip_money[$i],
                                'trip_money1'=>$trip_money1[$i],
                                'trip_ck'=>$trip_ck[$i],
                                'trip_zb'=>$trip_zb[$i],
                                'trip_zy'=>$trip_zy[$i],
                                'trip_yd'=>$trip_yd[$i],
                                'dizi'=>$xj[$i],
                                'view_image' => $view_image
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
       
        redirect(base_url("h5info_trip_yl/trip_list/$trip_data[h5_id]"));
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