<?php

namespace app\common\model;

/**
 * 订单权限控制类
 */
class OrderAuth extends \app\common\model\Base {

    protected $auto = array('update_time');

	/**
	 * 添加审核权限
	 * @param  integer $order_id $uid $role
	 */
	function addAuth($order_id = 0, $uid = 0, $role = 0){
		$data['order_id'] = $order_id;
		$data['auth_uid'] = $uid;
		$data['auth_role'] = $role;
		//->where('status',1)
		$orderAuthData = $this->where($data)->find();
		$result = true;
		if($orderAuthData == null){
			$result = $this->isUpdate(false)->save($data);
		}
		return $result;
	}
	

	
}