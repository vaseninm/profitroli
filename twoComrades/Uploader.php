<?php

namespace twoComrades;

class Uploader {

    const DB_NAME = 'files';

    const DB_COLUMN_ID = 'id';
    const DB_COLUMN_NAME = 'name';
    const DB_COLUMN_SIZE = 'size';
    const DB_COLUMN_UPLOAD_TIME = 'upload_time';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_SIZE = 'size';
    const COLUMN_UPLOAD_TIME = 'upload_time';
    const COLUMN_SERVER_PATH = 'server_path';

    /**
     * @var string
     */
    protected $uploadDir = null;
    /**
     * @var integer
     */
    protected $maxSize = null;
    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var mysqli
     */
    protected $mysqli = null;

    /**
     * @param array $file
     *
     * @return integer
     */
    public function upload(array $file) {
        if ($file['error'])
            throw new \Exception('Возникла ошибка с кодом ' . $file['error'] . '. Подробнее http://php.net/manual/ru/features.file-upload.errors.php');

        $extension = pathinfo($file['tmp_name'], PATHINFO_EXTENSION);

        if (! in_array($extension, $this->extensions))
            throw new \Exception('Неразрешенное расширение файла ' . $extension . '.');

        $filename = $file['name']; //@todo уникальное имя, например uniqid() или $id . $name
        $filePath = $this->uploadDir . $filename;

        if (! move_uploaded_file($file['tmp_name'], $filePath))
            throw new \Exception('Ошибка при сохранение файла.');

        if (isset($_SESSION[ini_get(session.upload_progress.prefix) . $_POST[ini_get(session.upload_progress.name)]])) {
            $progress = $_SESSION[ini_get(session.upload_progress.prefix).$_POST[ini_get(session.upload_progress.name)]];

            $time = time() - $progress['start_time'];

            //@todo решение немного костыльное, тк в если будет несколько файлов в форме, товремя будет некорректно
        } else {
            $time = 0;
        }

        $this->mysqli->query('INSERT INTO ' . Uploader::DB_NAME . ' SET
            ' . Uploader::DB_COLUMN_NAME . ' = ' . $this->mysqli->real_escape_string($filename) . ',
            ' . Uploader::DB_COLUMN_SIZE . ' = ' . (int) $file['size'] . ',
            ' . Uploader::DB_COLUMN_UPLOAD_TIME . ' = ' . (int) $time . '
        ');

        return $this->mysqli->insert_id;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function info($id) {
        $result = $this->mysqli->query('SELECT * FROM ' . Uploader::DB_NAME . ' WHERE id = ' . (int) $id);
        $info = $result->fetch_assoc();
        $result->free();

        return [
            Uploader::COLUMN_ID => $info[Uploader::DB_COLUMN_ID],
            Uploader::COLUMN_NAME => $info[Uploader::DB_COLUMN_NAME],
            Uploader::COLUMN_SIZE => $info[Uploader::DB_COLUMN_SIZE],
            Uploader::COLUMN_UPLOAD_TIME => $info[Uploader::DB_COLUMN_UPLOAD_TIME],
            Uploader::COLUMN_SERVER_PATH => $this->uploadDir . $info[Uploader::DB_COLUMN_NAME],
        ];
    }

    /**
     * @param mysqli $mysqli
     * @return $this
     */
    public function setMysqli(mysqli $mysqli) {
        $this->mysqli = $mysqli;

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setUploadDir($dir) {
        $this->uploadDir = $dir;

        return $this;
    }

    /**
     * @param integer $size
     * @return $this
     */
    public function setMaxSize($size) {
        $this->maxSize = $size;

        return $this;
    }

    /**
     * @param array $extension
     * @return $this
     */
    public function setExtensions(array $extension) {
        $this->extensions = $extension;

        return $this;
    }

    /**
     * @param $extension
     * @return $this
     */
    public function addExtension($extension) {
        if (! in_array($extension, $this->extensions)) $this->extensions[] = $extension;

        return $this;
    }
} 