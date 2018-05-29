 <?php
/**
 * 用户
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->table = 'v_users';
    $this->load->model('User_model');
    $this->load->helper('url');
    $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
    $this->load->library('session');
    $this->load->helper('cookie');
   // $this->load->library('image_lib');
  }
  public function auth_add(){
    $data['list']=$this->User_model->get_select_all('*','pid=0','id','ASC','v_auth_menu');
    $this->load->view('admin_auth/auth_add',$data);
  }

  public function auth_insert(){
    $pid=$this->input->post('pid',true);
    $name=$this->input->post('name',true);
    $url=$this->input->post('url',true);
    $data=array(
        'pid'=>$pid,
        'name'=>$name,
        'url'=>$url
    );
    $this->User_model->user_insert('v_auth_menu',$data);
    redirect(base_url('auth/auth_add'));
  }

  public function auth_list(){
    $data['count_url']=$this->count_url;
    if(isset($_SESSION['admin_id'])){
      $admin_id=$_SESSION['admin_id'];
      $auth_id=$this->User_model->get_select_all('v_admin_role.auth_id',"admin_id=$admin_id",'admin_id','ASC','v_admin_role',1,'v_admin_user',"v_admin_role.role_id=v_admin_user.role_id");
      if($auth_id){
        $auth_id=$auth_id[0]['auth_id'];
        $sql="SELECT * FROM v_auth_menu WHERE id IN ($auth_id) ";
        $rs=$data['list']=$this->User_model->query_use($sql);
        $data['list']=$this->generateTree($rs);
        $rs=$this->User_model->get_count("is_display ='0'", 'v_report');
        $data['report_num']=$rs['count'];
        $data['son_list']=$this->User_model->get_select_all('*','1=1','displayorder','ASC','v_activity_terms');

        $this->load->view('admin_auth/auth_left',$data);
      }

    }else{
      echo '登录超时';
      echo '<meta http-equiv="refresh" content="2; url=/admin/login">';die;
    }


  }

  /*
   * 角色编辑插入
   */
public function admin_role_edit(){
  $admin_id=$this->input->post('admin_id',true);
  $role_id=$this->input->post('role_id',true);
  $this->User_model->update_one(array('admin_id'=>$admin_id),array('role_id'=>$role_id),$table='v_admin_user');
  redirect(base_url("auth/admin_role_change/{$admin_id}"));

}
/* 管理员角色改遍
       */
  public function admin_role_change($admin_id){
    $data['admin_id']=$admin_id;
    $rs=$this->User_model->get_select_one('role_id,admin_name',array('admin_id'=>$admin_id),'v_admin_user');
    $data['role_id']=$rs['role_id'];
    $data['admin_name']=$rs['admin_name'];
    $data['list']=$this->User_model->get_select_all('*','1=1','role_id','ASC','v_admin_role');
    //print_r($data);exit();

    $this->load->view('admin_auth/admin_role_change',$data);
  }

/*
 * 角色列表
 */
  public function role_list_show(){
    $data['role_list']=$this->User_model->get_select_all('*','1=1','role_id','ASC','v_admin_role');
    $data['auth_list']=$this->User_model->get_select_all('*','pid=0','id','ASC','v_auth_menu');
    $this->load->view('admin_auth/role_list',$data);
  }

  public function role_auth_edit(){
    $role_id=$this->input->post('role_id',true);
    $auth_id=$this->input->post('auth_id',true);
    $auth_id=implode(',',$auth_id);
   // echo "<pre>";
   // print_r($role_id);
   // print_r($auth_id);
    //exit();
    $this->User_model->update_one(array('role_id'=>$role_id),array('auth_id'=>$auth_id),'v_admin_role');
    redirect(base_url('auth/role_list_show'));
  }

  /*
   * 角色删除
   */

  public function role_del($role_id){
    $where=array('role_id'=>$role_id);
    $this->User_model->del($where,'v_admin_role');
    redirect(base_url('auth/role_list_show'));
  }
  public function generateTree($arrs){
    $tree =$arrs;

    $sql="SELECT * FROM v_auth_menu where pid>0 ";
    $rs=$this->User_model->query_use($sql);
    //echo "<pre>";print_r($rs);exit();
    foreach($tree as $k1=>$v1){
      foreach($rs as $k2=>$v2){
        if($v2['pid']==$v1['id']){
          $tree[$k1]['son'][]=$rs[$k2];
        }
      }
    }
    //echo "<pre>";print_r($tree);exit();
    return $tree;
  }

  public function role_add(){
    $data['list']=$this->User_model->get_select_all('id,name','pid=0','id','ASC','v_auth_menu');
    $this->load->view('admin_auth/role_add',$data);
  }
  public function admin_add(){
    $data['list']=$this->User_model->get_select_all('*','1=1','role_id','ASC','v_admin_role');
    $this->load->view('admin_auth/admin_add',$data);
  }
  public function auth_del($id){
    $this->User_model->del(array('id'=>$id),'v_auth_menu');
    redirect('auth/auth_list_show');
  }
  public function auth_list_show(){
    $rs=$this->User_model->get_select_all('*','pid=0','id','ASC','v_auth_menu');
    $data['list']=$this->generateTree($rs);
    //echo "<pre>";print_r( $data['list']);exit();
   $this->load->view('admin_auth/auth_left_show.php',$data);
  }

  public function role_insert(){
    $auth_id=$this->input->post('auth',true);
    $auth_id=implode(',',$auth_id);
    $name=$this->input->post('role_name',true);
    $data=array(
        'auth_id'=>$auth_id,
        'role_name'=>$name,
    );
    //echo "<pre>";print_r($data);exit();
    $this->User_model->user_insert('v_admin_role',$data);
    redirect(base_url('auth/role_add'));
  }
  public function admin_insert(){
    $admin_name=$this->input->post('name',true);
    //echo $admin_name;exit();
    $rs=$this->User_model->get_count("admin_name ='$admin_name' AND STATUS='0'", 'v_admin_user');
  //  echo $this->db->last_query();print_r($rs);exit();
    if($rs['count']>0){
      echo "该用户名已经存在";
      exit();
    }
    $password=$this->input->post('password',true);
    $salt=rand(0,9999);
    $password=md5(md5($password).$salt);
    $role_id=$this->input->post('role_id',true);

    $data=array(
        'admin_name'=>$admin_name,
        'password'=>$password,
        'role_id'=>$role_id,
        'salt'=>$salt,
        'add_time'=>time()
    );
    //echo "<pre>";print_r($data);exit();
    $this->User_model->user_insert('v_admin_user',$data);
    redirect(base_url('auth/admin_add'));
  }
  public function auth_url_sub(){
    $id=$this->input->post('id',true);
    $url=$this->input->post('url',true);
    $this->User_model->update_one(array('id'=>$id),array('url'=>$url),'v_auth_menu');
    echo 1;
  }

}