var webSiteUrl = 'http://192.168.0.119:801';

if($api.getStorage('user') != null && $api.getStorage('user') != "undefined"){
    var user=$api.getStorage('user');
    var isvip= user.vip;
    var token= user.token;
    var uid=  user.id;
    var utype=  user.type;


    var islogin=1;
}else{

    var islogin=0;
}



var kong='<p style="text-align:center;">暂无数据</p>';




function loginPlay () {
    api.openWin({
        name: 'loginName',
        url: './login.html',
        pageParam: {
            name: 'text'
        }
    });

}

function forgetPlay () {
    api.openWin({
        name: 'loginName',
        url: './forget.html',
        pageParam: {
            name: 'text'
        }
    });

}
function indexPlay () {
    api.openWin({
        name: 'loginName',
        url: './slide.html',
        pageParam: {
            name: 'text'
        }
    });
}
function newsdetail(id){
    api.openWin({
        name: 'news_info',
        url: 'news_info.html',
        reload: true,
        pageParam: {
            id: id,
        }
    });
}
function hetongdetail(id){
    api.openWin({
        name: 'contract_details',
        url: 'contract_details.html',
        reload: true,
        pageParam: {
            id: id,
        }
    });
}
function detailPlay (name,id) {
    api.openWin({
        name: name,
        url: name+'.html',
        pageParam: {
            id: id,
        }
    });
}
function askdetail(id){
    api.openWin({
        name: 'faskdetail',
        url: 'faskdetail.html',
        pageParam: {
            id: id,
        }
    });
}
function lawyer (id) {
    api.openWin({
        name: 'lawyer_details',
        url: './lawyer_details.html',
        pageParam: {
            id: id,
        }
    });
}
function lawyerdynamic (id) {
    api.openWin({
        name: 'lawyer_dynamic_details',
        url: './lawyer_dynamic_details.html',
        pageParam: {
            id: id,
        }
    });
}
function alldynamic (id) {
    api.openWin({
        name: 'lawyer_alldynamic',
        url: './lawyer_alldynamic.html',
        pageParam: {
            id: id,
        }
    });
}
function lawSpecial (catename,name,typeid) {
    api.openWin({
        name: 'law_special_details',
        url: './law_special_details.html',
        pageParam: {
            catename: catename,
            name: name,
            typeid: typeid,
        }
    });
}

function aaplay (name,url) {
    api.openWin({
        name: name,
        url:  url,
        reload: true,
        pageParam: {

        }
    });
}
function paplay (name,id) {
    api.openWin({
        name: name,
        url:  name+'.html',
        reload: true,
        pageParam: {
            id:id
        }
    });
}

$(function () {
    window['adaptive'].desinWidth = 750;
    window['adaptive'].baseFont = 28;
    window['adaptive'].maxWidth = 750;
    window['adaptive'].init();



});
//计算弹出框偏移值
function boxCentre(name){
    var add_W = name.width()/2;
    var add_H = name.height()/2;
    name.css({
        "margin-left": -add_W,
        "margin-top": -add_H,
    });
};


