<?php
/**
 * ページに関するサービス
 *
 * LICENSE: ライセンスに関する情報
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
     * @param string  $sortColmn  並べ替えをするカラムのカラム名
     * @param string $order       asc か　desc
     * @param int    $pageNumber  ページ番号(オフセットカウント)
     * @param int    $limit       一つのページに出力する数(オフセット)
     * @return array ページ情報の一覧
     * @author charlesvineyard
     */
    public function findPages($sortColmn, $order, $pageNumber, $limit)
    {
        return $this->_pageDao->findSortedPages(
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
        return $this->_pageDao->findLastCreatedPages($limit, true, true);

    }

    /**
     * ページを数えます。
     *
     * @param int $status ページの状態（Setuco_Data_Constant_Page::STATUS_*）
     *                     指定しなければ全ての状態のものを数えます。
     * @param int $createYear  YYYY形式の年 指定すればその年に作成したものを数えます
     * @param int $createMonth MM形式の月 $createYearと一緒に指定すればその月に作成したものを数えます
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPages($status = null, $createYear = null, $createMonth = null)
    {
        $startDate = null;
        $endDate = null;
        if ($createYear != null) {
            if ($createMonth != null) {
                $startDate = new Zend_Date("{$createYear}-{$createMonth}", 'YYYY-M');
                $endDate = new Zend_Date($createYear . '-' . ($createMonth + 1), 'YYYY-M');
            } else {
                $startDate = new Zend_Date($createYear, 'YYYY');
                $endDate = new Zend_Date($createYear + 1, 'YYYY');
            }
        }
        return $this->_pageDao->countPages($status, $startDate, $endDate);
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
        return $this->countPages(
                Setuco_Data_Constant_Page::STATUS_RELEASE,
                $date->get(Zend_Date::YEAR),
                $date->get(Zend_Date::MONTH_SHORT)
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
     * @param int       $category_id カテゴリーID
     * @return int 登録したページのID
     * @author charlesvineyard
     */
    public function registPage($title, $contents, $outline, $tags,
            $create_date, $status, $category_id)
    {
        $account = $this->_accountDao->findByLoginId(Zend_Auth::getInstance()->getIdentity());
        $page = array(
            'title'       => $title,
            'contents'    => $contents,
            'outline'     => $outline,
            'create_date' => $createDate,
            'account_id'  => $account['id'],
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
        $tagIds = array();
        foreach ($tagNames as $tag) {
            $tagId = $this->_tagDao->findTagIdByTagName($tag);
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
     * @param int   $id       ページID
     * @param array $pageInfo ページ情報の配列
     * @return void
     * @author charlesvineyard
     */
    public function updatePage($id, $pageInfo)
    {
        $this->_pageTagDao->delete($this->_pageTagDao->getAdapter()->quoteInto('page_id = ?', $id));
        
        $tagIds = $this->_registTagsIfNotExist($pageInfo['tag']);
        foreach ($tagIds as $tagId) {
            $this->_pageTagDao->insert(array(
                'page_id' => $id,
                'tag_id'  => $tagId
            ));
        }
        unset($pageInfo['tag']);
        
        $where = $this->_pageDao->getAdapter()->quoteInto('id = ?', $id);
        $this->_pageDao->update($pageInfo, $where);
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
        $where = $this->_pageDao->getAdapter()->quoteInto('id = ?', $id);
        $this->_pageDao->delete($where);
    }
}
