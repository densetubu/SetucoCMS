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
     * 複数ファイルアップロード用inputのidとnameになる文字列の共通部分。末尾に連番を振る。
     * @var string
     */
    private $_fileInputID_base = 'upload_img_';
    /**
     * 複数ファイルアップロード用inputのidとname。$_fileInputID_baseに連番を振ったもの。
     * 連番は1〜FILE_COUNT_MAXまでで、init()で設定される。
     * @var array
     */
    private $_fileInputIDs = array();

    /**
     * アップロードできる最大ファイル個数
     */
    const FILE_COUNT_MAX = 5;

    /**
     * アップロードできるファイルサイズの最大値（Byte単位）
     */
    const FILE_SIZE_MAX = 512000; // 500kB

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
     * ファイルの説明の長さ 最長文字数
     */
    const FILECOMMENT_LENGTH_MAX = 300;

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
        for ($inputID = 1; $inputID <= self::FILE_COUNT_MAX; $inputID++) {
            $this->_fileInputIDs[] = $this->_fileInputID_base . (string) $inputID;
        }
        $this->_media = new Admin_Model_Media();
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

        $searchForm = $this->_createSearchForm();
        if ($this->_getParam('isNarrowDown')) {
            // 絞り込みフォーム経由でIndexに来た
            $extensions = Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS();
            $fileType = array_key_exists($this->_getParam('fileType'), $extensions) ?
                    $extensions[$this->_getParam('fileType')] :
                    Setuco_Data_Constant_Media::FILEEXT_ALL_VALUE;
            $currentPage = 1; // 新たに絞り込みされた場合は常に1ページ目表示
            $searchForm->isValid($_POST);
        } else {
            $fileType = $this->_getParam('type', Setuco_Data_Constant_Media::FILEEXT_ALL_VALUE);
        }

        // viewにファイル絞込み・ソート用フォームの作成
        $searchForm->setDefault('fileType', array_search($fileType,
                        Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS()));
        $this->view->searchForm = $searchForm;

        // ソート指定のカラムとオーダー取得 (デフォルトではファイル名'name'の昇順'asc')
        $sortColumn = $this->_getParam('sort', 'name');
        $order = $this->_getParam('order', 'asc');

        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->fileType = $fileType;
        $this->view->sortColumn = $sortColumn;
        $this->view->order = $order;

        // ファイル情報の取得とファイルの存在確認
        $medias = $this->_media->findMedias(
                        $sortColumn, $order, $this->_getPageLimit(), $currentPage, $fileType);
        $this->view->medias = $medias;

        // アップロードできる最大サイズをviewに教える
        $this->view->fileSizeMax = (int) (self::FILE_SIZE_MAX / 1024) . 'kB';

        // ディレクトリに問題なければviewにファイルアップロード用フォームを設定
        $dirErrors = array();
        if (!Setuco_Util_Media::isWritableUploadDir()) {
            $dirErrors[] = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . '　が存在しないか、書き込みできません。';
        }
        if (!Setuco_Util_Media::isWritableThumbDir()) {
            $dirErrors[] = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . '　が存在しないか、書き込みできません。';
        }
        if (count($dirErrors) > 0) {
            $this->view->dirErrors = $dirErrors;
        }
        $this->view->uploadForm = $this->_getParam('uploadForm', $this->_createUploadForm());

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_media->countMedias($fileType));

        // フラッシュメッセージ設定 (flashMessengerを使うとforward前に設定したメッセージが表示されないので直接viewに設定
        $flashMsgs = $this->_getParam('flashMsgs');
        if (count($flashMsgs) > 0) {
            $this->view->flashMessages = $flashMsgs;
        }
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

        $form = $this->_createUploadForm();

        if (empty($_POST)) {
            // iniサイズを超えた場合など、不正なPOSTが行われた場合
            $form->isValid($_POST);
            $form->setErrorMessages(array("アップロード中にサーバーエラーが発生しました。"));
            $this->_setParam('uploadForm', $form);
            return $this->_forward(
                    'index'
            );
        }

        $files = array();
        $fileInfos = array();
        foreach ($this->_fileInputIDs as $inputName) {
            $file = $form->getElement($inputName);
            $files[$inputName] = $file;
            $fileInfos[$inputName] = array_pop($file->getFileInfo());
        }

        $noFileCount = 0;
        $uploadSuccessMsgs = array();
        $uploadErrorMsgs = array();
        foreach ($fileInfos as $inputName => $fileInfo) {

            // 個々のファイルをバリデート
            if (!$files[$inputName]->isValid($inputName)) {
                $msgs = $files[$inputName]->getMessages();
                foreach ($msgs as $msg) {
                    $uploadErrorMsgs[] = $msg;
                }
                continue; // 次のファイル処理を続ける
            }

            // ファイルが選択されなかったinputは飛ばす
            if (empty($fileInfo['tmp_name'])) {
                $noFileCount++;
                if ($noFileCount >= self::FILE_COUNT_MAX) {
                    // 一つも選択されていなければエラー
                    $form->markAsError();
                    $form->setErrorMessages(array("ファイルを選択してください。"));
                    $this->_setParam('uploadForm', $form);
                    return $this->_forward(
                            'index'
                    );
                }
                continue;
            }

            $filePath = pathinfo($fileInfo['name']);
            $extType = $filePath['extension'];

            // ファイルの保存先と物理名（id.拡張子)を指定
            $newId = $this->_media->createNewMediaID();
            $files[$inputName]->addFilter('Rename', array(
                'target' => Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$newId}.{$extType}",
                'overwrite' => true
            ));

            // ファイルの受信と保存
            if (!$files[$inputName]->receive($inputName)) {
                $uploadErrorMsgs[] = "ファイル「{$fileInfo['name']}」が正しく送信されませんでした。";
                $this->_media->deleteMediaById($newId);
                continue;
            }

            $newFileName = $files[$inputName]->getFileName($inputName);

            // 拡張子が画像でファイル内容が有効ならサムネイル生成
            if (Setuco_Util_Media::isImageExtension($extType)) {
                if (!$this->_media->isValidImageData($newFileName)) {
                    $uploadErrorMsgs[] = "ファイル「{$fileInfo['name']}」は不正な画像データです。";
                    $this->_removeFileById($newId);
                    continue;
                }
                if (!$this->_media->saveThumbnailFromImage($newFileName)) {
                    $uploadErrorMsgs[] = "ファイル「{$fileInfo['name']}」のサムネイルが生成できませんでした。";
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
                $uploadErrorMsgs[] = "ファイル「{$fileInfo['name']}」がデータベースに保存できませんでした。";
                $this->_media->deleteMediaById($newId);
                $this->_removeFileById($newId);
                continue;
            }

            $uploadSuccessMsgs[] = "ファイル「{$fileInfo['name']}」 をアップロードしました。";
        }

        if (count($uploadErrorMsgs) > 0) {
            $form->setErrorMessages($uploadErrorMsgs);
            $form->markAsError();
            $this->_setParam('flashMsgs', $uploadSuccessMsgs);
            $this->_setParam('uploadForm', $form);
            return $this->_forward(
                    'index'
            );
        } else {
            foreach ($uploadSuccessMsgs as $msg) {
                $this->_helper->flashMessenger->addMessage($msg);
            }
            $this->_helper->redirector('index');
        }
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

        $this->view->mediaData = $mediaData;
        $this->view->fileSizeMax = (int) (self::FILE_SIZE_MAX / 1024) . 'kB';
        $updateForm = $this->_getParam(
                        'updateForm',
                        $this->_createUpdateForm($id)
        );
        $updateForm->setDefaults(array(
            'name' => $this->_getParam('inputName', $mediaData['name']),
            'comment' => $this->_getParam('inputComment', $mediaData['comment']),
            'preExtension' => $mediaData['type']
        ));
        $this->view->updateForm = $updateForm;

        $this->_pageTitle = "「{$mediaData['name']}」({$mediaData['id']}.{$mediaData['type']})の編集";

        // フラッシュメッセージ設定
        $this->_showFlashMessages();
    }

    /**
     * ファイル更新処理のアクション。DBのレコードとファイルシステム上の実ファイル両方を更新する。
     * formアクションに遷移する。
     *
     * @return void
     * @author akitsukada
     */
    public function updateAction()
    {

        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_helper->redirector('index');
        }

        // 編集対象のファイルIDを取得（指定されていなかったらmediaトップへ）
        $id = $this->_getParam('id');
        if (is_null($id)) {
            $this->_helper->redirector('index');
        }

        $form = $this->_createUpdateForm($id);
        $post = $this->getRequest()->getPost();

        // Postのバリデーション
        $form->isValid($this->_getAllParams());

        if (empty($_POST)) {
            // iniサイズを超えた場合など、不正なPOSTが行われた場合
            $form->setErrorMessages(array("アップロード中にサーバーエラーが発生しました。"));
            $this->_setParam('updateForm', $form);
            return $this->_forward(
                    'form', null, null,
                    array('id' => $id)
            );
        }

        $inputName = $form->getElement('name')->getValue();
        $inputComment = $form->getElement('comment')->getValue();

        if ($form->isErrors()) {
            return $this->_updateFailed($id, $inputName, $inputComment, $form);
        }

        $file = $form->getElement('upload_img');

        // サービスにDBをUpdateさせるためのファイルの情報
        $fileInfo = array(
            'name' => $inputName,
            'comment' => $inputComment,
            'update_date' => date('Y/m/d H:i:s')
        );

        $isFileUploaded = false;

        // ファイル関連はファイルが選択された場合のみ処理
        if ($file->getFileName()) {

            // 新しくアップロードされたオリジナルファイルの情報を取得
            $newFileInfo = pathinfo($file->getFileName());
            $extType = $newFileInfo['extension'];
            $preExtType = $post['preExtension'];
            $isFileUploaded = true;

            // 既存の同IDファイルを退避
            if (!$this->_backupFileById($id, $preExtType)) {
                return $this->_updateFailed(
                        $id, $inputName, $inputComment, $form,
                        "既存のファイル{$id}.{$extType}が削除できません。"
                );
            }

            // 既存の同IDファイルを削除
            if (!$this->_removeFileById($id)) {
                $this->_recoverFromBackUpFile($id, $preExtType);
                return $this->_updateFailed(
                        $id, $inputName, $inputComment, $form,
                        "既存のファイル{$id}.{$extType}が削除できません。"
                );
            }

            // ファイルの保存先と保存名を指定
            $file->addFilter('Rename', array(// 別名を指定
                'target' => Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$id}.{$extType}",
                'overwrite' => true
            ));

            // ファイルの受信と保存
            if (!$file->receive()) {
                $this->_recoverFromBackUpFile($id, $preExtType);
                return $this->_updateFailed(
                        $id, $inputName, $inputComment, $form,
                        'ファイルが正しく送信されませんでした。'
                );
            }

            $newFileName = $file->getFileName();

            // 画像なら有効な画像データかどうか確認
            if (Setuco_Util_Media::isImageExtension($extType)) {
                if (!$this->_media->isValidImageData($newFileName)) {
                    $this->_recoverFromBackUpFile($id, $preExtType);
                    return $this->_updateFailed(
                            $id, $inputName, $inputComment, $form,
                            "{$newFileInfo['basename']}は不正な画像データです。"
                    );
                }

                // サムネイルを保存
                if (!$this->_media->saveThumbnailFromImage($newFileName)) {
                    $this->_recoverFromBackUpFile($id, $preExtType);
                    return $this->_updateFailed(
                            $id, $inputName, $inputComment, $form,
                            'サムネイルが保存できませんでした。'
                    );
                }
            }

            // 保存するファイル情報、拡張子を取得しておく
            $fileInfo['type'] = $extType;
        }

        $redirectUrl = '/admin/media/form/id/' . $id;

        // DBの更新
        if (!$this->_media->updateMediaInfo($id, $fileInfo)) {
            if ($isFileUploaded) {
                $this->_recoverFromBackUpFile($id, $preExtType);
            }
            return $this->_updateFailed(
                    $id, $inputName, $inputComment, $form,
                    'ファイルが正しく更新できませんでした。'
            );
        }

        // 処理正常終了
        if ($isFileUploaded) {
            $this->_removeBackupFile($id, $preExtType);
        }

        $this->_helper->flashMessenger->addMessage('ファイル情報を更新しました。');
        $this->_redirect($redirectUrl);
    }

    /**
     * 更新処理中にエラーが起きたとき、$formにエラー情報をセットしてformActionにforwardする
     *
     * @param  int $id
     * @param  string $inputName
     * @param  string $inputComment
     * @param  Zend_Form $form
     * @return void
     */
    private function _updateFailed($id, $inputName, $inputComment, Zend_Form $form, $errorMessage = null)
    {
        $this->_setParam('inputName', $inputName);
        $this->_setParam('inputComment', $inputComment);
        $this->_setParam('updateForm', $form);
        $form->markAsError();
        if ($errorMessage !== null) {
            $form->setErrorMessages(array($errorMessage));
        }
        $this->_forward(
                'form', null, null,
                array('id' => $id)
        );
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
     * @return Zend_Form ファイルの絞込み・ソート用フォームオブジェクト
     * @todo フォームのハッシュ値の設定
     * @author akitsukada
     */
    private function _createSearchForm()
    {
        // 絞り込みフォームのオブジェクト
        $searchForm = new Zend_Form();
        $searchForm->setMethod('post');
        $searchForm->setAction('/admin/media/index');

        // ファイルタイプのセレクトボックス
        $fileTypeSelector = new Zend_Form_Element_Select('fileType');
        $fileTypeSelector->clearDecorators()
                ->setLabel('ファイルの種類')
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array())
                ->addMultiOption(
                        Setuco_Data_Constant_Media::FILEEXT_ALL_INDEX,
                        Setuco_Data_Constant_Media::FILEEXT_ALL_STRING)
                ->addMultiOptions(Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS());

        $fileTypeRangeValidator = new Zend_Validate_Between(0, count(Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS()));
        $fileTypeRangeValidator->setMessage("ファイルの種類を選択してください。");
        $fileTypeSelector->addValidator($fileTypeRangeValidator);
        $searchForm->addElement($fileTypeSelector);

        // 絞込みボタン
        $searchFormSubmit = new Zend_Form_Element_Submit('search',
                        array('class' => 'upSub', 'Label' => '絞込み')
        );
        $searchFormSubmit->clearDecorators()
                ->addDecorator('ViewHelper');
        $searchForm->addElement($searchFormSubmit);

        $hiddenIsNarrowDown = new Zend_Form_Element_Hidden('isNarrowDown', array(
                    'id' => 'isNarrowDown', 'value' => TRUE));
        $hiddenIsNarrowDown->clearDecorators()->addDecorator('ViewHelper');
        $searchForm->addElement($hiddenIsNarrowDown);

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
        $uploadForm = new Setuco_Form();
        $uploadForm->setName('upload_img');
        $uploadForm->setMethod('post');
        $uploadForm->setAction($this->_helper->url('create'));
        $uploadForm->setAttrib('enctype', 'multipart/form-data');

        // ファイル選択パーツ作成と余分な装飾タグの除去
        $fileCount = 1;
        for (; $fileCount <= self::FILE_COUNT_MAX; $fileCount++) {
            $inputName = 'upload_img_' . $fileCount;
            $fileSelector = new Zend_Form_Element_File($inputName);
            $fileSelector
                    ->clearDecorators()
                    ->addDecorator('file');
            $fileSelector->setValidators($this->_makeFileValidators());
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
     * @return Zend_Form ファイルの更新（=上書きアップロード）用フォームオブジェクト
     * @todo フォームのハッシュ値の設定
     * @author akitsukada
     *
     */
    private function _createUpdateForm($id)
    {

        // 編集用フォームの作成
        $updateForm = new Setuco_Form();
        $updateForm->setName('upload_img');
        $updateForm->setMethod('post');
        $updateForm->setAction($this->_helper->url('update/id/' . $id));
        $updateForm->setAttrib('enctype', 'multipart/form-data');

        // ファイル名テキスト
        $txtFileName = new Zend_Form_Element_Text('name', array(
                    'id' => 'name',
                    'required' => TRUE,
                    'filters' => array(new Setuco_Filter_FullWidthStringTrim())
                ));
        $txtFileName->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null));
        $txtFileName->setValidators($this->_makeFileNameValidators());

        // ファイルの説明テキスト
        $txtFileComment = new Zend_Form_Element_Text('comment', array(
                    'id' => 'comment',
                    'required' => FALSE,
                    'filters' => array(new Setuco_Filter_FullWidthStringTrim())
                ));
        $txtFileComment->clearDecorators()
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null));
        $txtFileComment->setValidators($this->_makeFileCommentValidators());

        // ファイル選択
        $fileSelector = new Zend_Form_Element_File('upload_img', array(
                    'id' => 'upload_img',
                    'required' => FALSE,
                    'size' => 55
                ));
        $fileSelector->setLabel(null)
                ->clearDecorators()
                ->addDecorator('file')
                ->addDecorator('Label', array('tag' => null))
                ->addDecorator('HtmlTag', null);
        $fileSelector->setValidators($this->_makeFileValidators());

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
        $updateForm->addElement($btnSubmit);

        $hiddenType = new Zend_Form_Element_Hidden('preExtension', array('id' => 'preExtension'));
        $hiddenType->clearDecorators()
                ->addDecorator('ViewHelper');
        $updateForm->addElement($hiddenType);

        // フォームを返す
        return $updateForm;
    }

    private function _makeFileNameValidators()
    {
        $validators = array();

        $nameNotEmptyValidator = new Zend_Validate_NotEmpty();
        $nameNotEmptyValidator->setMessage('ファイル名を入力してください。');
        $validators[] = array($nameNotEmptyValidator, TRUE);

        $nameLengthValidator = new Zend_Validate_StringLength(array('max' => self::FILENAME_LENGTH_MAX));
        $nameLengthValidator->setEncoding('UTF-8');
        $nameLengthValidator->setMessage('ファイル名は%max%文字以下で入力してください。');
        $validators[] = array($nameLengthValidator, TRUE);

        return $validators;
    }

    private function _makeFileCommentValidators()
    {
        $validators = array();

        $commentLengthValidator = new Zend_Validate_StringLength(
                        array('max' => self::FILECOMMENT_LENGTH_MAX));
        $commentLengthValidator->setEncoding('UTF-8');
        $commentLengthValidator->setMessage(array("説明は%max%文字以下で入力してください。"));
        $validators[] = array($commentLengthValidator, true);

        return $validators;
    }

    /**
     * ファイル選択inputのバリデータを作成して返す。
     *
     * @return array バリデータの配列
     * @author akitsukada
     */
    private function _makeFileValidators()
    {

        $validators = array();

        $fileExtValidator = new Zend_Validate_File_Extension(Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS());
        $fileExtValidator->setMessage("拡張子エラー「%value%」　アップロードできるファイルの種類は "
                . implode(", ", Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS()) . " です。");
        $validators[] = $fileExtValidator;

        $fileSizeValidator = new Zend_Validate_File_Size(array(
                    'min' => self::FILE_SIZE_MIN,
                    'max' => self::FILE_SIZE_MAX
                ));
        $minSizeString = self::FILE_SIZE_MIN . 'Byte';
        $maxSizeString = (self::FILE_SIZE_MAX / 1024) . 'kB';
        $sizeErrorString =
                "サイズエラー「%value%（%size%）」　アップロードできるファイルのサイズは {$minSizeString} 以上 {$maxSizeString} 以下です。";
        $fileSizeValidator->setMessages(array(
            Zend_Validate_File_Size::TOO_BIG => $sizeErrorString,
            zend_validate_file_size::TOO_SMALL => $sizeErrorString
        ));
        $validators[] = $fileSizeValidator;

        $fileUploadValidator = new Zend_Validate_File_Upload();
        $fileUploadValidator->setMessage(
                "サーバーエラー　アップロードできるファイルのサイズは {$minSizeString} 以上 {$maxSizeString} 以下です。",
                Zend_Validate_File_Upload::INI_SIZE);
        $fileUploadValidator->setMessage(
                "サーバーエラー　アップロードできるファイルのサイズは {$minSizeString} 以上 {$maxSizeString} 以下です。",
                Zend_Validate_File_Upload::FORM_SIZE);
        $validators[] = $fileUploadValidator;

        return $validators;
    }

    /**
     * アップロードされた、指定IDのファイル（ファイル本体とサムネイル両方）をファイルシステム上から削除する。
     * ファイル本体は、uploadディレクトリ内の<指定ID>.(jpg|gif|png|pdf|txt) を全て削除する。
     * <指定ID>.jpg.bakなどは削除しない。
     * サムネイルは、thumbnailディレクトリ内の<指定ID>.gifを削除する。
     * 処理中にfalseとなった場合、その時点で削除してしまったファイルは元に戻らない。
     *
     * @param int $id 削除するファイルのID
     * @return boolean 正常にファイル本体／サムネイルとも削除できればTrue、一つでも削除に失敗すればFalse。
     * @author akitsukada
     */
    private function _removeFileById($id)
    {
        // ファイルの削除
        $uploadDir = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH();
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
        $thumbDir = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH();
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
     * @param string $preExtension 既存ファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _backupFileById($id, $preExtension)
    {

        if (!$this->_removeBackupFile($id, $preExtension)) {
            return false;
        }
        // ファイル名に.bakを付ける
        $fileName = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$id}.{$preExtension}";
        if (file_exists($fileName)) {
            if (!rename($fileName, $fileName . '.bak')) {
                return false;
            }
        }
        // サムネイルも
        $thumbName = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . "/{$id}.gif";
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
     * @param string $extension バックアップファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _removeBackupFile($id, $extension)
    {
        $fileName = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$id}.{$extension}";
        if (file_exists($fileName . '.bak')) {
            if (!unlink($fileName . '.bak')) {
                return false;
            }
        }
        $thumbName = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . "/{$id}.gif";
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
     * @param string $extension バックアップファイルの拡張子
     * @return boolean 処理成功すればtrue,失敗すればfalse
     * @author akitsukada
     */
    private function _recoverFromBackUpFile($id, $extension)
    {
        $this->_removeFileById($id);
        $fileName = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$id}.{$extension}";
        if (file_exists($fileName . '.bak')) {
            if (!rename($fileName . '.bak', $fileName)) {
                return false;
            }
        }
        $thumbName = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . "/{$id}.gif";
        if (file_exists($thumbName . '.bak')) {
            if (!rename($thumbName . '.bak', $thumbName)) {
                return false;
            }
        }
        return true;
    }

}
