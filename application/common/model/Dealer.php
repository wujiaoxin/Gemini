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
 * 经销商类
 * @author 
 */
class Dealer extends \app\common\model\Base {
	public $keyList = array(
		array('name'=>'id' ,'title'=>'ID', 'type'=>'hidden'),
		array('name'=>'name' ,'title'=>'企业名称', 'type'=>'text'),
		array('name'=>'invite_code' ,'title'=>'邀请码', 'type'=>'hidden'),
		array('name'=>'property' ,'title'=>'属性', 'type'=>'text', 'help'=>''),
		array('name'=>'city' ,'title'=>'城市', 'type'=>'text', 'help'=>''),
		array('name'=>'addr' ,'title'=>'地址', 'type'=>'text', 'help'=>''),
		array('name'=>'credit_code' ,'title'=>'企业信用代码', 'type'=>'text', 'help'=>''),
		array('name'=>'rep' ,'title'=>'法人代表', 'type'=>'text', 'help'=>''),
		array('name'=>'contacts' ,'title'=>'业务联系人', 'type'=>'text', 'help'=>''),
		array('name'=>'mobile' ,'title'=>'手机号', 'type'=>'text', 'help'=>''),
		array('name'=>'duties' ,'title'=>'职务', 'type'=>'text', 'help'=>''),
		array('name'=>'mail' ,'title'=>'邮箱', 'type'=>'text', 'help'=>''),
		array('name'=>'banks' ,'title'=>'目前合作金融机构', 'type'=>'text', 'help'=>''),
		array('name'=>'bank_name' ,'title'=>'开户银行', 'type'=>'text', 'help'=>''),
		array('name'=>'bank_branch' ,'title'=>'开户网点', 'type'=>'text', 'help'=>''),
		array('name'=>'bank_account_name' ,'title'=>'开户名', 'type'=>'text', 'help'=>''),
		array('name'=>'bank_account_id' ,'title'=>'开户账号', 'type'=>'text', 'help'=>''),
		array('name'=>'priv_bank_name' ,'title'=>'开户银行(私户)', 'type'=>'text', 'help'=>''),
		array('name'=>'priv_bank_branch' ,'title'=>'开户网点(私户)', 'type'=>'text', 'help'=>''),
		array('name'=>'priv_bank_account_name' ,'title'=>'开户名(私户)', 'type'=>'text', 'help'=>''),
		array('name'=>'priv_bank_account_id' ,'title'=>'开户账号(私户)', 'type'=>'text', 'help'=>''),
		array('name'=>'forms' ,'title'=>'合作形式', 'type'=>'select', 'option'=>array(
			'1' => '新车分期',
			'2' => '二手车分期',
			'3' => '融资租赁',
			'4' => '库存融资',
			'5' => '供应链金融'
		), 'help'=>''),
		array('name'=>'sales' ,'title'=>'月销量', 'type'=>'text', 'help'=>''),
		array('name'=>'ratio' ,'title'=>'分期比例', 'type'=>'text', 'help'=>''),
		array('name'=>'dealer_lic_pic' ,'title'=>'营业执照照片', 'type'=>'image', 'help'=>''),
		array('name'=>'rep_idcard_pic' ,'title'=>'法人身份证', 'type'=>'image', 'help'=>''),
		array('name'=>'contacts_pic' ,'title'=>'联系人名片', 'type'=>'image', 'help'=>'加盖公章'),
		array('name'=>'info_pic' ,'title'=>'信息表照片', 'type'=>'image', 'help'=>''),		
		array('name'=>'descr' ,'title'=>'备注信息', 'type'=>'textarea', 'help'=>'')
	);

	protected $type = array(
		'dealer_lic_pic'  => 'integer',
		'rep_idcard_pic'  => 'integer',
		'contacts_pic'  => 'integer',
		'info_pic'  => 'integer'
	);
	
	public function buildInviteCode() { 
		$randStr = str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ123456789');
		$rand = substr($randStr,0,6);
		$Dealer = db('Dealer');
		$result = $Dealer->where('invite_code',$rand)->find();
		if($result == null){
			return $rand;
		}else{
			return $this->buildInviteCode();
		}
	}
}