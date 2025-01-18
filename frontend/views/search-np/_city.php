<?php
/**
 * @var array $city
 */

?>
<div class="search_result_additionally_list">
    <?php foreach ($city as $item): ?>
        <div class="addresses-item" data-item_city='<?= $item['item_city'] ?>'><?= $item['name'] ?></div>
    <?php endforeach ?>
</div>
