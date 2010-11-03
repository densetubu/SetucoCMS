<?php
/**
 * 管理側のページを管理するコントローラー。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author	    akitsukaa     
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author	    akitsukaa 
 */
class Admin_PageController extends Setuco_Controller_Action_AdminAbstract
{
    /** 
     * ページの一覧表示のアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

    }

    /** 
     * ページ新規作成フォームのアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function formAction()
    {
        $form = $this->_createForm();
        $this->view->form = $form;
    }
    
    private function _createForm()
    {
        $form = new Setuco_Form();
        $form->enableDojo()
             ->setAction($this->_helper->url('create'))
             ->addElement(
                 'Submit',
                 'sub_open',
                 array(
                     'label' => '公開して保存',
                 )
             )
             ->addElement(
                 'Submit',
                 'sub_draft',
                 array(
                     'label' => '下書きして保存'
                 )
             )
             ->addElement(
                 'Text',
                 'page_title',
                 array(
                     'id' => 'page_title',
                     'value' => '',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Select',
                 'category_id',
                 array(
                     'multiOptions' => array(
                         '選択肢1' => '紳助さん'
                     )
                 )
             )
             ->addElement(
                 'Editor',
                 'page_contents',
                 array(
                     'id' => 'page_contents',
                     'plugins' => array(
                        'undo',         // 戻す
                        'redo',         // やり直し
                        'cut',          // 切り取り
                        'copy',         // コピー
                        'paste',        // ペースト
                        'selectAll',    // 全て選択
                        'bold',         // 太字
                        'italic',       // イタリック
                        'underline',    // 下線
                        'strikethrough',        // 取り消し線
                        'subscript',    // 下付き文字
                        'superscript',  // 上付き文字
                        'subscript',    // 下付き文字
                        'superscript',  // 上付き文字
                        'removeFormat', // 形式の除去
                        'insertOrderedList',    // 番号付きリスト
                        'insertUnorderedList',  // 黒丸付きリスト
                        'insertHorizontalRule', // 水平罫線
                        'indent',       // インデント
                        'outdent',      // アウトインデント
                        'justifyLeft',  // 左揃え
                        'justifyRight', // 右揃え
                        'justifyCenter', // 中央揃え
                        'justifyFull',  // 両端揃え
                        'createLink',   // リンクの作成
                        'unlink',       // リンクの除去
                        'delete',       // 削除
                        'toggleDir',    // 方向の切り替え
                        'foreColor',    // テキストの色
                        'hiliteColor',  // マーカー(背景の色)
                        'fontSize',     // サイズ
                        'formatBlock',  // フォーマット
                        'insertImage',  // イメージの挿入
                        'fullscreen',   // フルスクリーン
                        'viewsource',   // HTMLソース表示
                        'print',        // 印刷
                        'newpage',      // 新規ページ
                     ),
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Text',
                 'page_outline',
                 array(
                     'id' => 'page_outline',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'Text',
                 'tag',
                 array(
                     'id' => 'tag',
                     'validators' => array(
                         'notEmpty'
                     ),
                     'filters' => array(
                         'StringTrim'
                     )
                 )
             )
             ->addElement(
                 'DateTextBox',
                 'create_date',
                 array(
                     'id' => 'create_date',
                 )
             )
             ->addElement(
                 'TimeTextBox',
                 'create_time',
                 array(
                     'id' => 'create_time',
                 )
             );
        $form->setMinimalDecoratorElements(array(
            'sub_open',
            'sub_draft',
            'page_title',
            'category_id',
            'page_outline',
            'tag',
        ));
        $form->removeDecoratorsOfElements(
            array (
                'Errors',
                'Description',
                'HtmlTag',
                'Label',
            ),
            array(
                'page_contents',
                'create_date',
                'create_time'
            )
        );
        return $form;
    }

    /** 
     * ページを新規作成する
     * indexアクションに遷移します
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/page/form');        
    }

    /** 
     * 作成したページを公開前にプレビューするアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function previewAction()
    {
    }

    /** 
     * ページを更新処理するアクション
     * indexアクションに遷移します ※
     * ※ただしスケルトンのときだけ
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/page/index');        
    }

    /** 
     * ページを削除するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/page/index');        
    }

}

