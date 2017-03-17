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

class User extends Base {
	public function login() {
		return $this->fetch();
	}

	public function guide() {
		return $this->fetch();
	}

	public function myStaff() {
		return $this->fetch();
	}

	public function newStaff() {
		return $this->fetch();
	}

	public function myShop() {
		return $this->fetch();
	}

	public function loanItem() {
		return $this->fetch();
	}

	public function repayItem() {
		return $this->fetch();
	}

	public function payItem() {
		return $this->fetch();
	}
}