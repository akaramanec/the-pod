<?php

namespace frontend\assets;

use yii\web\AssetBundle;


class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'assets/css/vendors.css',
        'assets/css/app.css',
        'assets/css/style.css?v=1.2'
    ];
    public $js = [
        'assets/js/vendors.js',
        'assets/js/app.js',
        'assets/js/cart.js',
        'assets/js/main.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
