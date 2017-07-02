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

			$map = array('d.guarantee_id'=>$uid);

			$map['o.credit_status'] = '3';

			$list = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();

		}else{
			return $this->error('请重新登录');
		}
		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);

		$this->setMeta('贷款申请');

		return $this->fetch();
	}

	public function creditReview() {
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
				$info = array(

					'status'=>$data['status'],

					'proposal_limit'=>$data['proposal_limit'],

					'descr'=>$data['descr']
				);
				$result = db('order')->where('id',$data['id'])->update($info);
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
				'o.status'=>11,
				'd.guarantee_id'=>$uid
			);
			$list = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname as salesman')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();
			$data = array(

				'infoStr' =>json_encode($list)
			);

			$this->assign($data);
		}

		$this->setMeta('信用审核');

		return $this->fetch('creditReview');
	}

	public function loanLimit() {
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
				$info = array(

					'status'=>$data['status'],

					'examine_limit'=>$data['examine_limit'],

					'descr'=>$data['descr']
				);
				
				$result = db('order')->where('id',$data['id'])->update($info);
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
				'o.status'=>12,
				'd.guarantee_id'=>$uid
			);
			$list = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname as salesman')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();

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
		if ($role != '18') {
			$uids = db('member')->alias('m')->join("__DEALER__ d","m.dealer_id = d.id")->field('d.mobile')->where('uid',$uid)->find();
			$res = db('member')->field('uid')->where('mobile',$uids['mobile'])->find();
			$uid = $res['uid'];
		}
		if (IS_POST) {
			$data = input('post.');
			if (isset($data['status'])) {
				$info = array(

					'status'=>$data['status'],

					'examine_limit'=>$data['examine_limit'],

					'descr'=>$data['descr']
				);
				
				$result = db('order')->where('id',$data['id'])->update($info);
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
				'o.status'=>13,
				'd.guarantee_id'=>$uid
			);
			$list = db('Order')->alias('o')->field('o.*,d.name as dealername,m.realname as salesman')->join('__DEALER__ d','o.dealer_id = d.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where($map)->select();
			
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

		$maps = array(
			'm.mobile'=>$order_info['mobile'],
			'c.credit_status'=>3,

		);
		$member_info = db('member')->alias('m')->field('m.*,c.credit_result,c.credit_level,c.credit_score')->join('__CREDIT__ c','c.uid = m.uid','LEFT')->where($maps)->find();

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

	public function fview() {

		if (IS_POST) {
			$data = input('post.');
			if ($data['status'] == '3') {
				$info = array(
					'status'=>$data['status'],
					'endtime'=>$data['loan_term']
				);
				$res = model('Order')->save($data,['id'=>$data['id']]);
				if ($res) {
					$resp['code'] = 1;
					$resp['msg'] = '申请垫资成功！';
				}else{
					$resp['code'] = 0;
					$resp['msg'] = '申请垫资失败！';
				}
				return json($resp);
			}else{
				$resp['code'] = 0;
				$resp['msg'] = '无法放款';
				return json($resp);
			}
		}else{

			$id   = input('id', '', 'trim,intval');

			$order_info = db('order')->alias('o')->field('o.*,m.realname as salesman,m.mobile as salesmobile')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->where('id', $id)->find();

			$maps = array(
				'm.mobile'=>$order_info['mobile'],
				'c.credit_status'=>3,

			);
			$member_info = db('member')->alias('m')->field('m.*,c.credit_result,c.credit_level,c.credit_score')->join('__CREDIT__ c','c.uid = m.uid','LEFT')->where($maps)->find();

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

			$this->setMeta('财务审核');

			return $this->fetch();

		}
		
		
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

	

	//上传订单文件
	public function upload($type = null, $order_id = null, $form_key = null, $form_label = null, $file = null){
		$controller = controller('common/Files');
		$action     = $this->request->action();
		return $controller->$action();
	}

	public function deleteFile() {
		//TODO: remove local file & check uid
		$id   = input('id', '', 'trim,intval');
		$uid  =  session('user_auth.uid');
		$resp['status'] = 1;//TODO 标准化返回参数	
		$data['status'] = -1;
		if($id == ''){
			//return $this->error("缺少参数");
			$resp['status'] = 0;
			$resp['info'] = "缺少参数";
		}else{
			$resp['code'] = db("OrderFiles")->where(array('id' => $id,'uid' => $uid))->update($data);
		}		
		echo json_encode($resp);
	}

	public function welcome(){
		//担保公司员工
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$mobile = session('business_mobile');
		if ($role != 18) {
			$name = db('member')->field('realname as username')->where('uid',$uid)->find();
		}else{
			$name = db('Dealer')->field('name as username')->where('mobile',$mobile)->find();
		}

		$data = array(
				'info'    => $name,
				'infoStr' => json_encode($name),
		);
		$this->assign($data);
		return $this->fetch();
	}
}