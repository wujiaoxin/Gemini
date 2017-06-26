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
		$action   = CONTROLLER_NAME;
		$res = ACTION_NAME;
		switch ($role) {
			case '14':
				if ($action == 'User' || $action == 'Index' ||$action == 'Account'  || $res == 'application' || $res == 'loanlimit' || $res == 'finance') {
					return $this->success('无权限操作','examine/creditReview');
				}
				break;
			case '15':

				if ( $action == 'User' || $action == 'Index' ||$action == 'Account'  || $res == 'application' || $res == 'creditreview' || $res == 'finance') {
					return $this->success('无权限操作','examine/loanLimit');
				}
				break;
			case '16':
				if ( $action == 'Index' ||$action == 'Account'  || $res == 'application' || $res == 'creditreview' || $res == 'loanlimit') {
					return $this->success('无权限操作','examine/finance');
				}
				break;
			case '17':
				if ( $action == 'Index' ||$action == 'Account'  || $action == 'Examine' || $res == 'loanitem' || $res == 'repayitem' || $res == 'mystaff' || $res == 'myshop') {
					return $this->success('无权限操作','user/myChannel');
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