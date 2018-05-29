<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新疆特产全攻略</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv=X-UA-Compatible content="IE=Edge">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <script src="/public/car/js/jquery-1.10.1.min.js"></script>
    <link rel="stylesheet" href="/public/car/css/init.css">
    <link rel="stylesheet" href="/public/car/css/index.css?2014">
	<style>
		.nothing{text-align:center; color:#999; height:50px; line-height:50px; padding:10px auto; background:#fff;}
		.loading{background:url(/public/myshop/images/loading.jpg) center center no-repeat #fff; background-size:auto 20px; height:50px;}
	</style>
</head>
<body style="background-color:#f3f3f3;">
	<div class="warp">
		<!-- <header class="header">
			<a href="javascript:void(0)" class="i_back">返回</a>
			<a href="#" class="i_share"></a>
		</header> -->
		<div class="header-img">
			<img class="head-img" src="/public/car/image/header.jpg"/>
			<div class="head-nav">
				<h2 class="nav-tit">全季热销</h2>
				<h3 class="nav-tit-en">Hot-sale product</h3>
				<ul class="head-nav-ul">
					<?php foreach($hot_list as $k=>$v){?>
						<a href="<?php echo $v['wx_url']?>"><li>
								<div class="nav-div-img"><img class="cir-img" src="<?php echo $v['image_c']?>"/></div>
								<p class="nav-p-tit"><?php echo $v['goods_name']?></p>
							</li>
						</a>
					<?php }?>
				</ul>
<!--				<p class="navlist-tell">说明性文字说明性文字说明性文字</p>-->
			</div>
		</div>
		<div class="product-list" style="display: none;">
			<h2 class="nav-tit">更多产品</h2>
			<h3 class="nav-tit-en">More products</h3>
			<ul class="product-nav">
				<li data-type="0" class="nav-check">全部</li>
				<li data-type="1">日用品</li>
				<li data-type="2">化妆品</li>
				<li data-type="3">美食</li>
				<li data-type="4">其他推荐</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="product-d-list">
			<ul>
				<?php foreach($goods_list as $k=>$v){?>
					<li class="list">
						<a href="<?php echo $v['wx_url']?>"><div>
								<img src="<?php echo $v['image_s']?>"/>
								<h4 class="pro-tit"><?php echo $v['goods_name']?></h4>
							</div></a>
						<p>￥<span data-price="70" data-num="0"><?php echo $v['ori_price']?></span><a class="add-car" data-id="<?php echo $v['goods_id']?>">+ 购物车</a></p>
					</li>
				<?php }?>
			</ul>

<!--			<a href="#" class="check-more">查看更多</a>-->
			<div class='loading' style="display: none;background: transparent;"></div>
			<div class='nothing car-show-tell' style="display: none;background: transparent;">没有更多了！</div>
			<h4 class="show-tell"></h4>
		</div>

		<div class="clear"></div>
		<footer class="footer">
			<ul class="bottom-ul">
				<a href="<?php echo $to_order?>"><li class="ding-li">我的订单</li></a>
				<a href="<?php echo $to_cart?>"><li class="car-li">购物车 <span class="p-num"><?php echo $car_count?></span> 件商品</li></a>
			</ul>
		</footer>
		<div class="d-success">加入购物车成功</div>
	</div>
	<script language="javascript" src="/public/car/js/index.js"></script>
	<script>
		$(function(){

			function change_image()
			{
				$('.product-nav li').each(function(){
					var index=$(this).index()+1;
					var image=''
					if($(this).hasClass('nav-check'))
					{
						image="/public/car/image/pnav"+index+"_c.png"
						$(this).css("background-image","url("+image+")");
					}else{
						index=$(this).index()+1;
						image="/public/car/image/pnav"+index+".png"
						$(this).css("background-image","url("+image+")");
					}
				})
			}

			$('.product-nav li').on('click',function(){
				var index=$(this).index();
				$('.product-nav li').removeClass();
				if($(this).hasClass('nav-check')){
					$(this).removeClass('nav-check');
				}else{
					$(this).addClass('nav-check');
				}
				change_image();
				get_page_one()
			});

			change_image();
			var page=1;
			var max_page='<?php echo $max_page?>'

			$('.product-d-list').on('click','.add-car',function(){
				var car_url='<?php echo $car_add?>';
				var goods_id=$(this).attr('data-id')
				$.ajax({
					data:{"goods_id":goods_id,"num":1},
					url:car_url,
					type:"post",
					success:function(mess){

						$('.d-success').fadeIn(300);
						$('.d-success').fadeOut(500);
						var car_num=$('.p-num').html();
						car_num++
						$('.p-num').html(car_num);
					}
				});


			})

			function get_goods_type()
			{
				 var rs=$('.nav-check').attr('data-type')
				if(typeof(rs)=="undefined")
				{
					return 0;
				}else{
					return rs;
				}

			}
			function get_page_one()
			{
				//$('.product-d-list li').remove();
				var type=get_goods_type()
				var url='<?php echo $sub_url?>';
				$.ajax({
					data:{"page":1,"type":type,ajax:'1'},
					url:url,
					type:"post",
					success:function(mess){
						$('.product-d-list li').remove();
						$('.loading').css('display','none');
						$('.nothing').css('display','none');
						page=1;

						mess=$.parseJSON(mess);
						//mess=$.parseJSON(mess);
						max_page=mess['max_page']
						mess=mess['goods_list']

						var len=mess.length;
						//alert(len)
						if(len > 0)
						{
							for(var i=0;i<=len;i++)
							{

								var wx_url=mess[i]['wx_url'];

								var image_r=mess[i]['image_s'];
								var goods_name=mess[i]['goods_name'];
								var ori_price=mess[i]['ori_price'];
								var goods_id=mess[i]['goods_id'];



								var str_all='<li><a href='+wx_url+'>' +
										'<div><img src='+image_r+'><h4 class="pro-tit">'+goods_name+'</h4></div></a>' +
										'<p>￥<span data-price="70" data-num="0">'+ori_price+'</span><a class="add-car" data-id="'+goods_id+'">+ 购物车</a></p></li>'
								$('.product-d-list ul').append(str_all);

							}
						}
					}
				});
			}

			$(document).ready(function() {
				$(window).scroll(function() {
					//$(document).scrollTop() 垂直滚动的距离

					if ($(document).scrollTop() <= 0) {
						// alert("0");
					}

					if ($(document).scrollTop() >= $(document).height()  - $(window).height())
					{

						var url='<?php echo $sub_url?>';
						page++;
						$('.loading').css('display','block');
						//alert(page);
						if(page>max_page){
							$('.loading').css('display','none');
							$('.nothing').css('display','block');
							//alert('没有更多了！')
						}
						//alert('下拉加载更多');
						var order=$('.order').val();
						if(page<=max_page){
							// alert
							var type=get_goods_type()
							$.ajax({
								data:{"page":page,"type":type,"ajax":1},
								url:url,
								type:"post",
								success:function(mess){
									//alert(page);
									mess=$.parseJSON(mess);
									mess=mess['goods_list']
									var len=mess.length;
									if(len > 0)
									{
										for(var i=0;i<=len;i++)
										{

											var wx_url=mess[i]['wx_url'];
											var image_r=mess[i]['image_s'];
											var goods_name=mess[i]['goods_name'];
											var ori_price=mess[i]['ori_price'];
											var goods_id=mess[i]['goods_id'];
											var str_all='<li><a href='+wx_url+'>' +
													'<div><img src='+image_r+'><h4 class="pro-tit">'+goods_name+'</h4></div></a>' +
													'<p>￥<span data-price="70" data-num="0">'+ori_price+'</span><a class="add-car" data-id="'+goods_id+'">+ 购物车</a></p></li>'
											$('.product-d-list ul').append(str_all);

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
</body>
</html>
