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
     * メディア表のDAO
     *
     * @var unknown_type
     */
    private $_dao = null;

    const ICON_PATH_IMG = '/media/thumbnail/';
    const ICON_PATH_PDF = '/images/media/icn_pdf.gif';
    const ICON_PATH_TXT = '/images/media/icn_txt.gif';
    private $_thumbnailDirectory = null;
    private $_thumbnailWidth = null;

    /**
     * コンストラクター。DAOのインスタンスを初期化する
     *
     *
     */
    public function __construct($thumbnailDirectory, $thumbnailWidth)
    {
        $this->_dao = new Common_Model_DbTable_Media();
        $this->_thumbnailDirectory = $thumbnailDirectory;
        $this->_thumbnailWidth = $thumbnailWidth;
    }

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

        $select = $this->_dao->select()
                             ->order("{$condition['sort']} {$condition['order']}")
                             ->limitPage($currentPage, $limit);

        if ($condition['type'] !== 'all') {
            // 拡張子絞り込み指定されていた場合のみWhere句を設定
            $select->where('type = ?', $condition['type']);
        } else {
            $select->where('type != ?', 'new');
        }

        $medias = $this->_dao->executeSelect($select)->toArray();
        return $this->_addThumbnailInfo($medias); // サムネイルのパス情報を追加した配列をreturn

    }

    private function _addThumbnailInfo(array $medias)
    {
        $thumbUrl = '';
        foreach ($medias as $cnt => $media) {
            switch ($media['type']) {
                case 'pdf' :
                    $thumbUrl = self::ICON_PATH_PDF;
                    break;
                case 'txt' :
                    $thumbUrl = self::ICON_PATH_TXT;
                    break;
                case 'jpg' : // 以下の３種類の場合はまとめて処理
                case 'gif' :
                case 'png' :
                    $thumbUrl = self::ICON_PATH_IMG . $media['id'] . '.gif';
                    $thumbImage = imagecreatefromgif(APPLICATION_PATH . '/../public' . self::ICON_PATH_IMG . $media['id'] . '.gif');
                    $thumbWidth = imagesx($thumbImage);
                    $medias[$cnt]['thumbWidth'] = $this->_thumbnailWidth > $thumbWidth ? $thumbWidth : $this->_thumbnailWidth;
                    break;
            }
            $medias[$cnt]['thumbUrl'] = $thumbUrl;
        }
        return $medias;
    }

    public function saveThumnailFromImage($imagePath, $thumbWidth)
    {
        
        // アップロードされた画像のオブジェクトを保持
        $originalImage = null;
        
        // 透過色情報
        $transIndex = 0;
        $transColor = null;
        
        // 画像のパスからイメージオブジェクト取得
        $imageInfo = pathinfo($imagePath);
        $ext = $imageInfo['extension'];
        switch ($ext) {
            case 'jpg' :
                $originalImage = imagecreatefromjpeg($imagePath);
                break;
            case 'gif' :
                $originalImage = imagecreatefromgif($imagePath);
                break;
            case 'png' :
                $originalImage = imagecreatefrompng($imagePath);
                break;
            default :
                return false;  // 拡張子が対応画像(jpg, gif, png)でなければfalse
        }
        
        
        // 画像のオリジナルサイズ取得
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);
        
        // 比率計算＆サムネイルサイズ設定
        $rate = $thumbWidth / $originalWidth;
        $thumbHeight = $originalHeight * $rate;

        if ($originalWidth < $thumbWidth && $originalHeight < $thumbHeight) {
            // もし元画像が十分に小さければそのサイズのままサムネイルにする
            $thumbWidth = $originalWidth;
            $thumbHeight = $originalHeight;
        }
        
        // サムネイル用イメージオブジェクト生成
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        // gifかpngの場合は背景の透過処理
        if ($ext == 'gif' || $ext == 'png') { 
            $transIndex = imagecolortransparent($originalImage);
            $transColor = imagecolorsforindex($originalImage, $transIndex);
            $transIndex = imagecolorallocate($thumbImage, $transColor['red'], $transColor['green'], $transColor['blue']);
            imagefill($thumbImage, 0, 0, $transIndex);
            imagecolortransparent($thumbImage, $transIndex);
        }
        
        // 算出したサイズに李サンプリングコピー
        imagecopyresampled($thumbImage, $originalImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight); 
        // imagecopyresized($thumbImage, $originalImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);　// リサイズだけで処理すると多少粗いサムネイルになる

        // サムネイルを保存 
        $thumbPath = $this->_thumbnailDirectory . '/' . $imageInfo['filename'] . '.gif';
        imagegif($thumbImage, $thumbPath . '');
        
        // 画像オブジェクト破棄
        imagedestroy($originalImage);
        imagedestroy($thumbImage);
        
    }

    /**
     * Media表から、指定したIDのレコードを削除する
     *
     * @param  int 		$id 削除したいファイルのID
     * @return boolean	true:削除成功、false:削除失敗
     * @author akitsukada
     */
    public function deleteMediaById($id)
    {
        return $this->_dao->deleteById($id);
    }

    /**
     * Media表から、条件に合うファイルの件数をカウントする
     *
     * @param    array $condition 条件を指定する配列
     * @return   int カウント結果の件数
     * @author   akitsukada
     */
    public function countMedias($ext = null)
    {
        return $this->_dao->count($ext);

    }

    /**
     * 受け取ったファイルの情報でMedia表を更新する（１件）
     *
     * @param  array $mediaInfo 更新対象のレコードを「カラム名 => 値」で表現した連想配列
     * @return boolean true:更新成功、false:更新失敗
     * @author akitsukada
     */
    public function updateMediaInfo($id, $mediaInfo)
    {
        // DBにデータを登録
        try {

            //アップデートする条件のwhere句を生成する
            $where = $this->_dao->getAdapter()->quoteInto("id = ?", $id);

            $this->_dao->update($mediaInfo, $where);
            $result = true;

        } catch (Zend_Exception $e) {
            $result = false;
        }

        return $result;

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

        $media = $this->_dao->findById($id);
        return $media[0];
    }


    /**
     * ファイルの新規登録のため、Media表を確認して新しいメディアIDを採番し取得する
     *
     * @return int 新規登録用のメディアID
     * @author akitsukada
     */
    public function createNewMediaID()
    {

        // 新しいレコードをDBに挿入してIDを得る
        try {

            // nameとtypeは一時的な名前、create_dateやupdate_dateは現在時刻のレコード
            $newRec = array (
                'name' => 'tmpName',
                'type' => 'new',
                'create_date' => date("Y-m-d H:i:s", time()),
                'update_date' => date("Y-m-d H:i:s", time()),
            );

            $result = $this->_dao->insert($newRec);

        } catch (Zend_Exception $e) {
            $result = false;
        }

        return $result; // 適当に60程度まで
    }
}
