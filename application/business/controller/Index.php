<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\business\controller\Baseness;

class Index extends Baseness {
	public function _initialize(){
		$uid = session("uid");
		$mobile =session('mobile');
		if($uid == null){
			return $this->error("请先登录",url("/business/user/login"));
		}
		$is_success = db('Dealer')->field('priv_bank_name')->where('mobile',$mobile)->find();
		if(!$is_success['priv_bank_name']){
			$this->redirect('/business/user/guide');
		}
	}
	public function index() {
		$mobile = session('mobile');
		$uid = session('uid');
		$order = get_orders($uid,'0','order');//借款项目
		$order_repay = get_orders($uid,'0','order_repay');//还款项目
		$dealer_money = get_orders($uid,'0','dealer_money');//交易记录
		$money = get_money($uid,'money');//资金
		$lines = db('dealer')->field('money,lines,lines_ky')->where('mobile',$mobile)->find();
		$info = array(
			'order'=>$order,
			'money'=>$money,
			'lines'=>$lines,
			'order_repay'=>$order_repay,
			'dealer_money'=>$dealer_money,
			);
		$data = array(
				'info'=>$info,
				'infoStr'=>json_encode($info)
			);
		$this->assign($data);
		return $this->fetch();
	}
}