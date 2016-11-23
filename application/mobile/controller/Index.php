<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\mobile\controller;
use app\common\controller\Base;

class Index extends Base {
	public function _initialize() {
		parent::_initialize();
		if (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}
	public function index() {
		$welcomeText='请登录';
		if (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$welcomeText = $user['username'];
		}
		$this->assign('welcomeText', $welcomeText);
		return $this->fetch();
	}
	
	public function protocol() {		
		return $this->fetch();
	}
	
	public function privacy() {		
		return $this->fetch();
	}
	
	public function sendSmsCode($phone = '', $verifycode = '', $send_code  = '', $type = ''){
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if (!$phone) {
			$data["code"] = 0;
			$data["msg"] = '手机号不能为空！';
			return json($data);
		}
		if ($verifycode) {
			$verify = new \org\Verify();
			$result = $verify->check($verifycode, 1);
			if (!$result) {
				$data["code"] = 0;
				$data["msg"] = '图形验证码错误！';
				return json($data);
			}
		} else {
			$data["code"] = 0;
			$data["msg"] = '图形验证码为空！';
			return json($data);
		}		
		$smsCode = rand(1000,9999);
		$smsMsg = '验证码:' . $smsCode;
		
		//$rc = true;
		$rc = $this->sendSms($phone,$smsMsg);
		if($rc){
			session('smsCode',$smsCode);
			$resp['code'] = 1;
			$resp['msg'] = '发送成功'.$smsCode;			
		}		
		return json($resp);
	}
	
}
