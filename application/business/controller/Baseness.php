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
		$uid = session("user_auth.uid");
		if($mobile == null || $uid == null){
			return $this->redirect("/business/login/login");
		}
		$result = db('dealer')->field('status')->where('mobile',$mobile)->find();
		
		if ($result['status'] == '3') {
			return $this->redirect('/business/login/waiting');
		}
	}
}