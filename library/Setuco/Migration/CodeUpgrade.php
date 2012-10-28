<?php

class Setuco_Migration_CodeUpgrade
{

    private
    $_savePath,
    $_downloadUrl,
    $_extractPath,
    $_targetPath;

    
    public function __construct(array $params)
    {
        if (!isset($params['save_path'])) {
            throw new InvalidArgumentException('ダウンロードするディレクトリを指定してください:インスタンス生成時にsave_pathを指定して下さい');
        }

        if (!isset($params['download_url'])) {
            throw new InvalidArgumentException('ダウンロードしてくるURLを指定してください:インスタンス生成時にdownload_urlを指定して下さい');
        }

        if (!isset($params['extract_path'])) {
            throw new InvalidArgumentException('解凍するディレクトリを指定してください:インスタンス生成時にextract_pathを指定して下さい');
        }

        if (!isset($params['target_path'])) {
            throw new InvalidArgumentException('アップグレード先のファイルを指定してください:インスタンス生成時にtarget_pathを指定して下さい');
        }


        $this->_savePath = $params['save_path'];
        $this->_downloadUrl = $params['download_url'];
        $this->_extractPath = $params['extract_path'];
        $this->_targetPath = $params['target_path'];
    }

    public function checkConfig()
    {
        if (!@fopen($this->_downloadUrl, "r")) {
            throw new InvalidArgumentException("{$this->_downloadUrl}というURLは存在しません");
        }

        if (!is_writable($this->_savePath)) {
            throw new InvalidArgumentException("{$this->_savePath}は書き込み権限がありません");
        }

        $this->_checkDirExists($this->_targetPath);
    }

    public function download($saveName)
    {
        $putsDir = "{$this->_savePath}/{$saveName}";
        $resource = file_get_contents($this->_downloadUrl);

        return (file_put_contents($putsDir, $resource) !== false);
    }

    public function searchFilePaths()
    {
        $filePathList = array();
        foreach ($this->_getFilePathsByDirPath($this->_targetPath) as $filePath) {

            if (is_dir($filePath)) {
                $filePathList = array_merge($filePathList, $this->_searchFilePathByDirPath($filePath));
            } else {
                $filePathList[] = $filePath;
            }
        }

        return $filePathList;
    }

    public function searchSecretFilePathsByPath($dirPath)
    {
        $this->_checkDirExists($dirPath);

        $filePathList = array();
        foreach (glob("{$dirPath}/.*") as $filePath) {

            //ディレクトリは取得しない
            if (!is_dir($filePath)) {
                $filePathList[] = $filePath;
            }
        }

        return $filePathList;
    }

    public function searchAllFilePaths()
    {
        $filePathList = $this->searchFilePaths();

        $dirAllPaths = array();
        foreach ($filePathList as $filePath) {
            $dirAllPaths[] = dirname($filePath);
        }

        $dirPaths = array_unique($dirAllPaths);

        foreach ($dirPaths as $dirPath) {
            $filePathList = array_merge($filePathList, $this->searchSecretFilePathsByPath($dirPath));
        }

        return $filePathList;
    }

    public function checkWritePermissionFileList(array $fileList)
    {
        foreach ($fileList as $fileName) {
            if (!is_writable($fileName)) {
                throw new InvalidArgumentException("{$fileName}には書き込み権限がありません");
            }
        }

        return true;
    }

    public function isOverWriteFile($targetFile, $compareFile)
    {
        if (!file_exists($targetFile)) {
            throw new InvalidArgumentException("置換する{$targetFile}というファイルが存在しません");
        }

        if (!file_exists($compareFile)) {
            throw new InvalidArgumentException("置換先の{$compareFile}というファイルが存在しません");
        }

        return (filesize($targetFile) !== filesize($compareFile));
    }

    private function _searchFilePathByDirPath($dirPath)
    {
        $filePathList = array();

        foreach ($this->_getFilePathsByDirPath($dirPath) as $filePath) {

            if (is_dir($filePath)) {
                $filePathList = array_merge($filePathList, $this->_searchFilePathByDirPath($filePath));
            } else {
                $filePathList[] = $filePath;
            }
        }

        return $filePathList;
    }

    private function _getFilePathsByDirPath($dirPath)
    {
        if (!preg_match("/\/$/", $dirPath)) {
            $dirPath .= '/';
        }

        return glob("{$dirPath}*");
    }

    private function _checkDirExists($dirPath)
    {
        if (!file_exists($dirPath)) {
            throw new InvalidArgumentException("{$dirPath}というディレクトリは存在しません");
        }
    }

}
