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
	$result = db('dealer')->alias('d')->field('d.name as dealer_name,d.mobile')->join('__MEMBER__ m','d.mobile = m.mobile')->where('m.uid',$uid)->find();
	// var_dump($result);die;
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
  **生成还款列表
  */
  function set_order_repay($order_id){

    $order = db('order')->where('id',$order_id)->find();

    if ($order) {

      $repay_time = time()+$order['endtime']*24*60*60;

      $order_repay = array(

          'order_id'=>$order_id,

          'mid'=>$order['mid'],

          'repay_money'=>$order['loan_limit'],

          'manage_money'=>'0',

          'repay_time'=>$repay_time,

          'status'=>'-1',

          'has_repay'=>'0',

          'loadtime'=>['endtime'],

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

  			$interest_rate = 0.08/10;
  			
  		}elseif ($time >5 && $time <=9) {

  			$interest_rate = 1/10;

  		}elseif ($time >9 && $time <=12) {

  			$interest_rate = 1.2/10;

  		}elseif ($time >12 && $time <=15) {

  			$interest_rate = 1.5/10;

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
	function examine_log($action = null,$controller = null,$param = null , $record_id = null,$status = null , $type) {

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
		
		db('examine_log')->insert($data);
	}