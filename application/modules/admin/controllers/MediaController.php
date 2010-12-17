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
     * サムネイルの表示時の幅。ビュー的な要素だがサムネイル生成時にモデルにも伝えるためここで定義。
     */
    const THUMBNAIL_WIDTH = 65;

    /**
     * 絞り込み処理で使うファイル種別「全て」のインデックス
     */
    const FILEEXT_ALL_INDEX = -1;

    /**
     * 絞り込み処理で使うファイル種別「全て」の値
     */
    const FILEEXT_ALL_VALUE = 'all';

    /**
     * 一覧表示時、１ページに何件のファイルを表示するか
     */
    const PAGE_LIMIT = 10;

    /**
     * SetucoCMSで扱えるファイルの種類（拡張子）。種類の追加／削除をしたい場合はサービス(Admin_Model_Media)も編集する必要がある。
     *
     * @var array
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
        parent::init();
        $this->_media = new Admin_Model_Media($this->_getThumbnailDest(), self::THUMBNAIL_WIDTH);
        $this->_setPageLimit(self::PAGE_LIMIT);
    }

    /**
     * ファイルのアップロードフォームやアップロードしてあるファイルの一覧を表示するページ
     *
     * @return void
     * @author akitsukada
     * @todo 複数ファイルアップロードの対応
     */
    public function indexAction()
    {

        // ページネーターのカレントページの取得
        $currentPage = $this->_getPageNumber();

        // ファイルタイプの絞り込み条件取得(デフォルトでは'all')
        $type = self::FILEEXT_ALL_VALUE;
        if ($this->getRequest()->isPost()) { // isPost == trueならばファイル種別絞り込みフォームでsubmitされている
            $type = array_key_exists($this->_getParam('fileType'), $this->_fileExt) ? $this->_fileExt[$this->_getParam('fileType')] : self::FILEEXT_ALL_VALUE;
            $currentPage = 1; // 新たに絞り込みされた場合は常に1ページ目表示
        } else {
            $type = $this->_getParam('type', self::FILEEXT_ALL_VALUE);     // ソートリンクでの指定
        }

        // ソート指定のカラムとオーダー取得 (デフォルトではファイル名'name'の昇順'asc')
        $sort = $this->_getParam('sort', 'name');
        $order = $this->_getParam('order', 'asc');

        // データ取得の条件を作る
        $condition = array(
            'type' => $type,
            'sort' => $sort,
            'order' => $order
        );

        // viewに現在指定されている条件の情報を渡す(カレントページや適用されているソートの矢印などの判断のため)
        $this->view->condition = $condition;

        // viewにファイルデータを渡す
        $this->view->medias = $this->_media->findMedias($condition, $currentPage, $this->_getPageLimit());

        // アップロードできる最大サイズをviewに教える
        $this->view->fileSizeMax = (int) (self::FILE_SIZE_MAX / 1024) . 'KB';

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
        $this->view->searchForm = $this->_createSearchForm($condition['type']);

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_media->countMedias($condition['type']));

        // フラッシュメッセージ設定
        $this->_showFlashMessages();
    }

    /**
     * ファイルの新規アップロード処理。DB（media）に新規レコード挿入、ファイルシステム上に受信した実ファイルを保存。画像ファイルの場合はサムネイルも生成する。
     *
     * @return void
     * @author akitsukada
     * @todo ファイルとDB、２フェーズコミットにしてエラー時のロールバックをちゃんと制御する
     */
    public function createAction()
    {

        // ファイルアップロードのポスト後でなければmediaのトップページへ
        if (!$this->getRequest()->isPost()) {
            $this->_helper->redirector('index');
        }

        // ファイル受信に使うadapter
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // フォームのファイル制限サイズ
        $minSizeString = self::FILE_SIZE_MIN . 'Byte';
        $maxSizeString = (int) (self::FILE_SIZE_MAX / 1024) . 'KB';
        $fileUploadValidator = new Zend_Validate_File_Upload(array());
        /*
          $fileUploadValidator->setMessages(array(
          Zend_Validate_File_Upload::FORM_SIZE => "アップロードできるファイルサイズは{$maxSizeString}までです。",
          Zend_Validate_File_Upload::INI_SIZE => "アップロードできるファイルサイズは{$maxSizeString}までです。"
          ));
         */
        $adapter->addValidator($fileUploadValidator, true);

        // ファイル個数設定
        // 0個でもシステムエラーが出ないようにする
        $adapter->setOptions(array('ignoreNoFile' => true));

        // ファイルタイプ（拡張子）の制限
        $exts = implode(',', $this->_fileExt);
        $fileExtensionValidator = new Zend_Validate_File_Extension($exts);
        /*
          $fileExtensionValidator->setMessages(array(
          Zend_Validate_File_Extension::FALSE_EXTENSION
          => "アップロードできるファイルの種類は {$exts} です。",
          Zend_Validate_File_Extension::NOT_FOUND
          => "アップロードできるファイルの種類は {$exts} です。",
          ));
         */
        $adapter->addValidator($fileExtensionValidator);

        // ファイルサイズ（サーバー側判定）
        $fileSizeValidator = new Zend_Validate_File_Size(array('bytestring' => true));
        $fileSizeValidator->setMax(self::FILE_SIZE_MAX);
        $fileSizeValidator->setMin(self::FILE_SIZE_MIN);
        /* デフォルトメッセージの方がサイズなどわかりやすいのでsetMessagesしない（@todo 将来的にカスタムする） */
        $adapter->addValidator($fileSizeValidator);

        // file inputを取得し1ファイルずつ処理
        $fileInfos = $adapter->getFileInfo();

        $uploadCount = 0;
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
                    // 一つも選択されていなければエラー表示
                    // @todo エラー処理
                    $this->_helper->flashMessenger->addMessage("ファイルが選択されていません。");
                    $this->_helper->redirector('index');
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
                // @todo newidをDBから削除
                continue;
            }
            if (!$this->_media->saveThumnailFromImage($adapter->getFileName($inputName))) {
                $uploadErrorMsgs[] = "ファイル{$filePath['basename']}のサムネイルが生成できませんでした。";
                // @todo newidをDBから削除
                // @todo ファイルを削除
                continue;
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
                // @todo newidをDBから削除
                // @todo ファイルの削除
                // @todo サムネイルの削除
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

        // IDに該当するファイル情報をサービスから取得
        $mediaData = $this->_media->findMediaById($id);

        // ビューにファイル情報を渡す
        $this->view->mediaData = $mediaData;

        // フォームの作成とviewへのセット
        $this->view->updateForm = $this->_createUpdateForm($id, $mediaData['name'], $mediaData['comment']);

        // アップロードできる最大サイズをviewに教える
        $this->view->fileSizeMax = self::FILE_SIZE_MAX . ' Byte';

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

        // 更新フォームオブジェクト取得
        $form = $this->_createUpdateForm($id);

        // Postデータ取得
        $post = $this->getRequest()->getPost();

        // formアクションへのリダイレクトURL
        $redirectUrl = '/admin/media/form/id/' . $id;

        // Postのバリデーション
        if (!$form->isValid($post)) {
            $msgs = $form->getMessages();
            foreach ($msgs[$form->getName()] as $msgCode => $msg) {
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

        // ファイル受信に使うadapterの作成
        $adapter = new Zend_File_Transfer_Adapter_Http();

        // ファイル関連はファイルが選択された場合のみ処理
        if ($adapter->getFileName()) {

            // 新しくアップロードされたオリジナルファイルの情報を取得
            $newFileInfo = pathinfo($adapter->getFileName());
            $extType = $newFileInfo['extension'];             // 拡張子取得
            // 既存のファイルと新ファイルで拡張子が違う場合は既存のファイル情報を取得（新ファイルアップロード後に古いファイルをremoveできるようにするため）
            $oldFileInfo = $this->_media->findMediaById($id);
            if ($extType !== $oldFileInfo['type']) {
                $isDeleteOldFile = true;
            }

            // 既存の同IDファイル削除
            if (!$this->_removeFileById($id)) {
                $this->_helper->flashMessenger->addMessage('既存のファイルが削除できませんでした。');
                $this->_redirect($redirectUrl);
            }

            // ファイルの保存先と保存名を指定
            $adapter->addFilter('Rename', array(// 別名を指定
                'target' => $this->_getUploadDest() . '/' . $id . '.' . $extType,
                'overwrite' => true
            ));

            // ファイルの受信と保存
            if (!$adapter->receive()) {
                $this->_helper->flashMessenger->addMessage('ファイルが正しく送信されませんでした。');
                $this->_redirect($redirectUrl);
            }

            // サムネイルを保存
            $this->_media->saveThumnailFromImage($adapter->getFileName());

            // 保存するファイル情報、拡張子を取得しておく
            $fileInfo['type'] = $extType;
        }

        // DBの更新
        if (!$this->_media->updateMediaInfo($id, $fileInfo)) {
            $this->_helper->flashMessenger->addMessage('ファイルが正しく更新できませんでした。');
            $this->_redirect($redirectUrl);
        }

        // 処理正常終了
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
     * @param  string $type 絞り込みたいファイル種別
     * @return Zend_Form ファイルの絞込み・ソート用フォームオブジェクト
     * @todo フォームのハッシュ値の設定
     * @author akitsukada
     */
    private function _createSearchForm($type = 'all')
    {
        // 絞り込みフォームのオブジェクト
        $searchForm = new Zend_Form();
        $searchForm->setMethod('post');
        $searchForm->setAction('/admin/media/index');

        // ファイルタイプのセレクトボックス
        $typeSelector = new Zend_Form_Element_Select('fileType');
        $typeSelector->clearDecorators()
                ->setLabel('ファイルの種類')
                ->setValue(array_search($type, $this->_fileExt))
                ->addDecorator('ViewHelper')
                ->addDecorator('Label', array('tag' => null))
                ->addMultiOption(self::FILEEXT_ALL_INDEX, '--指定なし--')
                ->addMultiOptions($this->_fileExt);

        $searchForm->addElement($typeSelector);

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

        // ファイル用バリデータ設定
        $fileSelector = $this->_setFileValidators($fileSelector, true);

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
     * ファイルアップロード／アップデートフォームのFile選択inputにバリデータをセットする
     *
     * @param Zend_File_Transfer_Adapter_Http | Zend $fileUploader バリデータを設定するファイル選択input
     * @return Zend_Form_Element_File バリデータ設定済みのファイル選択input
     * @todo ファイル名長さ仕様確認＆制限実装
     * @todo 拡張子対応外で大きなファイルをアップロードするとContent-lengthエラーになることについて対応
     * @author akitsukada
     */
    private function _setFileValidators($fileUploader)
    {

        // ファイルサイズ（クライアント側判定）
        $fileUploader->setMaxFileSize(self::FILE_SIZE_MAX);
        $fileUploadValidator = new Zend_Validate_File_Upload();
        $fileUploadValidator->setMessages(array(
            Zend_Validate_File_Upload::FORM_SIZE => 'アップロードできるファイルのサイズは ' . self::FILE_SIZE_MIN . ' Byte以上 ' . self::FILE_SIZE_MAX . ' Byte以下です。',
            Zend_Validate_File_Upload::INI_SIZE => 'サーバで設定された制限サイズを超えています。',
            Zend_Validate_File_Upload::NO_FILE => 'ファイルが選択されていません。'
        ));
        // HTMLのMAX_FILE_SIZEの時点でNGなら以降の検証を行わない
        $fileUploader->addValidator($fileUploadValidator, true);
        // @todo breakChainOnFailureのtrueが働かない？？（以降の検証も実施してしまう）
        //　一つのinputでUpできるファイル個数
        $minCount = 1;
        $maxCount = 1;

        /*
          $fileCountValidator = new Zend_Validate_File_Count(array('min' => $minCount, 'max' => $maxCount));
          $fileCountValidator->setMessages(array(
          Zend_Validate_File_Count::TOO_FEW => "ファイルが選ばれていません。",
          Zend_Validate_File_Count::TOO_MANY => "一度にアップロードできるファイルは {$maxCount} 個以内です。ファイルが多すぎます。"
          ));
          $fileUploader->addValidator($fileCountValidator);
         */ $fileUploader->addValidator(
                'Count',
                array('min' => $minCount, 'max' => $maxCount),
                array('messages', array(
                        Zend_Validate_File_Count::TOO_FEW => "ファイルが選ばれていません。",
                        Zend_Validate_File_Count::TOO_MANY => "一度にアップロードできるファイルは {$maxCount} 個以内です。ファイルが多すぎます。"
                ))
        );

        // ファイルサイズ（サーバー側判定）
        $fileSizeValidator = new Zend_Validate_File_Size(array('bytestring' => true));
        $fileSizeValidator->setMax(self::FILE_SIZE_MAX);
        $fileSizeValidator->setMin(self::FILE_SIZE_MIN);
        $fileSizeValidator->setMessages(array(
            Zend_Validate_File_Size::TOO_BIG => 'アップロードできるファイルのサイズは ' . self::FILE_SIZE_MIN . ' Byte以上 ' . self::FILE_SIZE_MAX . ' Byte以下です。',
            Zend_Validate_File_Size::TOO_SMALL => 'アップロードできるファイルのサイズは ' . self::FILE_SIZE_MIN . ' Byte以上 ' . self::FILE_SIZE_MAX . ' Byte以下です。'
        ));
        $fileUploader->addValidator($fileSizeValidator);

        // 受け入れる拡張子 ホワイトリスト式
        $fileExtensionValidator = new Zend_Validate_File_Extension(implode(',', $this->_fileExt));
        $fileExtensionValidator->setMessages(array(
            Zend_Validate_File_Extension::FALSE_EXTENSION => 'アップロードできるファイルの種類は ' . implode(', ', $this->_fileExt) . ' です。',
            Zend_Validate_File_Extension::NOT_FOUND => 'アップロードできるファイルの種類は ' . implode(', ', $this->_fileExt) . ' です。'
        ));
        $fileUploader->addValidator($fileExtensionValidator);

        return $fileUploader;
    }

    /**
     * アップロードされた、指定IDのファイル（ファイル本体とサムネイル両方）をファイルシステム上から削除する。
     * ファイル本体は、uploadディレクトリ内の<指定ID>.(jpg|gif|png|pdf|txt) を全て削除する。<指定ID>.jpg.bakなどは削除しない。
     * サムネイルは、thumbnailディレクトリ内の<指定D>.gifを削除する。
     * 結果がFalseの場合、その時点で削除してしまったファイルは元に戻らない。
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
                    if (preg_match("/^{$id}\.(" . implode("|", $this->_fileExt) . ")$/", $file)) {
                        if (!unlink("{$uploadDir}/{$file}")) {
                            return false;
                        }
                    }
                }
            }
        }

        // サムネイルの削除
        $thumbDir = $this->_getThumbnailDest();
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
    private function _getThumbnailDest()
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
        $dir = $this->_getThumbnailDest();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

}
