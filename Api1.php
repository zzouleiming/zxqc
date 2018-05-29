<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL);
class Api1 extends My_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table = 'v_users';
		$this->load->model('User_Api_model');
		$this->load->model('User_model');
		$this->load->model('Admin_model');
		$this->load->library('uploadimg');
		//$this->load->library('Imagick');
		$this->load->library('common');
		$this->load->library('image_lib');
		//$this->load->library('waf');
		$this->load->helper(array('form', 'url'));
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));



		// $this->load->controller('Api1');

		// $this->alipayh5();
		//签名
		//$this->sign1();
	}
	public function index()
	{
		echo pi();
		//echo bin2hex('1105143311');
		//echo $this->config->item('base_url');
		//变量可以在这里定义，或者来自配置文件，也可以去数据库中查
		// $variable = array('abc'=>'asdfasdf');
		//echo  $this->CI->load->vars('base_url');


		if ( !$foo = $this->cache->get('foo'))
		{
			echo 'Saving to the cache!<br />';
			$foo = 'welcome';

			// Save into the cache for 5 minutes
			$this->cache->save('foo', $foo, 5);
		}
		echo  $foo;

		//$this->cache->delete('foo');
		//		$this->output->cache("30");
		$i= 0;
		$data['url'][$i]['info'] = '获取推流地址';
		$data['url'][$i]['src']  = '/index.php/Api1/get_publish?user_id=2';
		$i++;
		$data['url'][$i]['info'] = '获取配置参数';
		$data['url'][$i]['src']  = '/index.php/Api1/get_config';
		$i++;
		$data['url'][$i]['info'] = '授权注册/登录   短信                          type=tel&mobile=手机号&code=验证码sys=1&device_id=设备id   sys 1   ios   2   android  3  windows   4 其他    ';
		$data['url'][$i]['src']  = '/index.php/api1/register1?type=weixin&openid=1222&headimgurl=http://wx.qlogo.cn/mmopen/LIUI5tJGiauB3z4rp4zYXCssMJGoP0SEhcgolZYqmVU4Mn2r6J1zbtCRJSxm8hhWiatt1e50jmvbS8P4g9iaxzMuA/0&nickname=cef&sex=1&sys=1&device_id=wfawfawfawfawfawefawfawefa';
		$i++;
		$data['url'][$i]['info'] = '短信验证码获取';
		$data['url'][$i]['src']  = '/index.php/api1/message?mobile=13556121245';
		$i++;

		$data['url'][$i]['info'] = '直播开始';
		$data['url'][$i]['src']  = '/index.php/api1/show_start?user_id=166&title=jaijfa&video_name=166_1460069722&adv_id=111';
		$i++;
		$data['url'][$i]['info'] = '直播结束  用户id     视屏id';
		$data['url'][$i]['src']  = '/index.php/api1/show_stop?user_id=4&video_id=8&praise=12&count=2&creates=22';
		$i++;
		$data['url'][$i]['info'] = '一般注册';
		$data['url'][$i]['src']  = '/index.php/api1/common_register?user_name=dw&password=123456&confirm_password=123456';
		$i++;
		$data['url'][$i]['info'] = '一般登录';
		$data['url'][$i]['src']  = '/index.php/api1/common_login?user_name=dw&password=123456';
		$i++;
		/*$data['url'][$i]['info'] = '点赞';
		$data['url'][$i]['src']  = '/index.php/api1/praise?praise_id=1';
		$i++;*/
		$data['url'][$i]['info'] = '增加预播';
		$data['url'][$i]['src']  = '/api/advance_video?title=123&time=222&user_id=23&image=111';
		$i++;
		$data['url'][$i]['info'] = '预播列表';
		$data['url'][$i]['src']  = '/api/advance_list?user_id=2';
		$i++;
		$data['url'][$i]['info'] = '删除预播';
		$data['url'][$i]['src']  = '/index.php/api1/advance_del?adv_id=33';
		$i++;
		$data['url'][$i]['info'] = '个人信息';
		$data['url'][$i]['src']  = '/index.php/api1/user_info?user_id=2';
		$i++;
		$data['url'][$i]['info'] = '上传头像  (图片要用file传  参数名为files)';
		$data['url'][$i]['src']  = '/index.php/api1/avatar_upload?user_id=1&files=dizhi';
		$i++;
		$data['url'][$i]['info'] = '修改昵称';
		$data['url'][$i]['src']  = '/index.php/api1/update_user?user_id=1&user_name=大脑袋兔兔';
		$i++;
		$data['url'][$i]['info'] = '修改性别';
		$data['url'][$i]['src']  = '/index.php/api1/update_user?user_id=1&sex=男';
		$i++;
		$data['url'][$i]['info'] = '修改个性签名';
		$data['url'][$i]['src']  = '/index.php/api1/update_user?user_id=1&pre_sign=大脑袋兔兔 个性签名';
		$i++;
		$data['url'][$i]['info'] = '举报';
		$data['url'][$i]['src']  = '/index.php/api1/report?user_id=1';
		$i++;
		$data['url'][$i]['info'] = '拉黑';
		$data['url'][$i]['src']  = '/index.php/api1/defriend_add?user_id=1&defriend=2';
		$i++;
		$data['url'][$i]['info'] = '拉黑列表';
		$data['url'][$i]['src']  = '/index.php/api1/defriend_list?user_id=1&page=1';
		$i++;
		$data['url'][$i]['info'] = '取消拉黑';
		$data['url'][$i]['src']  = '/index.php/api1/defriend_del?user_id=1&defriend=2';
		$i++;
		$data['url'][$i]['info'] = '发送私信';
		$data['url'][$i]['src']  = '/index.php/api1/letter_add?to=1&from=2&content=%E6%B5%8B%E8%AF%95';
		$i++;
		$data['url'][$i]['info'] = '私信列表';
		$data['url'][$i]['src']  = '/index.php/api1/letter_list?user_id=1&page=1';
		$i++;
		$data['url'][$i]['info'] = '私信详细';
		$data['url'][$i]['src']  = '/index.php/api1/letter_info?to=1&from=2';
		$i++;
		$data['url'][$i]['info'] = '删除私信';
		$data['url'][$i]['src']  = '/index.php/api1/letter_del?to=1&from=2';
		$i++;
		$data['url'][$i]['info'] = '禁言';
		$data['url'][$i]['src'] = '/index.php/api1/gag_add?video_id=5&gag_id=2';
		$i++;
		$data['url'][$i]['info'] = '取消禁言';
		$data['url'][$i]['src']  = '/index.php/api1/gag_del?video_id=5&gag_id=2';
		$i++;
		$data['url'][$i]['info'] = '是否禁言';
		$data['url'][$i]['src']  = '/index.php/api1/is_gag?video_id=5&gag_id=2';
		$i++;
		$data['url'][$i]['info'] = '加关注';
		$data['url'][$i]['src']  = '/index.php/api1/follow_add?fans_id=5&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '关注列表  0 接收推送   1  不接受';
		$data['url'][$i]['src']  = '/index.php/api1/follow_list?user_id=2&page=1';
		$i++;
		$data['url'][$i]['info'] = '取消关注列表 ';
		$data['url'][$i]['src']  = '/index.php/api1/follow_del?fans_id=5&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '判断用户是否关注主播 fans_id  是用户id    user_id 主播id   video_id 视屏id';
		$data['url'][$i]['src']  = '/index.php/api1/is_follow?fans_id=5&user_id=2&video_id=5';
		$i++;
		$data['url'][$i]['info'] = '加粉';
		$data['url'][$i]['src']  = '/index.php/api1/fans_add?fans_id=5&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '粉丝列表  ';
		$data['url'][$i]['src']  = '/index.php/api1/fans_list?user_id=2&page=1';
		$i++;
		$data['url'][$i]['info'] = '取消关注粉 ';
		$data['url'][$i]['src']  = '/index.php/api1/fans_del?fans_id=5&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '判断用户是否粉主播 fans_id  是用户id    user_id 主播id ';
		$data['url'][$i]['src']  = '/index.php/api1/is_fans?fans_id=5&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '视屏列表  (type new  最新     hot 热门)';
		$data['url'][$i]['src']  = '/index.php/api1/video_list?type=hot';
		$i++;
		$data['url'][$i]['info'] = '关注视屏列表';
		$data['url'][$i]['src']  = '/index.php/api1/fans_video?user_id=2';
		$i++;
		$data['url'][$i]['info'] = '观看视屏';
		$data['url'][$i]['src']  = '/index.php/api1/watch_start?video_id=2&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '停止观看视屏';
		$data['url'][$i]['src']  = '/index.php/api1/watch_stop?video_id=2&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '正在观看人数和观看过的人数(10秒钟请求一次)';
		$data['url'][$i]['src']  = '/index.php/api1/watch_num?video_id=2';
		$i++;
		$data['url'][$i]['info'] = '排行标题';
		$data['url'][$i]['src']  = '/index.php/api1/rank_info';
		$i++;
		$data['url'][$i]['info'] = '排行榜 (1 时 2 日 3 周 4 月  5  年)';
		$data['url'][$i]['src']  = '/index.php/api1/rank_list?type=5&user_id=2&page=1';
		$i++;
		$data['url'][$i]['info'] = '推送测试';
		$data['url'][$i]['src']  = '/index.php/api1/pushinfo';
		$i++;
		$data['url'][$i]['info'] = '推送列表';
		$data['url'][$i]['src']  = '/index.php/api1/pushlist';
		$i++;
		$data['url'][$i]['info'] = '关闭所有关注者推送';
		$data['url'][$i]['src']  = '/index.php/api1/close_push?user_id=2&type=all';
		$i++;
		$data['url'][$i]['info'] = '关闭单个关注者推送';
		$data['url'][$i]['src']  = '/index.php/api1/close_push?user_id=2&close_id=3';
		$i++;
		$data['url'][$i]['info'] = '开启所有关注者推送';
		$data['url'][$i]['src']  = '/index.php/api1/open_push?user_id=2&type=all';
		$i++;
		$data['url'][$i]['info'] = '开启单个关注者推送';
		$data['url'][$i]['src']  = '/index.php/api1/open_push?user_id=2&close_id=3';
		$i++;
		$data['url'][$i]['info'] = '推流查看地址';
		$data['url'][$i]['src']  = 'http://42.121.193.231:8080/stat';
		$i++;
		$data['url'][$i]['info'] = '关闭流';
		$data['url'][$i]['src']  = 'http://42.121.193.231:8080/control/drop/publisher?app=hls&name=';
		/*$i++;
		$data['url'][$i]['info'] = '添加测试数据   t=v_rank_day 时   t=v_rank_month 月  t=v_rank_year 年';
		$data['url'][$i]['src']  = '/index.php/api1/test_sql?t=v_rank_day';*/
		$i++;
		$data['url'][$i]['info'] = '搜索';
		$data['url'][$i]['src']  = '/index.php/api1/search?keyword=美女';
		$i++;
		$data['url'][$i]['info'] = '直播添加商品';
		$data['url'][$i]['src']  = '/index.php/api1/add_goods?user_id=2&video_id=3&goods_name=iphone6&price=5888&goods_number=6';
		$i++;
		$data['url'][$i]['info'] = '直播查看商品';
		$data['url'][$i]['src']  = '/index.php/api1/video_goods?video_id=3';
		$i++;
		$data['url'][$i]['info'] = '提交订单';
		$data['url'][$i]['src']  = '/index.php/api1/add_order?video_id=3&user_id=5&goods_id=3&goods_number=2&consignee=dw&address=shanghai&mobile=13112345678';
		$i++;
		$data['url'][$i]['info'] = '当地人信息';
		$data['url'][$i]['src']  = '/index.php/api1/local_info?user_id=830&type=guide&page=1';
		$i++;
		$data['url'][$i]['info'] = '添加个人地址';
		$data['url'][$i]['src']  = '/index.php/api1/add_address?user_id=830&consignee=abc&zipcode=230011&mobile=13112345678&area=shanghai&address=lvzhouzhonghuan';
		$i++;
		$data['url'][$i]['info'] = '获取个人地址';
		$data['url'][$i]['src']  = '/index.php/api1/get_address?user_id=830';
		$i++;
		$data['url'][$i]['info'] = '获取banner信息';
		$data['url'][$i]['src']  = '/index.php/api1/get_banner';
		$i++;
		$data['url'][$i]['info'] = '翻译';
		$data['url'][$i]['src']  = '/index.php/api1/translate?from=auto&to=en&content=你好';
		$i++;
		$data['url'][$i]['info'] = '我的评价';
		$data['url'][$i]['src']  = '/index.php/api1/my_evaluate?user_id=1&page=1';
		$i++;
		$data['url'][$i]['info'] = '签到';
		$data['url'][$i]['src']  = '/index.php/api1/checkin?user_id=1';
		$i++;
		$data['url'][$i]['info'] = '我的认证';
		$data['url'][$i]['src']  = '/index.php/api1/my_auth?user_id=1';
		$i++;
		$data['url'][$i]['info'] = '删除个人录播';
		$data['url'][$i]['src']  = '/index.php/api1/del_video?user_id=1&video_id=2';
		$i++;
		$data['url'][$i]['info'] = '推荐用户列表';
		$data['url'][$i]['src']  = '/index.php/api1/recommend?user_id=1';
		$i++;
		$data['url'][$i]['info'] = '观看直播发表评论';
		$data['url'][$i]['src']  = '/index.php/api1/comment?user_id=1029&video_id=1&content=hahaha';
		$i++;
		$data['url'][$i]['info'] = '引导广告';
		$data['url'][$i]['src']  = '/index.php/api1/adv';
		$i++;
		$data['url'][$i]['info'] = '退出登录';
		$data['url'][$i]['src']  = '/index.php/api1/logout?user_id=830&device_id=0a39be938311aa1e2ced8c777e5b333e55a110c127517ec75b048bb29fc3787b';
		$i++;
		$data['url'][$i]['info'] = '获取地理位置信息';
		$data['url'][$i]['src']  = '/index.php/api1/get_position?user_id=1&dimension=12.111&longitude=231.125';
		$i++;
		$data['url'][$i]['info'] = '分享时榜';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=time';
		$i++;
		$data['url'][$i]['info'] = '分享日榜';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=day';
		$i++;
		$data['url'][$i]['info'] = '分享周榜';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=week';
		$i++;
		$data['url'][$i]['info'] = '分享月榜';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=month';
		$i++;
		$data['url'][$i]['info'] = '分享年榜';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=year';
		$i++;
		$data['url'][$i]['info'] = '分享视屏';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=video&video_id=222&user_id=2';
		$i++;
		$data['url'][$i]['info'] = '分享预播';
		$data['url'][$i]['src']  = $this->config->item('base_url').'index.php/index/share?type=advance&adv_id=6&user_id=1';
		$i++;
		$data['url'][$i]['info'] = '版本信息';
		$data['url'][$i]['src']  = '/index.php/api1/version';
		$i++;
		$data['url'][$i]['info'] = '用户反馈 user_id 当前用户id  content 内容' ;
		$data['url'][$i]['src']  = '/index.php/api1/response?user_id=1&content=意见建议';
		$i++;
		$data['url'][$i]['info'] = '公共参数';
		$data['url'][$i]['src']  = '/index.php/api1/com_public';
		$i++;
		$data['url'][$i]['info'] = '查询排行榜位置  type (1 时 2 日 3 周 4 月  5  年)';
		$data['url'][$i]['src']  = '/index.php/api1/rank_user?user_id=2&type=1';
		$i++;
		$data['url'][$i]['info'] = '返回引导页第三方信息接口';
		$data['url'][$i]['src']  = '/index.php/api1/thrid_party';
		$i++;
		$data['url'][$i]['info'] = '判断流是否存在 0  不存在  1  存在 ';
		$data['url'][$i]['src']  = '/index.php/api1/rtmp_status?video_id=1';
		$i++;
		$data['url'][$i]['info'] = '签名 appid 现在默认为appkey    sign 客户端自动生成值   timestamp 时间戳';
		$data['url'][$i]['src']  = '/index.php/api1/appid=appkey&sign=123456&timestamp=1458306639';
		$i++;
		$data['url'][$i]['info'] = '获取视频地址';
		$data['url'][$i]['src']  = '/index.php/api1/get_videourl?video_id=1';

		$this->load->view('api/api_info',$data);
	}

	/**
	 * [register 获取配置参数]
	 * @return [type] [description]
	*/
	public function get_config()
	{
		$data['banner_on'] = $this->config->item('banner_on');
		$this->data_back($data,'0X000','success');
	}
	
	/**
	 * [register 获取推流地址，端口号]
	 * @return [type] [description]
	*/
	public function get_publish()
	{
		$user_id  = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) :'';
		if(!$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$time = time();
		//$video_name = $user_id . '_' . $time;
		$video_name = $user_id;
		$auth_key = $this->get_auth($video_name);
		$data['video_name'] = $video_name;
		if($this->config->item('rtmp_flg') == 0)
		{
			$data['publish_url'] = 'rtmp://42.121.193.231/hls/'.$video_name;
		}
		elseif($this->config->item('rtmp_flg') == 1)
		{
			$data['publish_url'] = $this->config->item('publish_url').$video_name.'?auth_key='.$auth_key.'&vhost=video.etjourney.com';
		}
		elseif($this->config->item('rtmp_flg') == 2)
		{
			$data['publish_uc_url'] = $this->config->item('publish_uc_url').$video_name.'?record=true';
		}
		//cdn测试
		if($user_id=='1085')
		{
			$data['publish_url'] = 'rtmp://push.etjourney.com/live/'.$video_name;
		}

		$this->data_back($data,'0X000','success');

		
	}
	
	/**
	 * [register 鉴权签名]
	 * @return [type] [description]
	*/
	public function get_auth($video_name,$type='')
	{
		$result = '';
		if($video_name)
		{
			if($type)
			{
				$video_name .= $type;
			}
			//$end  = intval(substr($video_name,-10)) + 86400;
			$end  = time() + 86400;
			$para = $end . '-0-0-';
			$sign = md5('/etjourney/' . $video_name . '-' . $para .$this->config->item('cdn_key'));
			$result = $para.$sign;
		}
		return $result;
	}

	/**
	 * [register 一般注册 --请求接口]
	 * @return [type] [description]
	*/
	public function common_register()
	{
		$user_name = isset($_REQUEST['user_name']) ? htmlspecialchars(trim(urldecode($_REQUEST['user_name']))): '';
		$password = isset($_REQUEST['password']) ? htmlspecialchars(trim(urldecode($_REQUEST['password']))) : '';
		$confirm_password = isset($_REQUEST['confirm_password']) ? htmlspecialchars(trim(urldecode($_REQUEST['confirm_password']))) : '';
		$email    = isset($_REQUEST['email']) ? htmlspecialchars(trim(urldecode($_REQUEST['email']))) : '';
		$mobile_phone = isset($_REQUEST['mobile_phone']) ? htmlspecialchars(trim(urldecode($_REQUEST['mobile_phone']))) : '';
		if(empty($user_name) || empty($password) || empty($confirm_password))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if($password!=$confirm_password){
			$this->data_back('密码不一致，请重新输入','0X011','fail');
		}
		$where = array('user_name' => $user_name);
		$count = $this->User_Api_model->count_all($where,'v_users');
		if($count)
		{
			$this->data_back('该用户名已存在','0X011','fail');
		}
		$time = time();
		//根据IP获取地址
		$ip = $this->common->real_ip();
		$address = $this->common->GetIpLookup($ip);
		$openid = md5($password.$time);
		$param = array(
				'user_name' => $user_name,
				'password'  => md5($password),
				'openid'    => $openid,
				'image'     => $this->config->item('default_avatar'),
				'register_time' => $time,
				'login_time'    => $time,
				'sex'			=> '2',
				'address'		=> $address,
				'regist_type'	=> '6'
				);
		$data['user_id'] = $this->User_Api_model->insert_string($param,'v_users');
		$data['openid'] = $openid;
		
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? intval($_REQUEST['sys']) :4;
			$count = $this->User_model->get_count(" device_id = '$device_id'",'v_device');
			if(empty($count['count']))
			{
				$device = array(
				'user_id'   => $data['user_id'],
				'device_id' => $device_id,
				'type'      => $sys);
				$this->User_Api_model->insert_string($device,'v_device');
			}
			else
			{
				$this->User_Api_model->comment_update(array('user_id'=>$data['user_id']),array('login'=>0),'v_device');
			}
		}
		$this->data_back($data,'0X000');
	}
	
	/**
	 * [register 一般登录 --请求接口]
	 * @return [type] [description]
	*/
	public function common_login()
	{
		$user_name = isset($_REQUEST['user_name']) ? htmlspecialchars(trim(urldecode($_REQUEST['user_name']))): '';
		$password = isset($_REQUEST['password']) ? htmlspecialchars(trim(urldecode($_REQUEST['password']))) : '';
		$language = isset($_REQUEST['language']) ? htmlspecialchars(trim(urldecode($_REQUEST['language']))) : '';
		if(empty($user_name) || empty($password))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data = array();
		//$where = array('user_name' => $user_name,'password' => md5($password),'regist_type' => '6');
		$userinfo = $this->User_Api_model->comment_select('user_id,password,openid'," user_name='$user_name' AND regist_type='6' ",'','',0,1,'v_users');
		if(empty($userinfo))
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back('用户不存在','0X011','fail');
				}elseif($language == 'zh_HK' || $language == 'zh_TW')
				{
					$this->data_back('用戶不存在','0X011','fail');
				}else{
					$this->data_back('User is not exists!','0X011','fail');
				}
			}else{
				$this->data_back('用户不存在','0X011','fail');
			}
		}
		else
		{
			if($userinfo[0]['password'] != md5($password))
			{
				if($language)
				{
					if($language == 'zh')
					{
						$this->data_back('密码错误','0X011','fail');
					}elseif($language == 'zh_HK' || $language == 'zh_TW')
					{
						$this->data_back('密碼錯誤','0X011','fail');
					}else{
						$this->data_back('Password error!','0X011','fail');
					}
				}else{
					$this->data_back('密码错误','0X011','fail');
				}
			}
		}
		$param['user_name'] = $user_name;
		$param['password']  = md5($password);
		$param['login_time'] = time();
		$data = $this->User_Api_model->login($param,'v_users','common');
		$data['openid'] = $userinfo[0]['openid'];
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? intval($_REQUEST['sys']) :4;
			$count = $this->User_model->get_count(" user_id=$data[user_id] AND device_id = '$device_id' ",'v_device');
			echo 'count='.$count['count'];
			if(empty($count['count']))
			{
				$device = array(
				'user_id'   => $data['user_id'],
				'device_id' => $device_id,
				'type'      => $sys);
				$this->User_Api_model->insert_string($device,'v_device');
			}
			else
			{
				$this->User_Api_model->comment_update(array('user_id'=>$data['user_id']),array('login'=>0),'v_device');
			}
			
		}
		$this->data_back($data,'0X000');
		
	}
	
	/**
	 * [register 授权注册 --请求接口]
	 * @return [type] [description]
	*/
	public function register()
	{	
		$type       = isset($_REQUEST['type']) ? trim($_REQUEST['type']) :'';
		$data  = array();
		if(empty($type))
		{
			$this->data_back('type为空.授权失败','0X001','fail');
		}
		if($type == 'weixin' || $type == 'qq' || $type == 'twitter' || $type == 'facebook' || $type == 'weibo')
		{
			$openid     = isset($_REQUEST['openid']) ? trim($_REQUEST['openid']) :'';
			$nickname   = isset($_REQUEST['nickname']) ? trim(addslashes($_REQUEST['nickname'])) :'';
			$headimgurl = isset($_REQUEST['headimgurl']) ? trim($_REQUEST['headimgurl']) :'';
			$sex        = isset($_REQUEST['sex']) ? intval(trim($_REQUEST['sex'])) :0;
			switch ($type) {
				case 'weixin':
					$regist_type = '1';
					break;
				case 'weibo':
					$regist_type = '2';
					break;
				case 'qq':
					$regist_type = '3';
					break;
				case 'facebook':
					$regist_type = '4';
					break;
				case 'twitter':
					$regist_type = '5';
					break;
				default:
					$regist_type = '';
					break;
				}
			if(!$openid)
			{
				$this->data_back('openid.为空授权失败','0X001','fail');
			}
			$where = array('openid'  => $openid,'regist_type' => $regist_type);
			if($sex == 2 || $sex==1)
			{
				$sex1 = '1';
			}
			else{ 
				$sex1 = '0';
			}
			$count = $this->User_Api_model->count_all($where,'v_users');
			//根据IP获取地址
			$ip = $this->common->real_ip();
			$address = $this->common->GetIpLookup($ip);
			if(empty($count))
			{
				//$nickname=$this->User_Api_model->repeat_user_name($nickname);
				$param = array(
						'account'   => $nickname,
						'user_name' => $nickname,
						'openid'    => $openid,
						'register_time' => time(),
						'login_time'    => time(),
						'image'			=> $headimgurl,
						'sex'			=> $sex1,
						'address'		=> $address,
						'regist_type'	=> $regist_type
						);
				$data['user_id'] = $this->User_Api_model->insert_string($param,'v_users');
			}
			else
			{	
				$param['openid'] = $openid;
				$param['address'] = $address;
				$param['login_time'] = time();
				$data = $this->User_Api_model->login($param,'v_users','weixin');
			}
			$data['openid'] = $openid;
			$data['base_url'] = $this->config->item('base_url');
		}
		elseif($type == 'tel')
		{
			//手机号码
			$mobile     = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) :0;
			if(!$mobile)
			{
				$this->data_back('手机号不能为空','0X005','fail');
			}
			if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$mobile))
			{   
    			$this->data_back('手机号格式不对','0X003','fail');
         	}
         	$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) :0;
         	if(empty($code))
			{
				$this->data_back('验证码不能为空','0X005','fail');
			}
			//5分钟验证码过期
			$time = time() - 300;
	    	$count = $this->User_Api_model->comment_count(' COUNT(*) AS count,login_time '," user_name='$mobile' AND froms='tel' AND openid='$code'", 'v_users');
	    	$param['user_name']     = $mobile;
			if(empty($count[0]['count']))
			{
				$this->data_back('验证码不正确','0X006','fail');
			}
			else{
				if($count[0]['login_time'] < $time)
				$this->data_back('验证码已过期','0X007','fail');
		
				$param['login_time'] = time();
				$data = $this->User_Api_model->login($param,'v_users','tel');
			}
		}
		if(!empty($data))
		{	//type 1   ios   2   android  3  windows   4 其他
			$device_id   = isset($_REQUEST['device_id']) ? trim(addslashes($_REQUEST['device_id'])) :'';
			$sys        = isset($_REQUEST['sys']) ? trim($_REQUEST['sys']) :4;
			$count = $this->User_model->get_count(" user_id=$data[user_id] AND device_id='$device_id' ",'v_device');
			if(empty($count['count']))
			{
				$device = array(
				'user_id'   => $data['user_id'],
				'device_id' => $device_id,
				'type'      => $sys);
				$this->User_Api_model->insert_string($device,'v_device');
			}
			else
			{
				$this->User_Api_model->comment_update(array('user_id'=>$data['user_id']),array('login'=>0),'v_device');
			}
			
		}
		$this->data_back($data,'0X000');
	}
	/**
	 * [show_start 直播开始]
	 * @url http://video.dhdyz.com/index.php/Api/show_start?user_id=5&title=jaijfa&adv_id=111
	 * @return [type] [description]
	*/
	public function show_start()
	{  
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';
		$title   = isset($_REQUEST['title'])   ? trim($_REQUEST['title'])     : '';
		$adv_id = isset($_REQUEST['adv_id']) ? intval($_REQUEST['adv_id']) : '';
		$act_id = isset($_REQUEST['act_id']) ? $_REQUEST['act_id'] : 0;
		$act_shop_id = isset($_REQUEST['act_shop_id']) ? intval($_REQUEST['act_shop_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		if(stristr($act_id,'p')){
			$act_shop_id=substr($act_id,1);
			$act_id=0;
		}
		//$location=$this->input->post_get('location',true);
		//if(!$location){
		//	$this->data_back('参数为空','0X011','fail');
		//}
        //$str="/^((-?(([1-9]?[0-9])|(1[0-7][0-9]))([.])?\d{3})|(180.000))[,]((-?(([1-8]?[0-9])|)([.])?\d{3})|(90,000))$/";
        //if(!preg_match($str,$location)){
        //    $this->data_back('经纬度有误','0x030','fail');
        //}
        $video_name   = isset($_REQUEST['video_name'])   ? trim($_REQUEST['video_name'])     : '';
		if(!$user_id || !$video_name)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//$user_info = $this->User_Api_model->user_info($user_id,'v_users');
		$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$user_id.'_user_info',$this->config->item('catch_time'));
		if(!$title)
		{
			$title = $user_info['user_name'] . '在直播';
		}
		$where['user_id'] = $user_id;
		$count = $this->User_Api_model->count_all($where,'v_follow');
		$address = '';
		$lct = explode(",",$location);
		$lat = $lct[0];
		$lng = $lct[1];
		if($location)
		{
			$lct = explode(",",$location);
			$lat = $lct[0];
			$lng = $lct[1];
			$position = $this->geocoder($lat,$lng);
			if($position)
			{
				$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
				$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
				$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
				if($city)
				{
					$address = $city;
				}elseif($province){
					$address = $province;
				}elseif($country){
					$address = $country;
				}
				$all_address=$country.$city;
			}else{
				$all_address='未知';
			}
		}else{
			$all_address='未知';
		}
		$param = array(
				'user_id'     => $user_id,
				'start_time'  => time(),
				'title'       => $title,
				'ip'          => $this->common->real_ip(),
				'location'	  => $location,
				'lat'	  => $lat,
				'lng'	  => $lng,
				'address'	  => $address,
				'all_address'	  => $all_address,
				'act_id'	  => $act_id,
				'act_shop_id'	  => $act_shop_id
				//'socket_info' => '42.121.5.3:2120'			
			);
			
		$ipinfo = $this->common->GetIpLookup($param['ip']);
		//是否有未关闭直播的信息
		$video_on = $this->User_Api_model->get_video_info($user_id,'v_video');
		if($video_on)
		{
			$this->User_Api_model->comment_update(array('user_id'=>$user_id,'is_off'=>0),array('is_off'=>1,'stop_time'=>time()),'v_video');
		}
		//推流模式
		if($this->config->item('rtmp_flg') == 2)
		{
			$param['push_type'] == 1;
		}
		//获取曾经直播过的信息
		//$show_time = $this->User_Api_model->get_video_count($user_id,'v_video');
		//if($show_time['count'] == 0)
		//{
		//	$info = 'insert';
		//}
		//else
		//{
		//	$info = 'update';
		//	$param['praise'] = 0;
		//}
		$video_id = $this->User_Api_model->start_info($user_id,$param,'v_video','insert');

		//var_dump($video_id);
		if($video_id)
		{
			if($adv_id)
			{
				$this->User_Api_model->update_watch(array('adv_id'=>$adv_id),array('video_id'=>$video_id),'v_video_advance');
			}
			//更新用户当前直播ID
			$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('video_id'=>$video_id),'v_users');
			//生成缩略图
			//$video_name = $video_id.'_'.time();
			$image = $this->config->item('base_dir').'/uploads/'.$video_id.'.jpg';
			$rtmp = $this->get_rtmp($video_name);
			$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 5 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
			exec($exec);
			$param['image'] = '/uploads/'.$video_id.'.jpg';
			$param['video_name'] = $video_name;
			//获取socket地址
			$socket = $this->get_socket($video_id,$user_id,$video_name);
			if($socket)
			{
				//$socket['socket_port']=2141;
				$param['socket_info'] = $socket['socket_ip'].':'.$socket['socket_port'];
			}
			$this->User_Api_model->start_info($user_id,$param,'v_video','update');
			$data['video_id'] = "$video_id";
			$data['count']    = "$count";
			$data['title']    = $title;
			$data['socket_ip']    = $socket['socket_ip'];
			$data['socket_port']  = $socket['socket_port'];
			$data['ipinfo']    = $ipinfo;

			$follow_list = $this->User_Api_model->comment_select('user_id'," fans_id=$user_id AND status='0' ",'','','','','v_follow',1);
			if(!empty($follow_list))
			{
				$sep = $user_list = '';
				foreach ($follow_list as $key => $value)
				{
					$user_list .= $sep . $value['user_id'];
					$sep = ',';
				}
				$device_list['user_name'] = $user_info['user_name'];
				$device_list['video_info'] = $this->getvideoinfo($user_id);
				$device_list['video_info']['rtmp'] = $rtmp;
				//$device_list['list']= $this->User_Api_model->comment_select(' device_id,type',"user_id IN ($user_list) ",'','','','','v_device',1);
				$device_list['list']= $this->User_Api_model->comment_select(' d.device_id,d.type',"d.login=0 AND d.user_id IN ($user_list)  ",'','','','','v_device AS d LEFT JOIN v_users AS u ON d.user_id=u.user_id',1);
				if(!empty($device_list))
				{
					$this->pushinfo($device_list,'show_start');
				}
			}				
			
			$this->data_back($data,'0X000','success');
		}
		else
		{
			$this->data_back('当前没有视屏','0X019','fail');
		}
	}

	/**
	 * [show_stop  直播结束]
	 * @url http://video.dhdyz.com/index.php/Api/show_stop?user_id=4&video_id=8
	 * @return [type] [description]
	*/
	public function show_stop()
	{

		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : '';
		//点赞数
		$praise = isset($_REQUEST['praise']) ? intval($_REQUEST['praise']) : 0;
		//分数
		$creates = isset($_REQUEST['creates']) ? intval($_REQUEST['creates']) : 0;
		//关注总数  从show_start 获取
		$follow_count = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 0;
		$time = time();
		$where['user_id'] = $user_id;
		//清除僵尸看客
		$this->User_Api_model->gag_del($data= array('watch'=>$video_id),$table = 'v_temp_users');
		$this->User_Api_model->update_video_zombie($video_id,'v_users');
		$count = $this->User_Api_model->count_all($where,'v_follow');
		$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id,'',2);
		if(empty($video_info))
		{
			$this->data_back('视频已结束','0X010','fail');
		}
		else
		{
			if($video_info['stop_time'] && $time - intval($video_info['stop_time']) > 60)
			{
				$this->data_back('视频已结束','0X010','fail');
			}
		}
		//关闭SOCKET
		if($video_id)
		{
			$this->User_Api_model->comment_update(array('video_id'=>$video_id),array('open_status'=>0,'video_id'=>0,'user_id'=>0,'start_time'=>0,'video_name'=>''),'v_socket');
			$cmd = "ps aux | grep '".$this->config->item('socket_server')." ".substr($video_info['socket_info'],-4)."' | grep -v grep | cut -c 9-15 | xargs kill -s 9";
			exec($cmd);
		}
		
		if($video_info['stop_time'] && $time - intval($video_info['stop_time']) > 15)
		{
			$creates_count = 0;
		}
		else
		{
			$creates_count = floor((($count- $follow_count)*2 + $creates + $video_info['views'])*0.02*($time-$video_info['start_time']));
		}
		//直播超过3分钟增加3积分
		$credits = $time-intval($video_info['start_time']) >= 180 ? 3 : 0;
		//参加活动增加积分
		if($video_info['act_id'])
		{
			$credits = ceil($credits * 1.5);
		}
		$user_data  =array(
			'praise'  => $praise,
			'creates' => $creates_count,
			'credits' => $credits);
		$this->User_Api_model->praise_creates_update($user_id,$user_data,'v_users');
		//如果有点赞  更新 用户表和日榜、月榜、年榜表
		if($creates_count)
		{
			
			$data1 = array(
				'user_id'  => $user_id,
				'score'   => $creates_count,
				'dateline' => $time
				);

			//更新日榜
			$rank_day_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m%d %H'")),'v_rank_day');
			if($rank_day_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
			}
			$data1['time'] = date('n',$time);
			$this->User_Api_model->rank_day($data1,'v_rank_day',$param);
			//更新月榜
			$rank_month_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m%d'")),'v_rank_month');
			if($rank_month_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
				//$data1['param'] = "'%Y%m%d'";

			}
			$data1['time'] = date('d',$time);
			$this->User_Api_model->rank_month($data1,'v_rank_month',$param);
			
			//更新年榜
			$rank_year_user = $this->User_Api_model->get_count(array_merge($data1,array('param'=> "'%Y%m'")),'v_rank_year');
			if($rank_year_user['count'] == 0)
			{
				$param = 'insert';
			}
			else
			{
				$param = 'update';
				//$data1['param'] = "'%Y%m'";
			}
			$data1['time'] = date('m',$time);
			$this->User_Api_model->rank_month($data1,'v_rank_year',$param);
		}
		$num =$time-$video_info['start_time'];
		if($num <= 0 )
		{
			$data['start_time'] = "00:00:00";
		}
		else
		{
			$hour = floor($num/3600);
			$minute = floor(($num-3600*$hour)/60);
			$second = floor((($num-3600*$hour)-60*$minute)%60);
			if($hour < 10)
			{
				$hour = '0'.$hour;
			}
			if($minute < 10)
			{
				$minute = '0'.$minute;
			}
			if($second < 10)
			{
				$second = '0'.$second;
			}
			$data['start_time'] = $hour.':'.$minute.':'.$second;

		}
		$data['praise'] = "$praise";
		$data['title'] = "$video_info[title]";
		$data['views'] = "$video_info[views]";
		//$this->User_Api_model->gag_del(array('video_id'=>$video_id),'v_video');
		if($video_info['is_off'] != '3')
		{
			if(!@fopen($this->config->item('record_url').$video_info['video_name'].'-live.m3u8','r'))
			{
				$is_off = 2;
			}
			else
			{
				$is_off = $time-intval($video_info['start_time']) >= 60 ? 1 : 2;
			}
		}
		else
		{
			$is_off = intval($video_info['is_off']);
		}
		$this->User_Api_model->comment_update(array('video_id'=>$video_id),array('stop_time'=>$time,'is_off'=>$is_off,'display_order'=>30000),'v_video');
		$this->data_back($data,'0X000');
	}

	/**
	 * [praise 点赞接口]
	 * @url  http://video.dhdyz.com/index.php/Api/praise?praise_id=1
	 * @return [type] [ok]
	 */
	/*public function praise()
	{
		$praise_id = isset($_REQUEST['praise_id']) ? intval($_REQUEST['praise_id']) : '';
		//插入到视屏表
		$this->User_Api_model->praise_video($praise_id,'v_video');
		$this->User_Api_model->praise_video($praise_id,'v_users');
		echo json_encode(array('info'=>'ok'));
	}*/

	
	public function rank_info()
	{	
		$i=0;
		//$data[$i] = '时榜';
		//$i++;
		$data[$i] = '日榜';
		$i++;
		$data[$i] = '周榜';
		$i++;
		$data[$i] = '月榜';
		$i++;
		$data[$i] = '年榜';
		$i++;
		//$this->data_back($data,'0X000');
		$i=0;
		$data1[$i] = 'Day';
		$i++;
		$data1[$i] = 'Weekly';
		$i++;
		$data1[$i] = 'Monthly';
		$i++;
		if(stristr($_SERVER['HTTP_USER_AGENT'],'android')){
			$data1[$i] = 'Annual';
		}else{
			$data1[$i] = 'Annualy';
		}

		
		$i=0;
		$data2[$i] = '日次';
		$i++;
		$data2[$i] = '週次';
		$i++;
		$data2[$i] = '月次';
		$i++;
		$data2[$i] = '年次';
		
		$i=0;
		$data3[$i] = '일순위';
		$i++;
		$data3[$i] = '주순위';
		$i++;
		$data3[$i] = '월순위';
		$i++;
		$data3[$i] = '년순위';
		
		$i=0;
		$data4[$i] = 'เมื่อวันที่';
		$i++;
		$data4[$i] = 'สัปดาห์';
		$i++;
		$data4[$i] = 'เดือน';
		$i++;
		$data4[$i] = 'ปี';
		$data_arr = array('result'=>'success', 'msg'=>'0X000', 'info'=>$data,'info_en'=>$data1,'info_jp'=>$data2,'info_ko'=>$data3,'info_th'=>$data4);
        die(json_encode($data_arr));
	}
	/**
	 * [rank_list 排行榜]
	 * @ [type] [ 1 时   2 日  3 周  4  月   5 年]
	 * @return [type] [description]
	 */
	public function rank_list()
	{
		$time = time();
        $h = date('H',$time);
        $d = date('d',time());

        $param = "'%Y%m%d %H'";

		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 1;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$union   = isset($_REQUEST['union']) ? intval($_REQUEST['union']) : 0;
		$page_num =10;
	    //初始化查询条件
        $select = $where = $group_by = $order_by = '';
	    if($type == 1)
	    {
	    	
	    	//时排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-3600) ";
	    	$table    = 'v_rank_day';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_time';
	    }
	    elseif($type ==2)
	    {
	    	//日排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-86400) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_day';
	    	
	    }
	    elseif($type ==3)
	    {
	    	//周排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-604800) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_week';

	    }elseif($type ==4)
	    {
	    	//月排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-2592000) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_month';


	    }elseif($type ==5)
	    {
	    	//年排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-31536000) ";
	    	$table    = 'v_rank_year';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_year';

	    }
	    if(empty($count))
	    {
	    	$this->data_back('无排行榜', '0x000');  //返回数据
	    }
	    $start = ($page-1)*$page_num;
	    $data['list'] = $this->User_Api_model->rank_day_list($select,$where,$group_by,$order_by,$start,$page_num,$table,$this->config->item('catch'),$info,$this->config->item('catch_time'));
		if(!empty($data['list']))
		{
			foreach ($data['list'] as $key => $value) {
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),'v_users');
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['image'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['image'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
				}
				//用户等级认证信息
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['auth']);
				if(empty($union))
				{
					$select = "  COUNT(*) AS count ";
					$where        = " user_id=$user_id AND fans_id=$value[user_id] ";
				    $count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
				    $data['list'][$key]['follow'] = $count_follow[0]['count'];
				}
				
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('无排行榜', '0x000');  //返回数据
		}
	}
	
	/**
	 * [user_info 获取主播信息]
	 * @url = http://video.dhdyz.com/index.php/Api/user_info?user_id=1
	 * @return [type] [description]
	*/
	public function user_info()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		if(empty($user_id))
		{

			$this->data_back("参数为空", '0x011','fail');
		}
		if($user_id<0){
			$user_id=-$user_id;
			$temp_user_watch_by_id=$this->User_Api_model->get_temp_user_info_by_id($user_id);
			$arr=array();
			//for($i=0;$i<$count;$i++){
			$temp_arr=array(
				'user_id'=>'-'.$user_id,
				'image'=>'http://api.etjourney.com//public/images/user/temp_user.png',
				'user_name'=>'游客'.$user_id,
				'sex'=> "0",
				'pre_sign'=>'',
				'address'=>'',
				'froms'=>'',
				'register_time'=>'0',
				'login_time'=>'0',
				'creates'=>'0',
				'video_list'=>$arr,
				'watch'=>$temp_user_watch_by_id['watch'],
				'video_id'=>'0',
				'praise'=>'0','video_sum'=>'0','fan'=>'0',
				'following'=>'0','msgnew'=>0,'letter'=>'0','level'=>'1','openid'=>'',
				'credits'=>'0','checkin_time'=>'0','auth'=>'0','is_guide'=>'0','is_attendant'=>'0',
				'is_driver'=>'0','is_merchant'=>'0','checkin'=>'0','range_guide'=>'','range_attendant'=>'',
				'range_driver'=>'','range_merchant'=>''
			);
			$data=$temp_arr;
			$this->data_back($data, '0x000');
			//}
		}
		$data = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$user_id.'_user_info',$this->config->item('catch_time'));
		if(empty($data))
		{
			$this->data_back('用户信息不存在','0X015','fail');
		}
		else
		{
			$where = " fans_id=$data[user_id] ";
			$fan_count = $this->User_model->get_count($where,'v_follow');
			if($user_id==1734)
			{
			$data['fan'] = strval($fan_count['count'] + ceil((1466673763-1466481400)/6));
			}else
			{
			$data['fan'] = strval($fan_count['count']);
			}
			$follow_count = $this->User_model->get_count(" user_id=$data[user_id] ",'v_follow');
			$data['following'] = strval($follow_count['count']);
			$letter_count = $this->User_model->get_count(" from_id=$data[user_id] AND related='1' AND des_del='0' ",'v_letter');
			$new_letter = $this->User_model->get_count(" to_id=$data[user_id] AND related='0' AND new='1' AND des_del='0' ",'v_letter');
			$data['msgnew'] = $new_letter['count'];
			$data['letter'] = strval($letter_count['count']);
			$data['level'] = $this->get_level($data['credits']);
			//当日是否已签到
			if(date('Ymd',time()) == date('Ymd',intval($data['checkin_time'])))
			{
				$data['checkin'] = '1';
			}
			else
			{
				$data['checkin'] = '0';
			}
			if(stristr($data['image'], 'http')===false)
			{
				$data['image'] = $this->config->item('base_url'). ltrim($data['image'],'.').'?'.time();
			}
			//获取用户认证信息
			$range_guide = $range_attendant = $range_driver = $range_merchant = '';
			if($data['is_guide'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_views');
				if($res)
				{
					$range_guide = $res[0]['id_range'];
				}
			}
			if($data['is_attendant'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_locals');
				if($res)
				{
					$range_attendant = $res[0]['id_range'];
				}
			}
			if($data['is_driver'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_drivers');
				if($res)
				{
					$range_driver = $res[0]['id_range'];
				}
			}
			if($data['is_merchant'] == '1')
			{
				$res = $this->User_Api_model->comment_select(' id_range '," user_id=$user_id AND is_temp='0' ",'','',0,1,'v_auth_business');
				if($res)
				{
					$range_merchant = $res[0]['id_range'];
				}
			}
			$data['range_guide']     = $range_guide;
			$data['range_attendant'] = $range_attendant;
			$data['range_driver']    = $range_driver;
			$data['range_merchant']  = $range_merchant;
		}
		//获取用户直播录像
	    $where  = " user_id=$user_id AND is_off=1 ";
		//echo 'where='.$where;die;
	    $res = $this->User_Api_model->video_list($where,0,6,'new','v_video');
		if($res)
		{
			foreach($res as $key=>$value)
			{
				$list[$key]['video_id'] = $value['video_id'];
				$list[$key]['title'] = $value['title'];
				$list[$key]['start_time'] = $value['start_time'];
				$list[$key]['stop_time'] = $value['stop_time'];
				//if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
					$list[$key]['user_id'] = $user_id;
					$list[$key]['user_name'] = $data['user_name'] ;
					$list[$key]['auth'] = $data['auth'] ;
					$list[$key]['avatar'] = $data['image'];
				//}
				$list[$key]['views'] = $value['views'];
				$list[$key]['praise'] = $value['praise'];
				$list[$key]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
				$list[$key]['video_dec'] =$data['user_name'].'在'.$value['all_address'].'的精彩直播'.$value['title'].',世界那么大赶快来看看!';
				if($value['stop_time'] && time() - intval($value['stop_time']) < 20)
				{
					$list[$key]['image'] = $this->config->item('base_url') . '/tmp/video.jpg';
					$list[$key]['video_url'] = '';
					$list[$key]['video_exist'] = '0';
				}
				else
				{
					$list[$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					$list[$key]['video_url'] = $this->get_rec($value['video_name'],$value['push_type']);
					$list[$key]['video_exist'] = '1';
				}
				if($key==5)
				{
					$last_video = $value['video_id'];
				}
			}
			//删除超过保留期的录播视频
			//if(isset($last_video))
			//{
			//	$result = $this->User_Api_model->update_recode($last_video,$user_id);
			//}
		}
		else
		{
			$list = array();
		}
		$data['video_list'] = $list;
		$this->data_back($data, '0x000');  //返回数据
	}

	/**
	 * [avatar_upload 上传头像]
	 * @return [type] [description]
	*/
	public function avatar_upload()
	{
		//vars($_FILES['files']);
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!isset($_FILES['files']))
		{
			$this->data_back("图片为空", '0x016','fail');
		}
		$name1 = $user_id.'_avatar_big';
		$image = $this->uploadimg->upload_image($_FILES['files'],'user',$name1);
		$name2 = $user_id.'_avatar_small';
		$image = $this->thumb($image,$name2);
		if(empty($user_id)  || empty($image))
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		else
		{
			$param = array(
					'user_id' => $user_id,
					'image'   => $image
				);
			$data = $this->User_Api_model->avatar($param);
			//echo $data;
			if (!empty($data)) {

				$image = $this->config->item('base_url'). ltrim($image,'.');
				$this->data_back($image, '0x000');  //返回数据
			}
			else
			{
				$this->data_back("异常", '0x013','fail');
			}
		}
	}
	
	function thumb($url,$key2='time')
	{
		$arr['image_library'] = 'gd2';
		$arr['source_image'] = $url;
		$arr['maintain_ratio'] = TRUE;
		$type=pathinfo($url,PATHINFO_EXTENSION);
		if($key2=='time'){
		$key2=time();
		}
		$arr['new_image']='./public/images/user/'.$key2.'.'.$type;
		$arr['width']     = 64;
		$arr['height']   = 64;
		//echo $arr['new_image'];
		$res = $this->image_lib->initialize($arr);
		//echo 'res=['.$res.']';
	
		if($this->image_lib->resize()){
		return  $arr['new_image'];
		}
  }

	/**
	 * [update_user 修改用户信息]
	 * @return [type] [description]
	*/
	public function update_user()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$user_name = isset($_REQUEST['user_name']) ? trim(htmlspecialchars($_REQUEST['user_name'])) : '';
		$pre_sign = isset($_REQUEST['pre_sign']) ? trim(htmlspecialchars($_REQUEST['pre_sign'])) : '';
		$sex = isset($_REQUEST['sex']) ? trim(htmlspecialchars($_REQUEST['sex'])) : '';
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if(!$user_name && !$sex && !isset($_REQUEST['pre_sign']))
		{
			$this->data_back('参数有误', '0x011','fail');  //返回数据
		}
		if($user_name)
		{	
			if(strlen($user_name)>30){$this->data_back('参数有误', '0x011','fail'); }

			$param['user_name'] = $user_name;
			$count = $this->User_Api_model->count_all($param,'v_users');
			if($count)
			{
				$this->data_back('用户名已经存在','0X015','fail');
			}
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		}
		if(isset($_REQUEST['pre_sign']))
		{
			$param['pre_sign'] = $pre_sign;
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		}
			if($sex == '女')
			{$param['sex'] = '1';}
			elseif($sex=='男')
			{$param['sex'] = '0';}
			//else{$param['sex'] = '2';}
			//$param['sex'] = $sex;
			$data = $this->User_Api_model->update_user($user_id,$param,'v_users');
		
			$this->data_back('修改成功', '0x000');  //返回数据

	}

	/**举报**/
	public function report()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$report_user_id = isset($_REQUEST['report_user_id']) ? intval($_REQUEST['report_user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$where= array('user_id'=>$user_id);
		$this->User_Api_model->reprot_video($where,'v_video');
		if($report_user_id && $video_id)
		{
			$count = $this->User_Api_model->comment_count(' COUNT(*) AS count '," user_id=$user_id AND video_id=$video_id ", 'v_report');
			if(empty($count[0]['count']))
			{
				$param = array(
						'user_id' => $user_id,
						'report_user_id'    => $report_user_id,
						'video_id' => $video_id,
						'report_time'    => time()
						);
				$report_id = $this->User_Api_model->insert_string($param,'v_report');
			}
		}
		$this->data_back("举报成功", '0x000');  //返回数据
	}

	/**拉黑**/
	public function defriend_add()
	{
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		$defriend    = isset($_REQUEST['defriend'])    ? intval($_REQUEST['defriend'])  : 0;
		if(empty($user_id) || empty($defriend))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'user_id' => $user_id,
				'defriend'  => $defriend
			);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		$this->data_back("拉黑成功", '0x000'); 
		//取消对方的关注
		$this->User_Api_model->gag_del("(user_id=$user_id AND fans_id=$defriend) OR (fans_id=$user_id AND user_id=$defriend) ",'v_follow');
		//删除自己客户端私信
		$this->User_Api_model->gag_del("(to_id=$user_id AND from_id=$defriend) OR (from_id=$user_id AND to_id=$defriend) ",'v_letter');
		$param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_defriend');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("拉黑成功", '0x000');  //返回数据
		}
	}
	/**
	拉黑列表
	**/
	public function defriend_list()
	{
		$page      = isset($_REQUEST['page'])      ? intval($_REQUEST['page'])    : 1;
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		if(!$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$start = ($page-1)*$this->config->item('page_num');
		$where['user_id'] = $user_id;
		$data = $this->User_Api_model->select_string($where,'','',$start,$this->config->item('page_num'),'v_defriend');
		if(empty($data))
		{
			$this->data_back(array(),'0X014','fail');
		}
		else
		{
			foreach ($data as $key => $value) {

				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[defriend]");
				$user_info = $this->User_Api_model->user_info(" user_id=$value[defriend] ",'v_users',$this->config->item('catch'),$value['defriend'].'_user_info',$this->config->item('catch_time'));

				if(stristr($user_info['image'], 'http'))
				{
					$data1[$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data1[$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data1[$key]['user_name'] = $user_info['user_name'];
				$data1[$key]['defriend'] = $value['defriend'];
				$data1[$key]['auth'] = $user_info['auth'];
				$data1[$key]['sex'] = $user_info['sex'];
				$data1[$key]['level'] = $this->get_level($user_info['credits']);
				$data1[$key]['pre_sign'] = $user_info['pre_sign'];
			}
			$this->data_back($data1,'0X000');
		}
	}

	/**取消拉黑**/
	public function defriend_del()
	{
		$user_id      = isset($_REQUEST['user_id'])      ? intval($_REQUEST['user_id'])    : 0;
		$defriend    = isset($_REQUEST['defriend'])    ? intval($_REQUEST['defriend'])  : 0;
		if(empty($user_id) || empty($defriend))
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		$param = array(
				'user_id' => $user_id,
				'defriend'  => $defriend
			);
		$data = $this->User_Api_model->gag_del($param,'v_defriend');
		$this->data_back("取消成功", '0x000');
	}
	

	/**
	 * [letter 发私信]
	 * @url http://video.dhdyz.com/index.php/Api/letter_info?to=1&from=2&content=%E6%B5%8B%E8%AF%95
	 * @return [type] [description]
	*/
	public function letter_add()
	{
		$to      = isset($_REQUEST['to'])      ? intval($_REQUEST['to'])    : 0;
		$from    = isset($_REQUEST['from'])    ? intval($_REQUEST['from'])  : 0;
		$content = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : 0;
		$time = time();
		if(!$to|| !$from || !$content)
		{
			$this->data_back("参数不全", '0x011','fail');
		}
		$param = array(
			'user_id' => $to,
			'defriend'  => $from
		);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		{
			$this->data_back("对方已将你拉黑", '0x000');
		}
		$param = array(
				'to_id'=> $to,
				'from_id'=> $from);

		$where = " (to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to) ";
		$count = $this->User_Api_model->rank_count('COUNT(*) AS count,des_del ',$where,'v_letter');

		if(empty($count[0]['count']))
		{	
			$param = array(
				'to_id'       => $to,
				'from_id'     => $from,
				'content'  => $content,
				'dateline' => $time,
				'related'  => '1'
			);
			$this->User_Api_model->letter_add($param,'v_letter');
			$param = array(
				'to_id'       => $from,
				'from_id'     => $to,
				'content'  => $content,
				'dateline' => $time,
				'new'      => '1',
				'related'  => '1'
			);
			$this->User_Api_model->letter_add($param,'v_letter');

			$param = array(
					'to_id'       => $to,
					'from_id'     => $from,
					'content'  => $content,
					'new'      => '1',
					'dateline' => $time,
					
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');
			/*$param = array(
					'from_id'       => $to,
					'to_id'     => $from,
					'content'  => $content,
					'dateline' => $time,
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');*/
		}
		else
		{	

			$where = " ((to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to)) AND related='1' ";
			$this->User_Api_model->comment_update($where,array('des_del'=>'0'),'v_letter');
			$letter_id = $this->User_Api_model->rank_count('letter_id',$where,'v_letter');
			if(empty($letter_id[0]['letter_id']))
				$this->data_back("参数不全", '0x011','fail');
			for($i=0;$i<2;$i++)
			{
				$where = array(
					'letter_id' => $letter_id[$i]['letter_id']);
				$data = array(
					'content' => $content);
				$this->User_Api_model->comment_update($where,$data,'v_letter');
			}
			$param = array(
					'to_id'       => $to,
					'from_id'     => $from,
					'content'  => $content,
					'new'      => '1',
					'dateline' => $time
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');
			/*$param = array(
					'from_id'       => $to,
					'to_id'     => $from,
					'content'  => $content,
					'dateline' => $time,
				);
			$data = $this->User_Api_model->letter_add($param,'v_letter');*/
		}
		if ($data) {
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("异常", '0x013','fail');
		}
	}

	/**
	 * [letter_list 私信列表]
	 * @return [type] [description]
	*/
	public function letter_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$start = ($page-1)*$this->config->item('page_num');
		if(empty($user_id))
		{
			$this->data_back("user_id参数为空", '0x011','fail');
		}
		//初始化数据操作
		$data =  array();
		$order_by     = array('dateline'=>'DESC');
		$where = " from_id=$user_id AND related='1' AND des_del<>'2' ";
		$data = $this->User_Api_model->comment_select('*',$where,'','dateline DESC',$start,$this->config->item('page_num'),'v_letter','',
			$this->config->item('catch'),$user_id.$start. 'letter_list',$this->config->item('catch_time'));
		if(!empty($data))
		{
			foreach ($data as $key => $value) {
				$new_letter = $this->User_model->get_count(" to_id=$user_id AND from_id=$value[to_id] AND related='0' AND new='1' ",'v_letter');
				$data[$key]['msgnew'] = $new_letter['count'];
				$user_info = $this->User_Api_model->user_info(" user_id=$value[to_id] ",'v_users',$this->config->item('catch'),$value['to_id'].'_user_info',$this->config->item('catch_time'));
				$data[$key]['from_name'] = $user_info['user_name'];
				if(stristr($user_info['image'], 'http'))
				{
					$data[$key]['from_avatar'] = $user_info['image'];
				}
				else
				{
					$data[$key]['from_avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data[$key]['sex'] = $user_info['sex'];
				$data[$key]['auth'] = $user_info['auth'];
				$data[$key]['level'] = $this->get_level($user_info['credits']);
			}
		}
		//获取系统消息
		$res = $this->User_Api_model->comment_select('pid,pm_type,message,is_new,add_time'," user_id=$user_id ",'','add_time DESC',0,1,'v_prompt');
		if($res)
		{
			$pm['letter_id'] = $res[0]['pid'];
			$pm['content'] = $res[0]['message'];
			$pm['from_id'] = strval($user_id);
			$pm['to_id'] = '1';
			$pm['dateline'] = $res[0]['add_time'];
			$new = $this->User_model->get_count(" user_id=$user_id AND is_new=1 ",'v_prompt');
			$pm['msgnew'] = $new['count'];
			$pm['from_name'] = '系统消息';
			$pm['from_avatar'] = $this->config->item('sys_icon');
			$pm['sex'] = '2';
			$pm['auth'] = '0';
			$pm['level'] = '0';
			array_unshift($data,$pm);
		}
		if(!empty($data))
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x018','fail');
		}
	}

	/**
	 * [letter_info 私信详情]
	 * @return [type] [description]
	*/
	public function letter_info()
	{
		$to   = isset($_REQUEST['to'])   ? intval($_REQUEST['to'])   : 0;
		$from = isset($_REQUEST['from']) ? intval($_REQUEST['from']) : 0;
		if(!$from)
		{
			$this->data_back("to或者from参数为空", '0x011','fail');
		}
		if(!$to)
		{
			$res = $this->User_Api_model->comment_select('pid,pm_type,message,is_new,add_time'," user_id=$from ",'','add_time','','','v_prompt',1);
			if(!empty($res))
			{
				$this->User_Api_model->comment_update(array('user_id'=>$from),array('is_new'=>0),'v_prompt');
				$to_user_info = $this->User_Api_model->user_info(" user_id=$from ",'v_users',$this->config->item('catch'),$from.'_user_info',$this->config->item('catch_time'));
				foreach ($res as $key => $value)
				{
					$data[$key]['letter_id'] = $value['pid'];
					$data[$key]['content'] = $value['message'];
					$data[$key]['from_id'] = '0';
					$data[$key]['to_id'] = strval($from);
					$data[$key]['rand_id'] = '0';
					$data[$key]['des_del'] = '0';
					$data[$key]['new'] = '0';
					$data[$key]['related'] = '0';
					$data[$key]['dateline'] = $value['add_time'];
					if(stristr($to_user_info['image'], 'http'))
					{
						$data[$key]['to_avatar'] = $to_user_info['image'];
					}
					else
					{
						$data[$key]['to_avatar'] = $this->config->item('base_url').ltrim($to_user_info['image'],'.');
					}
					$data[$key]['to_name'] = $to_user_info['user_name'];
					$data[$key]['from_name'] = '系统消息';
					$data[$key]['from_avatar'] = $this->config->item('sys_icon');
				}
				$this->data_back($data, '0x000');  //返回数据
			}
		}
		$where = " ((to_id=$to AND from_id=$from ) OR (to_id =$from AND from_id=$to)) AND related='0' ";
		//$data = $this->User_Api_model->letter_info($to,$from,'v_letter');
		$data = $this->User_Api_model->letter_info($where,'v_letter');
		//echo $this->db->last_query();
		if(!empty($data))
		{
			$this->User_Api_model->comment_update(array('to_id'=>$from,'from_id'=>$to),array('new'=>'0'),'v_letter');
			foreach ($data as $key => $value) {
				//$to_user_info = $this->Admin_model->user_info('v_users',"user_id=$value[to_id]");
				$to_user_info = $this->User_Api_model->user_info(" user_id=$value[to_id] ",'v_users',$this->config->item('catch'),$value['to_id'].'_user_info',$this->config->item('catch_time'));
				if(stristr($to_user_info['image'], 'http'))
				{
					$data[$key]['to_avatar'] = $to_user_info['image'];
				}
				else
				{
					$data[$key]['to_avatar'] = $this->config->item('base_url').ltrim($to_user_info['image'],'.');
				}
				$data[$key]['to_name'] = $to_user_info['user_name'];
				//$from_user_info = $this->Admin_model->user_info('v_users',"user_id=$value[from_id]");
				$from_user_info = $this->User_Api_model->user_info(" user_id=$value[from_id] ",'v_users',$this->config->item('catch'),$value['from_id'].'_user_info',$this->config->item('catch_time'));
				$data[$key]['from_name'] = $from_user_info['user_name'];
				if(stristr($from_user_info['image'], 'http'))
				{
					$data[$key]['from_avatar'] = $from_user_info['image'];
				}
				else
				{
					$data[$key]['from_avatar'] = $this->config->item('base_url').ltrim($from_user_info['image'],'.');
				}
				//$data[$key]['from_avatar'] = $from_user_info['image'] ?  $this->config->item('base_url').ltrim($from_user_info['image'],'.') : "";
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x018','fail');
		}
	}

	/**
	 * [letter_del 私信删除]
	 * @return [type] [description]
	*/
	public function letter_del()
	{
		$to   = isset($_REQUEST['to'])   ? intval($_REQUEST['to'])   : 0;
		$from = isset($_REQUEST['from']) ? intval($_REQUEST['from']) : 0;
		if(!$to||!$from)
		{
			$this->data_back("to或者from参数为空", '0x011','fail');
		}
		$del = $this->User_model->get_count(" from_id=$to AND to_id=$from AND related='1' AND des_del='2' ",'v_letter');
		if($del['count']==0)
		{
			$this->User_Api_model->letter_del($to,$from,'v_letter','1','1');
		}
		$this->User_Api_model->letter_del($from,$to,'v_letter','1','2');
		$data = $this->User_Api_model->letter_del($to,$from,'v_letter');
		if(empty($data))
			$this->data_back("返回数据为空、未找到私信", '0x014','fail');
		$this->data_back('删除成功', '0x000');  //返回数据
	}
	/**
	 * [gag 当前视屏下 -某用户 - 禁言]
	 * 
	 * @return [type] [description]
	*/
	public function gag_add()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['gag_id']   ? intval($_REQUEST['gag_id'])   : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'video_id' => $video_id,
				'user_id_1'  => $user_id
			);
		$count = $this->User_Api_model->count_all($param,'v_gag');
		if($count)
		{
			$this->data_back("禁言成功", '0x000'); 
		}
		$param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_gag');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("禁言成功", '0x000');  //返回数据
		}
	}

	/**
	 * [del_gag 取消禁言]
	 * @return [type] [description]
	*/
	public function gag_del()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['gag_id']   ? intval($_REQUEST['gag_id'])   : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'video_id' => $video_id,
				'user_id_1'  => $user_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_gag');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("取消禁言成功", '0x000');  //返回数据
		}
	}
	/**
	 * [del_gag 判断是否禁言]
	 * @return [type] [description]
	*/
	public function is_gag()
	{
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$gag_id   = isset($_REQUEST['gag_id']) ? intval($_REQUEST['gag_id']) : 0;
		if(!$video_id || !$gag_id)
			$this->data_back('参数不能为空','0X011','fail');
		$where['video_id']  = $video_id;
		$where['user_id_1'] = $gag_id; 
		$count = $this->User_Api_model->count_all($where, 'v_gag');
		if(empty($count))
		$this->data_back('禁言', '0x000');  //返回数据
		$this->data_back("已禁言", '0x000');

	}

	/**
	 * [fan_add 粉丝]
	 * @return [type] [description]
	*/
	public function fans_add()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!$fans_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$param = array(
				'fans_id'  => $fans_id,
				'user_id'  => $user_id,
				'dateline' => time() 
			);
		$data = $this->User_Api_model->gag_add($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("关注成功", '0x000');  //返回数据
		}
	}

	/**
	 * [fans_list 粉丝列表]
	 * @return [type] [description]
	*/
	public function fans_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		//if($fans_id){
			$fans_user_arr=array();
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		//}else{
		//	$fans_user_arr=array();
		//}
		//echo "<pre>";print_r($fans_user_arr);
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = $where = $group_by = $order_by = '';
		$page_num =10;
		//$data['now_page'] = $page;
		if($fans_id)
		{
			$where = " fans_id=$fans_id ";
		}
		else
		{
			$where = " fans_id=$user_id ";
		}
		$count = $this->User_model->get_count($where,'v_follow');
		if(empty($count['count']))
		{
			$this->data_back("没有关注者", '0x017','fail');
		}
		$start = ($page-1)*$page_num;
		$select = ' user_id ';
		$order_by = " dateline ASC ";
		$data['list'] = $this->User_Api_model->comment_select($select,
			$where,$group_by,$order_by,$start,$page_num,'v_follow');
		if(!empty($data['list']))
		{
			foreach ($data['list'] as $key => $value) {
				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[fans_id]");
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),
					'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',
					$this->config->item('catch_time'));
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id'])){
					$is_follow='1';
				}else{
					$is_follow='0';
				}
				//if(!$fans_id){
				//	$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
				//	foreach($fans_user_arr_2 as $v){
				//		$fans_user_arr[]=$v['fans_id'];
				//	}
				//	if(in_array($value['user_id'],$fans_user_arr)){
				//		$is_follow='1';
				//	}else{
				//		$is_follow='0';
				//	}
				//}
				$data['list'][$key]['follow'] = $is_follow;
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['credits']);
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('没有关注者', '0x017','fail');  //返回数据
		}
	}
	
	/**
	 * [fan_del 取消粉]
	 * @return [type] [description]
	*/
	
	public function fans_del()
	{
		//$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 0;
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(empty($fans_id) || empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}	
		$param = array(
				'fans_id'  => $fans_id,
				'user_id'  => $user_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			$this->data_back("取消关注成功", '0x000');  //返回数据
		}
	}
	

	/**
	 * [is_fans 判断用户是否粉主播]
	 * @return [type] [description]
	*/
	
	public function is_fans()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		//初始化数据操作
		$select = "  COUNT(*) AS count ";
		$where        = " user_id=$user_id AND fans_id=$fans_id ";
	    $count = $this->User_Api_model->comment_count($select,$where,'v_follow');
	  	if(empty($count[0]['count']))
	  	{
	  		$this->data_back('关注', '0x000');  //返回数据
	  	}
	  	else
	  	{
	  		$this->data_back("已关注", '0x000');  //返回数据
	  	}
	}

	/**
	 * [follow_add 关注]
	 * @return [type] [description]
	*/
	public function follow_add()
	{
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$language = isset($_REQUEST['language']) ? trim($_REQUEST['language']) : '';
		if(!$fans_id ||!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}elseif($fans_id<0)
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("无法关注游客", '0x011','fail');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("無法關注遊客", '0x011','fail');
				}else{
					$this->data_back("Tourists can not be followed!", '0x011','fail');
				}
			}else
			{
				$this->data_back("无法关注游客", '0x011','fail');
			}
		}elseif($fans_id == $user_id){
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("无法关注自己", '0x011','fail');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("無法關注自己", '0x011','fail');
				}else{
					$this->data_back("Can not follow yourself!", '0x011','fail');
				}
			}else
			{
				$this->data_back("无法关注自己", '0x011','fail');
			}
		}
		$param = array(
				'user_id'  => $user_id,
				'fans_id'  => $fans_id
			);
		$count = $this->User_Api_model->count_all($param,'v_follow');
	    if($count)
	    {
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("您已关注", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("您已關注", '0x000');
				}else{
					$this->data_back("You have been followed yet", '0x000');
				}
			}else
			{
				$this->data_back("您已关注", '0x000');  //返回数据
			}
	    }
	    $param['dateline'] = time();
		$data = $this->User_Api_model->gag_add($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("关注成功", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("關注成功", '0x000');
				}else{
					$this->data_back("Success", '0x000');
				}
			}else
			{
				$this->data_back("关注成功", '0x000');  //返回数据
			}
		}
	}

	/**
	 * [follow_list 关注列表]
	 * @return [type] [description]
	*/
	public function follow_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		$fans_id= isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		if($fans_id){
			$fans_user_arr=array();
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		}else{
			$fans_user_arr=array();
		}
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$group_by = $order_by = '';
		//$data['now_page'] = $page;
		if($fans_id)
		{
			$where = " user_id=$fans_id ";
		}
		else
		{
			$where = " user_id=$user_id ";
		}
		$count = $this->User_Api_model->count_all(array('user_id'=>$user_id),'v_follow');
		if(empty($count))
		{
			$this->data_back("没有关注者", '0x000','fail');
		}
		/* $data['max_page'] = ceil($count['count']/$page_num);
         if($page>$data['max_page'])
         {
           $page=1;
         }*/
		$start = ($page-1)*$this->config->item('page_num');
		$select = ' fans_id,status ';
		$order_by = " dateline ASC ";
		$data['list'] = $this->User_Api_model->comment_select($select,
			$where,$group_by,$order_by,$start,$this->config->item('page_num'),'v_follow');
		//echo "<pre>";
		// print_r($data);
		if($data['list'])
		{
			foreach ($data['list'] as $key => $value) {
				//$user_info = $this->Admin_model->user_info('v_users',"user_id=$value[user_id]");
				$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['fans_id']),
					'v_users',$this->config->item('catch'),$value['fans_id'].'_user_info',$this->config->item('catch_time'));
				//print_r($user_info);
				if(stristr($user_info['image'], 'http'))
				{
					$data['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data['list'][$key]['avatar'] = $this->config->item('base_url') . $user_info['image'];
				}
				$data['list'][$key]['user_name'] = $user_info['user_name'];
				$data['list'][$key]['user_id']=$data['list'][$key]['fans_id'];
				unset($data['list'][$key]['fans_id']);
				if(in_array($value['fans_id'],$fans_user_arr)||$value['fans_id']==$user_id){
					$is_follow='1';
				}else{
					$is_follow='0';
				}
				if(!$fans_id){$is_follow='1';}
				$data['list'][$key]['follow'] = $is_follow;
				$data['list'][$key]['sex'] = $user_info['sex'];
				$data['list'][$key]['auth'] = $user_info['auth'];
				$data['list'][$key]['level'] = $this->get_level($user_info['credits']);
				$data['list'][$key]['pre_sign'] = $user_info['pre_sign'];
			}

			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('没有关注者', '0x000','fail');  //返回数据
		}
	}
	/**
	 * [follow_del 取消关注]
	 * @return [type] [description]
	*/
	public function follow_del()
	{
		//$type    = isset($_REQUEST['type'])    ? intval($_REQUEST['type'])    : 0;
		$fans_id = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$language = isset($_REQUEST['language']) ? trim($_REQUEST['language']) : '';
		if(!$fans_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}	
		$param = array(
				'user_id'  => $user_id,
				'fans_id'  => $fans_id
			);
		$data = $this->User_Api_model->gag_del($param,'v_follow');
		if(empty($data))
		{
			$this->data_back("处理异常", '0x013','fail');
		}
		else
		{
			if($language)
			{
				if($language == 'zh')
				{
					$this->data_back("取消关注成功", '0x000');
				}elseif($language == 'zh-HK' || $language == 'zh-TW')
				{
					$this->data_back("取消關注成功", '0x000');
				}else{
					$this->data_back("Cancel success", '0x000');
				}
			}else
			{
				$this->data_back("取消关注成功", '0x000');  //返回数据
			}
		}
	}

	/**
	 * [is_follow 判断用户是否关注主播]
	 * @return [type] [description]
	*/
	public function is_follow()
	{
		$fans_id  = isset($_REQUEST['fans_id']) ? intval($_REQUEST['fans_id']) : 0;
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		//初始化数据操作
		/*$select = "  COUNT(*) AS count ";
		$where        = " user_id=$fans_id AND fans_id=$user_id ";
	    $count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
	    */
        $where = array(
	    		'user_id' => $user_id,
	    		'fans_id' => $fans_id);
	    $count = $this->User_Api_model->count_all($where,'v_follow');
	    unset($where);
	    if(empty($video_id))
	    {
	    	$count_gag =0;
	    }
	    else
	    {
	    	$where = array(
	    		'video_id' => $video_id,
	    		'user_id_1' => $fans_id);

	    	 $count_gag = $this->User_Api_model->count_all($where,'v_gag');
	    }
		
	    if(empty($count) && empty($count_gag))
	    {                                                                                               
	    	$data['follow'] = "0";
	    	$data['gag']    = "0";
	    }
	    elseif(empty($count) && !empty($count_gag))
	    {
	    	$data['follow'] = "0";
	    	$data['gag']    = "1";
	    }
	    elseif(!empty($count) && empty($count_gag))
	    {	$data['follow'] = "1";
	    	$data['gag']    = "0";

	    }elseif(!empty($count) && !empty($count_gag))
	    {
	    	$data['follow'] = "1";
	    	$data['gag']    = "1";
	    }
	    $this->data_back($data,'0X000');
	}

	/**
	 * [watch_start 观看者观看]
	 * @return [type] [description]
	*/
	public function watch_start()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['user_id']  ? intval($_REQUEST['user_id'])  : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//修改用户当前观看的视屏
		$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('watch'=>$video_id),'v_users');
		//修改视屏表 观看者+1
		//$this->User_Api_model->update_views(array('video_id'=>$video_id),'v_video');
		$count = $this->User_Api_model->comment_count(" COUNT(*) AS count "," watch=$video_id ",'v_users');
		if($count && $count[0]['count'] >= 20)
		{
			$watch_num = ($user_id%4 + 1) * 5;
		}
		else
		{
			$watch_num =  1;
		}
		$this->User_Api_model->update_watchs(array('video_id'=>$video_id),'v_video',$watch_num);
		$this->data_back("正在观看", '0x000');  //返回数据
	}

	/**
	 * [watch_stop 观看者停止观看]
	 * @return [type] [description]
	*/
	public function watch_stop()
	{
		$video_id = $_REQUEST['video_id'] ? intval($_REQUEST['video_id']) : 0;
		$user_id  = $_REQUEST['user_id']  ? intval($_REQUEST['user_id'])  : 0;
		if(!$video_id || !$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//修改用户当前观看的视屏
		$this->User_Api_model->update_watch(array('user_id'=>$user_id),array('watch'=>0),'v_users');
		//更新观看人数
		$count = $this->User_Api_model->comment_count(" COUNT(*) AS count "," watch=$video_id ",'v_users');
		if($count && $count[0]['count'] >= 20)
		{
			$watch_num = -($user_id%4 + 1);
		}
		else
		{
			$watch_num = -1;
		}
		$this->User_Api_model->update_watchs(array('video_id'=>$video_id),'v_video',$watch_num);
		$this->data_back("观看结束", '0x000');  //返回数据
	}

	/**
	 * [watch_num 正在观看人数和观看过的人数]
	 * @return [type] [description]
	*/
	public function watch_num()
	{	
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		if(!$video_id )
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id,'',2);
		//var_dump($video_info);
		$video_type = '0';
		$msg_info = '';
		if($video_info)
		{
			if($video_info['is_off'] == '0')
			{
				$image = '/opt/nginx/html/zxqc'.ltrim($video_info['image'],'.');
				if(!file_exists($image))
				{
					$rtmp = $this->get_rtmp($video_info['video_name']);
					$exec = 'ffmpeg -i "'.$rtmp.'" -y -t 0.001 -ss 1 -f image2 -r 1  '.$image.' > /dev/null 2>&1 &';
					exec($exec);
				}
			}
			elseif($video_info['is_off'] == '1' || $video_info['is_off'] == '2')
			{
				if($video_info['push_done'] == 1)
				{
					$video_type = '3';
					if($user_id == intval($video_info['user_id']))
					{
						$this->User_Api_model->comment_update(array('video_id'=>$video_info['video_id']),array('push_done'=>2),'v_video');
						$msg_info = '网络异常，您的直播已中断！';
					}
					else
					{
						$msg_info = '直播已关闭';
					}
				}
				else
				{
					$video_type = '1';
					$msg_info = '直播已关闭';
				}
			}
			elseif($video_info['is_off'] == '3')
			{
				$video_type = '2';
				$msg_info = '您已被管理员禁播！';
			}
		}
		//添加僵尸看客
		//$this->add_zombies($video_id);

		$views = $this->User_Api_model->view_num($video_id);

		$count = $this->User_Api_model->watch_num($video_id);
		$temp_user_count_and_id=$this->User_Api_model->temp_watch_num($video_id);
		foreach ($count['user_info'] as $key => $value) {
			if(stristr($value['image'], 'http')){
				$count['user_info'][$key]['image'] = $value['image'];			}
			else{
				$count['user_info'][$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
			}
		}
		for($i=0;$i<$temp_user_count_and_id['count'];$i++){
			$temp_arr=array(
				'user_id'=>'-'.$temp_user_count_and_id['temp_users_id'][$i]['temp_users_id'],
				'image'=>'http://api.etjourney.com//public/images/user/temp_user.png',
				'user_name'=>'游客'.$temp_user_count_and_id['temp_users_id'][$i]['temp_users_id'],
				'sex'=> "0"
			);
			$count['user_info'][]=$temp_arr;
		}
		//$count['count']=(string)($count['count']+$temp_user_count_and_id['count']);
		$count['count']=(string)(intval($video_info['watch_num'])+$temp_user_count_and_id['count']);
		$data = array(
			'now_watch' => $count['count'],
			'all_watch' => $views['views'],
			'user_info' => $count['user_info'],
			'video_type' => $video_type,
			'msg_info'  => $msg_info
		);
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
	 * [video_list 视屏列表]
	 * @return [type] [description]
	*/
	public function video_list()
	{
		//$this->data_back(array(), '0x002','fail');
		//最新 最热视屏   默认最热
		$type = isset($_REQUEST['type']) ? $_REQUEST['type']         : 'new';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		//$data['type'] = $type;
		$time = time() - $this->config->item('vide_list_catch_time');
		$title = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		if($title)
		{
			//$where = "(v.title LIKE '%$data[title]%' OR v.user_id LIKE '%$data[title]%' OR u.user_name LIKE '%$data[title]%')";
	    	$where = " (title LIKE '%$title%' OR user_id LIKE '%$title%') AND $time > start_time AND types=1 ";
		}
		else
		{
			$where  = " 1=1 AND $time > start_time AND types=1 ";
		}
		//是否显示录播
		if($this->config->item('record_status'))
		{
			$where  .= " AND is_off<2 ";
		}
		else
		{
			$where  .= " AND is_off=0 ";
		}
		$page_num =10;
		//$data['now_page'] = $page;
		$count = $this->User_model->get_count($where,'v_video');
		//$data['max_page'] = ceil($count['count']/$page_num);
		/* if($page>$data['max_page'])
         {
           $page=1;
         }*/
		if($type == 'hot')
		{
			if($page == 1)
			{
				//$page_num  = 11;
				$page_num = 10;
			}
			else
			{
				$page_num = 10;
			}

		}
		$start = ($page-1)*$page_num;

		//if($type =='hot')
		//{
		//	if($count['count'] > 11 && $page > 1)
		//	{
		//		$start = ($page-1)*$page_num +1;
		//	}
		//}

		$video = $this->User_Api_model->video_list($where,
			$start,
			$page_num,
			$type,
			'v_video',
			0,
			$type.$start.'video_list',
			$this->config->item('catch_time'));
		$data = array();
		if(!empty($video))
		{

			foreach ($video as $key => $value) {
				//视频截图尚未生成时，列表暂不显示
				//if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')) || (!@fopen('http://oss.etjourney.com/etjourney/'.$value['video_name'].'.m3u8','r') && $value['is_off']=='1'))
				if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')) || ($value['stop_time'] != '0' && (time()-intval($value['stop_time']) < 600) && $value['is_off']=='1'))
				//if(!file_exists('/opt/nginx/html/zxqc'.ltrim($value['image'],'.')))
				{
					continue;
				}
				$info = $value;
				$distance = "";
				$lct2 = explode(",",$value['location']);
				$lat2 = $lct2[0];
				$lng2 = $lct2[1];
				if($location && $value['location'])
				{
					$lct1 = explode(",",$location);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					if($lat2 == '0.0' && $lng2 == '0.0')
					{
						$distance = '∞';
					}
					else
					{
						$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
					}
				}
				$info['ipinfo'] =  "";
				if(empty($value['address']))
				{
					if($lat2 == '0.0' && $lng2 == '0.0')
					{
						$info['ipinfo'] = '火星?';
					}
					else
					{
						$position = $this->geocoder($lat2,$lng2);
						if($position)
						{
							$address = '';
							$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
							$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
							$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
							$description = isset($position['result']['sematic_description']) ? $position['result']['sematic_description'] : '';
							if($city)
							{
								$address = $city;
							}elseif($province){
								$address = $province;
							}elseif($country){
								$address = $country;
							}elseif($description){
								$address = $description;
							}
							if($address)
							{
								$info['ipinfo'] = $address;
								$this->User_Api_model->comment_update(array('video_id'=>$value['video_id']),array('address'=>$address),'v_video');
							}
						}
						if(!empty($value['ip']) && empty($info['ipinfo']))
						{
							$info['ipinfo'] = $this->common->GetIpLookup($value['ip']);
						}
					}
					//echo 'ipinfo='.$info['ipinfo'];
				}
				else
				{
					$info['ipinfo'] = $value['address'];
				}
				//$data[$key]['image'] = 'http://y0.ifengimg.com/cmpp/2016/03/25/03/63845c99-e4f4-442c-aad0-2e85fc11d0c4_size23_w510_h287.jpg';
				//$data[$key]['rtmp'] ='rtmp://hzrtmp01.ys7.com/livestream/464786166_1_1_1_0';
				if($value['is_off'] == '1')
				{
					$info['rtmp'] = $this->get_rec($value['video_name'],$value['push_type']);
				}
				else
				{
					$info['rtmp'] = $this->get_rtmp($value['video_name']);
				}
				$user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
				//$data[$key]['avatar'] = $user_info['image'];
				if(stristr($user_info['image'], 'http'))
				{
					$info['avatar'] = $user_info['image'];
				}
				else
				{
					$info['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
				}
				$info['user_name'] = $user_info['user_name'];
				if($user_info['sex']=='2')
				{
					$info['sex'] = '0';
				}
				else
				{
					$info['sex'] = $user_info['sex'];
				}
				$info['auth'] = $user_info['auth'];
				$info['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
				if($value['is_off'] == '0')
				{
					$info['image'] .= '?'.intval(time()/120);
				}
				$info['distance'] = strval($distance);
				$info['video_type'] = strval($value['is_off']);
				$info['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
				$info['video_dec']=$info['user_name'].'在'.$info['ipinfo'].'的精彩直播'.$info['title'].',世界那么大赶快来看看!';
				$data[] = $info;

			}

			$this->data_back($data, '0x000');  //返回数据

		}
		else
		{
			$this->data_back(array(), '0x002','fail');
		}
	}

	/**
	 * [fans_video 关注视屏列表]
	 * @return [type] [description]
	*/
	public function fans_video()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$page =    isset($_REQUEST['page'])      ? intval($_REQUEST['page'])     : 1;
		$location =    isset($_REQUEST['location'])  ? trim($_REQUEST['location'])   : '';
		$time = time()-$this->config->item('vide_list_catch_time');
		if(empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//初始化数据操作
		$select_count = "  COUNT(*) AS count ";
		//$select       = " user_id ";
		$select       = " fans_id ";
		//$where        = " fans_id=$user_id ";
		$where        = " user_id=$user_id ";
	    $group_by     = "";
	    $order_by     = " dateline ASC ";
		$page_num =10;
	    $count = $this->User_Api_model->comment_count($select_count,$where,'v_follow');
	    $count = $count[0]['count'];
		if(empty($count))
		{
			$info['video'] = $this->config->item('base_url').'public/images/app/follow_false.png';
			$info['list']  = array();
			$this->data_back($info,'0X000');
		}
	    //$data['max_page'] = ceil($count/$page_num);
	    $start = ($page-1)*$page_num;
	    //所有关注的用户
		$data = $this->User_Api_model->comment_select($select,$where,$group_by,$order_by,$start,$page_num,'v_follow');
		$select   = " v.* ,f.f_id,f.fans_id,f.status,f.dateline  ";
		//$table    = " v_video AS v LEFT JOIN  v_follow  AS f ON f.user_id= v.user_id ";
		$table    = " v_video AS v LEFT JOIN  v_follow  AS f ON f.fans_id= v.user_id ";
		//$where        = " f.fans_id=$user_id ";
		$where        = " f.user_id=$user_id ";
		//if($this->config->item('record_status'))
		//{
		//	$where  .= " AND v.is_off<2 ";
		//}
		//else
		//{
			$where  .= " AND v.is_off=0 ";
		//}
		$group_by = "";
		$order_by = " is_off,video_id DESC ";
		//关注正在直播的视屏
		$data_info1 = $this->User_Api_model->comment_select($select,$where,$group_by,$order_by,$start,$page_num,$table);
		//echo $this->db->last_query();
		//var_dump($data_info1);
		if(empty($data_info1))
		{
			$data_info['video'] = $this->config->item('base_url').'public/images/app/follow_false.png';
		}
		else
		{
			foreach ($data_info1 as $k => $v) {
				if($v['is_off'] == '1')
				{
					$data_info['video'][$k]['rtmp'] = $this->get_rec($v['video_name'],$v['push_type']);
				}
				else
				{
					$data_info['video'][$k]['rtmp'] = $this->get_rtmp($v['video_name']);
				}
				$distance = "";
				if($location && $v['location'])
				{
					$lct1 = explode(",",$location);
					$lct2 = explode(",",$v['location']);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					$lat2 = $lct2[0];
					$lng2 = $lct2[1];
					$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
				}
				if(stristr($v['image'], 'http'))
				{
					$data_info['video'][$k]['image'] = $v['image'];
				}
				else
				{
					$data_info['video'][$k]['image'] = $this->config->item('base_url') . ltrim($v['image'],'.');
				}
				
				if(!empty($v['address']))
				{
					$data_info['video'][$k]['ipinfo'] =  $v['address'];
				}
				elseif(!empty($v['ip']))
				{
					$data_info['video'][$k]['ipinfo'] = $this->common->GetIpLookup($v['ip']);
				}
				else
				{
					$data_info['video'][$k]['ipinfo'] =  "";
				}
				$data_info['video'][$k]['video_id'] =  $v['video_id'];
				$data_info['video'][$k]['title'] =  $v['title'];
				$data_info['video'][$k]['start_time'] =  $v['start_time'];
				$data_info['video'][$k]['report'] =  $v['report'];
				$data_info['video'][$k]['praise'] =  $v['praise'];
				$data_info['video'][$k]['user_id'] =  $v['user_id'];
				$data_info['video'][$k]['views'] =  $v['views'];
				$data_info['video'][$k]['video_name'] =  $v['video_name'];
				$data_info['video'][$k]['socket_info'] =  $v['socket_info'];
				//$user_info1 = $this->Admin_model->user_info('v_users',"user_id=$v[user_id]");
				$user_info1 = $this->User_Api_model->user_info(" user_id=$v[user_id] ",'v_users',$this->config->item('catch'),$v['user_id'].'_user_info',$this->config->item('catch_time'));
				$data_info['video'][$k]['user_name'] =  $user_info1['user_name'];
				if(stristr($user_info1['image'], 'http'))
				{
					$data_info['video'][$k]['avatar'] = $user_info1['image'];
				}
				else
				{
					$data_info['video'][$k]['avatar'] = $this->config->item('base_url')  . ltrim($user_info1['image'],'.');
				}
				$data_info['video'][$k]['sex'] = $user_info1['sex']=='2' ? '0' : $user_info1['sex'];
				$data_info['video'][$k]['auth'] = $user_info1['auth'];
				$data_info['video'][$k]['level'] = $this->get_level($user_info1['credits']);
				$data_info['video'][$k]['video_type'] = $v['is_off'];
				$data_info['video'][$k]['distance'] = strval($distance);

			}
		}
		if(empty($data))
		{
			$data_info1['list'] = '';
		}
		else
		{
			foreach ($data as $key => $value) {
				//$user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",
				$user_info = $this->User_Api_model->user_info(" user_id=$value[fans_id] ",
					'v_users',
					$this->config->item('catch'),
					//$value['user_id'].'_user_info',
					$value['fans_id'].'_user_info',
					$this->config->item('catch_time'));
				$data_info['list'][$key]['user_id']   = $user_info['user_id'];
				$data_info['list'][$key]['user_name'] = $user_info['user_name'];
				$data_info['list'][$key]['avatar']    = $user_info['image'];
				if(stristr($user_info['image'], 'http'))
				{
					$data_info['list'][$key]['avatar'] = $user_info['image'];
				}
				else
				{
					$data_info['list'][$key]['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
				}
				$data_info['list'][$key]['sex']       = $user_info['sex']=='2' ? '0' : $user_info['sex'];
				$data_info['list'][$key]['pre_sign']  = $user_info['pre_sign'];
				$data_info['list'][$key]['auth']      = $user_info['auth'];
				$data_info['list'][$key]['level']     = $this->get_level($user_info['credits']);
			}
		}
		
		$this->data_back($data_info, '0x000');
	}


	/**
		发送验证码
	**/
	public function message()
	{
		//发送号码
		$mobile     = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) :0;
		if(empty($mobile))
		{
			$this->data_back('手机号不能为空','0X002','fail');
		}
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$mobile))
		{   
			$this->data_back('手机号格式不对','0X003','fail');
     	}
     	$ch = curl_init();
		//验证码
        $code = rand(111111,999999);
		//短信内容
		$content = '【坐享其成】您的验证码是'.$code.'，有效时间5分钟，请不要告诉他人';
		$content = urlencode($content);
		//返回值格式 1为xml，2为json；默认为1
		$tag = 2;
	    $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile='.$mobile.'&content='.$content.'&tag='.$tag;
		//echo $url;
	    $header = array(
	        'apikey:1ba343c8f992b5c7857afe6ac8850d2f',
	    );
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $res = curl_exec($ch);
	    $count = $this->User_Api_model->comment_count(' COUNT(*) AS count '," user_name='$mobile' AND froms='tel'", 'v_users');
    	$param['user_name']     = $mobile;
		if(empty($count[0]['count']))
		{	
			$param['user_name']     = $mobile;
			$param['froms']         = 'tel';
			$param['register_time'] = time();
			$param['login_time'] = time();
			$param['openid']       =  $code;
			//$param['image'] = $headimgurl;
			//$param['sex'] = $sex;
			$data = $this->User_Api_model->register($param,'v_users');
		}
		else
		{
			$param['user_name'] = $mobile;
			$param['froms']     = 'tel';
			$param['openid']    = $code;
	 		$param['login_time'] = time();
			$data = $this->User_Api_model->comment_update(" user_name=$param[user_name] AND froms='tel'",$param,'v_users');
		}
	}


	public function search()
	{
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$fans_user_arr=array();
		if($user_id)
		{
			$fans_user_arr_2=$this->User_Api_model->getfans_list($user_id);
			foreach($fans_user_arr_2 as $v){
				$fans_user_arr[]=$v['fans_id'];
			}
		}
		//搜索用户名称
	    if($keyword)
	    {
	    	$where_video = " title LIKE '%$keyword%'";
	    	$where_user = " user_name LIKE '%$keyword%' AND status='0' ";
	    }
	    else
	    {
	      $where_video  = $where_user = " 1=1 ";
	    }

		if(!empty($type) && $type != 'all')
		{
			$where_user .= " AND is_".$type."='1' ";
		}
		$order_by = " displayorder,creates DESC,groupid ";
		$count_user = $this->User_Api_model->comment_count(' COUNT(*) AS count ',$where_user, 'v_users');
		if(!empty($count_user[0]['count']))
		{
			$data['user'] = $this->User_Api_model->comment_select("user_id,user_name,image,sex,pre_sign,credits,auth ",
				$where_user,'',$order_by,0,10,'v_users');
			if(!empty($data['user']))
			{
				foreach ($data['user'] as $key => $value) {
					
					if(stristr($value['image'], 'http'))
					{
						$data['user'][$key]['avatar'] = $value['image'];
					}
					else
					{
						$data['user'][$key]['avatar'] = $this->config->item('base_url') . $value['image'];
					}
					$data['user'][$key]['user_id'] = $value['user_id'];
					$data['user'][$key]['user_name'] = $value['user_name'];
					$data['user'][$key]['level'] = $this->get_level($value['credits']);
					if(in_array($value['user_id'],$fans_user_arr) || $user_id == intval($value['user_id'])){
						$data['user'][$key]['is_follow'] = '1';
					}else{
						$data['user'][$key]['is_follow'] = '0';
					}
				}
			}
			else
			{
				$data['user'] = array();
			}
		}
		else
		{
			$data['user'] = array();
		}
	    $page_num =10;
	    $count = $this->User_model->get_count($where_video,'v_video');
		if(!empty($count['count']) && !$type)
	    {
			$page_num = 10;
		    $start = ($page-1)*$page_num;
		    $data['video'] = $this->User_Api_model->comment_select(' video_id,start_time,image,title,video_name,user_id,ip,views,report,praise,types,socket_info ',$where_video,'','',$start,$page_num,'v_video');
		    if(!empty($data['video']))
			{
				foreach ($data['video'] as $key => $value) {
					
					if(stristr($value['image'], 'http'))
					{
						$data['video'][$key]['image'] = $value['image'];
					}
					else
					{
						$data['video'][$key]['image'] = $this->config->item('base_url') . $value['image'];
					}
					
					$data['video'][$key]['rtmp'] = $this->get_rtmp($value['video_name']);
					//if(stristr($value['video_name'],'rtmp://'))
					//{
					//	$data['video'][$key]['rtmp'] = $value['video_name'];
					//}else
					//{
					//	if($this->config->item('rtmp_flg') == 0)
					//	{
					//		$data[$key]['rtmp'] = 'rtmp://42.121.193.231/hls/'.$value['video_name'];
					//	}
					//	elseif($this->config->item('rtmp_flg') == 1)
					//	{
					//		$auth_key = $this->get_auth($value['video_name']);
					//		$data[$key]['rtmp'] = 'rtmp://video.etjourney.com/etjourney/'.$value['video_name'].'?auth_key='.$auth_key;
					//	}
					//	$data['video'][$key]['rtmp'] = 'rtmp://42.121.193.231/hls/'.$value['video_name'];
					//}
					$user_info = $this->User_Api_model->user_info(" user_id=$value[user_id] ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
					$data['video'][$key]['user_name'] = $user_info['user_name'];
					$data['video'][$key]['sex'] = $user_info['sex'];
					if(stristr($user_info['image'], 'http'))
					{
						$data['video'][$key]['avatar'] = $user_info['image'];
					}
					else
					{
						$data['video'][$key]['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
					}
					if(!empty($data['video'][$key]['ip']))
					{
						$data['video'][$key]['ipinfo'] = $this->common->GetIpLookup($value['ip']);
					}
					else
					{
						$data['video'][$key]['ipinfo'] =  "";
					}
				}
			}
			else
			{
				$data['video'] = array();
			}
		}else
		{
			$data['video'] = array();
		}
		$this->data_back($data,'0X000');
	}

	/**
	增加预播
		http://video.dhdyz.com/api/advance_video?title=123&time=222&user_id=23&image=111
		title 标题、 time  时间戳    files  数组集合   图片     user_id   用户id
	**/
	public function advance_video()
	{
		$title = isset($_REQUEST['title']) ? trim(addslashes($_REQUEST['title'])) :'';
		$time  = isset($_REQUEST['time']) ? intval(trim($_REQUEST['time'])) :'';
		if(isset($_FILES['files']))
			$image = $this->uploadimg->upload_image($_FILES['files'],'advance');
		//$image = '';
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :'';

		if(empty($user_id) || empty($title) || empty($time) || empty($image))//&& !$image)
			$this->data_back('参数为空','0X011','fail');
			$data = array(
				'title'       => $title,
				'user_id'     => $user_id,
				'adv_time'    => $time,
				'image'       => $image);
		$info = $this->User_Api_model->insert_string($data,'v_video_advance');
		if(empty($info))
		{
			$this->data_back('增加预播失败','0X014','fail');
		}
		else
		{
			$this->data_back(array('adv_id'=>$info),'0X000');
		}
	}

	/**预播列表
		http://video.dhdyz.com/api/advance_list?user_id=2
	**/

	public function advance_list()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :'';
		$page    = isset($_REQUEST['page']) ? intval(trim($_REQUEST['page'])) :1;
		if(empty($user_id))
			$this->data_back('user_id 为空参数为空','0X011','fail');
		$start = ($page-1)*$this->config->item('page_num');
		$time = time()-86400;
		$where = "user_id=$user_id AND adv_time > $time";
		$info = $this->User_Api_model->advance_list('*',$where,'','adv_time DESC',$start,$this->config->item('page_num'),'v_video_advance');

		//echo $this->db->last_query();
		if(empty($info)) $this->data_back('您还没有预播','0X012','fail');
		foreach ($info as $key => $value) {
			if($value['adv_time']  < time())
			{	$info[$key]['stop'] = '1';
				$info[$key]['adv_time'] = '直播已结束';
			}
			else
			{
				$info[$key]['time']  = $value['adv_time'];
				$num = $value['adv_time'] - time();
				if($num > 300)
				{
					$info[$key]['click'] = '1';
				}
				else
				{
					$info[$key]['click'] = '0';
				}
				//echo $num;
				$day = floor($num/86400);
				$hour = floor(($num-86400*$day)/3600);
				//echo $num-86400*$day;
				$minute = floor(($num-86400*$day-3600*$hour)/60);
				//$second = floor((($num-86400*$day-3600*$hour)-60*$minute)%60);
				if($day < 10)
				{
					$day = '0'.$day;                                          
				}
				if($hour < 10)
				{
					$hour = '0'.$hour;
				}
				if($minute < 10)
				{
					$minute = '0'.$minute;
				}
				/*if($second < 10)
				{
					$second = '0'.$second;
				}*/

				$info[$key]['stop'] = '0';
				$info[$key]['adv_time'] = '距离直播还有'.$day.'天'.$hour.'小时'.$minute.'分钟';

			}
			if(stristr($value['image'], 'http'))
				{
					$info[$key]['image'] = $value['image'];
				}
				else
				{
					$info[$key]['image'] = !empty($value['image']) ? $this->config->item('base_url') . ltrim($value['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
				}

		}
		$this->data_back($info,'0X000');

	}

	/**
		删除预播
		http://video.dhdyz.com/api/advance_del?adv_id=33
	**/
	public function advance_del()
	{
		$adv_id = isset($_REQUEST['adv_id']) ? intval(trim($_REQUEST['adv_id'])) :'';
		if(empty($adv_id))
			$this->data_back('预播id为空','0X011','fail');
		$where = array('adv_id'=> $adv_id);
		$info = $this->User_Api_model->del($where,'v_video_advance');
		if(empty($info))
		{
			$this->data_back('异常处理','0X013','fail');
		}
		else
		{
			$this->data_back(array('adv_id'=>$adv_id),'0X000');
		}
	}


	public function share()
	{
		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		switch ($type) {
			case 'time':

				break;
			case 'day':
				# code...
				break;
				case 'week':
				# code...
				break;
				case 'month':
				# code...
				break; 
				case 'year':
				break;
				case 'video':

				break;
				case 'advance':
				# code...
				break;
			default:
				# code...
				break;
		}
		$this->data_back($data['info']= '分享成功','0X000');
	}

	public function data_back($info, $msg = '', $result = 'success')
	{
		$data_arr = array('result'=>$result, 'msg'=>$msg, 'info'=>$info);
        die(json_encode($data_arr));  
	}
	/******************************************************************/
	public function test()
	{
		$this->load->view('api/test');
	}

	public function push_sys()
	{	
		$time = time();
		$where = " adv_time > $time -600 ";
		//$where = " adv_time = 1458101069 ";
		$video_list = $this->User_Api_model->comment_select('user_id',$where,0,0,0,0,'v_video_advance',1);
		//var_dump($video_list);die;
		foreach ($video_list as $key => $value)
		{	$user_info = $this->User_Api_model->user_info(" user_id = $value[user_id]",'v_users');
			//给不在直播的用户推送通知
			if(empty($user_info['video_id']))
			{
				$device_list['user_name'] = $user_info['user_name'];
				$device_list['list']= $this->User_Api_model->comment_select(' device_id,type',"user_id= $value[user_id] AND login=0 ",'','','','','v_device',1);
				if(!empty($device_list))
				{
					$this->pushinfo($device_list,'advance');
				}
			}
		}
	}

	public function pushinfo($data= array(),$type1='')
	{
		$video_info = isset($data['video_info']) ? $data['video_info'] : '';
		if($type1 == 'advance')
		{
			$info = '还有5分钟就要直播了,赶快准备下把';
			$video_info['advance'] = 1;
		}
		elseif($type1 == 'show_start')
		{
			$info = '在直播 快来围观';
			$video_info['advance'] = 0;
		}
        $token1=$token2= $sep = $type_1 = $type_2 = '';
        for($i=0;$i<count($data['list']);$i++)
        {
        	if($data['list'][$i]['type'] == '1')
        	{
        		
        		$token1 .= $sep.$data['list'][$i]['device_id'];
        		$sep =',';
        		$type_1 = 1;
        		
        	}
        	elseif($data['list'][$i]['type'] == '2')
        	{

        		$token2 .= $sep.$data['list'][$i]['device_id'];
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
	        $aps['alert'] = $data['user_name'] . $info ;
	        $aps['sound'] = '';
	        $aps['content-available'] = 1;
	        $aps['video_info'] = $video_info;
	        $payload['aps'] = $aps;
	        $params['production_mode'] = false;
	        $params['payload'] = $payload;
	        $params['device_tokens'] = $token1 ;
	        $post_body = json_encode($params);
			$sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
			$array = $this->http_post_data($url . $sign, $post_body);

    	}
    	if($type_2 == 2){
			$app_master_secret = $this->config->item('app_master_secret_android');
		    $params['appkey'] = $this->config->item('youmeng_apikey_android');
	        $payload['display_type'] = "notification";
	        $payload['body'] = array('title'=> '坐享其成','ticker'=>$data['user_name'] . '在直播','text'=> $data['user_name'] . $info,'sound'=>'','video_info'=>$video_info);
	        $params['payload'] = $payload;
	        $params['device_tokens']= $token2 ;
	        $post_body = json_encode($params);
			$sign = MD5("POST" . $urlForSign . $post_body . $app_master_secret);
			$array = $this->http_post_data($url . $sign, $post_body);
    	}
	}
	/**
		关闭关注者的推送
	**/
	public function close_push()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :0;
		$close_id = isset($_REQUEST['close_id']) ? intval(trim($_REQUEST['close_id'])) :0;
		$type = isset($_REQUEST['type']) ? trim(htmlspecialchars($_REQUEST['type'])) : '';
		if($type == 'all')
		{
			//$where = array('fans_id'=>$user_id);
			$where = array('user_id'=>$user_id);
			$data  =  array('status'=>'1');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			//echo $this->db->last_query();
			$this->data_back('关闭成功','0X000');
		}
		else
		{	
			//$where = array('fans_id'=>$user_id,'user_id' => $close_id);
			$where = array('user_id'=>$user_id,'fans_id' => $close_id);
			$data  =  array('status'=>'1');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			$this->data_back('关闭成功','0X000');

		}
	}
	/**
		打开关注者的推送
	**/
	public function open_push()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) :0;
		$close_id = isset($_REQUEST['close_id']) ? intval(trim($_REQUEST['close_id'])) :0;
		$type = isset($_REQUEST['type']) ? trim(htmlspecialchars($_REQUEST['type'])) : '';
		if($type == 'all')
		{
			//$where = array('fans_id'=>$user_id);
			$where = array('user_id'=>$user_id);
			$data  =  array('status'=>'0');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			if(empty($info))
			{
				$this->data_back('异常处理','0X013','fail');
			}
			else
			{
				$this->data_back('关闭成功','0X000');
			}
		}
		else
		{	
			//$where = array('fans_id'=>$user_id,'user_id' => $close_id);
			$where = array('user_id'=>$user_id,'fans_id' => $close_id);
			$data  =  array('status'=>'0');
			$info = $this->User_Api_model->update_watch($where,$data,'v_follow');
			if(empty($info))
			{
				$this->data_back('异常处理','0X013','fail');
			}
			else
			{
				$this->data_back('关闭成功','0X000');
			}

		}
	}

	/**
		播主添加商品
	 **/
	public function add_goods()
	{
		$user_id = isset($_REQUEST['user_id']) ? intval(trim($_REQUEST['user_id'])) : 0;
		$video_id  = isset($_REQUEST['video_id']) ? intval(trim($_REQUEST['video_id'])) : 0;
		$goods_name  = isset($_REQUEST['goods_name']) ? trim($_REQUEST['goods_name']) : '';
		$price  = isset($_REQUEST['price']) ? floatval(trim($_REQUEST['price'])) : 0;
		$goods_number  = isset($_REQUEST['goods_number']) ? intval(trim($_REQUEST['goods_number'])) : 1;
		//参数验证
		if(empty($user_id) || empty($video_id) || empty($goods_name) || empty($price) || empty($goods_number))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//该用户是否已在当前直播添加过商品
		//$where = array('video_id'=>$video_id,'user_id'=>$user_id,'is_delete'=>0);
		//$count = $this->User_Api_model->count_all($where,'v_goods');
		//if($count)
		//{
			//$this->data_back('本次直播已添加过商品','0X011','fail');
		//}
		//添加商品
		$goods  =  array(
						'goods_name'  =>$goods_name,
						'shop_price'  =>$price,
						'goods_number'=>$goods_number,
						'video_id'    =>$video_id,
						'user_id'     =>$user_id,
						'add_time'    =>time()
						);
		$goods_id = $this->User_Api_model->insert_string($goods,'v_goods');
		if($goods_id)
		{
			$data['goods_id'] = strval($goods_id);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back('商品添加失败！','0X020','fail');
		}
	}
	
	/**
		查看直播商品信息
	 **/
	public function video_goods()
	{
		$video_id  = isset($_REQUEST['video_id']) ? intval(trim($_REQUEST['video_id'])) : 0;
		//参数验证
		if(empty($video_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//直播是否已结束
		$video = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if(empty($video))
		{
			//$this->data_back('直播已结束','0X011','fail');
		}
		//直播内商品信息
		$select = " * ";
		$where = " video_id='$video_id' ";
		$order_by = " goods_id DESC ";
		//$goods_info = $this->User_Api_model->select_string($where,'','',0,1,'v_goods');
		$goods_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,1,'v_goods');
		if(empty($goods_info))
		{
			$this->data_back(array(),'0X014','fail');
		}
		else
		{
			foreach ($goods_info as $key => $value)
			{
				$data[$key]['goods_id']    = $value['goods_id'];
				$data[$key]['goods_name']  = $value['goods_name'];
				$data[$key]['price']       = $value['shop_price'];
				$data[$key]['goods_number']= $value['goods_number'];
				//获取已支付商品数量
				$select = " COALESCE(SUM(g.goods_number),0) AS pay_number ";
				$table  = " v_order_info o, v_order_goods g ";
				$where  = " o.order_id=g.order_id AND o.video_id=$video_id AND g.goods_id=$value[goods_id] AND o.order_status<>4 AND o.pay_status=1 ";
				$count  = $this->User_Api_model->comment_count($select,$where,$table);
				$data[$key]['pay_number'] = $count[0]['pay_number'];
			}
			$this->data_back($data, '0x000');  //返回数据
		}
	}
	/**
		提交订单
	 **/
	public function add_order()
	{
		$goods_id  = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
		$goods_number  = isset($_REQUEST['goods_number']) ? intval($_REQUEST['goods_number']) : 0;
		$video_id  = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$consignee   = isset($_REQUEST['consignee']) ? trim($_REQUEST['consignee']) : '';
		$address   = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '';
		$mobile   = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
		$note   = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';
		//参数验证
		if(empty($goods_id) || empty($goods_number) || empty($video_id) || empty($user_id) || empty($consignee) || empty($mobile) || empty($address))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		//获取商品信息
		$select = " g.goods_id,g.goods_name,g.goods_number,g.shop_price,g.add_time,g.video_id,g.user_id,g.is_delete,u.user_id,u.user_name ";
		$where  = " g.goods_id='$goods_id' AND g.user_id=u.user_id ";
		$table  = " v_goods g, v_users u ";
		$goods_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,$table);
		if($goods_info)
		{
			//检查商品库存
			if($goods_info[0]['goods_number'] < $goods_number)
			{
				$this->data_back('商品库存不足','0X021','fail');
			}
			//检查商品所在直播间是否关闭
			$video_info = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
			if(empty($video_info))
			{
				//$this->data_back('商品已下架','0X022','fail');
			}
			//获取买家用户信息
			$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",
					'v_users',
					$this->config->item('catch'),
					$user_id.'_user_info',
					$this->config->item('catch_time'));
			if(empty($user_info))
			{
				$this->data_back('用户不存在','0X022','fail');
			}
			//计算订单总金额
			$amount = $goods_info[0]['shop_price'] * $goods_number;
			$order_sn = $this->get_order_sn();
			//生成订单信息
			$order  =  array(
							'order_sn'    =>$order_sn,
							'user_id_buy' =>$user_id,
							'user_id_buy_name' =>$user_info['user_name'],
							'user_id_sell' =>$goods_info[0]['user_id'],
							'user_id_sell_name' =>$goods_info[0]['user_name'],
							'consignee'   =>$consignee,
							'address'     =>$address,
							'mobile'      =>$mobile,
							'goods_amount'=>$amount,
							'order_amount'=>$amount,
							'video_id'    =>$video_id,
							'goods_all_num' =>$goods_number,
							'commont'     =>$note,
							'add_time'    =>time()
							);
			$order_id = $this->User_Api_model->insert_string($order,'v_order_info');
			if($order_id)
			{
				//生成订单商品信息
				$order_goods  =  array(
							'order_id'    =>$order_id,
							'goods_id' =>$goods_id,
							'goods_name' =>$goods_info[0]['goods_name'],
							'goods_number' =>$goods_number,
							'goods_price' =>$goods_info[0]['shop_price'],
							'goods_sum'   =>$amount
							);
				$rec_id = $this->User_Api_model->insert_string($order_goods,'v_order_goods');
				/* 更新商品库存信息 */
				$this->User_Api_model->update_goods($goods_id,$goods_number);
				//返回订单成功信息
				$data['order_id'] = $order_id;
				$data['order_info'] = $goods_info[0]['goods_name'];
				$data['order_sn'] = $order_sn;
				$data['notify_url'] = $this->config->item('alipay_notify');
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back('订单提交失败','0X022','fail');
			}
		}
		else
		{
			$this->data_back('商品已下架','0X023','fail');
		}
	}
	/**
		当地人信息
	 **/
	public function local_info()
	{
		$type  = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$location = isset($_REQUEST['location']) ? trim($_REQUEST['location']) : '';
		switch ($type) {
			//导游
			case 'guide':
				$type = 'is_guide';
				break;
			//地陪
			case 'attendant':
				$type = 'is_attendant';
				break;
			//司机
			case 'driver':
				$type = 'is_driver';
				break;
			//商户
			case 'merchant':
				$type = 'is_merchant';
				break;
			default:
				$type = '';
				break;
		}
		if(!$type || !$user_id)
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data['userlist'] = array();
		$data['videolist'] = array();
		//验证是否有用户
		$count = $this->User_Api_model->count_all(array($type=>'1'),'v_users');
		if(empty($count))
		{
			$this->data_back($data, '0x000');
		}
		//获取直播列表
		$select = " v.*,u.user_name,u.image as avatar,u.sex,u.auth ";
		//$where  = " v.user_id=u.user_id AND v.is_off=0 AND u.groupid='$groupid' ";
		$where  = " v.user_id=u.user_id AND u.$type='1' ";
		$table  = " v_video v, v_users u ";
		$order_by = " is_off,from_unixtime(start_time,'%Y%m%d') DESC,views DESC ";
		//是否显示录播
		if($this->config->item('record_status'))
		{
			$where  .= " AND v.is_off<2 ";
		}
		else
		{
			$where  .= " AND v.is_off=0 ";
		}
		//首页只显示2个视频，显示更多（page>1）时每页10个
		if($page > 1)
		{
			$page = $page - 1;
			$page_num = 10;
			$user_hidden = true;
		}
		else
		{
			$user_hidden = false;
			$page_num = 2;
		}
	    $start = ($page-1)*$page_num;
		$video_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,$start,$page_num,$table);
		if($video_info)
		{
			foreach($video_info as $key=>$value)
			{
				$distance = "";
				if($location && $value['location'])
				{
					$lct1 = explode(",",$location);
					$lct2 = explode(",",$value['location']);
					$lat1 = $lct1[0];
					$lng1 = $lct1[1];
					$lat2 = $lct2[0];
					$lng2 = $lct2[1];
					$distance = $this->GetDistance($lat1,$lng1, $lat2,$lng2, 2);
				}
				if($value['is_off'] == '1')
				{
					$data['videolist'][$key]['rtmp'] = $this->get_rec($value['video_name'],$value['push_type']);
				}
				else
				{
					$data['videolist'][$key]['rtmp'] = $this->get_rtmp($value['video_name']);
				}
				if(stristr($value['image'], 'http'))
				{
					$data['videolist'][$key]['image'] = $value['image'];
				}
				else
				{
					$data['videolist'][$key]['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
				}
				
				$data['videolist'][$key]['ipinfo'] = $value['address'];
				//if(!empty($value['ip']))
				//{
				//	$data['videolist'][$key]['ipinfo'] = $this->common->GetIpLookup($value['ip']);
				//}
				//else
				//{
				//	$data['videolist'][$key]['ipinfo'] =  "";
				//}
				$data['videolist'][$key]['video_id'] =  $value['video_id'];
				$data['videolist'][$key]['title'] =  $value['title'];
				$data['videolist'][$key]['start_time'] =  $value['start_time'];
				$data['videolist'][$key]['report'] =  $value['report'];
				$data['videolist'][$key]['praise'] =  $value['praise'];
				$data['videolist'][$key]['user_id'] =  $value['user_id'];
				$data['videolist'][$key]['user_name'] =  $value['user_name'];
				$data['videolist'][$key]['views'] =  $value['views'];
				$data['videolist'][$key]['video_name'] =  $value['video_name'];
				$data['videolist'][$key]['socket_info'] =  $value['socket_info'];
				$data['videolist'][$key]['share_replay_path'] =  "http://api.etjourney.com/temp_video/tmpvideo.html?video_id={$value['video_id']}";
				$data['videolist'][$key]['video_dec']=$value['user_name'].'在'.$value['address'].'的精彩直播'.$value['title'].',世界那么大赶快来看看!';


				if(stristr($value['avatar'], 'http'))
				{
					$data['videolist'][$key]['avatar'] = $value['avatar'];
				}
				else
				{
					$data['videolist'][$key]['avatar'] = $this->config->item('base_url')  . ltrim($value['avatar'],'.');
				}
				$data['videolist'][$key]['sex'] = $value['sex'] == '2' ? '0' : $value['sex'];
				$data['videolist'][$key]['auth'] = $value['auth'];
				$data['videolist'][$key]['distance'] = strval($distance);
				$data['videolist'][$key]['video_type'] = strval($value['is_off']);
			}
		}
		//首页显示用户列表
		if(!$user_hidden)
		{
			//获取热门推荐用户列表
			$select = " u.*,COALESCE(f.fans_id,0) AS is_follow ";
			$where  = " u.$type='1' ";
			$table  = " v_users AS u LEFT JOIN v_follow AS f ON u.user_id=f.fans_id  AND f.user_id='$user_id' ";
			$order_by = " displayorder,u.user_id DESC ";
			$user_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,20,$table);
			if($user_info)
			{
				foreach($user_info as $key=>$value)
				{
					$data['userlist'][$key]['user_id']   = $value['user_id'];
					$data['userlist'][$key]['user_name']   = $value['user_name'];
					$data['userlist'][$key]['avatar']    = $value['image'];
					if(stristr($value['image'], 'http'))
					{
						$data['userlist'][$key]['avatar'] = $value['image'];
					}
					else
					{
						$data['userlist'][$key]['avatar'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					}
					$data['userlist'][$key]['sex']       = $value['sex'];
					$data['userlist'][$key]['pre_sign']  = $value['pre_sign'];
					$data['userlist'][$key]['user_id']   = $value['user_id'];
					$data['userlist'][$key]['is_follow'] = empty($value['is_follow']) ? '0' : '1'; 
					$data['userlist'][$key]['auth']      = $value['auth'];
					$data['userlist'][$key]['level']     = $this->get_level($value['credits']);
				}
			}
		}
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
		添加地址信息
	 **/
	public function add_address()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$consignee   = isset($_REQUEST['consignee']) ? trim($_REQUEST['consignee']) : '';
		$mobile   = isset($_REQUEST['mobile']) ? trim($_REQUEST['mobile']) : '';
		$zipcode   = isset($_REQUEST['zipcode']) ? trim($_REQUEST['zipcode']) : '';
		$area   = isset($_REQUEST['area']) ? trim($_REQUEST['area']) : '';
		$address   = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '';
		$type   = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'insert';
		//参数验证
		if(empty($user_id) || empty($consignee) || empty($mobile) || empty($address))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$user_info = $this->User_Api_model->user_info(array('user_id'=>$user_id),'v_users');
		if($user_info)
		{
			$param = array(
						'user_id'   => $user_id,
						'consignee' => $consignee,
						'mobile'    => $mobile,
						'zipcode'   => $zipcode,
						'area'		=> $area,
						'address'	=> $address
						);
			//查询是否已有该用户地址信息
			$address_info = $this->User_Api_model->comment_select('address_id'," user_id=$user_id ",'','',0,1,'v_user_address');
			if(empty($address_info))
			{
				$data['address_id'] = $this->User_Api_model->insert_string($param,'v_user_address');
				$this->data_back('添加成功', '0x000');  //返回数据
			}
			else
			{
				$data['address_id'] = $this->User_Api_model->comment_update(array('user_id'=>$user_id),$param,'v_user_address');
				$this->data_back('修改成功', '0x000');  //返回数据
			}
		}
		else
		{
			$this->data_back('用户不存在','0X022','fail');
		}
	}
	/**
		获取地址信息
	 **/
	public function get_address()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		//参数验证
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$data = $this->User_Api_model->comment_select('address_id,consignee,mobile,zipcode,area,address'," user_id=$user_id ",'','',0,1,'v_user_address');
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x014','fail');
		}
	}
	
	/**
		获取banner图信息
	 **/
	public function get_banner()
	{
		$data = $this->User_Api_model->comment_select('title,image_url,link_url'," status=0 ",'','displayorder',0,10,'v_banner');
		if($data)
		{
			foreach($data as $key => $value)
			{
				if(!stristr($value['image_url'], 'http'))
				{
					$data[$key]['image_url'] = $this->config->item('base_url'). ltrim($value['image_url'],'.');
				}
			}
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("返回数据为空", '0x014','fail');
		}
	}
	
	/**
		翻译
	 **/
	public function translate()
	{
		$content   = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		$from  = isset($_REQUEST['from']) ? trim($_REQUEST['from']) : 'auto';
		$to   = isset($_REQUEST['to']) ? trim($_REQUEST['to']) : '';
		//参数验证
		if(empty($content) || empty($to))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		if($from == $to)
		{
			$info['trans_result'] = $content;
			$this->data_back($info, '0x000');  //返回数据
		}
		switch ($to) {
			case 'ar':
				$to = 'ara';
				break;
			case 'fr':
				$to = 'fra';
				break;
			case 'ko':
				$to = 'kor';
				break;
			case 'ja':
				$to = 'jp';
				break;
			case 'es':
				$to = 'spa';
				break;
			default:
				break;
		}
		$info['trans_result'] = $content;
		$result = $this->baidu_translate($content,$from,$to);
		if($result)
		{
			$info['trans_result'] = $result;
		}
		$this->data_back($info, '0x000');  //返回数据
	}
	
	/**
		我的评价
	 **/
	public function my_evaluate()
	{
		$user_id   = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$page    = isset($_REQUEST['page'])    ? intval($_REQUEST['page'])    : 1;
		//参数验证
		if(empty($user_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$page_num = 200;
	    $start = ($page-1)*$page_num;
		$res = $this->User_Api_model->comment_select('user_id_buy,evaluate,star_num'," user_id_sell=$user_id AND evaluate<>'' ",'',' add_time DESC ',$start,$page_num,'v_order_info');
		if($res)
		{
			$star_sum = 0;
			$count = 0;
			foreach($res as $key=>$value)
			{
				if($value['user_id_buy'])
				{
					$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id_buy']),'v_users');
					if(stristr($user_info['image'], 'http'))
					{
						$image = $user_info['image'];
					}
					else
					{
						$image = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
					}
					$data['eva_list'][] = array(
												'user_id_buy' => $value['user_id_buy'],
												'evaluate' => $value['evaluate'],
												'star_num' => $value['star_num'],
												'user_name' => $user_info['user_name'],
												'image' => $image
										);
				}
				$star_sum = $star_sum + intval($value['star_num']);
				$count = $count + 1;
			}
			//总评分
			$data['total'] = strval(round($star_sum/$count,1));
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("暂无评价", '0x014','fail');
		}
	}
	
	/**
		签到
	 **/
	public function checkin()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		if(empty($user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$user_info = $this->User_Api_model->comment_select(" credits,checkin_time,checkin_status "," user_id=$user_id ","","",0,1,"v_users");
		if($user_info)
		{
			$time = time();
			$credits = intval($user_info[0]['credits']);
			$checkin_status = intval($user_info[0]['checkin_status']);
			$chk_time = intval($user_info[0]['checkin_time']);
			$now_date = date("Ymd",$time);
			$chk_date = date("Ymd",$chk_time);
			$overdue = $time - $chk_time;
			if($now_date == $chk_date)
			{
				$this->data_back("您今天已经签过到了", '0x014','fail');
			}
			if($overdue > 172800)
			{
				$credits = $credits + 1;
				$checkin_status = 1;
			}
			else
			{
				if($checkin_status == 0)
				{
					$credits = $credits + 1;
					$checkin_status = 2;
				}
				elseif($checkin_status == 1)
				{
					$credits = $credits + 2;
					$checkin_status = $checkin_status + 1;
				}
				elseif($checkin_status > 1)
				{
					$credits = $credits + 3;
				}
			}
			$param  = array(
							'credits' => $credits,
							'checkin_time' => $time,
							'checkin_status' => strval($checkin_status)
						);
			$this->User_Api_model->comment_update(" user_id=$user_id ",$param,'v_users');
			$data['credits'] = strval($credits);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("用户不存在", '0x014','fail');
		}
	}
	
	/**
		我的认证
	 **/
	public function my_auth()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$language  = isset($_REQUEST['language'])  ? trim($_REQUEST['language'])  : '';
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		switch ($language) {
			case 'ja':
				$view     = 'ガイド';
				$local    = '地元ガイド';
				$driver   = '運転手';
				$business = '商戸';
				$to = 'jp';
				break;
			case 'ko':
				$view     = '가이드';
				$local    = '현지 가이드';
				$driver   = '기사';
				$business = '상점';
				$to = 'kor';
				break;
			case 'th':
				$view     = 'ไกด์ ';
				$local    = 'ไกด์ท้องถิ่น ';
				$driver   = 'คนขับรถ ';
				$business = 'ผู้ค้า';
				$to = 'th';
				break;
			case 'en':
				$view     = 'Guides';
				$local    = 'Locals';
				$driver   = 'Drivers';
				$business = 'Merchants';
				$to = 'en';
				break;
			case 'zh-Hant':
				$view     = '導遊';
				$local    = '地陪';
				$driver   = '司機';
				$business = '商戶';
				$to = 'zh';
				break;
			default:
				$view     = '认证导游';
				$local    = '认证导游';
				$driver   = '认证导游';
				$business = '认证商户';
				$to = 'zh';
				break;
			}
		$data = array();
		//认证导游信息
		$res = $this->User_Api_model->comment_select(" id_range,id_view_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_views');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$view,'range'=>$range,'verify'=>$res[0]['id_view_status']);
		}
		//认证地陪信息
		$res = $this->User_Api_model->comment_select(" id_range,id_local_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_locals');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$local,'range'=>$range,'verify'=>$res[0]['id_local_status']);
		}
		//认证司机信息
		$res = $this->User_Api_model->comment_select(" id_range,id_driver_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_drivers');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$driver,'range'=>$range,'verify'=>$res[0]['id_driver_status']);
		}
		//认证商户信息
		$res = $this->User_Api_model->comment_select(" id_range,id_business_status "," user_id=$user_id ","","id_auth_time DESC",0,1,'v_auth_business');
		if($res)
		{
			if($to != 'zh')
			{
				$range = $this->baidu_translate($res[0]['id_range'],'auto',$to);
			}
			else
			{
				$range = $res[0]['id_range'];
			}
			$data[] = array('auth_type'=>$business,'range'=>$range,'verify'=>$res[0]['id_business_status']);
		}
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("没有认证信息", '0x014','fail');
		}
	}
	
	/**
		删除个人录播
	 **/
	public function del_video()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$video_id  = isset($_REQUEST['video_id'])  ? intval($_REQUEST['video_id'])  : 0;
		if(!$user_id || !$video_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		//查询
		$res = $this->User_Api_model->comment_select(" video_id "," video_id=$video_id AND user_id=$user_id AND is_off=1 ","","",0,1,'v_video');
		if($res)
		{
			$this->User_Api_model->comment_update(array('video_id'=>$video_id,'user_id'=>$user_id),array('is_off'=>2,'del_time'=>time()),'v_video');
			$this->data_back('删除成功', '0x000');  //返回数据
		}
		else
		{
			$this->data_back("视频不存在", '0x014','fail');
		}
	}
	
	/**
		推荐认证用户列表
	 **/
	public function recommend()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$type     = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
		$time     = time();
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		if($type == 'all')
		{
	    	//周排行
	    	$select   = " DISTINCT user_id ";
	    	$where    = " dateline > ($time-604800) ";
	    	$table    = 'v_rank_month';
	    	$count    = $this->User_Api_model->rank_count($select,$where,$table);
	    	$count    = count($count);
	    	$select   = ' user_id, SUM(score) AS count ';
	    	$group_by = 'user_id';
	    	$order_by = 'SUM(score) DESC';
	    	$info = 'rank_week';
			if(empty($count))
			{
				$this->data_back('无排行榜', '0x000');  //返回数据
			}
			$page     = 1;
			$page_num = 20;
			$start = ($page-1)*$page_num;
			$data= $this->User_Api_model->rank_day_list($select,$where,$group_by,$order_by,$start,$page_num,$table,$this->config->item('catch'),$info,$this->config->item('catch_time'));
			if(!empty($data))
			{
				foreach ($data as $key => $value) {
					$user_info = $this->User_Api_model->user_info(array('user_id'=>$value['user_id']),'v_users');
					$data[$key]['user_name'] = $user_info['user_name'];
					if(stristr($user_info['image'], 'http'))
					{
						$data[$key]['image'] = $user_info['image'];
					}
					else
					{
						$data[$key]['image'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
					}
					//用户等级认证信息
					$data[$key]['sex'] = $user_info['sex'];
					$data[$key]['pre_sign'] = $user_info['pre_sign'];
					$data[$key]['auth'] = $user_info['auth'];
					$data[$key]['level'] = $this->get_level($user_info['auth']);
					$select = "  COUNT(*) AS count ";
					$where        = " user_id=$user_id AND fans_id=$value[user_id] ";
					$count_follow = $this->User_Api_model->comment_count($select,$where,'v_follow');
					$data[$key]['is_follow'] = intval($count_follow[0]['count']) > 0 ? '1' : '0';
					
				}
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back("没有推荐", '0x014','fail');
			}
		}
		else
		{
			//获取热门推荐用户列表
			$select = " u.*,COALESCE(f.fans_id,0) AS is_follow ";
			$where  = " u.auth='1' ";
			$table  = " v_users AS u LEFT JOIN v_follow AS f ON u.user_id=f.fans_id  AND f.user_id='$user_id' ";
			$order_by = " displayorder,creates DESC,groupid ";
			if(!empty($type) && $type != 'all')
			{
				$where .= " AND u.is_".$type."='1' ";
			}
			$user_info = $this->User_Api_model->comment_select($select,$where,'',$order_by,0,20,$table);
			if($user_info)
			{
				foreach($user_info as $key=>$value)
				{
					$data[$key]['user_id']   = $value['user_id'];
					$data[$key]['user_name']   = $value['user_name'];
					$data[$key]['avatar']    = $value['image'];
					if(stristr($value['image'], 'http'))
					{
						$data[$key]['avatar'] = $value['image'];
					}
					else
					{
						$data[$key]['avatar'] = $this->config->item('base_url') . ltrim($value['image'],'.');
					}
					$data[$key]['sex']       = $value['sex'];
					$data[$key]['pre_sign']  = $value['pre_sign'];
					$data[$key]['user_id']   = $value['user_id'];
					$data[$key]['is_follow'] = empty($value['is_follow']) ? '0' : '1'; 
					$data[$key]['auth']      = $value['auth'];
					$data[$key]['level']     = $this->get_level($value['credits']);
				}
				
				$this->data_back($data, '0x000');  //返回数据
			}
			else
			{
				$this->data_back("没有推荐", '0x014','fail');
			}
		}
	}
	
	/**
		引导图广告
	 **/
	public function adv()
	{
		//获取广告链接和图片链接
		$data['image_url'] = 'http://meinv.aawap.net/dealimages/img/o/s146477t14418295491.jpg';
		$data['link_url'] = 'http://bbs.aawap.net/bbs/thread-60389-1-1.html';
		if($data)
		{
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("没有广告", '0x014','fail');
		}
	}
	
	/**
		退出登录
	 **/
	public function logout()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$device_id  = isset($_REQUEST['device_id'])  ? trim($_REQUEST['device_id'])  : 0;
		if(!$user_id)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$this->User_Api_model->comment_update(array('user_id'=>$user_id,'login'=>0),array('login'=>1),'v_device');
		$this->data_back("退出成功",'0x000','success');  //返回数据
	}
	
	/**
		获取地理位置信息
	 **/
	public function get_position()
	{
		$user_id  = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id'])  : 0;
		$dimension  = isset($_REQUEST['dimension'])  ? trim($_REQUEST['dimension'])  : 0;
		$longitude  = isset($_REQUEST['longitude'])  ? trim($_REQUEST['longitude'])  : 0;
		if(!$user_id || !$dimension || !$longitude)
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$time = time();
		$data['country'] = '';
		$data['city'] = '';
		$data['show_status'] = '1';
		$data['msg_info'] = '';
		if($dimension == $longitude && $dimension == '0.0')
		{
			$data['msg_info'] = 'Hi，境外才能开播哦！';
			$data['msg_code'] = '1';
			$this->data_back($data, '0x000');  //返回数据
		}
		$position = $this->geocoder($dimension,$longitude);
		//var_dump($position);
		if($position)
		{
			$address = '';
			$country = isset($position['result']['addressComponent']['country']) ? $position['result']['addressComponent']['country'] : '';
			$province = isset($position['result']['addressComponent']['province']) ? $position['result']['addressComponent']['province'] : '';
			$city = isset($position['result']['addressComponent']['city']) ? $position['result']['addressComponent']['city'] : '';
			$description = isset($position['result']['sematic_description']) ? $position['result']['sematic_description'] : '';
			if($city)
			{
				$address = $city;
			}elseif($province){
				$address = $province;
			}elseif($country){
				$address = $country;
			}elseif($description){
				$address = $description;
			}
			if($address)
			{
				$this->User_Api_model->comment_update(array('user_id'=>$user_id),array('address'=>$address),'v_users');
			}
			$data['country'] = $country;
			$data['city']    = $city;
		}
		$ban = $this->User_Api_model->comment_select('user_id'," user_id=$user_id AND ban_out_time>$time AND statue='0' AND is_show='1' ",'','',0,1,'v_ban_user');
		if($ban)
		{
			$data['msg_info'] = '非常抱歉，您已被禁播！';
		}
		else
		{
			$user_info = $this->User_Api_model->comment_select('user_name,auth,status,is_merchant'," user_id=$user_id AND status='0' ",'','',0,1,'v_users');
			if($user_info && $user_info[0]['status']=='0')
			{
				if($user_info[0]['is_merchant'])
				{
					$data['show_status'] = '0';
				}
				else
				{
					if($position)
					{
						if($position['status']==0 && empty($country))
						{
							$data['show_status'] = '0';
						}
						else
						{
							if($country != '中国')
							{
								$data['show_status'] = '0';
							}
							else
							{
								if(strstr($province,'香港') || strstr($province,'台湾') || strstr($province,'澳门'))
								{
									$data['show_status'] = '0';
								}
								else
								{
									$data['msg_info'] = 'Hi，境外才能开播哦！';
									$data['msg_code'] = '1';
								}
							}
						}
					}
					else
					{
						$data['msg_info'] = 'Hi，境外才能开播哦！';
						$data['msg_code'] = '1';
					}
				}
			}
		}
		$this->data_back($data, '0x000');  //返回数据
	}
	/**
	获取视频信息
	**/
	public function defriend()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$to_user_id  = isset($_REQUEST['to_user_id']) ? intval($_REQUEST['to_user_id']) : 0;
		if(empty($user_id) || empty($to_user_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$data['defriend'] = '0';
		$param = array(
			'user_id' => $to_user_id,
			'defriend'  => $user_id
		);
		$count = $this->User_Api_model->count_all($param,'v_defriend');
		if($count)
		{
			$data['defriend'] = '1';
		}
		$this->data_back($data, '0x000');  //返回数据
	}
	
	/**
	获取微信支付信息
	**/
	/*public function get_wxpay()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$order_id  = isset($_REQUEST['order_id'])  ? intval($_REQUEST['order_id'])  : 0;
		if(empty($user_id) || empty($order_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " order_id,order_sn,user_id_buy,order_status,pay_id,pay_status,order_amount ";
		$where = " order_id=$order_id ";
		$order_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,'v_order_info');
		if($order_info)
		{
			if($order_info[0]['user_id_buy'] != $user_id || $order_info[0]['order_status'] != '0' || $order_info[0]['pay_status'] != '0')
			{
				$this->data_back("订单无法支付", '0x011','fail');
			}
		}
		else
		{
			$this->data_back("订单不存在", '0x011','fail');
		}
		$order_sn = $order_info[0]['order_sn'];
		$amount = floatval($order_info[0]['order_amount']) * 100;
		$body = '用户'.$user_id.'的订单';
		require_once("./application/third_party/wxpay/WxPayApp.php");
		$ip = $this->common->real_ip();
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		$unifiedOrder->setParameter("body",$body);//商品描述
		$unifiedOrder->setParameter("out_trade_no",$order_sn);//商户订单号 
		$unifiedOrder->setParameter("spbill_create_ip",$ip);//终端IP
		$unifiedOrder->setParameter("total_fee",$amount);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","APP");//交易类型
		
		$data['prepayid'] = $unifiedOrder->getPrepayId();
		if($data['prepayid'])
		{
			$app_pay = new Wxpay_client_pub();
			$data['appid'] = WxPayConf_pub::APPID;
			$data['partnerid'] = WxPayConf_pub::MCHID;
			$data['package'] = 'Sign=WXPay';
			$data['noncestr'] = $app_pay->createNoncestr();
			$data['timestamp'] = strval(time());
			$app_pay->setParameter("appid",$data['appid']);
			$app_pay->setParameter("partnerid",$data['partnerid']);
			$app_pay->setParameter("prepayid",$data['prepayid']);
			$app_pay->setParameter("package",$data['package']);
			$app_pay->setParameter("noncestr",$data['noncestr']);
			$app_pay->setParameter("timestamp",$data['timestamp']);
			$data['sign'] = $app_pay->getSign($app_pay->parameters);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("微信预付单号获取失败", '0x011','fail');
		}
		//echo 'prepay_id='.$prepay_id.'<br />';
		
	}*/
	
	/**
	获取上/下一条录播
	**/
	public function get_record()
	{
		$type = isset($_REQUEST['type']) ? $_REQUEST['type']    : 'next';
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		//echo $video_id;
		if(empty($video_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " * ";
		if($type=='next')
		{
			$where  = " video_id>$video_id AND is_off=1";
			$order = " video_id ";
		}
		else
		{
			$where  = " video_id<$video_id AND is_off=1 ";
			$order = " video_id DESC ";
		}
		$data = $this->User_Api_model->comment_select($select,$where,'',$order,0,1,'v_video');
		if($data)
		{
			$data_info = $data[0];
			$data_info['rtmp'] = $this->get_rec($data_info['video_name'],$data_info['push_type']);
			$data_info['image'] = $this->config->item('base_url') . ltrim($data_info['image'],'.');
			$user_id = intval($data_info['user_id']);
			$user_info1 = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$data_info['user_id'].'_user_info',$this->config->item('catch_time'));
			$data_info['user_name'] =  $user_info1['user_name'];
			if(stristr($user_info1['image'], 'http'))
			{
				$data_info['avatar'] = $user_info1['image'];
			}
			else
			{
				$data_info['avatar'] = $this->config->item('base_url')  . ltrim($user_info1['image'],'.');
			}
			$data_info['sex'] = $user_info1['sex']=='2' ? '0' : $user_info1['sex'];
			$data_info['auth'] = $user_info1['auth'];
			$data_info['level'] = $this->get_level($user_info1['credits']);
			$data_info['video_type'] = $data_info['is_off'];
			$this->data_back($data_info, '0x000');
		}
		else
		{
			$this->data_back("没有视频", '0x011','fail');
		}
	}
	/**
	观看直播发表评论
	**/
	public function comment()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$video_id = isset($_REQUEST['video_id']) ? intval($_REQUEST['video_id']) : 0;
		$content  = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		if(empty($user_id) || empty($video_id) || empty($content))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$user_info = $this->User_Api_model->user_info(array('user_id'=>$user_id),'v_users');
		if($user_info)
		{
			if(stristr($user_info['image'], 'http'))
			{
				$avatar = $user_info['image'];
			}
			else
			{
				$avatar = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'tmp/avatar.png';
			}
		}
		else
		{
			$this->data_back("发送失败", '0x011','fail');
		}
		$video = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if($video)
		{
			$socket = explode(':',$video['socket_info']);
			if($socket)
			{
				$ip   = $socket[0];
				$port = $socket[1];
			}
		}
		else
		{
			$this->data_back("发送失败", '0x011','fail');
		}
		$message =array('user_id'=>$user_id,'user_name'=>$user_info['user_name'],'image'=>$avatar,'content'=>$content,'auth'=>$user_info['auth']);
		$this->send_msg($message,$ip,$port);
		$this->data_back('发送成功', '0x000');
	}
	
	/**
	 * [send_msg  发送socket信息]
	 */
	function send_msg($message=array(),$ip,$port)
	{
		$service_port = intval($port);
		$address = $ip;
		if($message)
		{
			$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket < 0) 
			{
				return false;
			}
			$result = @socket_connect($socket, $address, $service_port);
			if ($result < 0)
			{
				return false;
			}
			$in = array(
				'user_id'=>$message['user_id'],
				'user_name'=>$message['user_name'],
				'type'     =>'comment',
				'content'  =>$message['content'],
				'avatar'   =>$message['image'],
				'auth'     =>$message['auth']
				);
			$in = json_encode($in)."\n";
			@socket_write($socket, $in, strlen($in));
			@socket_close($socket);
			return true;
		}
		else
		{
			return false;
		}
		
	}
	/**
	 *  根据坐标点获取地理位置信息（百度接口）
	 **/
	function geocoder($dimension, $longitude) 
	{
		$result = '';
		//$res = $this->http_post_data($this->config->item('baidu_map_url'), $param);
		//$url = $this->config->item('baidu_map_url').'?ak='.$this->config->item('baidu_key').'&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
		$url = $this->config->item('baidu_map_url').'?ak=GU1rfcDjP4ZEZVZQo3UBA3jH8Q2x2RKY&callback=renderReverse&location='.$dimension.','.$longitude.'&output=json';
		$result = file_get_contents($url);
		//echo $result;
		$result = substr($result,29);
		$result = substr($result, 0, -1);
		$fileType = mb_detect_encoding($result , array('UTF-8','GBK','LATIN1','BIG5')) ;
		//echo 'filetype='.$fileType;
		if( $fileType != 'UTF-8'){
			$result = mb_convert_encoding($result ,'utf-8' , $fileType);
		}
		//echo $result;
		//$result = json_decode($result,true);
		$result = json_decode($result,true);
		$errorinfo = json_last_error();
		//echo $errorinfo;

		return $result;
	}
	/**
	**/
	function http_post_data($url, $data_string) {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
            'Content-Type: application/json; charset=utf-8',  
            'Content-Length: ' . strlen($data_string))  
        );  
        ob_start();  
        curl_exec($ch);  
        $return_content = ob_get_contents();  
        ob_end_clean();  
  
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
        return array($return_code, $return_content);  
    } 

	public  function test_sql()
	{
		$table = $mobile     = isset($_REQUEST['t']) ? trim($_REQUEST['t']) :'';
		$h = date('H',time());
		$time = time();
		if($table == 'v_rank_day'){$h = date('H',time());}
		elseif($table == 'v_rank_month'){$h = date('d',time());}
		elseif($table == 'v_rank_year'){$h = date('n',time());}
		elseif($table == 'v_users')
		{

		}
		for($i=0;$i<20;$i++)
		{
			if($table == 'v_users')
			{
				$param['user_name'] = '佩恩小罗罗'.rand(1,900);
				$param['image'] = '/public/images/'.rand(1,9).'.jpg';
				$data = $this->User_Api_model->register($param,$table);
			
			}
			else
			{
				$param['user_id'] = rand(21,100);
				$param['time'] = $h;
				$param['score'] = rand(1,50);
				$param['dateline'] = $time;
				$data = $this->User_Api_model->register($param,$table);

			}
		}
			
		$this->data_back("ok",'0x000','success');  //返回数据
	}

	public function version()
	{

		$this->data_back("2.2.0",'0x000','success');  //返回数据
	}

	public function response()
	{
		$this->data_back("反馈意见",'0x000','success');  //返回数据
	}

	public function up()
	{
		var_dump($_FILES);die;
	}


	public function com_public()
	{
	}



	public function test_url()
	{
		
		$data['url'][0]['info'] = '测试连接';
		$data['url'][0]['src']  = 'http://54101.mmb.cn/wap/Column.do?columnId=12579';
		$h =  date('H',time());
		if($h==18)
		{
			if(stristr($data['url'][0]['src'],'54101'))
			{
				$data['url'][0]['src'] = 'http://baidu.com';
			}
		}
		$this->load->view('api/api_info',$data);
		
	}

	/**
	查询排行榜位置
	**/
	public function rank_user()
	{
		$type    = isset($_REQUEST['type'])       ? intval($_REQUEST['type'])       : 1;
		$user_id = isset($_REQUEST['user_id'])    ? intval($_REQUEST['user_id'])    : '';
		$time = time();
		if($type == 1)
		{
			$where = " dateline > ($time-3600) AND user_id=$user_id ";
			$table = 'v_rank_day';
			$name = "时榜";
			$info['url'] = '/index.php/index/share?type=time';
		}
		elseif($type == 2)
		{
			$where = " dateline > ($time-86400) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "日榜";
			$info['url'] = '/index.php/index/share?type=day';
		}
		elseif($type == 3)
		{

			$where = " dateline > ($time-604800) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "周榜";
			$info['url'] = '/index.php/index/share?type=week';
		}
		elseif($type == 4)
		{
			$where = " dateline > ($time-2592000) AND user_id=$user_id ";
			$table = 'v_rank_month';
			$name = "月榜";
			$info['url'] = '/index.php/index/share?type=month';
		}
		elseif($type == 5)
		{
			$where = " dateline > ($time-31536000) AND user_id=$user_id ";
			$table = 'v_rank_year';
			$name = "年榜";
			$info['url'] = '/index.php/index/share?type=year';
		}
		$data = $this->User_Api_model->rank_user($where,$table);
		if(empty($data))
		{
			$info['title'] = "坐享其成红人榜最新出炉，快来围观！";
		}
		else
		{
			if($data['pm'] > 100)
			{
				$info['title'] = "坐享其成红人榜最新出炉，快来围观！";
			}
			else
			{
				
				$info['title'] = "我在坐享其成直播".$name."中获得了第".$data['pm']."名,快来围观！";
			}
		}
		$user_info = $this->User_Api_model->user_info(" user_id= $user_id",'v_users');
		if(stristr($user_info['image'], 'http'))
		{
			$info['avatar'] = $user_info['image'];
		}
		else
		{
			$info['avatar'] = !empty($user_info['image']) ? $this->config->item('base_url') . ltrim($user_info['image'],'.') :  $this->config->item('base_url') . 'public/images/120.png';
		}
		$this->data_back( $info,'0x000','success');  //返回数据
	}


	/**

	**/
	public function thrid_party()
	{
		$data[0] = 'weixin';
		$data[1] = 'weibo';
		$data[2] = 'qq';
		$data[3] = 'twitter';
		$data[4] = 'face_book';
		$this->data_back($data,'0x000','success');  //返回数据
	}


	/** 判断流是否存在**/
	public function rtmp_status()
	{
		$video_id    = isset($_REQUEST['video_id'])       ? intval($_REQUEST['video_id'])       : '';
		$where = " video_id=$video_id AND is_off=0 ";
    	$count = $this->User_model->get_count($where,'v_video');
    	if($count['count'])
    	{
    		$data = '1';
    	}
    	else
    	{
    		$data = '0';
    	}
    	$this->data_back($data,'0x000','success');  //返回数据
	}
	
	/** 添加僵尸看客**/
	public function add_zombies($video_id=0)
	{
		$limit = 0;
		$value = $this->User_Api_model->get_video_info(0,'v_video',$video_id);
		if($value)
		{
			$time = time() - intval($value['start_time']);
			if($time > 25 && $time < 60)
			{
				$limit = rand(0,3);
			}elseif($time < 120){
				$limit = rand(0,2);
			}elseif($time < 300){
				$limit = rand(0,1);
			}
		}
		if($limit)
		{
			$this->User_Api_model->update_video_zombie($video_id,'v_users',$limit);
			$this->User_Api_model->update_views(array('video_id'=>$video_id),'v_video',$limit);
		}
	}
	/**
	获取视频信息
	**/
	public function getvideoinfo($user_id)
	{
		$result = array();
		$value = $this->User_Api_model->get_video_info($user_id,'v_video');
		if($value)
		{
			if(!empty($value['ip']))
			{
				$result['ipinfo'] = $this->common->GetIpLookup($value['ip']);
			}
			else
			{
				$result['ipinfo'] =  "";
			}
			
			$result['rtmp'] = $this->get_rtmp($value['video_name']);
			
			$user_info = $this->User_Api_model->user_info(" user_id=$user_id ",'v_users',$this->config->item('catch'),$value['user_id'].'_user_info',$this->config->item('catch_time'));
	
			if(stristr($user_info['image'], 'http'))
			{
				$result['avatar'] = $user_info['image'];
			}
			else
			{
				$result['avatar'] = $this->config->item('base_url') . ltrim($user_info['image'],'.');
			}
			$result['user_name'] = $user_info['user_name'];
			$result['sex'] = $user_info['sex'];
			$result['image'] = $this->config->item('base_url') . ltrim($value['image'],'.');
			$result['user_id'] = $user_id;
			$result['video_id'] = $value['video_id'];
			$result['video_name'] = $value['video_name'];
			$result['socket_info'] = $value['socket_info'];
		}
		return $result;
	}
	
	/**
	获取视频流地址信息
	**/
	function get_rtmp($video_name)
	{
		$result = '';
		if($video_name)
		{
			if(stristr($video_name,'rtmp://'))
			{
				$result = $video_name;
			}
			else
			{
				if($this->config->item('rtmp_flg') == 0)
				{
					$result = 'rtmp://42.121.193.231/hls/'.$video_name;
				}
				elseif($this->config->item('rtmp_flg') == 1)
				{
					$auth_key = $this->get_auth($video_name);
					$result = $this->config->item('rtmp_url').$video_name.'?auth_key='.$auth_key;
				}
				elseif($this->config->item('rtmp_flg') == 2)
				{
					$auth_key = $this->get_auth($video_name);
					$result = $this->config->item('rtmp_uc_url').$video_name;
				}
			}
		}
		return $result;
	}
	
	/**
	获取录播地址信息
	**/
	function get_rec($video_name,$push_type)
	{
		$result = '';
		if($video_name)
		{
			if($push_type == '0')
			{
				$result = $this->config->item('record_url').$video_name.'.m3u8';
			}
			elseif($push_type == '1')
			{
				$result = $this->config->item('record_uc_url').$video_name.'.m3u8';
			}
		}
		return $result;
	}
	
	/**
	获取SOCKET信息
	**/
	function get_socket($video_id,$user_id,$video_name)
	{
		$result = array();
		$result['socket_ip'] = '114.55.65.195';
		$result['socket_port'] = '2120';
		$socket_on = '';
		if($video_id)
		{
			$where = 'video_id='.$video_id;
			$res = $this->User_Api_model->get_socket_list('*',$where,'','',0,1,'v_socket');
			if($res)
			{
				$result['socket_ip'] = $res[0]['socket_ip'];
				$result['socket_port'] = $res[0]['socket_port'];
				return $result;
			}
			$where = ' video_id=0 AND user_id=0 ';
			$order = ' open_status DESC,id ';
			$socket = $this->User_Api_model->get_socket_list(' * ',$where,'',$order,0,3,'v_socket');
			if($socket)
			{
				foreach($socket as $value)
				{
					if($value['open_status'] == '1' && !$socket_on)
					{
						if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$value['socket_port']."' | grep -v grep"))
						{
							exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$value['socket_port']. ' > /dev/null 2>&1 &');
						}
						$this->User_Api_model->comment_update(array('id'=>$value['id']),array('video_id'=>$video_id,'user_id'=>$user_id,'start_time'=>time(),'video_name'=>$video_name),'v_socket');
						$result['socket_ip'] = $value['socket_ip'];
						$result['socket_port'] = $value['socket_port'];
						$socket_on = '1';
					}
					if($value['open_status'] == '0')
					{
						if(!exec("ps aux | grep '".$this->config->item('socket_server')." ".$value['socket_port']."' | grep -v grep"))
						{
							exec('nohup php '.$this->config->item('socket_path').$this->config->item('socket_server').' '.$value['socket_port']. ' > /dev/null 2>&1 &');
							$this->User_Api_model->comment_update(array('id'=>$value['id']),array('open_status'=>1),'v_socket');
						}
						if(!$socket_on)
						{
							$this->User_Api_model->comment_update(array('id'=>$value['id']),array('video_id'=>$video_id,'user_id'=>$user_id,'open_status'=>1,'start_time'=>time(),'video_name'=>$video_name),'v_socket');
							$result['socket_ip'] = $value['socket_ip'];
							$result['socket_port'] = $value['socket_port'];
							$socket_on = '1';
						}
						//else
						//{
						//	$this->User_Api_model->comment_update(array('id'=>$value['id']),array('open_status'=>1),'v_socket');
						//}
					}
				}
			}
		}
		
		return $result;
		
	}
	/**
	* 得到新订单号
	* @return  string
	*/
	function get_order_sn()
	{
		/* 选择一个随机的方案 */
		mt_srand((double) microtime() * 1000000);
	
		return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}
	 
	/** 
	* 计算两组经纬度坐标 之间的距离 
	* params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km); 
	* return m or km 
	*/ 
	function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2) 
	{ 
		$earth_radius =6378.137;//地球半径 
		$pi=3.1415926;
		$radLat1 = $lat1 * $pi / 180.0; 
		$radLat2 = $lat2 * $pi / 180.0; 
		$a = $radLat1 - $radLat2; 
		$b = ($lng1 * $pi / 180.0) - ($lng2 * $pi / 180.0); 
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2))); 
		$s = $s * $earth_radius; 
		$s = round($s * 1000); 
		if ($len_type > 1) 
		{ 
		$s /= 1000; 
		} 
		return round($s, $decimal); 
	} 
	
	/**
	* 积分等级
	* @return  string
	*/
	function get_level($credits=0)
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
	
//翻译入口
function baidu_translate($query, $from, $to)
{
	$res = '';
    $args = array(
        'q' => $query,
        'appid' => $this->config->item('baidu_trans_id'),
        'salt' => rand(10000,99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = $this->buildSign($query, $this->config->item('baidu_trans_id'), $args['salt'], $this->config->item('baidu_trans_key'));
    $ret = $this->call($this->config->item('baidu_trans_url'), $args);
    $ret = json_decode($ret, true);
	if($ret)
	{
		if($ret['trans_result'])
		{
			$res = $ret['trans_result']['0']['dst'];
		}
	}
    return $res;
}

//加密
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

//发起网络请求
function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array())
{/*{{{*/
    $ret = false;
    $i = 0; 
    while($ret === false) 
    {
        if($i > 1)
            break;
        if($i > 0) 
        {
            sleep(1);
        }
        $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ch = curl_init();
    if($method == "post") 
    {
        $data = $this->convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    else 
    {
        $data = $this->convert($args);
        if($data) 
        {
            if(stripos($url, "?") > 0) 
            {
                $url .= "&$data";
            }
            else 
            {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args))
    {
        foreach ($args as $key=>$val)
        {
            if (is_array($val))
            {
                foreach ($val as $k=>$v)
                {
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }
            else
            {
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/

	public function get_map_info(){
		set_time_limit(0);
		if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
			$select='v_video.video_id,v_video.user_id,v_video.start_time,
      location,v_users.user_name,v_users.image as user_image,all_address,push_type,
      views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
      socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
		}else{
			$select='v_video.video_id,v_video.user_id,
      	location,v_users.user_name,v_users.image as user_image,all_address,v_video.start_time,push_type,
      	views,v_video.praise,video_name,title,location,v_video.image as video_image,title,is_off,
      		socket_info,credits,sex,is_guide,is_attendant,is_driver,is_merchant,auth';
		}
		$data=$this->User_model->get_select_all($select,'is_off<2','v_video.user_id', 'ASC','v_video',1,'v_users',"v_video.user_id=v_users.user_id");
		//echo '<pre>';print_r($data);exit();
		if($data===FALSE){
			$this->data_back(array('result'=>'fail'));
		}else{
			if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
				foreach($data as $k=>$v){
					$data[$k]['level']=$this->get_level($v['credits']);
					if($v['is_off']==1){
						$data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
						$data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
						$data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
					}else{
						$data[$k]['path']=$this->get_rtmp($v['video_name']);
						$data[$k]['share_replay_path'] ="";
						$data[$k]['video_dec']='测试描述'.$v['title'];
					}
				}
			}else{
				foreach($data as $k=>$v){
					$data[$k]['level']=$this->get_level($v['credits']);
					if($v['is_off']==1){
						$data[$k]['path']=$this->get_rec($v['video_name'],$v['push_type']);
						$data[$k]['share_replay_path'] ="http://api.etjourney.com/temp_video/tmpvideo.html?video_id=$v[video_id]";
						$data[$k]['video_dec']=$v['user_name'].'在'.$v['all_address'].'的精彩直播'.$v['title'].',世界那么大赶快来看看!';
					}else{
						$data[$k]['path']=$this->get_rtmp($v['video_name']);
						$data[$k]['share_replay_path'] ="";
						$data[$k]['video_dec']='测试描述'.$v['title'];
					}
				}
			}
			$this->data_back($data,'0X000','success');
		}
	}

//advertisement
//0 不显示 1显示
	public function advertisement(){
		$arr=array('time'=>2,'image'=>'http://api.etjourney.com//public/images/thumb/banner/20160713.jpg','is_show'=>'1');
		if(stristr($_SERVER['HTTP_USER_AGENT'],'iphone')){
			$arr['image']="http://api.etjourney.com/public/images/advertisement/adver_ios.jpg?2";
		}else{
			$arr['image']="http://api.etjourney.com/public/images/advertisement/adver_andiord.jpg";

		}
		if($arr===false){
			$this->data_back(array('result'=>'fail'));
		}else{
			$this->data_back($arr);
		}
	}
	
	public function get_videourl(){
		$video_id  = isset($_REQUEST['video_id']) ? intval(trim($_REQUEST['video_id'])) : 0;
		//参数验证
		if(empty($video_id))
		{
			$this->data_back('参数为空','0X011','fail');
		}
		$video = $this->User_Api_model->get_video_info(0,'v_video',$video_id,'',2);
		if(empty($video))
		{
			$this->data_back('视频不存在','0X011','fail');
		}
		else
		{
			if($video['video_name'])
			{
				$rtmp = $this->get_rec($video['video_name'],$video['push_type']);
				echo '视频地址：'.$rtmp;
			}
			else
			{
				$this->data_back('视频不存在','0X011','fail');
			}
		}
	}

	/**
	获取微信支付信息
	**/
	public function get_wxpay()
	{
		/*$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$order_id  = isset($_REQUEST['order_id'])  ? intval($_REQUEST['order_id'])  : 0;
		if(empty($user_id) || empty($order_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " order_id,order_sn,user_id_buy,order_status,pay_id,pay_status,order_amount ";
		$where = " order_id=$order_id ";
		$order_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,'v_order_info');
		if($order_info)
		{
			if($order_info[0]['user_id_buy'] != $user_id || $order_info[0]['order_status'] != '0' || $order_info[0]['pay_status'] != '0')
			{
				$this->data_back("订单无法支付", '0x011','fail');
			}
		}
		else
		{
			$this->data_back("订单不存在", '0x011','fail');
		}
		$order_sn = $order_info[0]['order_sn'];
		$amount = floatval($order_info[0]['order_amount']) * 100;
		$body = '用户'.$user_id.'的订单';*/
		echo '111111';
		require_once("./application/third_party/wxpay/WxPay.php");
		$ip = $this->common->real_ip();
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		$unifiedOrder->setParameter("body",'测试商品');//商品描述
		$unifiedOrder->setParameter("out_trade_no",'20161214001');//商户订单号 
		$unifiedOrder->setParameter("spbill_create_ip",$ip);//终端IP
		$unifiedOrder->setParameter("total_fee",1);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
		
		//$url = $unifiedOrder->getCodeUrl();
		echo 'url='.$url;
		$url=urlencode($url);

	echo '<img  src="http://paysdk.weixin.qq.com/example/qrcode.php?data='.$url.'" style="width:150px;height:150px;"/>';
	
		
	}
/***支付宝测试demo
**/
public function zfbs(){
    $order_sn=$this->input->post('order_sn',TRUE);
    $datas['consignee']=$this->input->post('tel',TRUE);
    $datas['mobile']=$this->input->post('username',TRUE);
    $data['xj_zyx']=$this->input->post('xj_zyx',TRUE);
    $datas['card']=$this->input->post('cId',TRUE);
    $datas['goTime']=$this->input->post('goTime',TRUE);
    $datas['goFly']=$this->input->post('goFly',TRUE);
    $datas['comeTime']=$this->input->post('comeTime',TRUE);
    $datas['comeFly']=$this->input->post('comeFly',TRUE);

   // $where="order_sn=$order_sn";
    $rs= $this->User_model->update_one(array('order_sn'=>$order_sn),$datas,'v_h5_order');
   // echo $this->db->last_query();die;
    if($rs){
	    $where ="order_sn=$order_sn";
        $data['info']=$this->User_model->get_select_one($select='*',$where,'v_h5_order' );     

    }
       
      // echo $this->db->last_query();
       
    //   echo "<pre>";
    //   print_r($data);
    //   echo "</pre>";

       $data['url']=base_url('Api1/zfbs2');
	$this->load->view('zfbpay.by/index',$data);
}
public function zfbs2(){
   

	$this->load->view('zfbpay.by/alipayapi',$data);


}
//支付宝返回页面
public function zfb_fanhui(){
$zf_zt=$this->input->get('trade_status',TRUE);
$order_sn=$this->input->get('out_trade_no',TRUE);
$where="order_sn=$order_sn";
$datav['info']=$this->User_model->get_select_one('*',$where,'v_h5_order');
$datav['url']=base_url("h5info_temp_zyx/xj");
$data['zf_state']='1';
if($zf_zt='TRADE_SUCCESS'){
	  $this->User_model->update_one($where,$data,'v_h5_order');

}

	$this->load->view('zxqcny1/zfb_fanhui',$datav);
}
/**
	支付宝测试
	**/
	public function alipayh5()
	{
		$this->config->load('alipay_h5', TRUE);
		require_once("./application/third_party/alipay/alipay_submit_h5.class.php");
		$id= '20161216'.rand(1000,9999);
		$submit = new AlipaySubmit($this->config->item('alipay_h5'));
		$body = $submit->buildRequestForm(array(
			//'app_id'            => '2016041401295932',//固定值 无需改动
			//'method'           => 'alipay.trade.wap.pay',//支付宝合作者身份(PID)
			//'charset'         => $this->config->item('input_charset', 'alipayPc'),//支付宝合作者身份(PID)
			//'sign_type'         => $this->config->item('sign_type', 'alipayPc'),//支付宝合作者身份(PID)
			//'notify_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/alipay_notify',//服务器异步通知页面地址
			//'return_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/alipay_return',//页面跳转同步通知地址
			//'out_trade_no'      => $id,//订单号
			//'subject'	        => '测试商品',//商品名称
			//'total_amount'         => 0.01,//交易金额 精确到小数点后两位
			//'body'              => '测试商品',//商品描述
			//'timestamp'              => '2017-06-30 16:46:53',//商品描述
			//
			//'product_code'      => 'QUICK_WAP_WAY'
			'service'           => 'alipay.wap.create.direct.pay.by.user',//固定值 无需改动
			'partner'           => '2088911908052351',//支付宝合作者身份(APPID)
			'seller_id'         => '2088911908052351',//支付宝合作者身份(APPID)
			'payment_type'      => '1',//固定值 无需改动
			'notify_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/h5_notify',//服务器异步通知页面地址
			'return_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/alipay_return',//页面跳转同步通知地址
			'out_trade_no'      => $id,//订单号
			'subject'	        => '测试商品',//商品名称
			'total_fee'         => 0.01,//交易金额 精确到小数点后两位
			'body'              => '测试商品',//商品描述
			'show_url'          => 'http://'.$_SERVER['HTTP_HOST'].'/bussell/app_shop_list',//商品展示网址
			'anti_phishing_key' => '',
			'exter_invoke_ip'   => '',
			'_input_charset'    => $this->config->item('input_charset', 'alipay_h5')//参数编码字符集UTF-8
		),'get','确认支付','');
		echo $body;
	}
	/**
	h5回调
	**/
	public function h5_notify(){
	$this->config->load('alipay', TRUE);
		include_once './application/third_party/alipay/alipay_notify.class.php';
        $alipayNotify = new AlipayNotify($this->config->item('alipay'));
        $verify_result = $alipayNotify->verifyNotify();
        
        //商户订单号
        $out_trade_no = $this->input->post('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->post('trade_no');
        //交易状态
        $trade_status = $this->input->post('trade_status'); 
        //交易时间
        $notify_time = strtotime($this->input->post('notify_time'));
        $total_fee = $this->input->post('total_fee');
        //防止重复提交
        
	
		//以log文件形式记录回调信息
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','out_trade_no='.$out_trade_no.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','trade_no='.$trade_no.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','trade_status='.$trade_status.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','total_fee='.$total_fee.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','notify_time='.$notify_time.PHP_EOL,FILE_APPEND);
        if($verify_result)
        {
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','验证通过'.PHP_EOL,FILE_APPEND);
        }else{
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','验证失败'.PHP_EOL,FILE_APPEND);
        }


        if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
 
            //根据订单号（out_trade_no）更新订单状态
            echo "success";
 
            
        }else {
            //订单失败   
            echo "fail";
        }
    }
	/**
	支付宝测试
	**/
	public function alipay()
	{
		$this->config->load('alipay', TRUE);
		require_once("./application/third_party/alipay/alipay_submit.class.php");
		$id= '20161216';
		$submit = new AlipaySubmit($this->config->item('alipay'));
		$body = $submit->buildRequestForm(array(
			'service'           => 'create_direct_pay_by_user',//固定值 无需改动
			'partner'           => $this->config->item('partner', 'alipay'),//支付宝合作者身份(PID)
			'seller_id'         => $this->config->item('partner', 'alipay'),//支付宝合作者身份(PID)
			'payment_type'      => '1',//固定值 无需改动
			'notify_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/alipay_notify',//服务器异步通知页面地址
			'return_url'        => 'http://'.$_SERVER['HTTP_HOST'].'/api1/alipay_return',//页面跳转同步通知地址
			'out_trade_no'      => $id,//订单号
			'subject'	        => '测试商品',//商品名称
			'total_fee'         => 0.02,//交易金额 精确到小数点后两位
			'body'              => '测试商品',//商品描述
			'show_url'          => 'http://'.$_SERVER['HTTP_HOST'].'/bussell/app_shop_list',//商品展示网址
			'anti_phishing_key' => '',
			'exter_invoke_ip'   => '',
			'_input_charset'    => $this->config->item('input_charset', 'alipay')//参数编码字符集UTF-8
		),'get','确认支付','');
		echo $body;
	}
	/**
	支付宝测试回调
	**/
	public function alipay_notify()
	{
		$this->config->load('alipay', TRUE);
		include_once './application/third_party/alipay/alipay_notify.class.php';
        $alipayNotify = new AlipayNotify($this->config->item('alipay'));
        $verify_result = $alipayNotify->verifyNotify();
        
        //商户订单号
        $out_trade_no = $this->input->post('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->post('trade_no');
        //交易状态
        $trade_status = $this->input->post('trade_status'); 
        //交易时间
        $notify_time = strtotime($this->input->post('notify_time'));
        $total_fee = $this->input->post('total_fee');
        //防止重复提交
        
	
		//以log文件形式记录回调信息
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','out_trade_no='.$out_trade_no.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','trade_no='.$trade_no.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','trade_status='.$trade_status.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','total_fee='.$total_fee.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','notify_time='.$notify_time.PHP_EOL,FILE_APPEND);
        if($verify_result)
        {
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','验证通过'.PHP_EOL,FILE_APPEND);
        }else{
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_notify.log','验证失败'.PHP_EOL,FILE_APPEND);
        }


        if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
 
            //根据订单号（out_trade_no）更新订单状态
            echo "success";
 
            
        }else {
            //订单失败   
            echo "fail";
        }
    }
	/**
	支付宝测试
	**/
	public function alipay_return($method)
	{
		echo $_SERVER['DOCUMENT_ROOT'];
		echo 'method='.$method;
		echo '<pre>';
		print_r($this->input->get());exit();
		$this->config->load('alipay', TRUE);
		include_once './application/third_party/alipay/alipay_notify.class.php';
        $alipayNotify = new AlipayNotify($this->config->item('alipay'));
        $verify_result = $alipayNotify->verifyReturn();
        
        //商户订单号
        $out_trade_no = $this->input->get('out_trade_no');
        //支付宝交易号
        $trade_no = $this->input->get('trade_no');
        //交易状态
        $trade_status = $this->input->get('trade_status'); 
        //交易时间
        $notify_time = strtotime($this->input->get('notify_time'));
        //防止重复提交
        
	
		//以log文件形式记录回调信息
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_return.log','out_trade_no='.$out_trade_no.PHP_EOL,FILE_APPEND);
		file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_return.log','trade_status='.$trade_status.PHP_EOL,FILE_APPEND);
        if($verify_result)
        {
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_return.log','验证通过'.PHP_EOL,FILE_APPEND);
        }else{
			file_put_contents('/opt/nginx/html/zxqc/logfile/alipay_return.log','验证失败'.PHP_EOL,FILE_APPEND);
        }


        if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
 
            //根据订单号（out_trade_no）更新订单状态
            echo '交易成功';
            
 
            
        }else {
            //订单失败   
            echo '交易失败';
            
        }
    }
//测试新疆微信支付
    public function get_wxpay1_xj($order_sn)
	{
		//$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		//$order_id  = isset($_REQUEST['order_id'])  ? intval($_REQUEST['order_id'])  : 0;
		//$user_id = 1085;
		//$order_id = 1922;
		//if(empty($user_id) || empty($order_id))
	//	{
//			$this->data_back("参数为空", '0x011','fail');
//		}
	//	$order_sn$this->input->post('order_sn',TRUE);
		$select = " order_id,order_sn,user_id_buy,order_status,pay_id,pay_status,order_amount,goods_amount,front_amount,`from`,pid ";
		$where = " order_sn=$order_sn ";
		$order_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,'v_order_info');
		//echo "<pre>";
		//print_r($order_info);
		//echo "</pre>";die;
		if($order_info)
		{
			if($order_info[0]['order_sn'] != $order_sn || $order_info[0]['order_status'] != '0' || $order_info[0]['pay_status'] != '0')
			{
				$this->data_back("订单无法支付", '0x011','fail');
			}
		}
		else
		{
			$this->data_back("订单不存在", '0x011','fail');
		}
		$order_sn = $order_info[0]['order_sn'];
		if($order_info[0]['from']=='6'){
			//旅拍定金
			$amount = floatval($order_info[0]['front_amount']) * 100;
		}elseif($order_info[0]['from']=='7' && $order_info[0]['pid']!='0'){
			//旅拍尾款
			$amount = (floatval($order_info[0]['goods_amount'])-floatval($order_info[0]['front_amount'])) * 100;
		}else{
			$amount = floatval($order_info[0]['goods_amount']) * 100;
		}
		$body = '用户'.$user_id.'的订单';
		require_once("./application/third_party/wxpay/WxPayApp.php");
		$ip = $this->common->real_ip();
		echo $amount;
		
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		$unifiedOrder->setParameter("body",$body);//商品描述
		$unifiedOrder->setParameter("out_trade_no",$order_sn);//商户订单号 
		$unifiedOrder->setParameter("spbill_create_ip",$ip);//终端IP
		$unifiedOrder->setParameter("total_fee",$amount);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","APP");//交易类型
		
		$data['prepayid'] = $unifiedOrder->getPrepayId();
		if($data['prepayid'])
		{
			$app_pay = new Wxpay_client_pub();
			$data['appid'] = WxPayConf_pub::APPID;
			$data['partnerid'] = WxPayConf_pub::MCHID;
			$data['package'] = 'Sign=WXPay';
			$data['noncestr'] = $app_pay->createNoncestr();
			$data['timestamp'] = strval(time());
			$app_pay->setParameter("appid",$data['appid']);
			$app_pay->setParameter("partnerid",$data['partnerid']);
			$app_pay->setParameter("prepayid",$data['prepayid']);
			$app_pay->setParameter("package",$data['package']);
			$app_pay->setParameter("noncestr",$data['noncestr']);
			$app_pay->setParameter("timestamp",$data['timestamp']);
			$data['sign'] = $app_pay->getSign($app_pay->parameters);
		    $this->data_back($data, '0x000');  //返回数据
     
		}
		else
		{
			$this->data_back("微信预付单号获取失败", '0x011','fail');
		}
		//echo 'prepay_id='.$prepay_id.'<br />';
		
	}

	public function get_wxpay1()
	{
		$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
		$order_id  = isset($_REQUEST['order_id'])  ? intval($_REQUEST['order_id'])  : 0;
		$user_id = 1085;
		$order_id = 1922;
		if(empty($user_id) || empty($order_id))
		{
			$this->data_back("参数为空", '0x011','fail');
		}
		$select = " order_id,order_sn,user_id_buy,order_status,pay_id,pay_status,order_amount,goods_amount,front_amount,`from`,pid ";
		$where = " order_id=$order_id ";
		$order_info = $this->User_Api_model->comment_select($select,$where,'','',0,1,'v_order_info');
		if($order_info)
		{
			if($order_info[0]['user_id_buy'] != $user_id || $order_info[0]['order_status'] != '0' || $order_info[0]['pay_status'] != '0')
			{
				$this->data_back("订单无法支付", '0x011','fail');
			}
		}
		else
		{
			$this->data_back("订单不存在", '0x011','fail');
		}
		$order_sn = $order_info[0]['order_sn'];
		if($order_info[0]['from']=='6'){
			//旅拍定金
			$amount = floatval($order_info[0]['front_amount']) * 100;
		}elseif($order_info[0]['from']=='7' && $order_info[0]['pid']!='0'){
			//旅拍尾款
			$amount = (floatval($order_info[0]['goods_amount'])-floatval($order_info[0]['front_amount'])) * 100;
		}else{
			$amount = floatval($order_info[0]['goods_amount']) * 100;
		}
		$body = '用户'.$user_id.'的订单';
		require_once("./application/third_party/wxpay/WxPayApp.php");
		$ip = $this->common->real_ip();
		echo $amount;
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		
		$unifiedOrder->setParameter("body",$body);//商品描述
		$unifiedOrder->setParameter("out_trade_no",$order_sn);//商户订单号 
		$unifiedOrder->setParameter("spbill_create_ip",$ip);//终端IP
		$unifiedOrder->setParameter("total_fee",$amount);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","APP");//交易类型
		
		$data['prepayid'] = $unifiedOrder->getPrepayId();
		if($data['prepayid'])
		{
			$app_pay = new Wxpay_client_pub();
			$data['appid'] = WxPayConf_pub::APPID;
			$data['partnerid'] = WxPayConf_pub::MCHID;
			$data['package'] = 'Sign=WXPay';
			$data['noncestr'] = $app_pay->createNoncestr();
			$data['timestamp'] = strval(time());
			$app_pay->setParameter("appid",$data['appid']);
			$app_pay->setParameter("partnerid",$data['partnerid']);
			$app_pay->setParameter("prepayid",$data['prepayid']);
			$app_pay->setParameter("package",$data['package']);
			$app_pay->setParameter("noncestr",$data['noncestr']);
			$app_pay->setParameter("timestamp",$data['timestamp']);
			$data['sign'] = $app_pay->getSign($app_pay->parameters);
			$this->data_back($data, '0x000');  //返回数据
		}
		else
		{
			$this->data_back("微信预付单号获取失败", '0x011','fail');
		}
		//echo 'prepay_id='.$prepay_id.'<br />';
		
	}

	public function order_paid()
    {
		/* 取得支付编号 */
		$order_sn = 2016122744891;
		$trade_no = '722B04C4135E22AA671F44732779B95F';
		echo '11111';
		/* 获取订单信息 */
		$res = $this->User_Api_model->comment_select(' order_id,order_status,pay_status,user_id_sell,order_amount,pid,`from`,goods_amount,front_amount '," order_sn='$order_sn' ",'','',0,1,'v_order_info');
		
		if($res)
		{
			if($res[0]['order_status']=='0' && $res[0]['pay_status'] == '0')
			{
				$data = $res[0];
				$data['trade_no'] = $trade_no;
				$data['pay_name'] = 'wxpay';
				$data['pay_id'] = 2;
				/* 更新订单状态 */
				//print_r($data);exit();
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
	public function copy_image()
	{
		$res = $this->User_Api_model->comment_select(' image'," video_id=210 ",'','',0,1,'v_video');
		if($res)
		{
			$image = '/opt/nginx/html/zxqc'.$res[0]['image'];
			$exec = 'cp -p '.$image.' /opt/bin/bak/image/';
			echo $exec.'<br />';
			if(!exec($exec))
			{
				echo 'error';
			}
		}
	}
}