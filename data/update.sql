
/**1.增加商户描述和固定电话
*/
alter table gemini_member  add desc varchar(255)  null  COMMENT '商户描述';/*备注*/
alter table gemini_member  add tel varchar(255) null COMMENT '固定电话';/*固定电话*/


/*
**20170406添加
*/
alter table gemini_member add realname varchar(20)  null  COMMENT '真实姓名';/*真实姓名*/
alter table gemini_dealer  add idno varchar(20) NOT null  COMMENT '法人身份证号码';/*法人身份证*/
alter table gemini_dealer  add is_old tinyint(1) NOT null  COMMENT '证照状态';/*证照状态*/
alter table gemini_dealer  add radiotime int(10) NOT null  COMMENT '营业时间';/*营业时间*/
alter table gemini_dealer  add money numeric(20,2) NOT null  COMMENT '总金额';/*总金额*/
alter table gemini_dealer  add lines numeric(20,2) NOT null  COMMENT '信用额度';/*信用额度*/
alter table gemini_dealer  add b_money numeric(20,2) NOT null  COMMENT '保证金金额';/*保证金金额*/
alter table gemini_order  add mid int(11) NOT null  COMMENT '车商id';/*车商id*/
alter table gemini_order  add endtime int(11) NOT null  COMMENT '借款期限';/*借款期限*/
alter table gemini_order  add free int(11) NOT null  COMMENT '借款费用';/*借款费用*/
alter table gemini_dealer  add paypassword varchar(32) NOT null  COMMENT '交易密码';
alter table gemini_payment  add pwd_type tinyint(1) NOT null  COMMENT '支付方式';
alter table gemini_carry add free numeric(20,2) NOT null  COMMENT '提现费用';
alter table gemini_carry add status tinyint(1) NOT null  COMMENT '0待审核，1已付款，2未通过，3待付款';
alter table gemini_carry add bankzon varchar(255) null  COMMENT '银行流水号';
alter table gemini_carry add descr varchar(255) null  COMMENT '提现备注';
/*
**充值记录表
*/
DROP TABLE IF EXISTS `gemini_payment`;
CREATE TABLE `gemini_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '充值商户id',
  `pay_id` int(11) NOT NULL COMMENT '充值订单号',
  `is_pay` int(11) NOT NULL DEFAULT '0' COMMENT '是否充值0未充值1已充值-1审核中',
  `money` int(11) NOT NULL COMMENT '充值金额',
  `pay_type` tinyint(1) NOT NULL COMMENT '充值方式',
  `create_time` int(11) NOT NULL COMMENT '充值创建时间',
  `bank_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '银行卡账户',
  `descr` varchar(255) NOT NULL COMMENT '充值备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*
**提现记录表
*/
DROP TABLE IF EXISTS `gemini_carry`;
CREATE TABLE `gemini_carry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '提现商户id',
  `carry_billon` int(11) NOT NULL COMMENT '提现订单号',
  `is_pay` int(11) NOT NULL DEFAULT '0' COMMENT '是否提现0未提现1已提现',
  `money` int(11) NOT NULL COMMENT '提现金额',
  `pay_type` tinyint(1) NOT NULL COMMENT '提现方式',
  `create_time` int(11) NOT NULL COMMENT '提现创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新提现时间',
  `bank_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '银行卡账户',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*
**资金记录表
*/
DROP TABLE IF EXISTS `gemini_dealer_money`;
CREATE TABLE `gemini_dealer_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '商户id',
  `account_money` decimal(20,2) NOT NULL COMMENT '操作金额',
  `desc` varchar(255) NOT NULL COMMENT '备注',
  `type` tinyint(2) NOT NULL COMMENT '0支付款项，1垫资到账，2垫资还款，3充值，4提现',
  `deal_other` tinyint(1) NOT NULL COMMENT '0系统，1商户',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;