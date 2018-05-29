<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>我的订单</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv=X-UA-Compatible content="IE=Edge">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <script src="/public/car/js/jquery-1.10.1.min.js"></script>
    <link rel="stylesheet" href="/public/car/css/init.css?2017">
    <link rel="stylesheet" href="/public/car/css/index.css?2017041919119">
    <style>
        .nothing{text-align:center; color:#999; height:50px; line-height:50px; padding:10px auto; background:#fff;}
        .loading{background:url(/public/myshop/images/loading.jpg) center center no-repeat #fff; background-size:auto 20px; height:50px;}
    </style>
</head>
<body>
	<div class="warp">
<!--        <header class="header">-->
<!--            <a href="javascript:void(0)" class="i_back">返回</a>-->
<!--            我的订单-->
<!--        </header>-->
        <div class="order_list">
        <?php foreach($order_info as $k=>$v){?>
            <div class="dingdan">
                <h4>下单时间：<?php echo date('Y-m-d',$v['add_time'])?><span><?php echo $v['order_state']?></span></h4>
                <?php foreach($v['goods'] as $k1=>$v1){?>
                    <div class="car-pro">
                        <img class="car-p-img" src="<?php echo $v1['image_s']?>"/>
                        <h4 class="car-p-tit"><?php echo $v1['goods_name']?></h4>
                        <span class="car-pp">￥<?php echo $v1['goods_sum']?></span>
                        <label class="ding-num">x<?php echo $v1['goods_number']?></label>
                        <div class="clear"></div>
                    </div>
                <?php }?>
                <div class="dingdb">
                    <?php if($v['order_status']==1 || $v['order_status']==2 ){?>
                        <button class="order_sure" data-id="<?php echo $v['order_id']?>">确认收货</button>
                    <?php }elseif($v['order_status']==3){?>
                        <button data-id="<?php echo $v['order_id']?>">交易成功</button>
                    <?php }elseif($v['order_status']==4){?>
                        <button data-id="<?php echo $v['order_id']?>">交易关闭</button>
                    <?php }elseif($v['order_status']==0){?>
                        <button class="order_pay" data-id="<?php echo $v['order_id']?>">点击付款</button>
                    <?php }?>
                </div>
            </div>
        <?php }?>
        </div>
        <div class='loading' style="display: none"></div>
        <div class='nothing car-show-tell' style="display: none">没有更多了！</div>
    </div>
</body>
<div id='span'></div>
<script src="/public/auth/js/span.js"></script>
<script>
    var opts = {
        lines: 13, // 花瓣数目
        length: 8, // 花瓣长度
        width: 4, // 花瓣宽度
        radius: 12, // 花瓣距中心半径
        corners: 1, // 花瓣圆滑度 (0-1)
        rotate: 0, // 花瓣旋转角度
        direction: 1, // 花瓣旋转方向 1: 顺时针, -1: 逆时针
        color: '#45bdcf', // 花瓣颜色
        position: 'fixed',
        speed: 1, // 花瓣旋转速度
        trail: 60, // 花瓣旋转时的拖影(百分比)
        shadow: false, // 花瓣是否显示阴影
        hwaccel: false, //spinner 是否启用硬件加速及高速旋转
        className: 'spinner', // spinner css 样式名称
        zIndex: 2e9, // spinner的z轴 (默认是2000000000)
        top: '50%', // spinner 相对父容器Top定位 单位 px
        left: '50%'// spinner 相对父容器Left定位 单位 px
    };

    var spinner = new Spinner(opts);
    var target=document.getElementById("span");

    var page=1;
    var max_page='<?php echo $max_page?>'
    $(function(){
        function pay(json,order_id){
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                    document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
            }else{
                onBridgeReady(json,order_id);
            }
        }

        function onBridgeReady(json,order_id){
            //alert(json);
            json=JSON.parse(json);
            //alert(json);
            var appId=json['appId'];
            var timeStamp=json['timeStamp'];
            var nonceStr=json['nonceStr'];
            var package=json['package'];
            var signType=json['signType'];
            var paySign=json['paySign'];
            //	alert(appId);
            WeixinJSBridge.invoke('getBrandWCPayRequest', {
                    "appId" : appId,
                    "timeStamp":timeStamp,
                    "nonceStr" :nonceStr,
                    "package" :package,
                    "signType" :signType,
                    "paySign" : paySign
                },
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    if(res.err_msg == "get_brand_wcpay_request:ok" )
                    {
                        alert('success');
                        // location.href="http://api.etjourney.com/bussell/pay_succeed?type=1&call_url=1&order_id="+order_id;
                    }else{
                      //  alert('fail');
                        //location.href="http://api.etjourney.com/bussell/pay_fail?type=1&call_url=1&order_id="+order_id;
                    }
                }
            );
        }

        $('.order_list').on('click','.order_pay',function(){
            var order_id=$(this).attr('data-id')
            var pay_url='<?php echo $pay_url?>'
            spinner.spin(target);
            $.ajax({
                data:{"order_id":order_id},
                url:pay_url,
                type:"post",
                success:function(mess){
                   // alert(mess)
                    spinner.spin();
                    mess=JSON.parse(mess);
                    var order_id=mess['order_id'];
                    mess=JSON.parse(mess['json']);
                    pay(mess,order_id)
                }
            });
        })
        $('.order_list').on('click','.order_sure',function(){
            var order_id=$(this).attr('data-id')
            var sure_url='<?php echo $sure_url?>'
            if(confirm('请确认已收到商品！'))
            {
                $.ajax({
                    data:{"order_id":order_id},
                    url:sure_url,
                    type:"post",
                    success:function(mess){
                        window.location.reload()
                    }
                });
                $(this).text('交易完成')
            }
        })

        $(document).ready(function() {
            $(window).scroll(function() {
                //$(document).scrollTop() 垂直滚动的距离

                if ($(document).scrollTop() <= 0) {
                    // alert("0");
                }

                if ($(document).scrollTop() >= $(document).height()  - $(window).height())
                {

                    var url='<?php echo $more_url?>';
                    page++;
                    $('.loading').css('display','block');
                    //alert(page);
                    if(page>max_page)
                    {
                        $('.loading').css('display','none');
                        $('.nothing').css('display','block');
                    }
                    //加载
                    if(page<=max_page){
                        $.ajax({
                            data:{"page":page,"ajax":1},
                            url:url,
                            type:"post",
                            success:function(mess){
                                //   alert(mess);
                                mess=$.parseJSON(mess);
                                mess=mess['order_info']
                                var len=mess.length;
                                if(len > 0)
                                {
                                    for(var i=0;i<len;i++)
                                    {
                                        var add_date=mess[i]['add_date'];
                                        var order_state=mess[i]['order_state'];

                                        var order_status=mess[i]['order_status'];
                                        var order_id=mess[i]['order_id'];

                                        var str_first='<div class="dingdan"><h4>下单时间：'+add_date+'<span>'+order_state+'</span></h4>'
                                        var str_end='<div class="dingdb">'
                                        if(order_status==1 || order_status==2 )
                                        {
                                            str_end+='<button class="order_sure" data-id="'+order_id+'">确认收货</button>'
                                        }
                                        else if(order_status==0)
                                        {
                                            str_end+='<button class="order_pay" data-id="'+order_id+'">点击付款</button>'
                                        }
                                        else if(order_status==3)
                                        {
                                            str_end+='<button data-id="'+order_id+'">交易成功</button>'
                                        }
                                        else if(order_status==4)
                                        {
                                            str_end+='<button data-id="'+order_id+'">交易关闭</button>'
                                        }

                                            str_end+='</div> </div>'


                                        var str_middle=''
                                        var len_temp=mess[i]['goods'].length;
                                        for(var j=0;j<len_temp;j++)
                                        {
                                            var image_s=mess[i]['goods'][j]['image_s'];
                                            var goods_name=mess[i]['goods'][j]['goods_name'];
                                            var goods_sum=mess[i]['goods'][j]['goods_sum'];
                                            var goods_number=mess[i]['goods'][j]['goods_number'];



                                            str_middle+='<div class="car-pro"><img class="car-p-img" src="'+image_s+'"/>' +
                                                '<h4 class="car-p-tit">'+goods_name+'</h4>' +
                                                '<span class="car-pp">￥'+goods_sum+'</span>' +
                                                '<label class="ding-num">x'+goods_number+'</label><div class="clear"></div></div>'
                                        }
                                        $('.order_list').append(str_first+str_middle+str_end);

                                    }
                                }

                            }
                        });
                    }
                }
            });
        });

    })
</script>
</html>