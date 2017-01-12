//撤销订单
function hideOrder(id,type){
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
    }