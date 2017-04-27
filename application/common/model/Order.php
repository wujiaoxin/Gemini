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
			if ($status == 3) {
				$name = '3,4';
				$filter['status'] = array('IN',$name);
			}else{
				$filter['status'] = $status;
			}
		}
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
	
	//订单统计
	public function get_all_order_total($uid = 0, $type = null, $status = null){

		$filter['uid'] = $uid;
		/*if($type == null){
			$filter['type'] =['<',3];
		}else{
			$filter['type'] = $type;
		}*/
		if($status == null){
			$filter['status'] = ['>',-1];
		}else{
			if ($status == 3) {
				$name = '3,4';
				$filter['status'] = array('IN',$name);
			}else{
				$filter['status'] = $status;
			}
		}
		$total = '';
		$filter['credit_status'] = '3';
		$total['order_num'] = db('Order')->where($filter)->count();
		$ord = db('Order')->field('sum(loan_limit) as loan_limit')->where($filter)->find();
		$total['loan_limit'] = $ord['loan_limit'];
		return $total;
	}

	//添加订单
	public function add_order($uid, $data){

		unset($data['type']);

		$dealer_mobile = db('member')->alias('m')->field('d.mobile')->join('dealer d','d.id = m.dealer_id')->where('m.uid',$uid)->find();

		$mid = db('member')->field('uid')->where('mobile',$dealer_mobile['mobile'])->find();

		$forms = db('dealer')->field('forms')->where('mobile',$dealer_mobile['mobile'])->find();
		$is_order = db('order')->field('mobile')->where('mobile',$data['mobile'])->find();
		// var_dump($is_order);die;

		if (empty($forms['forms'])) {

			$forms['forms'] = '1';
		}

		if (isset($is_order)) {

			if ($is_order['mobile'] == $data['mobile']) {
				
				$data['car_price'] = $data['price'];
				$result = $this->allowField(true)->save($data,['mobile'=>$data['mobile']]);
				return 111;
			}

		}else{
			$order_sn = $this->build_order_sn();

			$data =array(
				'uid'=>$uid,
				'mid'=>$mid['uid'],
				'mobile'=>$data['mobile'],
				'car_price'=>$data['price'],
				'sn' =>$order_sn,
				'status'=>-2,
				'type' =>$forms['forms']
				);

			$result = $this->allowField(true)->save($data);
		}
		
		return $this->id;
	}
	//保存订单
	public function save_order($uid, $data){
		$data =array(
			'loan_limit' => $data['loan_limit'],
			'endtime' => $data['loan_term'],
			'status'=>'3'
			);
		$data['id'] = $uid;
		$result = $this->save($data,['id'=>$data['id']]);
		return $result;
	}

	// 客户订单查询

	public function search_order($id){

		$info = db('member')->alias('m')->field('m.bankcard,m.mobile,o.name,o.idcard_face_pic,o.idcard_back_pic')->join('__ORDER__ o','m.mobile = o.mobile')->where('m.uid',$id)->find();
		// var_dump($info);die;


		$mobile = db('member')->field('mobile')->where('uid',$id)->find();
		$orders = db('Order')->alias('o')->field('o.*')->join('__MEMBER__ m','m.mobile = o.mobile')->where('o.mobile',$mobile['mobile'])->select();

		foreach ($orders as $k => $v) {
			$status  = db('order_repay')->field('status,has_repay')->where('order_id', $v['sn'])->find();
			$orders[$k]['repay_status'] = $status['has_repay'];
		}
		$nums = db('order')->where('mobile',$mobile['mobile'])->count();

		$map = array(
			'status' => '1',
			'mobile'=>$mobile['mobile']
			);
		$num_repay = db('order')->where($map)->count();

		$num_repay = array(
				'nums'=> $nums,
				'num_success'=>$num_repay
			);
		$result = array(
				'info' => $info,
				'orders' => $orders,
				'nums' =>$num_repay
			);
		return $result;
	}

}