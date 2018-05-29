<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/4/14
 * Time: 14:24
 */

defined('BASEPATH') OR exit('No direct script access allowed');
class H5info_visa extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->wx=stristr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===FALSE?FALSE:TRUE;
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->helper('url');
        $this->load->library('common');
        $this->auth();
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

    /*
    * 增加页面
    */
    public function index()
    {
        $data['sub_url']=base_url('h5info_visa/to_insert');

        $this->load->view('h5/index_visa',$data);
    }

    /*
    * h5签证页面列表
    */
    public function h5list()
    {
        //$where="is_show=1 and type_id=2";
        $where="is_show=1 and type_id=2";
        $data['info']=$this->User_model->get_select_all($select='*',$where,'h5_id','ASC','v_h5_info');
        // echo '<pre>';
        //  print_r(json_encode($data));exit();
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]['edit_url']=base_url("h5info_visa/edit_index/$v[h5_id]");
            $data['info'][$k]['detail_url']=base_url("h5show_visa/index/$v[h5_id]");
            $data['info'][$k]['del_url']=base_url("h5info_visa/del_h5/$v[h5_id]");
            $data['info'][$k]['visa_url']=base_url("h5info_visa/visa_list/$v[h5_id]");
            $data['info'][$k]['goods_url']=base_url("h5info_visa/goods_list/$v[h5_id]");
        }
        $this->load->view('h5/h5list_visa',$data);
    }


    /*
    * 修改主页面
    */
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
        //$data['video_src']=$h5_info['video_src'];
        $data['uploader']=$h5_info['uploader'];
        $data['url_type']=$h5_info['url_type'];


        $data['sign_to_know']=json_decode($h5_info['sign_to_know'],TRUE);


        //$data['visa_info']=$this->User_model->get_visa_info($id);
        //$data['goods_info']=$this->User_model->get_goods_info($id);

        $data['headimage']=$this->User_model->get_image(10,$id);
        $data['textimage']=$this->User_model->get_image(11,$id);
        $data['shareimage']=$this->User_model->get_image(17,$id);
        $data['countryimage']=$this->User_model->get_image(18,$id);

       //echo '<pre>';print_r($data);exit();

        $data['sub_url']=base_url('h5info_visa/to_sub');

       // echo '<pre>';print_r($data);
        $this->load->view('h5/edit_index_visa',$data);
    }


    /*
    * 页面删除
    */
    public function del_h5($id)
    {
        $this->User_model->update_one(array('h5_id'=>$id),array('is_show'=>'2'),$table='v_h5_info');
      return  redirect($_SERVER['HTTP_REFERER']);
    }


    /*
    * 主数据插入
    */
    public function to_insert()
    {
        set_time_limit(0);
        $data['h5_title']=$this->input->post('h5_title',TRUE);
        $data['uploader']=$this->input->post('uploader',TRUE);
        $data['share_desc']=$this->input->post('share_desc',TRUE);
        $data['url_type']=$this->input->post('url_type',TRUE);
        //$video_src=$this->input->post('video_src',TRUE);
        if($video_src)
        {
            $data['video_src']=$video_src;
        }


        //报名须知
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

        $data['type_id']='2';
       // print_r($data);exit();

        $h5_id=$this->User_model->user_insert('v_h5_info',$data);

        if(isset($_FILES['image1']) && $_FILES['image1']['error']==0 )
        {
            $head_image=$this->upload_image('image1','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>10,'url'=>$head_image));
        }
        if(isset($_FILES['image2']) && $_FILES['image2']['error']==0 )
        {
            $index_image=$this->upload_image('image2','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>11,'url'=>$index_image));
        }
        if(isset($_FILES['image3']) && $_FILES['image3']['error']==0 )
        {
            $share_image=$this->upload_image('image3','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>17,'url'=>$share_image));
        }
        if(isset($_FILES['image4']) && $_FILES['image4']['error']==0 )
        {
            $country_image=$this->upload_image('image4','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>18,'url'=>$country_image));
        }

        redirect(base_url("h5info_visa/h5list"));

    }


    /*
    * 主页面数据修改
    */
    public function to_sub()
    {
        set_time_limit(0);
        $h5_id=$this->input->post('h5_id',TRUE);
      // echo '<pre>';print_r($_POST);exit();


        $data['h5_title']=$this->input->post('h5_title',TRUE);
        $data['share_desc']=$this->input->post('share_desc',TRUE);
        $data['sta_price']=$this->input->post('sta_price',TRUE);
        $data['url_type']=$this->input->post('url_type',TRUE);
        //$video_src=$this->input->post('video_src',TRUE);
        if($video_src)
        {
            $data['video_src']=$video_src;
        }

        //报名须知
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

        //主数据更新
        $this->User_model->update_one(array('h5_id'=>$h5_id),$data,'v_h5_info');

        //图片更新
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
        if(isset($_FILES['image4']) && $_FILES['image4']['error']==0)
        {
            $this->User_model->update_one(array('link_id'=>$h5_id,'type'=>18),array('isdelete'=>1),'v_images');
            $head_image=$this->upload_image('image4','H5image');
            $this->User_model->user_insert('v_images',array('link_id'=>$h5_id,'type'=>18,'url'=>$head_image));
        }

        redirect(base_url("h5info_visa/edit_index/$h5_id"));

    }


    /*
    * 签证列表
    */
    public function visa_list($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }
        //获取签证列表信息
        $where = " h5_id=$id AND is_del=0 ";
        $visa_list = $this->User_model->get_select_all($select='*',$where,$order_title='visa_id',$order='ASC',$table='v_h5_visa');

        if($visa_list)
        {
            $data['visa_list'] = $visa_list;
            foreach ($visa_list as $k => $v) {
                $data['visa_list'][$k]['edit_url'] = base_url("h5info_visa/visa_edit/$v[visa_id]");
                $data['visa_list'][$k]['del_url'] = base_url("h5info_visa/visa_del/$v[visa_id]");
                $data['visa_list'][$k]['detail_url'] = base_url("h5show_visa/visa_detail/$v[visa_id]");
            }
        }

        $data['visa_add_url']=base_url("h5info_visa/visa_add/$id");

        $this->load->view('h5/visa_list',$data);

    }

    /*
    * 签证添加
    */
    public function visa_add($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/visa_add"));
        }

        $data['sub_url']=base_url("h5info_visa/visa_insert/$id");

        $this->load->view('h5/visa_add',$data);

    }

    /*
    * 签证修改
    */
    public function visa_edit($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        //获取签证信息
        $data['visa_info'] = $this->User_model->get_select_one($select='*',array('visa_id'=>$id,'is_del'=>0),$table='v_h5_visa');

        $data['sub_url']=base_url("h5info_visa/visa_sub/$id");

        $this->load->view('h5/visa_edit',$data);

    }

    /*
    * 签证数据提交
    */
    public function visa_insert($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }
        //echo '<pre>'; print_r($_POST);die;
        $data['h5_id']=$id;
        $data['country']=$this->input->post('country',TRUE);
        $data['place']=$this->input->post('place',TRUE);
        $data['visa_title']=str_replace(',', '，', $this->input->post('visa_title',TRUE));
        $data['price']=$this->input->post('price',TRUE);
        $data['visa_type']=$this->input->post('visa_type',TRUE);
        $data['visa_long']=$this->input->post('visa_long',TRUE);
        $data['visa_times']=$this->input->post('visa_times',TRUE);
        $data['visa_days']=$this->input->post('visa_days',TRUE);
        $data['visa_date']=$this->input->post('visa_date',TRUE);
        $data['visa_range']=$this->input->post('visa_range',TRUE);
        $data['visa_link']=$this->input->post('visa_link',TRUE);
        $content=$this->input->post('content',TRUE);
        $data['visa_content']=$content ? $content : '';

        //图片更新
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['visa_image']=$this->upload_image('image','H5image');
        }

        //添加签证信息
        $visa_id=$this->User_model->user_insert('v_h5_visa',$data);

        redirect(base_url("h5info_visa/visa_list/$id"));

    }

    /*
    * 签证修改数据提交
    */
    public function visa_sub($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        $data['country']=$this->input->post('country',TRUE);
        $data['place']=$this->input->post('place',TRUE);
        $data['visa_title']=str_replace(',', '，', $this->input->post('visa_title',TRUE));
        $data['price']=$this->input->post('price',TRUE);
        $data['visa_type']=$this->input->post('visa_type',TRUE);
        $data['visa_long']=$this->input->post('visa_long',TRUE);
        $data['visa_times']=$this->input->post('visa_times',TRUE);
        $data['visa_days']=$this->input->post('visa_days',TRUE);
        $data['visa_date']=$this->input->post('visa_date',TRUE);
        $data['visa_range']=$this->input->post('visa_range',TRUE);
        $data['visa_link']=$this->input->post('visa_link',TRUE);
        $content=$this->input->post('content',TRUE);
        $data['visa_content']=$content ? $content : '';

        //图片更新
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['visa_image']=$this->upload_image('image','H5image');
        }

        //更新签证信息
        $this->User_model->update_one(array('visa_id'=>$id,'is_del'=>0),$data,'v_h5_visa');

        redirect(base_url("h5info_visa/visa_edit/$id"));

    }

    /*
    * 签证删除
    */
    public function visa_del($id)
    {
        if($id)
        {
            $this->User_model->update_one(array('visa_id'=>$id),array('is_del'=>'1'),$table='v_h5_visa');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /*
    * 旅游产品列表
    */
    public function goods_list($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }
        //获取签证列表信息
        $where = " h5_id=$id AND is_del=0 ";
        $goods_list = $this->User_model->get_select_all($select='*',$where,$order_title='goods_id',$order='ASC',$table='v_h5_goods');

        if($goods_list)
        {
            $data['goods_list'] = $goods_list;
            foreach ($goods_list as $k => $v) {
                $data['goods_list'][$k]['edit_url'] = base_url("h5info_visa/goods_edit/$v[goods_id]");
                $data['goods_list'][$k]['del_url'] = base_url("h5info_visa/goods_del/$v[goods_id]");
                $data['goods_list'][$k]['detail_url'] = base_url("h5show_visa/goods_detail/$v[goods_id]");
            }
        }

        $data['goods_add_url']=base_url("h5info_visa/goods_add/$id");

        $this->load->view('h5/goods_list',$data);
    }

    /*
    * 旅游产品添加
    */
    public function goods_add($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        $data['sub_url']=base_url("h5info_visa/goods_insert/$id");

        $this->load->view('h5/goods_add',$data);

    }

    /*
    * 旅游产品修改
    */
    public function goods_edit($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        //获取旅游产品信息
        $data['goods_info'] = $this->get_goods_info($id);

        //echo '<pre>';
        //print_r($data['goods_info']);die;
        $data['sub_url']=base_url("h5info_visa/goods_sub/$id");
        $this->load->view('h5/goods_edit',$data);

    }

    /*
    * 旅游产品删除
    */
    public function goods_del($id)
    {
        if($id)
        {
            $this->User_model->update_one(array('goods_id'=>$id),array('is_del'=>'1'),$table='v_h5_goods');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /*
    * 旅游产品数据提交
    */
    public function goods_insert($id)
    {
        if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        $data['h5_id']=$id;
        $data['trip_name']=$this->input->post('trip_name',TRUE);
        $data['trip_id']=$this->input->post('trip_id',TRUE);
        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['goods_price']=$this->input->post('goods_price',TRUE);
        $data['goods_link']=$this->input->post('goods_link',TRUE);

        //旅游产品图
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['goods_image']=$this->upload_image('image','H5image');
        }

        //旅游产品详情
        $goods_info = array();
        $view_name = $this->input->post('view_name',TRUE);
        $view_intro = $this->input->post('view_intro',TRUE);
        $view_tip = $this->input->post('view_tip',TRUE);


        if(count($view_name)>=1)
        {
            for($i=0;$i<count($view_name);$i++)
            {
                $view_file_name='view_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                $goods_info[] = array(
                                'view_name' => $view_name[$i],
                                'view_intro' => $view_intro[$i],
                                'view_image' => $new_url,
                                'view_tip' => $view_tip[$i]
                );
            }
        }
        $data['goods_info']=json_encode($goods_info);

        //旅游产品提示信息
        $tip_info = array();
        $tip_title = $this->input->post('tip_title',TRUE);
        $tip_intro = $this->input->post('tip_intro',TRUE);

        if(count($tip_title)>=1)
        {
            for($i=0;$i<count($tip_title);$i++)
            {
                $tip_info[] = array(
                                'tip_title' => $tip_title[$i],
                                'tip_intro' => $tip_intro[$i]
                );
            }
        }
        $data['tip_info']=json_encode($tip_info);


        //旅游产品套餐信息
        $package_info = array();
        $package_title = $this->input->post('package_title',TRUE);
        $package_price = $this->input->post('package_price',TRUE);
        $package_intro = $this->input->post('package_intro',TRUE);

        if(count($package_title)>=1)
        {
            for($i=0;$i<count($package_title);$i++)
            {
                if($package_title[$i])
                {
                    $package_info[] = array(
                                    'package_title' => $package_title[$i],
                                    'package_price' => $package_price[$i],
                                    'package_intro' => $package_intro[$i]
                    );
                }
            }
        }
        $data['package_info']=json_encode($package_info);


        //添加旅游产品信息
        $visa_id=$this->User_model->user_insert('v_h5_goods',$data);

        redirect(base_url("h5info_visa/goods_list/$id"));

    }

    /*
    * 旅游产品修改数据提交
    */
    public function goods_sub($id)
    {
                if(!$id)
        {
            redirect(base_url("h5info_visa/h5list"));
        }

        //获取产品信息
        $where = 'goods_id='.$id.' AND is_del=0';
        $goods = $this->User_model->get_select_one($select='*',$where,'v_h5_goods');
        if(!$goods)
        {
            redirect(base_url("h5info/h5list"));
        }

        $data['trip_name']=$this->input->post('trip_name',TRUE);
        $data['trip_id']=$this->input->post('trip_id',TRUE);
        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['goods_price']=$this->input->post('goods_price',TRUE);
        $data['goods_link']=$this->input->post('goods_link',TRUE);
        $del_image=explode(',',$this->input->post('del_image_id',TRUE));

        //旅游产品图
        if(isset($_FILES['image']) && $_FILES['image']['error']==0)
        {
            $data['goods_image']=$this->upload_image('image','H5image');
            
        }


        //旅游产品详情
        $goods_info_new = array();
        $view_name = $this->input->post('view_name',TRUE);
        $view_intro = $this->input->post('view_intro',TRUE);
        $view_tip = $this->input->post('view_tip',TRUE);

        //景点信息
        $goods_info = json_decode($goods['goods_info']);
        if(count($view_name)>=1)
        {
            for($i=0;$i<count($view_name);$i++)
            {
                $view_file_name='view_img'.($i+1);
                $new_url=$this->more_pic_upload($view_file_name);
                $view_image = $this->merge_image($goods_info[$i]->view_image,$del_image,$new_url);
                //var_dump($view_image);
                $goods_info_new[] = array(
                                'view_name' => $view_name[$i],
                                'view_intro' => $view_intro[$i],
                                'view_image' => $view_image,
                                'view_tip' => $view_tip[$i]
                );
            }
        }
        $data['goods_info']=json_encode($goods_info_new);

        //旅游产品提示信息
        $tip_info = array();
        $tip_title = $this->input->post('tip_title',TRUE);
        $tip_intro = $this->input->post('tip_intro',TRUE);

        if(count($tip_title)>=1)
        {
            for($i=0;$i<count($tip_title);$i++)
            {
                $tip_info[] = array(
                                'tip_title' => $tip_title[$i],
                                'tip_intro' => $tip_intro[$i]
                );
            }
        }
        $data['tip_info']=json_encode($tip_info);


        //旅游产品套餐信息
        $package_info = array();
        $package_title = $this->input->post('package_title',TRUE);
        $package_price = $this->input->post('package_price',TRUE);
        $package_intro = $this->input->post('package_intro',TRUE);

        if(count($package_title)>=1)
        {
            for($i=0;$i<count($package_title);$i++)
            {
                if($package_title[$i])
                {
                    $package_info[] = array(
                                    'package_title' => $package_title[$i],
                                    'package_price' => $package_price[$i],
                                    'package_intro' => $package_intro[$i]
                    );
                }
            }
        }
        $data['package_info']=json_encode($package_info);

        //更新签证信息
        $this->User_model->update_one(array('goods_id'=>$id,'is_del'=>0),$data,'v_h5_goods');

        redirect(base_url("h5info_visa/goods_edit/$id"));

    }


    /*
    * 获取旅游产品信息
    */
    function get_goods_info($id)
    {
        $result = array();
        if($id)
        {
            $where = 'goods_id='.$id.' AND is_del=0';
            $goods = $this->User_model->get_select_one($select='*',$where,'v_h5_goods');
            if($goods)
            {
                $result = $goods;
                $result['goods_info'] = json_decode($goods['goods_info']);
                $result['tip_info'] = json_decode($goods['tip_info']);
                $result['package_info'] = json_decode($goods['package_info']);
            }
        }

        return $result;
    }
    /*
    * 不确定图片上传
    */
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

}