<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\riskmgr\controller;
use app\common\controller\Base;
class Judgemen extends Base {
	//裁判文书
	public function index() {
		return $this->fetch();
	}

	public function lst(){
		return view();
	}
	public function judgemen_list($name='',$password =''){
		if($password != "yinlian"){
			return $this->error("查询密码错误", 'blacklist');
		}
		$entityName = input('name');

		$accout = \com\Yinlian::$accout;

		$server = \com\Yinlian::$service.'info/judgement';
		$data =array(
			'account'=>$accout,
			'entityName'=>$entityName,
			);
		$sign = \com\Yinlian::buildSign($data);

		$data1 =array(
			'account'=>$accout,
			'sign'=>$sign,
			);

		$url = $server.'?'.http_build_query($data1).'&entityName='.$entityName;
		$resp =  \com\Yinlian::sendHttpRequest($url);
		$data =json_decode($resp,true);
		if ($data['resCode'] != '0000') {
			$this->error($data['resMsg']);
		}
		$data =array(
			'infoStr'=>json_encode($data)
		);
		$this->assign('results',$resp);
		$this->assign($data);
		return view();
	}

	public function judgemen_detail($ids = '',$password=''){
		if($password != "yinlian"){
			return $this->error("查询密码错误", 'blacklist');
		}
		// $id = '0E5ED19DC9853FD4';测试
		
		$id = input('ids');

		$accout = \com\Yinlian::$accout;

		$server = \com\Yinlian::$service.'info/judgementDetail';
		$data =array(
			'account'=>$accout,
			'id'=>$id,
			);
		$sign = \com\Yinlian::buildSign($data);

		$data1 =array(
			'account'=>$accout,
			'id'=>$id,
			'sign'=>$sign,
			);

		$url = $server.'?'.http_build_query($data1);
		
		$resp =  \com\Yinlian::sendHttpRequest($url);

		$results =json_decode($resp,true);
		if ($data['resCode'] != '0000') {
			$this->error($data['resMsg']);
		}
		$data =array(
			'infoStr'=>json_encode($results)
		);
		$this->assign($data);

		return view();
	}

}
