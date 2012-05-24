<?php
/* 
 * mediaテーブルのフィクスチャークラス
 */

class Fixture_Media extends Setuco_Test_Fixture_Abstract
{

    const ID_JPEG = 1;
    const ID_PNG  = 2;
    const ID_PDF  = 3;

    public function getColumns()
    {
        return array('id', 'name', 'type', 'create_date', 'update_date', 'comment');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'create_date'        => "2012-04-11 08:42:09",
              'update_date'        => "2012-04-11 08:42:09",
              'comment'            => "2012-04-11 08:42:09にアップロード",
            );
    }

    public function getDataOfJpeg()
    {
        return array('id' => self::ID_JPEG,  'name' => 'image.jpeg', 'type' => 'jpg');
    }

    public function getDataOfPng()
    {
        return array('id' => self::ID_PNG,  'name' => 'image.png', 'type' => 'png');
    }

    public function getDataOfPDF()
    {
        return array('id' => self::ID_PDF,  'name' => 'sample.pdf', 'type' => 'pdf');
    }

}

