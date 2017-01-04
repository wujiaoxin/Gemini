<?php

namespace app\common\model;

class MemberWechat extends \think\Model{

	protected $param;
	public function initialize(){
		parent::initialize();
		$this->param = \think\Request::instance()->param();
	}
	
	public function loginByOpenid($openid = null){
		if($openid == null){
			return -1;
		}
		$user = db('Member')->where('openid',$openid)->find();
		if($user != null){
			//$user = $user->toArray();
			if(is_array($user) && $user['status']){
				// 更新登录信息 
				$data = array(
					'uid'             => $user['uid'],
					'login'           => array('exp', '`login`+1'),
					'last_login_time' => time(),
					'last_login_ip'   => get_client_ip(1),
				);
				db('Member')->where(array('uid'=>$user['uid']))->update($data);
				// 记录登录SESSION和COOKIES 
				$auth = array(
					'uid'             => $user['uid'],
					'username'        => $user['username'],
					'role'            => $user['access_group_id'],
					'last_login_time' => $user['last_login_time'],
				);
				session('user_auth', $auth);
				session('user_auth_sign', data_auth_sign($auth));
				return 1;
			}else{
				return -1; //用户被禁用
			}
		} else {
			return 0; //openid用户不存在
		}
	}

}