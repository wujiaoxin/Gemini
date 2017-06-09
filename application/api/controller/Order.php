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
		$role = session('user_auth.role');
		// echo $role;die;
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$data = input('post.');
		if ($data['mobile'] == '' || $data['price'] == '' ) {
			$resp['code'] = 0;
			$resp['msg'] = '无法创建订单';
		}
		$orderModel = model('Order');
		$list = $orderModel->add_order($uid,$role,$data);
		if (!$list) {
			$resp['code'] = 0;
			$resp['msg'] = '提交失败！';
		}else{
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
			$map = '(uid = '.$uid.')';
			// $map = '(uid = '.$uid.' or bank_uid = '.$uid.')';
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
			$map .= ' and credit_status = 3';
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
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		
		
		$data['total'] = $orderModel->get_all_order_total($uid,$role, $type, $status);
		
		$resp['code'] = 1;
		$resp['msg'] = '获取成功!';
		$resp['data'] = $data;
 
		return json($resp);
	}


	//获取订单统计
	public function getTotal($type = null) {
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';
		$orderModel = model('Order');
		if ($uid>0) {
			
			$data = $orderModel->get_order_total($uid,$role,$type);
			
			$resp['code'] = 1;
			$resp['msg'] = '获取成功!';
			$resp['data'] = $data;
		}else{
			$resp['code'] = 0;
			$resp['msg'] = '请重新登录!';
		}
		
		return json($resp);
	}

	//获取订单详情
	public function detail($id){
		$uid = session('user_auth.uid');
		$role = session('user_auth.role');
		$resp['code'] = 0;
		$resp['msg'] = '未知错误';

		$orderModel = model('Order');

		$info = $orderModel->search_detail($id);
		
		if($role==0){

			$resp['code'] = 1;

			$resp['msg'] = '获取成功!';

			$resp['data'] = $info ;
	 
			return json($resp);

		}
		//$filter['uid'] = $uid;
		$filter['order_id'] = $info['id'];
		$filter['status'] = 1;//有效文件
		$files = db('OrderFiles')->field('id,path,size,create_time,form_key,form_label')->where($filter)->order('create_time DESC')->limit(16)->select();

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
	
	//更新还款银行卡信息
	public function updateBankInfo($type = null, $bank_name = null, $bank_branch = null, $bank_account_name = null, $bank_account_id = ''){
		$resp = '{
			"code": 1,
			"msg": "保存成功"
		}';
		if (empty($bank_account_id)) {
			$resp = '{
				"code": 0,
				"msg": "银行卡不能为空"
			}';
			$resp = json_decode($resp);
			return $resp;
		}
		$uid = session('user_auth.uid');
		$creditResult = db('credit')->field('uid,mobile,order_id,credit_status,credit_result')->where("uid",$uid)->order('id desc')->find();
		
		if($creditResult['credit_result'] == 1){//授信审核通过
			//$orderData = $creditResult['order_id'];
			$orderData['status'] = 0;
			$results =db('order')->where("id", $creditResult['order_id'])->where("status",-2)->update($orderData);
			if ($results) {
				$res = array(
				'uid'=>$uid,
				'type'=>5,
				'order_id'=>$creditResult['order_id'],
				'bank_account_id'=>$bank_account_id,
				'create_time'=>time()
				);
				db('bankcard')->insert($res);

			}else{
				$resp = '{
					"code": 0,
					"msg": "找不到订单"
				}';
			}
			
		}else{
			$resp = '{
				"code": 0,
				"msg": "保存失败"
			}';
		}
		
		
		
		
		$resp = json_decode($resp);
		return $resp;
	}
	
}
