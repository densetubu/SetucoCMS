<?php

/**
 * エディタのテンプレートを管理するサービスクラスです。
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
class Admin_Model_Template extends Setuco_Model_Abstract
{

    /**
     * @var Common_Model_Template
     */
    private $_templateDAO;


    public function  __construct(Zend_Db_Adapter_Pdo_Abstract $adapter = null)
    {
        parent::__construct($adapter);
        $this->_templateDAO = new Common_Model_DbTable_Template($adapter);
    }

    /**
     * テンプレートデータを登録する
     *
     * @param array $registData 登録するデータ
     * @return 登録に成功したか
     * @author suzuki-mar
     * @todo registをregisterに変更する
     */
    public function registTemplate(array $registData)
    {
        $registData['id']        = $this->_templateDAO->findNextAutoIncrementNumber();
        $registData['file_name'] = $registData['id'];

        $content = $registData['content'];
        //要素があるままだとデータ登録時にエラーになってしまうので
        unset($registData['content']);


        $registeredId = $this->_templateDAO->insert($registData);

        if (is_numeric($registeredId)) {
            if($this->_createTemplateFile($registeredId)) {
                return true;
            }
        }

        throw new Setuco_Exception('templateの登録に失敗してしまいました');
    }


    /**
     * テンプレートファイルを作成する
     *
     * @param  int $registeredId テンプレートファイルのID
     * @return boolean 作成に成功したかどうか
     */
    private function _createTemplateFile($registeredId)
    {
        $fileName = "{$this->_getBasePath()}{$registeredId}.html";
        return (file_put_contents($fileName, 'hogehoge') !== false);
    }

    /**
     * 次のファイル名を取得する
     * ファイル名はレコードID
     *
     * @param $int ファイル名を取得するアカウントID
     * 
     */
    public function findNextFileName()
    {
        $nextId = $this->_templateDAO->findNextAutoIncrementNumber();
        return "{$nextId}";
    }

    /**
     * テンプレートを保存するベースとなるパスを取得する
     *
     * @return string テンプレートのベースパス
     */
    protected function _getBasePath()
    {
        return '/Users/suzukimasayuki/project/setucodev/tests/data/template/';
    }

}

