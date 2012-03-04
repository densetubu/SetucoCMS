<?php
/**
 * ページに関するサービス
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
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * ページ管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     charlesvineyard
 */
class Admin_Model_Page extends Common_Model_PageAbstract
{
    /**
     * ページタグDAO
     *
     * @var Common_Model_DbTable_PageTag
     */
    private $_pageTagDao;

    /**
     * アカウントDAO
     *
     * @var Common_Model_DbTable_Account
     */
    private $_accountDao;

    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_pageDao = new Common_Model_DbTable_Page();
        $this->_tagDao = new Common_Model_DbTable_Tag();
        $this->_pageTagDao = new Common_Model_DbTable_PageTag();
        $this->_accountDao = new Common_Model_DbTable_Account();
    }


    /**
     * ページ情報を取得する
     * アカウント情報も取得します。
     *
     * @param string $sortColmn  並べ替えをするカラムのカラム名
     * @param string $order       asc か　desc
     * @param int    $pageNumber  ページ番号(オフセットカウント)
     * @param int    $limit       一つのページに出力する数(オフセット)
     * @return array ページ情報の一覧
     * @author charlesvineyard
     */
    public function findPages($sortColmn, $order, $pageNumber, $limit)
    {
        return $this->_pageDao->loadPages4Pager(
            $sortColmn, $order, $pageNumber, $limit, true);
    }

    /**
     * 最近作成されたページを取得します。
     *
     * 取得順序は作成日時の降順です。
     *
     * @param  int $limit 取得ページ数
     * @return array ページ情報の配列
     * @author charlesvineyard
     */
    public function findLastCreatedPages($limit)
    {
        return $this->_pageDao->loadLastCreatedPages($limit, true, true);
    }

    /**
     * 今月作成(公開)したページ数を取得する
     *
     * @return int 今月作成(公開)したページ数
     * @author charlesvineyard
     */
    public function countPagesCreatedThisMonth()
    {
        $date = new Zend_Date();
        $createYear = $date->get(Zend_Date::YEAR);
        $createMonth = $date->get(Zend_Date::MONTH_SHORT);

        $startDate = new Zend_Date("{$createYear}-{$createMonth}", 'Y-M');
        $endDate = new Zend_Date($createYear . '-' . ($createMonth + 1), 'Y-M');

        return $this->_pageDao->countPagesByStatusAndCreateDateSpan(
            Setuco_Data_Constant_Page::STATUS_RELEASE, $startDate, $endDate
        );
    }

    /**
     * ページを登録します。
     *
     * @param string    $title       ページタイトル
     * @param string    $contents    ページコンテンツ
     * @param string    $outline     ページの概要
     * @param array     $tags        タグ名の配列
     * @param Zend_Date $create_date 作成日時
     * @param int       $status      公開状態
     * @param int       $categoryId カテゴリーID
     * @param int       $accountId アカウントのID
     * @return int 登録したページのID
     * @author charlesvineyard suzuki-mar
     */
    public function registPage($title, $contents, $outline, $tags,
            $createDate, $status, $categoryId, $accountId)
    {
        $page = array(
            'title'       => $title,
            'contents'    => $contents,
            'outline'     => $outline,
            'create_date' => $createDate,
            'account_id'  => $accountId,
            'status'      => $status,
            'category_id' => $categoryId,
            'update_date' => $createDate,
        );
        $pageId = $this->_pageDao->insert($page);
        $tagIds = $this->_registTagsIfNotExist($tags);
        foreach ($tagIds as $tagId) {
            $this->_pageTagDao->insert(array(
                'page_id' => $pageId,
                'tag_id'  => $tagId
            ));
        }
        return $pageId;
    }

    /**
     * タグがもしなければ登録します。
     * 登録後または既に存在するタグIDの配列を返します。
     *
     * @param  array $tagNames タグ名の配列
     * @return array 指定したタグ名のタグIDの配列
     * @author charlesvineyard
     */
    private function _registTagsIfNotExist($tagNames)
    {
        if (empty($tagNames)) {
            return array();
        }

        $tagIds = array();
        foreach ($tagNames as $tag) {
            $tagId = $this->_tagDao->loadTagIdByTagName($tag);
            if ($tagId === null) {
                $insertedTagId = $this->_tagDao->insert(array('name' => $tag));
                $tagIds[] = $insertedTagId;
            } else {
                $tagIds[] = $tagId;
            }
        }
        return $tagIds;
    }

    /**
     * ページを更新する
     *
     * @param int   $id         ページID
     * @param array $updateData キー:カラム名、値:更新する値の配列
     * @return bool 更新できたら true。更新するデータがなければ false。
     * @author charlesvineyard
     */
    public function updatePage($id, $updateData)
    {
        // ページとタグの関連を一旦全て削除
        $this->_pageTagDao->delete($this->_pageTagDao->getAdapter()->quoteInto('page_id = ?', $id));

        if (!empty($updateData['tag'])) {
            $tagIds = $this->_registTagsIfNotExist($updateData['tag']);
            foreach ($tagIds as $tagId) {
                $this->_pageTagDao->insert(array(
                    'page_id' => $id,
                    'tag_id'  => $tagId
                ));
            }
        }
        unset($updateData['tag']);

        $updateData['update_date'] = new Zend_Date();

        return $this->_pageDao->updateByPrimary($updateData, $id);
    }

    /**
     * ページを削除する
     *
     * @param int $id ページID
     * @return void
     * @author charlesvineyard
     */
    public function deletePage($id)
    {
        return $this->_pageDao->deleteByPrimary($id);
    }
}
