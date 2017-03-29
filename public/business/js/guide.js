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
                        var Enterprise = $("#Enterprise").val();
                        var businessLicense = $("input[name='businessLicense']:checked").val();
                        var loc_province = $("#loc_province").val();
                        var loc_city = $("#loc_city").val();
                        var loc_town = $("#loc_town").val();
                        var loc_address = loc_province + ',' + loc_city  + ',' + loc_town;
                        var address = $("#address").val();
                        var termOfValidity = $("#termOfValidity").val();
                        if(Enterprise == ""){
                            ui_alert("alert-error","请输入企业名称");
                            return false;
                        }else if(!businessLicense){
                            ui_alert("alert-error","请选择营业执照类型");
                            return false;
                        }else if(!loc_province && !loc_city && !loc_town){
                            ui_alert("alert-error","请选择单位所在地");
                            return false;
                        }else if(address == ""){
                            ui_alert("alert-error","请填写详细地址");
                            return false;
                        }else if(termOfValidity == ""){
                            ui_alert("alert-error","请填写营业期限");
                            return false;
                        }
                        ajax_jquery({
                            url: apiUrl +'/api/order/save?t='+Math.random(),
                            data:{
                                'type': '1',
                                'Enterprise': Enterprise,
                                'businessLicense': businessLicense,
                                'loc_address': loc_address,
                                'address': address,
                                'termOfValidity': termOfValidity
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
                        var legalPerson = $("#legalPerson").val();
                        var idcardNum = $("#idcardNum").val();
                        if(legalPerson == ""){
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
                            url: apiUrl +'/api/order/save?t='+Math.random(),
                            data:{
                                'type': '1',
                                'legalPerson': legalPerson,
                                'idcardNum': idcardNum
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
                        var legalPerson = $("#legalPerson").val();
                        var idcardNum = $("#idcardNum").val();
                        if(legalPerson == ""){
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
                            url: apiUrl +'/api/order/save?t='+Math.random(),
                            data:{
                                'type': '1',
                                'legalPerson': legalPerson,
                                'idcardNum': idcardNum
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
                    console.log(current)
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
        }

    };

}();