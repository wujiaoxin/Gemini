<?php
  //查询真实姓名
  function serch_real($uid){
    $real = db('member')->where('uid',$uid)->find();
    return $real['realname'];
  }
  /*
  ** 操作资金
  ** name 操作类型(数字)
  ** money_type 操作金额
  ** type 操作类型
  ** momod 操作方法
  */
  function modify_account($data,$uid,$name=0,$money_type=0,$type=0,$memod=0){
     $dealer = db('dealer')->alias('d')->field('d.id as dealer_id')->join('__MEMBER__ m','d.mobile = m.mobile')->where('uid', $uid)->find();
    if(isset($data['money']) && $memod == 'INSERT'){
        if($type == 'recharge'){
           $sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
           $rec_money = array(
                'uid'=>$dealer['dealer_id'],
                'sn'=>$sn,
                'status'=>'-1',
                'money'=>$data['money'],
                'recharge_type'=>$data['recharge_type'],
                'descr'=>$data['descr'],
                'platform_account'=>$data['platform_account'],
                'create_time'=>time()
            );
            $result = db('recharge')->insert($rec_money);
            if ($result){
                $resp["code"] = 1;
                $resp["msg"] = '处理中';
                return $resp;
            }else{
                $resp["code"] = 0;
                $resp["msg"] = '充值失败';
                return $resp;
            }
        }
        if ($type == 'withdraw') {
          $data_moneys = array(
                'uid'=>$dealer['dealer_id'],
                'sn'=>$data['sn'],
                'status'=>'-1',
                'money'=>$data['money'],
                'type'=>'0',
                'bank_account'=>$data['bank_name'],
                'dealer_bank'=>$data['dealer_bank'],
                'dealer_bank_branch'=>$data['dealer_bank_branch'],
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
    if (isset($data['fee']) && $memod == 'INSERT') {
      $fee_money = array(
          'uid'=>$dealer['dealer_id'],
          'account_money'=>$data['fee'],
          'desc'=>'冻结订单为'.$data['order_id'].'的资金',
          'type'=>$name,
          'deal_other'=>'0',
          'create_time'=>time()
      );

      $result = db('dealer_money')->insert($fee_money);
      return $result;
    }
  }
  
  /*
  **订单处理
  */
  function cl_order($o_id,$bank_name){
    $uid = session('user_auth.uid');
    $order = db('order')->where('sn',$o_id)->select();
    if ($order) {

      $priv_bank_account_id = db('dealer')->where('priv_bank_account_id',$bank_name)->find();
      $bank_account_id = db('dealer')->where('bank_account_id',$bank_name)->find();

      $datas =array(
          'sn'=>$o_id,
          'status'=>'0',
          'money'=>$order['0']['examine_limit'],
          'bank_name'=>$bank_name,
          'create_time'=>time(),
          'update_time'=>'0',
          'descr'=>'提现申请'
        );
      if ($priv_bank_account_id) {
        $datas['dealer_bank'] = $priv_bank_account_id['priv_bank_name'];
        $datas['dealer_bank_branch'] = $priv_bank_account_id['priv_bank_branch'];
      }
      if ($bank_account_id) {
         $datas['dealer_bank'] = $bank_account_id['bank_name'];
         $datas['dealer_bank_branch'] = $bank_account_id['bank_branch'];
      }
      modify_account($datas,$uid,'4','1','withdraw','INSERT');

      //提现资金记录

      money_record($datas, $uid, 4, 1);

      $result = db('order')->where('sn',$o_id)->setField('finance', '4');
    }
    return $result;
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
  ** 首页资金记录
  ** uid 车商uid
  */
  function get_money($uid){
    $mobile = session('business_mobile');
    //可用资金(记录为可提订单金额)
      // $types = '2,4';
      $map = array(
        'mid'=>$uid,
        'finance'=>'3',
        // 'type'=>array('IN',$types)
      );
      $money = db('order')->where($map)->sum('examine_limit');

    //审核中（记录为状态未审核通过）
      $where = array( 'mid'=>$uid);
      $status = '0,3,4,5';
      $where['status'] = array('IN',$status);
      // $where['type'] = array('IN',$types);
      $money_jk = db('order')->where($where)->sum('loan_limit');
      $order_loan = db('order')->where($where)->count('id');

    //全部订单
      // $map['type'] = array('IN',$types);
      $money_total = db('order')->where('mid',$uid)->sum('loan_limit');
      $order_loan_total = db('order')->where('mid',$uid)->count('id');
      $data = array(
        'available_money'=>$money,
        'loan_money'=>$money_jk,
        'order_loan_num'=>$order_loan,
        'repay_money'=>$money_total,
        'order_repay_num'=>$order_loan_total
        );
    return $data;
  }
  /*
  **时间类型
  */
  function to_datetime($dateRange){
    $begintime =strtotime(date('Y-m-d 23:59:59',time()));//开始时间
    switch ($dateRange) {
      case '1':
        $endtime = strtotime(date('Y-m-d 00:00:00',time()));//结束时间    
        break;
      case '2':
      //7
        $endtime = strtotime('-7 day');//结束时间
        break;
      case '3':
      //one month
        $endtime = strtotime("-1 month");//结束时间
        break;
      case '4':
      // two moneth
        $endtime = strtotime('-2 month');//结束时间
        break;
      case '5':
        // three month
        $endtime = strtotime('-3 month');//结束时间
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



  /*
  ** 资金记录
  ** data array数据
  ** uid 交易者 
  ** type 交易类型
  ** name 交易对象
  */
  function money_record($data, $uid, $type = 0, $name){

    //冻结资金
    
    $dealer_money = db('dealer')->alias('d')->field('d.id as dealer_id,d.money,d.lock_money')->join('__MEMBER__ m','d.mobile = m.mobile')->where('uid', $uid)->find();
    

    //待收金额和可用
    $map =array(

      'finance' => '3',

      'mid'=>$uid

      );
    $repay_moneys = db('order')->where($map)->sum('examine_limit');
    //总金额
    $total_money = $dealer_money['money'] + $dealer_money['lock_money'] + $repay_moneys ;

    $info = array(

      'uid'=>$dealer_money['dealer_id'],
      'type'=> $type,
      'deal_other'=>$name,
      'create_time'=>time(),
      'total_money'=>$total_money,
      'account_money'=>$data['money'],
      'use_money'=>$dealer_money['money'],
      'lock_money'=>$dealer_money['lock_money'],
      'repay_money'=>$repay_moneys,
      'descr'=>$data['descr'],
      );
    $result = db('dealer_money')->insert($info);
    return $result;

  }