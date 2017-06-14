<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\pay\controller;
use app\common\controller\Base;

class Notify extends Base {
	public function index() {
		return $this->fetch();
	}
	/*
	* 绑卡异步处理
	*/
	public function bangCard(){
	
		$data = input('');
		$result = json_decode($data,true);

		if ($result['SignInfo'] == '88') {
			$arr = array(
				'moneymoreid'=>$result['MoneymoremoreId']
			);
			$map = array(
				'type'=>1,
				'idcard'=> $result['IdentificationNo']
			);
			db('bankcard')->where($map)->update($arr);

		}
		$filename="bangCard_log.txt";
		$handle=fopen($filename,"a+");
		if($handle){
			fwrite($handle,"=========双乾绑卡功能=============\r\n");
			fwrite($handle, date("Y-m-d h:i:s")."\r\n");
			fwrite($handle,$data."\r\n");
			fwrite($handle,"==========================\r\n\r\n");
		}
		fclose($handle);
		echo "SUCCESS";die;
	}
}
