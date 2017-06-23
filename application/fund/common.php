<?php
  
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
