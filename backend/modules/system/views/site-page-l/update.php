<?php

use backend\modules\system\models\Addition;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\SitePageL */
/* @var $addition Addition */

$this->title = 'Обновить содержание: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Содержание страниц', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_form', [
    'model' => $model,
    'addition' => $addition
]) ?>
