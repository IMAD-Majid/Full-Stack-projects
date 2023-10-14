<?php
include "connect.php";

$sqls = [
    "create database facebook;",
    "create table facebook.users (
        userid varchar(64) not null,
        userpassword varchar(12) not null,
        primary key (userid)
    );",
	"insert into facebook.users
	(userid, userpassword)
	values
	('root', 't');",
    "create table facebook.expressions (
        expressionid int not null auto_increment,
        userid varchar(64) not null,
        createdat datetime,
        text varchar(500) not null,
        primary key (expressionid),
        foreign key (userid)
        references facebook.users(userid)
		on delete cascade
    );",
    "create table facebook.posts (
        postid int not null auto_increment,
        expressionid int not null,
        primary key (postid),
        foreign key (expressionid)
        references facebook.expressions(expressionid)
		on delete cascade
    );",
    "create table facebook.comments (
        commentid int not null auto_increment,
        expressionid int not null,
        postid int not null,
        primary key (commentid),
        foreign key (expressionid)
        references facebook.expressions(expressionid)
		on delete cascade,
        foreign key (postid)
        references facebook.posts(postid)
		on delete cascade
    );",
    "create table facebook.messages (
        messageid int not null auto_increment,
        expressionid int not null,
        friendid varchar(64) not null,
        primary key (messageid),
        foreign key (expressionid)
        references facebook.expressions(expressionid)
		on delete cascade,
        foreign key (friendid)
        references facebook.users(userid)
		on delete cascade
    );",
    "create table facebook.reaches (
        userid varchar(64) not null,
        expressionid int not null,
        constraint pk_reach primary key (userid, expressionid),
        foreign key (expressionid)
        references facebook.expressions(expressionid)
		on delete cascade,
        foreign key (userid)
        references facebook.users(userid)
		on delete cascade
    );"
];

foreach ($sqls as $sql){
    executeQuery($sql);
}

header("Location: login.php");

?>
