<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Dealer extends Admin {

	public function index() {
		//define('IS_ROOT', is_administrator());		
		$map = '';
		if (!IS_ROOT) {
			$uid = session('user_auth.uid');
			if($uid > 0){
				$map = 'uid = '.$uid.' or bank_uid = '.$uid;
			}else{
				return $this->error('请重新登录');
			}
		}
		$order = "id desc";
		$list  = db('Dealer')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta("车商管理");
		return $this->fetch();
	}

	//添加
	public function add() {
		$link = model('Dealer');
		if (IS_POST) {
			$data = input('post.');
			$uid = session('user_auth.uid');
			if($uid > 0){
				$data['uid'] = $uid;
			}
			if ($data) {
				unset($data['id']);
				$data['invite_code'] = $link->buildInviteCode();
				$result = $link->save($data);
				if ($result) {
					return $this->success("新建成功！", url('Dealer/index'));
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
			$this->setMeta("录入新车商");
			return $this->fetch('public/edit');
		}
	}

	//修改
	public function edit() {
		$link = model('Dealer');
		$id   = input('id', '', 'trim,intval');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('Dealer/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			$map  = array('id' => $id);
			$info = db('Dealer')->where($map)->find();

			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
			);
			$this->assign($data);
			$this->setMeta("编辑车商");
			return $this->fetch('public/edit');
		}
	}

	//查看
	public function view() {
		$link = model('Dealer');
		$id   = input('id', '', 'trim,intval');
		$map = '';
		if (!IS_ROOT) {
			$uid = session('user_auth.uid');
			if($uid > 0){
				$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and id = '.$id;
			}else{
				return $this->error('请重新登录');
			}
		}else{
			$map = 'id = '.$id;
		}
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				$result = $link->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('Dealer/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($link->getError());
			}
		} else {
			//$map  = array('id' => $id);
			$info = db('Dealer')->where($map)->find();

			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
			);
			$this->assign($data);
			$this->setMeta("查看车商信息");
			return $this->fetch();
		}
	}
	


	//删除
	public function delete() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error('非法操作！');
		}
		$link = db('Dealer');
		$map    = array('id' => array('IN', $id));
		$map['status'] = 2;
		$result = $link->where($map)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}