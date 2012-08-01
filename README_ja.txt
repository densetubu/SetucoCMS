============
README
============

--------------------------------------------
リリース情報
--------------------------------------------
SetucoCMS バージョン 1.2.0
2012/02/26 リリース

SetucoCMS バージョン 1.2.1
2012/05/27 リリース

SetucoCMS バージョン 1.2.2
2012/08/01 リリース

詳細な変更履歴は下記のファイルをご覧ください。
/CHANGELOG_ja.txt

--------------------------------------------
設定
--------------------------------------------
This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.

VHOSTの設定
---------------------

下記はVHOSTへの記述サンプルです。.

<VirtualHost *:80>
   DocumentRoot "/path/to/SetucoCMS/public"
   ServerName localhost

   <Directory "/path/to/SetucoCMS/public">
       Options FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

--------------------------------------------
システム要件
--------------------------------------------
SetutcoCMS 要件:
PHP 5.2.4 以降
MySQL Server 5.1 以降

--------------------------------------------
お問い合わせ／フィードバック
--------------------------------------------
何かお気づきの点などありましたら下記の連絡先までお願いいたします。
SetucoCMS-public ML:setucocms-public@lists.sourceforge.jp

--------------------------------------------
ライセンス
--------------------------------------------
ライセンス情報は下記のファイルをご覧ください。
/docs/COPYING.txt
