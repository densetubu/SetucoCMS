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
     * ページャー用の、1ページあたりのデータ表示件数
     * @todo 外だしする
     */
    const PAGE_LIMIT = 10;

    /**
     * 扱えるファイルの種類
     */
    private $_fileTypes = array('all', 'jpg', 'gif', 'png', 'pdf', 'txt');
    
    /**
     *
     * ファイルのアップロードフォームや
     * アップロードしてあるファイルの一覧を表示するアクションです
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

        // カレントページの取得
        $currentPage = $this->_getPage();

        // ファイルタイプの絞り込み取得
        
        $type = $this->_fileTypes[$this->_getParam('fileType', $this->_getParam('type', 0))];

        if ($type === null) {
            $type = 'all';
        }
        
       
        // ソートカラムとオーダー取得
        $sort  = $this->_getParam('sort', 'name'); // どの列でソートするか（media表の列名が入る）
        $order = $this->_getParam('order',  'asc'); // asc か desc が入る

        // findの条件を作る
        $condition = array(
            'type'  => $type,
            'sort'  => $sort,
            'order' => $order
        );
        
        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->condition = $condition;

        // サービスにファイルデータ取得を指示
        $mediaService = new Admin_Model_Media();
        $mediaData    = $mediaService->findMedias($condition, $currentPage, self::PAGE_LIMIT);
        $count        = $mediaService->countMedias($condition);

        // viewにファイルデータを渡す
        $this->view->mediaData = $mediaData;

        // viewにファイルアップロード用フォームを設定
        $this->view->uploadForm = $this->_createUploadForm();
        // アップロードできる最大サイズを設定
        $this->view->maxFileSize = (int)(self::MAX_FILE_SIZE / 1024);

        // ファイル絞込み・ソート用フォームの作成
        $search = new Zend_Form();
        $this->view->searchForm = $this->_createSearchForm($condition);        
        
        // ページネーター設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($count);

        // フラッシュメッセージ設定
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
        
    }


    /**
     * ファイルのアップロード処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     * @todo エラー判定とその処理をちゃんと書く
     */
    public function createAction()
    {
        
        
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/admin/media/index');
        }
        
        $form = $this->_createUploadForm();
        if (!$form->isValid($_POST)) {
            echo "検証エラー";
            $this->_helper->flashMessenger('ファイルのサイズオーバーか、または対応外のファイル形式です。');
            $this->_redirect('/admin/media/index');
        }
        if (!$form->upload_img->receive()) {
            $this->_helper->flashMessenger('ファイルが正しく送信されませんでした。');
            $this->_redirect('/admin/media/index');
        }
        
        $service = new Admin_Model_Media();
        if (!$service->saveUploadedMedia($this->upload_img->getFileName())) {
            $this->_helper->flashMessenger('ファイルが正しく保存できませんでした。');
            $this->_redirect('/admin/media/index');
        }        
        
        $this->_helper->flashMessenger('ファイルをアップロードしました。');
        $this->_redirect('/admin/media/index');
    }

    /**
     * 更新処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        
        $this->_redirect('/admin/media/index');
    }

    /**
     * 削除処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/media/index');
    }

    /**
     * アップロード済みのファイルを編集するアクションです
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function formAction()
    {

        $id = $this->_getParam('id');
        $service = new Admin_Model_Media();
        $mediaData = $service->findMediaById($id);
        
        $this->view->mediaData = $mediaData;
        
    }


    /**
     * ページャで使う現在の（クリックされた）ページ番号を取得する
     * Enter description here ...
     */
    private function _getPage()
    {
        $currentPage = $this->_getParam('page');
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }
        return $currentPage;
    }

    /**
     * ファイルアップロードフォームを作成する
     */
    private function _createUploadForm()
    {
        $uploadForm = new Zend_Form();
        $uploadForm->setMethod('post');
        $uploadForm->setAction('/admin/media/create');
        $uploadForm->setAttrib('enctype', 'multipart/form-data');
        
        $fileSelector = new Zend_Form_Element_File('upload_img', array('size' => 55));
        $fileSelector->setLabel(null)
                    ->addDecorator('Label', array('tag' => null))
                    ->setDestination($_SERVER['DOCUMENT_ROOT'] . '/media/upload') // public/media/uploadディレクトリの権限に注意
                    ->setMaxFileSize(self::MAX_FILE_SIZE)
                    ->addValidator('Size', false, self::MAX_FILE_SIZE)
                    ->addValidator('Extension', false, 'jpg,png,gif,pdf,txt');

      
        $uploadFormSubmit = new Zend_Form_Element_Submit(
        	'up', array('class' => 'upSub', 'Label' => 'アップロード')
        );
        $uploadFormSubmit->clearDecorators()
                         ->addDecorator('ViewHelper');
        
        $uploadForm->addElement($fileSelector);
        $uploadForm->addElement($uploadFormSubmit);
        return $uploadForm;
    }
    
    /**
     * ファイルの絞込み・ソート用フォームを作成する
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
                     ->addDecorator('ViewHelper')
                     ->addDecorator('Label', array('tag' => null));

        $typeSelector->addMultiOptions($this->_fileTypes);
        $typeSelector->addMultiOption('0', '--指定なし--');
        // @todo 現在適用されている絞り込みのファイル種類をselectedにする
        
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
}
