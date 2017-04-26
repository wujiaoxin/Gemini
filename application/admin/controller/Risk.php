<?php

namespace app\admin\controller;
use app\common\controller\Admin;

class risk extends Admin {
 
	public function index() {
		return ""; 
	}

	public function rating() {
		
		$creditList = db('credit')->alias('c')->join('__MEMBER__ m','c.uid = m.uid')->where("credit_status",3)->order('id desc')->fetchSQL(false)->select();
		//TODO: JOIN 不支持 field?...
		
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
 
			
			$result = db('credit')->where('id', $data['id'])->update($data);
			
			if ($result) {
				return $this->success("提交成功！", url('rating'));
			} else {
				return $this->error("提交失败！");
			}
			
		}else{
			$creditList = db('credit')->alias('c')->join('__MEMBER__ m','c.uid = m.uid')->where("id",$id)->order('id desc')->fetchSQL(false)->find();			
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
