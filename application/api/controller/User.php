<?php

namespace app\api\controller;
use app\common\controller\Api;

class User extends Api {
	
	public function index($username = '', $password = '', $verify = ''){
		$resp["code"] = 1;
		$resp["msg"] = "USER-API";
		return json($resp);
	}
	
	/**
	 * 用户注册
	 * @author fwj <fwj@vpdai.com>
	 */
	public function reg($mobile = '', $password = '', $authcode = '', $invitecode = ''){
		$model = \think\Loader::model('User');
		if (!$mobile || !$password) {
				return $this->error('手机号或者密码不能为空！','');
			//	return ['code'=>0,'msg'=>'手机号或者密码不能为空！'];
		}		
		//$this->checkVerify($verify);		
		//创建注册用户
		$uid = $model->registerByMobile($mobile, $password,$password , false);

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
	
	public function login($mobile = '', $password = '', $imgVerify = ''){
		
		$resp["code"] = 1;
		$resp["msg"] = "登录成功！";
		return $resp;
	}
	
	public function logout(){
		
		$resp["code"] = 1;
		$resp["msg"] = "登出成功！";
		return $resp;
	}
	
	
	public function editPassword($oldPassword = '', $newPassword = ''){
		
		$resp["code"] = 1;
		$resp["msg"] = "修改成功！";
		return $resp;
	}
	
	
	public function userInfo(){
		
		$resp["code"] = 1;
		$resp["msg"] = "修改成功！";
		$data["id"] = 1;
		$data["headerimgurl"] = "https://xxx.xxx.com/xxx.png";
		$data["realname"] = "张三";
		$data["mobile"] = "15869025220";
		$data["roleid"] = 1;
		$data["status"] = 1;
		$resp["data"] = $data;
		return $resp;
	}
	

	
	public function sendSmsVerify($mobile = "", $imgVerify = null){
	
		if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
			$resp["code"] = 0;
			$resp["msg"] = "手机号格式错误!";
			return $resp;
		}
		
		$needImgVerify = session('needImgVerify');
		if($needImgVerify == 1 ){
			if($imgVerify == null){
				session('needImgVerify', 1);
				$resp["code"] = -1;
				$resp["msg"] = "需要图形验证码!";
				return $resp;
			}else{
				$storeImgVerify = session('imgVerify');
				if($storeImgVerify != $imgVerify){
					$resp["code"] = -2;
					$resp["msg"] = "图形验证码错误!";
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
		
 	/**
	 * 验证码
	 * @param  integer $id 验证码ID
	 * @author 郭平平 <molong@tensent.cn>
	 */
	public function getImgVerify($sid) {
		$verify = new \org\Verify(array('length' => 4));
		$verify->entry($sid);
		return json('')->header(['Content-Type' => 'image/png']);
	}

	
}

