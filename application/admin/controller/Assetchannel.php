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

class assetchannel extends Admin {

	public function index() {
		//define('IS_ROOT', is_administrator());

		//TODO 需要区分商家推广和商家运营	
		$map = '';
		
		$order = "create_time desc";

		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$map = 'd.status > 0';
		if($uid > 0){
			
			$list  = db('Dealer')->alias('d')->field('d.*,m.uid')->join('__MEMBER__ m','m.mobile = d.mobile','LEFT')->where($map)->order($order)->select();

		}else{
			return $this->error('请重新登录');
		}

		// $list  = db('Dealer')->where($map)->order($order)->paginate(10);
		
		foreach ($list as $k => $v) {
			$list[$k]['qrcode_url'] = 'https://pan.baidu.com/share/qrcode?w=512&h=512&url='.url("/public/wechat/user/register").'?authcode='.$v['invite_code'];
		}
		// var_dump($list);die;
		$result =array(
			'data' =>$list,
			);
		$data = array(
			'infoStr' =>json_encode($result),
		);
		// var_dump($data);die;
		$this->assign($data);
		$this->setMeta("资产渠道");
		return $this->fetch('');
	}

	//添加
	public function add() {
		$link = model('Dealer');
		if (IS_POST) {
			$data = input('post.');
			$uid = session('user_auth.uid');
			if ($data['status'] == '1') {
				$data['lines'] = '1000000';
				$data['b_money'] = '1';
				$data['money_level'] = '1000000';
				$data['lines_ky'] = '1000000';
			}
			if ($data) {
				unset($data['id']);
				$data['invite_code'] = $link->buildInviteCode();
				$passwords = 'vpdai'.substr($data['idno'],12,6);
				//加入担保公司
				if ($data['property'] =='3') {
					$res = model('User')->registeraddStaff($data['mobile'],$passwords,$passwords,false,'18');
				}else{
					$res = model('User')->registeraddStaff($data['mobile'],$passwords,$passwords,false,'7');
				}
				if (!$res) {
					return $this->error("注册失败！！");;
				}
				$result = $link->save($data);
				if ($result) {
					return $this->success("新建成功！", url('assetchannel/index'));
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
				if ($data['status'] == 1) {
					$data['lines'] = '1000000';
					$data['b_money'] = '1';
					$data['money_level'] = '1000000';
					$data['lines_ky'] = '1000000';
				}
				if ($data['property'] =='3') {
					$res =array('access_group_id'=>'18','realname'=>$data['rep'],'idcard'=>$data['idno']);
					model('User')->save($res,array('mobile'=>$data['mobile']));
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
		$result = $link->where($map)->update(['status'=>-1]);

		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}

	// 新增员工
	public function addStaff(){

		if (IS_POST){

			$data = input('post.');
			// var_dump($data);die;
			if ($data) {
				
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
					$userinfo['dealer_id'] = $data['id'];
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
			return $this->fetch('addStaff');
		}
	}
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
}