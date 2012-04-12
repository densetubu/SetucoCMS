<?php
/**
 * 閉じていないタグが存在すればタグを閉じる
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
 * @category    Setuco
 * @package     Setuco
 * @subpackage  Filter
 * @license     http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright   Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar
 */

/**
 * @package     Setuco
 * @subpackage  Filter
 * @author      suzuki-mar
 */
class Setuco_Filter_ModifiedUnclosedHtmlTag implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $text フィルタをかける文字列
     * @return string タグを閉じていないものが存在すればタグを閉じます
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($text)
    {
        if (preg_match("/<.*?>/", $text) === 0) {
            return $text;
        }

        $html = $this->_convertTagToHtml($text);

        $doc    = new DOMDocument();
        $doc->loadHTML($html);
        $validityHtml   = $doc->saveHTML();

        return  $this->_clipContentTag($validityHtml);;
    }

    /**
     * htmlの断片を完全なHTMLに修正する
     *
     * @param string $tag HTMLの断片
     * @return string 完全なHTML
     * @author suzuki-mar
     */
    private function _convertTagToHtml($tag)
    {
       //metaタグがないと文字化けになってしまう
        $head = '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head>';
        $html = "<html>{$head}\n<body>{$tag}</body></html>";
        return $html;
    }

    /**
     * コンテンツのタグを取り出す
     *
     * @param string $html コンテンツのタグを取り出すHTML
     * @return string 必要のない文字を消した物
     * @author suzuki-mar
     */
    private function _clipContentTag($html)
    {
        $contentTag = str_replace(
                '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">' . "\n",
                '',
                $html);

        $contentTag = str_replace(
                '<html>', '',
                $contentTag);

        $contentTag = str_replace(
                '<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>', '',
                $contentTag);

        $contentTag = str_replace(
                '<body>', '',
                $contentTag);

        $contentTag = str_replace(
                '</body>', '',
                $contentTag);

        $contentTag = str_replace(
                '</html>', '',
                $contentTag);

        return trim($contentTag);
    }
}