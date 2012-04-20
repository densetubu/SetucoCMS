<?php
/**
 * 管理側のページを管理するコントローラー。
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      akitsukada
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      akitsukada
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
     * 標準の並べ替え項目
     *
     * @var string
     */
    const DEFAULT_SORT_COLUMN = 'create_date';

    /**
     * 標準の並べ替え順序
     *
     * @var string
     */
    const DEFAULT_ORDER = 'desc';


    /**
     * 標準の検索タイプ ANDなのかORなのか
     *
     * @var string
     */
    const DEFAULT_SEARCH_TYPE = 'AND';

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
     * パラメーターにidがあればページを編集する
     * パラメーターにqueryがあればページを検索する
     *
     * @return void
     * @author akitsukada charlesvineyard
     */
    public function indexAction()
    {
        if ($this->_hasParam('id')) {
            return $this->_editFormOperation();
        }

        if ($this->_hasParam('query')) {
            return $this->_searchOperation();
        }

        $sortColumn = $this->_getParam('sort', self::DEFAULT_SORT_COLUMN);
        $sortValidator = new Zend_Validate_InArray(array('title', 'create_date'));
        if (!$sortValidator->isValid($sortColumn)) {
            $sortColumn = self::DEFAULT_SORT_COLUMN;
        }
        $order = $this->_getParam('order', self::DEFAULT_ORDER);
        $orderValidator = new Zend_Validate_InArray(array('asc', 'desc'));
        if (!$orderValidator->isValid($order)) {
            $order = self::DEFAULT_ORDER;
        }

        $pages = $this->_pageService->findPages($sortColumn, $order, $this->_getPageNumber(), $this->_getPageLimit());
        $pages = self::adjustPages($pages);

        $this->view->pages = $pages;
        $this->view->searchForm = $this->_getParam('searchForm', $this->_createSearchForm());
        $this->view->categoryForm = $this->_createCategoryForm();
        $this->view->statusForm = new Setuco_Form_Page_StatusUpdate();
        $this->setPagerForView($this->_pageService->countAllPages());
        $this->_showFlashMessages();
    }

    /**
     * ページ編集フォームの処理
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _editFormOperation()
    {
        $idValidator = new Zend_Validate_Db_RecordExists(array(
            'table' => 'page',
            'field' => 'id'
        ));
        $id = $this->_getParam('id');
        if (!$idValidator->isValid($id)) {
            throw new Setuco_Controller_Exception('ページがありません。', 404);
        }

        $page = $this->_pageService->findPage($id);
        $createDate = new Zend_Date($page['create_date'], Zend_Date::ISO_8601);
        $currentPageValues = array(
            'page_title'    => $page['title'],
            'category_id'   => Setuco_Data_Converter_CategoryInfo::convertCategoryId4View($page['category_id']),
            'page_contents' => $page['contents'],
            'page_outline'  => $page['outline'],
            'tag'           => $this->_createCSTagNames($id),
            'create_date'   => $createDate->toString(self::FORMAT_DATE_TEXT_BOX),
            'create_time'   => $createDate->toString(self::FORMAT_TIME_TEXT_BOX),
            'hidden_id'     => $id,
        );

        $form = $this->_getParam('form', $this->_createUpdateForm()->setDefaults($currentPageValues));

        $this->_pageTitle = "「{$page['title']}」の編集";
        $this->view->pageTitle = "「{$page['title']}」の編集";
        $this->_helper->viewRenderer('form');
        $this->view->form = $form;
        $this->_showFlashMessages();
    }

    /**
     * ページを検索して一覧表示する処理
     *
     * @return void
     * @author charlesvineyard suzuki-mar
     */
    protected function _searchOperation()
    {
        $searchForm = $this->_createSearchForm();
        
        if (!$this->_isValidSearchForm($searchForm, $this->_getAllParams())) {
            $this->_setParam('searchForm', $searchForm);
        }

        $pageParamIns = $this->_createPageParamInstance($searchForm);
        
        //検索条件を指定していない
        if (!$pageParamIns->isSettingSearchCondition()) {
            
        }

        $pages = $this->_pageService->searchPages($pageParamIns);

        $this->view->params = $this->_makeQueryString((array) $searchForm->getValue('targets'));

        $pages = self::adjustPages($pages);

        $this->_helper->viewRenderer('index');
        $this->view->pages = $pages;
        $this->view->searchForm = $searchForm;
        $this->view->categoryForm = $this->_createCategoryForm();
        $this->view->statusForm = new Setuco_Form_Page_StatusUpdate();
        $this->setPagerForView(
            $this->_pageService->countPagesByKeyword($pageParamIns)
        );

        $this->_pageTitle = "ページの編集・削除";

    }

    /**
     * Page_Paramインスタンスを生成します
     *
     * @param Setuco_Form $form 入力した値が入っているフォームクラス
     * @return Common_Model_Page_Param パラメーターを設定した物
     * @author suzuki-mar
     */
    private function _createPageParamInstance(Setuco_Form $form)
    {
        $keyword = $form->getValue('query');
        $sortColumn = $this->_getParam('sort', self::DEFAULT_SORT_COLUMN);
        $sortOrder = $this->_getParam('order', self::DEFAULT_ORDER);
        //$searchType = $this->_getParam('search_type', self::DEFAULT_SEARCH_TYPE);

        //検索パラメーターの引数オブジェクトを生成する
        $keyword = $form->getValue('query');
        $targets = (array) $form->getValue('targets');
        $refinements = $this->_makeRefinements($form);
        $tagIds = $this->_tagService->findTagIdsByKeyword($keyword);

        $pageParamIns = new Common_Model_Page_Param(
            $keyword,
            $tagIds,
            $this->_getPageNumber(),
            $this->_getPageLimit(),
            $targets,
            $refinements,
            $sortColumn,
            $sortOrder/*,
            $searchType*/
        );

        return $pageParamIns;
    }

    /**
     * 検索後のソートボタンのリンクURLに使うため、検索された条件のURLパラメータを取得します。
     *
     * @param array   $targets _searchOperationメソッドで取得したパラメータ'targets'の配列
     * @return string /query/~~(/targets/~~){3}/category_id/~~/account_id/~~/status/~~
     * @author akitsukada
     */
    private function _makeQueryString($targets)
    {
        $params = $this->getRequest()->getParams();
        $paramsInfo = '/query/' . $params['query'];
        foreach ($targets as $column) {
            $paramsInfo .= '/targets/' . $column;
        }
        $paramsInfo .= '/category_id/' . $params['category_id'];
        $paramsInfo .= '/account_id/' . $params['account_id'];
        $paramsInfo .= '/status/' . $params['status'];
        return $paramsInfo;
    }

    /**
     * ページ検索フォームが有効かどうかを判断します。
     * フォームには検証する値やもしあればエラー情報が格納されます。
     *
     * @param  Setuco_Form $form    フォーム
     * @param  array       $values  検証する値
     * @return bool        有効なら true
     * @author charlesvineyard
     */
    private function _isValidSearchForm($form, $values)
    {
        // 既設のバリデーターでチェック
        $form->isValid($values);

        // キーワードが入力されたときだけ検索対象をチェックする
        if (!empty($values['query'])) {
            $targets = $form->getElement('targets');
            $targets->setRequired(true);
            $targets->setValidators($this->_makeSearchTargetsValidators());
            if (!$targets->isValid(isset($values['targets']) ? $values['targets'] : null)) {
                $form->markAsError();
            }
        }
        // キーワードが入力されないときは絞り込みを指定してるかチェックする
        else {
            if (!$this->_isSelectedRefinements($values)) {
                $form->addError('キーワードを入力するか、カテゴリー・制作者・公開状態のどれかを指定してください。');
            }
        }

        return !$form->isErrors();
    }

    /**
     * 絞り込みが選択されたかどうか判断します。
     *
     * @param  array $values    入力パラメータ
     * @return bool  選択されたら true
     * @author charlesvineyard
     */
    private function _isSelectedRefinements($values) {
        if ($values['category_id'] != null
            && $values['category_id'] !== self::UNSELECTED_VALUE
        ) {
            return true;
        }
        if ($values['account_id'] != null
            && $values['account_id'] !== self::UNSELECTED_VALUE
        ) {
            return true;
        }
        if ($values['status'] != null
            && $values['status'] !== self::UNSELECTED_VALUE
        ) {
            return true;
        }
        return false;
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
                Setuco_Data_Converter_CategoryInfo::convertCategoryId4Data($searchForm->getValue('category_id'));
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
    public static function adjustPages($pages)
    {
        foreach ($pages as $key => $page) {
            $createDate = new Zend_Date($page['create_date'], Zend_Date::ISO_8601);
            $pages[$key]['create_date'] = $createDate->toString('YYYY/MM/dd');
            $pages[$key]['category_id'] = Setuco_Data_Converter_CategoryInfo::convertCategoryId4View($pages[$key]['category_id']);
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
        $categories[Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;

        $form = new Setuco_Form();
        $form->setAction($this->_helper->url('index'))
             ->addElement(
                 'Text',
                 'query',
                 array(
                     'id' => 'query',
                     'class' => 'defaultInput',
                     'filters' => array(
                         'RestParamDecode',
                         'FullWidthStringTrim',
                     ),
                     'validators' => $this->_makeSearchQueryValidators()
                 )
             )
             ->addElement(
                 'MultiCheckbox',
                 'targets',
                 array(
                     'multiOptions' => $targetOptions,
                     'separator' => "</dd>\n<dd>",
                     'value' => array(
                         'title',
                         'contents',
                         'outline',
                         'tag'
                     ),    // selected指定
                     'filters' => array(
                         'RestParamDecode',
                     ),
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'multiOptions' => $this->_addUnselectedOption($categories),
                     'value' => self::UNSELECTED_VALUE,    // selected指定
                     'filters' => array(
                         'RestParamDecode',
                     ),
                 )
             )
             ->addElement(
                 'Select',
                 'account_id',
                 array(
                     'multiOptions' => $this->_addUnselectedOption(
                         $this->_accountService->findAllAccountIdAndNicknameSet()),
                     'value' => Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE,    // selected指定
                     'filters' => array(
                         'RestParamDecode',
                     ),
                 )
             )
             ->addElement(
                 'Select',
                 'status',
                 array(
                     'multiOptions' => $this->_addUnselectedOption(
                         Setuco_Data_Constant_Page::ALL_STATUSES()),
                     'value' => Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE,    // selected指定
                     'filters' => array(
                         'RestParamDecode',
                     ),
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
     * ページ検索キーワード用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeSearchQueryValidators()
    {
        $name = 'キーワード';
        $validators = array();

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 50
            )
        );
        $stringLength->setMessage($name . 'は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }


    /**
     * 検索対象用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeSearchTargetsValidators()
    {
        $name = '検索対象';
        $validators = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage($name . 'を選択してください。');
        $validators[] = array($notEmpty, true);

        return $validators;
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
        $categories[Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form_Page_CategoryUpdate();
        $form->setCategories($categories);
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
        $this->view->form = $this->_getParam('form', $this->_createForm());

        //フラッシュメッセージを設定する
        $this->_showFlashMessages();
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
        $categories[Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $nowDate = Zend_Date::now();
        $form = new Setuco_Form();
        $form->enableDojo()
             ->setAction($this->_helper->url('create'))
             ->addElementPrefixPath('Setuco_Filter', 'Setuco/Filter/', 'filter');
        $form->addElement(
            'Submit',
            'sub_open1',
            array(
                'label'   => '公開して保存',
            )
        );
        $form->addElement(
            'Submit',
            'sub_draft1',
            array(
                'label' => '下書きで保存',
            )
        );
        $form->addElement(
            'Submit',
            'sub_open2',
            array(
                'label'   => '公開して保存',
            )
        );
        $form->addElement(
            'Submit',
            'sub_draft2',
            array(
                'label' => '下書きで保存',
            )
        );
        $form->addElement(
            'Text',
            'page_title',
            array(
                'id' => 'page_title',
                'required' => true,
                'filters' => array(
                    'StringTrim'
                ),
                'validators' => $this->_makePageTitleValidators(),
            )
        );
        $form->addElement(
            'Select',
            'category_id',
            array(
                'required' => true,
                'multiOptions' => $categories,
                'value' => Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE,    // selected指定
            )
        );
        $form->addElement(
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
                    'StringTrim',
                    'SplitFirstBrTag'
                ),
                'validators' => $this->_makePageContentsValidators(),
            )
        );
        $form->addElement(
            'Text',
            'page_outline',
            array(
                'id' => 'page_outline',
                'filters' => array(
                    'StringTrim'
                ),
                'validators' => $this->_makePageOutlineValidators(),
            )
        );
        $form->addElement(
            'Text',
            'tag',
            array(
                'id' => 'tag',
                'filters' => array(
                    'StringTrim'
                ),
            )
        );
        $form->addElement(
            'DateTextBox',
            'create_date',
            array(
                'id' => 'create_date',
                'value' => $nowDate->toString(self::FORMAT_DATE_TEXT_BOX),
                'filters' => array(
                    'StringTrim'
                ),
                'validators' => $this->_makeCreateDateValidators(),
            )
        );
        $form->addElement(
            'TimeTextBox',
            'create_time',
            array(
                'id' => 'create_time',
                'value' => $nowDate->toString(self::FORMAT_TIME_TEXT_BOX),
                'TimePattern' => 'HH:mm:ss',    // 表示用フォーマット
                'filters' => array(
                    'StringTrim'
                ),
                'validators' => $this->_makeCreateTimeValidators(),
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
     * ページタイトル用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makePageTitleValidators()
    {
        $name = 'ページタイトル';
        $validators = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage($name . 'を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 100
            )
        );
        $stringLength->setMessage($name . 'は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }

    /**
     * コンテンツ用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makePageContentsValidators()
    {
        $name = 'コンテンツ';
        $validators = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage($name . 'を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 1000000
            )
        );
        $stringLength->setMessage($name . 'は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }

    /**
     * ページの概要用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makePageOutlineValidators()
    {
        $name = 'ページの概要';
        $validators = array();

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 300
            )
        );
        $stringLength->setMessage($name . 'は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }

    /**
     * タグ用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeTagValidators()
    {
        $name = 'タグ';
        $validators = array();

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 50
            )
        );
        $stringLength->setMessage($name . 'は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }

    /**
     * 作成日用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeCreateDateValidators()
    {
        $name = '作成日';
        $validators = array();

        $date = new Zend_Validate_Date(
            array(
                'format' => self::FORMAT_DATE_TEXT_BOX
            )
        );
        $date->setMessage($name . 'の形式が正しくありません。');
        $validators[] = array($date, true);

        return $validators;
    }

    /**
     * 作成時刻用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeCreateTimeValidators()
    {
        $name = '作成時刻';
        $validators = array();

        $date = new Zend_Validate_Date(
            array(
                'format' => self::FORMAT_TIME_TEXT_BOX
            )
        );
        $date->setMessage($name . 'の形式が正しくありません。');
        $validators[] = array($date, true);

        return $validators;
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
        $post = $_POST;

        $nowDate = Zend_Date::now();
        $post['create_date'] = Setuco_Util_String::getDefaultIfEmpty(
            $this->_getParam('create_date'), $nowDate->toString(self::FORMAT_DATE_TEXT_BOX));
        $post['create_time'] = Setuco_Util_String::getDefaultIfEmpty(
            $this->_getParam('create_time'), $nowDate->toString(self::FORMAT_TIME_TEXT_BOX));

        if (! $this->_isValidPageForm($form, $post)) {
            $this->_setParam('form', $form);
            return $this->_forward('form');
        }

        $createdPageId = $this->_pageService->registPage(
            $form->getValue('page_title'),
            $form->getValue('page_contents'),
            $form->getValue('page_outline'),
            array_unique($this->_splitTagValue($form->getValue('tag'))),
            new Zend_Date(
                $post['create_date'] . $post['create_time'],
                self::FORMAT_DATE_TEXT_BOX . self::FORMAT_TIME_TEXT_BOX
            ),
            $this->_getInputStatus(),
            Setuco_Data_Converter_CategoryInfo::convertCategoryId4Data($form->getValue('category_id')),
            $this->_getAccountInfos('id')
        );

        $this->_helper->flashMessenger('新規ページを作成しました。');
        $this->_helper->redirector('index', null, null, array('id' => $createdPageId));
    }

    /**
     * ページ作成フォームが有効かどうかを判断します。
     * フォームには検証する値やもしあればエラー情報が格納されます。
     *
     * @param  Setuco_Form $form    フォーム
     * @param  array       $values  検証する値
     * @return bool        有効なら true
     * @author charlesvineyard
     */
    private function _isValidPageForm($form, $values)
    {
        // 既設のバリデーターでチェック
        $form->isValid($values);

        // 作成日時の未来日付チェック
        $createDateTime = new Zend_Date($values['create_date'] . $values['create_time'],
            self::FORMAT_DATE_TEXT_BOX . self::FORMAT_TIME_TEXT_BOX);
        if ($createDateTime->compare(Zend_Date::now()) > 0) {
            $form->getElement('create_date')->addError('未来の日時は指定できません。');
            $form->markAsError();
        }

        // タグ名を1つずつチェック
        $tags = $this->_splitTagValue($values['tag']);
        $form->getElement('tag')->setValidators($this->_makeTagValidators());
        if ($tags != null) {
            foreach ($tags as $tag) {
                if (!$form->getElement('tag')->isValid($tag)) {
                    $form->markAsError();
                    break;
                }
            }
        }
        $form->getElement('tag')->setValue($values['tag']);

        return !$form->isErrors();
    }

    /**
     * 入力されたページの状態を取得します。
     *
     * @return ページの状態
     * @author charlesvineyard
     */
    private function _getInputStatus()
    {
        if ($this->_getParam('sub_open1') !== null) {
            return Setuco_Data_Constant_Page::STATUS_RELEASE;
        }
        if ($this->_getParam('sub_open2') !== null) {
            return Setuco_Data_Constant_Page::STATUS_RELEASE;
        }
        return Setuco_Data_Constant_Page::STATUS_DRAFT;
    }

    /**
     * 作成したページを公開前にプレビューするアクション
     *
     * @return void
     * @author akitsukada
     * @todo 実装
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
        $post = $_POST;

        $nowDate = Zend_Date::now();
        $post['create_date'] = Setuco_Util_String::getDefaultIfEmpty(
            $this->_getParam('create_date'), $nowDate->toString(self::FORMAT_DATE_TEXT_BOX));
        $post['create_time'] = Setuco_Util_String::getDefaultIfEmpty(
            $this->_getParam('create_time'), $nowDate->toString(self::FORMAT_TIME_TEXT_BOX));

        if (! $this->_isValidPageForm($form, $post)) {
            $this->_setParam('form', $form);
            return $this->_forward('index', null, null, array('id' => $form->getValue('hidden_id')));
        }

        $updatePageInfo = array(
            'title'       => $form->getValue('page_title'),
            'category_id' => Setuco_Data_Converter_CategoryInfo::convertCategoryId4Data($form->getValue('category_id')),
            'contents'    => $form->getValue('page_contents'),
            'outline'     => $form->getValue('page_outline'),
            'tag'         => array_unique($this->_splitTagValue($form->getValue('tag'))),
            'create_date' => new Zend_Date(
                $post['create_date'] . $post['create_time'],
                self::FORMAT_DATE_TEXT_BOX . self::FORMAT_TIME_TEXT_BOX
            ),
            'status'      => $this->_getInputStatus(),
        );
        $id = $form->getValue('hidden_id');
        $this->_pageService->updatePage($id, $updatePageInfo);

        $this->_helper->flashMessenger('ページを更新しました。');
        $this->_helper->redirector('index', null, null, array('id' => $id));
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
            return $this->_forward('index');
        }
        $this->_pageService->updatePage(
            $form->getValue('h_page_id_c'),
            array(
                'category_id' => Setuco_Data_Converter_CategoryInfo::convertCategoryId4Data($form->getValue('category_id'))
            )
        );
        $this->_helper->flashMessenger('「' . $form->getValue('h_page_title_c') . '」のカテゴリーを変更しました。');
        $this->_helper->redirector('index');
    }

    /**
     * ページの状態を更新するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateStatusAction()
    {
        $form = new Setuco_Form_Page_StatusUpdate();
        if (!$form->isValid($_POST)) {
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

    /**
     * 入力されたタグ文字列を分割し、配列に変換します。
     *
     * 文字列の前後の空白は削除されます。
     * 分割後の文字列が空文字の場合は戻り値に含まれません。
     * 例) _splitTagValue('a, b ,,c,')
     *    → [0] => 'a'
     *      [1] => 'b'
     *      [2] => 'c'
     *
     * @param string $tagValue カンマ区切りの文字列
     * @return array 分割された文字列
     * @author charlesvineyard
     */
    private function _splitTagValue($tagValue)
    {
        $strings = explode(',', $tagValue);
        $result = array();
        foreach ($strings as $string) {
            $trimed = trim($string);
            if ($trimed !== '') {
                $result[] = $trimed;
            }
        }
        return $result;
    }
}

