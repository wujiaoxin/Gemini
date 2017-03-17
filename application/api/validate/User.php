<?php
namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'mobile'     => 'unique:member',
        'password'   => 'require',
		'smsverify'  => 'require',
    ];
	protected $message = array(
		'smsverify.require'    => '密码必须',
		'mobile.unique'    => '手机号已存在',
		'password.require' => '密码必须',
	);
}
