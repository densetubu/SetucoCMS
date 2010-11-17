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
     * サムネイル保存ディレクトリのbaseUrl用パス。
     */
    const THUMB_DIR_PATH_FROM_PUBLIC = '/images/media/thumbnail/';

    /**
     * PDFファイル用アイコンファイルのパス
     */
    const ICON_PATH_PDF = '/images/admin/media/icn_pdf.gif';

    /**
     * TXTファイル用アイコンファイルのパス
     */
    const ICON_PATH_TXT = '/images/admin/media/icn_txt.gif';

    /**
     * メディア表のDAO
     * 
     * @var Common_Model_DbTable_Media
     */
    private $_mediadao = null;

    /**
     * サムネイル保存ディレクトリの物理絶対パス
     * @var string
     */
    private $_thumbnailDirectoryFQPath = '';

    /**
     * サムネイルの表示幅、標準値
     * @var int
     */
    private $_thumbnailWidth = null;

    /**
     * コンストラクター。DAOのインスタンスを初期化する
     *
     * @param string $thumbnailDirectoryFQPath サムネイル保存ディレクトリの物理絶対パス
     * @param int $thumbnailWidth サムネイルの標準表示幅
     * @return void
     * @author akitsukada
     */
    public function __construct($thumbnailDirectoryFQPath, $thumbnailWidth)
    {
        $this->_mediadao = new Common_Model_DbTable_Media();
        $this->_thumbnailDirectoryFQPath = $thumbnailDirectoryFQPath;
        $this->_thumbnailWidth = $thumbnailWidth;
    }

    /**
     * Media表から、絞込み条件とページネーターのカレントページにしたがって$limit件（オフセット$currentPage-1）のデータを取得する
     *
     * @param    array $condition    「'type'：ファイル種別,'sort'：ソートキー項目,'order'：ソート順」の連想配列
     * @param    int   $pageNumber   ページネーター用の、現在表示したいページ番号
     * @param    int   $limit        ページネーター用の、1ページに表示する最大件数
     * @return   array 取得したデータを格納した二次元配列
     * @author   akitsukada
     */
    public function findMedias($condition, $pageNumber, $limit)
    {

        $select = $this->_mediadao->select()
                        ->order("{$condition['sort']} {$condition['order']}")
                        ->limitPage($pageNumber, $limit);

        if ($condition['type'] !== 'all') {
            // 拡張子絞り込み指定されていた場合のみWhere句を設定
            $select->where('type = ?', $condition['type']);
        } else {
            $select->where('type != ?', 'new');
        }

        $medias = $this->_mediadao->executeSelect($select)->toArray();
        return $this->_addThumbnailPathInfo($medias); // サムネイルのパス情報を追加した配列をreturn
    }

    /**
     * データベースから取得したMediaデータの、ファイル種別に応じてサムネイルのパス情報を付加する
     * 
     * @param array $medias DBからfetchAllしてきたデータ
     * @return array サムネイル情報付加済みの配列
     * @author akitsukada
     */
    private function _addThumbnailPathInfo(array $medias)
    {
        $thumbUrl = '';
        foreach ($medias as $cnt => $media) {
            switch ($media['type']) {
                case 'pdf' :
                    $thumbUrl = self::ICON_PATH_PDF;
                    $medias[$cnt]['thumbWidth'] = $this->_thumbnailWidth;
                    break;
                case 'txt' :
                    $thumbUrl = self::ICON_PATH_TXT;
                    $medias[$cnt]['thumbWidth'] = $this->_thumbnailWidth;
                    break;
                case 'jpg' : // 以下の３種類の場合はまとめて処理
                case 'gif' :
                case 'png' :
                    $thumbUrl = self::THUMB_DIR_PATH_FROM_PUBLIC . $media['id'] . '.gif';
                    $thumbImage = imagecreatefromgif($this->_thumbnailDirectoryFQPath . '/' . $media['id'] . '.gif');
                    $thumbWidth = imagesx($thumbImage);
                    $medias[$cnt]['thumbWidth'] = $this->_thumbnailWidth > $thumbWidth ? $thumbWidth : $this->_thumbnailWidth;
                    break;
            }
            $medias[$cnt]['thumbUrl'] = $thumbUrl;
        }
        return $medias;
    }

    /**
     * ファイルシステム上の画像ファイル絶対パスからサムネイルを生成し保存する
     * 
     * @param string $imagePath ファイルシステム上に保存された（アップロードされた）画像ファイルの絶対パス
     * @return boolean サムネイル生成、保存に成功したらtrue,失敗ならfalse
     */
    public function saveThumnailFromImage($imagePath)
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
        $thumbWidth = $this->_thumbnailWidth;
        $rate = $thumbWidth / $originalWidth;
        $thumbHeight = $originalHeight * $rate;

        // もし元画像が十分に小さければそのサイズのままサムネイルにする
        if ($originalWidth < $thumbWidth && $originalHeight < $thumbHeight) {
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
        $thumbPath = $this->_thumbnailDirectoryFQPath . '/' . $imageInfo['filename'] . '.gif';
        imagegif($thumbImage, $thumbPath . '');

        // 画像オブジェクト破棄
        imagedestroy($originalImage);
        imagedestroy($thumbImage);
    }

    /**
     * DBのMedia表から、指定したIDのレコードを削除する
     *
     * @param  int        $id 削除したいファイルのID
     * @return boolean    true:削除成功、false:削除失敗
     * @todo   page_media 表からの子レコード削除
     * @author akitsukada
     */
    public function deleteMediaById($id)
    {
        return $this->_mediadao->deleteById($id);
    }

    /**
     * DBのMedia表から、条件に合うファイルの件数をカウントする
     *
     * @param    array $condition 条件を指定する配列
     * @return   int カウント結果の件数
     * @author   akitsukada
     */
    public function countMedias($ext = null)
    {
        return $this->_mediadao->count($ext);
    }

    /**
     * 受け取ったファイルの情報で、Media表の指定されたIDのレコードを更新する
     *
     * @param  array $mediaInfo 更新対象のレコードを「カラム名 => 値」で表現した連想配列
     * @return boolean 更新に成功したらtrue、失敗したらfalse
     * @author akitsukada
     */
    public function updateMediaInfo($id, $mediaInfo)
    {
        // DBにデータを登録
        //アップデートする条件のwhere句を生成する
        $where = $this->_mediadao->getAdapter()->quoteInto("id = ?", (int)$id); 
        if ($this->_mediadao->update($mediaInfo, $where) == 1) { // 更新した行数として必ず1か0が返ってくる
            return true;
        } else {
            return false;
        }
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

        $media = $this->_mediadao->find($id)->toArray();
        return $media[0];
    }

    /**
     * ファイルの新規登録のため、Media表を確認して新しいメディアIDを採番し取得する
     *
     * @return mixed ID取得成功時は新規登録用のID、失敗時はfalse
     * @author akitsukada
     */
    public function createNewMediaID()
    {

        // nameとtypeは一時的な名前、create_dateやupdate_dateは現在時刻のレコード
        $newRec = array(
            'name' => 'tmpName',
            'type' => 'new',
            'create_date' => date("Y-m-d H:i:s", time()),
            'update_date' => date("Y-m-d H:i:s", time()),
        );

        $result = $this->_mediadao->insert($newRec);

        return $result;
    }

}
