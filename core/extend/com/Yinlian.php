<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace com;

class Yinlian {

	/*
	**测试账户
	*/

	/*public static $accout = 'T102006';//
	public static $pk = "T102006";
	public static $service = 'https://222.72.248.198/';*/

	/*
	**正式系统
	*/
	public static $accout = '102007';
	public static $pk = '46876f41f59b4e949a5d61ccfb1faff4';
	public static $service = 'https://data.unionpaysmart.com/';


	public static function  sendHttpRequest($url,$params ='') {
		$ch = curl_init();
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		$opt = array(
				CURLOPT_URL     => $url,
				CURLOPT_HEADER  => 0,
				CURLOPT_RETURNTRANSFER  => 1,
				CURLOPT_HTTPHEADER =>array ('Content-type:application/x-www-form-urlencoded;charset=UTF-8')
				//CURLOPT_TIMEOUT         => $timeout,
				);
		if ($params) {
			$opt['CURLOPT_POST'] = 1;
			$opt['CURLOPT_POSTFIELDS'] = http_build_query($params);
		}
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