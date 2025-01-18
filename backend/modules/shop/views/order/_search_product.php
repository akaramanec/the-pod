<?php
/**
 * @var object $model
 * @var string $action
 * @var string $placeholder
 */

?>
<div class="search-block">
    <form action="<?= $action ?>" method="get">
        <input type="text"
               id="search_id"
               onkeyup="search(this.value)"
               class="form-control"
               autocomplete="off"
               placeholder="<?= $placeholder ?>"
               name="q"
               required>
        <input type="hidden" name="model_id" value="<?= $model->id ?>">
        <input type="hidden" id="search_input" name="search_id" value="">

        <button id="btn_search" type="submit" hidden></button>
        <div id="search_results"></div>
    </form>
</div>


