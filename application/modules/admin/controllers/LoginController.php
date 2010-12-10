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
class Admin_LoginController extends Setuco_Controller_Action_Abstract
{
    /**
     * 初期化処理
     *
     * @return void
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_setLayoutName('layout-login');
    }

    /**
     * ログインフォーム
     *
     * @return void
     * @author Yuu Yamanaka
     */
    public function indexAction()
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
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
        if (!$authModel->login(
                $form->getValue('login_id'),
                $form->getValue('password'))
        ) {
            $this->_setParam('form', $form);
            $form->addError('アカウントIDまたはパスワードが間違っています。');
            return $this->_forward('index');
        }
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
        $this->_helper->flashMessenger('ログアウトしました。');
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
        $form->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->setAction($this->_helper->url('auth'))
             ->addDecorator('FormElements')
             ->addDecorator('Form');
        $accountId = new Zend_Form_Element_Text('login_id', array('label' => 'アカウントID'));
        $accountId->setRequired(true)
                  ->setValidators($this->_makeLoginIdValidators())
                  ->setFilters(array('StringTrim'))
                  ->clearDecorators()
                  ->setDisableLoadDefaultDecorators(true)
                  ->addDecorator('ViewHelper')
                  ->addDecorator('HtmlTag', array('tag' => 'dd'))
                  ->addDecorator('Label', array('tag' => 'dt'));
        $password = new Zend_Form_Element_Password('password', array('label' => 'パスワード'));
        $password->setRequired(true)
                  ->setValidators($this->_makePasswordValidators())
                  ->clearDecorators()
                 ->setDisableLoadDefaultDecorators(true)
                 ->addDecorator('ViewHelper')
                 ->addDecorator('HtmlTag', array('tag' => 'dd'))
                 ->addDecorator('Label', array('tag' => 'dt'));
        $submit = new Zend_Form_Element_Submit('sub');
        $submit->setLabel('ログイン')
               ->clearDecorators()
               ->setDisableLoadDefaultDecorators(true)
               ->addDecorator('ViewHelper')
               ->addDecorator('HtmlTag', array('tag' => 'p'));
        $form->addElements(array(
            $accountId,
            $password,
            $submit
        ));
        return $form;
    }

    /**
     * アカウントID用のバリデーターを作成する。
     *
     * @return array Zend_Validate インターフェースの配列
     * @author charlesvineyard
     */
    private function _makeLoginIdValidators()
    {
        $validators[] = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('アカウントIDを入力してください。');
        $validators[] = $notEmpty;

        return $validators;
    }

    /**
     * パスワード用のバリデーターを作成する。
     *
     * @return array Zend_Validate インターフェースの配列
     * @author charlesvineyard
     */
    private function _makePasswordValidators()
    {
        $validators[] = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('パスワードを入力してください。');
        $validators[] = $notEmpty;

        return $validators;
    }
}

