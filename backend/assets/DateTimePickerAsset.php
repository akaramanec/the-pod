<?php

namespace backend\assets;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
    ];
    public $js = [
        'bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
        'bootstrap-datetimepicker/locales/bootstrap-datetimepicker.ru.js',
        'bootstrap-datetimepicker/locales/bootstrap-datetimepicker.uk.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
