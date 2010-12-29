<?php
/**
 * 管理側のサイト構造一覧のコントローラ
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
 * @author     charlesvineyard
 */

require_once APPLICATION_PATH . '/modules/admin/controllers/PageController.php';

/**
 * サイト構造一覧のコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */
class Admin_DirectoryController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * サイト構造サービス
     *
     * @var Admin_Model_Directory
     */
    private $_directoryService;

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
        $this->_directoryService = new Admin_Model_Directory();
        $this->_pageService = new Admin_Model_Page();
        $this->_categoryService = new Admin_Model_Category();
    }

    /**
     * サイト構造(ディレクトリー)の一覧を表示するアクションです。
     *
     * @return void
     * @author charlesvineyard
     */
    public function indexAction()
    {
        if ($this->_getParam('category_id') !== null) {
            return $this->_showCategoryPagesOperation();
        }
        $this->view->directory = $this->_directoryService->createDirectoryInfo();
    }

    /**
     * カテゴリーに含まれるページ一覧を表示する処理
     *
     * @throws Zend_Uri_Exception
     * @author charlesvineyard
     */
    protected function _showCategoryPagesOperation()
    {
        $categoryId = $this->_getParam('category_id');
        $categoryIdValidator = new Zend_Validate_Db_RecordExists(array(
            'table' => 'category',
            'field' => 'id'
        ));
        if ($categoryId !== Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE) {
            if (!$categoryIdValidator->isValid($categoryId)) {
                throw new UnexpectedValueException('指定されたページがありません。');    // TODO 暫定仕様
            }
        }

        // TODO 検索のメソッド変える
        $pages = $this->_pageService->searchPages(
            null,
            $this->_getPageNumber(),
            $this->_getPageLimit(),
            array(),
            array('category_id' =>
                ($categoryId === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE)
                ? null : $categoryId
            ),
            'title',
            'asc'
        );
        $pages = Admin_PageController::adjustPages($pages);

        $pageCount = $this->_pageService->countPagesByKeyword(
                null, array(), array('category_id' =>
                ($categoryId === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE)
                ? null : $categoryId));

        $categoryName = ($categoryId === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE)
            ? Setuco_Data_Constant_Category::UNCATEGORIZED_STRING
            : $this->_categoryService->findNameById($categoryId);

        $this->_helper->viewRenderer('category-page');
        $this->view->pages = $pages;
        $this->view->categoryForm = $this->_createCategoryForm($categoryId);
        $this->view->statusForm = $this->_createStatusForm($categoryId);
        $this->setPagerForView($pageCount);
        $this->view->isSearched = true;
        $this->view->pageCount = $pageCount;
        $this->view->categoryName = $categoryName;
        $this->_pageTitle = "「{$categoryName}」カテゴリーに含まれるページ一覧";
        $this->_showFlashMessages();
    }

    /**
     * カテゴリー変更フォームを作成します。
     *
     * @param  string カテゴリー変更前のID(表示用)
     * @return Setuco_Form_Page_CategoryUpdate フォーム
     * @author charlesvineyard
     */
    private function _createCategoryForm($preCategoryId)
    {
        $categories = $this->_categoryService->findAllCategoryIdAndNameSet();
        $categories[Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        $form = new Setuco_Form_Page_CategoryUpdate();
        $form->setCategories($categories);
        $form->getElement('h_pre_category_id_c')->setValue($preCategoryId);
        return $form;
    }

    /**
     * 状態変更フォームを作成します。
     *
     * @param  string カテゴリー変更前のID(表示用)
     * @return Setuco_Form_Page_StatusUpdate フォーム
     * @author charlesvineyard
     */
    private function _createStatusForm($preCategoryId)
    {
        $form = new Setuco_Form_Page_StatusUpdate();
        $form->getElement('h_pre_category_id_s')->setValue($preCategoryId);
        return $form;
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
        d('updateCategory');
        $form = new Setuco_Form_Page_CategoryUpdate();
        if (!$form->isValid($_POST)) {
            return $this->_forward('index', null, null, array(
                'category_id' => $this->_getParam('h_pre_category_id_c', null)
            ));
        }
        $this->_pageService->updatePage(
            $form->getValue('h_page_id_c'),
            array(
                'category_id' =>
                    ($form->getValue('category_id') === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE) ?
                    null : $form->getValue('category_id')
            )
        );

        $this->_helper->flashMessenger('「' . $form->getValue('h_page_title_c') . '」のカテゴリーを変更しました。');
        $this->_helper->redirector('index', null, null, array(
            'category_id' => $form->getValue('h_pre_category_id_c')
        ));
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
        $form = $this->_createStatusForm();
        if (!$form->isValid($_POST)) {
            return $this->_forward('index', null, null, array(
                'category_id' => $this->_getParam('h_pre_category_id_s', null)
            ));
        }

        $this->_pageService->updatePage(
            $form->getValue('h_page_id_s'),
            array(
                'status' => $form->getValue('status')
            )
        );

        $this->_helper->flashMessenger('「' . $form->getValue('h_page_title_s') . '」の状態を変更しました。');
        $this->_helper->redirector('index', null, null, array(
            'category_id' => $form->getValue('h_pre_category_id_s')
        ));
    }
}
