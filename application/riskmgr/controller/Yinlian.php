<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\riskmgr\controller;
use app\common\controller\Base;

class Yinlian extends Base {
	public function index() {
		return $this->fetch();
	}
	
	public function results($idcard = '', $name = '', $bankcard='', $mobile='', $password = "") {
		if($password != "yinlian"){
			return $this->error("查询密码错误", 'index');
		}		
		$status = 1;//正式服务器 1

		$accout = 'T99999';
		$pk = "dbf9b1be199abd09a4a75f1bb08a37a8";
		$server = 'https://222.72.248.198:44443/tal-server/quota/usernature';

		if($status){
			$accout = '102007';
			$pk = "46876f41f59b4e949a5d61ccfb1faff4";
			$server = 'https://profile.unionpaysmart.com/quota/usernature';
		}

		$data  = array( 'account' => $accout,
						//'address' => '杭州市',
					   'card' => $bankcard,
					   //'email' => 'a@test.com',
						'identityCard' => $idcard,
					   'identityType' =>'1',
					   'index' => 'all',
					   'mobile' => $mobile,
					   'name' => $name,
					   'orderId' => rand(100000,999999)//,
					   //'sign' => 'BE11C991DE06605162B3B8A98F84E480'
					   );

		$dataStr = '';
		foreach($data as $key => $value) {			
			$dataStr = $dataStr .$key .$value;
		}
		$dataStr = $dataStr . $pk;
		$sign = md5($dataStr); 
		$sign = strtoupper($sign); 
		$data["sign"] = $sign;

		$url = $server;
		$url = $url.'?'.http_build_query($data);

		$ch = curl_init();
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		$opt = array(
				CURLOPT_URL     => $url,
				//CURLOPT_POST    => 1,
				CURLOPT_HEADER  => 0,
				//CURLOPT_POSTFIELDS => http_build_query($data),
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
		$results = $resp;
		
		
		$filename="query_log.txt";
		$handle=fopen($filename,"a+");
		if($handle){
			fwrite($handle,"=========银联=============\r\n");
			fwrite($handle, date("Y-m-d h:i:sa")."\r\n");
			fwrite($handle,$name."\r\n");
			fwrite($handle,$idcard."\r\n");
			fwrite($handle,$results."\r\n");
			fwrite($handle,"==========================\r\n\r\n");
		}
		fclose($handle);
		
		$this->assign('query', $name);
		//$this->assign('url', $url);
		$this->assign('results', $results);
		return $this->fetch();
	}
}
