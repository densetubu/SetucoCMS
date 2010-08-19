○site表

・表の作成
CREATE TABLE site (
	name text,
	url text,
	comment text,
	keyword text,
	opend_date timestamp
);

・初期値の追加
INSERT INTO site (opend_date)
VALUES(now());

○ambition表
表の作成
CREATE TABLE ambition (
	ambition text
);

・初期値の追加
INSERT INTO ambition (ambition)
VALUES('任意の初期値');

○goal表
表の作成
CREATE TABLE goal (
	page_count int
	target_month timestamp
);

○media表
表の作成
CREATE TABLE media (
	id int auto_increment primary key,
	name text,
	type text,
	create_date timestamp,
	update_date timestamp,
	comment text
);

・初期値の追加
INSERT INTO goal (page_count)
VALUES('任意の初期値');
○category表
表の作成
CREATE TABLE category (
	id int_increment primary key,
	name text,
	parent_id int
);

○page表
表の作成
CREATE TABLE page (
	id int auto_increment primary key,
	title text,
	contents text,
	outline text,
	create_date timestamp,
	account_id int,
	status int,
	category int,
	update_date timestamp
);

○account表
表の作成
CREATE TABLE account (
	id int auto_increment primary key,
	login_id text unique,
	nickname text unique,
	password text
);

○page_tag表
表の作成
CREATE TABLE page_tag (
	page_id int,
	tag_id int,
	primary key(page_id,tag_id)
);

○tag表
表の作成
CREATE TABLE tag (
	id int auto_increment primary key,
	name text unique
);

○page_media表
表の作成
CREATE TABLE page_media (
	page_id int,
	media_id int,
	primary key(media_id,page_id)
);

索引の作成
CREATE INDEX page_tag_tag_index
ON page_tag(tag_id);