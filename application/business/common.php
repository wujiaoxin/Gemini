<?php
  //查询真实姓名
  function serch_real($uid){
    $real = db('member')->where('uid',$uid)->find();
    return $real['realname'];
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