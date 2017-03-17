<?php

function sendSms($mobile, $content){
	//TODO: move to config module;
	$uid = '161110_hwj_hnkj';
	$pwd = 'me1989';
	$http = 'http://61.174.50.42:8080/sms/ylSend3.do';
	if (empty($mobile) || empty($content)) {
		return false;
	}
	$content = mb_convert_encoding($content, 'gbk', 'utf-8');//utf8 to gbk
	$data = array(
		'uid' => $uid, //用户账号
		'pwd' => $pwd,//strtolower(md5($pwd)), //MD5位32密码
		'rev' => $mobile, //号码
		'msg' => $content, //内容
		'sdt' => '', //定时发送
		'snd' => '101', //子扩展号
		//'encode' => 'utf8',
	);
	$param = '';
	while (list($k, $v) = each($data)) {
		$param .= rawurlencode($k) . "=" . rawurlencode($v) . "&"; //转URL标准码
	}
	$param = substr($param, 0, -1);		
	$url = $http.'?'.$param;		
	$rc = file_get_contents($url);		
	//TODO: 判断RC;		
	return true;
	/*
	$re = $this->postSMS($http, $data); //POST方式提交
	if (trim($re) == '100') {
		return true;
	} else {
		return "发送失败! 状态：" . $re;
	}*/
}

/*
function checkErrorTimes(){
	$errorTimes = session('errorTimes');
	return $errorTimes;
}*/
