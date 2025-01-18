<?php

use src\helpers\Common;
use yii\helpers\Url;

/** @var array $category */
?>
<li>
    <a class="<?=
    Common::active_search_category($category['id']) ?>"
       href="<?= Url::to(['/event/category/index', 'id' => $category['id']]) ?>">
        <?= $category['name']; ?>
    </a>
    <?php if (isset($category['children'])): ?>
        <ul>
            <?= $this->getMenuHtml($category['children']) ?>
        </ul>
    <?php endif; ?>
</li>
