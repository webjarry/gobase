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
    <script type="text/javascript" src="/Public/Home/js/lawyer_user.js"></script>
    <style type="text/css">

        .dashang {
            background: url("/Public/Home/img/jinbi.png") left center no-repeat !important;
            background-size: 0.3rem auto !important;
            font-size: 0.26rem;
            color: #f3a536 !important;
            margin-right: 0.3rem;
        }
    </style>
</head>
<body>
<header class="header">
    <a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>
    <h2>服务列表</h2>
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
<section class="ask_list">
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
<!--<div class="ask_list">-->
    <!--<ul id='con'>-->


    <!--</ul>-->
<!--</div>-->


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
         var p= {sid:para('id'),page:page};
         $.post(webSiteUrl+'/app/public/question_list',p,function(ret){
             console.log(ret.data)
            if (ret.code == 200) {

                var tj = ret.data;
                var html = '';
                var dashang = '';
                if (tj != null) {
                    for (var i =0;i< tj.length; i++) {
                        if(tj[i].reward_price!=0){
                            dashang = '<em class="dashang">'+tj[i].reward_price+'元</em>';
                        }else {
                            dashang = ''
                        }
//                        html += '<li><div class="evaluate_user"><img src="'+webSiteUrl+list[i].icon+'"><div class="info"><p>'+list[i].phone+'</p><span>'+list[i].time+'</span></div></div><div class="evaluate_info"><p>'+list[i].content+'</p></div></li>';
                        html += '<li onclick="askdetail(' + tj[i].id + ')"><a href="javascript:;"><div class="userInfo"><img src="' + tj[i].icon + '" alt=""><div class="userName"><h6>' + tj[i].phone + '</h6><span><em style="top: 0;">' + tj[i].time + '</em></span></div></div><div class="comment_info"><p>' + tj[i].content + '</p></div><div class="ask_kinds"><span>' + tj[i].ajtype + '</span><div class="ask_active">'+dashang+'<em class="answer_num">' + tj[i].num + '人回答</em></div></div></a></li>';
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