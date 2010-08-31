DROP TABLE IF EXISTS page_tag ;
DROP TABLE IF EXISTS page_media;
DROP TABLE IF EXISTS page;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS site;
DROP TABLE IF EXISTS ambition;
DROP TABLE IF EXISTS goal;

/*タグ表*/
CREATE TABLE tag (
	id int auto_increment,
	name varchar(30) unique,
	PRIMARY KEY(id)
);

/*カテゴリ表*/
CREATE TABLE category (
	id int auto_increment,
	name text,
	parent_id int,
	primary key(id),
	FOREIGN KEY(parent_id) REFERENCES category(id)
	ON DELETE CASCADE
) TYPE=INNODB;

/*アカウント表*/
CREATE TABLE account (
	id int auto_increment,
	login_id varchar(30) unique,
	nickname varchar(30) unique,
	password text,
	PRIMARY KEY(id)
);

/*メディア表*/
CREATE TABLE media (
	id int auto_increment,
	name text,
	type text,
	create_date timestamp,
	update_date timestamp,
	comment text,
	PRIMARY KEY(id)
);

/*ページ表*/
CREATE TABLE page (
	id int auto_increment PRIMARY KEY,
	title text,
	contents text,
	outline text,
	create_date timestamp,
	account_id int REFERENCES account(id) ON DELETE CASCADE,
	status int,
	category_id int REFERENCES category(id) ON DELETE CASCADE,
	update_date timestamp
) TYPE=INNODB;

/*ページメディア表*/
CREATE TABLE page_media (
	page_id int REFERENCES page(id) ON DELETE CASCADE,
	media_id int REFERENCES media(id) ON DELETE CASCADE,
	PRIMARY KEY(media_id,page_id)
) TYPE=INNODB;

/*ページタグ表*/
CREATE TABLE page_tag (
	page_id int  REFERENCES page(id)
	ON DELETE CASCADE,
	tag_id int REFERENCES tag(id)
	ON DELETE CASCADE,
	PRIMARY KEY(page_id,tag_id)
) TYPE=INNODB;

/*サイト表*/
CREATE TABLE site (
	name text,
	url text,
	comment text,
	keyword text,
	opend_date timestamp
);

/*野望表*/
CREATE TABLE ambition (
	ambition text
);

/*目標表*/
CREATE TABLE goal (
	page_count int,
	target_month timestamp
);

/*索引の作成*/
CREATE INDEX page_tag_tag_index
ON page_tag(tag_id);

/*サイト表へ初期値の追加*/
INSERT INTO site (opend_date)
VALUES(now());

/*野望表へ初期値の追加*/
INSERT INTO ambition (ambition)
VALUES('任意の初期値');

/*目標表へ初期値の追加*/
INSERT INTO goal (page_count)
VALUES('任意の初期値');
