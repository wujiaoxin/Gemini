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
			if ($success['access_group_id'] == '7' || $success['access_group_id'] == '13' || $success['access_group_id'] == '14' ||$success['access_group_id'] == '15' || $success['access_group_id'] == '16' || $success['access_group_id'] == '17' || $success['access_group_id'] == '18' || $success['access_group_id'] == '19') {
				$user = model('User');
				$uid  = $user->login($mobile, $password);
				if ($uid > 0) {
					$resp["code"] = 1;
					$resp["msg"] = '登录成功！';
					session("business_mobile", $mobile);
					if ($success['access_group_id'] == '13' || $success['access_group_id'] == '14' ||$success['access_group_id'] == '15' || $success['access_group_id'] == '16' || $success['access_group_id'] == '17' || $success['access_group_id'] == '18' ) {
						$resp['data'] ='1';
					}elseif ($success['access_group_id'] == '19') {
						$resp['data'] = '3';
					}else{
						$resp['data'] = '2';
					}
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
			}else{
				$resp["code"] = 0;
				$resp["msg"] = '没有权限登陆此后台！';
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
		$res = db('dealer')->field('status,property')->where('mobile',$mobile)->find();
		if ($res['status'] == '1' && $res['property'] == '3') {
			$this->redirect(url('/guarantee/index/index'));
		}elseif ($res['status'] == '1' && $res['property'] == '2') {
			$this->redirect(url('index/index'));
		}elseif ($res['status'] == '1' && $res['property'] == '2') {
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