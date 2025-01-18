<?php

namespace backend\assets;

use yii\web\AssetBundle;

class VueAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Montserrat:300,400,500,600',
        'html-template/app-assets/vendors/css/vendors.min.css',
        'html-template/app-assets/vendors/css/charts/apexcharts.css',
        'html-template/app-assets/vendors/css/extensions/tether-theme-arrows.css',
        'html-template/app-assets/vendors/css/extensions/tether.min.css',
        'html-template/app-assets/vendors/css/extensions/shepherd-theme-default.css',
        'html-template/app-assets/css/bootstrap-extended.css',
        'html-template/app-assets/css/colors.css',
        'html-template/app-assets/css/components.css',
        'html-template/app-assets/css/themes/dark-layout.css',
        'html-template/app-assets/css/themes/semi-dark-layout.css',
        'html-template/app-assets/css/core/menu/menu-types/vertical-menu.css',
        'html-template/app-assets/css/core/colors/palette-gradient.css',
        'html-template/app-assets/css/pages/dashboard-analytics.css',
        'html-template/app-assets/css/pages/card-analytics.css',
        'html-template/app-assets/css/plugins/tour/tour.css',
        'html-template/app-assets/css/core/menu/menu-types/vertical-menu.css',
        'html-template/app-assets/css/core/colors/palette-gradient.css',
        'html-template/app-assets/css/plugins/file-uploaders/dropzone.css',
        'html-template/app-assets/css/pages/data-list-view.css',

//        'css/all.min.css',
//        'css/style.css',
    ];
    public $js = [
        'html-template/app-assets/vendors/js/vendors.min.js',
        'html-template/app-assets/vendors/js/charts/apexcharts.min.js',
        'html-template/app-assets/vendors/js/extensions/tether.min.js',
        'html-template/app-assets/vendors/js/extensions/shepherd.min.js',
        'html-template/app-assets/js/core/app-menu.js',
        'html-template/app-assets/js/core/app.js',
        'html-template/app-assets/js/scripts/components.js',
        'html-template/app-assets/js/scripts/pages/dashboard-analytics.js',
        'html-template/app-assets/vendors/js/extensions/dropzone.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/datatables.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/datatables.checkboxes.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js',
        'html-template/app-assets/vendors/js/tables/datatable/dataTables.select.min.js',
        'html-template/app-assets/js/scripts/ui/data-list-view.js'
//        'js/main.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}
