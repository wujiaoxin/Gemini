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

class Order extends Admin {

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
		$list  = db('Order')->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta("订单管理");
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
			$this->setMeta("新建订单");
			return $this->fetch('public/edit');
		}
	}

	//修改
	public function edit() {
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
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
			$map  = array('id' => $id);
			$info = db('Order')->where($map)->find();

			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
			);
			$this->assign($data);
			$this->setMeta("编辑订单");
			return $this->fetch('public/edit');
		}
	}
	
	/**
	 * 设置审核状态
	 */
	public function status($id, $status) {
		$model = model('Order');

		$map['id'] = $id;
		$result    = $model::where($map)->setField('status', $status);
		if (false !== $result) {
			return $this->success("操作成功！");
		} else {
			return $this->error("操作失败！！");
		}
	}

	//删除
	public function delete() {
		$id = $this->getArrayParam('id');
		if (empty($id)) {
			return $this->error('非法操作！');
		}
		$link = db('Order');

		$map    = array('id' => array('IN', $id));
		$result = $link->where($map)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}