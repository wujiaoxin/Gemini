// 按照指定长度为数字前面补零
function PrefixInteger(num, length) {
    return (Array(length).join('0') + num).slice(-length);
}

function tpl(data){
    var html = '';
    if(typeof(data) =='object'){
    for(var index = 0; index < data.length; index++){
        var job = data[index].access_group_id == '8'?"财务":"业务员";
        var status = '';
        var opera = '';
        if(data[index].status){
            status = '正常';
            opera = '停用';
        }else{
            status = '停用';
            opera = '启用';
        }

        html += '<tr class="odd gradex">'
            +'<td>' + PrefixInteger(index+1,6) + '</td>'
            +'<td>'+ data[index].realname +'</td>'
            +'<td>'+ data[index].mobile +'</td>'
            +'<td>'+ job +'</td>'
            +'<td>'+ formatDate(data[index].reg_time) +'</td>'
            +'<td data-status="'+ data[index].status +'">'+ status +'</td>'
            +'<td><a class="updateStatus" data-id="'+ data[index].mobile +'">'+ opera +'</a></td>'
            +'</tr>';
        }
    }
    return html;
};
$('#table-myStaff tbody').html(tpl(info.sales));

$("input[type=text]").each(function(i){
    var name = $(this).attr('name');
    if(info[name] !='' ){
        $(this).val(info[name]);
    }
});

function btnSentUploader(picName){
    $("#picker_" + picName).SentUploader({
        fileNumLimit:1,
        uploadEvents: {
            uploadComplete:function(file){}
        },
        listName : 'fileList_' + picName,
        hiddenName: 'field_' + picName,
        hiddenValType:1,
        fileSingleSizeLimit:20*1024*1024,
        closeX:true,
        compress:{
            width: 1024,
            quality: 90,
            allowMagnify: false,
            crop: false,
            preserveHeaders: true,
            noCompressIfLarger: false,
            compressSize: 0
        }
    },
    {
        fileType: 'service',
        filename : 'images',
    });
};

$(function(){
    var id = getUrlParam('id');
    TableManaged.init();

    btnSentUploader("dealer_lic_pic");
    btnSentUploader("rep_idcard_pic");
    btnSentUploader("rep_idcard_back_pic");

    initCheckBox('property');
    initCheckBox('forms');

    if(typeof(info['city']) == "string" && info['city'] != ''){
        var city = info['city'].split(',');
        $("#loc_province").select2('val',city[0]).trigger("change");
        $("#loc_city").select2('val',city[1]).trigger("change");
        $("#loc_town").select2('val',city[2]).trigger("change");
    }

    $('#status').val(info['status']);

    //银行卡绑定弹窗
    $('#bankcard-name').val(info.rep);
    $('#bankcard-mobile').val(info.mobile);
    $('#bankcard-idcard').val(info.idno);
    $('#bankcard-account').val(info.priv_bank_account_id);

    $('#bindCardBtn').click(function(){
        var RealName = $('#bankcard-name').val();
        var Mobile = $('#bankcard-mobile').val();
        var IdentificationNo = $('#bankcard-idcard').val();
        var CardNumber = $('#bankcard-account').val();
        var Province = $('.select2_province').select2("val");
        var City = $('.select2_city').select2("val");
        var BankCode = $('.select2_bank').select2("val");
        if(CardNumber == ""){
            ui_alert("请输入银行卡账号");
            return false;
        }else if(!validateBankNum(CardNumber)){
            ui_alert("银行卡账号有误");
            return false;
        }else if(BankCode == ""){
            ui_alert("请选择开户银行");
            return false;
        }else if(Province == ""){
            ui_alert("请选择省份");
            return false;
        }else if(City == ""){
            ui_alert("请选择城市");
            return false;
        }else if(RealName == ""){
            ui_alert("请输入开户人姓名");
            return false;
        }else if(Mobile == ""){
            ui_alert("请输入手机号");
            return false;
        }else if(!validatePhoneNumber(Mobile)){
            ui_alert("请输入正确手机号");
            return false;
        }else if(IdentificationNo == ""){
            ui_alert("请输入开户人身份证号");
            return false;
        }else if(!validateIdCard(IdentificationNo)){
            ui_alert("身份证号输入有误");
            return false;
        }
        ajax_jquery({
            url: apiUrl +'/guarantee/account/bindCard',
            data:{
                "RealName": RealName,
                "Mobile": Mobile,
                "IdentificationNo": IdentificationNo,
                "CardNumber": CardNumber,
                "Province": Province,
                "City": City,
                "BankCode": BankCode
            },
            success:function(resp){
                if (resp.code == "1" ) {
                        ui_alert("银行卡绑定成功","success");
                        window.location.href = "/guarantee/account/index";
                } else {
                    if (typeof(resp.msg) == 'string' && resp.msg != '') {
                        ui_alert(resp.msg);
                    } else {
                        ui_alert("绑定失败，请重试或联系客服!");
                    }
                    return false;
                }
            }
        });
    });

    $('#dealerExamineBtn').click(function(){
        var name = $("#name").val();
        var credit_code = $("#credit_code").val();
        var loc_province = $("#loc_province").val();
        var loc_city = $("#loc_city").val();
        var loc_town = $("#loc_town").val();
        var loc_address = loc_province + ',' + loc_city  + ',' + loc_town;
        var addr = $("#addr").val();
        var lic_validity = $("#lic_validity").val();
        var field_dealer_lic_pic = $("#field_dealer_lic_pic").val();
        var rep = $("#rep").val();
        var idno = $("#idno").val();
        var mobile = $("#mobile").val();
        var mail = $("#mail").val();
        var field_rep_idcard_pic = $("#field_rep_idcard_pic").val();
        var field_rep_idcard_back_pic = $("#field_rep_idcard_back_pic").val();
        var property = encodeCheckbox('property');
        var propertyLen = $("input[name='property']:checked").length;
        var forms = encodeCheckbox('forms');
        var formsLen = $("input[name='forms']:checked").length;
        var bank_account_name = $("#bank_account_name").val();
        var bank_name = $("#bank_name").val();
        var bank_account_id = $("#bank_account_id").val();
        var bank_branch = $("#bank_branch").val();
        var priv_bank_account_name = $("#priv_bank_account_name").val();
        var priv_bank_name = $("#priv_bank_name").val();
        var priv_bank_account_id = $("#priv_bank_account_id").val();
        var priv_bank_branch = $("#priv_bank_branch").val();
        var status = $('#status').val();
        var guarantee_id = $('#guarantee_id').val();
        var descr = $('#descr').val().replace(/\n/g,"").replace(/\s+/g, "");

        if(name == ""){
            ui_alert("请输入企业名称");
            return false;
        }else if(credit_code == ""){
            ui_alert("请输入企业信用代码");
            return false;
        }else if(!loc_province && !loc_city && !loc_town){
            ui_alert("请选择单位所在地");
            return false;
        }else if(addr == ""){
            ui_alert("请填写详细地址");
            return false;
        }else if(lic_validity == ""){
            ui_alert("请填写营业期限");
            return false;
        }else if(field_dealer_lic_pic == ""){
            ui_alert("请上传营业执照照片");
            return false;
        }else if(rep == ""){
            ui_alert("请填写法人姓名");
            return false;
        }else if(idno == ""){
            ui_alert("请填写法人身份证号");
            return false;
        }else if(!validateIdCard(idno)){
            ui_alert("身份证号填写有误");
            return false;
        }else if(mobile == ""){
            ui_alert("请输入手机号");
            return false;
        }else if(field_rep_idcard_pic == ""){
            ui_alert("请上传身份证正面照");
            return false;
        }else if(field_rep_idcard_back_pic == ""){
            ui_alert("请上传身份证反面照");
            return false;
        }else if(property == ""){
            ui_alert("请选择门店属性");
            return false;
        }else if(propertyLen != "1"){
            ui_alert("请选择唯一门店属性");
            return false;
        }else if(forms == ""){
            ui_alert("请选择合作形式");
            return false;
        }else if(formsLen != "1"){
            ui_alert("请选择唯一合作形式");
            return false;
        }else if(bank_account_name == ""){
            ui_alert("请输入公户开户人姓名");
            return false;
        }else if(bank_name == ""){
            ui_alert("请输入公户开户银行");
            return false;
        }else if(bank_account_id == ""){
            ui_alert("请输入公户开户银行账号");
            return false;
        }else if(!validateBankNum(bank_account_id)){
            ui_alert("公户开户银行账号输入有误");
            return false;
        }else if(bank_branch == ""){
            ui_alert("请输入公户开户支行名称");
            return false;
        }else if(priv_bank_account_name == ""){
            ui_alert("请输入私户开户人姓名");
            return false;
        }else if(priv_bank_name == ""){
            ui_alert("请输入私户开户银行");
            return false;
        }else if(priv_bank_account_id == ""){
            ui_alert("请输入私户开户银行账号");
            return false;
        }else if(!validateBankNum(priv_bank_account_id)){
            ui_alert("私户银行账号输入有误");
            return false;
        }else if(priv_bank_branch == ""){
            ui_alert("请输入私户开户支行名称");
            return false;
        }

        var ajaxurl =  '';
        if(typeof(id) == 'string' && id != ''){
            ajaxurl = apiUrl + '/admin/assetChannel/edit'
        }else{
            ajaxurl = apiUrl + '/admin/assetChannel/add'
        }
        ajax_jquery({
            url: ajaxurl,
            data:{
                'id': id,
                'name': name,
                'credit_code': credit_code,
                'city': loc_address,
                'addr': addr,
                'lic_validity': lic_validity,
                'dealer_lic_pic': field_dealer_lic_pic,
                'rep': rep,
                'idno': idno,
                'mobile': mobile,
                'mail': mail,
                'rep_idcard_pic': field_rep_idcard_pic,
                'rep_idcard_back_pic': field_rep_idcard_back_pic,
                'property': property,
                'forms': forms,
                'bank_account_name': bank_account_name,
                'bank_name': bank_name,
                'bank_account_id': bank_account_id,
                'bank_branch': bank_branch,
                'priv_bank_account_name': priv_bank_account_name,
                'priv_bank_name': priv_bank_name,
                'priv_bank_account_id': priv_bank_account_id,
                'priv_bank_branch': priv_bank_branch,
                'status': status,
                'descr': descr,
                'guarantee_id':guarantee_id
            },
            success:function(resp){
                if (resp.code == "1" ) {
                    ui_alert("提交成功","success")
                    window.location.href = "/admin/assetChannel/index.html";
                } else {
                    if (typeof(resp.msg) == 'string') {
                        ui_alert(resp.msg);
                        return false;
                    }
                }
            }
        });
    });

    $('#addStaffBtn').click(function(){
        window.location.href = '/admin/assetChannel/addStaff?id='+id;
    });

   $('#table-myStaff').on('click','.updateStatus',function(){
        var self = this;
        var mobile = $(this).attr('data-id');
        var statusTd = $(this).parent('td').prev('td');
        var status = $(statusTd).attr('data-status');
        status = status>0?0:1; //更改stautus
        ajax_jquery({
            url: apiUrl +'/admin/assetchannel/editStaff',
            data:{
                'mobile': mobile,
                'status': status
            },
            success:function(resp){
                console.log(mobile)
                if (resp.code == "1" ) {
                    $(statusTd).attr('data-status',status);
                    if(status){
                        $(statusTd).text('正常');
                        $(self).text('停用');
                    }else{
                        $(statusTd).text('停用');
                        $(self).text('启用');
                    }
                } else {
                    if (typeof(resp.msg) == 'string') {
                        ui_alert(resp.msg);
                        window.location.reload();
                    }
                }
            }
        });
   });

});