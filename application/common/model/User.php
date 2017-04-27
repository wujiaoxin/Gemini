<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 用户模型
*/
class User extends Base{

	protected $name = "Member";
	protected $createTime = 'reg_time';
	protected $updateTime = 'last_login_time';

	protected $type = array(
		'uid'  => 'integer',
	);
	protected $insert = array('salt', 'password', 'status', 'reg_time');
	protected $update = array();

	public $editfield = array(
		array('name'=>'uid','type'=>'hidden'),
		array('name'=>'username','title'=>'用户名','type'=>'readonly','help'=>''),
		array('name'=>'nickname','title'=>'昵称','type'=>'text','help'=>''),
		array('name'=>'mobile','title'=>'手机','type'=>'text','help'=>''),
		array('name'=>'password','title'=>'密码','type'=>'password','help'=>'为空时则不修改'),
		array('name'=>'sex','title'=>'性别','type'=>'select','option'=>array('0'=>'保密','1'=>'男','2'=>'女'),'help'=>''),
		array('name'=>'addr','title'=>'地址','type'=>'text','help'=>'地址信息，用于签约地址'),
		array('name'=>'status','title'=>'状态','type'=>'select','option'=>array('0'=>'禁用','1'=>'启用'),'help'=>''),
	);

	public $addfield = array(
		array('name'=>'username','title'=>'用户名','type'=>'text','help'=>'用户名会作为默认的昵称'),
		array('name'=>'mobile','title'=>'手机','type'=>'text','help'=>''),
		array('name'=>'password','title'=>'密码','type'=>'password','help'=>'用户密码不能少于6位'),
		array('name'=>'repassword','title'=>'确认密码','type'=>'password','help'=>'确认密码'),
		array('name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'),
	);
    
	public $useredit = array(
		array('name'=>'uid','type'=>'hidden'),
		array('name'=>'nickname','title'=>'昵称','type'=>'text','help'=>''),
		array('name'=>'sex','title'=>'性别','type'=>'select','option'=>array('0'=>'保密','1'=>'男','2'=>'女'),'help'=>''),
		array('name'=>'email','title'=>'邮箱','type'=>'text','help'=>'用户邮箱，用于找回密码等安全操作'),
		array('name'=>'mobile','title'=>'联系电话','type'=>'text','help'=>''),
		array('name'=>'addr','title'=>'地址','type'=>'text','help'=>'地址信息，用于签约地址'),
	);
/*
	public $userextend = array(
		array('name'=>'company','title'=>'单位名称','type'=>'text','help'=>''),
		array('name'=>'company_addr','title'=>'单位地址','type'=>'text','help'=>''),
		array('name'=>'company_contact','title'=>'单位联系人','type'=>'text','help'=>''),
		array('name'=>'company_zip','title'=>'单位邮编','type'=>'text','help'=>''),
		array('name'=>'company_depart','title'=>'所属部门','type'=>'text','help'=>''),
		array('name'=>'company_post','title'=>'所属职务','type'=>'text','help'=>''),
		array('name'=>'company_type','title'=>'单位类型','type'=>'select', 'option'=>'', 'help'=>''),
	);
*/
	protected function setStatusAttr($value){
		return 1;
	}

	protected function setPasswordAttr($value, $data){
		return md5($value.$data['salt']);
	}
	protected function setPaypasswordAttr($value, $data){
		return md5($value.$data['mobile']);
	}

	/**
	* 用户登录模型
	*/
	public function login($username = '', $password = '', $type = 1){
		$map = array();
		if (\think\Validate::is($username,'email')) {
			$type = 2;
		}elseif (preg_match("/^1[34578]{1}\d{9}$/",$username)) {
			$type = 3;
		}
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			case 4:
				$map['uid'] = $username;
				break;
			case 5:
				$map['uid'] = $username;
				break;
			default:
				return 0; //参数错误
		}

		$user = $this->db()->where($map)->find();
		if($user != null){
			$user = $user->toArray();
			if(is_array($user) && $user['status']){
				/* 验证用户密码 */
				if(md5($password.$user['salt']) === $user['password']){
					$this->autoLogin($user); //更新用户登录信息
					return $user['uid']; //登录成功，返回用户ID
				} else {
					return -2; //密码错误
				}
			}else{
				return -1; //用户被禁用
			}
		} else {
			return -1; //用户不存在
		}
	}

	/**
	 * 用户注册
	 * @param  integer $user 用户信息数组
	 */
	function register($username, $password, $repassword, $email, $isautologin = true){
		$data['username'] = $username;
		$data['salt'] = rand_string(6);
		$data['password'] = $password;
		$data['repassword'] = $repassword;
		$data['email'] = $email;
		$result = $this->validate(true)->save($data);
		if ($result) {
			$data['uid'] = $this->data['uid'];
			//$this->extend()->save($data);
			if ($isautologin) {
				$this->autoLogin($this->data);
			}
			return $data['uid'];
		}else{
			if (!$this->getError()) {
				$this->error = "注册失败！";
			}
			return false;
		}
	}

	/**
	 * 车商用户添加
	 * @param  integer $user 用户信息数组
	 */
	function registeraddStaff($mobile, $password, $repassword, $email, $isautologin = true){
		$data['mobile'] = $mobile;
		$data['salt'] = rand_string(6);
		$data['password'] = $password;
		$data['repassword'] = $repassword;
		$data['username'] = '1';
		$data['email'] = $email;
		$result = $this->validate(true)->save($data);
		if ($result) {
			$data['uid'] = $this->data['uid'];
			//$this->extend()->save($data);
			if ($isautologin) {
				$this->autoLogin($this->data);
			}
			return $data['uid'];
		}else{
			if (!$this->getError()) {
				$this->error = "注册失败！";
			}
			return false;
		}
	}
	/**
	 * 用户手机注册
	 * @param  integer $user 用户信息数组
	 */
	function registerByMobile($mobile, $password){
		$data['username'] = $mobile;
		$data['salt'] = rand_string(6);		
		$data['password'] = $password;
		$data['mobile'] = $mobile;
		$user['uid'] = '';
		$result = $this->validate(true)->save($data);
		if ($result) {
			$user['uid'] = $this->uid;
			$this->autoLogin($user);
			return $this->uid;
		}else{
			if (!$this->getError()) {
				$this->error = "注册失败！";
			}
			return false;
		}
	}

	/**
	 * 自动登录用户
	 * @param  integer $user 用户信息数组
	 */
	private function autoLogin($user){
		/* 更新登录信息 */
		$data = array(
			'uid'             => $user['uid'],
			'login'           => array('exp', '`login`+1'),
			'last_login_time' => time(),
			'last_login_ip'   => get_client_ip(1),
		);
		$this->where(array('uid'=>$user['uid']))->update($data);
		$user = $this->where(array('uid'=>$user['uid']))->find();

		/* 记录登录SESSION和COOKIES */
		$auth = array(
			'uid'             => $user['uid'],
			'username'        => $user['username'],
			'role'            => $user['access_group_id'],
			'last_login_time' => $user['last_login_time'],
		);

		session('user_auth', $auth);
		session('user_auth_sign', data_auth_sign($auth));
	}
	
	public function bindWechat($uid){
		$data['uid'] = $uid;
		$openid = session('user_openid');
		if(!empty($openid)){
			$empty['openid'] = null;			
			$this->where(array('openid' => $openid))->update($empty);//自动解除之前绑定关系
			$data['openid'] = $openid;
			$this->where(array('uid' => $uid))->update($data);
		}		
	}

	public function logout(){
		session('user_auth', null);
		session('user_auth_sign', null);
	}

	public function getInfo($uid){
		$data = $this->where(array('uid'=>$uid))->find();
		if ($data) {
			return $data->toArray();
		}else{
			return false;
		}
	}

	/**
	 * 修改用户资料
	 */
	public function editUser($data, $ischangepwd = false){
		if ($data['uid']) {
			if (!$ischangepwd || ($ischangepwd && $data['password'] == '')) {
				unset($data['salt']);
				unset($data['password']);
			}else{
				$data['salt'] = rand_string(6);
			}
			$result = $this->validate('member.edit')->save($data, array('uid'=>$data['uid']));
			if ($result) {
				return true;
				//return $this->extend->save($data, array('uid'=>$data['uid']));
			}else{
				return false;
			}
		}else{
			$this->error = "非法操作！";
			return false;
		}
	}

	public function editpw($data, $is_reset = false){
		$uid = $is_reset ? $data['uid'] : session('user_auth.uid');
		if (!$is_reset) {
			
			//后台修改用户时可修改用户密码时设置为true
			$result = $this->checkPassword($uid,$data['oldpassword']);
			if(!$result){
				return false;
			}

			$validate = $this->validate('member.password');
			if (false === $validate) {
				return false;
			}
		}

		$data['salt'] = rand_string(6);

		return $this->save($data, array('uid'=>$uid));
	}
	
	public function resetpw($mobile,$passwd){
		$validate = $this->validate('member.password');
		if (false === $validate) {
			return false;
		}
		$data['password'] = $passwd;
		$data['salt'] = rand_string(6);
		return $this->save($data, array('mobile'=>$mobile));
	}

	public function setpaypw($mobile,$passwd){
		$data['paypassword'] = $passwd;
		$data['mobile'] = $mobile;
		return $this->save($data, array('mobile'=>$mobile));
	}
	public function editpaypw($data, $is_reset = false){
		$uid = $is_reset ? $data['uid'] : session('user_auth.uid');
		if (!$is_reset) {
			
			//后台修改用户时可修改用户密码时设置为true
			$result = $this->checkPaypassword($uid,$data['oldpaypassword']);
			if(!$result){
				return false;
			}

			$validate = $this->validate('member.paypassword');
			if (false === $validate) {
				return false;
			}
		}

		$data['salt'] = rand_string(6);

		return $this->save($data, array('uid'=>$uid));
	}

	protected function checkPassword($uid,$password){
		
		if (!$uid || !$password) {
			$this->error = '原始用户UID和密码不能为空';
			return false;
		}	
		$user = $this->where(array('uid'=>$uid))->find();
		if (md5($password.$user['salt']) === $user['password']) {
			return true;
		}else{
			$this->error = '原始密码错误！';
			return false;
		}
	}
	protected function checkPaypassword($uid,$paypassword){
		
		if (!$uid || !$password) {
			$this->error = '原始用户UID和密码不能为空';
			return false;
		}	
		$user = $this->where(array('uid'=>$uid))->find();
		if (md5($password.$user['password']) === $user['paypassword']) {
			return true;
		}else{
			$this->error = '原始密码错误！';
			return false;
		}
	}
	/*
	protected function getUid($mobile){
		
		if (!$mobile) {
			$this->error = '手机号不能为空';
			return false;
		}	
		$user = $this->where(array('mobile'=>$mobile))->find();
		if($user){
			return $user['uid'];
		}else{
			$this->error = '用户不存在';
			return false;
		}

	}
	*/
/*
	public function extend(){
		return $this->hasOne('MemberExtend', 'uid');
	}
*/
}