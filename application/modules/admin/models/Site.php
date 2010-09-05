<?php
/**
 * サイト情報管理サービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     ece_m
 */

/**
 * サイト情報管理クラス
 * 
 * @package    Admin
 * @subpackage Model
 * @author     ece_m
 */
class Admin_Model_Site
{
	/**
	 * サイト情報を取得する
	 * 
	 */
	public function getSiteInfo()
	{
		$result = array('name' => '日本電子専門学校 電設部?',
						'url' => 'http://design1.chu.jp/testsetuco/penguin/',
						'comment' => '日本電子専門学校電設部SetucoCMSプロジェクトです。',
						'keyword' => 'せつこ,俺だ,結婚,してくれ');
		return $result;	
	}
}

