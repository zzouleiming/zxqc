<?php
/* *
支付宝回调
 */
// 订单控制器类
class Notify_wxpay extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
	$this->load->model('User_Api_model');
	$this->load->model('User_model');
    $this->load->model('Admin_model');
  }
  
  public function callback ($new='') {
    // 加载微信类库
    if(empty($new))
    {
    	include_once("/opt/nginx/html/zxqc/api/application/third_party/wxpay/WxPay.php");
    }
    else
    {
    	include_once("/opt/nginx/html/zxqc/api/application/third_party/wxpay/WxPayNew.php");
    }
    
    //使用通用通知接口
	$notify = new Notify_pub();

	//存储微信的回调
	  $xml= file_get_contents("php://input");
	  //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	$notify->saveData($xml);
	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
	if($notify->checkSign() == FALSE){
		$notify->setReturnParameter("return_code","FAIL");//返回状态码
		$notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码



	}
	$returnXml = $notify->returnXml();
	echo $returnXml;
	
	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	
	//以log文件形式记录回调信息
	$log_ = new Log_();
	$log_name="./notify_url.log";//log文件路径
	$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");

	if($notify->checkSign() == TRUE)
	{
		if ($notify->data["return_code"] == "FAIL") {
			//此处应该更新一下订单状态，商户自行增删操作
			$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
		}
		elseif($notify->data["result_code"] == "FAIL"){
			//此处应该更新一下订单状态，商户自行增删操作
			$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
		}
		else{
			//此处应该更新一下订单状态，商户自行增删操作
			$log_->log_result($log_name,"【支付成功】:\n".$returnXml."\n");
			$order = $notify->getData();
			$out_trade_no = $order["out_trade_no"];
			$total_fee    = $order["total_fee"];
			$trade_no = $order["transaction_id"];
			if ($this->check_money($out_trade_no , $total_fee))
			{
				//$this->User_model->user_insert($table='v_ts_istype',$data=array('user_id'=>'333'));
				$this->order_paid($out_trade_no, 2,'',$trade_no);
			}
		}
		
		//商户自行增加处理流程,
		//例如：更新订单状态
		//例如：数据库操作
		//例如：推送支付完成信息
	}
  }
  
    /**
     * 检查支付的金额是否相符
	**/
    function check_money($out_trade_no,$total_fee)
    {
		if(is_numeric($out_trade_no))
		{
			$res = $this->User_Api_model->comment_select(' order_amount,goods_amount,front_amount,`from`,pid '," order_sn='$out_trade_no' ",'','',0,1,'v_order_info');
			if($res)
			{
				$amount = floatval($res[0]['order_amount']) * 100;
				$goods_amount = floatval($res[0]['goods_amount']) * 100;
				$front_amount = floatval($res[0]['front_amount']) * 100;
                $from=$res[0]['from'];
                $pid=$res[0]['pid'];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		/*if ($total_fee == $amount || $total_fee == $goods_amount || ($total_fee==$front_amount && ($from=='8' || $from=='6')))
		{
			return true;
		}
		else
		{
			return false;
		}*/
        if(($from=='0' OR $from=='5' OR $from=='11' OR $from=='12' OR $from=='3'OR $from=='2') AND $total_fee == $goods_amount )
        {
            return true;
        }
        elseif( ($from=='6' OR $from=='8')  AND $total_fee == $front_amount )
        {
            return true;
        }
        elseif(($from=='7' OR  $from=='9') AND  $total_fee == $goods_amount AND $pid=='0')
        {
            return true;
        }
        elseif(($from=='7' OR  $from=='9') AND  $total_fee == ($goods_amount-$front_amount) AND $pid!='0')
        {
            return true;
        }
        else
        {
            return false;
        }
	}
    /**
     * 更新订单状态
	**/
    function order_paid($out_trade_no, $pay_status = 1, $note = '',$trade_no = '')
    {
		/* 取得支付编号 */
		$order_sn = intval($out_trade_no);
		/* 获取订单信息 */
		$res = $this->User_Api_model->comment_select(' order_id,order_status,pay_status,user_id_sell,order_amount,pid,`from`,goods_amount,front_amount '," order_sn='$order_sn' ",'','',0,1,'v_order_info');
		//$log_->log_result($log_name,"【订单信息】:\n".$res."\n");
		if($res)
		{
			if($res[0]['order_status']=='0' && $res[0]['pay_status'] == '0')
			{
				$data = $res[0];
				$data['trade_no'] = $trade_no;
				$data['pay_name'] = 'wxpay';
				$data['pay_id'] = 2;
				//$this->User_model->user_insert($table='v_ts_istype',$data=array('user_id'=>'123'));
				/* 更新订单状态 */
        		$this->User_Api_model->update_order_paid($data);
				/* 更新订单状态 */
				/*$param = array(
							'order_status' => '1',
							'confirm_time' => time(),
							'pay_status'   => 1,
							'pay_time'     => time(),
							'pay_id'       => 2,
							'pay_name'     => 'wxpay',
							'trade_no'     => $trade_no
				);
				$this->User_Api_model->comment_update(" order_sn=$order_sn ",$param,'v_order_info');*/
				/* 取得订单商品 */
				//$order_goods = $this->User_Api_model->comment_select(" order_id,goods_id,goods_number "," order_id=$order_id ",'','',0,10,'v_order_goods');
				//if($order_goods)
				//{
				//	foreach($order_goods as $value)
				//	{
				//		/* 更新商品库存信息 */
				//		$this->User_Api_model->update_goods($value['goods_id'],$value['goods_number']);
				//	}
				//}
				/* 更新买家提现金额 */
				//$this->User_Api_model->update_amount($res[0]['user_id_sell'],$res[0]['order_amount']);
			}
            //旅拍产品 尾款付完 更新定金订单为不显示
           /* if($res[0]['pid']!='0')
            {
                $order_pid=$res[0]['pid'];
                $param = array(
                    'order_status' => '-2',
                );
                $this->User_Api_model->comment_update(" order_id=$order_pid ",$param,'v_order_info');

            }*/
		}
	}
}