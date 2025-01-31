<?php

namespace backend\modules\media\models;

use src\helpers\DieAndDumpHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class ImgSave extends Behavior
{
    public $mainImg;
    public $multiImg = [];
    public $entityImg;
    public $sizeOriginal = 1500;
    private $_pathId;
    private $_entityImg;

    public function events()
    {
        if (Yii::$app->controller->action->id == 'create') {
            return [
                ActiveRecord::EVENT_AFTER_INSERT => 'saveImg',
                ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ];
        }
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveImg',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function saveImg()
    {
        $this->_entityImg = Yii::createObject([
            'class' => ImgInit::class,
            'entity' => $this->entityImg,
        ]);
        $this->_pathId = $this->_entityImg->path . $this->owner->id . DIRECTORY_SEPARATOR;
        BaseFileHelper::createDirectory($this->_pathId, 0777);
        $this->owner->mainImg = UploadedFile::getInstance($this->owner, 'mainImg');
        if (isset($this->owner->mainImg)) {
            $this->mainImg();
        }
        $this->owner->mainImg = null;
        $this->owner->multiImg = UploadedFile::getInstances($this->owner, 'multiImg');
        if (isset($this->owner->multiImg)) {
            $this->multiImg();
        }
    }

    public function mainImg()
    {
        @unlink($this->_pathId . $this->owner->img);
        $name_img = 'm' . time() . '.' . $this->owner->mainImg->extension;
        $fullPath = $this->_pathId . $name_img;
        $this->owner->mainImg->saveAs($fullPath);
        $this->upload($fullPath);
        $this->_entityImg->saveMainImg($this->owner->id, $name_img);
        @chmod($fullPath, 0777);
    }

    public function multiImg()
    {
        $x = 1;
        foreach ($this->owner->multiImg as $file) {
            $name_img = 'i' . time() . $x . '.' . $file->extension;
            $fullPath = $this->_pathId . $name_img;
            $file->saveAs($fullPath);
            $this->upload($fullPath);
            $img = new Images();
            $img->entity_id = $this->owner->id;
            $img->entity = $this->entityImg;
            $img->img = $name_img;
            $img->sort = $x;
            $img->save();
            $x++;
            @chmod($fullPath, 0777);
        }
    }

    public function upload($fullPath)
    {
        $size = getimagesize($fullPath);
        $widthOriginal = $size[0];
        $heightOriginal = $size[1];
        $image = Yii::createObject([
            'class' => SimpleImage::className(),
        ]);
        $image->load($fullPath);
        if ($heightOriginal <= $widthOriginal) {
            $image->resizeToWidth($this->sizeOriginal);
        } else {
            $image->resizeToHeight($this->sizeOriginal);
        }
        $image->save($fullPath);
    }

    public function afterDelete()
    {
        $this->_entityImg = Yii::createObject([
            'class' => ImgInit::class,
            'entity' => $this->entityImg,
        ]);
        BaseFileHelper::removeDirectory($this->_entityImg->path . $this->owner->id);
    }


    public static function uploadStatic($fullPath)
    {
        $size = getimagesize($fullPath);
        $widthOriginal = $size[0];
        $heightOriginal = $size[1];
        $image = Yii::createObject([
            'class' => SimpleImage::class,
        ]);
        $image->load($fullPath);
        if ($heightOriginal <= $widthOriginal) {
            $image->resizeToWidth(1500);
        } else {
            $image->resizeToHeight(1500);
        }
        $image->save($fullPath);
    }
}
