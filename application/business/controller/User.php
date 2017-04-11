<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\business\controller\Baseness;
 class User extends Baseness {
	public function guide() {
		$mobile = session("mobile");
		$modelDealer = model('Dealer');
		// 检测商户是否已经录入信息
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				unset($data['id']);
				unset($data['status']);
				unset($data['mobile']);
				$result = $modelDealer->save($data, array('mobile' => $mobile));
				if ($result) {
					return $this->success("修改成功！", url(''));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($modelDealer->getError());
			}
		} else {
			$info = db('Dealer')->where(array('mobile' => $mobile))->find();			
			if(!$info){
				$data['mobile'] = $mobile;
				$data['invite_code'] = $modelDealer->buildInviteCode();
				$result = $modelDealer->save($data);
				$info = db('Dealer')->where(array('mobile' => $mobile))->find();
			}
			$data = array(
				'info'    => $info,
				'infoStr' => json_encode($info),
			);
			$this->assign($data);
			return $this->fetch();
		}
	}

	public function myStaff() {
		//商家员工
		$uid = session("uid");
		$members = db('member')->alias('m')->join('__DEALER__ d','m.invite_code = d.invite_code')->field("m.uid,m.realname,m.mobile,m.reg_time,m.status,m.access_group_id")->select();
		$data = array(
				'info'    => $members,
				'infoStr' => json_encode($members),
		);
		$this->assign($data);
		return $this->fetch();
	}
	/*
	 * 商家操作员工是否有效
	 * */
	public function editStaff(){
		if (IS_POST) {
			$data = input('post.');
			if($data){
				$status = db('Member')->where('mobile',$data['mobile'])->setField('status',$data['status']);
				if ($status) {
					$data['code'] = '1';
					$data['msg'] = '更新成功';
				}else{
					$data['code'] = '0';
					$data['msg'] = '更新失败';
				}
			}
			return json($data);
		}
	}
	public function newStaff() {
		return $this->fetch();
	}
	/*
	 * 商户新增员工接口
	 * */
	public function addStaff(){
		// var_dump($GLOBALS);die;
		if (IS_POST){
			$data = input('post.');
			if ($data) {
				$invit = db('Dealer')->alias('d')->field('d.invite_code')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
				$user = model('User');
				$uid = $user->registerByMobile($data['mobile'], $data['password']);
				if ($uid > 0) {
					$userinfo['realname'] = $data['name'];
					$userinfo['mobile'] = $data['mobile'];
					$userinfo['status'] = 0;
					$userinfo['invite_code'] = $invit['invite_code'];
					$userinfo['access_group_id'] = $data['job'];
					$userinfo['desc'] = $data['remark'];
					$userinfo['tel'] = $data['telphone'];
					$userinfo['reg_time'] = time();
					//保存信息
					if (!db('Member')->where(array('uid' => $uid))->update($userinfo)) {
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
			}
		}
	}

	public function myShop() {
		return $this->fetch();
	}

	public function loanItem() {
		$uid = session('uid');
		if (IS_POST) {
			$data = input('post.');
			$map['mid'] =$uid;
			if ($data['type'] !='') {
				$map['type'] = $data['type'];
			}
			if ($data['status'] !='') {
				$map['status'] = $data['status'];
			}
			$result = db('order')->where($map)->select();
		}else{
			$result = db('order')->where('mid',$uid)->select();
		}
		foreach ($result as $k => $v) {
			$result[$k]['realname'] = serch_real($v['uid']);
		}
		$data = array(
			'info'=>$result,
			'infoStr'=>json_encode($result)
		);
		// var_dump($data);die;
		$this->assign($data);
		return $this->fetch();
	}

	public function repayItem() {
		$uid =session('uid');
		if (IS_POST) {
			$data = input('post.');
			$map['mid'] =$uid;
			if ($data['type'] !='') {
				$map['type'] = $data['type'];
			}
			if ($data['status'] !='') {
				$map['status'] = $data['status'];
			}
			$result = db('order')->where($map)->order('status ASC')->select();
		}else{
			$order_repay = db('order_repay')->where('mid',$uid)->order('status ASC')->select();
			
		}
		foreach ($order_repay as $k => $v) {
			$uid = serch_order($v['order_id']);
			$order_repay[$k]['yewu_realname'] = serch_real($uid);
		}
		$data = array(
			'info'=>$order_repay,
			'infoStr'=>json_encode($order_repay)
		);
		$this->assign($data);
		return $this->fetch();
	}

	public function payItem() {
		$uid =session('uid');
		if (IS_POST) {
			$data = input('post.');
			$map['mid'] =$uid;
			if ($data['type'] !='') {
				$map['type'] = $data['type'];
			}
			if ($data['status'] !='') {
				$map['status'] = $data['status'];
			}
			$result = db('order')->where($map)->order('status ASC')->select();
		}else{
			$order = db('order')->where('mid',$uid)->order('status ASC')->select();
			
		}
		foreach ($order as $k => $v) {
			$order[$k]['realname'] = serch_real($v['uid']);
		}
		$data = array(
			'info'=>$order,
			'infoStr'=>json_encode($order)
		);
		$this->assign($data);
		return $this->fetch();
	}
	//设置交易密码
	public function setpay(){
		$mobile = session("mobile");
		$user = model('User');
		if (IS_POST) {
			$data = input('post.');
			if ($data['paypassword'] === $data['repaypassword']) {
				$result = $user->setpaypw($mobile,$data['paypassword']);
				if($result){
					return ['code'=>1,'msg'=>'支付密码设置成功'];
				}
			}else{
				return ['code'=>1003,'msg'=>'两次密码不一致'];
			}
		}
		
	}
	//修改交易密码
	public function resetpay(){
		if (IS_POST) {
			$user = model('User');
			$data = $this->request->post();

			$res = $user->editpaypw($data);
			if ($res) {
				return $this->success('修改密码成功！');
			} else {
				return $this->error($user->getError());
			}
		} else {
			$this->setMeta('修改密码');
			return $this->fetch();
		}

	}
	//发送验证码
	public function sendSmsVerify($mobile = "", $imgVerify = null){
		if(!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
			$resp["code"] = 0;
			$resp["msg"] = "手机号格式错误";
			return $resp;
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
}