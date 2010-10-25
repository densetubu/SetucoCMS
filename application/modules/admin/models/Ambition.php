<?php
/**
 * 野望に関するサービスです。
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
class Admin_Model_Ambition
{
    /**
     * 野望DAO
     *
     * @var Common_Model_DbTable_Ambition
     */
    private $_ambitionDao;
    
    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_ambitionDao = new Common_Model_DbTable_Ambition();
    }
        
    /**
     * 野望をロードします。
     *
     * @return string 野望文字列
     * @author charlesvineyard
     */
    public function load()
    {
        $result = $this->_ambitionDao->fetchRow()->toArray();
        return $result['ambition'];
    }

    /**
     * 野望を更新します。
     *
     * @param  string $ambition 野望
     * @return void
     * @author charlesvineyard
     */
    public function update($ambition)
    {
        $this->_ambitionDao->update(array('ambition' => $ambition));
    }
}

