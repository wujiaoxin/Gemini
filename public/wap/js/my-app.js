var myApp = new Framework7({
    modalButtonCancel: "取消",
    modalButtonOk: "确认"
});
var $$ = Framework7.$;
var even = 'click' || 'touchstart';
var _android = navigator.userAgent.match(/linux/i) || navigator.userAgent.match(/android/i) || navigator.platform.match(/android/i) ? true : false;
var _iOS = navigator.userAgent.match(/(ipod|ipad|iphone)/i) ? true : false;
var modules = {
    mylink: function () {
        $$(".mylink").on(even, function () {
            var pageUrl = $$(this).attr("href");
            location.href = pageUrl;
        })
    },
    swiper: function(){
        var mySwiper = myApp.swiper('.swiper-container', {
            pagination:'.swiper-container .swiper-pagination',
            paginationClickable :true,
            autoplay : 5000,//自动播放时间
            spaceBetween: 10
        });
    }
}
var mainView = myApp.addView('.view-main', {
    dynamicNavbar: true
});

modules.mylink();
$$(document).on('pageInit', function (e) {
    modules.mylink();
});
//error
function ui_error(msg, fn, title) {
    if (fn) {
        myApp.alert(msg, title ? "<strong>" + title + "</strong>" : '', function () {
            fn();
        });
    } else {
        myApp.alert(msg, title ? "<strong>" + title + "</strong>" : '');
    }
}
/*function myurl(url){
    location.href = url;
}*/
$$(".myhref").on(even, function () {
    var pageUrl = $$(this).attr("data-url");
    location.href = pageUrl;
});
function ui_alert(msg, fn, title, btn) {
    var mymodel = myApp.modal({
        title: title ? title + '<div class="close-btn"><img src="/public/wap/images/x.png" width="20"/></div>' : '<div class="close-btn"><img src="/public/wap/images/x.png" width="20"/></div>',
        text: msg,
        buttons: [
            {
                text: btn || '确认',
                onClick: function () {
                    if (fn) {
                        fn();
						myApp.closeModal(mymodel);
                    } else {
                        myApp.closeModal(mymodel);
                    }
                }
            }
        ]
    });
    $$('.close-btn').on(even, function () {
        myApp.closeModal(mymodel);
    });
}


function ui_ask(msg, ok_text, cancel_text, ok, cancel) {
    var modal = myApp.modal({
        title: msg,
        text: '',
        afterText: '',
        buttons: [
            {
                text: cancel_text,
                bold: true,
                onClick: function () {
                    if (cancel) {
                        cancel();
                    }
                }
            },
            {
                text: ok_text,
                bold: true,
                onClick: function () {
                    if (ok) {
                        ok();
                    }
                }
            },
        ]
    });
}

//ok(callback) cancel(callback)
function ui_confirm(msg, callback_ok, callback_cancel) {
    myApp.confirm(msg, '提示', function () {
        if (callback_ok) {
            callback_ok();
        }
    }, function () {
        if (callback_cancel) {
            callback_cancel();
        }
    });
}

/*common.js*/
function ajax_jquery(options) {
    if (options == undefined) {
        options = new Object();
    } else if (typeof options != 'object') {
        return;
    } else {
    }
    var options_default = {
        url: '/mobile/index',
        type: 'POST',
        dataType: 'json',
        beforeSend: ajaxLoading(),
        data: new Object(),
        success: _ajax_success,
        error: _ajax_error,
        complete: function (data) {
            $("#loading").hide();
        },
        timeout: 60000,
        async: true,
    };
    var options_merge = new Object();
    $.extend(options_merge, options_default, options);

    $.ajax(options_merge);
}

function ajaxLoading() {
    $("#loading").show();
}

function ajaxAlertMsg(resp, ok_msg, fn) {
    if (resp.code == "1" ) {
        var message = ok_msg ? ok_msg : (resp.msg ? resp.msg : '成功');
        ui_alert(message, fn);
    } else if (resp.code == '75000015') {
        //var message = resp.msg ? resp.msg : '您还未登录，请先登录';
        var message = '您还未登录，请先登录';
        ui_alert(message, function () {
            window.location.href = "login"; //如果超时就处理 ，指定要跳转的页面
        },'','去登录');
    } else {
        var message = resp.msg ? resp.msg : '系统错误，请联系客服';
        ui_alert(message);
    }
    return;
}

/**
 * ajax 请求失败时调用此函数
 * @param XMLHttpRequest XMLHttpRequest对象
 * @param textStatus 错误信息 可以是null 、"timeout" 、"error" 、"notmodified" 或 "parsererror"。
 * @param errorThrown 捕获的异常对象（可选）
 */
function _ajax_error(XMLHttpRequest, textStatus, errorThrown) {
    // 通常 textStatus 和 errorThrown 之中只有一个会包含信息
//    this; // 调用本次AJAX请求时传递的options参数
    var session_status = XMLHttpRequest.getResponseHeader("Session-Status"); //通过XMLHttpRequest取得响应头，Session-Status，
    if (session_status == 'TimeOut') {
        ui_alert('登录超时，请重新登录', function () {
            window.location.href = "/mobile/user/logout"; //如果超时就处理 ，指定要跳转的页面
        });
    } else if (session_status == 'Empty') {
        ui_error('权限限制，请联系管理员');
    } else if (textStatus == 'timeout') {
        ui_error('加载超时，请重试');
    } else {
        console.log("XHR="+XMLHttpRequest+"\ntextStatus="+textStatus+"\nerrorThrown=" + errorThrown);
    }
}

/**
 * 请求成功后的回调函数
 * @param data 由服务器返回，并根据dataType参数进行处理后的数据
 * @param textStatus 描述状态的字符串
 */
function _ajax_success(data, textStatus) {
    // data 可能是 xmlDoc, jsonObj, html, text, 等等...
    this; // 调用本次AJAX请求时传递的options参数
}

function trim(str) {
    if (typeof( str ) == 'string') {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    } else {
        return '';
    }
}

/* 判断是否是移动设备 */
function is_mobile() {
    return navigator.userAgent.match(/mobile/i);
}

/* 验证数据类型 */
function validate(data, datatype) {
    if (datatype.indexOf("|")) {
        tmp = datatype.split("|");
        datatype = tmp[0];
        data2 = tmp[1];
    }
    switch (datatype) {
        case "require":
            if (data == "") {
                return false;
            } else {
                return true;
            }
            break;
        case "email":
            var reg = /^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$/g;
            return reg.test(data);
            break;
        case "number":
            var reg = /^[0-9]+\.{0,1}[0-9]{0,3}$/;
            return reg.test(data);
            break;
        case "html":
            var reg = /<...>/;
            return reg.test(data);
            break;
        case "eqt":
            data2 = $("#" + data2).val();
            return data >= data2
            break;
    }
}

function validateChinese(str) {
    var reg = /.*[\u4e00-\u9fa5]+.*$/;
    if (!reg.test(str)) {
        return false;
    } else {
        return true;
    }
}

function validateRealName(realName) {
    var reg = /^[\u4E00-\u9FA5]+$/;
    if (!reg.test(realName)) {
        return false;
    } else {
        return true;
    }
}
//验证手机号
function validatePhoneNumber(mobile) {
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1})|145|147|222)+\d{8})$/;
    if (!myreg.test(mobile)) {
        return false;
    } else {
        return true
    }
}

//验证密码
function validatePassword(pass) {
    if (pass.length < 6 || pass.length > 10 || pass.match(/[^a-zA-Z0-9]+/)) {
        return false;
    }
    var ls = 0;
    if (pass.match(/(([a-z])|([A-Z]))+/)) {
        ls++;
    }
    if (pass.match(/([0-9])+/)) {
        ls++;
    }
    if (ls < 2) {
        return false;
    } else {
        return true;
    }
}

function checkValidPasswd(str) {
    //var reg = /^[x00-x7f]+$/;
    var reg = /[a-zA-Z]+(?=[0-9]+)|[0-9]+(?=[a-zA-Z]+)/g;
    if (!reg.test(str)) {
        return false;
    }
    if (str.length < 6 || str.length > 10) {
        return false;
    }
    return true;
}

//用户名验证
function validateUsername(username) {
    var myreg = /^[\u4E00-\u9FA5a-zA-Z0-9_]{3,20}$/;
    if (!myreg.test(username)) {
        return false;
        //汉字 英文字母 数字 下划线组成，3-20位
    } else {
        return true
    }
}

function validateSmscode(checkcode) {
    var patn = /^[^\s?<>\'\"!@%#$~&*():;]*$/;
    if (!patn.test(checkcode)) {
        return false;
    } else {
        return true;
    }
}

function validateBankNum(banknum) {
    if (banknum.length < 16 || banknum.length > 19) {
        return false;
    } else {
        var num = /^\d*$/;  //全数字
        if (!num.exec(banknum)) {
            return false;
        } else {
            return true;
        }
    }
}

//验证身份证格式
function validateIdCard(pId) {
//检查身份证号码

    var arrVerifyCode = [1, 0, "x", 9, 8, 7, 6, 5, 4, 3, 2];
    var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    var Checker = [1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1];

    if (pId.length != 15 && pId.length != 18)    return false;

    var Ai = pId.length == 18 ? pId.substring(0, 17) : pId.slice(0, 6) + "19" + pId.slice(6, 16);

    if (!/^\d+$/.test(Ai))  return false;

    var yyyy = Ai.slice(6, 10), mm = Ai.slice(10, 12) - 1, dd = Ai.slice(12, 14);

    var d = new Date(yyyy, mm, dd), now = new Date();
    var year = d.getFullYear(), mon = d.getMonth(), day = d.getDate();

    if (year != yyyy || mon != mm || day != dd || d > now || year < 1940) return false;

    for (var i = 0, ret = 0; i < 17; i++)  ret += Ai.charAt(i) * Wi[i];
    Ai += arrVerifyCode[ret %= 11];

    return pId.length == 18 && pId != Ai ? false : true;
}

if ($("#userinfo-alt").length >= 1) {
    alertWin($("#userinfo-alt"));
}
function alertWin(obj) {
    $(".wincon").fadeIn();
    $(".wincon-con").hide();
    obj.fadeIn();
}

$(".wincon-bg,.close-btn,.guanbi-btn").click(function (e) {
    $(".wincon").fadeOut();
    e.stopPropagation();
});

function toBorrowMultiple(flag) {
    var param = {
        "borrow_type": 1,
    };
    ajax_jquery({
        url: appPath + '/Borrow/ajax_can_borrow?t=' + Math.random(),
        data: param,
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (resp.data.can_borrow == 1) {
                    location.href = appPath + '/BorrowMultiple/index.html';
                } else if (resp.data.borrow_status == 4 || resp.data.borrow_status == 7) {
                    location.href = appPath + '/BorrowMultiple/index.html';
                } else if (resp.data.borrow_status == 0 && typeof(flag) != 'undefined' && flag == 1) {
                    var write_info = '<div class="simg zmbg"><img src="/public/wap/images/icon-write_green.png" width="100%"/></div>' +
                        '<b>补充资料</b><br/>' +
                        '尊敬的用户，请先填写现金分期专属资料，才能享受3000-10000的大额现金分期服务。';
                    ui_alert(write_info, function () {
                        location.href = appPath + '/UserSupply/more.html';
                    }, '', "去填写");
                    $(".modal-button").css({"background": "#fff", "color": "#666"});
                } else if (resp.data.borrow_status == 16 && typeof(flag) != 'undefined' && flag == 1) {
                    var write_info = '<div class="simg qbg"><img src="/public/wap/images/icon-write_blue.png" width="100%"/></div>' +
                        '<b>填写资料</b><br/>' +
                        '尊敬的用户，请先填写申请资料';
                    ui_alert(write_info, function () {
                        location.href = appPath + '/UserInfo/center.html';
                    }, '', "去填写");
                    $(".modal-button").css({"color": "#666"});
                } else if ( (resp.data.borrow_status == 30 ||
                    resp.data.borrow_status == 31 ||
                    resp.data.borrow_status == 32 ||
                    resp.data.borrow_status == 33 ||
                    resp.data.borrow_status == 34 ||
                    resp.data.borrow_status == 35 ||
                    resp.data.borrow_status == 36) && typeof(flag) != 'undefined' && flag == 1) {
                    var write_info = '<div class="simg zmbg"><img src="/public/wap/images/icon-write_green.png" width="100%"/></div>' +
                        '<b>资料过期</b><br/>' + resp.data.borrow_msg;
                    ui_alert(write_info, function () {
                        if(resp.data.borrow_status == 31){
                            location.href = appPath + '/UserInfo/index.html';
                        }else{
                            location.href = appPath + '/UserInfo/center.html';
                        }
                    }, '', (resp.data.borrow_status == 30 || resp.data.borrow_status == 32) ? "去认证" : (resp.data.borrow_status == 31 ? "去更新" : '去填写'));
                    $(".modal-button").css({"background": "#fff", "color": "#666"});
                } else if (resp.data.borrow_status == 13 || resp.data.borrow_status == 14 || resp.data.borrow_status == 15) {
                    var message = resp.data.borrow_msg ? resp.data.borrow_msg : '您当前状态还不能发起借款';
                    if (typeof(resp.data.jump_type) != 'undefined' && resp.data.jump_type == 2) {
                        ui_alert(message, function () {
                            location.href = appPath + '/BorrowSingle/index.html';
                        }, '', "单期借款");
                    } else {
                        ui_alert(message, function () {
                        }, '', "知道了");
                    }
                    $(".modal-button").css({"background": "#3bc3f6", "color": "#fff"});
                } else {
                    var message = resp.data.borrow_msg ? resp.data.borrow_msg : '您当前状态还不能发起借款';
                    ui_error(message);
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}
function toBorrowSingle(flag) {
    var param = {
        "borrow_type": 2,
    };
    ajax_jquery({
        url: appPath + '/Borrow/ajax_can_borrow?t=' + Math.random(),
        data: param,
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (resp.data.can_borrow == 1) {
                    location.href = appPath + '/BorrowSingle/index.html';
                } else if (resp.data.borrow_status == 4 || resp.data.borrow_status == 7) {
                    location.href = appPath + '/BorrowSingle/index.html';
                } else if (resp.data.borrow_status == 0 && typeof(flag) != 'undefined' && flag == 1) {
                    var write_info = '<div class="simg qbg"><img src="/public/wap/images/icon-write_blue.png" width="100%"/></div>' +
                        '<b>填写资料</b><br/>' +
                        '尊敬的用户，请先填写申请资料。';
                    ui_alert(write_info, function () {
                        location.href = appPath + '/UserInfo/center.html';
						
                    }, '', "去填写");
                    $(".modal-button").css({"color": "#666"});
                }  else if ( (resp.data.borrow_status == 30 ||
                    resp.data.borrow_status == 31 ||
                    resp.data.borrow_status == 32 ||
                    resp.data.borrow_status == 33 ||
                    resp.data.borrow_status == 34 ||
                    resp.data.borrow_status == 35 ||
                    resp.data.borrow_status == 36) && typeof(flag) != 'undefined' && flag == 1) {
                    var write_info = '<div class="simg zmbg"><img src="/public/wap/images/icon-write_green.png" width="100%"/></div>' +
                        '<b>资料过期</b><br/>' + resp.data.borrow_msg;
                    ui_alert(write_info, function () {
                        if(resp.data.borrow_status == 31){
                            location.href = appPath + '/UserInfo/index.html';
                        }else{
                            location.href = appPath + '/UserInfo/center.html';
                        }
                    }, '', (resp.data.borrow_status == 30 || resp.data.borrow_status == 32) ? "去认证" : (resp.data.borrow_status == 31 ? "去更新" : '去填写'));
                    $(".modal-button").css({"background": "#fff", "color": "#666"});
                } else {
                    var message = resp.data.borrow_msg ? resp.data.borrow_msg : '您当前状态还不能发起借款';
                    ui_error(message);
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function toUserDebitcard() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/UserExtend/user_cardlist.html";
                } else {
                    ui_error('请先填写身份认证', function () {
                        window.location.href = appPath + "/UserInfo/center.html";
                    });
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function goToUrl(url){
	if(url){
		window.location.href = url;
	}else{
		return false;
	}	
}

function getUrlParam(name) {  
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); 
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return null;
}

function toUserMessage() {
    window.location.href = appPath + "/UserExtend/message.html";
}
function toBorrowProgress() {
    window.location.href = appPath + "/Borrow/progress.html";
}
function toBorrowRecord() {
    window.location.href = appPath + "/Borrow/record.html";
}
function toUpdatePassword() {
    window.location.href = appPath + "/UserInfo/update_password.html";
}
function toUpdatePhone() {
    window.location.href = appPath + "/UserInfo/update_phone.html";
}
function toUserIdentity() {
    window.location.href = appPath + "/UserInfo/identity.html";
}
function toActivity() {
    window.location.href = appPath + '/Activity/index.html';
}
function toUserInfo() {
    window.location.href = appPath + '/UserInfo/index.html';
}
function toMainHome(){
    window.location.href = appPath + '/Main/index.html';
}
function toBorrowRepay() {
    window.location.href = appPath + '/Repay/index.html';
}
function toBorrowProgress() {
    window.location.href = appPath + '/Borrow/progress.html';
}
function toUserCenter() {
    window.location.href = appPath + "/UserInfo/center.html";
}
function toConsumeTb(){
    window.location.href = appPath + "/UserExtend/consume_tb.html";
}
function toConsumeJd(){
    window.location.href = appPath + "/UserExtend/consume_jd.html";
}
function toConsumePhone(){
    window.location.href = appPath + "/UserExtend/consume_phone.html";
}
function toMoreInternet() {
    window.location.href = appPath + "/UserExtend/more_internet.html";
}
function toMoreAddress() {
    window.location.href = appPath + "/UserExtend/more_address.html";
}
function toMoreCompany() {
    window.location.href = appPath + "/UserExtend/more_company.html";
}
function toMoreEdu() {
    window.location.href = appPath + "/UserExtend/more_edu.html";
}
function toUserJob() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/UserInfo/job.html";
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function toUserCenterRecord() {
    ajax_jquery({
        url: appPath + '/Borrow/uploadUserActionInfo?t=' + Math.random(),
        success: function () {
            window.location.href = appPath + "/UserInfo/center.html";
        }
    });
}

function toUserContact() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/UserInfo/contact.html";
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}
function toUserCreditCard() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/UserExtend/credit_card.html";
                } else {
                    ui_alert('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}
function toUserConsume() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/UserExtend/consume.html";
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}
function toUserSocialSecurity() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    window.location.href = appPath + "/Social/index.html";
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}
function toUserMore() {
	ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                	window.location.href = appPath + "/UserExtend/more.html";
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
 
}
function toHotApp() {
    var u = navigator.userAgent;
    if (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1) {//安卓手机
        window.location.href = appPath + "/Public/hot.html?type=android";
    } else if (u.indexOf('iPhone') > -1) {//苹果手机
        window.location.href = appPath + "/Public/hot.html?type=ios";
    } else {
        window.location.href = appPath + "/Public/hot.html";
    }
}

function toCallPhone(phoneNum) {
	if(phoneNum == null){
		phoneNum = '0571-87813085';
	}
    var u = navigator.userAgent;
    if (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1 || u.indexOf('iPhone') > -1) {//手机
        window.location.href = "tel:"+phoneNum;
    } else {
        ui_alert(phoneNum);
    }
}
function toLogout() {
    ajax_jquery({
        url: appPath + '/User/logout?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                //ui_alert('退出成功', function () {
                    window.location.href = appPath + "/Main/index.html";
                //});
            } else {
                ui_error(resp.msg);
            }
        }
    });
}

function doUserAlipay() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    ajax_jquery({
                        url: appPath + '/UserInfo/ajax_alipay_status?t=' + Math.random(),
                        success: function (resp) {
                            if (resp.status == "1" && resp.error == "00000000") {

                                if (resp.data.is_auth == 0) {
                                    if (resp.data.is_skip == 1) {
                                        enterAlipay();
                                    } else if (resp.data.is_skip == 0 && resp.data.can_skip == 1 && resp.data.is_frist >= 1) { //可以跳过 没有跳过
                                        enterorskipAlipay();
                                    } else {
                                        enterAlipay();
                                    }
                                } else if (resp.data.is_auth == 1) {
                                    if (resp.data.is_expires == 1) {
                                        enterAlipay();
                                    } else {
                                        ui_error(resp.data.auth_tips ? resp.data.auth_tips : '');
                                    }
                                } else {
                                    ui_error('芝麻授权信息读取失败，请稍候重试');
                                }
                            } else {
                                ajaxAlertMsg(resp);
                            }
                        }
                    });
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function enterAlipay() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    ui_ask('进入芝麻验证？', '是', '否', function () {
                        ajax_jquery({
                            url: appPath + '/UserInfo/ajax_alipay_auth_url?t=' + Math.random(),
                            success: function (resp) {
                                if (resp.status == "1" && resp.error == "00000000") {
                                    if (typeof (resp.data.url) == 'string') {
                                        window.location.href = resp.data.url;
                                    } else {
                                        ui_error('芝麻授权地址获取失败');
                                    }
                                } else {
                                    ajaxAlertMsg(resp);
                                }
                            }
                        });
                    });
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function enterorskipAlipay() {
    ajax_jquery({
        url: appPath + '/UserInfo/ajax_is_realname?t=' + Math.random(),
        success: function (resp) {
            if (resp.status == "1" && resp.error == "00000000") {
                if (typeof(resp.data.is_realname) != 'undefined' && resp.data.is_realname == 1) {
                    ui_ask('可跳过或者进行芝麻验证？', '验证', '跳过', function () {
                        ajax_jquery({
                            url: appPath + '/UserInfo/ajax_alipay_auth_url?t=' + Math.random(),
                            success: function (resp) {
                                if (resp.status == "1" && resp.error == "00000000") {
                                    if (typeof (resp.data.url) == 'string') {
                                        window.location.href = resp.data.url;
                                    } else {
                                        ui_error('芝麻授权地址获取失败');
                                    }
                                } else {
                                    ajaxAlertMsg(resp);
                                }
                            }
                        });
                    }, function () {
                        ajax_jquery({
                            url: appPath + '/UserInfo/ajax_alipay_skip?t=' + Math.random(),
                            success: function (resp) {
                                if (resp.status == "1" && resp.error == "00000000") {
                                    ui_alert('芝麻授权跳过操作成功', function () {
                                        window.location.href = appPath + "/UserInfo/center.html?status=2";
                                    });
                                } else {
                                    ajaxAlertMsg(resp);
                                }
                            }
                        });
                    });
                } else {
                    ui_error('请先填写身份认证');
                }
            } else {
                ajaxAlertMsg(resp);
            }
        }
    });
}

function sendSmsCode(type) {
    var param = {
        "send_code": 'borrowcode',
        "send_type": type
    };
    ajax_jquery({
        url: appPath + '/Public/ajax_send_sms?t=' + Math.random(),
        data: param,
        success: function (resp) {
            console.log(resp);
            if (resp.status == "1" && resp.error == "00000000") {
                if (type == "voice") {
                    ui_alert('验证码将以电话形式通知到您，请注意接听', null, '获取语音验证码')
                } else if (type == "sms") {
                    settime();
                }
            } else {
                //doRefreshVerfiy();
                if (typeof(resp.msg) == 'string' && resp.msg != '') {
                    ui_alert(resp.msg);
                } else {
                    ui_alert("发送验证码失败");
                }
            }
        }
    });
    return false;
}
var countdown = 30;
function settime() {
    if (countdown == 0) {
        $("#getcode").removeAttr("disabled");
        $("#getcode").val("发送验证码");
        countdown = 30;
        return;
    } else {
        $("#getcode").attr("disabled", true);
        $("#getcode").val("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () {
        settime();
    }, 1000);
}



(function (doc, win) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth) return;
            if (clientWidth > 640) {
                clientWidth = 640;
            }
            docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
        };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);