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
class Common_Model_DbTable_Media extends Zend_Db_Table_Abstract
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
     * メディア表のレコードを全県取得する 
     */
    public function findAll()
    {
        //データを取得するSelectオブジェクトを生成する
        $select = $this->select($this->_name);
        
        //データを取得する
        $result = $this->fetchAll($select);

        return $result;

    }
    
    public function findById($id) 
    {
        $select = $this->select($this->_name)->where('id = ?', $id);
        return $this->fetchAll($select)->toArray();
    }
    
    public function deleteById($id) 
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        if ($this->delete($where) == 1) {
            return true;
        }
        return false;
    }
    
    public function count($ext = null)
    {
        $select = $this->select($this->_name);
        if ($ext !== 'all') {
            $select->where('type = ?', $ext);
        }
        $select->where('type != ?', 'new'); // ゴミファイル(.new)がひっかかってしまわないように
        $result = $this->fetchAll($select);       
        return count($result);
    }
    
    
    public function executeSelect(Zend_Db_Table_Select $select) 
    {
        return $this->fetchAll($select);
    }
    
}
