<?php

namespace backend\modules\media\widgets;

use yii\base\Widget;
use yii\bootstrap4\Html;

class FileSaveWidget extends Widget
{
    public $model;
    public $multiple;
    public $modal_id;

    public function run()
    {
        return $this->render('file-save', [
            'model' => $this->model,
            'multiple' => $this->multiple,
            'modal_id' => $this->modal_id,
        ]);
    }

    public static function mainFile($target = 'save_main_file', $title = 'Файл')
    {
        return Html::button($title, [
            'class' => 'btn btn-outline-primary btn-block btn-mod-file',
            'title' => $title,
            'data-toggle' => 'modal',
            'data-target' => '#' . $target,
        ]);
    }

    public static function multipleFile()
    {
        return Html::button('Загрузка файлов', [
            'class' => 'btn btn-outline-primary btn-block btn-mod-file',
            'title' => 'Загрузка файлов',
            'data-toggle' => 'modal',
            'data-target' => '#save_multiple_file',
        ]);
    }
}
