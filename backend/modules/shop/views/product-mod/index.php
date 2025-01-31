<?php

use backend\modules\shop\models\Product;
use src\helpers\Common;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $model Product */

?>
<div class="row mt-4">
    <div class="col-md-12">
        <h3><?= Yii::t('app', 'Product Modifications') ?></h3>
        <?= Html::a(Yii::t('app', 'Create Modification'), ['product-mod/create', 'productId' => $model->id], ['class' => 'btn btn-success float-right mt-3']) ?>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getMod(),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]),
            'pager' => Common::pager4(),
            'tableOptions' => ['class' => 'table'],
            'options' => ['tag' => 'div', 'class' => 'grid-view table-responsive mt-3', 'id' => 'main-grid'],
            'columns' => [
                'id',
                'name',
                'value',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return Html::a('<i class="far fa-edit"></i>', ['/shop/product-mod/update', 'id' => $key],
                                [
                                    'class' => 'btn btn-outline-dark',
                                    'title' => Yii::t('app', 'Edit'),
                                ]);

                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-trash-alt"></i>', ['/shop/product-mod/delete', 'id' => $key],
                                [
                                    'title' => Yii::t('app', 'Delete'),
                                    'class' => 'btn btn-outline-dark',
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>