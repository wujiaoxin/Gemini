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
		if($uid > 0){
			$map = 'uid = '.$uid.' or bank_uid = '.$uid;
		}else{
			return $this->error('请重新登录');
		}
		
		$order = "id desc";
		$list  = db('Order')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		return $this->fetch();
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
		$link = model('Order');
		if (IS_POST) {
			$data = input('post.');
			$uid = session('user_auth.uid');
			if($uid > 0){
				$data['uid'] = $uid;
			}
			if ($data) {
				unset($data['id']);
				$data['sn'] = $link->build_order_sn();
				$result = $link->save($data);
				if ($result) {
					return $this->success("新建成功！", url('Order/index'));
				} else {
					return $this->error($link->getError());
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$data = array(
				'keyList' => $link->keyList,
			);
			$this->assign($data);
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
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and id = '.$id;
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
	public function examine() {//TODO:权限控制&status id过滤
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
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
			if ($data) {
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("提交成功！", url('Order/index'));
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {

			
		}
	}
	
}
