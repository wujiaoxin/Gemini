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
		array('name'=>'sn' ,'title'=>'订单编号', 'type'=>'hidden'),
		array('name'=>'bank_uid' ,'title'=>'审核银行', 'type'=>'hidden', 'value'=>'9'),
		array('name'=>'type' ,'title'=>'订单类别', 'type'=>'select', 'option'=>array(
			'1' => '新车垫资',
			'2' => '二手车垫资',
			'3' => '车抵贷',
			'4' => '其他订单',
		), 'help'=>''),
		array('name'=>'name' ,'title'=>'贷款人姓名', 'type'=>'text', 'help'=>''),
		array('name'=>'idcard_num' ,'title'=>'贷款人身份证号', 'type'=>'text', 'help'=>''),
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
	
	public function build_order_sn(){
		return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}
	
	
	public function get_order_list($uid = 0, $role = 0, $type = 0, $status = null){
		$filter['auth_uid'] = $uid;
		$filter['auth_role'] = $role;
		if($type == 3){
			$filter['type'] = $type;
		}else{
			$filter['type'] =['<',3];
		}
		if($status == null){
			$filter['status'] = ['>',-1];
		}else{
			$filter['status'] = $status;
		}
		//$filter['status'] = ['>',-1];
		$sort = "id desc";
		$list = db('OrderAuth')->alias('a')->join('Order b','a.order_id = b.id','LEFT')->where($filter)->order($sort)->paginate(15);
		return $list;
	}
	public function get_all_order_list($uid = 0, $role = 0, $status = null){
		$filter['auth_uid'] = $uid;
		$filter['auth_role'] = $role;
		if($status == null){
			$filter['status'] = ['>',-1];
		}else{
			$filter['status'] = $status;
		}
		//$filter['status'] = ['>',-1];
		$sort = "id desc";
		$list = db('OrderAuth')->alias('a')->join('Order b','a.order_id = b.id','LEFT')->where($filter)->order($sort)->paginate(15);
		return $list;
	}
	
	
	/*
	public function extend(){
		return $this->hasOne('OrderExtend', 'uid');
	}
	*/
	
}