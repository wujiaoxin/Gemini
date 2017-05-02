<?php
//use Firebase\JWT\JWT;




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

function generateToken($uid = "", $sid = null){
	$pkey = "gemini";
	$token = array(
		//"iss" => "https://api.vpdai.com",
		//"aud" => "https://api.vpdai.com",
		"iat" => time(),//#token发布时间
		"exp" => time()+86400,//#token过期时间
		"uid" => $uid,
		"sid" => $sid
	);
	
	$token = json_encode($token);
	
	$key = md5($token.''.$pkey);
	
	$token = base64_encode($token) .'.'. $key;
	
	return $token;
	
	/**
	 * IMPORTANT:
	 * You must specify supported algorithms for your application. See
	 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
	 * for a list of spec-compliant algorithms.
	 */
	//$jwt = JWT::encode($token, $key);
	////$decoded = JWT::decode($jwt, $key, array('HS256'));
	////print_r($jwt);	
	////print_r("\n");
	//return $jwt;
	
}

function decodedToken($token = ""){
	$pkey = "gemini";
	$tempArr = explode('.', $token, 2);
	if(sizeof($tempArr)!=2){
		return '';
	}	
	$decoded = base64_decode($tempArr[0]);
	$key = $tempArr[1];	
	if($key == md5($decoded.''.$pkey)){
		return $decoded;
	}else{
		return '';
	}
	
	//$decoded = JWT::decode($token, $key, array('HS256'));
	//print_r($decoded);
	//return $decoded;
}


/*
function checkErrorTimes(){
	$errorTimes = session('errorTimes');
	return $errorTimes;
}*/



function httpPost($url, $param){

	$headers = array(
		'Content-Type:application/json;charset=UTF-8'
	);

	$ch = curl_init();
	$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
	$opt = array(
			CURLOPT_URL     => $url,
			CURLOPT_POST    => 1,
			CURLOPT_HEADER  => 0,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => json_encode($param),
			CURLOPT_RETURNTRANSFER  => 1,
			//CURLOPT_TIMEOUT         => $timeout,
			);
	if ($ssl)
	{
		$opt[CURLOPT_SSL_VERIFYHOST] = FALSE;
		$opt[CURLOPT_SSL_VERIFYPEER] = FALSE;
	}
	curl_setopt_array($ch, $opt);
	$resp = curl_exec($ch);
	curl_close($ch);		
	return $resp;
}


function saveCollectData( $data = "" ){
	$filename = RUNTIME_PATH."/collect_data.txt";
	$handle = fopen($filename,"a+");
	if($handle){
		fwrite($handle, "==========================\r\n");
		fwrite($handle, date("Y-m-d h:i:sa")."\r\n");
		fwrite($handle, "==========================\r\n");
		fwrite($handle, $data."\r\n");
		fwrite($handle, "==========================\r\n\r\n");
	}
	fclose($handle);
}