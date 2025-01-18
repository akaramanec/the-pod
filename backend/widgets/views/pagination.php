<?php
use src\helpers\Common;

?>
<select id="pagination" class="form-control pull-right" data-pagination_name="<?= $pagination_name ?>">
    <option <?= Common::selectedPagination($pagination_name, 0) ?> value="0" >Выкл.</option>
    <option <?= Common::selectedPagination($pagination_name, 40) ?> value="40">40</option>
    <option <?= Common::selectedPagination($pagination_name, 45) ?> value="45">45</option>
    <option <?= Common::selectedPagination($pagination_name, 50) ?> value="50">50</option>
    <option <?= Common::selectedPagination($pagination_name, 80) ?> value="80">80</option>
    <option <?= Common::selectedPagination($pagination_name, 100) ?> value="100">100</option>
    <option <?= Common::selectedPagination($pagination_name, 200) ?> value="200">200</option>
    <option <?= Common::selectedPagination($pagination_name, 300) ?> value="300">300</option>
    <option <?= Common::selectedPagination($pagination_name, 400) ?> value="400">400</option>
    <option <?= Common::selectedPagination($pagination_name, 500) ?> value="500">500</option>
</select>