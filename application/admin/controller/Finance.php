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

		$data = array(
				'infoStr' => json_encode($result)
			);

		$this->assign($data);
		$this->setMeta('支付审核');
		return $this->fetch();
	}

	// 放款审核
	public function loan() {

		if (IS_POST) {

			$data = input('post.');

			// var_dump($data);die;
			if (isset($data['status'])) {

				$datas = array(

					'descr' => $data['descr'],

					'status'=> $data['status']

				);
				
				if ($data['status'] == '1') {

					$money = db('dealer')->alias('d')->field('d.lock_money,d.lines_ky,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->join('__ORDER__ o','m.uid = o.mid')->where('o.id',$data['id'])->find();

					$result = db('order')->field('fee,loan_limit,examine_limit,type')->where('id',$data['id'])->find();
					
					if ($result['type'] == '2' || $result['type'] == '4') {

						if ($money['lock_money'] >= $result['fee']) {//判断冻结金额和订单费用
						
							$datas['finance'] = '3';

							//可用额度设置

							$lines_result = $money['lines_ky'] - $result['examine_limit'];//最终可用额度

							if ($lines_result < '0') {

								$resp['code'] = 0;

								$resp['msg'] = '可用额度不足，请提醒用户充值！';

								return json($resp);
							}
						
							$lock_money_result = $money['lock_money'] - $result['fee'];//剩余冻结金额

							$moeny_result = array(

								'lock_money' => $lock_money_result,

								'lines_ky' =>$lines_result

								);

							db('dealer')->where('mobile',$money['mobile'])->update($moeny_result);//改变资金流水

							db('order')->where('id',$data['id'])->update($datas);//更新订单状态

							//生成还款计划表
							
							$res = model('Repay')->set_repay($data['id']);

							if ($res) {

								$resp['code'] = 1;

								$resp['msg'] = '放款审核成功!';

							}else{

								$resp['code'] = 0;

								$resp['msg'] = '还款计划异常';
							}
							

						}else{
							$resp['code'] = 0;

							$resp['msg'] = '冻结资金异常!';
						}

					}else{

						$datas['finance'] = '3';

						$datas['descr'] = $data['descr'];

						db('order')->where('id',$data['id'])->update($datas);//更新订单状态

						//等额本息

						$res = model('Repay')->make_plan($data['id']);

						if ($res) {

							$resp['code'] = 1;

							$resp['msg'] = '放款审核成功!';

						}else{

							$resp['code'] = 0;

							$resp['msg'] = '还款计划异常';
						}
					}
					
				}else{

					db('order')->where('id',$data['id'])->update($datas);

					$resp['code'] = -1;

					$resp['msg'] = '放款审核失败!';

				}
				examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);
			}else{

				$result = db('order')->where('id',$data['id'])->find();

				$sercher =  serch_name($result['dealer_id']);

				$result['dealer_name'] = $sercher['dealer_name'];//渠道名称

				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;

				}
				
			return json($resp);

		}else{

			$result = db('order')->where('finance','2')->select();

			foreach ($result as $k => $v) {

				$sercher = serch_name($v['dealer_id']);

				$result[$k]['dealer_name'] = $sercher['dealer_name'];//渠道名称

			}

			$data = array(
				'infoStr' => json_encode($result)
			);

			$this->assign($data);

		}
		$this->setMeta('放款审核');
		return $this->fetch();

	}

	// 充值审核  OK
	public function recharge() {

		if (IS_POST) {

			$data = input('post.');

			if (isset($data['status'])) {

				$result = db('recharge')->field('uid,status')->where('sn',$data['id'])->find();

				if ($result['status'] == '-1') {

					if ($data['status'] == '1') {

						$name = serch_name_dealer($result['uid']);

						$deal_money = db('dealer')->field('money')->where('mobile',$name['mobile'])->find();

						$total_money = $deal_money['money'] + $data['actual_amount'];

						db('dealer')->where('mobile',$name['mobile'])->setField('money',$total_money);
						
						db('recharge')->where('sn',$data['id'])->update($data);

						$resp['code'] = 1;

						$resp['msg'] = '充值审核成功!';

					}else{

						$datas = array(

							'status' => $data['status'],

							'descr'=>$data['descr']

							);

						db('recharge')->where('sn',$data['id'])->update($datas);

						$resp['code'] = 0;

						$resp['msg'] = '充值审核不通过';
					}
					

				}else{

					$resp['code'] = 2;

					$resp['msg'] = '已审核!';

				}
				examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);
			}else{

				$result = db('recharge')->where('sn',$data['id'])->find();

				$sercher =  serch_name_dealer($result['uid']);

				$result['dealer_name'] = $sercher['dealer_name'];//渠道名称

				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;
			}
			
			return json($resp);
		}else{
			$result = db('recharge')->order('create_time DESC,status ASC')->select();
			
			foreach ($result as $k => $v) {

				$sercher = serch_name_dealer($v['uid']);

				$result[$k]['dealer_name'] = $sercher['dealer_name'];
			}

			$data = array(
				'infoStr' => json_encode($result)
			);

			$this->assign($data);
		}
		$this->setMeta('充值审核');
		return $this->fetch();
	}

	// 提现审核
	public function withdraw() {

		if (IS_POST) {

			$data = input('post.');
			// var_dump($data);die;
			if (isset($data['status'])) {
				
				$data['update_time'] = time();

				if ($data['status']) {

					$carry_info = db('carry')->where('sn',$data['id'])->find();

					$datas = array(

						'status' => $data['status'],

						'descr'=>$data['descr'],
						
						'serial_num'=>$data['serial_num'],
						
						'actual_amount'=>$carry_info['money'],
						
						'platform_account'=>$data['platform_account'],

						);

					if ($carry_info['status'] == '-1') {

						db('carry')->where('sn',$data['id'])->update($data);

						db('order')->where('sn',$data['id'])->setField('finance','4');

						$resp['code'] = 1;

						$resp['msg'] = '提现审核成功!';

					}else{

						$resp['code'] = 2;

						$resp['msg'] = '提现已审核!';
					}
					
				}else{

					$datas = array(

						'status'=>'0',

						'descr' => $data['descr']

						);

					db('carry')-> where('sn',$data['id'])->update($datas);

					$resp['code'] = 0;

					$resp['msg'] = '提现审核不通过';
				}
				examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);

			}else{

				$result = db('carry')->where('sn',$data['id'])->find();

				$sercher = serch_name_dealer($result['uid']);

				$result['dealer_name'] = $sercher['dealer_name'];

				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;

			}
			
			return json($resp);

		}else{

			$result = db('carry')->order('create_time DESC, status ASC')->select();

			foreach ($result as $k => $v) {

				$sercher = serch_name_dealer($v['uid']);

				$result[$k]['dealer_name'] = $sercher['dealer_name'];

			}

			$data = array(
				'infoStr' => json_encode($result)
			);

			$this->assign($data);

		}
		$this->setMeta('提现审核');
		return $this->fetch();
	}
	
	// 回款审核
	public function receivable() {

		if (IS_POST) {

			$data = input('post.');
			
			if (isset($data['status'])) {

				
				if ($data['status'] == '1') {


					$ids = db('order_repay')->field('status')->where('id',$data['id'])->order('status ASC, true_repay_time DESC')->find();

					if ($ids['status'] == '-2') {
						
						$result = model('repay')->receivable($data);

						if ($result) {


							$resp['code'] = 1;

							$resp['msg'] = 'OK';
							
							$resp['code'] = 1;

							$resp['msg'] = '回款审核成功！';

						}else{

							$resp['code'] = 0;

							$resp['msg'] = '回款审核异常！';

						}

					}elseif($ids['status'] == '-1'){

						$resp['code'] = 0;

						$resp['msg'] = '操作错误,未到还款日！';

					}else{

						$resp['code'] = 1;

						$resp['msg'] = '回款审核已提交！';
					}
				}else{

					$datas['status'] = '-1';

					$datas['has_repay'] = '-1';

					$datas['descr'] = $data['descr'];

					db('order_repay')->where('id',$data['id'])->update($datas);//更新订单状态

					$resp['code'] = '1';

					$resp['msg'] = '还款不通过';

				}

				examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);

				return json($resp);
			}else{

				$result = db('order_repay')->where('id',$data['id'])->find();

				$sercher = serch_name($result['dealer_id']);

				$result['dealer_name'] = $sercher['dealer_name'];


				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;

			}

			return json($resp);

		}else{

			$result = db('order_repay')->where('status','-2')->order('status')->select();
			foreach ($result as $k => $v) {

				$sercher = serch_name($v['dealer_id']);

				$result[$k]['dealer_name'] = $sercher['dealer_name'];

			}

			$data = array(

				'infoStr' => json_encode($result)
			);

			$this->assign($data);

		}
		$this->setMeta('还款审核');

		return $this->fetch();
	}

	// 平台资金记录
	public function transaction() {

		$result = db('dealer_money')->order('create_time DESC')->select();

		foreach ($result as $k => $v) {

			$serch_name = serch_name_dealer($v['uid']);

			$result[$k]['dealer_name'] = $serch_name['dealer_name'];
		}

		$data = array(
				'infoStr' => json_encode($result)
			);

		$this->assign($data);

		$this->setMeta('平台资金记录');

		return $this->fetch();
	}

}