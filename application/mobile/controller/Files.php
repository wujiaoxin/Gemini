<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\mobile\controller;
use app\common\controller\Base;

class Files extends Base {

	public function _empty() {
		$controller = controller('common/Files');
		$action     = ACTION_NAME;
		return $controller->$action();
	}
	
	
}