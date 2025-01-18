<?php

use yii\helpers\Html;

$this->title = $name;
?>
<div class="container" style="margin-top: 150px;">
    <div class="row">
        <div class="col-sm-12">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="alert alert-danger">
                <?= nl2br(Html::encode($message)) ?>
            </div>
        </div>
    </div>
</div>
