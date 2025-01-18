<?php
/* @var $content string */

use src\helpers\Common;
use yii\bootstrap4\Breadcrumbs;

?>
<?php $this->beginContent('@blog/views/layouts/main.php') ?>
<div class="main-base">
    <div class="left_side">
        <?= $this->render("_left_side") ?>
    </div>
    <div class="container-fluid">
        <div class="row">
            <?= Common::alert4(); ?>
            <div class="col-sm-8">
                <?php if ($this->title): ?>
                    <h2 class="title-base"><?= $this->title ?></h2>
                <?php endif; ?>
                <?= Breadcrumbs::widget([
                    'encodeLabels' => false,
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
            <div class="col-sm-4">
                <?php if (isset($this->params['pagination'])): ?>
                        <?= $this->params['pagination'] ?>
                <?php endif; ?>
                <?php if (isset($this->params['right_content'])): ?>
                    <div class="btn-group base-btn-group float-right" role="group">
                        <?= $this->params['right_content'] ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<?php $this->endContent() ?>
