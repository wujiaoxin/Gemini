<?php

namespace app\common\controller;

class Api extends \think\Controller {
	public function _initialize() {
		// 读取数据库中的配置
		$config = cache('db_config_data');
		if (!$config) {
			$config = model('Config')->lists();
			cache('db_config_data', $config);
		}
		config($config);

		//$this->checkJWT();
		//检查SID
		if($this->checkSID() == false ){
			$resp["code"] = -1;
			$resp["msg"] = "会话异常";
			header('Content-Type: application/json; charset=utf-8'); 
			exit(json_encode($resp, JSON_UNESCAPED_UNICODE)); 			
		}
	}


	protected function checkSID() {
		$sid = input('sid');
		if (isset($sid)) {
			session_id ($sid);
			$storeSID = session('sid');
			if($storeSID ==  $sid){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	protected function checkLogin() {
		$userid = session('userid');
		if (isset($userid)) {
			return true;
		}else{
			return false;
		}
	}
	
	protected function checkJWT() {		
		$headerAuth = request()->header('Authorization');
		list($jwt) = sscanf( $headerAuth, 'Bearer %s');
		if($jwt == null){
			$jwt = input('token');
		}
		if($jwt == null){
			return false;
		}
		//print_r($jwt);
		$authInfo = decodedToken($jwt);
		return $authInfo;		
	}	
}