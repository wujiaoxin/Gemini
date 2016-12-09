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

class Order extends Base {
	public function _initialize() {
		parent::_initialize();
		if (!is_login()) {
			$this->redirect('mobile/user/login');exit();
		}elseif (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}

	public function index() {
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and status > -1';
		}else{
			return $this->error('请重新登录');
		}

		$order = "id desc";
		$list  = db('Order')->where($map)->order($order)->paginate(10);
		$data = array(
			'list' => $list,
			//'page' => $list->render(),
		);
		$this->assign($data);
		return $this->fetch();
	}
	
	
	public function getOrderList($status = null) {
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.')';
		}else{
			$resp['code'] = 0;
			$resp['msg'] = '请重新登录';
			return json($resp);
		}
		if($status == null){
			$map = $map.' and status > -1';
		}else{
			$map = $map.' and status ='.(int)$status;
		}
		
		$order = "id desc";
		$list  = db('Order')->where($map)->order($order)->paginate(6);
		
		$resp['code'] = 1;
		$resp['msg'] = 'OK';
		$resp['data'] = $list;
		return json($resp);
	}
	
	public function carinfo() {
		//$this->assign($user);		
		return $this->fetch();
	}
	
	public function progress() {
		//$this->assign($user);		
		return $this->fetch();
	}
	
	
	//添加
	public function add() {
		$role = session('user_auth.role');
		if($role == 2){//银行人员
			return $this->redirect('mobile/order/index');	
		}		
		if (IS_POST) {
			$resp['code'] = 0;
			$resp['msg'] = '未知错误';
			$data = input('post.');
			$uid = session('user_auth.uid');
			$link = model('Order');
			$data['sn'] = $link->build_order_sn();
			if($uid > 0){
				$data['uid'] = $uid;
			}
			if ($data) {
				unset($data['id']);
				$result = $link->save($data);
				if ($result) {
					$resp['code'] = 1;
					$resp['msg'] = '新建成功！';
					if(isset($data['bank_uid'])){
						$msg = 'Boss,VP贷有单子来了,请您登录审批';
						$this->notifiedUserbySMS($data['bank_uid'], $msg);
					}
					
				} else {
					$resp['msg'] = $link->getError();
				}
			} else {
				$resp['msg'] = $link->getError();
			}
			return json($resp);
		} else {
			$this->assign('title', '新建订单');
			return $this->fetch('edit');
		}
	}
	//查看
	public function view() {
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and id = '.$id.' and status > -1';
		}else{
			return $this->error('请重新登录');
		}

		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('Order/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$info = db('Order')->where($map)->find();
			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
				'role'    => $role,
			);
			$this->assign($data);			
			if($role == 2){//银行审核
				return $this->fetch('examine');				
			}else{
				return $this->fetch();
			}
			
		}
	}
	
	//审核API
	public function examine() {
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
		if($id == ''){
			return $this->error("缺少参数");
		}
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(bank_uid = '.$uid.') and id = '.$id;
		}else{
			return $this->error('请重新登录');
		}
		if (IS_POST) {
			$data = input('post.');
			$saveData['id'] = $id;
			if ($data && isset($data['status'])) {				
				if($data['status'] != 1 && $data['status'] != 2 ){
					return $this->error("非法参数");
				}				
				$saveData['status'] = $data['status'];				
				if($data['status'] == 1 && isset($data['addr'])){//审核成功
					if($data['addr'] == 2){
						$saveData['addr'] = '银行柜台';
					}else{
						$saveData['addr'] = '车商门店';					
					}
					$saveData['update_time'] = time();				
				}
				$result = $link->where($map)->update($saveData);
				//dump($result);
				//$result = $link->save($saveData, array('id' => $saveData['id']));
				if ($result) {
					$realData = $link->where($map)->find();	
					$msg = '';				
					if($realData['status'] == 1){//审核通过
						$OrderExtend = model('OrderExtend');
						$OrderExtend->addByBank($id);
						$msg = '壕，您的客户'.$realData['name'].'信息已审核通过，请您尽快登录查看';					
					}else if($realData['status'] == 2){ //审核拒绝
						$msg = '壕，您的客户'.$realData['name'].'信息审核未通过，具体原因请您尽快登录查看';	
					}
					if(isset($realData['uid']) && $msg!= ''){
						$this->notifiedUserbySMS($realData['uid'], $msg);
					}
					return $this->success("提交成功！");
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {

		}
	}
	
	
	//撤销订单
	public function cancel() {
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
		if($id == ''){
			return $this->error("缺少参数");
		}
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(uid = '.$uid.') and id = '.$id;
		}else{
			return $this->error('请重新登录');
		}
		if (IS_POST) {
			$data = $link->where($map)->find();
			$saveData['id'] = $id;
			if ($data) {				
				if($data['status'] != 0){
					return $this->error("非法参数");
				}				
				$saveData['status'] = -1;
				$result = $link->where($map)->update($saveData);
				if ($result) {
					return $this->success("提交成功！");
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {

		}
	}
	
	//首付资料等扩展信息
	public function extend() {
		$OrderExtend = model('OrderExtend');
		$id   = input('id', '', 'trim,intval');
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and id = '.$id;
		}else{
			return $this->error('请重新登录');
		}

		if (IS_POST) {
			$data = input('post.');
			$data['uid'] = $uid;
			if ($data) {
				$result = $OrderExtend->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('Order/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($OrderExtend->getError());
			}
		} else {
			$info = db('OrderExtend')->where($map)->find();
			$data = array(
				'keyList' => $OrderExtend->keyList,
				'info'    => $info,
				'role'    => $role,
			);
			$this->assign($data);
			return $this->fetch('extend');
		}
	}
	
	protected function notifiedUserbySMS($uid, $msg){
		
		$user = db('Member')->find($uid);
		if( isset( $user['mobile'] )){
			$this->sendSms($user['mobile'], $msg);
		}else{
			return false;
		}		
		return true;

    }
	
	
}
