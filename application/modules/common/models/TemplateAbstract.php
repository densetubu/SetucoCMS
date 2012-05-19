<?php
/**
 * 共通のテンプレート管理用サービス
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
 * ファイル管理クラス
 *
 * @package    Common
 * @subpackage Model
 * @author     suzuki-mar
 */
abstract class Common_Model_TemplateAbstract extends Setuco_Model_Abstract
{
    /**
     * @var Common_Model_DbTable_Template
     */
    protected $_templateDAO;


    public function  __construct(Zend_Db_Adapter_Pdo_Abstract $adapter = null)
    {
        parent::__construct($adapter);
        $this->_templateDAO = new Common_Model_DbTable_Template($adapter);
    }

    /**
     * すべてのテンプレートを取得する
     *
     * @return array 有効な全テンプレートレコード
     */
    public function findAllTemplates()
    {
        return $this->_templateDAO->loadAllTemplates();
    }

    /**
     * テンプレートを保存するベースとなるパスを取得する
     *
     * @return string テンプレートのベースパス
     * @todo 実際のテンプレートがあるファイルの場所を見るよう変更する
     */
    protected function _getBasePath()
    {
        return APPLICATION_PATH . '/../public/js/template/';
    }


}