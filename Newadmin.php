 <?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Newadmin extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        //session
        $this->load->library('session');
        $this->load->helper('url');
        $this->lang->load('log', 'english');
        $this->load->library('common');
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->web_url="http://new.cnzz.com/v1/login.php?siteid=1258510548";
        $this->down_count='http://mobile.umeng.com/apps ';
    $this->long_css=array('en-US','ko-kr','ko-KR','ja-JP','ja-jp','th-th','th-TH','en-us');
        //$this->load->library('priv');
        //权限
       // $this->load->library('priv');
        $this->load->model('User_model');
       // $this->load->model('User_Api_model');
        $this->load->model('Admin_model');
        $this->load->library('image_lib');
    }
    /******** 车用商品增删改查*********/

    //add edit 页面
    public function car_goods_add()
    {
        //$this->auth_or_no();

        $goods_id=$this->input->get('goods_id',TRUE);
        $data=[];
        // echo '<pre>';print_r($data);exit();
        $data['sub_url']="/newadmin/car_goods_insert";
        if($goods_id)
        {
            $data['sub_url']="/newadmin/car_goods_sub";
            $data['info']=$this->User_model->get_select_one('*',array('goods_id'=>$goods_id),'wx_new_goods');
            $data['info']['image_r']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'7','isdelete'=>0),'v_images');
            $data['info']['image_r']=isset($data['info']['image_r']['url'])?$data['info']['image_r']['url']:$data['info']['image_r']['url'];

            $data['info']['image_s']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'8','isdelete'=>0),'v_images');
            $data['info']['image_s']=isset($data['info']['image_s']['url'])?$data['info']['image_s']['url']:$data['info']['image_s']['url'];

            $data['info']['image_c']=$this->User_model->get_select_one('*',array('link_id'=>$goods_id,'type'=>'9','isdelete'=>0),'v_images');
            $data['info']['image_c']=isset($data['info']['image_c']['url'])?$data['info']['image_c']['url']:'';
        }
        $this->load->view('newadmin/car_goods_add',$data);
    }
    //增加提交
    public function car_goods_insert()
    {
        //echo'<pre>';print_r($_POST);

        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['ori_price']=$this->input->post('ori_price',TRUE);
        $data['oori_price']=$this->input->post('oori_price',TRUE);
        $data['is_show']=$this->input->post('is_show',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $data['addtime']=time();
        $goods_id=$this->User_model->user_insert('wx_new_goods',$data);
        if($_FILES['image_r']['error']==0 && $_FILES['image_s']['error']==0)
        {

            $image_r=$this->upload_image('image_r', 'car');
            $image_s=$this->upload_image('image_s', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'7','url'=>$image_r,'createdAt'=>time()));
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'8','url'=>$image_s,'createdAt'=>time()));

        }
        if($_FILES['image_c']['error']==0 )
        {
            $image_c=$this->upload_image('image_c', 'car');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'9','url'=>$image_c,'createdAt'=>time()));

        }

        return redirect("/newadmin/car_goods_add?goods_id=$goods_id");



    }

    //修改提交
    public function car_goods_sub()
    {
       // echo'<pre>';print_r($_POST);
        $data['goods_name']=$this->input->post('goods_name',TRUE);
        $data['type']=$this->input->post('type',TRUE);
        $data['ori_price']=$this->input->post('ori_price',TRUE);
        $data['oori_price']=$this->input->post('oori_price',TRUE);
        $data['is_show']=$this->input->post('is_show',TRUE);
        $data['content']=$this->input->post('content',FALSE);
        $data['addtime']=time();

        $goods_id=$this->input->post('goods_id',TRUE);
        if($_FILES['image_r']['error']==0)
        {
            $image_r=$this->upload_image('image_r', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'7'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'7','url'=>$image_r,'createdAt'=>time()));

        }
        if($_FILES['image_s']['error']==0)
        {
            $image_s=$this->upload_image('image_s', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'8'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'8','url'=>$image_s,'createdAt'=>time()));
        }

        if($_FILES['image_c']['error']==0 )
        {
            $image_c=$this->upload_image('image_c', 'car');
            $this->User_model->update_one(array('link_id'=>$goods_id,'type'=>'9'),array('isdelete'=>1),'v_images');
            $this->User_model->user_insert('v_images',array('link_id'=>$goods_id,'type'=>'9','url'=>$image_c,'createdAt'=>time()));

        }
        $this->User_model->update_one(array('goods_id'=>$goods_id),$data,'wx_new_goods');
        return redirect("/shopadmin/car_goods_add?goods_id=$goods_id");
    }

    //列表
    public function car_goods_list($page=1)
    {
        $where='1=1';
        $is_show=$this->input->get('is_show');
        if($is_show==2)
        {
            $where.=" AND is_show=2";
        }else{
            $where.=" AND  is_show =1";
        }
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $is_off= $data['is_off']= $this->input->get('is_off',true);
   
  /**      if(!$is_off){
            $is_off=0;
        }
        $wheres=' 1=1 ';


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

**/
        $page_num =5;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'wx_new_goods');
        $data['max_page'] = ceil($count['count']/$page_num);
        //print_r($data['max_page']);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

     /**   $data['info']=$this->User_model->get_select_all($select='*',$where,$order_title='displayorder',$order='ASC',$table='wx_new_goods');
    
      
        //  print_r($data['info']);
        foreach($data['info'] as $k=>$v)
        {
            $data['info'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['info'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['info'][$k]["edit_url"]="/newadmin/car_goods_add?goods_id=$v[goods_id]";
        }
        $data['order_url']=base_url('newadmin/car_order');
       // print_r($data);
 
 **/
  //  $data['list'] = $this->db->query("SELECT * FROM wx_new_goods ;");

        $data['list']=$this->User_model->get_goods_lists($where,'wx_new_goods',$order_title='displayorder',$order='ASC',$start,$page_num);
         
          foreach($data['list'] as $k=>$v)
        {
            $data['list'][$k]["url"]=base_url("goodsforcar/goods_detail?goods_id=$v[goods_id]");
            $data['list'][$k]["show_url"]="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            $data['list'][$k]["edit_url"]="/newadmin/car_goods_add?goods_id=$v[goods_id]";
        }
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['is_show']=$is_show ? $is_show : 1;
     
       // echo "<pre>";
      // print_r($data);
      //  echo "</pre>";
     $this->load->view('newadmin/car_goods_list',$data);
    

    }

    /*********************/

    /*
     * 后台frame
     */
    public function get_lan_user(){
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        return $lang;
    }
    public function get_lan_bydb($user_id){
        $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
        // echo $rs['lan'];
        return $rs['lan'];
    }
    public function new_lan_bydb($user_id){
        $rs=$this->User_model->get_select_one('lan',array('user_id'=>$user_id),'v_users');
        $lang = $rs['lan'];
        switch ($lang) {
            case 'zh-cn' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-CN' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-tw' :
                $this->lang->load('ft', 'english');
                break;
            case 'zh-TW' :
                $this->lang->load('ft', 'english');
                break;
            case 'ja-jp' :
                $this->lang->load('jp', 'english');
                break;
            case 'ja-JP' :
                $this->lang->load('jp', 'english');
                break;
            case 'ko-kr' :
                $this->lang->load('hy', 'english');
                break;
            case 'ko-KR' :
                $this->lang->load('hy', 'english');
                break;
            case 'th-th' :
                $this->lang->load('th', 'english');
                break;
            case 'th-TH' :
                $this->lang->load('th', 'english');
                break;
            default:
                $this->lang->load('eng', 'english');
                break;
        }

    }

    public function new_lan_byweb(){
        preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        $lang = $matches[1];
        switch ($lang) {
            case 'zh-cn' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-CN' :
                $this->lang->load('jt', 'english');
                break;
            case 'zh-tw' :
                $this->lang->load('ft', 'english');
                break;
            case 'zh-TW' :
                $this->lang->load('ft', 'english');
                break;
            case 'ja-jp' :
                $this->lang->load('jp', 'english');
                break;
            case 'ja-JP' :
                $this->lang->load('jp', 'english');
                break;
            case 'ko-kr' :
                $this->lang->load('hy', 'english');
                break;
            case 'th-th' :
                $this->lang->load('th', 'english');
                break;
            case 'th-TH' :
                $this->lang->load('th', 'english');
                break;
            case 'ko-KR' :
                $this->lang->load('hy', 'english');
                break;
            default:
                $this->lang->load('eng', 'english');
                break;
        }

    }

    public function index()
    {
        if(empty($_SESSION['admin_id']))
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
        $this->load->view('newadmin/index');
    }
    public function top(){
        if(empty($_SESSION['admin_id']))
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }else{
            $admin_id=$_SESSION['admin_id'];
            $data=$this->User_model-> get_select_one('admin_name',array('admin_id'=>$admin_id),$table='v_admin_user');
            $this->load->view('newadmin/top',$data);
        }

    }
    public function  left()
    {
        $data['count_url']=$this->count_url;
        if(isset($_SESSION['admin_id'])){
            $admin_id=$_SESSION['admin_id'];
            $auth_id=$this->User_model->get_select_all('v_admin_role.auth_id',"admin_id=$admin_id",'admin_id','ASC','v_admin_role',1,'v_admin_user',"v_admin_role.role_id=v_admin_user.role_id");
            if($auth_id){
               // $data['title_arr']=$this->User_model->get_select_all('*',"status='1'",'displayorder', 'ASC','v_activity_terms');
                //$data['title_arr2']=$this->User_model->get_select_all('*',"is_show='1' AND is_set='0' AND special='0' AND pid ='0'",'displayorder', 'ASC','v_activity_father');
                $data['title_arr3']=$this->User_model->get_select_all('*',"is_show='1' AND is_set='0' AND special='1' AND pid='0'",'displayorder', 'ASC','v_activity_father');

                $auth_id=$auth_id[0]['auth_id'];
                $sql="SELECT * FROM v_auth_menu WHERE id IN ($auth_id) ";
                $rs=$data['list']=$this->User_model->query_use($sql);
                $data['list']=$this->generateTree($rs);
                $rs=$this->User_model->get_count("is_display ='0'", 'v_report');
                $data['report_num']=$rs['count'];
                $data['son_list']=$this->User_model->get_select_all('*','1=1','displayorder','ASC','v_activity_terms');
                $data['web_url']=  $this->web_url;
                $data['down_count']=$this->down_count;
                $this->load->view('newadmin/left',$data);
            }

        }else{
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    public function main($page=1)
    {
        if(isset($_SESSION['admin_id'])){

            $data['title'] = trim($this->input->get('title',true));
            $data['time1']=strtotime($this->input->get('time1',true));
            $data['time2']=strtotime($this->input->get('time2',true));
            $data['type']=trim($this->input->get('type',true));
            $data['see']=trim($this->input->get('see',true));
            $where="is_display='0'";

            if($data['time1']){
                $where.=" AND report_time >=$data[time1]";
            }
            if($data['time2']){
                $data['time2']+=86400;
                $where.="  AND report_time <=$data[time2]";
            }

            if($data['title'])
            {
                if($data['type']==1){
                    $where.= " AND address LIKE '%$data[title]%' ";
                }elseif($data['type']==2){
                    $where.= " AND (u1.user_name LIKE '%$data[title]%' OR u2.user_name LIKE '%$data[title]%'  )";
                }elseif($data['type']==3){
                    $where.= " AND title LIKE '%$data[title]%' ";
                }
            }else{
                $data['type']=0;
            }
            if($data['see']==null || $data['see']==0){
                $data['see']=0;
                $where.="  AND see = '0' AND v_report.video_id!=0";

            }elseif($data['see']==1){
                $where.="  AND see = '1' AND v_report.video_id!=0";
            }


            $page_num =100;
            $data['now_page'] = $page;
            $count = $this->User_model->get_report_count($where);

            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list']=$this->User_model->get_report_list($where,$start, $page_num);
            $data['time2']=strtotime($this->input->get('time2',true));
            //echo "<pre>";print_r($data);exit();
            $this->User_model->update_one(array('see'=>'0'),array('see'=>'1'),$table='v_report');
            $this->load->view('newadmin/main',$data);
            // $this->load->view('newadmin/report',$data);
        }else{
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }


    }

    public function change_user_order(){
        $user_id=$this->input->post('user_id',TRUE);
        $order=$this->input->post('order',TRUE);
        $this->User_model->update_one(array('user_id'=>$user_id),array('displayorder'=>$order),$table='v_users');
    }


    /*
     * 登录界面
     */
    public function login()
    {
        if(isset($_SESSION['admin_id']))
        {
            echo "登录成功";
            echo '<meta http-equiv="refresh" content="1; url=/newadmin/index">';die;
        }
        $this->load->view('newadmin/login');
    }
    //后台登录验证
    public function act_login()
    {
        $admin_name =  $this->input->post('username',true);
        $password   =  $this->input->post('password',true);
        if(empty($admin_name))
        {
            echo '登录名称不能为空';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
        if(empty($password))
        {
            echo '密码不能为空';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
        $data = $this->Admin_model->check_admin_new($admin_name,$password);
        if($data)
        {
            $_SESSION['admin_id']=$data['admin_id'];
            $_SESSION['admin_name']=$data['admin_name'];
            $data1['admin_name'] = $data['admin_name'];
            $data1['admin_id'] = $data['admin_id'];
            $data1['role_id'] = $data['role_id'];
            $this->put_admin_log("管理员登录");
            //$data1['action_list'] = $data->action_list;
            // $this->admin_log($data1['admin_name'],'menu_user','login');
            echo '<meta http-equiv="refresh" content="0; url=/newadmin/index">';die;
        }
        else
        {
            echo '登录失败';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    /*
     * 退出登录
     */
    public function logout()
    {
        $this->put_admin_log("管理员退出");
        unset($_SESSION['admin_id']);

       echo '<meta http-equiv="refresh" content="1; url=/newadmin/login">';
    }
    //地接关联商户
    public function all_and_user(){
        $this->auth_or_no();
        $data['info']=$this->User_model->get_select_all($select='v_users.user_id,v_users.user_name',array('id_business_status'=>'2','is_temp'=>'0'),$order_title='user_id',
            $order='ASC',$table='v_auth_business',$left=1,$left_table='v_users',$left_title="v_users.user_id=v_auth_business.user_id");
        $this->load->view('activity/all_one',$data);
    }

    public function all_one_insert(){
        $user_id=$this->input->post('user_id',true);
        $users=$this->input->post('users',true);
        $rs=$this->User_model->get_select_one('user_id',array('user_id'=>$user_id),'v_all_one_users');
        if($rs===0){
            $this->User_model->user_insert($table='v_all_one_users',array('user_id'=>$user_id,'users'=>$users));
            redirect(base_url('newadmin/all_and_user'));
        }else{
            echo '已有该用户关联信息，不可添加，可以修改';
           // sleep(3);
            //redirect(base_url("newadmin/all_one_edit/{$user_id}"));
        }

    }

    /*
     * 密码修改
     */
    public function password_edit_adv(){
        if(isset($_SESSION['admin_id'])){

            $admin_id=$_SESSION['admin_id'];
            $where=array('admin_id'=>$admin_id);
            $data=$this->User_model->get_select_one($select='admin_name',$where,'v_admin_user');
            unset($_COOKIE);
            $this->load->view('newadmin/admin_password_edit',$data);

        }else{
            return false;
        }
    }

    public function password_edit_sub(){
        if(isset($_SESSION['admin_id'])){
            $admin_id=$_SESSION['admin_id'];

            $where=array('admin_id'=>$admin_id);
            $old_pass=$this->input->post('password_old',true);
            $new_pass=$this->input->post('password_new',true);
            $data=$this->User_model->get_select_one('password,salt',$where,'v_admin_user');
            if($data['password']==md5(md5($old_pass).$data['salt'])){
                $new_pass=md5(md5($new_pass).$data['salt']);
                $this->User_model->update_one($where,array('password'=>$new_pass),'v_admin_user');
                $this->put_admin_log("管理员密码修改");
                unset($_SESSION['admin_id']);
                redirect(base_url('newadmin/index'));
            }else{
                echo '旧密码错误';
            }
        }else{
            return false;
        }
    }
    public function set_rec(){
        $video_id=$this->input->post('video_id',true);
        $val=$this->input->post('val',true);

        if($this->User_model->update_one(array('video_id'=>$video_id), array('is_rec'=>$val),$table='v_video'))
        {
         echo 1;
        }
    }

    /*
     * 所有直播视频
     */
    public function live_video_list($page=1)
    {
        if(isset($_SESSION['admin_id'])){
            $data['title'] = trim($this->input->get('title'));
            $data['time1']=strtotime($this->input->get('time1'));
            $data['time2']=strtotime($this->input->get('time2'));
            $data['type']=trim($this->input->get('type'));
            $this->User_model->ban_user_del_all();

            // $where=' is_off<2';
            $where  = " is_off=0 ";
            if($data['time1']){
                $where.=" AND start_time >=$data[time1]";
            }
            if($data['time2']){
                $data['time2']+=86400;
                $where.="  AND start_time <=$data[time2]";
            }

            if($data['title'])
            {
                if($data['type']==1){
                    $where.= " AND v_video.address LIKE '%$data[title]%' ";
                }elseif($data['type']==2){
                    $where.= " AND v_users.user_name LIKE '%$data[title]%' ";
                }elseif($data['type']==3){
                    $where.= " AND v_video.title LIKE '%$data[title]%' ";
                }else{
                    $data['type']=0;
                }
            }else{
                $data['type']=0;
            }

            $page_num =100;
            $data['now_page'] = $page;
            $count = $this->User_model->get_video_count($where,'v_video');
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list'] = $this->User_model->video_list($where,$start,$page_num,'v_video');
            //echo $this->db->last_query();exit();
            $order_count=$this->User_model->get_count("is_off=0 AND display_order<30000", 'v_video');
            $data['order']=$order_count['count'];
            $data['time2']=strtotime($this->input->get('time2'));
            $this->load->view('newadmin/video_list',$data);
        }else{
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }

    }
    /*
     * 100年大礼包
     * 3153600000
     */
    public function ban_ever(){
        $user_id=$this->input->post_get('user_id',true);
        $video_id=$this->input->post_get('video_id',true);
        $row=$this->User_model->get_count(array('user_id'=>$user_id,'statue'=>'0'), 'v_ban_user');
        $rs=$this->User_model->get_select_one('video_name,start_time',array("video_id"=>$video_id),'v_video');
        $time=time()-$rs['start_time'];
        if($time<60){
            echo 1;
        }else{
            if($row['count']==0){
                $time=time()+3153600000;
                $data=array(
                    'user_id'=>$user_id,
                    'ban_in_time'=>time(),
                    'ban_out_time'=>$time,
                    'is_show'=>'1',
                    'is_socket'=>'1',
                    'is_letter'=>'1'
                );
                $this->User_model->user_insert('v_ban_user',$data);
                $this->User_model->update_one(array("video_id"=>$video_id),array('is_off'=>3,'display_order'=>30000),'v_video');

                $this->put_admin_log("永久封禁用户 $user_id");
                $url=$this->getstopurl($rs['video_name']);
                $this->https_request($url);
                $this->new_lan_bydb($user_id);
                $info=$this->lang->line('sys_yjfj');
                $this->push_sys($user_id,$info);
                echo 0;
            }else{
                echo 2;
            }
        }

    }
    /*
 * 禁播（86400 //604800//2592000//31536000//3153600000）
 */

    public function ban_show()
    {
        $user_id=$this->input->post('user_id',true);
        $video_id=$this->input->post('video_id',true);
        $row=$this->User_model->get_count(array('user_id'=>$user_id,'statue'=>'0'), 'v_ban_user');
        $rs=$this->User_model->get_select_one('video_name,start_time',array("video_id"=>$video_id),'v_video');
        $time_live=time()-intval($rs['start_time']);
        if($time_live<60)
        {
            echo 0;
        }
        else
        {
            $val=$this->input->post('val',true);
            if($val==0){return;}
            elseif($val==1){$ban_time=86400;}
            elseif($val==2) {$ban_time=604800;}
            elseif($val==3){$ban_time=2592000;}
            elseif($val==4){$ban_time=31536000;}
            elseif($val==5){$ban_time=3153600000;}
            $time=time()+$ban_time;
            if($row['count']==0)
            {
                $data=array(
                    'user_id'=>$user_id,
                    'ban_in_time'=>time(),
                    'ban_out_time'=>$time,
                    'is_show'=>'1',

                );
                $ban_time=date('Y年m月d日 H:i:s',$time);
                $this->User_model->user_insert('v_ban_user',$data);
                /*$row=$this->User_model->get_select_all('device_id,type',array('user_id'=>$user_id),'phone_id', 'ASC','v_device');
                if(!empty($row))
                {
                    $info="抱歉，您已被系统禁播到{$ban_time},如有疑问，请联系客服!";
                    $this->pushinfo($row,$info);
                }*/

                //$info="抱歉，您已被系统禁播到{$ban_time},如有疑问，请联系客服!";

                $this->User_model->update_one(array("video_id"=>$video_id),array('is_off'=>3,'display_order'=>30000),'v_video');
                $this->put_admin_log("禁播用户 $user_id");
                $url=$this->getstopurl($rs['video_name']);
                $this->https_request($url);

                $this->new_lan_bydb($user_id);
                $info=$this->lang->line('sys_jb1').','.$ban_time.$this->lang->line('sys_jb2');

                $this->push_sys($user_id,$info);
                echo $val;

            }
            else
            {
                echo 6;
            }
        }

    }


    /*
     * 后台停播视频
     */
    public function stop_show()
    {
        $video_id=$this->input->post_get('video_id',true);
        $rs=$this->User_model->get_select_one('video_name,start_time',array("video_id"=>$video_id),'v_video');
        $time=time()-$rs['start_time'];
        if($time<60)
        {
            echo 0;
        }
        else
        {
            $this->User_model->update_one(array("video_id"=>$video_id),array('is_off'=>3,'display_order'=>30000),'v_video');
            $url=$this->getstopurl($rs['video_name']);
            $fh=$this->https_request($url);
            $this->put_admin_log("停播视频$video_id");
            //echo $rs['video_name'].'<br>'.$url.'<br>'.$fh;
//		redirect(base_url('admin/ban_video_list'));
            echo 1;
        }
    }

    public function order_top()
    {
        $video_id=$this->input->post('video_id',true);
        $val=$this->input->post('val',true);
        $this->put_admin_log("排序视频;序列$val;$video_id");
        if($val==30000){
            $this->User_model->update_one(array("video_id"=>$video_id),array('display_order'=>$val),'v_video');
            echo 3;
        }else{
            $row=$this->User_model->get_count(array('display_order'=>$val), 'v_video');
            if($row['count']==0){
                $this->User_model->update_one(array("video_id"=>$video_id),array('display_order'=>$val),'v_video');
                echo 2;
            }else{
                echo 1;
            }
        }

    }

    /*
     * 活动关联视频
     */
    public function act_and_video($page=1)
    {
        if(isset($_SESSION['admin_id']))
        {
            $data['title'] = trim($this->input->get('title',true));
            $data['time1']=strtotime($this->input->get('time1',true));
            $data['time2']=strtotime($this->input->get('time2',true));
            $data['type']=trim($this->input->get('type',true));
            $where=' is_off<2';
            if($data['time1'])
            {
                $where.=" AND start_time >=$data[time1]";
            }
            if($data['time2'])
            {
                $data['time2']+=86400;
                $where.="  AND start_time <=$data[time2]";
            }

            if($data['title'])
            {
                if($data['type']==1){
                    $where.= " AND v_video.address LIKE '%$data[title]%' ";
                }elseif($data['type']==2){
                    $where.= " AND v_users.user_name LIKE '%$data[title]%' ";
                }elseif($data['type']==3){
                    $where.= " AND v_video.title LIKE '%$data[title]%' ";
                }
            }
            else
            {
                $data['type']=0;
            }
            $page_num =40;
            $data['now_page'] = $page;
            $count = $this->User_model->get_video_count($where,'v_video');
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list'] = $this->User_model->video_list($where,$start,$page_num,'v_video','start_time');
            $order_count=$this->User_model->get_count(" is_off<2 AND display_order<30000", 'v_video');
            $data['order']=$order_count['count'];
            //echo "<pre>";print_r($data);exit();
            $data['fatherself_activity']=$this->User_model->get_select_all('act_id,title',"is_show='1' AND is_set='1' AND special='0' ",'start_time','DESC','v_activity_father');
            $rsterms=$this->User_model->get_select_all('act_id',"special='1' AND is_set='0'AND is_show='1' ",'act_id','ASC','v_activity_father');
            $terms=array();
            foreach($rsterms as $k=>$v){
                $terms[]=$v['act_id'];
            }
            $terms=implode(',',$terms);
            if($this->input->get('test')){
                print_r($terms);exit();
            }
            $where="is_show='1' AND (pid IN($terms) OR special='2') AND act_status='2' AND is_temp='0'";
            $data['son_activity']=$rs=$this->User_model->get_select_all('act_id,title',$where,'act_id','ASC','v_activity_children');
            $data['time2']=strtotime($this->input->get('time2'));
            //$this->User_model->get_select_one($select='*',$where='1=1',$table='v_auth_users',$ororder='0');
            $this->load->view('newadmin/act_video_list',$data);
        }else{
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    //车载产品序列
    public function car_order()
    {
            $goods_id=$this->input->post('goods_id');
          //  echo $goods_id;
            $order=intval($this->input->post('order'));

            $this->User_model->update_one(array('goods_id'=>$goods_id),array('displayorder'=>$order),'wx_new_goods');
    }

    /*
     *  被禁用户列表
     */
    public function ban_user_list($page=1)
    {
        if(isset($_SESSION['admin_id']))
        {
            $this->User_model->ban_user_del_all();
            $data['title'] = trim($this->input->get('title',true));
            $data['time1']=strtotime($this->input->get('time1',true));
            $data['time2']=strtotime($this->input->get('time2',true));
            $data['type']=trim($this->input->get('type',true));
            $where  = "statue ='0' ";
            if($data['time1'])
            {
                $where.=" AND ban_in_time >=$data[time1]";
            }
            if($data['time2'])
            {
                $data['time2']+=86400;
                $where.="  AND ban_in_time <=$data[time2]";
            }
            if($data['title'])
            {
                if($data['type']==1)
                {
                    $where.= " AND v_users.user_name LIKE '%$data[title]%' ";
                }
                elseif($data['type']==2)
                {
                    $where.= " AND v_users.user_name LIKE '%$data[title]%' ";
                }
                elseif($data['type']==3)
                {
                    $where.= " AND v_users.user_name LIKE '%$data[title]%' ";
                }
            }
            else
            {
                $data['type']=0;
            }
            $data['title'] = trim($this->input->get('title',true));
            $page_num =100;
            $data['now_page'] = $page;
            $count = $this->User_model->get_ban_count($where,'v_ban_user');
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list'] =$this->User_model->get_ban_info($where,$start,$page_num);
            //echo "<pre>";print_r($data);exit();
            $data['time2']=strtotime($this->input->get('time2',true));
            $this->load->view('newadmin/ban_user_list',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }

    }

    //违规视频列表

    public function ban_video_list($page=1)
    {
        if(isset($_SESSION['admin_id']))
        {
            $data['title'] = trim($this->input->get('title',true));
            if($data['title'])
            {
                $where = " title LIKE '%$data[title]%' AND is_off=3 ";
            }
            else
            {
                //$where='1=1';
                $where  = " is_off=3 ";
            }
            $page_num =100;
            $data['now_page'] = $page;
            $count = $this->User_model->get_count($where,'v_video');
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;

            $data['list'] = $this->User_model->video_list($where,$start,$page_num,'v_video','start_time');
            $order_count=$this->User_model->get_count("display_order<30000", 'v_video');
            $data['order']=$order_count['count'];
            if($data['list']>0)
            {
                foreach($data['list'] as $k=>$v)
                {
                    $re=$this->User_model->get_select_one('*',"user_id=$v[user_id] AND statue='0'",'v_ban_user');
                    if($re!=0)
                    {
                        $data['list'][$k]['ban_id']=$re['ban_id'];
                        $re['ban_in_time']=date('Y-m-d H:i:s',$re['ban_in_time']);
                        $data['list'][$k]['ban_in_time']=$re['ban_in_time'];
                        $re['ban_out_time']=date('Y-m-d H:i:s',$re['ban_out_time']);
                        $data['list'][$k]['ban_out_time']=$re['ban_out_time'];
                        $data['list'][$k]['is_show']=$re['is_show'];
                        $data['list'][$k]['is_socket']=$re['is_socket'];
                        $data['list'][$k]['is_letter']=$re['is_letter'];
                    }
                    else
                    {
                        $data['list'][$k]['ban_id']=0;
                    }
                }
            }

            //echo "<pre>";print_r($data);exit();
            $this->load->view('newadmin/ban_video_list',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }

    }




    public function re_test()
    {
        $count = $this->User_model->get_report_count();
        echo "<pre>";
        print_r($count);
    }
    /*
 * 举报列表
 */

    public function repoter_list($page=1)
    {
        if(isset($_SESSION['admin_id']))
        {
            $data['title'] = trim($this->input->get('title',true));
            $data['time1']=strtotime($this->input->get('time1',true));
            $data['time2']=strtotime($this->input->get('time2',true));
            $data['type']=trim($this->input->get('type',true));
            $data['see']=trim($this->input->get('see',true));
            $where="is_display='0'";
            if($data['time1'])
            {
                $where.=" AND report_time >=$data[time1]";
            }
            if($data['time2'])
            {
                $data['time2']+=86400;
                $where.="  AND report_time <=$data[time2]";
            }
            if($data['title'])
            {
                if($data['type']==1)
                {
                    $where.= " AND address LIKE '%$data[title]%' ";
                }
                elseif($data['type']==2)
                {
                    $where.= " AND (u1.user_name LIKE '%$data[title]%' OR u2.user_name LIKE '%$data[title]%'  )";
                }
                elseif($data['type']==3)
                {
                    $where.= " AND title LIKE '%$data[title]%' ";
                }
            }
            else
            {
                $data['type']=0;
            }

            if($data['see']==3)
            {
                $where.=' AND v_report.video_id=0';
            }
            else
            {
                $where.=' AND v_report.video_id!=0';
            }

            if($data['see']==null || $data['see']==0)
            {
                $data['see']=0;
                $where.="  AND see = '0'";

            }
            elseif($data['see']==1)
            {
                $where.="  AND see = '1'";
            }
            $page_num =100;
            $data['now_page'] = $page;
            $count = $this->User_model->get_report_count($where);
            $data['max_page'] = ceil($count['count']/$page_num);
            if($page>$data['max_page'])
            {
                $page=1;
            }
            $start = ($page-1)*$page_num;
            $data['list']=$this->User_model->get_report_list($where,$start, $page_num);
            $data['time2']=strtotime($this->input->get('time2',true));
            //echo "<pre>";print_r($data);exit();
            $this->User_model->update_one(array('see'=>'0'),array('see'=>'1'),$table='v_report');
            $this->load->view('newadmin/report',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    /*
     * 后台僵尸评论列表
     */
    public function comment_list($page=1)
    {
       $this->auth_or_no();
        $data['title'] = trim($this->input->get('title',true));
        $where='1=1';
        if($data['title'])
        {
            $where.= " AND comment LIKE '%$data[title]%' ";
        }


        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_rand_comment');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $data['list']=$this->User_model-> get_select_more('*',$where,$start, $page_num, 'c_id','DESC','v_rand_comment');
        $this->load->view('newadmin/commet',$data);
    }

    public function com_add()
    {
        $this->auth_or_no();
        $this->load->view('newadmin/commet_add');
    }

    public function com_insert()
    {
        $com=$this->input->post('com',true);
       $c_id=$this->User_model->user_insert('v_rand_comment',array('COMMENT'=>$com));
        $this->put_admin_log("添加僵尸评论$c_id");
        redirect(base_url('newadmin/comment_list'));
    }
    public function com_sub()
    {
        $com=$this->input->post('com',true);
        $c_id=$this->input->post('c_id',true);
        $this->User_model-> update_one(array('c_id'=>$c_id),array('COMMENT'=>$com),'v_rand_comment');
        $this->put_admin_log("修改僵尸评论$c_id");
        redirect(base_url('newadmin/comment_list'));
    }
    /*
     * 评论删除
     */

    public function com_del(){
        $c_id=$this->input->get('c_id',true);
        $page=$this->input->get('page',true);
        $this->User_model->del(array('c_id'=>$c_id),'v_rand_comment');
        $this->put_admin_log("删除僵尸评论$c_id");
        redirect(base_url("newadmin/comment_list/{$page}"));

    }

    public function com_edit(){
        $this->auth_or_no();
        $c_id=$this->input->get('c_id',true);
        $data=$this->User_model->get_select_one($select='*',array('c_id'=>$c_id),'v_rand_comment');
        $data['edit']=1;
        $this->load->view('newadmin/commet_add',$data);
    }

//
    public function push_sysinfo(){
        $data=array();
        $this->load->view('newadmin/push_sys_info',$data);
    }

    public function push_do()
    {
        $content=trim($this->input->post('content',true));
        $where='login=0';
        $data=$this->User_model->get_select_all('type,device_id',$where,$order_title='type',$order='ASC',$table='v_device');

        $this-> pushinfo($data,$content);
        redirect(baser_url('bussell/push_sysinfo'));
    }
    public function pushinfo($data,$info)
    {
        $token1=$token2= $sep = $type_1 = $type_2 = '';
        for($i=0;$i<count($data);$i++)
        {
            if($data[$i]['type'] == '1')
            {

                $token1 .= $sep.$data[$i]['device_id'];
                $sep =',';
                $type_1 = 1;

            }
            elseif($data[$i]['type'] == '2')
            {

                $token2 .= $sep.$data[$i]['device_id'];
                $sep =',';
                $type_2 = 2;
            }
        }
        //$token1 = '37499ba4d41b50a6811ba2aabcef53b2a54958793b8b1d90d86af8b533ef6d88,571ce7ae1aea82c982b5869559b95adb393307e3f4322e7beb50aff09558b728,9ddfa0de3772b41c75f295a83453a07f0d305e2677da7b5b9dfbb74024f627cb,efe3d3e9d536143f53825b39db1080bcbd9c59b3a0e96a39d8f9bacd65d9bc97';
        $url = "http://msg.umeng.com/api/send?sign=";
        $urlForSign = 'http://msg.umeng.com/api/send';
        $params['timestamp'] = time();
        $params['type'] = 'listcast';
        if($type_1 == 1)
        {
            $app_master_secret = $this->config->item('app_master_secret_ios');
            $params['appkey'] = $this->config->item('youmeng_apikey_ios');
            $aps['alert'] =  $info ;
            $aps['sound'] = '';
            $aps['content-available'] = 1;
            // $aps['video_info'] = $video_info;
            $payload['aps'] = $aps;
            $params['production_mode'] = FALSE;
            $params['payload'] = $payload;
            $params['device_tokens'] = $token1 ;
            $post_body = json_encode($params);
            $sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
            $array = $this->http_post_data($url . $sign, $post_body);
        }
        if($type_2 == 2)
        {
            $app_master_secret = $this->config->item('app_master_secret_android');
            $params['appkey'] = $this->config->item('youmeng_apikey_android');
            $payload['display_type'] = "notification";
            $payload['body'] = array('text'=>$info,'sound'=>'');
            $params['payload'] = $payload;
            $params['device_tokens']= $token2 ;
            $post_body = json_encode($params);
            $sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
            $array = $this->http_post_data($url . $sign, $post_body);
        }
    }
//url 列表
    public function share_url()
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        $where=" 1=1 ";
        if($data['title'])
        {
            if($data['type']==1){
                $where.= " AND title LIKE '%$data[title]%' ";
            }elseif($data['type']==2){
                $where.= " AND title LIKE '%$data[title]%' ";
            }elseif($data['type']==3){
                $where.= " AND title LIKE '%$data[title]%' ";
            }
        }
        else
        {
            $data['type']=0;
        }
       // $data['list']= $this->User_model->get_select_all($select='*',$where,'id','DESC','v_share_url');
            $data['list']=array(array( 'id'=>1,'title'=>'热门','url'=>'http://api.etjourney.com/video/see_hot'));
          // echo '<pre>';print_r($data);
        $this->load->view('video/newurl',$data);
    }


    //视频删除
    public function video_list_del()
    {
        if(isset($_SESSION['admin_id']))
        {
            $video_id=$this->input->get('video_id',true);
            $title=$this->input->get('title',true);
            $this->User_model->update_one(array('video_id'=>$video_id),array('is_off'=>4),'v_video');
            $type=$this->input->get('type',true);
            $this->put_admin_log("删除视频$video_id");
            if($type==1)
            {
                redirect(base_url("newadmin/live_video_list?title=$title&type=$type"));
            }
            elseif($type==3)
            {
                redirect(base_url("newadmin/act_and_video?title=$title&type=$type"));
            }
            else
            {
                redirect(base_url("newadmin/ban_video_list?title=$title&type=$type"));
            }
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    /*
     * 认证管理
     */
    //导游 未提交  1 审核中 2 审核通过 3审核未通过
    public function guide_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;

        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        $id_view_status= $data['id_view_status']= $this->input->get('id_view_status',true);
        if(!$id_view_status)
        {
            $id_view_status='1';
        }
        $where=' 1=1 ';

        if($data['time1'])
        {
            $where.=" AND id_auth_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND id_auth_time <=$data[time2]";
        }
        if($data['title'])
        {
            if($data['type']==1)
            {
                $where.= " AND id_range LIKE '%$data[title]%' ";
            }
            elseif($data['type']==2)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
            elseif($data['type']==3)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
        }
        else
        {
            $data['type']=0;
        }
        $where.="  AND id_view_status= '$id_view_status'";

        // echo $where;exit();
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_auth_views');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='view_id,user_id,user_name,auth_name,id_auth_time,id_style,id_num,id_view_status';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'id_auth_time','desc','v_auth_views');
        // echo $this->db->last_query();exit();

        //print_r($data);
        if( $data['id_view_status']==2)
        {
            $data['ed']=1;
        }
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/guide_manage',$data);

    }
    /*
 * 后台商户列表
 */
    public function business_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        $id_business_status= $data['id_business_status']= $this->input->get('id_business_status');
        if(!$id_business_status)
        {
            $id_business_status='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND id_auth_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND id_auth_time <=$data[time2]";
        }
        if($data['title'])
        {
            if($data['type']==1)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
            elseif($data['type']==2)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
            elseif($data['type']==3)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
        }
        else
        {
            $data['type']=0;
        }
        $where.="  AND id_business_status= '$id_business_status'";
        // echo $where;exit();
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_auth_business');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='business_id,user_id,user_name,shop_name,auth_name,id_auth_time,id_style,id_num,id_business_status';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'id_auth_time','DESC','v_auth_business');
        //print_r($data);3
        if( $data['id_business_status']==2)
        {
            $data['ed']=1;
        }
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/business_manage',$data);
    }

    /*
  * 后台地陪列表
  */
    public function locals_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;

        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        $id_local_status= $data['id_local_status']= $this->input->get('id_local_status',true);
        if(!$id_local_status)
        {
            $id_local_status='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND id_auth_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND id_auth_time <=$data[time2]";
        }
        if($data['title'])
        {
            if($data['type']==1)
            {
                $where.= " AND id_range LIKE '%$data[title]%' ";
            }
            elseif($data['type']==2)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
            elseif($data['type']==3)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
        }
        else
        {
            $data['type']=0;
        }
        $where.="  AND id_local_status= '$id_local_status'";

        // echo $where;exit();
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_auth_locals');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='local_id,user_id,user_name,auth_name,id_auth_time,id_style,id_num,id_local_status';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'id_auth_time','DESC','v_auth_locals');
        //print_r($data);
        if($data['id_local_status']==2)
        {
            $data['ed']=1;
        }
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/locals_manage',$data);
    }
//后台司机列表
    public function drivers_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $id_driver_status= $data['id_driver_status']= $this->input->get('id_driver_status',true);
        if(!$id_driver_status)
        {
            $id_driver_status='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND id_auth_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND id_auth_time <=$data[time2]";
        }
        if($data['title'])
        {
            if($data['type']==1)
            {
                $where.= " AND id_range LIKE '%$data[title]%' ";
            }
            elseif($data['type']==2)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
            elseif($data['type']==3)
            {
                $where.= " AND user_name LIKE '%$data[title]%' OR auth_name LIKE '%$data[title]%'";
            }
        }
        else
        {
            $data['type']=0;
        }
        $where.="  AND id_driver_status= '$id_driver_status'";

        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_auth_drivers');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='driver_id,user_id,user_name,auth_name,id_auth_time,id_style,id_num,id_driver_status';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'id_auth_time','DESC','v_auth_drivers');
        //print_r($data);
        if($data['id_driver_status']==2)
        {
            $data['ed']=1;
        }
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/drivers_manage',$data);
    }

    /*
   * 后台提现审核列表
   */
    public function withdraw_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $auth_status= $data['auth_status']= $this->input->get('auth_status',true);
        if(!$auth_status)
        {
            $auth_status='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND id_auth_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND id_auth_time <=$data[time2]";
        }
        if($data['title'])
        {
            $where.= " AND user_name LIKE '%$data[title]%' OR user_id LIKE '%$data[title]%'";
        }
        else
        {
            $data['type']=0;
        }
        $where.="  AND auth_status= '$auth_status'";
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_auth_users');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        //$data['list'] = $this->User_model->user_list($where,$start,$page_num);
        $select='auth_id,user_id,user_name,auth_money_time,auth_name,auth_status';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'auth_money_time');
        $data['time2']=strtotime($this->input->get('time2'));
        //echo "<pre>";echo $this->db->last_query();var_dump($data);exit();
        $this->load->view('newadmin/withdraw_manage',$data);
    }

     public function my_products_admin($page=1)
     {
         $data['user_id']=$user_id=$this->input->get('user_id',true);
         //$select="v_activity_children.act_id,v_activity_children.title,v_activity_children.tag,v_activity_children.banner_product as banner_image,v_goods.ori_price,v_goods.shop_price,v_goods.low,v_goods.goods_id,v_activity_children.special";
         $this->auth_or_no();
         $data['count_url']=$this->count_url;
         $data['title'] = trim($this->input->get('title',true));
         $data['time1']=strtotime($this->input->get('time1',true));
         $data['time2']=strtotime($this->input->get('time2',true));
         $is_show= $data['is_show']= $this->input->get('is_show',true);
         if($is_show==null)
         {
             $is_show='1';
             $data['is_show']='1';
         }
         //1210,2273,1862
         $where=" v_activity_children.special='3' AND v_goods.goods_id IS NOT NULL AND v_activity_children.user_id=$user_id";
         if($data['time1'])
         {
             $where.=" AND v_activity_children.add_time >=$data[time1]";
         }
         if($data['time2'])
         {
             $data['time2']+=86400;
             $where.="  AND v_activity_children.add_time <=$data[time2]";
         }
         if($data['title'])
         {
             $where.= " AND v_activity_children.title LIKE '%$data[title]%' ";
         }
         $where.="  AND v_activity_children.is_show= '$is_show'";

         // echo $where;exit();
         $page_num =100;
         $data['now_page'] = $page;
         $count = $this->User_model->get_products_count($where,'v_activity_children');
         $data['max_page'] = ceil($count['count']/$page_num);
         if($page>$data['max_page'])
         {
             $page=1;
         }
         $start = ($page-1)*$page_num;
         $select='v_activity_children.type,v_activity_children.user_id,v_activity_children.act_id,v_activity_children.title,v_activity_children.add_time,v_activity_children.displayorder,goods_id';
         // $data['list']=$this->User_model->get_products_list($select,$where,$start,$page_num);

         $data['list']=$this->User_model->get_products_list_all($select,$where,$start,$page_num,'1');

         $data['time2']=strtotime($this->input->get('time2'));
         if($this->input->get('test',TRUE)){
             echo '<pre>';

             print_r($data);exit();
         }
         $this->load->view('newadmin/my_products_list',$data);


     }
//日期过期 自动判定下架
    public function date_down_show($act_id)
    {

       // $act_id=$data['info']['act_id'];
        $data['goods']=$this->User_model->get_select_one('goods_id',array('act_id'=>$act_id,'is_show'=>'1'),'v_goods');
        $goods_id=$data['goods']['goods_id'];
        $select='v_goods_attr.goods_attr_id,v_goods_attr.attr_id,v_goods_attr.supply_info_one,v_goods_attr.goods_id,v_goods_attr.pid,v_goods_attr.attr_val,v_goods_attr.attr_price,v_attr.attr_name,v_attr.attr_type';
        $where=array('v_goods_attr.goods_id'=>$goods_id,'v_goods_attr.is_show'=>'1');
        $data['attr']=$this->User_model->get_select_all($select,$where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
        if(is_array($data['attr']))
        {
            foreach($data['attr'] as $k=>$v)
            {
                if($v['attr_type']==1)
                {
                    $temp[]=$v['attr_val'];
                }
            }
            if(end($temp)<time()+86400){
                $this->User_model-> update_one(array('act_id'=>$act_id),array('is_show'=>'2'),$table='v_activity_children');
            }
        }
    }


    //后台特价产品列表
    public function products_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $is_show= $data['is_show']= $this->input->get('is_show',true);
        if($is_show==null)
        {
            $is_show='1';
            $data['is_show']='1';
        }
        //1210,2273,1862
        $where="v_activity_children.special IN ('2','4','5','6','7') AND v_goods.is_show='1'";
        if($data['time1'])
        {
            $where.=" AND v_activity_children.add_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND v_activity_children.add_time <=$data[time2]";
        }
        if($data['title'])
        {
            $where.= " AND v_activity_children.title LIKE '%$data[title]%' ";
        }
        $where.="  AND v_activity_children.is_show= '$is_show'";

        // echo $where;exit();
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_products_count($where,'v_activity_children');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='v_activity_children.type,v_activity_children.user_id,v_activity_children.act_id,v_activity_children.special,v_activity_children.title,v_activity_children.add_time,v_activity_children.displayorder,goods_id';
       // $data['list']=$this->User_model->get_products_list($select,$where,$start,$page_num);

        $data['list']=$this->User_model->get_products_list_all($select,$where,$start,$page_num,'1');
        foreach( $data['list'] as $k=>$v)
        {
            $this->date_down_show($v['act_id']) ;
        }

        $data['time2']=strtotime($this->input->get('time2'));
        if($this->input->get('test',TRUE)){
            echo '<pre>';

            print_r($data);exit();
        }
        $this->load->view('newadmin/products_list',$data);

    }
    //选择app首页推荐
     public function app_for_index()
     {
        // $where="v_goods.is_show='1' AND ( (v_activity_children.is_show='1' AND v_activity_children.special IN ('2','4','5','6') ) OR (v_ts.is_show='1'))";
         $data['temp_products']=$this->User_model-> get_tsp_products($page_num=1000,$start=0);

         $data['products']=array();

         foreach($data['temp_products'] AS $k=>$v)
         {
             if (stristr($v['act_image'], 'http') === false)
             {
                 $data['temp_products'][$k]['act_image'] = $this->config->item('base_url') . ltrim($v['act_image'], '.');
             }
             if (stristr($v['ts_image'], 'http') === false)
             {
                 $data['temp_products'][$k]['ts_image'] = $this->config->item('base_url') . ltrim($v['ts_image'], '.');
             }

             if($v['act_id']>0)
             {
                 $data['products'][]=array(
                     'act_id'=>$v['act_id'],
                     'ts_id'=>'0',
                     'goods_id'=>$v['goods_id'],
                     'title'=>$v['act_title'],
                     'goods_buy'=>$v['act_order_sell'],
                     'image'=> $data['temp_products'][$k]['act_image'],
                     'ori_price'=>$v['ori_price'],
                     'oori_price'=>$v['oori_price'],
                     'front_price'=>'0',
                     'for_index'=>$v['act_index']
                 );
             }
             else
             {
                 $data['products'][]=array(
                     'ts_id'=>$v['ts_id'],
                     'act_id'=>'0',
                     'goods_id'=>$v['goods_id'],
                     'title'=>$v['ts_title'],
                     'goods_buy'=>$v['ts_order_sell'],
                     'image'=> $data['temp_products'][$k]['ts_image'],
                     'ori_price'=>$v['ori_price'],
                     'oori_price'=>$v['oori_price'],
                     'front_price'=>$v['front_price'],
                     'for_index'=>$v['ts_index']
                 );
             }
         }
         unset($data['temp_products']);
  //      echo $this->db->last_query();
         if($this->input->get('test'))
         {
             echo '<pre>';print_r($data);exit();
         }
         $data['sub_url']=base_url('newadmin/do_for_index');
         $this->load->view('newadmin/products_for_index_view',$data);

     }

     public function do_for_index()
     {
         $act=$this->input->post('act');
         $ts=$this->input->post('ts');
         $data_z=array("for_index"=>'0');
         $data_o=array("for_index"=>'1');
         if(is_array($act) OR is_array($ts))
         {

            $this->User_model->update_one("1=1",$data_z,$table='v_activity_children');
            $this->User_model->update_one("1=1",$data_z,$table='v_ts');
             if(is_array($act))
             {
                 $act_str=implode(',',$act);
                 $where=" act_id IN ($act_str)";
                 $this->User_model->update_one($where,$data_o,$table='v_activity_children');
             }

             if(is_array($ts))
             {
                 $ts_str=implode(',',$ts);
                 $where=" ts_id IN ($ts_str)";
                 $this->User_model->update_one($where,$data_o,$table='v_ts');
             }

         }
         redirect($_SERVER['HTTP_REFERER']);
     }

    //特价下架
    public function down_activity($act_id)
    {
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'2'),'v_activity_children');
        // echo $this->db->last_query();exit();
      //  echo $_SERVER['HTTP_REFERER'];exit();
        redirect($_SERVER['HTTP_REFERER']);
    }
    //特价删除
    public function del_activity($act_id)
    {
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_activity_children');
        // echo $this->db->last_query();exit();
      //  redirect(base_url("newadmin/products_list"));
        redirect($_SERVER['HTTP_REFERER']);
    }




    //特价上架
    public function up_activity($act_id)
    {
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'1'),'v_activity_children');
        // echo $this->db->last_query();exit();
       // redirect(base_url("newadmin/products_list"));
        redirect($_SERVER['HTTP_REFERER']);
    }

     //lp上架
     public function up_lp($ts_id)
     {
         $this->User_model->update_one(array('ts_id'=>$ts_id),array('is_show'=>'1'),'v_ts');
         // echo $this->db->last_query();exit();
         // redirect(base_url("newadmin/products_list"));
         redirect($_SERVER['HTTP_REFERER']);
     }


     //lp下架
     public function down_lp($ts_id)
     {
         $this->User_model->update_one(array('ts_id'=>$ts_id),array('is_show'=>'2'),'v_ts');
         // echo $this->db->last_query();exit();
         // redirect(base_url("newadmin/products_list"));
         redirect($_SERVER['HTTP_REFERER']);
     }

     //lp删除
     public function del_lp($ts_id)
     {
         $this->User_model->update_one(array('ts_id'=>$ts_id),array('is_show'=>'3'),'v_ts');
         // echo $this->db->last_query();exit();
         // redirect(base_url("newadmin/products_list"));
         redirect($_SERVER['HTTP_REFERER']);
     }

     //ts 改变排序
     public function order_ts()
     {
         $ts_id=$this->input->post('ts_id',TRUE);
         $order=$this->input->post('order',TRUE);
         $this->User_model->update_one(array('ts_id'=>$ts_id),array('displayorder'=>$order),'v_ts');
         // echo $this->db->last_query();exit();
         // redirect(base_url("newadmin/products_list"));
         //redirect($_SERVER['HTTP_REFERER']);
     }
    //操作log
    public function admin_log_list($page =1)
    {
        $this->auth_or_no();
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        //echo "<pre>";print_r($data);exit();
        $where=' 1=1';
        if($data['time1'])
        {
            $where.=" AND log_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND log_time <=$data[time2]";
        }
        if($data['title'])
        {
            $where.= " AND admin_name LIKE '%$data[title]%' ";
        }
        $where.="  AND  status='0'";
        $page_num =80;
        $data['now_page'] = $page;
        $count = $this->User_model->get_admin_log_count($where,'v_admin_log');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

        $data['list'] = $this->User_model->get_select_all('log_id,log_time,user_id,log_info,ip_address,admin_name',$where,'log_time','desc','v_admin_log',1,'v_admin_user',"v_admin_user.admin_id=v_admin_log.user_id",false,1,$start,$page_num);
        //echo $this->db->last_query();
        //echo "<pre>";print_r($data);exit();
        $data['time2']=strtotime($this->input->get('time2',true));
        $this->load->view('newadmin/admin_log_list',$data);
    }
    /*
 * 活动申请记录
 */
    public function activity_log($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type',true));
        $act_status= $data['act_status']= $this->input->get('act_status',true);
        if(!$act_status)
        {
            $act_status='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND act_time_adv >=$data[time1]";
        }
        if($data['time2']){
            $data['time2']+=86400;
            $where.="  AND act_time_adv <=$data[time2]";
        }

        if($data['title'])
        {
            if($data['type']==1){
                $where.= " AND user_name LIKE '%$data[title]%' ";
            }elseif($data['type']==2){
                $where.= " AND user_name LIKE '%$data[title]%'";
            }elseif($data['type']==3){
                $where.= " AND user_name LIKE '%$data[title]%'";
            }
        }else{
            $data['type']=0;
        }
        $where.="  AND act_status= '$act_status'";

        $page_num =10;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_activity_temp');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='act_id,user_id,user_name,title,start_time,end_time,content,mobile,email,act_status,act_time_adv';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'act_time_adv','DESC','v_activity_temp');
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/activity_log',$data);
    }
    public function activity_log_del(){
        $act_id=$this->input->get('act_id',TRUE);
        $where=array('act_id'=>$act_id);
        $this->User_model->del($where,'v_activity_temp');
        $this->put_admin_log("活动申请删除 活动id{$act_id}");
        redirect(base_url('newadmin/activity_log'));
    }
    //活动申请通过
    public function activity_ok()
    {
        if(isset($_SESSION['admin_id'])){
        $act_id=$this->input->get('act_id',true);
        $user_id=$this->input->get('user_id',true);
        $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'2'),'v_activity_temp');

        $this->put_admin_log("通过活动申请$act_id");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_hds');
        $this->push_sys($user_id,$info);
        redirect(base_url('newadmin/activity_log'));
        }
    }
    public function activity_no()
    {
        $act_id=$this->input->get('act_id',true);
        $user_id=$this->input->get('user_id',true);
        $this->User_model->update_one(array('act_id'=>$act_id),array('act_status'=>'3'),'v_activity_temp');
        $this->put_admin_log("否决活动申请$act_id");
        $this->new_lan_bydb($user_id);
        $info=$this->lang->line('sys_hdf');
        $this->push_sys($user_id,$info);
        redirect(base_url('newadmin/activity_log'));
    }
    /*
       * 后台活动列表
       */
    public function activity_list($page=1)
    {
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $is_show= $data['is_show']= $this->input->get('is_show',true);
        if($is_show==null)
        {
            $is_show='1';
            $data['is_show']='1';
        }
        $where=' 1=1 ';
        if($data['time1'])
        {
            $where.=" AND start_time >=$data[time1]";
        }
        if($data['time2'])
        {
            $data['time2']+=86400;
            $where.="  AND end_time <=$data[time2]";
        }
        if($data['title'])
        {
            $where.= " AND title LIKE '%$data[title]%' ";
        }
        $where.="  AND is_show= '$is_show'";
        // echo $where;exit();
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_activity');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $select='act_id,title,start_time,end_time,content,is_show,add_time,displayorder';
        $data['list']=$this->User_model->get_select_more($select,$where,$start,$page_num,'displayorder','ASC','v_activity');
        $data['time2']=strtotime($this->input->get('time2'));
        $this->load->view('newadmin/activity_manage',$data);
    }


    public function location_list_image()
    {
        if(isset($_SESSION['admin_id']))
        {

            $where=' 1=1 ';
            $where.="  AND is_down= '1'";
            $where="   is_hot= '1'";
            $data['list']=$this->User_model->get_select_more('id,name,image,displayorder',$where,$start=0, $page_num=100, 'displayorder', $order='ASC', $table='v_location');
            if(!$data)
            {
                $data=array();
            }
           //echo '<pre>';print_r($data);exit();
            $this->load->view('newadmin/location_image',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }

    public function country_down()
    {
        $id=$this->input->get('id',true);

        $this->User_model->update_one(array('id'=>$id),array('is_down'=>'0'),'v_location');
        $this->put_admin_log("撤下location $id");
        redirect('newadmin/location_list_image');
    }

    //优惠券后台列表，增删改
    public function dis_code_list_admin()
    {
        $data['info']=$this->User_model->get_select_all($select='*',$where="is_show <'2'",$order_title='addtime',$order='ASC',$table='v_dis_code');
        if($data['info']===FALSE)
        {
            $data['info']=array();
        }
        $this->load->view('lxgj/youhuiq_admin',$data);
    }
    public function plus_code()
    {
       // $row=$this->User_model->get_select_one($select='name,name_en,image,comment',$where="is_show='1'",$table='v_dis_code',$ororder='0',$order_title='1',$d='DESC');

        $row['name']='新增';
        $row['name_en']='new';
        $row['image']="http://api.etjourney.com//public/images/crop/location/1473328741.jpg";
        $row['addtime']=time();
        $row['is_show']='1';
      // echo '<pre>';print_r($row);exit();
        $this->User_model->user_insert($table='v_dis_code',$row);
        redirect(base_url('newadmin/dis_code_list_admin'));
    }
    public function up_dis_code_image()
    {
        if($_FILES['file1']['error']==0)
        {
            $data['image']=$this->upload_image('file1','location');
           // $data['image']=$this->imagecropper( $data['image'],'location','time',$width='100',$height='100');

        }
        $id=$this->input->post('id',TRUE);
        $data['name']=$this->input->post('name',TRUE);
        $data['name_en']=$this->input->post('name_en',TRUE);
        $data['comment']=$this->input->post('comment',TRUE);
        $this->User_model->update_one(array('id'=>$id),$data,'v_dis_code');
        $this->put_admin_log("修改旅行工具优惠券 $id");
        redirect(base_url('newadmin/dis_code_list_admin'));
    }

    public function down_dis_code_image()
    {

        $id=$this->input->post_get('id',TRUE);
        $data=array('is_show'=>'2');
        $this->User_model->update_one(array('id'=>$id),$data,'v_dis_code');
        $this->put_admin_log("修改旅行工具优惠券 $id");
        redirect(base_url('newadmin/dis_code_list_admin'));
    }



    public function up_country_image()
    {
       $data['displayorder']=$this->input->post('displayorder',TRUE);

        if($_FILES['file1']['error']==0)
        {
            $data['image']=$this->upload_image('file1','location');

        }

        $id=$this->input->post('id',TRUE);
        $this->User_model->update_one(array('id'=>$id),$data,'v_location');
        $this->put_admin_log("上传地址形象图片 $id 同时改变地址顺序为  $data[displayorder]");
        redirect(base_url('newadmin/location_list_image'));
    }

    //后台广告
    public function adv_list()
    {
        if(isset($_SESSION['admin_id']))
        {
            $is_show= $data['is_show']= $this->input->get('is_show',true);
            if($is_show!=='0')
            {
                $data['is_show']=$is_show='1';
            }
            $where=' 1=1 ';
            $where.="  AND is_show= '$is_show'";
            $data['list']=$this->User_model->get_select_more($select='*',$where,$start=0, $page_num=100, 'type', $order='ASC', $table='v_adver');
            if(!$data)
            {
                $data=array();
            }
            $this->load->view('newadmin/adv_list',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }

    //广告撤下
    public function adv_down()
    {
        $adv_id=$this->input->get('adv_id',true);
        $this->User_model->update_one(array('adv_id'=>$adv_id),array('is_show'=>'0'),'v_adver');
        $this->put_admin_log("撤下广告$adv_id");
        redirect('newadmin/adv_list');
    }

    public function up_image()
    {
        if($_FILES['file1']['error']==0)
        {
            $data['image']=$this->upload_image('file1','adv');
            $data['type']=$this->input->post('type',true);
            $data['is_show']='1';
            $data['time']=$this->input->post('time',true);
            $data['add_time']=time();
            $this->User_model->update_one(array('type'=>$data['type']),array('is_show'=>'3'),'v_adver');
            $adv_id=$this->User_model->user_insert($table='v_adver',$data);
            $this->put_admin_log("上传广告$adv_id");
        }
        redirect(base_url('newadmin/adv_list'));
    }

    /*
     * 首页banner
     */
    public function menu_banner()
    {
        if(isset($_SESSION['admin_id']))
        {
            $data['title'] = trim($this->input->get('title',true));
            $data['time1']=strtotime($this->input->get('time1',true));
            $data['time2']=strtotime($this->input->get('time2',true));
            $status= $data['status']= $this->input->get('status',true);
            if(!$status)
            {
                $status='0';
            }
            $where=' 1=1 ';
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
                $where.= " AND title LIKE '%$data[title]%' ";
            }
            $where.="  AND status= '$status'";
            $data['list']=$this->User_model->get_select_all($select='*',$where,'displayorder', 'ASC','v_banner');
            $count=$this->User_model->get_count($where, $table='v_banner');
            $data['hidden']=FALSE;
            if($count['count']>=5)
            {
                $data['hidden']=TRUE;
            }
            $this->load->view('newadmin/menu_banner',$data);
        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }

    public function banner_add()
    {
        if(isset($_SESSION['admin_id']))
        {
            $data['activity']=$this->User_model->get_select_all('act_id,title',"is_show='1'",'start_time','ASC');
            $data['activity_terms']=$rs=$this->User_model->get_select_all('id,title',"STATUS='1'",'id','ASC','v_activity_terms');
            $data['location']=$this->User_model->get_select_all('id,name',"is_hot='1'",'id','ASC','v_location');
            $this->load->view('newadmin/banner_add',$data);

        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
    }
    public function banner_edit()
    {
        if(isset($_SESSION['admin_id']))
        {
            $ban_id=$this->input->get('ban_id',true);
            $data['banner']=$this->User_model->get_select_one('*',array('ban_id'=>$ban_id),'v_banner');

            $data['activity']=$this->User_model->get_select_all('act_id,title',"is_show='1'",'start_time','ASC');
            $data['activity_terms']=$rs=$this->User_model->get_select_all('id,title',"STATUS='1'",'id','ASC','v_activity_terms');
            $data['location']=$this->User_model->get_select_all('id,name',"is_hot='1'",'id','ASC','v_location');

            /*$act_id=array();
            foreach($rs as $k=>$v){
                $act_id[]=$v['id'];
            }
            $act_id=implode(',',$act_id);*/
            //$data['shop_activity']=$this->User_model->get_select_all('act_id,title',"is_show='1' AND pid IN ($act_id)",'start_time','ASC','v_activity_son');


            $this->load->view('newadmin/banner_edit',$data);

        }
        else
        {
            echo '登录超时';
            echo '<meta http-equiv="refresh" content="2; url=/newadmin/login">';die;
        }
        //redirect('admin/menu_banner');
    }
    public function size_up($name){
        $file_size=$_FILES[$name]['size'];
        if ($file_size>51200){
            echo "请让图片小于50k";
            exit();
        }
    }

    public function banner_insert(){

        $type=$this->input->post('type',true);
        $range_id=$this->input->post('range_id',true);
        $app_hot=$this->input->post('app_hot',true);
        if(!$app_hot)
        {
            $app_hot='0';
        }
        $app_feat=$this->input->post('app_feat',true);
        if(!$app_feat)
        {
            $app_feat='0';
        }
        if($range_id)
        {
            $range_name=$this->User_model->get_select_one('name',array('id'=>$range_id),'v_location');
            $range_name=$range_name['name'];
        }else
        {
            $range_name='无';
        }
        $put_url=$this->input->post('put_url',true);
        $title=$this->input->post('title',true);

       // $this->size_up('file1');
        // $image_url=$this->upload_image('file1','banner','time');

        $image_url=$this->upload_image('file1','banner','time');
        $displayorder=$this->input->post('displayorder',true);


        $data=array(
            'link_url'=>$put_url,
            'title'=>$title,
            'type'=>$type,
            'range_id'=>$range_id,
            'range_name'=>$range_name,
            'displayorder'=>$displayorder,
            'add_time'=>time(),
            'image_url'=>$image_url,
            'app_hot'=>$app_hot,
            'app_feat'=>$app_feat,

        );
        $ban_id=$this->User_model->user_insert('v_banner',$data);
        $this->put_admin_log("添加banner$ban_id");
        //	$this->User_model->update_one(array('ban_id'=>$ban_id),array('image_url'=>$image_url),$table='v_banner');
        redirect("newadmin/menu_banner");

    }

    public function banner_sub(){
        $ban_id=$this->input->post('ban_id',true);
        $put_url=$this->input->post('put_url',true);
        $title=$this->input->post('title',true);
        $type=$this->input->post('type',true);
        $app_hot=$this->input->post('app_hot',true);
        if(!$app_hot)
        {
            $app_hot='0';
        }
        $app_feat=$this->input->post('app_feat',true);
        if(!$app_feat)
        {
            $app_feat='0';
        }

        $range_id=$this->input->post('range_id',true);
        if($range_id)
        {
            $range_name=$this->User_model->get_select_one('name',array('id'=>$range_id),'v_location');
            $range_name=$range_name['name'];
        }else
        {
            $range_name='无';
        }
  
        $displayorder=$this->input->post('displayorder',true);

        $data=array(
            'link_url'=>$put_url,
            'title'=>$title,
            'displayorder'=>$displayorder,
            'range_id'=>$range_id,
            'range_name'=>$range_name,
            'type'=>$type,
            'add_time'=>time(),
            'app_hot'=>$app_hot,
            'app_feat'=>$app_feat,
        );
        if($_FILES['file1']['error']==0){
            $image_url=$this->upload_image('file1','banner','time');
            $data['image_url']=$image_url;
        }else{
            //$row=$this->User_model->get_select_one('image_url',array('ban_id'=>$ban_id),'v_banner');
            //$data['image_url']=$row['image_url'];
        }

        //echo "<pre>";print_r($data);exit();
        $this->User_model->update_one(array('ban_id'=>$ban_id),$data,$table='v_banner');
        $this->put_admin_log("修改banner $ban_id");
        redirect("newadmin/banner_edit?ban_id=$ban_id");
    }

    public function banner_down(){
        $ban_id=$this->input->get('ban_id',true);
        $this->User_model->update_one(array('ban_id'=>$ban_id),array('status'=>1,'displayorder'=>'99'),'v_banner');
        $this->put_admin_log("撤下banner $ban_id");
        redirect('newadmin/menu_banner');
    }
    public function banner_del(){
        $ban_id=$this->input->get('ban_id',true);
        $this->User_model->del(array('ban_id'=>$ban_id),'v_banner');
        $this->put_admin_log("删除banner $ban_id");
        redirect('newadmin/menu_banner');
    }
    /*
  * 商品表
  *
  */
    public function goods_list($page=1){
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $is_off= $data['is_off']= $this->input->get('is_off',true);
        if(!$is_off){
            $is_off=0;
        }
        $where=' 1=1 ';


        if($data['time1']){
            $where.=" AND add_time >=$data[time1]";
        }
        if($data['time2']){
            $data['time2']+=86400;
            $where.="  AND add_time <=$data[time2]";
        }

        if($data['title'])
        {
            if($data['type']==1){
                $where.= " AND address LIKE '%$data[title]%' ";
            }elseif($data['type']==2){
                $where.= " AND user_name LIKE '%$data[title]%' ";
            }elseif($data['type']==3){
                $where.= " AND title LIKE '%$data[title]%'";
            }
        }else{
            $data['type']=0;
        }
        if($is_off==0){
            $where.="  AND is_off= $is_off";
        }elseif($is_off==1){
            $where.="  AND is_off > 0";
        }


        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_goods_count($where,'v_goods');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;

        $data['list']=$this->User_model->get_goods_list($where,'v_goods',$start,$page_num);
        $data['time2']=strtotime($this->input->get('time2',true));
        $this->load->view('newadmin/goods_manage',$data);
    }

    public function goods_del(){
        $goods_id=$this->input->get('goods_id',true);
        $is_off=$this->input->get('is_off',true);
        $page=$this->input->get('page',true);
        $this->User_model->del(array('goods_id'=>$goods_id),'v_goods');
        redirect(base_url("newadmin/goods_list/$page?is_off={$is_off}"));
    }

    /**
     * [user_list 用户列表]
     * @param  integer $page [页数]
     * @return [type]        [description]
     */
    public function user_list($page=1){

        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $data['title'] = trim($this->input->get('title',true));
        $data['time1']=strtotime($this->input->get('time1',true));
        $data['time2']=strtotime($this->input->get('time2',true));
        $data['type']=trim($this->input->get('type'));
        $sta= $data['sta']= $this->input->get('sta',true);
        if(!$sta){
            $sta=1;
        }
        $where=" regist_type!='7' ";


        if($data['time1']){
            $where.=" AND register_time >=$data[time1]";
        }
        if($data['time2']){
            $data['time2']+=86400;
            $where.="  AND register_time <=$data[time2]";
        }

        if($data['title'])
        {

            $where.= " AND user_name LIKE '%$data[title]%' ";

        }else{
            $data['type']=0;
        }
        if($sta==1){
            $where.="  AND groupid= '1'";
        }elseif($sta==2){
            $where.="  AND groupid= '4'";
        }elseif($sta==3){
            $where.="  AND groupid= '1' AND auth='1' ";
        }elseif($sta==4){
            $where.="   AND auth='1' ";
        }elseif($sta==5){
            $where.="   AND status!='0' AND status!='3'";
        }elseif($sta==6){
            $where.="   AND regist_type='6' AND groupid= '1'";
        }elseif($sta==7){
            $where.="   AND regist_type!='6' AND groupid= '1'";
        }
        $page_num =100;
        $data['now_page'] = $page;
        $count = $this->User_model->get_count($where,'v_users');
        $data['count']=$this->User_model->get_all_user_count('v_users');
        $data['max_page'] = ceil($count['count']/$page_num);
        if($page>$data['max_page'])
        {
            $page=1;
        }
        $start = ($page-1)*$page_num;
        $data['list'] = $this->User_model->get_select_more('*',$where,$start,$page_num,'user_id','DESC','v_users');
        if(is_array( $data['list'])){
            foreach($data['list'] as $k=>$v){
                $data['list'][$k]['level']=$this->get_level($v['credits']);
            }
        }
        $data['time2']=strtotime($this->input->get('time2',true));

        $this->load->view('newadmin/user_list',$data);
    }

    public function get_level($credits=0)
    {
        $level = '';
        $credits = intval($credits);
        if($credits <= 50)
        {
            $level = '1';
        }elseif($credits <= 100){
            $level = '2';
        }elseif($credits <= 500){
            $level = '3';
        }elseif($credits <= 1000){
            $level = '4';
        }elseif($credits <= 2500){
            $level = '5';
        }elseif($credits <= 5000){
            $level = '6';
        }elseif($credits <= 8000){
            $level = '7';
        }elseif($credits <= 12000){
            $level = '8';
        }elseif($credits <= 16000){
            $level = '9';
        }elseif($credits <= 20000){
            $level = '10';
        }elseif($credits <= 35000){
            $level = '11';
        }elseif($credits > 35000){
            $level = '12';
        }
        return $level;
    }
    //后台下拉列表
    public function country_list(){
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%'  AND level=2 " ;
            $data['sou']=1;
        }else{
            $where=array('level'=>2);
        }
        $select='id,name,name_pinyin,name_en,is_down';
        $order='name_pinyin';
        $this->lang->load('jt', 'english');
        $data['list']=$this->User_model->get_city($select,$where,$order);

        $this->load->view('newadmin/list_country',$data);
    }


    //下拉列表
    public function city_list(){
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%'  AND level >=3 " ;
            $data['sou']=1;
        }else{
            $where=array('level >='=>3);
        }
        $select='id,name,name_pinyin,name_en,is_down';
        $order='name_pinyin';
        $this->lang->load('jt', 'english');
        $data['list']=$this->User_model->get_city($select,$where,$order);

        $this->load->view('newadmin/list_city',$data);
    }





    //后台下拉列表
    public function city_country_list(){
        $this->auth_or_no();
        $data['count_url']=$this->count_url;
        $keyword=$this->input->post('keyword',true);
        if($keyword){
            $where="name LIKE '%$keyword%' or name_en LIKE '%$keyword%' or name_ft LIKE '%$keyword%' " ;
            $data['sou']=1;
        }else{
            $where="1=1";
        }
        $select='id,name,name_pinyin,name_en,is_hot';
        $order='name_pinyin';
        $this->lang->load('jt', 'english');
        $data['list']=$this->User_model->get_city($select,$where,$order);

        $this->load->view('newadmin/list_all',$data);
    }


    public function city_country_sub(){
        if(!isset($_SESSION['admin_id']))
        {
            return false;
        }
        $city=$this->input->post('city',true);
        $this->User_model->update_one("1=1",array('is_hot'=>'0'),$table='v_location');
        foreach($city as $k=>$v){
            $where=array('id'=>$v);
            $data=array('is_hot'=>'1');
            $this->User_model->update_one($where,$data,$table='v_location');
        }

        redirect(base_url('newadmin/city_country_list'));

    }

    public function coun_sub(){
        if(!isset($_SESSION['admin_id']))
        {
            return false;
        }
        $city=$this->input->post('city',true);
        $this->User_model->update_one("level=2",array('is_down'=>'0'),$table='v_location');
        foreach($city as $k=>$v){
            $where=array('id'=>$v);
            $data=array('is_down'=>'1');
            $this->User_model->update_one($where,$data,$table='v_location');
        }

        redirect(base_url('newadmin/country_list'));

    }

    public function city_sub(){
        if(!isset($_SESSION['admin_id']))
        {
            return false;
        }
        $city=$this->input->post('city',true);
        $this->User_model->update_one("level=2",array('is_down'=>'0'),$table='v_location');
        foreach($city as $k=>$v){
            $where=array('id'=>$v);
            $data=array('is_down'=>'1');
            $this->User_model->update_one($where,$data,$table='v_location');

        }

        redirect(base_url('newadmin/city_list'));

    }

    //系统消息

    public function push_sys($user_id,$info){
        $data=array(
            'pm_type'=>0,
            'user_id'=>$user_id,
            'message'=>$info,
            'is_new'=>1,
            'add_time'=>time()
        );
        $this->User_model->user_insert('v_prompt',$data);
    }
    //辅助数
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

    public function get_aly_url($video_name){
        $para['Action']='ForbidLiveStream';
        $para['DomainName']='video.etjourney.com';
        $para['AppName']='etjourney';
        $para['StreamName']=$video_name;
        $para['LiveStreamType']='publisher';
        $para['Format']='JSON';
        $para['Version']='2014-11-11';
        $para['AccessKeyId']='vNXHrUOlKeC7uHL9';
        $para['SignatureMethod']='HMAC-SHA1';
        $para['Timestamp']=substr(gmdate(DATE_ATOM,time()),0,19).'Z';
        $para['SignatureVersion']='1.0';
        $para['SignatureNonce']=$this->getRandChar(16);
        //$key = 'DlDvHRZ6Gv9f1IFRR4UCncC8Q9cLSu'.'&';
        $key = 'ThXh2tCuLabUhfWP3043znCBm0Vr07'.'&';
        $String = urlencode($this->formatParaMap($para));
        $String = 'GET'.'&%2F&'.$String;
        $para['Signature'] = $this->getSignature($String,$key);
        $String = $this->formatParaMap($para,1);
        $url = 'https://cdn.aliyuncs.com/?'.$String;

        return $url;
    }

    public function getstopurl($video_name){
        $url = " https://api.ucloud.cn/?Action=ForbidLiveStream&Domain=rtmpuc.etjourney.com&Application=live&StreamId={$video_name}";


       // https://api.ucloud.cn/?Action=ForbidLiveStream &Domain=publish.ucloud.com.cn&Application=live&StreamId=123

        return $url;
    }

    function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];
        }
        return $str;
    }
//项目图片目录创建测试方法
    public function mk_test()
    {
        $name1=date('Ymd',time());
        $name2=date('H',time());
        echo $name1;

        echo '<br>';
        echo $name2;
        if (!file_exists('./upload/'))
        {
            if(!mkdir('./upload/',0777))
            {
                return false;
            }

        }
        if(!file_exists("./upload/$name1/"))
        {

            if(!mkdir("./upload/$name1/",0777))
            {
                return false;
            }

        }

        if(!file_exists("./upload/$name1/$name2"))
        {
           if(!mkdir("./upload/$name1/$name2",0777))
           {
               return false;
           }

        }
    }


    public function upload_image($filename,$fileurl,$key='time',$lt=0)
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl,0777))
            {
                return false;
            }
        }

        return $this->shangchuan($filename,$fileurl,$key,$lt);
    }

    public function shangchuan($filename,$fileurl,$key='time',$lt=0)
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
            if($key=='time'){
                $key =md5(time().rand(1,999999));
            }

            $pic_url="./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
         //   return substr($pic_url,1);

            return ltrim($pic_url,'.');
        }
    }
     //官方后台，个人产品增加
     public function products_add_from_admin()
     {
         $this->auth_or_no();
         $act_id=$this->input->get('act_id',TRUE);

         // echo '<pre>';print_r($data);exit();
         if($act_id)
         {
             $data['info']=$this->User_model->get_select_one('act_id,title,user_id,banner_image,banner_product,banner_hot,form,type,content_text,day_list,range,range_name,discount_type,tag,users,is_show',array('act_id'=>$act_id),'v_activity_children');
             if($data['info']['type']='0')
             {

             }
             elseif($data['info']['type']='1')
             {
                 $act_id=$data['info']['act_id'];
                 $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,shop_price,ori_price,oori_price',
                     array('act_id'=>$act_id,'is_show'=>'1'),'v_goods');
                 $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
                 $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
                 $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

                 $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
                 $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
                 $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);


                 $goods_id=$data['goods']['goods_id'];
                 $select='v_goods_attr.goods_attr_id,v_goods_attr.attr_id,v_goods_attr.supply_info_one,v_goods_attr.supply_info_two,v_goods_attr.goods_id,v_goods_attr.pid,v_goods_attr.attr_val,v_goods_attr.attr_price,v_attr.attr_name,v_attr.attr_type';
                 $where=array('v_goods_attr.goods_id'=>$goods_id,'v_goods_attr.is_show'=>'1');
                 $data['attr']=$this->User_model->get_select_all($select,$where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
                 if(!$data['attr']){
                     $data['attr']=array();

                 }
                 $data['other_attr']=array();
                 $data['date']=array();
                 $data['date']['attr_name']='';
                 $data['date']['attr_type']='';
                 $data['date']['goods_attr_id']=array();
                 $data['date']['attr_val_list']=array();
                 $data['date']['attr_price_list']=array();
                 foreach($data['attr'] as $k=>$v)
                 {
                     if($v['attr_type']==1)
                     {
                         $data['date']['attr_name']=$v['attr_name'];
                         $data['date']['attr_type']=$v['attr_type'];
                         $data['date']['goods_attr_id'][]=$v['goods_attr_id'];
                         $data['date']['attr_val_list'][]=date('Y-n-j',$v['attr_val']);
                         $data['date']['attr_price_list'][]=$v['attr_price'];
                     }
                 }
                 foreach($data['attr'] as $k=>$v)
                 {

                     if($v['attr_type']==2)
                     {
                         if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                         {
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_name']=$v['attr_name'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_type']=$v['attr_type'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['pid']=$v['pid'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['list'][]=$v;
                             // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                         }
                     }
                     elseif($v['attr_type']==3)
                     {
                         if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                         {
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][]=$v;
                             // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                         }
                     }
                 }

                 //unset($data['attr']);
                 //$data['other_attr']=array_values($data['other_attr']);
                 $data['day']=$data['info']['day_list'];
                 $data['day']=json_decode( $data['day'],true);
                 foreach( $data['day'] as $k =>$v)
                 {
                     $data['day'][$k]=   str_replace("<br>","\n",$v);
                 }
             }

         }
         else
         {
             $data=array();
             $data['user_id']=$this->input->get('user_id',TRUE);
             if(!$data['user_id'])
             {
                 $data['user_id']=0;
             }
         }
         // $time=time();
         $time_first=$time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
         $timeend= strtotime('+3 month', $time);
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
         $tdaysend=end($data['date']['cal']);
         // $days=$tdaysend['time']-$time_first;
         $data['days']=$this->prDates($time_first,$tdaysend['time']);
         // echo $time_first;
         //  echo $tdaysend['time'];
         if($this->input->get('test')){
             echo '<pre>';
             print_r($data);
             // print_r($data);
             exit();

         }
         // $this->load->view('newadmin/goods_add_edit_new',$data);
         $this->load->view('newadmin/product_add_from_admin',$data);
     }

//入库 个人产品
     public function products_insert_from_admin()
     {
         $goods_number=trim($this->input->post('goods_number',true));
         $shop_price=$this->input->post('shop_price',true);

         if( $goods_number<=0 && $shop_price<=0){
             return false;
         }
         $data['title']=$this->input->post('title',true);

         $data['discount_type']=$this->input->post('discount_type',true);
         $data['range']=$this->input->post('range',true);
         $data['range_name']=$this->input->post('range_detail',true);

         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content_text']=$this->input->post('content_text',true);
         $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

         $data['content']= $data['content_text'];

         $data['users']=$this->input->post('users',true);
         $data['users']= str_replace("，",",", $data['users']);


         $data['user_id']=$this->input->post('user_id',true);


         $day=$this->input->post('day',true);
         //print_r($day);exit();
         $new_day=$day;
         foreach($day as $k=>$v){
             $new_day[$k] = str_replace("\n","<br>", $v);
             if(stristr($v,'请填写行程描述'))
             {
                 unset($new_day[$k]);
             }
         }
         $new_day=array_values($new_day);
         $data['day_list']=json_encode($new_day);

         $data['is_show']=$this->input->post('up_down_goods',true);
         if(! $data['is_show']){
             $data['is_show']='0';
         }
         if($this->input->post('top'))
         {
             $data['displayorder']='1';
         }

         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }else{
             $data['banner_image']='';
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='341',$height='240');

         }else{
             $data['banner_hot']='';
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='260',$height='260');
         }else{
             $data['banner_product']='';
         }



         $data['act_status']='2';
         //$data['is_show']='2';
         $data['special']='3';
         $data['add_time']=time();
         //echo '<pre>';print_r($data);exit();
         $data['type']='1';
         $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
         $this->put_admin_log("添加个人产品{$act_id}");

         $dateto=$this->input->post('dateto',true);
         $pricehas=$this->input->post('pricehas',true);
         $priceno=$this->input->post('priceno',true);
         $pricecom=$this->input->post('pricecom',true);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);

         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'shop_price'=>$shop_price,
             'ori_price'=>$shop_price,
             'act_id'=>$act_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         $goods_id=$this->User_model->user_insert('v_goods',$data);

         redirect(base_url("newadmin/products_add_from_admin?act_id={$act_id}"));
     }



     public function products_sub_from_admin()
     {



         $act_id=$this->input->post('act_id',TRUE);
         if(!$act_id){
             return false;
         }
         $temp_row=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_children');
         $goods_number=trim($this->input->post('goods_number',true));
         $shop_price=$this->input->post('shop_price',true);

         // echo '<pre>';
         //print_r($_POST);exit();
         if( $goods_number<=0 ){
             $goods_number=10;
         }
         if($shop_price<=0){
             $shop_price=1;
         }


         $data['title']=$this->input->post('title',true);

         $data['discount_type']=$this->input->post('discount_type',true);
         $data['range']=$this->input->post('range',true);
         $data['range_name']=$this->input->post('range_detail',true);

         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content_text']=$this->input->post('content_text',true);
         $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

         $data['content']= $data['content_text'];

         $data['users']=$this->input->post('users',true);
         $data['users']= str_replace("，",",", $data['users']);


         $data['user_id']=$this->input->post('user_id',true);
            $data['add_time']=time();

         $day=$this->input->post('day',true);
         //print_r($day);exit();
         $new_day=$day;
         foreach($day as $k=>$v){
             $new_day[$k] = str_replace("\n","<br>", $v);
             if(stristr($v,'请填写行程描述'))
             {
                 unset($new_day[$k]);
             }
         }
         $new_day=array_values($new_day);
         $data['day_list']=json_encode($new_day);

         $data['is_show']=$this->input->post('up_down_goods',true);
         if(!$data['is_show']){
             $data['is_show']='0';
         }
         if($this->input->post('top'))
         {
             $data['displayorder']='1';
         }


         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }
         else
         {
            // $data['banner_image']=$temp_row['banner_image'];
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');
         }
         else
         {
            // $data['banner_hot']=$temp_row['banner_hot'];
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='260',$height='260');
         }
         else
         {
            // $data['banner_product']=$temp_row['banner_product'];
         }

         $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
         $this->put_admin_log("编辑个人产品{$act_id}");

         $dateto=$this->input->post('dateto',true);
         $dateto=str_replace("\n","<br>", $dateto);

         $pricehas=$this->input->post('pricehas',true);
         $pricehas=str_replace("\n","<br>", $pricehas);

         $priceno=$this->input->post('priceno',true);
         $priceno=str_replace("\n","<br>", $priceno);

         $pricecom=$this->input->post('pricecom',true);
         $pricecom=str_replace("\n","<br>", $pricecom);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);


         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'shop_price'=>$shop_price,
             'ori_price'=>$shop_price,
             'act_id'=>$act_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         //print_r($data);exit();
         $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_goods');
         $this->del_attr();
         $goods_id=$this->User_model->user_insert('v_goods',$data);


         redirect(base_url("newadmin/products_add_from_admin?act_id={$act_id}"));
     }

//官方后特价产品增加

    public function goods_add()
    {
        $this->auth_or_no();
        $act_id=$this->input->get('act_id',TRUE);
        $data['location']=array();
        $data['seats_arr']=array();
        // echo '<pre>';print_r($data);exit();
        if($act_id)
        {

            $data['location']=$this->User_model->get_select_all('v_act_range.range_id,v_location.name',array('v_act_range.act_id'=>$act_id,'v_act_range.is_show'=>'1'),'v_act_range.ar_id','ASC','v_act_range',1,'v_location',"v_act_range.range_id=v_location.id");
            if(!is_array($data['location'])){
                $data['location']=array();
            }

            $data['info']=$this->User_model->get_select_one('act_id,title,user_id,banner_image,banner_product,banner_hot,form,type,xtype,ipro,special,photo_time,business_id,hot,rec,seats,
            content_text,day_list,range,range_name,discount_type,tag,users,is_show',array('act_id'=>$act_id),'v_activity_children');
            $data['seats_arr']=explode(',',$data['info']['seats']);
            if($data['info']['type']='0')
            {

            }
            elseif($data['info']['type']='1')
            {
                $act_id=$data['info']['act_id'];
                $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,ori_price,oori_price',
                    array('act_id'=>$act_id,'is_show'=>'1'),'v_goods');
                $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
                $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
                $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

                $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
                $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
                $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);


                $goods_id=$data['goods']['goods_id'];
                $select='v_goods_attr.goods_attr_id,v_goods_attr.attr_id,v_goods_attr.supply_info_one,v_goods_attr.supply_info_two,v_goods_attr.goods_id,v_goods_attr.pid,v_goods_attr.attr_val,v_goods_attr.attr_price,v_attr.attr_name,v_attr.attr_type';
                $where=array('v_goods_attr.goods_id'=>$goods_id,'v_goods_attr.is_show'=>'1');
                $data['attr']=$this->User_model->get_select_all($select,$where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
                if(!$data['attr']){
                    $data['attr']=array();

                }
                $data['other_attr']=array();
                $data['date']=array();
                $data['date']['attr_name']='';
                $data['date']['attr_type']='';
                $data['date']['goods_attr_id']=array();
                $data['date']['attr_val_list']=array();
                $data['date']['attr_price_list']=array();
                foreach($data['attr'] as $k=>$v)
                {
                    if($v['attr_type']==1)
                    {
                        $data['date']['attr_name']=$v['attr_name'];
                        $data['date']['attr_type']=$v['attr_type'];
                        $data['date']['goods_attr_id'][]=$v['goods_attr_id'];
                        $data['date']['attr_val_list'][]=date('Y-n-j',$v['attr_val']);
                        $data['date']['attr_price_list'][]=$v['attr_price'];
                    }
                }
                foreach($data['attr'] as $k=>$v)
                {

                    if($v['attr_type']==2)
                    {
                        if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                        {
                            $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_name']=$v['attr_name'];
                            $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_type']=$v['attr_type'];
                            $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['pid']=$v['pid'];
                            $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['list'][]=$v;
                            // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                        }
                    }
                    elseif($v['attr_type']==3)
                    {
                        if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                        {
                            $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][]=$v;
                            // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                        }
                    }
                }

           //unset($data['attr']);
            //$data['other_attr']=array_values($data['other_attr']);
            $data['day']=$data['info']['day_list'];
            $data['day']=json_decode( $data['day'],true);
            foreach( $data['day'] as $k =>$v)
            {
                $data['day'][$k]=   str_replace("<br>","\n",$v);
            }
            }

        }
        else
        {
           //WWW $data=array();
            $data['user_id']=$this->input->get('user_id',TRUE);
            if(!$data['user_id'])
            {
                $data['user_id']=3706;
            }
        }
        // $time=time();
        $data['range_list']=$this->User_model->get_select_all('id,name',"is_hot='1' OR is_down='1'",'id','ASC','v_location');

        $time_first=$time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
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
        $tdaysend=end($data['date']['cal']);
       // $days=$tdaysend['time']-$time_first;
        $data['days']=$this->prDates($time_first,$tdaysend['time']);
       // echo $time_first;
      //  echo $tdaysend['time'];
        if($this->input->get('test')){
            echo '<pre>';
            print_r($data);
           // print_r($data);
            exit();

        }
       // $this->load->view('newadmin/goods_add_edit_new',$data);
        $this->load->view('newadmin/goods_add_edit',$data);
    }


    public function prDates($start,$end)
    {
        $day=array();
        $dt_start = $start;
        $dt_end = $end;
        while ($dt_start<=$dt_end)
        {
            $day[]=date('Y-n-j',$dt_start);
          //  var_dump($day) ;
            $dt_start = strtotime('+1 day',$dt_start);
        }
       return $day;
    }

    public function goods_add_new()
    {
        //$this->auth_or_no();
        $act_id=$this->input->get('act_id',TRUE);
        $data['location']=array();
       //echo '<pre>';print_r($data);exit();
        if($act_id)
        {
            $data['info']=$this->User_model->get_select_one('act_id,title,user_id,banner_image,banner_product,banner_hot,form,type,xtype,ipro,special,photo_time,business_id,hot,rec,seats,
            content_text,day_list,range,range_name,discount_type,tag,users,is_show',array('act_id'=>$act_id),'v_activity_children');
            $act_id=$data['info']['act_id'];
            $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,ori_price,oori_price',
                array('act_id'=>$act_id,'is_show'=>'1'),'v_goods');

            $data['seats_arr']=explode(',',$data['info']['seats']);
            $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
            $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
            $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

            $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
            $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
            $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);


            $goods_id=$data['goods']['goods_id'];
            $select='v_goods_attr.goods_attr_id,v_goods_attr.attr_id,v_goods_attr.supply_info_one,v_goods_attr.supply_info_two,v_goods_attr.goods_id,v_goods_attr.attr_val,v_goods_attr.attr_price,v_attr.attr_name,v_attr.attr_type';
            $where=array('v_goods_attr.goods_id'=>$goods_id,'v_goods_attr.is_show'=>'1');
            $data['attr']=$this->User_model->get_select_all($select,$where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
            $data['other_attr']=array();
            $data['date']=array();
            foreach($data['attr'] as $k=>$v)
            {
                if($v['attr_type']==1){
                    $data['date']['attr_name']=$v['attr_name'];
                    $data['date']['attr_type']=$v['attr_type'];
                    $data['date']['attr_val_list'][]=date('Y-n-j',$v['attr_val']);
                    $data['date']['attr_price_list'][]=$v['attr_price'];
                }else{
                    $data['other_attr'][$v['attr_id']]['attr_name']=$v['attr_name'];
                    $data['other_attr'][$v['attr_id']]['attr_type']=$v['attr_type'];
                    $data['other_attr'][$v['attr_id']]['list'][]=$v;
                }

            }
            unset($data['attr']);

            $data['other_attr']=array_values($data['other_attr']);
           $data['location']=$this->User_model->get_select_all('v_act_range.range_id,v_location.name',array('v_act_range.act_id'=>$act_id,'v_act_range.is_show'=>'1'),'v_act_range.ar_id','ASC','v_act_range',1,'v_location',"v_act_range.range_id=v_location.id");
            if(!is_array($data['location'])){
                $data['location']=array();
            }
        }
        else
        {
          //  $data=array();
            $data['user_id']=$this->input->get('user_id',TRUE);
            if(!$data['user_id'])
            {
                $data['user_id']=0;
            }
        }
        $data['range_list']=$this->User_model->get_select_all('id,name',"is_hot='1' OR is_down='1'",'id','ASC','v_location');
       // $time=time();
        $time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
        $timeend= strtotime('+5 month', $time);
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


        if($this->input->get('test')){
            echo '<pre>';print_r($data);exit();

        }
        $this->load->view('newadmin/goods_add_edit_new',$data);
    }

    //特价产品插入_new
    public function products_insert()
    {
        // echo '<pre>';print_r($_POST);exit();
        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);
        if(!$oori_price){
            $oori_price=$ori_price;
        }
        if( $goods_number<=0 && $ori_price<=0){
            return false;
        }
        $date_name=$this->input->post('date_name',true);
        if(!$date_name){
            $date_name='日期';
        }
        $up_date=$this->input->post('up_date',true);

        $attr=$this->input->post('attr',TRUE);

        $attr_arr_up=array();
        //print_r($up_date);
        if(is_array($up_date))
        {
            foreach($up_date as $k=>$v)
            {
                $attr_arr_up[]=array('date'=>strtotime($v),'attr'=>$attr[$v]);
                // $attr_arr_up[]['attr']=$attr[$v];
            }
        }else{
            $up_date=array();
        }

        //echo '<pre>';print_r($attr_arr_up);exit();
        $date_attr_insert=array(
            'attr_name'=>$date_name,
            'attr_type'=>'1',
            'add_time'=>time()
        );





        $data['title']=$this->input->post('title',true);
        $data['ipro']=$this->input->post('ipro',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special= $data['special']=$this->input->post('special',true);
       // $data['range_name']=$this->input->post('range_detail',true);
        //$data['range_id']=$this->input->post('range_id',true);


        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
      //  $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];


        $data['user_id']=$this->input->post('user_id',true);
        $data['business_id']=$this->input->post('business_id',true);
        $data['hot']=$this->input->post('hot',true);
        $data['rec']=$this->input->post('rec',true);

        $seats=$this->input->post('seats',TRUE);
        if(is_array($seats))
        {
            $data['seats']=implode(',',$seats);
        }
        //$data['users']=$this->input->post('users',true);
       // $data['users']= str_replace("，",",", $data['users']);


        $data['user_id']=$this->input->post('user_id',true);

        $shop_price=0;
        if($special=='7'){
            $shop_price=$ori_price;
        }

        $data['is_show']=$this->input->post('up_down_goods',true);
        if(! $data['is_show']){
            $data['is_show']='0';
        }
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }

        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $data['user_id'].'banner');
            $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
        }else{
            $data['banner_image']='';
        }

        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

        }else{
            $data['banner_hot']='';
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');
        }else{
            $data['banner_product']='';
        }


        $data['order_sell']=rand(1,100);
        $data['act_status']='2';
        //$data['is_show']='2';
     //   $data['special']='2';
        $data['add_time']=time();
        //echo '<pre>';print_r($data);exit();
        $data['type']='1';
        $act_id=$this->User_model->user_insert($table='v_activity_children',$data);

        $range_id=$this->input->post('range_id',true);
        if(!is_array($range_id)){
            $range_id=array();
        }
        foreach($range_id as $k=>$v){
            $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
        }

        $this->put_admin_log("添加特价产品{$act_id}");

        $dateto=$this->input->post('dateto',true);
        $pricehas=$this->input->post('pricehas',true);
        $priceno=$this->input->post('priceno',true);
        $pricecom=$this->input->post('pricecom',true);


        $direction=$this->input->post('direction',true);
        $direction=str_replace("\n","<br>", $direction);

        $attention_list=$this->input->post('attention_list',true);
        $attention_list=str_replace("\n","<br>", $attention_list);

        $change_goods=$this->input->post('change_goods',true);
        $change_goods=str_replace("\n","<br>", $change_goods);

        $low=$this->input->post('low',true);
        if($low!='1'){
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'ori_price'=>$ori_price,
            'oori_price'=>$oori_price,
            'shop_price'=>$shop_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'direction'=>$direction,
            'attention_list'=>$attention_list,
            'change_goods'=>$change_goods,
            'is_show'=>'1',
        );
        $goods_id=$this->User_model->user_insert('v_goods',$data);





            $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
            $date_attr_goods_insert=array();
            foreach($attr_arr_up as $k=>$v)
            {

                $date_attr_goods_insert['attr_id']=$attr_id;
                $date_attr_goods_insert['goods_id']=$goods_id;
                $date_attr_goods_insert['add_time']=time();
                $date_attr_goods_insert['attr_val']=$v['date'];
                $date_attr_goods_insert['attr_price']=0;
                // echo '<pre>';print_r($date_attr_goods_insert);
                $pid=$this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
                foreach($v['attr']['radio'] as $k1=>$v1)
                {
                    if($v1['attr_name']!='')
                    {
                        $temp_arr=array(
                            'attr_name'=>$v1['attr_name'],
                            'attr_type'=>'2',
                            'add_time'=>time()
                        );
                        $radio_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
                        foreach($v1['attr_list'] as $k2=>$v2)
                        {
                            $temp_arr2['attr_id']=$radio_attr_id;
                            $temp_arr2['goods_id']=$goods_id;
                            $temp_arr2['add_time']=time();
                            $temp_arr2['attr_val']=$v2['attr_value'];
                            $temp_arr2['attr_price']=$v2['attr_price'];
                            $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                            $temp_arr2['pid']=$pid;
                            $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
                        }
                    }
                }


                $temp_arr=array(
                    'attr_name'=>'人数多选',
                    'attr_type'=>'3',
                    'add_time'=>time()
                );
                $check_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

                foreach($v['attr']['check'] as $k3=>$v3)
                {


                    $temp_arr2['attr_id']=$check_attr_id    ;
                    $temp_arr2['goods_id']=$goods_id;
                    $temp_arr2['add_time']=time();
                    $temp_arr2['attr_val']=$v3['attr_value'];
                    $temp_arr2['attr_price']=$v3['attr_price'];

                    $temp_arr2['pid']=$pid;
                    $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);

                }

            }

        redirect(base_url("newadmin/goods_add?act_id={$act_id}"));
    }

    public function products_insert_new()
    {

        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);
        // echo '<pre>';
        //print_r($_POST);exit();
        if(!$oori_price){
            $oori_price=$ori_price;
        }
        if( $goods_number<=0 && $ori_price<=0){
            return false;
        }

        $date_name=$this->input->post('date_name',true);
        if(!$date_name){
            $date_name='日期';
        }
        $date=$this->input->post('date_val',true);
        $date_price=$this->input->post('date_price',true);
        $date_arr=array();
        foreach($date as $k=>$v )
        {
            $date_arr[$k]['date']=strtotime($v);
            $date_arr[$k]['date_price']=0;
            if(isset($date_price[$k]))
            {
                $date_arr[$k]['date_price']=$date_price[$k];
            }
        }
        $date_attr_insert=array(
            'attr_name'=>$date_name,
            'attr_type'=>'1',
            'add_time'=>time()
        );
      //  echo '<pre>';print_r($date_arr);exit();
        $form=$this->input->post('form',TRUE);
        $attr_arr=array();
        $data['form']=1;
        for($i=1;$i<=$form;$i++)
        {
            $attr_name='attr_name'.$i;
            $attr='attr'.$i;
            $attr_price='attr_price'.$i;
            $attr_type='attr_type'.$i;
            $supply_info_one='supply_info_one'.$i;
            $supply_info_two='supply_info_two'.$i;
            $attr_name= $this->input->post($attr_name);
            $attr_type= $this->input->post($attr_type);
            if($attr_name)
            {
                $attr_arr[$i]['attr_name']=$attr_name;
                if(!$attr_type){
                    $attr_arr[$i]['attr_type']=2;
                }else{
                    $attr_arr[$i]['attr_type']=$attr_type;
                }


                $attr_arr[$i]['attr']=  array_filter($this->input->post($attr));
                $attr_arr[$i]['attr_price']= array_filter($this->input->post($attr_price)) ;

                $attr_arr[$i]['supply_info_one']= array_filter($this->input->post($supply_info_one)) ;
                $attr_arr[$i]['supply_info_two']= array_filter($this->input->post($supply_info_two)) ;
                foreach( $attr_arr[$i]['attr'] as $k=>$v)
                {
                    if(isset($attr_arr[$i]['attr_price'][$k])){
                        $attr_arr[$i]['attr_arr'][]=array(
                            'attr_val'=>$v,
                            'attr_price'=>$attr_arr[$i]['attr_price'][$k],
                            'supply_info_one'=>isset($attr_arr[$i]['supply_info_one'][$k])?$attr_arr[$i]['supply_info_one'][$k]:'',
                            'supply_info_two'=>isset($attr_arr[$i]['supply_info_two'][$k])?$attr_arr[$i]['supply_info_two'][$k]:'',
                        );
                    }else{
                        $attr_arr[$i]['attr_arr'][]=array(
                            'attr_val'=>$v,
                            'attr_price'=>0,
                            'supply_info_one'=>isset($attr_arr[$i]['supply_info_one'][$k])?$attr_arr[$i]['supply_info_one'][$k]:'',
                            'supply_info_two'=>isset($attr_arr[$i]['supply_info_two'][$k])?$attr_arr[$i]['supply_info_two'][$k]:'',
                        );
                    }

                }
                unset( $attr_arr[$i]['attr']);
                unset( $attr_arr[$i]['attr_price']);
                unset( $attr_arr[$i]['supply_info_one']);
                unset( $attr_arr[$i]['supply_info_two']);
                $data['form']++;
            }
        }
        $attr_arr=array_values($attr_arr);

        //echo '<pre>';print_r($_POST);print_r($attr_arr);exit();

        $data['title']=$this->input->post('title',true);
        $data['ipro']=$this->input->post('ipro',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special= $data['special']=$this->input->post('special',true);
      //  $data['range_name']=$this->input->post('range_detail',true);


        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
      //  $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];


        $data['user_id']=$this->input->post('user_id',true);
        $data['business_id']=$this->input->post('business_id',true);
        $data['hot']=$this->input->post('hot',true);
        $data['rec']=$this->input->post('rec',true);

        $seats=$this->input->post('seats',TRUE);
        if(is_array($seats))
        {
            $data['seats']=implode(',',$seats);
        }

        $data['is_show']=$this->input->post('up_down_goods',true);
        if(!$data['is_show'])
        {
            $data['is_show']='0';
        }
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }

        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $data['user_id'].'banner');
            $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
        }else{
            $data['banner_image']='';
        }

        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

        }else{
            $data['banner_hot']='';
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='150',$height='150');
        }else{
            $data['banner_product']='';
        }



        $data['act_status']='2';
        //$data['is_show']='2';
       // $data['special']='2';
        $data['add_time']=time();
        $data['order_sell']=rand(1,100);
        //echo '<pre>';print_r($data);exit();
        $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
        $range_id=$this->input->post('range_id',true);
        if(!is_array($range_id)){
            $range_id=array();
        }
        foreach($range_id as $k=>$v){
            $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
        }
        $this->put_admin_log("添加特价产品{$act_id}");

        $dateto=$this->input->post('dateto',true);
        $pricehas=$this->input->post('pricehas',true);
        $priceno=$this->input->post('priceno',true);
        $pricecom=$this->input->post('pricecom',true);


        $direction=$this->input->post('direction',true);
        $direction=str_replace("\n","<br>", $direction);

        $attention_list=$this->input->post('attention_list',true);
        $attention_list=str_replace("\n","<br>", $attention_list);

        $change_goods=$this->input->post('change_goods',true);
        $change_goods=str_replace("\n","<br>", $change_goods);

        $low=$this->input->post('low',true);
        if($low!='1'){
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'ori_price'=>$ori_price,
            'oori_price'=>$oori_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'direction'=>$direction,
            'attention_list'=>$attention_list,
            'change_goods'=>$change_goods,
            'is_show'=>'1',

        );
        if($special==7){
            $data['shop_price']=$ori_price;
        }
        $goods_id=$this->User_model->user_insert('v_goods',$data);
        if($date_attr_insert['attr_name']!='' )
        {
            $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
            $date_attr_goods_insert=array();
            foreach($date_arr as $k=>$v)
            {
                $date_attr_goods_insert['attr_id']=$attr_id;
                $date_attr_goods_insert['goods_id']=$goods_id;
                $date_attr_goods_insert['add_time']=time();
                $date_attr_goods_insert['attr_val']=$v['date'];
                $date_attr_goods_insert['attr_price']=$v['date_price'];
                // echo '<pre>';print_r($date_attr_goods_insert);
                $this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
            }

        }
        //   exit();
        foreach($attr_arr as $k=>$v){
            $temp_arr=array(
                'attr_name'=>$v['attr_name'],
                'attr_type'=>$v['attr_type'],
                'add_time'=>time()
            );
            $attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
            $temp_arr2=array();
            foreach($v['attr_arr'] as $k1=>$v1){
                $temp_arr2['attr_id']=$attr_id;
                $temp_arr2['goods_id']=$goods_id;
                $temp_arr2['add_time']=time();
                $temp_arr2['attr_val']=$v1['attr_val'];
                $temp_arr2['attr_price']=$v1['attr_price'];

                $temp_arr2['supply_info_one']=$v1['supply_info_one'];
                $temp_arr2['supply_info_two']=$v1['supply_info_two'];
                $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
            }

        }

        redirect(base_url("newadmin/goods_add_new?act_id={$act_id}"));
    }


    public function products_sub_new()
    {
        //echo '<pre>';
        //print_r($_POST);
        //exit();
        $act_id=$this->input->post('act_id',TRUE);
        if(!$act_id){
            return false;
        }
        $temp_row=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_children');
        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);

        // echo '<pre>';
        //print_r($_POST);exit();
        if( $goods_number<=0 ){
            $goods_number=10;
        }
        if($ori_price<=0){
            $ori_price=0;
        }
        if(!$oori_price){
            $oori_price=$ori_price;
        }
        $date_name=$this->input->post('date_name',true);
        if(!$date_name){
            $date_name='日期';
        }
        $date=$this->input->post('date_val',true);
        $date_price=$this->input->post('date_price',true);
        $date_arr=array();
        foreach($date as $k=>$v )
        {
            $date_arr[$k]['date']=strtotime($v);
            $date_arr[$k]['date_price']=0;
            if(isset($date_price[$k]))
            {
                $date_arr[$k]['date_price']=$date_price[$k];
            }
        }

        $date_attr_insert=array(
            'attr_name'=>$date_name,
            'attr_type'=>'1',
            'add_time'=>time()
        );
        $form=$this->input->post('form',TRUE);
        $attr_arr=array();
        $data['form']=1;
        for($i=1;$i<=$form;$i++)
        {
            $attr_name='attr_name'.$i;
            $attr='attr'.$i;
            $attr_price='attr_price'.$i;
            $attr_type='attr_type'.$i;
            $supply_info_one='supply_info_one'.$i;
            $supply_info_two='supply_info_two'.$i;
            $attr_name= $this->input->post($attr_name);
            $attr_type= $this->input->post($attr_type);
            if($attr_name)
            {
                $attr_arr[$i]['attr_name']=$attr_name;
                if(!$attr_type){
                    $attr_arr[$i]['attr_type']=2;
                }else{
                    $attr_arr[$i]['attr_type']=$attr_type;
                }


                $attr_arr[$i]['attr']=  array_filter($this->input->post($attr));
                $attr_arr[$i]['attr_price']= array_filter($this->input->post($attr_price)) ;

                $attr_arr[$i]['supply_info_one']= array_filter($this->input->post($supply_info_one)) ;
                $attr_arr[$i]['supply_info_two']= array_filter($this->input->post($supply_info_two)) ;
                foreach( $attr_arr[$i]['attr'] as $k=>$v)
                {
                    if(isset($attr_arr[$i]['attr_price'][$k])){
                        $attr_arr[$i]['attr_arr'][]=array(
                            'attr_val'=>$v,
                            'attr_price'=>$attr_arr[$i]['attr_price'][$k],
                            'supply_info_one'=>isset($attr_arr[$i]['supply_info_one'][$k])?$attr_arr[$i]['supply_info_one'][$k]:'',
                            'supply_info_two'=>isset($attr_arr[$i]['supply_info_two'][$k])?$attr_arr[$i]['supply_info_two'][$k]:'',
                        );
                    }else{
                        $attr_arr[$i]['attr_arr'][]=array(
                            'attr_val'=>$v,
                            'attr_price'=>0,
                            'supply_info_one'=>isset($attr_arr[$i]['supply_info_one'][$k])?$attr_arr[$i]['supply_info_one'][$k]:'',
                            'supply_info_two'=>isset($attr_arr[$i]['supply_info_two'][$k])?$attr_arr[$i]['supply_info_two'][$k]:'',
                        );
                    }

                }
                unset( $attr_arr[$i]['attr']);
                unset( $attr_arr[$i]['attr_price']);
                unset( $attr_arr[$i]['supply_info_one']);
                unset( $attr_arr[$i]['supply_info_two']);
                $data['form']++;
            }
        }
        $attr_arr=array_values($attr_arr);
        //echo '<pre>';print_r($attr_arr);exit();

        $data['title']=$this->input->post('title',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special=$data['special']=$this->input->post('special',true);




        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
       // $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];

        $data['user_id']=$this->input->post('user_id',true);
        $data['business_id']=$this->input->post('business_id',true);
        $data['hot']=$this->input->post('hot',true);
        $data['rec']=$this->input->post('rec',true);
        $seats=$this->input->post('seats',TRUE);
        if(is_array($seats))
        {
            $data['seats']=implode(',',$seats);
        }

        $data['add_time']=time();

        $day=$this->input->post('day',true);
        //print_r($day);exit();
        $new_day=$day;
        foreach($day as $k=>$v){
            $new_day[$k] = str_replace("\n","<br>", $v);
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $data['day_list']=json_encode($new_day);

        $data['is_show']=$this->input->post('up_down_goods',true);
        if(!$data['is_show']){
            $data['is_show']='0';
        }
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }


        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $data['user_id'].'banner');
            $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
        }
        else
        {
           // $data['banner_image']=$temp_row['banner_image'];
        }

        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');
        }
        else
        {
           // $data['banner_hot']=$temp_row['banner_hot'];
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='260',$height='260');
        }
        else
        {
            //$data['banner_product']=$temp_row['banner_product'];
        }

        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
        $this->put_admin_log("编辑特价产品{$act_id}");

        $dateto=$this->input->post('dateto',true);
        $dateto=str_replace("\n","<br>", $dateto);

        $pricehas=$this->input->post('pricehas',true);
        $pricehas=str_replace("\n","<br>", $pricehas);

        $priceno=$this->input->post('priceno',true);
        $priceno=str_replace("\n","<br>", $priceno);

        $pricecom=$this->input->post('pricecom',true);
        $pricecom=str_replace("\n","<br>", $pricecom);


        $direction=$this->input->post('direction',true);
        $direction=str_replace("\n","<br>", $direction);

        $attention_list=$this->input->post('attention_list',true);
        $attention_list=str_replace("\n","<br>", $attention_list);

        $change_goods=$this->input->post('change_goods',true);
        $change_goods=str_replace("\n","<br>", $change_goods);


        $low=$this->input->post('low',true);
        if($low!='1'){
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'ori_price'=>$ori_price,
            'shop_price'=>$ori_price,
            'oori_price'=>$oori_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'direction'=>$direction,
            'attention_list'=>$attention_list,
            'change_goods'=>$change_goods,
            'is_show'=>'1'
        );
        if($special==7){
            $data['shop_price']=$ori_price;
        }
        //print_r($data);exit();
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_goods');
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'0'),'v_act_range');
        $range_id=$this->input->post('range_id',true);
        if(!is_array($range_id)){
            $range_id=array();
        }
        foreach($range_id as $k=>$v){
            $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
        }
        $this->del_attr();
        $goods_id=$this->User_model->user_insert('v_goods',$data);

        if($date_attr_insert['attr_name']!='' )
        {
            $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
            $date_attr_goods_insert=array();
            foreach($date_arr as $k=>$v)
            {
                $date_attr_goods_insert['attr_id']=$attr_id;
                $date_attr_goods_insert['goods_id']=$goods_id;
                $date_attr_goods_insert['add_time']=time();
                $date_attr_goods_insert['attr_val']=$v['date'];
                $date_attr_goods_insert['attr_price']=$v['date_price'];

                $this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
            }

        }

        foreach($attr_arr as $k=>$v)
        {
            $temp_arr=array(
                'attr_name'=>$v['attr_name'],
                'attr_type'=>$v['attr_type'],
                'add_time'=>time()
            );
            $attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
            $temp_arr2=array();
            foreach($v['attr_arr'] as $k1=>$v1)
            {
                $temp_arr2['attr_id']=$attr_id;
                $temp_arr2['goods_id']=$goods_id;
                $temp_arr2['add_time']=time();
                $temp_arr2['attr_val']=$v1['attr_val'];
                $temp_arr2['attr_price']=$v1['attr_price'];

                $temp_arr2['supply_info_one']=$v1['supply_info_one'];
                $temp_arr2['supply_info_two']=$v1['supply_info_two'];
                $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
            }

        }

        redirect(base_url("newadmin/goods_add_new?act_id={$act_id}"));
    }






    public function products_sub()
    {
        //echo '<pre>';print_r($_POST);exit();
        $act_id=$this->input->post('act_id',TRUE);
        if(!$act_id){
            return false;
        }
        $temp_row=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_children');
        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);
        if(!$oori_price){
            $oori_price=0;
        }
        // echo '<pre>';
        //print_r($_POST);exit();
        if( $goods_number<=0 ){
            $goods_number=10;
        }
        if($ori_price<0){
            $ori_price=0;
        }
        $date_name=$this->input->post('date_name',true);
        if(!$date_name){
            $date_name='日期';
        }

        $up_date=$this->input->post('up_date',true);

        $attr=$this->input->post('attr',TRUE);

        $attr_arr_up=array();
        //print_r($up_date);
        foreach($up_date as $k=>$v)
        {
            $attr_arr_up[]=array('date'=>strtotime($v),'attr'=>$attr[$v]);
            // $attr_arr_up[]['attr']=$attr[$v];
        }

        $date_attr_insert=array(
            'attr_name'=>$date_name,
            'attr_type'=>'1',
            'add_time'=>time()
        );

        $data['title']=$this->input->post('title',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special=$data['special']=$this->input->post('special',true);

        if($special==7)
        {
            $shop_price=$ori_price;
        }else{
            $shop_price=0;
        }

       // $data['range_name']=$this->input->post('range_detail',true);
      //  $data['range_id']=$this->input->post('range_id',true);

        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
        //$data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];

        $data['user_id']=$this->input->post('user_id',true);
        $data['business_id']=$this->input->post('business_id',true);
        $data['hot']=$this->input->post('hot',true);
        $data['rec']=$this->input->post('rec',true);
        $data['add_time']=time();
        $seats=$this->input->post('seats',TRUE);
        if(is_array($seats))
        {
            $data['seats']=implode(',',$seats);
        }


        $day=$this->input->post('day',true);
        //echo '<pre>';print_r($attr_arr);exit();

        $new_day=$day;
        foreach($day as $k=>$v){
            $new_day[$k] = str_replace("\n","<br>", $v);
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $data['day_list']=json_encode($new_day);

        $data['is_show']=$this->input->post('up_down_goods',true);
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }

        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $data['user_id'].'banner');
            $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
        }


        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');
        }

        $data['type']='1';

        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
        $this->put_admin_log("编辑特价产品{$act_id}");


        $dateto=$this->input->post('dateto',true);
        $dateto=str_replace("\n","<br>", $dateto);

        $pricehas=$this->input->post('pricehas',true);
        $pricehas=str_replace("\n","<br>", $pricehas);

        $priceno=$this->input->post('priceno',true);
        $priceno=str_replace("\n","<br>", $priceno);

        $pricecom=$this->input->post('pricecom',true);
        $pricecom=str_replace("\n","<br>", $pricecom);


        $direction=$this->input->post('direction',true);
        $direction=str_replace("\n","<br>", $direction);

        $attention_list=$this->input->post('attention_list',true);
        $attention_list=str_replace("\n","<br>", $attention_list);

        $change_goods=$this->input->post('change_goods',true);
        $change_goods=str_replace("\n","<br>", $change_goods);


        $low=$this->input->post('low',true);
        if($low!='1'){
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'ori_price'=>$ori_price,
            'oori_price'=>$oori_price,
            'shop_price'=>$shop_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'direction'=>$direction,
            'attention_list'=>$attention_list,
            'change_goods'=>$change_goods,
            'is_show'=>'1'
        );
        //print_r($data);exit();
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_goods');
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'0'),'v_act_range');
        $range_id=$this->input->post('range_id',true);
        if(!is_array($range_id)){
            $range_id=array();
        }
        foreach($range_id as $k=>$v){
            $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
        }
        $this->del_attr();
        $goods_id=$this->User_model->user_insert('v_goods',$data);

        $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
        $date_attr_goods_insert=array();
        foreach($attr_arr_up as $k=>$v)
        {

            $date_attr_goods_insert['attr_id']=$attr_id;
            $date_attr_goods_insert['goods_id']=$goods_id;
            $date_attr_goods_insert['add_time']=time();
            $date_attr_goods_insert['attr_val']=$v['date'];
            $date_attr_goods_insert['attr_price']=0;
            // echo '<pre>';print_r($date_attr_goods_insert);
            $pid=$this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
            foreach($v['attr']['radio'] as $k1=>$v1)
            {
                if($v1['attr_name']!='')
                {
                    $temp_arr=array(
                        'attr_name'=>$v1['attr_name'],
                        'attr_type'=>'2',
                        'add_time'=>time()
                    );
                    $radio_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
                    foreach($v1['attr_list'] as $k2=>$v2)
                    {
                        $temp_arr2['attr_id']=$radio_attr_id;
                        $temp_arr2['goods_id']=$goods_id;
                        $temp_arr2['add_time']=time();
                        $temp_arr2['attr_val']=$v2['attr_value'];
                        $temp_arr2['attr_price']=$v2['attr_price'];
                        $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                        $temp_arr2['pid']=$pid;
                        $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
                    }
                }
            }


            $temp_arr=array(
                'attr_name'=>'多选',
                'attr_type'=>'3',
                'add_time'=>time()
            );
            $check_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

            foreach($v['attr']['check'] as $k1=>$v1)
            {


                $temp_arr2['attr_id']=$check_attr_id    ;
                $temp_arr2['goods_id']=$goods_id;
                $temp_arr2['add_time']=time();
                $temp_arr2['attr_val']=$v1['attr_value'];
                $temp_arr2['attr_price']=$v1['attr_price'];

                $temp_arr2['pid']=$pid;
                $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);

            }

        }

        redirect(base_url("newadmin/goods_add?act_id={$act_id}"));
    }


    //特价产品插入
    public function discount_goods_insert()
    {



        $data['title']=$this->input->post('title',true);

        $data['discount_type']=$this->input->post('discount_type',true);
        $data['range']=$this->input->post('range',true);
        $data['range_name']=$this->input->post('range_detail',true);

        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',true);
        $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];

        $data['users']=$this->input->post('users',true);
        $data['users']= str_replace("，",",", $data['users']);


        $data['user_id']=$this->input->post('user_id',true);


        $day=$this->input->post('day',true);
       //print_r($day);exit();
        $new_day=$day;
        foreach($day as $k=>$v){
            $new_day[$k] = str_replace("\n","<br>", $v);
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $data['day_list']=json_encode($new_day);

        $data['is_show']=$this->input->post('up_down_goods',true);
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }

        $banner=$this->upload_image('banner', $data['user_id'].'banner');
        $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');

        $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
        $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

        $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
        $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');


        $data['act_status']='2';
        //$data['is_show']='2';
        $data['special']='2';
        //echo '<pre>';print_r($data);exit();
        $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
        $this->put_admin_log("添加特价产品{$act_id}");

        $dateto=$this->input->post('dateto',true);
        $pricehas=$this->input->post('pricehas',true);
        $priceno=$this->input->post('priceno',true);
        $pricecom=$this->input->post('pricecom',true);


        $goods_number=trim($this->input->post('goods_number',true));
        $shop_price=$this->input->post('shop_price',true);

        $low=$this->input->post('low',true);
        if($low!='1'){
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'shop_price'=>$shop_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'is_show'=>'1'
        );
        if( $goods_number>0 && $shop_price>0)
        {
            $godds_id=$this->User_model->user_insert('v_goods',$data);
        }
        redirect(base_url("newadmin/goods_add?act_id={$act_id}"));
    }



    //特价产品插入
    public function discount_goods_sub()
    {
        if(!isset($_SESSION['admin_id'])){
            redirect(base_url('newadmin/login'));
        }
        $act_id=$this->input->post('act_id',true);
        $data['title']=$this->input->post('title',true);

        $data['discount_type']=$this->input->post('discount_type',true);
        $data['range']=$this->input->post('range',true);
        $data['range_name']=$this->input->post('range_detail',true);
        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',true);
        $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

        $data['content']= $data['content_text'];

        $data['users']=$this->input->post('users',true);
        $data['users']= str_replace("，",",", $data['users']);


        $data['user_id']=$this->input->post('user_id',true);

        $data['hot']=$this->input->post('hot',true);
        if(!$data['hot'])
        {
            $data['hot']=0;
        }
        $day=$this->input->post('day',true);
        //print_r($day);exit();
        $new_day=$day;
        foreach($day as $k=>$v)
        {
            if(stristr($v,'请填写行程描述'))
            {
                unset($new_day[$k]);
            }
        }
        $new_day=array_values($new_day);
        $data['day_list']=json_encode($new_day);
        $data['is_show']=$this->input->post('up_down_goods',true);
        if(!$data['is_show']){
            $data['is_show']='2';
        }
        if($this->input->post('top'))
        {
            $data['displayorder']='1';
        }
      //  $row=$this->User_model->get_select_one('banner_image,banner_product,banner_hot',array('act_id'=>$act_id),'v_activity_children');
        if($_FILES['banner']['error']==0)
        {
            $banner=$this->upload_image('banner', $data['user_id'].'banner');
            $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
        }
        else
        {
            //$data['banner_image']=$row['banner_image'];
        }
        if($_FILES['banner_hot']['error']==0){
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');
        }else{
          //  $data['banner_hot']=$row['banner_hot'];
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');

        }else{
          //  $data['banner_product']=$row['banner_product'];
        }


        //$data['is_show']='2';

        //echo '<pre>';print_r($data);exit();
        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
        $goods_id= $this->User_model->get_select_one('goods_id',array('act_id'=>$act_id),'v_goods');
        $goods_id=$goods_id['goods_id'];
       // $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
        $this->put_admin_log("编辑特价产品{$act_id}");

        $dateto=$this->input->post('dateto',true);
        $pricehas=$this->input->post('pricehas',true);
        $priceno=$this->input->post('priceno',true);
        $pricecom=$this->input->post('pricecom',true);

        $goods_number=trim($this->input->post('goods_number',true));
        $shop_price=$this->input->post('shop_price',true);
        $low=$this->input->post('low',true);
        if($low!='1')
        {
            $low='0';
        }
        $data=array(
            'goods_name'=> $data['title'],
            'goods_number'=>$goods_number,
            'shop_price'=>$shop_price,
            'act_id'=>$act_id,
            'add_time'=>time(),
            'low'=>$low,
            'dateto'=>$dateto,
            'pricehas'=>$pricehas,
            'priceno'=>$priceno,
            'pricecom'=>$pricecom,
            'is_show'=>'1'
        );
        if( $goods_number>0 && $shop_price>0)
        {
            $this->User_model->update_one(array('goods_id'=>$goods_id),$data,$table='v_goods');
           // $this->User_model->user_insert('v_goods',$data);
        }
        redirect(base_url("newadmin/goods_add?act_id={$act_id}"));
    }

    //部分操作log

    public function put_admin_log($log_info)
    {
        $admin_id= $_SESSION['admin_id'];
        $admin_name=$this->User_model->get_select_one($select='admin_name',array('admin_id'=>$admin_id),$table='v_admin_user');
        $log_info=$log_info .';管理员 '.$admin_name['admin_name'].'操作';
        $logs= array(
            'log_time' => time(),
            'user_id'  => $_SESSION['admin_id'],
            'log_info' => $log_info,
            'ip_address'=> $this->real_ip()
        );
        $this->User_model->user_insert('v_admin_log',$logs);
       // $this->Admin_model->add_logs($logs);
    }
    /**
     * 获得用户的真实IP地址
     *
     * @access  public
     * @return  string
     */
    public function real_ip()
    {
        static $realip = NULL;

        if ($realip !== NULL)
        {
            return $realip;
        }

        if (isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip)
                {
                    $ip = trim($ip);

                    if ($ip != 'unknown')
                    {
                        $realip = $ip;

                        break;
                    }
                }
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                if (isset($_SERVER['REMOTE_ADDR']))
                {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
                else
                {
                    $realip = '0.0.0.0';
                }
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $realip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }
    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    function formatParaMap($paraMap,$type=0)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($type || (($k=="Timestamp" || $k=="StartTime" || $k=="EndTime") && !$type))
            {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar='';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    function getSignature($str, $key)
    {
        $signature = "";
        if (function_exists('hash_hmac'))
        {
            $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
        }
        else
        {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) > $blocksize)
            {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*', $hashfunc(
                            ($key ^ $ipad) . $str
                        )
                    )
                )
            );
            $signature = base64_encode($hmac);
        }
        return $signature;
    }

    function https_request($url,$data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data))
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    public function priv_auth()
    {
        if(isset($_SESSION['admin_id']))
        {
            $admin_id=$_SESSION['admin_id'];
            $rs=$this->User_model->get_select_one('role_id',array('admin_id'=>$admin_id),'v_admin_user');
            $role_id=$rs['role_id'];
            $rs=$this->User_model->get_select_one('auth_id',array('role_id'=>$role_id),'v_admin_role');
            $auth_id=$rs['auth_id'];
            $auth_id_arr=explode(',',$auth_id);
            $url_arr=array();
            $url_arr_list=array();
            $url_arr_all=array();
            foreach($auth_id_arr as $k=>$v)
            {
                $rs=$this->User_model->get_select_all('url',array('pid'=>$v),'id','ASC',$table='v_auth_menu');
                foreach($rs as $k1=>$v2)
                {
                    $url_arr[]=$v2['url'];
                }

            }
            foreach($url_arr as $k=>$v)
            {
               // $temp=array();
                $temp=explode('/',$v);
                $url_arr_list[]=$temp;
            }
          //  echo $_SERVER['PHP_SELF'];
            $self_arr=explode('/',$_SERVER['PHP_SELF']);
            $self=array($self_arr[2],$self_arr[3]);

            foreach($url_arr_list as $k=>$v)
            {
                $temp=array($v[3],$v[4]);
                $url_arr_all[]=$temp;
            }
          //  echo "<pre>";
           // $self=array('newadmin','menu_banner');
            if(in_array($self,$url_arr_all))
            {
                echo 1;
            }
            else
            {
                return FALSE;
            }
          //  print_r($self);
           // print_r($url_arr_all);

        }
        else
        {
            return false;
        }

    }

    public  function imagecropper($source_path='./tmp/avatar.png',$key1='test',$key2='time',$target_width='100', $target_height='100',$lt=0)
    {
        $source_info   = getimagesize($source_path);
        $source_width  = $source_info[0];
        $source_height = $source_info[1];
        $source_mime   = $source_info['mime'];
        $source_ratio  = $source_height / $source_width;
        $target_ratio  = $target_height / $target_width;

        // 源图过高
        if ($source_ratio > $target_ratio)
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        }
        // 源图过宽
        elseif ($source_ratio < $target_ratio)
        {
            $cropped_width  = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        }
        // 源图适中
        else
        {
            $cropped_width  = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }
        if($source_mime=='image/jpeg')
        {
            $source_image = imagecreatefromjpeg($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=md5(time().rand(1,99999));
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagejpeg($target_image,$new_image);


            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            if($lt==1)
            {
             return ltrim($new_image,'.');
            }
           return $new_image;
          //  return  substr($new_image,1);
        }
        elseif($source_mime=='image/png')
        {
            $source_image = imagecreatefrompng($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

            $alpha = imagecolorallocatealpha($target_image, 0, 0, 0, 127);
            imagefill($target_image, 0, 0, $alpha);
            $alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
            imagefill($cropped_image, 0, 0, $alpha);
            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagesavealpha($target_image, true);
            imagepng($target_image,$new_image);

            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);

            if($lt==1)
            {
                return ltrim($new_image,'.');
            }
            return $new_image;
        }
        else
        {
            $source_image = imagecreatefromgif($source_path);
            $target_image  = imagecreatetruecolor($target_width, $target_height);
            $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
            // 裁剪
            imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
            // 缩放
            imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
            $type=pathinfo($source_path,PATHINFO_EXTENSION);
            if($key2=='time'){
                $key2=time();
            }
            $new_image='./public/images/crop/'.$key1.'/'.$key2.'.'.$type;
            imagegif($target_image,$new_image);
            imagedestroy($source_image);
            imagedestroy($target_image);
            imagedestroy($cropped_image);
            if($lt==1)
            {
                return ltrim($new_image,'.');
            }
            return $new_image;
        }
    }

    public function del_attr()
    {
        $goods_id=$this->User_model->get_all($select='goods_id',array('is_show'=>'3'),$table='v_goods',$order_title='goods_id');
        $new_goods_id=array();
        foreach($goods_id as $k=>$v)
        {
            $new_goods_id[]=$v['goods_id'];
        }
        $new_goods_id=implode(',',$new_goods_id);
        $where="goods_id IN ($new_goods_id)";

        //$this->User_model->del($where,'v_goods_attr');
    }

    public function auth_or_no()
    {
        if(!isset($_SESSION['admin_id'])){
            redirect(base_url('newadmin/login'));
        }
    }
     //官方自用筛选类产品模版插入
    public function pro_type_add()
    {


        $this->load->view('newadmin/pro_type_add_view');
    }
     public function pro_up()
     {
         $id=$this->input->get('id',TRUE);
         $this->User_model->update_one(array('id'=>$id),array('is_show'=>'1'),$table='v_pro_type');
         redirect($_SERVER['HTTP_REFERER']);
     }
     public function pro_down()
     {
         $id=$this->input->get('id',TRUE);
         $this->User_model->update_one(array('id'=>$id),array('is_show'=>'2'),$table='v_pro_type');
         redirect($_SERVER['HTTP_REFERER']);
     }

     public function pro_del()
     {
         $id=$this->input->get('id',TRUE);
         $this->User_model->update_one(array('id'=>$id),array('is_show'=>'3'),$table='v_pro_type');
         redirect($_SERVER['HTTP_REFERER']);
     }
     public function pro_type_list($page=1)
     {


         $this->auth_or_no();
         $data['count_url']=$this->count_url;
         $data['title'] = trim($this->input->get('title',true));
         $data['time1']=strtotime($this->input->get('time1',true));
         $data['time2']=strtotime($this->input->get('time2',true));
         $is_show= $data['is_show']= $this->input->get('is_show',true);
         if($is_show==null)
         {
             $is_show='1';
             $data['is_show']='1';
         }
         //1210,2273,1862
         $where=" 1= 1";
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
             $where.= " AND name LIKE '%$data[title]%' ";
         }
         $where.="  AND is_show= '$is_show'";


         $select='*';
         // $data['list']=$this->User_model->get_products_list($select,$where,$start,$page_num);

         $data['list']=$this->User_model->get_select_all($select,$where,$order_title='id',$order='ASC',$table='v_pro_type');

         $data['time2']=strtotime($this->input->get('time2'));
         if($this->input->get('test',TRUE)){
             echo '<pre>';

             print_r($data);exit();
         }
         $this->load->view('newadmin/pro_type_list',$data);
     }

     public function pro_type_edit()
     {

         $id=$this->input->get('id',TRUE);
         $data['info']=$this->User_model->get_select_one('id,user_id,name,json',array('id'=>$id),'v_pro_type');
         $json=json_decode($data['info']['json'],TRUE);
         $data['info']['radio']=$json[0];
         $data['info']['check']=$json[1];
       //  echo '<pre>';print_r($data);exit();
         $this->load->view('newadmin/pro_type_add_view',$data);
     }
     public function pro_type_insert()
     {



         echo '<pre>';print_r($_POST);
         $user_id=$this->input->post('user_id',TRUE);
         $name=$this->input->post('name',TRUE);
         $radio=$this->input->post('radio',TRUE);
         if(is_array($radio))
         {
             $radio=array_values($radio);
         }else{
             $radio=array();
         }
         $check=$this->input->post('check',TRUE);
         if(!$check)
         {
          return false;
         }
         $json=json_encode(array($radio,$check));
         $data=array(
             'user_id'=>$user_id,
             'name'=>$name,
             'json'=>$json,
             'add_time'=>time(),
             'is_show'=>'1',
         );
        $id= $this->User_model->user_insert($table='v_pro_type',$data);
         redirect(base_url("newadmin/pro_type_edit?id=$id"));
//         $this->load->view('newadmin/pro_type_add_view');
     }

     public function pro_type_sub()
     {
         $user_id=$this->input->post('user_id',TRUE);
         $id=$this->input->post('id',TRUE);
         $name=$this->input->post('name',TRUE);
         $radio=$this->input->post('radio',TRUE);
         if(is_array($radio))
         {
             $radio=array_values($radio);
         }else{
             $radio=array();
         }
         $check=$this->input->post('check',TRUE);
         if(!$check)
         {
             return false;
         }
         $json=json_encode(array($radio,$check));
         $data=array(
             'user_id'=>$user_id,
             'name'=>$name,
             'json'=>$json,
             'add_time'=>time(),
             'is_show'=>'1',
         );
        $this->User_model->update_one(array('id'=>$id),$data,'v_pro_type');
         redirect(base_url("newadmin/pro_type_edit?id=$id"));
//         $this->load->view('newadmin/pro_type_add_view');
     }
     //筛选类产品增加
     //1当地项目 special=5
     public function products_local_add()
     {
       //  $this->output->enable_profiler(TRUE);
         $act_id=$this->input->get('act_id',TRUE);
         $data['location']=array();
         $data['range_list']=$this->User_model->get_select_all('id,name',"is_hot='1' OR is_down='1'",'id','ASC','v_location');

         //echo '<pre>';print_r($data);exit();
         if($act_id)
         {

             $data['location']=$this->User_model->get_select_all('v_act_range.range_id,v_location.name',array('v_act_range.act_id'=>$act_id,'v_act_range.is_show'=>'1'),'v_act_range.ar_id','ASC','v_act_range',1,'v_location',"v_act_range.range_id=v_location.id");
             if(!is_array($data['location'])){
                 $data['location']=array();
             }

             $data['info']=$this->User_model->get_select_one('act_id,title,user_id,banner_image,banner_product,banner_hot,form,type,xtype,ipro,special,photo_time,business_id,hot,rec,seats,
            content_text,day_list,range,range_name,discount_type,tag,users,is_show',array('act_id'=>$act_id),'v_activity_children');
             $data['seats_arr']=explode(',',$data['info']['seats']);
             $pro=$this->User_model->get_select_one('id,user_id,name,json',array('id'=>$data['info']['ipro']),'v_pro_type');

                 $temp_arr=json_decode($pro['json'],TRUE);
                 //$temp_arr=$temp_arr[0];
                 $data['info']['choose_radio']=$temp_arr[0];
                 $data['info']['choose_check']=$temp_arr[1];


             if($data['info']['type']='0')
             {

             }
             elseif($data['info']['type']='1')
             {
                 $act_id=$data['info']['act_id'];
                 $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,ori_price,oori_price',
                     array('act_id'=>$act_id,'is_show'=>'1'),'v_goods');
                 $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
                 $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
                 $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

                 $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
                 $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
                 $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);


                 $goods_id=$data['goods']['goods_id'];
                 $select='v_goods_attr.goods_attr_id,v_goods_attr.attr_id,v_goods_attr.supply_info_one,v_goods_attr.supply_info_two,v_goods_attr.goods_id,v_goods_attr.pid,v_goods_attr.attr_val,v_goods_attr.attr_price,v_attr.attr_name,v_attr.attr_type';
                 $where=array('v_goods_attr.goods_id'=>$goods_id,'v_goods_attr.is_show'=>'1');
                 $data['attr']=$this->User_model->get_select_all($select,$where,'v_goods_attr.goods_attr_id','ASC','v_goods_attr',1,'v_attr',"v_attr.attr_id=v_goods_attr.attr_id");
                 if(!$data['attr']){
                     $data['attr']=array();

                 }
                 $data['other_attr']=array();
                 $data['date']=array();
                 $data['date']['attr_name']='';
                 $data['date']['attr_type']='';
                 $data['date']['goods_attr_id']=array();
                 $data['date']['attr_val_list']=array();
                 $data['date']['attr_price_list']=array();
                 foreach($data['attr'] as $k=>$v)
                 {
                     if($v['attr_type']==1)
                     {
                         $data['date']['attr_name']=$v['attr_name'];
                         $data['date']['attr_type']=$v['attr_type'];
                         $data['date']['goods_attr_id'][]=$v['goods_attr_id'];
                         $data['date']['attr_val_list'][]=date('Y-n-j',$v['attr_val']);
                         $data['date']['attr_price_list'][]=$v['attr_price'];
                     }
                 }
                 foreach($data['attr'] as $k=>$v)
                 {

                     if($v['attr_type']==2)
                     {
                         if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                         {
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_name']=$v['attr_name'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['attr_type']=$v['attr_type'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['pid']=$v['pid'];
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][$v['attr_id']]['list'][]=$v;
                             // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                         }
                     }
                     elseif($v['attr_type']==3)
                     {
                         if(($key=array_search($v['pid'],$data['date']['goods_attr_id']))!==FALSE)
                         {
                             $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']][]=$v;
                             // $data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]=array_values($data['other_attr'][$data['date']['attr_val_list'][$key]][$v['attr_type']]);
                         }
                     }
                 }

                 //unset($data['attr']);
                 //$data['other_attr']=array_values($data['other_attr']);
                 $data['day']=$data['info']['day_list'];
                 $data['day']=json_decode( $data['day'],true);
                 foreach( $data['day'] as $k =>$v)
                 {
                     $data['day'][$k]=   str_replace("<br>","\n",$v);
                 }
             }

         }
         else
         {
            // $data=array();
             $data['user_id']=$this->input->get('user_id',TRUE);
             if(!$data['user_id'])
             {
                 $data['user_id']=0;
             }
         }
         // $time=time();
         $time_first=$time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
         $timeend= strtotime('+2 month', $time);
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
         $tdaysend=end($data['date']['cal']);
         // $days=$tdaysend['time']-$time_first;
         $data['days']=$this->prDates($time_first,$tdaysend['time']);
         // echo $time_first;
         //  echo $tdaysend['time'];
         if($this->input->get('test')){
             echo '<pre>';
             print_r($data);
             // print_r($data);
             exit();

         }
         $data['type_info']=$this->User_model->get_select_all('id,user_id,name,json',array('is_show'=>'1'),'id','ASC','v_pro_type');
         foreach($data['type_info'] as $k=>$v)
         {
             $temp_arr=json_decode($v['json'],TRUE);
             //$temp_arr=$temp_arr[0];
             $data['choose_radio'][$v['id']]=$temp_arr[0];
             $data['choose_check'][$v['id']]=$temp_arr[1];
         }
          //  echo '<pre>';print_r($data);exit();
         $this->load->view('newadmin/products_local',$data);
     }

     public function products_local_insert()
     {

         $goods_number=trim($this->input->post('goods_number',true));
         $ori_price=$this->input->post('ori_price',true);
         $oori_price=$this->input->post('oori_price',true);
         if(!$oori_price){
             $oori_price=$ori_price;
         }
         if( $goods_number<=0 && $ori_price<=0){
             return false;
         }
         $date_name=$this->input->post('date_name',true);
         if(!$date_name){
             $date_name='日期';
         }
         $up_date=$this->input->post('up_date',true);

         $attr=$this->input->post('attr',TRUE);

         $attr_arr_up=array();
         //print_r($up_date);
         if(is_array($up_date))
         {
             foreach($up_date as $k=>$v)
             {
                 $attr_arr_up[]=array('date'=>strtotime($v),'attr'=>$attr[$v]);
                 // $attr_arr_up[]['attr']=$attr[$v];
             }
         }else{
             $up_date=array();
         }

         //echo '<pre>';print_r($attr_arr_up);exit();
         $date_attr_insert=array(
             'attr_name'=>$date_name,
             'attr_type'=>'1',
             'add_time'=>time()
         );


         $data['title']=$this->input->post('title',true);
         $data['ipro']=$this->input->post('ipro',true);

         $data['xtype']=$this->input->post('xtype',true);
         $data['tctype']=$this->input->post('tctype',true);
         $special= $data['special']=$this->input->post('special',true);
         //$data['range_name']=$this->input->post('range_detail',true);

         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content_text']=$this->input->post('content_text',true);
         $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

         $data['content']= $data['content_text'];

         //$data['users']=$this->input->post('users',true);$data['users']= str_replace("，",",", $data['users']);


         $data['user_id']=$this->input->post('user_id',true);
         $data['business_id']=$this->input->post('business_id',true);
         $data['hot']=$this->input->post('hot',true);
         $data['rec']=$this->input->post('rec',true);

         $seats=$this->input->post('seats',TRUE);
         if(is_array($seats))
         {
             $data['seats']=implode(',',$seats);
         }
         $day=$this->input->post('day',true);
         //print_r($day);exit();
         $new_day=$day;
         foreach($day as $k=>$v){
             $new_day[$k] = str_replace("\n","<br>", $v);
             if(stristr($v,'请填写行程描述'))
             {
                 unset($new_day[$k]);
             }
         }
         $new_day=array_values($new_day);
         $data['day_list']=json_encode($new_day);

         $data['is_show']=$this->input->post('up_down_goods',true);
         if(! $data['is_show']){
             $data['is_show']='0';
         }
         if($this->input->post('top'))
         {
             $data['displayorder']='1';
         }

         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }else{
             $data['banner_image']='';
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

         }else{
             $data['banner_hot']='';
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');
         }else{
             $data['banner_product']='';
         }



         $data['act_status']='2';
         //$data['is_show']='2';
      //   $data['special']='5';
         $data['add_time']=time();
         //echo '<pre>';print_r($data);exit();
         $data['type']='1';
         $act_id=$this->User_model->user_insert($table='v_activity_children',$data);

         $range_id=$this->input->post('range_id',true);
         if(!is_array($range_id)){
             $range_id=array();
         }
         foreach($range_id as $k=>$v){
             $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
         }

         $this->put_admin_log("添加特价产品{$act_id}");

         $dateto=$this->input->post('dateto',true);
         $pricehas=$this->input->post('pricehas',true);
         $priceno=$this->input->post('priceno',true);
         $pricecom=$this->input->post('pricecom',true);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);

         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'ori_price'=>$ori_price,
             'oori_price'=>$oori_price,
             'act_id'=>$act_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         if($special==7){
             $data['shop_price']=$ori_price;
         }
         $goods_id=$this->User_model->user_insert('v_goods',$data);
         $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
         $date_attr_goods_insert=array();
         foreach($attr_arr_up as $k=>$v)
         {

             $date_attr_goods_insert['attr_id']=$attr_id;
             $date_attr_goods_insert['goods_id']=$goods_id;
             $date_attr_goods_insert['add_time']=time();
             $date_attr_goods_insert['attr_val']=$v['date'];
             $date_attr_goods_insert['attr_price']=0;
             // echo '<pre>';print_r($date_attr_goods_insert);
             $pid=$this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
             foreach($v['attr']['radio'] as $k1=>$v1)
             {
                 if($v1['attr_name']!='')
                 {
                     $temp_arr=array(
                         'attr_name'=>$v1['attr_name'],
                         'attr_type'=>'2',
                         'add_time'=>time()
                     );
                     $radio_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
                     foreach($v1['attr_list'] as $k2=>$v2)
                     {
                         $temp_arr2['attr_id']=$radio_attr_id;
                         $temp_arr2['goods_id']=$goods_id;
                         $temp_arr2['add_time']=time();
                         $temp_arr2['attr_val']=$v2['attr_value'];
                         $temp_arr2['attr_price']=$v2['attr_price'];
                         $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                         $temp_arr2['pid']=$pid;
                         $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
                     }
                 }
             }

             $temp_arr=array(
                 'attr_name'=>'多选',
                 'attr_type'=>'3',
                 'add_time'=>time()
             );
             $check_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

             foreach($v['attr']['check'] as $k1=>$v1)
             {


                 $temp_arr2['attr_id']=$check_attr_id    ;
                 $temp_arr2['goods_id']=$goods_id;
                 $temp_arr2['add_time']=time();
                 $temp_arr2['attr_val']=$v1['attr_value'];
                 $temp_arr2['attr_price']=$v1['attr_price'];
                 $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                 $temp_arr2['pid']=$pid;
                 $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);

             }

         }

         redirect(base_url("newadmin/products_local_add?act_id={$act_id}"));
     }


     public function products_local_sub()
     {
         //echo '<pre>';
         //print_r($_POST);
         //exit();
         $act_id=$this->input->post('act_id',TRUE);
         if(!$act_id){
             return false;
         }
         $temp_row=$this->User_model->get_select_one('*',array('act_id'=>$act_id),'v_activity_children');
         $goods_number=trim($this->input->post('goods_number',true));
         $ori_price=$this->input->post('ori_price',true);
         $oori_price=$this->input->post('oori_price',true);
         if(!$oori_price){
             $oori_price=0;
         }
         // echo '<pre>';
         //print_r($_POST);exit();
         if( $goods_number<=0 ){
             $goods_number=10;
         }
         if($ori_price<0){
             $ori_price=0;
         }
         $date_name=$this->input->post('date_name',true);
         if(!$date_name){
             $date_name='日期';
         }

         $up_date=$this->input->post('up_date',true);

         $attr=$this->input->post('attr',TRUE);

         $attr_arr_up=array();
         //print_r($up_date);
         foreach($up_date as $k=>$v)
         {
             $attr_arr_up[]=array('date'=>strtotime($v),'attr'=>$attr[$v]);
             // $attr_arr_up[]['attr']=$attr[$v];
         }

         $date_attr_insert=array(
             'attr_name'=>$date_name,
             'attr_type'=>'1',
             'add_time'=>time()
         );


         $data['title']=$this->input->post('title',true);

         $data['xtype']=$this->input->post('xtype',true);
         $data['tctype']=$this->input->post('tctype',true);
         $special=$data['special']=$this->input->post('special',true);


         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content_text']=$this->input->post('content_text',true);
         $data['content_text'] = str_replace("\n","<br>", $data['content_text']);

         $data['content']= $data['content_text'];

         //$data['users']=$this->input->post('users',true);$data['users']= str_replace("，",",", $data['users']);
         $seats=$this->input->post('seats',TRUE);
         if(is_array($seats))
         {
             $data['seats']=implode(',',$seats);
         }

         $data['user_id']=$this->input->post('user_id',true);
         $data['business_id']=$this->input->post('business_id',true);
         $data['hot']=$this->input->post('hot',true);
         $data['rec']=$this->input->post('rec',true);
     $data['add_time']=time();

         $data['is_show']=$this->input->post('up_down_goods',true);
         if($this->input->post('top'))
         {
             $data['displayorder']='1';
         }




         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }
         else
         {
             //$data['banner_image']=$temp_row['banner_image'];
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='360');
         }
         else
         {
            // $data['banner_hot']=$temp_row['banner_hot'];
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='260',$height='260');
         }
         else
         {
            // $data['banner_product']=$temp_row['banner_product'];
         }
         $data['type']='1';

         $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');
         $this->put_admin_log("编辑特价产品{$act_id}");


         $dateto=$this->input->post('dateto',true);
         $dateto=str_replace("\n","<br>", $dateto);

         $pricehas=$this->input->post('pricehas',true);
         $pricehas=str_replace("\n","<br>", $pricehas);

         $priceno=$this->input->post('priceno',true);
         $priceno=str_replace("\n","<br>", $priceno);

         $pricecom=$this->input->post('pricecom',true);
         $pricecom=str_replace("\n","<br>", $pricecom);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);


         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'ori_price'=>$ori_price,
             'oori_price'=>$oori_price,
             'act_id'=>$act_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         if($special==7){
             $data['shop_price']=$ori_price;
         }
         //print_r($data);exit();
         $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_goods');

         $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'0'),'v_act_range');
         $range_id=$this->input->post('range_id',true);
         if(!is_array($range_id)){
             $range_id=array();
         }
         foreach($range_id as $k=>$v){
             $this->User_model->user_insert($table='v_act_range',array('act_id'=>$act_id,'range_id'=>$v,'addtime'=>time()));
         }
         $this->del_attr();
         $goods_id=$this->User_model->user_insert('v_goods',$data);

         $attr_id=$this->User_model-> user_insert($table='v_attr',$date_attr_insert);
         $date_attr_goods_insert=array();
         foreach($attr_arr_up as $k=>$v)
         {

             $date_attr_goods_insert['attr_id']=$attr_id;
             $date_attr_goods_insert['goods_id']=$goods_id;
             $date_attr_goods_insert['add_time']=time();
             $date_attr_goods_insert['attr_val']=$v['date'];
             $date_attr_goods_insert['attr_price']=0;
             // echo '<pre>';print_r($date_attr_goods_insert);
             $pid=$this->User_model-> user_insert($table='v_goods_attr',$date_attr_goods_insert);
             foreach($v['attr']['radio'] as $k1=>$v1)
             {
                 if($v1['attr_name']!='')
                 {
                     $temp_arr=array(
                         'attr_name'=>$v1['attr_name'],
                         'attr_type'=>'2',
                         'add_time'=>time()
                     );
                     $radio_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);
                     foreach($v1['attr_list'] as $k2=>$v2)
                     {
                         $temp_arr2['attr_id']=$radio_attr_id;
                         $temp_arr2['goods_id']=$goods_id;
                         $temp_arr2['add_time']=time();
                         $temp_arr2['attr_val']=$v2['attr_value'];
                         $temp_arr2['attr_price']=$v2['attr_price'];
                         $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                         $temp_arr2['pid']=$pid;
                         $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
                     }
                 }
             }


             $temp_arr=array(
                 'attr_name'=>'多选',
                 'attr_type'=>'3',
                 'add_time'=>time()
             );
             $check_attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

             foreach($v['attr']['check'] as $k1=>$v1)
             {


                 $temp_arr2['attr_id']=$check_attr_id    ;
                 $temp_arr2['goods_id']=$goods_id;
                 $temp_arr2['add_time']=time();
                 $temp_arr2['attr_val']=$v1['attr_value'];
                 $temp_arr2['attr_price']=$v1['attr_price'];
                 $temp_arr2['supply_info_one']=$v2['supply_info_one'];
                 $temp_arr2['pid']=$pid;
                 $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);

             }

         }

         redirect(base_url("newadmin/products_local_add?act_id={$act_id}"));
     }

     //当地项目列表
     public function products_local_list($page=1)
     {
         $this->auth_or_no();
         $data['count_url']=$this->count_url;
         $data['title'] = trim($this->input->get('title',true));
         $data['time1']=strtotime($this->input->get('time1',true));
         $data['time2']=strtotime($this->input->get('time2',true));
         $is_show= $data['is_show']= $this->input->get('is_show',true);
         if($is_show==null)
         {
             $is_show='1';
             $data['is_show']='1';
         }
         //1210,2273,1862
         $where="v_activity_children.special IN ('4','5','6','7') AND v_goods.is_show='1'";
         if($data['time1'])
         {
             $where.=" AND v_activity_children.add_time >=$data[time1]";
         }
         if($data['time2'])
         {
             $data['time2']+=86400;
             $where.="  AND v_activity_children.add_time <=$data[time2]";
         }
         if($data['title'])
         {
             $where.= " AND v_activity_children.title LIKE '%$data[title]%' ";
         }
         $where.="  AND v_activity_children.is_show= '$is_show'";

         // echo $where;exit();
         $page_num =100;
         $data['now_page'] = $page;
         $count = $this->User_model->get_products_count($where,'v_activity_children');
         $data['max_page'] = ceil($count['count']/$page_num);
         if($page>$data['max_page'])
         {
             $page=1;
         }
         $start = ($page-1)*$page_num;
         $select='v_activity_children.type,v_activity_children.user_id,v_activity_children.act_id,v_activity_children.title,v_activity_children.add_time,v_activity_children.displayorder,goods_id,v_activity_children.special';
         // $data['list']=$this->User_model->get_products_list($select,$where,$start,$page_num);

         $data['list']=$this->User_model->get_products_list_all($select,$where,$start,$page_num,'1');
        foreach($data['list'] as $k=>$v)
        {
            switch($v['special'])
            {
                case '4':
                    $data['list'][$k]['special_name']='特价线路';
                    break;
                case '5':
                    $data['list'][$k]['special_name']='当地项目';
                    break;
                case '6':
                    $data['list'][$k]['special_name']='包车';
                    break;
                case '7':
                    $data['list'][$k]['special_name']='当地特产';
                    break;
            }
        }
         $data['time2']=strtotime($this->input->get('time2'));
         if($this->input->get('test',TRUE)){
             echo '<pre>';

             print_r($data);exit();
         }
         $this->load->view('newadmin/products_local_list',$data);

     }
     //旅拍产品列表

     public function photo_list($page=1)
     {
         $this->auth_or_no();
         $data['count_url']=$this->count_url;
         $data['title'] = trim($this->input->get('title',true));
         $data['time1']=strtotime($this->input->get('time1',true));
         $data['time2']=strtotime($this->input->get('time2',true));
         $is_show= $data['is_show']= $this->input->get('is_show',true);
         if($is_show==null)
         {
             $is_show='1';
             $data['is_show']='1';
         }
         //1210,2273,1862
         $where="v_goods.is_show='1'";
         if($data['time1'])
         {
             $where.=" AND v_ts.add_time >=$data[time1]";
         }
         if($data['time2'])
         {
             $data['time2']+=86400;
             $where.="  AND v_ts.add_time <=$data[time2]";
         }
         if($data['title'])
         {
             $where.= " AND v_ts.title LIKE '%$data[title]%' ";
         }
         $where.="  AND v_ts.is_show= '$is_show'";

         // echo $where;exit();
         $page_num =100;
         $data['now_page'] = $page;
         $count = $this->User_model->get_photo_count($where,'v_ts');
         $data['max_page'] = ceil($count['count']/$page_num);
         if($page>$data['max_page'])
         {
             $page=1;
         }
         $start = ($page-1)*$page_num;
         $select='v_ts.type,v_ts.user_id,v_ts.ts_id,v_ts.title,v_ts.add_time,v_ts.displayorder,goods_id';
         // $data['list']=$this->User_model->get_products_list($select,$where,$start,$page_num);

         $data['list']=$this->User_model->get_photo_list($select,$where,$start,$page_num);

         $data['time2']=strtotime($this->input->get('time2'));
         if($this->input->get('test',TRUE)){
             echo '<pre>';

             print_r($data);exit();
         }
         $this->load->view('newadmin/products_photo_list',$data);

     }

//后台旅拍增加
     public function trip_photo_add()
     {
         $this->auth_or_no();
         $ts_id=$this->input->get('ts_id',TRUE);

         // echo '<pre>';print_r($data);exit();
         if($ts_id)
         {
             $data['info']=$this->User_model->get_select_one('*',array('ts_id'=>$ts_id),'v_ts');
             $data['info']['attr_arr']=json_decode($data['info']['attr_json'],TRUE);
             $data['attr_arr']=json_decode($data['info']['attr_json'],TRUE);
             $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,ori_price,oori_price,front_price',
                 array('ts_id'=>$ts_id,'is_show'=>'1'),'v_goods');
             $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
             $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
             $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

             $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
             $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
             $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);
             $data['date']['attr_val_list']=array();
             if(is_array($data['info']['attr_arr'])){
                 foreach( $data['info']['attr_arr'] as $k=>$v)
                 {
                     $data['date']['attr_val_list'][]=$k;
                 }
             }
         }
         else
         {
             $data=array();
             $data['user_id']=$this->input->get('user_id',TRUE);
             if(!$data['user_id'])
             {
                 $data['user_id']=0;
             }
         }
         // $time=time();
         $time_first=$time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
         $timeend= strtotime('+3 month', $time);
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
         $tdaysend=end($data['date']['cal']);
         // $days=$tdaysend['time']-$time_first;
         $data['days']=$this->prDates($time_first,$tdaysend['time']);
         // echo $time_first;
         //  echo $tdaysend['time'];
         if($this->input->get('test')){
             echo '<pre>';
             print_r($data);
             // print_r($data);
             exit();

         }
         // $this->load->view('newadmin/goods_add_edit_new',$data);
         $this->load->view('newadmin/trip_photo_view',$data);
     }

     //后台旅拍增加
     public function trip_photo_add_new()
     {
         $this->auth_or_no();
         $ts_id=$this->input->get('ts_id',TRUE);

         // echo '<pre>';print_r($data);exit();
         if($ts_id)
         {
             $data['info']=$this->User_model->get_select_one('*',array('ts_id'=>$ts_id),'v_ts');
             $data['info']['attr_arr']=json_decode($data['info']['attr_json'],TRUE);
             $data['attr_arr']=json_decode($data['info']['attr_json'],TRUE);
             $data['goods']=$this->User_model->get_select_one('goods_id,direction,attention_list,change_goods,goods_name,goods_number,shop_price,dateto,pricehas,priceno,low,pricecom,ori_price,oori_price,front_price',
                 array('ts_id'=>$ts_id,'is_show'=>'1'),'v_goods');
             $data['goods']['pricecom']= str_replace("<br>","\n", $data['goods']['pricecom']);
             $data['goods']['pricehas']= str_replace("<br>","\n", $data['goods']['pricehas']);
             $data['goods']['priceno']= str_replace("<br>","\n", $data['goods']['priceno']);

             $data['goods']['direction']= str_replace("<br>","\n", $data['goods']['direction']);
             $data['goods']['attention_list']= str_replace("<br>","\n", $data['goods']['attention_list']);
             $data['goods']['change_goods']= str_replace("<br>","\n", $data['goods']['change_goods']);
             $data['date']['attr_val_list']=array();
             if(is_array($data['info']['attr_arr'])){
                 foreach( $data['info']['attr_arr'] as $k=>$v)
                 {
                     $data['date']['attr_val_list'][]=$k;
                 }
             }
         }
         else
         {
             $data=array();
             $data['user_id']=$this->input->get('user_id',TRUE);
             if(!$data['user_id'])
             {
                 $data['user_id']=0;
             }
         }
         // $time=time();
         $time_first=$time=strtotime(date('Y',time()).'-'.date('n',time()).'-1');
         $timeend= strtotime('+3 month', $time);
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
         $tdaysend=end($data['date']['cal']);
         // $days=$tdaysend['time']-$time_first;
         $data['days']=$this->prDates($time_first,$tdaysend['time']);
         // echo $time_first;
         //  echo $tdaysend['time'];
         if($this->input->get('test')){
             echo '<pre>';
             print_r($data);
             // print_r($data);
             exit();

         }
         // $this->load->view('newadmin/goods_add_edit_new',$data);
         $this->load->view('newadmin/trip_photo_new_view',$data);
     }



     public function trip_photo_insert()
     {
         //echo '<pre>';print_r($_POST);exit();
         $goods_number=trim($this->input->post('goods_number',true));
         $ori_price=$this->input->post('ori_price',true);
         $oori_price=$this->input->post('oori_price',true);
         $front_price=$this->input->post('front_price',true);

         // echo '<pre>';
         //print_r($_POST);exit();
         if( $ori_price<0){
             $ori_price=0;
         }
         if(!$oori_price){
             $oori_price=$ori_price;
         }
         if( $goods_number<0 ){
             $goods_number=10;
         }
        $attr_arr=$this->input->post('attr',TRUE);
         foreach($attr_arr as $k=>$v)
         {
            if(!$v['str_price'])
            {
                echo '套系基准价不正确';exit();
            }
         }

         //  echo '<pre>';print_r($date_arr);exit();

         $data['attr_json']=json_encode($attr_arr);

         //echo '<pre>';print_r($_POST);print_r($attr_arr);exit();
         $data['title']=$this->input->post('title',true);

         $data['type']=trim($this->input->post('type',true));
         $data['hotelstars']=trim($this->input->post('hotelstars',true));
         $data['hoteldays']=trim($this->input->post('hoteldays',true));
         $data['flighttickets']=trim($this->input->post('flighttickets',true));

         $data['photo_time']=trim($this->input->post('photo_time',true));
         $data['flighttickets']=trim($this->input->post('flighttickets',true));


         $data['flight']=trim($this->input->post('flight',true));




         $data['range_name']=$this->input->post('range_detail',true);
         if(!$data['range_name']){
            echo '目的地填写不正确';exit();
         }

         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content']=$this->input->post('content',true);




         $data['user_id']=$this->input->post('user_id',true);
         $data['business_id']=$this->input->post('business_id',true);




         $data['is_show']=$this->input->post('up_down_goods',true);
         if(!$data['is_show'])
         {
             $data['is_show']='0';
         }

         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }else{
             $data['banner_image']='';
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

         }else{
             $data['banner_hot']='';
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='300',$height='300');
         }else{
             $data['banner_product']='';
         }




         $data['add_time']=time();
         //echo '<pre>';print_r($data);exit();
         $ts_id=$this->User_model->user_insert($table='v_ts',$data);
         $this->put_admin_log("添加旅拍{$ts_id}");

         $dateto=$this->input->post('dateto',true);
         $pricehas=$this->input->post('pricehas',true);
         $priceno=$this->input->post('priceno',true);
         $pricecom=$this->input->post('pricecom',true);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);

         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'ori_price'=>$ori_price,
             'oori_price'=>$oori_price,
             'front_price'=>$front_price,
             'ts_id'=>$ts_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         $goods_id=$this->User_model->user_insert('v_goods',$data);



         redirect(base_url("newadmin/trip_photo_add?ts_id={$ts_id}"));

     }



     public function trip_photo_sub()
     {
         //echo '<pre>';print_r($_POST);exit();
         $goods_number=trim($this->input->post('goods_number',true));
         $ori_price=$this->input->post('ori_price',true);
         $oori_price=$this->input->post('oori_price',true);
         $front_price=$this->input->post('front_price',true);

         $ts_id=$this->input->post('ts_id',true);
          //  $rs=$this->User_model->get_select_one('banner_image,banner_hot,banner_product',array('ts_id'=>$ts_id),'v_ts');
         // echo '<pre>';
         //print_r($_POST);exit();
         if( $ori_price<0){
             $ori_price=0;
         }
         if(!$oori_price){
             $oori_price=$ori_price;
         }
         if( $goods_number<0 ){
             $goods_number=10;
         }
         $attr_arr=$this->input->post('attr',TRUE);
         foreach($attr_arr as $k=>$v)
         {
             if(!$v['str_price'])
             {
                 echo '套系基准价不正确';exit();
             }
         }

           //echo '<pre>';print_r($attr_arr);exit();

         $data['attr_json']=json_encode($attr_arr);

         //echo '<pre>';print_r($_POST);print_r($attr_arr);exit();
         $data['title']=$this->input->post('title',true);

         $data['hot']=trim($this->input->post('hot',true));
         $data['rec']=trim($this->input->post('rec',true));


         $data['range_name']=$this->input->post('range_detail',true);
         if(!$data['range_name']){
             echo '目的地填写不正确';exit();
         }

         $data['tag']=$this->input->post('tag',true);
         $data['tag']= str_replace("，",",", $data['tag']);

         $data['content']=$this->input->post('content',true);



         $data['title']=$this->input->post('title',true);

         $data['type']=trim($this->input->post('type',true));
         $data['hotelstars']=trim($this->input->post('hotelstars',true));
         $data['hoteldays']=trim($this->input->post('hoteldays',true));
         $data['flighttickets']=trim($this->input->post('flighttickets',true));

         $data['photo_time']=trim($this->input->post('photo_time',true));
         $data['flighttickets']=trim($this->input->post('flighttickets',true));


         $data['flight']=trim($this->input->post('flight',true));
         $data['user_id']=$this->input->post('user_id',true);
         $data['business_id']=$this->input->post('business_id',true);




         $data['is_show']=$this->input->post('up_down_goods',true);
         if(!$data['is_show'])
         {
             $data['is_show']='0';
         }

         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['banner_image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }else{
             //$data['banner_image']=$rs['banner_image'];
         }

         if($_FILES['banner_hot']['error']==0)
         {
             $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
             $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

         }else{
             //$data['banner_hot']=$rs['banner_hot'];
         }

         if($_FILES['banner_product']['error']==0)
         {
             $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
             $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='260',$height='260');
         }else{
           //  $data['banner_product']=$rs['banner_product'];
         }




         $data['add_time']=time();
         //echo '<pre>';print_r($data);exit();
        // $ts_id=$this->User_model->user_insert($table='v_ts',$data);

         $this->User_model->update_one(array('ts_id'=>$ts_id),$data,$table='v_ts');
         $this->put_admin_log("修改旅拍{$ts_id}");

         $dateto=$this->input->post('dateto',true);
         $pricehas=$this->input->post('pricehas',true);
         $priceno=$this->input->post('priceno',true);
         $pricecom=$this->input->post('pricecom',true);


         $direction=$this->input->post('direction',true);
         $direction=str_replace("\n","<br>", $direction);

         $attention_list=$this->input->post('attention_list',true);
         $attention_list=str_replace("\n","<br>", $attention_list);

         $change_goods=$this->input->post('change_goods',true);
         $change_goods=str_replace("\n","<br>", $change_goods);

         $low=$this->input->post('low',true);
         if($low!='1'){
             $low='0';
         }
         $data=array(
             'goods_name'=> $data['title'],
             'goods_number'=>$goods_number,
             'ori_price'=>$ori_price,
             'oori_price'=>$oori_price,
             'front_price'=>$front_price,
             'ts_id'=>$ts_id,
             'add_time'=>time(),
             'low'=>$low,
             'dateto'=>$dateto,
             'pricehas'=>$pricehas,
             'priceno'=>$priceno,
             'pricecom'=>$pricecom,
             'direction'=>$direction,
             'attention_list'=>$attention_list,
             'change_goods'=>$change_goods,
             'is_show'=>'1'
         );
         $this->User_model->update_one(array('ts_id'=>$ts_id),array('is_show'=>'2'),$table='v_goods');
         $goods_id=$this->User_model->user_insert('v_goods',$data);



         redirect(base_url("newadmin/trip_photo_add?ts_id={$ts_id}"));

     }

     public function photo_in()
     {
         $user_id=$this->input->get('user_id',TRUE);
         if(!$user_id){
            return false ;
         }
         $data=$this->User_model->get_select_all($select='*',"user_id=$user_id AND is_show='1'",'id',$order='ASC',$table='v_ts_photo');

         $this->load->view('newadmin/photo_in');
     }

    //摄影师作品集
//摄影作品添加页面
public function pho_insert_show(){
  
  
  $this->load->view('myshop/pho_add_from_admin');

}
//入库 个人产品
     public function pho_insert_from_admin()
     {
         
         $data['title']=$this->input->post('title',true);
         $data['user_id']=$this->input->post('user_id',true);
         $data['user_name']=$this->input->post('user_name',true);
         if($_FILES['banner']['error']==0)
         {
             $banner=$this->upload_image('banner', $data['user_id'].'banner');
             $data['image']=$this->imagecropper($banner,'banner','time',$width='750',$height='320');
         }else{
             $data['image']='';
              redirect(base_url('Lxgj/pho_insert_show?debug=1'));
         }
          $num=$this->User_model->get_count($where="user_id=$data[user_id] AND is_show='1'", $table='v_ts_photo');
          if($num>10){
             redirect(base_url('Lxgj/pho_insert_show?debug=1'));
          }
         $data['add_time']=time();
         $detail=$this->input->post('detail',true);
         $detail=str_replace("\n","<br>", $detail);
        $data=array(
            'title'=> $data['title'],
             'add_time'=>time(),
             'desc'=>$detail,
             'user_id'=>$data['user_id'],
             'user_name'=>$data['user_name'],
             'is_show'=>'1',
             'image'=>$data['image']

            );
         $photos_id=$this->User_model->user_insert('v_ts_photo',$data);
      redirect(base_url('Lxgj/pho_insert_show?debug=1'));

     }
     
    //旅游筛选类产品 轮播图展示
     public function add_lbpicforpc($act_id)
     {
        $where="link_id=$act_id AND type='1' AND isdelete='0' ";
         $data['list']=$this->User_model->get_select_more('id,thumb as image',$where,$start=0, $page_num=100, 'id', $order='ASC', $table='v_images');
         if(!$data)
         {
             $data=array();
         }
         $data['act_id']=$act_id;
         //echo '<pre>';print_r($data);exit();
         $this->load->view('newadmin/trip_lb_image',$data);
     }

     //轮播图 增加页面
     public function lpadd($act_id)
     {
         $where="link_id=$act_id AND type='1' AND isdelete='0' ";
         $data['image_arr']=$this->User_model->get_select_more('id,thumb as image',$where,$start=0, $page_num=100, 'id', $order='ASC', $table='v_images');
         if(! $data['image_arr'])
         {
             $data['image_arr']=array();
         }
         $data['act_id']=$act_id;
         $this->load->view('newadmin/lb_image_add',$data);
     }

     public function lbpic_insert()
     {
        $act_id=$this->input->post('act_id',TRUE);
         if($_FILES['file1']['error']==0)
         {
             $data=array(
                 'user_id'=>'0',
                 'link_id'=>$act_id,
                 'type'=>'1',
             );

             $data['thumb']=$data['url']=$this->upload_image('file1','lb','time',1);
            // $data['thumb']=$this->imagecropper($data['url'],'lb','time',$width='500',$height='340','1');

             $this->User_model->user_insert($table='v_images',$data);
         }
         redirect($_SERVER['HTTP_REFERER']);
     }

     public function lbpic_sub()
     {
         $id=$this->input->post('id',TRUE);
     }

     public function del_pic()
     {
         $id=$this->input->get('id',TRUE);
         $this->User_model->update_one(array('id'=>$id),array('isdelete'=>'1'),$table='v_images');
         redirect($_SERVER['HTTP_REFERER']);
     }


     public function test_pic($big_img, $width, $height, $small_img)
     {


             $source_path=$big_img;
             $target_width=$width;
             $target_height=$height;
             $key1='ue_new';
             $key2='time';


             $source_info   = getimagesize($source_path);
             $source_width  = $source_info[0];
             $source_height = $source_info[1];
             $source_mime   = $source_info['mime'];
             $source_ratio  = $source_height / $source_width;
             $target_ratio  = $target_height / $target_width;

             // 源图过高
             if ($source_ratio > $target_ratio)
             {
                 $cropped_width  = $source_width;
                 $cropped_height = $source_width * $target_ratio;
                 $source_x = 0;
                 $source_y = ($source_height - $cropped_height) / 2;
             }
             // 源图过宽
             elseif ($source_ratio < $target_ratio)
             {
                 $cropped_width  = $source_height / $target_ratio;
                 $cropped_height = $source_height;
                 $source_x = ($source_width - $cropped_width) / 2;
                 $source_y = 0;
             }
             // 源图适中
             else
             {
                 $cropped_width  = $source_width;
                 $cropped_height = $source_height;
                 $source_x = 0;
                 $source_y = 0;
             }
             if($source_mime=='image/jpeg')
             {
                 $source_image = imagecreatefromjpeg($source_path);
                 $target_image  = imagecreatetruecolor($target_width, $target_height);
                 $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

                 // 裁剪
                 imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
                 // 缩放
                 imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

                 $type=pathinfo($source_path,PATHINFO_EXTENSION);
                 if($key2=='time'){
                     $key2=time();
                 }
                 $new_image='./public/images/'.$key1.'/'.$key2.'.'.$type;
                 imagejpeg($target_image,$new_image);


                 imagedestroy($source_image);
                 imagedestroy($target_image);
                 imagedestroy($cropped_image);

                 return $new_image;
                 //  return  substr($new_image,1);
             }
             elseif($source_mime=='image/png')
             {
                 $source_image = imagecreatefrompng($source_path);
                 $target_image  = imagecreatetruecolor($target_width, $target_height);
                 $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

                 $alpha = imagecolorallocatealpha($target_image, 0, 0, 0, 127);
                 imagefill($target_image, 0, 0, $alpha);
                 $alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
                 imagefill($cropped_image, 0, 0, $alpha);
                 // 裁剪
                 imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
                 // 缩放
                 imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
                 $type=pathinfo($source_path,PATHINFO_EXTENSION);
                 if($key2=='time'){
                     $key2=time();
                 }
                 $new_image='./public/images/'.$key1.'/'.$key2.'.'.$type;
                 imagesavealpha($target_image, true);
                 imagepng($target_image,$new_image);

                 imagedestroy($source_image);
                 imagedestroy($target_image);
                 imagedestroy($cropped_image);

                 return $new_image;
             }
             else
             {
                 $source_image = imagecreatefromgif($source_path);
                 $target_image  = imagecreatetruecolor($target_width, $target_height);
                 $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
                 // 裁剪
                 imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
                 // 缩放
                 imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
                 $type=pathinfo($source_path,PATHINFO_EXTENSION);
                 if($key2=='time'){
                     $key2=time();
                 }
                 $new_image='./public/images/'.$key1.'/'.$key2.'.'.$type;
                 imagegif($target_image,$new_image);
                 imagedestroy($source_image);
                 imagedestroy($target_image);
                 imagedestroy($cropped_image);

                 return $new_image;
             }

     }

     public function get_test(){
         echo $this->test_pic($big_img="./public/images/ue_crop/20161227/1482835422228515.jpg", $width='700', $height='300', $small_img=1);
     }

 }