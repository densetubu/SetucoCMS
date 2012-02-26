<?php
/**
 * ページの状態を変更するフォーム
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
 * @subpackage Form_Page
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Form_Page
 * @author     charlesvineyard
 */
class Setuco_Form_Page_StatusUpdate extends Setuco_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
        $urlHelper = new Zend_Controller_Action_Helper_Url();

        $this->setAction($urlHelper->simple('update-status'))
            ->addElement(
                'Select',    // selected 指定はビューでする
                'status',
                array(
                    'id'           => 'status',
                    'required'     => true,
                    'onchange'     => 'showPageElementEdit(this);',
                    'multiOptions' => Setuco_Data_Constant_Page::ALL_STATUSES(),
                    'decorators'   => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_id_s',
                array(
                    'id'         => 'h_page_id_s',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_page_title_s',
                array(
                    'id'         => 'h_page_title_s',
                    'required'   => true,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Hidden',
                'h_pre_category_id_s',
                array(
                    'id'         => 'h_pre_category_id_s',
                    'required'   => false,
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'Submit',
                'sub_status',
                array(
                    'id'         => 'sub_status',
                    'label'      => '変更',
                    'decorators' => array('ViewHelper')
                )
            )
            ->addElement(
                'button',
                'cancel_status',
                array(
                    'id'         => 'cancel_status',
                    'label'      => 'キャンセル',
                    'onclick'    => 'hidePageElementEdit(this);',
                    'decorators' => array('ViewHelper')
                )
            );
    }
}