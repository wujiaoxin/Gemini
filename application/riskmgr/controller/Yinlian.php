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
		$index = "S0503,S0660,S0661,S0469,S0507,S0534,S0535,S0560,S0537,S0546,S0526,S0495,S0027,S0107,S0586,S0588,S0684,S0685,S0686,S0687,S0630,S0307,S0308,S0559,S0210,S0670,S0043,S0428,S0434,S0440,S0443,S0446,S0449,S0640,S0572,S0514,S0668,S0666,S0151,S0266,S0199,S0188,S0410,S0218,S0416,S0422,S0452";
		$data  = array( 'account' => $accout,
						//'address' => '杭州市',
					   'card' => $bankcard,
					   //'email' => 'a@test.com',
						'identityCard' => $idcard,
					   'identityType' =>'1',
					   'index' => $index,
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
		$info = json_decode($results,true);
		$arr = array();

		if ($info['resCode'] != '0000') {
			$this->error($info['statMsg']);
		}
		if ($info['data']['validate'] == '1') {
			
			foreach ($info['data']['result'] as $k => $v) {
				$arr[] =array('name'=>$k,'value'=>$v);
			}
		}
		$this->assign('res',$arr);

		$this->assign('query', $name);
		//$this->assign('url', $url);
		$this->assign('results', $results);
		return $this->fetch();
	}

	public function blacklist(){
		return view();
	}

	public function blacklist_lst($idcard ='',$password = ""){
		if (request()->isPost()) {
			if($password != "yinlian"){
				return $this->error("查询密码错误", 'blacklist');
			}
			$data =input('post.');
			//350205198311185962
			$idcard = $data['idcard'];
			$accout = \com\Yinlian::$accout;
			$server = \com\Yinlian::$service.'info/p2pBlack';

			$info =array(
				'account'=>$accout,
				'entityId'=>$idcard,
				);
			$sign = \com\Yinlian::buildSign($info);

			$infores =array(
				'account'=>$accout,
				'entityId'=>$idcard,
				'sign'=>$sign,
				);
			$url = $server.'?'.http_build_query($infores);
			$resp =  \com\Yinlian::sendHttpRequest($url);
			$data =json_decode($resp,true);
			$data =array(
				'infoStr'=>json_encode($data)
			);
			$this->assign($data);
			return view();
		}else{
			return view();
		}
		
	}

	//银联对应关系调用接口
	public function authvalid($idcard='',$realname='',$bankcard='',$mobile='',$type=''){
		//测试
		/*$idcard = '6222620110009991101';
		$name = '王菲菲';
		$mobile = '18888888888';
		$idcid = '411527199101133522';
		$type = '4';*/
		$accout = \com\Yinlian::$accout;
		$server = \com\Yinlian::$service.'auth/valid';

		$info =array(
			'account'=>$accout,
			'card'=>$idcard,
			'cid'=>$bankcard,
			'mobile'=>$mobile,
			'name'=>$realname,
			'type'=>$type
			);
		$sign = \com\Yinlian::buildSign($info);

		$infores =array(
			'account'=>$accout,
			'card'=>$idcard,
			'cid'=>$bankcard,
			'mobile'=>$mobile,
			'name'=>$realname,
			'type'=>$type,
			'sign'=>$sign,
			);
		$url = $server.'?'.http_build_query($infores);
		$resp =  \com\Yinlian::sendHttpRequest($url);
		$data =json_decode($resp,true);
		return $data;
		
	}
}