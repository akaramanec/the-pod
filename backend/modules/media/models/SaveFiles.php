<?php

namespace backend\modules\media\models;

use Yii;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class SaveFiles
{
    private $_id;
    private $_model;
    private $_path;
    private $_table;

    public function __construct($conf = [])
    {
        $this->_id = $conf['id'];
        $this->_model = $conf['model'];
        $this->setFilesPath();
        $this->setFilesTable();
        $this->files();
    }

    private function files()
    {
        BaseFileHelper::createDirectory($this->_path);
        $this->_model->files = UploadedFile::getInstances($this->_model, 'files');

        if (isset($this->_model->files)) {
            $this->save();
        }
    }

    private function save()
    {
        $x = 1;
        foreach ($this->_model->files as $file) {
            $name = 'i' . time() . $x;
            $fullPath = $this->_path . $name . '.' . $file->extension;
            $file->saveAs($fullPath);

            Yii::$app->db->createCommand("INSERT INTO $this->_table SET id=:id, name=:name, original_name=:original_name")
                ->bindValue(':id', $this->_id)
                ->bindValue(':name', $name . '.' . $file->extension)
                ->bindValue(':original_name', $file->name)
                ->execute();

            $x++;
        }
    }

    private function setFilesPath()
    {
        if ($this->_model instanceof \src\interfaces\SaveFiles) {
            return $this->_path = $this->_model->getFilesPath() . $this->_id . DIRECTORY_SEPARATOR;
        }
        throw new \Exception('\src\interfaces\SaveFiles');
    }

    private function setFilesTable()
    {
        if ($this->_model instanceof \src\interfaces\SaveFiles) {
            return $this->_table = $this->_model->getFilesTable();
        }
        throw new \Exception('\src\interfaces\SaveFiles');
    }
}