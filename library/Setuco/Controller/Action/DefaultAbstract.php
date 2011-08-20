<?php

/**
 * defaultモジュールの共通のコントローラーです
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
 * @package    Setuco
 * @subpackage Controller_Action
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

        $modelCategory = new Default_Model_Category();
        $categories = $modelCategory->findCategoryList();

        $uncategorizedCategoryInfos = Setuco_Data_Constant_Category::UNCATEGORIZED_INFO();

        $modelPage = new Default_Model_Page();
        $uncategorizedCategoryInfos['is_used'] = $modelPage->isEntryUncategorizedPage();

        //カテゴリーに登録していない場合は未分離のカテゴリーのみ表示する
        if ($categories !== false) {
            array_push($categories, $uncategorizedCategoryInfos);
        } else {
            $categories = array($uncategorizedCategoryInfos);
        }

        return $categories;
    }
}
