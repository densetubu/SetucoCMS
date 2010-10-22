<?php
/**
 * アカウントに関するサービスです。
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
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     charlesvineyard
 */
class Admin_Model_Account
{
    /**
     * アカウントDAO
     *
     * @var Common_Model_DbTable_Account
     */
    private $_accountDao;
    
    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_accountDao = new Common_Model_DbTable_Account();
    }
    
    /**
     * アカウント情報をロードします。
     *
     * @return array アカウント情報の配列
     * @author charlesvineyard
     */
    public function load($loginId)
    {
        return $this->_accountDao->findByLoginId($loginId);
    }
}

