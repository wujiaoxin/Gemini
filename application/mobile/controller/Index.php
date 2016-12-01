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

class Index extends Base {
	
	public function _initialize() {
		parent::_initialize();
		if (!is_login() and !in_array($this->url, array('mobile/index/index'))) {
			$this->redirect('mobile/user/login');exit();
		}		
		if (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}
	public function index() {
		$welcomeText='请登录';
		if (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$welcomeText = $user['nickname'];
		}
		$this->assign('welcomeText', $welcomeText);
		return $this->fetch();
	}	
}
