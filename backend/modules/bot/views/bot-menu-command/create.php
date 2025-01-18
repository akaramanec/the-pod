<?php



/* @var $this yii\web\View */
/* @var $model backend\modules\bot\models\BotMenuCommand */

$this->title = 'Create Bot Menu Command';
$this->params['breadcrumbs'][] = ['label' => 'Bot Menu Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

