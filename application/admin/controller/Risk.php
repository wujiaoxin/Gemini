<?php

namespace app\admin\controller;
use app\common\controller\Admin;

class risk extends Admin {
 
	public function index() {
		return ""; 
	}

	public function rating() {
		$creditList = db('credit')->alias('c')->field('c.id,c.uid,c.order_id,c.credit_status,c.credit_result,c.credit_score,c.create_time,c.credit_level,o.name as realname,o.idcard_num as idcard,o.dealer_id,o.uid as umid,d.name as dealer_name,m.realname as u_realname')->join('__ORDER__ o','c.order_id = o.id','LEFT')->join('__MEMBER__ m','m.uid = o.uid','LEFT')->join('__DEALER__ d','d.id = o.dealer_id','LEFT')->where("c.credit_status",3)->order('id desc')->fetchSQL(false)->select();
		
		$data = array(
			'infoStr' =>json_encode($creditList),
		);
		$this->assign($data);
		$this->setMeta('客户评级');
		return $this->fetch();
	}

	public function ratingInfo($id = null) {
		
		if (IS_POST) {
			$data = input('post.');
			//黑名单
			$res_crd = db('credit')->where('id', $data['id'])->find();

			if ($res_crd['credit_result'] != '0') {
				return $this->error("已审核！");
			}

			if ($data['refuse_reason'] == '3' && $res_crd['credit_result'] == '0') {
				$risks = model('Risk');
				
				
				$res = db('credit')->alias('c')->field('c.uid,c.order_id,m.realname,m.idcard,m.bankcard')->join('__MEMBER__ m','c.uid = m.uid')->where('c.id',$data['id'])->find();
				$collect = db('collect_data')->where('uid',$res_crd['uid'])->order('id DESC')->select();
				foreach ($collect as $k => $v) {
					if ($v['key'] == 'device') {
						$datas['device_number'] = $v['value'];
					}
				}
				$datas['idcard'] = $res['idcard'];
				$datas['bankcard'] = $res['bankcard'];
				$datas['uid'] = $res['uid'];
				$datas['order_id'] = $res['order_id'];
				$datas['mobile'] = $data['mobile'];
				$datas['name'] = $res['realname'];
				$datas['data_sources'] = '1';
				$risks->save($datas);
			}
			if ($data['credit_result'] == '-1') {
				if ($data['refuse_reason'] == '1'  && $res_crd['credit_result'] == '0') {
					$data['credit_result'] = $data['treatment'];
					$data['refuse_reason'] = '1';
				}
			}

			
			//评级通过生成金融方案
			if ($data['credit_result'] == '1') {
				$data['uid'] = $res_crd['uid'];
				$data['order_id'] = $res_crd['order_id'];
				$program = model('Programme');
				$program->result($data);
				$data['refuse_reason'] = '0';
			}
			
			$result = db('credit')->where('id', $data['id'])->fetchSQL(false)->update($data);			
			//var_dump($result);
			/*if($data['credit_result'] == 1){//授信审核通过
				$mobile = $data['mobile'];//TODO 手机号查库
				$orderData['status'] = 0;
				db('order')->where("mobile",$mobile)->where("status",-2)->update($orderData);//TODO result判断 事务操作 保证数据完整性
			}
			*/
			
			if ($result) {
				return $this->success("提交成功！", url('rating'));
			} else {
				return $this->error("提交失败！");
			}
			
		}else{
			$creditList = db('credit')->alias('c')->field('c.*,m.realname,m.idcard,o.car_price,m.bankcard,o.create_time')->join('__MEMBER__ m','c.uid = m.uid')->join('__ORDER__ o','c.order_id = o.id')->where("c.id",$id)->order('id desc')->fetchSQL(false)->find();
			$collect = model('Collect');
			$manualVerification = db('customer_info')->where('credit_id',$creditList['id'])->find();
			$basic_info = array(
				'realname'=>$creditList['realname'],
				'idcard'=>$creditList['idcard'],
				'bankcard'=>$creditList['bankcard'],
				'mobile'=>$creditList['mobile'],
				'salesman_carprice'=>$creditList['car_price'],
				'year'=>getIDCardInfo($creditList['idcard']),
				'platform'=>get_collect($creditList['uid'],'platform','device'),//设备平台
				'addr'=>get_collect($creditList['uid'],'addr','location'),
				'wanip'=>get_collect($creditList['uid'],'wanip','network'),
				'device'=>get_collect($creditList['uid'],'device','device'),//设备型号
				'phone_serial'=>get_collect($creditList['uid'],'imei','device'),//国际移动设备身份码
				'salesman_qrcode'=>$creditList['create_time']
				);//基本信息
			$programme = db('programme')->where(['uid'=>$creditList['uid'],'order_id'=>$creditList['order_id']])->find();
			$where = array(
				'uid'=>$creditList['uid'],
				'key'=>'message',
				'group'=>'message'
				);
			$message_info = db('Collect_data')->field('value')->where($where)->select();
			if ($message_info) {
				$message_info = json_decode($message_info['0']['value'],true);//短信列表
			}
			if (is_array($creditList)) {
				$creditList = array_merge($creditList,$basic_info);
			}


			$creditList =array(
				'creditList'=>$creditList,
				'programme'=>$programme,
				'manualVerification' =>$manualVerification,
				'basicInfo'=>$basic_info
				);
			$data = array(
				'infoStr' =>json_encode($creditList),
			);
			// var_dump($data);die;
			$this->assign($data);
			$this->assign('message',$message_info);
			$this->setMeta('评级详情');
			return $this->fetch('ratingInfo');
		}
	}

	public function blacklist() {
		$risks = model('Risk');
		if (IS_POST) {
			$data = input('post.');
			$result = db('Member_blacklist')->where('id', $data['id'])->update($data);
			if ($result) {
				return $this->success("提交成功！", url('blacklist'));
			} else {
				return $this->error("提交失败！");
			}
		}else{
			$result = $risks->select();
			$data = array(
				'infoStr' =>json_encode($result),
			);
			$this->assign($data);
			$this->setMeta('黑名单');
			return $this->fetch();
		}

	
	}

	public function addBlacklist() {
		$risks = model('Risk');
		if (IS_POST) {
			$data = input('post.');
			$res = $risks->where('id',$data['id'])->find();
			if ($res['status'] == 0) {
				$data['status'] = '1';
				$result = $risks->where('id', $data['id'])->update($data);
			}else{
				return $this->error('已审核');
			}
			
			if ($result) {
				return $this->success("提交成功！", url('blacklist'));
			} else {
				return $this->error("提交失败！");
			}
		}else{
			$id = input('id');
			$result = $risks->where('id',input('id'))->find();
			$data = array(
				'infoStr' =>json_encode($result),
			);
			$this->assign($data);
			$this->setMeta('黑名单');
			return $this->fetch('addBlacklist');
		}
	}


	public function manualVerification(){
		
		if (IS_POST) {
			$data = input('post.');
			
			$res_crd = db('credit')->where('id', $data['id'])->find();

			$is_success = db('customer_info')->field('uid')->where('uid',$res_crd['uid'])->find();

			if (!empty($is_success)) {
				return $this->error('已保存过信息');
			}

			$info = $data['manual_verification'];
			
			$results = json_decode($info,true);

			$results['uid'] = $res_crd['uid'];

			$results['credit_id'] = $data['id'];
			
			$res = db('customer_info')->insert($results);

			if ($res) {
				return $this->success('保存成功');
			}else{
				return $this->error('保存失败');
			}
		}
	}

}
