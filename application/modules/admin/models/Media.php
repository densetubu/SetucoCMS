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

        $medias = $this->_mediaDao_SelectAll();
        $workArray = array();
        
        // ファイル種別の絞込み
        if ($condition['type'] != 'all') {
            foreach ($medias as $i => $media) {
                if ($media['type'] == $condition['type']) {
                    array_push($workArray, $media);
                }
            }    
            $medias = $workArray;
        }
        
        
        // スタブ限定のチートソート
        if (
            ($condition['sort'] == 'name'        && $condition['order'] == 'desc') ||
            ($condition['sort'] == 'update_date' && $condition['order'] == 'asc') ||
            ($condition['sort'] == 'create_date' && $condition['order'] == 'desc') 
        ) {
            $medias = array_reverse($medias); // 逆転する
        }
        
        // ページャーの反映
        $startRecord = ($currentPage - 1) * $limit;
        
        $result = array_slice($medias, $startRecord, $limit);
        return $result;
        

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
    public function countMedias($condition = null)
    {
        // SELECT count(*) FROM media;
        $fileCount = 23; // 適当
        if ($condition == null) {
            return $fileCount;
        }
        
        if ($condition['type'] == 'all') {
            return $fileCount;
        }
        
        $medias = $this->_mediaDao_SelectAll();
        $cnt = 0;
        foreach ($medias as $i => $media) {
            if ($media['type'] == $condition['type']) {
                $cnt++;
            }
        }
        return $cnt;        
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
            'id'         => $id,
            'name'       => '表示名' . ($id),
            'type'       => $type, 
            'createDate' => "2010-08-23 05:01:11",
            'updateDate' => "2010-08-24 05:01:11",
            'comment'    => '表示名' . ($id) . 'の説明'
        );
        
        return $res;

    }
 
    /**
     * スタブ専用のメソッド。DAOの代わりにmedia表のデータを全件作って返す
     */
    private function _mediaDao_SelectAll() {

        $res = array();
        $types = array('jpg', 'gif', 'png', 'pdf', 'txt'); 
        $count = $this->countMedias();
       
        for ($i = 0; $i < $count; $i++) {
    
            $type     = $types[$i % sizeof($types)] ;
            $thumb    = '';
            switch ($type) {
                case 'jpg' :
                case 'gif' :
                case 'png' :
                    $thumb = 'thumb_img.png';
                    break;
                case 'pdf' :
                    $thumb = 'thumb_pdf.gif';
                    break;
                case 'txt' :
                    $thumb = 'thumb_txt.gif';
                    break;
            }
            
            $res[$i] = array(
                'id'         => $i,
                'name'       => '表示名' . ($i + 1),
                'type'       => $type, 
                'createDate' => "2010-08-23 05:01:" . sprintf("%02d", 59 - $i),
                'updateDate' => "2010-08-24 05:01:" . sprintf("%02d", $i),
                'comment'    => '表示名' . ($i). 'の説明',
                'thumbUrl'	 => '/media/thumbnail/' . $thumb
            );
            
        }

        // Zend_Db_Select, fetchAll　した状態の配列を返す
        return $res;
        
    }
}
