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

class Customer extends Admin {

	public function index($type = 3, $status = null) {
		
		if($status == null){
			$filter['status'] = ['>',-1];
		}else{
			$filter['status'] = $status;
		}
		
		/*if($type == 3){
			$filter['type'] = $type;
		}else{
			$filter['type'] =['<',3];
		}*/
		
		if (!IS_ROOT) {
			$uid = session('user_auth.uid');
			$role = session('user_auth.role');
			if($role==1){//报单人-车商经理
				$filter['uid'] = $uid;
				$list  = db('Order')->where($filter)->order("id desc")->paginate(10);
			}else{
				$Order = model('Order');
				$list = $Order->get_all_order_list($uid, $role, $status);
			}
		}else{
			$list  = db('Order')->where($filter)->order("id desc")->paginate(10);
		}
		
		$data = array(
			'infoStr' =>json_encode($list) ,
		);
		$this->assign($data);
		$this->setMeta("客户管理");
		return $this->fetch();
	}

	//查看
	public function view() {
		
		//checkOrderAuth
		
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$id   = input('id', '', 'trim,intval');
		$info = null;
		
		if($uid == null){
			return $this->error('请重新登录');
		}
		
		$filter['id'] = $id;
		
		if (IS_ROOT) {//管理员
			$info = db('Order')->where($filter)->find();
		}else{
			if($role==1){//报单人-车商经理
				$filter['uid'] = $uid;
				$info = db('Order')->where($filter)->find();
			}else{
				$authfilter['order_id'] = $id;
				$authfilter['auth_uid'] = $uid;
				$authfilter['auth_role'] = $role;			
				$auth = db('OrderAuth')->where($authfilter)->find();
				if($auth != null){
					$info = db('Order')->where("id",$id)->find();
				}
			}

		}
		if($info == null){
			return $this->error('没有查看该订单权限');
		}
		
		//end checkOrderAuth
		$Order = model('Order');
		if (IS_POST) {
			
			$data = input('post.');
			if ($data) {
				$result = $Order->save($data, array('id' => $data['id']));
				if ($result) {
					return $this->success("修改成功！", url('Order/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error($Order->getError());
			}
		} else {			
			$fileFilter['order_id'] = $info['id'];
			$fileFilter['status'] = 1;//有效文件
			$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($fileFilter)->limit(100)->select();
			$data = array(
						'keyList' => $Order->keyList,
						'info'    => $info,
						'files'   => $files,
						'role'    => $role,
			);
			$this->assign($data);
			$this->setMeta("客户管理");
			return $this->fetch();
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
			if($status == 1){//审核通过
				$OrderExtend = model('OrderExtend');
				$OrderExtend->addByBank($id);
			}			
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