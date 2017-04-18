<?php

namespace app\api\controller;
use app\common\controller\Api;

class User extends Api {

	public function index($username = '', $password = '', $verify = ''){
		$resp["code"] = 1;
		$resp["msg"] = "USER-API";
		return json($resp);
	}  
	
	public function reg($mobile = null, $password = null, $smsverify = null, $authcode = null, $invitecode = null, $sid = null){
		$model = model('User');
		$resp["code"] = 0;
		$resp["msg"] = "注册失败";
		
		if (!$mobile) {
			return ['code'=>1002,'msg'=>'手机号不能为空'];
		}
		if (!$password) {
			return ['code'=>1003,'msg'=>'密码不能为空'];
		}
		$storeMobile = session('mobile');
		$storeSmsCode = session('smsCode');

		if($mobile != $storeMobile || $smsverify != $storeSmsCode){
			return ['code'=>1005,'msg'=>'短信验证码错误'];
		}
		
		$uid = $model->registerByMobile($mobile, $password, $password, false);
		if (0 < $uid) {
			$userinfo = array('nickname' => $mobile, 'status' => 1, 'reg_time' => time(), 'last_login_time' => time(), 'last_login_ip' => get_client_ip(1));
			if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
				return $this->error('注册失败！', '');
			} else {
				$token = generateToken($uid, $sid);
				$resp["code"] = 1;
				$resp["msg"] = '注册成功';			
				$data["token"] = $token;
				$resp["data"] = $data;
				session('token',$token);
				return json($resp);
			}
		} else {
			return $this->error($model->getError());
		}
	}
	
	public function login($mobile = '', $password = '', $imgverify = null, $sid = null){
		$resp["code"] = 0;
		$resp["msg"] = '未知错误';		
		if (!$mobile || !$password) {
			$resp["code"] = 0;
			$resp["msg"] = '用户名或者密码不能为空！';
			return json($resp);
		}		
		//验证码验证 TODO
		//$this->checkVerify($verify);

		$user = model('User');
		$uid  = $user->login($mobile, $password);
		if ($uid > 0) {
			
			//session('uid',$uid);
			//session('mobile',$mobile);
			//$token = rand(100000,999999);
			$token = generateToken($uid, $sid);
			session('token',$token);
			
			$userInfo = db('member')->field('mobile,username,realname,idcard,bankcard,status,access_group_id,headerimgurl')->where('uid',$uid)->find();
			$userInfo['roleid'] = $userInfo['access_group_id'];
			unset($userInfo['access_group_id']);
			
			if(empty($userInfo['headerimgurl'])){
				$userInfo['headerimgurl'] = "https://www.vpdai.com/public/images/default_avatar.jpg";
			}
			$userInfo['token'] = generateToken($uid, $sid);
			
			$resp["code"] = 1;
			$resp["msg"] = '登录成功';	
			$resp["data"] = $userInfo;
			
			return json($resp);
		} else {
			switch ($uid) {
				case -1:{
						$resp["code"] = 0;
						$resp["msg"] = "用户不存在或被禁用！";
						break; //系统级别禁用
				}
				case -2:{
						$resp["code"] = 1002;
						$resp["msg"] = "密码错误";
						break;
					}
				default:{
						$resp["code"] = 0;
						$resp["msg"] = "登录失败";
						break; // 0-接口参数错误（调试阶段使用）
					}
				}
			return json($resp);
		}

	}
	
	public function logout(){
		session(null);
		$resp["code"] = 1;
		$resp["msg"] = "登出成功！";
		return $resp;
	}
	
	public function editPassword($oldPassword = '', $newPassword = '') {
		$user = model('User');
		
		$data['uid']  = session('user_auth.uid');
		
		$data['oldpassword'] = $oldPassword;
		$data['password'] = $newPassword;
		
		$result = $user->editpw($data);
		if ($result !== false) {
			$resp["code"] = 1;
			$resp["msg"] = "修改成功！";
			return json($resp);
		}else{
			$resp["code"] = 0;
			$resp["msg"] = $user->getError();
			return json($resp);
		}
	}

	public function userInfo(){
		
		$uid  = session('user_auth.uid');
		if ($uid > 0) {
			
			$userInfo = db('member')->field('mobile,username,realname,idcard,bankcard,status,access_group_id,headerimgurl')->where('uid',$uid)->find();
			$userInfo['roleid']   = $userInfo['access_group_id'];
			unset($userInfo['access_group_id']);
			
			if(empty($userInfo['headerimgurl'])){
				$userInfo['headerimgurl'] = "https://www.vpdai.com/public/images/default_avatar.jpg";
			}
			
			$resp["code"] = 1;
			$resp["msg"] = '获取成功';	
			$resp["data"] = $userInfo;

			return json($resp);
		}else{
			$resp["code"] = 0;
			$resp["msg"] = "获取失败";
			return $resp;
		}
	}
	

	
	public function sendSmsVerify($mobile = "", $imgVerify = null){
	
		if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
			$resp["code"] = 0;
			$resp["msg"] = "手机号格式错误";
			return $resp;
		}
		
		$needImgVerify = session('needImgVerify');
		if($needImgVerify == 1 ){
			if($imgVerify == null){
				session('needImgVerify', 1);
				$resp["code"] = -2;
				$resp["msg"] = "需要图形验证码";
				return $resp;
			}else{
				$storeImgVerify = session('imgVerify');
				if($storeImgVerify != $imgVerify){
					$resp["code"] = 1001;
					$resp["msg"] = "图形验证码错误";
					return $resp;
				}
			}
		}
		
		$lastSmsSendTime = session('lastSmsSendTime');
		if($lastSmsSendTime != null){
			$nowTime = time();
			if($nowTime - $lastSmsSendTime < 30){
				session('needImgVerify', 1);
				$resp["code"] = -3;
				$resp["msg"] = "发送间隔少于30秒!";
				return $resp;
			}
		}
		//TODO:验证码多次输入错误或反复发送验证码
		//$errorTimes = $session('errorTimes');
		//$errorTimes = $errorTimes?0:$errorTimes;

		$content = "";		
		$smsCode = rand(100000,999999);
		$smsMsg = '您的验证码为:' . $smsCode;
		
		//if(1){
		if(sendSms($mobile,$smsMsg)){
			session('smsCode',$smsCode);
			session('mobile',$mobile);
			session('needImgVerify', 0);
			session('lastSmsSendTime',time());
			$resp["code"] = 1;
			$resp["msg"] = "发送成功！";
			
		}else{
			//session('errorTimes',$errorTimes++);
			$resp["code"] = 0;
			$resp["msg"] = "发送失败！";
		}
		return $resp;
	}
	
	
	public function resetPassword($mobile = '', $token = null, $smsverify = null, $newPassword = null, $idcard = null){

		$resp["code"] = 0;
		$resp["msg"] = "找回失败";
	
		if (!$mobile) {
			return ['code'=>1002,'msg'=>'手机号不能为空'];
		}
		if (!$newPassword) {
			return ['code'=>1003,'msg'=>'新密码不能为空'];
		}
		$storeMobile = session('mobile');
		
		if($token != null){
			$storeToken = session('token');
			if($token == $storeToken && $mobile == $storeMobile){
				$resp["code"] = 1;
				$resp["msg"] = "修改成功";
			}
		}else{
			if($smsverify != null){
				
				$storeSmsCode = session('smsCode');
				if($mobile == $storeMobile && $smsverify == $storeSmsCode){
					$resp["code"] = 1;
					$resp["msg"] = "修改成功";
				}else{
					return ['code'=>1005,'msg'=>'短信验证码错误'];
				}
			}
			
		}
		
		if($resp["code"] == 1){			
			$user = model('User');	
			$result = $user->resetpw($mobile,$newPassword);
			if ($result !== false) {
				return $this->success("修改成功", "");
			}else{
				return $this->error($user->getError(), '');
			}
			
		}
		return $resp;
	}
	
	
	public function checkSmsVerify($mobile = '', $smsverify = null, $sid = null){
		
		if (!$mobile) {
			return ['code'=>1002,'msg'=>'手机号不能为空'];
		}
		if (!$smsverify) {
			return ['code'=>1003,'msg'=>'验证码不能为空'];
		}
		
		$storeMobile = session('mobile');
		$storeSmsCode = session('smsCode');

		if($mobile != $storeMobile || $smsverify != $storeSmsCode){			
			return ['code'=>1005,'msg'=>'短信验证码错误'];
		}else{		
			$token = generateToken($mobile, $sid);
			$resp["code"] = 1;
			$resp["msg"] = '校验成功';			
			$data["token"] = $token;
			$resp["data"] = $data;
			session('token',$token);
			return json($resp);
		}
	}
		
	public function getImgVerify() {
		$verify = new \org\Verify(array('length' => 4));
		$verify->entry(1);
		return json('')->header(['Content-Type' => 'image/png']);
	}
	
	public function checkImgVerify($code) {
		if ($code) {
			$verify = new \org\Verify();
			$result = $verify->check($code,1);
			if (!$result) {
				return $this->error("验证码错误！", "");
			}else{
				return $this->success('验证码正确',"");
			}
		} else {
			return $this->error("验证码为空！", "");
		}
	}
	
	public function updateBaseInfo($realname = null, $idcard = null, $bankcard = null ) {		
		$uid  = session('user_auth.uid');
		if ($uid > 0) {
			$saveData["uid"] = $uid;
			
			if($realname!=null){
				$saveData["realname"] = $realname;
			}
			if($idcard!=null){
				$saveData["idcard"] = $idcard;
			}
			if($idcard!=null){
				$saveData["bankcard"] = $bankcard;
			}
			
			db('member')->where('uid',$uid)->update($saveData);
			
			$resp["code"] = 1;
			$resp["msg"] = '更新成功';	
			$resp["data"] = $saveData;

			return json($resp);
		}else{
			$resp["code"] = 0;
			$resp["msg"] = "更新失败";
			return json($resp);
		}
	}
	
}

