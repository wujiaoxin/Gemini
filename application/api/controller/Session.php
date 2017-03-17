<?php

namespace app\api\controller;

class Session extends \think\Controller {
	
	public function create($client = null) {
		if($client == null){
			$resp["code"] = 0;
			$resp["msg"] = "未知客户端";
			return json($resp);
		}
		session('createTime', time());
		session('client', $client);
		$id = session_id ();
		session('sid', $id);
		$resp["code"] = 1;
		$resp["msg"] = "创建成功";
		$data["sid"] = $id;
		$data["expire"] = 86400;
		$resp["data"] = $data;
		return json($resp);
	}
	
	
	public function check($sid) {
		$result = false;
		if (isset($sid)) {
			session_id ($sid);
			$storeSID = session('sid');
			if($storeSID == $sid){
				$result = true;
			}
		}
		if($result == true){
			$resp["code"] = 1;
			$resp["msg"] = "会话正常";
			$data["sid"] = $sid;
			$data["createTime"] = session('createTime');
			$resp["data"] = $data;
		}else{
			$resp["code"] = 0;
			$resp["msg"] = "sid无效";
			$data["sid"] = $sid;
			$resp["data"] = $data;
		}
		return json($resp);
	}

}

