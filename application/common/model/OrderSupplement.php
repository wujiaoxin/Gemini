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
class OrderSupplement extends \app\common\model\Base {

	public $keyList = array(
		array('name'=>'id' ,'title'=>'ID', 'type'=>'hidden'),
		array('name'=>'uid' ,'title'=>'报单人用户ID', 'type'=>'hidden'),
		array('name'=>'bank_uid' ,'title'=>'银行审核人员ID', 'type'=>'hidden', 'value'=>'9'),
		array('name'=>'supplement_pic_1' ,'title'=>'近6个月银行流水', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_2' ,'title'=>'工作证明', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_3' ,'title'=>'企业营业执照', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_4' ,'title'=>'房产证编号页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_5' ,'title'=>'房产证详细内容页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_6' ,'title'=>'结婚证机关签章页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_7' ,'title'=>'结婚证详细内容页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_8' ,'title'=>'户口本户主页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_9' ,'title'=>'户口本个人详细页', 'type'=>'image', 'help'=>''),
		array('name'=>'supplement_pic_10' ,'title'=>'其他材料', 'type'=>'image', 'help'=>''),
		array('name'=>'status' ,'title'=>'补充资料审核状态', 'type'=>'select','option'=>array('1'=>'审核通过','0'=>'待审核','2'=>'审核未通过',), 'help'=>''),
		array('name'=>'descr' ,'title'=>'备注信息', 'type'=>'textarea', 'help'=>'')
	);

    protected $auto = array('update_time');

	protected $type = array(
		'supplement_pic_1'  => 'integer',
		'supplement_pic_2'  => 'integer',
		'supplement_pic_3'  => 'integer',		
		'supplement_pic_4'  => 'integer',
		'supplement_pic_5'  => 'integer',
		'supplement_pic_6'  => 'integer',
		'supplement_pic_7'  => 'integer',
		'supplement_pic_8'  => 'integer',
		'supplement_pic_9'  => 'integer',
		'supplement_pic_10'  => 'integer',
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