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