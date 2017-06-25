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

		$result = db('order_repay')->alias('r')->field('r.*,d.name as dealer_name,o.sn')->join('__DEALER__ d','r.dealer_id = d.id','LEFT')->join('__ORDER__ o','o.id = r.order_id','LEFT')->select();
		$data = array(

			'infoStr' => json_encode($result)
		);

		$this->assign($data);

		$this->setMeta('回款审核');
		return $this->fetch();
	}


	public function withhold() {
		
		$result = db('member_withhold')->alias('w')->field('w.*,m.realname')->join('__MEMBER__ m','w.uid = m.uid','LEFT')->select();
		$data = array(
			'infoStr' =>json_encode($result),
		);
		$this->assign($data);
		$this->setMeta('代扣审核');
		return $this->fetch();
	}

	public function sign() {
		$map = array(
			'type'=>1
		);
		$result = db('Bankcard')->where($map)->select();
		$data = array(

			'infoStr' => json_encode($result)
		);

		$this->assign($data);

		$this->setMeta('客户签约');
		return $this->fetch();
	}

	public function view() {

		if (IS_POST) {
			$data = input('post.');
			$info = db('member_withhold')->where('id',$data['id'])->find();
			if ($info['signstatus'] == '0') {
				$name = serch_realname($info['uid']);
				$order = db('order_repay')->alias('d')->join('__ORDER__ o','o.id = d.order_id')->where('order_id',$info['order_id'])->order('repay_time')->find();
				$service = "installmentSign";
				$res  = array( 
					'service' => $service,
					'orderNo' => '2007050512345678912345678' . rand(100000,999999),
					'signType' =>'MD5',
					'notifyUrl' => url('pay/yixingtong/signstage'),
					// 'notifyUrl' => 'https://'.$_SERVER['HTTP_HOST'].'/pay/yixingtong/signstage.html',
					// 'notifyUrl' => 'https://t.vpdai.com/pay/yixingtong/signstage.html',
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
					// 'paperContractNo' => '20170505' . rand(100000,999999),
					'imageUrl1' => 'https://'.$_SERVER['HTTP_HOST'].get_order_files($data['image_contract'])['path'],
					'productName' => '90贷',
					'productPrice' => $order['examine_limit'],
					'totalCapitalAmount' => $order['examine_limit'],
					'installmentPolicy' => 'CUSTOMIZE',
					'firstRepayDate' => date('Y-m-d',$info['first_repaydate']),
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
						'idcard_face_pic'=>$data['idcard_face_pic'],
						'idcard_back_pic'=>$data['idcard_back_pic'],
						'idcard_time'=>$data['idcard_time'],
						'bankimage'=>$data['bankimage'],
						'profession'=>$res['profession'],
						'address'=>$res['address'],
						'total_amount'=>$order['repay_money'],
						'image_contract'=>$data['image_contract'],
						'interest_rate'=>$res['interestRate'],
						'capital_amount'=>$order['self_money'],
						'other_amount'=>'0',
						'installment_policy'=>'CUSTOMIZE',
					);
				// var_dump($res_info);
				
				$result = \com\Withhold::installSign($res);
				if ($result['resultCode'] == 'EXECUTE_SUCCESS') {
					$resp['code'] = '1';
					$resp['msg'] = '签约成功';
					$res_info['signstatus'] = '1';

				}elseif ($result['resultCode'] == 'EXECUTE_PROCESSING') {
					$resp['code'] = '1';
					$resp['msg'] = '签约处理中';
					$res_info['signstatus'] = '2';
				}else{
					$resp['code'] = '0';
					$resp['msg'] = $result['resultMessage'];
					$res_info['signstatus'] = '-1';
				}
				db('member_withhold')->where('id',$info['id'])->update($res_info);
			}else{
				$resp['code'] = 0;
				$resp['msg'] = '已签约';
			}
			
			return json($resp);
		}else{
			$result = db('member_withhold')->alias('w')->field('w.*,o.examine_limit as loan_limit,o.type,r.repay_money,m.realname')->join('__ORDER__ o','w.order_id = o.id','LEFT')->join('__ORDER_REPAY__ r','w.order_id = r.order_id','LEFT')->join('__MEMBER__ m','w.uid = m.uid','LEFT')->find(input('id'));

			$files = db('order_files')->where('order_id',$result['order_id'])->limit(9)->order('create_time DESC')->select();
			
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

	public function signview($id=null) {
		if (IS_POST) {
			$data = input('post.');

			//四要素验证绑卡
			$idcard = $data['IdentificationNo'];
			$realname = $data['RealName'];
			$bankcard = $data['CardNumber'];
			$mobile = $data['Mobile'];
			$event = new \app\riskmgr\controller\Yinlian();
			$res = $event->authvalid($idcard,$realname,$bankcard,$mobile,5);
			if (!empty($res)) {
				if ($res['resCode'] == '0000' && $res['stat'] == '1') {
					$info = array(
						'status'=>1,
						'update_time'=>time(),
					);
					db('bankcard')->where('id',$data['id'])->update($info);
					$resp["code"] = 1;
					$resp["msg"] = '绑卡成功';
				}elseif ($res['resCode'] == '0000' && $res['stat'] == '2') {
					$resp["code"] = 4;
					$resp["msg"] = '实名信息不匹配';	
					// $resp["data"] = $saveData;
					return json($resp);
				}else{
					$resp["code"] = 5;
					$resp["msg"] = empty($res['resMsg']) ? '数据异常': $res['resMsg'];;	
					// $resp["data"] = $saveData;
					return json($resp);
				}
			}else{
				$resp["code"] = 3;
				$resp["msg"] = "绑卡异常,请联系客服";
				return json($resp);
			}


			
			/* //乾多多绑卡
			$epay = new \epay\Epay();
			$ret = $epay::bankcard($data);//单卡绑定
			// $ret = $epay::bindcardag($data);//多卡绑定
	        if (empty($ret)) {
	        	$resp['code'] = '0';
	        	$resp['msg'] = '绑卡异常,请联系客服';
	        	return json($resp);
	        }
	        $ret  = json_decode($ret,true);
	        if ($ret['ResultCode'] == '88') {
	        	
	        	$arr = array(
					'moneymoreid'=>$result['MoneymoremoreId'],
					'status'=>1,
				);
				$map = array(
					'order_id'=>-1,
					'type'=>1,
					'idcard'=> $result['IdentificationNo'],
				);
				db('bankcard')->where($map)->update($arr);
				$resp['code']='1';
	        	$resp['msg'] = '绑卡成功';
	        }else{
	        	$resp['code'] = '0';
	        	$resp['msg'] = $ret['Message'];
	        }*/
			return json($resp);
		}else{
			$result = db('Bankcard')->alias('b')->field('b.*,m.mobile')->join('__MEMBER__ m','m.uid = b.uid')->where('id',$id)->find();
			if (!empty($result['bank_branch'])) {
				$resl  = explode(',', $result['bank_branch']);
				$result['province'] = $resl[0];
				$result['city'] = $resl[1];
			}
			$data = array(
				'infoStr' =>json_encode($result),
			);
			$this->assign($data);
			$this->setMeta('审核查看');
			return $this->fetch();
		}
	}

}