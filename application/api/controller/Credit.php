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
				'mobile'      => $mobile,
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
			
			$this->setCreditResult(3);
			
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
			$this->setCreditResult(3);
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
			$this->setCreditResult(3);
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
	
		$uid = session('user_auth.uid');		
	
		$creditResult = db('credit')->field('uid,mobile,credit_status,credit_result')->where("uid",$uid)->order('id desc')->find();

		if($creditResult == null){
			
			$userInfo = db('member')->field('uid,mobile,status')->where('uid',$uid)->find();
			$mobile = $userInfo['mobile'];	
			
			$data['uid'] = $uid;
			$data['mobile'] = $mobile;			
			$data['credit_status'] = 2;
			$data['credit_result'] = 0;
			$data['update_time'] = time();
			$data['create_time'] = time();
			$result = db('credit')->insert($data);
			
			$respStr = '{
				"code": 1,
				"msg": "获取成功！",
				"data": {
					"resultcode": 5,
					"resultmsg": "资料待提交"
				}
			}';
			$resp = json_decode($respStr); 
			return json($resp);
		} 
		
		if($creditResult['credit_status'] == 3){
			
			if($creditResult['credit_result'] == 0){
				$respStr = '{
					"code": 1,
					"msg": "获取成功！",
					"data": {
						"resultcode": 4,
						"resultmsg": "资料审核中"
					}
				}';
			}
			
			if($creditResult['credit_result'] == 2){
				$respStr = '{
					"code": 1,
					"msg": "获取成功！",
					"data": {
						"resultcode": 2,
						"resultmsg": "更换常用银行卡"
					}
				}';
			}
			
			if($creditResult['credit_result'] == 3){
				$respStr = '{
					"code": 1,
					"msg": "获取成功！",
					"data": {
						"resultcode": 3,
						"resultmsg": "使用常用手机号重新注册申请"
					}
				}';
			}
			
			if($creditResult['credit_result'] == -1){
				$respStr = '{
					"code": 1,
					"msg": "获取成功！",
					"data": {
						"resultcode": -1,
						"resultmsg": "审核未通过"
					}
				}';
			}
			
			if($creditResult['credit_result'] == 1){//TODO 获取金融方案
				$respStr = '{
					"code": 1,
					"msg": "获取成功！",
					"data": {
						"resultcode": 1,
						"resultmsg": "授信通过",
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
		
		//默认返回
		$respStr = '{
				"code": 1,
				"msg": "获取成功！",
				"data": {
					"resultcode": 5,
					"resultmsg": "资料待提交"
				}
			}';
		$resp = json_decode($respStr); 
		return json($resp);

	}
	
	protected function setCreditResult( $credit_status = 0 ) {
		
		$uid = session('user_auth.uid');
		$mobile = session('mobileCollect.mobile');
		$mobile_password = session('mobileCollect.password');
		$mobile_collect_token = session('mobileCollect.token');
		
		if(empty($mobile)){
			return false;
		}
		
		$data['uid'] = $uid;
		$data['mobile'] = $mobile;
		
		$data['mobile_password'] = $mobile_password;
		
		
		$data['credit_status'] = $credit_status;
		$data['mobile_collect_token'] = $mobile_collect_token;
		$data['update_time'] = time();
		
		$orderData = db('order')->field('id,credit_status')->where("mobile",$mobile)->where("status",-2)->order('id desc')->find();		
		if($orderData != null){			
			$data['order_id'] = $orderData['id'];
		}
		
		$creditResult = db('credit')->field('id')->where("uid",$uid)->order('id desc')->find();
		if($creditResult == null){//TODO 未关联订单错误提示
			$data['create_time'] = time();
			$result = db('credit')->insert($data);			
		}else{
			$result = db('credit')->where('id', $creditResult['id'])->update($data);
		}
		
		$orderData['credit_status'] = $credit_status;
		db('order')->where("mobile",$mobile)->update($orderData);
		
		return $result;

	}

	
}
