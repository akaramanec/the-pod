<?php

namespace backend\modules\media\models;

use Yii;
use yii\helpers\Url;

class File
{
    public static function main($entity, $id, $name_file)
    {
        if ($name_file) {
            $entityImg = Yii::createObject([
                'class' => FileInit::class,
                'entity' => $entity,
            ]);
            $fullPath = $entityImg->path . $id . DIRECTORY_SEPARATOR . $name_file;
            if (is_file($fullPath)) {
                return $entityImg->relativePathUrl . $id . DIRECTORY_SEPARATOR . $name_file;
            }
        }
        return null;
//        return Html::img(Yii::$app->params['imgUrl'] . self::cache($entity, $id, $name_img, $size), ['width' => $width, 'id' => 'mainImg']);
    }
}