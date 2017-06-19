<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\guarantee\controller;
use app\guarantee\controller\Baseness;
 class User extends Baseness {
	public function myStaff() {
		//担保公司员工
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if ($role == 13) {
			$mobile = db('dealer')->alias('d')->field('d.mobile')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
			$uids = db('member')->field('uid')->where('mobile',$mobile['mobile'])->find();
			$uid = $uids['uid'];
		}
		$result = db('member')->alias('m')->join('__DEALER__ d','m.mobile = d.mobile')->field('d.id')->where('m.uid',$uid)->order('id DESC')->find();
		$members = db('member')->where('dealer_id',$result['id'])->select();
		$data = array(
				'info'    => $members,
				'infoStr' => json_encode($members),
		);
		$this->assign($data);
		return $this->fetch('myStaff');
	}
	/*
	 * 担保公司操作员工是否有效
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
		$role = session('user_auth.role');
		if ($role == 13) {
			$this->error('没有权限新增员工');
		}
		return $this->fetch('newStaff');
	}
	/*
	 * 商户新增员工接口
	 * */
	public function addStaff(){
		if (IS_POST){

			$data = input('post.');
			if ($data) {
				$uid = session('user_auth.uid');
				$role = session('user_auth.role');
				if ($role == '18') {
					$invit = db('Dealer')->alias('d')->field('d.id')->join('__MEMBER__ m','m.mobile = d.mobile')->where('m.uid',$uid)->find();
				}else{
					$invit = db('Dealer')->alias('d')->field('d.id')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
				}
				
				$user = model('User');
				//创建注册用户
				$uid = $user->registeraddStaff($data['mobile'], $data['password'], $data['password'],NULL, false);
				if ($uid > 0) {
					$userinfo['realname'] = $data['name'];
					$userinfo['nickname'] = $data['name'];
					$userinfo['username'] = $data['mobile'];
					$userinfo['status'] = 1;
					$userinfo['access_group_id'] = $data['job'];
					$userinfo['desc'] = $data['remark'];
					$userinfo['tel'] = $data['telphone'];
					$userinfo['reg_time'] = time();
					$userinfo['last_login_ip'] = get_client_ip(1);
					$userinfo['dealer_id'] = $invit['id'];
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
		$role =session('user_auth.role');
		$mobile = session('business_mobile');
		if ($role != 18) {
			$mobile = db('dealer')->alias('d')->field('d.mobile')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
			$uids = db('member')->field('uid')->where('mobile',$mobile['mobile'])->find();
			$uid = $uids['uid'];
			$mobile = $mobile['mobile'];
		}
		$where = array(

			'o.mid' => $uid,

			'o.status'=>1

			);
		//分组统计
		$result = db('order')->field('o.mid,o.uid,sum(o.examine_limit) as result,m.realname')->alias('o')->join('__MEMBER__ m','o.uid = m.uid','LEFT')->where($where)->order('result DESC')->group('o.uid')->limit(5)->select();
		
		$num = db('order')->field('o.mid,o.uid,count(o.id) as result,m.realname')->alias('o')->join('__MEMBER__ m','o.uid = m.uid','LEFT')->where($where)->order('result DESC')->group('o.uid')->limit(5)->select();
		$avg = db('order')->field('o.mid,o.uid,avg(o.examine_limit) as result,m.realname')->alias('o')->join('__MEMBER__ m','o.uid = m.uid','LEFT')->where($where)->order('result DESC')->group('o.uid')->limit(5)->select();
		$forms = db('Dealer')->field('guarantee_id')->where('mobile',$mobile)->find();

		if (IS_POST) {
			$data = input('post.');
			//一个月内每天的订单数量
			$res = $data['term']/30;
			$begin = date('Y-m-d',strtotime("-{$res} month"));//30天前
	        $begin =strtotime($begin);
	        $end =strtotime(date('Y-m-d'))+86399;
	        $map['create_time'] = array(array('gt',$begin),array('lt',$end));
	        $map['mid'] =$uid;
	        $map['status'] = '1';
	        $res =db('order')->field("COUNT(*) as tnum,sum(examine_limit) as total_money, FROM_UNIXTIME(create_time,'%Y-%m-%d') as time")->where($map)->group('time')->select();
	        $tnum =0;
	        $tamount=0;
	        foreach ($res as $val){
	            $arr[$val['time']] = $val['tnum'];
	            $brr[$val['time']] = $val['total_money'];
	            $tnum += $val['tnum'];
	            $tamount += $val['total_money'];
	        }
	        
	        for($i=$begin;$i<=$end;$i=$i+24*3600){
	            $tmp_num = empty($arr[date('Y-m-d',$i)]) ? 0 : $arr[date('Y-m-d',$i)];
	            $tmp_amount = empty($brr[date('Y-m-d',$i)]) ? 0 : $brr[date('Y-m-d',$i)];               
	            $date = date('Y-m-d',$i);
	            $list[] = array('time'=>$date,'num'=>$tmp_num,'total_money'=>$tmp_amount);
	        }
	        $resp['code'] = '1';
	        $resp['data'] = $list;
	        return json($resp);
		}else{
			$list = (empty($list)) ? '' : $list;
			$info = array(
					'money'=>$result,
					'num'=>$num,
					'avg'=>$avg,
					'guarantee_id'=>$forms['guarantee_id'],
				);
			$data = array(
					'info'=>$info,
					'infoStr'=>json_encode($info)
				);

			$this->assign($data);
			return $this->fetch('myShop');
		}
		
	}

	/*public function myShop() {
		$uid =session('user_auth.uid');
		$role =session('user_auth.role');
		$mobile = session('business_mobile');
		if ($role != 18) {
			$mobile = db('dealer')->alias('d')->field('d.mobile')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
			$uids = db('member')->field('uid')->where('mobile',$mobile['mobile'])->find();
			$uid = $uids['uid'];
		}
		$where = array(

			'mid' => $uid,

			'status'=>1

			);

		//分组统计
		$result = db('order')->field('mid,uid,sum(examine_limit) as result')->where($where)->order('result DESC')->group('uid')->limit(5)->select();
		foreach ($result as $k => $v) {
			$result[$k]['realname'] = serch_real($v['uid']);
		}
		$num = db('order')->field('mid,uid,count(id) as result')->where($where)->order('result DESC')->group('uid')->limit(5)->select();
		foreach ($num as $k => $v) {
			$num[$k]['realname'] = serch_real($v['uid']);
		}
		$avg = db('order')->field('mid,uid,avg(examine_limit) as result')->where($where)->order('result DESC')->group('uid')->limit(5)->select();
		foreach ($num as $k => $v) {
			$avg[$k]['realname'] = serch_real($v['uid']);
		}
		//一个月内每天的订单数量
		$begin = date('Y-m-d',strtotime("-1 month"));//30天前
        $begin =strtotime($begin);
        $end =strtotime(date('Y-m-d'))+86399;
        $map['create_time'] = array(array('gt',$begin),array('lt',$end));
        $map['mid'] =$uid;
        $map['status'] = '1';
        $res =db('order')->field("COUNT(*) as tnum,sum(examine_limit) as total_money, FROM_UNIXTIME(create_time,'%Y-%m-%d') as time")->where($map)->group('time')->select();
        $tnum =0;
        $tamount=0;
        foreach ($res as $val){
            $arr[$val['time']] = $val['tnum'];
            $brr[$val['time']] = $val['total_money'];
            $tnum += $val['tnum'];
            $tamount += $val['total_money'];
        }

        for($i=$begin;$i<=$end;$i=$i+24*3600){
            $tmp_num = empty($arr[date('Y-m-d',$i)]) ? 0 : $arr[date('Y-m-d',$i)];
            $tmp_amount = empty($brr[date('Y-m-d',$i)]) ? 0 : $brr[date('Y-m-d',$i)];
            $date = date('Y-m-d',$i);
            $list[] = array('time'=>$date,'num'=>$tmp_num,'total_money'=>$tmp_amount);

        }
		$info = array(
				'money'=>$result,
				'num'=>$num,
				'avg'=>$avg,
				'time'=>$list
			);
		$data = array(
				'info'=>$info,
				'infoStr'=>json_encode($info)
			);
		$this->assign($data);
		return $this->fetch('myShop');
	}*/

	public function loanItem() {
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if (IS_POST) {
			$data = input('post.');
			if ($role != '18') {
				$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
				$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
				$uid = $res['uid'];
			}
			// $resl = db('Dealer')->field('id')->where('guarantee_id',$uid)->select();
			/*$arr = array();
			foreach ($resl as $k => $v) {
				$ids = db('member')->field('uid')->where('dealer_id',$v['id'])->find();
				$arr[] = $ids['uid'];
			}
			$list = array();*/
			
			$map = array('d.guarantee_id'=>$uid);
			if ($data['type']) {
				$map['o.type'] = $data['type'];
			}
			/*if (isset($data['status'])) {
				if ($data['status'] != '') {
	    			$map['status'] = $data['status'];
	    		}
			}*/
			$map['o.credit_status'] = '3';
			if ($data['dateRange']) {
				$result = to_datetime($data['dateRange']);
				$endtime =$result['endtime'];
				$begintime = $result['begintime'];

				$result = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->whereTime('o.create_time','between',["$endtime","$begintime"])->order('create_time DESC')->select();
			}else{
				$result = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname as salesman')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();
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
		$role =session('user_auth.role');
		$mobile = session('business_mobile');

		if (IS_POST) {

			$data = input('post.');
			$dealer_id = db('Dealer')->field('id,forms')->where('mobile',$mobile)->find();
			if (isset($data['payPwd']) && isset($data['orderId'])){

				$user = db('member')->field('paypassword')->where('mobile',$mobile)->find();

				if (empty($user['paypassword'])) {
					$resp['code'] = '2';
					$resp['msg'] = '未设置交易密码';
					return json($resp);
				}

				$ids = db('order_repay')->field('repay_money,status')->where('order_id',$data['orderId'])->find();

				if ($ids['status'] == '-2') {

					$resp['code'] = '2';

					$resp['msg'] = '还款申请已提交！';

					return json($resp);

				}
				if(md5($data['payPwd'].$mobile) == $user['paypassword']){

					$data['money'] = $ids['repay_money'];

					$data['descr'] = '';

					money_record($data,$uid,2,0);

					$bank_info = model('Bank')->get_bank($data['bankcard']);

					$info = array(
						'true_repay_money' =>$ids['repay_money'],
						'true_repay_time' =>time(),
						'status' =>'-2',
						'has_repay' =>'-2',
						'dealer_bank_account'=>$data['bankcard'],
						'dealer_bank'=>$bank_info['dealer_bank'],
						'dealer_bank_branch'=>$bank_info['dealer_bank_branch']
						);
					db('order_repay')->where('order_id',$data['orderId'])->update($info);
					$resp['code'] = '1';
					$resp['msg'] = '还款申请成功';
					return json($resp);
				}else{
					$resp['code'] = '0';
					$resp['msg'] = '交易密码错误';
				}
				return json($resp);
			}else{
				if ($role != '18') {
					$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
					$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
					$uid = $res['uid'];
				}
				// $resl = db('Dealer')->field('id')->where('guarantee_id',$uid)->select();
				// $map['o.dealer_id'] =$dealer_id['id'];
				if ($data['type']) {
					$map['d.type'] = $data['type'];
				}
				/*if ($dealer_id['forms'] == '1' || $dealer_id['forms'] == '3') {
					$map['d.type'] = '';
				}*/
				if (isset($data['status'])) {
					if ($data['status'] != '') {
		    			$map['o.status'] = $data['status'];
		    		}
				}
				$map = array('d.guarantee_id'=>$uid);
				if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];


					$order_repay = db('order_repay')->alias('o')->field('o.*,d.name as dealername,m.realname as yewu_realname')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->whereTime('o.create_time','between',["$endtime","$begintime"])->order('create_time DESC')->select();
					
				}else{
					$order_repay = db('order_repay')->alias('o')->field('o.*,d.name as dealername,m.realname as yewu_realname')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->order('create_time DESC')->select();
					
				}
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

	/*public function payItem() {

		$uid =session('user_auth.uid');

		$mobile = session('business_mobile');

		if (IS_POST) {

			$data = input('post.');

			if (isset($data['payPwd']) && isset($data['payOrder'])) {

				$user = db('member')->field('paypassword')->where('mobile',$mobile)->find();
				if (empty($user['paypassword'])) {
					$resp['code'] = '2';
					$resp['msg'] = '未设置交易密码';
					return json($resp);
				}
				if(md5($data['payPwd'].$mobile) == $user['paypassword']){

					//车商只做费用记录
					$fee = db('order')->field('fee,status')->where('sn',$data['payOrder'])->find();

					if ($fee['status'] == '2') {

						$resp['code'] = '0';
						$resp['msg'] = '重复支付';

					}
					$money = db('dealer')->field('money,lock_money')->where('mobile',$mobile)->find();

					$use_money = $money['money'] - $fee['fee'];

					if ($use_money < 0) {

						$resp['code'] = '0';

						$resp['msg'] = '余额不足，请充值！！！';
					}else{

						$lock_money = $money['lock_money'] + $fee['fee'];

						$datas = array(
							'money'=>$use_money,
							'lock_money' => $lock_money
							);

						db('Dealer')->where('mobile',$mobile)->update($datas);//冻结资金
						$data['money'] = $fee['fee'];
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
			}else{

				$map['mid'] = $uid;
				$map['status'] = '1';
				if ($data['type']) {
					$map['type'] = $data['type'];
				}
				$dealer_id = db('Dealer')->field('forms')->where('mobile',$mobile)->find();
				if ($dealer_id['forms'] == '1' || $dealer_id['forms'] == '3') {
					$map['type'] = '';
				}
				if ($data['status']) {
					if ($data['status'] == '2') {
						$map['finance'] = ['>','2'];
					}else{
						$map['finance'] = $data['status'];
					}
				}
				if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];
					$order_pay = db('order')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->order('finance ASC')->select();
				}else{
					$order_pay = db('order')->where($map)->order('finance ASC')->select();
				}
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
	}*/

	//设置交易密码
	public function setpay(){
		$mobile = session("business_mobile");
		$role = session('user_auth.role');
		if ($role == 13) {
			$this->error('没有权限设置交易密码');
		}
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
		$role = session('user_auth.role');
		if ($role == 13) {
			$this->error('没有权限修改交易密码');
		}
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
		//if(sendSms($mobile,$smsMsg)){
		if(sendSmsCode($mobile,$smsCode)){
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
	//修改账户密码
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

	public function myChannel() {
		//担保公司员工
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if ($role != '18') {
			$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
			$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
			$uid = $res['uid'];
		}
		$list = db('dealer')->where('guarantee_id',$uid)->select();
		$result =array(
			'data' =>$list,
			);
		$data = array(
				'info'    => $result,
				'infoStr' => json_encode($result),
		);
		$this->assign($data);
		return $this->fetch('myChannel');
	}

	//添加
	public function newChannel() {
		$role = session('user_auth.role');
		if ($role == 13) {
			$this->error('没有权限新增渠道');
		}
		if ($role == 17) {
			
		}
		$link = model('Dealer');
		if (IS_POST) {
			$data = input('post.');
			// var_dump($data);die;
			$uid = session('user_auth.uid');
			if ($data['status'] == '1') {
				$data['lines'] = '1000000';
				$data['b_money'] = '1';
				$data['money_level'] = '1000000';
				$data['lines_ky'] = '1000000';
			}
			$data['guarantee_id'] = $uid;//担保公司id  TODO
			if ($data) {
				unset($data['id']);
				$data['invite_code'] = $link->buildInviteCode();
				$passwords = 'vpdai'.substr($data['idno'],12,6);
				model('User')->registeraddStaff($data['mobile'],$passwords,$passwords,false,'7');
				$result = $link->save($data);
				if ($result) {
					return $this->success("新建成功！", url('guarantee/index'));
				} else {
					return $this->error($link->getError());
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$info['rep_idcard_pic'] = '';
			$info['dealer_lic_pic'] = '';
			$info['rep_idcard_back_pic'] = '';
			$info['info_pic'] = '';
			$data = array(
				'keyList' => $link->keyList,
				'info' => $info,
				'infoStr' => '{}',
			);
			$this->assign($data);
			return $this->fetch('newChannel');
		}
	}

	//修改
	public function editChannel() {
		$link = model('Dealer');
		$id   = input('id', '', 'trim,intval');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				//$data['status'] = 1;
				if ($data['status'] == 1) {
					$data['lines'] = '1000000';
					$data['b_money'] = '1';
					$data['money_level'] = '1000000';
					$data['lines_ky'] = '1000000';
				}
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('assetchannel/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$map  = array('id' => $id);
			$info = db('Dealer')->where($map)->find();
			$sales = db('member')->alias('m')->field('m.*')->join('__DEALER__ d','m.dealer_id = d.id')->where('d.id',$id)->order('id DESC')->select();
			$info['sales'] = $sales;
			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
				'infoStr' => json_encode($info),
			);
			$this->assign($data);
			$this->setMeta("车商审核");
			return $this->fetch();
		}
	}

}