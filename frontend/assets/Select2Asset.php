<?php

namespace frontend\assets;

use yii\web\AssetBundle;


class Select2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'lib/select2/dist/css/select2.css',
        'lib/select2-bootstrap4/dist/select2-bootstrap4.min.css'
    ];
    public $js = [
        'lib/select2/dist/js/select2.full.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
