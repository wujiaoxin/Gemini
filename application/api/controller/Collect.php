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

class Collect extends Api {
	
	public function index() {
		$resp['code'] = 1;
		$resp['msg'] = 'CreditAPI';
		return json($resp);
	}

	public function collected() {
		$resp = '{
				"code": 1,
				"msg": "获取成功！",
				"data": {
					"contact": {
						"updateTime": 1491906569,
						"needUpdate": 0
					},
					"message": {
						"updateTime": null,
						"needUpdate": 1
					},
					"device": {
						"updateTime": 1491906569,
						"needUpdate": 0
					},
					"location": {
						"updateTime": 1491906569,
						"needUpdate": 1
					},
					"network": {
						"updateTime": 1491906569,
						"needUpdate": 0
					}
				}
			}';
		
		$resp = json_decode($resp);
		return $resp;
	}
	

	public function contact($contact = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');
		saveCollectData("uid:".$uid. "\r\ncontact:" .$contact);
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}
	
	
	public function message($message = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		$uid = session('user_auth.uid');
		saveCollectData("uid:".$uid. "\r\nmessage:".$message);
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}

	
	public function device($platform = null, $device = null, $imei = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');
		saveCollectData("uid:".$uid. "\r\nplatform:" .$platform. "\r\ndevice:" . $device. "\r\nimei:" . $imei);
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}
	
	public function location($longitude = '', $latitude = null, $addr = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$uid = session('user_auth.uid');
		saveCollectData("uid:".$uid. "\r\nlongitude:" .$longitude. "\r\nlatitude:" .$latitude. "\r\naddr:" . $addr );
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}
	
	public function network($lanip = null, $SSID = null, $mac = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');
		saveCollectData("uid:".$uid. "\r\nlanip:" .$lanip. "\r\nSSID:" .$SSID. "\r\nmac:" . $mac );
		
		$resp['code'] = 1;
		$resp['msg'] = '提交成功';
 
		return json($resp);
	}

	

	
}
