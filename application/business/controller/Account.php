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
		if (IS_POST) {
			$data = input('post.');
	      	// var_dump($data);die;
	      	$mobile = session("mobile");
			if (isset($data['smsVerify'])) {
			$storeSmsCode = session('smsCode');
			  if($data['smsVerify'] != $storeSmsCode){
			    return ['code'=>1005,'msg'=>'短信验证码错误'];
			  }
			}
			// 修改交易密码
			if (isset($data['newPayPwd'])) {
			    $user = model('User');
			    $result = $user->setpaypw($mobile,$data['newPayPwd']);
			    if($result){
			      return ['code'=>1,'msg'=>'支付密码设置成功'];
			    }else{
			      return ['code'=>1003,'msg'=>'两次密码不一致'];
			    }
			}
			//修改手机号
			if (isset($data['newMobile'])) {
				$result = db('member')->where('mobile',$mobile)->setField('mobile', $data['newMobile']);
				$result1 = db('dealer')->where('mobile',$mobile)->setField('mobile', $data['newMobile']);
				if ($result && $result1) {
					session('mobile',$data['newMobile']);
					return ['code'=>1,'msg'=>'手机号修改成功'];
				}else{
					return ['code'=>1000,'msg'=>'手机号修改失败'];
				}
			}
			//修改邮箱
			if (isset($data['mail'])) {
				$result = db('member')->where('mobile',$mobile)->setField('email', $data['mail']);
				if ($result) {
					return ['code'=>1,'msg'=>'邮箱添加成功'];
				}else{
					return ['code'=>1001,'msg'=>'邮箱修改失败'];
				}
  			}
		}else{
			$mobile = session("mobile");
      		$account = db('dealer')->alias('d')->join('__MEMBER__ m','d.mobile = m.mobile')->field('d.rep,d.idno,d.credit_code,m.password,d.mobile,m.email,d.name,m.paypassword')->where('m.mobile',$mobile)->find();
	      	if ($account){
	            $data['infoStr'] = json_encode($account);
	            $data = array(
	          		'info'=>$account,
	          		'infoStr'=>json_encode($account)
	          	);
	            // var_dump($data);die;
	            $this->assign($data);
	      	} else{
		        $data['code'] = '1';
		        $data['msg'] = '信息出错';
		        $this->assign(json_encode($data));
	        }
			return $this->fetch();
		}
	}
	
	public function balance() {
	    $mobile = session('mobile');
	    $uid = session('uid');
	    if (IS_POST) {
	    	$data = input('post.');
	    	if ($data['type'] == '1') {
	    		$carrys = db('dealer_money')->where('uid',$uid)->where('type','3')->select();
	    	}
	    	
			$payment = db('dealer_money')->where('uid',$uid)->where('type','4')->select();
			$info = array(
			  'carrys'=>$carrys,
			  'payment'=>$payment
			);
	    }else{
	      //资金记录
	      $info = get_money($uid,'money');
	      $data = array(
	          'info' => $info,
	          'infoStr'=>json_encode($info)
	        );
	      // var_dump($data);die;
	      $this->assign($data);
	      return $this->fetch();
	    }
	}
    /*
     * 充值
     * */
	public function recharge() {
		if(IS_POST){
			$data = input('post.');
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
			$paypassword = $data['paypassword'];
			$pay = db('member')->field('paypassword')->where('mobile',$mobile)->find();
			if(md5($paypassword.$mobile) == $pay['paypassword']){
			  foreach ($data['withdrawOrders'] as $k => $v) {
			   $resp=  cl_order($v,$data['bank_card']);
			   return $resp;
			  }
			}else{
			   return ['code'=>'0','msg'=>'交易密码错误'];
			}
		}else{
			$bankcard =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
			$map = array(
			    'mid'=>$uid,
			    'status'=>'10'
			  );
			$orders =db('order')->where($map)->select();
			foreach ($orders as $k => $v) {
			    $orders[$k]['realname'] = serch_real($v['uid']);
			}
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
		$bankcard =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
		$data = array(
				'info'=>$bankcard,
				'infoStr'=>json_encode($bankcard)
			);
		// var_dump($data);die;
		$this->assign($data);
		return $this->fetch();
	}
	public function transaction() {
		$uid = session('uid');
		if (IS_POST) {
			$data = input('post.');
			// var_dump($data);die;
			$map['uid']=$uid;
			if ($data['type']) {
				$map['type'] = $data['type'];
			}
			$info = db('dealer_money')->where($map)->order('id DESC')->select();
			if ($info) {
				$resp['code'] = '1';
				$resp['msg'] = 'OK';
				$resp['data'] = $info;
			}else{
				$resp['code'] = '1';
				$resp['msg'] = '暂无数据';
			}
			return json($resp);
		}
		return $this->fetch();
	}
	public function lineOfCredit() {
		$mobile = session('mobile');
		$credit_code = db('dealer')->field('lines_ky')->where('mobile',$mobile)->find();
		if (IS_POST) {
			$data = input('post.');
			$datas =array(
				'use_cred'=>$data['creditLimit'],
				'lock_cred'=>$credit_code['lines_ky'],
				'status'=>'0',
				'create_time'=>time(),
				'desrc'=>$data['descr']
				);
			$result = db('dealer_credit')->insert($datas);
			if ($result) {
				return ['code'=>1,'msg'=>'申请成功'];
			}else{
				return ['code'=>0,'msg'=>'申请失败'];
			}

		}
		return $this->fetch();
	}
	public function creditRecord(){
		if (IS_POST) {
			
		}else{
			
		}

	}
  	public function info() {
		$mobile = session('mobile');
		$deals = db('dealer')->field('name,credit_code,addr,city,forms,idno,rep,rep_idcard_pic,dealer_lic_pic')->where('mobile',$mobile)->find();
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
		$password = '011316';
		$mobile = session('mobile');
		$user = db('member')->where('mobile',$mobile)->find();
		// echo $user['paypassword'].'<br>';
		// echo md5($password.$mobile);
		// die;
		if(md5($password.$user['mobile']) === $user['paypassword']){
		    // echo 111;die;
		}
		return $this->fetch();
	}
}