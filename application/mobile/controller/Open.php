<?php
// +----------------------------------------------------------------------
// | /mobile/open
// +----------------------------------------------------------------------
// | 移动端公开访问接口
// +----------------------------------------------------------------------
// | Author: fwj
// +----------------------------------------------------------------------

namespace app\mobile\controller;
use app\common\controller\Base;

class Open extends Base {
	
	public function _initialize() {
		parent::_initialize();
	}
	public function index() {
		return "ok";
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
		$smsMsg = '您的验证码为:' . $smsCode;
		
		//$rc = true;
		$rc = $this->sendSms($phone,$smsMsg);
		if($rc){
			session('smsPhone',$phone);
			session('smsCode',$smsCode);
			$resp['code'] = 1;
			$resp['msg'] = '发送成功'.$smsCode;//fixed:方便调试，发布需删除
		}		
		return json($resp);
	}
	
}
