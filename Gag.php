<?php
/**
 * 禁言
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Gag extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->table = 'v_gag';
    $this->table_user = 'v_users';
    $this->load->model('Gag_model');
    $this->load->model('User_model');
    $this->load->model('Admin_model');
  }

  /**
   * [user_list 用户列表]
   * @param  integer $page [页数]
   * @return [type]        [description]
   */
	public function gag_list($page=1)
	{
     $data['title'] = trim($this->input->get('title'));
    if($data['title'])
    {
      $where = "(gag_id LIKE '%$data[title]%' OR user_id LIKE '%$data[title]%' )";
    }
    else
    {
      $where  = '1=1';
    }
    $page_num =10;
    $data['now_page'] = $page;
    $count = $this->Gag_model->get_count($where,'v_gag');
    $data['count'] = $count['count'];
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
      $page=1;
    $start = ($page-1)*$page_num;
    $data['list'] = $this->Gag_model->gag_list($where,$start,$page_num);
      //$data['time']=date('Y:m:d H:i:s',)
     //echo "<pre>";print_r($data);exit();
    foreach ($data['list'] as $key => $value) {
      $user_id_1 = $this->Admin_model->user_info($this->table_user,' user_id='.$value->user_id_1);
      $user_id_2 = $this->Admin_model->user_info($this->table_user,' user_id='.$value->user_id_2);
      $data['list'][$key]->user_name_1 = $user_id_1['user_name'];
      $data['list'][$key]->user_name_2 = $user_id_2['user_name'];
    }
		$this->load->view('user/gag_list',$data);
	}

  /**
   * [user_info 禁言详情]
   * @param  string $user_id [用户id]
   * @return [type]     [description]
   */
  public function gag_info($user_id='')
  {
   /* if(empty($user_id))
    {
        echo '非法操作，用户id 为空';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';
    }*/
    if(empty($user_id))
    {
      $this->load->view('user/user_info');
    }
    else
    {
      $data['info'] = $this->Admin_model->user_info($this->table_user,' user_id=' .$user_id);
      $this->load->view('user/user_info',$data);
    }

  }

  /**
   * [user_edit 编辑用户]
   * @param  string $user_id [用户id]
   * @return [type]          [description]
   */
  public function user_edit()
  {
    //修改
    $user_id=$this->input->post('user_id');
    $images = $this->upload_image("image","user");
    if($images)
    {
      $data['image'] = $images;
    }
    if(!empty($user_id))
    {
      $data = array(
               'user_name' => $this->input->post('user_name')
               );
      if($images)
      {
        $data['image'] = $images;
      }
      $num=$this->User_model->user_update($this->table,$data,$user_id);
      if($num)
      {
        echo '修改成功';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_info/'.$user_id.'">';die;
      }
      else
      {
        echo '修改失败';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_info/'.$user_id.'">';die;
      }
    }
    else
    {
      //添加
      $data = array(
               'user_name'     => $this->input->post('user_name'),'image'=> $images
               );
      $num=$this->User_model->user_insert($this->table,$data);
      if($num)
      {
        echo '添加成功';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';die;
      }
      else
      {
        echo '添加失败';
        echo '<meta http-equiv="refresh" content="1; url=/user/user_list">';die;
      }
    }
  }

  /**
   * [gag_del 取消禁言]
   * @param  string $gag_id [禁言id]
   * @return [type]         [description]
   */
  public function gag_del($gag_id='')
  {
    if(empty($gag_id))
    {
      echo '失败';
      echo '<meta http-equiv="refresh" content="1; url=/gag/gag_list">';
    }
    $num=$this->Gag_model->gag_del($gag_id);
    if($num)
    {
      echo '取消禁言成功';      
      echo '<meta http-equiv="refresh" content="1; url=/gag/gag_list">';
    }else
    {
       echo '取消禁言失败';
       echo '<meta http-equiv="refresh" content="1; url=/gag/gag_list">';
    }

  }

  /**
   * [image 图片上传]
   * @param  [type] $filename [description]
   * @param  [type] $fileurl  [description]
   * @return [type]           [description]
   */
 public function upload_image($filename,$fileurl)
 {
    /* 如果目标目录不存在，则创建它 */
    if (!file_exists('./public/images/'.$fileurl))
    {
      if (!mkdir('./public/images/'. $fileurl))
      {
        return false;
      }
    }
    $file = $_FILES[$filename];
    return $this->shangchuan($filename,$fileurl);
  }

  public function shangchuan($filename,$fileurl)
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
        $br = false;break;
    }
    if($br)
    {
      $key =time();
      $pic_url="./public/images/".$fileurl."/".$key.$br;
      move_uploaded_file($file['tmp_name'], $pic_url);
      return $pic_url;
    }
  }

}