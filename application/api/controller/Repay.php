<?php

namespace app\api\controller;
use app\common\controller\Api;

class Repay extends Api {
	
	public function index() {	
		$resp['code'] = 1;
		$resp['msg'] = 'RepayAPI';
		return json($resp);
	}	

	
	public function getList() {
		
		$uid = session('user_auth.uid');
		
		$repayList = db('order_repay')->field('product_name as name, repay_period as period, totalperiod as totalperiod, repay_money as monthpay, FROM_UNIXTIME(repay_time,\'%Y-%m-%d\') as time, has_repay as isrepaid,order_id as orderid,has_repay')->where("uid",$uid)->order('repay_time')->fetchSQL(false)->select();
		//不分页
		$resp['code'] = 1;		
		$resp['msg'] = "获取成功！";		
		$data["per_page"] = count($repayList);
		$data["current_page"] = 1;
		$data["total"] = count($repayList);	
		$data["data"] = $repayList;			
		$resp['data'] = $data;
		
		/*
		//系统分页
		$repayList = db('order_repay')->field('product_name as name, repay_period as period, totalperiod as totalperiod, repay_money as monthpay, FROM_UNIXTIME(repay_time,\'%Y-%m-%d\') as time, has_repay as isrepaid')->where("uid",$uid)->order('id desc')->fetchSQL(false)->paginate(10);
		$resp['code'] = 1;		
		$resp['msg'] = "获取成功！";
		$resp['data'] = $repayList;
		*/

		/*
		$resp = '{
			"code": 1,
			"msg": "获取成功！",
			"data": {
				"total": 2,
				"per_page": 15,
				"current_page": 1,
				"data": [
					{
						"name": "新车分期",
						"period": 1,
						"totalperiod": 3,
						"monthpay": 2000,
						"time": "2017-02-04",
						"isrepaid": 1
					},
					{
						"name": "二手车垫资",
						"period": 1,
						"totalperiod": 1,
						"monthpay": 10000,
						"time": "2017-07-04",
						"isrepaid": 0
					}
				]
			}
		}';
		$resp = json_decode($resp);*/
		return json($resp);
	}
	/*
	** $orderid 订单id
	** $period 订单期数
	*/
	public function searchrepay($orderid,$period){
		$map = array('order_id'=>$orderid,'repay_period'=>$period);
		$res = db('order_repay')->alias('a')->field('a.*,o.update_time as create_time')->join('__ORDER__ o','a.order_id=o.id')->where($map)->find();
		if ($res) {
			$resp['code'] = 1;
			$resp['msg'] = '获取成功!';
			$resp['data'] = $res;
		}else{
			$resp['code'] = 0;
			$resp['msg'] = '获取失败!';
			$resp['data'] = '';
		}
		return json($resp);
	}

	/* 
	** $orderid 订单id
	** $period  订单期数
	*/
	public function selfrepay($orderid,$period){

		$uid = session('user_auth.uid');

		$map = array('order_id'=>$orderid,'uid'=>$uid);

		$contractNo = db('member_withhold')->field('contractno')->where($map)->find();

		$map['repay_period'] = $period;

		$res = db('order_repay')->field('repay_money,repay_time,id,status,has_repay')->where($map)->find();
		
		//判断是否绑卡
		$where = array('uid'=>$uid,'order_id'=>$orderid);
		$withhold = db('member_withhold')->field('signstatus')->where($where)->find();
		if(!empty($withhold)){
			if($withhold['signstatus'] != 1){
				$resp['code'] = 5;
				$resp['msg'] = '没有绑卡,请联系客服';
				return json($resp);
			}
		}
		//到期执行代扣还款 TODO
		$datatime = date('Y-m',$res['repay_time']);
		$endtime = date('Y-m',time());
		if ($datatime > $endtime) {
			$resp['code'] = 2;
			$resp['msg'] = '未到还款时间,请联系客服';
			return json($resp);
		}
		
		switch ($res['status']) {
			case '-2':
				$resp['code'] = -2;
				$resp['msg'] = '还款处理中';
				return json($resp);
			case '1':
				$resp['code'] = 0;
				$resp['msg'] = '已还款';
				return json($resp);
		}

		$service = "installmentSelfRepay";
		$orderon = '2007050512345678912' . rand(100000,999999);
		$externalOrderNo = '20070505123456789' . rand(100000,999999);
    	$data = array(
    		'service' => $service,
    		'signType' =>'MD5',
			'notifyUrl' => url('pay/yixingtong/notifyurl'),
			// 'notifyUrl' => 'https://t.vpdai.com/pay/yixingtong/notifyurl',
			'orderNo' => $orderon,
    		'contractNo'=>$contractNo['contractno'],
    		'externalOrderNo'=>$externalOrderNo,
    		'totalAmount'=>$res['repay_money'],
    		);
    	db('order_repay')->where($map)->update(['orderon'=>$externalOrderNo]);//TODO未生效
    	$result = \com\Withhold::selfrepay($data);
    	if ($result['resultCode'] == 'EXECUTE_SUCCESS') {
    		$resp['code'] = 1;
			$resp['msg'] ='还款成功';
			db('order_repay')->where($map)->update(['status'=>'-2','has_repay'=>'-2']);//TODO未生效
			$ress = array(
				'money'=>$res['repay_money'],
				'descr'=>'',
			);
			money_record($ress,$uid,6,0);

    	}elseif ($result['resultCode'] == 'EXECUTE_PROCESSING') {
    		$resp['code'] = -2;
			$resp['msg'] ='还款处理中';
    	}else{
    		$resp['code'] = 3;
    		$resp['msg'] = $result['resultMessage'];
    	}
    	$results = json_encode($result);
    	$filename="query_log.txt";
		$handle=fopen($filename,"a+");
		if($handle){
			fwrite($handle,"=========还款操作=============\r\n");
			fwrite($handle, date("Y-m-d h:i:sa")."\r\n");
			fwrite($handle,$results."\r\n");
			fwrite($handle,"==========================\r\n\r\n");
		}
		fclose($handle);
		return json($resp);
	}
	
}
