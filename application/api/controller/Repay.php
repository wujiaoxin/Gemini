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
		
		$repayList = db('order_repay')->field('product_name as name, repay_period as period, totalperiod as totalperiod, repay_money as monthpay, repay_time as time, has_repay as isrepaid')->where("uid",$uid)->order('id desc')->fetchSQL(false)->select();
		
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
		$repayList = db('order_repay')->field('product_name as name, repay_period as period, totalperiod as totalperiod, repay_money as monthpay, repay_time as time, has_repay as isrepaid')->where("uid",$uid)->order('id desc')->fetchSQL(false)->paginate(10);
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

	
}
