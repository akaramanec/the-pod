<?php
/**
 * @var $this \yii\web\View
 * @var $orderNp \backend\modules\shop\models\OrderNp
 * @var $form ActiveForm
 */
?>

<div class="order__delivery">
    <div class="search-block-block">
        <?= $form->field($orderNp, 'city')->textInput(
            [
                'onkeyup' => 'searchCity(this.value)',
                'autofocus' => 'off',
                'id' => 'city',
                'autocomplete' => 'off'
            ]) ?>
        <div id="search_result_additionally_list"></div>
        <div id="search_item_id"
             hidden></div>
    </div>
    <div class="branch-select">
        <?= $this->render('@frontend/views/order/_search-branch', [
            'listWarehouses' => [],
            'value' => $orderNp->branch,
        ]) ?>
    </div>
</div>
