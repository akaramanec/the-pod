<?php

/* @var $this \yii\web\View */

/* @var $content string */


use frontend\assets\AppAsset;
use yii\bootstrap4\Html;


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MKW9BP5');
    </script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="IlYL2_xkD6hKUD-az1JKrfbEKEpUVDSS153Hzt1xWcc" />
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="<?= \src\helpers\Common::canonicalUrl() ?>"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= Yii::$app->site->microMarking ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MKW9BP5"
            height="0" width="0" style="display:none;visibility:hidden">
    </iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?= \common\widgets\Alert::widget(); ?>
<?= $content ?>
<?php require('_footer.php') ?>
<?php require('_cart_modal.php') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
