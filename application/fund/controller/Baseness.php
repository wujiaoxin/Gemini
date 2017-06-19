<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\fund\controller;
use app\common\controller\Base;
class Baseness extends base{
	public function _initialize(){
		parent::_initialize();
		$mobile = session("business_mobile");
		$uid = session("user_auth.uid");
		$role = session("user_auth.role");
		if($mobile == null || $uid == null){
			return $this->redirect("/business/login/login");
		}
		$result = db('dealer')->field('status')->where('mobile',$mobile)->find();

		if ($result['status'] == '3') {
			return $this->redirect('/guarantee/login/waiting');
		}
		switch ($role) {
			case '14':
				$action   = CONTROLLER_NAME;
				if ($action != 'dataReview') {
					return $this->error('没有访问权限');
				}
				break;
			case '15':
				$action   = CONTROLLER_NAME;
				if ($action != 'Index') {
					return $this->error('没有访问权限');
				}
				break;
			case '16':
				$action   = ACTION_NAME;
				if ($action == 'finance' || $action == 'repayItem'|| $action == 'payItem') {
					return view();
				}else{
					return $this->error('没有访问权限');
				}
				break;
			case '17':
				$action   = CONTROLLER_NAME;
				if ($action != 'Index') {
					return $this->error('没有访问权限');
				}
				break;
			default:
				# code...
				break;
		}
	}
	protected function setMeta($title = '') {
		$this->assign('meta_title', $title);
	}
}