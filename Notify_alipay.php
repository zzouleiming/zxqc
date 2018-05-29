<?php
/* *
支付宝回调
 */
// 订单控制器类
class Notify_alipay extends CI_Controller{
  public function __construct()
  {
    parent::__construct();
	$this->load->model('User_Api_model');
    $this->load->model('Admin_model');
    // 加载支付宝配置
    $this->config->load('alipay', TRUE);
    $this->config->load('alipay_new', TRUE);
  }
  // $method参数只能是'return'或'notify'，对应URL
  public function callback ($method) {
    // 加载支付宝返回通知类库
    require_once("/opt/nginx/html/zxqc/api/application/third_party/alipay/alipay_notify.class.php");
    // 初始化支付宝返回通知类
    $alipayNotify = new AlipayNotify($this->config->item('alipay'));
    // 20170216 兼容支付宝新账号验证
    $alipayNotifyNew = new AlipayNotify($this->config->item('alipay_new'));

    $input = array();
    $is_ajax = FALSE;
    $notify_status = 'success';

    // 这里做同步还是异步的判断并获取返回数据验证请求
    switch ($method) {
      case 'notify':
        $result = $alipayNotify->verifyNotify();
		//file_put_contents('/opt/nginx/html/zxqc/log.txt','result='.$result.PHP_EOL,FILE_APPEND);
        // 20170216 兼容支付宝新账号验证
        if(!$result)
        {
        	$result = $alipayNotifyNew->verifyNotify();
		//file_put_contents('/opt/nginx/html/zxqc/log.txt','result_new='.$result.PHP_EOL,FILE_APPEND);
        }
        $input = $this->input->post();
        $is_ajax = TRUE;
        break;

      case 'return':
        $result = $alipayNotify->verifyReturn();
        // 20170216 兼容支付宝新账号验证
        if(!$result)
        {
        	$result = $alipayNotifyNew->verifyReturn();
        }
        $input = $this->input->get();
        break;
      
      default:
        return $this->out_not_found();
        break;
    }

      $input_str=json_encode($input);
      file_put_contents('/opt/nginx/html/zxqc/api/logfile/alipay_notify.log','post='.$input_str.PHP_EOL,FILE_APPEND);
    // 支付宝返回支付成功和交易结束标志
    if ($result && ($input['trade_status'] == 'TRADE_FINISHED' || $input['trade_status'] == 'TRADE_SUCCESS')) {


		//客户端
		//商户订单号
		$out_trade_no = $input['out_trade_no'];
		//支付宝交易号
		$trade_no     = $input['trade_no'];
		//交易状态
		$trade_status = $input['trade_status'];
		//交易金额
		$total_fee    = $input['total_fee'];

		/* 检查支付的金额是否相符 */
		if (!$this->check_money($out_trade_no , $total_fee))
		{
			//return false;
			echo "fail";     //反馈给支付宝的参数，切勿删除，否则支付宝服务器会不断重发通知，直到超过24小时22分钟。	
			return false;
		}				
		if($trade_status == 'TRADE_FINISHED')   //TRADE_SUCCESS与TRADE_FINISHED是有本质区别的，不懂请百度。
		{
			$this->order_paid($out_trade_no);
			//return true;
			echo "success";  //反馈给支付宝的参数，切勿删除，否则支付宝服务器会不断重发通知，直到超过24小时22分钟。
		}
		else if ($trade_status == 'TRADE_SUCCESS')
		{
			$this->order_paid($out_trade_no, 2,'',$trade_no);
			//return true;
			echo "success";  //反馈给支付宝的参数，切勿删除，否则支付宝服务器会不断重发通知，直到超过24小时22分钟。
			
		}else
		{
			//return false;
			echo "fail";     //反馈给支付宝的参数，切勿删除，否则支付宝服务器会不断重发通知，直到超过24小时22分钟。	
		}
		
    } else {
      // 否则置状态为失败
      //$notify_status = 'fail';
	  echo 'fail';
    }

    //if ($is_ajax) {
    //  // 异步方式调用模板输出状态
    //  $this->view->load('alipay', array('status' => $notify_status));
    //} else {
    //  // 同步方式跳转到订单详情控制器，redirect方法要你自己写
    //  return $this->redirect("order/view/$id#status:$notify_status");
    //}
  }
    /**
     * 检查支付的金额是否相符
	**/
    function check_money($out_trade_no,$total_fee)
    {
		if(is_numeric($out_trade_no))
		{
			$res = $this->User_Api_model->comment_select(' order_amount,goods_amount,front_amount,`from`,pid'," order_id=$out_trade_no ",'','',0,1,'v_order_info');
			if($res)
			{
				$amount = $res[0]['order_amount'];
				$goods_amount = $res[0]['goods_amount'];
                $front_amount = floatval($res[0]['front_amount']);
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
        if(($from=='0' OR $from=='5' OR $from=='11' OR $from=='3') AND $total_fee == $goods_amount )
        {
            return true;
        }
        elseif( $from=='6'  AND $total_fee == $front_amount )
        {
            return true;
        }
        elseif($from=='7'  AND  $total_fee == $goods_amount AND $pid=='0')
        {
            return true;
        }
        elseif($from=='7' AND  $total_fee == (floatval($goods_amount)-$front_amount) AND $pid!='0')
        {
            return true;
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
	}
    /**
     * 更新订单状态
	**/
    function order_paid($out_trade_no, $pay_status = 1, $note = '',$trade_no = '')
    {
		/* 取得支付编号 */
		$order_id = intval($out_trade_no);
		/* 获取订单信息 */
		$res = $this->User_Api_model->comment_select(' order_id,order_status,pay_status,user_id_sell,order_amount,pid,`from`,goods_amount,front_amount '," order_id=$order_id ",'','',0,1,'v_order_info');
		if($res)
		{
			if($res[0]['order_status']=='0' && $res[0]['pay_status'] == '0')
			{
				$data = $res[0];
				$data['trade_no'] = $trade_no;
				$data['pay_name'] = 'alipay';
				$data['pay_id'] = 1;
				/* 更新订单状态 */
        		$this->User_Api_model->update_order_paid($data);

				/* 更新订单状态 */
				/*$param = array(
							'order_status' => '1',
							'confirm_time' => time(),
							'pay_status'   => 1,
							'pay_time'     => time(),
							'pay_id'       => 1,
							'pay_name'     => 'alipay',
							'trade_no'     => $trade_no
				);
				$this->User_Api_model->comment_update(" order_id=$order_id ",$param,'v_order_info');*/
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

            /*if($res[0]['pid']!='0')
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