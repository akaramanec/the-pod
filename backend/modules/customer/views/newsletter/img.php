<div class="float-left">
    <?php if ($model->img): ?>
        <div class="mr-3">
            <?= Img::main(NEWSLETTER, $model->id, $model->img, '400x400', 120) ?>
        </div>
    <?php endif;

    use backend\modules\media\models\Img; ?>
</div>
<label for="inputMainImg" class="custom-file-upload mt-3"><i
        class="fas fa-cloud-upload-alt"></i>
    Загрузить изображение </label>
<?= $form->field($model, 'mainImg')->fileInput(['id' => 'inputMainImg',])->label(false) ?>
