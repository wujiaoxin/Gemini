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
  ** type 资金类型
  */
  function get_money($uid,$type){
    $mobile = session('business_mobile');
    if($type == 'money'){
      //可用资金(记录为可提订单金额)
        $types = '2,4';
        $map = array(
          'mid'=>$uid,
          'finance'=>'3',
          'type'=>array('IN',$types)
          );
        $money = db('order')->where($map)->sum('examine_limit');
      //借款金额（记录为状态未审核通过）
        $where = array(
            'mid'=>$uid,
          );
        $name = '3,4,5';
        $where['status'] = array('IN',$name);
        $where['type'] = array('IN',$types);
        $money_jk = db('order')->where($where)->sum('examine_limit');
      //待还资金
         $uids = db('dealer')->alias('d')->field('d.id')->join('__MEMBER__ m','m.mobile = d.mobile')->where('m.uid',$uid)->find();
        $where_repay = array(
          'dealer_id'=>$uids['id'],
          'status'=>'-1',
          );
        $wheres = 'loantime < 50';
        $repay_money = db('order_repay')->where($where_repay)->where($wheres)->sum('repay_money');
        // 借款中的订单

        $order_loan = db('order')->where($where)->count('id');
        //还款中的订单
        $map_where = array(

          'dealer_id'=>$uids['id'],
          'status' => '-1',
          );
        $order_repay = db('order_repay')->where($map_where)->where($wheres)->count('id');
        $data = array(
          'available_money'=>$money,
          'loan_money'=>$money_jk,
          'repay_money'=>(string)$repay_money,
          'order_loan_num'=>$order_loan,
          'order_repay_num'=>$order_repay,
          );
    }
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
    
    $dealer_money = db('dealer')->alias('d')->field('money,lock_money')->join('__MEMBER__ m','d.mobile = m.mobile')->where('uid', $uid)->find();
    

    //待收金额和可用
    $map =array(

      'finance' => '3',

      'mid'=>$uid

      );
    $repay_moneys = db('order')->where($map)->sum('examine_limit');
    //总金额
    $total_money = $dealer_money['money'] + $dealer_money['lock_money'] + $repay_moneys ;

    $info = array(

      'uid'=>$uid,
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

  /*
**查询车商名称
*/
function serch_realname($uid){
  $result = db('member')->field('realname')->where('uid',$uid)->find();
  // var_dump($result);die;
  return $result['realname'];
}
/*
**查询车商名称
*/
function serch_name($uid){

  $result = db('dealer')->field('name as dealer_name')->where('id',$uid)->find();
  return $result;

}