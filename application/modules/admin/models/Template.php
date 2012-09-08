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
class Admin_Model_Template extends Common_Model_TemplateAbstract
{

    /**
     * テンプレートデータを登録する
     *
     * @param array $registData 登録するデータ
     * @return 登録に成功したか
     * @author suzuki-mar
     * @todo registをregisterに変更する
     * @todo 例外の処理をちゃんとする
     * @todo トランザクション処理をする
     */
    public function registTemplate(array $registData)
    {
        $registData['id'] = $this->_templateDAO->findNextAutoIncrementNumber();
        $registData['file_name'] = $registData['id'];

        $content = $registData['content'];
        //要素があるままだとデータ登録時にエラーになってしまうので
        unset($registData['content']);

        $registeredId = $this->_templateDAO->insert($registData);

        if (!is_numeric($registeredId)) {
            throw new Setuco_Exception('templateの登録に失敗してしまいました');
        }

        if (!$this->_createTemplateFile($registeredId, $content)) {
            throw new Setuco_Exception('templateの登録に失敗してしまいました');
        }

        return true;
    }

    /**
     * テンプレートデータを削除する
     *
     * @param int $id 削除するレコードのID
     * @return boolean 削除に成功したか
     * @author suzuki-mar
     * @todo 例外の処理をちゃんとする
     * @todo トランザクション処理をする
     */
    public function deleteTemplate($id)
    {
        if (!$this->_templateDAO->deleteByPrimary($id)) {
            throw new Setuco_Exception('templateの削除に失敗してしまいました');
        }

        if (!unlink($this->_getTemplatePathById($id))) {
            throw new Setuco_Exception('templateの削除に失敗してしまいました');
        }

        return true;
    }


    /**
     * テンプレートデータを更新する
     *
     * @param int $id 更新するレコードのID
     * @param array $updateDatas 更新するデータ
     * @return boolean 更新することができたか
     * @author suzuki-mar
     * @todo 例外の処理をちゃんとする
     * @todo トランザクション処理をする
     */
    public function updateTemplate($id, array $updateDatas)
    {
        $content = $updateDatas['content'];
        unset($updateDatas['content']);

        
        if (!$this->_templateDAO->updateByPrimary($updateDatas, $id)) {
           throw new Setuco_Exception('templateの更新に失敗してしまいました');
        }

        if (!$this->_createTemplateFile($id, $content)) {
            throw new Setuco_Exception('templateの更新に失敗してしまいました');
        }

        return true;
    }

    /**
     * IDからテンプレートのパスを取得する
     *
     * @param int $id テンプレートのパスを取得するID
     * @return string テンプレートのパス
     * @author suzuki-mar
     */
    protected function _getTemplatePathById($id)
    {
        return "{$this->_getBasePath()}{$id}.html";
    }

    /**
     * テンプレートファイルを作成する
     *
     * @param  int      $registeredId テンプレートファイルのID
     * @param  String   $content 登録した内容
     * @return boolean 作成に成功したかどうか
     */
    private function _createTemplateFile($registeredId, $content)
    {
        return (file_put_contents($this->_getTemplatePathById($registeredId), $content) !== false);
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

}

