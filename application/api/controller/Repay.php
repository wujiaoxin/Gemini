<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use app\common\controller\Api;

class Repay extends Api {
	
	public function index() {	
		$resp['code'] = 1;
		$resp['msg'] = 'RepayAPI';
		return json($resp);
	}	
	
	public function getList() {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$data["total"] = "0";
		$data["per_page"] = "15";
		$data["current_page"] = "1";
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功';
		$resp['data'] = $data;
 
		return json($resp);
	}

	
}
