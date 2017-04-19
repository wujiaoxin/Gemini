<?php

namespace app\api\controller;
use app\common\controller\Api;

class Credit extends Api {
	
	public function index() {	
		$resp['code'] = 1;
		$resp['msg'] = 'CreditAPI';
		return json($resp);
	}
	
	//手机服务密码
	public function mobilePassword($mobilepassword = null) {
		set_time_limit(0);
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');
		if ($uid > 0) {
			$userInfo = db('member')->field('mobile,realname,idcard')->where('uid',$uid)->find();
		}else{
			return json($resp);
		}
		
		$idcard = $userInfo["idcard"];
		$mobile = $userInfo["mobile"];
		$name = $userInfo["realname"];
		
		
		$getTokenUrl = 'https://collect.hulushuju.com/api/applications/mobile';
		$postCollectUrl = 'https://collect.hulushuju.com/api/authorize/mobile/collect';
		
		
		$httpUrl = $getTokenUrl;
		$httpParam  = array(
				'company_account' => 'vpdai_CRAWLER',
				'name' => $name,
				'identity_card_number' => $idcard,
				'cell_phone_number' => $mobile
			   );
		$httpResp = json_decode(httpPost($httpUrl,$httpParam));
		
		//var_dump($httpResp->code);
		
		if($httpResp->code == 8209 || $httpResp->code == 12291){
			$mobileCollectToken = $httpResp->data->token;
			
			$mobileCollect = array(
				'token'       => $mobileCollectToken,
				'password'    => $mobilepassword,
				'step'        => 1,
				'time'        => time()
			);
			session('mobileCollect', $mobileCollect);
			
		}else{
			return json($resp);			
		}
		
		
		$httpUrl = $postCollectUrl;
		$httpParam  = array(
				'token' => $mobileCollectToken,
				'password' => $mobilepassword
			);
		
		$httpResp = json_decode(httpPost($httpUrl,$httpParam));

		//if(isSet($getTokenResp))
		if($httpResp->code == 8209 || $httpResp->code == 12291){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
			//$resp['mobileCollectToken'] = $mobileCollectToken;
			$resp['data'] = $httpResp;
		}else{
			$resp['data'] = $httpResp;
		}
 
		return json($resp);
	}

	
	public function mobileCaptcha($captcha = null) {
		set_time_limit(0);
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		
		//$getTokenUrl = 'https://collect.hulushuju.com/api/applications/mobile';
		$postCollectUrl = 'https://collect.hulushuju.com/api/authorize/mobile/collect';
		
		$mobileCollectToken = session('mobileCollect.token');
		
		$httpUrl = $postCollectUrl;
		$httpParam  = array(
				'token' => $mobileCollectToken,
				'captcha' => $captcha
			);
		
		$httpResp = json_decode(httpPost($httpUrl,$httpParam));
		$resp['data'] = $httpResp;
		
		
		
		if($httpResp->code == 8209 || $httpResp->code == 12291){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
			$resp['data'] = $httpResp;
		}
		
		if($httpResp->code == 12800){
			$resp['code'] = 1001;
			$resp['msg'] = '再次输入验证码';
			$resp['data'] = $httpResp;
		}
		
		//12291 
		return json($resp);
	}
	
	
	public function sendMobileCaptcha() {
		set_time_limit(0);
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		$resendUrl = "https://collect.hulushuju.com/api/authorize/captcha/collect/resend";
		
		$mobileCollectToken = session('mobileCollect.token');
		
		$httpUrl = $resendUrl;
		$httpParam  = array(
				'token' => $mobileCollectToken
			);
		
		$httpResp = json_decode(httpPost($httpUrl,$httpParam));
		$resp['data'] = $httpResp;
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}
	
	
	
	public function results() {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$data["id"] = "1001";
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功';
		$resp['data'] = $data;
 
		return json($resp);
	}

	
}
