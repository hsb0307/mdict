
﻿
CREATE TABLE Users
(
  UserId NUMBER(10,0) NOT NULL
, UserName VARCHAR2(127)
, Password VARCHAR2(511)
, PasswordQuestion VARCHAR2(127)
, PasswordAnswer VARCHAR2(127)
, RealName VARCHAR2(127)
, Gender VARCHAR2(15)
, Birthday  date default sysdate not null
, PINCodes VARCHAR2(18)
, Mobile VARCHAR2(22)
, Telephone VARCHAR2(22)
, Company VARCHAR2(127)
, Email VARCHAR2(127)
, QQ VARCHAR2(15)
, WordCategory number(3,0) default (101) NOT NULL
, CreateDate date default sysdate not null
, IsApproved CHAR(1)
, IsPublic CHAR(1) default 0 not null
, RoleId NUMBER(2,0) default (0) NOT NULL
, Description VARCHAR2(1023)
, CONSTRAINT Users_PK PRIMARY KEY  ( UserID )
  ENABLE 
);

CREATE SEQUENCE Users_UserId INCREMENT BY 100 START WITH 1 MAXVALUE 999999999;

CREATE OR REPLACE TRIGGER BEFORE_INSERT_Users
  BEFORE INSERT ON Users FOR EACH ROW
BEGIN
  SELECT Users_UserId.nextval INTO :new.UserId FROM dual;
END;

CREATE TABLE DictionaryA
(
  WordId NUMBER(10) NOT NULL
, Chinese VARCHAR2(1023)
, Pinyin VARCHAR2(1023)
, Mongolian VARCHAR2(1023)
, MongolianLatin VARCHAR2(1023)
, MongolianCyrillic VARCHAR2(1023)
, English VARCHAR2(1023)
, Japanese VARCHAR2(1023)
, Russian VARCHAR2(1023)
, ChineseExampleSentence VARCHAR2(2048)
, MongolianExampleSentence VARCHAR2(2048)
, EnglishExampleSentence VARCHAR2(2048)
, JapaneseExampleSentence VARCHAR2(2048)
, RussianExampleSentence VARCHAR2(2048)
, QueryCode VARCHAR2(63)
, ExamineGroup number(3,0) NULL
, OriginalCategory number(3,0) NOT NULL
, WordCategory number(3,0) NOT NULL
, SourceDictionary number(7,0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, IsPublished number(1,0) default (0) NOT NULL
, Description VARCHAR2(2048)
, CreatedDate date default sysdate not null
, LastModifiedBy NUMBER(10,0) NULL
, LastModifiedDate date default sysdate not null
, CONSTRAINT DictionaryA_PK PRIMARY KEY  ( WordId )
  ENABLE 
);


CREATE SEQUENCE DictionaryA_WordId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER BEFORE_INSERT_DictionaryA
  BEFORE INSERT ON DictionaryA FOR EACH ROW
BEGIN
  SELECT DictionaryA_WordId.nextval INTO :new.WordId FROM dual;
END;



CREATE TABLE ApprovePackage
(
  PackageId NUMBER(10,0) NOT NULL
, PackageName VARCHAR2(127)
, UserId NUMBER(10,0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, CreateDate date default sysdate not null
, CONSTRAINT ApprovePackage_PK PRIMARY KEY  ( PackageId )
  ENABLE 
);

CREATE TABLE ApproveItems
(
  ItemId NUMBER(10,0) NOT NULL
, PackageId NUMBER(10,0) NOT NULL
, WordId NUMBER(10) NOT NULL
, CreateDate date default sysdate not null
, ModifiedDate date default sysdate not null
, Status number(3,0) default (0) NOT NULL
, CONSTRAINT ApproveItems_PK PRIMARY KEY  ( ItemId )
  ENABLE 
);

CREATE SEQUENCE ApprovePackage_PackageId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER Before_Insert_ApprovePackage
  BEFORE INSERT ON ApprovePackage FOR EACH ROW
BEGIN
  SELECT ApprovePackage_PackageId.nextval INTO :new.PackageId FROM dual;
END;

CREATE SEQUENCE ApproveItem_Id INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER Before_Insert_ApproveItems
  BEFORE INSERT ON ApproveItems FOR EACH ROW
BEGIN
  SELECT ApproveItem_Id.nextval INTO :new.ItemId FROM dual;
END;


CREATE TABLE RevisePackage
(
  PackageId NUMBER(10,0) NOT NULL
, PackageName VARCHAR2(127)
, UserId NUMBER(10,0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, CreateDate date default sysdate not null
, CONSTRAINT RevisePackage_PK PRIMARY KEY  ( PackageId )
  ENABLE 
);

CREATE TABLE ReviseItems
(
  ItemId NUMBER(10,0) NOT NULL
, PackageId NUMBER(10,0) NOT NULL
, WordId NUMBER(10) NOT NULL
, CreateDate date default sysdate not null
, ModifiedDate date default sysdate not null
, Status number(3,0) default (0) NOT NULL
, CONSTRAINT ReviseItems_PK PRIMARY KEY  ( ItemId )
  ENABLE 
);

CREATE SEQUENCE RevisePackage_PackageId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER Before_Insert_RevisePackage
  BEFORE INSERT ON RevisePackage FOR EACH ROW
BEGIN
  SELECT RevisePackage_PackageId.nextval INTO :new.PackageId FROM dual;
END;

CREATE SEQUENCE ReviseItem_Id INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER Before_Insert_ReviseItems
  BEFORE INSERT ON ReviseItems FOR EACH ROW
BEGIN
  SELECT ReviseItem_Id.nextval INTO :new.ItemId FROM dual;
END;



CREATE TABLE EditPackage
(
  PackageId NUMBER(10,0) NOT NULL
, PackageName VARCHAR2(127)
, UserId NUMBER(10,0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, CreateDate date default sysdate not null

, CONSTRAINT EditPackage_PK PRIMARY KEY  ( PackageId )
  ENABLE 
);

CREATE TABLE EditItems
(
  ItemId NUMBER(10,0) NOT NULL
, PackageId NUMBER(10,0) NOT NULL
, WordId NUMBER(10) NOT NULL
, CreateDate date default sysdate not null
, ModifiedDate date default sysdate not null
, Status number(3,0) default (0) NOT NULL
, CONSTRAINT EditItems_PK PRIMARY KEY  ( ItemId )
  ENABLE 
);

CREATE SEQUENCE EditPackage_PackageId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER BEFORE_INSERT_EditPackage
  BEFORE INSERT ON EditPackage FOR EACH ROW
BEGIN
  SELECT EditPackage_PackageId.nextval INTO :new.PackageId FROM dual;
END;

CREATE SEQUENCE EditItem_Id INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER Before_Insert_EditItems
  BEFORE INSERT ON EditItems FOR EACH ROW
BEGIN
  SELECT EditItem_Id.nextval INTO :new.ItemId FROM dual;
END;

CREATE INDEX DICTIONARYA_CHINESE ON DICTIONARYA (CHINESE ASC);
CREATE INDEX DICTIONARYA_SOURCEDICTIONARY ON DICTIONARYA (SOURCEDICTIONARY);


CREATE TABLE Logs
(
 LogId NUMBER(10,0) NOT NULL
, UserId NUMBER(10,0) NOT NULL
, CategoryId number(3,0) default (4) NOT NULL
, ModuleId number(3,0) default (0) NOT NULL
, OperationId NUMBER(5) default (0) NOT NULL
, OperationName VARCHAR2(255)
, ContentText VARCHAR2(3071)
, IPAddress VARCHAR2(63)
, CreateDate date default sysdate not null
, ObjectId NUMBER(10) default (0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, Description VARCHAR2(1023)
, CONSTRAINT Logs_PK PRIMARY KEY  ( LogId )
  ENABLE 
);

CREATE SEQUENCE Logs_LogId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;

CREATE OR REPLACE TRIGGER BEFORE_INSERT_Logs
  BEFORE INSERT ON Logs FOR EACH ROW
BEGIN
  SELECT Logs_LogId.nextval INTO :new.LogId FROM dual;
END;

CREATE TABLE Topics
(
 TopicId NUMBER(10,0) NOT NULL
, UserId NUMBER(10,0) NOT NULL
, Title VARCHAR2(255) NOT NULL
, FullText VARCHAR2(3071) NULL
, LastPostUserId NUMBER(10,0) NULL
, LastPostText VARCHAR2(3071)
, LastPostDate date NULL
, DateCreated date default sysdate not null
, Status number(3,0) default (0) NOT NULL
, CONSTRAINT Topic_PK PRIMARY KEY  ( TopicId )
  ENABLE 
);

CREATE SEQUENCE Topic_TopicId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;

CREATE OR REPLACE TRIGGER BEFORE_INSERT_Topics
  BEFORE INSERT ON Topics FOR EACH ROW
BEGIN
  SELECT Topic_TopicId.nextval INTO :new.TopicId FROM dual;
END;

CREATE TABLE Posts
(
 PostId NUMBER(10,0) NOT NULL
, TopicId NUMBER(10,0) NOT NULL
, UserId NUMBER(10,0) NOT NULL
, Title VARCHAR2(255) NULL
, FullText VARCHAR2(3071) NULL
, DateCreated date default sysdate not null
, Status number(3,0) default (0) NOT NULL
, CONSTRAINT Post_PK PRIMARY KEY  ( PostId )
  ENABLE 
);

CREATE SEQUENCE Post_PostId INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;

CREATE OR REPLACE TRIGGER BEFORE_INSERT_Posts
  BEFORE INSERT ON Posts FOR EACH ROW
BEGIN
  SELECT Post_PostId.nextval INTO :new.PostId FROM dual;
END;



CREATE TABLE ChineseEnglishDictionary
(
  WordId NUMBER(10) NOT NULL
, Chinese VARCHAR2(1023)
, Pinyin VARCHAR2(1023)
, English VARCHAR2(1023)
, Japanese VARCHAR2(1023)
, QueryCode VARCHAR2(63)
, POS VARCHAR2(31)
, WordCategory number(4,0) default (0) NOT NULL
, Status number(3,0) default (0) NOT NULL
, LastModifiedDate date default sysdate not null
, CONSTRAINT ChineseEnglishDictionary_PK PRIMARY KEY  ( WordId )
  ENABLE 
);


CREATE SEQUENCE ChineseEnglishDictionary_Id INCREMENT BY 1 START WITH 1 MAXVALUE 999999999;
CREATE OR REPLACE TRIGGER BEFORE_INSERT_ChineseEnglishDictionary
  BEFORE INSERT ON ChineseEnglishDictionary FOR EACH ROW
BEGIN
  SELECT ChineseEnglishDictionary_Id.nextval INTO :new.WordId FROM dual;
END;