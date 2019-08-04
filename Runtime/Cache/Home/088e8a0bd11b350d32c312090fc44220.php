<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html style="background: #f3f3f3">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>法援宝</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/api.css" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/swiper.min.css" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/lawyer_user.css" />
    <script type="text/javascript" src="/Public/Home/js/api.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/swiper.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/mobile.js"></script>
	<script type="text/javascript" src="/Public/layer/layer.js"></script>
	<script type="text/javascript" src="/Public/Home/js/ajaxupload.3.5.js" ></script>
	<script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <style type="text/css">
        .money_list{padding: 0.2rem 0 0.1rem;}
        .money_list dl{display: flex;flex-wrap: wrap;justify-content: space-between;}
        .money_list dl dd{width: 30%;height: 1rem;font-size: 0.36rem;color: #333333;text-align: center;line-height: 1rem;background: #f5f5f5;margin-bottom: 0.15rem;border-radius: 5px;font-family: Arial; border: 0.03rem #f5f5f5 solid;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
        .money_list dl dd span{font-size: 0.24rem;}
        .money_list dl dd.on{color: #f3a536; background: #f8f1df; border: 0.03rem #f3a536 solid; }
        .tips{ color: #333333;font-size: 0.24rem;}
        .tips em{ color: #f3a536;}
        .daShang{ padding-bottom: 0.2rem;}
        .daShang p{ width: 40%;}
        .daShang input{ width: 60%; height: 0.72rem!important; border: 1px #cccccc solid;border-radius: 5px; text-align: left!important; padding: 0 0.15rem;}
        .notify ul li{padding-left: 0.45rem!important; background: url("/Public/Home/img/laba.png") left center no-repeat;background-size: 0.32rem auto; font-size: 0.26rem; color: #999999;}
        .notify ul li span{ color: #f3a536;}
    </style>
</head>
<body style="background: #f3f3f3">
<header class="header">
    <a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>
    <h2>免费咨询</h2>
    <a href="javascript:void (0);" class="share_btn" onclick="aaplay('index')"><img src="/Public/Home/img/shouye1.png" alt=""></a>
    <!--<a href="javascript:void(0);" class="qiandao_btn">注册</a>-->
</header>
<!--<section class="message_notify" style="margin-top: 0.25rem;">
    <div class="notify swiper-container" >
        <ul class="swiper-wrapper" id='gonggao'>
            <li class="swiper-slide">138****7687用户悬赏<em>100.00元</em>的咨询问题已解决！</li>
            <li class="swiper-slide">138****7687用户悬赏<em>100.00元</em>的咨询问题已解决！</li>
            <li class="swiper-slide">138****7687用户悬赏<em>100.00元</em>的咨询问题已解决！</li>
        </ul>
    </div>
    <script>
        var mySwiper = new Swiper('.notify',{
            slidesPerView: 1,
            autoplay: 3000,
            loop: true,
            onlyExternal: true
        })
    </script>
</section>-->
<section class="release_contract" style="padding-top: 0.2rem; padding-bottom: 1.2rem;">
    <div class="release_list">
        <ul>
            <li>
                <div class="release_item">
                    <p>选择案件类型</p>
                    <span class="kinds_name"></span>
                </div>
            </li>
            <li>
                <div class="release_item">
                    <p>问题概述</p>
                </div>
                <textarea name="" id="content" cols="30" rows="10" placeholder="请输入您要咨询的问题：如案情经过、证据情况、以便 律师更好的为您解答。"></textarea>
                <div class="mp3">
                    <!--<a href="javascript:void(0);" class="mp3_btn"><img src="/Public/Home/img/MP3.png" alt=""></a>-->
                    <span>限500字</span>
                </div>
            </li>
            <!--<li>
                <div class="release_item">
                    <p>添加定位</p>
                    <span>重庆市  南岸区</span>
                </div>
            </li>-->
            <li>
                <div class="release_item">
                    <p class="privacy">是否隐私发布？(需支付<?php echo C('WEB_LTATION_SITE');?>元)</p>
                    <span class="release_checked"><input type="checkbox" id='private'></span>
                </div>
            </li>
            <li>
                <div class="release_item">
                    <p>悬赏发布</p>
                </div>
                <p class="tips"><em>悬赏咨询，律师快速抢答不用等！</em>悬赏越高，回答<em>质量</em>越好！</p>
                <div class="money_list">
                    <dl>
                        <dd data-num="3">3<span>元</span></dd>
                        <dd class='on' data-num="5">5<span>元</span></dd>
                        <dd data-num="10">10<span>元</span></dd>
                        <dd data-num="20">20<span>元</span></dd>
                        <dd data-num="50">50<span>元</span></dd>
                        <dd data-num="0"><span>免费发布</span></dd>
                    </dl>
                </div>
                <div class="release_item daShang">
                    <p>输入打赏金额(元)：</p>
                    <input type="number" name="money" placeholder="请输入要打赏的金额">
                </div>
            </li>
        </ul>
    </div>
</section>
<section class="fixed_btn" onclick="tijiao()">
    <a href="javascript:void(0)">提交信息</a>
</section>


<!--<section class="fixed_select">-->
    <!--<div class="select_content">-->
        <!--<h6 style="font-size: 0.26rem;">选择案件类型</h6>-->
        <!--<ul style="text-align: center;" id='cate'>-->
            <!---->
        <!--</ul>-->
    <!--</div>-->
<!--</section>-->
<section class="fixed_case">
    <div class="case_content">
        <div class="case_kinds">
            <ul id="f">
                <li class="">民事类</li>

            </ul>
        </div>
        <div class="kinds_list" id="son">
            <ul>
                <li>一般民事</li>
                <li>合同纠纷</li>

            </ul>
        </div>
    </div>
</section>

<section class="fixed_tips">
    <div class="tips_content">
        <ul>
            <li>您的咨询内容不会向其他用户公开，仅律师可见。</li>
            <li>发布私密咨询需要支付服务费，此费用不作为律师服务费。您可以通过采纳、送心意等方式向律师表达谢意。</li>
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
    var ajid=0;
 
    cate();

    $(".money_list").on('click','dd',function () {
        $(this).addClass('on').siblings('dd').removeClass('on');
        $("input[name=money]").val('');
    });

    $('.release_list').on('input','input[name=money]',function () {
        var val = $(this).val();
        if(val!=''){
            $(".money_list dd").removeClass('on');
        }
    });

    var m = 0;
    var money = 0;
    function tijiao(){
        if(!loginCheck()){
            return;
        }


         var len = $(".money_list dd").length;
        if($(".money_list dd").hasClass('on')){
          for(var i = 0; i<len;i++){
            if($(".money_list dd").eq(i).hasClass('on')){
              m = $(".money_list dd").eq(i).attr('data-num');
            }
          }
        }else {
          m = $("input[name=money]").val();
        }
        
	
        var content=$('#content').val();
        var private=$('#private').prop('checked')?1:0;
         if(private==1){
             price = parseFloat(m) + 10;
             money = parseFloat(m);
         }else {
             price = parseFloat(m);
             money = parseFloat(m);
         }
         var reward_price = money;
        if(ajid==0){
            alert('请选择案件类型！');return;
        }else if(content==''){
            alert('请输入咨询内容！');return;
        }
		
		//alert();return false;
		
		var index = layer.load(1, {
			shade: [0.4,'#000'] //0.1透明度的白色背景
		});
	
		//var p={token:token,model:'fask',ajid:ajid,content:content,private:private};
		var p={token:token,model:'fask',ajid:ajid,content:content,private:private,reward_price:reward_price,price:price}
		
			/*$.post('<?php echo U("");?>',p,function(ret){
				window.location.href = '<?php echo U("want_consultation_pay");?>';
            });*/
		    $.post(webSiteUrl+'/app/user/apply',p,function(ret){
                layer.close(index);
                if(ret.code==200){
					alert(ret.msg);
                    if(private==1 || m!=0){
                        var orderno=ret.orderno
                        window.location.href = '/home/user/want_consultation_pay?orderno='+orderno+'&money='+price;
                    }else{
                        aaplay('free_consultation')
                    }


                }else{
				
					alert(ret.msg);
				
				}
            });
		

		
        
       
    };
    // function cate(){
    //     $.post(webSiteUrl+'/app/public/ajcate',function(ret){
    //         if(ret.code==200){
    //
    //             var tj=ret.data;
    //
    //             html='';
    //             if(tj != null){
    //                 for(var i=0;i<tj.length;i++){
    //
    //
    //                     var son=tj[i].son
    //                     if(son != null){
    //
    //                         for(var j=0;j<son.length;j++){
    //                             html+='<li style="cursor: pointer"  data="'+son[j].id+'">'+son[j].name+'</li>';
    //                         }
    //
    //                     }
    //
    //                 }
    //             }
    //             $('#cate').html(html)
    //         }
    //     });
    //
    // };

    function cate(){
        $.post(webSiteUrl+'/app/public/ajcate',function(ret){
            if(ret.code==200){

                f = "";
                s = "";
                var tj=ret.data;
                if(tj != null){
                    for(var i=0;i<tj.length;i++){
                        f+='<li  style="">'+tj[i].name+'</li>';

                        var son=tj[i].son
                        if(son != null){
                            s+='<ul style="cursor:pointer">';
                            for(var j=0;j<son.length;j++){
                                s+='<li style="cursor:pointer" data="'+son[j].id+'">'+son[j].name+'</li>';
                            }
                            s+='</ul>';
                        }

                    }
                }
                $('#f').html(f)
                $('#son').html(s)



            }else{
                alert(ret.msg);
            }
        });

    }
    
    //我要咨询 类型选择
    // $(".kinds_name").click(function () {
    //     $(".fixed_select").fadeToggle();
    //     $(".fixed_select .select_content").animate({
    //         'top': '0',
    //         'opacity': '1'
    //     },400);
    // });
    //选择类型
    $('.case_kinds ul li').eq(0).addClass('active');
    $(".kinds_list ul").eq(0).show();
    $(".kinds_name").click(function () {
        $(".fixed_case").fadeIn().find('.case_content').animate({'top':0,'opacity':1},400);
        $(".kinds_list ul").eq(0).show();
    });
    $(".fixed_case .case_content").click(function (e) {
        e.stopPropagation();
    });

    $(".case_content").on("click",".case_kinds ul li",function () {
        $(this).addClass('active').siblings('li').removeClass('active');
        $(".kinds_list ul").eq($(this).index()).show().siblings('ul').hide();
    });
    var str = '';
    $(".case_content").on('click',".kinds_list ul li",function () {

        $(".kinds_list ul li").removeClass('active');
        $(this).addClass('active');
        var _str = $('.case_kinds ul li.active').html();
        str = $(this).html();
        $(".kinds_name").html(str);
        $(".fixed_case").fadeOut().find('.case_content').animate({'top': '4rem','opacity':0},400);

        ajid = $(this).attr('data');
    });

    //关闭弹窗
    $(".fixed_case").click(function (e) {
        $(".fixed_case").fadeOut().find('.case_content').animate({'top': '4rem','opacity':0},400);
    });
    // $(document).on('click',".select_content ul li",function () {
    //     ajid=$(this).attr('data');
    //     $(".kinds_name").text($(this).html());
    //     $(".fixed_select").fadeToggle();
    //     $(".fixed_select .select_content").animate({
    //         'top': '-4rem',
    //         'opacity': '0'
    //     },400);
    // })
    $(".release_checked").click(function () {
        if($(this).hasClass('active')){
            $(this).removeClass('active').find('input').prop('checked',false);
            
        }else{
            $(this).addClass('active').find('input').prop('checked',true);
            $(".fixed_tips").fadeIn();
        }
    });

    //提示关闭
    $(".fixed_tips").click(function () {
        $(this).fadeOut();
    });
    $(".fixed_tips").hide();

    
</script>
</html>