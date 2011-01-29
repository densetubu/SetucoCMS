<?php

/**
 * キーワードの文字数と個数をチェックする
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Validate
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco_Validate
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
 */
class Setuco_Validate_KeywordLength extends Zend_Validate_Abstract
{
    const INVALID = 'stringLengthInvalid';
    const TOO_STRING_SHORT = 'stringLengthTooShort';
    const TOO_STRING_LONG = 'stringLengthTooLong';
    const TOO_KEYWORD_LOW = 'keywordCountTooLow';
    const TOO_KEYWORD_MUCH = 'keywordCountTooMuch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "不正な文字です。",
        self::TOO_STRING_SHORT => "キーワードは、%min%文字以上で入力してください。",
        self::TOO_STRING_LONG => "キーワードは、%max%文字以下で入力してください。",
        self::TOO_KEYWORD_LOW => "キーワードは、%count_min%個以上で入力してください。",
        self::TOO_KEYWORD_MUCH => "キーワードは、%count_max%個以下で入力してください。",
    );
    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min',
        'max' => '_max',
        'count_min' => '_count_min',
        'count_max' => '_count_max',
    );
    /**
     * キーワードの最小文字数
     *
     * @var int
     */
    protected $_min = null;
    /**
     * キーワードの最大文字数
     *
     * @var int
     */
    protected $_max = null;
    /**
     * キーワードの最小個数
     *
     * @var int
     */
    protected $_count_min = null;
    /**
     * キーワードの最大個数
     *
     * @var int
     */
    protected $_count_max = null;
    /**
     * キーワードの区切り文字 デフォルトは','
     *
     * @var string
     */
    protected $_delimiter = ',';
    /**
     * 文字列をチェックするバリデータクラス
     *
     * @var Zend_Validate_StringLength
     */
    protected $_stringValidate;

    /**
     * インスタンス変数の設定をする
     * 引数は配列のみ対応
     *
     * @param array インスタンスに設定するパラメーター
     * @author suzuki-mar
     */
    public function __construct(array $options)
    {

        //変数名だけ追加すればパラメータを設定できるようにするために
        foreach ($options as $key => $value) {

            $variableName = "_{$key}";

            if (isset($this->$variableName) || is_null($this->$variableName)) {
                $this->$variableName = $value;
            }
        }

        $this->_checkParams();

        //StringLengthでパラメータのチェックをしてもらう
        $this->_stringValidate = new Zend_Validate_StringLength(array(
                    'max' => $this->_max,
                    'min' => $this->_min
                ));
    }

    /**
     * パラメーターのチェックをする
     *
     * @return boolean パラメーターが正しいか
     * @throws Setuco_Exception パラメータが不正の場合
     * @author suzuki-mar
     */
    private function _checkParams()
    {
        if ($this->_count_min > $this->_count_max) {
            throw new Setuco_Exception('最大個数(count_max)より最小個数(count_min)のほうが大きいです。');
        }

        if (!(is_numeric($this->_count_min) || is_null($this->_count_min))) {
            throw new Setuco_Exception('count_minは数字で指定してください');
        }

        if (!(is_numeric($this->_count_max) || is_null($this->_count_max))) {
            throw new Setuco_Exception('count_maxは数字で指定してください');
        }

        return true;
    }

    /**
     * キーワードのチェックをする
     *
     * @param string $targetString チェックするキーワード
     * @return キーワードが正しいかどうか
     * @author suzuki-mar
     */
    public function isValid($targetString)
    {
        $keywordLists = explode($this->_delimiter, $targetString);

        if (count($keywordLists) < $this->_count_min) {
            $this->_error(self::TOO_KEYWORD_LOW);
        }

        if (count($keywordLists) > $this->_count_max) {
            $this->_error(self::TOO_KEYWORD_MUCH);
        }

        foreach ($keywordLists as $value) {
            if (!$this->_stringValidate->isValid($value)) {
                $stringErrorMessages = $this->_stringValidate->getMessages();
                $stringErrorTypes = array_keys($stringErrorMessages);
                if ($stringErrorTypes[0] === Zend_Validate_StringLength::TOO_LONG) {
                    $this->_error(self::TOO_STRING_LONG);
                    break;
                } elseif ($stringErrorTypes[0] === Zend_Validate_StringLength::TOO_SHORT) {
                    $this->_error(self::TOO_STRING_SHORT);
                    return false;
                } else {
                    $this->_error(self::INVALID);
                    break;
                }
            }
        }

        if (count($this->_messages)) {
            return false;
        } else {
            return true;
        }
    }

}

