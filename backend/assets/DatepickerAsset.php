<?php

namespace backend\assets;

use yii\web\AssetBundle;

class DatepickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'bootstrap-datepicker/css/bootstrap-datepicker.min.css',
    ];
    public $js = [
        'bootstrap-datepicker/js/bootstrap-datepicker.min.js',
        'bootstrap-datepicker/locales/bootstrap-datepicker.ru.min.js',
        'bootstrap-datepicker/locales/bootstrap-datepicker.uk.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
