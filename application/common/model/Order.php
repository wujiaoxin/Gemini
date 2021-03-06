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
		if ($role == '0') {
			$ids = db('member')->field('mobile')->where('uid',$uid)->find();
			$map  = '(mobile = '.$ids['mobile'].')';
		}elseif($role == '7'){
			$map = '(uid = '.$uid.' and mid = '.$uid.')';
		}else{
			$map = '(uid = '.$uid.')';
		}
		if($type == 3){
			
			$map = $map.' and type ='.$type;
		}else{
			$map = $map.' and type < 5 ';
		}
		if($status == null){
			
			$map = $map.' and status > -1';
		}else{

			if ($status == 3) {
				
				$map = $map.' and (status in (3,4,11,12,13) or finance = 2)';

			}elseif ($status == 1) 
			{
				$map = $map.' and finance  in (3,4) ';
				
			}else{
				$map = $map.' and status ='.(int)$status;
			}
		}
		$sort = "id desc";
		$map .= ' and credit_status = 3';

		$list  = db('Order')->where($map)->order($sort)->paginate(15);

		// $list = db('OrderAuth')->alias('a')->join('Order b','a.order_id = b.id','LEFT')->where($filter)->order($sort)->paginate(15);
		return $list;
	}
	public function get_all_order_list($uid = 0, $role = 0, $status = null){

		if ($role == '0') {
			$ids = db('member')->field('mobile')->where('uid',$uid)->find();
			$filter['mobile'] = $ids['mobile'];
		}else{
			$filter['uid'] = $uid;
		}
		if($status == null){
			$filter['status'] = ['>',-1];
		}else{
			$filter['status'] = $status;
		}
		//$filter['status'] = ['>',-1];
		$sort = "id desc";
		$filter['credit_status'] = '3';
		// $list = db('OrderAuth')->alias('a')->join('Order b','a.order_id = b.id','LEFT')->where($filter)->order($sort)->paginate(15);
		$list = db('Order')->where($filter)->order($sort)->paginate(15);
		return $list;
	}
	
	
	/*
	public function extend(){
		return $this->hasOne('OrderExtend', 'uid');
	}
	*/
	
	//订单统计
	public function get_all_order_total($uid = 0, $role = 0 ,$type = null, $status = null){

		if ($role == '1') {

			$filter = '(uid = '.$uid.')';
		}elseif($role == '7'){

			$filter = '(uid = '.$uid.' and mid = '.$uid.')';


		}elseif ($role == '0') {
			$ids = db('member')->field('mobile')->where('uid',$uid)->find();
			$filter  = '(mobile = '.$ids['mobile'].')';
		}else{
			$filter = '(uid = '.$uid.')';
		}
		$total = '';
		$filter .= ' and credit_status = 3'; 
		if($status == null){

			$filter = $filter.' and status > -1';
			$ord = db('Order')->field('sum(loan_limit) as loan_limit')->where($filter)->find();

		}else{

			if ($status == 3) {

				$filter = $filter.' and (status in (3,4,11,12,13) or finance = 2)';

				$ord = db('Order')->field('sum(loan_limit) as loan_limit')->where($filter)->whereOr('finance','2')->find();
			
			}elseif ($status == 1) {

					$filter = $filter.' and finance  in (3,4) ';

					$ord = db('Order')->field('sum(examine_limit) as loan_limit')->where($filter)->find();

			}else{

				$filter = $filter.' and status ='.(int)$status;
				$ord = db('Order')->field('sum(loan_limit) as loan_limit')->where($filter)->find();

			}
		}
		
		$total['order_num'] = db('Order')->where($filter)->count();

		if (empty($ord['loan_limit'])) {

			$ord['loan_limit'] = '0';
		}
		$total['order_num'] = (string)$total['order_num'];
		$total['loan_limit'] = (string)$ord['loan_limit'];
		
		return $total;
	}

	//添加订单
	public function add_order($uid, $role,$data){
		unset($data['type']);

		if ($role == '1') {
			
			$dealer_mobile = db('member')->alias('m')->field('d.mobile,d.id,d.forms')->join('dealer d','d.id = m.dealer_id')->where('m.uid',$uid)->find();

			$mid = db('member')->field('uid')->where('mobile',$dealer_mobile['mobile'])->find();

		}elseif ($role == '7') {
			$dealer_mobile = db('member')->alias('m')->field('d.mobile,d.id,d.forms')->join('dealer d','d.mobile = m.mobile')->where('m.uid',$uid)->find();

			$mid['uid'] = $uid;
		}

		
		$is_order = db('order')->field('mobile,status,id')->where('mobile',$data['mobile'])->order('id DESC')->find();

		if ($is_order['status'] == -2) {
			$data['car_price'] = $data['price'];
			$result = $this->allowField(true)->save($data,['id'=>$is_order['id']]);
			return $is_order['id'];

		}else{
			
			$order_sn = $this->build_order_sn();

			if (!empty($dealer_mobile) || !empty($mid)) {
				$data =array(
					'uid'=>$uid,
					'mid'=>$mid['uid'],
					'dealer_id'=>$dealer_mobile['id'],
					'mobile'=>$data['mobile'],
					'car_price'=>$data['price'],
					'sn' =>$order_sn,
					'status'=>-2,
					'type' =>$dealer_mobile['forms']
				);

				$result = $this->allowField(true)->save($data);
			}
		}
		if (empty($result)) {
			return false;
		}else{
			return $this->id;
		}
	}
	//保存订单
	public function save_order($uid, $data){

		$data1 =array(
			'loan_limit' => $data['loan_limit'],
			'endtime' => $data['loan_term'],
			'status'=>'3',
			);
		if (isset($data['type'])) {
			$data1['type'] = $data['type'];
		}
		$status = db('order')->alias('o')->field('d.guarantee_id')->join('__DEALER__ d','o.dealer_id = d.id')->where('o.id',$uid)->find();
		if (!empty($status['guarantee_id'])) {
			$data1['status']  = '11';
		}else{
			$data1['status'] = '3';
		}
		$data1['id'] = $uid;
		$result = $this->save($data1,['id'=>$data1['id']]);
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

	//客户详情查询

	public function search_detail($id){

		$info = db('order')->where('id',$id)->find();

		$result = db('member')->field('realname,mobile as sales_mobile')->where('uid',$info['uid'])->find();

		$result_one = db('dealer')->alias('d')->join('__MEMBER__ m','m.mobile = d.mobile')->field('name,property')->where('m.uid',$info['mid'])->find();
		
		//TODO :空判断
		if ($result) {

			$info['sales_mobile'] = $result['sales_mobile'];//业务员手机号

			$info['sales_realname'] = $result['realname'];//业务员真实姓名

		}else{

			$info['sales_mobile'] = '';//业务员手机号

			$info['sales_realname'] = '';//业务员真实姓名

		}
		
		$info['dealer_name'] = $result_one['name'];//车商名称
		$info['property'] = $result_one['property'];//车商属性
		return $info;
	}

	//获取订单编号
	public function get_sn($id){

		return $this->field('sn')->find($id);

	}

	//获取订单统计
	public function get_order_total($uid,$role,$type){
		$res = array();
		$res['pending'] = $this->get_all_order_total($uid,$role,'','0');//待提交
		$res['examine'] = $this->get_all_order_total($uid,$role,'','3');//审核中
		$res['supplement'] = $this->get_all_order_total($uid,$role,'','5');//补充资料
		$res['loan'] = $this->get_all_order_total($uid,$role,'','1');//已放款
		$res['refuse'] = $this->get_all_order_total($uid,$role,'','2');//已拒绝
		$res['total'] = $this->get_all_order_total($uid,$role,'','');//全部订单
		$res['every'] = $this->get_orders($uid,$role);
		return $res;
	}

	private function get_orders($uid,$role){
		if ($role == '1') {

			$filter['uid'] = $uid;

		}elseif($role == '7'){

			$filter['mid'] = $uid;

			$filter['uid'] = $uid;

		}else{

			$filter['uid'] = $uid;
		}
		$total = '';
		$filter['credit_status'] = '3';
		$begin_time = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
		$end_time = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
		$begin =strtotime($begin_time);
        $end =strtotime($end_time);
        $filter['create_time'] = array(array('gt',$begin),array('lt',$end));
        $res = array();
		$res['loan_num']= $this->where($filter)->where('finance','4')->count();
		$res['loan_num'] = (string)$res['loan_num'];
		$results= $this->field('sum(examine_limit) as loan_limit')->where($filter)->where('finance','4')->find();
		if (empty($results['loan_limit'])) {
			$results['loan_limit'] = '0';
		}
		$res['loan_money'] = (string)$results['loan_limit'];

		$res['nums'] = $this->where($filter)->count();
		$res['nums'] = (string)$res['nums'];
		return $res;
	}

}