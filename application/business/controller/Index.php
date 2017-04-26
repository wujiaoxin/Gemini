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
		$uid = session("user_auth.uid");
		$mobile =session('mobile');
		if($uid == null){
			return $this->redirect(url("/business/login/login"));
		}
		$is_success = db('Dealer')->field('priv_bank_name')->where('mobile',$mobile)->find();
		if(!$is_success['priv_bank_name']){
			$this->redirect('/business/user/guide');
		}
		$result = db('Dealer')->field('status')->where('mobile',$mobile)->find();
		if ($result['status'] != '1') {
			return $this->redirect('/business/login/waiting');
		}
		
	}
	public function index() {
		$mobile = session('mobile');
		$uid = session('user_auth.uid');
		$order_loan = get_orders($uid,'0','order');//借款项目
		$order_repay = get_orders($uid,'0','order_repay');//还款项目
		// var_dump($order_repay);die;
		$order_pay = db('dealer_money')->where('uid',$uid)->order('id DESC')->limit(5)->select();;//交易记录
		// var_dump($order_pay);die;
		$money = get_money($uid,'money');//资金
		// var_dump($money);die;
		$lines = db('dealer')->field('lines,lines_ky,name')->where('mobile',$mobile)->find();
		$info = array(
			'order_loan'=>$order_loan,
			'money'=>$money,
			'lines'=>$lines,
			'order_repay'=>$order_repay,
			'order_pay'=>$order_pay,
			);
		// var_dump($info);die;
		$data = array(
				'info'=>$info,
				'infoStr'=>json_encode($info)
			);
		$this->assign($data);
		return $this->fetch();
	}
}