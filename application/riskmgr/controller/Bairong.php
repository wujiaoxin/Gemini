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

class Bairong extends Base {
	public function index() {
		return $this->fetch();
	}
	
	public function results($idcard = '', $name = '', $mobile = '', $password = "") {
		if($password != "bairong"){
			return $this->error("查询密码错误", 'index');
		}		
		require_once("config.php");
		require_once("com.bairong.api.class.php");
		$headerTitle = array(
			'huaxiang' => array(
				"Accountchange",
				"ApplyLoan",
				"SpecialList"
			)
		);

		$targetList = array(
			array(
					//"line_num" => "000001",
					"name" => $name,
					"id" => $idcard,
					"cell" => $mobile,
			)
		);

		CONFIG::init();

		$core = Core::getInstance(CONFIG::$account,CONFIG::$password,CONFIG::$apicode,CONFIG::$querys);

		$core -> pushTargetList($targetList);
		$core -> mapping($headerTitle);
		
		$results = $core -> query_result;
		
		$this->assign('query', $name);
		$this->assign('results', $results);
		
		$filename="query_log.txt";
		$handle=fopen($filename,"a+");
		if($handle){
			fwrite($handle,"=========百融=============\r\n");
			fwrite($handle, date("Y-m-d h:i:sa")."\r\n");
			fwrite($handle,$name."\r\n");
			fwrite($handle,$idcard."\r\n");
			fwrite($handle,$results."\r\n");
			fwrite($handle,"==========================\r\n\r\n");
		}
		fclose($handle);
		
		return $this->fetch();
	}
}
