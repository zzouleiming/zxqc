<?php
/* *
支付宝回调
 */
// 订单控制器类
class Notify_wxnew extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
	  $this->load->model('Goodsforcar_model');
	  // 加载微信类库
	  $this->load->library('Wxauth');
	  $this->load->helper('url');
  }
  
  public function callback () {

	  set_time_limit(0);
    //使用通用通知接口
	//$notify = new Notify_pub();

	//存储微信的回调
	  $xml= file_get_contents("php://input");
	  //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	  $this->wxauth->saveData($xml);

	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
	if($this->wxauth->checkSign() == FALSE){
		$this->wxauth->setReturnParameter("return_code","FAIL");//返回状态码
		$this->wxauth->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$this->wxauth->setReturnParameter("return_code","SUCCESS");//设置返回码



	}
	$returnXml =  $this->wxauth->returnXml();

	echo $returnXml;
  	//$this->Goodsforcar_model->user_insert($table='v_ts_istype',$data=array('type_name'=>json_encode($returnXml)));
	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	
	//以log文件形式记录回调信息
	$log_name="./notify_url.log";//log文件路径
	 // $this->wxauth->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");

	if( $this->wxauth->checkSign() == TRUE)
	{
		if ( $this->wxauth->data["return_code"] == "FAIL") {
			//此处应该更新一下订单状态，商户自行增删操作
			$this->wxauth->log_result($log_name,"【通信出错】:\n".$xml."\n");
		}
		elseif( $this->wxauth->data["result_code"] == "FAIL"){
			//此处应该更新一下订单状态，商户自行增删操作
			$this->wxauth->log_result($log_name,"【业务出错】:\n".$xml."\n");
		}
		else{
			//此处应该更新一下订单状态，商户自行增删操作
		//	$this->wxauth->log_result($log_name,"【支付成功】:\n".$returnXml."\n");
			$order =  $this->wxauth->getData();
			$out_trade_no = $order["out_trade_no"];
			$total_fee    = $order["total_fee"];
			$trade_no = $order["transaction_id"];
			//$this->wxauth->log_result($log_name,"【val】:\n".$out_trade_no."\n".$total_fee."\n".$trade_no."\n");
			//$this->Goodsforcar_model->user_insert($table='v_ts_istype',$data=array('type_name'=>json_encode($order)));
			$bool=$this->check_money($out_trade_no,$total_fee);
			//$this->wxauth->log_result($log_name,"【bool】:\n".$bool."\n");
			if($bool==TRUE)
			{
				$this->order_paid($out_trade_no,$trade_no);
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
   public function check_money($out_trade_no,$total_fee)
    {

		//$this->wxauth->log_result("./notify_url.log","【out_trade_no】:\n".$out_trade_no."\n");

		$rs=$this->Goodsforcar_model->get_order_detail($out_trade_no);

		//$this->wxauth->log_result("./notify_url.log","【order_detail】:\n".json_encode($rs)."\n");

		if(count($rs)>0)
		{
			$amount = floatval($rs['order_amount']) * 100;
			//$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('type_name'=>$amount.'--'.$total_fee));

			if($total_fee==$amount)
			{
				//$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('type_name'=>json_encode($amount)));
				return true;
			}
		}
		else
		{
			return false;
		}

	}
    /**
     * 更新订单状态
	**/
   public function order_paid($out_trade_no,$trade_no = '')
    {
		/* 取得支付编号 */
		/* 获取订单信息 */
		$rs=$this->Goodsforcar_model->get_order_detail($out_trade_no);
		//$this->wxauth->log_result("./notify_url.log","【rs】:\n".json_encode($rs)."\n");
	//	$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('type_name'=>json_encode(22)));
		//$log_->log_result($log_name,"【订单信息】:\n".$res."\n");
		if($rs)
		{
			//$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('user_id'=>1,'type_name'=>json_encode($rs)));
			if($rs['order_status']=='0' && $rs['pay_status'] == '0')
			{

				//$data = $rs;
				$data['trade_no'] = $trade_no;
				$data['pay_id'] = '2';
				$data['order_status']=1;
				$data['pay_time']=time();
				/* 更新订单状态 */
				//$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('user_id'=>2,'type_name'=>json_encode($data)));

				$this->Goodsforcar_model->update_one(array('order_id'=>$rs['order_id']),$data,'wx_order_info');
				//$str=$this->db->last_query();
				//$this->Goodsforcar_model->user_insert($table='v_ts_istype',array('user_id'=>3,'type_name'=>$str));
			}

		}
	}

	public function test()
	{
		$data=array(
				"trade_no"=>"4002202001201703295067483222",
    		"pay_id"=> "2",
    "order_status"=> 1,
    "pay_time"=> 1490776930
		);

		$this->Goodsforcar_model->update_one(array('order_id'=>2722),$data,'wx_order_info');
	  echo $this->db->last_query();
	}
}