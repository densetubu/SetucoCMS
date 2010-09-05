<?php
/**
 * PageテーブルのDAOクラスです。
 *
 * @category   Setuco
 * @package    Common_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @since      File available since Release 0.1.0
 * @author     mitchang
 */
class Common_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
	/*
	 * 	テーブル名
	 *	@var String
	 */
	protected $_name = 'page';
	
	/*
	 *	プライマリキーのフィールド名
	 *	@var String
	 */
	protected $_primary = 'id';
}

