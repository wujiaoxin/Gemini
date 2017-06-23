<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;
	
/**
 * 还款类
 * @author molong <molong@tensent.cn>
 */
class Repay extends \app\common\model\Base {

	protected $name = "Order_repay";

	/*
	**还款审核
	*/

	public function receivable($data){

		$ids = db('order_repay')->field('order_id,status,repay_period,totalperiod,interest_money')->where('id',$data['id'])->order('status ASC, true_repay_time DESC')->find();

		$result = db('order')->field('examine_limit,type')->where('id',$ids['order_id'])->find();
		
		if ($ids['repay_period'] <= $ids['totalperiod']){

			if ($result['type'] == '2' || $result['type'] == '4') {

				$money = db('dealer')->alias('d')->field('d.lines_ky,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->join('order_repay o','d.id = o.dealer_id')->where('o.id',$data['id'])->find();

				$lines_result = $money['lines_ky'] + $result['examine_limit'];//最终可用额度

				$money_result = array(

					'lines_ky' =>$lines_result

				);

				db('dealer')->where('mobile',$money['mobile'])->update($money_result);//改变可用额度
			}

			$wheret = ['id'=>$data['id'],'repay_period'=>$ids['repay_period']];
			$data['has_repay'] = '1';
			$data['true_interest_money'] = $ids['interest_money'];
			$res = $this->save($data,$wheret);

			if ($res) {

				return true;

			}else{

				return false;
			}

		}

	}


	/*
	**还款计划（垫资还款）
	*/
	public function set_repay($order_id){

	    $order = db('order')->where('id',$order_id)->find();

	    $customer = db('member')->where('mobile',$order['mobile'])->find();

	    if ($order) {

	      $repay_time = time()+$order['endtime']*24*60*60;
	      $repay_money  = $order['examine_limit'] + $order['fee'];
	      $order_repay = array(

	          'order_id'=>$order_id,

	          'uid'=>$customer['uid'],

	          'dealer_id'=> $order['dealer_id'],

	          'repay_money'=>$repay_money,

	          'manage_money'=>'0',

	          'repay_time'=>$repay_time,

	          'status'=>'-1',

	          'has_repay'=>'-1',

	          'loantime'=>$order['endtime'],

	          'repay_period'=>'1',

	          'totalperiod'=>'1',

	          'true_repay_money'=>'0',

	          'true_repay_time'=>'0',

	          'product_name'=>repay_type($order['type']),

	          'self_money' =>$order['examine_limit']

	        );
	      $result = $this->save($order_repay);

	      return $result;

	    }
	}


	/*
	**还款计划（等额本息）
	*/

	function make_plan($order_id){

		$deal = db('order')->where('id',$order_id)->find();

		$deal['product_name'] = repay_type($deal['type']);
		

		$totalperiod = floor($deal['endtime']/30);

		$deal['rate'] = get_rate($deal['endtime']);

		$list = array();
		
		$has_use_self_money = 0;
		
		$repay_day = time();

		$uids = db('member')->field('uid')->where('mobile',$deal['mobile'])->find();

		for($i=1; $i <= $totalperiod; $i++){

			$load_repay = array();

			// $load_repay['repay_time'] = time()+30*24*60*60*$i;
			$load_repay['repay_time']  =  $repay_day = next_replay_month ($repay_day);
			// $load_repay['repay_money11'] = date('Y-m-d H:i:s',$load_repay['repay_time']);
			$load_repay['repay_period'] = $i;

			$load_repay['totalperiod'] = intval($totalperiod);

			$load_repay['rate'] = $deal['rate']*100;

			$load_repay['repay_money'] = pl_it_formula($deal['examine_limit'],$deal['rate'],$totalperiod);

			$deal['month_repay_money'] = $load_repay['repay_money'];

			$load_repay['self_money'] = round($deal['examine_limit'] *$deal['rate']*pow((1+$deal['rate']),$i-1)/(pow(($deal['rate']+1),$totalperiod)-1),2);


			$has_use_self_money += $load_repay['self_money'];

			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			
			$load_repay['order_id'] = $deal['id'];

			$load_repay['uid'] = $uids['uid'];

			$load_repay['dealer_id'] = $deal['dealer_id'];

			$load_repay['status'] = -1;

			$load_repay['has_repay'] = -1;

			$load_repay['loantime'] = $deal['endtime'];

			$load_repay['product_name'] = $deal['product_name'];

			$list[] = $load_repay;
		}

		$this->saveAll($list);

		return 1;
	}


	/*
	**还款计划 (等本等息)
	*/
	function  make_interest($order_id){

		$deal = db('order')->alias('o')->field('o.*,p.vp_rate as rate')->join('__PROGRAMME__ p','o.id = p.order_id')->where('id',$order_id)->find();

		$deal['product_name'] = repay_type($deal['type']);

		$totalperiod = floor($deal['endtime']/30);

		//利率
		// $deal['rate'] = get_rate($deal['endtime']);

		$deal['rate'] = $deal['rate']/1000;

		$list = array();
		
		$has_use_self_money = 0;
		
		$repay_day = time();

		$uids = db('member')->field('uid')->where('mobile',$deal['mobile'])->find();

		//放款金额
		$deal['examine_limit'] = $deal['examine_limit'] + $deal['fee'];

		for($i=1; $i <= $totalperiod; $i++){

			$load_repay = array();

			$load_repay['repay_time'] = $repay_day = next_replay_month ($repay_day);
			
			$load_repay['repay_period'] = $i;

			$load_repay['totalperiod'] = intval($totalperiod);

			$load_repay['rate'] = $deal['rate'];

			$load_repay['repay_money'] = round(($deal['examine_limit']*(1+$deal['rate']*$totalperiod)/$totalperiod),2);

			$deal['month_repay_money'] = $load_repay['repay_money'];

			$load_repay['self_money'] = round($deal['examine_limit']/$totalperiod,2);

			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			
			$load_repay['order_id'] = $deal['id'];

			$load_repay['uid'] = $uids['uid'];

			$load_repay['dealer_id'] = $deal['dealer_id'];

			$load_repay['status'] = -1;

			$load_repay['has_repay'] = -1;

			$load_repay['loantime'] = $deal['endtime'];

			$load_repay['product_name'] = $deal['product_name'];

			$list[] = $load_repay;
		}
		$this->saveAll($list);

		return 1;
	}
}