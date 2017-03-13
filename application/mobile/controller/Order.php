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
/*
		$order = "id desc";
		$list  = db('Order')->where($map)->order($order)->paginate(10);
		$data = array(
			'list' => $list,
			//'page' => $list->render(),
		);
		$this->assign($data);
*/
		return $this->fetch();
	}
	

	
	public function getOrderList($status = null, $type = null) {
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
		if($role == 1){
			if($status == null){
				$map = $map.' and status > -1';
			}else{
				$map = $map.' and status ='.(int)$status;
			}
			if($type == '3'){
				$map = $map.' and type = 3';
			}else{
				$map = $map.' and type < 3';
			}
			
			$sort = "id desc";
			$list  = db('Order')->where($map)->order($sort)->paginate(15);
			
			$resp['code'] = 1;
			$resp['msg'] = 'OK';
			$resp['data'] = $list;
			return json($resp);
		}else{
			$Order = model('Order');
			$list = $Order->get_order_list($uid, $role, $type, $status);
			$resp['code'] = 1;
			$resp['msg'] = 'OK';
			$resp['data'] = $list;
			return json($resp);
		}
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
		} else {//TODO:获取报单人信息筛选合作银行
			$bankList = db('Member')->field('uid,nickname,mobile,addr')->where('access_group_id',2)->limit(5)->select();
			$data = array(
				'bankList' =>  $bankList,
			);
			$this->assign($data);
			$this->assign('title', '新建订单');
			return $this->fetch();
		}
	}
	//查看
	public function view() {
		$link = model('Order');
		$id   = input('id', '', 'trim,intval');
		$type   = input('type', '', 'trim,intval');
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.') and id = '.$id.' and status > -1';
		}else{
			return $this->error('请重新登录');
		}
		if($role==1){
			$info = db('Order')->where($map)->find();
		}else{
			$authfilter['order_id'] = $id;
			$authfilter['auth_uid'] = $uid;
			$authfilter['auth_role'] = $role;			
			$auth = db('OrderAuth')->where($authfilter)->find();
			if($auth != null){
				$info = db('Order')->where("id",$id)->find();
			}
		}
		//$filter['uid'] = $uid;
		$filter['order_id'] = $info['id'];
		$filter['status'] = 1;//有效文件
		$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($filter)->limit(100)->select();
		$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
				'files'   => $files,
				'role'    => $role,
		);
		$this->assign($data);
		if($info["type"] == 3){
			return $this->fetch('viewCarloan');
		}else{
			return $this->fetch('viewBorrow');
		}
		/*if($info["type"] == 3){//车抵贷
			$filter['uid'] = $uid;
			$filter['order_id'] = $info['id'];
			$filter['status'] = 1;//有效文件
			$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($filter)->limit(100)->select();
			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
				'files'   => $files,
				'role'    => $role,
			);
			$this->assign($data);
			return $this->fetch('viewCarloan');
		}else{//垫资订单
			$supplementModle = model('OrderSupplement');
			$supplement = db('OrderSupplement')->where($map)->find();		
			$data = array(
				'keyList' => $link->keyList,
				'info'    => $info,
				'role'    => $role,
				'supplementKeyList' => $supplementModle->keyList,
				'supplementInfo' => $supplement,
			);
			$this->assign($data);
			if($role == 2){//银行审核
					return $this->fetch('examine');
			}else{
				return $this->fetch();
			}
		}*///////注意此处括号
	}
	
	
	/*
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
						if($realData['type'] == 2){//二手车
							$OrderCarInfo = model('OrderCarInfo');
							$OrderCarInfo->addByBank($id);
						}
						$msg = '壕，您的客户'.$realData['name'].'信息已审核通过，请您尽快登录查看';					
					}else if($realData['status'] == 2){ //审核拒绝
						$OrderSupplement = model('OrderSupplement');
						$OrderSupplement->addByBank($id);
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
	*/
	
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
				if($data['status'] != 0 && $data['status'] != -2){
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
	
	//审核拒绝后补充信息
	public function supplement() {
		$OrderSupplement = model('OrderSupplement');
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
				$result = $OrderSupplement->save($data, array('id' => $data['id']));
				if ($result) {
					db('Order')->where($map)->update(['status' => '0']);
					return $this->success("提交成功！", url('Order/index'));
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($OrderSupplement->getError());
			}
		} else {
			$info = db('OrderSupplement')->where($map)->find();
			$data = array(
				'keyList' => $OrderSupplement->keyList,
				'info'    => $info,
				'role'    => $role,
			);
			$this->assign($data);
			return $this->fetch('supplement');
		}
	}
	
	
	//二手车订单车辆信息
	public function carinfo() {
		$OrderCarInfo = model('OrderCarInfo');
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
				$result = $OrderCarInfo->save($data, array('id' => $data['id']));
				if ($result) {
					//db('Order')->where($map)->update(['status' => '0']);
					return $this->success("提交成功！", url('Order/index'));
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($OrderCarInfo->getError());
			}
		} else {
			$info = db('OrderCarInfo')->where($map)->find();
			$data = array(
				'keyList' => $OrderCarInfo->keyList,
				'info'    => $info,
				'role'    => $role,
			);
			$this->assign($data);
			return $this->fetch();
		}
	}
	
	
	//车抵贷
	public function carloan() {
		$uid  = session('user_auth.uid');
		$role = session('user_auth.role');
		if($role != 1){
			return $this->error('没有权限', url('Index/index'));
		}
		$orderModel = model('Order');
		if($uid > 0){
			$map['uid'] = $uid;
			$map['status'] = -2;
			$map['type'] = 3;
		}else{
			return $this->error('请重新登录', url('User/login'));
		}
		if (IS_POST) {
			$data = input('post.');
			$orderAuthModel = model('OrderAuth');
			if ($data) {
				$data['status'] = 0;
				$result = $orderModel->isUpdate(true)->save($data, array('id' => $data['id'], 'uid' => $uid));
				
				if($result){
					//$bankList = db('Member')->field('uid,nickname,mobile,addr')->where('access_group_id',2)->limit(5)->select();
					$examineRole = 3;//VP贷风控经理
					$examineUid = 0;
					$examineUser = db('Member')->field('uid,mobile')->where('access_group_id',$examineRole)->find();//TODO:建立车商管理关系
					if($examineUser !=null){
						$examineUid = $examineUser['uid'];
					}
					$result = $orderAuthModel->addAuth($data['id'],$examineUid,$examineRole);
				}
				
				if ($result) {
					return $this->success("提交成功！", url('Order/index'));
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($orderModel->getError());
			}
		} else {
			$orderData = db('Order')->where($map)->find();
			if($orderData == null){
				$orderData['uid'] = $uid;
				$orderData['type'] = 3;
				$orderData['status'] = -2;
				$orderData['create_time'] = time();
				$orderData['sn'] = $orderModel->build_order_sn();
				$orderData['id'] = db('Order')->insertGetId($orderData);
			}else{
				$filter['uid'] = $uid;
				$filter['order_id'] = $orderData['id'];
				$filter['status'] = 1;//有效文件
				$files = db('OrderFiles')->field('id,path,url,size,create_time,form_key,form_label')->where($filter)->limit(100)->select();
				//$filesLength=count($files);
				//if($filesLength > 0 && $filesLength < 100){//单例不允许超过100张
					foreach($files as $key=>$value) {
						if (!empty($value['form_key'])) {
							$orderData[$value['form_key']] = $value;
						}
					}
				//}
			}			
			$data = array(
				'orderDataStr' => json_encode($orderData),
				'role'         => $role,
			);
			$this->assign($data);
			return $this->fetch();
		}
	}
	
	
	//新版垫资
	public function borrow($type = '1') {
		$uid  = session('user_auth.uid');
		$role = session('user_auth.role');
		if($role != 1){
			return $this->error('没有权限', url('Index/index'));
		}
		$orderModel = model('Order');
		if($uid > 0){
			$map['uid'] = $uid;
			$map['status'] = -2;
			$map['type'] = $type;
		}else{
			return $this->error('请重新登录', url('User/login'));
		}
		if (IS_POST) {
			$data = input('post.');
			$orderAuthModel = model('OrderAuth');
			if ($data) {
				$data['status'] = 0;
				$result = $orderModel->isUpdate(true)->save($data, array('id' => $data['id'], 'uid' => $uid));//TODO:防status偷天换日  id为空判断
				
				if($result){
					//$bankList = db('Member')->field('uid,nickname,mobile,addr')->where('access_group_id',2)->limit(5)->select();
					$examineRole = 3;//VP贷风控经理
					$examineUid = 0;
					$examineUser = db('Member')->field('uid,mobile')->where('access_group_id',$examineRole)->find();//TODO:建立车商管理关系
					if($examineUser !=null){
						$examineUid = $examineUser['uid'];
					}
					$result = $orderAuthModel->addAuth($data['id'],$examineUid,$examineRole);
				}
				if ($result) {
					return $this->success("提交成功！", url('Order/index'));
				} else {
					return $this->error("提交失败！");
				}
			} else {
				return $this->error($orderModel->getError());
			}
		} else {
			$orderData = db('Order')->where($map)->find();
			if($orderData == null){
				$orderData['uid'] = $uid;
				$orderData['type'] = $type;
				$orderData['status'] = -2;
				$orderData['create_time'] = time();
				$orderData['sn'] = $orderModel->build_order_sn();
				$orderData['id'] = db('Order')->insertGetId($orderData);
			}else{
				$filter['uid'] = $uid;
				$filter['order_id'] = $orderData['id'];
				$filter['status'] = 1;//有效文件
				$files = db('OrderFiles')->field('id,path,url,size,create_time,form_key,form_label')->where($filter)->limit(100)->select();
				//$filesLength=count($files);
				//if($filesLength > 0 && $filesLength < 100){//单例不允许超过100张
					foreach($files as $key=>$value) {
						if (!empty($value['form_key'])) {
							$orderData[$value['form_key']] = $value;
						}
					}
				//}
			}
			$bankList = db('Member')->field('uid,nickname,mobile,addr')->where('access_group_id',2)->limit(5)->select();		
			$data = array(
				'orderDataStr' => json_encode($orderData),
				'role'         => $role,
				'bankList' =>  $bankList,
			);
			$this->assign($data);
			return $this->fetch();
		}
	}
	
	
	//审核订单
	public function examine() {		
		$id     = input('id', '', 'trim,intval');
		$status =  input('status', '', 'trim,intval');
		$uid    = session('user_auth.uid');
		$role   = session('user_auth.role');		
		$filter["auth_role"] = $role;
		if($id != ''){
			$filter["order_id"] = $id;			
		}else{
			return $this->error("缺少参数");
		}
		if($uid > 0){
			$filter["auth_uid"] = $uid;
		}else{
			return $this->error('请重新登录');
		}
		if($status != 1 && $status != 2){
			return $this->error("非法参数");
		}
		if (IS_POST) {			
			$auth = db('OrderAuth')->where($filter)->find();
			if($auth == null){
				return $this->error('没有权限审核该订单');
			}
			$orderModel = model('Order');			
			$result = $orderModel->where("id",$id)->update(['status' => $status]);
			if ($result) {
				//TODO:1.短信通知；2.添加权限(财务、资方)；
				return $this->success("提交成功！", url('Order/index'));
			} else {
				return $this->error("提交失败！");
			}
		}
		return $this->error("请使用POST提交");
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
	
	// 注册补充
	public function newOrder() {
		//$this->assign($user);		
		return $this->fetch();
	}
	// 客户授权
	public function customerAuth() {
		//$this->assign($user);		
		return $this->fetch();
	}
	//订单信息提交
	public function orderSupplement() {
		//$this->assign($user);		
		return $this->fetch();
	}
}
