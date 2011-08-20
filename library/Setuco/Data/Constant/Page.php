<?php
/**
 * ページに関する定数
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
 * @subpackage Data_Constant
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Data_Constant
 * @author     charlesvineyard
 */
class Setuco_Data_Constant_Page
{
    /**
     * 状態：下書き
     * 
     * @var int
     */ 
    const STATUS_DRAFT = 0;
    
    /**
     * 状態：下書きの文字列表現
     * 
     * @var string
     */ 
    const STATUS_DRAFT_STRING = '下書き';
    
    /**
     * 状態：公開
     * 
     * @var int
     */ 
    const STATUS_RELEASE = 1;

    /**
     * 状態：公開の文字列表現
     * 
     * @var string
     */ 
    const STATUS_RELEASE_STRING = '公開';
    
    /**
     * 全ての状態を取得します。
     * 
     * @return array 状態のint値と文字列の連想配列
     * @author charlesvineyard
     */
    public static function ALL_STATUSES()
    {
        return array(
            self::STATUS_DRAFT => self::STATUS_DRAFT_STRING,
            self::STATUS_RELEASE => self::STATUS_RELEASE_STRING,
        );
    }

}