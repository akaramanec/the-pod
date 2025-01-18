<?php

namespace backend\modules\media\widgets;

use yii\base\Widget;
use yii\bootstrap4\Html;

class ImgSaveWidget extends Widget
{
    public $model;
    public $multiple;
    public $modal_id;

    public function run()
    {
        return $this->render('img-save', [
            'model' => $this->model,
            'multiple' => $this->multiple,
            'modal_id' => $this->modal_id,
        ]);
    }

    public static function mainImg($target = 'save_main_img', $title = 'Загрузить фото')
    {
        return Html::button($title, [
            'class' => 'btn btn-outline-primary btn-mod-img',
            'title' => $title,
            'data-toggle' => 'modal',
            'data-target' => '#' . $target,
        ]);
    }

    public static function multipleImg()
    {
        return Html::button('Multiple Img', [
            'class' => 'btn btn-outline-primary btn-block btn-mod-img',
            'title' => 'Множественные изображения',
            'data-toggle' => 'modal',
            'data-target' => '#save_multiple_img',
        ]);
    }
}
