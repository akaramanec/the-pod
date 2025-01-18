<?php
/**
 * @var $filter FilterWidget
 * @var array $attribute
 * @var array $avpml
 * @var array $priceMaxMin
 * @var object $searchModel
 */

use frontend\widgets\FilterWidget;
use yii\bootstrap4\ActiveForm;

?>


<div class="filter">

    <div class="filter__header">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
        ]); ?>
        <div class="d-none"
             id="slider_price"></div>
        <div class="d-none slider_price-wrap">
            <?= $form->field($searchModel, 'price_from', ['options' => ['id' => 'price_from']])->textInput(
                [
                    'value' => $priceMaxMin['min'],
                    'class' => 'price_from is-invalid text-center ',
                ])->label(false) ?>
            <?= $form->field($searchModel, 'price_to', ['options' => ['id' => 'price_to']])->textInput([
                'value' => $priceMaxMin['max'],
                'class' => 'price_to text-center',
            ])->label(false) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <button class="base-button btn--dark"
                id="button_search">Фильтровать
        </button>
    </div>
    <div class="filter__items">
        <?php foreach ($attribute as $att): ?>
            <div class="custom-control-wrap">
                <p><?= $att['name'] ?></p>
                <?php foreach ($avpml as $av): ?>
                    <?php if ($av['attributeValue']['attribute_id'] == $att['id']): ?>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input attribute_value <?= $av['attributeValue']['shopAttribute']['slug'] ?>"
                                   type="checkbox"
                                   data-aid="<?= $att['id'] ?>"
                                   data-attribute="<?= $att['slug'] ?>"
                                   value="<?= $av['attributeValue']['id'] ?>"
                                   id="f-<?= $av['attributeValue']['id'] ?>"
                                <?= $filter->checkedMod($av['attributeValue']['id']) ?>>
                            <label class="custom-control-label"
                                   for="f-<?= $av['attributeValue']['id'] ?>">
                                <?= $av['attributeValue']['name'] ?>
                            </label>
                        </div>
                    <?php endif; ?>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>
    <div hidden
         class="d-none">
        <div id="price_min_range"><?= $priceMaxMin['min'] ?></div>
        <div id="price_max_range"><?= $priceMaxMin['max'] ?></div>
    </div>
</div>
