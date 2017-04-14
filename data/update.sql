
/**1.增加商户描述和固定电话
*/
alter table gemini_member  add desc varchar(255)  null  COMMENT '商户描述';/*备注*/
alter table gemini_member  add tel varchar(255) null COMMENT '固定电话';/*固定电话*/


/*
**20170406添加
*/
alter table gemini_member  realname varchar(20)  null  COMMENT '真实姓名';/*真实姓名*/
alter table gemini_dealer  add idno varchar(20) NOT null  COMMENT '法人身份证号码';/*法人身份证*/
alter table gemini_dealer  add is_old tinyint(1) NOT null  COMMENT '证照状态';/*证照状态*/
alter table gemini_dealer  add radiotime int(10) NOT null  COMMENT '营业时间';/*营业时间*/
alter table gemini_dealer  add money numeric(20,2) NOT null  COMMENT '总金额';/*总金额*/
alter table gemini_dealer  add lines numeric(20,2) NOT null  COMMENT '信用额度';/*信用额度*/
alter table gemini_dealer  add b_money numeric(20,2) NOT null  COMMENT '保证金金额';/*保证金金额*/
alter table gemini_order  add mid int(11) NOT null  COMMENT '车商id';/*车商id*/
alter table gemini_order  add endtime int(11) NOT null  COMMENT '借款期限';/*借款期限*/
alter table gemini_order  add free int(11) NOT null  COMMENT '借款费用';/*借款费用*/

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
  `pay_type` tinyint(1) NOT NULL COMMENT '支付方式',
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


/*
20170410
*/
alter table gemini_dealer MODIFY radiotime VARCHAR(20);
alter table gemini_payment MODIFY pay_id VARCHAR(255)  NOT NULL COMMENT '充值订单号';
alter table gemini_carry MODIFY carry_billon VARCHAR(255)  NOT NULL COMMENT '提现订单号';
alter table gemini_member add paypassword varchar(255)  null  COMMENT '商家支付密码';/*真实姓名*/

/*
20170411
*/
alter table gemini_dealer add rep_idcard_back_pic int(11)  NOT NULL  COMMENT '法人身份证反面照片';

/*
20170412
*/
DROP TABLE IF EXISTS `gemini_dealer_credit`;
CREATE TABLE `gemini_dealer_credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `use_cred` decimal(20,2) NOT NULL COMMENT '操作额度',
  `lock_cred` decimal(20,2) NOT NULL COMMENT '冻结信用额度',
  `status` tinyint(1) NOT NULL COMMENT '状态 -1审核不通过，0审核中，1审核通过',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  `desrc` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='信用额度记录表';



-- 20170414
-- 加入充值支付方式
alter table gemini_payment add payment_type tinyint(3)  NOT NULL  COMMENT '充值方式1线下支付';
-- 加入提现费用
alter table gemini_carry add  fee int(11) default '0'  COMMENT '提现费用';
-- 加入提现银行流水
alter table gemini_carry add  serial_num varchar(255)   COMMENT '提现银行流水';