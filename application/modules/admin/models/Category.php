<?php
/**
 * 管理側のカテゴリー管理用サービス
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
 * カテゴリー管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Category
{
	/**
	 * 初期設定をする
	 *
	 * @author suzuki_mar
	 */
	public function __construct()
	{
		$this->_dao = new Common_Model_DbTable_Category();

	}

	/**
	 * タグ情報タグを取得する
	 *
	 * @param String $sort カテゴリーを昇順か降順でソートするのか 文字列
	 * @param int $page 現在のページ番号
	 * @param int $limit 1ページあたり何件のデータを取得するのか
	 * @return array 	カテゴリー情報の一覧
	 * @author  saniker10, suzuki-mar
	 * @todo スタブのデータを取得している
	 */
	public function searchCategories($sort, $page, $limit)
	{
        //pageを数値にキャストする
        $page = (int)$page;
		
		//ソートする方法をパラメータによって変更する desc意外は昇順(asc)
		if($sort === 'desc') {
			$sort = "DESC";
		} else {
			$sort = "ASC";
		}

		//指定したソートをしたデータを取得する
		$searchResult = $this->_dao->findSortCategories($sort, $page, $limit);

		//配列の方が操作しやすいので配列を戻り値にする
		$result = $searchResult->toArray();
		
		//１ページのみ未分類のカテゴリーを追加する
		if ($page === 1) {
		  $result[] = array('name' => '未分類',  'id' => false, 'is_check' => false);
		}
		
		return $result;
	}

	/**
	 * 検索条件で、リミットしなかった場合に該当結果が何件あったのかを取得する
	 *
	 * @param String $keyword 検索するキーワード
	 * @return int 何件該当したデータが存在したか
	 * @author suzuki-mar
	 */
	public function countCategories()
	{
		$result = $this->_dao->count();
		
		return $result;
	}

	/**
	 * 指定したidのデータが存在するか
	 *
	 * @param  numeric 存在するかを調べるid
	 * @return boolean 指定したidが存在するか
	 * @author suzuki-mar
	 * @todo 固定値(true)をかえしている
	 */
	public function isExistsId($id)
	{
		
		//idのカテゴリーが存在するかを調べる
		$category = $this->_dao->findById($id);
		
		//取得できたら、trueにする
		$result = (boolean)$category;
		
		return $result;
	}

	/**
	 * カテゴリーを新規作成する
	 * コントローラーから、バリデートチェックした入力パラメーターをすべて取得する
	 *
	 * @param array $inputData 入力したデータ: バリデートチェックした入力データ
	 * @return boolean 登録できたか
	 * @author suzuki-mar
	 */
	public function registCategory($inputData)
	{
		//DBに登録するデータを生成する
		$saveData['name'] = $inputData['cat_name'];

		//データを新規登録する
		$result = $this->_regiser($saveData);

		return $result;
	}

	/**
	 * カテゴリーを編集する
	 * コントローラーから、バリデートチェックした入力パラメーターをすべてと、編集するidを取得する
	 *
	 * @param array $inputData 入力したデータ: バリデートチェックした入力データ
	 * @param int   $updateId  アップデートするデータのID
	 * @return boolean 編集できたか
	 * @author suzuki-mar
	 */
	public function updateCategory($inputData, $updateId)
	{
		//アップデートするデータを作成する
        $updateData['name'] = $inputData['name'];
        
        //アップデートする
        $result = $this->_updateById($updateData, $updateId);
	    return $result;
	}

	/**
	 * カテゴリーを削除する
	 *
	 * コントローラーから、削除するidを取得する
	 *
	 * @param  int   $deleteId  削除するデータのID
	 * @return boolean 削除できたか
	 * @author suzuki-mar
	 */
	public function deleteCategory($deleteId)
	{

		//データを削除する
		$result = $this->_deleteByPrimary($deleteId);
				
		return $result;
	}
	
	/******
	 * 使いまわせるかもしれないメソッド
	 *********/
	
	/**
	 * １件のみデータを新規作成する
	 * 
	 * @param array $saveData 新規登録するデータ
	 * @return 新規作成できたか
	 * @author suzuki-mar
	 */
	protected function _regiser($saveData)
	{
	
       //作成に失敗したときに例外が発生する
        try {
            //データをinsertする
            $this->_dao->insert($saveData);
            $result = true;

        } catch (Zend_Exception $e) {
            $result = false;
        }
        
        return $result;
        
	}
	
	 /**
     * 指定したプライマリキーのデータをアップデートする
     *
     * @param array $inputData 入力したデータ: バリデートチェックした入力データ
     * @param int   $updateId  アップデートするデータのID
     * @return boolean 編集できたか
     * @author suzuki-mar
     */
    protected function _updateById($updateData, $updateId)
    {       
        //アップデートに失敗したときに例外が発生する
        try {
            //データをupdateする
            $primary    = $this->_dao->getPrimary();
            
            //数値にキャストする
            $updateId = (int)$updateId;
            //アップデートする条件のwhere句を生成する
            $where      = $this->_dao->getAdapter()->quoteInto("{$primary} = ?", $updateId);
                        
            $this->_dao->update($updateData, $where);
            $result     = true;

        } catch (Zend_Exception $e) {
            $result = false;            
        }
        
        return $result;
    }
	
    /**
     * 指定したプライマリキーのデータを削除する
     *
     * コントローラーから、削除するidを取得する
     *
     * @param  int   $deleteId  削除するデータのID
     * @return boolean 削除できたか
     * @author suzuki-mar
     */
    protected  function _deleteByPrimary($deleteId)
    {

        //アップデートに失敗したときに例外が発生する
        try {
            //データをupdateする
            $primary    = $this->_dao->getPrimary();
            
            //数値にキャストする
            $deleteId = (int)$deleteId;
            //アップデートする条件のwhere句を生成する
            $where      = $this->_dao->getAdapter()->quoteInto("{$primary} = ?", $deleteId);
                        
            $this->_dao->delete($where);
            $result     = true;

        } catch (Zend_Exception $e) {
            $result = false;            
        }
        
        return $result;
    }

}



