<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace epay;

class Epay {

	/*
	**测试账户
	*/

	public static $PlatformMoneymoremore = 's3';//
	public static $service = 'http://test.moneymoremore.com:88/main/sloan/';
	
	/*
	**正式系统
	*/
	/*public static $PlatformMoneymoremore = '';
	public static $service = 'https:// xd.95epay.com/sloan/';*/


	public static function  sendHttpRequest($params, $url) {
		$ch = curl_init();
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		$opt = array(
				CURLOPT_URL     => $url,
				CURLOPT_POST    => 1,
				CURLOPT_HEADER  => 0,
				CURLOPT_POSTFIELDS => http_build_query($params),
				CURLOPT_RETURNTRANSFER  => 1,
				CURLOPT_HTTPHEADER =>array ('Content-type:application/x-www-form-urlencoded;charset=UTF-8')
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

	//签名
	public static function buildSign($data){ 
		$dataStr = '';
		foreach($data as $key => $value) {			
			$dataStr = $dataStr .$key .$value;
		}
		$dataStr = $dataStr . self::$pk;
		$sign = md5($dataStr);
		$sign = strtoupper($sign);
		return $sign;
	} 

	
}