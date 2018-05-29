<?php
/**
 * 公共类
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
    	//session
    	$this->load->library('session');
		$this->load->model('Admin_model');
	}

	/**
	 * 判断管理员对某一个操作是否有权限。
	 *
	 * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
	 * @param     string    $priv_str    操作对应的priv_str
	 * @param     string    $msg_type       返回的类型
	 * @return true/false
	 */
	public function admin_priv($priv_str, $msg_type = '' , $msg_output = true)
	{
	    if ($_SESSION['action_list'] == 'all')
	    {
	        return true;
	    }

	    if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false)
	    {
	        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
	        if ( $msg_output)
	        {
	            sys_msg($_LANG['priv_error'], 0, $link);
	        }
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

	/**
	 * 记录管理员的操作内容
	 *
	 * @access  public
	 * @param   string      $sn         数据的唯一值
	 * @param   string      $action     操作的类型
	 * @param   string      $content    操作的内容
	 * @return  void
	 */
	function admin_log($sn = '', $action, $content)
	{
		//加载方式二
	    //$log_info = $GLOBALS['_LANG']['log_action'][$action] . $GLOBALS['_LANG']['log_action'][$content] .': '. addslashes($sn);
	    $log_info = addslashes($action) .':'.addslashes($sn) . $content;
	    $data= array(
	    			'log_time' => time(),
	    			'user_id'  => $_SESSION['admin_id'],
	    			'log_info' => $log_info,
	    			'ip_address'=> $this->real_ip()
	    	);
	    $this->Admin_model->add_logs($data);
	}

	/**
	 * 获得用户的真实IP地址
	 *
	 * @access  public
	 * @return  string
	 */
	function real_ip()
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




}
