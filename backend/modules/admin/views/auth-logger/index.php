<?php

use src\helpers\Common;
use src\helpers\Date;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $searchModel backend\modules\admin\models\search\AuthLoggerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Auth Logger';
$this->params['breadcrumbs'][] = $this->title;
$this->params['pagination'] = \common\widgets\PaginationWidget::widget();
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>
<div class="row">
    <div class="col-md-2">
        <?= $form->field($searchModel, 'id') ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($searchModel, 'admin_id') ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($searchModel, 'controller') ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($searchModel, 'action') ?>
    </div>
    <div class="col-md-2">
        <div role="group" class="btn-group base-btn-group mt-4">
            <?= Html::submitButton('Search', ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="row">
    <div class="col-md-12">
        <?= LinkPager::widget(ArrayHelper::merge([
            'pagination' => $pages,
        ], Common::pager4())); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-hover">
            <tbody>
            <?php foreach ($models as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $item->admin_id ?></td>
                    <td><?= $item->controller ?></td>
                    <td><?= $item->action ?></td>
                    <td><?php \yii\helpers\VarDumper::dump($item->request, 1000, 5) ?></td>
                    <td><?php \yii\helpers\VarDumper::dump($item->data, 1000, 5) ?></td>
                    <td><?= Date::format_datetime_all($item->created_at) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<? //= GridView::widget([
//    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
//    'pager' => Common::pager4(),
//    'tableOptions' => ['class' => 'table'],
//    'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive', 'id' => 'main-grid'],
//    'columns' => [
//        'id',
//        'admin_id',
//        'controller',
//        'action',
//        'created_at:datetime',
//
//    ],
//]); ?>
