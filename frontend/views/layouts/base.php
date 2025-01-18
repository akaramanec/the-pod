<?php
/* @var $content string */

use yii\bootstrap4\Breadcrumbs;

?>
<?php $this->beginContent('@frontend/views/layouts/main.php') ?>
<div class="main-base">
    <div class="container-fluid">
        <div class="row">
            <?= \common\widgets\Alert::widget(); ?>
            <div class="col-sm-12">
                <?= Breadcrumbs::widget([
                    'encodeLabels' => false,
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
        </div>
    </div>
    <?= $content ?>
</div>
<?php $this->endContent() ?>
