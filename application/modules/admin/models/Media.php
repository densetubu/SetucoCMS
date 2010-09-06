<?php
/**
 * 管理側のファイル管理用サービス
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
 * @author     akitsukada
 */

/**
 * ファイル管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     akitsukada
 */
class Admin_Model_Media
{
    

    /**
     * メソッドの説明
     *
     * @param    型 $hoge 説
     * @param    型 $foo 説
     * @param    型 $bar 説
     * @return ＜型 説明 | void＞
     * @author   akitsukada
     */
    public function findMedias($condition, $currentPage, $limit)
    {
        
        $res = array();
        $start = ($currentPage - 1) * $limit + 1;
        $end = $start + $limit;
        $allCount = $this->countMedias($condition);
        $types = array('jpg', 'gif', 'png', 'pdf', 'txt');
        
       
        for ($i = $start; $i < $end && $i <= $allCount; $i++) {
    
            $type = $types[$i % sizeof($types)] ;

            if ($condition['type'] != 'all' && $type != $condition['type']) {
               
                continue;
            }
            
            $res[$i] = array(
                'id'          => $i,
                'name'        => '表示名' . ($i),
                'type'        => $type, 
                'createDate' => "2010-08-23 05:01:11",
                'updateDate' => "2010-08-24 05:01:11",
                'comment'     => '表示名' . ($i). 'の説明'
            );
                
            $createDate = strptime($res[$i]['createDate'], "%Y-%m-%d %H:%M:%S");
            $res[$i]['createDate'] = $createDate['tm_mon'] . '月' . $createDate['tm_mday'] . '日';
            
            $updateDate = strptime($res[$i]['updateDate'], "%Y-%m-%d %H:%M:%S");
            $res[$i]['updateDate'] = $updateDate['tm_mon'] . '月' . $updateDate['tm_mday'] . '日';
            
            // ファイルタイプによってふさわしい一覧表示用のアイコンを準備
            switch ($res[$i]['type']) {
                case 'txt':
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_txt.gif';
                    break;
                case 'pdf':
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_pdf.gif';
                    break;
                case 'jpg':
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_img.png';
                    break;
                case 'gif':
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_img.png';
                    break;
                case 'png':
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_img.png';
                    break;
                default:
                    $res[$i]['iconUrl'] = '/media/thumbnail/thumb_img.png';
                    break;
            }
        }

        // Zend_Db_Select, fetchAll　した状態の配列を返す
        return $res;

    }


    /**
     * メソッドの説明
     *
     * @param    型 $hoge 説
     * @param    型 $foo 説
     * @param    型 $bar 説
     * @return ＜型 説明 | void＞
     * @author   akitsukada
     */
    public function countMedias($condition)
    {
        // SELECT count(*) FROM media;
        return 23;
    }

    /**
     * 
     * @param $filename
     */
    public function saveUploadedMedia($filename) 
    {
        return true;
    }
    
    /**
     * IDを指定してファイル一件のデータを取得
     * @param $id
     */
    public function findMediaById($id)
    {
        
        $types = array('jpg', 'gif', 'png', 'pdf', 'txt');
    
        $type = $types[$id % sizeof($types)] ;
        $res = array(
            'id'          => $id,
            'name'        => '表示名' . ($id),
            'type'        => $type, 
            'createDate' => "2010-08-23 05:01:11",
            'updateDate' => "2010-08-24 05:01:11",
            'comment'     => '表示名' . ($id) . 'の説明'
        );
        
        return $res;

    }
    
}
