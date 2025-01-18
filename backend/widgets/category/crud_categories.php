<?php
use src\helpers\Common;
/** @var array $category */
?>
<li>
    <a class="<?= Common::active_search_category($category['id']) ?>"
       href="/rent/category/index?id=<?= $category['id'] ?>">
        <?= $category['name']; ?>
    </a>
    <?php if (isset($category['children'])): ?>
    <ul>
        <?= $this->getMenuHtml($category['children']) ?>
    </ul>
    <?php endif; ?>
</li>
