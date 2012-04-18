
<?php

/**
 * SQLのコードを生成するジェネレータークラス
 *
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Sql
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * @package    Setuco
 * @subpackage Sql
 * @author     suzuki-mar
 */
class Setuco_Sql_Generator
{
    /**
     * SQL文中のバックスラッシュ(\)を置換する文字列
     *
     * @var string
     */
    const BACKSLASH_REPLACER = '__BS__';

    /**
     * 複数LIKE用の検索する文字列のリストを作成する
     *
     * createMultiLikeメソッドを使用してPDOなどのプレスホルダーを使用する場合にセットで使用する
     *
     * 戻り値の例 "hoge fuga"と"keyword"の引数を渡した場合
     * ":keyword0" => "%hoge%"
     * ":keyword1" => "%fuga%"
     *
     * @param string $targetString LIKEで検索する文字列
     * @param string $placeBaseName プレースホルダーベース名　これを連番にする
     * @return array 複数LIKEの検索文字列のリスト
     * @author suzuki-mar
     */
    public static function createMultiLikeTargets($targetString, $placeBaseName)
    {
        return self::createMulitiBindParams($targetString, $placeBaseName, '%', '%');
    }

    /**
     * 引数で渡した文字列を分解して複数のLIKE用のSQLを生成する
     * fuga,barという文字列を渡したら生成されるSQLは以下のようなもの
     * ((hoge LIKE "%fuga%") AND (foo LIKE "%bar%"))
     *
     * @param string $targetString LIKEで検索する文字列
     * @param string $columnName 検索するカラム名
     * @param string[option] $placeBaseName プレスホルダーを使用する場合のベース名　これを連番にする
     * @param string[option] $searchOperator ANDかORで検索するか デフォルトはAND
     * @return string 生成されたSQLの文字列
     * @author suzuki-mar
     */
    public static function createMultiLike4Keyword($targetString, $columnName, $placeBaseName = null, $searchOperator = 'AND')
    {
        $targetLists = Setuco_Util_String::convertArrayByDelimiter($targetString);

        $result = '( ';

        for ($i = 0; $i < count($targetLists); $i++) {
            $result .= "(";
            $target = ":{$placeBaseName}{$i}";
            $result .= "{$columnName} LIKE %{$target}%";
            $result .= ") {$searchOperator} ";
        }

        //最後のAND(OR)を削除するため
        //後ろから削除するので        
        $result = preg_replace("/ {$searchOperator} $/", '', $result);
        $result .= " )";

        return $result;
    }

    /**
     * WHERE句のLIKE演算子や正規表現に与える文字列を\（バックスラッシュ）でエスケープします。
     * エスケープされた文字が検索できるようになります。
     * バックスラッシュ自体を検索するときは、getBsReplacedExpressionとセットで使う必要があります。
     *
     * @param string $str LIKE検索を行う検索対象文字列
     * @return string エスケープ済みの検索対象文字列
     * @author akitsukada suzuki-mar
     */
    public static function escapeLikeString($str)
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
     * @author akitsukada suzuki-mar
     */
    public static function createBsReplacedExpression($columnName)
    {
        return "replace({$columnName}, '\\\\', '" . self::BACKSLASH_REPLACER . "')";
    }

    /**
     * 指定した文字列を分割して、指定したフォーマットのバインドパラメーターのリストを作成する
     *
     * 'bind param', 'binds', 'first', 'second'という引数を渡したら
     * ':binds0' => 'firstbindsecond'
     * ':binds1' => 'firstparamsecond'
     * が戻り値となる
     *
     * @param string $targetString 分割する文字列
     * @param string $placeBaseName プレースホルダーのベース名 これを連番にする
     * @param string[option] $beginningString 分割した文字列の先頭に追加する文字列
     * @param string[option] $endString 分割した文字列の後に追加する文字列
     */
    public static function createMulitiBindParams($targetString, $placeBaseName, $beginningString = null, $endString = null)
    {
        $targetLists = Setuco_Util_String::convertArrayByDelimiter($targetString);

        $result = '';

        for ($i = 0; $i < count($targetLists); $i++) {
            $key = ":{$placeBaseName}{$i}";
            $value = $targetLists[$i];
            if (!is_null($beginningString)) {
                $value = $beginningString . $value;
            }

            if (!is_null($endString)) {
                $value .= $endString;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}