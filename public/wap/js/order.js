var g_orderType = {'1':'二手车按揭贷款','2':'二手车按揭垫资','3':'新车按揭贷款','4':'新车按揭垫资'};
var g_orderStatus = {'-2':'编辑中','-1':'已撤回','1':'审核通过','2':'审核拒绝','3':'资料审核','4':'额度审核','5':'补充资料'};
//撤销订单
function hideOrder(id,type){
	ui_ask('确认撤销订单？', '确定', '取消',function () {
		ajax_jquery({
			url: '/mobile/order/cancel?id='+id+'&t=' + Math.random(),
			success: function (resp) {
				if (resp.code == 1) {
					ui_alert("提交成功", function () {
						window.location.href = "/mobile/order/index?type="+type;
					});
				} else {
					ajaxAlertMsg(resp);
				}
			}
		});
        return false;
    },function () {
        return false;
    });
}

//身份证识别
function tryOCR(id,type){
            if(type != 'idcard_face'){
                return;
            }
            ajax_jquery({
                type: 'post',
                url: '/mobile/open/tryOCR',
                datatype: 'json',
                data: {
                    "id": id
                },
                success: function(data){
                    if(data.code == 1){
                        $('#idcard_name').val(data.data.name);
                        $('#idcard_num').val(data.data.idcard_num);
                    }else{
                        if (typeof(data.msg) == 'string') {
                            ui_alert(data.msg);
                        }
                    }
                }
            });
        };

//时间戳格式化 2010-12-22 03:34:21
function formatDate(timeStr){
    var timeStr = timeStr*1000;
    var now =new Date(timeStr);
    var year=now.getFullYear();
    var month=now.getMonth()+1;
    var date=now.getDate();
    var hour=fillZero(now.getHours());
    var minute=fillZero(now.getMinutes());
    var second=fillZero(now.getSeconds());
    return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;
};
function fillZero(i){
    if (i<10){
        i="0" + i;
    }
    return i
};

//提交订单审核
function examine(id){
    // var addr =  $("#addr").val();
    var status = $('input[name="examine_status"]').filter(':checked').val();
    if(typeof(status) == "undefined"){
        ui_alert("请审核订单");
    }
    var param = {
        "id": id,
        "status": status,
        "addr": ''
    };
    ajax_jquery({
        url: '/mobile/order/examine?t=' + Math.random(),
        data: param,
        success: function (resp) {
            if(resp.code == 1){
                ui_alert("提交成功!", function () {
                     window.location.href = '/mobile/order' ;
                 });
            }else{
                if (typeof(resp.msg) == 'string') {
                    ui_alert(resp.msg);
                }
            }
        }
    })
};

function getOrderTotal(type,status){
    var data = '';
    ajax_jquery({
        url: apiUrl+ '/api/order/total',
        data: {
            'type': type,
            'status': status
        },
        async:false,
        success: function (resp) {
            if(resp.code == 1){
                data = resp.data.total;
            }else{
                if(resp.code == '-1' || resp.code == '-3'){
                    window.location.href = wechatStaticPath + "/user/login.html";
                }else{
                    ui_alert(resp.msg);
                    data = '-1';
                }

            }
        }
    });
    return data;
}



