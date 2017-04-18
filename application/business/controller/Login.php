<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\common\controller\Base;
class Login extends Base {
	public function login($mobile = '', $password = '') {
		if (IS_POST) {
			$resp["code"] = 0;
			$resp["msg"] = '未知错误！';
			
			if (!$mobile || !$password) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者密码不能为空！';
				return json($resp);
			}

			$user = model('User');
			$uid  = $user->login($mobile, $password);

			if ($uid > 0) {
				$resp["code"] = 1;
				$resp["msg"] = '登录成功！';
				session("mobile", $mobile);
				// session("uid", $uid);
				return json($resp);
			} else {
				switch ($uid) {
				case -1:$error = '用户不存在或被禁用！';
					break; //系统级别禁用
				case -2:$error = '密码错误！';
					break;
				default:$error = '未知错误！';
					break; // 0-接口参数错误（调试阶段使用）
				}
				$resp["code"] = 0;
				$resp["msg"] = $error;
				return json($resp);
			}
		} else {
			$is_success = db('dealer')->field('bank_account_id')->where('mobile',$mobile)->find();
			if ($is_success['bank_account_id']){
				return $this->redirect("/business/user/guide");
			}
			return $this->fetch();
		}

	}
	public function logout(){
		$resp["code"] = 1;
		$resp["msg"] = "登出成功！";
		session(null);
		return $resp;
	}
	public function waiting(){
		$mobile = session('mobile');
		db('dealer')->where('mobile',$mobile)->setField('status','3');
		return $this->fetch();
	}
}