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
	      	$mobile = session("business_mobile");
			if (isset($data['smsVerify'])) {
				$storeSmsCode = session('smsCode');
				if($data['smsVerify'] != $storeSmsCode){
					$resp['code'] = '1005';
					$resp['msg'] = '短信验证码错误';
					return json($resp);
				}
			}
			// 修改交易密码
			if (isset($data['newPayPwd'])) {
			    $user = model('User');
			    $result = $user->setpaypw($mobile,$data['newPayPwd']);
			    if($result){
					$resp['code'] = '1';
					$resp['msg'] = '支付密码设置成功';
					return json($resp);
			    }else{
			    	$resp['code'] = '1';
					$resp['msg'] = '两次密码不一致';
					return json($resp);
			    }
			}
			//修改手机号
			if (isset($data['newMobile'])) {
				$result = db('member')->where('mobile',$mobile)->setField('mobile', $data['newMobile']);
				$result1 = db('dealer')->where('mobile',$mobile)->setField('mobile', $data['newMobile']);
				if ($result && $result1) {
					session('mobile',$data['newMobile']);
					$resp['code'] = '1';
					$resp['msg'] = '手机号修改成功';
					return json($resp);
				}else{
					$resp['code'] = '1000';
					$resp['msg'] = '手机号修改失败';
					return json($resp);
				}
			}
			//修改邮箱
			if (isset($data['email'])) {
				$result = db('member')->where('mobile',$mobile)->setField('email', $data['email']);
				if ($result) {
					$resp['code'] = '1';
					$resp['msg'] = '邮箱添加成功';
					return json($resp);
				}else{
					$resp['code'] = '1001';
					$resp['msg'] = '邮箱添加失败';
					return json($resp);
				}
  			}
		}else{
			$mobile = session("business_mobile");
      		$account = db('dealer')->alias('d')->join('__MEMBER__ m','d.mobile = m.mobile')->field('d.rep,d.idno,d.credit_code,m.password,d.mobile,m.email,d.name,m.paypassword,d.credit_code,d.priv_bank_account_id')->where('m.mobile',$mobile)->find();
	      	if ($account){
	            $data['infoStr'] = json_encode($account);
	            $data = array(
	          		'info'=>$account,
	          		'infoStr'=>json_encode($account)
	          	);
	            $this->assign($data);
	      	} else{
		        $data['code'] = '1';
		        $data['msg'] = '信息出错';
		        $this->assign(json_encode($data));
	        }
			return $this->fetch();
		}
	}
	// 绑卡
	public function bindCard(){
		$mobile = session("business_mobile");
		if (request()->isPost()) {
			$data = input('post.');
			$map = array('bank_account_id'=>$data['CardNumber'],'type'=>1);
			$res = db('bankcard')->where($map)->find();
			$uids = db('Dealer')->field('id')->where('mobile',$mobile)->find();
			if (empty($res)) {
				$arr = array(
					'uid'=>$uids['id'],
					'type'=>1,
					'order_id'=>-3,
					'bank_account_id'=>$data['CardNumber'],
					'bank_account_name'=>$data['RealName'],
					'create_time'=>time(),
					'idcard'=>$data['IdentificationNo']
				);
				db('bankcard')->insert($arr);
			}
			$epay = new \epay\Epay();
			$ret = $epay::bankcard($data);
	        if (empty($ret)) {
	        	$resp['code'] = '0';
	        	$resp['msg'] = '绑卡异常,请联系客服';
	        	return json($resp);
	        }
	        $ret  = json_decode($ret,true);
	        if ($ret['ResultCode'] == '88') {
	        	$resp['code']='1';
	        	$resp['msg'] = '绑卡成功';
	        }else{
	        	$resp['code'] = '0';
	        	$resp['msg'] = $ret['Message'];
	        }
	        return json($resp);
		}
	}
	public function balance() {
	    $mobile = session('business_mobile');
	    $uid = session('user_auth.uid');
	    if (IS_POST) {
	    	$data = input('post.');
	    	$map['uid'] = $uid;
	    	if (isset($data['status'])) {
	    		if ($data['status'] != '') {
	    			$map['status'] = $data['status'];
	    		}
			}
	    	if ($data['type'] == '2') {
	    		if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];
					$carrys = db('carry')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->order('create_time DESC')->select();
				}else{
					$carrys = db('carry')->where($map)->order('create_time DESC')->select();
				}
	    		if ($carrys) {
					$resp['code'] = '1';
					$resp['msg'] = '数据正常';
					$resp['type'] = '2';
					$resp['data']= $carrys;
				}else{
					$resp['code'] = '0';
					// $resp['msg'] = '未查到数据';
				}
	    	}
	    	
	    	if ($data['type'] == '1') {
	    		if ($data['dateRange']) {
					$result = to_datetime($data['dateRange']);
					$endtime =$result['endtime'];
					$begintime = $result['begintime'];
					$recharge = db('recharge')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->order('create_time DESC')->select();
				}else{
					$recharge = db('recharge')->where($map)->order('create_time DESC')->select();
				}
				if ($recharge) {
					$resp['code'] = '1';
					$resp['msg'] = '数据正常';
					$resp['type'] = '1';
					$resp['data']= $recharge;
				}else{
					$resp['code'] = '0';
					// $resp['msg'] = '未查到数据';
				}
	    	}
			return json($resp);
	    	
	    }else{
	      //资金记录
	      $info = get_money($uid,'money');
	      
	      $dealer_money = db('dealer')->alias('d')->field('money as total_money')->join('__MEMBER__ m','d.mobile = m.mobile')->where('m.uid',$uid)->find();
	      
	      $info['money'] = $dealer_money['total_money'] + $info['available_money'];
	      
	      $data = array(
	          'info' => $info,
	          'infoStr'=>json_encode($info)
	        );
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
			$uid = session('user_auth.uid');
			if (is_numeric($data['money'])){
				//加入资金记录
				money_record($data, $uid, 3, 0);
			    $resp = modify_account($data,$uid,'recharge','INSERT');
			    return json($resp);
			}
		}else{
		  return $this->fetch();
		}
	}
    /*
     * 提现
     * */
  	public function withdraw() {
		$mobile = session('business_mobile');
		$uid = session('user_auth.uid');
		if(IS_POST){
			$data = input('post.');
			$paypassword = $data['paypassword'];
			$pay = db('member')->field('paypassword')->where('mobile',$mobile)->find();
			if (empty($pay['paypassword'])) {
				$resp['code'] = '2';
				$resp['msg'] = '未设置交易密码';
				return json($resp);
			}
			if(md5($paypassword.$mobile) == $pay['paypassword']){
				foreach ($data['withdrawOrders'] as $k => $v) {
					$resp1=  cl_order($v,$data['bank_card']);
			    }
			    if ($resp1) {
			    	$resp['code'] = '1';
      				$resp['msg']='提现处理中';
			    }else{
			    	$resp['code'] = '1';
      				$resp['msg']='提现失败';
			    }
			}else{
				$resp['code'] = '0';
				$resp['msg'] = '交易密码错误';
			}
			return json($resp);
		}else{
			$bankcard =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
			$types = '2,4';
			$map = array(
			    'o.mid'=>$uid,
			    'o.finance'=>'3',
			    'o.type'=> array('IN',$types)
			  );

			$orders =db('order')->alias('o')->field('o.*,m.realname')->join('__MEMBER__ m','m.uid = o.uid')->where($map)->select();
			$info = array(
			    'bankcard'=>$bankcard,
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
		$mobile = session('business_mobile');
		$uid = session('user_auth.uid');
		$bankcard =db('dealer')->field('bank_account_id,bank_name,priv_bank_account_id,priv_bank_name')->where('mobile',$mobile)->find();
		$data = array(
				'info'=>$bankcard,
				'infoStr'=>json_encode($bankcard)
			);
		$this->assign($data);
		return $this->fetch();
	}
	public function transaction() {
		$uid = session('user_auth.uid');
		if (IS_POST) {
			$data = input('post.');
			$map['uid']=$uid;
			if ($data['type']) {
				$map['type'] = $data['type'];
			}
			if ($data['dateRange']) {
				$result = to_datetime($data['dateRange']);
				$endtime =$result['endtime'];
				$begintime = $result['begintime'];
				$info = db('dealer_money')->where($map)->whereTime('create_time','between',["$endtime","$begintime"])->order('id DESC')->select();
			}else{
				$info = db('dealer_money')->where($map)->order('id DESC')->select();
			}
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
		$mobile = session('business_mobile');
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
		return $this->fetch('lineOfCredit');
	}
	public function creditRecord(){
		if (IS_POST) {
			
		}else{
			
		}
		return $this->fetch('creditRecord');
	}
  	public function info() {
		$mobile = session('business_mobile');
		$deals = db('dealer')->field('name,credit_code,addr,city,forms,idno,rep,rep_idcard_pic,dealer_lic_pic,invite_code')->where('mobile',$mobile)->find();
		if($deals){
			$deals['qrcode_url'] = 'https://pan.baidu.com/share/qrcode?w=512&h=512&url='.url("/public/wechat/user/register").'?authcode='.$deals['invite_code'];
			$data['code'] = '1';
			$data['info']=$deals;
			$data['infoStr'] = json_encode($deals);
			$this->assign($data);
		}
		return $this->fetch();
  	}
	public function message() {
		return $this->fetch();
	}
}