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
	
	public function balance() {
		return $this->fetch();
	}
    /*
     * 充值
     * */
	public function recharge() {
      if(IS_POST){
          $data = input('post.');
          $uid = session('uid');
          $pay_id = mt_rand(0,999).time();
          if (is_numeric($data['money'])){
              /*
               * 资金记录
               * */
              $data_money = array(
                  'uid'=>$uid,
                  'account_money'=>$data['money'],
                  'deal_other'=>'0',
                  'descr'=>$data['descr'],
                  'create_time'=>time()
              );
              db('dealer_money')->insert($data_money);
              /*
               * 充值记录
               * */
              $rec_money = array(
                  'user_id'=>$uid,
                  'pay_id'=>$pay_id,
                  'is_pay'=>'-1',
                  'money'=>$data['money'],
                  'pay_type'=>$data['pay_type'],
                  'descr'=>$data['descr'],
                  'create_time'=>time()
              );
              $result = db('payment')->insert($rec_money);
              if ($result){
                  $resp["code"] = 1;
                  $resp["msg"] = '处理中';
                  return json($resp);
              }else{
                  $resp["code"] = 0;
                  $resp["msg"] = '充值失败';
                  return json($resp);
              }
          }
        }else{
            return $this->fetch();
        }
	}
    /*
     * 提现
     * */
  public function withdraw() {
      $mobile = session('mobile');
      $uid = session('uid');
      if(IS_POST){
          $data = input('post.');

      }else{
          $deals =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
          $orders =db('order')->where('mid',$uid)->select();
          foreach ($orders as $k => $v) {
              $orders[$k]['realname'] = serch_real($v['uid']);
          }
          $info = array(
              'info'=>$deals,
              'orders'=>$orders
          );
          $data = array(
              'info'    => $info,
              'infoStr' => json_encode($info),
          );
          $this->assign($data);
          return $this->fetch();
      }
  }

  public function bankcard() {
      $mobile = session('mobile');
      $uid = session('uid');
      $deals =db('dealer')->field('bank_name,bank_account_id,priv_bank_name,priv_bank_account_id')->where('mobile',$mobile)->find();
      $this->assign($deals);
      return $this->fetch();
  }
  public function transaction() {
    return $this->fetch();
  }
  public function lineOfCredit() {
    return $this->fetch();
  }
  public function info() {
      $mobile = session('mobile');
      $deals = db('dealer')->field('name,credit_code,addr,forms,rep,rep_idcard_pic')->where('mobile',$mobile)->find();
      if($deals){
          $data['code'] = '1';
          $data['info'] = json_encode($deals);
          $this->assign($data);
      }
      return $this->fetch();
  }
  public function message() {
    return $this->fetch();
  }
    /*
     * 账户设置
     * */
    public function addacout(){
        if (IS_POST){

        }
    }
}