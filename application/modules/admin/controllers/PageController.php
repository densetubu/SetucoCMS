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
     * タグサービス
     *
     * @var Admin_Model_Tag
     */
    private $_tagService;
    
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
     * 指定なしのvalue属性
     *
     * @var string
     */
    const UNSELECTED_VALUE = 'default';
    
    /**
     * カテゴリーが未分類のvalue属性
     * 
     * @var string
     */
    private $_uncategorizedValue = Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE;

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
        $this->_tagService = new Admin_Model_Tag();
        
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
        $pages = $this->_pageService->findPages($sortColumn, $order, $this->_getPageNumber(), $this->_getPageLimit());
        $pages = $this->_adjustPages($pages);
        
        $this->view->pages = $pages;
        $this->view->searchForm = $this->_createSearchForm();
        $this->view->categoryForm = $this->_createCategoryForm();
        $this->view->statusForm = $this->_createStatusForm();
        $this->_showFlashMessages();
        $this->setPagerForView($this->_pageService->countPages());
    }

    /**
     * ページを検索して一覧表示するアクション
     *
     * @return void
     * @author charlesvineyard
     */
    public function searchAction()
    {
        $searchForm = $this->_createSearchForm();
        if (!$searchForm->isValid($this->_getAllParams())) {
            $this->_setParam('searchForm', $searchForm);
            return $this->_forward('index');
        }
        
        $keyword = $searchForm->getValue('query');
        $targets = (array) $searchForm->getValue('targets');
        $refinements = $this->_makeRefinements($searchForm);
        
        $pages = $this->_pageService->searchPages(
            $keyword,
            $this->_getPageNumber(),
            $this->_getPageLimit(),
            $targets,
            $refinements,
            'create_date',
            'desc'
        );
        
        $pages = $this->_adjustPages($pages);

        $this->_helper->viewRenderer('index');
        $this->view->pages = $pages;
        $this->view->searchForm = $searchForm;
        $this->view->categoryForm = $this->_createCategoryForm();
        $this->view->statusForm = $this->_createStatusForm();
        $this->setPagerForView(
            $this->_pageService->countPagesByKeyword(
                $keyword, $targets, $refinements
            )
        );
        $this->view->isSearched = true;
    }
    
    /**
     * 絞り込み条件を作成します。
     * 
     * @param Setuco_Form $searchForm 検索フォーム
     * @return array 絞り込み条件のペア
     * @author charlesvineyard
     */
    private function _makeRefinements($searchForm)
    {
        $refinements = array();
        if ($searchForm->getValue('category_id') !== self::UNSELECTED_VALUE) {
            $refinements['category_id'] =
                ($searchForm->getValue('category_id') === $this->_uncategorizedValue) ?
                null : $searchForm->getValue('category_id');
        }
        if ($searchForm->getValue('account_id') !== self::UNSELECTED_VALUE) {
            $refinements['account_id'] = $searchForm->getValue('account_id');
        }
        if ($searchForm->getValue('status') !== self::UNSELECTED_VALUE) {
            $refinements['status'] = $searchForm->getValue('status');
        }
        return $refinements;
    }
    
    /**
     * ページの内容をビュー用に整形します。
     * 
     * @param array $pages ページ情報の配列
     * @return array ページ情報の配列
     * @author charlesvineyard
     */
    private function _adjustPages($pages)
    {
        foreach ($pages as $key => $page) {
            $createDate = new Zend_Date($page['create_date'], Zend_Date::ISO_8601);
            $pages[$key]['create_date'] = $createDate->toString('YYYY/MM/dd');
            if ($page['category_id'] === null) {
                $pages[$key]['category_id'] = $this->_uncategorizedValue;
            }
        }
        return $pages;
    }

    /**
     * 検索フォームを作成します。
     * 
     * @return Setuco_Form フォーム
     * @author charlesvineyard
     */
    private function _createSearchForm()
    {
        $targetOptions = array(
            'title' => 'タイトル',
            'contents' => '本文',
            'outline' => '概要',
            'tag' => 'タグ',
        );
        $categories = $this->_categoryService->findAllCategoryIdAndNameSet();
        $categories[$this->_uncategorizedValue] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('search'))
             ->addElement(
                 'Text',
                 'query',
                 array(
                     'id' => 'query',
                     'class' => 'defaultInput',
                     'required' => true,
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'MultiCheckbox',
                 'targets',
                 array(
                     'required' => true,
                     'multiOptions' => $targetOptions,
                     'separator' => "</dd>\n<dd>",
                     'value' => array('title', 'contents', 'outline', 'tag'),
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
                     'value' => $this->_uncategorizedValue,    // selected指定
                 )
             )
             ->addElement(
                 'Select',
                 'status',
                 array(
                     'required' => true,
                     'multiOptions' => $this->_addUnselectedOption(
                         Setuco_Data_Constant_Page::allStatus()),
                     'value' => $this->_uncategorizedValue,    // selected指定
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
            'query',
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
        $categories[$this->_uncategorizedValue] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('update-category'))
            ->addElement(
                'Select',    // selected 指定はビューでする
                'category_id',
                array(
                    'id'           => 'category_id',
                    'required'     => true,
                    'onchange'     => 'showPageElementEdit(this);',
                    'multiOptions' => $categories,
                    'decorators'   => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_id_c',
                array(
                    'id'         => 'h_page_id_c',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_title_c',
                array(
                    'id'         => 'h_page_title_c',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Submit',
                'sub_category',
                array(
                    'id'         => 'sub_category',
                    'label'      => '変更',
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'button',
                'cancel_category',
                array(
                    'id'      => 'cancel_category',
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
                    'id'           => 'status',
                    'required'     => true,
                    'onchange'     => 'showPageElementEdit(this);',
                    'multiOptions' => Setuco_Data_Constant_Page::allStatus(),
                    'decorators'   => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_id_s',
                array(
                    'id'         => 'h_page_id_s',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_title_s',
                array(
                    'id'         => 'h_page_title_s',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Submit',
                'sub_status',
                array(
                    'id'         => 'sub_status',
                    'label'      => '変更',
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'button',
                'cancel_status',
                array(
                    'id'         => 'cancel_status',
                    'label'      => 'キャンセル',
                    'onclick'    => 'hidePageElementEdit(this);',
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
        if ($this->_isUpdating()) {
            $this->_formActionWhenUpdate();
            return;
        }
        $this->view->form = $this->_getParam('form', $this->_createForm());
    }
    
    /**
     * 更新フォームを表示するか判断します。
     * 
     * @return 更新なら true
     * @author charlesvineyard
     */
    private function _isUpdating() {
        return $this->_getParam('id') !== null;
    }
    
    /**
     * 新規作成フォームではなく更新フォームを表示する処理
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _formActionWhenUpdate()
    {
        $idValidator = new Zend_Validate_Db_RecordExists(array(
            'table' => 'page',
            'field' => 'id'
        ));
        $id = $this->_getParam('id');
        if (!$idValidator->isValid($id)) {
            throw new UnexpectedValueException('指定されたページがありません。');    // TODO 暫定仕様
        }
        $page = $this->_pageService->findPage($id);
        $tagValue = $this->_createCSTagNames($id);
        $createDate = new Zend_Date($page['create_date'], Zend_Date::ISO_8601);
        $currentPageValues = array(
            'page_title'    => $page['title'],
            'category_id'   => $page['category_id'],
            'page_contents' => $page['contents'],
            'page_outline'  => $page['outline'],
            'tag'           => $tagValue,
            'create_date'   => $createDate->toString(self::FORMAT_DATE_TEXT_BOX),
            'create_time'   => $createDate->toString(self::FORMAT_TIME_TEXT_BOX),
            'hidden_id'     => $id,
        );
        $form = $this->_createUpdateForm();
        $form->setDefaults($currentPageValues);
        $this->view->form = $form;
    }
    
    /**
     * ページ更新用フォームを作成します。
     * 
     * @return Setuco_Form ページ更新用フォーム
     * @author charlesvineyard
     */
    private function _createUpdateForm()
    {
        $form = $this->_createForm();
        $form->setAction($this->_helper->url('update'));
        $form->addElement('Hidden', 'hidden_id');
        $form->setMinimalDecoratorElements('hidden_id');
        return $form;
    }

    /**
     * ページIDからカンマ区切りのタグ名を取得します。
     * 
     * @param int ページID
     * @return string カンマ区切りのタグ名
     * @author charlesvineyard
     */
    private function _createCSTagNames($pageId)
    {
        $tags = $this->_tagService->findTagsByPageId($pageId);
        $tagValue = '';
        foreach ($tags as $tag) {
            $tagValue .= $tag['name'] . ',';
        }
        $tagValue = rtrim($tagValue, ',');
        return $tagValue;
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
        $categories[$this->_uncategorizedValue] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $nowDate = Zend_Date::now();
        $form = new Setuco_Form();
        $form->enableDojo()
             ->setAction($this->_helper->url('create'))
             ->addElement(
                 'Submit',
                 'sub_open1',
                 array(
                     'label'   => '公開して保存',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_draft1',
                 array(
                     'label' => '下書きで保存',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_open2',
                 array(
                     'label'   => '公開して保存',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_draft2',
                 array(
                     'label' => '下書きで保存',
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
                     ),
                     'escape' => true,
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'required' => true,
                     'multiOptions' => $categories,
                     'value' => $this->_uncategorizedValue,    // selected指定
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
                     ),
                     'escape' => true,
                 )
             )
             ->addElement(
                 'Text',
                 'page_outline',
                 array(
                     'id' => 'page_outline',
                     'filters' => array(
                         'StringTrim'
                     ),
                     'escape' => true,
                 )
             )
             ->addElement(
                 'Text',
                 'tag',
                 array(
                     'id' => 'tag',
                     'filters' => array(
                         'StringTrim'
                     ),
                     'escape' => true,
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
            'sub_open1',
            'sub_draft1',
            'sub_open2',
            'sub_draft2',
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
        if ($categoryId === $this->_uncategorizedValue) {
            $categoryId = null;
        }
        $pageId = $this->_pageService->registPage(
            $this->_getParam('page_title'),
            $this->_getParam('page_contents'),
            $this->_getParam('page_outline'),
            Setuco_Util_String::splitCsvString($this->_getParam('tag')),
            $this->_getInputCreateDate(),
            $this->_getInputStatus(),
            $categoryId
        );
        $this->_helper->flashMessenger('新規ページを作成しました。');
        $this->_helper->redirector('form', null, null, array('id' => $pageId));
    }

    /**
     * 入力されたページの状態を取得します。
     *
     * @return ページの状態
     * @author charlesvineyard
     */
    private function _getInputStatus()
    {
        if ($this->_getParam('sub_open') !== null) {
            return Setuco_Data_Constant_Page::STATUS_RELEASE;
        }
        return Setuco_Data_Constant_Page::STATUS_DRAFT;
    }

    /**
     * 入力された作成日時を取得します。
     *
     * @return 作成日時
     * @author charlesvineyard
     */
    private function _getInputCreateDate()
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
     * ページを更新するアクション
     *
     * @return void
     * @author akitsukada charlesvineyard
     */
    public function updateAction()
    {
        $form = $this->_createUpdateForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('form', $form);
            return $this->_forward('form', null, null, array('id', $form->getValue('hidden_id')));
        }
        $categoryId = $this->_getParam('category_id');
        if ($categoryId === $this->_uncategorizedValue) {
            $categoryId = null;
        }
        $updatePageInfo = array(
            'title'       => $form->getValue('page_title'),
            'category_id' => $form->getValue('category_id'),
            'contents'    => $form->getValue('page_contents'),
            'outline'     => $form->getValue('page_outline'),
            'tag'         => Setuco_Util_String::splitCsvString($form->getValue('tag')),
            'create_date' => $this->_getInputCreateDate(),
            'status'      => $this->_getInputStatus(),
        );
        $id = $form->getValue('hidden_id');
        $this->_pageService->updatePage($id, $updatePageInfo);
        $this->_helper->flashMessenger('ページを更新しました。');
        $this->_helper->redirector('form', null, null, array('id' => $id));
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
        $this->_pageService->updatePage(
            $form->getValue('h_page_id_c'),
            array(
                'category_id' => 
                    ($form->getValue('category_id') === $this->_uncategorizedValue) ? 
                    null : $form->getValue('category_id')
            )
        );
        $this->_helper->flashMessenger('「' . $form->getValue('h_page_title_c') . '」のカテゴリーを変更しました。');
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
        $this->_pageService->updatePage(
            $form->getValue('h_page_id_s'),
            array(
                'status' => $form->getValue('status')
            )
        );
        $this->_helper->flashMessenger('「' . $form->getValue('h_page_title_s') . '」の状態を変更しました。');
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
        $page = $this->_pageService->findPage($id);
        $this->_pageService->deletePage($id);
        $this->_helper->flashMessenger('「' . $page['title'] . '」を削除しました。');
        $this->_helper->redirector('index');
    }

}

