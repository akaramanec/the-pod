<?php

namespace backend\modules\media\models;

use src\helpers\DieAndDumpHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class FileSave extends Behavior
{
    public $mainFile;
    public $multiFile = [];
    public $entityFile;
    private $_pathId;
    private $_entityImg;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveFile',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function saveFile()
    {
        $this->_entityImg = Yii::createObject([
            'class' => FileInit::class,
            'entity' => $this->entityFile,
        ]);
        $this->_pathId = $this->_entityImg->path . $this->owner->id . DIRECTORY_SEPARATOR;

        $this->owner->mainFile = UploadedFile::getInstance($this->owner, 'mainFile');
        if (isset($this->owner->mainFile)) {
            $this->mainFile();
        }
        $this->owner->mainFile = null;
        $this->owner->multiFile = UploadedFile::getInstances($this->owner, 'multiFile');
        if (isset($this->owner->multiFile)) {
            $this->owner->multiFile();
        }
    }

    public function mainFile()
    {
        @unlink($this->_pathId . $this->owner->video);
        $name_file = 'f' . time() . '.' . $this->owner->mainFile->extension;
        $fullPath = $this->_pathId . $name_file;
        $this->owner->mainFile->saveAs($fullPath);
        $this->_entityImg->saveMain($this->owner->id, $name_file);
        @chmod($fullPath, 0777);
    }

    public function multiFile()
    {
        $x = 1;
        foreach ($this->owner->multiFile as $file) {
            $name_file = 'i' . time() . $x . '.' . $file->extension;
            $fullPath = $this->_pathId . $name_file;
            $file->saveAs($fullPath);
            $f = new Files();
            $f->entity_id = $this->owner->id;
            $f->entity = $this->entityFile;
            $f->file = $name_file;
            $f->name = $file->baseName;
            $f->sort = $x;
            $f->save();
            $x++;
            @chmod($fullPath, 0777);
        }
    }

    public function afterDelete()
    {
        $this->_entityImg = Yii::createObject([
            'class' => FileInit::class,
            'entity' => $this->entityFile,
        ]);
        BaseFileHelper::removeDirectory($this->_entityImg->path . $this->owner->id);
    }
}
