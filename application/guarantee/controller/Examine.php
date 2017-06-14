<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\guarantee\controller;
use app\guarantee\controller\Baseness;

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
		if($uid > 0){
			if ($role != 18) {
				$mobile = db('dealer')->alias('d')->field('d.mobile')->join('__MEMBER__ m','m.dealer_id = d.id')->where('m.uid',$uid)->find();
				$uids = db('member')->field('uid')->where('mobile',$mobile['mobile'])->find();
				$uid = $uids['uid'];
			}

			$resl = db('dealer')->field('id')->where('guarantee_id',$uid)->select();
			$arr = array();
			foreach ($resl as $k => $v) {
				$ids = db('member')->field('uid')->where('dealer_id',$v['id'])->find();
				$arr[] = $ids['uid'];
			}
			$list = array();
			if (!empty($arr)) {
				foreach ($arr as $vl) {
					$list = db('Order')->where('uid',$vl)->order('create_time DESC')->select();
				}
			}

		}else{
			return $this->error('请重新登录');
		}
		foreach ($list as $k => $v) {
			$list[$k]['salesman'] = serch_realname($v['uid']);
			$name = serch_name($v['dealer_id']);
			$list[$k]['dealername'] = $name['dealer_name'];
		}
		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);

		$this->setMeta('贷款申请');

		return $this->fetch();
	}

	public function dataReview() {
		$role =session('user_auth.role');
		$uid =session('user_auth.uid');
		if ($role != '18') {
			$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
			$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
			$uid = $res['uid'];
		}
		if (IS_POST) {

			$data = input('post.');
			if (isset($data['status'])) {
				$res = db('Order')->field('status')->where('id',$data['id'])->find();
				if ($res['status'] == '1') {
					$resp['code'] = 0;

					$resp['msg'] = '已审核!';
				}
				$info = array(

					'status'=>$data['status']

				);
				
				if ($data['status'] == '5') {
					
					$info['descr'] = $data['descr'];

					$result = db('order')->where('id',$data['id'])->update($info);

				}elseif ($data['status'] == '2') {
					$info['descr'] = $data['descr'];
					$result = db('order')->where('id',$data['id'])->update($info);

					
				}else{
					$info['proposal_limit'] = $data['proposal'];
					$info['examine_limit'] = $data['examine_limit'];
					$info['status'] = '10';//信审
					$result = db('order')->where('id',$data['id'])->update($info);
				}
				
				if ($result) {

						$resp['code'] = 1;

						$resp['msg'] = '提交成功';
					}else{

						$resp['code'] = 0;

						$resp['msg'] = '提交失败';
					}
			}else{

				$resp['code'] = 0;

				$resp['msg'] = '提交失败';
			}
			
			// var_dump($resp);die;
			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);
			return json($resp);
			
		}else{
			$map = array(
				'o.status'=>3,
				'd.guarantee_id'=>$uid
			);
			$list = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname as salesman')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();
			$data = array(

				'infoStr' =>json_encode($list)
			);

			$this->assign($data);
		}

		$this->setMeta('信用审核');

		return $this->fetch('dataReview');
	}

	public function loanLimit() {
		$role =session('user_auth.role');
		$uid =session('user_auth.uid');
		if (IS_POST){

			$data = input('post.');
			if (isset($data['status'])) {

				if ($data['status'] == '1') {

					$infos = array(
							'status' => '1',
							'examine_limit' =>$data['examine_limit'],
							'descr'=>$data['descr']
						);

					$result = db('order')->where('id',$data['id'])->update($infos);

					if ($result) {

						$info = db('order')->field('examine_limit,endtime,type')->where('id',$data['id'])->find();

						if ($info['type'] == '2' || $info['type'] == '4') {

							$fee = fee_money($info['endtime'],$info['examine_limit']);

							$fee1['fee'] = $fee;
							$fee1['finance'] = '2';
							db('order')->where('id',$data['id'])->update($fee1);
						}else{

							db('order')->where('id',$data['id'])->setField('finance','2');
						}
						$resp['code'] = 1;

						$resp['msg'] = '提交成功';

					}else{

						$resp['code'] = 0;

						$resp['msg'] = '提交失败';

						return json($resp);
					}
				}else{

					$info_s = array(

						'reject_reason' => $data['descr'],

						'status' =>$data['status'],

						'examine_limit' =>$data['examine_limit']

						);
					$result = db('order')->where('id',$data['id'])->update($info_s);
					if ($result) {

						$resp['code'] = 1;

						$resp['msg'] = '提交成功';
					}else{

						$resp['code'] = 0;

						$resp['msg'] = '提交失败';

					}

				}

			}else{

				$resp['code'] = 0;

				$resp['msg'] = '提交异常';
			}

			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);

			return json($resp);

		}else{
			
			if ($role != '18') {
				$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
				$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
				$uid = $res['uid'];
			}
			$resl = db('Dealer')->field('id')->where('guarantee_id',$uid)->select();
			if (!empty($resl)) {
				foreach ($resl as $vl) {
					$map['dealer_id'] =$vl['id'];
					$map['status'] = '4';
					$list = db('Order')->where($map)->order('create_time DESC')->select();
				}
			}

			if (!empty($list)) {
				foreach ($list as $k => $v) {
					$list[$k]['salesman'] = serch_realname($v['uid']);

					$name = serch_name($v['dealer_id']);

					$list[$k]['dealername'] = $name['dealer_name'];
				}
			}else{
				$list = '';
			}

			$data = array(

				'infoStr' =>json_encode($list)
			);
			
			$this->assign($data);

			$this->setMeta('查看审核');

		}

		$this->setMeta('额度审核');

		return $this->fetch('loanLimit');
	}

	public function finance() {
		$role =session('user_auth.role');
		$uid =session('user_auth.uid');
		if (IS_POST){

			$data = input('post.');
			if (isset($data['status'])) {

				if ($data['status'] == '1') {

					$infos = array(
							'status' => '1',
							'examine_limit' =>$data['examine_limit'],
							'descr'=>$data['descr']
						);

					$result = db('order')->where('id',$data['id'])->update($infos);

					if ($result) {

						$info = db('order')->field('examine_limit,endtime,type')->where('id',$data['id'])->find();

						if ($info['type'] == '2' || $info['type'] == '4') {

							$fee = fee_money($info['endtime'],$info['examine_limit']);

							$fee1['fee'] = $fee;
							$fee1['finance'] = '1';
							db('order')->where('id',$data['id'])->update($fee1);
						}else{

							db('order')->where('id',$data['id'])->setField('finance','2');
						}
						$resp['code'] = 1;

						$resp['msg'] = '提交成功';

					}else{

						$resp['code'] = 0;

						$resp['msg'] = '提交失败';

						return json($resp);
					}
				}else{

					$info_s = array(

						'reject_reason' => $data['descr'],

						'status' =>$data['status'],

						'examine_limit' =>$data['examine_limit']

						);
					$result = db('order')->where('id',$data['id'])->update($info_s);
					if ($result) {

						$resp['code'] = 1;

						$resp['msg'] = '提交成功';
					}else{

						$resp['code'] = 0;

						$resp['msg'] = '提交失败';

					}

				}

			}else{

				$resp['code'] = 0;

				$resp['msg'] = '提交异常';
			}

			examine_log(ACTION_NAME,CONTROLLER_NAME,json_encode($data),$data['id'], $data['status'],$resp['msg'],$data['descr']);

			return json($resp);

		}else{
			
			if ($role != '18') {
				$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
				$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
				$uid = $res['uid'];
			}
			$resl = db('Dealer')->field('id')->where('guarantee_id',$uid)->select();
			if (!empty($resl)) {
				foreach ($resl as $vl) {
					$map['dealer_id'] =$vl['id'];
					$map['finance'] = '3';
					$map['status'] = '1';
					$list = db('Order')->where($map)->order('create_time DESC')->select();
				}
			}

			if (!empty($list)) {
				foreach ($list as $k => $v) {
					$list[$k]['salesman'] = serch_realname($v['uid']);

					$name = serch_name($v['dealer_id']);

					$list[$k]['dealername'] = $name['dealer_name'];
				}
			}else{
				$list = '';
			}


			$data = array(

				'infoStr' =>json_encode($list)
			);
			
			$this->assign($data);

			$this->setMeta('查看审核');

		}

		$this->setMeta('财务审核');

		return $this->fetch('finance');
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