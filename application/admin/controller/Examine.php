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

		if (IS_POST) {

			$data = input('post.');

			if (isset($data['status'])) {

				$info = array(

					'status'=>$data['status']

					);
				
				if ($data['status'] == '4') {
					
					$info['descr'] = $data['descr'];

					$result = db('order')->where('id',$data['id'])->update($info);

					if ($result) {

						$resp['code'] = 1;

						$resp['msg'] = '审核通过';
					}else{

						$resp['code'] = 0;

						$resp['msg'] = '审核异常';

					}

				}

				if ($data['status'] == '2') {

					$info['reject_reason'] = $data['reject_reason'];
					$info['descr'] = $data['descr'];

					$result = db('order')->where('id',$data['id'])->update($info);

					if ($result) {

						$resp['code'] = 1;

						$resp['msg'] = '审核通过';
					}else{

						$resp['code'] = 0;

						$resp['msg'] = '审核异常';

					}
				}
					
			}else{

				$resp['code'] = 0;

				$resp['msg'] = '审核失败';
			}
			
			// var_dump($resp);die;
			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg']);
			return json($resp);
			
		}else{
			$list = db('Order')->where('status','3')->order('create_time')->select();

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
		}

		$this->setMeta('资料复核');

		return $this->fetch('dataReview');
	}

	public function loanLimit() {

		if (IS_POST){

			$data = input('post.');

			if (isset($data['status'])) {

				if ($data['status'] == '1') {

					$infos = array(
							'status' => '1',
							'finance' => '1',
							'examine_limit' =>$data['examine_limit'],
							'descr'=>$data['descr']
						);

					$result = db('order')->where('id',$data['id'])->update($infos);

					if ($result) {

						$info = db('order')->field('loan_limit,endtime,type')->where('id',$data['id'])->find();

						if ($info['type'] == '2' || $info['type'] == '4') {

							$fee = fee_money($info['endtime'],$info['loan_limit']);

							if ($fee) {

								db('order')->where('id',$data['id'])->setField('fee',$fee);
							}
						}
						$resp['code'] = 1;

						$resp['msg'] = '审核通过';

					}else{

						$resp['code'] = 0;

						$resp['msg'] = '审核失败';
					}
				}else{

					$info_s = array(

						'reject_reason' => $data['descr'],

						'status' =>$data['status'],

						'examine_limit' =>$data['examine_limit']

						);

					$result = db('order')->where('id',$data['id'])->update($info_s);
				}

			}else{

				$resp['code'] = 0;

				$resp['msg'] = '审核异常';
			}

			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg']);

			return json($resp);

		}else{
			
			$list = db('Order')->where('status','4')->order('create_time')->select();

			foreach ($list as $k => $v) {

				$list[$k]['salesman'] = serch_realname($v['uid']);

				$name = serch_name($v['mid']);

				$list[$k]['dealername'] = $name['dealer_name'];
			}

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
		
		$id   = input('id', '', 'trim,intval');

		$order_info = db('order')->where('id', $id)->find();

		$name = serch_name($order_info['mid']);

		$channel_info = db('dealer')->where('name',$name['dealer_name'])->find();

		$yewu = db('member')->field('realname,mobile')->where('uid',$order_info['uid'])->find();

		$channel_info['salesman'] = $yewu['realname'];

		$channel_info['salesmobile'] = $yewu['mobile'];

		$member_info = db('member')->where('uid', $order_info['mid'])->find();

		$credit_info = db('credit')->where('mobile', $order_info['mobile'])->order('id desc')->find();

		$repay_info = db('order_repay')->where('order_id', $order_info['sn'])->find();

		$examine_log  =db('examine_log')->where('record_id',$id)->select();

		foreach ($examine_log as $k => $v) {
			
			$result = db('member')->field('username')->where('uid',$v['uid'])->find();
			
			$examine_log[$k]['operator'] =  $result['username'];

		}
		// var_dump($examine_log);die;
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
			
			'credit_info' => $credit_info,

			'repay_info' => $repay_info,//还款信息

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

	public function delete(){

		$id   = input('id', '', 'trim,intval');

		if (empty($id)) {

			return $this->error('非法操作！');
		}

		$link = db('Order');

		$map    = array('id' => $id);

		$result = $link->where($map)->update(['status'=>'-1']);
		
		if ($result) {
			
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}