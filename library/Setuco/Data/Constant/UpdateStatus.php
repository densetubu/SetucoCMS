<?php
/**
 * 更新状況に関する定数
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