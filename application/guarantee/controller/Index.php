<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\guarantee\controller;
use app\guarantee\controller\Baseness;

class Index extends Baseness {
	public function _initialize(){
		$uid = session("user_auth.uid");
		$mobile =session('business_mobile');
		if($uid == null){
			return $this->redirect(url("/guarantee/login/login"));
		}
		$is_success = db('Dealer')->field('priv_bank_name')->where('mobile',$mobile)->find();
		if(!$is_success['priv_bank_name']){
			$this->redirect('/guarantee/user/guide');
		}
		$result = db('Dealer')->field('status')->where('mobile',$mobile)->find();
		if ($result['status'] != '1') {
			return $this->redirect('/guarantee/login/waiting');
		}
	}
	public function index() {
		$mobile = session('business_mobile');
		$uid = session('user_auth.uid');
		$where = 'status in(0,1,3,4,5) and mid = '.$uid;//借款项目
       	$order_loan = model('order')->where($where)->limit(5)->order('status ASC,id DESC')->select();
		$order_repay = get_orders($mobile,'0','order_repay');//还款项目
		$order_pay = db('dealer_money')->where('uid',$uid)->order('id DESC')->limit(5)->select();;//交易记录
		$money = get_money($uid,'money');//资金
		$lines = db('dealer')->field('lines,lines_ky,name')->where('mobile',$mobile)->find();
		$info = array(
			'order_loan'=>$order_loan,
			'money'=>$money,
			'lines'=>$lines,
			'order_repay'=>$order_repay,
			'order_pay'=>$order_pay,
			);
		$data = array(
				'info'=>$info,
				'infoStr'=>json_encode($info)
			);
		$this->assign($data);
		return $this->fetch();
	}
}