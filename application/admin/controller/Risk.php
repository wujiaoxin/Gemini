<?php

namespace app\admin\controller;
use app\common\controller\Admin;

class risk extends Admin {
 
	public function index() {
		return ""; 
	}

	public function rating() {
		
		$creditList = db('credit')->alias('c')->field('c.*,m.realname,m.idcard')->join('__MEMBER__ m','c.uid = m.uid')->where("credit_status",3)->order('id desc')->fetchSQL(false)->select();
		
		$data = array(
			'infoStr' =>json_encode($creditList),
		);
		//var_dump($data);die;
		$this->assign($data);
		
		$this->setMeta('客户评级');
		return $this->fetch();
	}

	public function ratingInfo($id = null) {
		
		if (IS_POST) {
			$data = input('post.');
			//黑名单
			$res_crd = db('credit')->where('id', $data['id'])->find();
			if ($data['refuse_reason'] == '3' && $res_crd['credit_result'] == '0') {
				$risks = model('Risk');
				$res = db('credit')->alias('c')->field('c.uid,c.order_id,m.realname,m.idcard,m.bankcard')->join('__MEMBER__ m','c.uid = m.uid')->where('c.id',$data['id'])->find();
				$collect = db('collect_data')->where('uid',$res_crd['uid'])->order('id DESC')->select();
				foreach ($collect as $k => $v) {
					if ($v['key'] == 'device') {
						$datas['device_number'] = $v['value'];
					}
				}
				$datas['idcard'] = $res['idcard'];
				$datas['bankcard'] = $res['bankcard'];
				$datas['uid'] = $res['uid'];
				$datas['order_id'] = $res['order_id'];
				$datas['mobile'] = $data['mobile'];
				$datas['name'] = $res['realname'];
				$datas['data_sources'] = '1';
				$risks->save($datas);
			}
			if ($data['credit_result'] == '-1') {
				if ($data['refuse_reason'] == '1'  && $res_crd['credit_result'] == '0') {
					$data['credit_result'] = $data['treatment'];
					$data['refuse_reason'] = '1';
				}
			}
			if ($res_crd['credit_result'] != '0') {
				return $this->error("已审核！");
			}
			$result = db('credit')->where('id', $data['id'])->fetchSQL(false)->update($data);			
			//var_dump($result);
			/*if($data['credit_result'] == 1){//授信审核通过
				$mobile = $data['mobile'];//TODO 手机号查库
				$orderData['status'] = 0;
				db('order')->where("mobile",$mobile)->where("status",-2)->update($orderData);//TODO result判断 事务操作 保证数据完整性
			}
			*/
			
			if ($result) {
				return $this->success("提交成功！", url('rating'));
			} else {
				return $this->error("提交失败！");
			}
			
		}else{
			$creditList = db('credit')->alias('c')->field('c.*,m.realname,m.idcard,o.car_price')->join('__MEMBER__ m','c.uid = m.uid')->join('__ORDER__ o','c.order_id = o.id')->where("c.id",$id)->order('id desc')->fetchSQL(false)->find();			
			$data = array(
				'infoStr' =>json_encode($creditList),
			);			
			$this->assign($data);
			$this->setMeta('评级详情');
			return $this->fetch('ratingInfo');
		}
	}

	public function blacklist() {
		$risks = model('Risk');
		if (IS_POST) {
			$data = input('post.');
			$result = db('Member_blacklist')->where('id', $data['id'])->update($data);
			if ($result) {
				return $this->success("提交成功！", url('blacklist'));
			} else {
				return $this->error("提交失败！");
			}
		}else{
			$result = $risks->select();
			$data = array(
				'infoStr' =>json_encode($result),
			);
			$this->assign($data);
			$this->setMeta('黑名单');
			return $this->fetch();
		}

	
	}

	public function addBlacklist() {
		$risks = model('Risk');
		if (IS_POST) {
			$data = input('post.');
			$res = $risks->where('id',$data['id'])->find();
			if ($res['status'] == 0) {
				$data['status'] = '1';
				$result = $risks->where('id', $data['id'])->update($data);
			}else{
				return $this->error('已审核');
			}
			
			if ($result) {
				return $this->success("提交成功！", url('blacklist'));
			} else {
				return $this->error("提交失败！");
			}
		}else{
			$id = input('id');
			$result = $risks->where('id',input('id'))->find();
			$data = array(
				'infoStr' =>json_encode($result),
			);
			$this->assign($data);
			$this->setMeta('黑名单');
			return $this->fetch('addBlacklist');
		}
	}
}
