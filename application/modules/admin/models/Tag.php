<?php
/**
 * 管理側のタグ管理用サービス
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
 * タグ管理サービス
 *
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Tag
{
    /**
     * 指定したIDのタグ情報を取得する
     *
     * @param  int $id タグID
     * @return array タグ情報
     * @author charlesvineyard
     */
    public function load($id)
    {
        return array('name' => 'タグ1', 'id' => 1);
    }

    /**
     * すべてのタグ情報タグを取得する
     *
     * @param  string $order
     * @return array タグ情報の一覧
     * @author saniker10, suzuki-mar
     */
    public function loadAll($order)
    {
        $result[] = array('name' => 'タグ1', 'id' => 1);
        $result[] = array('name' => 'タグ2', 'id' => 2);
        $result[] = array('name' => '新規タグ', 'id' => 3);
        return $result;
    }

    /**
     * タグを登録する
     *
     * @param string $tagName タグ名
     * @return void
     * @author  saniker10, suzuki-mar
     */
    public function regist($tagName)
    {
        // TODO
    }

    /**
     * タグを更新する
     *
     * @param  $id   タグID
     * @param  $name タグ名
     * @return void
     * @author charlesvineyard
     */
    public function update($id, $name)
    {
        // TODO
    }

    /**
     * タグを削除する
     *
     * @param  $id   タグID
     * @return void
     * @author charlesvineyard
     */
    public function delete($id)
    {
        // TODO
    }
}
