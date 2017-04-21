<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class examine extends Admin {

	/**
	 * 用户管理首页
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
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

	public function application() {

		$list = db('Order')->order('create_time')->select();
		// var_dump($list);die;
		foreach ($list as $k => $v) {
			$list[$k]['salesman'] = serch_realname($v['uid']);
			$name = serch_name($v['mid']);
			$list[$k]['dealername'] = $name['dealer_name'];
		}
		// var_dump($list);die;
		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);

		$this->setMeta('借款申请');

		return $this->fetch();
	}

	public function dataReview() {

		$list = db('Order')->order('create_time')->select();
		// var_dump($list);die;
		foreach ($list as $k => $v) {
			$list[$k]['salesman'] = serch_realname($v['uid']);
			$name = serch_name($v['mid']);
			$list[$k]['dealername'] = $name['dealer_name'];
		}
		// var_dump($list);die;
		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);
		
		$this->setMeta('资料复核');

		return $this->fetch();
	}

	public function loanLimit() {

		$data = array(

			'infoStr' =>json_encode($list) ,
		);

		$this->assign($data);

		$this->setMeta('借款额度审批');

		return $this->fetch();
	}

	public function examine() {

		$data = array(

			'infoStr' =>json_encode($list) ,
		);

		$this->assign($data);

		$this->setMeta('订单审核');

		return $this->fetch();
	}

	public function view() {

		if (IS_POST){

			$data = input('post.');
			
		}else{
			$id   = input('id', '', 'trim,intval');
			$order_info = db('order')->where('id', $id)->find();

			$name = serch_name($order_info['mid']);

			$channel_info = db('dealer')->where('name',$name['dealer_name'])->find();

			$member_info = db('member')->where('uid', $order_info['mid'])->find();

			$repay_info = db('order_repay')->where('order_id', $order_info['sn'])->find();

			$list = array(

				'order_info' => $order_info,//订单信息

				'channel_info' => $channel_info,//渠道信息

				'member_info' => $member_info,//客户信息

				'repay_info' => $repay_info,//还款信息

				);


			$data = array(

				'infoStr' =>json_encode($list)
			);

			$this->assign($data);

			$this->setMeta('查看审核');

		}


		
		return $this->fetch();
	}

	
}