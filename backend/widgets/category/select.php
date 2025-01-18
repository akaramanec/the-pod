<?php
/**
 * @var $category array
 * @var $tab string
 */
?>
<option value="<?= $category['id'] ?>"
    <?php if ($category['id'] == $this->model->parent_id) echo ' selected' ?>
    <?php if ($category['id'] == $this->model->id) echo ' disabled' ?>>
    <?= $tab . $category['name'] ?>
</option>
<?php if (isset($category['children'])): ?>
    <?= $this->getMenuHtml($category['children'], $tab . '- ') ?>
<?php endif; ?>
