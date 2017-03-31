<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\common\controller\Base;

class User extends Base {
	
	public function login($mobile = '', $password = '') {
		if (IS_POST) {
			$resp["code"] = 0;
			$resp["msg"] = '未知错误！';
			
			if (!$mobile || !$password) {
				$resp["code"] = 0;
				$resp["msg"] = '用户名或者密码不能为空！';
				return json($resp);
			}

			$user = model('User');
			$uid  = $user->login($mobile, $password);

			if ($uid > 0) {
				$resp["code"] = 1;
				$resp["msg"] = '登录成功！';
				session("mobile", $mobile);
				session("uid", $uid);
				return json($resp);
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
			}
		} else {
			return $this->fetch();
		}

	}

	public function guide() {		
		$mobile = session("mobile");
		$uid = session("uid");
		if($mobile == null || $uid == null){
			return $this->error("请重新登录",url("/business/user/login"));
		}
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
			prinf($data);
			$this->assign($data);
			return $this->fetch();
		}
	}

	public function myStaff() {
		//商家员工
		$uid = session("uid");
		$uid = db('Dealer')->alias('d')->field('id')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
//		prinf($uid);
		$members = db('member_info')->where('did',$uid['id'])->select();
		foreach($members as $k => $v){
			$members[$k]['effect'] = '';
			if($v['status'] == '1'){
				$members[$k]['status']= '正常';
			}else{
				$members[$k]['status']= '停用';
			}
			if($v['is_effect'] == '1'){
				$members[$k]['effect']= '启用';
			}else{
				$members[$k]['effect']= '停用';
			}
		}
		$data = array(
				'info'    => $members,
				'infoStr' => json_encode($members),
		);
//		prinf($data);
		$this->assign($data);
		return $this->fetch();
	}

	public function newStaff() {
		//商家添加新用户
		$uid = session("uid");
		$uid = db('Dealer')->alias('d')->field('id')->join('__MEMBER__ m','m.mobile = d.mobile')->find();
		$id = db('member_info')->where('did',$uid['id'])->field('sum(bid) as id')->find();
		if ($id){
			$userd['id'] = $id['id']+1;
			$this->assign('userd',$userd['id']);
		}else{
			$this->assign('userd','10000');
		}
		if (IS_POST){
			$data = input('post.');
			if ($data) {
				
			}

//			db('user')->insert($data);
		}
		return $this->fetch();
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