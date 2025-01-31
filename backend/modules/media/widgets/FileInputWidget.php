<?php

namespace backend\modules\media\widgets;

use backend\modules\media\models\Img;
use Yii;
use yii\bootstrap4\Html;
use yii\widgets\InputWidget;

class FileInputWidget extends InputWidget
{
    public $entity;
    public $mode;
    public $width = '100%';
    public $size = '400x400';
    public $_img;
    public $_fileInput;
    public $_js;

    public function run()
    {
        $this->setImg();
        $this->setFileInput();
        $this->setJs();
        Yii::$app->view->registerJS($this->_js, yii\web\View::POS_READY);
        return $this->_img . $this->_fileInput;
    }

    private function setImg()
    {
        switch ($this->mode) {
            case 'main':
                $this->_img = Html::img(Img::cache($this->entity, $this->model->id, $this->model->img, $this->size), [
                    'width' => $this->width,
                    'id' => $this->attribute,
                ]);
                break;
            case 'multiple':
                $this->_img = Img::images($this->entity, $this->model->id, $this->model->images, $this->size);
                break;
        }
    }

    /**
     * $form->field($model, 'mainImg')->widget(FileInputWidget::className(), [
     *  'entity' => NEWSLETTER,
     *  'mode' => 'main',
     *  'width' => '100'
     *  ])->label(false)
     * <?= $form->field($model, 'multiImg[]')->widget(FileInputWidget::className(), [
     * 'entity' => NEWSLETTER,
     * 'mode' => 'multiple'
     * ]
     * )->label(false) ?>
     */
    private function setFileInput()
    {
        switch ($this->mode) {
            case 'main':
                $this->_fileInput = '<label for="inputMainImg" class="custom-file-upload"><i class="fas fa-cloud-upload-alt"></i> Загрузить файл </label>' . Html::activeFileInput($this->model, $this->attribute, [
                        'id' => 'inputMainImg',
                    ]);
                break;
            case 'multiple':
                $this->_fileInput = Html::activeFileInput($this->model, $this->attribute, [
                    'multiple' => true,
                    'accept' => 'image/*',
                    'id' => 'inputMultiple'
                ]);;
                break;
        }
    }

    private function setJs()
    {
        $main = '';
        $multiple = '';

        if ($this->mode == 'main') {
            $main = <<< JS
function readMainImg() {
    if (this.files && this.files[0]) {
        var FR = new FileReader();
        FR.addEventListener("load", function (e) {
            document.getElementById("mainImg").src = e.target.result;
        });
        FR.readAsDataURL(this.files[0]);
    }
}

document.getElementById("inputMainImg").addEventListener("change", readMainImg);
JS;
        }
        if ($this->mode == 'multiple') {
            $multiple = <<< JS
function readMultiple() {
    let currentFiles = Array.from(this.files);
    allFiles = allFiles.concat(currentFiles);
    if (this.files) {
        $.each(this.files, function () {
            var FR = new FileReader();
            FR.addEventListener("load", function (e) {
                $('#input_multiple_block').append('<div class="img-item"><img src="' + e.target.result + '" width="100%"></div>');
            });
            FR.readAsDataURL(this);
        });
    }
    let uniqueFiles = Array.from(new Map(allFiles.map(file => [file.name, file])).values());
    let dataTransfer = new DataTransfer();
    uniqueFiles.forEach(file => dataTransfer.items.add(file));
    this.files = dataTransfer.files;
}
let allFiles = [];
document.getElementById("inputMultiple").addEventListener("change", readMultiple);
JS;
        }
        $this->_js = $main . $multiple;
    }
}
