<?php
/**
 * DbTableの抽象クラスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Db
 * @subpackage Table
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Db
 * @subpackage Table
 * @author      charlesvineyard
 */
class Setuco_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * 全部で何件あるのか取得する
     *
     * @return int 全てのデータ件数
     * @author suzuki-mar charlesvineyard
     */
    public function count()
    {
        $select = $this->select($this->_name);
        return $this->fetchAll($select)->count();
    }

}
