<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ChartAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        'css/jquery-ui.min.css',
    ];
    public $js = [
        'js/chart/chart.min.js',
    ];
    public $depends = [
        JqueryAsset::class
    ];
}
