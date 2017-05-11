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
			$success = db('member')->field('access_group_id')->where('mobile',$mobile)->find();
			if ($success['access_group_id'] != '7') {
				$resp["code"] = 0;
				$resp["msg"] = '没有权限登陆此后台！';
				return json($resp);
			}
			$user = model('User');
			$uid  = $user->login($mobile, $password);

			if ($uid > 0) {
				$resp["code"] = 1;
				$resp["msg"] = '登录成功！';
				session("business_mobile", $mobile);
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
		$mobile = session('business_mobile');
		$status = db('dealer')->field('status')->where('mobile',$mobile)->find();
		if ($status['status'] == '1') {
			$this->redirect(url('index/index'));
		}else{
			db('dealer')->where('mobile',$mobile)->setField('status','3');
		}
		return $this->fetch();
	}

	public function protocal(){
		return $this->fetch();
	}
}