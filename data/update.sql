/*
**1.增加商户描述和固定电话
*/
alter table gemini_member  add desc varchar(255)  null;
alter table gemini_member  add tel varchar(255) null;