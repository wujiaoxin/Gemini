var isimgverify = 0;
localStorage.clear();
var Login = function () {

    return {
        //main function to initiate the module
        init: function () {
			ajax_jquery({
	            url: apiUrl + '/api/session/create?t=' + Math.random(),
	            data: {
	                'client':'web'
	            },
	            success: function(resp) {
	                if(resp.code == 1){
	                    localStorage.setItem('sid', resp.data.sid);
	                }else{
	                    if (typeof(resp.msg) == 'string') {
	                        ui_alert(resp.msg);
	                        return false;
	                    }
	                }
	            }
	        });

            jQuery('#loginBtn').click(function(){
	        	var username = jQuery('#login-username').val();
	        	var password = jQuery('#login-password').val();
	        	var imgverify = jQuery("#login-rvalicode").val();
	        	var remember = jQuery("input[name='remember']").prop('checked');
	        	if(username == ""){
	        		ui_alert("请输入手机号");
	        		return false;
	        	}else if(!validatePhoneNumber(username)){
	        		ui_alert("手机号输入错误");
	        		return false;
	        	}else if(password == ""){
					ui_alert("请输入密码");
	        		return false;
	        	}else if(!validatePassword(password)){
	        		ui_alert("请输入8-16位英文数字组合密码");
	        		return false;
	        	} else if (isimgverify && imgverify == "") {
        			ui_alert("请输入图形验证码");
        			return false;
        		}
        		if(remember){
        			localStorage.setItem('rememberUsername',username);
        		}else {
        			localStorage.removeItem('rememberUsername');
        		}

		        ajax_jquery({
		            url: apiUrl +'/business/login/login?t=' + Math.random(),
		            data:{
		            	"mobile": username,
		            	"password": password,
		            	"imgverify": imgverify
		            },
		            success:function(resp){
		                if (resp.code == "1" ) {
		                		//localStorage.setItem('token',resp.data.token);
		                		if(resp.data == 1){
		                			ui_alert("登录成功","success");
		                			window.location.href = "/guarantee/examine/welcome";
		                		}else if(resp.data == 2){
		                			ui_alert("登录成功","success");
		                			window.location.href = "/business/user/myShop";
		                		}else if(resp.data == 3){
		                			ui_alert("登录成功","success");
		                			window.location.href = "/fund/examine/application";
		                		}else{
		                			ui_alert("权限错误,请联系客服");
		                		}
		                } else {
		                    if(resp.code == "-2" || resp.code == "1001"){
		                        isimgverify = 1;
		                        $(".rvalicode-cont").show();
		                    }else {
		                        isimgverify = 0;
		                        $(".rvalicode-cont").hide();
		                    }
		                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
		                        ui_alert(resp.msg);
		                    } else {
		                        ui_alert("登录失败，请重试!");
		                    }
		                    doRefreshVerfiy();
		                    return false;
		                }
		            }
		        });
	        });

	        jQuery('#resetBtn').click(function(){
	        	var username = jQuery('#reset-username').val();
	        	var password = jQuery('#reset-password').val();
	        	var smsverify = jQuery('#reset-smsVerify').val();
	        	if(username == ""){
	        		ui_alert("请输入手机号");
	        		return false;
	        	}else if(!validatePhoneNumber(username)){
	        		ui_alert("手机号输入错误");
	        		return false;
	        	}else if(smsverify == ""){
					ui_alert("请输入手机验证码");
	        		return false;
	        	}else if(password == ""){
					ui_alert("请输入密码");
	        		return false;
	        	}else if(!validatePassword(password)){
	        		ui_alert("请输入8-16位英文数字组合密码");
	        		return false;
	        	}
	        	 ajax_jquery({
		            url: apiUrl +'/api/user/resetPassword?t='+Math.random(),
		            data:{
		            	'mobile': username,
		            	'smsverify': smsverify,
		            	'newPassword': password
		            },
		            success:function(resp){
		                if (resp.code == "1" ) {
		                		ui_alert("密码重置成功","success");
		                        setTimeout('window.location.href = "/business/login/login";',2000);
		                } else {
		                    if (typeof(resp.msg) == 'string') {
		                        ui_alert(resp.msg);
		                        return false;
		                    }
		                }
		            }
		        });
	        });

	        jQuery('#registerBtn').click(function(){
	        	var username = jQuery('#register-username').val();
	        	var password = jQuery('#register-password').val();
	        	var smsverify = jQuery('#register-smsVerify').val();
	        	var tncChecked = jQuery('#register_tnc').prop("checked");
	        	if(username == ""){
	        		ui_alert("请输入手机号");
	        		return false;
	        	}else if(!validatePhoneNumber(username)){
	        		ui_alert("手机号输入错误");
	        		return false;
	        	}else if(password == ""){
					ui_alert("请输入密码");
	        		return false;
	        	}else if(!validatePassword(password)){
	        		ui_alert("请输入8-16位英文数字组合密码");
	        		return false;
	        	}else if(smsverify == ""){
	        		ui_alert("请输入短信验证码");
	        		return false;
        		}else if(!tncChecked){
        			ui_alert("请点击同意合作协议");
	        		return false;
        		}
		        ajax_jquery({
		            url: apiUrl +'/api/user/reg?t=' + Math.random(),
		            data:{
		            	"mobile": username,
		            	"password": password,
		            	"smsverify": smsverify,
						"authcode":"dealer"
		            },
		            success:function(resp){
		                if (resp.code == "1" ) {
		                		ui_alert("注册成功，请登录","success");
		                        setTimeout('window.location.href = "/business/login/login"',2000);
		                } else {
		                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
		                        ui_alert(resp.msg);
		                    } else {
		                        ui_alert("注册失败，请重试!");
		                    }
		                    return false;
		                }
		            }
		        });
	        });

	        $('.login-form input').keypress(function (e) {
	            if (e.which == 13) {
	                jQuery('#loginBtn').click();
	            }
	        });

	        $('.forget-form input').keypress(function (e) {
	            if (e.which == 13) {
	                jQuery('#resetBtn').click();
	            }
	        });

	        $('.register-form input').keypress(function (e) {
	            if (e.which == 13) {
	                jQuery('#registerBtn').click();
	            }
	        });

	        jQuery('#forget-password').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.forget-form').show();
	            jQuery('.alert').hide();
	        });

	        jQuery('#back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.forget-form').hide();
	            jQuery('.alert').hide();
	        });

	        jQuery('#register-btn').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.register-form').show();
	            jQuery('.alert').hide();
	        });

	        jQuery('#register-back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.register-form').hide();
	            jQuery('.alert').hide();
	        });
        }
    };

}();

	function sendSmsVerify(id) {
		var formName = $(id).attr('data-form');
	    var mobile = $('#'+formName+'-username').val();
	    var imgverify = $('#'+formName+'-rvalicode').val();
	    if (mobile == "") {
	        ui_alert("请输入手机号!");
	    } else if (!validatePhoneNumber(mobile)) {
	        ui_alert("请输入正确的手机号!");
	    } else if (isimgverify && imgverify == "") {
	        ui_alert("请输入图形验证码");
	    } else {
	        var param = {
	            "mobile": mobile,
	            "imgverify": imgverify
	        };
	        ajax_jquery({
	            url: apiUrl + '/api/user/sendSmsVerify?t=' + Math.random(),
	            data: param,
	            success: function (resp) {
	                if (resp.code == "1") {
	                    ui_alert("验证码发送成功,请注意查收","success");
	                    settime();
	                } else {
	                    if(resp.code == "-2" || resp.code == "1001"){
	                        isimgverify = 1;
							$("#regImgVerifyPic").attr("src",apiUrl + "/api/user/getImgVerify?sid=" + localStorage.sid + "&random=" + Math.random());
	                        $(".rvalicode-cont").show();
	                    }else {
	                        isimgverify = 0;
	                        $(".rvalicode-cont").hide();
	                    }
	                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
	                       	ui_alert(resp.msg);
	                    } else {
	                        ui_alert("验证码发送失败");
	                    }
	                    doRefreshVerfiy();
	                    return false;
	                }
	            }
	        });
	    }
	    //return false;
	}

    function doRefreshVerfiy(id) {
	    var verify=$(id).attr('src');
	    if(verify){
	        var verifyUrl= verify.split('?');
	        $(id).attr('src', verifyUrl[0] + '?'+ 'sid=' + localStorage.sid +'&random = ' + Math.random());
	    }
	}


	function getUrlParam(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]); return null;
	}

	// var t = getUrlParam('t');
	// if(t == 1){
	// 	$('#register-btn').click();
	// }
	var rememberUsername = localStorage.getItem('rememberUsername');
	if(rememberUsername){
		$("#username").val('rememberUsername');
	}
