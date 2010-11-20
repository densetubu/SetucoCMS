<?php
/**
 * categoryテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard
 */

/**
 * @package     Common_Model
 * @subpackage  DbTable
 * @author      charlesvineyard suzuki-mar
 */
class Common_Model_DbTable_Category extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var String
     */
    protected $_name = 'category';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'id';

    /**
     * テーブルのalias名
     *
     * @var String
     */
    protected $_alias = 'c';

    /**
     * 親が無いカテゴリーの仮想親ID
     * 
     * @var int
     */
    const PARENT_ROOT_ID = -1;

    /**
     * 未分類のカテゴリー
     */
    const DEFAULT_ID = 0;

    /**
     * 共通の初期設定をしたSELECTオブジェクト
     *
     * @param boolean[option] 外部結合するか　デフォルトはfalse
     * @return Zend_Db_Select 共通の初期設定をしたSELECTオブジェクト
     * @author suzuki-mar
     */
    protected function _initializeSelect($isJoinTable = false)
    {
        //初期設定をしているカテゴリーのSELECT文を取得する
        $select = $this->select();

        //外部結合するか
        if ($isJoinTable) {
            //取得しないカテゴリーを設定する
            $select->where('c.id != ?', self::PARENT_ROOT_ID);
        } else {
            $select->where('id != ?', self::PARENT_ROOT_ID);
        }


        return $select;
    }

    /**
     * 外部テーブルと結合したものを取得する
     * groupなどの必要な設定もしている
     *
     * @return Zend_Db_Select 外部テーブルと結合したSELECTオブジェクト
     * @author suzuki-mar
     */
    protected function _joinSelect()
    {
        //初期設定をしているカテゴリーのSELECT文を取得する
        $select = $this->_initializeSelect(true);

        $select->from(array('c' => $this->_name), array('*'));

        //テーブルを結合する 使用されていないものも取得する
        $select->joinLeft(array('p' => 'page'), 'c.id = p.category_id', array('title'));
        //結合するときはfalseにしないといけない
        $select->setIntegrityCheck(false);
        //categoryでグループ化
        $select->group('c.id');


        return $select;

    }

    /**
     * 未分類のカテゴリーを取得するwhere句をセットする　オプションで取得しないにもできる
     * 未分類のカテゴリーの処理が変わる可能性があるので
     *
     * @param Zend_Select $select where句をセットするSelectオブジェクト
     * @param boolean isDeselect 未分類のカテゴリーを取得しないのか デフォルトはtrue
     * @return Zend_Db_Select 未分類のカテゴリーに関するwhereを設定してSelectオブジェクト
     * @author suzuki-mar
     */
    protected function _defaultWhere(Zend_Db_Select $select, $isDeselect = true)
    {
        //デフォルトを取得する
        if ($isDeselect) {
            $operator = '=';
        } else {
            $operator = '!=';
        }

        $select->where("c.id {$operator} -1");
        return $select;
    }

    


    /**
     * カテゴリー一覧を取得する
     *
     * @param boolean[option] $isDeselectDefault 未使用カテゴリーは取得しないか デフォルトはfalse
     * @return array カテゴリー一覧　パラメーターがあるときは、未分類を取得しない
     * @author suzuki-mar
     */
    public function findCategoryLists($isDeselectDefault = false)
    {
        //結合したものを取得する
        $select = $this->_joinSelect();

        //五十音順でソートする
        $select->order('name ASC');



        $searchResult = $this->fetchAll($select);

        $result = $searchResult->toArray();

        //空だったらfalseを返す
        if (empty($result)) {
            return false;
        }

        return $result;
    }


    /**
     * 指定したソートでカテゴリー一覧を取得します。
     * @param String $sort カテゴリーを昇順か降順でソートするのか 文字列
     * @param int $page 現在のページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @return array    カテゴリー情報の一覧
     * @author  suzuki-mar
     */
    public function findSortCategories($sort, $page, $limit)
    {

        //初期設定をしたSELECTオブジェクト
        $select = $this->_initializeSelect();

        $select->limitPage($page, $limit)->order("name {$sort}");

        //データを取得する
        $result = $this->fetchAll($select);

        return $result;
    }

    /**
     * 指定したカラム・ソートでカテゴリー一覧を取得します。
     *
     * @param string|array $selectColumns 取得するカラム
     * @param string $sortColumn 並べ替えるカラム名
     * @param string $order 並び順(asc or desc) デフォルトは asc
     * @return array カテゴリー情報の一覧
     * @author charlesvineyard
     */
    public function findCategories($selectColumns, $sortColumn, $order = 'asc')
    {
        $select = $this->_initializeSelect()
                ->from($this->_name, $selectColumns)
                ->order("$sortColumn $order");
        return $this->fetchAll($select)->toArray();
    }

    /**
     * 指定の親カテゴリIdを持つカテゴリーを取得します。
     *
     * @param  int $parentId 親カテゴリID
     * @return array カテゴリー情報の一覧
     * @author charlesvineyard
     */
    public function findCategoriesByParentId($parentId)
    {
        $select = $this->_initializeSelect()->where('parent_id = ?', $parentId);
        return $this->fetchAll($select)->toArray();
    }

    /**
     * 全部で何件あるのか取得する
     *
     * @return int 何件のデータがあるのか
     * @author suzuki-mar
     */
    public function count()
    {
        $select = $this->select($this->_name);
        $result = $this->fetchCount($select);
        return $result;
    }


    /**
     * データの取得件数をカウントする
     *
     * @param Zend_Db_Select カウントするSelectオブジェクト
     * @return int 何件のデータがあるのか
     * @author suzuki-mar
     * @todo countRows に名前変更で親クラスに定義
     */
    public function fetchCount(Zend_Db_Select $select)
    {
        //検索結果をcountする
        $searchResult = $this->fetchAll($select)->toArray();
        $result = count($searchResult);

        return $result;
    }

    /**
     * idからデータを取得する
     *
     * @param int $id データを取得するid
     * @return array データを取得する　存在しなかったらfalseを返す
     * @author suzuki-mar
     */
    public function findById($id)
    {
        //$this->_primaryは、fetch時に配列になるので文字列の中間変数を作成する
        $primary = $this->_primary;

        //主キーがidとは限らないので、this-_primaryを使用する
        $select = $this->select()->from($this->_name)->where("{$primary} = ?", $id);

        //データを取得する
        $searchResult = $this->fetchRow($select);

        //取得に成功した場合のみ取得したデータを戻り値にする
        if ($searchResult) {
        	$result = $searchResult->toArray();
        } else {
            $result = false;
        }

        return $result;
    }
    

    /**
     * プライマリキーを取得する
     *
     * @return mixed プライマリキー　Zend_Db_Table->_primary
     * @author suzuki-mar
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

}

