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
     * 指定したソートでカテゴリー一覧を取得します。
     * @param String $sort カテゴリーを昇順か降順でソートするのか 文字列
     * @param int $page 現在のページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @return array    カテゴリー情報の一覧
     * @author  suzuki-mar
     */
    public function findSortCategories($sort, $page, $limit)
    {
    	
    	//データを取得するSelectオブジェクトを生成する
    	$select = $this->select($this->_name)->limitPage($page, $limit)->order("name {$sort}");
    	
    	//データを取得する
    	$result = $this->fetchAll($select);
    	
    	return $result;
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
    
    /****************
     *  使いまわせるかもしれないメソッド
     *****************/
    
    /**
     * データの取得件数をカウントする
     * 
     * @param Zend_Db_Select カウントするSelectオブジェクト
     * @return int 何件のデータがあるのか
     * @author suzuki-mar 
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
        	$result = $searchResult;
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

