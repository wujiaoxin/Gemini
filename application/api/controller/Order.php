<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use app\common\controller\Api;

class Order extends Api {
	/*public function _initialize() {
		parent::_initialize();
		if (!is_login()) {
			$data['code'] = 0;
			$data['msg'] = '会话已超时';
			return json($data);exit();
		}elseif (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}*/

	public function index() {		
		//$this->assign($user);		
		$resp['code'] = 1;
		$resp['msg'] = 'OrderAPI';
		return json($data);
	}
	
	//添加
	public function add($type = null, $mobile = null, $price = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}
	
	public function getList($status = null, $type = null) {
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		/*if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.')';
		}else{
			$resp['code'] = 0;
			$resp['msg'] = '请重新登录';
			return json($resp);
		}*/
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

	
	public function getQRCode($type = null, $mobile = null, $price = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		$data["url"] = "http://xxx.xxx.com/xxx.png";
		
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功';
		$resp['data'] = $data;
 
		return json($resp);
	}
	
	
	public function save($type = null, $mobile = null, $idcard = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		$data["id"] = "1001";
		
		
		$resp['code'] = 1;
		$resp['msg'] = '保存成功';
		$resp['data'] = $data;
 
		return json($resp);
	}

	
	public function total($type = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		$data["todo"] = "I'm comming";
		
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功!';
		$resp['data'] = $data;
 
		return json($resp);
	}
	
}
