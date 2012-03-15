/*****************************************************
 * INSERT SAMPLE RECORDS 
 *****************************************************/

DELETE FROM media;

DELETE FROM tag;
INSERT INTO tag
(id, name) VALUES 
(1, 'サンプルタグ１'),
(2, 'サンプルタグ２'),
(3, 'サンプルタグ３')
;

DELETE FROM category;
INSERT INTO category
(id, name, parent_id) VALUES 
(-1, 'no_parent', null),
(1, 'サンプルカテゴリ１', -1),
(2, 'サンプルカテゴリ２', -1),
(3, 'サンプルカテゴリ３', -1)
;


DELETE FROM page;
INSERT INTO page
(id, title, contents, outline, status, category_id, account_id, create_date, update_date) VALUES 
(1, 'サンプルページ１', '<p>サンプルコンテンツ１（公開）</p>', 'アウトライン１', 1, 1, 1, now(), now()),
(2, 'サンプルページ２', '<p>サンプルコンテンツ１（下書き）</p>', 'アウトライン２', 0, 1, 1, now(), now()),
(3, 'サンプルページ１ 続き', '<p>サンプルコンテンツ１（公開）</p>', 'アウトライン１', 1, 1, 1, now(), now())
;


DELETE FROM page_tag;
INSERT INTO page_tag
(page_id, tag_id) VALUES 
(1, 1),
(2, 2)
;

DELETE FROM goal;
INSERT INTO goal
(id, page_count, target_month) VALUES 
(1, 15,  cast(date_format(date_add(now(), interval -2 month), '%Y-%m-1') as date)),
(2, 14,  cast(date_format(date_add(now(), interval -1 month), '%Y-%m-1') as date)),
(3, 15,  cast(date_format(date_add(now(), interval 0 month), '%Y-%m-1') as date))
;


DELETE FROM page_media;
;



