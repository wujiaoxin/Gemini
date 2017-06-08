<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\guarantee\controller;
use app\common\controller\Base;
class Login extends Base {
	
	public function logout(){
		$resp["code"] = 1;
		$resp["msg"] = "登出成功！";
		session(null);
		return $resp;
	}
	public function waiting(){
		$mobile = session('business_mobile');
		$status = db('dealer')->field('status')->where('mobile',$mobile)->find();
		if ($status['status'] == '1') {
			$this->redirect(url('index/index'));
		}else{
			db('dealer')->where('mobile',$mobile)->setField('status','3');
		}
		return $this->fetch();
	}

	public function protocal(){
		return $this->fetch();
	}
}