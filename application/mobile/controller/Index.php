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
	}
	public function index() {
		$welcomeText='请登录';		
		if (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$welcomeText = $user['nickname'];	
			if(!empty($user['openid'])){
				$wechatInfo = db('MemberWechat')->field('nickname,headimgurl')->where('openid',$user['openid'])->find();
				if($wechatInfo!=null){					
					$user['nickname'] = $wechatInfo['nickname'];
					$user['headimgurl'] = $wechatInfo['headimgurl'];
					$welcomeText = $user['nickname'];
				}
			}		
			$this->assign('user', $user);
		}
		$this->assign('welcomeText', $welcomeText);
		return $this->fetch();
	}
	public function indexDealer() {	
		return $this->fetch('indexDealer');
	}
}
