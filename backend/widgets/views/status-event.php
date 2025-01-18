<?php
/* @var $event common\widgets\StatusEvent */

use backend\modules\event\models\Event;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>
<div class="modal fade" id="status_event" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $this->render('@backend/modules/event/views/event/_status_manual') ?>
                <?php
                $form = ActiveForm::begin([
                    'action' => ['/event/event/status', 'id' => $event->id],
                ]);
                ?>
                <?= $form->field($event, 'status')->dropDownList(Event::statusesAll()) ?>

                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-block']) ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
