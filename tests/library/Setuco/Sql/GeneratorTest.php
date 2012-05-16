<?php

//AllTestsなどでbootstrapをすでに読み込んでいるかもしれない
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}


/**
 * Setuco_Sql_Generatorのテストクラス
 *
 * @author suzuki-mar
 */
class Setuco_Sql_GeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testcreateMultiLike4Keyword_複数キーワード検索ようのLike句を作成する_スペースが無い場合()
    {
        $like = Setuco_Sql_Generator::createMultiLike4Keyword('aaa', 'column_name', 'AND');
        $this->assertSame('( (column_name LIKE :keyword0) )', $like);
    }

    public function testCreateMultiLike4Keyword_スペースが１つある場合()
    {
        $like = Setuco_Sql_Generator::createMultiLike4Keyword('aaa　bbb', 'name', 'AND');
        $this->assertSame('( (name LIKE :keyword0) AND (name LIKE :keyword1) )', $like);
    }

    public function testCreateMultiLike4Keyword_ORにすることもできる()
    {
        $like = Setuco_Sql_Generator::createMultiLike4Keyword('aaa bbb', 'name', 'OR');
        $this->assertSame('( (name LIKE :keyword0) OR (name LIKE :keyword1) )', $like);
    }

    public function testCreateMulitiLikeBind4Keyword_複数キーワード検索用のLIKE句のbindを作成する()
    {
        $binds = Setuco_Sql_Generator::createMultiLikeTargets('aaa');
        $expects = array(':keyword0' => '%aaa%');
        $this->assertSame($expects, $binds);
    }

    public function testCreateMulitiLikeBind4Keyword_スペースがある場合()
    {
        $binds = Setuco_Sql_Generator::createMultiLikeTargets('aaa bbb');
        $expects = array(':keyword0' => '%aaa%', ':keyword1' => '%bbb%');
        $this->assertSame($expects, $binds);
    }

    public function testCreateMultiBindParams_複数キーワード検索用のLIKE句で指定した形式で囲んだbindを作成する()
    {
        $binds = Setuco_Sql_Generator::createMulitiBindParams('aaa', '<[^>]*', '[^<]*>');
        $expects = array(':keyword0' => '<[^>]*aaa[^<]*>');
        $this->assertSame($expects, $binds);
    }

    public function testCreateMultiBindParams_スペースがある場合()
    {
        $binds = Setuco_Sql_Generator::createMulitiBindParams('aaa bbb', '[start]', '[end]');
        $expects = array(':keyword0' => '[start]aaa[end]', ':keyword1' => '[start]bbb[end]' );
        $this->assertSame($expects, $binds);
    }

    public function testCreateMultiNoSearchTagBindParams_タグを検索しないbindを作成する()
    {
        $expects = array (
          ':startExp0'  => '<[^>]*aaa[^<]*>',
          ':startExp1'  => '<[^>]*bbb[^<]*>',
          ':endExp0'    => '>[^<]*aaa+',
          ':endExp1'    => '>[^<]*bbb+',
        );

        $this->assertSame($expects, Setuco_Sql_Generator::createMulitiNoSearchTagBindParams('aaa bbb'));
    }


    public function testCreateBsReplacedExpression_カラム名をreplace関数を使用する形式に変換する_escapeLikeStringとセットで使用する()
    {
        $column = Setuco_Sql_Generator::createBsReplacedExpression('p.title');
        $expects = "replace(p.title, '\\\', '__BS__')";
        $this->assertSame($expects, $column);
    }

    public function testEscapeLikeString_バックスラッシュを指定した形式に変換する()
    {
        $string = Setuco_Sql_Generator::escapeLikeString('a\a');
        $expects = 'a\_\_BS\_\_a';
        $this->assertSame($expects, $string);
    }


    
}

