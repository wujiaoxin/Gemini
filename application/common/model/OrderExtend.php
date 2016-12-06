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
 * 订单扩展信息类
 */
class OrderExtend extends \app\common\model\Base {


	public $keyList = array(
		array('name'=>'id' ,'title'=>'ID', 'type'=>'hidden'),
		array('name'=>'uid' ,'title'=>'报单人用户ID', 'type'=>'hidden'),
		array('name'=>'bank_uid' ,'title'=>'银行审核人员ID', 'type'=>'hidden', 'value'=>'9'),
		array('name'=>'car_brand' ,'title'=>'车辆品牌', 'type'=>'text', 'help'=>''),
		array('name'=>'car_model' ,'title'=>'车辆车型', 'type'=>'text', 'help'=>''),
		array('name'=>'car_color' ,'title'=>'车辆颜色', 'type'=>'text', 'help'=>''),
		array('name'=>'car_VIN' ,'title'=>'车架号', 'type'=>'text', 'help'=>''),
		array('name'=>'insurance_comp' ,'title'=>'保险公司', 'type'=>'text', 'help'=>''),
		array('name'=>'insurance_id' ,'title'=>'保险单号', 'type'=>'text', 'help'=>''),
		array('name'=>'loan_amounts' ,'title'=>'垫资额度', 'type'=>'text', 'help'=>''),
		array('name'=>'purchase_time' ,'title'=>'购车时间', 'type'=>'text', 'help'=>''),
		array('name'=>'purchase_amount' ,'title'=>'购车金额', 'type'=>'text', 'help'=>''),
		array('name'=>'extend_pic_1' ,'title'=>'首付凭证', 'type'=>'image', 'help'=>''),
		array('name'=>'extend_pic_2' ,'title'=>'购车发票', 'type'=>'image', 'help'=>''),
		array('name'=>'extend_pic_3' ,'title'=>'银行放款通知书', 'type'=>'image', 'help'=>''),
		array('name'=>'extend_pic_4' ,'title'=>'车辆合格证', 'type'=>'image', 'help'=>''),
		array('name'=>'extend_pic_5' ,'title'=>'车辆保单', 'type'=>'image', 'help'=>''),
		array('name'=>'descr' ,'title'=>'备注信息', 'type'=>'textarea', 'help'=>'')
	);
	
    protected $auto = array('update_time');

	protected $type = array(
		'extend_pic_1'  => 'integer',
		'extend_pic_2'  => 'integer',
		'extend_pic_3'  => 'integer',		
		'extend_pic_4'  => 'integer',
		'extend_pic_5'  => 'integer',
	);
	/**
	 * 审核通过添加附属信息单号
	 * @param  integer $id order_id
	 */
	function addByBank($id){
		$orderExtendData = $this->find($id);
		if($orderExtendData == null){			
			$order = model('Order');
			$orderData = $order->find($id);			
			$data['id'] = $id;
			$data['uid'] = $orderData['uid'];	
			$data['bank_uid'] =  $orderData['bank_uid'];
			$result = $this->save($data);
			if ($result) {
				return $id;
			}else{
				if (!$this->getError()) {
					$this->error = "提交失败!！";
				}
				return false;
			}
		}else{
			return $id;
		}
	}
	
}