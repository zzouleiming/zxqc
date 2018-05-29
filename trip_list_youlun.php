<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>坐享其成后台管理-H5行程列表</title>
    <link href="/public/newadmin/css/css.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/public/newadmin/js/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="/public/newadmin/js/Calendar3.js"></script>
    <script>
        var time1='<?php if($time1!=0){echo date('Y-m-d',$time1);}?>';
        var time2='<?php if($time2!=0){echo date('Y-m-d',$time2);}?>';
        $(function(){
            var view_status='<?php echo $is_show ?>';
            if(view_status==0){
                $('#ing').addClass('on');
                $('#ed').removeClass('on');
                $('#close').removeClass('on');

            }else if(view_status==1){
                $('#ing').removeClass('on');
                $('#ed').addClass('on');
                $('#close').removeClass('on')
            }else if(view_status==2){
                $('#ing').removeClass('on');
                $('#ed').removeClass('on');
                $('#close').addClass('on');
            }

            $('.order').click(function(){
                var goods_id=$(this).attr('name');
                var order=$(this).parent().find('.displayorder').val();
                var order_url='<?php echo $order_url?>';
                alert(goods_id)
                $.ajax({
                    url:order_url,
                    data:{order:order,goods_id:goods_id},
                    type:'post',
                    cache:false,
                    success:function(mess){
                        // alert(mess)
                        location.reload(true);
                    }
                });

            });
            /*  intid=setInterval(function(){
             var timechooes1=$('.time1').val();
             var timechooes2=$('.time2').val();
             if(timechooes1!=time1){
             $('#form').submit();
             }
             if(timechooes2!=time2){
             $('#form').submit();
             }
             },500);*/



        })

    </script>
</head>

<body>
<div class="nav">
    <h2>>> 行程列表</h2>

</div>
<div class="main">
    <ul class="type">
<!--        <li id="ing"  class='on'><a href="/newadmin/cat_goods_list?is_show=1">显示</a></li>-->
        <li id="ed"><a href="<?=$add_url?>">增加</a></li>

    </ul>
    <div class="contact">
        <table border="0" cellpadding="0" cellspacing="0" class="info_list">
            <table border="0" cellpadding="0" cellspacing="0" class="info_list">
                <tr>
                    <th><span>ID</span></th>
                    <th><span>标题</span></th>


                    <th><span>操作</span></th>
                </tr>

                <?php foreach($info as $k=>$v){
                    $add_time=date('Y-m-d H:i:s',$v['add_time'] ); ?>
                    <tr>
                        <td><?=$v['day_id']?></td>
                        <td><?=$v['day_title']?></td>

                        <td><a href="<?=$v['edit_url']?>">修改</a>
                            <a href="<?=$v['detail_url']?>">详情</a>
                            <a href="<?=$v['del_url']?>">删除</a>
                        </td>
                    </tr>
                <?php }?>


            </table>

    </div>
</div>
</body>
</html>
