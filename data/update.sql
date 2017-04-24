-- ----------------------------
-- add by fwj 20170418
-- ---------------------------- 
-- 
alter table gemini_member add desc varchar(255) DEFAULT '' COMMENT '商户描述';
alter table gemini_member add tel varchar(255) DEFAULT '' COMMENT '固定电话';
alter table gemini_member add realname varchar(20)  DEFAULT '' COMMENT '真实姓名';
alter table gemini_member add paypassword varchar(255) DEFAULT '' COMMENT '支付密码';
alter table gemini_member add bankcard varchar(64)  DEFAULT '' COMMENT '银行卡号码';
alter table gemini_member add idcard varchar(64)  DEFAULT '' COMMENT '身份证号码';
alter table gemini_member add headerimgurl varchar(1024)  DEFAULT '' COMMENT '用户头像';


alter table gemini_dealer  add idno varchar(20) DEFAULT '' COMMENT '法人身份证号码';
alter table gemini_dealer  add is_old tinyint(1) DEFAULT 0  COMMENT '证照状态';
alter table gemini_dealer  add money numeric(20,2) DEFAULT 0  COMMENT '总金额';
alter table gemini_dealer  add lines numeric(20,2) DEFAULT 0  COMMENT '信用额度';
alter table gemini_dealer  add b_money numeric(20,2) DEFAULT 0  COMMENT '保证金金额';
alter table gemini_dealer  add rep_idcard_back_pic int(11)  DEFAULT NULL  COMMENT '法人身份证反面照片';
alter table gemini_dealer  add lic_validity varchar(20) DEFAULT '' COMMENT '营业期限';
alter table gemini_dealer  add lock_money numeric(20,2) DEFAULT 0  COMMENT '冻结资金';

alter table gemini_order  add mid int(11) DEFAULT 0  COMMENT '车商id';
alter table gemini_order  add endtime int(11) NOT NULL  COMMENT '借款期限';
alter table gemini_order  add fee int(11) NOT NULL  COMMENT '借款费用';
alter table gemini_order  add credit_status int(11) DEFAULT 0  COMMENT '授信状态:1.待授信;2.授信中;3.已授信';

-- 修复充值字段表和提现字段表

DROP TABLE IF EXISTS `gemini_recharge`;
CREATE TABLE `gemini_recharge` (
  `uid` int(11) NOT NULL COMMENT '充值商户id',
  `sn` varchar(255) NOT NULL COMMENT '充值订单号',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '是否充值:0审核失败,1已充值,-1审核中',
  `money` int(11) NOT NULL COMMENT '充值金额',
  `pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付方式',
  `create_time` int(11) NOT NULL COMMENT '充值创建时间',
  `bank_name` varchar(255) NOT NULL DEFAULT '0' COMMENT '银行卡账户',
  `descr` varchar(255) NOT NULL COMMENT '充值备注',
  `recharge_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '充值方式:1线下充值',
  `platform_account` varchar(255) NOT NULL DEFAULT '0' COMMENT '平台账户',
  `serial_num` varchar(255) NOT NULL DEFAULT '0' COMMENT '充值流水号',
  `actual_amount` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '到账金额',
  `fee` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `fee_bear` varchar(255) NOT NULL DEFAULT '0' COMMENT '手续费承担方',
  PRIMARY KEY (`sn`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值记录表';

DROP TABLE IF EXISTS `gemini_carry`;
CREATE TABLE `gemini_carry` (
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '提现商户id',
  `sn` varchar(255) NOT NULL DEFAULT '0' COMMENT '提现订单号',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '是否提现:-1,提现处理中 0未提现,1已提现',
  `money` int(11) NOT NULL COMMENT '提现金额',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现方式',
  `create_time` int(11) NOT NULL COMMENT '提现创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新提现时间',
  `bank_account` varchar(255) NOT NULL DEFAULT '0' COMMENT '银行卡账户',
  `fee` int(11) DEFAULT '0' COMMENT '提现费用',
  `serial_num` varchar(255) DEFAULT '0' COMMENT '提现银行流水',
  `descr` varchar(255) NOT NULL DEFAULT '0' COMMENT '提现备注',
  PRIMARY KEY (`sn`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='提现表';

DROP TABLE IF EXISTS `gemini_dealer_money`;
CREATE TABLE `gemini_dealer_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '商户id',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '5支付款项,1垫资到账,2垫资还款,3充值,4提现',
  `deal_other` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0系统，1商户',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `total_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '账户总额',
  `account_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '操作金额',
  `use_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '可用资金',
  `lock_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结资金',
  `repay_money` decimal(20,2) DEFAULT NULL COMMENT '待收资金',
  `descr` varchar(255) NOT NULL DEFAULT 'NULL' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='资金记录表';


DROP TABLE IF EXISTS `gemini_order_repay`;
CREATE TABLE `gemini_order_repay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `mid` int(11) NOT NULL COMMENT '车商id',
  `true_repay_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '真实还款金额',
  `repay_money` decimal(20,2) NOT NULL COMMENT '还款金额',
  `manage_money` decimal(20,2) NOT NULL COMMENT '管理费',
  `repay_time` int(11) NOT NULL COMMENT '还款时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1还款中,0提前,1准时还款,2逾期还款',
  `has_repay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1未还,1已还,2逾期',
  `true_repay_time` int(11) NOT NULL COMMENT '真实还款时间',
  `loantime` int(11) NOT NULL COMMENT '还款期限',
  `repay_period` tinyint(2) DEFAULT '0' COMMENT '还款期数',
  `descr` varchar(255) NOT NULL DEFAULT '' COMMENT '回款备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='还款记录表';

DROP TABLE IF EXISTS `gemini_dealer_credit`;
CREATE TABLE `gemini_dealer_credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `use_cred` decimal(20,2) NOT NULL COMMENT '操作额度',
  `lock_cred` decimal(20,2) NOT NULL COMMENT '冻结信用额度',
  `status` tinyint(1) NOT NULL COMMENT '状态:-1审核不通过,0审核中,1审核通过',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  `desrc` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='信用额度记录表';

DROP TABLE IF EXISTS `gemini_collect_data`;
CREATE TABLE `gemini_collect_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID ',
  `key` varchar(255) NOT NULL COMMENT '键值',
  `value` MEDIUMTEXT NOT NULL COMMENT '内容',
  `group` varchar(255) NOT NULL DEFAULT 'NULL' COMMENT '分组',
  `from` varchar(32) NOT NULL DEFAULT 'NULL' COMMENT '数据来源',
  `format` tinyint(1) NOT NULL DEFAULT '0' COMMENT '格式:0默认,1序列化JSON字符串',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='原始数据采集表';
