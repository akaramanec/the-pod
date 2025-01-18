<?php

use backend\modules\customer\models\CustomerTag;

?>
<h3 class="mb-3">Теги</h3>
<?= $form->field($model, 'tagsId')->checkbox([
    'class' => 'all_tag_customer_newsletter custom-control-input',
])->label('Выделить все') ?>
<?= $form->field($model, 'tag_customer')->checkboxList(CustomerTag::listWithQtyCustomer(), [
    'itemOptions' => [
        'class' => 'tag_customer_newsletter custom-control-input',
    ],
])->label(false) ?>
