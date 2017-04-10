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
use app\common\model;

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
        // var_dump($data);die;
        $uid = session('uid');
        if (is_numeric($data['money'])){
            modify_account($data,$uid,'3','0','member_money','INSERT');
            modify_account($data,$uid,'rechange','INSERT');
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
        foreach ($data['withdrawOrders'] as $k => $v) {
          cl_order($v,$data['bank_card']);
        }
      }else{
        $bankone =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
        $banktwo =db('dealer')->field('priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
        $map = array(
            'mid'=>$uid,
            'status'=>'10'
          );
        $orders =db('order')->where($map)->select();
        foreach ($orders as $k => $v) {
            $orders[$k]['realname'] = serch_real($v['uid']);
        }
        $bankcard =[$bankone,$banktwo];
        $info = array(
            'bankcard'=>$bankcard,
            'orders'=>$orders
        );
        $data = array(
            'info'    => $info,
            'infoStr' => json_encode($info),
        );
        // var_dump($data);die;
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
      $deals = db('dealer')->field('name,credit_code,addr,city,forms,idno,rep,rep_idcard_pic')->where('mobile',$mobile)->find();
      if($deals){
          $data['code'] = '1';
          $data['info']=$deals;
          $data['infoStr'] = json_encode($deals);
          // var_dump($data);die;
          $this->assign($data);
      }
      return $this->fetch();
  }
  public function message() {
    return $this->fetch();
  }
    //设置手机号和邮箱
    public function setemail(){
      
    }
    //修改手机号和邮箱
    public function editeamil(){
      
    }
}