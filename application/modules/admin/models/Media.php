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
     * Media表から、絞込み条件とページネーターのカレントページにしたがって$limit件のデータを取得する
     *
     * @param    array $condition 		「'type'：ファイル種別,'sort'：ソートキー項目,'order'：ソート順」の連想配列
     * @param    int   $currentPage 	ページネーター用の、現在表示したいページ番号
     * @param    int  	$limit	 		ページネーター用の、1ページに表示する最大件数
     * @return 	  array	取得したデータを格納した二次元配列
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
     * Media表から、指定したIDのレコードを削除する
     * 
     * @param  int 		$id 削除したいファイルのID
     * @return boolean	true:削除成功、false:削除失敗
     * @author akitsukada
     */
    public function deleteMedia($id) 
    {
        return true;
    }
    
    
    /**
     * Media表から、条件に合うファイルの件数をカウントする
     *
     * @param    array $condition 条件を指定する配列
     * @return   int カウント結果の件数
     * @author   akitsukada
     */
    public function countMedias($condition = null)
    {
        // SELECT count(*) FROM media;
        $fileCount = 38; // 現在はスタブなので適当な数字
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
     * 受け取ったファイルの情報でMedia表を更新する（１件）
     * 
     * @param  array $mediaInfo 更新対象のレコードを「カラム名 => 値」で表現した連想配列
     * @return boolean true:更新成功、false:更新失敗
     * @author akitsukada
     */
    public function updateMediaInfo($mediaInfo) 
    {
        // DBにデータを登録
        
        //var_dump($mediaInfo); exit();
        return true;

    }
    

    /**
     * Media表からIDを指定してファイル一件のデータを取得する
     * 
     * @param  int $id 取得したいファイル（メディア）のID
     * @return mixed 取得したファイルのデータを格納した配列。取得失敗時はnullを返す。
     * @author akitsukada
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
     * ファイルの新規登録のため、Media表を確認して新しいメディアIDを採番し取得する
     * 
     * @return int 新規登録用のメディアID 
     * @author akitsukada
     */
    public function createNewMediaID()
    {
        // DBを見て新しいメディアIDを取得して返す
        return time() % 60; // 適当に60程度まで
    }
        
 ######################## 以下スタブ用擬似DAO ###############################
 
    /**
     * スタブ専用の暫定メソッド。DAOの代わりにmedia表の擬似データを全件作って返す
     * 
     * @todo   DAOの実装が進んだら削除する
     * @author akitsukada
     */
    private function _mediaDao_SelectAll() 
    {

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
                    $thumb = '/media/thumbnail/thumb_img.png';
                    break;
                case 'pdf' :
                    $thumb = '/images/media/icn_pdf.gif';
                    break;
                case 'txt' :
                    $thumb = '/images/media/icn_txt.gif';
                    break;
            }
            
            $res[$i] = array(
                'id'         => $i,
                'name'       => '表示名' . ($i),
                'type'       => $type, 
                'createDate' => "2010-08-23 05:01:" . sprintf("%02d", 59 - $i),
                'updateDate' => "2010-08-24 05:01:" . sprintf("%02d", $i),
                'comment'    => '表示名' . ($i). 'の説明',
                'thumbUrl'	 => $thumb
            );
            
        }

        // Zend_Db_Select, fetchAll　した状態の配列を返す
        return $res;   
    }
}
