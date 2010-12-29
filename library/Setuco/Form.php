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
     * 当インスタンスのdojoを有効にする
     *
     * @return 当インスタンス
     * @author Yuu Yamanaka
     */
    public function enableDojo() {
        Zend_Dojo::enableForm($this);
        return $this;
    }

    /**
     * 全てのID属性に末尾文字列を付加します。
     * Form自体のID属性が未指定の場合は"setuco_form"を代用します。
     *
     * @param string $suffix 付加する文字列
     * @return Setuco_Form 当インスタンス
     * @author charlesvineyard
     */
    public function addAllIdSuffix($suffix) {
        $formId = is_null($this->getId()) ? 'setuco_form' : $this->getId();
        $this->setAttrib('id', $formId . $suffix);

        foreach ($this->getElements() as $element) {
            $element->setAttrib('id', $element->getId() . $suffix);
        }

        return $this;
    }

}
