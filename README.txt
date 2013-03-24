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

SetucoCMS バージョン 1.4.0
2012/06/17 リリース

SetucoCMS バージョン 1.5.0
2012/09/08 リリース

SetucoCMS バージョン 1.6.0
2013/02/23 リリース

SetucoCMS バージョン 1.6.1
2013/03/24 リリース

詳細な変更履歴は下記のファイルをご覧ください。
/CHANGELOG_ja.txt

--------------------------------------------
システム要件
--------------------------------------------
SetutcoCMS 要件:
PHP 5.2.4 以降
MySQL Server 5.1 以降


--------------------------------------------
セッティング
--------------------------------------------

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


VirtualHostの設定
---------------------
インストール時に必要であればVirtualHostの設定を行います。
設定ファイルに以下を追加します。

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

VirtualHostを設定した場合、.htaccessを以下のように書き換えてください

#RewriteBase /setucocmsという記述がありますので、先頭の[#]を外します
/setucocmsのパスを、VirtualHostの設定で指定したDocumentRootのパスと同じにします


データベースの作成
---------------------
SetucoCMSでの推奨環境はMySQLです
phpmyadminなどを利用して、SetucoCMSをインストールするデータベースを作成します

レンタルサーバーを利用している場合は、レンタルサーバーの管理画面にデータベースを使用するなどのメニューがありますので、そちらからphpmyadminにアクセスしてインストール用のデータベースを作成します


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
Mail: setucocms@gmail.com
Twitter: https://twitter.com/setucocms

--------------------------------------------
ライセンス
--------------------------------------------
ライセンス情報は下記のファイルをご覧ください。
/docs/COPYING.txt
