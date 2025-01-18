<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\notification\models\db\BotNotification */

$this->title = 'Создать уведомление';
$this->params['breadcrumbs'][] = ['label' => 'Bot Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

