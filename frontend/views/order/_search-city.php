<?php
/**
 * @var array $response
 */

use yii\helpers\Json;

?>

<div class="search_result_additionally_list">
    <?php if ($response->success == true && isset($response->data[0]->Addresses) && $response->data[0]->Addresses): ?>
        <?php foreach ($response->data[0]->Addresses as $item): ?>
            <div class="addresses-item"
                 data-item_city='<?= Json::encode($item) ?>'>
                <?= $item->Present ?>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="no-search-item">Ничего не найдено</div>
    <?php endif; ?>
</div>
