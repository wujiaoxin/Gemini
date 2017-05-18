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
		
		$result = db('member_withhold')->select();
		foreach ($result as $k => $v) {
			$name = serch_realname($v['uid']);
			$result[$k]['realname'] = $name;
		}
		$data = array(
			'infoStr' =>json_encode($result),
		);
		$this->assign($data);
		$this->setMeta('代扣审核');
		return $this->fetch();
	}

	public function view() {

		if (IS_POST) {
			$data = input('post.');
			$info = db('member_withhold')->where('id',$data['id'])->find();
			$name = serch_realname($info['uid']);
			$order = db('order_repay')->alias('d')->join('__ORDER__ o','o.id = d.order_id')->where('order_id',$info['order_id'])->order('repay_time')->find();
			$service = "installmentSign";
			$res  = array( 
				'service' => $service,
				'orderNo' => '2007050512345678912345678' . rand(100000,999999),
				'signType' =>'MD5',
				'notifyUrl' => 'https://'.$_SERVER['HTTP_HOST'].'/pay/yixingtong/signstage.html',
				'realName' => $name,
				'certNo' => $order['idcard_num'],
				'certValidTime' => $data['idcard_time'],
				'imageUrl2' => 'https://'.$_SERVER['HTTP_HOST'].get_order_files($data['idcard_face_pic'])['path'],
				'certBackImageUrl' => 'https://'.$_SERVER['HTTP_HOST'].get_order_files($data['idcard_back_pic'])['path'],
				'mobileNo' => $order['mobile'],
				'bankCardNo' => '6228480438657589174',
				'imageUrl3' => 'https://'.$_SERVER['HTTP_HOST'].get_order_files($data['bankimage'])['path'],
				'profession' => $data['profession'],
				'address' => $data['address'],
				'paperContractNo' => $order['sn'],
				'imageUrl1' => 'https://'.$_SERVER['HTTP_HOST'].get_order_files($data['image_contract'])['path'],
				'productName' => '90贷',
				'productPrice' => $order['examine_limit'],
				'totalCapitalAmount' => $order['examine_limit'],
				'installmentPolicy' => 'CUSTOMIZE',
				'firstRepayDate' => $info['first_repaydate'],
				'interestRate' => get_rate($order['endtime'])*100,
				'otherRate' => 0,
				'totalTimes' => $info['total_times'],
				'repayType' => 'SELF_REPAY',
				'eachTotalAmount' => get_arr($info['total_times'],$order['repay_money']),
				'eachCapitalAmount' => get_arr($info['total_times'],$order['self_money']),
				'eachInterestAmount' => get_arr($info['total_times'],$order['interest_money']),
				'eachOtherAmount' => get_arr($info['total_times'],0),
		    );
			$res_info = array(
					'orderno'=>$res['orderNo'],
					'idcard_face_pic'=>$res['imageUrl2'],
					'idcard_back_pic'=>$res['certBackImageUrl'],
					'idcard_time'=>$res['certValidTime'],
					'profession'=>$res['profession'],
					'address'=>$res['address'],
					'image_contract'=>$res['imageUrl1'],
					'interest_rate'=>$res['interestRate'],
					'total_amount'=>$order['repay_money'],
					'capital_amount'=>$order['self_money'],
					'other_amount'=>'0',
				);
			// var_dump($res_info);
			db('member_withhold')->where('id',$info['id'])->update($res_info);
			die;
			$result = \com\Withhold::installSign($res);
			if ($result['resultCode'] == 'EXECUTE_SUCCESS') {
				$resp['code'] = '1';
				$resp['msg'] = '签约成功';

			}elseif ($result['resultCode'] == 'EXECUTE_PROCESSING') {
				$resp['code'] = '1';
				$resp['msg'] = '签约处理中';
			}else{
				$resp['code'] = '0';
				$resp['msg'] = '银行卡验证失败';
			}
			return json($resp);
		}else{
			$result = db('member_withhold')->find(input('id'));
			$files = db('order_files')->where('order_id',$result['order_id'])->limit(9)->order('create_time DESC')->select();
			$name = serch_realname($result['uid']);
			$result['realname'] = $name;
			$res =array(
				'data'=>$result,
				'files'=>$files
				);
			$data = array(
				'infoStr' =>json_encode($res),
			);
			$this->assign($data);
			$this->setMeta('审核查看');
			return $this->fetch();
		}
	}

}