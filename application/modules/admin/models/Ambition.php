<?php
/**
 * 野望に関するサービスです。
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
    public function findAmbition()
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
    public function updateAmbition($ambition)
    {
        $this->_ambitionDao->update(array('ambition' => $ambition));
    }
}

