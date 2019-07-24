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
$(document).on('click','.back_btn',function(){
    history.back(-1)
});

function dtdetail(id) {
    window.location.href='/Home/User/lawyer_dynamic_details?id='+id;
}

function zhuanfa(id) {
    if(token==''||token==null||token==undefined || utype!=1){
        alert('您尚未登录,请登录')
        aaplay('login_account');
        return false;
    }
	window.location.href='/Home/User/zhuanfa_dynamic?id='+id;
}

function lawyer(id) {
    window.location.href='/Home/User/lawyer_details?id='+id;
}
function mind_pay(id,money) {
    window.location.href='/Home/User/mind_pay?id='+id+'&money='+money;
}
var kong='<p style="text-align:center;">暂无数据</p>';


function para(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}

function aaplay(name) {
    window.location.href='/Home/User/'+name;
}
function paplay(name,id) {
    window.location.href='/Home/User/'+name+'?id='+id;
}


function getScrollTop(){
    var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
    if(document.body){
        bodyScrollTop = document.body.scrollTop;
    }
    if(document.documentElement){
        documentScrollTop = document.documentElement.scrollTop;
    }
    scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
    return scrollTop;
}
//文档的总高度
function getScrollHeight(){
    var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
    if(document.body){
        bodyScrollHeight = document.body.scrollHeight;
    }
    if(document.documentElement){
        documentScrollHeight = document.documentElement.scrollHeight;
    }
    scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
    return scrollHeight;
}
//浏览器视口的高度
function getWindowHeight(){
    var windowHeight = 0;
    if(document.compatMode == "CSS1Compat"){
        windowHeight = document.documentElement.clientHeight;
    }else{
        windowHeight = document.body.clientHeight;
    }
    return windowHeight;
}
window.onscroll = function(){
    if(getScrollTop() + getWindowHeight()  + 100 >=  getScrollHeight() && getScrollTop() + getWindowHeight() + 100 < getScrollHeight()+30){
        try {
            if(typeof toDoRequest === "function") { //是函数    其中 FunName 为函数名称
                toDoRequest();
            } else { //不是函数


            }
        } catch(e) {}

        console.log("getScrollTop:"+getScrollTop()+"**getWindowHeight:"+getWindowHeight()+"**getScrollHeight:"+getScrollHeight())
    }

};

function personal () {
    if(token=='' || utype!=1){
        aaplay('login_account')

    }else{
        aaplay('personal')
    }
}
function personal_mymutualaid(){
    if(token=='' || utype!=1){
        aaplay('login_account')
    }else{
        aaplay('personal_mymutualaid')
    }
}
function indexPlay () {
    location.href='/Home/User/index'

}
function newsdetail(id){
    location.href='/Home/User/news_info?id='+id

}
function hetongdetail(id){
    location.href='/Home/User/contract_details?id='+id

}
function detailPlay (name,id) {
    location.href='/Home/User/'+name+'?id='+id

}
function askdetail(id){
    location.href='/Home/User/consult_details?id='+id

}

function search (key,type) {
    if(type==2){
        location.href='/Home/User/search_lawyer_details?key='+key

    }else{
        location.href='/Home/User/contract?key='+key

    }
    
}

function lawyerdynamic (id) {
    location.href='/Home/User/lawyer_dynamic_details?id='+id

}
function alldynamic (id) {
    location.href='/Home/User/lawyer_alldynamic?id='+id

}
function lawSpecial (catename,name,typeid) {
    location.href='/Home/User/law_special_details?typeid='+typeid+'&name='+name+'&catename='+catename;

}







