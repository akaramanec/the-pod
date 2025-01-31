<?php

namespace backend\modules\media\models;

use src\helpers\DieAndDumpHelper;
use Yii;
use src\helpers\appUrl;
use Imagine\Image\ManipulatorInterface;
use yii\bootstrap4\Html;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;

class Img
{

    public static function cache($entity, $id, $name_img, $size)
    {
        if (!$name_img) {
            return self::pathNoImg();
        }
        $entityImg = Yii::createObject([
            'class' => ImgInit::class,
            'entity' => $entity,
        ]);
        $fullPath = $entityImg->path . $id . DIRECTORY_SEPARATOR . $name_img;
        $fullPathCache = $entityImg->pathCache . $id . DIRECTORY_SEPARATOR . $size . '_' . $name_img;
        $relativePathCache = $entityImg->relativePathCache . $id . DIRECTORY_SEPARATOR . $size . '_' . $name_img;
        if (is_file($fullPathCache)) {
            return $relativePathCache;
        }
        if (is_file($fullPath)) {
            $createFolder = $entityImg->pathCache . $id;
            BaseFileHelper::createDirectory($createFolder);
            $s = explode('x', $size);
            Image::$thumbnailBackgroundColor = 'FFFFFF';
            Image::thumbnail($fullPath, $s[0], $s[1], $mode = ManipulatorInterface::THUMBNAIL_INSET)
                ->save($fullPathCache, ['quality' => 100]);
            return $relativePathCache;
        }
        return self::pathNoImg();
    }

    public static function main($entity, $id, $name_img, $size, $width = '100%')
    {
        if ($name_img) {
            return Html::img(Yii::$app->params['imgUrl'] . self::cache($entity, $id, $name_img, $size), ['width' => $width, 'id' => 'mainImg']);
        } else {
            return Html::img(Yii::$app->params['imgUrl'] . self::pathNoImg(), ['width' => $width]);
        }
    }

    public static function mainPath($entity, $id, $name_img, $size, $width = '100%')
    {
        if ($name_img) {
            return Yii::$app->params['imgUrl'] . self::cache($entity, $id, $name_img, $size);
        } else {
            return self::pathNoImg();
        }
    }

    public static function images($entity, $id, $images, $size, $width = '100%')
    {
        $img = '<div id="input_multiple_block">';
        foreach ($images as $item) {
            $img .= '<div class="img-item">' . self::main($entity, $id, $item->img, $size, $width) . self::delete_icon($item->entity_id, $entity) . '</div>';
        }
        return $img . '</div>';
    }

    public static function delete_icon($entity_id, $entity)
    {
        return Html::a('<i class="fas fa-times"></i>', ['/media/img/delete-img', 'entity_id' => $entity_id, 'entity' => $entity], [
            'class' => 'btn btn-outline-dark delete-img',
            'role' => 'button',
            'title' => 'Удалить изображения',
            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
            'data-method' => 'post'
        ]);
    }

    public static function deleteMainImg($entity_id, $entity)
    {
        return Html::a('<i class="fas fa-times"></i>', ['/media/img/delete-main-img', 'entity_id' => $entity_id, 'entity' => $entity], [
            'class' => 'btn btn-outline-dark delete-img',
            'role' => 'button',
            'title' => 'Удалить изображения',
            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
            'data-method' => 'post'
        ]);
    }

    public static function pathNoImg()
    {
        return '/bot-3.png';
    }

    public static function i($img, $size)
    {
        if ($img) {
            return Yii::$app->params['imgUrl'] . self::cache($img->entity, $img->entity_id, $img->img, $size);
        } else {
            return Yii::$app->params['imgUrl'] . self::pathNoImg();
        }
    }

    public static function iVb($img, $size)
    {
        if ($img) {
            return self::cache($img->entity, $img->entity_id, $img->img, $size);
        } else {
            return self::pathNoImg();
        }
    }

    public static function fullPath($img, $size)
    {
        if ($img) {
            return Yii::$app->params['imgUrl'] . self::cache($img->entity, $img->entity_id, $img->img, $size);
        } else {
            return Yii::$app->params['imgUrl'] . self::pathNoImg();
        }
    }

    public static function fullPathMain($entity, $model, $size)
    {
        if ($model->img) {
            return Yii::$app->params['imgUrl'] . self::cache($entity, $model->id, $model->img, $size);
        } else {
            return Yii::$app->params['imgUrl'] . self::pathNoImg();
        }
    }

    public static function review($review)
    {
        if ($review->img) {
            return Yii::$app->params['imgUrl'] . '/review_slider/' . $review->id . '/' . $review->img;
        } else {
            return Yii::$app->params['imgUrl'] . self::pathNoImg();
        }
    }

    public static function productImageWithCheckSupportExtension($image, $size)
    {
        if (Img::checkSupportExtension($image)) {
            $img = Img::fullPath($image, $size);
        } else {
            $img = Img::product($image);
        }
        return $img;
    }

    public static function product($product)
    {
        if (isset($product->img)) {
            return Yii::$app->params['imgUrl'] . '/product/' . $product->id . '/' . $product->img;
        } else {
            return Yii::$app->params['imgUrl'] . self::pathNoImg();
        }
    }

    public static function checkSupportExtension($image)
    {
        if (isset($image->img) && $image->img) {
            $extension = explode('.', $image->img);
            if (isset($extension[1]) && $extension[1] && in_array(strtolower($extension[1]), self::supportExtensions())) {
                return true;
            }
        }
        return false;
    }

    public static function supportExtensions()
    {
        return ['gif', 'jpeg', 'jpg', 'png', 'wbmp', 'xbm', 'bmp', 'webp'];
    }
}
