<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\common\controller\Base;
class Baseness extends base{
	public function _initialize(){
		parent::_initialize();
		$mobile = session("mobile");
		$uid = session("uid");
		if($mobile == null || $uid == null){
//			return $this->error("请重新登录",url("/business/login/login"));
			return $this->redirect("/business/login/login");
		}
		$is_success = db('Dealer')->field('idno')->where('mobile',$mobile)->find();
		if ($_SERVER['REDIRECT_URL'] != '/business/user/guide.html'){
			if(!$is_success){
				$this->redirect('user/guide');
			}
		}

	}
}