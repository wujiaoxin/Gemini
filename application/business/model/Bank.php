<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 模型基类
*/
class Bank extends \think\Model{

	protected $name = "Dealer";
	/**
	 * 银行卡判断
	 * @return 私户或者公户信息
	 */
	public function get_bank($bank_name){

		$priv_bank_account_id = $this->where('priv_bank_account_id',$bank_name)->find();

		$bank_account_id = $this->where('bank_account_id',$bank_name)->find();

		if ($priv_bank_account_id) {

			$datas['dealer_bank'] = $priv_bank_account_id['priv_bank_name'];

			$datas['dealer_bank_branch'] = $priv_bank_account_id['priv_bank_branch'];

		}
		if ($bank_account_id) {

			$datas['dealer_bank'] = $bank_account_id['bank_name'];

			$datas['dealer_bank_branch'] = $bank_account_id['bank_branch'];
		}
		return $datas;
	}
}