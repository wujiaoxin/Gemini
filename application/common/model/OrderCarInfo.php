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
 * 二手车订单车辆信息类
 * @author molong <molong@tensent.cn>
 */
class OrderCarInfo extends \app\common\model\Base {

	public $keyList = array(
		array('name'=>'id' ,'title'=>'ID', 'type'=>'hidden'),
		array('name'=>'uid' ,'title'=>'报单人用户ID', 'type'=>'hidden'),
		array('name'=>'bank_uid' ,'title'=>'银行审核人员ID', 'type'=>'hidden', 'value'=>'9'),
		array('name'=>'car_pic_1' ,'title'=>'车头含车牌全车照', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_2' ,'title'=>'车尾含车牌全车照', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_3' ,'title'=>'引擎盖打开发动机舱', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_4' ,'title'=>'侧面全车照', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_5' ,'title'=>'铭牌', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_6' ,'title'=>'风挡VIN', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_7' ,'title'=>'内饰', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_8' ,'title'=>'仪表盘含公里数', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_9' ,'title'=>'车辆登记证1', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_10' ,'title'=>'车辆登记证2', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_11' ,'title'=>'行驶证正副本', 'type'=>'image', 'help'=>''),
		array('name'=>'car_pic_12' ,'title'=>'车辆视频', 'type'=>'video', 'help'=>''),
		// array('name'=>'car_pic_13' ,'title'=>'车辆照片13', 'type'=>'image', 'help'=>''),
		// array('name'=>'car_pic_14' ,'title'=>'车辆照片14', 'type'=>'image', 'help'=>''),
		// array('name'=>'car_pic_15' ,'title'=>'车辆照片15', 'type'=>'image', 'help'=>''),
		array('name'=>'status' ,'title'=>'补充资料审核状态', 'type'=>'select','option'=>array('1'=>'审核通过','0'=>'待审核','2'=>'审核未通过',), 'help'=>''),
		array('name'=>'descr' ,'title'=>'备注信息', 'type'=>'textarea', 'help'=>'')
	);

    protected $auto = array('update_time');

	protected $type = array(
		'car_pic_1'  => 'integer',
		'car_pic_2'  => 'integer',
		'car_pic_3'  => 'integer',		
		'car_pic_4'  => 'integer',
		'car_pic_5'  => 'integer',
		'car_pic_6'  => 'integer',
		'car_pic_7'  => 'integer',
		'car_pic_8'  => 'integer',
		'car_pic_9'  => 'integer',
		'car_pic_10'  => 'integer',
		'car_pic_11'  => 'integer',
		'car_pic_12'  => 'integer',
		'car_pic_13'  => 'integer',
		'car_pic_14'  => 'integer',
		'car_pic_15'  => 'integer',
	);
	/**
	 * 审核通过添加附属信息单号
	 * @param  integer $id order_id
	 */
	function addByBank($id){
		$orderSupplementData = $this->find($id);
		if($orderSupplementData == null){			
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