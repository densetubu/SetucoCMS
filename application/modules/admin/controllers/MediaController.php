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
class Admin_MediaController extends Setuco_Controller_Action_AdminAbstract
{
    
    /**
     * 
     * Mediaサービスクラスのオブジェクト
     * @var Admin_Model_Media
     */
    private $_service = null;
    
    /**
     * アップロードできるファイルサイズの最大値
     * @todo 最大サイズを決める
     */
    const FILE_SIZE_MAX = 500000;
    
    /**
     * ファイルの表示名の長さ 最短文字数
     */
    const FILENAME_LENGTH_MIN = 1;
    
    /**
     * ファイルの表示名の長さ 最長文字数
     */
    const FILENAME_LENGTH_MAX = 50;
    
    const THUMBNAIL_WIDTH = 48;
    
    const FILEEXT_ALL_INDEX = -1;
    const FILEEXT_ALL = 'all';
    
    /**
     * SetucoCMSで扱えるファイルの種類（拡張子）
     *
     * @var array
     * @todo 'all'をなくしたい
     */
    private $_fileExt = array('jpg', 'gif', 'png', 'pdf', 'txt');
    
 
    /**
     * 初期化処理
     * 
     * @return void
     * @author akitsukada
     */
    public function init()
    {

        //親クラスの設定を引き継ぐ
        parent::init();
        
        //全アクションで使用するサービスクラスのインスタンを生成する
        $this->_service = new Admin_Model_Media($this->_getThumbnailDest(), self::THUMBNAIL_WIDTH);

    }
    
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
        $type = self::FILEEXT_ALL;
        if ($this->getRequest()->isPost()) { // isPost == trueならばファイル種別絞り込みフォームでsubmitされている
            $type = array_key_exists($this->_getParam('fileType', 0), $this->_fileExt) ? $this->_fileExt[$this->_getParam('fileType')] : self::FILEEXT_ALL; 
            $currentPage = 1; // 新たに絞り込みされた場合は常に1ページ目表示
        } else {
            $type = $this->_getParam('type', 'all');     // ソートリンクでの指定
        }

        // ソート指定のカラムとオーダー取得 (デフォルトではファイル名'name'の昇順'asc')
        $sort  = $this->_getParam('sort', 'name');
        $order = $this->_getParam('order', 'asc');

        // データ取得の条件を作る
        $condition = array(
            'type'  => $type,
            'sort'  => $sort,
            'order' => $order
        );
        
        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->condition = $condition;

        // viewにファイルデータを渡す
        $this->view->mediaData = $this->_service->findMedias($condition, $currentPage, parent::PAGE_LIMIT);
        
        // アップロードできる最大サイズをKB換算でviewに教える
        $this->view->maxFileSize = (int)(self::FILE_SIZE_MAX / 1024);

        // ディレクトリに問題なければviewにファイルアップロード用フォームを設定
        $dirErrors = array();
        if (!$this->_isWritableUploadDest()) {
            array_push($dirErrors, $this->_getUploadDest() . '　が存在しないか、書き込みできません。');
        }
        if (!$this->_isWritableThumbnailDest()) {
            array_push($dirErrors, $this->_getThumbnailDest() . '　が存在しないか、書き込みできません。');
        }
        if (count($dirErrors) == 0) {
            $this->view->uploadForm = $this->_createUploadForm();
        } else {
            $this->view->dirErrors = $dirErrors;
        }
        
        // viewにファイル絞込み・ソート用フォームの作成
        $this->view->searchForm = $this->_createSearchForm($condition);
        
        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_service->countMedias($condition['type']));
        
        // サムネイルの表示サイズ指定
        $this->view->thumbWidth = self::THUMBNAIL_WIDTH;
        
        // フラッシュメッセージ設定
        $this->_setFlashMessages();
        
    }


    /**
     * ファイルのアップロード処理です
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     * @todo エラー判定とその処理をちゃんと書く
     * @todo ファイルとDB、２フェーズコミットにしてエラー時のロールバックをちゃんと制御する
     * @todo サムネイルの生成と保存
     */
    public function createAction()
    { 
        
        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/admin/media/index');
        }
        
        // ファイル受信に使うadapterの作成とバリデータの設定
        $adapter  = new Zend_File_Transfer_Adapter_Http();
        $this->_setFileValidators($adapter);
        
        // すべてのファイルを検証
        if (!$adapter->isValid()) {
            $this->_helper->flashMessenger->addMessage('ファイルのサイズオーバーか、または対応外のファイル形式です。');
            $this->_redirect('/admin/media/index');
        }
        
        // オリジナルファイルの情報を取得
        $fileInfo = pathinfo($adapter->getFileName());

        
        // 拡張子取得
        $extType =  $fileInfo['extension'];
        
        // 保存時の物理名に使う新しいファイルIDを取得
        $newId = $this->_service->createNewMediaID();

        // ファイルの保存先と物理名（id)を指定
        $adapter->setDestination($this->_getUploadDest());
        $adapter->addFilter('Rename', array( // 別名を指定
        	'target' => $this->_getUploadDest() . '/' . $newId . '.' . $extType,
        	'overwrite' => true
        ));
        
        // ファイルの受信と保存
        if (!$adapter->receive()) {
            $this->_helper->flashMessenger->addMessage('ファイルが正しく送信されませんでした。');
            $this->_redirect('/admin/media/index');
        }
        $this->_service->saveThumnailFromImage($adapter->getFileName(), self::THUMBNAIL_WIDTH);
        
        // サービスにファイルの情報を渡してDB登録させる
        $dat = array(
            'id'         => $newId,
            'name'       => $fileInfo['filename'],
            'type'       => $extType,
            'comment'    => ''
   		);
        if (!$this->_service->updateMediaInfo($newId, $dat)) {
            $this->_helper->flashMessenger->addMessage('ファイルが正しく保存できませんでした。');
            // dbの削除
            $this->_redirect('/admin/media/index');
        }
        
        // dbの更新
        
        // 処理正常終了
        $this->_helper->flashMessenger->addMessage('ファイルをアップロードしました。');
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
        $mediaData = $this->_service->findMediaById($id);

        // ビューにファイル情報を渡す
        $this->view->mediaData = $mediaData;
        
        // フォームの作成とviewへのセット
        $this->view->updateForm = $this->_createUpdateForm($id, $mediaData['name'], $mediaData['comment']);
        
        // アップロードできる最大サイズをKB換算でviewに教える
        $this->view->maxFileSize = (int)(self::FILE_SIZE_MAX / 1024);
        
        // フラッシュメッセージ設定
        $this->_setFlashMessages();
        
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
        
        // 既存ファイルを消すかどうかのフラグ
        $isDeleteOldFile = false;
        
        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/admin/media/index');
        }
        
        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        if ($id === null) {
            $this->_redirect('/admin/media/index');
        }
        
        // 更新フォームオブジェクト取得
        $form = $this->_createUpdateForm($id);
        
        // Postデータ取得
        $post = $this->getRequest()->getPost();
        
        // formアクションへのリダイレクトURL
        $redirectUrl = '/admin/media/form/id/' . $id;
        
        // Postのバリデーション
        if (!$form->isValid($post)) {
            $this->_helper->flashMessenger->addMessage('ファイルが更新できませんでした。');
            $this->_redirect($redirectUrl);
        }

        // サービスにファイルの情報を渡してUpdateさせるためのデータ
        $dat = array(
            'name'       => $post['name'],
            'comment'    => $post['comment']
        );
        
        // ファイル受信に使うadapterの作成
        $adapter  = new Zend_File_Transfer_Adapter_Http();
        
        // ファイル関連はファイルが選択された場合のみ処理
        if ($adapter->getFileName()) {

            // ファイル受信バリデータの設定
            $this->_setFileValidators($adapter, true);
                    
            // すべてのファイルを検証
            if (!$adapter->isValid()) {
                $this->_helper->flashMessenger->addMessage('ファイルのサイズオーバーか、または対応外のファイル形式です。');
                $this->_redirect($redirectUrl);
            }
            
            // オリジナルファイルの情報を取得
            $fileInfo = pathinfo($adapter->getFileName());
            $extType =  $fileInfo['extension'];             // 拡張子取得
            
            // 既存のファイルと新ファイルで拡張子が違う場合は既存のファイル情報を取得（新ファイルアップロード後に古いファイルをremoveできるようにするため）
            $oldFileInfo = $this->_service->findMediaById($id);
            if ($extType !== $oldFileInfo['type']) {
                $isDeleteOldFile = true;
            }
            
            // ファイルの保存先と保存名を指定
            $adapter->addFilter('Rename', array( // 別名を指定
            	'target' => $this->_getUploadDest() . '/' . $id . '.' . $extType,
            	'overwrite' => true
            ));
            
            // ファイルの受信と保存
            if (!$adapter->receive()) {
                $this->_helper->flashMessenger->addMessage('ファイルが正しく送信されませんでした。');
                $this->_redirect($redirectUrl);
            }
            
            // 保存するファイル情報、拡張子を取得しておく
            $dat['type'] = $extType;

        }
        
        if (!$this->_service->updateMediaInfo($id, $dat)) {
            $this->_helper->flashMessenger->addMessage('ファイルが正しく更新できませんでした。');
            $this->_redirect($redirectUrl);
        }
        
        // @todo 拡張子が違うファイルで更新した場合の仕様は？
        
        
        // 処理正常終了
        $this->_helper->flashMessenger->addMessage('ファイル情報を更新しました。');
        $this->_redirect($redirectUrl);
        
    }

    
    /**
     * 
     */
    private function _setFileValidators(Zend_File_Transfer_Adapter_Abstract $adapter, $isUpdate = false)
    {
        if ($isUpdate) {
            $minCount = 0;
            $maxCount = 1;
        } else {
            $minCount = 1;
            $maxCount = 5;
        }
        
        $adapter->addValidator('FilesSize', false, array(1, self::FILE_SIZE_MAX, false))
                    ->addValidator('Count', false, array('min' => $minCount, 'max' => $maxCount)) 
                    ->addValidator('Extension', false, implode(',', $this->_fileExt));
                    
        return $adapter;    
    }
    
    /**
     * ファイル削除処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @todo ファイルとDBでちゃんと２フェーズコミットする。どちらかNGなら両方ともロールバックするなど
     * @author akitsukada
     */
    public function deleteAction()
    {
        
        $id = $this->_getParam('id');
        if ($id === null) {
            $this->_redirect('/admin/media/index');
        }
        
        if (!$this->_service->deleteMediaById($id)) {
            $this->_helper->flashMessenger->addMessage('ファイル情報を削除できませんでした。');
            $this->_redirect('/admin/media/index');
        }
        
        if (!$this->_removeFileById($id)) {
            $this->_helper->flashMessenger->addMessage('アップロードされたファイルを削除できませんでした。');
            $this->_redirect('/admin/media/index');
        }

        $this->_helper->flashMessenger->addMessage('ファイルを削除しました。');
        $this->_redirect('/admin/media/index');
    
    }

   
    /**
     * ファイル新規アップロード用フォームを作成するメソッドです。
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
                     ->setMultiFile(1)
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
     * ファイルの絞込み・ソート用フォームを作成するメソッドです。
     *
     * @return Zend_Form ファイルの絞込み・ソート用フォームオブジェクト
     * @author akitsukada
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
                     ->setValue(array_search($condition['type'], $this->_fileExt))
                     ->addDecorator('ViewHelper')
                     ->addDecorator('Label', array('tag' => null));
        
        $typeSelector->addMultiOption(self::FILEEXT_ALL_INDEX, '--指定なし--');
        $typeSelector->addMultiOptions($this->_fileExt);
        
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
     * ファイルの更新（=上書きアップロード）用フォームを作成するメソッドです。
     *
     * @return Zend_Form ファイルの更新（=上書きアップロード）用フォームオブジェクト
     * @author akitsukada
     *
     */
    private function _createUpdateForm($id, $name = null, $comment = null)
    {
        // 編集用フォームの作成
        $updateForm = new Zend_Form();
        $updateForm->setMethod('post');
        $updateForm->setAction($this->_helper->url('update/id/' . $id));
        $updateForm->setAttrib('enctype', 'multipart/form-data');
        
        // ファイル名編集テキストボックス
        $txtFileName = new Zend_Form_Element_Text('name');
        $txtFileName->clearDecorators()
                    ->setRequired(true) // @todo ファイル名文字数制限
                    ->setValue($name)
                    ->addFilter('StringTrim')
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
     * ファイルのアップロード先ディレクトリパスを得るメソッドです。
     *
     * @return string ファイル(サムネイルではない)のアップロード先ディレクトリ名
     * @author akitsukada
     */
    private function _getUploadDest()
    {
        return APPLICATION_PATH . '/../public/media/upload';
    }
    
    
    /**
     * ファイルのアップロード先ディレクトリが書き込み可能であるかを判定するメソッドです。
     *
     * @return boolean ファイルのアップロード先ディレクトリが書き込み可能か。
     * @author akitsukada
     */
    private function _isWritableUploadDest()
    {
        $dir = $this->_getUploadDest();
        return is_writable($dir) && is_dir($dir);
    }
    
        
    /**
     * サムネイルのアップロード先ディレクトリパスを得るメソッドです。
     *
     * @return string サムネイルのアップロード先ディレクトリ名
     * @author akitsukada
     */
    private function _getThumbnailDest()
    {
        return APPLICATION_PATH . '/../public/media/thumbnail';
    }

    
    /**
     * サムネイルのアップロード先ディレクトリが書き込み可能であるかを判定するメソッドです。
     *
     * @return boolean サムネイルのアップロード先ディレクトリが書き込み可能か。
     * @author akitsukada
     */
    private function _isWritableThumbnailDest()
    {
        $dir = $this->_getUploadDest();
        return is_writable($dir) && is_dir($dir);
    }

    
    private function _removeFileById($id)
    {
        $uploadDir = $this->_getUploadDest();
        $thumbDir = $this->_getThumbnailDest();
        if (($uploadDirHandle = opendir($uploadDir)) !== false && 
            ($thumbDirHandle = opendir($thumbDir))   !== false ) {
            while (($file = readdir($uploadDirHandle)) !== false && ($thumb = readdir($thumbDirHandle)) !== false) {
                if (($file != "." && $file != "..") && ($thumb != "." && $thumb != "..")) {
                    // アップロードされた*.*の削除
                    if (preg_match("/^{$id}\.(" . implode("|", $this->_fileExt) . ")$/", $file)) {  
                        if (!unlink("{$uploadDir}/{$file}")) {
                            return false;
                        }
                    }
                    // 生成されたサムネイル*.gifの削除
                    if (preg_match("/^{$id}\.gif$/", $thumb)) {  
                        if (!unlink("{$thumbDir}/{$thumb}")) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * フラッシュメッセージをビューに設定するメソッド。
     *
     * @return void
     * @author akitsukada
     *
     */
    private function _setFlashMessages()
    {
        // フラッシュメッセージ用の設定
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages;
        }
    }
    
}
