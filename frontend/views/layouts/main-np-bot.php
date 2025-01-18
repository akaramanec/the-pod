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
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<main class="main">
    <div class="container">
        <div class="np-form" style="color: white">
            <?php $this->beginBody() ?>
            <?= \common\widgets\Alert::widget(); ?>
            <?= $content ?>
            <?php $this->endBody() ?>
        </div>
    </div>
</main>
</body>
</html>
<?php $this->endPage() ?>
