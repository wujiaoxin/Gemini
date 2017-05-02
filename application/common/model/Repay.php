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




}