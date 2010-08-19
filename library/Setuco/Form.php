<?php
/**
 * SetucoCMS用に拡張したZend_Form
 * 
 * @author Yuu Yamanaka
 */
class Setuco_Form extends Zend_Form
{
    /**
     * 最小限のデコレータのみ使うフォーム要素を指定する
     * 
     * @param mixed $elements 要素名か要素名の配列
     * @return void
     */
    public function setMinimalDecoratorElements($elements)
    {
        $this->setElementDecorators(array('ViewHelper'), (array)$elements);
    }
    
    public function loadDefaultDecorators()
    {
        parent::loadDefaultDecorators();
        
        // dlタグのclass属性をセット
        if ($htmlTag = parent::getDecorator('HtmlTag')) {
            $htmlTag->setOption('class', 'straight');
        }
    }
}