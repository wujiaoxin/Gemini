//验证手机号
function validatePhoneNumber(mobile) {
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1})|145|147|222)+\d{8})$/;
    if (!myreg.test(mobile)) {
        return false;
    } else {
        return true
    }
}

//验证交易密码
function validatePayPwd(pwd) {
    var myreg = /^\d{6}$/;
    if (!myreg.test(pwd)) {
        return false;
    } else {
        return true
    }
}

//验证密码
function validatePassword(pass) {
    if (pass.length < 8 || pass.length > 16 || pass.match(/[^a-zA-Z0-9]+/)) {
        return false;
    }
    var ls = 0;
    if (pass.match(/(([a-z])|([A-Z]))+/)) {
        ls++;
    }
    if (pass.match(/([0-9])+/)) {
        ls++;
    }
    if (ls < 2) {
        return false;
    } else {
        return true;
    }
}


//验证银行卡号
function validateBankNum(banknum) {
    if (banknum.length < 15 || banknum.length > 19) {
        return false;
    } else {
        var num = /^\d*$/;  //全数字
        if (!num.exec(banknum)) {
            return false;
        } else {
            return true;
        }
    }
}


//验证身份证格式
function validateIdCard(pId) {
//检查身份证号码

    var arrVerifyCode = [1, 0, "X", 9, 8, 7, 6, 5, 4, 3, 2];
    var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    var Checker = [1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1];

    if (pId.length != 15 && pId.length != 18)    return false;

    var Ai = pId.length == 18 ? pId.substring(0, 17) : pId.slice(0, 6) + "19" + pId.slice(6, 16);

    if (!/^\d+$/.test(Ai))  return false;

    var yyyy = Ai.slice(6, 10), mm = Ai.slice(10, 12) - 1, dd = Ai.slice(12, 14);

    var d = new Date(yyyy, mm, dd), now = new Date();
    var year = d.getFullYear(), mon = d.getMonth(), day = d.getDate();

    if (year != yyyy || mon != mm || day != dd || d > now || year < 1940) return false;

    for (var i = 0, ret = 0; i < 17; i++)  ret += Ai.charAt(i) * Wi[i];
    Ai += arrVerifyCode[ret %= 11];

    return pId.length == 18 && pId != Ai ? false : true;
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

      

