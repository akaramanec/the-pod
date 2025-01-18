<?php

use backend\modules\notification\models\enum\NotificationSettingEnum;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\notification\models\form\NotificationForm */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(); ?>
<div class="row content-admin">
    <div class="col-md-12">
        <div class="bg">
            <?= $form->field($model, 'name')->textInput() ?>
            <?php foreach ($model->settingsFormField() as $type => $label) : ?>
                <?php if ($type === NotificationSettingEnum::NOT_ACTIVE_TIME): ?>
                    <?= $form->field($model, "settings[$type]")->widget(TimePicker::class, [
                        'pluginOptions' => [
                            'defaultTime' => '00:00',
                            'showSeconds' => false,
                            'showMeridian' => false,
                            'minuteStep' => 1,
                            'secondStep' => 5,
                        ]
                    ])->label($label); ?>
                <?php else : ?>
                    <?= $form->field($model, "settings[$type]")->textInput()->label($label) ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary btn-base float-right']) ?>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

