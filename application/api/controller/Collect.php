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
		$uid = session('user_auth.uid');
		$collectKeys= array('contact','message','device','location','network'); 
		foreach ($collectKeys as $collectKey){
			$dbData = db('collect_data')->where('group','=',$collectKey)->where('uid','=',$uid)->where('from','=','app')->field('update_time')->limit(1)->find();//'key,value,updateTime'
			if($dbData == null){
				$data[$collectKey]['needUpdate'] = 1;
				$data[$collectKey]['updateTime'] = null;
			}else{
				$data[$collectKey]['needUpdate'] = 0;
				$data[$collectKey]['updateTime'] = $dbData['update_time'];
			}
		} 
		$resp['code'] = 1;
		$resp['msg'] = '获取成功！';
		$resp['data'] = $data;

		return json($resp);
	}
	

	public function contact($contact = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$data["uid"] = session('user_auth.uid');
		$data["key"] = "contact";
		$data["value"] = $contact;
		$data["group"] = "contact";
		$data["from"] = "app";
		$data["format"] = 1;
		$data["create_time"] = time();
		$data["update_time"] = time();
		$result = db('collect_data')->insert($data);
		
		//saveCollectData("uid:".$uid. "\r\ncontact:" .$contact);
		if($result > 0){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
		}
		return json($resp);
	}
	
	
	public function message($message = '') {
		
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$data["uid"] = session('user_auth.uid');
		$data["key"] = "message";
		$data["value"] = $message;
		$data["group"] = "message";
		$data["from"] = "app";
		$data["format"] = 1;
		$data["create_time"] = time();
		$data["update_time"] = time();
		$result = db('collect_data')->insert($data);

		if($result > 0){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
		}
		return json($resp);
	}

	
	public function device($platform = '', $device = '', $imei = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');
		//saveCollectData("uid:".$uid. "\r\nplatform:" .$platform. "\r\ndevice:" . $device. "\r\nimei:" . $imei);
		$data = [
			['uid' => $uid , 'key' => 'platform', 'value' => $platform, 'group' => 'device', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'device', 'value' => $device, 'group' => 'device', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'imei', 'value' => $imei, 'group' => 'device', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()]
		];

		$result = db('collect_data')->insertAll($data);

		if($result > 0){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';			
		}
		return json($resp);
	}
	
	public function location($longitude = '', $latitude = '', $addr = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$uid = session('user_auth.uid');

		$data = [
			['uid' => $uid , 'key' => 'longitude', 'value' => $longitude, 'group' => 'location', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'latitude', 'value' => $latitude, 'group' => 'location', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'addr', 'value' => $addr, 'group' => 'location', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()]
		];

		$result = db('collect_data')->insertAll($data);

		if($result > 0){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
		}
		return json($resp);
	}
	
	public function network($lanip = '', $SSID = '', $mac = '') {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		
		$wanip = get_client_ip(0,true);
		
		$uid = session('user_auth.uid');

		$data = [
			['uid' => $uid , 'key' => 'lanip', 'value' => $lanip, 'group' => 'network', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'SSID', 'value' => $SSID, 'group' => 'network', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'mac', 'value' => $mac, 'group' => 'network', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()],
			['uid' => $uid , 'key' => 'wanip', 'value' => $wanip, 'group' => 'network', 'from' => 'app', 'format' => 0, 'create_time' => time(), 'update_time' => time()]
		];

		$result = db('collect_data')->insertAll($data);
		if($result > 0){
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
		}
		return json($resp);
	}

	

	
}
