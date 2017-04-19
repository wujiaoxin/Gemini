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

class Finance extends Admin {

	public function index() {
		
		return $this->fetch();
	}

	// 支付审核
	public function payment() {
		$result = db('order')->where('status','1')->order('finance DESC')->select();
		// var_dump($result);die;
		$data = array(
				'infoStr' => json_encode($result)
			);
		$this->assign($data);
		return $this->fetch();
	}

	// 放款审核
	public function loan() {
		if (IS_POST) {
			$data = input('post.');
			/*$order = model('order');
			$result = $order->save();
			if ($result) {
				$resp['code'] = 1;
				$resp['msg'] = 'OK';
			}else{
				$resp['code'] = 1;
				$resp['msg'] = '放款审核失败!';
			}*/
		}else{
			$result = db('order')->where('finance','3')->select();
			foreach ($result as $k => $v) {
				$result[$k]['dealer_name'] = serch_name($v['mid']);//渠道名称
			}
			// var_dump($result);die;
			$data = array(
				'infoStr' => json_encode($result)
			);
			$this->assign($data);
		}
		return $this->fetch();
	}

	// 充值审核
	public function recharge() {
		if (IS_POST) {
			$data = input('post.');
			/*$order = model('order');
			$result = $order->save();
			if ($result) {
				$resp['code'] = 1;
				$resp['msg'] = 'OK';
			}else{
				$resp['code'] = 1;
				$resp['msg'] = '充值审核失败!';
			}*/
		}else{
			$result = db('payment')->order('create_time DESC,is_pay ASC')->select();
			foreach ($result as $k => $v) {
				$result[$k]['dealer_name'] = serch_name($v['user_id']);
			}
			// var_dump($result);die;
			$data = array(
				'infoStr' => json_encode($result)
			);
			$this->assign($data);
		}
		return $this->fetch();
	}

	// 提现审核
	public function withdraw() {
		if (IS_POST) {
			$data = input('post.');
			$order = model('order');
			$result = $order->save();
			if ($result) {
				$resp['code'] = 1;
				$resp['msg'] = 'OK';
			}else{
				$resp['code'] = 1;
				$resp['msg'] = '提现审核失败!';
			}
		}else{
			$result = db('carry')->select();
			foreach ($result as $k => $v) {
				// $resul = serch_bank($v['user_id']);
				// $result[$k]['bank_account_name'] = $resul[''];
			}
			// var_dump($result);die;
			$data = array(
				'infoStr' => json_encode($result)
			);
			$this->assign($data);

		}
		return $this->fetch();
	}
	
	// 回款审核
	public function receivable() {
		if (IS_POST) {
			$data = input('post.');
			$order = model('order');
			$result = $order->save();
			if ($result) {
				$resp['code'] = 1;
				$resp['msg'] = 'OK';
			}else{
				$resp['code'] = 1;
				$resp['msg'] = '回款审核失败!';
			}
		}else{

		}
		return $this->fetch();
	}

	// 平台资金记录
	public function transaction() {
		if (IS_POST) {
			$data = input('post.');
			$order = model('order');
			$result = $order->save();
			if ($result) {
				$resp['code'] = 1;
				$resp['msg'] = 'OK';
			}else{
				$resp['code'] = 1;
				$resp['msg'] = '资金记录查询失败!';
			}
		}else{

		}
		return $this->fetch();
	}

}