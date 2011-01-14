<?php

/**
 * defaultモジュールの共通のコントローラーです
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco
 * @subpackage  Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_DefaultAbstract extends Setuco_Controller_Action_Abstract
{

    /**
     * ページのタイトル
     *
     * @var String
     */
    protected $_pageTitle = null;

    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか　削除するので使用しない
     * @todo 定数の削除 検討事項のチケット完了時に削除する
     */
    const PAGE_LIMIT = 10;


    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか
     * @var int
     * @todo PAGE_LIMITの削除
     */
    protected $_pageLimit = 10;

    /**
     * defaultモジュールコントローラの初期処理です。
     *
     * @return void
     * @author suzuki-mar
     */
    public function init()
    {
        parent::init();

    }


    /**
     * defaultモジュール共通でviewに変数を渡す処理をします。
     *
     * @return void
     * @author suzuki-mar
     */
    public function postDispatch()
    {
        //tagテーブルのモデルクラスのインスタンス生成
        $modelTag = new Default_Model_Tag();
        //タグクラウドをviewにセットする
        $this->view->tagClouds = $modelTag->getTagClouds();
        $this->view->categoryLists = $this->_getCategoryList();

        //siteテーブルのモデルクラスのインスタンス生成
        $modelSite = new Default_Model_Site();
        //サイト情報をviewにセットする
        $this->view->siteInfos = $modelSite->getSiteInfo();

        //ページタイトルがセットされていないときは、ページタイトルはデフォルトのページタイトル
        if (!is_null($this->_pageTitle)) {
            $this->view->pageTitle = $this->_pageTitle;
        }
    }

    /**
     * カテゴリー一覧を取得する
     *
     * @return array カテゴリー一覧　取得できなかった場合はfalseを返す
     * @author suzuki-mar
     */
    private function _getCategoryList()
    {
        //categoryテーブルのモデルクラスのインスタンス生成
        $modelCategory = new Default_Model_Category();

        $categories = $modelCategory->findCategoryList();


        //すでに登録されていたら、未分類のカテゴリーを追加する
        if (is_array($categories)) {
            $result = $this->_addDefaultCategory($categories);
        } else {
            $modelPage = new Default_Model_Page();
            $uncategorizedInfo = Setuco_Data_Constant_Category::UNCATEGORIZED_INFO();
            //ひとつでも記事が登録されていたら、リンクする
            $uncategorizedInfo['is_used'] = $modelPage->isEntryExists();
            $categories[] = $uncategorizedInfo;
            $result = $categories;
        }

        return $result;
    }

    /**
     * 未分類のカテゴリーを追加したカテゴリーを取得する
     *
     * @param array[option] $subjects 元となる配列
     * @return array 未分類のカテゴリーを追加したもの 未分類のカテゴリーはis_defaultの要素がある
     * @author suzuki-mar
     */
    private function _addDefaultCategory($categories)
    {
        $default = Setuco_Data_Constant_Category::UNCATEGORIZED_INFO();

        //カテゴリーが登録されていて配列の場合は、配列に未分類のカテゴリーを追加する
        //カテゴリーが新規作成されていない場合もリンクする
        foreach ($categories as $value) {
            //ひとつでも使用されていなかったら、設定する
            if (!($value['is_used'])) {
                $isLink = true;
                break;
            }
        }

        $default['is_used'] = (isset($isLink));

        //未分類のカテゴリーを追加する
        $categories[] = $default;
        return $categories;
    }

}
