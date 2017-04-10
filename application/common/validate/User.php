<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
* 设置模型
*/
class Member extends Base{

	protected $rule = array(
		'username'   => 'require|unique:member|/^[a-zA-Z0-9]\w{0,39}$/',
		'email'      => 'unique:member|email',
		'mobile'     => 'unique:member',
		'password'   => 'require',
		'repassword' => 'confirm:password'
		'paypassword'   => 'require',
		'repaypassword' => 'confirm:paypassword'
	);
	protected $message = array(
		'username.require'    => '用户名必须',
		'username.unique'    => '用户名已存在',
		'email.require'    => '邮箱必须',
		'email.unique'    => '邮箱已存在',
		'mobile.unique'    => '手机号已存在',
		'password.require' => '密码必须',
		'repassword.require'    => '确认密码和密码必须一致',
		'paypassword.require' => '支付密码必须',
		'repaypassword.require'    => '确认支付密码和支付密码必须一致',
	);
	protected $scene = array(
		'edit'     => 'email,mobile',
		'password' => 'password,repassword'
		'paypassword' => 'paypassword,repaypassword'
	);

}