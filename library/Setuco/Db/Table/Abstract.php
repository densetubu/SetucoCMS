<?php
/**
 * DbTableの抽象クラスです。
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
 * @subpackage Db_Table
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Db_Table
 * @author      charlesvineyard
 */
abstract class Setuco_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * SQL文中のバックスラッシュ(\)を置換する文字列
     *
     * @var string
     */
    const BACKSLASH_REPLACER = '__BS__';

    public function  __construct($config = array())
    {
        //Zend_Db_Tableと互換性を持つため
        if (is_null($config)) {
            $config = array();
        }

        parent::__construct($config);
    }


    /**
     * 全部で何件あるのか取得する
     *
     * @return int 全てのデータ件数
     * @author suzuki-mar charlesvineyard
     */
    public function countAll()
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
     * loadByPrimary('キー１の値', 'キー2の値');
     *
     * このメソッドは親クラスのfindメソッドに委譲しています。
     * 呼び出しに関する制約はそちらのコメントも参照してください。
     *
     * @param mixed $key プライマリキーの値(複数の場合あり)
     * @return array|null 取得した行の配列。なければ null。
     * @see Zend_Db_Table_Abstract::find()
     * @author charlesvineyard
     */
    public function loadByPrimary()
    {
        $args = func_get_args();
        foreach ($args as $i => $arg) {
            if (is_array($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に配列は指定できません。(" . ($i + 1) . "番目のキー)");
            }
            if (is_null($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に NULL は指定できません。(" . ($i + 1) . "番目のキー)");
            }
        }

        $rowset = parent::find($args);
        if ($rowset->count() == 0) {
            return null;
        }
        return $rowset->current()->toArray();
    }

    /**
     * 指定されたIDのレコードを削除します。
     *
     * このメソッドは複合キーに対応するため可変長引数です。
     * 主キーが2つからなる複合キーの場合は、次のように1つ1つを引数に指定して呼び出してください。
     * それぞれが _primary に指定したカラムと順序に対応します。
     * deleteByPrimary('キー１の値', 'キー2の値');
     *
     * @param mixed $key プライマリキーの値(複数の場合あり)
     * @return boolean 削除できたら true。該当レコードが存在しなければ false。
     * @author akitsukada charlesvineyard
     */
    public function deleteByPrimary()
    {
        $primary = (array) $this->_primary;

        if (count($primary) > func_num_args()) {
            throw new Zend_Db_Table_Exception("プライマリキーに対する値が少なすぎます。");
        }
        if (count($primary) < func_num_args()) {
            throw new Zend_Db_Table_Exception("プライマリキーに対する値が多すぎます。");
        }

        $where = array();
        $i = 0;
        foreach ($primary as $key) {
            $arg = func_get_arg($i);
            if (is_array($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に配列は指定できません。(" . ($i + 1) . "番目のキー)");
            }
            if (is_null($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に NULL は指定できません。(" . ($i + 1) . "番目のキー)");
            }
            $where[] = $this->getAdapter()->quoteInto($key . ' = ?', $arg);
            $i++;
        }

        $effectedRowCount = $this->delete($where);
        if ($effectedRowCount == 0) {
            return false;
        }
        return true;
    }

    /**
     * 指定されたIDのレコードを更新する。
     *
     * このメソッドは複合キーに対応するため可変長引数です。
     * 主キーが2つからなる複合キーの場合は、次のように1つ1つを引数に指定して呼び出してください。
     * それぞれが _primary に指定したカラムと順序に対応します。
     * deleteByPrimary('キー１の値', 'キー2の値');
     *
     * @param array $updateData キー:カラム名、値:更新する値の配列。
     * @param mixed $key プライマリキーの値(複数の場合あり)
     * @return boolean 更新できたら true。該当レコードが存在しなければ false。
     * @author charlesvineyard
     */
    public function updateByPrimary($updateData)
    {
        $primary = (array) $this->_primary;

        if (count($primary) > func_num_args() - 1) {
            throw new Zend_Db_Table_Exception("プライマリキーに対する値が少なすぎます。");
        }
        if (count($primary) < func_num_args() - 1) {
            throw new Zend_Db_Table_Exception("プライマリキーに対する値が多すぎます。");
        }

        $where = array();
        $i = 1;
        foreach ($primary as $key) {
            $arg = func_get_arg($i);
            if (is_array($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に配列は指定できません。(" . $i . "番目のキー)");
            }
            if (is_null($arg)) {
                throw new Zend_Db_Table_Exception("プライマリキーに対する値に NULL は指定できません。(" . $i . "番目のキー)");
            }
            $where[] = $this->getAdapter()->quoteInto($key . ' = ?', $arg);
            $i++;
        }

        $effectedRowCount = $this->update($updateData, $where);
        if ($effectedRowCount == 0) {
            return false;
        }
        return true;
    }

    /**
     * WHERE句のLIKE演算子や正規表現に与える文字列を\（バックスラッシュ）でエスケープします。
     * エスケープされた文字が検索できるようになります。
     * バックスラッシュ自体を検索するときは、getBsReplacedExpressionとセットで使う必要があります。
     *
     * @param string $str LIKE検索を行う検索対象文字列
     * @return string エスケープ済みの検索対象文字列
     * @author akitsukada
     */
    public function escapeLikeString($str)
    {
        $str = str_replace('\\', self::BACKSLASH_REPLACER, $str);
        $str = addcslashes($str, '%_<>{}:[]+.*()|^$?');
        return $str;
    }

    /**
     * $columnNameにSQL文のカラム名、リテラルを受け取り、MySQL,PostgreSQLのreplace関数を
     * 適用した表現を返します。replace関数は'\'をBACKSLASH_REPLACERに置換します。
     * 例："col" → "replace(col, '\\\\', '__BACKSLASH__')"
     * LIKE検索時には、escapeLikeStringとセットで使う必要があります。
     *
     * @param string $columnName
     * @return string 受け取ったカラム名にreplace関数を適用した表現
     * @author akitsukada
     */
    public function getBsReplacedExpression($columnName)
    {
        return "replace({$columnName}, '\\\\', '" . self::BACKSLASH_REPLACER . "')";
    }

    /**
     * 実行したSQLクエリーを全件取得する
     * 
     * @return array 実行したSQLクエリー
     * @author suzuki-mar
     */
    public function getExecutedAllSql()
    {
        foreach ($this->_db->getProfiler()->getQueryProfiles() as $profiler) {
           $result[] = $profiler->getQuery();
        }

        return $result;
    }

    /**
     * 実行した最後のSQLクエリーを取得する
     *
     * @return string 実行したSQLクエリー
     * @author suzuki-mar
     */
    public function getExecutedLastSql()
    {
        $allSqls = $this->getExecutedAllSql();
        $lastIndex = count($allSqls) - 1;
        return $allSqls[$lastIndex];
    }

    /**
     * バインドホルダーをSelectオブジェクトにバインドする
     *
     * 複数回セット出来るようにメソッドを定義しなおした
     *
     * @param Zend_Db_Select $select バインドするSELECTインスタンス
     * @return $this 自分自身のインスタンス
     * @author suzuki-mar
     */
    protected function _bindOfBindHolders(Zend_Db_Select &$select)
    {
        $select->bind($this->_bindHolders);
        return $this;
    }

}