<?php

namespace app\api\controller;
use app\common\controller\Api;

class Fixedrepay extends Api {
	
	public function index($start = ""){
		if ($start != 'koukuan') {
			return $this->redirect('/');
		}
		
		$map = array(
			"FROM_UNIXTIME(repay_time,'Y-m-d')"=>date("Y-m-d",time())

		);
		$res = db('order_repay',[],false)->field('id,repay_period,repay_time')->where($map)->select();
		foreach ($res as $k => $v) {
			$ra = $this->selfrepay($v['id'],$v['repay_period']);
		}
		
	}

	public function selfrepay($orderid,$period){

		$map = array('order_id'=>$orderid);

		$contractNo = db('member_withhold',[],false)->field('contractno,uid')->where($map)->find();

		$map['repay_period'] = $period;

		$res = db('order_repay',[],false)->field('repay_money,repay_time,id,status,has_repay')->where($map)->find();
		// echo $orderid.'<br>'.date('Y-m-d H:i:s',time());
		// echo "<hr>";
		//到期执行代扣还款 TODO
		$datatime = date('Y-m',$res['repay_time']);
		$endtime = date('Y-m',time());
		if ($datatime > $endtime) {
			return;
		}
		//判断是否绑卡
		$where = array('uid'=>$contractNo['uid'],'order_id'=>$orderid);
		$withhold = db('member_withhold')->field('signstatus')->where($where)->find();
		if(!empty($withhold)){
			if($withhold['signstatus'] != 1){
				return;
			}
		}else{
			return;
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
			db('order_repay')->where($map)->update(['status'=>'-2']);//TODO未生效
			money_record($ress,$contractNo['uid'],6,1);

    	}elseif ($result['resultCode'] == 'EXECUTE_PROCESSING') {
    		$resp['code'] = -2;
			$resp['msg'] ='还款处理中';
    	}else{
    		$resp['code'] = 3;
    		$resp['msg'] = $result['resultMessage'];
    	}
    	$results = json_encode($result);
    	$time = time();
    	$filename="query_log_{$time}.txt";
		$handle=fopen($filename,"a+");
		if($handle){
			fwrite($handle,"=========还款操作=============\r\n");
			fwrite($handle, date("Y-m-d h:i:sa")."\r\n");
			fwrite($handle,$results."\r\n");
			fwrite($handle,"==========================\r\n\r\n");
		}
		fclose($handle);
		sleep(2);
		ob_flush();//每次执行前刷新缓存 
	    flush(); 
		return json($resp);
	}
}
