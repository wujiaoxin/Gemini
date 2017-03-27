var Login = function () {
    
    return {
        //main function to initiate the module
        init: function () {
			ajax_jquery({
	            url: apiUrl + '/api/session/create',
	            data: {
	                'client':'web' 
	            },
	            success: function(resp) { 
	                if(resp.code == 1){
	                    localStorage.setItem('sid', resp.data.sid);
	                }else{
	                    if (typeof(resp.msg) == 'string') {
	                        errorAlert(resp.msg);
	                    }  
	                }
	            }
	        }); 


            jQuery('#loginBtn').click(function(){
	        	var username = jQuery('#login-username').val();
	        	var password = jQuery('#login-password').val();
	        	if(username == ""){
	        		ui_alert("alert-error","请输入手机号");
	        		return false;
	        	}else if(!validatePhoneNumber(username)){
	        		ui_alert("alert-error","手机号输入错误");
	        		return false;
	        	}else if(password == ""){
					ui_alert("alert-error","请输入密码");
	        		return false;
	        	}else if(!validatePassword(password)){
	        		ui_alert("alert-error","请输入8-16位英文数字组合密码");
	        		return false;
	        	}
		        ajax_jquery({
		            url: apiUrl +'/api/user/login?t='+Math.random(),
		            data:{
		            	"mobile": username,
		            	"password": password
		            },
		            success:function(resp){
		                if (resp.code == "1" ) {
		                		ui_alert("alert-success","登录成功");
		                        window.location.href = "/business/index/index";
		                } else {
		                    if (typeof(resp.msg) == 'string') {
		                        ui_alert(resp.msg);;
		                    }                 
		                }
		            }
		        });
	        });

	        jQuery('#loginBtn').click(function(){
	        	var username = jQuery('#login-username').val();
	        	var password = jQuery('#login-password').val();
	        	if(username == ""){
	        		ui_alert("alert-error","请输入手机号");
	        		return false;
	        	}else if(!validatePhoneNumber(username)){
	        		ui_alert("alert-error","手机号输入错误");
	        		return false;
	        	}else if(password == ""){
					ui_alert("alert-error","请输入密码");
	        		return false;
	        	}else if(!validatePassword(password)){
	        		ui_alert("alert-error","请输入8-16位英文数字组合密码");
	        		return false;
	        	}
		        ajax_jquery({
		            url: apiUrl +'/api/user/login?t='+Math.random(),
		            data:{
		            	"mobile": username,
		            	"password": password
		            },
		            success:function(resp){
		                if (resp.code == "1" ) {
		                		ui_alert("alert-success","登录成功");
		                        window.location.href = "/business/index/index";
		                } else {
		                    if (typeof(resp.msg) == 'string') {
		                        ui_alert(resp.msg);;
		                    }                 
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
	                jQuery('#forget-password').click();
	            }
	        });

	        $('.register-form input').keypress(function (e) {
	            if (e.which == 13) {
	                jQuery('#register-submit-btn').click();
	            }
	        });

	        jQuery('#forget-password').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.forget-form').show();
	        });

	        jQuery('#back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.forget-form').hide();
	        });

	     

	        jQuery('#register-btn').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.register-form').show();
	        });

	        jQuery('#register-back-btn').click(function () {
	            jQuery('.login-form').show();
	            jQuery('.register-form').hide();
	        });
        }

    };

}();
