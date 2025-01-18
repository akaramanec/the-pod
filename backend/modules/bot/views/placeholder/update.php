<?php


/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotPlaceholder */

$this->title = 'Форма';
$this->params['breadcrumbs'][] = ['label' => 'Bot Placeholders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
