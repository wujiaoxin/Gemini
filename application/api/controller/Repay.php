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
		$resp = json_decode($resp);
		return $resp;
	}

	
}
