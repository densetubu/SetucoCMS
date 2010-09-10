/*****************************************************
* CREATE TABLES
******************************************************/

-- タグ表 
CREATE TABLE tag (
    id      INT AUTO_INCREMENT NOT NULL,
    name    VARCHAR(255) UNIQUE NOT NULL,
    PRIMARY KEY(id)
);

-- アカウント表 
CREATE TABLE account (
    id       INT AUTO_INCREMENT NOT NULL,
    login_id VARCHAR(255) UNIQUE NOT NULL,
    nickname VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    PRIMARY KEY(id)
);

-- メディア表 
CREATE TABLE media (
    id          INT AUTO_INCREMENT NOT NULL,
    name        TEXT NOT NULL,
    type        TEXT NOT NULL,
    create_date TIMESTAMP NOT NULL,
    update_date TIMESTAMP NOT NULL,
    comment     TEXT,
    PRIMARY KEY(id)
);

-- カテゴリ表 
CREATE TABLE category (
    id          INT AUTO_INCREMENT NOT NULL,
    name        VARCHAR(255) NOT NULL,
    parent_id   INT NOT NULL,
    PRIMARY KEY(id),
    UNIQUE(name, parent_id),
    FOREIGN KEY(parent_id) REFERENCES category(id) ON DELETE CASCADE
) TYPE=INNODB;

-- ページ表 
CREATE TABLE page (
    id          INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    title       TEXT NOT NULL,
    contents    TEXT NOT NULL,
    outline     TEXT,
    create_date TIMESTAMP NOT NULL,
    account_id  INT NOT NULL REFERENCES account(id) ON DELETE CASCADE,
    status      INT NOT NULL,
    category_id INT REFERENCES category(id) ON DELETE CASCADE,
    update_date TIMESTAMP NOT NULL
) TYPE=INNODB;

-- ページメディア表 
CREATE TABLE page_media (
    page_id     INT NOT NULL REFERENCES page(id) ON DELETE CASCADE,
    media_id    INT NOT NULL REFERENCES media(id) ON DELETE CASCADE,
    PRIMARY KEY(media_id,page_id)
) TYPE=INNODB;

-- ページタグ表 
CREATE TABLE page_tag (
    page_id     INT NOT NULL REFERENCES page(id) ON DELETE CASCADE,
    tag_id      INT NOT NULL REFERENCES tag(id) ON DELETE CASCADE,
    PRIMARY KEY(page_id,tag_id)
) TYPE=INNODB;

-- サイト表 
CREATE TABLE site (
    id          INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name        TEXT NOT NULL,
    url         TEXT NOT NULL,
    comment     TEXT,
    keyword     TEXT,
    open_date   TIMESTAMP NOT NULL
);

-- 野望表 
CREATE TABLE ambition (
    id          INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    ambition    TEXT NOT NULL
);

-- 更新目標表 
CREATE TABLE goal (
    id           INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    page_count   INT NOT NULL,
    target_month TIMESTAMP NOT NULL
);


/*****************************************************
* CREAETE INDEX 
******************************************************/

-- 索引の作成 --
CREATE INDEX page_tag_tag_id_index
ON page_tag(tag_id);


/*****************************************************
* INSERT DEFAULTS
******************************************************/

-- 初期値の登録
INSERT INTO site 
    (name, url, comment, keyword, open_date)
VALUES
    ('日本電子専門学校 電設部', 'http://penguin.jec.ac.jp/', '日本電子専門学校電設部SetucoCMSプロジェクトです。', 'せつこ,俺だ,結婚,してくれ', '2010-02-11 10:00:00');

INSERT INTO ambition 
    (ambition)
VALUES
    ('せつこーおれだーけっこｎ（ｒｙ');

INSERT INTO goal 
    (page_count, target_month)
VALUES
    (13, '2010-02-01 00:00:00'),
    (15, '2010-03-01 00:00:00'),
    (10, '2010-04-01 00:00:00'),
    (14, '2010-05-01 00:00:00'),
    (14, '2010-06-01 00:00:00'),
    (14, '2010-07-01 00:00:00'),
    (14, '2010-08-01 00:00:00'),
    (17, '2010-09-01 00:00:00');
