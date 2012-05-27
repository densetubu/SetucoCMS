============
README
============

--------------------------------------------
リリース情報
--------------------------------------------
SetucoCMS バージョン 1.0.0
2011/03/04 リリース

SetucoCMS バージョン 1.1.0
2011/12/30 リリース

SetucoCMS バージョン 1.2.0
2012/02/26 リリース

SetucoCMS バージョン 1.3.0
2012/03/17 リリース

SetucoCMS バージョン 1.3.1
2012/05/27 リリース

詳細な変更履歴は下記のファイルをご覧ください。
/CHANGELOG_ja.txt

--------------------------------------------
システム要件
--------------------------------------------
SetutcoCMS 要件:
PHP 5.2.4 以降
MySQL Server 5.1 以降

--------------------------------------------
SETTING
--------------------------------------------
This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.

Setting Up Your VHOST
---------------------

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/path/to/SetucoCMS/public"
   ServerName localhost

   <Directory "/path/to/SetucoCMS/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>


.htaccessの設定
---------------------
public/ディレクトリにある「.htaccess.sample」を「.htaccess」にファイル名を変えてコピーします
VirtualHostの設定をした場合は.htaccessの設定はここで終わりです

そうでない場合は.htaccessの編集をします
「#RewriteBase」の行頭の「#」を取り除いて、パラメーターをアプリケーションのパスに書き換えてください

設置するアドレスが http://example.com/setucocms/ の場合の例

public/ディレクトリがwebルートになるように設定をします

    RewriteBase /setucocms/public


ディレクトリの権限
---------------------
一部のディレクトリは、画像などのファイルアップロードやインストーラー用に書き込み権限が必要になります
WEBサーバーに書き込み権限を与えるようにパーミッションの設定をします
パーミッションについてよくわからない場合は権限を「777」 に設定してください

コマンドの場合は以下のように設定します
$ chmod 777 public/media application/configs/


インストーラへアクセス
---------------------
設定が完了したらインストーラへアクセスしましょう
設置したURLの末尾に install/ をつけてアクセスします

    http://example.com/install/

データベースやサイト情報に関する設定はここから始まります


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
