<?php

$appid='wx7af6c15d303c83e8';
$redirect_uri = urlencode ( 'http://api.etjourney.com/gd/Wx_api' );
$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
header("Location: $url");exit; 
