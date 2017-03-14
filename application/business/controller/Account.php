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

class Account extends Base {
	public function index() {
		return $this->fetch();
	}
	
	public function cashValue() {
		return $this->fetch();
	}

	public function cash() {
	  return $this->fetch();
	}

  public function value() {
    return $this->fetch();
  }

  public function banks() {
    return $this->fetch();
  }
  public function fundlist() {
    return $this->fetch();
  }
  public function lineOfCredit() {
    return $this->fetch();
  }
  public function infor() {
    return $this->fetch();
  }
  public function message() {
    return $this->fetch();
  }
}