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
     * アカウントサービス
     *
     * @var Admin_Model_Account
     */
    private $_accountService;

    /**
     * 日付テキストボックスのvalue属性のフォーマット
     * (画面に表示されるものではない)
     *
     * @var string
     */
    const FORMAT_DATE_TEXT_BOX = 'YYYY-MM-dd';

    /**
     * 時刻テキストボックスのvalue属性のフォーマット
     * (画面に表示されるものではない)
     *
     * @var string
     */
    const FORMAT_TIME_TEXT_BOX = 'THH:mm:ss';

    /**
     * 未分類カテゴリーのvalue属性
     *
     * @var string
     */
    const UNCATEGORIZED_VALUE = 'uncategorized';

    /**
     * 指定なしのvalue属性
     *
     * @var string
     */
    const UNSELECTED_VALUE = 'default';

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
        $this->_accountService = new Admin_Model_Account();
    }

    /**
     * ページの一覧表示のアクション
     *
     * @return void
     * @author akitsukada charlesvineyard
     */
    public function indexAction()
    {
        $sortColumn = $this->_getParam('sort', 'title');
        $sortValidator = new Zend_Validate_InArray(array('title', 'create_date'));
        if (!$sortValidator->isValid($sortColumn)) {
            // 規定のもの以外はタイトルに
            $sortColumn = 'title';
        }
        $order = $this->_getParam('order', 'asc');
        $orderValidator = new Zend_Validate_InArray(array('asc', 'desc'));
        if (!$orderValidator->isValid($order)) {
            // 規定のもの以外はascに
            $order = 'asc';
        }
        $pages = $this->_pageService->loadPages($sortColumn, $order, $this->_getPageNumber(), $this->_getPageLimit());
        foreach ($pages as $key => $page) {
            $createDate = new Zend_Date($page['create_date'], 'YYYY-MM-dd hh:mm:ss');
            $pages[$key]['create_date'] = $createDate->toString('YYYY/MM/dd');
        }
        $this->view->pages = $pages;
        $this->view->searchForm = $this->_createSearchForm();
        $this->view->categoryForm = $this->_createCategoryForm();
        $this->view->statusForm = $this->_createStatusForm();
        $this->_showFlashMessages();
        $this->setPagerForView($this->_pageService->countPages());
    }
    
    /**
     * 検索フォームを作成します。
     * 
     * @return Setuco_Form フォーム
     * @author charlesvineyard
     */
    private function _createSearchForm()
    {
        $orAndOptions = array(
            0 => 'OR',
            1 => 'AND',
        );
        $targetOptions = array(
            0 => 'タイトル',
            1 => '本文',
            2 => '概要',
            3 => 'タグ',
        );
        $categories = $this->_categoryService->findAllCategoryIdAndNameSet();
        $categories[self::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('create'))
             ->addElement(
                 'Text',
                 'keyword',
                 array(
                     'id' => 'keyword',
                     'class' => 'defaultInput',
                     'required' => true,
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Radio',
                 'or_and',
                 array(
                     'required' => true,
                     'multiOptions' => $orAndOptions,
                     'separator' => "</dd>\n<dd>",
                     'value' => 'OR',    // selected指定
                 )
             )
             ->addElement(
                 'MultiCheckbox',
                 'targets',
                 array(
                     'required' => true,
                     'multiOptions' => $targetOptions,
                     'separator' => "</dd>\n<dd>",
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'required' => true,
                     'multiOptions' => $this->_addUnselectedOption($categories),
                     'value' => self::UNSELECTED_VALUE,    // selected指定
                 )
             )
             ->addElement(
                 'Select',
                 'account_id',
                 array(
                     'required' => true,
                     'multiOptions' => $this->_addUnselectedOption(
                         $this->_accountService->findAllAccountIdAndNicknameSet()),
                     'value' => self::UNCATEGORIZED_VALUE,    // selected指定
                 )
             )
             ->addElement(
                 'Select',
                 'status',
                 array(
                     'required' => true,
                     'multiOptions' => $this->_addUnselectedOption(
                         Setuco_Data_Constant_Page::allStatus()),
                     'value' => self::UNCATEGORIZED_VALUE,    // selected指定
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_search',
                 array(
                     'label'   => '検索',
                 )
             );
        $form->setMinimalDecoratorElements(array(
            'keyword',
            'or_and',
            'targets',
            'category_id',
            'account_id',
            'status',
            'sub_search',
            
        ));
        return $form;
    }
    
    /**
     * オプションに「指定なし」を追加します。
     * 
     * @param array $options オプション
     * @return array 指定なしを追加したオプション
     * @author charlesvineyard
     */
    private function _addUnselectedOption($options)
    {
        $addedOptions = array(self::UNSELECTED_VALUE => '--指定なし--');
        foreach ($options as $key => $value) {
            $addedOptions[$key] = $value;
        }
        return $addedOptions;
    }
    
    /**
     * カテゴリー変更フォームを作成します。
     *
     * @return Setuco_Form フォーム
     * @author charlesvineyard
     */
    private function _createCategoryForm()
    {
        $categories = $this->_categoryService->findAllCategoryIdAndNameSet();
        $categories[self::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('update-category'))
            ->addElement(
                'Select',    // selected 指定はビューでする
                'category_id',
                array(
                    'required' => true,
                    'onchange' => 'showPageElementEdit(this);',
                    'multiOptions' => $categories,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'hidden_page_id',
                array(
                    'required' => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'hidden_page_title',
                array(
                    'required' => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Submit',
                'sub_category',
                array(
                    'label'   => '変更',
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'button',
                'cancel_category',
                array(
                    'id'      => 'cancel',
                    'label'   => 'キャンセル',
                    'onclick' => 'hidePageElementEdit(this);',
                    'decorators' => array('ViewHelper')
                )
            );
        return $form;
    }

    /**
     * 状態変更フォームを作成します。
     *
     * @return Setuco_Form フォーム
     * @author charlesvineyard
     */
    private function _createStatusForm()
    {
        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('update-status'))
            ->addElement(
                'Select',    // selected 指定はビューでする
                'status',
                array(
                    'required' => true,
                    'onchange' => 'showPageElementEdit(this);',
                    'multiOptions' => Setuco_Data_Constant_Page::allStatus(),
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'hidden_page_id',
                array(
                    'required' => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'hidden_page_title',
                array(
                    'required' => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Submit',
                'sub_category',
                array(
                    'label'   => '変更',
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'button',
                'cancel_category',
                array(
                    'id'      => 'cancel',
                    'label'   => 'キャンセル',
                    'onclick' => 'hidePageElementEdit(this);',
                    'decorators' => array('ViewHelper')
                )
            );
        return $form;
    }

    /**
     * ページ新規作成フォームのアクション
     *
     * @return void
     * @author akitsukda charlesvineyard
     */
    public function formAction()
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
        $this->view->form = $this->_getParam('form', $this->_createForm());
    }

    /**
     * ページ編集フォームを作成します。
     *
     * @return Setuco_Form フォーム
     * @author akitsukda charlesvineyard
     */
    private function _createForm()
    {
        $categories = $this->_categoryService->findAllCategoryIdAndNameSet();
        $categories[self::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $nowDate = Zend_Date::now();
        $form = new Setuco_Form();
        $form->enableDojo()
             ->setAction($this->_helper->url('create'))
             ->addElement(
                 'Submit',
                 'sub_open',
                 array(
                     'label'   => '公開して保存',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_draft',
                 array(
                     'label' => '下書きして保存',
                 )
             )
             ->addElement(
                 'Text',
                 'page_title',
                 array(
                     'id' => 'page_title',
                     'required' => true,
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'required' => true,
                     'multiOptions' => $categories,
                     'value' => self::UNCATEGORIZED_VALUE,    // selected指定
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
                     'required' => true,
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
                     'value' => $nowDate->toString(self::FORMAT_DATE_TEXT_BOX),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'TimeTextBox',
                 'create_time',
                 array(
                     'id' => 'create_time',
                     'value' => $nowDate->toString(self::FORMAT_TIME_TEXT_BOX),
                     'TimePattern' => 'HH:mm:ss',    // 表示用フォーマット
                     'filters' => array(
                         'StringTrim'
                     )
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
        $categoryId = $this->_getParam('category_id');
        if ($categoryId === self::UNCATEGORIZED_VALUE) {
            $categoryId = null;
        }
        $this->_pageService->regist(
            $this->_getParam('page_title'),
            $this->_getParam('page_contents'),
            $this->_getParam('page_outline'),
            Setuco_Util_String::splitCsvString($this->_getParam('tag')),
            $this->_findCreateDate(),
            $this->_findStatus(),
            $categoryId
        );
        $this->_helper->flashMessenger('新規ページを作成しました。');
        $this->_helper->redirector('form');    // TODO 作ったページの編集ページに飛ぶ
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
        $createDate = Setuco_Util_String::getDefaultIfEmpty(
            $createDate, $nowDate->toString(self::FORMAT_DATE_TEXT_BOX));
        $createTime = $this->_getParam('create_time');
        $createTime = Setuco_Util_String::getDefaultIfEmpty(
            $createTime, $nowDate->toString(self::FORMAT_TIME_TEXT_BOX));
        return new Zend_Date(
            $createDate . $createTime,
            self::FORMAT_DATE_TEXT_BOX . self::FORMAT_TIME_TEXT_BOX);
    }

    /**
     * 作成したページを公開前にプレビューするアクション
     *
     * @return void
     * @author akitsukada
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
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/page/index');
    }

    /**
     * ページのカテゴリーを更新するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateCategoryAction()
    {
        $form = $this->_createCategoryForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('categoryForm', $form);
            return $this->_forward('index');
        }
        $this->_pageService->update(
            $form->getValue('hidden_page_id'),
            array(
                'category_id' => $form->getValue('category_id')
            )
        );
        $this->_helper->flashMessenger('「' . $form->getValue('hidden_page_title') . '」のカテゴリーを変更しました。');
        $this->_helper->redirector('index');
    }

    /**
     * ページのカテゴリーを更新するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateStatusAction()
    {
        $form = $this->_createStatusForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('statusForm', $form);
            return $this->_forward('index');
        }
        $this->_pageService->update(
            $form->getValue('hidden_page_id'),
            array(
                'status' => $form->getValue('status')
            )
        );
        $this->_helper->flashMessenger('「' . $form->getValue('hidden_page_title') . '」の状態を変更しました。');
        $this->_helper->redirector('index');
    }

    /**
     * ページを削除するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada charlesvineyard
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $validator = new Zend_Validate_Digits();
        if (!$validator->isValid($id)) {
            $this->_helper->redirector('index');
        }
        $page = $this->_pageService->load($id);
        $this->_pageService->delete($id);
        $this->_helper->flashMessenger('「' . $page['title'] . '」を削除しました。');
        $this->_helper->redirector('index');
    }

}

