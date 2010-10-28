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
     * ページDAO
     * 
     * @var Common_Model_DbTable_Page
     */
    private $_pageDao;
    
    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_pageDao = new Common_Model_DbTable_Page();
    }
    
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
     * 取得順序は作成日時の降順です。
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
     * @param int $createYear  YYYY形式の年 指定すればその年に作成したものを数えます
     * @param int $createMonth MM形式の月 $createYearと一緒に指定すればその月に作成したものを数えます
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPages($status = null, $createYear = null, $createMonth = null)
    {
        $startDate = null;
        $endDate = null;
        if ($createYear != null) {
            if ($createMonth != null) {
                $startDate = new Zend_Date("{$createYear}-{$createMonth}", 'YYYY-M');
                $endDate = new Zend_Date($createYear . '-' . ($createMonth + 1), 'YYYY-M');
            } else {
                $startDate = new Zend_Date($createYear, 'YYYY');
                $endDate = new Zend_Date($createYear + 1, 'YYYY');
            }
        }
        return $this->_pageDao->countPages($status, $startDate, $endDate);
    }

    /**
     * 今月作成(公開)したページ数を取得する
     *
     * @return int 今月作成(公開)したページ数
     * @author charlesvineyard
     */
    public function countPagesCreatedThisMonth()
    {
        $date = new Zend_Date();
        return $this->countPages(
                Setuco_Data_Constant_Page::STATUS_RELEASE,
                $date->get(Zend_Date::YEAR),
                $date->get(Zend_Date::MONTH_SHORT)
        );
    }

}
