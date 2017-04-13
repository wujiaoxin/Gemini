<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

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
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}

	
	public function mobileCaptcha($captcha = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		
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
