<?php
/**
 * mediaテーブルのDbTable(DAO)クラスです。
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
 * @author     mitchang akitsukada
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      mitchang akitsukada
 */
class Common_Model_DbTable_Media extends Setuco_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'media';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * media表から指定されたIDのレコードを削除する
     *
     * @param int $id 削除したいレコードのID
     * @return boolean 削除
     * @author akitsukada
     */
    public function deleteById($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        if ($this->delete($where) == 1) {
            return true;
        }
        return false;
    }

    /**
     * media表の、指定された拡張子のファイル件数をカウントする
     *
     * @param string $type カウントしたいファイルの拡張子。指定しなければ全てを数える
     * @return int カウントした件数
     * @author akitsukada
     */
    public function countMediasByType($type = null)
    {
        $select = $this->select($this->_name);
        if ($type !== 'all') {
            $select->where('type = ?', $type);
        }
        $select->where('type != ?', 'new'); // ゴミファイル(.new)がひっかかってしまわないように
        $result = $this->fetchAll($select);
        return count($result);
    }

    /**
     * 指定された条件でページネート、ソートされた検索結果を配列で返す
     *
     * @param Zend_Db_Table_Select $select 実行したいSelectオブジェクト
     * @return Selectオブジェクトの実行(fetchAll)結果
     * @author akitsukada
     */
    public function loadMedias($sortColumn, $order, $limit, $pageNumber, $targetExt, $tmpFileExt)
    {

        $select = $this->select()
                        ->order("{$sortColumn} {$order}")
                        ->limitPage($pageNumber, $limit);

        if ($targetExt === 'all') {
            // 絞り込みなしの場合、一時ファイル拡張子以外のレコードを全件取得
            $select->where('type != ?', $tmpFileExt);
        } else {
            $select->where('type = ?', $targetExt);
        }

        return $this->fetchAll($select)->toArray();
    }

}
