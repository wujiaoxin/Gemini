var g_orderType = {'1':'二手车按揭贷款','2':'二手车按揭垫资','3':'新车按揭贷款','4':'新车按揭垫资'};
var g_orderStatus = {'-2':'编辑中','-1':'已撤回','0':'待提交','1':'审核通过','2':'审核拒绝','3':'资料审核','4':'额度审核','5':'补充资料','10':'初级审核','11':'信用审核','12':'额度审核','13':'财务审核'};
var g_financeStatus ={'0':'','1':'待支付订单费用','2':'支付完成','3':'已放款','4':'已提现'};
var g_repayStatus = {'-2':'审核中','-1':'未还','0':'提前还款','1':'已还','2':'逾期'};
var g_transactionType = {'5':'支付款项','1':'垫资到账','2':'垫资还款','3':'充值','4':'提现','6':'还款','7':'放款'};
var g_dealObj = {'0':'系统','1':'商户'};
var g_examineStatus = {'-1':'处理中','0':'审核拒绝','1':'审核通过'};
var g_dealerStatus = {'3':'待审核','1':'审核通过','9':'终止合作'};
var g_dealerProperty = {'1':'车商','2':'4S店','3':'担保公司'};
var g_creditResult = {'-1':'审核未通过','0':'待审核','1':'通过','2':'更换常用银行卡','3':'更换常用手机号'};
var g_picList = [
    {
        form_key:"orderSupplement_pic_1",
        form_label:"身份证正面照"
    },
    {
        form_key:"orderSupplement_pic_2",
        form_label:"身份证反面照"
    },
    {
        form_key:"orderSupplement_pic_3",
        form_label:"人车合照"
    },
    {
        form_key:"orderSupplement_pic_4",
        form_label:"合格证"
    },
    {
        form_key:"orderSupplement_pic_5",
        form_label:"保单证书"
    },
    {
        form_key:"orderSupplement_pic_6",
        form_label:"购车发票"
    },
    {
        form_key:"orderSupplement_pic_7",
        form_label:"首付凭证"
    },
    {
        form_key:"orderSupplement_pic_8",
        form_label:"合同"
    },
    {
        form_key:"orderSupplement_pic_9",
        form_label:"代扣银行卡"
    },
    {
        form_key:"guarantee_pic_1",
        form_label:"担保函"
    },
    {
        form_key:"newcar_qualified_pic_1",
        form_label:"合格证"
    },
    {
        form_key:"secondcar_pic_1",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_2",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_3",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_4",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_5",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_6",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_7",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_pic_8",
        form_label:"二手车"
    },
    {
        form_key:"secondcar_idcard_face_1",
        form_label:"身份证正面照"
    },
    {
        form_key:"secondcar_idcard_back_1",
        form_label:"身份证反面照"
    },
    {
        form_key:"bankcard_pic_1",
        form_label:"银行卡"
    },
    {
        form_key:"carRegister_pic_1",
        form_label:"车辆登记证"
    },
    {
        form_key:"drivingLicense_pic_1",
        form_label:"车辆行驶证"
    },
    {
        form_key:"evidence_of_payment_1",
        form_label:"付款凭证"
    },
    {
        form_key:"ourContract_pic_1",
        form_label:"合同"
    },
    {
        form_key:"signContract_pic_1",
        form_label:"签约照片"
    },
    {
        form_key:"bankExamine_pic_1",
        form_label:"银行审批证明1"
    },
    {
        form_key:"bankExamine_pic_2",
        form_label:"银行审批证明2"
    },
    {
        form_key:"bankExamine_pic_3",
        form_label:"银行审批证明3"
    },
    {
        form_key:"propertyCertificate_pic_1",
        form_label:"资产证明"
    },
    {
        form_key:"incomeCertificate_pic_1",
        form_label:"收入证明"
    },
    {
        form_key:"transferOwnership_pic_1",
        form_label:"过户后材料"
    },
    {
        form_key:"otherMaterial_pic_1",
        form_label:"其他"
    },
    {
        form_key:"newcar_idcard_face_1",
        form_label:"身份证正面"
    },
    {
        form_key:"newcar_idcard_back_1",
        form_label:"身份证反面"
    },
    {
        form_key:"newcar_bankcard_pic_1",
        form_label:"银行卡"
    },
    {
        form_key:"newcar_Qualified_1",
        form_label:"车辆合格证"
    },
    {
        form_key:"newcar_borrowcontract_1",
        form_label:"借款合同"
    },
    {
        form_key:"newcar_bankExamine_pic_1",
        form_label:"银行贷款协议1"
    },
    {
        form_key:"newcar_bankExamine_pic_2",
        form_label:"银行贷款协议2"
    },
    {
        form_key:"newcar_signContract_pic_1",
        form_label:"签约照片"
    },
    {
        form_key:"secondcar_bankcard_pic_1",
        form_label:"银行卡"
    },
    {
        form_key:"secondcar_carRegister_pic_1",
        form_label:"车辆行驶证"
    },
    {
        form_key:"secondcar_drivingLicense_pic_1",
        form_label:"车辆登记证"
    },
    {
        form_key:"secondcar_borrowContract_pic1",
        form_label:"借款合同"
    },
    {
        form_key:"secondcar_bankExamine_pic_1",
        form_label:"银行贷款协议1"
    },
    {
        form_key:"secondcar_bankExamine_pic_2",
        form_label:"银行贷款协议2"
    },
    {
        form_key:"secondcar_signContract_pic_1",
        form_label:"签约照片"
    }];
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
    if(timeStr == '' || timeStr == null ){
        return '';
    }
    var timeStr = timeStr*1000;
    var now =new Date(timeStr);
    var year=now.getFullYear();
    var month=fillZero(now.getMonth()+1);
    var date=fillZero(now.getDate());
    var hour=fillZero(now.getHours());
    var minute=fillZero(now.getMinutes());
    var second=fillZero(now.getSeconds());
    return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;
}

// 格式化日期
function formatDate(timeStr){
    if(timeStr == '' || timeStr == null ){
        return '';
    }
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

//格式化checkBox
function encodeCheckbox(name){
    var data='';
    $("input[name="+name+"]:checkbox:checked").each(function() {
        data += $(this).val() + ',';
    })
    data = data.substring(0, data.length - 1);
    return data;
}

//初始化checkBox
function initCheckBox(name){
    var initData = {};
    if( typeof(info[name]) == "string" && info[name] != ''){
        initData = info[name].split(',');
        $("input[name="+name+"]:checkbox").each(function() {
            var thisValue = $(this).val();
            var isInArray = initData.indexOf(thisValue);
            if(isInArray != '-1'){
                $(this).click();
            }
        })
    }
}
