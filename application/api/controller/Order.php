<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use app\common\controller\Base;

class Order extends Base {
	public function _initialize() {
		parent::_initialize();
		if (!is_login()) {
			$data['code'] = 0;
			$data['msg'] = '会话已超时';
			return json($data);exit();
		}elseif (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}

	public function index() {		
		//$this->assign($user);		
		$data['code'] = 1;
		$data['msg'] = 'ok';
		return json($data);
	}
	
	//添加
	public function add() {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
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
					$resp['code'] = 1;
					$resp['msg'] = '新建成功！';
				} else {
					$resp['msg'] = $link->getError();
				}
			} else {
				$resp['msg'] = $link->getError();
			}
		}
		return json($resp);
	}


}
