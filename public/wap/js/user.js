function doRefreshVerfiy() {
    var verify=$('#verify').attr('src');
    if(verify){
        var verifyUrl= verify.split('?');
        $('#verify').attr('src', verifyUrl[0] + '?' + Math.random());
    }
}

var countdown = 30;
function settime() {
    if (countdown == 0) {
        $("#getSmsCode").removeAttr("disabled");
        $("#getSmsCode").val("发送验证码");
        $("#getSmsCode").html("发送验证码");
        countdown = 30;
        return;
    } else {
        $("#getSmsCode").attr("disabled", true);
        $("#getSmsCode").val("重新发送(" + countdown + ")");
        $("#getSmsCode").html("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () {
        settime();
    }, 1000);
}


function doLoginPost(){
    var phone = $("#username").val();
    var password = $("#password").val();
    if (phone == "" || password == "") {
        ui_alert("请输入手机号和密码!");
    }else if (validatePhoneNumber(phone) == false) {
        ui_alert("请输入正确的手机号!");
    } else if (!checkValidPasswd(password)) {
        ui_alert("请输入6-10位英文数字组合的密码");
    } else{
        var param = {
            "username": phone,
			"password": password
           // "password": hex_md5(password),
        };
        $("#login").attr("disabled", "disabled");
        ajax_jquery({
            url: '/mobile/user/login?t='+Math.random(),
            data:param,
            success:function(resp){
                if (resp.code == "1" ) {
                        window.location.href = "/mobile";
                } else {
					if (typeof(resp.msg) == 'string') {
						ui_alert(resp.msg);
					}                 
                }
            }
        });
        $("#login").removeAttr("disabled");
    }
    return false;
}
function doBindLoginPost(){//TODO:bind wechat openid

    var phone = $.trim($('#userName').val());
    var smsCode = $.trim($("#smsCode").val());
    var password = $("#password").val();

    if (phone == "") {
        ui_alert("请输入手机号!");
    } else if (!validatePhoneNumber(phone)) {
        ui_alert("请输入正确的手机号!");
    } else if (smsCode == "") {
        ui_alert("请输入手机验证码");
    } else if (password == "") {
        ui_alert("请输入新密码");
    } else if (!checkValidPasswd(password)) {
        ui_alert("请输入6-10位英文数字组合的密码");
    } else {
        var param = {
            "userName": phone,
            "smsCode": smsCode,
            "password": hex_md5(password)
        };
        $("#login").attr("disabled", "disabled");
        ajax_jquery({
            url: 'mobile/user/ajax_login_check?t='+Math.random(),
            data:param,
            success:function(resp){
                if (resp.status == "1" && resp.error == "00000000") {
                    if( typeof(resp.data.redirectUrl) == 'string'){
                        window.location.href = resp.data.redirectUrl;
                    }else{
                        window.location.href = "/mobie/index/index.html";
                    }
                } else {
                    ajaxAlertMsg(resp);
                }
            }
        });
        $("#login").removeAttr("disabled");
    }
    return false;
}

function doRegisterPost() {
    var phone = $.trim($('#username').val());
    var smsCode = $.trim($("#smsCode").val());
    var password = $("#password").val();
    var icode = $.trim($('#invite').val());
    var icodeSkip = $('#invite').attr('data-skip');
    var download  = $('#download').attr('data-skip')

    if (phone == "") {
        ui_alert("请输入手机号!");
    } else if (!validatePhoneNumber(phone)) {
        ui_alert("请输入正确的手机号!");
    } else if (smsCode == "") {
        ui_alert("请输入手机验证码");
    } else if (password == "") {
        ui_alert("请输入密码");
    } else if (!validatePassword(password)) {
        ui_alert("请输入6-10位英文数字组合的密码");
    } else if(download == 1){      //下载页用
        RegdownloadPost(phone,smsCode,password);
    } else if( icode == "" && icodeSkip != 1){
        iCodeEmptyCheck();
    } else {
        var param = {
            "username": phone,
            "smsCode": smsCode,
			"password": password,
           // "password": hex_md5(password),
            "icode":icode
        };
        if(typeof(redirect_uri) != 'undefined' && redirect_uri) {
            param['redirect_uri'] = redirect_uri;
        }
        ajax_jquery({
            url: '/mobile/user/register?t=' + Math.random(),
            data: param,
            success: function (resp) {
				if(resp.code == 1){
					ui_alert("注册成功!", function () {
                         window.location.href = '/mobile' ;            
                     });
				}else{
					if (typeof(resp.msg) == 'string') {
						ui_alert(resp.msg);
					}  
				}
            }
        });
        $("#submit").removeAttr("disabled");
    }
    return false;
}


function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
function newalert(msg){

    var mymodel = myApp.modal({
        title: '<div class="close-btn"><img src="/public/wap/images/x.png" width="20"/></div>',
        text: msg
    });
    $(".modal-inner").css({"padding-top":"50px","padding-bottom":"50px"});
    $$('.close-btn').on(even, function () {
        myApp.closeModal(mymodel);
    });
}
function doResetPasswordPost() {
    var phone = $.trim($('#username').val());
    var smsCode = $.trim($("#smsCode").val());
    var newPassword = $("#password").val();
    var idcard  = $("#idcard").val();
    var is_realname = $(".is_realname").val();
    if (phone == "") {
        ui_alert("请输入手机号!");
    } else if (!validatePhoneNumber(phone)) {
        ui_alert("请输入正确的手机号!");
    } else if (smsCode == "") {
        ui_alert("请输入手机验证码");
    } else if (newPassword == "") {
        ui_alert("请输入新密码");
    } else if (!validatePassword(newPassword)) {
        ui_alert("请输入6-10位英文数字组合的密码");
    } else if (idcard=="" && is_realname==1) {
        ui_alert("请输入正确的身份证后四位!");
    } else {
        var param = {
            "username": phone,
            "smsCode": smsCode,
            //"newPassword": hex_md5(newPassword),
			"newPassword": newPassword,
            "idcard": idcard
        };

        ajax_jquery({
            url: 'reset?t=' + Math.random(),
            data: param,
            success: function (resp) {
                if (resp.code == "1") {
                    ui_alert("修改成功", function () {
                        window.location.href = 'login';
                    });
                } else {
                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
                        ui_alert(resp.msg);
                    } else {
                        ui_alert("找回密码请求失败");
                    }
                }
            }
        });
    }
    return false;
}

function iCodeEmptyCheck(){
    ui_ask('您还未输入车商代码，<br/>输入车商代码，<br/>无需审核即可申请垫资。', '直接注册', '去输入',function () {
        $('#invite').val("");
        $('#invite').attr('data-skip', '1');
        doRegisterPost();
        return false;
    },function () {
        $('#invite').focus();
        return false;
    });
}

function iCodeErrorCheck(){
    ui_ask('您输入的邀请码无效，<br/>请重新输入。', '直接注册', '重新输入',function () {
        $('#invite').val("");
        $('#invite').attr('data-skip', '1');
        doRegisterPost();
        return false;
    },function () {
        $('#invite').focus();
        return false;
    });
}

function sendRestPhoneSmsCode(type) {
    return sendSmsCode('resetphonecode', type);
}

function sendRestPasswordSmsCode(type) {
    return sendSmsCode('resetpwcode', type);
}

function sendRegSmsCode(type) {
    return sendSmsCode('smscode', type);
}

function sendBindSmsCode(type) {
    return sendSmsCode('bindwchatcode', type);
}

function sendSmsCode(send_code, type) {

    var phone = $.trim($('#username').val());
    var verifycode = $.trim($("#rvalicode").val());

    if (phone == "") {
        ui_alert("请输入手机号!");
        return;
    } else if (!validatePhoneNumber(phone)) {
        ui_alert("请输入正确的手机号!");
        return;
    } else if (verifycode == "" && send_code!='resetphonecode') {
        ui_alert("请输入图形验证码");
        return;
    } else {
        var param = {
            "phone": phone,
            "verifycode": verifycode,
            "send_code": send_code,
            "send_type": type
        };
		
		if (type == "voice") {					//TODO:record phone num
			 ui_alert("请稍后再试或联系客服");
			 return;
		}		
		
        ajax_jquery({
            url: '/mobile/open/sendSmsCode?t=' + Math.random(),
            data: param,
            success: function (resp) {            
                if (resp.code == "1") {
                    if (type == "voice") {
                        ui_alert('验证码将以电话形式通知到您，请注意接听', null, '获取语音验证码');
                    } else if (type == "sms") {  
                    	//$(".none-box").show();
                    	//$(".is_realname").val(1);
                        settime();
                    }
                } else {
                	doRefreshVerfiy();
                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
                    	ajaxAlertMsg(resp);
                    } else {
                        ui_alert("发送验证码失败");
                    }
                }
            }
        });
    }
    //return false;
}