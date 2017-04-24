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
		$uid = session('user_auth.uid');
		$members = db('member')->alias('m')->join('__DEALER__ d','m.invite_code = d.invite_code')->field("m.uid,m.realname,m.mobile,m.reg_time,m.status,m.access_group_id")->select();
		$data = array(
				'info'    => $members,
				'infoStr' => json_encode($members),
		);
		$this->assign($data);
		return $this->fetch('myStaff');
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
		return $this->fetch('newStaff');
	}
	/*
	 * 商户新增员工接口
	 * */
	public function addStaff(){
		// var_dump($GLOBALS);die;
		if (IS_POST){
			$data = input('post.');
			// var_dump($data);die;
			if ($data) {
				$invit = db('Dealer')->alias('d')->field('d.invite_code')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
				// $data = $this->request->param();
				$user = model('User');
				//创建注册用户
				// var_dump($data);die;
				$uid = $user->register($data['mobile'], $data['password'], $data['password'],NULL, false);
				// echo $uid;die;
				if ($uid > 0) {
					$userinfo['realname'] = $data['name'];
					$userinfo['nickname'] = $data['name'];
					$userinfo['mobile'] = $data['mobile'];
					$userinfo['status'] = 1;
					$userinfo['invite_code'] = $invit['invite_code'];
					$userinfo['access_group_id'] = $data['job'];
					$userinfo['desc'] = $data['remark'];
					$userinfo['tel'] = $data['telphone'];
					$userinfo['reg_time'] = time();
					$userinfo['last_login_ip'] = get_client_ip(1);
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
					return $resp;
				}
			}
		}
	}

	public function myShop() {
		$uid =session('user_auth.uid');
		$mobile = session('mobile');
		//分组统计
		$result = db('order')->field('mid,uid,sum(loan_limit) as result')->order('result DESC')->group('uid')->select();
		foreach ($result as $k => $v) {
			$result[$k]['realname'] = serch_real($v['uid']);
		}
		$num = db('order')->field('mid,uid,count(id) as result')->order('result DESC')->group('uid')->select();
		foreach ($num as $k => $v) {
			$num[$k]['realname'] = serch_real($v['uid']);
		}
		$avg = db('order')->field('mid,uid,avg(loan_limit) as result')->order('result DESC')->group('uid')->select();
		foreach ($num as $k => $v) {
			$avg[$k]['realname'] = serch_real($v['uid']);
		}

		//一个月内每天的订单数量
		$times = time()-24*3600*30;
		$time =db('order')->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as time,create_time,count(id) as num,sum(loan_limit) as total_money")->group('time')->wheretime('create_time','>',$times)->order('time')->select();
		// var_dump($time);
		$temp['time'] ='0';
		$temp['num'] ='0';
		$temp['total_money'] ='0';
		for ($i=count($time); $i < 30; $i++) {
			array_unshift($time,$temp);
		}
		// var_dump($time);die;
		$info = array(
				'money'=>$result,
				'num'=>$num,
				'avg'=>$avg,
				'time'=>$time
			);
		$data = array(
				'info'=>$info,
				'infoStr'=>json_encode($info)
			);
		$this->assign($data);
		return $this->fetch('myShop');
	}

	public function loanItem() {
		$uid = session('user_auth.uid');
		// var_dump($_SESSION);die;
		if (IS_POST) {
			$data = input('post.');
			$map['mid'] =$uid;
			// var_dump($data);die;
			if ($data['type']) {
				$map['type'] = $data['type'];
			}
			if (isset($data['status'])) {
				if ($data['status'] != '') {
	    			$map['status'] = $data['status'];
	    		}
			}
			if ($data['dateRange']) {
				$result = to_datetime($data['dateRange']);
				$endtime =$result['endtime'];
				$begintime = $result['begintime'];
				$result = db('order')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->select();
			}else{
				// var_dump($map);die;
				$result = db('order')->where($map)->select();
			}
			
			foreach ($result as $k => $v) {
				$result[$k]['realname'] = serch_real($v['uid']);
			}
			if ($result) {
				$resp['code'] = '1';
				$resp['msg'] = '数据正常';
				$resp['data']= $result;
			}else{
				$resp['code'] = '0';
				// $resp['msg'] = '未查到数据';
			}
			return json($resp);
		}
		return $this->fetch('loanItem');
	}

	public function repayItem() {
		$uid =session('user_auth.uid');
		$mobile = session('mobile');
		if (IS_POST) {
			$data = input('post.');
			// var_dump($data);die;
			
			if (isset($data['payPwd']) && isset($data['orderId'])){
				
				$user = db('member')->field('paypassword')->where('mobile',$mobile)->find();

				$ids = db('order_repay')->field('repay_money')->where('order_id',$data['orderId'])->find();
				
				if(md5($data['payPwd'].$mobile) == $user['paypassword']){

					$data['money'] = $ids['repay_money'];

					$data['descr'] = $data['bankcard'];

					money_record($data,$uid,2,1);

					$info = array(
						'true_repay_money' =>$ids['repay_money'],
						'true_repay_time' =>time(),
						'status' =>'1',
						'has_repay' =>'1',
						'descr' => '还款时间为：'.date('Y-m-d H:i:s',time()).'还id为'.$data['orderId'].'的订单',
						'platform_account'=>$data['bankcard']
						);
					db('order_repay')->where('order_id',$data['orderId'])->update($info);
					$resp['code'] = '1';
					$resp['msg'] = '还款申请成功';
				}else{
					$resp['code'] = '0';
					$resp['msg'] = '交易密码错误';
				}
				return json($resp);
			}else{
				$map['o.mid'] =$uid;
				if ($data['type']) {
					$map['d.type'] = $data['type'];
				}
				if (isset($data['status'])) {
					if ($data['status'] != '') {
		    			$map['status'] = $data['status'];
		    		}
				}
				if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];
					$order_repay = db('order_repay')->alias('o')->field('o.*,d.type,d.uid')->join('__ORDER__ d',' d.id = o.order_id')->where($map)->whereTime('repay_time','between',["$endtime","$begintime"])->order('o.status ASC')->select();
				}else{
					$order_repay = db('order_repay')->alias('o')->field('o.*,d.type,d.uid')->join('__ORDER__ d',' d.id = o.order_id')->where($map)->order('o.status ASC')->select();
				}
				// var_dump($map);die;
				foreach ($order_repay as $k => $v) {
					$order_repay[$k]['yewu_realname'] = serch_real($v['uid']);
				}
				// var_dump($order_repay);die;
				if ($order_repay) {
					$resp['code'] = '1';
					$resp['msg'] = '数据正常';
					$resp['data']= $order_repay;
				}else{
					$resp['code'] = '0';
					// $resp['msg'] = '未查到数据';
				}
				return json($resp);
			}
		}else{
			$bankcard =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
			$data = array(
					'info'=>$bankcard,
					'infoStr'=>json_encode($bankcard)
				);
			$this->assign($data);
		}
		return $this->fetch('repayItem');
	}

	public function payItem() {
		$uid =session('user_auth.uid');
		$mobile = session('mobile');
		if (IS_POST) {
			$data = input('post.');
			// var_dump($data);die;
			if (isset($data['payPwd']) && isset($data['payOrder'])) {
				$user = db('member')->field('paypassword')->where('mobile',$mobile)->find();
				if(md5($data['payPwd'].$mobile) == $user['paypassword']){
					//车商只做费用记录
					$fee = db('order')->field('fee')->where('sn',$data['payOrder'])->find();
					// var_dump($fee);die;
					$money = db('dealer')->field('money,lock_money')->where('mobile',$mobile)->find();

					$use_money = $money['money'] - $fee['fee'];

					// echo $use_money;die;

					if ($use_money < 0) {
						$resp['code'] = '0';
						$resp['msg'] = '余额不足，请充值！！！';
					}else{
						// echo $use_money;die;
						$lock_money = $money['lock_money'] + $fee['fee'];
						$datas = array(
							'money'=>$use_money,
							'lock_money' => $lock_money
							);
						// var_dump($data);die;
						$fee['order_id'] = $data['payOrder'];
						db('Dealer')->where('mobile',$mobile)->update($datas);//冻结资金
						$data['money'] = $fee;
						$data['descr'] = '订单编号:'.$data['payOrder'].'支付成功';
						money_record($data, $uid, 5, 0);//资金记录
						//支付完成进行放款中
						$fk_deal = array(
							'finance'=>'2',
							'update_time'=>time()
							);
						$result = db('order')->where('sn',$data['payOrder'])->update($fk_deal);
						if ($result) {
							$resp['code'] = '1';
							$resp['msg'] = '支付订单成功';
						}else{
							$resp['code'] = '0';
							$resp['msg'] = '支付订单已完成';
						}
					}
				}else{
					$resp['code'] = '0';
					$resp['msg'] = '交易密码错误';
				}
				// var_dump($resp);
				// die;
			}else{

				$map['mid'] =$uid;
				if ($data['type']) {
					$map['type'] = $data['type'];
				}
				
				if ($data['status']) {
					if ($data['status'] == '2') {
						$map['finance'] = ['>','2'];
					}else{
						$map['finance'] = $data['status'];
					}
					
				}
				// var_dump($map);die;
				if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];
					$order_pay = db('order')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->order('finance ASC')->select();
				}else{
					$order_pay = db('order')->where($map)->order('finance ASC')->select();
				}
				// $order_pay = db('order')->where($map)->order('status ASC')->select();
				// var_dump($order_pay);die;
				foreach ($order_pay as $k => $v) {
					$order_pay[$k]['realname'] = serch_real($v['uid']);
				}
				if ($order_pay) {
					$resp['code'] = '1';
					$resp['msg'] = '数据正常';
					$resp['data']= $order_pay;
				}else{
					$resp['code'] = '0';
					// $resp['msg'] = '未查到数据';
				}
			}
			return json($resp);
		}else{
			$money = db('dealer')->field('money')->where('mobile',$mobile)->find();
			$data =array(
					'info'=>$money,
					'infoStr'=>json_encode($money)
				);
			$this->assign($data);
		}
		return $this->fetch('payItem');
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