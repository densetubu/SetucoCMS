<?php

/*
 * ページ検索をするためのパラメーターをまとめたクラス
 *
 * ページ検索メソッドのパラメーターは種類が多く,
 * ついくつかのメソッドで同じような引数を使いまわしていたので
 * 作成した
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Common
 * @subpackage Model_Page
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * ページ検索をするためのパラメーターをまとめたクラス
 *
 * @package    Common
 * @subpackage Model_Page
 * @author     suzuki-mar
 */
class Common_Model_Page_Param
{

    /**
     * 検索したいキーワード
     *
     * @var string
     */
    private $_keyword;
    /**
     * 検索したタグのIDリスト
     *
     * @var array
     */
    private $_tagIds;
    /**
     * ページネータで何ページ目を表示するか
     *
     * @var int
     */
    private $_pageNumber;
    /**
     * ページネータで1ページに何件表示するか
     *
     * @var int
     */
    private $_limit;
    /**
     * 検索対象のカラム名の配列
     *
     * @var array
     */
    private $_targetColumns = array('title', 'contents', 'outline', 'tag');
    /**
     * ページ検索で取得したものを絞り込むカラム名と値を指定する これにマッチしたものしか取得しない
     *
     * @var array
     */
    private $_refinements;
    /**
     * ソートするカラム名
     *
     * @var string
     */
    private $_sortColumn;

    /**
     * ソート方法をASCかDESCかを指定する
     *
     * @var string
     */
    private $_order;

    /**
     * 検索をANDかORで検索するか
     */
     private $_searchOperator;

     /**
      * SQLのエスケープした文字列
      */
     private $_escapeKeyword;

    /**
     * インスタンスを生成するときにメンバーをすべて指定する
     *
     * @param string $keyword 検索したいキーワード。
     * @param array[option] $tagIds 使用しているタグID デフォルトは空
     * @param int[option] $pageNumber ページネータで何ページ目を表示するか。 デフォルトは1ページ目
     * @param int[option] $limit ページネータで１ページに何件表示するか。 デフォルトは10件
     * @param array[option] $targetColumns 検索対象のカラム名の配列 デフォルトはnull
     * @param array[option] $refinements　ページ検索で取得したものを絞り込むカラム名と値を指定する これにマッチしたものしか取得しない デフォルトはnull
     * @param string[option] $sortColumn ソートするカラム名 デフォルトはupdate_date
     * @param string[option] $order ASCかDESCを指定する デフォルトはDESC
     * @param string[option] $searchOperator 検索をANDかORで検索するか デフォルトはAND
     * @author suzuki-mar
     */
    public function __construct($keyword, array $tagIds = array(), $pageNumber = 1, $limit = 10,
            $targetColumns = null, $refinements = null,
            $sortColumn = 'update_date', $order = 'DESC', $searchOperator = 'AND')
    {
        $this->_keyword = $keyword;
        $this->_tagIds = $tagIds;
        $this->_pageNumber = $pageNumber;
        $this->_limit = $limit;

        if (!is_null($targetColumns)) {
            $this->_targetColumns = $targetColumns;
        }

        $this->_refinements = $refinements;
        $this->_sortColumn = $sortColumn;
        $this->_order = $order;
        $this->_searchOperator = $searchOperator;
    }

    /**
     * キーワード検索をするものを取得するカラム名
     *
     * LIKE句で検索するもの
     *
     * @return array キーワード検索で検索するカラム名
     * @author suzuki-mar
     */
    public function getKeywordSearchColumns()
    {
        $columns = $this->_targetColumns;

        foreach($columns as $key => $name) {
            if ($name === 'tag') {
                unset($columns[$key]);
                break;
            }
        }
        
        return $columns;
    }

    /**
     * DAOで使用できるパラメーターを変更できる
     *
     * @param array $params 変更するパラメーター key フィールド名 value フィールドの値
     * @return void
     * @author suzuki-mar
     */
    public function setDaoParams($params)
    {
        foreach ($params as $key => $value) {
            $key = "_{$key}";
            $this->$key = $value;
        }
    }

    /**
     * エスケープした文字列を設定する
     *
     * @param string $escapeKeyword エスケープした文字列
     * @return void
     * @author suzuki-mar
     */
    public function setEscapeKeyword($escapeKeyword)
    {
        $this->_escapeKeyword = $escapeKeyword;

    }

    /**
     * パラメーターがからかを調べる
     * 
     * emptyはリテラル値しか判定しない
     *
     * @param string $name パラメーター名
     * @return boolean からか
     * @author suzuki-mar
     */
    public function isEmpty($name)
    {
       $name = '_' . $name;
       $value = $this->$name;

       return empty($value);
    }

    /**
     * 指定したターゲットが検索対象か
     *
     * @param string $columnName
     * @return boolean 検索対象か
     * @author suzuki-mar
     */
    public function isInTargetColumn($columnName)
    {
        return (in_array($columnName, $this->getTargetColumns()));
    }

    /**
     * タグで検索するか
     *
     * @return boolean タグで検索するか
     * @author suzuki-mar
     */
    public function isTargetTag()
    {
        if ($this->isInTargetColumn('tag')) {
            if (is_array($this->getTagIds()) && !$this->isEmpty('tagIds')) {
                return true;
            }
        }

        return false;
    }

    /**
     * キーワード検索をするか
     *
     * @return boolean キーワード検索をする
     * @author suzuki-mar
     */
    public function isSearchKeyword()
    {
        foreach ($this->getKeywordSearchColumns() as $name) {
            if ($this->isInTargetColumn($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 詳細検索をするか
     *
     * @return boolean 詳細検索をする
     * @author suzuki-mar
     */
     public function isSearchRefinements()
     {
         return (is_array($this->getRefinements()) && !$this->isEmpty('refinements'));
     }


    /**
     * getterを動的に呼び出せるようにする
     *
     * @author suzuki-mar
     */
    public function __call($name, $arguments)
    {
        //getterだけ呼び出せる
        if (strpos($name, 'get') === false) {
            throw new BadMethodCallException("{$name}というメソッドは存在しません。");
        }

        $paramName = preg_replace('/^get/', '', $name);
        $paramName = Setuco_Util_String::convertToFirstLower($paramName);
        $paramName = '_' . $paramName;

        return $this->$paramName;
    }

    /**
     * 検索条件を指定してあるか
     *
     * @return boolean 検索条件を指定してあるか
     * @author suzuki-mar
     */
    public function isSettingSearchCondition()
    {
        if ($this->isSearchRefinements()) {
            return true;
        }

        if ($this->isSearchKeyword() && $this->getKeyword() !== '') {
            return true;
        }

        return false;
    }
}
