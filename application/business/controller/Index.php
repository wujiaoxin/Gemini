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

class Index extends Base {
	public function index() {
		$uid = session("uid");
		if($uid == null){
			return $this->error("请先登录",url("/business/user/login"));
		}
		return $this->fetch();
	}
}