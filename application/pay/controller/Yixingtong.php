<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\pay\controller;
use app\common\controller\Base;

class Yixingtong extends Base {
	public function index() {
		return $this->fetch();
	}
	
	public function results($idcard = '', $name = '', $bankcard='', $mobile='', $password = "") {
		if($password != "yinlian"){
			return $this->error("查询密码错误", 'index');
		}		
		$status = 0;//正式服务器 1

		$partnerId = '20160831020000752643';//商户ID
		$pk = "b04fbc6afc77b131c355dd1788215dbb";
		$server = 'http://merchantapi.yijifu.net/gateway.html';

		if($status){
			$partnerId = '20160831020000752643';//商户ID
			$pk = "b04fbc6afc77b131c355dd1788215dbb";
			$server = 'http://merchantapi.yijifu.net/gateway.html';
		}
		
		
		$certValidTime = "20191224";
		$imageUrl2 = "";

		
		$service = "installmentSign";
		// $service = "installmentBankCardVerify";

		/*$data  = array( 'service' => $service,
						'partnerId' => $partnerId,
						'orderNo' => rand(100000,999999),
						'signType' =>'MD5',
						'notifyUrl' => '',//
						'realName' => $mobile,
						'certNo' => $bankcard,
						'certValidTime' => $name,
						'imageUrl2' => $imageUrl2,
						'certBackImageUrl' => $certBackImageUrl,
						'mobileNo' => $mobileNo,
						'bankCardNo' => $bankCardNo,
						'profession' => $profession,
						'address' => $address,
						'paperContractNo' => $paperContractNo,
						'productName' => $productName,
						'productPrice' => $productPrice,
						'totalCapitalAmount' => $totalCapitalAmount,
						'installmentPolicy' => 'CUSTOMIZE',
						'firstRepayDate' => $firstRepayDate,
						'interestRate' => $interestRate,
						'otherRate' => $otherRate,
						'totalTimes' => $totalTimes,
						'repayType' => $repayType,
						'eachTotalAmount' => $eachTotalAmount,
						'eachCapitalAmount' => $eachCapitalAmount,
						'eachInterestAmount' => $eachInterestAmount,
						'eachOtherAmount' => $eachOtherAmount,						
					   //'sign' => 'BE11C991DE06605162B3B8A98F84E480'
					   );*/
	$data  = array( 'service' => $service,
						'partnerId' => $partnerId,
						'orderNo' => '2007050512345678912345678' . rand(100000,999999),
						'signType' =>'MD5',
						'notifyUrl' => 'https://www.vpdai.com',
						'realName' => '小花',
						'certNo' => '130204199801014813',
						'certValidTime' => '20350918',
						'imageUrl2' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
						'certBackImageUrl' => 'https://v1.vpdai.com/uploads/order_files/20170113/90ba50c70dd55a670602ccee32038109.jpg',
						'mobileNo' => '15869025220',
						'bankCardNo' => '6228480438657589174',
						'imageUrl3' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
						'profession' => '工程师',
						'address' => '浙江省杭州市滨江区潮人汇',
						'paperContractNo' => '20170505' . rand(100000,999999),
						'imageUrl1' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
						'productName' => '90贷',
						'productPrice' => '10',
						'totalCapitalAmount' => '9',
						'installmentPolicy' => 'CUSTOMIZE',
						'firstRepayDate' => '2017-05-20',
						'interestRate' => 100,
						'otherRate' => 0,
						'totalTimes' => 9,
						'repayType' => 'SELF_REPAY',
						'eachTotalAmount' => '[2,2,2,2,2,2,2,2,2]',
						'eachCapitalAmount' => '[1,1,1,1,1,1,1,1,1]',
						'eachInterestAmount' => '[1,1,1,1,1,1,1,1,1]',
						'eachOtherAmount' => '[0,0,0,0,0,0,0,0,0]',
						'returnUrl'=>'http://lo.vpdai.com/pay/yixingtong/returnurl'						
					   //'sign' => 'BE11C991DE06605162B3B8A98F84E480'
					   );			   
		$res = array(
			'orderno'=>$data['orderNo'],
			);
		db('member_withhold')->insert($res);
			/*$data = array(
				'service'=>$service,
				'partnerId'=>$partnerId,
				'orderNo'=>'2007050512345678912345678' . rand(100000,999999),
				'signType'=>'MD5',
				'realName'=>'张三',
				'certNo'=>'450225198808149288',
				'bankCardNo'=>'6228480402637874213',
				'mobileNo'=>'15658099685',
				'outOrderNo'=>'42587-20160808135848315',
				'notifyUrl'=>'http://lo.vpdai.com'
			);	*/	   
		ksort($data);

		//$dataStr = http_build_query($data);
		
		$dataStr='';
		
		foreach($data as $key => $value) {			
			$dataStr = $dataStr .$key. '=' .$value . '&';
		}
		$dataStr = substr($dataStr,0,strlen($dataStr)-1); 
		$dataStr = $dataStr . $pk;
		$sign = md5($dataStr); 		
		//$sign = strtoupper($sign); 
		$sign = strtolower($sign); 
		
		$data["sign"] = $sign;
		

		$url = $server;
		//$url = $url.'?'.http_build_query($data);

		$ch = curl_init();
		$ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
		$opt = array(
				CURLOPT_URL     => $url,
				CURLOPT_POST    => 1,
				CURLOPT_HEADER  => 0,
				CURLOPT_POSTFIELDS => http_build_query($data),
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
		
 
		
		$this->assign('query', http_build_query($data));
		//$this->assign('url', $url);
		$this->assign('results', $results);
		return $this->fetch();
	}

	//签约分期收款TODO
	public function installment($idcard = '', $name = '', $bankcard='', $mobile='', $password = ""){
		$service = "installmentSign";

		//可变数据(需要传参)
		$data  = array( 
			'service' => $service,
			'orderNo' => '2007050512345678912345678' . rand(100000,999999),
			'signType' =>'MD5',
			'notifyUrl' => 'http://mengxd.com/index.php/index/index/signstage.html',
			'realName' => '小花',
			'certNo' => '130204199801014813',
			'certValidTime' => '20350918',
			'imageUrl2' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
			'certBackImageUrl' => 'https://v1.vpdai.com/uploads/order_files/20170113/90ba50c70dd55a670602ccee32038109.jpg',
			'mobileNo' => '15658099685',
			'bankCardNo' => '6228480438657589174',
			'imageUrl3' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
			'profession' => '工程师',
			'address' => '浙江省杭州市滨江区潮人汇',
			'paperContractNo' => '20170505' . rand(100000,999999),
			'imageUrl1' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
			'productName' => '90贷',
			'productPrice' => '10',
			'totalCapitalAmount' => '9',
			'installmentPolicy' => 'CUSTOMIZE',
			'firstRepayDate' => '2017-05-20',
			'interestRate' => 100,
			'otherRate' => 0,
			'totalTimes' => 9,
			'repayType' => 'SELF_REPAY',
			'eachTotalAmount' => '[2,2,2,2,2,2,2,2,2]',
			'eachCapitalAmount' => '[1,1,1,1,1,1,1,1,1]',
			'eachInterestAmount' => '[1,1,1,1,1,1,1,1,1]',
			'eachOtherAmount' => '[0,0,0,0,0,0,0,0,0]',
		   );
		$res = array(
			'orderno'=>$data['orderNo'],
			'papercontract'=>$data['paperContractNo'],
			);
		db('member_withhold')->insert($res);

		$result = \com\Withhold::installSign($data);

		if ($result['resultCode'] == 'EXECUTE_SUCCESS') {
			$resp['code'] = '1';
			$resp['data'] = $result;

		}elseif ($result['resultCode'] == 'EXECUTE_PROCESSING') {
			$resp['code'] = '1';
			$resp['data'] = $result;
		}else{
			$resp['code'] = '0';
			$resp['msg'] = '银行卡验证失败';
		}
		return json($resp);
	}


	//签约分期收款异步 TODO
	public function signstage(){
		/*异步处理结果
		{
		    "bankCode": "ABC",
		    "orderNo": "2007050512345678912345678676572",
		    "contractNo": "000g03001gz36sapyw00",
		    "notifyTime": "2017-05-11 11:40:07",
		    "signStatus": "SUCCESS",
		    "bankCardType": "DEBIT_CARD",
		    "resultCode": "comn_04_0001",
		    "sign": "6e8e9f84f303d37ab4821ecb9374e147",
		    "description": "签约成功",
		    "errorCode": "comn_04_0001",
		    "standByBankCardType": "",
		    "bankName": "农业银行",
		    "overdueFineDay": "0",
		    "resultMessage": "签约成功",
		    "version": "1.0",//
		    "paperContractNo": "20170505584263",//
		    "protocol": "httpPost",
		    "overdueRemitDay": "0",
		    "service": "installmentSign",
		    "success": "true",
		    "signType": "MD5",//
		    "partnerId": "20160831020000752643",//
		    "time": "0",//
		    "status": "INIT"
		}
		*/
		$data = input('post.');
		$success = $data['success'];
		if (!$success) {
			$resp['code'] = '0';
			$resp['msg'] = '接口调用异常';
			return ;
		}
		if ($data['status'] == 'CHECK_NEEDED') {
			$info['status'] = '1';
		}elseif ($data['status'] == 'CHECK_REJECT') {
			$info['status'] = '2';
		}elseif ($data['status'] == 'INIT') {
			$info['status'] = '3';
		}elseif ($data['status'] == 'DEALING') {
			$info['status'] = '4';
		}elseif ($data['status'] == 'SIGN_FAIL') {
			$info['status'] = '-1';
		}
		$time = strtotime($data['notifyTime']);
		
		if ($data['signStatus'] == 'SUCCESS') {

			$info['bankcode'] =$data['bankCode'];
			$info['contractno'] =$data['contractNo'];
			$info['bankcard_type'] =$data['bankCardType'];
			$info['bankname'] =$data['bankName'];
			$info['update_time'] =$time;
			$info['descr'] =$data['resultMessage'];
			$info['signstatus'] = '1';
			
			db('member_withhold')->where('orderNo',$data['orderNo'])->update($info);
		}
		if ($data['signStatus'] == 'UPAYSIGN_FAIL') {
			$info['signStatus']='3';
			$info['descr'] = $data['description'].$data['errorCode'];
			db('member_withhold')->where('orderno',$data['orderNo'])->update($info);
		}
		echo "success";exit();
	}
}
