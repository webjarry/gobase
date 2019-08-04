<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html style="background: #f9f9f9">
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
<body style="background: #f9f9f9">
<header class="header">
    <a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>
    <h2 id="title">图文咨询</h2>
    <a href="javascript:void(0);" onclick="aaplay('search_lawyer.html')" class="search_btn"><img src="/Public/Home/img/search.png" alt=""></a>
</header>
<section class="condition">
    <ul>
        <li>
            <p>全国</p>
            <div class="area_choose myarea">
                <div class="choose">
                    <div class="area">
                        <dl  id="cityf">
                            <dd>全国</dd>
                            <dd>北京市</dd>

                        </dl>
                    </div>
                    <div class="cities"  id="cityson">
                        <dl>
                            <dd>全部</dd>
                        </dl>
                        <dl>
                            <dd>全部</dd>
                            <dd>万州区</dd>

                        </dl>
                        <dl>2</dl>

                    </div>
                </div>
            </div>
        </li>
        <li>
            <p>案件类型</p>
            <div class="area_choose myaj">
                <div class="choose">
                    <div class="area">
                        <dl id="f">
                            <dd>民事类</dd>
                            <dd>刑事类</dd>

                        </dl>
                    </div>
                    <div class="cities"  id="son">
                        <dl>
                            <dd>一般民事</dd>
                            <dd>合同纠纷</dd>

                        </dl>
                        <dl>2</dl>
                        <dl>3</dl>

                    </div>
                </div>
            </div>
        </li>
        <li>
            <p>智能排序</p>
            <div class="area_choose">
                <div class="sort">
                    <dl>
                        <dd>智能排序</dd>
                        <dd>评价</dd>
                        <dd>执业年限</dd>
                    </dl>
                </div>
            </div>
        </li>
    </ul>
</section>
<section class="about_info" style="padding-top: 0.3rem;">
    <div class="recommend_lawyer">
        <ul id='con'>
            <!--<li>
                <a href="##">
                    <img src="/Public/Home/img/userimg1.jpg" alt="">
                    <div class="lawyers_info">
                        <h6>方大同律师 <em>执业1年</em></h6>
                        <p class="location">重庆市 南岸区</p>
                        <p class="addr">重庆市程建律师事务所</p>
                        <dl>
                            <dd>遗产纠纷</dd>
                            <dd>劳动仲裁</dd>
                            <dd>人身损害</dd>
                        </dl>
                        <p class="price">价格：50元/次</p>
                    </div>
                </a>
            </li>-->
            
        </ul>
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
    var ajid = 0;
    var addrid = 0;
    var sort = 0;
    var page = 1;

    var t=para('type')
    if(t==1){
        $('#title').html('图文咨询')
    }else if(t==2){
        $('#title').html('电话咨询')
    }else if(t==3){
        $('#title').html('见面咨询')
    }


    ajcate();

    function toDoRequest(){

        var p= { ajid:ajid,addrid:addrid, sort:sort,page:page};console.log(p)
        page++;
        $.post(webSiteUrl+'/app/public/searchlawyer',p,function(ret){

            //alert(JSON.stringify(ret))
            //alert(ret.msg)
            if(ret.code==200){

                html = "";
                var tj=ret.data;
                if(tj != null){
                    for(var i=0;i<tj.length;i++){
                        html+='<li onclick="lawyer('+tj[i].id+')"><a href="javascript:;"><img src="'+tj[i].icon+'" alt=""><div class="lawyers_info"><h6>'+tj[i].nickname+'律师 <em>执业'+tj[i].year+'年</em></h6><p class="location">'+tj[i].addr+'</p><p class="addr">'+tj[i].depart+'</p><dl>';
                        for(var j=0;j<tj[i].label.length;j++){
                            html+='<dd>'+tj[i].label[j]+'</dd>';
                        }
                        html+='</dl></div></a></li>';

                    }
                    if(page==1){
                        $('#con').html(html);
                    }else{
                        $('#con').append(html);
                    }


                }
            }else{
                if(page==1){
                    $('#con').html('<p style="text-align:center">暂无数据</p>');
                }
            }
        })

    }
    function ajcate(){
        $.post(webSiteUrl+'/app/public/ajcate',function(ret){
            if(ret.code==200){

                f = "";
                s = "";
                var tj=ret.data;
                if(tj != null){
                    for(var i=0;i<tj.length;i++){
                        f+='<dd  style="cursor: pointer">'+tj[i].name+'</dd>';

                        var son=tj[i].son
                        if(son != null){
                            s+='<dl style="cursor: pointer">';
                            for(var j=0;j<son.length;j++){
                                s+='<dd style="cursor: pointer" data="'+son[j].id+'">'+son[j].name+'</dd>';
                            }
                            s+='</dl>';
                        }

                    }
                }
                $('#f').html(f)
                $('#son').html(s)

                ajcity()

            }else{
                alert(ret.msg);
            }
        });

    }
    function ajcity(){
        $.post(webSiteUrl+'/app/public/region',function(ret){
            if(ret.code==200){

                f = "";
                s = "";
                var tj=ret.data;
                if(tj != null){
                    for(var i=0;i<tj.length;i++){
                        f+='<dd  style="cursor: pointer">'+tj[i].name+'</dd>';

                        var son=tj[i].son
                        if(son != null){
                            s+='<dl style="cursor: pointer">';
                            for(var j=0;j<son.length;j++){
                                s+='<dd style="cursor: pointer" data="'+son[j].id+'">'+son[j].name+'</dd>';
                            }
                            s+='</dl>';
                        }

                    }
                }
                $('#cityf').html(f)
                $('#cityson').html(s)

                toDoRequest()
            }else{
                alert(ret.msg);
            }
        });

    }

    //打开和关闭列表
    $(".condition ul li p").click(function (e) {
        if($(this).hasClass('on')){
            $(this).removeClass('on').siblings('.area_choose').slideUp();
            $(this).parents('li').siblings('li').find('.area_choose').slideUp();
        }else {
            $(this).addClass('on').siblings('.area_choose').slideDown().find(".cities dl").eq(0).show().siblings('dl').hide();
            $(this).parents('li').siblings('li').find('p').removeClass('on').siblings('.area_choose').slideUp();
        }
    });
    //城市切换
    $(document).on('click',".condition ul li .area_choose .area dl dd",function () {
        $(this).addClass('active').siblings('dd').removeClass('active');
        $(this).parents('.area').siblings(".cities").find('dl').eq($(this).index()).show().siblings('dl').hide();

    });

    $(document).on('click',".myarea .cities dl dd",function () {
        var city = $(this).text();
        $(this).parents('.area_choose').slideUp().siblings('p').text(city).removeClass('on');

        page = 1;
        addrid = $(this).attr('data');
        $('#con').empty()
        toDoRequest()
    });
    $(document).on('click',".myaj .cities dl dd",function () {
        var city = $(this).text();
        $(this).parents('.area_choose').slideUp().siblings('p').text(city).removeClass('on');

        page = 1;
        ajid = $(this).attr('data');
        $('#con').empty()
        toDoRequest()
    });

    //智能排序
    $(".area_choose .sort dl dd").click(function () {
        $(this).addClass('active').siblings('dd').removeClass('active');
        $(this).parents('.area_choose').slideUp().siblings('p').text($(this).html()).removeClass('on');

        page = 1;
        sort = parseInt($(this).index()+1);
        $('#con').empty()
        toDoRequest()
    });

    
</script>
</html>