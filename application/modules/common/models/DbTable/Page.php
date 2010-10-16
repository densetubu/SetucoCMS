<?php
/**
 * pageテーブルのDbTable(DAO)クラスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Common
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     mitchang
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      mitchang
 */
class Common_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'page';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * 記事の状態　公開
     */
    const STATUS_OPEN = 1;

    /**
     * 記事の状態　下書き
     */
    const STATUS_DRAFT = 0;

    /**
     * 新着記事を取得する
     *
     * @param int $getPageCount 何件の記事を取得するのか
     * @author suzuki-mar
     */
    public function findNewPages($getPageCount)
    {
        $select = $this->select();

        //公開しているものしか取得しない
        $select->where('status = ?', self::STATUS_OPEN);

        //編集日時が最新順にソートする
        $select->order('update_date DESC');

        //指定した件数しか取得しない
        $select->limit($getPageCount);

        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * カテゴリーに属する記事を取得します。
     *
     * @param int $categoryId カテゴリーID
     * @author charlesvineyard
     */
    public function findPagesByCategory($categoryId)
    {
        $select = $this->select();

        //公開しているものしか取得しない
        $select->where('category_id = ?', $categoryId);

        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

}
