<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\business\controller\Baseness;

class Index extends Baseness {
	public function index() {
		$uid = session("uid");
		$mobile =session('mobile');
		if($uid == null){
			return $this->error("请先登录",url("/business/user/login"));
		}
		$is_success = db('Dealer')->field('credit_code')->where('mobile',$mobile)->find();
		if(!$is_success['credit_code']){
			$this->redirect('/business/user/guide');
		}
		return $this->fetch();
	}
}