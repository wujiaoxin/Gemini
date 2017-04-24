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
		
		//TODO:未实名认证判断
		
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
		$resp['data'] = $httpResp;
		
		if($httpResp->code == 12291){
			$resp['code'] = 1;
			$resp['msg'] = '数据源授权成功';
			return json($resp);
		}

		if($httpResp->code == 12800){
			$resp['code'] = 2;
			$resp['msg'] = '输入短信验证码';
			return json($resp);
		}
		if($httpResp->code == 12544 || $httpResp->code == 12545 ){
			$resp['code'] = 3;
			$resp['msg'] = $httpResp->message;
			return json($resp);
		}
		
		$resp['code'] = $httpResp->code;
		$resp['msg'] = $httpResp->message;
		
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
		
		if($httpResp->code == 12291){
			$resp['code'] = 1;
			$resp['msg'] = '数据源授权成功';
			return json($resp);
		}

		if($httpResp->code == 12800){
			$resp['code'] = 2;
			$resp['msg'] = '输入短信验证码';
			return json($resp);
		}
		if($httpResp->code == 12544 || $httpResp->code == 12545 ){
			$resp['code'] = 3;
			$resp['msg'] = $httpResp->message;
			return json($resp);
		}
		
		$resp['code'] = $httpResp->code;
		$resp['msg'] = $httpResp->message;
		
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
		
		if($httpResp->code == 12291){
			$resp['code'] = 1;
			$resp['msg'] = '数据源授权成功';
			return json($resp);
		}

		if($httpResp->code == 12800){
			$resp['code'] = 2;
			$resp['msg'] = '输入短信验证码';
			return json($resp);
		}
		if($httpResp->code == 12544 || $httpResp->code == 12545 ){
			$resp['code'] = 3;
			$resp['msg'] = $httpResp->message;
			return json($resp);
		}
		
		$resp['code'] = $httpResp->code;
		$resp['msg'] = $httpResp->message;

		return json($resp);
	}
	
	
	
	public function results() {
		//TODO 根据订单审核结果返回
		
		$mobileCollectToken = session('mobileCollect.token');
		if(empty($mobileCollectToken)){
			$respStr = '{
				"code": 5,
				"msg": "资料待提交"
			}';
		}else{
			$respStr = '{
				"code": 1,
				"msg": "获取成功",
				"data": {
					"name": "90贷",
					"month": 36,
					"downpay": 10000,
					"loan": 2000,
					"avgmonthpay": 3333,
					"repay": [
						{
							"plan": "第一年",
							"period": "1-12",
							"monthpay": 7999
						},
						{
							"plan": "第二年",
							"period": "13-24",
							"monthpay": 6999
						},
						{
							"plan": "第三年",
							"period": "25-36",
							"monthpay": 6999
						}
					]
				}
			}';
		}
			
		$resp = json_decode($respStr);
 
		return json($resp);
	}

	
}
