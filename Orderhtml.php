<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2016/4/13
 * Time: 11:52
 */
error_reporting( E_ALL&~E_NOTICE );
defined('BASEPATH') OR exit('No direct script access allowed');
class Orderhtml extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Order_model');
        $this->load->model('User_model');
        $this->load->model('User_Api_model');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->helper('cookie');
          $this->load->library('common');
        $this->load->library('image_lib');  


        $this->load->model('Admin_model');
        $this->load->library('uploadimg');
        //$this->load->library('imagick');

        //$this->load->library('waf');
        $this->load->helper(array('form', 'url'));
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    //  unset($_COOKIE);
       //签名
      //  $this->sign();
        $this->count_url='http://s4.cnzz.com/stat.php?id=1258510548&web_id=1258510548';
        $this->down="http://a.app.qq.com/o/simple.jsp?pkgname=com.etjourney.zxqc";
       // $this->load->model('User_Api_model');
    }

    public function sub_order(){
        //echo 'test='.$_GET['test'];die;
        //if(!$_GET['test'])
        //{
        //    redirect(base_url('lst/nanyanginfo'));
        //}
        //echo '<pre>';
        //var_dump($_POST);
        $data['h5_title'] = $this->input->post('title',true);
        $order_fly = explode(',', $this->input->post('formFly',true));
        $data['order_fly'] = json_encode($order_fly);
        $order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        $order_car_come = explode(',', $this->input->post('formcar_come',true));
        $data['order_car_come'] = json_encode($order_car_come);
        $order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $order_car_go = explode(',', $this->input->post('formcar_go',true));
        $data['order_car_go'] = json_encode($order_car_go);
        $order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        $order_hotel = explode(',', $this->input->post('formHotel',true));
        $data['order_hotel'] = json_encode($order_hotel);
        $order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;

        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_form_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }
        //var_dump($trip);
        $data['order_trip'] = json_encode($trip);

        //$trip[0] = explode(',', $this->input->post('formStroke0',true));
        //$order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        //$trip[1] = explode(',', $this->input->post('formStroke1',true));
        //$order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        //$trip[2] = explode(',', $this->input->post('formStroke2',true));
        //$order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
        //$trip[3] = explode(',', $this->input->post('formStroke3',true));
        //$order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        //$trip[4] = explode(',', $this->input->post('formStroke4',true));
        //$order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;

        $data['order_amount'] = floatval($order_amount);
        $data['order_sn'] = $this->get_order_sn();
        $data['url'] = $_SERVER['HTTP_REFERER'];

        //var_dump($data);die;

        if($data['h5_title'] && $order_amount)
        {
            $res=$this->User_model->user_insert('v_h5_order',$data);
            if($res)
            {
                $this->load->view('nanyang/ok',$data);
            }
            else
            {
                redirect($data['url']);
            }
        }
        else
        {
            redirect($data['url']);
        }

    }
     public function sub_order_zyx2(){
       //echo "<pre>";
      // print_r($_POST);
      // echo "</pre>";die;
        //echo 'test='.$_GET['test'];die;
        //if(!$_GET['test'])
        //{
        //    redirect(base_url('lst/nanyanginfo'));
        //}   
        //echo '<pre>';
        //var_dump($_POST);die;
        $data['h5_id']=$this->input->post('h5_id',TRUE);
        $data['h5_title'] = $this->input->post('title',true);
        $order_fly = explode(',', $this->input->post('forminpAir',true));
        $data['order_fly'] = json_encode($order_fly);
        $order_amount = isset($order_fly[2]) ? intval($order_fly[2]) : 0;
        //$order_fly = explode(',', $this->input->post('formFly',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        //$order_air = explode(',', $this->input->post('formAir',true));
        //$data['order_air'] = json_encode($order_air);
        //$order_amount = isset($order_air[2]) ? $order_amount+intval($order_air[2]) : $order_amount;
        $order_car_come = explode(',', $this->input->post('formcar_come',true));
        $data['order_car_come'] = json_encode($order_car_come);
        $order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $order_car_go = explode(',', $this->input->post('formcar_go',true));
        $data['order_car_go'] = json_encode($order_car_go);
        $order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        $order_hotel = explode(',', $this->input->post('formhotel',true));
        $data['order_hotel'] = json_encode($order_hotel);
        $order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;

        //$order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        //$trip[0]=explode(';', $this->input->post('formstroke1',true));
        //$trip[1] = explode(',', $this->input->post('formstroke2',true));
        //$order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        //$trip[2] = explode(',', $this->input->post('formstroke3',true));
        //$order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
        //$trip[3] = explode(',', $this->input->post('formstroke4',true));
        //$order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        //$trip[4] = explode(',', $this->input->post('formstroke5',true));
        //$order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;

        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_form_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }

        $data['order_trip'] = json_encode($trip);

        $order_package = explode(',', $this->input->post('formaircraftHotel',true));
        $data['order_package'] = json_encode($order_package);
        $order_amount = isset($order_package[2]) ? $order_amount+intval($order_package[2]) : $order_amount;

        $data['order_amount'] = floatval($order_amount);
        $data['order_sn'] = $this->get_order_sn();
        $data['url'] = $_SERVER['HTTP_REFERER'];
//    echo "<pre>";
//    print_r($data);
//    echo "</pre>";die;
        if($data['h5_id']!="")
        {
            $res=$this->User_model->user_insert('v_h5_order',$data);
            if($res)
            {
                $this->load->view('ziyouxing/ok',$data);
            }
            else
            {
                redirect($data['url']);
            }
        }
        else
        {
            redirect($data['url']);
        }

    }

//模板二订单提交
    public  function sub_order_ok(){
       
          $order_package = explode(',', $this->input->post('formaircraftHotel',true));
          $h5_id=$this->input->post('h5_id',TRUE);
          $data['order_package']= json_encode($order_package);
          $data['order_amount']=$this->input->post('taocan_money',TRUE);
          $data['add_time']=date('Y-m-d',time());
          $data['h5_id']=$h5_id;
          $data['order_sn'] = $this->get_order_sn();
          $res=$this->User_model->user_insert('v_h5_order',$data);
          if($res){
 
               $this->load->view('ziyouxing/ok_temp',$data);  
              
          }
        
    }

   //新
      public  function sub_order_zyx(){
          $order_package = explode(';', $this->input->post('formaircraftHotel',true));
          $forminpFlight = explode(';', $this->input->post('forminpFlight',true));
          $formcar_come = explode(';', $this->input->post('formcar_come',true));
          $formcar_go = explode(';', $this->input->post('formcar_go',true));
          $formhotel = explode(';', $this->input->post('formhotel',true));
          $formstroke1 = explode(';', $this->input->post('formstroke1',true));
          $formstroke2 = explode(';', $this->input->post('formstroke2',true));
          $formstroke3 = explode(';', $this->input->post('formstroke3',true));
          $formstroke4 = explode(';', $this->input->post('formstroke4',true));
          $formstroke5 = explode(';', $this->input->post('formstroke5',true));
          $formpackage = explode(';', $this->input->post('formpackage',true));
          $forminpAir = explode(';', $this->input->post('forminpAir',true));

          $h5_id=$this->input->post('h5_id',TRUE);
          $data['order_trip']= json_encode($order_package);
          $data['order_car_come']= json_encode($formcar_come);
          $data['order_car_come1']= json_encode($formcar_come1);
          $data['order_car_go']= json_encode($formcar_go);
          $data['order_hotel']= json_encode($formhotel);
          $data['formstroke1']= json_encode($formstroke1);
          $data['order_package']= json_encode($formpackage);
          $data['order_fly']= json_encode($forminpAir);
          $url = $this->input->post('url',true);
          if($url)
          {
            $data['url'] = $url;
          }
          else
          {
            $data['url'] = $_SERVER['HTTP_REFERER'];
          }

          $data['h5_title'] = $this->input->post('title',true);
          $set_date = $this->input->post('godate',true);
          if($set_date){
            $data['set_date'] = $set_date;
          }
          $data['order_amount']=$order_package[2]+$forminpFlight[2]+$formcar_come[2]+ $formcar_go[2]+$formhotel[2]+$formstroke1[2]+$formstroke2[2]+$formstroke3[2]+$formstroke4[2]+$formstroke5[2]+$formpackage[2]+$forminpAir[2];
          if(!$data['order_amount']){
            $data['order_amount'] = $this->input->post('price',true);
          }
          $data['add_time']=date('Y-m-d',time());
     
          $data['order_sn'] = $this->get_order_sn();
          $res=$this->User_model->user_insert('v_h5_order',$data);
          if($res){
 
               $this->load->view('ziyouxing/ok',$data);  
              
          }
        
    } 
 //新
      public  function sub_order_ok1(){
          $data['h5_title'] = $this->input->post('title',true);
  //echo "<pre>";
 //print_r($_POST);
 // echo "</pre>";die;
       
          $order_package = explode(';', $this->input->post('formaircraftHotel',true));
          $forminpFlight = explode(';', $this->input->post('forminpFlight',true));
          $formcar_come = explode(';', $this->input->post('formcar_come',true));
          $formcar_go = explode(';', $this->input->post('formcar_go',true));
          $formhotel = explode(';', $this->input->post('formhotel',true));
          $formstroke1 = explode(';', $this->input->post('formstroke1',true));
          $formstroke2 = explode(';', $this->input->post('formstroke2',true));
          $formstroke3 = explode(';', $this->input->post('formstroke3',true));
          $formstroke4 = explode(';', $this->input->post('formstroke4',true));
          $formstroke5 = explode(';', $this->input->post('formstroke5',true));
          $formpackage = explode(';', $this->input->post('formpackage',true));
          $forminpAir = explode(';', $this->input->post('forminpAir',true));


     
   

        //  echo "<pre>";
        //  print_r($order_package);
        //  echo "</pre>";die;
          $h5_id=$this->input->post('h5_id',TRUE);
          $data['order_trip']= json_encode($order_package);
         // $data['forminpFlight']= json_encode($forminpFlight);
          $data['order_car_come']= json_encode($formcar_come);
          $data['order_car_come1']= json_encode($formcar_come1);
          $data['order_car_go']= json_encode($formcar_go);
          $data['order_hotel']= json_encode($formhotel);
           $data['formstroke1']= json_encode($formstroke1);
          $data['order_package']= json_encode($formpackage);
          $data['order_fly']= json_encode($forminpAir);
          
 //echo "<pre>";
// print_r($formstroke1[2]);
// echo "</pre>";
          $data['order_amount']=$order_package[2]+$forminpFlight[2]+$formcar_come[2]+ $formcar_go[2]+$formhotel[2]+$formstroke1[2]+$formstroke2[2]+$formstroke3[2]+$formstroke4[2]+$formstroke5[2]+$formpackage[2]+$forminpAir[2];
          $data['add_time']=date('Y-m-d',time());
     
          $data['order_sn'] = $this->get_order_sn();
       //   echo "<pre>";
       //   print_r($data);
       //   echo "<pre>";die;
          $res=$this->User_model->user_insert('v_h5_order',$data);
          if($res){
 
               $this->load->view('rbjjtc/ok',$data);  
              
          }
        
    } 

    //新疆订单查询
    public function chaxun(){

    $tel=$this->input->post('tel',TRUE);
    $h5_id=$this->input->post('h5_id',TRUE);
   $where ="consignee=$tel AND h5_id=$h5_id";
     $data['info']=$this->User_model->get_select_all($select='*',$where,'order_id','desc','v_h5_order');
   if($data['info']){

   	 foreach($data['info'] as $ks=>$vs){
      for($i=0;$i<count($vs['jipiao']);$i++){
         $data['jp'][$i]=array(
           'jipiao'=>$vs['jipiao'][$i]

          );

      }

  }
foreach ($data['info'] as $k => $v) {
   $data['info'][$k]['car1']=explode(';', $v['order_car_come']);
    $data['car1_a']=explode(',', $data['info'][$k]['car1'][0]);
    $data['car1_b']=explode(',', $data['info'][$k]['car1'][1]);
    $data['car1_c']=explode(',', $data['info'][$k]['car1'][2]);
    $data['car1_d']=explode(',', $data['info'][$k]['car1'][3]);
    $data['car1_e']=explode(',', $data['info'][$k]['car1'][4]);
        for($i=0;$i<count($data['car1_a']);$i++){
          $data['car1'][$i]=array(
          'a'=>$data['car1_a'][$i],
          'b'=>$data['car1_b'][$i],
          'c'=>$data['car1_c'][$i],
          'd'=>$data['car1_d'][$i],
          'e'=>$data['car1_e'][$i],

            );

        }
    
   $data['info'][$k]['car2']=explode(';', $v['order_car_come1']);
    $data['car2_a']=explode(',', $data['info'][$k]['car2'][0]);
    $data['car2_b']=explode(',', $data['info'][$k]['car2'][1]);
    $data['car2_c']=explode(',', $data['info'][$k]['car2'][2]);
    $data['car2_d']=explode(',', $data['info'][$k]['car2'][3]);
    $data['car2_e']=explode(',', $data['info'][$k]['car2'][4]);
        for($i=0;$i<count($data['car2_a']);$i++){
          $data['car2'][$i]=array(
          'a'=>$data['car2_a'][$i],
          'b'=>$data['car2_b'][$i],
          'c'=>$data['car2_c'][$i],
          'd'=>$data['car2_d'][$i],
          'e'=>$data['car2_e'][$i],

            );

        }

   $data['info'][$k]['car3']=explode(';', $v['order_car_go']);
       $data['car3_a']=explode(',', $data['info'][$k]['car3'][0]);
    $data['car3_b']=explode(',', $data['info'][$k]['car3'][1]);
    $data['car3_c']=explode(',', $data['info'][$k]['car3'][2]);
    $data['car3_d']=explode(',', $data['info'][$k]['car3'][3]);
    $data['car3_e']=explode(',', $data['info'][$k]['car3'][4]);
        for($i=0;$i<count($data['car2_a']);$i++){
          $data['car3'][$i]=array(
          'a'=>$data['car3_a'][$i],
          'b'=>$data['car3_b'][$i],
          'c'=>$data['car3_c'][$i],
          'd'=>$data['car3_d'][$i],
          'e'=>$data['car3_e'][$i],

            );

        }
   $data['info'][$k]['hotel1']=explode(';', $v['order_hotel']);
   $data['hotel1_a']=explode(',', $data['info'][$k]['hotel1'][0]);
    $data['hotel1_b']=explode(',', $data['info'][$k]['hotel1'][1]);
     $data['hotel1_c']=explode(',', $data['info'][$k]['hotel1'][2]);
      $data['hotel1_d']=explode(',', $data['info'][$k]['hotel1'][4]);
      for($i=0;$i<count($data['hotel1_a']);$i++){
         $data['hotle'][$i]=array(
         'a'=>$data['hotel1_a'][$i],  
         'b'=>$data['hotel1_b'][$i], 
         'c'=>$data['hotel1_c'][$i], 
         'd'=>$data['hotel1_d'][$i]     


          );    

      }
   

   $data['info'][$k]['trip']=explode(';', $v['order_trip']);
   $data['trip_a']=explode(',', $data['info'][$k]['trip'][1]);
   $data['trip_b']=explode(',', $data['info'][$k]['trip'][2]);
   $data['trip_c']=explode(',', $data['info'][$k]['trip'][3]);
   $data['trip_d']=explode(',', $data['info'][$k]['trip'][4]);
       for($i=0;$i<count($data['trip_a']);$i++){
         $data['trip'][$i]=array(
         'a'=>$data['trip_a'][$i],  
         'b'=>$data['trip_b'][$i], 
         'c'=>$data['trip_c'][$i], 
         'd'=>$data['trip_d'][$i]     


          );   
       }
   $data['info'][$k]['vip2']=explode(';', $v['vip1']);
   $data['vip2_a']=explode(',', $data['info'][$k]['vip2'][0]);
    $data['vip2_b']=explode(',', $data['info'][$k]['vip2'][1]);
     $data['vip2_c']=explode(',', $data['info'][$k]['vip2'][2]);
      $data['vip2_d']=explode(',', $data['info'][$k]['vip2'][3]);
       $data['vip2_e']=explode(',', $data['info'][$k]['vip2'][4]);
      for($i=0;$i<count($data['vip2_a']);$i++){
         $data['vip2'][$i]=array(
         'a'=>$data['vip2_a'][$i],  
         'b'=>$data['vip2_b'][$i], 
         'c'=>$data['vip2_c'][$i], 
         'd'=>$data['vip2_d'][$i],
           'e'=>$data['vip2_e'][$i]        


          ); 
       }

   $data['info'][$k]['jipiao1']=explode(';', $v['jipiao']);
    $data['jp']['name']=explode(',', $data['info'][$k]['jipiao1']['14']);
     $data['jp']['card']=explode(',', $data['info'][$k]['jipiao1']['16']);
         for($i=0;$i<count($data['jp']['name']);$i++){
         $data['jipiao'][$i]=array(
         'w_name'=>$data['jp'][0],
         'w_dizhi'=>$data['jp'][1],
         'w_date'=>$data['jp'][2],
         'w_time'=>$data['jp'][3],
         'w_cf'=>$data['jp'][4],
         'w_ddate'=>$data['jp'][5],
         'w_dtime'=>$data['jp'][6],
         'f_name'=>$data['jp'][7],
         'f_dizhi'=>$data['jp'][8],
         'f_date'=>$data['jp'][9],
         'f_time'=>$data['jp'][10],
         'f_cf'=>$data['jp'][11],
         'f_ddate'=>$data['jp'][12],
         'f_dtime'=>$data['jp'][13],
         'name'=>$data['jp']['name'][$i],
          'card'=>$data['jp']['card'][$i],
          'z_money'=> $data['jp']['17'],
          'd_money'=>$data['jp']['18']

  

          );
     
    }

}

   }else{
  echo "<script>alert('暂无该用户信息');history.back();</script>";
  

   }
 

   //echo "<pre>";
   //print_r( $data['info']);
   //echo "</pre>";
  $this->load->view('zxqcny1/cx',$data);


    }
   //测试新疆
   public function cs_xj($h5_id){
//echo "<pre>";
//print_r($_POST);
// echo "</pre>";

    $id=$h5_id;
    $title=$this->input->post('title',TRUE);
    $ganguo=$this->input->post('formfruit',TRUE);
    $vip=$this->input->post('formservies',TRUE);
    $car=$this->input->post('formcar_come');
    $car1=$this->input->post('formcar_come1');
    $car2=$this->input->post('formcar_go');
    $hotel= $this->input->post('formhotel');
    $xc=$this->input->post('formstroke1');
    $jp=$this->input->post('forminpAir',TRUE);
    $data['ganguo']=explode(';', $ganguo);
    $data['ganguo']['zl']=explode(',', $data['ganguo']['0']);
    $data['ganguo']['name']=explode(',', $data['ganguo']['1']);
    $data['ganguo']['money']=explode(',', $data['ganguo']['2']);
    $data['ganguo']['bz']=explode(',', $data['ganguo']['4']);
    $data['ganguo']['money_cl']=array_sum($data['ganguo']['money']);
    $data['jp']=explode(';', $jp);
    $data['jp']['name']=explode(',', $data['jp']['14']);
     $data['jp']['card']=explode(',', $data['jp']['16']);
// echo count($data['ganguo']['0']);
     for($i=0;$i<count($data['ganguo']['zl']);$i++){
         $data['gg'][$i]=array(
         'name'=>$data['ganguo']['name'][$i],
         'money'=>$data['ganguo']['money'][$i],
         'bz'=>$data['ganguo']['bz'][$i]

          );


     }
   // echo "<pre>";
 //  print_r($data['gg']);
  //  echo "</pre>";die;
    for($i=0;$i<count($data['jp']['name']);$i++){
         $data['jipiao'][$i]=array(
         'w_name'=>$data['jp'][0],
         'w_dizhi'=>$data['jp'][1],
         'w_date'=>$data['jp'][2],
         'w_time'=>$data['jp'][3],
         'w_cf'=>$data['jp'][4],
         'w_ddate'=>$data['jp'][5],
         'w_dtime'=>$data['jp'][6],
         'f_name'=>$data['jp'][7],
         'f_dizhi'=>$data['jp'][8],
         'f_date'=>$data['jp'][9],
         'f_time'=>$data['jp'][10],
         'f_cf'=>$data['jp'][11],
         'f_ddate'=>$data['jp'][12],
         'f_dtime'=>$data['jp'][13],
         'name'=>$data['jp']['name'][$i],
          'card'=>$data['jp']['card'][$i],
          'z_money'=> $data['jp']['17'],
          'd_money'=>$data['jp']['18']

  

          );
     
    }
//echo "<pre>";
//print_r($data['jipiao']);
//echo "</pre>";
    $data['car']=explode(';', $car);
    $data['car1']=explode(';', $car1);
    $data['car2']=explode(';', $car2);
    $data['vip1']=explode(';', $vip);

   $data['vip1']['name']=explode(',',$data['vip1']['1']);
   $data['vip1']['money']=explode(',',$data['vip1']['2']);
   $data['vip1']['sum']=explode(',',$data['vip1']['4']);
   $data['vip']['money_cl']=array_sum($data['vip1']['money']);
  // echo "<pre>";
  // print_r($data['vip']['money_cl']);
  // echo "<pre>";die;
   for($i=0;$i<count($data['vip1']['name']);$i++){
      $data['vip'][$i]=array(
           'vip_name'=>$data['vip1']['name'][$i],
           'vip_money'=>$data['vip1']['money'][$i],
           'vip_sum'=>$data['vip1']['sum'][$i]  

        );

   }
   
    $data['car']['baoche']=explode(',', $data['car']['1']);
    $data['car']['money']=explode(',', $data['car']['2']);
      $data['car']['bz']=explode(',', $data['car']['4']);
     $data['car']['money_cl']=array_sum($data['car']['money']);
     for($i=0;$i<count($data['car']['baoche']);$i++){
           $data['car_data'][$i]=array(
              'car_name'=>$data['car']['baoche'][$i],
              'car_money'=>$data['car']['money'][$i],
              'car_bz'=>$data['car']['bz'][$i]

            );

     }
     $data['car1']['baoche']=explode(',', $data['car1']['1']);
    $data['car1']['money']=explode(',', $data['car1']['2']);
      $data['car1']['bz']=explode(',', $data['car1']['4']);
     $data['car1']['money_cl']=array_sum($data['car1']['money']);
     for($i=0;$i<count($data['car1']['baoche']);$i++){
           $data['car_data1'][$i]=array(
              'car_name'=>$data['car1']['baoche'][$i],
              'car_money'=>$data['car1']['money'][$i],
              'car_bz'=>$data['car1']['bz'][$i]

            );

     }
      $data['car2']['baoche']=explode(',', $data['car2']['1']);
    $data['car2']['money']=explode(',', $data['car2']['2']);
      $data['car2']['bz']=explode(',', $data['car2']['4']);
     $data['car2']['money_cl']=array_sum($data['car2']['money']);
     for($i=0;$i<count($data['car2']['baoche']);$i++){
           $data['car_data2'][$i]=array(
              'car_name'=>$data['car2']['baoche'][$i],
              'car_money'=>$data['car2']['money'][$i],
              'car_bz'=>$data['car2']['bz']

            );

     }

    //echo "<pre>";
    //print_r($data['vip']);
   //echo "</pre>";die;
    $data['hotel']=explode(';', $hotel);
   $data['hotel']['jibi']=explode(',', $data['hotel']['0']);
    $data['hotel']['name']=explode(',', $data['hotel']['1']);
     $data['hotel']['jiage']=explode(',', $data['hotel']['2']);
      $data['hotel']['tianshu']=explode(',', $data['hotel']['4']);
   for($i=0;$i<count($data['hotel']['jibi']);$i++){
         $data['jd'][$i]=array(
        'jibi'=>$data['hotel']['jibi'][$i],
        'name'=>$data['hotel']['name'][$i],
        'jiage'=>$data['hotel']['jiage'][$i],
        'tianshu'=>$data['hotel']['tianshu'][$i]

          );


   }
  $data['hotel']['money']=array_sum($data['hotel']['jiage']);
  // echo "<pre>";
  //print_r($data['jd']);
  // echo "</pre>";die;

     $data['xc']=explode(';', $xc);
       $data['xc']['xc_name']=explode(',', $data['xc'][1]);
        $data['xc']['xc_date']=explode(',', $data['xc'][2]);
         $data['xc']['xc_money']=explode(',', $data['xc'][3]);
          $data['xc']['xc_xx']=explode(',', $data['xc'][4]);
        $data['xc']['money']=array_sum($data['xc']['xc_money']);
        for($i=0;$i<count($data['xc']['xc_name']);$i++){
              $data['xc_data'][$i]=array(
               'xc_name'=>$data['xc']['xc_name'][$i],
               'xc_date'=>$data['xc']['xc_date'][$i],
               'xc_money'=>$data['xc']['xc_money'][$i],
               'xc_xx'=>$data['xc']['xc_xx'][$i]

                );

        }
     $data['order']=date('Ymd',time());
     $sn=rand(100000,999999);
     $data['dd']=$data['order'].$sn;
     $data['money']=$data['hotel']['money']+$data['car']['money_cl']+$data['xc']['money']+$data['car1']['money_cl']+$data['car2']['money_cl']+$data['vip']['money_cl']+$data['jp']['17']+ $data['ganguo']['money_cl'];
       $data['wx_sub_url']=base_url('Api/get_wxpay_xj');
            $data['wx_sub_url_cash']=base_url('goodsforcar/wx_order_in/1/1/1');
            $data['zfb_url']=base_url('Api1/zfbs');
        //     $data['my_order']=base_url("h5info_temp_zyx/xj/$id");
     $datav= array(
        'h5_id' => $id,
        'order_amount'=>$data['money'],
        'order_sn'=>$data['dd'],
        'order_car_come'=>$car,
        'order_car_come1'=>$car2,
        'order_car_go'=>$car1,
        'order_hotel'=>$hotel,
        'order_trip'=>$xc,
        'dd_source'=>1,
        'zf_state'=>2,
        'vip1'=>$vip,
        'jipiao'=>$jp,
        'ganguo'=>$ganguo,
        'h5_title'=>$title,
        'add_time'=>$data['order']
         );
       $this->User_model->user_insert('v_h5_order',$datav);
 
        //echo 'prepay_id='.$prepay_id.'<br />';
     $order  =  array(
                            'order_sn'    =>$data['dd'],
                            'user_id_buy' =>"111",
                            'user_id_buy_name' =>"",
                            'user_id_sell' =>$id,
                            'user_id_sell_name' =>"坐享其成",
                            'consignee'   =>"",
                            'address'     =>"",
                            'mobile'      =>"",
                            'goods_amount'=>$data['money'],
                            'order_amount'=>$data['money'],
                            'video_id'    =>"",
                            'goods_all_num' =>1,
                            'commont'     =>"新疆",
                            'add_time'    =>time()
                            );
            $order_id = $this->User_Api_model->insert_string($order,'v_order_info');
          //  $orders=$this->User_model->get_select_one('*',arrar("order_sn=>$order_sn"),'v_order_info');
             $data['wxinfo']=$this->User_model->get_select_one($select='order_id',array('order_sn'=>$data['dd']),'v_order_info');

   //微信支付 
   
     
        //  echo "<pre>";
       //print_r($data['car2']);
      //echo "<?pre>";die;

    $this->load->view('zxqcny1/order_show',$data);
   } 
    public function sub_order_zyxx(){

    // echo "<pre>";
    //   print_r($_POST);
   //echo "</pre>";die;
        //echo 'test='.$_GET['test'];die;
        //if(!$_GET['test'])
        //{
        //    redirect(base_url('lst/nanyanginfo'));
        //}   
        //echo '<pre>';
        //var_dump($_POST);die;

        $data['h5_title'] = $this->input->post('title',true);
        $order_fly = explode(',', $this->input->post('forminpAir',true));
        $data['order_fly'] = json_encode($order_fly);
        $order_amount = isset($order_fly[2]) ? intval($order_fly[2]) : 0;
        //$order_fly = explode(',', $this->input->post('formFly',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        //$order_air = explode(',', $this->input->post('formAir',true));
        //$data['order_air'] = json_encode($order_air);
        //$order_amount = isset($order_air[2]) ? $order_amount+intval($order_air[2]) : $order_amount;
        //机票
      
        $hangban=explode(',', $this->input->post('formaircraftHotel',TRUE));
        $data['order_fly']=json_encode($hangban);
        $order_amount = isset($hangban[2]) ? $hangban+intval($hangban[2]) : $order_amount;
         //spa
        
         $spa=explode(',', $this->input->post('formspa',TRUE));
         $data['spa']=json_encode($spa);
       
          //门票
        
        $formmenpiao=explode(',', $this->input->post('formmenpiao',TRUE));
        $data['ticket']=json_encode($formmenpiao);
      

        $order_car_come = explode(',', $this->input->post('formcar_come',true));
        $data['order_car_come'] = json_encode($order_car_come);
        $order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $order_car_come1 = explode(',', $this->input->post('formcar_come1',true));
        $data['order_car_come1'] = json_encode($order_car_come1);
        $order_amount = isset($order_car_come1[2]) ? $order_amount+intval($order_car_come1[2]) : $order_amount;
        $order_car_come2 = explode(',', $this->input->post('formcar_come2',true));
        $data['order_car_come2'] = json_encode($order_car_come2);
        $order_amount = isset($order_car_come2[2]) ? $order_amount+intval($order_car_come2[2]) : $order_amount;
        $order_car_go = explode(',', $this->input->post('formcar_go',true));
        $data['order_car_go'] = json_encode($order_car_go);
        $order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        $order_hotel = explode(',', $this->input->post('formhotel',true));
        $data['order_hotel'] = json_encode($order_hotel);
        $order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;

        $order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        $trip[0]=explode(';', $this->input->post('formstroke1',true));
        $trip[1] = explode(',', $this->input->post('formstroke2',true));
        $order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        $trip[2] = explode(',', $this->input->post('formstroke3',true));
        $order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
      $trip[3] = explode(',', $this->input->post('formstroke4',true));
        $order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        $trip[4] = explode(',', $this->input->post('formstroke5',true));
        $order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;

        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_form_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }

        $data['order_trip'] = json_encode($trip);

        $order_package = explode(',', $this->input->post('formaircraftHotel',true));
        $data['order_package'] = json_encode($order_package);
        $order_amount = isset($order_package[2]) ? $order_amount+intval($order_package[2]) : $order_amount;

        $data['order_amount'] = floatval($order_amount);
        $data['order_sn'] = $this->get_order_sn();
        $url = $this->input->post('url',true);
        if($url)
        {
            $data['url'] = $url;
        }
        else
        {
            $data['url'] = $_SERVER['HTTP_REFERER'];
        }

        //买家信息
        $uName = $this->input->post('uName',true);
        $uTel = $this->input->post('uTel',true);
        $uDate = $this->input->post('uDate',true);
        $uNum = $this->input->post('uNum',true);
        $uNOte = $this->input->post('uNOte',true);
        $data['consignee'] = $uName ? $uName : '';
        $data['mobile'] = $uTel ? $uTel : '';
        $data['set_date'] = $uDate ? $uDate : '';
        $data['numbers'] = $uNum ? $uNum : '';
        $data['note'] = $uNOte ? $uNOte : '';
      
     // echo "<pre>";
     // print_r($data);
     // echo "</pre>";die;
       // if($data['h5_title'] && $order_amount)
       // {
            $res=$this->User_model->user_insert('v_h5_order',$data);
         //   if($res)
          //  {
                $this->load->view('ziyouxing/ok',$data);
           // }
            //else
           // {
             //   redirect($data['url']);
           // }
        //}
        //else
       // {
        //    redirect($data['url']);
       // }


        
      

    }

     public function sub_order_zyx3(){

    // echo "<pre>";
    //   print_r($_POST);
   //echo "</pre>";die;
        //echo 'test='.$_GET['test'];die;
        //if(!$_GET['test'])
        //{
        //    redirect(base_url('lst/nanyanginfo'));
        //}   
        //echo '<pre>';
        //var_dump($_POST);die;

        $data['h5_title'] = $this->input->post('title',true);
        $order_fly = explode(',', $this->input->post('forminpAir',true));
        $data['order_fly'] = json_encode($order_fly);
        $order_amount = isset($order_fly[2]) ? intval($order_fly[2]) : 0;
        //$order_fly = explode(',', $this->input->post('formFly',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        //$order_air = explode(',', $this->input->post('formAir',true));
        //$data['order_air'] = json_encode($order_air);
        //$order_amount = isset($order_air[2]) ? $order_amount+intval($order_air[2]) : $order_amount;
        //机票
      
        $hangban=explode(',', $this->input->post('formaircraftHotel',TRUE));
        $data['order_fly']=json_encode($hangban);
        $order_amount = isset($hangban[2]) ? $hangban+intval($hangban[2]) : $order_amount;
         //spa
        
         $spa=explode(',', $this->input->post('formspa',TRUE));
         $data['spa']=json_encode($spa);
       
          //门票
        
        $formmenpiao=explode(',', $this->input->post('formmenpiao',TRUE));
        $data['ticket']=json_encode($formmenpiao);
      

        $order_car_come = explode(',', $this->input->post('formcar_come',true));
        $data['order_car_come'] = json_encode($order_car_come);
        $order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $order_car_come1 = explode(',', $this->input->post('formcar_come1',true));
        $data['order_car_come1'] = json_encode($order_car_come1);
        $order_amount = isset($order_car_come1[2]) ? $order_amount+intval($order_car_come1[2]) : $order_amount;
        $order_car_come2 = explode(',', $this->input->post('formcar_come2',true));
        $data['order_car_come2'] = json_encode($order_car_come2);
        $order_amount = isset($order_car_come2[2]) ? $order_amount+intval($order_car_come2[2]) : $order_amount;
        $order_car_go = explode(',', $this->input->post('formcar_go',true));
        $data['order_car_go'] = json_encode($order_car_go);
        $order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        $order_hotel = explode(',', $this->input->post('formhotel',true));
        $data['order_hotel'] = json_encode($order_hotel);
        $order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;

        //$order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        //$trip[0]=explode(';', $this->input->post('formstroke1',true));
        //$trip[1] = explode(',', $this->input->post('formstroke2',true));
        //$order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        //$trip[2] = explode(',', $this->input->post('formstroke3',true));
        //$order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
        //$trip[3] = explode(',', $this->input->post('formstroke4',true));
        //$order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        //$trip[4] = explode(',', $this->input->post('formstroke5',true));
        //$order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;

        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_form_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }

        $data['order_trip'] = json_encode($trip);

        $order_package = explode(',', $this->input->post('formaircraftHotel',true));
        $data['order_package'] = json_encode($order_package);
        $order_amount = isset($order_package[2]) ? $order_amount+intval($order_package[2]) : $order_amount;

        $data['order_amount'] = floatval($order_amount);
        $data['order_sn'] = $this->get_order_sn();
        $url = $this->input->post('url',true);
        if($url)
        {
            $data['url'] = $url;
        }
        else
        {
            $data['url'] = $_SERVER['HTTP_REFERER'];
        }

        //买家信息
        $uName = $this->input->post('uName',true);
        $uTel = $this->input->post('uTel',true);
        $uDate = $this->input->post('uDate',true);
        $uNum = $this->input->post('uNum',true);
        $uNOte = $this->input->post('uNOte',true);
        $data['consignee'] = $uName ? $uName : '';
        $data['mobile'] = $uTel ? $uTel : '';
        $data['set_date'] = $uDate ? $uDate : '';
        $data['numbers'] = $uNum ? $uNum : '';
        $data['note'] = $uNOte ? $uNOte : '';
      
        if($data['h5_title'] && $order_amount)
        {
            $res=$this->User_model->user_insert('v_h5_order',$data);
            if($res)
            {
                $this->load->view('ziyouxing/ok',$data);
            }
            else
            {
                redirect($data['url']);
            }
        }
        else
        {
            redirect($data['url']);
        }


        
      

    }
   //新吉普自由行
        public function sub_order_zyx_jp(){

      //echo "<pre>";
      // print_r($_POST);
      //echo "</pre>";die;
        //echo 'test='.$_GET['test'];die;
        //if(!$_GET['test'])
        //{
        //    redirect(base_url('lst/nanyanginfo'));
        //}   
        //echo '<pre>';
        //var_dump($_POST);die;

        $data['h5_title'] = $this->input->post('title',true);
        $order_fly = explode(',', $this->input->post('forminpAir',true));
        $data['order_fly'] = json_encode($order_fly);
        $order_amount = isset($order_fly[2]) ? intval($order_fly[2]) : 0;
        //$order_fly = explode(',', $this->input->post('formFly',true));
        //$data['order_fly'] = json_encode($order_fly);
        //$order_amount = isset($order_fly[1]) ? intval($order_fly[1]) : 0;
        //$order_air = explode(',', $this->input->post('formAir',true));
        //$data['order_air'] = json_encode($order_air);
        //$order_amount = isset($order_air[2]) ? $order_amount+intval($order_air[2]) : $order_amount;
        //机票
        $hangban=explode(',', $this->input->post('formaircraftHotel',TRUE));
        $data['order_fly']=json_encode($hangban);
        $order_amount = isset($hangban[2]) ? $hangban+intval($hangban[2]) : $order_amount;
         //spa  
        $spa=$this->input->post('formspa',TRUE);
        $data['spa']=json_encode($spa);   
          //门票
        $formmenpiao=$this->input->post('formmenpiao',TRUE);
        $data['ticket']=json_encode($formmenpiao);
        $order_car_come = $this->input->post('formcar_come',true);
        $data['order_car_come'] = json_encode($order_car_come);
      //$order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $order_car_come1 = $this->input->post('formcar_come1',true);
        $data['order_car_come1'] = json_encode($order_car_come1);
      //$order_amount = isset($order_car_come1[2]) ? $order_amount+intval($order_car_come1[2]) : $order_amount;
        $order_car_come2 = $this->input->post('formcar_come2',true);
        $data['order_car_come2'] = json_encode($order_car_come2);
      //$order_amount = isset($order_car_come2[2]) ? $order_amount+intval($order_car_come2[2]) : $order_amount;
        $order_car_go =$this->input->post('formcar_go',true);
        $data['order_car_go'] = json_encode($order_car_go);
      //$order_amount = isset($order_car_go[2]) ? $order_amount+intval($order_car_go[2]) : $order_amount;
        $order_hotel = explode(',', $this->input->post('formhotel',true));
        $data['order_hotel'] = json_encode($order_hotel);
        $order_amount = isset($order_hotel[2]) ? $order_amount+intval($order_hotel[2]) : $order_amount;
        //$order_amount = isset($trip[0][2]) ? $order_amount+intval($trip[0][2]) : $order_amount;
        //$trip[0]=explode(';', $this->input->post('formstroke1',true));
        //$trip[1] = explode(',', $this->input->post('formstroke2',true));
        //$order_amount = isset($trip[1][2]) ? $order_amount+intval($trip[1][2]) : $order_amount;
        //$trip[2] = explode(',', $this->input->post('formstroke3',true));
        //$order_amount = isset($trip[2][2]) ? $order_amount+intval($trip[2][2]) : $order_amount;
        //$trip[3] = explode(',', $this->input->post('formstroke4',true));
        //$order_amount = isset($trip[3][2]) ? $order_amount+intval($trip[3][2]) : $order_amount;
        //$trip[4] = explode(',', $this->input->post('formstroke5',true));
        //$order_amount = isset($trip[4][2]) ? $order_amount+intval($trip[4][2]) : $order_amount;
        for ($i=1; $i<6 ; $i++) {
            $res = $this->get_form_info($this->input->post('formstroke'.$i,true));
            if($res['info'])
            {
                $trip[]=$res['info'];
                $order_amount = $order_amount + $res['amount'];
            }
        }

        $data['order_trip'] = json_encode($trip);

        $order_package = explode(',', $this->input->post('formaircraftHotel',true));
        $data['order_package'] = json_encode($order_package);
        $order_amount = isset($order_package[2]) ? $order_amount+intval($order_package[2]) : $order_amount;

        $data['order_amount'] = $this->input->post('allprice',TRUE);
        $data['order_sn'] = $this->get_order_sn();
       // $url = $this->input->post('url',true);
       // if($url)
       // {
       //     $data['url'] = $url;
       // }
       // else
       // {
       //     $data['url'] = $_SERVER['HTTP_REFERER'];
       // }

        //买家信息
        $uName = $this->input->post('uName',true);
        $uTel = $this->input->post('uTel',true);
        $uDate = $this->input->post('uDate',true);
        $uNum = $this->input->post('uNum',true);
        $uNOte = $this->input->post('uNOte',true);
        $data['consignee'] = $uName ? $uName : '';
        $data['mobile'] = $uTel ? $uTel : '';
        $data['set_date'] = $uDate ? $uDate : '';
        $data['numbers'] = $uNum ? $uNum : '';
        $data['note'] = $uNOte ? $uNOte : '';
      
        if($data['h5_title'])
        {
            $res=$this->User_model->user_insert('v_h5_order',$data);
            if($res)
            {
                $this->load->view('ziyouxing/ok_jp',$data);
            }
            else
            {
                redirect($data['url']);
            }
        }
        else
        {
            redirect($data['url']);
        }
    }

// 欧洲用车
    public function ouzhou_car(){
 //echo "<pre>";
 //print_r($_POST);
//echo "</pre>";die;
        $data['order_amount']=$this->input->post('allprice',TRUE);
        $data['is_ouzhou']=$this->input->post('isnew',TRUE);
        $data['h5_title'] = $this->input->post('title',true);
        $data['order_sn'] = $this->get_order_sn();
        $order_car_come = $this->input->post('formcar_come',true);
        $data['order_car_come'] = json_encode($order_car_come);
      //  $order_amount = isset($order_car_come[2]) ? $order_amount+intval($order_car_come[2]) : $order_amount;
        $formstroke1 = $this->input->post('formstroke1',true);
        $data['formstroke1'] = json_encode($formstroke1);
       // $order_amount = isset($formstroke1[2]) ? $order_amount+intval($formstroke1[2]) : 
        $formaircraftHotel =  $this->input->post('formaircraftHotel',true);
        $data['formaircraftHotel'] = json_encode($formaircraftHotel);
      //  $order_amount = isset($formaircraftHotel[2]) ? $order_amount+intval($formaircraftHotel[2]) : $order_amount;
        $rs=$this->User_model->user_insert('v_h5_order',$data);
     
           if($rs){
              $this->load->view('ziyouxing/ok_1',$data);
           }       
    }

    public function sub_zyx(){

        $data['title'] = $this->input->post('title',true);
        $data['forminpFlight'] =explode(';', $this->input->post('forminpFlight',true)) ;
        $data['formcar_come'] = explode(';', $this->input->post('formcar_come',true));
        $data['formcar_go'] =explode(';', $this->input->post('formcar_go',true)) ;
        $data['formhotel'] = explode(';', $this->input->post('formhotel',true));
        $data['formstroke1'] = explode(';', $this->input->post('formstroke1',true));
        $data['formstroke2'] =explode(';', $this->input->post('formstroke2',true)) ;
        $data['formstroke3'] = explode(';', $this->input->post('formstroke3',true));
        $data['formstroke4'] = explode(';', $this->input->post('formstroke4',true));
        $data['formstroke5'] = explode(';', $this->input->post('formstroke5',true));
        $data['formpackage'] = explode(';', $this->input->post('formpackage',true));
        $data['forminpAir'] = explode(';', $this->input->post('forminpAir',true));
        $data['formaircraftHotel'] =explode(';', $this->input->post('formaircraftHotel',true)) ;
        $data['url'] = $_SERVER['HTTP_REFERER'];

        if($data['title'])
        {
            $this->load->view('ziyouxing/write',$data);
        }
        else
        {
            redirect($data['url']);
        }

    }

    public function order_detail($order_sn){
        if(!$order_sn || !is_numeric($order_sn))
        {
            redirect(base_url('lst/nanyanginfo'));
        }
        $where = 'order_sn='.$order_sn.' AND is_del=0';
        $order_info = $this->User_model->get_select_one($select='*',$where,'v_h5_order');
        if($order_info)
        {
            $data['order_fly'] = json_decode($order_info['order_fly']);
            $data['order_car_come'] = json_decode($order_info['order_car_come']);
            $data['order_car_come1'] = json_decode($order_info['order_car_come1']);
            $data['order_car_go'] = json_decode($order_info['order_car_go']);
            $data['order_hotel'] = json_decode($order_info['order_hotel']);
            $data['order_trip'] = json_decode($order_info['order_trip']);
            $data['order_amount'] = json_decode($order_info['order_amount']);
            $data['order_sn'] = json_decode($order_info['order_sn']);
            $data['add_time'] = substr($order_info['add_time'], 0,10);
            $data['url']=$order_info['url'];
            $this->load->view('nanyang/order',$data);
        }
        else
        {
            $this->load->view('nanyang/nothing');
        }

    }

    public function order_detail_zyx($order_sn){

        if(!$order_sn || !is_numeric($order_sn))
        {
            redirect(base_url('lst/ziyouxinginfo'));
        }
        $where = 'order_sn='.$order_sn.' AND is_del=0';
        $order_info = $this->User_model->get_select_one($select='*',$where,'v_h5_order');
        if($order_info)
        {
            $data['order_fly'] = json_decode($order_info['order_fly']);
            $data['order_car_come'] = json_decode($order_info['order_car_come']);
            $data['order_car_come1'] = json_decode($order_info['order_car_come1']);
            $data['order_car_come2'] = json_decode($order_info['order_car_come2']);
            $data['order_car_go'] = json_decode($order_info['order_car_go']);
            $data['order_hotel'] = json_decode($order_info['order_hotel']);
            $data['order_trip'] = json_decode($order_info['order_trip']);
            $data['order_package'] = json_decode($order_info['order_package']);
          //$data['formaircraftHotel'] = json_decode($order_info['formaircraftHotel']);
            $data['formstroke1'] = json_decode($order_info['formstroke1']);
            $data['order_sn'] = $order_info['order_sn'];
            $data['add_time'] = substr($order_info['add_time'], 0,10);
            $data['h5_id']=$order_info['h5_id'];
            $data['url']=$order_info['url'];
            $data['set_date'] = $order_info['set_date'];
            $data['order_amount'] = $order_info['order_amount'];

            $this->load->view('ziyouxing/order',$data);
        }
        else
        {
            $this->load->view('ziyouxing/nothing');
        }

    }

   //欧洲用车订单详情
   public function order_detail_ouzhou($order_sn){
       if(!$order_sn || !is_numeric($order_sn))
        {
            redirect(base_url('lst/ziyouxinginfo'));
        }
        $where = 'order_sn='.$order_sn.' AND is_del=0';
        $order_info = $this->User_model->get_select_one($select='*',$where,'v_h5_order');
        if($order_info){
        $data['order_amount']=$order_info['order_amount'];
        $data['order_car_come']=json_decode($order_info['order_car_come']);
        $data['formstroke1']=json_decode($order_info['formstroke1']); 
        $data['formaircraftHotel']=json_decode($order_info['formaircraftHotel']);
        $data['jie']=explode(';',$data['order_car_come']);
        $data['song']=explode(';',$data['formstroke1']);
        $data['bao']=explode(';',$data['formaircraftHotel']);
        $data['jgoujia']=explode(',',$data['jie'][0]);
        $data['jchenshi']=explode(',',$data['jie'][1]);
        $data['jjiage']=explode(',',$data['jie'][2]);
        $data['jleixin']=explode(',',$data['jie'][3]);
        $data['sgoujia']=explode(',',$data['song'][0]);
        $data['schenshi']=explode(',',$data['song'][1]);
        $data['sjiage']=explode(',',$data['song'][2]);
        $data['sleixin']=explode(',',$data['song'][4]);
        $data['bgoujia']=explode(',',$data['bao'][0]);
        $data['bchenshi']=explode(',',$data['bao'][1]);
        $data['bjiage']=explode(',',$data['bao'][2]);
        $data['bleixin']=explode(',',$data['bao'][3]);
        $j=count($data['jgoujia']);
        $s=count($data['sgoujia']);
        $b=count($data['bgoujia']);
        for($i=0;$i<$j;$i++){
        $data['dataj'][$i]=array(
      'guojia'=>$data['jgoujia'][$i],
      'chenshi'=>$data['jchenshi'][$i],
      'jiage'=>$data['jjiage'][$i],
      'leixin'=>$data['jleixin'][$i]
    );
         }
      for($i=0;$i<$s;$i++){   
       $data['datas'][$i]=array(
      'guojia'=>$data['sgoujia'][$i],
      'chenshi'=>$data['schenshi'][$i],
      'jiage'=>$data['sjiage'][$i],
      'leixin'=>$data['sleixin'][$i]
    );
         }
       for($i=0;$i<$b;$i++){
       $data['datab'][$i]=array(
      'guojia'=>$data['bgoujia'][$i],
      'chenshi'=>$data['bchenshi'][$i],
      'jiage'=>$data['bjiage'][$i],
      'leixin'=>$data['bleixin'][$i]

    );
         }

        //echo "<pre>";
        //print_r($data);
        //echo "</pre>";

        }
         $this->load->view('ziyouxing/order_1',$data);
        
       
       
   } 
   //新吉普自由行详情
   public function jp_zyx_xq($order_sn){
   if(!$order_sn || !is_numeric($order_sn))
        {
            redirect(base_url('lst/ziyouxinginfo'));
        }
        $where = 'order_sn='.$order_sn.' AND is_del=0';
        $order_info = $this->User_model->get_select_one($select='*',$where,'v_h5_order');
        if($order_info)
        {
            $data['order_fly'] = json_decode($order_info['order_fly']);
            $data['order_car_come'] = json_decode($order_info['order_car_come']);
            $data['order_car_come1'] = json_decode($order_info['order_car_come1']);
            $data['order_car_come2'] = json_decode($order_info['order_car_come2']);
            $data['order_car_go'] = json_decode($order_info['order_car_go']);
            $data['order_hotel'] = json_decode($order_info['order_hotel']);
            $data['order_trip'] = json_decode($order_info['order_trip']);
            $data['order_package'] = json_decode($order_info['order_package']);
            $data['order_amount'] = json_decode($order_info['order_amount']);
            $data['order_sn'] = json_decode($order_info['order_sn']);
            $data['add_time'] = substr($order_info['add_time'], 0,10);
            $data['ticket']=json_decode($order_info['ticket']);
            $data['spa']=json_decode($order_info['spa']);
            $data['h5_id']=$order_info['h5_id'];
            $data['url']=$order_info['url'];
            //分割门票
            $menpiao=explode(';', $data['ticket']); 
            $menpiao['piaoname']=explode(',', $menpiao[0]);
            $menpiao['piaolx']=explode(',', $menpiao[1]);
            $menpiao['piaomoney']=explode(',', $menpiao[2]);
             $menpiao['date']=explode(',', $menpiao[3]);
            $menpiao['piao']=explode(',', $menpiao[4]);
            for($i=0;$i<count($menpiao['piaoname']);$i++){
                $data['menpiao'][$i]=array(
                'piaoname'=>$menpiao['piaoname'][$i],
                'piaolx'=>$menpiao['piaolx'][$i],
                'piaomoney'=>$menpiao['piaomoney'][$i],
                'date'=>$menpiao['date'][$i],
                'piao'=>$menpiao['piao'][$i]
                    );

            }
            //分割spa
            $spa=explode(';', $data['spa']); 
            $spa['piaoname']=explode(',', $spa[0]);
            $spa['piaolx']=explode(',', $spa[1]);
            $spa['piaomoney']=explode(',', $spa[2]);
            $spa['date']=explode(',', $spa[3]);
            $spa['piao']=explode(',', $spa[4]);
            for($i=0;$i<count($spa['piaoname']);$i++){
                $data['spa_piao'][$i]=array(
                'piaoname'=>$spa['piaoname'][$i],
                'piaolx'=>$spa['piaolx'][$i],
                'piaomoney'=>$spa['piaomoney'][$i],
                'date'=>$spa['date'][$i],
                'piao'=>$spa['piao'][$i]
                    );

            }
            //分割用车
            $car=explode(';', $data['order_car_come']); 
            $car['piaoname']=explode(',', $car[0]);
            $car['piaolx']=explode(',', $car[1]);
            $car['piaomoney']=explode(',', $car[2]);
            $car['piao']=explode(',', $car[3]);
            for($i=0;$i<count($car['piaoname']);$i++){
                $data['car'][$i]=array(
                'piaoname'=>$car['piaoname'][$i],
                'piaolx'=>$car['piaolx'][$i],
                'piaomoney'=>$car['piaomoney'][$i],
                'piao'=>$car['piao'][$i]
                    );

            }
              $car1=explode(';', $data['order_car_come1'] ); 
            $car1['piaoname']=explode(',', $car1[0]);
            $car1['piaolx']=explode(',', $car1[1]);
            $car1['piaomoney']=explode(',', $car1[2]);
            $car1['piao']=explode(',', $car1[3]);
            for($i=0;$i<count($car1['piaoname']);$i++){
                $data['car1'][$i]=array(
                'piaoname'=>$car1['piaoname'][$i],
                'piaolx'=>$car1['piaolx'][$i],
                'piaomoney'=>$car1['piaomoney'][$i],
                'piao'=>$car1['piao'][$i]
                    );

            }
            $car2=explode(';', $data['order_car_go']); 
            $car2['piaoname']=explode(',', $car2[0]);
            $car2['piaolx']=explode(',', $car2[1]);
            $car2['piaomoney']=explode(',', $car2[2]);
            $car2['piao']=explode(',', $car2[3]);
            for($i=0;$i<count($car2['piaoname']);$i++){
                $data['car2'][$i]=array(
                'piaoname'=>$car2['piaoname'][$i],
                'piaolx'=>$car2['piaolx'][$i],
                'piaomoney'=>$car2['piaomoney'][$i],
                'piao'=>$car2['piao'][$i]
                    );

            }
           
       //分割机票
            $data['jipiao']=explode(';',$data['order_fly'][0]);
           //echo "<pre>";
           // print_r( $menpiao);
           // echo "</pre>";
            $this->load->view('ziyouxing/order_2',$data);
        }
        else
        {
            $this->load->view('ziyouxing/nothing');
        }

    }

    public function order_detail_zyx_temp($order_sn){
        if(!$order_sn || !is_numeric($order_sn))
        {
            redirect(base_url('lst/ziyouxinginfo'));
        }
        $where = 'order_sn='.$order_sn.' AND is_del=0';
        $order_info = $this->User_model->get_select_one($select='*',$where,'v_h5_order');
        if($order_info)
        {
            $data['order_fly'] = json_decode($order_info['order_fly']);
            $data['order_car_come'] = json_decode($order_info['order_car_come']);
            $data['order_car_come1'] = json_decode($order_info['order_car_come1']);
            $data['order_car_come2'] = json_decode($order_info['order_car_come2']);
            $data['order_car_go'] = json_decode($order_info['order_car_go']);
            $data['order_hotel'] = json_decode($order_info['order_hotel']);
            $data['order_trip'] = json_decode($order_info['order_trip']);
            $data['order_package'] = json_decode($order_info['order_package']);
            $data['order_amount'] = json_decode($order_info['order_amount']);
            $data['order_sn'] = json_decode($order_info['order_sn']);
            $data['add_time'] = substr($order_info['add_time'], 0,10);
            $data['h5_id']=$order_info['h5_id'];
            $data['url']=$order_info['url'];
            $this->load->view('ziyouxing/order_temp',$data);
        }
        else
        {
            $this->load->view('ziyouxing/nothing');
        }

    }
    
    public function jp_zyx(){
     //   echo "<pre>";
     //   print_r($_POST);
     //   echo "</pre>";
        $car=$this->input->post('formcar_come');
        $car1=$this->input->post('formcar_come1');
        $car2=$this->input->post('formcar_come2');
        $hotel= $this->input->post('formhotel');
        $xc=$this->input->post('formstroke1');
        
        $data['car']=explode(';', $car);
        $data['car1']=explode(';', $car1);
        $data['car2']=explode(';', $car2);
        
        $data['car']['baoche']=explode(',', $data['car']['0']);
        $data['car']['bz']=explode(',', $data['car']['1']);
        $data['car']['money']=explode(',', $data['car']['2']);
        $data['car']['money_cl']=array_sum($data['car']['money']);
        for($i=0;$i<count($data['car']['baoche']);$i++){
            $data['car_data'][$i]=array(
                'car_name'=>$data['car']['baoche'][$i],
                'car_money'=>$data['car']['money'][$i],
                'car_bz'=>$data['car']['bz'][$i]
                
            );
            
        }
        $data['car1']['baoche']=explode(',', $data['car1']['0']);
        $data['car1']['bz']=explode(',', $data['car1']['1']);
        $data['car1']['money']=explode(',', $data['car1']['2']);
        $data['car1']['money_cl']=array_sum($data['car1']['money']);
        for($i=0;$i<count($data['car1']['baoche']);$i++){
            $data['car_data1'][$i]=array(
                'car_name'=>$data['car1']['baoche'][$i],
                'car_money'=>$data['car1']['money'][$i],
                'car_bz'=>$data['car1']['bz'][$i]
                
            );
            
        }
        $data['car2']['baoche']=explode(',', $data['car2']['0']);
        $data['car2']['bz']=explode(',', $data['car2']['1']);
        $data['car2']['money']=explode(',', $data['car2']['2']);
        $data['car2']['money_cl']=array_sum($data['car2']['money']);
        for($i=0;$i<count($data['car2']['baoche']);$i++){
            $data['car_data2'][$i]=array(
                'car_name'=>$data['car2']['baoche'][$i],
                'car_money'=>$data['car2']['money'][$i],
                'car_bz'=>$data['car2']['bz']
                
            );
            
        }

        $data['hotel']=explode(';', $hotel);
        $data['hotel']['jibi']=$data['hotel']['0'];
        $data['hotel']['name']=$data['hotel']['1'];
        $data['hotel']['jiage']=$data['hotel']['2'];
        $data['hotel']['tianshu']=$data['hotel']['3'];
        $data['hotel']['money']=$data['hotel']['jiage'];
        $data['jd'][] = $data['hotel'];
        
        $data['xc']=explode(';', $xc);
        $data['xc']['xc_name']=explode(',', $data['xc'][1]);
        $data['xc']['xc_money']=explode(',', $data['xc'][2]);
        $data['xc']['xc_date']=explode(',', $data['xc'][3]);
        $data['xc']['xc_xx']=explode(',', $data['xc'][4]);
        $data['xc']['money']=array_sum($data['xc']['xc_money']);
        for($i=0;$i<count($data['xc']['xc_name']);$i++){
            $data['xc_data'][$i]=array(
                'xc_name'=>$data['xc']['xc_name'][$i],
                'xc_date'=>$data['xc']['xc_date'][$i],
                'xc_money'=>$data['xc']['xc_money'][$i],
                'xc_xx'=>$data['xc']['xc_xx'][$i]
                
            );
            
        }
        
        $data['h5_id'] = $this->input->post('h5_id', true);
        $data['set_date'] = $this->input->post('godate', true);
        $data['h5_title'] = $this->input->post('title',true);
        $data['order_sn'] = $this->get_order_sn();
        $data['order_amount']=$data['hotel']['money']+$data['car']['money_cl']+$data['xc']['money']+$data['car1']['money_cl']+$data['car2']['money_cl'];
        $datav= array(
            'h5_id' => $data['h5_id'] ? $data['h5_id'] : 0,
            'h5_title' => $data['h5_title'],
            'order_amount'=>$data['order_amount'],
            'order_sn'=>$data['order_sn'],
            'order_car_come'=>$car,
            'order_hotel'=>$hotel,
            'order_trip'=>$xc,
            'dd_source'=>1,
            'zf_state'=>2,
            'vip1'=>$vip,
            'add_time'=> date('Ymd',time())
        );
//echo "<pre>";
//  print_r($datav);
 //       echo "</pre>";
        $order_id = $this->User_model->user_insert('v_h5_order',$datav);
        if(!$order_id){
            // 404 系统出错页面
        }
        
        //支付宝支付
        $data['zfb_url']=base_url('Api1/zfbs');

        //微信支付
        $data['wxjspay_url']=base_url('pay/wxjs_pay');
        $this->load->view('orderhtml/order_show_nocard',$data);
    }

    public function xj_zyx(){
        
        $data['h5_id'] = $this->input->post('h5_id', true);
        $data['h5_title'] = $this->input->post('title',true);
        $data['order_sn'] = $this->get_order_sn();
        $data['set_date'] = $this->input->post('godate',true);
        $data['order_amount'] = $this->input->post('price',true);
        $datav= array(
            'h5_id' => $data['h5_id'] ? $data['h5_id'] : 0,
            'h5_title' => $data['h5_title'],
            'set_date' => $data['set_date'],
            'order_amount' => $data['order_amount'],
            'order_sn' => $data['order_sn'],
            'order_car_come' =>'',
            'order_hotel' =>'',
            'order_trip' =>'',
            'dd_source' => 1,
            'zf_state' => 2,
            'vip1' => '',
            'add_time'=> date('Ymd',time())
        );
        $order_id = $this->User_model->user_insert('v_h5_order',$datav);
        if(!$order_id){
            // 404 系统出错页面
        }
        
        //支付宝支付
        $data['zfb_url']=base_url('Api1/zfbs');
        
        //微信支付
        $data['wxjspay_url']=base_url('pay/wxjs_pay');
        $this->load->view('orderhtml/order_show_card',$data);
    }

    //获取order_sn
    function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    //表单信息切割处理
    function get_form_info($data)
    {
        $amount = 0;
        $info = array();
        $temp = explode(';', $data);
        if($temp){
            foreach ($temp as $key => $value) {
                $tmp =  explode(',',$value);
                if($tmp)
                {
                    foreach ($tmp as $k => $v) {
                        $info[$k][]=$v;
                        if($key==3)
                        {
                            $amount = isset($v) ? $amount+intval($v) : $amount;
                        }
                    }
                }
            }
        }
        return array('amount'=>$amount,'info'=>$info);

    }

}
