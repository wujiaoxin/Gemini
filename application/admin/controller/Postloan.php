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

class Postloan extends Admin {

	public function repayment() {

		$repay = model('Repay');
		$order = model('Order');

		$result = $repay->select();

		foreach ($result as $k => $v) {

			$sercher = serch_name($v['dealer_id']);
			
			$order_sn = $order->get_sn($v['order_id']);
			
			$result[$k]['dealer_name'] = $sercher['dealer_name'];

			$result[$k]['sn'] = $order_sn['sn'];

		}
		$data = array(

			'infoStr' => json_encode($result)
		);

		$this->assign($data);

		$this->setMeta('回款审核');
		return $this->fetch();
	}


	public function withhold() {
		$this->setMeta('代扣审核');
		return $this->fetch();
	}

	public function view() {
		$this->setMeta('审核查看');
		return $this->fetch();
	}

}