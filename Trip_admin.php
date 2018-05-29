<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Trip_admin extends CI_Controller {
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

    //type 0 数据类型
    public function tp_one_add()
    {
        $act_id=$this->input->get('act_id',TRUE);
        $data['location']=array();
        //echo '<pre>';print_r($data);exit();
        $data['sub_url']=base_url("trip_admin/tp_one_insert?debug=1");
      //  $data['sub_url']="http://www.api.org/trip_admin/tp_one_insert";
        if($act_id)
        {
            $data['sub_url']=base_url("trip_admin/tp_one_sub?debug=1");
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
                }elseif($v['attr_type']==3)
                {
                    if($v['attr_val']=='成人')
                    {
                        $data['man_price']=$v['attr_price'];
                    }
                    else
                    {
                        $data['child_price']=$v['attr_price'];
                    }

                }
                else
                {
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
                $data['user_id']=3706;
            }
        }
        $data['range_list']=$this->User_model->get_select_all('id,name',"is_hot='1' OR is_down='1'",'id','ASC','v_location');
        // $time=time();
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


        if($this->input->get('test')){
            echo '<pre>';print_r($data);exit();

        }
        $this->load->view('admin_view/tp_one',$data);
    }



    public function tp_one_sub()
    {
        //echo '<pre>';
        //print_r($_POST);
        //exit();
        $act_id=$this->input->post('act_id',TRUE);
        if(!$act_id){
            return false;
        }

        $old_products_info=$this->User_model->get_trip_products_detail($act_id);

        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);
        $attr_name_arr=$this->input->post('attr_name',true);

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
        // echo '<pre>';print_r($date_arr);print_r($_POST);exit();

     

           if(is_array($attr_name_arr))
        {
            $form=count($attr_name_arr);

        }
        else
        {
            $form=0;
        }


        $attr_arr=array();

        for($i=1;$i<=$form;$i++)
        {

            $attr='attr'.$i;
            $attr_price='attr_price'.$i;

            $supply_info_one='supply_info_one'.$i;

            $attr_arr[$i]['attr_name']=$attr_name_arr[$i-1];
            $attr_arr[$i]['attr_type']='2';


            $attr_arr[$i]['attr']=$this->input->post($attr);
            $attr_arr[$i]['attr_price']= $this->input->post($attr_price) ;
            $attr_arr[$i]['supply_info_one']= $this->input->post($supply_info_one) ;

            foreach( $attr_arr[$i]['attr'] as $k=>$v)
            {
                $attr_arr[$i]['attr_arr'][]=array(
                    'attr_val'=>$v,
                    'attr_price'=>$attr_arr[$i]['attr_price'][$k]?$attr_arr[$i]['attr_price'][$k]:0,
                    'supply_info_one'=>$attr_arr[$i]['supply_info_one'][$k]?$attr_arr[$i]['supply_info_one'][$k]:'',

                );

            }
            unset( $attr_arr[$i]['attr']);
            unset( $attr_arr[$i]['attr_price']);
            unset( $attr_arr[$i]['supply_info_one']);


        }
        $attr_arr=array_values($attr_arr);

        //echo '<pre>';print_r($date_arr);print_r($attr_arr);exit();

        $data['title']=$this->input->post('title',true);
        $data['ipro']=$this->input->post('ipro',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special= $data['special']=$this->input->post('special',true);
        //  $data['range_name']=$this->input->post('range_detail',true);


        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
    
        $data['content']= $data['content_text'];


        $data['user_id']=$this->input->post('user_id',true);
      //  $data['business_id']=$this->input->post('business_id',true);
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
            $data['banner_image']=$old_products_info['banner_image'];
        }

        if($_FILES['banner_hot']['error']==0)
        {
            $banner_hot=$this->upload_image('banner_hot', $data['user_id'].'banner_hot');
            $data['banner_hot']= $this->imagecropper($banner_hot,'banner_hot','time',$width='500',$height='340');

        }else{
            $data['banner_hot']=$old_products_info['banner_hot'];
        }

        if($_FILES['banner_product']['error']==0)
        {
            $banner_product=$this->upload_image('banner_product', $data['user_id'].'banner_product');
            $data['banner_product']= $this->imagecropper($banner_product,'banner_product','time',$width='150',$height='150');
        }else{
            $data['banner_product']=$old_products_info['banner_product'];
        }



        $data['act_status']='2';
        $data['add_time']=time();

        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'3'),'v_goods');
        $this->User_model->update_one(array('act_id'=>$act_id),array('is_show'=>'0'),'v_act_range');
        $this->User_model->update_one(array('act_id'=>$act_id),$data,$table='v_activity_children');

       // $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
        //echo $act_id;
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
            'is_show'=>'1',

        );
        if($special==7){
            $data['shop_price']=$ori_price;
        }
        $goods_id=$this->User_model->user_insert('v_goods',$data);


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

                $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
            }

        }
        $man_price=$this->input->post("man_price",TRUE);
        if(!$man_price)
        {
            $man_price=0;
        }
        $child_price=$this->input->post("child_price",TRUE);
        if(!$child_price)
        {
            $child_price=0;
        }

        $temp_arr=array(
            'attr_name'=>'人数',
            'attr_type'=>'3',
            'add_time'=>time()
        );
        $attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

        $temp['attr_id']=$attr_id;
        $temp['goods_id']=$goods_id;
        $temp['add_time']=time();
        $temp['attr_val']='成人';
        $temp['attr_price']=$man_price;
        $this->User_model-> user_insert($table='v_goods_attr',$temp);

        $temp['attr_id']=$attr_id;
        $temp['goods_id']=$goods_id;
        $temp['add_time']=time();
        $temp['attr_val']='儿童';
        $temp['attr_price']=$child_price;
        $this->User_model-> user_insert($table='v_goods_attr',$temp);

        redirect(base_url("Trip_admin/tp_one_add?act_id={$act_id}"));
    }


    public function tp_one_insert()
    {


        //echo '<pre>';print_r($_POST);exit();
        $goods_number=trim($this->input->post('goods_number',true));
        $ori_price=$this->input->post('ori_price',true);
        $oori_price=$this->input->post('oori_price',true);
        $attr_name_arr=$this->input->post('attr_name',true);

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
         // echo '<pre>';print_r($date_arr);print_r($_POST);exit();

        if(is_array($attr_name_arr))
        {
            $form=count($attr_name_arr);

        }
        else
        {
            $form=0;
        }

        $attr_arr=array();

        for($i=1;$i<=$form;$i++)
        {

            $attr='attr'.$i;
            $attr_price='attr_price'.$i;

            $supply_info_one='supply_info_one'.$i;

            $attr_arr[$i]['attr_name']=$attr_name_arr[$i-1];
            $attr_arr[$i]['attr_type']='2';


                $attr_arr[$i]['attr']=$this->input->post($attr);
                $attr_arr[$i]['attr_price']= $this->input->post($attr_price) ;
                $attr_arr[$i]['supply_info_one']= $this->input->post($supply_info_one) ;

                foreach( $attr_arr[$i]['attr'] as $k=>$v)
                {
                    $attr_arr[$i]['attr_arr'][]=array(
                        'attr_val'=>$v,
                        'attr_price'=>$attr_arr[$i]['attr_price'][$k]?$attr_arr[$i]['attr_price'][$k]:0,
                        'supply_info_one'=>$attr_arr[$i]['supply_info_one'][$k]?$attr_arr[$i]['supply_info_one'][$k]:'',

                    );

                }
                unset( $attr_arr[$i]['attr']);
                unset( $attr_arr[$i]['attr_price']);
                unset( $attr_arr[$i]['supply_info_one']);


        }
        $attr_arr=array_values($attr_arr);

        //echo '<pre>';print_r($date_arr);print_r($attr_arr);exit();

        $data['title']=$this->input->post('title',true);
        $data['ipro']=$this->input->post('ipro',true);

        $data['xtype']=$this->input->post('xtype',true);
        $data['tctype']=$this->input->post('tctype',true);
        $special= $data['special']=$this->input->post('special',true);
        //  $data['range_name']=$this->input->post('range_detail',true);


        $data['tag']=$this->input->post('tag',true);
        $data['tag']= str_replace("，",",", $data['tag']);

        $data['content_text']=$this->input->post('content_text',false);
        $data['content']= $data['content_text'];


        $data['user_id']=$this->input->post('user_id',true);
       // $data['business_id']=$this->input->post('business_id',true);
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

        $act_id=$this->User_model->user_insert($table='v_activity_children',$data);
       //echo $act_id;
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
        $goods_id=$this->User_model->user_insert('v_goods',$data);


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

                $this->User_model-> user_insert($table='v_goods_attr',$temp_arr2);
            }

        }
        $man_price=$this->input->post("man_price",TRUE);
        if(!$man_price)
        {
            $man_price=0;
        }
        $child_price=$this->input->post("child_price",TRUE);
        if(!$child_price)
        {
            $child_price=0;
        }

        $temp_arr=array(
            'attr_name'=>'人数',
            'attr_type'=>'3',
            'add_time'=>time()
        );
        $attr_id=$this->User_model-> user_insert($table='v_attr',$temp_arr);

        $temp['attr_id']=$attr_id;
        $temp['goods_id']=$goods_id;
        $temp['add_time']=time();
        $temp['attr_val']='成人';
        $temp['attr_price']=$man_price;
        $this->User_model-> user_insert($table='v_goods_attr',$temp);

        $temp['attr_id']=$attr_id;
        $temp['goods_id']=$goods_id;
        $temp['add_time']=time();
        $temp['attr_val']='儿童';
        $temp['attr_price']=$child_price;
        $this->User_model-> user_insert($table='v_goods_attr',$temp);

        redirect(base_url("Trip_admin/tp_one_add?act_id={$act_id}"));
    }




    public function tp_three_add()
    {
        $data=array();
        $act_id=$this->input->get('act_id',TRUE);
        $data['location']=array();
        $data['sub_url']='/trip_admin/tp_three_sub';
        $data['seats_arr']=array();
        if($act_id)
        {

        }
        else
        {
            $data['user_id']=$this->input->get('user_id',TRUE);
            if(!$data['user_id'])
            {
                $data['user_id']=3706;
            }
        }

        $data['range_list']=$this->User_model->get_select_all('id,name',"is_hot='1' OR is_down='1'",'id','ASC','v_location');

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
        $data['days']=$this->prDates($time_first,$tdaysend['time']);

        if($this->input->get('test')){
            echo '<pre>';
            print_r($data);
            exit();

        }
        $this->load->view('admin_view/tp_three',$data);
    }

    public function tp_three_sub()
    {
        echo '<pre>';
        print_r($_POST);
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
            'ip_address'=> $this->common->real_ip()
        );
        $this->User_model->user_insert('v_admin_log',$logs);
        // $this->Admin_model->add_logs($logs);
    }





    public function upload_image($filename,$fileurl,$key='time',$lt=0)
    {
        /* 如果目标目录不存在，则创建它 */
        if (!file_exists('./public/images/'.$fileurl))
        {
            if (!mkdir('./public/images/'. $fileurl))
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
                $key =time().rand(1,999999);
            }

            $pic_url="./public/images/".$fileurl."/".$key.$br;
            move_uploaded_file($file['tmp_name'], $pic_url);
            //   return substr($pic_url,1);
            if($lt==1)
            {
                return  ltrim($pic_url,'.');
            }
            return $pic_url;
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
                $key2=time();
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



    public function test_form()
    {
        $this->load->view('admin_view/test_form');
    }



    public function test_post()
    {


        $temp_arr=array();
        $radio=$this->input->post("2017-1-1[radio]");
        echo '<pre>';
       // sort($radio);
        print_r($radio);

        foreach($radio as $k=>$v)
        {

        }

        //echo '目标：<hr>';
      //  print_r($str);




    }

}