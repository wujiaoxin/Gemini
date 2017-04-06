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