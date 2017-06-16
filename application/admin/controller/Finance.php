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
			$res = db('Order')->field('type,mobile,id,sn,create_time,fee,endtime,examine_limit')->where('id',$data['id'])->find();
			if (isset($data['status'])) {

				$datas = array(

					'descr' => $data['descr'],

					'status'=> $data['status'],

					'update_time'=>time()

				);
				if ($data['status'] == '1') {
					
					if ($res['type'] == '2' || $res['type'] == '4') {

						$sta = db('Order')->alias('o')->field('c.status,c.moneymoreid')->join('__BANKCARD__ c','o.dealer_id = c.uid')->find();
					}else{
						$stall = db('Order')->alias('o')->field('o.examine_limit,m.uid')->join('__MEMBER__ m','m.mobile = o.mobile')->find();
						$sta = db('Bankcard')->field('status,moneymoreid')->where('uid',$stall['uid'])->find();
						$sta['examine_limit'] = $stall['examine_limit'];
					}
					$stas = empty($sta['status'])? '-5' : $sta['status'];
					if ($stas < '-2') {

						$resp['code'] = 0;

						$resp['msg'] = '没有绑卡,无法放款';

						return json($resp);

					}elseif ($stas['status'] == '1'){
						// $money = db('dealer')->alias('d')->field('d.lock_money,d.lines_ky,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->join('__ORDER__ o','m.uid = o.mid')->where('o.id',$data['id'])->find();

						// $result = db('order')->field('fee,loan_limit,examine_limit,type,mobile,sn,endtime')->where('id',$data['id'])->find();

						if ($result['type'] == '2' || $result['type'] == '4') {

							// if ($money['lock_money'] >= $result['fee']) {//判断冻结金额和订单费用
							
							$datas['finance'] = '3';

							//可用额度设置

								// $lines_result = $money['lines_ky'] - $result['examine_limit'];//最终可用额度

								// if ($lines_result < '0') {

								// 	$resp['code'] = 0;

								// 	$resp['msg'] = '可用额度不足，请提醒用户充值！';

								// 	return json($resp);
								// }
							
								// $lock_money_result = $money['lock_money'] - $result['fee'];//剩余冻结金额

								// $moeny_result = array(

								// 	'lock_money' => $lock_money_result,

								// 	'lines_ky' =>$lines_result,

								// 	);

								// db('dealer')->where('mobile',$money['mobile'])->update($moeny_result);//改变资金流水

							

							//生成还款计划表
								$rest = db('order')->where('id',$data['id'])->update($datas);//更新订单状态	
								$resl = model('Repay')->set_repay($data['id']);

								if ($resl && $rest) {

									$resp['code'] = 1;

									$resp['msg'] = '放款审核成功!';

								}else{

									$resp['code'] = 0;

									$resp['msg'] = '还款计划异常';
								}
								

							// }else{
							// 	$resp['code'] = 0;

							// 	$resp['msg'] = '冻结资金异常!';
							// }

						}else{

							$datas['finance'] = '4';

							$datas['descr'] = $data['descr'];

							db('order')->where('id',$data['id'])->update($datas);//更新订单状态

							//等额本息

							// $res = model('Repay')->make_plan($data['id']);//

							if ($res['endtime'] < 500) {
								$llment = '12';
							}elseif ($res['endtime'] >500 && $res['endtime']< 1000) {
								$llment = '24';
							}else{
								$llment = '36';
							}

							//乾多多放款
							$datainfo = array(
								'PayMoneymoremore'=>$sta['moneymoreid'],
								'Amount'=>$sta['examine_limit'],
								'OrderNo'=>$res['sn'],
								'Installment'=>$llment,
								'BatchNo'=>$res['id'],
								'RepaymentDate'=>date('Y-m-d',time()+30*24*60*60),
								'NotifyURL'=>url('pay/notify/loan'),
							);
							$epay = new \epay\Epay();
							$ret = $epay::loan($datainfo);
					        if (empty($ret)) {
					        	$resp['code'] = '0';
					        	$resp['msg'] = '放款异常,请联系客服';
					        	return json($resp);
					        }
					        $ret  = json_decode($ret,true);
					        if ($ret['ResultCode'] == '88') {
					        	$resp['code']='1';
					        	$resp['msg'] = '放款成功';
					        	model('Repay')->make_interest($data['id']);
					        	//放款成功加入客户签约
								$customer_info = db('member')->where('mobile',$result['mobile'])->find();
								$b_map = array(
										'uid'=>$customer_info['uid'],
										'order_id'=>$data['id']
								);
								$bank_name = db('bankcard')->where($b_map)->find();
								if (!$bank_name) {
									$bank_name['bank_account_id'] = '';
								}
								$totalperiod = floor($result['endtime']/30);
								$cus_data = array(
										'uid'=>$customer_info['uid'],
										'mobile'=>$result['mobile'],
										'order_id'=>$data['id'],
										'idcard_num'=>$customer_info['idcard'],
										'bankcard'=>$bank_name['bank_account_id'],
										'signstatus'=>'0',
										'papercontract'=>$result['sn'],
										'product_name'=>'90贷',
										'product_price'=>$result['examine_limit'],
										'money'=>$result['examine_limit'],
										'create_time'=>time(),
										'total_times'=>$totalperiod,
										'repay_type'=>'SELF_REPAY',
										'other_rate'=>0,
										'first_repaydate'=>time()+30*3600*24

									);
								db('member_withhold')->insert($cus_data);
					        }else{
					        	$resp['code'] = '0';
					        	$resp['msg'] = $ret['Message'];
					        }

						}
						
					}

					
				}else{

					db('order')->where('id',$data['id'])->update($datas);

					$resp['code'] = -1;

					$resp['msg'] = '放款审核失败!';
				}
				examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);
			}else{
				//垫资
				if ($res['type'] == '2' || $res['type'] == '4') {
					$map = array(
						'o.id'=>$data['id'],
					);
					$result = db('order')->alias('o')->field('o.*,d.name as dealer_name,c.bank_account_name,c.bank_account_id as dealer_bankcard,c.moneymoreid as qdd_mark')->join('__DEALER__ d','d.id = o.dealer_id','LEFT')->join('__BANKCARD__ c','c.uid = o.dealer_id','LEFT')->where($map)->order('c.create_time DESC')->fetchSql(false)->find();
					
				}else{
				//贷款
					$restinfo = db('Member')->alias('m')->field('m.realname as name,c.bank_account_name,c.bank_account_id as dealer_bankcard,c.moneymoreid as qdd_mark')->join('__BANKCARD__ c','c.uid = m.uid','LEFT')->where('mobile',$res['mobile'])->find();
					$result =  array_merge($res,$restinfo);

				}
				if ($result) {
					$result['loan_money'] = $result['fee']+$result['examine_limit'];
					$result['repay_time'] = date('Y-m-d H:i:s',time()+30*24*60*60);
					$resp['code'] = 1;

					$resp['msg'] = '查询成功';

					$resp['data'] = $result;
				}else{
					$resp['code'] = 0;

					$resp['msg'] = '查询失败';
				}
				
			}
			return json($resp);

		}else{

			$result = db('order')->alias('o')->field('o.*,d.name as dealer_name')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->where('finance','2')->select();
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

				$result = db('recharge')>alias('c')->field('c.*,d.name as dealer_name')->join('__DEALER__ d','c.uid = d.id')->where('sn',$data['id'])->find();

				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;
			}
			
			return json($resp);
		}else{
			$result = db('recharge')->alias('c')->field('c.*,d.name as dealer_name')->join('__DEALER__ d','c.uid = d.id')->order('create_time DESC,status ASC')->select();
			
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

				$result = db('carry')->alias('c')->field('c.*,d.name as dealer_name')->join('__DEALER__ d','d.id = c.uid','LEFT')->where('sn',$data['id'])->find();

				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;

			}
			
			return json($resp);

		}else{

			$result = db('carry')->alias('c')->field('c.*,d.name as dealer_name')->join('__DEALER__ d','d.id = c.uid','LEFT')->order('create_time DESC, status ASC')->select();

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

				$result = db('order_repay')->alias('r')->field('r.*,d.name as dealer_name')->join('__DEALER__ d','d.id = r.dealer_id','LEFT')->where('r.id',$data['id'])->find();


				$resp['code'] = 1;

				$resp['msg'] = '查询成功';

				$resp['data'] = $result;

			}

			return json($resp);

		}else{

			$order = model('Order');

			$result = db('order_repay')->alias('r')->field('r.*,o.sn,d.name as dealer_name')->join('__ORDER__ o','o.id = r.order_id','LEFT')->join('__DEALER__ d','r.dealer_id = d.id')->where('r.status','-2')->order('status')->select();

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

		$result = db('dealer_money')->alias('m')->field('m.*,d.name as dealer_name')->join('__DEALER__ d','d.id = m.uid','LEFT')->order('create_time DESC')->select();

		$data = array(
				'infoStr' => json_encode($result)
			);

		$this->assign($data);

		$this->setMeta('平台资金记录');

		return $this->fetch();
	}

}