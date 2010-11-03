<?php
/**
 * SetucoCMS 用に拡張した Zend_Form です。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category    Setuco
 * @package     Form
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      Yuu Yamanaka
 */

/**
 * @category    Setuco
 * @package     Form
 * @author      Yuu Yamanaka
 */
class Setuco_Form extends Zend_Form
{
    /**
     * 最小限のデコレータのみ使うフォーム要素を指定する
     * 
     * @param mixed $elements 要素名か要素名の配列
     * @return void
     * @author Yuu Yamanaka
     */
    public function setMinimalDecoratorElements($elements)
    {
        $this->setElementDecorators(array('ViewHelper'), (array)$elements);
    }

    /**
     * デコレータを指定のフォーム要素から削除します。
     * 
     * @param mixed $decorators デコレータ名かデコレータ名の配列
     * @param mixed $elements 要素名か要素名の配列
     * @return void
     * @author charlesvineyard
     */
    public function removeDecoratorsOfElements($decorators, $elements)
    {
        foreach ((array)$elements as $element) {
            foreach ((array)$decorators as $decorator) {
                $this->getElement($element)->removeDecorator($decorator);
            }
        }
        return $this;
    }
    
    /**
     * Dojoエレメントを有効にする
     * 
     * @return 当インスタンス
     * @author Yuu Yamanaka
     */
    public function enableDojo() {
        Zend_Dojo::enableForm($this);
        return $this;
    }


    /**
     * input(select,textarea)タグだけのForm_Elementを生成する
     *
     * @param String $type Form_Elementのタイプ
     * @param String $name Form_Elementに設定する名前 inputタグのname属性にも使える
     * @return Form_Element input(select, textarea)タグだけのZend_Form_Elementを生成する
     * @author suzuki-mar
     */
    public function createElementOfViewHelper($type, $name)
    {
        //Form_Element(inputタグ）を取得する
        $element = $this->createElement($type, $name);

        //inputタグだけにする
        $element->clearDecorators()
            ->addDecorator('ViewHelper');

        //Form_Element_Submitでは、Labelの指定を無効(null)にすると、submitボタンのvalueを変更できない
        if (!$this->_isSubmitElement($element)) {
            $element->addDecorator('Label', array('tag' => null));
        }
        
        return $element;
    }

    /**
     * Elementオブジェクトが、Submitかをしらべる
     * 
     * @param Zend_Form_Element $element Form_Elementオブジェクト
     * @return boolean ElementオブジェクトがSubmitオブジェクトか
     * @author suzuki-mar
     */
    private function _isSubmitElement(Zend_Form_Element $element)
    {
        $result = (preg_match('/_Submit$/', get_class($element)) ); 
        return $result;
    }
}
