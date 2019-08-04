<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>法援宝</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/api.css" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/lawyer_user.css" />
    <script type="text/javascript" src="/Public/Home/js/api.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/mobile.js"></script>
    <script type="text/javascript" src="/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/Home/js/ajaxupload.3.5.js" ></script>
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>

</head>
<body>
<header class="header">
    <a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>
    <h2>律师评价</h2>
    <a href="javascript:void (0);" class="share_btn" onclick="aaplay('index')"><img src="/Public/Home/img/shouye1.png" alt=""></a>
</header>
<section class="evaluate_kinds">
    <ul>
        <!--<li>全部(20)</li>
        <li>讲解耐心(5)</li>
        <li>回复及时(5)</li>
        <li>分析到位(5)</li>
        <li>服务热情(5)</li>-->
    </ul>
</section>
<section class="evaluate_list">
    <ul id='list'>
        <!-- <li>
            <div class="evaluate_user">
                <img src="/Public/Home/img/userimg1.jpg" alt="">
                <div class="info">
                    <p>138****5815</p>
                    <span>2018-11-11　14:45</span>
                </div>
            </div>
            <div class="evaluate_info">
                <p>非常不错，律师很耐心，也非常专业，完美解决了我的问题。</p>
            </div>
        </li> -->
        
    </ul>
</section>


</body><script>
    var user="<?php echo ($user); ?>";
    if(user != null && user != 'undefined'){
        var token="<?php echo ($user["token"]); ?>";
        var uid="<?php echo ($user["id"]); ?>";
        var utype="<?php echo ($user["type"]); ?>";
        var vip="<?php echo ($user["vip"]); ?>";
        var islogin=1;
        var loginphone="<?php echo ($user["phone"]); ?>";
        var nickname="<?php echo ($user["nickname"]); ?>";
        var uicon="<?php echo ($user["icon"]); ?>";
        var balance="<?php echo ($user["balance"]); ?>";
        var reward="<?php echo ($user["reward"]); ?>";
        var xs="<?php echo ($user["xs"]); ?>";


    }else{
        var islogin=0;
    }
    function loginCheck() {
       
        if(token=='' || utype!=1){
            alert('您尚未登录,请登录123')
            window.location.href = '/Home/Public/login_account';
			return false;
        }else{
            return true;
        }
    }

</script><script type="text/javascript" src="/Public/Home/js/lawyer_user.js"></script>
<script>


    var page=1;


    toDoRequest();
        


    //众筹列表
    function toDoRequest(){
        loginCheck()
         var p= {uid:para('id'),type:3,page:page};
         $.post(webSiteUrl+'/app/public/pl',p,function(ret){
             console.log(ret.data)
            if (ret.code == 200) {

                var list = ret.data.com;
                var html = '';
                if (list != null) {
                    for (var i =0;i< list.length; i++) {
html += '<li><div class="evaluate_user"><img src="'+webSiteUrl+list[i].icon+'"><div class="info"><p>'+list[i].phone+'</p><span>'+list[i].time+'</span></div></div><div class="evaluate_info"><p>'+list[i].content+'</p></div></li>';

                    }
                    if(page==1){
                        $('#list').html(html);
                    }else{
                        $('#list').append(html);
                    }
                    page++;
              
                    
                }
            }else{
                $('#list').html('<p style="text-align:center">暂无数据</p>');
            }
        });
    }

</script>
</html>