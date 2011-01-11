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

    /**
     * IDからひとつの行を取得し、配列で返します。
     * 行が見つからなければ null を返します。
     *
     * このメソッドは複合キーに対応するため可変長引数です。
     *
     * 主キーが2つからなる複合キーの場合は、次のように1つ1つを引数に指定して呼び出してください。
     * それぞれが _primary に指定したカラムと順序に対応します。
     * findById('キー１の値', 'キー2の値');
     *
     * このメソッドは親クラスのfindメソッドに委譲しています。
     * 呼び出しに関する制約はそちらのコメントも参照してください。
     *
     * @param mixed $key プライマリキーの値(複数の場合あり)
     * @return array|null 取得した行の配列。なければ null。
     * @see Zend_Db_Table_Abstract::find()
     * @author charlesvineyard
     */
    public function findById()
    {
        $rowset = call_user_func_array('parent::find', func_get_args());
        if ($rowset->count() == 0) {
            return null;
        }
        return $rowset->current()->toArray();
    }

}
