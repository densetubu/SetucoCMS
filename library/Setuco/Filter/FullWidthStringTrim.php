<?php
/**
 * StringTrimフィルターのラッパー。全角スペースのトリミングに対応。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage    Filter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     akitsukada
 */

/**
 * @package    Setuco
 * @subpackage    Filter
 * @author     akitsukada
 */
class Setuco_Filter_FullWidthStringTrim extends Zend_Filter_StringTrim
{

    /**
     * StringTrimのtrim対象文字に半角／全角スペースを指定する
     *
     * @param  string|array|Zend_Config $charList
     * @return void
     */
    public function __construct($charList = ' 　')
    {
        parent::__construct($charList);
    }

    /**
     * StringTrimの_unicodeTrimメソッド、全角スペース対応版
     *
     * @param string $value
     * @param string $charlist
     * @return string
     */
    protected function _unicodeTrim($value, $charlist = '\\\\s')
    {
        $chars = preg_replace(
            array( '/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'),
            array( '\\\\\\0', '\\', '\/' ),
            $charlist
        );

        $pattern = '^[' . $chars . ']*|[' . $chars . ']*$';
        return preg_replace("/$pattern/sSDu", '', $value);
    }
}