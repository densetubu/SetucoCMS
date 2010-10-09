<?php
/**
 * pageテーブルのDbTable(DAO)クラスです。
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
 * @author     mitchang
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      mitchang
 */
class Common_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
	/**
	 * テーブル名
	 *
	 * @var string
	 */
	protected $_name = 'page';

	/**
	 * プライマリーキーのカラム名
	 *
	 * @var string
	 */
	protected $_primary = 'id';

	/**
	 * 記事の状態　公開
	 */
	const STATUS_OPEN = 1;

	/**
	 * 記事の状態　下書き
	 */
	const STATUS_DRAFT = 0;

}

