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
 * 订单类
 * @author molong <molong@tensent.cn>
 */
class Order extends \app\common\model\Base {
	//protected $name = 'link';

	public $keyList = array(
		array('name'=>'id' ,'title'=>'ID', 'type'=>'hidden'),
		array('name'=>'bank_uid' ,'title'=>'审核银行', 'type'=>'hidden', 'value'=>'9'),
		array('name'=>'type' ,'title'=>'订单类别', 'type'=>'select', 'option'=>array(
			'1' => '新车垫资',
			'2' => '二手车垫资',
			'3' => '车抵贷',
			'4' => '其他订单',
		), 'help'=>''),
		array('name'=>'name' ,'title'=>'贷款人姓名', 'type'=>'text', 'help'=>''),
		array('name'=>'loan_limit' ,'title'=>'贷款额度', 'type'=>'text', 'help'=>''),
		array('name'=>'idcard_face_pic' ,'title'=>'身份证正面', 'type'=>'image', 'help'=>''),
		array('name'=>'idcard_back_pic' ,'title'=>'身份证反面', 'type'=>'image', 'help'=>''),
		array('name'=>'driving_lic_pic' ,'title'=>'驾驶证照片', 'type'=>'image', 'help'=>''),
		array('name'=>'status' ,'title'=>'状态', 'type'=>'select','option'=>array('1'=>'审核通过','0'=>'待审核','2'=>'审核未通过',), 'help'=>''),
		array('name'=>'addr' ,'title'=>'签单地址', 'type'=>'text', 'help'=>''),
		array('name'=>'descr' ,'title'=>'备注信息', 'type'=>'textarea', 'help'=>'')
	);

    protected $auto = array('update_time');

	protected $type = array(
		'idcard_face_pic'  => 'integer',
		'idcard_back_pic'  => 'integer',
		'driving_lic_pic'  => 'integer',
	);
}