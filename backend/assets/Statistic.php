<?php
namespace backend\assets;

use yii\web\AssetBundle;

class Statistic extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    //https://www.amcharts.com/demos/multiple-value-axes/#code
    public $js = [
        '//www.amcharts.com/lib/4/core.js',
        '//www.amcharts.com/lib/4/charts.js',
        '//www.amcharts.com/lib/4/themes/animated.js',
        'js/amcharts4/lang/ru_RU.js',
        'js/statistic.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
