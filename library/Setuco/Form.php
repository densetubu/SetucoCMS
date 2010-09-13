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
     * Dojoエレメントを有効にする
     * 
     * @return void
     * @author Yuu Yamanaka
     */
    public function enableDojo() {
        Zend_Dojo::enableForm($this);
    }

    /**
     * Zend_Form_Elementを出力する
     * 指定したElementを表示する 
     * $optionsで変更できるのは、class, valueのみ
     *
     * @paran String $name Elementの名前 
     * @param array[option] $options オプションパラメーター key 変更したい属性 value 変更したい値
     * @return true
     * @author suzuki-mar
     */
    public function displayElement($name, $options = null) 
    {
        //該当するElementを取得する
        $element = $this->getElement($name);

        //存在しないElementを取得することはないので、該当するものがなかったら例外を発生させる
        if (is_null($element)) {
            throw new Zend_Exception("{$name}の要素は" . __CLASS__ . 'にはありません');
        }


        //optionが指定されている場合のみ設定する
        if (!is_null($options)) {

            //setAttribでは、指定できないものは個別指定する
            if (isset($options['value'])) {
                
                //Form_Element_Submitは、value(label)の指定の仕方が違うので判定する 
                //判定は、Submitクラスを拡張するかもしれないので、それを考慮する
                if ($this->_isSubmitElement($element))  {
                    $element->setLabel($options['value']);
                } else {
                    $element->setValue($options['value']);
                }
            }

            //オプションの指定があったら、それを使用する
            foreach ($options as $key => $value) {
                $element->setAttrib($key,  $value);
            }
        }

        //タグを出力する
        echo $element;

        return true;
    }

    /**
     * input(select,textarea)タグだけのForm_Elementを生成する
     *
     * @param String $type Form_Elementのタイプ
     * @param String $name Form_Elementに設定する名前 inputタグのname属性にも使える
     * @return Form_Element input(select, textarea)タグだけのZend_Form_Elementを生成する
     * @author suzuki-mar
     */
    public function createElementOfViewHelper($type, $param) 
    {
        //Form_Element(inputタグ）を取得する
        $element = $this->createElement($type, $param);

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
