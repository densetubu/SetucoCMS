<?php
/**
 * 更新状況に関する定数
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
class Setuco_Data_Constant_UpdateStatus
{
    /**
     * 更新状況：普通
     * 
     * @var int 
     */ 
    const NORMAL = 0;
    
    /**
     * 更新状況：良い
     * 
     * @var int 
     */ 
    const GOOD   = 5;

    /**
     * 更新状況：悪い
     * 
     * @var int 
     */ 
    const BAD    = -5; 
    
    /**
     * 更新状況：月初め
     * 
     * @var int 
     */ 
    const FIRST  = 100; 

}