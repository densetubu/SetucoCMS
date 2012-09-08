<?php
/**
 * 共通のファイル管理用サービス
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
 * @package    Common
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * ファイル管理クラス
 *
 * @package    Common
 * @subpackage Model
 * @author     suzuki-mar
 */
abstract class Common_Model_MediaAbstract
{
    /**
     * ファイルの新規登録中に作成する一時ファイルの名前
     * (物理ファイル名でなくmediaテーブルのname属性の値)
     */
    const TEMP_FILE_NAME = 'tmpName';

    /**
     * ファイルの新規登録中に作成する一時ファイルの拡張子
     */
    const TEMP_FILE_EXTENSION = 'new';

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
    protected $_mediaDao = null;

    /**
     * コンストラクター。DAOのインスタンスを初期化する
     *
     * @return void
     * @author akitsukada
     */
    public function __construct()
    {
        $this->_mediaDao = new Common_Model_DbTable_Media();
    }

    /**
     * Media表から、絞込み条件とページネーターのカレントページにしたがって
     * $limit件（オフセット=$currentPage-1）のデータを取得する
     *
     * @param    array $condition    「'type'：ファイル種別,'sort'：ソートキー項目,'order'：ソート順」の連想配列
     * @param    int   $pageNumber   ページネーター用の、現在表示したいページ番号
     * @param    int   $limit        ページネーター用の、1ページに表示する最大件数
     * @return   array 取得したデータを格納した二次元配列
     * @author   akitsukada
     */
    public function findMedias($sortColumn, $order, $pageNumber, $limit, $fileExt)
    {
        $medias = $this->_mediaDao->loadMedias4Pager(
                $sortColumn, $order, $pageNumber, $limit,
                $fileExt, self::TEMP_FILE_EXTENSION);
        foreach ($medias as $cnt => $media) {
            $media = $this->_addThumbPathInfo($media);
            $medias[$cnt] = $media;
        }
        return $medias; // サムネイルのパス情報を追加した配列をreturn
    }

    /**
     * データベースから取得したMediaデータの、ファイル種別に応じてサムネイルのパス情報を付加する
     *
     * @param array $media DBから取得したファイル情報１件分
     * @return array|false サムネイル情報付加済みの配列。処理に失敗したらfalse。
     * @author akitsukada
     */
    protected function _addThumbPathInfo(array $media)
    {
        $fileName = "{$media['id']}.{$media['type']}";
        $filePath = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$fileName}";
        $fileExists = file_exists($filePath);

        $media['uploadUrl'] = Setuco_Data_Constant_Media::UPLOAD_DIR_PATH_FROM_BASE . $fileName;
        $media = $this->_fixMediaPathInfo($media);

        $media['thumbUrl'] = '';
        $media['thumbWidth'] = 0;

        switch ($media['type']) {
            case 'pdf' :
                $media['thumbUrl'] = self::ICON_PATH_PDF;
                $media['thumbWidth'] = Setuco_Data_Constant_Media::THUMB_WIDTH;
                break;
            case 'txt' :
                $media['thumbUrl'] = self::ICON_PATH_TXT;
                $media['thumbWidth'] = Setuco_Data_Constant_Media::THUMB_WIDTH;
                break;
            case 'jpg' : // Fall Through 以下の３種類の場合はまとめて処理
            case 'gif' :
            case 'png' :
                if ($media['thumbExists']) {
                    $thumbName = "{$media['id']}.gif";
                    $thumbPath = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . '/' . $thumbName;
                    $thumbImage = imagecreatefromgif($thumbPath);
                    $thumbWidth = imagesx($thumbImage);
                    $media['thumbWidth'] = Setuco_Data_Constant_Media::THUMB_WIDTH > $thumbWidth ?
                            $thumbWidth : Setuco_Data_Constant_Media::THUMB_WIDTH;
                }
                $media['thumbUrl'] = Setuco_Data_Constant_Media::THUMB_DIR_PATH_FROM_BASE . $media['id'] . '.gif';
                break;
            default :
                return false;
        }
        return $media;
    }

    protected function _fixMediaPathInfo($media)
    {
        $pathinfo = pathinfo($media['uploadUrl']);
        $thumbFullPath = '';
        if (Setuco_Util_Media::isImageExtension($pathinfo['extension'])) {
            $thumbFullPath = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . '/' . $pathinfo['filename'] . '.gif';
        } elseif ($pathinfo['extension'] === 'pdf') {
            $thumbFullPath = APPLICATION_PATH . "/../public" . self::ICON_PATH_PDF;
        } elseif ($pathinfo['extension'] === 'txt') {
            $thumbFullPath = APPLICATION_PATH . "/../public" . self::ICON_PATH_TXT;
        }
        $mediaFullPath = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . "/{$pathinfo['basename']}";
        $media['mediaExists'] = file_exists($mediaFullPath);
        $media['thumbExists'] = file_exists($thumbFullPath);
        return $media;
    }

    /**
     * ファイルシステム上の画像ファイル絶対パスからサムネイルを生成し保存する
     *
     * @param string $imagePath ファイルシステム上に保存された（アップロードされた）画像ファイルの絶対パス
     * @return boolean サムネイル生成、保存に成功したらtrue,失敗ならfalse
     */
    public function saveThumbnailFromImage($imagePath)
    {
        //不正な画像データの場合はサムネイルを作成しない
        if (!Setuco_Util_Media::isValidImageData($imagePath)) {
            return false;
        }

        // アップロードされた画像のオブジェクトを保持
        $originalImage = null;

        // 透過色情報
        $transIndex = 0;
        $transColor = null;

        // 画像のパスからイメージオブジェクト取得
        $imageInfo = pathinfo($imagePath);
        $ext = Setuco_Util_Media::getImageType($imagePath);

        switch ($ext) {
            case 'jpeg' :
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
        $thumbWidth = Setuco_Data_Constant_Media::THUMB_WIDTH;
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
            $transColor = @imagecolorsforindex($originalImage, $transIndex);
            $transIndex = imagecolorallocate($thumbImage, $transColor['red'], $transColor['green'], $transColor['blue']);
            imagefill($thumbImage, 0, 0, $transIndex);
            imagecolortransparent($thumbImage, $transIndex);
        }

        // 算出したサイズにリサンプリングコピー
        imagecopyresampled($thumbImage, $originalImage, 0, 0, 0, 0,
                $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);

        // サムネイルを保存
        $thumbPath = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . '/' . $imageInfo['filename'] . '.gif';
        imagegif($thumbImage, $thumbPath . '');

        // 画像オブジェクト破棄
        imagedestroy($originalImage);
        imagedestroy($thumbImage);

        return true;
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
        return $this->_mediaDao->deleteByPrimary($id);
    }

    /**
     * DBのMedia表から、指定した拡張子のレコードを数える
     *
     * @param    string $type 拡張子の文字列。指定しなければ全てを数える。
     * @return   int カウント結果の件数
     * @author   akitsukada
     */
    public function countMediasByType($type = null)
    {
        return $this->_mediaDao->countMediasByType($type);
    }

    /**
     * 受け取ったファイルの情報で、Media表の指定されたIDのレコードを更新する
     *
     * @param  array $updateData 更新対象のレコードを「カラム名 => 値」で表現した連想配列
     * @return int 更新した行数（IDを指定しているので0か1になる）
     * @author akitsukada
     */
    public function updateMediaInfo($id, $updateData)
    {
        return $this->_mediaDao->updateByPrimary($updateData, $id);
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
        $media = $this->_mediaDao->loadByPrimary($id);
        $media = $this->_addThumbPathInfo($media);
        return $media;
    }

    /**
     * ファイルの新規登録のため、Media表を確認して新しいメディアIDを採番し取得する
     *
     * @return mixed ID取得成功時は新規登録用のID、失敗時はfalse
     * @author akitsukada
     */
    public function createNewMediaID()
    {
        // nameとtypeは一時的な名前、create_dateやupdate_dateは現在時刻の仮レコードを登録してIDを得る
        $now = new Zend_Date();
        $newRec = array(
            'name' => self::TEMP_FILE_NAME,
            'type' => self::TEMP_FILE_EXTENSION,
            'create_date' => $now->toString('yyyy-MM-dd HH:mm:ss'),
            'update_date' => $now->toString('yyyy-MM-dd HH:mm:ss')
        );
        $result = $this->_mediaDao->insert($newRec);
        return $result;
    }

    /**
     * 全てのメディアデータを取得する
     *
     * @return array メディアデータのリスト
     * @author suzuki-mar
     */
    public function findAllMedias()
    {
        return $this->_addModelDatas($this->_mediaDao->loadAllMedias());
    }

    /**
     * 画像のメディアデータを取得する
     *
     * @return array 画像のメディアデータのリスト
     * @author suzuki-mar
     */
    public function findImageMedias()
    {
        return $this->_addModelDatas($this->_mediaDao->loadImageMedias());
    }

    /**
     * レコードにサムネなどのデータを追加する
     *
     * @param $targetData サムネのデータを追加する配列
     * @return
     * @author suzuki-mar
     */
    protected function _addModelDatas($targetDatas)
    {
        $medias = array();

        foreach ($targetDatas as $cnt => $media) {
            $media = $this->_addThumbPathInfo($media);

            //不正なメディアデータは弾く
            if ($media === false) {
                continue;
            }

            $medias[] = $media;
        }



        return $medias;
    }
}
