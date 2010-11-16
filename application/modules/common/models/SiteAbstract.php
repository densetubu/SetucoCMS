<?php
/**
 * サイト情報管理用サービス
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
 * @author     charlesvineyard
 */

/**
 * サイト情報管理クラス
 *
 * @package    Common
 * @subpackage Model
 * @author     charlesvineyard
 */
abstract class Common_Model_SiteAbstract
{
    /**
     * サイトDAO
     *
     * @var Common_Model_DbTable_Site
     */
    protected $_siteDao;
    
    /**
     * サイト情報を取得する
     *
     * @return array サイト情報
     * @author charlesvineyard
     */
    public function getSiteInfo()
    {
        return $this->_siteDao->fetchRow()->toArray();
    }

}
