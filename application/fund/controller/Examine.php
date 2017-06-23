<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\fund\controller;
use app\fund\controller\Baseness;

class Examine extends Baseness {
	public function index() {
		$nickname      = input('nickname');
		$map['status'] = array('egt', 0);
		if (is_numeric($nickname)) {
			$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
		} else {
			$map['nickname'] = array('like', '%' . (string) $nickname . '%');
		}

		$order = "uid desc";
		$list  = model('User')->where($map)->order($order)->paginate(15);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('用户信息');
		return $this->fetch();
	}
	//TODO
	public function application() {

		$uid = session('user_auth.uid');
		$role = session('user_auth.role');

		$list = db('Order')->alias('o')->field('o.*,m.realname as salesman,d.name as dealername')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->join('__DEALER__ d','d.id = o.dealer_id','LEFT')->where('o.fund_id',$uid)->select();
		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);

		$this->setMeta('贷款申请');

		return $this->fetch();
	}


	public function view() {
		
		$id   = input('id', '', 'trim,intval');

		$order_info = db('order')->alias('o')->field('o.*,m.realname as salesman,m.mobile as salesmobile')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where('id', $id)->find();

		$member_info = db('member')->alias('m')->field('m.*,c.credit_result,c.credit_level,c.credit_score')->join('__CREDIT__ c','c.uid = m.uid','LEFT')->where('m.mobile', $order_info['mobile'])->find();

		$examine_log  =db('examine_log')->alias('l')->field('l.*,m.username as operator')->join('__MEMBER__ m','m.uid = l.uid','LEFT')->where('l.record_id',$id)->select();

		foreach ($examine_log as $k => $v) {

			$examine_log[$k]['params'] = json_decode($v['param']);

			unset($examine_log[$k]['param']);
		}


		$fileFilter['order_id'] = $id;

		$fileFilter['status'] = 1;//有效文件

		$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($fileFilter)->order('create_time DESC')->limit(100)->select();

		$list = array(

			'order_info' => $order_info,//订单信息

			'member_info' => $member_info,//客户信息
			
			'files'   => $files,//附件资料

			'examine_log'   => $examine_log,//审核历史

			);

		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);

		$this->setMeta('查看审核');

		return $this->fetch();
	}

	
}