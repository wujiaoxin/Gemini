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

	// 支付审核 OK
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

			if (isset($data['status'])) {

				$datas = array(

					'descr' => $data['descr']

				);
				
				if ($data['status']) {

					$money = db('dealer')->alias('d')->field('d.lock_money,d.lines_ky,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->join('__ORDER__ o','m.uid = o.mid')->where('o.id',$data['id'])->find();

					$result = db('order')->field('fee,loan_limit')->where('id',$data['id'])->find();

					if ($money['lock_money'] > $result['fee']) {//判断冻结金额和订单费用
						
						$datas['finance'] = '3';

						$lines_result = $moeny['lines_ky'] - $result['loan_limit'];//最终可用额度

						$lock_money_result = $money['lock_money'] - $result['fee'];//剩余冻结金额

						$moeny_result = array(

							'lock_money' => $lock_money_result,

							'lines_ky' =>$lines_result

							);

						db('dealer')->where('mobile',$money['mobile'])->update($moeny_result);//改变资金流水

						db('order')->where('id',$data['id'])->update($datas);//更新订单状态

						//生成还款计划表
						set_order_repay($data['id']);

						$resp['code'] = 1;

						$resp['msg'] = '放款审核成功!';

					}else{
						$resp['code'] = 0;

						$resp['msg'] = '冻结资金异常!';
					}
					

				}else{

					db('order')->where('id',$data['id'])->update($datas);

					$resp['code'] = 0;

					$resp['msg'] = '放款审核失败!';

				}
				
			}else{

				$result = db('order')->where('id',$data['id'])->find();

				foreach ($result as $k => $v) {

					$sercher = serch_name($v['mid']);
					// var_dump($sercher);die;

					$result[$k]['dealer_name'] = $sercher['dealer_name'];//渠道名称

				}

				// var_dump($result);die;

				$data = array(
					'infoStr' => json_encode($result)
				);

				$this->assign($data);

				}

		}else{

			$result = db('order')->where('finance','2')->select();

			foreach ($result as $k => $v) {

				$sercher = serch_name($v['mid']);
				// var_dump($sercher);die;

				$result[$k]['dealer_name'] = $sercher['dealer_name'];//渠道名称

			}

			// var_dump($result);die;

			$data = array(
				'infoStr' => json_encode($result)
			);

			$this->assign($data);

		}

		return $this->fetch();

	}

	// 充值审核  OK
	public function recharge() {

		if (IS_POST) {

			$data = input('post.');
			
			if (isset($data['status'])) {

				$datas = array(

					'status'=>$data['status'],

					'descr' => $data['descr']

				);

				if ($data['status'] == '1') {

					$result = db('recharge')->field('uid,money')->where('sn',$data['id'])->find();
					// var_dump($result);die;

					$name = serch_name($result['uid']);

					$deal_money = db('dealer')->field('money')->where('mobile',$name['mobile'])->find();

					$total_money = $deal_money['money'] + $result['money'];
					// echo $total_money;die;

					db('dealer')->where('mobile',$name['mobile'])->setField('money',$total_money);
					
					db('recharge')->where('sn',$data['id'])->update($datas);

					$resp['code'] = 1;

					$resp['msg'] = '充值审核成功!';

				}else{

					db('recharge')->where('sn',$data['id'])->update($datas);

					$resp['code'] = 0;

					$resp['msg'] = '充值审核失败!';
				}

			}else{

				$result = db('recharge')->where('sn',$data['id'])->find();
				// var_dump($result);die;
				foreach ($result as $k => $v) {

					$sercher = serch_name($v['uid']);
					// var_dump($sercher);die;

					$result[$k]['dealer_name'] = $sercher['dealer_name'];
				}
				// var_dump($result);die;

				$data = array(
					'infoStr' => json_encode($result)
				);

				$this->assign($data);
			}
		}else{
			$result = db('recharge')->order('create_time DESC,status ASC')->select();
			// var_dump($result);die;
			foreach ($result as $k => $v) {

				$sercher = serch_name($v['uid']);
				// var_dump($sercher);die;

				$result[$k]['dealer_name'] = $sercher['dealer_name'];
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

			if (isset($data['status'])) {
				
				$datas = array(

					'status'=>$data['status'],

					'descr' => $data['descr']

				);

				$datas['update_time'] = time();

				if ($data['status']) {

					db('carry')->where('sn',$data['id'])->update($datas);

					db('order')->where('id',$data['id'])->setField('finance','4');

					$resp['code'] = 1;

					$resp['msg'] = '提现审核成功!';

				}else{

					db('carry')-> where('sn',$data['id'])->update($datas);

					$resp['code'] = 0;

					$resp['msg'] = '提现审核失败!';
				}


			}else{

				$result = db('carry')->where('sn',$data['id'])->find();

				foreach ($result as $k => $v) {

					$sercher = serch_name($v['uid']);
					// var_dump($sercher);die;

					$result[$k]['dealer_name'] = $sercher['dealer_name'];

				}
				// var_dump($result);die;

				$data = array(
					'infoStr' => json_encode($result)
				);

				$this->assign($data);

			}

		}else{

			$result = db('carry')->order('create_time DESC, status ASC')->select();

			foreach ($result as $k => $v) {

				$sercher = serch_name($v['uid']);
				// var_dump($sercher);die;

				$result[$k]['dealer_name'] = $sercher['dealer_name'];

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

			if (isset($data['status'])) {
				
				$datas = array(

					'status' => $data['status'],

					'descr' => $data['descr']

				);

				if ($data['status']) {

					$money = db('dealer')->alias('d')->field('d.lines_ky,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->join('order_repay o','m.uid = o.mid')->where('o.id',$data['id'])->find();

					$result = db('order')->field('loan_limit')->where('order_id',$data['id'])->find();

					$lines_result = $moeny['lines_ky'] + $result['loan_limit'];//最终可用额度

					$moeny_result = array(

						'lines_ky' =>$lines_result

						);

					db('dealer')->where('mobile',$money['mobile'])->update($moeny_result);//改变可用额度

					db('order_repay')->where('order_id',$data['id'])->update($datas);//更新订单状态

					$resp['code'] = 1;

					$resp['msg'] = 'OK';

				}else{

					$resp['code'] = 0;

					$resp['msg'] = '回款审核失败!';

				}


			}else{

				$result = db('order_repay')->where('order_id',$data['id'])->select();

				foreach ($result as $k => $v) {

					$sercher = serch_name($v['mid']);
					// var_dump($sercher);die;

					$result[$k]['dealer_name'] = $sercher['dealer_name'];

				}
				// var_dump($result);die;

				$data = array(
					'infoStr' => json_encode($result)
				);

				$this->assign($data);

			}

		}else{
			$result = db('order_repay')->where('status','>',-1)->select();

			foreach ($result as $k => $v) {

				$sercher = serch_name($v['uid']);
				// var_dump($sercher);die;

				$result[$k]['dealer_name'] = $sercher['dealer_name'];

			}
			// var_dump($result);die;

			$data = array(
				'infoStr' => json_encode($result)
			);

			$this->assign($data);

		}
		return $this->fetch();
	}

	// 平台资金记录
	public function transaction() {

		$result = db('dealer_money')->order('create_time DESC')->select();

		$data = array(
				'infoStr' => json_encode($result)
			);

		$this->assign($data);

		return $this->fetch();
	}

}