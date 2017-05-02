<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿'))) {
	if ($data === false || $data === null) {
		return $data;
	}
	$data = (array) $data;
	foreach ($data as $key => $row) {
		foreach ($map as $col => $pair) {
			if (isset($row[$col]) && isset($pair[$row[$col]])) {
				$data[$key][$col . '_text'] = $pair[$row[$col]];
			}
		}
	}
	return $data;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null) {
	if (!isset($status)) {
		return false;
	}
	switch ($status) {
	case -1:return '已删除';
		break;
	case 0:return '禁用';
		break;
	case 1:return '正常';
		break;
	case 2:return '待审核';
		break;
	default:return false;
		break;
	}
}

// 获取数据的状态操作
function show_status_op($status) {
	switch ($status) {
	case 0:return '启用';
		break;
	case 1:return '禁用';
		break;
	case 2:return '审核';
		break;
	default:return false;
		break;
	}
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false) {
	$list = array(
		1 => '系统',
		2 => '用户',
	);
	if ($all) {
		return $list;
	}
	return $list[$type];
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null) {
	if (empty($id) && !is_numeric($id)) {
		return false;
	}
	$list = cache('action_list');
	if (empty($list[$id])) {
		$map       = array('status' => array('gt', -1), 'id' => $id);
		$list[$id] = db('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null) {
	if (empty($value)) {
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info            = db('Model')->where($map);
	if (empty($field)) {
		$info = $info->field(true)->find();
	} else {
		$info = $info->value($field);
	}
	return $info;
}

/*
** 操作资金
** name 操作类型(数字)
** money_type 操作金额
** type 操作类型
** momod 操作方法
*/
function modify_account($data,$uid,$name=0,$money_type=0,$type=0,$memod=0){
	if ($type == 'payment' && $memod = 'UPDATE') {
		$dealer_money = db('dealer')->field('money')->where('id',$data['dealer_id'])->find();
		$use_money = $dealer_money['money']-$data['fee'];
		db('dealer')->where('id',$data['dealer_id'])->setField('money',$use_money);
		$result = array(
			'uid'=>$uid,
			'account_money' => $data['fee'],
			'desc' => $data['desrc'],
			'type' => 0,
			'deal_other' => 0,
			'create_time' => time()
			);
		db('dealer_money')->save($result);
	}
}
/*
**查询车商名称
*/
function serch_name($uid){

	$result = db('dealer')->field('name as dealer_name')->where('id',$uid)->find();
	return $result;

}


function serch_name_dealer($uid){
	$result = db('dealer')->alias('d')->field('d.name as dealer_name,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->where('m.uid',$uid)->find();

	return $result;

}
/*
**查询车商名称
*/
function serch_realname($uid){
	$result = db('member')->field('realname')->where('uid',$uid)->find();
	// var_dump($result);die;
	return $result['realname'];
}

 /*
  **生成还款列表（垫资还款）
  */
  function set_order_repay($order_id){

    $order = db('order')->where('id',$order_id)->find();

    if ($order) {

      $repay_time = time()+$order['endtime']*24*60*60;

      $order_repay = array(

          'order_id'=>$order_id,

          'uid'=>$mobile['uid'],

          'dealer_id'=> $order['dealer_id'],

          'repay_money'=>$order['examine_limit'],

          'manage_money'=>'0',

          'repay_time'=>$repay_time,

          'status'=>'-1',

          'has_repay'=>'0',

          'loadtime'=>$order['endtime'],

          'true_repay_money'=>'0',

          'true_repay_time'=>'0',

        );

      $result = db('order_repay')->insert($order_repay);

      return $result;

    }
  }
  	/*
	** 计算支付费用
	** time 借款时间
	** money 借款金额
	** 利率为日利率
  	*/
  	function fee_money($time, $money){

  		if ($time <= 5 && $time >=1 ) {

  			$interest_rate = 0.8/1000;
  			
  		}elseif ($time >5 && $time <=9) {

  			$interest_rate = 1/1000;

  		}elseif ($time >9 && $time <=12) {

  			$interest_rate = 1.2/1000;

  		}elseif ($time >12 && $time <=15) {

  			$interest_rate = 1.5/1000;

  		}else {

  			$interest_rate = '';

  		}

  		$result = $money * $interest_rate * $time;

  		return $result;
  	}

  	/**
	 * 记录行为日志，并执行该行为的规则
	 * @param string $action 行为标识
	 * @param string $model 触发行为的模型名
	 * @param int $param 参数
	 * @param int $record_id 触发行为的记录id
	 * @param int $user_id 执行行为的用户id
	 */
	function examine_log($action = null,$controller = null,$param = null , $record_id = null,$status = null , $type, $descr =null) {

		if (empty($user_id)) {
			$user_id = is_login();
		}
		//插入行为日志
		$data['uid']     = $user_id;
		$data['ip']   = ip2long(get_client_ip());
		$data['controller'] = $controller;
		$data['action'] = $action;
		$data['param'] = $param;
		$data['record_id'] = $record_id;
		$data['status'] = $status;
		$data['type'] = $type;
		$data['create_time'] = time();
		$data['descr'] = $descr;
		
		db('examine_log')->insert($data);
	}

	/*
	**还款计划（等额本息）
	*/
	function make_repay_plan($data){

		$deal = db('order')->where('id',$data)->find();

		if ($deal['type'] == '1') {

			$deal['product_name'] = '二手车按揭贷款';
			
		}elseif ($deal['type'] == '2') {

			$deal['product_name'] = '二手车按揭垫资';

			
		}elseif ($deal['type'] == '3') {

			$deal['product_name'] = '新车按揭贷款';

			
		}elseif ($deal['type'] == '4') {

			$deal['product_name'] = '新车按揭垫资';

		}


		$totalperiod = floor($deal['endtime']/30);

		if ($totalperiod == '12') {
			
			$deal['rate'] = 1.1/100;


		}elseif ($totalperiod == '24') {
			
			$deal['rate'] = 1.3/100;

		}elseif ($totalperiod == '36') {
			
			$deal['rate'] = 1.5/100;
		}
		$list = array();
		
		$has_use_self_money = 0;
		
		$repay_day = time();

		$uids = db('member')->field('uid')->where('mobile',$deal['mobile'])->find();
		for($i=1; $i <= $totalperiod; $i++){

			$load_repay = array();

			// $load_repay['repay_time'] = time()+30*24*60*60*$i;
			$load_repay['repay_time']  = $repay_day = next_replay_month ($repay_day);
			// $load_repay['repay_money11'] = date('Y-m-d H:i:s',$load_repay['repay_time']);
			$load_repay['repay_period'] = $i;

			$load_repay['totalperiod'] = intval($totalperiod);

			$load_repay['rate'] = $deal['rate']*100;

			$load_repay['repay_money'] = pl_it_formula($deal['examine_limit'],$deal['rate'],$totalperiod);

			$deal['month_repay_money'] = $load_repay['repay_money'];

			$load_repay['self_money'] = round($deal['examine_limit'] *$deal['rate']*pow((1+$deal['rate']),$i-1)/(pow(($deal['rate']+1),$totalperiod)-1),2);


			$has_use_self_money += $load_repay['self_money'];

			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			
			$load_repay['order_id'] = $deal['id'];

			$load_repay['uid'] = $uids['uid'];

			$load_repay['dealer_id'] = $deal['dealer_id'];

			$load_repay['status'] = -1;

			$load_repay['has_repay'] = -1;

			$load_repay['loantime'] = $deal['endtime'];

			$load_repay['product_name'] = $deal['product_name'];

			$list[] = $load_repay;
		}
		return $list;
	}
	function next_replay_month($time,$m=1){
		$str_t = to_timespan(to_date($time)." ".$m." month ");
		return $str_t;
	}
	function to_date($utc_time, $format = 'Y-m-d H:i:s') {
		if (empty ( $utc_time )) {
			return '';
		}
		$timezone = time();
		$time = $utc_time + 8 * 3600; 
		return date ($format, $time );
	}
	function to_timespan($str, $format = 'Y-m-d H:i:s'){
		$timezone = 8; 
		$time = intval(strtotime($str));
		if($time!=0)
			$time = $time - $timezone * 3600;
	    return $time;
	}
	/**
	 * 等额本息还款计算方式
	 * $money 贷款金额
	 * $rate 月利率
	 * $remoth 还几个月
	 * 返回  每月还款额
	*/
	function pl_it_formula($money,$rate,$remoth){
		if((pow(1+$rate,$remoth)-1) > 0)
			return round($money * ($rate*pow(1+$rate,$remoth)/(pow(1+$rate,$remoth)-1)),2);

		else
			return 0;
	}

	/**
	 * 获取该期本金
	 * int $Idx  第几期
	 * floatval $amount_money 总的借款多少
	 * floatval $month_repay_money 月还本息
	 * floatval $rate 费率
	 */
	function get_self_money($idx,$amount_money,$month_repay_money,$rate){
		return $month_repay_money - get_benjin($idx,$idx,$amount_money,$month_repay_money,$rate)*$rate/$idx/100;

	}
	/**
	 * 获取该期剩余本金
	 * int $Idx  第几期
	 * int $all_idx 总的是几期
	 * floatval $amount_money 总的借款多少
	 * floatval $month_repay_money 月还本息
	 * floatval $rate 费率
	 */
	function get_benjin($idx,$all_idx,$amount_money,$month_repay_money,$rate){
		//计算剩多少本金
		$benjin = $amount_money;
		for($i=1;$i<$idx+1;$i++){
			$benjin = $benjin - ($month_repay_money - $benjin*$rate/$idx/100);
		}
		return $benjin;
	}