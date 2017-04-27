<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use app\common\controller\Api;

class Order extends Api {
	/*public function _initialize() {
		parent::_initialize();
		if (!is_login()) {
			$data['code'] = 0;
			$data['msg'] = '会话已超时';
			return json($data);exit();
		}elseif (is_login()) {
			$user = model('User')->getInfo(session('user_auth.uid'));
			$this->assign('user', $user);
		}
	}*/

	public function index() {		
		//$this->assign($user);		
		$resp['code'] = 1;
		$resp['msg'] = 'OrderAPI';
		return json($data);
	}
	
	//添加
	public function add($mobile = null, $price = null) {

		$uid = session('user_auth.uid');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if ($_POST) {
			$data = input('post.');
			$orderModel = model('Order');
			$list = $orderModel->add_order($uid, $data);
			if (!$list) {
				$resp['code'] = 0;
				$resp['msg'] = '提交失败！';
			}
			$data['id'] = $list;
			$resp['code'] = 1;
			$resp['msg'] = '提交成功';
			$resp['data'] = $data;
		}
		return json($resp);
	}
	
	public function getList($status = null, $type = null) {
		$map = '';
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		if($uid > 0){
			$map = '(uid = '.$uid.' or bank_uid = '.$uid.')';
		}else{
			$resp['code'] = 0;
			$resp['msg'] = '请重新登录';
			return json($resp);
		}
		if($role == 1){

			if($status == null){

				$map = $map.' and status > -1';

			}else{
				if ($status == 3) {

					$map = $map.' and status in (3,4)';

				}else{

					$map = $map.' and status ='.(int)$status;
				}
				
			}
			/*if($type == '3'){
				$map = $map.' and type = 3';
			}else{
				$map = $map.' and type < 3';
			}*/
			
			$sort = "id desc";
			$list  = db('Order')->where($map)->order($sort)->paginate(15);
			
			$resp['code'] = 1;
			$resp['msg'] = 'OK';
			$resp['data'] = $list;
			return json($resp);
		}else{
			$Order = model('Order');

			$list = $Order->get_order_list($uid, $role, $type, $status);
			$resp['code'] = 1;
			$resp['msg'] = 'OK';
			$resp['data'] = $list;
			return json($resp);
		}
	}

	
	public function getQRCode($id = null, $mobile = null, $price = null) {
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		$data["url"] = "https://t.vpdai.com/api/open/appdl?mobile=".$mobile."&order_id=".$id."&from=dealer&price=".$price;
		$data["url"] = urlencode($data["url"]);
		$data["url"] = "https://pan.baidu.com/share/qrcode?w=512&h=512&url=".$data['url'];
		$resp['code'] = 1;
		$resp['msg'] = '获取成功';
		$resp['data'] = $data;
		return json($resp);
	}
	
	
	public function save($id = null, $type = null, $mobile = null, $idcard = null, $loan_limit = null, $loan_term = null) {
		// $uid = session('user_auth.uid');
		$uid = $id;
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		if ($_POST) {
			$data = input('post.');
			unset($data['type']);
			$list = $orderModel->save_order($uid,$data);
			if ($list) {
				$data["id"] = $data['id'];
				$resp['code'] = 1;
				$resp['msg'] = '保存成功';
				$resp['data'] = $data;
			}else{
				$resp['code'] = 0;
				$resp['msg'] = '提交失败!';
			}
		}
		return json($resp);
	}

	
	public function total($type = null,$status= null) {
		$uid = session('user_auth.uid');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		
		$data['total'] = $orderModel->get_all_order_total($uid, $type, $status);
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功!';
		$resp['data'] = $data;
 
		return json($resp);
	}

	//获取订单详情
	public function detail($id){
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$list = db('order')->where('id',$id)->find();
		$link = model('Order');
		if($role==1){
			$info = db('Order')->where('id',$id)->find();
		}else{
			$authfilter['order_id'] = $id;
			$authfilter['auth_uid'] = $uid;
			$authfilter['auth_role'] = $role;			
			$auth = db('OrderAuth')->where($authfilter)->find();
			if($auth != null){
				$info = db('Order')->where("id",$id)->find();
			}
		}
		//$filter['uid'] = $uid;
		$filter['order_id'] = $info['id'];
		$filter['status'] = 1;//有效文件
		$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($filter)->limit(100)->select();

		$result = db('member')->field('realname,mobile as sales_mobile')->where('uid',$info['uid'])->find();
		$result_one = db('dealer')->alias('d')->join('__MEMBER__ m','m.mobile = d.mobile')->field('name')->where('m.uid',$info['mid'])->find();
		//TODO :空判断
		$info['sales_mobile'] = $result['sales_mobile'];//业务员手机号
		$info['sales_realname'] = $result['realname'];//业务员真实姓名
		$info['dealer_name'] = $result_one['name'];//车商名称
		$data = array(
				'info'    => $info,
				'files'   => $files,
		);

		if ($data) {
			$resp['code'] = 1;
			$resp['msg'] = '获取成功!';
			$resp['data'] = $data;

		}else{
			$resp['code'] = 0;
			$resp['msg'] = '获取失败!';
		}
		return json($resp);
	}

	//上传订单文件
	public function upload($type = null, $order_id = null, $form_key = null, $form_label = null, $file = null){
		$controller = controller('common/Files');
		$action     = $this->request->action();
		return $controller->$action();
	}
	
	//更新银行卡信息 TODO
	public function updateBankInfo($type = null, $bank_name = null, $bank_branch = null, $bank_account_name = null, $bank_account_id = null){
		$resp = '{
			"code": 1,
			"msg": "保存成功"
		}';
		$resp = json_decode($resp);
		return $resp;
	}
	
}
