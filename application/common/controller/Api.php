<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

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
		
		//检查SID
		if($this->checkSID() == false ){
			$resp["code"] = 100;
			$resp["msg"] = "sid无效";
			return json($resp);
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

	
}