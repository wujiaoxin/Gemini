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

class bill extends Base {
	//多笔交易验证
	public function index() {
		return $this->fetch();
	}

	public function lst(){
		return view();
	}

	public function bill_list(){
			
			$data = input('post.');
			if($data['password'] != "yinlian"){
				return $this->error("查询密码错误");
			}
			$accout = \com\Yinlian::$accout;
			$server = \com\Yinlian::$service.'bill/merchant/verify';
			$datas =array(
				'account'=>$accout,
				'beginDate'=>$data['begindate'],
				'endDate'=>$data['enddate'],
				'mid'=>$data['mid'],
				'name'=>$data['name'],
				'type'=>$data['type'],
				);
			$sign = \com\Yinlian::buildSign($datas);
			$data1 =array(
				'account'=>$accout,
				'beginDate'=>$data['begindate'],
				'endDate'=>$data['enddate'],
				'mid'=>$data['mid'],
				'type'=>$data['type'],
				'sign'=>$sign,
				);
			$url = $server.'?'.http_build_query($data1).'&name='.$data['name'];
			$resp =  \com\Yinlian::sendHttpRequest($url);
			$data =json_decode($resp,true);
			if ($data['resCode'] != '0000') {
				$this->error($data['resMsg']);
			}
			$data =array(
				'infoStr'=>json_encode($data)
			);
			$this->assign($data);
			return view();
	}

	public function bill_verify(){
			$data = input('post.');
			if($data['password'] != "yinlian"){
				return $this->error("查询密码错误");
			}
			$accout = \com\Yinlian::$accout;
			$server = \com\Yinlian::$service.'bill/personal/verify';
			$data_one =array(
				'account'=>$accout,
				'beginDate'=>$data['begindate'],
				'card'=>$data['card'],
				'endDate'=>$data['enddate'],
				'name'=>$data['name'],
				);
			$sign = \com\Yinlian::buildSign($data_one);

			$res =array(
				'account'=>$accout,
				'beginDate'=>$data['begindate'],
				'card'=>$data['card'],
				'endDate'=>$data['enddate'],
				'name'=>$data['name'],
				'sign'=>$sign,
				);
			$url = $server.'?'.http_build_query($res);

			$resp =  \com\Yinlian::sendHttpRequest($url);

			$data =json_decode($resp,true);

			if ($data['resCode'] != '0000') {
				$this->error($data['resMsg']);
			}
			$data =array(
				'infoStr'=>json_encode($data)
			);
			$this->assign($data);

			return view();
	}

}
