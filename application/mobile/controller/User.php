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

class User extends Base {
	public function _initialize() {
		parent::_initialize();
		if (!is_login() and !in_array($this->url, array('mobile/user/login', 'mobile/user/reset', 'mobile/user/register'))) {
			$this->redirect('mobile/user/login');exit();
		}elseif (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			//if (!$this->checkProfile($user) && $this->url !== 'user/profile/index') {
			//	return $this->error('请补充完个人资料！', url('user/profile/index'));
			//}
			$this->assign('user', $user);
		}
	}

	public function index() {	
		
		
		//$this->assign($user);
		
		
		return $this->fetch();
	}
	
	
	public function register($username = '', $password = '', $smsCode  = '', $icode = ''){
		if (IS_POST) {			
			$data["code"] = 0;
			$data["msg"] = '未知错误！';
			
			if (!$username || !$password) {
				$data["code"] = 0;
				$data["msg"] = '用户名或者密码不能为空！';
				return json($data);
			}
			//TODO：check smsCode
			$user = model('User');
			$uid = $user->registerByMobile($username, $password, $password , false);
			if ($uid > 0) {
				$userinfo = array('nickname' => $username, 'status' => 1, 'reg_time' => time(), 'last_login_time' => time(), 'last_login_ip' => get_client_ip(1));
				//保存信息
				if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
					$data["code"] = 0;
					$data["msg"] = '注册失败！！';
					return json($data);
				} else {
					$data["code"] = 1;
					$data["msg"] = '注册成功！';
					return json($data);
				}
			} else {
				$data["code"] = 0;
				$data["msg"] = $user->getError();
				return json($data);
			}
		} else {
			return $this->fetch();
		}
	}
	
	public function login($username = '', $password = '') {
		if (IS_POST) {
			$data["code"] = 0;
			$data["msg"] = '未知错误！';
			
			if (!$username || !$password) {
				$data["code"] = 0;
				$data["msg"] = '用户名或者密码不能为空！';
				return json($data);
			}
			//验证码验证
			//$this->checkVerify($verify);

			$user = model('User');
			$uid  = $user->login($username, $password);

			if ($uid > 0) {
				$data["code"] = 1;
				$data["msg"] = '登录成功！';
				//$data["redirectUrl"] = url('admin/index/index');
				return json($data);
				//return $this->success('登录成功！', url('admin/index/index'));
			} else {
				switch ($uid) {
				case -1:$error = '用户不存在或被禁用！';
					break; //系统级别禁用
				case -2:$error = '密码错误！';
					break;
				default:$error = '未知错误！';
					break; // 0-接口参数错误（调试阶段使用）
				}
				$data["code"] = 0;
				$data["msg"] = $error;

				return json($data);
				//return $this->error($error, '');
			}
		} else {
			return $this->fetch();
		}

	}
	
	
	public function logout () {
		$user = model('User');
		$user->logout();
		$this->redirect('mobile/user/login');
	}

	
	public function reset() {

		return $this->fetch();
	}
}
