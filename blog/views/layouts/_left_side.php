<?php

use src\helpers\Common;

?>
<a href="/" class="<?= Common::activeSide('home') ?>">
    <i class="fab far fa-circle"></i> Главная
</a>
<a href="/customer/index" class="<?= Common::activeSide('customer') ?>">
    <i class="fab far fa-circle"></i> Рефералы
</a>
<a href="/order/index" class="<?= Common::activeSide('order') ?>">
    <i class="fab far fa-circle"></i> Заказы
</a>

<?php if (Common::isIP()): ?>
    <h5 data-toggle="collapse"
        data-target="#logger_left_side"
        aria-expanded="false"
        onclick="openClose('logger_left_side')"
        aria-controls="logger_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['bot-menu', 'bot-menu-command', 'logger', 'home']) ?>"
        id="heading_logger_left_side">
        <i class="fab fab fa-connectdevelop"></i> Dev <span class="open-close"><i class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="logger_left_side">
        <a href="/home/demo" class="<?= Common::activeSide('home') ?>">
            <i class="fab far fa-circle"></i> Demo
        </a>
    </div>
<?php endif; ?>
<a class="logo-boto fixed-bottom" href="https://boto.agency/ru" target="_blank"></a>

