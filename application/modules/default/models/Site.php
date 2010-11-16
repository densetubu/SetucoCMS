<?php
/**
 * 閲覧側のサイト情報管理用サービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Default
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * サイト情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Site extends Common_Model_SiteAbstract
{
    /**
     * 初期設定をする
     *
     * @author suzuki_mar
     */
    public function __construct()
    {
        $this->_siteDao = new Common_Model_DbTable_Site();
    }

    /**
     * サイト情報を取得する。開設年も取得します。
     *
     * @return array サイト情報
     * @author suzuki-mar charlesvineyard
     */
    public function getSiteInfo()
    {
        $result = parent::getSiteInfo();
        $openDate = new Zend_Date($result['open_date'], Zend_Date::ISO_8601);
        $result['start_year'] = $openDate->toValue(Zend_Date::YEAR_8601);
        return $result;
    }
}
