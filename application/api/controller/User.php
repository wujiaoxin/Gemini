<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use think\Request;
use app\common\controller\Base;

class User extends Base {
	

	public function index($username = '', $password = '', $verify = ''){
		/*
		if (IS_POST) {
			if (!$username || !$password) {
				return $this->error('用户名或者密码不能为空！','');
			}
			//验证码验证
			$this->checkVerify($verify);
			$user = model('User');
			$uid = $user->login($username,$password);
			if ($uid > 0) {
				$url = session('http_referer') ? session('http_referer') : url('user/index/index');
				return $this->success('登录成功！', $url);
			}else{
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				return $this->error($error,'');
			}
		}else{
			session('http_referer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
			if (is_login()) {
				return $this->redirect('user/index/index');
			}else{
				return $this->fetch();
			}
		}*/
		//return $this->success('user api index', url('admin/user/index'));
		
		$data = ['name' => 'thinkphp', 'status' => '1'];
		
		//"Content-Type: image/png"
       // return json($data)->code(201)->header(['Content-Type' => 'text/json']);
	   return $data;
	}

	
	
	/**
	 * 用户注册
	 * @author fwj <fwj@vpdai.com>
	 */
	public function reg($mobile = '', $password = '', $repassword  = '', $verify = ''){
		$model = \think\Loader::model('User');
		if (!$mobile || !$password) {
				return $this->error('手机号或者密码不能为空！','');
			//	return ['code'=>0,'msg'=>'手机号或者密码不能为空！'];
		}		
		$this->checkVerify($verify);		
		//创建注册用户
		$uid = $model->registerByMobile($mobile, $password,$repassword , false);

		if (0 < $uid) {
			$userinfo = array('nickname' => $mobile, 'status' => 1, 'reg_time' => time(), 'last_login_time' => time(), 'last_login_ip' => get_client_ip(1));
			//保存信息
			if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
				return $this->error('注册失败！', '');
			} else {
				return $this->success('注册成功！', url('admin/user/index'));
			}
		} else {
			return $this->error($model->getError());
		}

	}
	
	public function login() {
		
		$date["old"] = session('name');
		session('name',time());
		$date["new"] = session('name');
		
		
		//dump(session_id());verify_code
		return json($date);
	}
		
 	/**
	 * 验证码
	 * @param  integer $id 验证码ID
	 * @author 郭平平 <molong@tensent.cn>
	 */
	public function getverify($id = 1) {
		//header("Content-Type: image/png");
		$verify = new \org\Verify(array('length' => 4));
		$verify->entry($id);		
		
		
		//return header("Content-Type: image/png");
		//header("Content-Type: image/png");
		return json('')->header(['Content-Type' => 'image/png']);//hehe
	}

	
}

