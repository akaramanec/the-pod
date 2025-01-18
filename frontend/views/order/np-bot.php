<?php

use yii\bootstrap4\ActiveForm;

/**
 * @var $this \yii\web\View
 * @var $orderNp \backend\modules\shop\models\OrderNp
 * @var $form ActiveForm
 * @var $linkBackInBot string
 */
Yii::$app->view->registerJsFile('/js/order.js',
    [
        'depends' => [
            \yii\web\YiiAsset::class,
            \yii\bootstrap4\BootstrapPluginAsset::class
        ]
    ]);
\frontend\assets\Select2Asset::register($this);
?>
<div class="np-bot">
    <?php $form = ActiveForm::begin(); ?>
    <div class="search-block-block">
        <?= $form->field($orderNp, 'city')->textInput(
            [
                'onkeyup' => 'searchCity(this.value)',
                'autofocus' => 'off',
                'id' => 'city',
                'autocomplete' => 'off'
            ]) ?>
        <div id="search_result_additionally_list"></div>
        <div id="search_item_id" hidden></div>
    </div>

    <div class="form-group">
        <label for="branch_ref" class="col-form-label">Отделение</label>
        <select id="branch_ref"
                class="form-control"
                name="branch_ref">
            <option value=""></option>
        </select>
    </div>

    <div class="d-flex flex-column align-items-center flex-md-row w-100 justify-content-around p-3">
        <button class="base-button btn--ligh"
                type="submit">Заказать
        </button>
        <?php ActiveForm::end(); ?>
        <a class="base-button btn--light"
           href="<?= $linkBackInBot ?>">Назад</a>

    </div>
</div>
<div id="deliveryAllSlug" hidden>{}</div>
<?php
$script = <<< JS
      $('#branch_ref').select2({
            theme: 'bootstrap4'
        });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
