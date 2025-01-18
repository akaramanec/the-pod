<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\customer\models\BloggerWithdrawalRequest */

$this->title = 'Create Blogger Withdrawal Request';
$this->params['breadcrumbs'][] = ['label' => 'Blogger Withdrawal Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blogger-withdrawal-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
