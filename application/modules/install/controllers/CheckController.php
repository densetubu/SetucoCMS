<?php

/**
 * 設置する環境が整っているか確認をするコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 0.1.0
 * @author     Takayuki Otake
 */

/*
 * SetucoCMSを設置する環境が整っているか確認をするコントローラ
 *
 * @package    Install
 * @subpackage Controller
 * @author     Takayuki Otake
 */
class Install_CheckController extends Setuco_Controller_Action_Abstract
{
    /**
     * インストール環境が揃っているか確認するアクション
     *
     * @author Takayuki Otake
     */
    public function indexAction()
    {
        $all_ok = true;
        $required_ok = true;

        // PHP Extension
        if (false === ($php_extensions['pdo'] = extension_loaded('pdo'))) {
            $all_ok = false;
            $required_ok = false;
        }
        if (false === ($php_extensions['mysql'] = extension_loaded('mysql'))) {
            $all_ok = false;
            $required_ok = false;
        }
        if (false === ($php_extensions['mbstring'] = extension_loaded('mbstring'))) {
            $all_ok = false;
            $required_ok = false;
        }
        if (false === ($php_extensions['gd'] = extension_loaded('gd'))) {
            $all_ok = false;
        }
        $this->view->extensions = $php_extensions;

        // is directory writable
        $isWritable = array(
            'configs' => $this->_isWritable('configs/'),
            'media' => $this->_isWritable('../public/media/'),
        );
        if (in_array(false, $isWritable)) {
            $all_ok = false;
        }
        $this->view->isWritable = $isWritable;

        // inclued apache modules
        $ap_modules = apache_get_modules();
        if (false === ($apache_modules['mod_rewrite'] = array_search('mod_rewrite', $ap_modules))) {
            $all_ok = false;
            $required_ok = false;
        }
        $this->view->apache_modules = $apache_modules;


        $this->view->all_ok = $all_ok;
        $this->view->required_ok = $required_ok;
    }

    /**
     * ファイルやディレクトリに書き込み権限があるかどうか調べる
     *
     * @param String $filename 
     * @author Takayuki Otake
     */
     private function _isWritable($filename)
     {
         $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $filename;
         return is_writable($path);
     }

}


