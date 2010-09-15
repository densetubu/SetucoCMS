<?php
/**
 * ページに関するサービス
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
 * @author     charlesvineyard
 */

/**
 * ページ管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     charlesvineyard
 */
class Admin_Model_Page
{
    /**
     * ページをロードします。
     *
     * @param string  $sortColmn  並べ替えをするカラムのカラム名
     * @param boolean $isAsc      昇順なら true
     * @param int     $beginIndex 取得開始位置(0から始まる）
     * @param int     $endIndex   取得終了位置(この位置自体は含みません)
     * @return array ページ情報の配列
     * @author charlesvineyard
     */
    public function loadPages($sortColmn, $isAsc, $beginIndex, $endIndex)
    {
        return array(
            'id'          => 1,
            'title'       => 'ページ1',
            'contents'    => 'ぺーじの内容だよ1',
            'outline'     => 'ページの概要1',
            'createDate' => '2011/09/01 00:00:00',
            'accountId'  => '1',
            'status'      => '0',
            'categoryId' => '1',
            'updateDate' => '2011/09/01 00:00:00'
        );
    }

    /**
     * 最近作成されたページを取得します。
     *
     * @param  int $count 取得ページ数
     * @return array ページ情報の配列
     * @author charlesvineyard
     */
    public function loadLastCreatedPages($count)
    {
        // TODO loadPages()が確定したらそれを呼ぶ
        return array(
            array('id'          => 1,
                  'title'       => 'ページ1',
                  'contents'    => 'ぺーじの内容だよ1',
                  'outline'     => 'ページの概要1',
                  'createDate' => '2011/09/05 12:00:00',
                  'accountId'  => '1',
                  'status'      => '1',
                  'categoryId' => '1',
                  'updateDate' => '2011/09/01 00:00:00'
            ),
            array('id'          => 2,
                  'title'       => 'ページ2',
                  'contents'    => 'ぺーじの内容だよ2',
                  'outline'     => 'ページの概要2',
                  'createDate' => '2011/09/05 10:00:00',
                  'accountId'  => '1',
                  'status'      => '1',
                  'categoryId' => '2',
                  'updateDate' => '2011/09/02 00:00:00'
            ),
            array('id'          => 3,
                  'title'       => 'ページ3',
                  'contents'    => 'ぺーじの内容だよ3',
                  'outline'     => 'ページの概要3',
                  'createDate' => '2011/09/05 09:00:00',
                  'accountId'  => '1',
                  'status'      => '0',
                  'categoryId' => '3',
                  'updateDate' => '2011/09/03 00:00:00'
            ),

            array('id'          => 4,
                  'title'       => 'ページ4',
                  'contents'    => 'ぺーじの内容だよ4',
                  'outline'     => 'ページの概要4',
                  'createDate' => '2011/09/05 08:00:00',
                  'accountId'  => '1',
                  'status'      => '1',
                  'categoryId' => '4',
                  'updateDate' => '2011/09/04 00:00:00'
            ),

            array('id'          => 5,
                  'title'       => 'ページ5',
                  'contents'    => 'ぺーじの内容だよ5',
                  'outline'     => 'ページの概要5',
                  'createDate' => '2011/09/05 07:00:00',
                  'accountId'  => '1',
                  'status'      => '0',
                  'categoryId' => '5',
                  'updateDate' => '2011/09/05 00:00:00'
            )
        );
    }

    /**
     * ページを数えます。
     *
     * @param int $status ページの状態（Setuco_Data_Constant_Page::STATUS_*）
     *                     指定しなければ全ての状態のものを数えます。
     * @param mixed $year  YYYY形式の年
     * @param mixed $month MM形式の月
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPage($status = null, $year = null, $month = null)
    {
        if ($status == null) {
            return 50;
        }
        return 5;
    }

}
