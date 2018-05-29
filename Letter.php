<?php
/**
 * 私信
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Letter extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->table = 'v_letter';
    $this->table_user = 'v_users';
    $this->load->model('Letter_model');
    $this->load->model('User_model');
    $this->load->model('Admin_model');
  }

  /**
   * [user_list 私信列表]
   * @param  integer $page [页数]
   * @return [type]        [description]
   */
  public function letter_list($page=1)
  {
    $data['title'] = $this->input->get('title',true);
    if($data['title'])
    {
       $where = "(from_id LIKE '%$data[title]%' OR to_id LIKE '%$data[title]%' ) AND related = '0'";
    }
    else
    {
      $where  = "related = '1'";
    }
    $page_num =100;
    $data['now_page'] = $page;
    $count = $this->User_model->get_count($where,'v_letter');
    $data['max_page'] = ceil($count['count']/$page_num);
    if($page>$data['max_page'])
    {
      $page=1;
    }
    $start = ($page-1)*$page_num;
    $data['list'] = $this->Letter_model->letter_list($where,$start,$page_num);
    foreach ($data['list'] as $key => $value) {

        $to_name = $this->Admin_model->user_info($this->table_user,' user_id='.$value['to_id']);
        $from_name = $this->Admin_model->user_info($this->table_user,' user_id='.$value['from_id']);
        $data['list'][$key]['from_name'] =  $from_name['user_name'];
        $data['list'][$key]['to_name'] = $to_name['user_name'];
    }
    unset($key,$value);
    //var_dump($data);
    $this->load->view('user/letter_list',$data);
  }


  public function letter_info($letter_id='')
  {
    if(empty($letter_id))
    {
      $this->load->view('user/letter_info');
    }
    else
    {
      $data['list'] = $this->Letter_model->letter_info($letter_id);
      foreach ($data['list'] as $key => $value) {
        $to_name = $this->Admin_model->user_info('v_users', array('user_id'=>$value['to_id']));
        $from_name = $this->Admin_model->user_info('v_users',array('user_id'=>$value['from_id']));
        $data['list'][$key]['from_name'] =  $from_name['user_name'];
        $data['list'][$key]['to_name'] = $to_name['user_name'];
        $data['list'][$key]['rand_id'] = $value['rand_id'];
      }
      $this->load->view('user/letter_info',$data);
    }

  }


/**
 * [letter_del 删除私信]
 * @param  string $id      [对应letter_id 或者 rand_id ]
 * @param  string $type    [类型    1  删除一条  2 删除整条]
 * @return [type]          [description]
 */
  public function letter_del($id='', $type='')
  {

    if($type == 1)
    {
      $where = " letter_id = $id ";
    }
    else
    {
       $where = " letter_id = $id ";
    }
    $num=$this->Letter_model->letter_del($this->table,$where);
    if($num)
    {
      echo '删除成功';
      if ($type == 2)
      {
        echo '<meta http-equiv="refresh" content="1; url=/letter/letter_list">';
      }
      elseif($type ==1)
      {
        echo '<meta http-equiv="refresh" content="1; url=/letter/letter_list">';
      }
    }else
    {
      echo '删除失败';
      if ($type == 2)
      {
        echo '<meta http-equiv="refresh" content="1; url=/letter/letter_list">';
      }
      elseif($type==1)
      {
        echo '<meta http-equiv="refresh" content="1; url=/letter/letter_list">';
      }
      
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