<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html style="background: #f9f9f9">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <title>法援宝</title>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/api.css"/>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="/Public/Home/css/lawyer_user.css"/>
    <script type="text/javascript" src="/Public/Home/js/api.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery-1.9.1.min.js"></script>

    <script type="text/javascript" src="/Public/Home/js/mobile.js"></script>
    <script type="text/javascript" src="/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/Home/js/ajaxupload.3.5.js"></script>
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>

    <script src="/Public/webim/chat/demo/javascript/dist/webim.config.js"></script>
    <script src="/Public/webim/chat/sdk/dist/strophe-1.2.8.min.js"></script>
    <script src="/Public/webim/chat/sdk/dist/webimSDK-1.4.13.js"></script>
    <script src="/Public/webim/chat/webrtc/dist/adapter.js"></script>
    <script src="/Public/webim/chat/webrtc/dist/webrtc-1.4.12.js"></script>
    <!-- fix: ie8 upload -->
    <script type='text/javascript' src='/Public/webim/chat/ie8/swfupload/uploadShim.js'></script>

</head>
<style type="text/css">
    .remember {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 0.4rem;
        font-size: 0.26rem;
        color: #666666;
        padding-left: 0.2rem;
    }

    .reBtn {
        display: inline-block;
    }

    .reBtn input {
        position: relative;
        top: 0.03rem;
        margin-right: 0.05rem;
    }

    .remember a {
        color: #666666;
    }
</style>

<body style="background: #f9f9f9">
<header class="header">
    <!--<a href="javascript:void(0);"  class="back_btn"><img src="/Public/Home/img/head-fanhui.png" alt=""></a>-->
    <h2>登录</h2>
    <a href="<?php echo U('Public/register');?>" class="qiandao_btn" onclick=""
       style="position: absolute;  right: 3%; top: 0; color: #fff;">注册</a>
    <!--<a href="javascript:void(0);" class="qiandao_btn">注册</a>-->
</header>
<section class="login_choose">
    <ul>
        <li class="active"><a href="<?php echo U('Public/login_account');?>">账号密码登录</a></li>
        <li class=""><a href="<?php echo U('Public/login');?>">手机快捷登录</a></li>

    </ul>
</section>

<section class="login_section">
    <div class="fyb_logo"><img src="/Public/Home/img/fyb_logo.png" alt=""></div>
    <div class="login_box">
        <ul>
            <li class="username"><input type="text" placeholder="请输入手机号/账号" id='phone' value="<?php echo ($phone); ?>"></li>
            <li class="tel_code"><input type="password" placeholder="请输入密码" id='password' value="<?php echo ($password); ?>"></li>
        </ul>
        <div class="remember">
            <div class="reBtn" style="cursor: pointer"><input class="reChecked" type="checkbox">记住密码</div>
            <a href="<?php echo U('Public/forget');?>">忘记密码？</a>
        </div>
        <p class="agree" style="margin-top: 0.1rem">成功登录即表示您同意<a href="javascript:;" onclick="yhxy()">《用户协议》</a></p>
        <button class="login_submit" type="submit" onclick="login()">进入法援宝</button>
    </div>
    <div class="login_kinds">
        <h6><span>第三方登录</span></h6>
        <ul>
            <li><a href="javascript:;" onclick="sanfangqq()"><img src="/Public/Home/img/ologin1.png" alt=""></a></li>
            <?php if($is_weixin == 1): ?><li><a href="/Home/Home/wx_login"><img src="/Public/Home/img/ologin2.png" alt=""></a></li><?php endif; ?>
            <li><a href="<?php echo ($web_url); ?>"><img src="/Public/Home/img/ologin3.png" alt=""></a></li>
        </ul>
    </div>
</section>
<aside class="download">
    <a href="<?php echo U('lawyer/login');?>" class="user_download">律师用户点击跳转【法援宝律师端】</a>
</aside>


</body>
<script>
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
            alert('您尚未登录,请登录')
            window.location.href = '/Home/Public/login_account';
			return false;
        }else{
            return true;
        }
    }

</script>
<script type="text/javascript" src="/Public/Home/js/lawyer_user.js"></script>

<script type="text/javascript">
    var conn = {};
    conn = new WebIM.connection({
        isMultiLoginSessions: WebIM.config.isMultiLoginSessions,
        https: typeof WebIM.config.https === 'boolean' ? WebIM.config.https : location.protocol === 'https:',
        url: WebIM.config.xmppURL,
        isAutoLogin: true,
        heartBeatWait: WebIM.config.heartBeatWait,
        autoReconnectNumMax: WebIM.config.autoReconnectNumMax,
        autoReconnectInterval: WebIM.config.autoReconnectInterval,
        apiUrl: WebIM.config.apiURL
    });

    // tips: ie8 support  fileInputId should match with the file in the document
    WebIM.flashUpload = UploadShim({fileInputId: 'image'}, conn).flashUpload;

    // listern，添加回调函数
    conn.listen({
        onOpened: function (message) {          //连接成功回调，连接成功后才可以发送消息
            //如果isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
            // 手动上线指的是调用conn.setPresence(); 在本例中，conn初始化时已将isAutoLogin设置为true
            // 所以无需调用conn.setPresence();
            console.log("%c [opened] 连接已成功建立", "color: green")
        },
        onTextMessage: function (message) {

        },  //收到文本消息
        onEmojiMessage: function (message) {

        },   //收到表情消息
        onPictureMessage: function (message) {

        }, //收到图片消息
        onCmdMessage: function (message) {
            console.log('CMD');
        },     //收到命令消息
        onAudioMessage: function (message) {
            console.log("Audio");
        },   //收到音频消息
        onLocationMessage: function (message) {
            console.log("Location");
        },//收到位置消息
        onFileMessage: function (message) {
            console.log("File");
        },    //收到文件消息
        onVideoMessage: function (message) {

        },   //收到视频消息
        onPresence: function (message) {

        },       //收到联系人订阅请求（加好友）、处理群组、聊天室被踢解散等消息
        onRoster: function (message) {
            console.log('Roster');
        },         //处理好友申请
        onInviteMessage: function (message) {
            console.log('Invite');
        },  //处理群组邀请
        onOnline: function () {
            console.log('onLine');
        },                  //本机网络连接成功
        onOffline: function () {
            console.log('offline');
        },                 //本机网络掉线
        onError: function (message) {
            console.log('Error');
            console.log(message);
            if (message && message.type == 1) {
                console.warn('连接建立失败！请确认您的登录账号是否和appKey匹配。')
            }
        },           //失败回调
        onBlacklistUpdate: function (list) {
            // 查询黑名单，将好友拉黑，将好友从黑名单移除都会回调这个函数，list则是黑名单现有的所有好友信息
            console.log(list);
        }     // 黑名单变动
    });
</script>

<script>
    localStorage.clear();


    $(".reBtn").click(function () {
        if ($(".reChecked").prop('checked')) {
            $(this).find('input[type=checkbox]').prop('checked', false);
        } else {
            $(this).find('input[type=checkbox]').prop('checked', true);
        }
    });

    function login() {
        var phone = $("#phone").val();
        var password = $("#password").val();
        if ($(".reChecked").prop('checked')) {
            var remember = 1;
        } else {
            var remember = 0;
        }
        if (phone == '') {
            alert("请输入手机号！");
            return;
        } else if (!/^1[345678]\d{9}$/.test(phone)) {
            alert("手机号格式错误！");
            return;
        } else if (password == '') {
            alert("请输入密码！");
            return;
        } else if (!/^(\w){6,20}$/.test(password)) {
            alert("密码必须是6-20个字母、数字、下划线！");
            return;
        }
        var p = {phone: phone, password: password, type: 1, remember: remember};
        $.post("<?php echo U('Public/waplogin');?>", p, function (ret) {
            var ret = jQuery.parseJSON(ret);
            if (ret.code == 200) {
                //alert(ret.msg)
                console.log(JSON.stringify(ret.data));

                var id = para('id')
                if (id > 0) {
                    //alert(para('fenxiang'));
                    if (para('fenxiang') == 1) {
                        location.href = '/home/user/personal_mycrowd_funding_jk?id=' + id;
                    } else {
                        location.href = '/home/user/crowd_funding_friends2?id=' + id;
                    }

                } else {

                    var option = {
                        username: phone,
                        password: '123456',
                        nickname: '',
                        appKey: WebIM.config.appkey,
                        success: function () {
                            console.log('regist success!');
                            var options = {
                                apiUrl: WebIM.config.apiURL,
                                user: phone,
                                pwd: "123456",
                                appKey: WebIM.config.appkey
                            };
                            conn.open(options);
                        },
                        error: function () {
                            console.log('regist error');
                            var options = {
                                apiUrl: WebIM.config.apiURL,
                                user: phone,
                                pwd: "123456",
                                appKey: WebIM.config.appkey
                            };
                            conn.open(options);

                        },
                        apiUrl: WebIM.config.apiURL
                    };
                    conn.signup(option);

                    location.href = '/home/user/index';
                }


            } else {
                alert(ret.msg)
            }
        });


    }

    function yhxy() {
        location.href = '/home/public/yhxy';
    }

    //qq登录
    function sanfangqq() {
        location.href = '/Home/Public/qq_login';

    }

    //微博登录
    function initWBBind() {
        location.href = '/home/public/wb_login';

    }


</script>
</html>