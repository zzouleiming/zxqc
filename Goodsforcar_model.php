<?php
/**
 * Created by PhpStorm.
 * User: xiaohei
 * Date: 2017/3/17
 * Time: 15:55
 */
class Goodsforcar_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->judge_time();
        $this->load->helper('url');
    }



    public function judge_time()
    {
        $time=time()-3600;
        $where="order_status=0 AND add_time<$time";
        return $this-> update_one($where,$data=array('order_status'=>4),$table='wx_order_info');
    }


    //获取车用商品列表
    public function get_goods_list($where="1=1",$page_num=6,$start=0,$expensive=0)
    {
        $this->db->select("a.*,b.url AS image_r,c.url AS image_s,d.url AS image_c");
        $this->db->where($where);
        $this->db->from('wx_new_goods as a');
        $this->db->join('v_images AS b',"a.goods_id=b.link_id AND b.type=7 AND b.isdelete=0",'left');
        $this->db->join('v_images AS c',"a.goods_id=c.link_id AND c.type=8 AND c.isdelete=0",'left');
        $this->db->join('v_images AS d',"a.goods_id=d.link_id AND d.type=9 AND d.isdelete=0",'left');
        $this->db->limit($page_num,$start);
        $this->db->order_by("a.displayorder",'ASC');
        $this->db->order_by("a.goods_id",'ASC');
        $query=$this->db->get();
        $result=$query->result_array();
        foreach($result as $k=>$v)
        {
            $result[$k]['wx_url']="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            if($expensive==1)
            {
                $result[$k]['ori_price']=$v['oori_price'];
                $result[$k]['wx_url']="/goodsforcar/goods_detail/1?goods_id=$v[goods_id]";
            }
        }

        return $result;
    }

    //微信商品订单插入
    public function wx_order_insert($order_info,$order_goods,$type=0)
    {


        $this->db->trans_begin();
        $order_id=$this->user_insert('wx_order_info',$order_info);
        foreach($order_goods as $k=>$v)
        {
            $order_goods[$k]['order_id']=$order_id;
            $this->user_insert('wx_order_goods',$order_goods[$k]);
        }
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            if($type)
            {
                $this->del(array('user_id'=>$order_info['user_id_buy']),'wx_shopping_cart');
            }

            $this->db->trans_commit();
            return $order_id;


        }
    }

    //获取单件商品信息
    public function get_goods_one($goods_id,$expensive=0)
    {

        $this->db->select("a.*,b.url AS image_r,c.url AS image_s");
        $this->db->where(array('a.goods_id'=>$goods_id));
        $this->db->from('wx_new_goods as a');
        $this->db->join('v_images AS b',"a.goods_id=b.link_id AND b.type=7 AND b.isdelete=0",'left');
        $this->db->join('v_images AS c',"a.goods_id=c.link_id AND c.type=8 AND c.isdelete=0",'left');
        $this->db->order_by("a.displayorder",'ASC');
        $this->db->order_by("a.goods_id",'ASC');
        $query=$this->db->get();
        $result=$query->row_array();
        $result['wx_url']="/goodsforcar/goods_detail?goods_id=$result[goods_id]";
        if($expensive==1)
        {
            $result['ori_price']=   $result['oori_price'];
        }
        return $result;
    }

    //快速获得商品单价
    public function get_goods_price($goods_id)
    {
        $this->db->select('ori_price');
        $this->db->where(array('goods_id'=>$goods_id));
        $query=$this->db->get('wx_new_goods');
        $rs=$query->row_array();
        return $rs['ori_price'];
    }

    //快速获取商品总价  $new_arr(ID，num)
    public function get_goods_price_all($new_arr)
    {
        $order_price=0;
        foreach($new_arr as $k=>$v)
        {
            $order_price+=$v*($this->get_goods_price($k));
        }
        return $order_price;
    }


    //导游关联表连接
   public function  get_jion(){
        $sql="select g.business_id as business_id  from v_wx_users as u inner join wx_guide_business as g on u.user_id=g.user_id";
        $query=$this->db->query($sql);
        $row=$query->row_array();
        return $row['business_id'];
        
    }
    //根据提交信息判断 商铺ID
    public function get_trip_id($thip_id,$business_name)
    {
        if(stristr($thip_id, 'JT'))
        {
            return 67373;
        }
        elseif(stristr($thip_id, 'SMT'))
        {
            return 67374;
        }
        //elseif(stristr($thip_id, 'zt'))
        elseif(stristr($thip_id, 'NP') || stristr($thip_id, 'np'))
        {
            return 67375;
        }
        elseif(stristr($thip_id, 'YL') || stristr($thip_id, 'yl'))
        {
            return 67376;
        }
        return '1';
    }

    //获取购物车信息 单个用户 购物数量和
    public function get_car_sum($user_id)
    {
        $this->db->select_sum('sum');
        $this->db->where(array('is_show'=>1,'user_id'=>$user_id));
        $this->db->from('wx_shopping_cart');
        $query=$this->db->get();
        $result=$query->row_array();
        return $result['sum'];
    }

    //获取某个用户的所有购物车信息
    public function get_car_info($user_id,$page_num=6,$start=0,$expensive=0)
    {
        $business_id = $_SESSION['business_id'];
        $this->db->select('a.*,a.sum as num,b.goods_name,b.ori_price,b.oori_price,c.url as image_s');
        $this->db->where(array('a.is_show'=>1,'a.user_id'=>$user_id, 'b.business_id' => $business_id));
        $this->db->join('wx_new_goods as b','b.goods_id=a.goods_id','left');
        $this->db->join('v_images as c','b.goods_id=c.link_id and c.type=8 and c.isdelete=0','left');
        $this->db->limit($page_num,$start);
        $query=$this->db->get('wx_shopping_cart as a');
        $rs=$query->result_array();
        if($expensive==1)
        {
            foreach($rs as $k=>$v)
            {
                $rs[$k]['ori_price']=$v['oori_price'];
            }
        }
        return $rs;
    }

    //获取某个用户 某件商品的购物车信息
    public function get_car_one($user_id,$goods_id)
    {
        $this->db->select('a.*,b.goods_name,b.ori_price,c.url as image_s');
        $this->db->where(array('a.is_show'=>1,'a.user_id'=>$user_id,'a.goods_id'=>$goods_id));
        $this->db->join('wx_new_goods as b','b.goods_id=a.goods_id','left');
        $this->db->join('v_images as c','b.goods_id=c.link_id and c.type=8 and c.isdelete=0','left');
        $query=$this->db->get('wx_shopping_cart as a');
        return $query->row_array();
    }

    public function get_count($where,$table)
    {
        $this->db->select(' count(*) as count');
        $this->db->where($where);
        $query= $this->db->get($table);
        $row = $query->row_array();
        return $row['count'];

    }

    /*
 * 删除
 */

    public function del($where,$table)
    {
        $this->db->where($where);
        return $this->db->delete($table);
    }

    //数量增加
    public function amount_update($fielded,$field,$where,$table='wx_shopping_cart')
    {
        $this->db->set($fielded, $field, FALSE);
        $this->db->where($where);
        $this->db->update($table);
    }

    //0 导游查询 1店铺查询
    public function account_list($guide_id,$business_id,$page_num=8,$start=0,$type=0)
    {

        if($type)
        {
            $sql="select a.trip_id,a.pay_id,a.user_id_sell,round(sum(a.order_amount),2) as sum_money from wx_order_info a,
              (select b.trip_id from
                    (select trip_id,count(trip_id) as count from wx_order_info  where order_status=3 and business_id=$business_id and is_show=1 group by trip_id) as b ,
                    (select trip_id,count( trip_id) as count from wx_order_info where business_id=$business_id and is_show=1 and order_status in (1,2,3) group by trip_id)  as c
              where b.count=c.count and c.trip_id=b.trip_id) as d
              where a.trip_id=d.trip_id  and a.business_id=$business_id
              group by a.trip_id,a.pay_id,a.user_id_sell";
        }
        else
        {
            $sql="select a.trip_id,a.pay_id,a.user_id_sell,round(sum(a.order_amount),2) as sum_money from wx_order_info a,
              (select b.trip_id from
                    (select trip_id,count(trip_id) as count from wx_order_info  where order_status=3 and user_id_sell=$guide_id and is_show=1 group by trip_id) as b ,
                    (select trip_id,count( trip_id) as count from wx_order_info where user_id_sell=$guide_id and is_show=1 and order_status in (1,2,3) group by trip_id)  as c
              where b.count=c.count and c.trip_id=b.trip_id) as d
              where a.trip_id=d.trip_id and a.user_id_sell=$guide_id
              group by a.trip_id,a.pay_id,a.user_id_sell";
        }
        $query = $this->db->query($sql);
        $rs=$query->result_array();

        return $rs;
    }


    //for business_id  trip_id date
    public function  get_account_infO($business_id,$trip_id=0,$date=0)
    {
        $where=" a.trip_id=d.trip_id  and a.business_id=$business_id";
        if($trip_id)
        {
            $where.=" and a.trip_id LIKE '%$trip_id%'";
        }

        if($date)
        {
            $date=strtotime($date);
            $date_end=$date+84600;
            $where.=" AND a.add_time >=$date AND a.add_time<$date_end";

        }

        $sql="select a.trip_id,a.pay_id,a.user_id_sell,round(sum(a.order_amount),2) as sum_money from wx_order_info a,
              (select b.trip_id from
                    (select trip_id,count(trip_id) as count from wx_order_info  where order_status=3 and business_id=$business_id and is_show=1 group by trip_id) as b ,
                    (select trip_id,count( trip_id) as count from wx_order_info where business_id=$business_id and is_show=1 and order_status in (1,2,3) group by trip_id)  as c
              where b.count=c.count and c.trip_id=b.trip_id) as d
              where {$where}
              group by a.trip_id,a.pay_id,a.user_id_sell";


        $query = $this->db->query($sql);
        $rs=$query->result_array();

        return $rs;
    }

    //获取多条订单的商品记录
    public function get_order_goods_all($where="1=1",$page_num=6,$start=0)
    {


        $select="a.goods_name,b.add_time,a.goods_id,a.goods_price,sum(a.goods_sum) as goods_sum,sum(a.goods_number) as  goods_count,b.user_id_sell,b.trip_id,c.user_name,d.url as image_s";
        $this->db->select($select);
        $this->db->join('wx_order_info as b',"b.order_id=a.order_id",'left');
        $this->db->join('v_wx_users as c',"c.user_id=b.user_id_sell",'left');
        $this->db->join('v_images as d',"d.link_id=a.goods_id AND d.type =8 AND d.isdelete=0",'left');
        $this->db->where($where);
        $this->db->limit($page_num,$start);
        $this->db->group_by("b.trip_id");
        $this->db->group_by("a.goods_id");
       // $this->db->group_by("goods_count");
        $this->db->group_by("image_s");

        $this->db->order_by("b.trip_id",'DESC');
        $this->db->order_by("b.add_time",'DESC');
        $query=$this->db->get('wx_order_goods as a');
        $row=$query->result_array();

        return $row;
    }

    //获取多条订单的商品记录 count
    public function get_order_goods_all_count($where="1=1")
    {


        $sql ="SELECT count(t.tcounts) as counts FROM
                (SELECT count(*) as tcounts FROM `wx_order_goods` as `a`
                LEFT JOIN `wx_order_info` as `b` ON `b`.`order_id`=`a`.`order_id`
                LEFT JOIN `v_wx_users` as `c` ON `c`.`user_id`=`b`.`user_id_sell`
                LEFT JOIN `v_images` as `d` ON `d`.`link_id`=`a`.`goods_id` AND `d`.`type` =8
                WHERE ".$where." GROUP BY `b`.`trip_id`, `a`.`goods_id` ) as t ";
        $query=$this->db->query($sql);
        $row=$query->row_array();
        return $row['counts'];

    }

    //快速获取名字
    public function get_user_name($user_id)
    {
        $sql="select user_name from v_wx_users where user_id=$user_id";
        $query=$this->db->query($sql);
        $row=$query->row_array();
        return $row['user_name'];
    }

    //获取多条订单记录
    public function get_order_all($where="1=1",$page_num=6,$start=0,$order=0)
    {
        $selectgoods="goods_id,goods_name,goods_price,goods_number,goods_sum,oori_price";
        $select="consignee,add_time,mobile,goods_all_num,user_id_sell,
        user_id_buy,business_id,trip_id,
        order_amount,order_status,order_id,order_sn,commont
        ";
        $this->db->select($select);
        $this->db->where($where);
        $this->db->limit($page_num,$start);

        if(!$order)
        {
            $this->db->order_by("trip_id",'DESC');
        }

        $this->db->order_by("add_time",'DESC');
        $query=$this->db->get('wx_order_info');
        $resultorder=$query->result_array();
        if(count($resultorder)>0)
        {
            foreach($resultorder as $k=>$v)
            {
                $resultorder[$k]['user_id_sell_name']=$this->get_user_name($v['user_id_sell']);

                if($v['order_status']==0 )
                {
                    $resultorder[$k]['order_state']='未付款';
                }
                elseif($v['order_status']==1)
                {
                    $resultorder[$k]['order_state']='已付款';
                }
                elseif($v['order_status']==2)
                {
                    $resultorder[$k]['order_state']='现金交易';
                }
                elseif($v['order_status']==3)
                {
                    $resultorder[$k]['order_state']='交易完成';
                }else{
                    $resultorder[$k]['order_state']='交易关闭';
                }
                $resultorder[$k]['add_date']=date('Y-m-d',$v['add_time']);
                $resultorder[$k]['show_for_guide']=base_url("caradmin/show_detail?order_id=$v[order_id]");
                $select_goods=$selectgoods;
                $this->db->select($select_goods);
                $this->db->where(array('order_id'=>$v['order_id']));
                $query=$this->db->get('wx_order_goods');
                $row=$query->result_array();
                $resultorder[$k]['goods']=$row;
                foreach($resultorder[$k]['goods'] as $k1=>$v1)
                {
                    $resultorder[$k]['goods'][$k1]=array_merge($resultorder[$k]['goods'][$k1],$this->get_car_goods_image($v1['goods_id']));
                }
            }


        }
        return $resultorder;

    }


    //获取地接社店铺信息
    public function get_shop_one($where="1=1")
    {
        $this->db->select('*');
        $this->db->where($where);
        $query=$this->db->get('wx_shop');
        return $query->row_array();
    }

    //获取单订单的信息 买家用

    public function get_order_detail($order_id)
    {
        $where="order_id=$order_id OR order_sn=$order_id";
        $selectgoods="goods_id,goods_name,goods_price,goods_number,goods_sum,oori_price";
        $select="consignee,add_time,mobile,goods_all_num,user_id_sell,pay_status,
        appId,nonceStr,timeStamp,signType,package,paySign,
        user_id_buy,business_id,trip_id,
        order_amount,order_status,order_id,order_sn,commont";
        $this->db->select($select);
        $this->db->where($where);
        $query=$this->db->get('wx_order_info');
        $resultorder=$query->row_array();
        if(count($resultorder)>0)
        {
            $select_goods=$selectgoods;
            $this->db->select($select_goods);
            $this->db->where(array('order_id'=>$resultorder['order_id']));
            $query=$this->db->get('wx_order_goods');
            $row=$query->result_array();
            $resultorder['goods']=$row;
            foreach($resultorder['goods'] as $k=>$v)
            {
                $resultorder['goods'][$k]=array_merge($resultorder['goods'][$k],$this->get_car_goods_image($v['goods_id']));

                $resultorder['goods'][$k]['goods_url']="/goodsforcar/goods_detail?goods_id=$v[goods_id]";
            }
            if($resultorder['order_status']==0 )
            {
                $resultorder['order_state']='未付款';
            }
            elseif($resultorder['order_status']==1)
            {
                $resultorder['order_state']='已付款';
            }
            elseif($resultorder['order_status']==2)
            {
                $resultorder['order_state']='现金交易';
            }
            elseif($resultorder['order_status']==3)
            {
                $resultorder['order_state']='交易完成';
            }else{
                $resultorder['order_state']='交易关闭';
            }
            $resultorder['add_date']=date('Y-m-d',$resultorder['add_time']);
            $resultorder['show_for_guide']=base_url("caradmin/show_detail?order_id=$resultorder[order_id]");
        }
        return $resultorder;
    }
    public function get_order_detail_xj($order_id)
    {
        $where="order_id=$order_id OR order_sn=$order_id";
  
        $this->db->select($select);
        $this->db->where($where);
        $query=$this->db->get('v_order_info');
        $resultorder=$query->row_array();
      
        return $resultorder;
    }


//获取 车用产品图  7wx车卖 长方形图；8微信车卖正方形图
    public function get_car_goods_image($goods_id)
    {
        $where="link_id=$goods_id AND type IN (7,8)";
        $this->db->select('url,type');

        $this->db->where($where);
        $db=$this->db->get('v_images');
        $rs= $db->result_array();
        $rs[0]['type']==7?$arr=array('image_r'=>$rs[0]['url'],'image_s'=>$rs[1]['url']):$arr=array('image_r'=>$rs[1]['url'],'image_s'=>$rs[0]['url']);
        return $arr;
    }


    public function get_wxuser_info($where='1=1')
    {
        $this->db->select('*');
        $this->db->where($where);
        $db=$this->db->get('v_wx_users');
        $rs=$db->row_array();
        if(isset($rs['hash_id']) && $rs['hash_id']==0)
        {
            $this->update_one($where,array('hash_id'=>md5($rs['openid'])),'v_wx_users');
        }
        return $rs;
    }

    public function get_wxuser_all($where='1=1')
    {
        $this->db->select('*');
        $this->db->where($where);
        $db=$this->db->get('v_wx_users');
        $rs= $db->result_array();
        return $rs;
    }
    //简单分页
 public  function get_goods_lists($where,$table,$start, $page_num){
         $this->db->select('*');
        $this->db->where($where);
   $query=$this->db->get($table,$page_num,$start);
        $result = $query->result_array();
        if(count($result)>0)
        {
            foreach($result as $k=>$row)
            {
                if(isset($row['image']))
                {
                    if (stristr($row['image'], 'http') === false)
                    {
                        $result[$k]['image'] = $this->config->item('base_url') . ltrim($row['image'], '.');
                    }
                }
            }

            return $result;
        }
        else
        {
            return 0;
        }
    }

    public  function user_insert($table='',$data=array())
    {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
    }

    public function update_one($where,$data=array(),$table='v_activity')
    {
        $this->db->where($where);
        return $this->db->update($table,$data);
    }


}