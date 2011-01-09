<?php
/**
 * タグ管理用サービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Common
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * タグ管理サービス
 *
 * @package    Common
 * @subpackage Model
 * @author     suzuki-mar
 */
abstract class Common_Model_TagAbstract
{
    /**
     * タグDAO
     *
     * @var Common_Model_DbTable_Tag
     */
    protected $_tagDao;

    /**
     * 指定したIDのタグ情報を取得する
     *
     * @param  int $id タグID
     * @return array タグ情報
     * @author charlesvineyard
     */
    public function findTag($id)
    {
        return $this->_tagDao->find($id)->current()->toArray();
    }

    /**
     * ページIDで指定されたページにつけられたタグの情報を返す。
     *
     * @param int $pageId タグを取得したいページのID
     * @return array 取得したタグ情報を格納した配列
     * @author akitsukada
     */
    public function findTagsByPageId($pageId)
    {
        return $this->_tagDao->loadTagByPageId($pageId);
    }

}
