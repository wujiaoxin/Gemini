<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Epay extends Admin
{
	public function index(){

		$rsa = new \epay\RSA();
		$postData = array();
		$postData['PlatformMoneymoremore'] = "s1";
		$postData['RealName'] = "朱小强";
		$postData['Mobile'] = "13771683496";
		$postData['IdentificationNo'] = "321324198309025455";
		$CardNumber = "6228480405885414079";
		$postData['CardNumber'] = $CardNumber;
		$postData['Province'] = "10";
		$postData['City'] = "1078";
		$postData['BranchBankName'] = "园区支行";
		$postData['BankCode'] = "3";
		$postData['Remark'] = "";
		$postData['NotifyURL'] = "http://www.mengxd.com";
		$postData['SignInfo'] =  "";	

		$dataStr = $postData['PlatformMoneymoremore'].$postData['RealName'].$postData['Mobile'].$postData['IdentificationNo'].$postData['CardNumber'].$postData['Province'].$postData['City'].$postData['BranchBankName'].$postData['BankCode'].$postData['Remark'].$postData['NotifyURL'];

		$postData['CardNumber'] = $rsa->encrypt($CardNumber);
		echo $postData['CardNumber'];die;
		//echo $rsa->decrypt($postData['CardNumber']);
		//echo $dataStr;
		//$dataStr = "s1张波32020119691023803467657889900899000191200舍得坊1http://www.baidu.com";
		$postData['SignInfo'] =  $rsa->sign($dataStr);
		//print $postData['SignInfo'];exit;

		var_dump($postData);die;
		$postAction = \epay\Epay::$service."bindCard.action";
		//$postAction = "http://test.moneymoremore.com:88/main/sloan/bindCard.action";
		$inputPost = "<form action='".$postAction."' method='post' id='form_action'>";

		foreach ($postData as $key => $value) {
			$inputPost.= "<input type='hidden' name='".$key."' value='".$value."'>";
		}
		$inputPost .= "<input type='submit'  value='submit'>";
		$inputPost .= "</form><script>window.onload=function(){document.getElementById('form_actionX').submit();}</script>";
		//print $inputPost;
		//print_r($postData);
 		$ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_POST, TRUE); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        curl_setopt($ch, CURLOPT_URL, $postAction);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);

        $ret = curl_exec($ch);
        curl_close($ch);

        print_r($ret);

	}
}