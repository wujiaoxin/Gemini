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
	public static $service = 'https://xd.95epay.com/sloan/';*/


	//绑卡接口
	public static function bankcard($data){
		$rsa = new RSA();
		$postData = array();
		$postData['PlatformMoneymoremore'] = self::$PlatformMoneymoremore;
		$postData['RealName'] = $data['RealName'];
		$postData['Mobile'] = $data['Mobile'];
		$postData['IdentificationNo'] = $data['IdentificationNo'];
		$CardNumber = $data['CardNumber'];
		$postData['CardNumber'] = $CardNumber;
		$postData['Province'] = $data['Province'];
		$postData['City'] = $data['City'];
		$postData['BranchBankName'] = "";
		$postData['BankCode'] = $data['BankCode'];
		$postData['Remark'] = "";
		$postData['NotifyURL'] = url('pay/notify/bangCard');
		$postData['SignInfo'] =  "";	
		$dataStr = $postData['PlatformMoneymoremore'].$postData['RealName'].$postData['Mobile'].$postData['IdentificationNo'].$postData['CardNumber'].$postData['Province'].$postData['City'].$postData['BranchBankName'].$postData['BankCode'].$postData['Remark'].$postData['NotifyURL']; 
		$postData['CardNumber'] = $rsa->encrypt($CardNumber);
		$postData['SignInfo'] = self::buildSign($dataStr);
		$url = self::$service."bindCard.action";
		$resp = self::sendHttpRequest($postData,$url);
		return $resp;
	}


	//多卡绑卡接口

	public static function bangcard($data){

		$postData = array();
		$postData['PlatformMoneymoremore'] = self::$PlatformMoneymoremore;
		$postData['BindMoneymoremore'] = "c7";
		$CardNumber = $data['CardNumber'];
		$postData['CardNumber'] = $CardNumber;
		$postData['Province'] = $data['province'];
		$postData['City'] = $data['city'];
		$postData['BranchBankName'] = $data['BranchBankName'];
		$postData['BankCode'] = $data['BranchBankName'];
		$postData['Remark'] = '';
		$postData['NotifyURL'] = $data['NotifyURL'];
		$postData['SignInfo'] =  "";

		$dataStr = $postData['PlatformMoneymoremore'].$postData['BindMoneymoremore'].$postData['CardNumber'].$postData['Province'].$postData['City'].$postData['BranchBankName'].$postData['BankCode'].$postData['Remark'].$postData['NotifyURL']; 
		$postData['CardNumber'] = $rsa->encrypt($CardNumber);

		$postData['SignInfo'] = self::buildSign($dataStr);
		$url = self::$service."bindCard.action";
		$resp = self::sendHttpRequest($postData,$url);
		return $resp;
	}

	//放贷接口

	public static function loan($data){
		
		$loanList = array();
		$loanList[0]['PayMoneymoremore'] = self::$PlatformMoneymoremore;
		$loanList[0]['Amount'] = $data['Amount'];
		$loanList[0]['Amount'] = $data['Amount'];

		$postData = array();
		$postData['LoanJsonList'] = json_encode($loanList);
		$postData['PlatformMoneymoremore'] = self::$PlatformMoneymoremore;
		$postData['BatchNo'] = $data['BatchNo'];
		$postData['RepaymentDate'] = $data['RepaymentDate'];
		$postData['Installment'] = $data['Installment'];
		$postData['Remark'] = "";
		$postData['NotifyURL'] = $data['NotifyURL'];

		$postData['SignInfo'] = "";     
		$dataStr = $postData['LoanJsonList'].$postData['PlatformMoneymoremore'].$postData['BatchNo'].$postData['RepaymentDate'].$postData['Installment'].$postData['Remark'].$postData['NotifyURL'];

		$postData['LoanJsonList'] = urlencode($postData['LoanJsonList']);

		$postData['SignInfo'] = self::buildSign($dataStr);
		$url = self::$service."bindCard.action";
		$resp = self::sendHttpRequest($postData,$url);
		return $resp;
	}


	public static function repayment($data){

		$repay[0]["RepaymentMoneymoremore"] = '';
		$repay[0]["RepaymentAmount"] = $data['repay_money'];
		$repay[0]["LoanNo"] = $data['loanno'];

		$fuck[] = $repay;
		$postData = array();
		$postData['LoanJsonList'] = json_encode($repay);
		$postData['PlatformMoneymoremore'] = self::$PlatformMoneymoremore;
		$postData['Remark'] = "";
		$postData['NotifyURL'] = $data['NotifyURL'];
		$postData['SignInfo'] = "";     
		$dataStr = $postData['LoanJsonList'].$postData['PlatformMoneymoremore'].$postData['Remark'].$postData['NotifyURL'];

		$postData['LoanJsonList'] = urlencode($postData['LoanJsonList']);
		
		$postData['SignInfo'] = self::buildSign($dataStr);
		$url = self::$service."bindCard.action";
		$resp = self::sendHttpRequest($postData,$url);
		return $resp;

	}

	//签名
	public static function buildSign($dataStr){ 
		$rsa = new RSA();
		$sign =  $rsa->sign($dataStr);
		return $sign;
	}


	public static function  sendHttpRequest($postData, $url) {
		
		$inputPost = "<form action='".$url."' method='post' id='form_action'>";

		foreach ($postData as $key => $value) {
			$inputPost.= "<input type='hidden' name='".$key."' value='".$value."'>";
		}
		$inputPost .= "<input type='submit'  value='submit'>";
		$inputPost .= "</form><script>window.onload=function(){document.getElementById('form_actionX').submit();}</script>";

 		$ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);

        $resp = curl_exec($ch);
        curl_close($ch);
		return $resp;
	}
	
}