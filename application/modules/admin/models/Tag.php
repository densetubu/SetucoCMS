<?php
/**
 * 管理側のタグ管理用サービス
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
 * @author     saniker10, suzuki-mar
 */

/**
 * タグ管理サービス
 *
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Tag
{
    /**
     * タグDAO
     *
     * @var Common_Model_DbTable_Tag
     */
    private $_tagDao;

    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_tagDao = new Common_Model_DbTable_Tag();
    }

    /**
     * 指定したIDのタグ情報を取得する
     *
     * @param  int $id タグID
     * @return array タグ情報
     * @author charlesvineyard
     */
    public function load($id)
    {
        return $this->_tagDao->find($id)->current()->toArray();
    }

    /**
     * すべてのタグ情報を取得する
     *
     * @param  string $order       asc か　desc
     * @param  int    $pageNumber  ページ番号(オフセットカウント)
     * @param  int    $limit       一つのページに出力する数(オフセット)
     * @return array タグ情報の一覧
     * @author saniker10, suzuki-mar, charlesvineyard
     */
    public function loadAllTags($order, $pageNumber, $limit)
    {
        return $this->_tagDao->findSortedTags($order, $pageNumber, $limit);
    }

    /**
     * すべてのタグを数えます
     * 
     * @return int すべてのタグの個数
     * @author charlesvineyard
     */
    public function countAllTags()
    {
        return $this->_tagDao->fetchAll()->count();
    }

    /**
     * タグを登録する
     *
     * @param  string $name タグ名
     * @return void
     * @author saniker10, suzuki-mar
     */
    public function regist($name)
    {
        $this->_tagDao->insert(array('name' => $name));
    }

    /**
     * タグを更新する
     *
     * @param  $id   タグID
     * @param  $name タグ名
     * @return void
     * @author charlesvineyard
     */
    public function update($id, $name)
    {
        $where = $this->_tagDao->getAdapter()->quoteInto('id = ?', $id);
        $this->_tagDao->update(array('name' => $name), $where);
    }

    /**
     * タグを削除する
     *
     * @param  $id   タグID
     * @return void
     * @author charlesvineyard
     */
    public function delete($id)
    {
        $where = $this->_tagDao->getAdapter()->quoteInto('id = ?', $id);
        $this->_tagDao->delete($where);
    }
}
