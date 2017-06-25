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
	/*public function _initialize(){
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
	}*/
	public function index() {
		$role =session('user_auth.role');
		$uid =session('user_auth.uid');
		$mobile =session('business_mobile');
		if ($role != '18') {
			$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
			$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
			$uid = $res['uid'];
			$lines = db('Dealer')->alias('d')->field('lines,lines_ky,name')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
		}
		//借款项目
		$map = array(
				'd.guarantee_id'=>$uid
			);
		$order_loan = db('Order')->alias('o')->field('o.*')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->limit(5)->select();
		//还款项目

		$order_repay = db('order_repay')->alias('r')->alias('o')->field('o.*')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->where($map)->limit(5)->select();
		//交易记录

		$order_pay = db('dealer_money')->alias('r')->alias('o')->field('o.*')->join('__DEALER__ d','o.uid = d.id','LEFT')->join('__MEMBER__ m','m.dealer_id = d.id','LEFT')->where($map)->limit(5)->select();
		
		//担保机构额度
		
		$lines = db('Dealer')->alias('d')->field('lines,lines_ky,name')->where('mobile',$mobile)->find();



		//借款详情
		/*$types = '2,4';
        $map = array(
          'mid'=>$uid,
          'finance'=>'3',
          'type'=>array('IN',$types)
          );
        $available_money = db('order')->where($map)->sum('examine_limit');

		 $money = array(
          'available_money'=>$available_money,
          'loan_money'=>$money_jk,
          'repay_money'=>(string)$repay_money,
          'order_loan_num'=>$order_loan,
          'order_repay_num'=>$order_repay,
          );*/

		//资金
		$info = array(
			'order_loan'=>$order_loan,
			// 'money'=>$money,
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