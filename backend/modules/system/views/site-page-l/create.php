<?php

use backend\modules\system\models\Addition;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\SitePageL */
/* @var $addition Addition */

$this->title = 'Создать содержание';
$this->params['breadcrumbs'][] = ['label' => 'Содержание страниц', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
    'addition' => $addition
]) ?>

