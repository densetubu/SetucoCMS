<?php
/**
 * pagebreak(more)の部分を詳細ページへのリンクに変換します
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Contents_Convert
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki_mar
 */

/**
 * 
 * @package    Setuco
 * @subpackage Contents_Convert
 * @author     suzuki_mar
 */
class Setuco_Contents_Convert_PageBreak implements Setuco_Contents_ConvertInterface
{
    /**
     * ベースとなるURL
     * この部分に /id/{id}を追加する
     */
    private $_baseUrl;

    /**
     * 置換する文字列
     * この部分を詳細へのリンクに置き換える
     */
    private $_pageBreakString;

    /**
     * 詳細へのリンク
     * この部分をaタグの文書にする
     */
    private $_replacementString;

    /**
     * この文字以下の場合は、変換しない
     */
    private $_replaceStringLength;

    
    const DEFAULT_PAGE_BREAK_STRING = 'pagebreak';
    const DEFAULT_REPLACEMENT_STRING = 'もっと読む';
    const DEFAULT_REPLACEMENT_STRING_LENGTH = 300;

    public function  __construct()
    {
        $this->_pageBreakString     = self::DEFAULT_PAGE_BREAK_STRING;
        $this->_replacementString   = self::DEFAULT_REPLACEMENT_STRING;
        $this->_replaceStringLength = self::DEFAULT_REPLACEMENT_STRING_LENGTH;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
    }

    public function setPageBreakString($pageBreak)
    {
        $this->_pageBreakString = $pageBreak;
    }

    public function setReplaceMentString($replacementString)
    {
        $this->_replacementString = $replacementString;
    }

    public function setReplaceStringLength($replaceStringLength)
    {
        $this->_replaceStringLength = $replaceStringLength;
    }


    /**
     * pagebreak(more)を記事を変換する
     *
     * pagebreakの部分が記事への詳細に置き換わる
     *
     * @param array $entry 記事情報
     * @return array 記事情報
     * @author suzuki-mar
     */
    public function convert(array $entry)
    {
        if (!$this->_isConvertLength($entry['contents'])) {
            return $entry;
        }


        $pattern = "/\<\!\-\- {$this->_pageBreakString} \-\-\>.*/";
        $replacement = "<a href=\"{$this->_baseUrl}/id/{$entry['id']}\">{$this->_replacementString}</a>";

        $entry['contents'] = preg_replace($pattern, $replacement, $entry['contents']);


        return $entry;
    }

    /**
     * テキストが変換する文字サイズ以上か
     *
     * @param string $contents 文字サイズを調べるテキスト
     * @return boolean 変換する文字サイズか
     * @author suzuki-mar
     */
     private function _isConvertLength($contents)
     {
        $text = str_replace("<!-- {$this->_pageBreakString} -->", '', $contents);

        return (mb_strlen($text, 'utf-8') >= $this->_replaceStringLength);
     }

}
