var g_orderType = {'1':'二手车按揭贷款','2':'二手车按揭垫资','3':'新车按揭贷款','4':'新车按揭垫资'};
var g_orderStatus = {'-2':'编辑中','-1':'已撤回','0':'待审核','1':'审核通过','2':'审核拒绝','3':'资料审核','4':'额度审核','5':'补充资料'};
var g_financeStatus ={'0':'','1':'待支付订单费用','2':'支付完成','3':'放款中','4':'放款完成'};
var g_repayStatus = {'-2':'审核中','-1':'未还','0':'提前还款','1':'已还','2':'逾期'};
var g_transactionType = {'5':'支付款项','1':'垫资到账','2':'垫资还款','3':'充值','4':'提现'};
var g_dealObj = {'0':'系统','1':'商户'};
var g_examineStatus = {'-1':'处理中','0':'审核拒绝','1':'审核通过'};
var g_dealerStatus = {'3':'待审核','1':'审核通过','9':'终止合作'}
var apiUrl ='';
function ui_alert(msg,type,position){
   $.messager.show(msg, {placement: position,type:type});
};

function ajax_jquery(options) {
    if (options == undefined) {
        options = new Object();
    } else if (typeof options != 'object') {
        return;
    } else {
    }
    var sid =localStorage.getItem('sid');
    var options_default = {
        url: '',
        type: 'POST',
        dataType: 'json',
        data: {
            sid: sid
        },
        success: _ajax_success,
        error: _ajax_error,
        timeout: 60000,
        async: true,
        cache:false
    };
    var options_merge = new Object();
    $.extend(true,options_merge, options_default, options);

    $.ajax(options_merge);
}

function _ajax_success(data, textStatus) {
    // data 可能是 xmlDoc, jsonObj, html, text, 等等...
    this; // 调用本次AJAX请求时传递的options参数
}

function _ajax_error(XMLHttpRequest, textStatus, errorThrown) {
    // 通常 textStatus 和 errorThrown 之中只有一个会包含信息
//    this; // 调用本次AJAX请求时传递的options参数
    var session_status = XMLHttpRequest.getResponseHeader("Session-Status"); //通过XMLHttpRequest取得响应头，Session-Status，
    if (session_status == 'TimeOut') {
        ui_alert('登录超时，请重新登录');
        // window.location.href = "/mobile/user/login"; //如果超时就处理 ，指定要跳转的页面
    } else if (session_status == 'Empty') {
        ui_alert('权限限制，请联系管理员');
    } else if (textStatus == 'timeout') {
        ui_alert('加载超时，请重试');
    } else {
        console.log("XHR="+XMLHttpRequest+"\ntextStatus="+textStatus+"\nerrorThrown=" + errorThrown);
    }
}

// 格式化数字20,000,00.00
function formatAmount(n) {
    if(!n){
        return '0.00';
    }
    n = parseFloat(n).toFixed(2);
    n = n.toString().replace(/\B(?=(?:\d{3})+\b)/g, ',');
    return n;
}

// 格式化日期时间
function formatDatetime(timeStr){
    var timeStr = timeStr*1000;
    var now =new Date(timeStr);
    var year=now.getFullYear();
    var month=now.getMonth()+1;
    var date=now.getDate();
    var hour=fillZero(now.getHours());
    var minute=fillZero(now.getMinutes());
    var second=fillZero(now.getSeconds());
    return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;
}

// 格式化日期
function formatDate(timeStr){
    var timeStr = timeStr*1000;
    var now = new Date(timeStr);
    var year = now.getFullYear();
    var month = fillZero(now.getMonth()+1);
    var date = fillZero(now.getDate());
    return   year+"-"+month+"-"+date;
}

function fillZero(i){
    if (i<10){
        i="0" + i;
    }
    return i
}

//发送短信验证码
function sendSms(id) {
    var formName = $(id).attr('data-form');
    var mobile = $('#'+formName+'-username').val();
    var imgverify = $('#'+formName+'-rvalicode').val();
    if (mobile == "") {
        ui_alert("请输入手机号!");
    }else if (!validatePhoneNumber(mobile)) {
        ui_alert("请输入正确的手机号!");
    }
    ajax_jquery({
        url: apiUrl + '/business/user/sendSmsVerify',
        data: {
            "mobile": mobile,
         },
        success: function (resp) {
            if (resp.code == "1") {
                ui_alert("验证码发送成功,请注意查收","success");
            } else {
                if (typeof(resp.msg) == 'string' && resp.msg != '') {
                    ui_alert(resp.msg);
                } else {
                    ui_alert("验证码发送失败");
                }
                return false;
            }
        }
    });
}

//获取url参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

//验证email
function checkEmail(str){
    var re = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 
    if (!re.test(str)) {
        return false;
    } else {
        return true;
    }
}

//*加密身份证、手机号、银行卡、邮箱
function encryptID(idcard){
    return idcard = idcard.replace(/^(\d{6})\d+(\d{4})$/,"$1********$2");
}

function encryptMobile(mobile){
    return mobile = mobile.replace(/^(\d{3})\d{4}(\d{4})$/, '$1****$2');
}

function encryptBankcard(bankcard){
    return bankcard = bankcard.replace(/\d+(\d{4})$/,"**** **** **** $1");
}

function encryptMail(mail){
    return mail = mail.replace(/^(\w?)(\w+)(\w)(@\w+\.[a-z]+(\.[a-z]+)?)$/, "$1****$3$4");
}

// 初始化三级联动city
function initCity(data){
    if(typeof(data) == "string" && data != ''){
        var city = data.split(',');
        $("#loc_province").select2('val',city[0]).trigger("change");
        $("#loc_city").select2('val',city[1]).trigger("change");
        $("#loc_town").select2('val',city[2]).trigger("change");
        var loc_province = $('#loc_province').select2('data').text;
        var loc_city = $('#loc_city').select2('data').text;
        var loc_town = $('#loc_town').select2('data').text;
        var city_addr = loc_province + loc_city + loc_town;
        return city_addr;
    }
}
