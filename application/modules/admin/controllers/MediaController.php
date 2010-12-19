<?php
/**
 * 管理側のファイル管理のコントローラ
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
     * Mediaサービスクラスのオブジェクト
     * @var Admin_Model_Media
     */
    private $_media = null;

    /**
     * アップロードできる最大ファイル個数
     */
    const FILE_COUNT_MAX = 5;

    /**
     * アップロードできるファイルサイズの最大値（Byte単位）
     */
    const FILE_SIZE_MAX = 512000; // 500KB

    /**
     * アップロードできるファイルサイズの最小値（Byte単位）
     */
    const FILE_SIZE_MIN = 1;

    /**
     * ファイルの表示名の長さ 最短文字数
     */
    const FILENAME_LENGTH_MIN = 1;

    /**
     * ファイルの表示名の長さ 最長文字数
     */
    const FILENAME_LENGTH_MAX = 50;

    /**
     * サムネイルの表示時の幅。
     */
    const THUMBNAIL_WIDTH = 65;

    /**
     * 一覧表示時、１ページに何件のファイルを表示するか
     */
    const PAGE_LIMIT = 10;

    /**
     * 初期化処理
     *
     * @return void
     * @author akitsukada
     */
    public function init()
    {
        parent::init();
        $this->_media = new Admin_Model_Media(
                        $this->_getUploadDest(),
                        $this->_getThumbDest(),
                        self::THUMBNAIL_WIDTH
        );
        $this->_setPageLimit(self::PAGE_LIMIT);
    }

    /**
     * ファイルのアップロードフォームやアップロードしてあるファイルの一覧を表示するページ
     *
     * @return void
     * @author akitsukada
     */
    public function indexAction()
    {

        // ページネーターのカレントページの取得
        $currentPage = $this->_getPageNumber();

        $fileType = Setuco_Data_Constant_Media::FILEEXT_ALL_VALUE;

        if ($this->getRequest()->isPost()) { // 絞り込みフォーム経由でIndexに来た
            $extensions = Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS();
            $fileType = array_key_exists($this->_getParam('fileType'), $extensions) ?
                    $extensions[$this->_getParam('fileType')] :
                    Setuco_Data_Constant_Media::FILEEXT_ALL_VALUE;
            $currentPage = 1; // 新たに絞り込みされた場合は常に1ページ目表示
        } else {
            $fileType = $this->_getParam('type', Setuco_Data_Constant_Media::FILEEXT_ALL_VALUE);     // ソートリンクでの指定
        }

        // ソート指定のカラムとオーダー取得 (デフォルトではファイル名'name'の昇順'asc')
        $sortColumn = $this->_getParam('sort', 'name');
        $order = $this->_getParam('order', 'asc');

        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->fileType = $fileType;
        $this->view->sortColumn = $sortColumn;
        $this->view->order = $order;

        // viewにファイルデータを渡す
        $medias = $this->_media->findMedias(
                        $sortColumn, $order, $fileType, $this->_getPageLimit(), $currentPage);
        $this->view->medias = $medias;

        foreach ($medias as $cnt => $media) {
            // ファイル、サムネイルがファイルシステム上に見つからないときメッセージを表示
            foreach ($media['notFound'] as $msg) {
                $this->_helper->flashMessenger->addMessage($msg);
            }
        }

        // アップロードできる最大サイズをviewに教える
        $this->view->fileSizeMax = (int) (self::FILE_SIZE_MAX / 1024) . 'KB';

        // ディレクトリに問題なければviewにファイルアップロード用フォームを設定
        $dirErrors = array();
        if (!$this->_isWritableUploadDest()) {
            $dirErrors[] = $this->_getUploadDest() . '　が存在しないか、書き込みできません。';
        }
        if (!$this->_isWritableThumbnailDest()) {
            $dirErrors[] = $this->_getThumbDest() . '　が存在しないか、書き込みできません。';
        }
        if (count($dirErrors) == 0) {
            $this->view->uploadForm = $this->_createUploadForm();
        } else {
            $this->view->dirErrors = $dirErrors;
        }

        // viewにファイル絞込み・ソート用フォームの作成
        $this->view->searchForm = $this->_createSearchForm($fileType);

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_media->countMedias($fileType));

        // フラッシュメッセージ設定
        $this->_showFlashMessages();
    }

    /**
     * ファイルの新規アップロード処理。DB（media）に新規レコード挿入、ファイルシステム上に受信した実ファイルを保存。
     * 画像ファイルの場合はサムネイルも生成する。
     *
     * @return void
     * @author akitsukada
     */
    public function createAction()
    {

        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_helper->redirector('index');
        }

        // ファイル受信に使うadapter
        $adapter = $this->_createFileTransferAdapter();

        // file inputを取得し1ファイルずつ処理
        $fileInfos = $adapter->getFileInfo();
        if (empty($fileInfos)) {
            $this->_helper->flashMessenger->addMessage("ファイルのアップロードに失敗しました。");
        }

        $noFileCount = 0;
        $uploadSuccessMsgs = array();
        $uploadErrorMsgs = array();
        foreach ($fileInfos as $inputName => $fileInfo) {

            if (!$adapter->isValid($inputName)) {
                $msgs = $adapter->getMessages();
                // @todo エラー処理(今は暫定的にフラッシュメッセージでエラー表示)
                foreach ($msgs as $msgCode => $msg) {
                    $uploadErrorMsgs[] = $msg;
                }
                continue; // 次のファイル処理を続ける
            }

            // ファイルが選択されなかったinputは飛ばす
            if (empty($fileInfo['tmp_name'])) {
                $noFileCount++;
                if ($noFileCount >= self::FILE_COUNT_MAX) {
                    // 一つも選択されていなければエラー
                    $uploadErrorMsgs[] = "ファイルが選択されていません。";
                    break;
                }
                continue;
            }

            $filePath = pathinfo($fileInfo['name']);
            $extType = $filePath['extension'];

            // ファイルの保存先と物理名（id)を指定
            $newId = $this->_media->createNewMediaID(); // mediaテーブルにtmpレコード挿入

            $adapter->setDestination($this->_getUploadDest());
            $adapter->addFilter('Rename', array(
                'target' => $this->_getUploadDest() . "/{$newId}.{$extType}",
                'overwrite' => true
            ));

            // ファイルの受信と保存
            if (!$adapter->receive($inputName)) {
                $uploadErrorMsgs[] = "ファイル{$filePath['basename']}が正しく送信されませんでした。";
                $this->_media->deleteMediaById($newId);
                continue;
            }

            $newFileName = $adapter->getFileName($inputName);

            // 有効な画像か
            if (Setuco_Util_Media::isImageExtension($extType)) {
                if (!$this->_media->isValidImageData($newFileName)) {
                    $uploadErrorMsgs[] = "{$filePath['basename']}は不正な画像データです。";
                    $this->_removeFileById($newId);
                    continue;
                }
            }

            // サムネイルの生成と保存
            if (Setuco_Util_Media::isImageExtension($extType)) {
                if (!$this->_media->saveThumbnailFromImage($newFileName)) {
                    $uploadErrorMsgs[] = "ファイル{$filePath['basename']}のサムネイルが生成できませんでした。";
                    $this->_media->deleteMediaById($newId);
                    $this->_removeFileById($newId);
                    continue;
                }
            }

            // サービスにファイルの情報を渡してDB登録させる
            $dat = array(
                'id' => $newId,
                'name' => $filePath['filename'],
                'type' => $extType,
                'comment' => date('Y/m/d H:i:s にアップロード')
            );

            if (!$this->_media->updateMediaInfo($newId, $dat)) {
                $uploadErrorMsgs[] = "ファイル{$filePath['basename']}がデータベースに保存できませんでした。";
                $this->_media->deleteMediaById($newId);
                $this->_removeFileById($newId);
                continue;
            }

            $uploadSuccessMsgs[] = "ファイル {$filePath['basename']} をアップロードしました。";
        }

        foreach ($uploadErrorMsgs as $msg) {
            $this->_helper->flashMessenger->addMessage($msg);
        }
        foreach ($uploadSuccessMsgs as $msg) {
            $this->_helper->flashMessenger->addMessage($msg);
        }
        $this->_helper->redirector('index');
    }

    /**
     * ファイル更新用のページ
     *
     * @return void
     * @author akitsukada
     *
     */
    public function formAction()
    {

        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        $validator = new Zend_Validate_Digits();
        if (!$validator->isValid($id)) {
            $this->_helper->redirector('index');
        }

        $mediaData = $this->_media->findMediaById($id);
        if (!empty($mediaData['notFound'])) {
            foreach ($mediaData['notFound'] as $cnt => $msg) {
                $this->_helper->flashMessenger->addMessage($msg);
            }
        }
        $this->view->mediaData = $mediaData;
        $this->view->updateForm = $this->_createUpdateForm($id, $mediaData['name'], $mediaData['comment']);
        $this->view->fileSizeMax = (int) (self::FILE_SIZE_MAX / 1024) . 'KB';

        $this->view->headTitle("「{$mediaData['name']}」({$mediaData['id']}.{$mediaData['type']})の編集",
                Zend_View_Helper_Placeholder_Container_Abstract::SET);

        // フラッシュメッセージ設定
        $this->_showFlashMessages();
    }

    /**
     * ファイル更新処理のアクション。DBのレコードとファイルシステム上の実ファイル両方を更新する。
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
            $this->_helper->redirector('index');
        }

        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        if (is_null($id)) {
            $this->_helper->redirector('index');
        }

        $adapter = $this->_createFileTransferAdapter();
        $form = $this->_createUpdateForm($id);
        $post = $this->getRequest()->getPost();

        // formアクションへのリダイレクトURL
        $redirectUrl = '/admin/media/form/id/' . $id;

        // Postのバリデーション
        if (!$adapter->isValid()) {
            $msgs = $adapter->getMessages();
            foreach ($msgs as $msgCode => $msg) {
                $this->_helper->flashMessenger->addMessage($msg);
            }
            $this->_redirect($redirectUrl);
        }

        // サービスにDBをUpdateさせるためのファイルの情報
        $fileInfo = array(
            'name' => $post['name'],
            'comment' => $post['comment'],
            'update_date' => date('Y/m/d H:i:s')
        );

        $isFileUploaded = false;

        // ファイル関連はファイルが選択された場合のみ処理
        if ($adapter->getFileName()) {

            // 新しくアップロードされたオリジナルファイルの情報を取得
            $newFileInfo = pathinfo($adapter->getFileName());
            $extType = $newFileInfo['extension'];
            $isFileUploaded = true;

            // 既存の同IDファイルを退避
            if (!$this->_backupFileById($id, $extType)) {
                $updateErrorMsgs[] = "既存のファイル{$id}.{$extType}が削除できません。";
                $this->_redirect($redirectUrl);
            }

            // ファイルの保存先と保存名を指定
            $adapter->addFilter('Rename', array(// 別名を指定
                'target' => $this->_getUploadDest() . "/{$id}.{$extType}",
                'overwrite' => true
            ));

            // ファイルの受信と保存
            if (!$adapter->receive()) {
                $this->_helper->flashMessenger->addMessage('ファイルが正しく送信されませんでした。');
                $this->_recoverFromBackUpFile($id, $extType);
                $this->_redirect($redirectUrl);
            }

            // 画像なら有効な画像データかどうか確認
            if (Setuco_Util_Media::isImageExtension($extType)) {
                if (!$this->_media->isValidImageData($adapter->getFileName())) {
                    $this->_helper->flashMessenger->addMessage("{$newFileInfo['basename']}は不正な画像データです。");
                    $this->_recoverFromBackUpFile($id, $extType);
                    $this->_redirect($redirectUrl);
                }

                // サムネイルを保存
                if (!$this->_media->saveThumbnailFromImage($adapter->getFileName())) {
                    $this->_helper->flashMessenger->addMessage('サムネイルが保存できませんでした。');
                    $this->_recoverFromBackUpFile($id, $extType);
                    $this->_redirect($redirectUrl);
                }
            }

            // 保存するファイル情報、拡張子を取得しておく
            $fileInfo['type'] = $extType;
        }

        // DBの更新
        if (!$this->_media->updateMediaInfo($id, $fileInfo)) {
            $this->_helper->flashMessenger->addMessage('ファイルが正しく更新できませんでした。');
            if ($isFileUploaded) {
                $this->_recoverFromBackUpFile($id, $extType);
            }
            $this->_redirect($redirectUrl);
        }

        // 処理正常終了
        if ($isFileUploaded) {
            $this->_removeBackupFile($id, $extType);
        }
        $this->_helper->flashMessenger->addMessage('ファイル情報を更新しました。');
        $this->_redirect($redirectUrl);
    }

    /**
     * ファイル削除処理。DB（mediaテーブル）のレコードとファイルシステム上の実ファイルを両方削除する。
     *
     * @return void
     * @todo ファイルとDBでちゃんと２フェーズコミットする。どちらかNGなら両方ともロールバックするなど
     * @author akitsukada
     */
    public function deleteAction()
    {
        // IDが渡されていなければリダイレクト
        $id = $this->_getParam('id', null);
        if (is_null($id)) {
            $this->_helper->redirector('index');
        }

        // DBのファイル情報を削除
        if (!$this->_media->deleteMediaById($id)) {
            $this->_helper->flashMessenger->addMessage('ファイル情報を削除できませんでした。');
            $this->_helper->redirector('index');
        }

        // ファイルシステム上のファイルを削除
        if (!$this->_removeFileById($id)) {
            $this->_helper->flashMessenger->addMessage('アップロードされたファイルを削除できませんでした。');
            $this->_helper->redirector('index');
        }

        $this->_helper->flashMessenger->addMessage('ファイルを削除しました。');
        $this->_helper->redirector(
                'index', null, null,
                array(
                    'page' => $this->_getPageNumber(),
                    'type' => $this->_getParam('type', 'all'),
                    'sort' => $this->_getParam('sort', 'name'),
                    'order' => $this->_getParam('order', 'asc')
                )
        );
    }

    /**
     * ファイルの絞込み・ソート用フォームを作成する
     *
     * @param  string $fileType 絞り込みたいファイル種別
     * @return Zend_Form ファイルの絞込み・ソート用フォームオブジェクト
     * @todo フォームのハッシュ値の設定
     * @author akitsukada
     */
    private function _createSearchForm($fileType = 'all')
    {
        // 絞り込みフォームのオブジェクト
        $searchForm = new Zend_Form();
        $searchForm->setMethod('post');
        $searchForm->setAction('/admin/media/index');

        // ファイルタイプのセレクトボックス
        $fileTypeSelector = new Zend_Form_Element_Select('fileType');
        $fileTypeSelector->clearDecorators()
                ->setLabel('ファイルの種類')
                ->setValue(array_search($fileType,
                                Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS()))
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null))
                ->addMultiOption(
                        Setuco_Data_Constant_Media::FILEEXT_ALL_INDEX,
                        Setuco_Data_Constant_Media::FILEEXT_ALL_STRING)
                ->addMultiOptions(Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS());

        $searchForm->addElement($fileTypeSelector);

        // 絞込みボタン
        $searchFormSubmit = new Zend_Form_Element_Submit(
                        'search',
                        array('class' => 'upSub', 'Label' => '絞込み')
        );
        $searchFormSubmit->clearDecorators()
                ->addDecorator('ViewHelper');

        $searchForm->addElement($searchFormSubmit);

        return $searchForm;
    }

    /**
     * ファイル新規アップロード用フォームを作成する
     *
     * @return Zend_Form ファイル新規アップロード用フォームオブジェクト
     * @author akitsukada
     * @todo フォームのハッシュ値の設定
     * @todo 複数ファイルのアップロード
     */
    private function _createUploadForm()
    {

        // フォームオブジェクト作成
        $uploadForm = new Zend_Form();
        $uploadForm->setName('upload_img');
        $uploadForm->setMethod('post');
        $uploadForm->setAction($this->_helper->url('create'));
        $uploadForm->setAttrib('enctype', 'multipart/form-data');

        // ファイル選択パーツ作成と余分な装飾タグの除去
        $fileCount = 1;
        for (; $fileCount <= self::FILE_COUNT_MAX; $fileCount++) {
            $inputName = 'upload_img' . $fileCount;
            $fileSelector = new Zend_Form_Element_File($inputName);
            $fileSelector
                    ->clearDecorators()
                    ->addDecorator('file');

            $fileSelector->setMaxFileSize(self::FILE_SIZE_MAX);
            $uploadForm->addElement($fileSelector);
        }

        // submitボタンの作成と余分な装飾タグの除去
        $uploadFormSubmit = new Zend_Form_Element_Submit(
                        'up', array('class' => 'upSub', 'Label' => 'アップロード')
        );
        $uploadFormSubmit->clearDecorators()
                ->addDecorator('ViewHelper');

        // 作成したパーツをフォームに追加
        $uploadForm->addElement($uploadFormSubmit);

        // フォームを返す
        return $uploadForm;
    }

    /**
     * ファイルの更新（=上書きアップロード）用フォームを作成する
     *
     * @param  int      $id      更新対象ファイルのID
     * @param  string   $name    更新対象ファイルの名前（オプション）
     * @param  string   $comment 更新対象ファイルの説明（オプション）
     * @return Zend_Form ファイルの更新（=上書きアップロード）用フォームオブジェクト
     * @todo フォームのハッシュ値の設定
     * @author akitsukada
     *
     */
    private function _createUpdateForm($id, $name = null, $comment = null)
    {
        // 編集用フォームの作成
        $updateForm = new Zend_Form();
        $updateForm->setName('upload_img');
        $updateForm->setMethod('post');
        $updateForm->setAction($this->_helper->url('update/id/' . $id));
        $updateForm->setAttrib('enctype', 'multipart/form-data');

        // ファイル名編集テキストボックス
        $txtFileName = new Zend_Form_Element_Text('name');
        $txtFileName->clearDecorators()
                ->setRequired(true)
                ->setValue($name)
                ->addFilter('StringTrim')
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null));

        // ファイルの説明テキストボックス
        $txtFileComment = new Zend_Form_Element_Text('comment');
        $txtFileComment->clearDecorators()
                ->setRequired(false)
                ->setValue($comment)
                ->addFilter('StringTrim')
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
        $updateForm->addElement($fileSelector);
        ;
        $updateForm->addElement($btnSubmit);

        // フォームを返す
        return $updateForm;
    }

    /**
     * ファイル受信用のAdapterを生成して返す
     *
     * @return Zend_File_Transfer_Adapter_Http
     * @author akitsukada
     */
    private function _createFileTransferAdapter()
    {
        // ファイル受信に使うadapter
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // フォームのファイル制限サイズ
        $minSizeString = self::FILE_SIZE_MIN . 'Byte';
        $maxSizeString = (int) (self::FILE_SIZE_MAX / 1024) . 'KB';
        $fileUploadValidator = new Zend_Validate_File_Upload(array());
        $adapter->addValidator($fileUploadValidator, true);

        // ファイル個数設定
        // 0個でもシステムエラーが出ないようにする
        $adapter->setOptions(array('ignoreNoFile' => true));
        $fileMaxCountValidator = new Zend_Validate_File_Count(array('min' => 0, 'max' => 5));

        // ファイルタイプ（拡張子）の制限
        $exts = implode(',', Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS());
        $fileExtensionValidator = new Zend_Validate_File_Extension($exts);
        $adapter->addValidator($fileExtensionValidator);

        // ファイルサイズ（サーバー側判定）
        $fileSizeValidator = new Zend_Validate_File_Size(array('bytestring' => true));
        $fileSizeValidator->setMax(self::FILE_SIZE_MAX);
        $fileSizeValidator->setMin(self::FILE_SIZE_MIN);
        /* デフォルトメッセージの方がサイズなどわかりやすいのでsetMessagesしない（@todo 将来的にカスタムする） */
        $adapter->addValidator($fileSizeValidator);

        return $adapter;
    }

    /**
     * アップロードされた、指定IDのファイル（ファイル本体とサムネイル両方）をファイルシステム上から削除する。
     * ファイル本体は、uploadディレクトリ内の<指定ID>.(jpg|gif|png|pdf|txt) を全て削除する。
     * <指定ID>.jpg.bakなどは削除しない。
     * サムネイルは、thumbnailディレクトリ内の<指定D>.gifを削除する。
     * 処理中にfalseとなった場合、その時点で削除してしまったファイルは元に戻らない。
     *
     * @param int $id 削除するファイルのID
     * @return boolean 正常にファイル本体／サムネイルとも削除できればTrue、一つでも削除に失敗すればFalse。
     * @author akitsukada
     */
    private function _removeFileById($id)
    {
        // ファイルの削除
        $uploadDir = $this->_getUploadDest();
        if (($uploadDirHandle = opendir($uploadDir)) !== false) {
            while (($file = readdir($uploadDirHandle)) !== false) {
                if (($file != "." && $file != "..")) {
                    // アップロードされた*.*(Setuco対応拡張子のみ)の削除
                    if (preg_match("/^{$id}\.(" . implode("|",
                                            Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS()) . ")$/", $file)) {
                        if (!unlink("{$uploadDir}/{$file}")) {
                            return false;
                        }
                    }
                }
            }
        }

        // サムネイルの削除
        $thumbDir = $this->_getThumbDest();
        if (($thumbDirHandle = opendir($thumbDir)) !== false) {
            while (($thumb = readdir($thumbDirHandle)) !== false) {
                if (($thumb != "." && $thumb != "..")) {
                    // 生成されているサムネイル $id.gif の削除
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
     * uploadとthumbnailディレクトリにある指定のファイル(ファイル名：<id.拡張子>)に
     * ".bak"を付けて<id.拡張子.bak>にリネームする
     * <ID.拡張子>　→　<ID.拡張子.bak>
     * ファイル更新失敗時にもとに戻すため
     *
     * @param int $id ファイルのID
     * @param string $extension ファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _backupFileById($id, $extension)
    {

        if (!$this->_removeBackupFile($id, $extension)) {
            return false;
        }

        // ファイル名に.bakを付ける
        $fileName = $this->_getUploadDest() . "/{$id}.{$extension}";
        if (file_exists($fileName)) {
            if (!rename($fileName, $fileName . '.bak')) {
                return false;
            }
        }
        // サムネイルも
        $thumbName = $this->_getThumbDest() . "/{$id}.gif";
        if (file_exists($thumbName)) {
            if (!rename($thumbName, $thumbName . '.bak')) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定したIDと拡張子の〜.bakファイル（本体もサムネイルも両方）を削除する
     * <ID.拡張子.bak>　→　削除
     *
     * @param int $id ファイルのID
     * @param string $extension ファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _removeBackupFile($id, $extension)
    {
        $fileName = $this->_getUploadDest() . "/{$id}.{$extension}";
        if (file_exists($fileName . '.bak')) {
            if (!unlink($fileName . '.bak')) {
                return false;
            }
        }
        $thumbName = $this->_getThumbDest() . "/{$id}.gif";
        if (file_exists($thumbName . '.bak')) {
            if (!unlink($thumbName . '.bak')) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定したIDと拡張子の〜.bakファイルを、".bak"がない元のファイル名にリネームする。
     * <ID.拡張子.bak>　→　<ID.拡張子>
     * <ID.拡張子>ファイルは存在していないことが前提
     *
     * @param int $id ファイルのID
     * @param string $extension ファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _recoverFromBackUpFile($id, $extension)
    {
        $this->_removeFileById($id);
        $fileName = $this->_getUploadDest() . "/{$id}.{$extension}";
        if (file_exists($fileName . '.bak')) {
            if (!rename($fileName . '.bak', $fileName)) {
                return false;
            }
        }
        $thumbName = $this->_getThumbDest() . "/{$id}.gif";
        if (file_exists($thumbName . '.bak')) {
            if (!rename($thumbName . '.bak', $thumbName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * ファイルのアップロード先ディレクトリのフルパスを得る
     *
     * @return string ファイル(サムネイルではない)のアップロード先ディレクトリ名
     * @author akitsukada
     */
    private function _getUploadDest()
    {
        return APPLICATION_PATH . '/../public/media/upload';
    }

    /**
     * サムネイルのアップロード先ディレクトリのフルパスを得る
     *
     * @return string サムネイルのアップロード先ディレクトリ名
     * @author akitsukada
     */
    private function _getThumbDest()
    {
        return APPLICATION_PATH . '/../public/media/thumbnail';
    }

    /**
     * ファイルのアップロード先ディレクトリが書き込み可能であるかを判定する
     *
     * @return boolean ファイルのアップロード先ディレクトリが書き込み可能か
     * @author akitsukada
     */
    private function _isWritableUploadDest()
    {
        $dir = $this->_getUploadDest();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

    /**
     * サムネイルのアップロード先ディレクトリが書き込み可能であるかを判定する
     *
     * @return boolean サムネイルのアップロード先ディレクトリが書き込み可能か
     * @author akitsukada
     */
    private function _isWritableThumbnailDest()
    {
        $dir = $this->_getThumbDest();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

}
