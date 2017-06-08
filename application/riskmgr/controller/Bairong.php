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
		/*$result = '{ "Rule_final_decision": "Review", "Rule_final_weight": "5", "Rule_name_XJS030": "评分无值", "Rule_weight_XJS030": "5", "als_fst_cell_bank_inteday": "265", "als_fst_id_bank_inteday": "265", "als_lst_cell_bank_consnum": "1", "als_lst_cell_bank_csinteday": "1", "als_lst_cell_bank_inteday": "265", "als_lst_id_bank_consnum": "1", "als_lst_id_bank_csinteday": "1", "als_lst_id_bank_inteday": "265", "als_m12_cell_avg_monnum": "1.00", "als_m12_cell_bank_allnum": "1", "als_m12_cell_bank_avg_monnum": "1.00", "als_m12_cell_bank_max_inteday": "", "als_m12_cell_bank_max_monnum": "1", "als_m12_cell_bank_min_inteday": "", "als_m12_cell_bank_min_monnum": "0", "als_m12_cell_bank_orgnum": "1", "als_m12_cell_bank_selfnum": "0", "als_m12_cell_bank_tot_mons": "1", "als_m12_cell_max_inteday": "", "als_m12_cell_max_monnum": "1", "als_m12_cell_min_inteday": "", "als_m12_cell_min_monnum": "0", "als_m12_cell_tot_mons": "1", "als_m12_id_avg_monnum": "1.00", "als_m12_id_bank_allnum": "1", "als_m12_id_bank_avg_monnum": "1.00", "als_m12_id_bank_max_inteday": "", "als_m12_id_bank_max_monnum": "1", "als_m12_id_bank_min_inteday": "", "als_m12_id_bank_min_monnum": "0", "als_m12_id_bank_orgnum": "1", "als_m12_id_bank_selfnum": "0", "als_m12_id_bank_tot_mons": "1", "als_m12_id_max_inteday": "", "als_m12_id_max_monnum": "1", "als_m12_id_min_inteday": "", "als_m12_id_min_monnum": "0", "als_m12_id_tot_mons": "1", "brcreditpoint": "", "code": "00", "flag_applyloanstr": "1", "flag_execution": "0", "flag_ruleapplyloan": "0", "flag_ruleexecution": "0", "flag_rulescore": "0", "flag_rulespeciallist": "0", "flag_score": "0", "flag_specialList_c": "0", "swift_number": "3000592_20170516143251_2376" }';
		$res = json_decode($result,true);
		$arr = array();
		foreach ($res as $k => $v) {
			$arr[] =array('name'=>$k,'value'=>$v);
		}*/
		// var_dump($arr);die;
		// die;
		// $this->assign('res',$arr);
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
		

		$res = json_decode($results,true);
		$arr = array();
		foreach ($res as $k => $v) {
			$arr[] =array('name'=>$k,'value'=>$v);
		}
		
		$this->assign('res',$arr);
		
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

	//百融四要素验证
	/*public function verification_card($idcard = '', $name = '', $mobile = '', $bank = '' ,$password = ""){
		
		require_once("config.php");
		require_once("com.bairong.api.class.php");
		$headerTitle = array(
			"haina" => array(
				"BankFourPro"
			),
		);
		
		$targetList = array(
			array(
					"meal" => "BankFourPro",
					"name" => '王闫飞',
					"id" => '411527199101133522',
					"cell" => '18603821907',
					'bank_id'=>'6222620110009991101',
					
			)
		);
		CONFIG::init();

		$core = Core::getInstance(CONFIG::$account,CONFIG::$password,CONFIG::$apicode,CONFIG::$querys);

		$core -> pushTargetList($targetList);
		$core -> mapping($headerTitle);
		
		$results = $core -> query_result;
		$res = json_decode($results,true);
		if ($res['code'] == '600000') {
			$result = $res['product']['msg'];
		}else{
			$result = $res['code'];
		}
		
		return $result;
	}*/
}
