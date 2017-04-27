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
			$result = db('credit')->where('id', $data['id'])->fetchSQL(false)->update($data);			
			//var_dump($result);
			if($data['credit_result'] == 1){//授信审核通过
				$mobile = $data['mobile'];//TODO 手机号查库
				$orderData['status'] = 0;
				db('order')->where("mobile",$mobile)->where("status",-2)->update($orderData);//TODO result判断 事务操作 保证数据完整性
			}
			
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
		$this->setMeta('黑名单');
		return $this->fetch();
	}

	public function addBlacklist() {
		$this->setMeta('黑名单');
		return $this->fetch('addBlacklist');
	}
}
