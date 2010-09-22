<?php
/**
 * ログイン処理をするコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka
 */

/**
 * ログイン処理をするコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     Yuu Yamanaka
 */
class Admin_LoginController extends Setuco_Controller_Action_Admin
{
    /**
     * ログインフォーム
     *
     * @return void
     * @author Yuu Yamanaka
     */
    public function indexAction()
    {
        $this->view->errors = $this->_getParam('errors');
        $this->view->form = $this->_getParam('form', $this->_createLoginForm());
    }

    /**
     * ログイン処理
     * indexコントローラーのindexアクションに遷移します
     *
     * @return void
     * @author Yuu Yamanaka
     */
    public function authAction()
    {
        $form = $this->_createLoginForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('form', $form);
            return $this->_forward('index');
        }

        $authModel = new Admin_Model_Auth();
        if (!$authModel->login($form->getValue('loginId'),
                    $form->getValue('password'))) {
            $this->_setParam('form', $form);
            $this->_setParam('errors',
                    array('ログインIDまたはパスワードが間違っています'));
            return $this->_forward('index');
        };
        $this->_helper->redirector('index', 'index');
    }

    /**
     * ログアウト処理
     * indexアクションに遷移します
     *
     * @return void
     * @author Yuu Yamanaka
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }

    /**
     * ログインフォームオブジェクトを作成して返す
     *
     * @return Setuco_Form
     * @author Yuu Yamanaka
     */
    private function _createLoginForm()
    {
        $form = new Setuco_Form();
        $form->setMethod('post')
             ->setAction($this->_helper->url('auth'));
        $form->getDecorator('HtmlTag')->setOption('class', 'straight');

        $form->addElement('text', 'loginId', array(
                    'label'    => 'アカウント名',
                    'required' => true,
                    'filters'  => array('StringTrim')
                    ));
        $form->addElement('password', 'password', array(
                    'label'    => 'パスワード',
                    'required' => true
                    ));
        $form->addElement('submit', 'submit', array(
                    'label'    => 'ログイン'
                    ));

        // デコレータの調整
        $form->setMinimalDecoratorElements('submit');

        return $form;
    }

    /**
     * レイアウト名を取得します。
     *
     * @return string レイアウト名
     * @author charlesvineyard
     */
    protected function getLayoutName()
    {
        return 'layout-login';
    }
}

