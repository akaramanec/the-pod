<?php



/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenu */

$this->title = 'Update Bot Menu: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bot Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>


