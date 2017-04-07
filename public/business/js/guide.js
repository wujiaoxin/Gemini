var guide = function () {
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }
            var form = $('#submit_form');
            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index) {
                    // ui_alert("alert-error",'on tab click disabled');
                    return false;
                },
                onNext: function (tab, navigation, index) {
                    if(index == 1){
                        var name = $("#name").val();
                        var businessLicense = $("input[name='businessLicense']:checked").val();
                        var loc_province = $("#loc_province").val();
                        var loc_city = $("#loc_city").val();
                        var loc_town = $("#loc_town").val();
                        var loc_address = loc_province + ',' + loc_city  + ',' + loc_town;
                        var addr = $("#addr").val();
                        var termOfValidity = $("#termOfValidity").val();
                        if(name == ""){
                            ui_alert("alert-error","请输入企业名称");
                            return false;
                        }else if(!businessLicense){
                            ui_alert("alert-error","请选择营业执照类型");
                            return false;
                        }else if(!loc_province && !loc_city && !loc_town){
                            ui_alert("alert-error","请选择单位所在地");
                            return false;
                        }else if(addr == ""){
                            ui_alert("alert-error","请填写详细地址");
                            return false;
                        }else if(termOfValidity == ""){
                            ui_alert("alert-error","请填写营业期限");
                            return false;
                        }
                        ajax_jquery({
                            url: apiUrl +'/business/user/guide?t='+Math.random(),
                            data:{
                                'name': name,
                                'businessLicense': businessLicense,
                                'city': loc_address,
                                'addr': addr,
                                'radiotime': termOfValidity
                            },
                            success:function(resp){
                                if (resp.code == "1" ) {
                                    console.log('1')
                                } else {
                                    if (typeof(resp.msg) == 'string') {
                                        ui_alert("alert-error",resp.msg);
                                        return false;
                                    }                 
                                }
                            }
                        });
                    }else if(index == 2){
                        var rep = $("#rep").val();
                        var idcardNum = $("#idcardNum").val();
                        if(rep == ""){
                            ui_alert("alert-error","请填写法人姓名");
                            return false;
                        }else if(idcardNum == ""){
                            ui_alert("alert-error","请填写法人身份证号");
                            return false;
                        }else if(!validateBankNum(idcardNum)){
                            ui_alert("alert-error","身份证号填写有误");
                            return false;
                        }
                        ajax_jquery({
                            url: apiUrl +'/business/user/guide?t='+Math.random(),
                            data:{
                                'rep': rep,
                                'idno': idcardNum
                            },
                            success:function(resp){
                                if (resp.code == "1" ) {
                                    console.log('2')
                                } else {
                                    if (typeof(resp.msg) == 'string') {
                                        ui_alert("alert-error",resp.msg);
                                        return false;
                                    }                 
                                }
                            }
                        });
                    }else if(index == 3){
                        var property = encodeCheckbox('property');
                        var forms = encodeCheckbox('forms');
                       
                        if(property == ""){
                            ui_alert("alert-error","请选择门店属性");
                            return false;
                        }else if(forms == ""){
                            ui_alert("alert-error","请选择合作形式");
                            return false;
                        }
                        ajax_jquery({
                            url: apiUrl +'/business/user/guide?t='+Math.random(),
                            data:{
                                'property': property,
                                'forms': forms
                            },
                            success:function(resp){
                                if (resp.code == "1" ) {
                                } else {
                                    if (typeof(resp.msg) == 'string') {
                                        ui_alert("alert-error",resp.msg);
                                        return false;
                                    }                 
                                }
                            }
                        });
                    }

                    $(".alert").hide();
                   
                    if (form.valid() == false) {
                        return false;
                    }

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                        $('#form_wizard_1').find('.button-submit').show();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                        $('#form_wizard_1').find('.button-submit').hide();
                    }
                    App.scrollTo($('.page-title'));
                },
                onPrevious: function (tab, navigation, index) {
                    $(".alert").hide();

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                        $('#form_wizard_1').find('.button-submit').show();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                        $('#form_wizard_1').find('.button-submit').hide();
                    }

                    App.scrollTo($('.page-title'));
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').hide();
            $('#form_wizard_1 .button-submit').click(function(){
                var bank_account_name = $("#bank_account_name").val();
                var bank_name = $("#bank_name").val();
                var bank_account_id = $("#bank_account_id").val();
                var bank_branch = $("#bank_branch").val();
                var priv_bank_account_name = $("#priv_bank_account_name").val();
                var priv_bank_name = $("#priv_bank_name").val();
                var priv_bank_account_id = $("#priv_bank_account_id").val();
                var priv_bank_branch = $("#priv_bank_branch").val();

                if(bank_account_name == ""){
                    ui_alert("alert-error","请输入公户开户人姓名");
                    return false;
                }else if(bank_name == ""){
                    ui_alert("alert-error","请输入公户开户银行");
                    return false;
                }else if(bank_account_id == ""){
                    ui_alert("alert-error","请输入公户开户银行账号");
                    return false;
                }else if(!validateBankNum(bank_account_id)){
                    ui_alert("alert-error","公户开户银行账号输入有误");
                    return false;
                }else if(bank_branch == ""){
                    ui_alert("alert-error","请输入公户开户支行名称");
                    return false;
                }else if(priv_bank_account_name == ""){
                    ui_alert("alert-error","请输入私户开户人姓名");
                    return false;
                }else if(priv_bank_name == ""){
                    ui_alert("alert-error","请输入私户开户银行");
                    return false;
                }else if(priv_bank_account_id == ""){
                    ui_alert("alert-error","请输入私户开户银行账号");
                    return false;
                }else if(!validateBankNum(priv_bank_account_id)){
                    ui_alert("alert-error","私户银行账号输入有误");
                    return false;
                }else if(priv_bank_branch == ""){
                    ui_alert("alert-error","请输入私户开户支行名称");
                    return false;
                }

                ajax_jquery({
                    url: apiUrl +'/business/user/guide?t='+Math.random(),
                    data:{
                        'bank_account_name': bank_account_name,
                        'bank_name': bank_name,
                        'bank_account_id': bank_account_id,
                        'bank_branch': bank_branch,
                        'priv_bank_account_name': priv_bank_account_name,
                        'priv_bank_name': priv_bank_name,
                        'priv_bank_account_id': priv_bank_account_id,
                        'priv_bank_branch': priv_bank_branch
                    },
                    success:function(resp){
                        if (resp.code == "1" ) {
                            ui_alert("alert-success",'提交成功')
                            window.location.href = "/business/index/index";
                        } else {
                            if (typeof(resp.msg) == 'string') {
                                ui_alert("alert-error",resp.msg);
                                return false;
                            }                 
                        }
                    }
                });
            });
        }
    };
}();

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
        for(var i in initData){
            $("input[name="+name+"]").eq(initData[i]-1).click();
        }
    }    
}





























