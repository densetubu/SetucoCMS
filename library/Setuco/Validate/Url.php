<?php
/**
 * 文字列がURLなのかを調べるバリデータークラスです
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package     Setuco
 * @subpackage  Validate
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package     Setuco
 * @subpackage  Validate
 * @author      suzuki-mar
 */


class Setuco_Validate_Url extends Zend_Validate_Abstract
{
    /**
     * エラーメッセージのキー
     *
     * @var string
     */
    const NOT_URL = 'notURL';

    /**
     * エラーメッセージの配列
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_URL => 'URLを入力してください。'
    );

    /**
     * データがURL形式の文字列かをチェックする
     *
     * @param string $checkData チェックするデータ
     * @return boolean URL形式の文字列か
     */
    public function isValid($checkData)
    {
        $checkData = (string) $checkData;
        $this->_setValue($checkData);

        $validator = new Zend_Validate_Callback(array('Zend_Uri', 'check'));

        if ($validator->isValid($checkData)) {
            return true;
        } 
        
        $this->_error(self::NOT_URL);
        return false;
    }
}

