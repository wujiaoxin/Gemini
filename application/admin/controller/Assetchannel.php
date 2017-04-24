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

class assetChannel extends Admin {

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
			'infoStr' =>json_encode($list) ,
		);
		$this->assign($data);
		$this->setMeta("资产渠道");
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
			$this->setMeta("录入新车商");
			return $this->fetch('edit');
		}
	}

	//修改
	public function edit() {
		$link = model('Dealer');
		$id   = input('id', '', 'trim,intval');
		if (IS_POST) {
			$data = input('post.');
			if ($data) {
				//$data['status'] = 1;
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
				'infoStr' => json_encode($info),
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

	// 新增员工
	public function addStaff(){
		// var_dump($GLOBALS);die;
		if (IS_POST){
			$data = input('post.');
			// var_dump($data);die;
			if ($data) {
				// $invit = db('Dealer')->alias('d')->field('d.invite_code')->join('__MEMBER__ m','m.mobile = d.mobile')->where('uid',$data['id'])->find();
				$invit =db('Dealer')->field('invite_code')->where('id',$data['id'])->find();
				// var_dump($invit);die;
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
		}else {
			$this->setMeta("新增员工");
			return $this->fetch();
		}
	}
}