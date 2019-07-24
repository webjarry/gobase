//var webSiteUrl = 'http://192.168.0.119:801';
var webSiteUrl = 'http://www.test2.test';
$(function () {
    window['adaptive'].desinWidth = 750;
    window['adaptive'].baseFont = 28;
    window['adaptive'].maxWidth = 750;
    window['adaptive'].init();



});
function isWeiXin(){
    //window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
    var ua = window.navigator.userAgent.toLowerCase();
    //通过正则表达式匹配ua中是否含有MicroMessenger字符串
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
if(isWeiXin()){
    $('.pay_list ul li').eq(0).hide()
}
function personal () {
    if(token==''){
        aaplay('login_account')

    }else{
        aaplay('personal')
    }
}
function lawyer(id) {
    window.location.href='/Home/Lawyer/lawyer_details?id='+id;
}
function sysmsg(id) {
    window.location.href='/Home/Lawyer/msg_detail?id='+id;
}
function newsdetail(id) {
    window.location.href='/Home/Lawyer/news_info?id='+id;
}
function askdetail(id) {
    window.location.href='/Home/Lawyer/faskdetail?id='+id;
}

function dtdetail(id) {
    window.location.href='/Home/Lawyer/lawyer_dynamic_details?id='+id;
}
function hetongdetail(id) {
    window.location.href='/Home/Lawyer/contract_details?id='+id;
}
$(document).on('click','.back_btn',function(){
    history.back(-1)
});

var kong='<p style="text-align:center;">暂无数据</p>';

function para(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}
function aaplay(name) {
    window.location.href='/Home/Lawyer/'+name;
}
function paplay(name,id) {
    window.location.href='/Home/Lawyer/'+name+'?id='+id;
}






