function ui_alert(classname,message){
        jQuery('.alert').removeClass('alert-error alert-success').children('span').text(message).end()
                .addClass(classname).show();
        setTimeout("$('.alert').slideUp('slow')",3000);
    };
jQuery('.alert > .close').click(function(){
    jQuery('.alert').removeClass('alert-error alert-success').hide();
});

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
        ui_alert('alert-error','登录超时，请重新登录');
        // window.location.href = "/mobile/user/login"; //如果超时就处理 ，指定要跳转的页面
    } else if (session_status == 'Empty') {
        ui_alert('alert-error','权限限制，请联系管理员');
    } else if (textStatus == 'timeout') {
        ui_alert('alert-error','加载超时，请重试');
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
        ui_alert("alert-error","请输入手机号!");
    }else if (!validatePhoneNumber(mobile)) {
        ui_alert("alert-error","请输入正确的手机号!");
    } 
    ajax_jquery({
        url: apiUrl + '/business/user/sendSmsVerify',
        data: {
            "mobile": mobile,
         },
        success: function (resp) {
            if (resp.code == "1") {
                ui_alert("alert-success","验证码发送成功,请注意查收");
            } else {
                if (typeof(resp.msg) == 'string' && resp.msg != '') {
                    ui_alert("alert-error",resp.msg);
                } else {
                    ui_alert("alert-error","验证码发送失败");
                }
                return false;
            }
        }
    });
}