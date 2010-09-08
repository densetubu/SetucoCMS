<?php
/**
 * ファイル管理のコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     akitsukada
 */

/**
 * ファイル管理画面の操作を行うコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     akitsukada
 */
class Admin_MediaController extends Setuco_Controller_Action_Admin
{

    /**
     * アップロードできるファイルサイズの最大値
     * @todo 最大サイズを決める
     */
    const MAX_FILE_SIZE = 500000;
    
    /**
     * ページネーターー用の、1ページあたりのデータ表示件数
     * @todo 外だしする
     */
    const PAGE_LIMIT = 10;  

    /**
     * SetucoCMSで扱えるファイルの種類（拡張子）
     * 
     * @var array
     */
    private $_fileTypes = array('all', 'jpg', 'gif', 'png', 'pdf', 'txt');
 
    /**
     * MimeTypeと拡張子の変換用配列
     * 
     * @var array
     */
    private $_mimeTypes = array(
        'application/pdf'    => 'pdf',
        'image/jpeg'         => 'jpg',
        'image/gif'          => 'gif',
        'image/png'          => 'png',
        'text/plain'         => 'txt'
    );
    
    /**
     *
     * ファイルのアップロードフォームやアップロードしてあるファイルの一覧を表示するページです
     *
     * @return void
     * @author akitsukada
     * @todo 複数ファイルアップロードの対応
     */
    public function indexAction()
    {

        // ページネーターーのカレントページの取得
        $currentPage = $this->_getPage();

        // ファイルタイプの絞り込み条件取得(デフォルトでは'all')
        $type = 'all';
        if ($this->getRequest()->isPost()) {
            $type = $this->_fileTypes[$this->_getParam('fileType', 0)]; // 絞込みフォームでの指定
            $currentPage = 1;
        } else {
            $type = $this->_getParam('type', 'all');     // ソートリンクでの指定
        }
       
        // ソート指定のカラムとオーダー取得 (デフォルトではファイル名'name'の昇順'asc')
        $sort  = $this->_getParam('sort', 'name'); 
        $order = $this->_getParam('order',  'asc'); 

        // データ取得の条件を作る
        $condition = array(
            'type'  => $type,
            'sort'  => $sort,
            'order' => $order
        );
        
        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->condition = $condition;

        // 条件にあったファイルデータの取得をサービスに指示
        $mediaService = new Admin_Model_Media();
        $mediaData    = $mediaService->findMedias($condition, $currentPage, self::PAGE_LIMIT);
        $count        = $mediaService->countMedias($condition);

        // viewにファイルデータを渡す
        $this->view->mediaData = $mediaData;

        // アップロードできる最大サイズをKB換算でviewに教える
        $this->view->maxFileSize = (int)(self::MAX_FILE_SIZE / 1024);

        // viewにファイルアップロード用フォームを設定
        $this->view->uploadForm = $this->_createUploadForm();
        
        // viewにファイル絞込み・ソート用フォームの作成
        $this->view->searchForm = $this->_createSearchForm($condition);        
        
        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($count);

        // フラッシュメッセージ用の設定
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
        
    }


    /**
     * ファイルのアップロード処理です
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     * @todo エラー判定とその処理をちゃんと書く
     * @todo サムネイルの生成と保存
     */
    public function createAction()
    {
        
        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/admin/media/index');
        }
        
        // Mediaサービスを用意
        $service = new Admin_Model_Media();
        
        // ファイル受信に使うadapterの作成とバリデータの設定
        $adapter  = new Zend_File_Transfer_Adapter_Http();
        $adapter->addValidator('FilesSize', false, array(1, self::MAX_FILE_SIZE, false)) 
                ->addValidator('Count', false, array('min' => 1, 'max' => 5))
                ->addValidator('Extension', false, 'jpg,png,gif,pdf,txt');

        // すべてのファイルを検証
        if (!$adapter->isValid()) { 
            $this->_helper->flashMessenger('ファイルのサイズオーバーか、または対応外のファイル形式です。');
            $this->_redirect('/admin/media/index');
        }
        
        // 保存名に使う新しいファイルIDを取得
        $mediaId = $service->createNewMediaID();
        
        // 拡張子を取得
        $extType = $this->_mimeTypes[$adapter->getMimeType()];
        
        // DBに保存するオリジナルファイル名を取得
        $fileInfo = $adapter->getFileInfo();
        $origName = $fileInfo['upload_img']['name'];
        
        // ファイルの保存先と保存名を指定
        $adapter->setDestination($this->_getUploadDest());
        $adapter->addFilter('Rename', array( // 別名を指定
        	'target' => $this->_getUploadDest() . '/' . $mediaId . '.' . $extType, 
        	'overwrite' => true
        ));
        
        // ファイルの受信と保存
        if (!$adapter->receive()) {
            $this->_helper->flashMessenger('ファイルが正しく送信されませんでした。');
            $this->_redirect('/admin/media/index');
        }
        
        // サービスにファイルの情報を渡してDB登録させる
        $dat = array(
            'id'         => $mediaId,
            'name'       => $origName,
            'type'       => $extType, 
            'comment'    => ''
   		);
        if (!$service->updateMediaInfo($dat)) {
            $this->_helper->flashMessenger('ファイルが正しく保存できませんでした。');
            $this->_redirect('/admin/media/index');
        }
        
        // 処理正常終了
        $this->_helper->flashMessenger('ファイルをアップロードしました。');
        $this->_redirect('/admin/media/index');

    }


    /**
     * ファイルの更新操作をするページです
     *
     * @return void
     * @author akitsukada
     * 
     */
    public function formAction()
    {

        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        if ($id === null) {
            $this->_redirect('/admin/media/index');
        }
        
        // IDに該当するファイル情報をサービスから取得
        $service = new Admin_Model_Media();
        $mediaData = $service->findMediaById($id);

        // ビューにファイル情報を渡す
        $this->view->mediaData = $mediaData; 
        
        // フォームの作成とviewへのセット
        $this->view->updateForm = $this->_createUpdateForm($id, $mediaData['name'], $mediaData['comment']);
    
        // アップロードできる最大サイズをKB換算でviewに教える
        $this->view->maxFileSize = (int)(self::MAX_FILE_SIZE / 1024);
        
        // フラッシュメッセージ用の設定
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
        
    }
    
    
    /**
     * ファイル更新処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     */
    public function updateAction()
    {
        
        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/admin/media/index');
        }
    
        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        if ($id === null) {
            $this->_redirect('/admin/media/index');
        }
        
        // formアクションへのリダイレクトURL
        $redirectUrl = '/admin/media/form/id/' . $id;
        
        // Mediaサービスを用意
        $service = new Admin_Model_Media();
        
        // 更新フォームオブジェクト取得
        $form = $this->_createUpdateForm($id);
        
        // Postデータ取得
        $post = $this->getRequest()->getPost();
        
        if (!$form->isValid($post)) {
            $this->_helper->flashMessenger('ファイルが更新できませんでした。');
            $this->_redirect($redirectUrl);
        }
        
        // ファイル受信に使うadapterの作成
        $adapter  = new Zend_File_Transfer_Adapter_Http();
        // ファイル関連はファイルが選択された場合のみ処理
        if ($adapter->getFileName()) {

            // ファイル受信バリデータの設定
            $adapter->addValidator('FilesSize', false, array(1, self::MAX_FILE_SIZE, false)) 
                    ->addValidator('Count', false, array('min' => 0, 'max' => 1)) // updateでは１件のファイルのみを扱うとする
                    ->addValidator('Extension', false, 'jpg,png,gif,pdf,txt');
                    
            // すべてのファイルを検証
            if (!$adapter->isValid()) { 
                $this->_helper->flashMessenger('ファイルのサイズオーバーか、または対応外のファイル形式です。');
                $this->_redirect($redirectUrl);
            }
            
            // 保存名に使う新しいファイルIDを取得
            $mediaId = $service->createNewMediaID();
            
            // 拡張子を取得
            $extType = $this->_mimeTypes[$adapter->getMimeType()];
            
            // ファイルの保存先と保存名を指定
            $adapter->addFilter('Rename', array( // 別名を指定
            	'target' => $this->_getUploadDest() . '/' . $id . '.' . $extType, 
            	'overwrite' => true
            ));
            
            // ファイルの受信と保存
            if (!$adapter->receive()) {
                $this->_helper->flashMessenger('ファイルが正しく送信されませんでした。');
                $this->_redirect($redirectUrl);
            }
            
            
            // サービスにファイルの情報を渡してUpdateさせる
            $dat = array(
                'id'         => $id,
                'name'       => $post['name'],
                'type'       => $extType, 
                'comment'    => $post['comment']
       		);
            if (!$service->updateMediaInfo($dat)) {
                $this->_helper->flashMessenger('ファイルが正しく更新できませんでした。');
                $this->_redirect($redirectUrl);
            }
        }
        
        
        // 処理正常終了
        $this->_helper->flashMessenger('ファイル情報を更新しました。');
        $this->_redirect($redirectUrl);
        
    }


    /**
     * ファイル削除処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     */
    public function deleteAction()
    {
        
        $id = $this->_getParam('id');
        if ($id === null) {
            $this->_redirect('/admin/media/index');
        }
        
        $service = new Admin_Model_Media();
        if (!$service->deleteMedia($id)) {
            $this->_helper->flashMessenger('ファイル情報を削除できませんでした。');
            $this->_redirect('/admin/media/index');
        }

        $this->_helper->flashMessenger('ファイルを削除しました。');
        $this->_redirect('/admin/media/index');
    
    }

   
    /**
     * ページネーターで使う現在の（クリックされた）ページ番号を取得するメソッドです
     * 
     * @return int 現在ページネーターで表示すべきページ番号
     * @author akitsukada
     */
    private function _getPage()
    {
        // URLからページ番号の指定を得る ( デフォルトは1 )
        $currentPage = $this->_getParam('page');
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }
        return $currentPage;
    }

    
    /**
     * ファイル新規アップロード用フォームを作成するメソッドです
     * 
     * @return Zend_Form ファイルの新規アップロード用フォームオブジェクト
     * @author akitsukada
     * @todo 複数ファイルのアップロード
     */
    private function _createUploadForm()
    {
        
        // フォームオブジェクト作成
        $uploadForm = new Zend_Form();
        $uploadForm->setMethod('post');
        $uploadForm->setAction('/admin/media/create');
        $uploadForm->setAttrib('enctype', 'multipart/form-data');
        
        // ファイル選択パーツ作成と余分な装飾タグの除去
        $fileSelector = new Zend_Form_Element_File('upload_img', array('size' => 55));
        $fileSelector->setLabel(null)
                     ->addDecorator('Label', array('tag' => null));
      
        // submitボタンの作成と余分な装飾タグの除去
        $uploadFormSubmit = new Zend_Form_Element_Submit(
        	'up', array('class' => 'upSub', 'Label' => 'アップロード')
        );
        $uploadFormSubmit->clearDecorators()
                         ->addDecorator('ViewHelper');
        
        // 作成したパーツをフォームに追加
        $uploadForm->addElement($fileSelector);
        $uploadForm->addElement($uploadFormSubmit);

        // フォームを返す
        return $uploadForm;

    }
    

    /**
     * ファイルの絞込み・ソート用フォームを作成するメソッドです
     * 
     * @return Zend_Form ファイルの絞込み・ソート用フォームオブジェクト
     * 
     */
    private function _createSearchForm($condition)
    {
        $searchForm = new Zend_Form();
        $searchForm->setMethod('post');
        $searchForm->setAction('/admin/media/index');        
        
        // ファイルタイプのセレクトボックス
        $typeSelector = new Zend_Form_Element_Select('fileType');
        $typeSelector->clearDecorators()
                     ->setLabel('ファイルの種類')
                     ->setValue(array_search($condition['type'], $this->_fileTypes))
                     ->addDecorator('ViewHelper')
                     ->addDecorator('Label', array('tag' => null));
                     
        $typeSelector->addMultiOptions($this->_fileTypes);
        $typeSelector->addMultiOption(0, ' --指定なし-- ' );
        
        
        // 絞込みボタン
        $searchFormSubmit = new Zend_Form_Element_Submit(
        	'search', array('class' => 'upSub', 'Label' => '絞込み')
        );
        $searchFormSubmit->clearDecorators()
                         ->addDecorator('ViewHelper');
                    
        $searchForm->addElement($typeSelector);
        $searchForm->addElement($searchFormSubmit);
        
        return $searchForm;
        
    }
    
    
    /**
     * ファイルの更新（=上書きアップロード）用フォームを作成するメソッドです
     * 
     * @return Zend_Form ファイルの更新（=上書きアップロード）用フォームオブジェクト
     * @author
     * 
     */
    private function _createUpdateForm($id, $name = null, $comment = null) 
    {
        // 編集用フォームの作成
        $updateForm = new Zend_Form();
        $updateForm->setMethod('post');
        $updateForm->setAction('/admin/media/update/id/' . $id);
        $updateForm->setAttrib('enctype', 'multipart/form-data');
        
        // ファイル名編集テキストボックス
        $txtFileName = new Zend_Form_Element_Text('name');
        $txtFileName->clearDecorators()
                    ->setRequired(true)
                    ->setValue($name)
                    ->addDecorator('ViewHelper')
                    ->addDecorator('Label', array('tag' => null));

        // ファイルの説明テキストボックス
        $txtFileComment = new Zend_Form_Element_Text('comment');
        $txtFileComment->clearDecorators()
                       ->setRequired(false)
                       ->setValue($comment)
                       ->addDecorator('ViewHelper')
                       ->addDecorator('Label', array('tag' => null));
                       
        // ファイル選択パーツ作成と余分な装飾タグの除去
        $fileSelector = new Zend_Form_Element_File('upload_img', array('size' => 55));
        $fileSelector->setLabel(null)
                     ->setRequired(false)
                     ->addDecorator('Label', array('tag' => null))
                     ->addDecorator('HtmlTag', null);
                     
        // submitボタンの作成と余分な装飾タグの除去
        $btnSubmit = new Zend_Form_Element_Submit(
        	'up', array('class' => 'upSub', 'Label' => '編集を保存する')
        );
        $btnSubmit->clearDecorators()
                  ->addDecorator('ViewHelper');
                    
        // 作成したパーツをフォームに設定
        $updateForm->addElement($txtFileName);
        $updateForm->addElement($txtFileComment);
        $updateForm->addElement($fileSelector);;
        $updateForm->addElement($btnSubmit);
        
        // フォームを返す
        return $updateForm;

    }
    
       
    /**
     * ファイルのアップロード先ディレクトリを得るメソッドです。ディレクトリが存在しない場合は作成します。
     * 
     * @return string ファイル(サムネイルではない)のアップロード先ディレクトリ名
     * @todo ディレクトリ作成時のパーミッション検討？
     */
    private function _getUploadDest() 
    {
        
        $dir = APPLICATION_PATH . '/../public/media/upload';
        if (!(file_exists($dir) && is_dir($dir))) {
            mkdir( $dir, 0777, true );
        }
        return $dir;
    }
    
        
    /**
     * サムネイルのアップロード先ディレクトリを得るメソッドです。ディレクトリが存在しない場合は作成します。
     * 
     * @return string サムネイルのアップロード先ディレクトリ名
     * @todo ディレクトリ作成時のパーミッション検討？
     */
    private function _getThumbnailDest() 
    {
        $dir = APPLICATION_PATH . '/../public/media/thumbnail';
        if (!(file_exists($dir) && is_dir($dir))) {
            mkdir( $dir, 0777, true );
        }
        return $dir;
    }
    
}
