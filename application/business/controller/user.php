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
//			 prinf($data);
			$this->assign($data);
			return $this->fetch();
		}
	}

	public function myStaff() {
		//商家员工
		$uid = session("uid");
		$members = db('member')->alias('m')->join('__DEALER__ d','m.invite_code = d.invite_code')->field("m.uid,m.username,m.mobile,m.reg_time,m.status,m.access_group_id")->select();
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

		//商家员工编号
		/*$uid = session("uid");
		$uid = db('Dealer')->alias('d')->field('id')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
		$id = db('member_info')->where('did',$uid['id'])->field('sum(bid) as id')->find();
		if ($id){
			$userd['id'] = $id['id']+1;
			$this->assign('userd',$userd['id']);
		}else{
			$this->assign('userd','10000');
		}*/
		return $this->fetch();
	}
	/*
	 * 商户新增员工接口
	 * */
	public function addStaff(){
		if (IS_POST){
			$data = input('post.');
			if ($data) {
				$invit = db('Dealer')->alias('d')->field('d.invite_code')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
				$user = model('User');
				$uid = $user->registerByMobile($data['mobile'], $data['password']);
				if ($uid > 0) {
					$userinfo['nickname'] = $data['name'];
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
		return $this->fetch();
	}

	public function repayItem() {
		return $this->fetch();
	}

	public function payItem() {
		return $this->fetch();
	}
}