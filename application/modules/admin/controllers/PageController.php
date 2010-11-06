<?php
/**
 * 管理側のページを管理するコントローラー。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author	    akitsukada
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author	    akitsukada
 */
class Admin_PageController extends Setuco_Controller_Action_AdminAbstract
{
    /**
     * ページサービス
     *
     * @var Admin_Model_Page
     */
    private $_pageService;

    /**
     * カテゴリーサービス
     *
     * @var Admin_Model_Category
     */
    private $_categoryService;

    /**
     * 初期処理
     *
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_pageService = new Admin_Model_Page();
        $this->_categoryService = new Admin_Model_Category();
    }

    /**
     * ページの一覧表示のアクション
     *
     * @return void
     * @author	akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

    }

    /**
     * ページ新規作成フォームのアクション
     *
     * @return void
     * @author akitsukda charlesvineyard
     */
    public function formAction()
    {
        $form = $this->_createForm();
        $this->view->form = $form;
    }

    /**
     * ページ編集フォームを作成します。
     *
     * @return Setuco_Form フォーム
     * @author akitsukda charlesvineyard
     */
    private function _createForm()
    {
        $categories = $this->_categoryService->searchAllCategoryIdAndNameSet();
        $form = new Setuco_Form();
        $form->enableDojo()
             ->setAction($this->_helper->url('create'))
             ->addElement(
                 'Submit',
                 'sub_open',
                 array(
                     'label'   => '公開して保存',
                     'onclick' => 'setStatus(this)',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_draft',
                 array(
                     'label' => '下書きして保存',
                     'onclick' => 'setStatus(this)',
                     )
             )
             ->addElement(
                 'Text',
                 'page_title',
                 array(
                     'id' => 'page_title',
                     'value' => '',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'multiOptions' => $categories
                 )
             )
             ->addElement(
                 'Editor',
                 'page_contents',
                 array(
                     'id' => 'page_contents',
                     'plugins' => array(
                        'undo',         // 戻す
                        'redo',         // やり直し
                        'cut',          // 切り取り
                        'copy',         // コピー
                        'paste',        // ペースト
                        'selectAll',    // 全て選択
                        'bold',         // 太字
                        'subscript',    // 下付き文字
                        'superscript',  // 上付き文字
                        'removeFormat', // 形式の除去
                        'insertOrderedList',    // 番号付きリスト
                        'insertUnorderedList',  // 黒丸付きリスト
                        'insertHorizontalRule', // 水平罫線
                        'createLink',   // リンクの作成
                        'unlink',       // リンクの除去
                        'delete',       // 削除
                        'foreColor',    // テキストの色
                        'hiliteColor',  // マーカー(背景の色)
                        'fontSize',     // サイズ
                        'insertImage',  // イメージの挿入
                        'fullscreen',   // フルスクリーン
                        'viewsource',   // HTMLソース表示
                        'newpage',      // 新規ページ
                     ),
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Text',
                 'page_outline',
                 array(
                     'id' => 'page_outline',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Text',
                 'tag',
                 array(
                     'id' => 'tag',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'DateTextBox',
                 'create_date',
                 array(
                     'id' => 'create_date',
                 )
             )
             ->addElement(
                 'TimeTextBox',
                 'create_time',
                 array(
                     'id' => 'create_time',
                 )
             );
        $form->setMinimalDecoratorElements(array(
            'sub_open',
            'sub_draft',
            'page_title',
            'category_id',
            'page_outline',
            'tag',
        ));
        $form->removeDecoratorsOfElements(
            array (
                'Errors',
                'Description',
                'HtmlTag',
                'Label',
            ),
            array(
                'page_contents',
                'create_date',
                'create_time'
            )
        );
        return $form;
    }

    /**
     * ページを新規作成する
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada charlesvineyard
     */
    public function createAction()
    {
        $form = $this->_createForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('form', $form);
            return $this->_forward('form');
        }
        $this->_pageService->regist(
            $this->_getParam('page_title'),
            $this->_getParam('page_contents'),
            $this->_getParam('page_outline'),
            Setuco_Util_String::splitCsvString($this->_getParam('tag')),
            $this->_findCreateDate(),
            $this->_findStatus(),
            $this->_getParam('category_id')
        );
        $this->_helper->flashMessenger('新規ページを作成しました。');
        $this->_helper->redirector('form');
    }

    /**
     * 入力されたページの状態を求めます。
     *
     * @return ページの状態
     * @author charlesvineyard
     */
    private function _findStatus()
    {
        if ($this->_getParam('sub_open') !== null) {
            return Setuco_Data_Constant_Page::STATUS_RELEASE;
        }
        return Setuco_Data_Constant_Page::STATUS_DRAFT;
    }

    /**
     * 入力された作成日時を求めます。
     *
     * @return 作成日時
     * @author charlesvineyard
     */
    private function _findCreateDate()
    {
        $nowDate = Zend_Date::now();
        $createDate = $this->_getParam('create_date');
        $createDate = Setuco_Util_String::getDefaultIfEmpty($createDate, $nowDate->toString('YYYY-MM-dd'));
        $createTime = $this->_getParam('create_time');
        $createTime = Setuco_Util_String::getDefaultIfEmpty($createTime, $nowDate->toString('THH:mm:00'));
        return new Zend_Date($createDate . $createTime, 'YYYY-MM-ddTHH:mm:00');
    }

    /**
     * 作成したページを公開前にプレビューするアクション
     *
     * @return void
     * @author	akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function previewAction()
    {
    }

    /**
     * ページを更新処理するアクション
     * indexアクションに遷移します ※
     * ※ただしスケルトンのときだけ
     *
     * @return void
     * @author	akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/page/index');
    }

    /**
     * ページを削除するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author	akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/page/index');
    }

}

