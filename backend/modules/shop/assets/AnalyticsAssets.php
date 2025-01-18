<?php

namespace backend\modules\shop\assets;

use backend\assets\ChartAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class AnalyticsAssets extends AssetBundle
{
    public $sourcePath = '@backend/modules/shop/assets';

    public $css = [
        'css/analytic.css',
    ];
    public $js = [
        'js/analytic.js'
    ];
    public $depends = [
//        YiiAsset::class,
//        BootstrapPluginAsset::class,
        ChartAsset::class
    ];

    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}