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
			$this->redirect('mobile/user/login');
			exit();
		}
	}

	public function index() {
		//$this->assign($user);	
		$user = model('User')->getInfo(session('user_auth.uid'));
		if(!empty($user['openid'])){
			$wechatInfo = db('MemberWechat')->field('nickname,headimgurl')->where('openid',$user['openid'])->find();
			if($wechatInfo!=null){
				$user['nickname'] = $wechatInfo['nickname'];
				$user['headimgurl'] = $wechatInfo['headimgurl'];
			}
		}		
		$this->assign('user', $user);
		return $this->fetch();
	}
	
	
	public function register($username = '', $password = '', $smsCode  = '', $icode = ''){
		if (IS_POST) {
			$resp["code"] = 0;
			$resp["msg"] = '未知错误！';
			
			if (!$username || !$password) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者密码不能为空！';
				return json($resp);
			}
			
			if (!$smsCode) {
				$resp["code"] = 0;
				$resp["msg"] = '短信验证码不能为空！';
				return json($resp);
			}
			
			$realSmsCode = session('smsCode');
			$smsPhone = session('smsPhone');
			if($realSmsCode != $smsCode || $smsPhone != $username){
				$resp["code"] = 0;
				$resp["msg"] = '短信验证码错误！';
				return json($resp);
			}
			
			if($icode != ''){
				$dealerInfo = db('Dealer')->field('id,name')->where('invite_code',$icode)->where('status',1)->find();
				if($dealerInfo == null){
					$resp["code"] = 0;
					$resp["msg"] = '车商邀请码有误！';
					return json($resp);
				}else{
					$addr = $dealerInfo["name"];
					$access_group_id = 1;//TODO:后台权限组添加
				}
			}else{
				$addr = '待审核用户';
				$access_group_id = 0;
			}
			$user = model('User');
			$uid = $user->registerByMobile($username, $password);
			if ($uid > 0) {
				$userinfo['nickname']    = $username;
				$userinfo['addr']        = $addr;
				$userinfo['status']      = 1;
				$userinfo['invite_code'] = $icode;
				$userinfo['access_group_id'] = $access_group_id;
				$userinfo['reg_time']    = time();
				$userinfo['status']      = 1;			
				$openid = session('user_openid');
				if(!empty($openid)){
					$userinfo['openid'] = $openid;
				}
				//保存信息
				if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
					//TODO:更新信息信息失败回滚
					$resp["code"] = 0;
					$resp["msg"] = '注册失败！！';
					return json($resp);
				} else {
					$resp["code"] = 1;
					$resp["msg"] = '注册成功！';
					return json($resp);
				}
			} else {
				$resp["code"] = 0;
				$resp["msg"] = $user->getError();
				return json($resp);
			}
		} else {
			return $this->fetch();
		}
	}
	
	public function login($username = '', $password = '', $wxbind = 0) {
		if (IS_POST) {
			$resp["code"] = 0;
			$resp["msg"] = '未知错误！';
			
			if (!$username || !$password) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者密码不能为空！';
				return json($resp);
			}
			//验证码验证
			//$this->checkVerify($verify);

			$user = model('User');
			$uid  = $user->login($username, $password);

			if ($uid > 0) {
				$resp["code"] = 1;
				$resp["msg"] = '登录成功！';
				if($wxbind == 1){
					$user->bindWechat($uid);
				}
				//$resp["redirectUrl"] = url('admin/index/index');
				return json($resp);
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
				$resp["code"] = 0;
				$resp["msg"] = $error;

				return json($resp);
				//return $this->error($error, '');
			}
		} else {
			$this->assign('wxbind', $wxbind);
			return $this->fetch();
		}

	}
	
	
	public function logout () {
		$user = model('User');
		$user->logout();
		$this->redirect('mobile/user/login');
	}

/*
重置密码
@parame

TODO:check idcard if needed;
*/
	public function reset($username = '', $smsCode = '', $newPassword = '', $idcard = "") {
		if (IS_POST) {			
			$resp["code"] = 0;
			$resp["msg"] = '未知错误！';
			
			if (!$username || !$newPassword) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者新密码不能为空！';
				return json($resp);
			}
			
			if (!$smsCode) {
				$resp["code"] = 0;
				$resp["msg"] = '短信验证码不能为空！';
				return json($resp);
			}
			
			$realSmsCode = session('smsCode');
			$smsPhone = session('smsPhone');
			if($realSmsCode != $smsCode || $smsPhone != $username){
				$resp["code"] = 0;
				$resp["msg"] = '短信验证码错误！';
				return json($resp);
			}
			
			$user = model('User');	
			$result = $user->resetpw($username,$newPassword);
			if ($result !== false) {
				return $this->success("更新成功！", "");
			}else{
				return $this->error($user->getError(), '');
			}
		}else{
			return $this->fetch();
		}
	}
	
/*
修改密码
@parame

@return

*/
	public function editpwd($oldpassword = '', $password = '') {		
		if (IS_POST) {
			$user = model('User');
			$data['oldpassword'] = $oldpassword;
			$data['password'] = $password;			
			$result = $user->editpw($data);
			if ($result !== false) {
				return $this->success("更新成功！", "");
			}else{
				return $this->error($user->getError(), '');
			}
		}else{
			return $this->fetch();
		}
	}

	// 车商员工注册页
	public function registerDealer() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 车商员工登录页
	public function loginDealer() {
		//$this->assign($user);		
		return $this->fetch();
	}
	//车商员工个人中心
	public function personalCenter() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 二维码页面
	public function QRCode() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 我的积分
	public function myPoints() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 我的客户
	public function myCustomers() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 账户设置
	public function myAccount() {
		//$this->assign($user);		
		return $this->fetch();
	}	

}
