<?php
/**
 * ページに関する定数
 *
 * LICENSE: ライセンスに関する情報
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