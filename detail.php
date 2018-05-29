<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $goods_info['goods_name']?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv=X-UA-Compatible content="IE=Edge">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <script src="/public/car/js/jquery-1.10.1.min.js"></script>
    <link rel="stylesheet" href="/public/car/css/init.css">
    <link rel="stylesheet" href="/public/car/css/index.css">
</head>
<body style="background-color: #fff;">
	<div class="warp">
<!--        <header class="header">-->
<!--            <a href="javascript:void(0)" class="i_back">返回</a>-->
<!--        </header>-->
        <div class="detail-top">
            <img src="<?php echo $goods_info['image_r']?>"/>
            <p class="detail-name"><?php echo $goods_info['goods_name']?></p>
            <p class="detail-p-price" data-price="70">￥<span><?php echo $goods_info['ori_price']?></span></p>
            <div class="d-add-num">
                <label>购买数量</label>
                <ul class="add-ul">
                    <li class="de">-</li>
                    <li class="num">1</li>
                    <li class="add">+</li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
        <div class="detail-d">
            <h3 class="d-pro-tit">商品介绍</h3>
            <?php echo $goods_info['content']?>
<!--            <img src="/public/car/image/detail_d.jpg"/>-->
<!--            <p>--><?php //echo $goods_info['content']?><!-- 品介绍</p>-->
        </div>
        <footer class="footer-detail">
            <ul class="bottom-ul">
                <a href="<?php echo $to_order?>"><li class="ding-li">我的订单</li></a>
                <a href="javascript:void(0)" class="add_cart"><li class="detail-car-li">加入购物车</li></a>
                <a href="javascript:void(0)"><li class="detail-pay-li">立即购买</li></a>
            </ul>
        </footer>
        <!-- 添加成功提示框 -->
        <div class="d-success">加入购物车成功</div>
    </div>
     <script language="javascript" src="/public/car/js/index.js"></script>
    <script>
        $(function(){
            var car_url='<?php echo $car_add?>';
            var order_in_session='<?php echo $order_in_session?>';
            var goods_id='<?php echo  $goods_info['goods_id']?>'
            $('.add_cart').click(function(){
                var num=$('.num').text()
                $.ajax({
                    data:{"goods_id":goods_id,"num":num},
                    url:car_url,
                    type:"post",
                    success:function(mess){
                       $('.d-success').fadeIn(300);
                        $('.d-success').fadeOut(500);
                    }
                });
            })
            $('.detail-pay-li').click(function(){
                var goods_id='<?php echo  $goods_info['goods_id']?>'
                var num=$('.num').text()
                $.ajax({
                    data:{"goods_id":goods_id,"num":num},
                    url:order_in_session,
                    type:"post",
                    success:function(mess){
                        if(mess==1)
                        {
                            location.href='<?php echo $sub_url?>'
                        }

                    }
                });
            })

            $('.d-success').click(function(){
                $(this).fadeOut();
            })
            $('.add').click(function(){
                var num=$('.num').text()
                num++
                $('.num').text(num)
            })

            $('.de').click(function(){
                var num=$('.num').text()
                if(num>0)
                {
                    num--
                    $('.num').text(num)
                }

            })
        })
        
pushHistory(); 
window.addEventListener("popstate", function(e) { 
	pushHistory();
	location.href="<?php echo base_url('goodsforcar/index').'?business_id='.$_SESSION['business_id']?>";
}, false); 
function pushHistory() { 
	var state = { 
		title: "title", 
		url: "#" 
	}; 
	window.history.pushState(state, "title", "#"); 
} 
    </script>
</body>
</html>