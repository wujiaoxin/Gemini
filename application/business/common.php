<?php
    /*
     *查询真实姓名
     * */
    function serch_real($uid){
        $real = db('member')->where('uid',$uid)->find();
        return $real['realname'];
    }
  function editmd($mid,$content){
        $result = db('member')->where('uid',$mid)->update($content);
          eco($result);
        return $result;
  }
  function serch_order($order_id){
    $result =db('order')->field('uid')->where('sn',$order_id)->find();
    return $result['uid'];
  }

  /*
  **操作资金
  **name 
  */
  function modify_account($data,$uid,$name=0,$money_type=0,$type=0,$memod=0){
    if(isset($data['money']) && $memod == 'INSERT'){
        if($type == 'rechange'){
           $pay_id = 'vpdai'.mt_rand(0,999).time();
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
        if($type == 'member_money'){
          $data_money = array(
              'uid'=>$uid,
              'account_money'=>$data['money'],
              'desc'=>$data['descr'],
              'type'=>$name,
              'deal_other'=>$money_type,
              'create_time'=>time()
          );
          db('dealer_money')->insert($data_money);
        }
        if ($type == 'withdraw') {
          $data_moneys = array(
                'user_id'=>$uid,
                'carry_billon'=>$data['carry_billon'],
                'is_pay'=>$data['is_pay'],
                'money'=>$data['money'],
                'pay_type'=>'0',
                'bank_name'=>$data['bank_name'],
                'update_time'=>$data['update_time'],
                'create_time'=>time()
            );
          $result = db('carry')->insert($data_moneys);
          if ($result){
                $resp["code"] = 1;
                $resp["msg"] = '处理中';
                return json($resp);
            }else{
                $resp["code"] = 0;
                $resp["msg"] = '提现失败';
                return json($resp);
            }
        }
    }

  }
  /*
  **生成还款列表
  */
  function set_order_repay($order_id){
    $uid = session('uid');
    $order = db('order')->where('sn',$order_id)->select();
    if ($order) {
      $repay_time = time()+$order[0]['endtime']*24*60*60;
      $order_repay = array(
          'order_id'=>$order_id,
          'mid'=>$uid,
          'repay_money'=>$order[0]['loan_limit'],
          'manage_money'=>$order[0]['free'],
          'repay_time'=>$repay_time,
          'status'=>'-1',
          'has_repay'=>'0',
          'loadtime'=>$order[0]['endtime'],
          'true_repay_money'=>'0',
          'true_repay_time'=>'0',
        );
      db('order_repay')->insert($order_repay);
    }
  }
  /*
  **订单处理
  */
  function cl_order($o_id,$bank_name){
    $uid = session('uid');
    $order = db('order')->where('sn',$o_id)->select();
    if ($order) {
      $is_success = db('order')->where('sn',$o_id)->setField('status','14');
      if ($is_success) {
        $datas =array(
            'carry_billon'=>$o_id,
            'is_pay'=>'0',
            'money'=>$order['0']['loan_limit'],
            'bank_name'=>$bank_name['0'],
            'create_time'=>time(),
            'update_time'=>'0',
            'descr'=>'提现申请'
          );
        modify_account($datas,$uid,'4','1','withdraw','INSERT');
        modify_account($datas,$uid,'4','1','member_money','INSERT');
        set_order_repay($o_id);
        $data['code'] = '1';
        $data['msg']='提现成功';

      }else{
        $data['code'] = '1';
        $data['msg']='提现失败';
      }
      // var_dump($data);
      return $data;
    }
  }