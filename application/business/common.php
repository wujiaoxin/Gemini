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
        if($type == 'recharge'){
           $sn = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
           $rec_money = array(
                'uid'=>$uid,
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
          // var_dump($data);die;
          $data_moneys = array(
                'uid'=>$uid,
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
          // echo $result;die;
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
          'uid'=>$uid,
          'account_money'=>$data['fee'],
          'desc'=>'冻结订单为'.$data['order_id'].'的资金',
          'type'=>$name,
          'deal_other'=>'0',
          'create_time'=>time()
      );
      // var_dump($fee_money);die;

      $result = db('dealer_money')->insert($fee_money);
      return $result;
    }
  }
  
  /*
  **订单处理
  */
  function cl_order($o_id,$bank_name){
    $uid = session('user_auth.uid');
    // echo $o_id;die;
    $order = db('order')->where('sn',$o_id)->select();

    
    // var_dump($order);die;
    if ($order) {

      $priv_bank_account_id = db('dealer')->where('priv_bank_account_id',$bank_name)->find();
      $bank_account_id = db('dealer')->where('bank_account_id',$bank_name)->find();
      // var_dump($bank_account_id);die;

      $datas =array(
          'sn'=>$o_id,
          'status'=>'0',
          'money'=>$order['0']['loan_limit'],
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
      // echo $uid;
      // var_dump($datas);die;
      modify_account($datas,$uid,'4','1','withdraw','INSERT');

      //提现资金记录

      money_record($datas, $uid, 4, 1);

      // $result = set_order_repay($o_id);
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
  ** 首页资金记录
  ** uid 车商uid
  ** type 资金类型
  */
  function get_money($uid,$type){
    $mobile = session('mobile');
    if($type == 'money'){
      //可用资金(记录为可提订单金额)
        $map = array(
          'mid'=>$uid,
          'finance'=>'3'
          );
        // var_dump($map);die;
        $money = db('order')->where($map)->sum('loan_limit');
        // var_dump($money);die;
      //借款金额（记录为状态未审核通过）
        $where = array(
            'mid'=>$uid,
          );
        $name = '3,4,5';
        $where['status'] = array('IN',$name);
        $money_jk = db('order')->where($where)->sum('loan_limit');
      //待还资金
        $where_repay = array(
          'mid'=>$uid,
          'status'=>'-1'
          );
        $repay_money = db('order_repay')->where($where_repay)->sum('repay_money');
        // 借款中的订单
        $order_loan = db('order')->where('mid',$uid)->count('id');
        //还款中的订单
        $order_repay = db('order_repay')->where('mid',$uid)->where('status','-1')->count('id');
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
       $data = db('order')->where('mid',$mid)->limit(5)->order('status ASC,id DESC')->select();
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
        $data = db('order_repay')->alias('o')->field('o.*,d.type')->join('__ORDER__ d','o.order_id = d.sn')->limit(5)->select();
       // $data = db('order_repay')->where('mid',$mid)->limit(4)->select();
    }
    if ($type == 'dealer_money') {
      $data = db('order_repay')->where('status ','>',3)->where('mid',$mid)->limit(5)->select();
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



  /*
  ** 资金记录
  ** data array数据
  ** uid 交易者 
  ** type 交易类型
  ** name 交易对象
  */
  function money_record($data, $uid, $type = 0, $name){

    // var_dump($data);die;
    //冻结资金
    
    $dealer_money = db('dealer')->alias('d')->field('money,lock_money')->join('__MEMBER__ m','d.mobile = m.mobile')->where('uid', $uid)->find();
    

    //待收金额和可用
    $map =array(

      'finance' => '3',

      'mid'=>$uid

      );
    $repay_moneys = db('order')->where($map)->sum('loan_limit');
    // var_dump($repay_moneys);die;
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
    // var_dump($info);die;
    $result = db('dealer_money')->insert($info);
    return $result;

  }