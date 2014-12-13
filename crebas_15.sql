/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2014/12/13 14:21:21                          */
/*==============================================================*/


drop table if exists code;

drop table if exists file;

drop table if exists notification;

drop table if exists printer;

drop table if exists user;

/*==============================================================*/
/* Table: code                                                  */
/*==============================================================*/
create table code
(
   id                   int not null auto_increment comment 'id',
   use_id               int not null comment '�û�_id',
   code                 char(32) comment '��',
   start_time           datetime comment '������ʱ��',
   type                 char(8) comment '����',
   primary key (id)
);

alter table code comment '��֤��';

/*==============================================================*/
/* Table: file                                                  */
/*==============================================================*/
create table file
(
   id                   int not null auto_increment comment 'id',
   use_id               int not null comment '�û�_id',
   pri_id               int not null comment '��ӡ��_id',
   name                 char(32) comment '�ļ���',
   url                  char(64) comment '�ļ����λ��',
   time                 datetime comment '�ļ��ϴ���ʱ��',
   requirements         char(100) comment '��ӡҪ�󣨱�ע��',
   amount               int comment '��ӡ����',
   sides_info           char(10) comment '��˫����Ϣ',
   status               char(10) comment '�ļ�״̬',
   primary key (id)
);

alter table file comment '�ļ�';

/*==============================================================*/
/* Table: notification                                          */
/*==============================================================*/
create table notification
(
   id                   int not null auto_increment comment 'id',
   fil_id               int not null comment '�ļ�_id',
   content              text comment '����',
   primary key (id)
);

alter table notification comment '֪ͨ��Ϣ';

/*==============================================================*/
/* Table: printer                                               */
/*==============================================================*/
create table printer
(
   id                   int not null auto_increment comment 'id',
   name                 char(20) comment '��ӡ�������',
   account              char(30) comment '�˺�',
   password             char(32) comment '����',
   address              char(30) comment '��ַ',
   phone                char(20) comment '�绰',
   qq                   char(15) comment 'QQ',
   primary key (id)
);

alter table printer comment '��ӡ��';

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   id                   int not null auto_increment comment 'id',
   student_number       char(10) comment 'ѧ��',
   password             char(32) comment '����',
   name                 char(6) comment '����',
   gender               char(3) comment '�Ա�',
   phone                char(20) comment '�绰',
   email                char(32) comment 'email',
   primary key (id)
);

alter table user comment '�û�';

alter table code add constraint FK_code_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table notification add constraint FK_notification_of_file foreign key (fil_id)
      references file (id) on delete restrict on update restrict;

