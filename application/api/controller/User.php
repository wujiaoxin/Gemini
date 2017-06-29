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
		
		$dealer_id = null;
		$access_group_id = 0;//C端客户
		
		if(!empty($authcode)){
			if($authcode == "dealer"){
				$access_group_id = 7;//车商管理员
			}else{
				$dealerInfo = db('Dealer')->field('id,name')->where('invite_code',$authcode)->where('status',1)->find();
				if($dealerInfo == null){
						$resp["code"] = 0;
						$resp["msg"] = '车商邀请码有误！';
						return json($resp);
				}else{
					$dealer_id = $dealerInfo["id"];
					$access_group_id = 1;//车商员工
					//TODO:roleid
				}
			}
		}

		if($mobile != $storeMobile || $smsverify != $storeSmsCode){
			return ['code'=>1005,'msg'=>'短信验证码错误'];
		}
		
		$uid = $model->registerByMobile($mobile, $password, $password, false);
		if (0 < $uid) {
			$userinfo = array('nickname' => $mobile,'realname' => "未实名",'dealer_id' => $dealer_id,'access_group_id' => $access_group_id,'invite_code' => $invitecode, 'status' => 1, 'reg_time' => time(), 'last_login_time' => time(), 'last_login_ip' => get_client_ip(1));
			if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
				$resp["code"] = 0;
				$resp["msg"] = '注册失败';
				return json($resp);
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
			$resp["code"] = 0;
			$resp["msg"] = $model->getError();
			return json($resp);
		}
	}
	
	public function login($mobile = '', $password = '', $imgverify = null, $smsverify=null, $sid = null){
		$resp["code"] = 0;
		$resp["msg"] = '未知错误';		
				
		//验证码验证 TODO
		//$this->checkVerify($verify);

		$user = model('User');
		if ($smsverify) {
			$uid  = $user->ulogin($mobile, $smsverify);
			/* 验证验证码 */
			$storeSmsCode = session('smsCode');
			if ($smsverify != $storeSmsCode) {
				return ['code'=>1005,'msg'=>'短信验证码错误'];
			}
			$role = session('user_auth.role');
			if ($role != '1') {
				return ['code'=>1010,'msg'=>'没有权限登录'];
			}
		}else{
			if (!$mobile || !$password) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者密码不能为空！';
				return json($resp);
			}
			$uid  = $user->login($mobile, $password);
		}
		if ($uid > 0) {
			
			
			//session('uid',$uid);
			//session('mobile',$mobile);
			//$token = rand(100000,999999);
			$token = generateToken($uid, $sid);
			session('token',$token);
			
			$userInfo = db('member')->field('uid,mobile,username,realname,idcard,bankcard,status,access_group_id,headerimgurl')->where('uid',$uid)->find();
			$userInfo['roleid'] = $userInfo['access_group_id'];
			unset($userInfo['access_group_id']);
			
			if(empty($userInfo['headerimgurl'])){
				$userInfo['headerimgurl'] = "https://www.vpdai.com/public/images/default_avatar.jpg";
			}
			$userInfo['token'] = generateToken($uid, $sid);
			$role  = session('user_auth.role');
			if ($role  == '0' || $role == '1' || $role == '7') {
				$resp["code"] = 1;
				$resp["msg"] = '登录成功';	
				$resp["data"] = $userInfo;
				
			}else{
				$resp["code"] = 0;
				$resp["msg"] = '没有权限登录';	
			}
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
		$role  = session('user_auth.role');
		if ($uid > 0) {
			if ($role  == '0') {
				$userInfo = db('member')->field('uid,mobile,username,realname,idcard,bankcard,status,access_group_id,headerimgurl')->where('uid',$uid)->find();
				$userInfo['roleid']   = $userInfo['access_group_id'];
				unset($userInfo['access_group_id']);
			}elseif ($role  == '1') {
				$userInfo = db('member')->alias('m')->field('m.uid,m.mobile,m.username,m.realname,m.idcard,m.bankcard,m.status,m.access_group_id,m.headerimgurl,d.name as dealer_name')->join('__DEALER__ d','m.dealer_id = d.id','LEFT')->where('m.uid',$uid)->find();
			}else{
				$userInfo = db('member')->alias('m')->field('m.uid,m.mobile,m.username,m.realname,m.idcard,m.bankcard,m.status,m.access_group_id,m.headerimgurl,d.name as dealer_name')->join('__DEALER__ d','m.mobile = d.mobile','LEFT')->where('m.uid',$uid)->find();
			}
			
			
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
	

	
	public function sendSmsVerify($mobile = "", $imgverify = null){
	
		if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
			$resp["code"] = 0;
			$resp["msg"] = "手机号格式错误";
			return $resp;
		}
		
		/*$needImgVerify = session('needImgVerify');
		if($needImgVerify == 1 ){
			if($imgverify == null){
				session('needImgVerify', 1);
				$resp["code"] = -2;
				$resp["msg"] = "需要图形验证码";
				return $resp;
			}else{
				$storeImgVerify = session('imgVerify');
				if($storeImgVerify != $imgverify){
					$resp["code"] = 1001;
					$resp["msg"] = "图形验证码错误";
					return $resp;
				}
			}
		}*/
		
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
		//if(sendSms($mobile,$smsMsg)){
		if(sendSmsCode($mobile,$smsCode)){
			session('smsCode',$smsCode);
			session('mobile',$mobile);
			session('needImgVerify', 0);
			session('lastSmsSendTime',time());
			$resp["code"] = 1;
			$resp["msg"] = "发送成功！";
			//$resp["data"] = $smsCode;
			
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
			//银联三要素验证
			$authvalidTime = session('authvalidTime');
			if($authvalidTime != null){
				if($authvalidTime == 1){
					session('authvalidTime', 2);
					$resp["code"] = -3;
					$resp["msg"] = "已验证成功";
				}
			}else{
				$event = new \app\riskmgr\controller\Yinlian();
				$res = $event->authvalid($idcard,$realname,$bankcard,'',4);
				if (!empty($res)) {
					if ($res['resCode'] == '0000' && $res['stat'] == '1') {
						$resp["code"] = 1;
						$resp["msg"] = '更新成功';	
						$resp["data"] = $saveData;
						session('authvalidTime',1);
					}elseif ($res['resCode'] == '0000' && $res['stat'] == '2') {
						$resp["code"] = 4;
						$resp["msg"] = '实名信息不匹配';	
						// $resp["data"] = $saveData;
						return json($resp);
					}else{
						$resp["code"] = 5;
						$resp["msg"] = empty($res['resMsg']) ? '数据异常': $res['resMsg'];;	
						// $resp["data"] = $saveData;
						return json($resp);
					}
				}else{
					$resp["code"] = 3;
					$resp["msg"] = "信息录入异常,请联系客服";
					return json($resp);
				}
			}
			
			db('member')->where('uid',$uid)->update($saveData);			
			
			//加入绑卡记录
			$results = array(
				'uid'=>$uid,
				'type'=>1,
				'order_id'=>-1,//C端绑卡判断
				'bank_account_id'=>$bankcard,
				'idcard'=>$idcard,
				'bank_account_name'=>$realname,
				'create_time'=>time(),
				'status'=>2,
				);
			db('bankcard')->insert($results);
			$userInfo = db('member')->field('mobile')->where("uid",$uid)->find();
			
			if($userInfo != null ){
				$mobile = $userInfo['mobile'];
				if(!empty($mobile)){
					$orderData['name'] = $realname;
					$orderData['idcard_num'] = $idcard;
					$where = array(
						'mobile'=>$mobile,
						'status'=>-2
					);
					db('order')->where($where)->update($orderData);//更新order表
					
					$price = db('order')->field('type,car_price,id')->where($where)->find();

					if ($price['type'] == 2 || $price['type'] == 4) {

						if ($price['car_price'] < 20) {

							$info['uid'] = $uid;

							$info['mobile'] = $mobile;

							$info['order_id'] = $price['id'];

							$info['mobile_password'] = "";
							
							
							$info['credit_status'] = 3;
							$info['mobile_collect_token'] = '';
							$info['update_time'] = time();

							$info['create_time'] = time();

							$result = db('credit')->insert($info);			
						}
					}
					

					$resp["code"] = 1;
					$resp["msg"] = "更新成功";		
				}else{
					$resp["code"] = 0;
					$resp["msg"] = "更新失败";
				}
			}else{
				$resp["code"] = 0;
				$resp["msg"] = "未知错误";
			}
			return json($resp);
		}else{
			$resp["code"] = 0;
			$resp["msg"] = "更新失败";
			return json($resp);
		}
	}
	
	public function decodedToken($token = ''){
		$decode = decodedToken($token);
		$data = json_decode($decode);		
		if(empty($data)){
			$resp["code"] = 0;
			$resp["msg"] = "解析失败";
		}else{
			$resp["code"] = 1;
			$resp["msg"] = "解析成功";
			$resp["data"] = $data;
		}
		return json($resp);
	}
	


	public function urlogin($mobile = '',$smsverify = null, $sid = null){
		$resp["code"] = 0;
		$resp["msg"] = '未知错误';

		if (!$mobile) {
			$resp["code"] = 0;
			$resp["msg"] = '用户名不能为空！';
			return json($resp);
		}
		$user = model('User');
		$uid  = $user->ulogin($mobile, $smsverify);
		if ($uid > 0) {
			
			$token = generateToken($uid, $sid);
			session('token',$token);
			
			$userInfo = db('member')->field('uid,mobile,username,realname,idcard,bankcard,status,access_group_id,headerimgurl')->where('uid',$uid)->find();
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


	//上传头像文件
	public function upload($file = null){
		$controller = controller('common/Avatar');
		$action     = $this->request->action();
		return $controller->$action();
	}
}

