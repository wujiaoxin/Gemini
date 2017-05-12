<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace com;

class Withhold {

	public static $partnerId = '20160831020000752643';//商户ID
	public static $pk = "b04fbc6afc77b131c355dd1788215dbb";
	public static $url = 'http://merchantapi.yijifu.net/gateway.html';

	//签约分期收款接口

	public static function installSign($data){
		$data['partnerId']=self::$partnerId;
		ksort($data);
		$res = self::getRequestParamString($data);
		$data['sign']=self::buildSign($res);
		$res = self::sendHttpRequest($data,self::$url);
		$res = json_decode($res, true);

		return $res;
		// echo "<pre>";
		// var_dump($res);die;
		
	}

	//银行卡验证接口

	public static function  insbank($data){

		$data['partnerId']=self::$partnerId;
		ksort($data);
		$res = self::getRequestParamString($data);
		$data['sign']=self::buildSign($res);
		$res = self::sendHttpRequest($data,self::$url);
		$res = json_decode($res, true);
		return $res;
	}
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
	/**
	* 组装报文
	* @param unknown_type $params        	
	* @return string
	*/
	public static function  getRequestParamString($params) {
		/*$params_str = '';
		foreach ( $params as $key => $value ) {
			$params_str .= ($key . '=' . (!isset ( $value ) ? '' : urlencode( $value )) . '&');
		}
		return substr ( $params_str, 0, strlen ( $params_str ) - 1 );*/

		$dataStr='';
		
		foreach($params as $key => $value) {			
			$dataStr = $dataStr .$key. '=' .$value . '&';
		}
		$dataStr = substr($dataStr,0,strlen($dataStr)-1);
		return  $dataStr;	
	}

	//签名
	public static function buildSign($res){ 
		$str = $res.self::$pk;
		$sign = md5($str);
		$sign = strtolower($sign);
		return $sign;
	} 
}