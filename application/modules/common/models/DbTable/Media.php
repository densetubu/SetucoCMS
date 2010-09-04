<?php
/**
 * MediaテーブルのDAOクラスです。
 *
 * @category   Setuco
 * @package    Common_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @since      File available since Release 0.1.0
 * @author     mitchang
 */
class Common_Model_DbTable_Media extends Zend_Db_Table_Abstract
{
	/*
	 * 	テーブル名
	 *	@var String
	 */
	protected $_name = 'media';
	
	/*
	 *	プライマリキーのフィールド名
	 *	@var String
	 */
	protected $_primary = 'id';
}

