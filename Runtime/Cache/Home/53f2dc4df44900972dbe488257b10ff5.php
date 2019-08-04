<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html style="background: #f3f3f3">
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
    <script type="text/javascript" src="/Public/Home/js/mobile.js"></script><script type="text/javascript" src="/Public/layer/layer.js"></script><script type="text/javascript" src="/Public/Home/js/ajaxupload.3.5.js" ></script><script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>

</head>
<body style="background: #f3f3f3">
<header class="header">
    <a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>
    <h2>我要咨询</h2>
    <a href="javascript:void (0);" class="share_btn" onclick="aaplay('index')"><img src="/Public/Home/img/shouye1.png" alt=""></a>
</header>

<section class="cost_calc">
    <div class="calc_list">
        <h6 class="cost_title">选择咨询方式</h6>
        <ul>
            <li>
                <a href="want_consult_release.html">
                    <img src="/Public/Home/img/ask01.png" alt="">
                    <div class="cost_name">
                        <h6>免费咨询</h6>
                        <p>免费咨询，多名律师为您解答</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:;" onclick="asktype(1)">
                    <img src="/Public/Home/img/ask02.png" alt="">
                    <div class="cost_name">
                        <h6>图文咨询</h6>
                        <p>通过图文、语音与律师在线沟通</p>
                    </div>
                </a>
                <em class="tuijan">推荐</em>
            </li>
            <li>
                <a href="javascript:;" onclick="asktype(2)">
                    <img src="/Public/Home/img/ask03.png" alt="">
                    <div class="cost_name">
                        <h6>电话咨询</h6>
                        <p>专业律师给您回电解答</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:;" onclick="asktype(3)">
                    <img src="/Public/Home/img/ask04.png" alt="">
                    <div class="cost_name">
                        <h6>见面咨询</h6>
                        <p>约定时间、地点、与律师见面详谈</p>
                    </div>
                </a>
            </li>
        </ul>
		<div class="release_tips" style="color:#bbb;">
          注：律师回复时间为工作日早上8点---晚上8点，对于深夜和凌晨订单回复时间将有所延迟。
        </div>
    </div>
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
    function asktype(type) {
        location.href='/Home/user/lawyers_list?type='+type
    }

    //提示关闭
    $(".fixed_tips").click(function () {
        $(this).fadeOut();
    });


</script>
</html>