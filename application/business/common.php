<?php
  //查询真实姓名
  function serch_real($uid){
    $real = db('member')->where('uid',$uid)->find();
    return $real['realname'];
  }
  
  function editmd($mid,$content){
    $result = db('member')->where('uid',$mid)->update($content);
    return $result;
  }
  function serch_order($order_id){
    $result =db('order')->field('uid,type')->where('sn',$order_id)->find();
    return $result;
  }
  /*
  ** 操作资金
  ** name 操作类型(数字)
  ** money_type 操作金额
  ** type 操作类型
  ** momod 操作方法
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
                return json_encode($resp);
            }else{
                $resp["code"] = 0;
                $resp["msg"] = '充值失败';
                return json_encode($resp);
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
                return json_encode($resp);
            }else{
                $resp["code"] = 0;
                $resp["msg"] = '提现失败';
                return json_encode($resp);
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
          'manage_money'=>$order[0]['fee'],
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
      $is_success = db('order')->where('sn',$o_id)->setField('status','15');
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
        $resp['code'] = '1';
        $resp['msg']='提现成功';

      }else{
        $resp['code'] = '1';
        $resp['msg']='提现失败';
      }
      return json($resp);
    }
  }
  //发送验证码
  function sendSms($mobile, $content){
    //TODO: move to config module;
    $uid = '161110_hwj_hnkj';
    $pwd = 'me1989';
    $http = 'http://61.174.50.42:8080/sms/ylSend3.do';
    if (empty($mobile) || empty($content)) {
      return false;
    }
    $content = mb_convert_encoding($content, 'gbk', 'utf-8');//utf8 to gbk
    $data = array(
      'uid' => $uid, //用户账号
      'pwd' => $pwd,//strtolower(md5($pwd)), //MD5位32密码
      'rev' => $mobile, //号码
      'msg' => $content, //内容
      'sdt' => '', //定时发送
      'snd' => '101', //子扩展号
      //'encode' => 'utf8',
    );
    $param = '';
    while (list($k, $v) = each($data)) {
      $param .= rawurlencode($k) . "=" . rawurlencode($v) . "&"; //转URL标准码
    }
    $param = substr($param, 0, -1);   
    $url = $http.'?'.$param;    
    $rc = file_get_contents($url);    
    //TODO: 判断RC;   
    return true;
    /*
    $re = $this->postSMS($http, $data); //POST方式提交
    if (trim($re) == '100') {
      return true;
    } else {
      return "发送失败! 状态：" . $re;
    }*/
  }
  /*
  ** type 操作类型
  ** data 操作内容（数组）
  */
  function editemail($data,$type){
    if ($type == 'newPayPwd') {
     
    }
    if ($type =='newMobile') {
      
    }
    if ($type =='mail') {
      
    }
  }
  /*
  ** 资金记录
  ** uid 车商uid
  ** type 资金类型
  */
  function get_money($uid,$type){
    $mobile = session('mobile');
    if($type == 'money'){
      //可用资金(记录为可提订单金额)
        $map = array(
          'mobile'=>$mobile,
          'status'=>'11'
          );
        $money = db('order')->where($map)->sum('loan_limit');
      //借款金额（记录为状态未审核通过）
        $where = array(
            'mobile'=>$mobile,
            'status'=>'12'
          );
        $money_jk = db('order')->where($where)->sum('loan_limit');
      //待还资金
        $repay_money = db('order_repay')->where('mid',$uid)->sum('repay_money');
        // 借款中的订单
        $order_loan = db('order')->where('mid',$uid)->count('id');
        //还款中的订单
        $order_repay = db('order_repay')->where('mid',$uid)->count('id');
        $data = array(
          'available_money'=>$money,
          'loan_money'=>$money_jk,
          'repay_money'=>(string)$repay_money,
          'order_loan_num'=>$order_loan,
          'order_repay_num'=>$order_repay,
          );
    }
    if ($type =='lines') {
      # code...
    }
    return $data;
  }

  /*
  ** 订单排行
  ** type 业务类型
  ** money 金额排行
  ** num 数量排行
  ** avg 均价排行
  */
  function get_order($mid,$uid,$type){
    if ($type =='money') {
      $map = array(
          'uid'=>$uid,
          'status'=>'11'
        );
      $money = db('order')->where($map)->sum('loan_limit');
      $real_name = serch_real($uid);
      $data = array(
        'money'=>$money,
        'realname'=>$realname
        );
    }
    if ($type =='num') {
     
    }
    if ($type =='avg') {
     
    }
    return $data;
  }
  /*
  ** status 订单状态
  ** type 业务类型
  */
  function get_orders($mid,$status=0,$type){
    if ($type == 'order') {
       $data = db('order')->where('mid',$mid)->limit(4)->order('status ASC,id DESC')->select();
       foreach ($data as $k => $v) {
         if ($v['status'] == '-1') {
            $data[$k]['progress'] = '1';
         }elseif ($v['status'] >='0' && $v['status']<'3') {
           $data[$k]['progress'] = '30';
         }elseif ($v['status'] == '3') {
           $data[$k]['progress'] = '40';
         }elseif ($v['status'] == '4') {
           $data[$k]['progress'] = '45';
         }elseif ($v['status'] == '5') {
           $data[$k]['progress'] = '50';
         }elseif ($v['status'] == '6') {
           $data[$k]['progress'] = '80';
         }elseif ($v['status'] >= '10') {
           $data[$k]['progress'] = '90';
         }
       }
    }
    if ($type == 'order_repay') {
       $data = db('order_repay')->where('mid',$mid)->limit(4)->select();
    }
    if ($type == 'dealer_money') {
      $data = db('order_repay')->where('status ','>',3)->where('mid',$mid)->limit(4)->select();
    }
    return $data;
  }

  /*
  **时间类型
  */
  function to_datetime($dateRange){
    $begintime =strtotime(date('Y-m-d 00:00:00',time()));//开始时间
    switch ($dateRange) {
      case '1':
        $endtime = strtotime(date('Y-m-d 00:00:00',time()+3600*24));//结束时间    
        break;
      case '2':
      //7
        $endtime = strtotime(date('Y-m-d 00:00:00',time()-3600*24*7));//结束时间
        break;
      case '3':
      //one month
        $endtime = strtotime(date('Y-m-d 00:00:00',time()-3600*24*30));//结束时间
        break;
      case '4':
      // two moneth
        $endtime = strtotime(date('Y-m-d 00:00:00',time()-3600*24*60));//结束时间
        break;
      case '5':
        // three month
        $endtime = strtotime(date('Y-m-d 00:00:00',time()-3600*24*90));//结束时间
        break;
      default:
        break;
    }
    // $time = ' between '.$begintime.' and '.$endtime.' ';
    // $map['create_time'] = $time;
    $result = array(
      'endtime'=>$endtime,
      'begintime'=>$begintime
      );
    return $result;
  }