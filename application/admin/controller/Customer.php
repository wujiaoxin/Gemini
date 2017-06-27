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

class Customer extends Admin {

	public function index() {
		
		$map = array(

			'access_group_id'  => '0',
			// 'm.idcard' => 'o.idcard_num',

			// 'm.mobile'=> 'o.mobile'
			
			);

		$list = db('member')->where($map)->select();

		// var_dump($list);die;

		$data = array(

			'infoStr' =>json_encode($list)
		);

		$this->assign($data);
		$this->setMeta('客户管理');
		return $this->fetch();

	}

	//查看
	public function view() {
		
		$id   = input('id', '', 'trim,intval');
		// echo $id;die;
		$id = 25;

		//todo 暂无表结构  评分和评级等级

		$Order = model('order');

		$result = $Order->search_order($id);
		// var_dump($result);die;
		$data = array(

			'infoStr' =>json_encode($result)
		);

		$this->assign($data);

		// var_dump($result);die;
		$this->assign($result);
		return $this->fetch();

	}
	
}