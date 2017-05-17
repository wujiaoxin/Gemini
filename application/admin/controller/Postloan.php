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

		if (IS_POST) {
			$data = input('post.');
			$info = db('member_withhold')->where('id',$data['id'])->find();
			$repay = db('order_repay')->where('order_id',$info['order_id'])->order('repay_time')->find();
			$arr = array();
			$arr1 = array();
			$arr2 = array();
			for ($i=0; $i < $repay['totalperiod']; $i++) { 
				$arr[] = $repay['repay_money'];
				$arr1[] = $repay['interest_money'];
				$arr2[] = '0';
			}
			$service = "installmentSign";
			$res  = array( 
				'service' => $service,
				'orderNo' => '2007050512345678912345678' . rand(100000,999999),
				'signType' =>'MD5',
				'notifyUrl' => 'http://mengxd.com/index.php/index/index/signstage.html',
				'realName' => $order_info['name'],
				'certNo' => $order_info['idcard_num'],
				'certValidTime' => $idcard_time,
				'imageUrl2' => 'https://v1.vpdai.com'.$info['idcard_face_pic'],
				'certBackImageUrl' => 'https://v1.vpdai.com'.$info['idcard_back_pic'],
				'mobileNo' => $order_info['mobile'],
				'bankCardNo' => '6228480438657589174',
				'imageUrl3' => 'https://v1.vpdai.com/uploads/order_files/20170113/ab58b08d3e8f33235c02e620fd0c439a.jpg',
				'profession' => '职工',
				'address' => $addr,
				'paperContractNo' => $order_info['sn'],
				'imageUrl1' => $info['image_contract'],
				'productName' => '90贷',
				'productPrice' => $order_info['examine_limit'],
				'totalCapitalAmount' => $totalperiod,
				'installmentPolicy' => 'CUSTOMIZE',
				'firstRepayDate' => '2017-05-20',
				'interestRate' => 100,
				'otherRate' => 0,
				'totalTimes' => 9,
				'repayType' => 'SELF_REPAY',
				'eachTotalAmount' => '[2,2,2,2,2,2,2,2,2]',
				'eachCapitalAmount' => '[1,1,1,1,1,1,1,1,1]',
				'eachInterestAmount' => '[1,1,1,1,1,1,1,1,1]',
				'eachOtherAmount' => '[0,0,0,0,0,0,0,0,0]',
		   );
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
		}
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