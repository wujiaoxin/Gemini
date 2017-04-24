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

		$list = db('Order')->where('status','3')->order('create_time')->select();
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

		return $this->fetch('dataReview');
	}

	public function loanLimit() {

		if (IS_POST){

			$data = input('post.');

			if (isset($data['status'])) {

				$result = db('order')->where('id',$data['id'])->setField('status',$data['status']);

				if ($result) {

					$info = db('order')->field('loan_limit,endtime')->where('id',$data['id'])->find();
					
					$fee = fee_money($info['endtime'],$info['loan_limit']);

					db('order')->where('id',$data['id'])->setField('fee',$fee);

					$resp['code'] = 1;

					$resp['msg'] = '审核通过';

				}else{

					$resp['code'] = 0;

					$resp['msg'] = '审核失败';
				}
				
			}else{

				$resp['code'] = 0;

				$resp['msg'] = '审核异常';
			}

			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg']);

		}else{
			
			$list = db('Order')->where('status','4')->order('create_time')->select();
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

			$this->setMeta('查看审核');

		}

		$this->setMeta('借款额度审批');

		return $this->fetch('loanLimit');
	}


	public function view() {
		
		if (IS_POST){

			$data = input('post.');

			// var_dump($data);die;

			if (isset($data['status'])) {

				$result = db('order')->where('id',$data['id'])->setField('status',$data['status']);
				// echo $result;die;
				if ($result) {

					$info = db('order')->field('loan_limit,endtime')->where('id',$data['id'])->find();
					
					// var_dump($info);die;
					
					$fee = fee_money($info['endtime'],$info['loan_limit']);
					// echo $fee;die;
					if ($fee) {
						db('order')->where('id',$data['id'])->setField('fee',$fee);
					}

					$resp['code'] = 1;

					$resp['msg'] = '审核通过';
					// var_dump($resp);die;
				}else{

					$resp['code'] = 0;

					$resp['msg'] = '审核失败';
				}
				
			}else{

				$resp['code'] = 0;

				$resp['msg'] = '审核异常';
			}
			// var_dump($resp);die;
			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg']);
			return json($resp);
			// var_dump($resp);die;

		}else{
			$id   = input('id', '', 'trim,intval');
			$order_info = db('order')->where('id', $id)->find();

			$name = serch_name($order_info['mid']);

			$channel_info = db('dealer')->where('name',$name['dealer_name'])->find();

			$yewu = db('member')->field('realname,mobile')->where('uid',$order_info['uid'])->find();

			$channel_info['salesman'] = $yewu['realname'];
			$channel_info['salesmobile'] = $yewu['mobile'];

			$member_info = db('member')->where('uid', $order_info['mid'])->find();

			$repay_info = db('order_repay')->where('order_id', $order_info['sn'])->find();

			$examine_log  =db('examine_log')->where('record_id',$id)->select();

			foreach ($examine_log as $k => $v) {
				$examine_log[$k]['params'] = json_decode($v['param']);
				unset($examine_log[$k]['param']);
			}

			// var_dump($examine_log);die;

			$fileFilter['order_id'] = $id;

			$fileFilter['status'] = 1;//有效文件

			$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($fileFilter)->limit(100)->select();

			$list = array(

				'order_info' => $order_info,//订单信息

				'channel_info' => $channel_info,//渠道信息

				'member_info' => $member_info,//客户信息

				'repay_info' => $repay_info,//还款信息

				'files'   => $files,//附件资料

				'examine_log'   => $examine_log,//审核历史

				);


			$data = array(

				'infoStr' =>json_encode($list)
			);

			$this->assign($data);

			$this->setMeta('查看审核');

		}


		
		return $this->fetch();
	}

	public function delete(){
		// $id = $this->getArrayParam('id');
		$id   = input('id', '', 'trim,intval');
		// echo $id;die;
		if (empty($id)) {
			return $this->error('非法操作！');
		}
		$link = db('Order');

		$map    = array('id' => $id);
		// var_dump($map);die;
		$result = $link->where($map)->update(['status'=>'-1']);
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}