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
	** 计算支付费用
	** time 借款时间
	** money 借款金额
	** 利率为日利率
	*/
	/*function fee_money($time, $money){

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
	}*/

	
/*设备信息*/
function get_collect($id,$key,$group){
	$map = array(
		'uid'=>$id,
		'key'=>$key,
		'group'=>$group
		);
	$res = db('Collect_data')->where($map)->order('create_time DESC')->find();
	if ($res) {
		return $res['value'];
	}else{
		$res = array();
		return $res;
	}
	
}
function  get_arr($total,$value){
	$arr = array();
	for ($i=0; $i < $total; $i++) { 
		$arr[] = $value;
	}
	return json_encode($arr);
}