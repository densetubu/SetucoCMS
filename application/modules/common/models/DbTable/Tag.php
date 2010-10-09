<?php
/**
 * tagテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard suzuki-mar
 */

/**
 * @package     Common_Model
 * @subpackage  DbTable
 * @author      charlesvineyard suzuki-mar
 */
class Common_Model_DbTable_Tag extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected $_name = 'tag';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';
    
    
    /**
     * タグの名前とどれぐらい使用されているかを取得する
     *
     * @return array タグの名前とどれぐらい使用されているかの配列
     * @author suzuki-mar
     */
    public function findTagCount() 
    {
    	//タグ名とどれぐらい使用されているかをカウントする
        $select = $this->select()->from(array('t' => $this->_name), array('id', 'name'));
        
        //テーブルを結合する
        $select->join(array('pt' => 'page_tag'), 't.id = pt.tag_id', array('count' => 'COUNT(pt.tag_id)'));
        $select->join(array('p' => 'page'), 'p.id = pt.page_id', array('update_date', 'create_date'));
        
        //結合するときはfalseにしないといけない
        $select->setIntegrityCheck(false);
        
        //公開しているものしか取得しない
        $select->where('p.status = ?', Common_Model_DbTable_Page::STATUS_OPEN);
        //tagごとにカウントする
        $select->group('pt.tag_id');

        //編集順にソートする
        $select->order('p.update_date DESC');
        
        
        
        $result = $this->fetchAll($select)->toArray();
        
        return $result;
    }
    
}

