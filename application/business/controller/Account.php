<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\business\controller;
use app\business\controller\Baseness;

class Account extends Baseness {
	public function index() {
        $mobile = session("mobile");
        $account = db('dealer')->alias('d')->join('__MEMBER__ m','d.mobile = m.mobile')->field('d.rep,d.idno,d.credit_code,m.password,d.mobile,m.email')->where('m.mobile',$mobile)->find();
        if ($account){
            $data['code'] = '1';
            $data['info'] = $account;
            $accounst = json_encode($data);
            $this->assign($accounst);
        } else{
          $data['code'] = '1';
          $data['msg'] = '信息出错';
          $this->assign(json_encode($data));
        }
		return $this->fetch();
	}
	
	public function cashValue() {
		return $this->fetch();
	}

	public function cash() {
	  return $this->fetch();
	}

  public function value() {
    return $this->fetch();
  }

  public function banks() {
    return $this->fetch();
  }
  public function fundlist() {
    return $this->fetch();
  }
  public function lineOfCredit() {
    return $this->fetch();
  }
  public function infor() {
    $deals = db('dealer')->field('name,credit_code,addr,forms,rep,rep_idcard_pic')->where('mobile',$mobile)->find();
    if($deals){
       $data['code'] = '1';
       $data['info'] = json_encode($deals);
      prinf($data);
       $this->assign($data);
    }
    return $this->fetch();
  }
  public function message() {
    return $this->fetch();
  }
}