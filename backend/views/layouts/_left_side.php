<?php

use src\helpers\Common;
use src\services\Role;

?>
<?php if (Role::check('statistics')): ?>
    <a href="/customer/statistic/index" class="<?= Common::activeSide('statistic') ?>">
        <i class="fab far fa-circle"></i> Статистика
    </a>
<?php endif; ?>
<?php if (Role::check('analytics')): ?>
    <a href="/shop/analytics/index" class="<?= Common::activeSide('analytics') ?>">
        <i class="fab far fa-circle"></i> Аналитика
    </a>
<?php endif; ?>
<?php if (Role::check('bloggers')): ?>
    <a href="/customer/customer-blogger/index" class="<?= Common::activeSide('customer-blogger') ?>">
        <i class="fab far fa-circle"></i> Блогеры
    </a>
    <a href="/customer/withdrawal-request/index" class="<?= Common::activeSide('withdrawal-request') ?>">
        <i class="fab far fa-circle"></i> Запросы на виплату
    </a>
<?php endif; ?>
<?php if (Role::check('customer')): ?>
    <a href="/customer/customer/index" class="<?= Common::activeSide('customer') ?>">
        <i class="fab far fa-circle"></i> Пользователи
    </a>
<?php endif ?>
<?php if (Role::check('orders')): ?>
    <a href="/shop/order/index" class="<?= Common::activeSide('order') ?>">
        <i class="fab far fa-circle"></i> Заказы
    </a>
<?php endif; ?>
<?php if (Role::check('shop')): ?>
    <h5 data-toggle="collapse"
        data-target="#shop_left_side"
        aria-expanded="false"
        onclick="openClose('shop_left_side')"
        aria-controls="shop_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['category', 'product']) ?>"
        id="heading_shop_left_side">
        <i class="fa fa-shopping-bag"></i> <?= Yii::t('app', 'Shop') ?> <span class="open-close"><i
                    class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="shop_left_side">
        <a href="/shop/category" class="<?= Common::activeSide('category') ?>">
            <i class="fab far fa-circle"></i> <?= Yii::t('app', 'Categories') ?>
        </a>
        <a href="/shop/product" class="<?= Common::activeSide('product') ?>">
            <i class="fab far fa-circle"></i> <?= Yii::t('app', 'Products') ?>
        </a>
    </div>
<?php endif; ?>
<?php if (Role::check('order-poll&poll')): ?>
    <h5 data-toggle="collapse"
        data-target="#poll_left_side"
        aria-expanded="false"
        onclick="openClose('poll_left_side')"
        aria-controls="poll_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['order-poll', 'poll']) ?>"
        id="heading_poll_left_side">
        <i class="fab fas fa-poll-h fa-fw"></i> Опросы <span class="open-close"><i
                    class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="poll_left_side">
    <?php if (Role::check('order-poll')): ?>
        <a href="/shop/order-poll/index" class="<?= Common::activeSide('order-poll') ?>">
            <i class="fab far fa-circle"></i> Опросы
        </a>
    <?php endif; ?>
    <?php if (Role::check('poll')): ?>
        <a href="/shop/poll/index" class="<?= Common::activeSide('poll') ?>">
            <i class="fab far fa-circle"></i> Конструткор
        </a>
    <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (Role::check('newsletter')): ?>
    <a href="/customer/newsletter/index" class="<?= Common::activeSide('newsletter') ?>">
        <i class="fab far fa-circle"></i> Рассылка
    </a>
<?php endif; ?>
<?php if (Role::check('link-bot')): ?>
    <a href="/customer/link-bot/index" class="<?= Common::activeSide('link-bot') ?>">
        <i class="fab far fa-circle"></i> Метрика
    </a>
<?php endif; ?>
<?php if (Role::check('attribute')): ?>
    <a href="/shop/attribute/index" class="<?= Common::activeSide('attribute') ?>">
        <i class="fab far fa-circle"></i> Атрибуты
    </a>
<?php endif; ?>
<?php if (Role::check('tag')): ?>
    <a href="/customer/tag/index" class="<?= Common::activeSide('tag') ?>">
        <i class="fab far fa-circle"></i> Теги
    </a>
<?php endif; ?>
<?php if (Role::check('delivery')): ?>
    <a href="/shop/delivery/index" class="<?= Common::activeSide('delivery') ?>">
        <i class="fab far fa-circle"></i> Доставка
    </a>
<?php endif; ?>
<?php if (Role::check('notification')): ?>
    <h5 data-toggle="collapse"
        data-target="#notification_left_side"
        aria-expanded="false"
        onclick="openClose('notification_left_side')"
        aria-controls="notification_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['notification', 'notice']) ?>"
        id="heading_notification_left_side">
        <i class="fab fas fa-users fa-fw"></i>Уведомления
        <span class="open-close"><i class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="notification_left_side">
        <?php if (Role::check('notification')): ?>
            <a href="/notification/notification/index" class="<?= Common::activeSide('notification') ?>">
                <i class="fab far fa-circle"></i> Новым пользователям
            </a>
        <?php endif; ?>
        <?php if (Role::check('notice')): ?>
            <a href="/shop/notice/index" class="<?= Common::activeSide('notice') ?>">
                <i class="fab far fa-circle"></i> Поле покупки
            </a>
        <?php endif; ?>
    </div>
<?php endif ?>
<?php if (Role::check('content')): ?>
    <h5 data-toggle="collapse"
        data-target="#content_left_side"
        aria-expanded="false"
        onclick="openClose('content_left_side')"
        aria-controls="content_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['faq', 'review-slider', 'staff', 'command', 'notice-np']) ?>"
        id="heading_content_left_side">
        <i class="fab fas fa-users fa-fw"></i> Контент <span class="open-close"><i
                    class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="content_left_side">
    <?php if (Role::check('command')): ?>
        <a href="/bot/command/index" class="<?= Common::activeSide('command') ?>">
            <i class="fab far fa-circle"></i> Тексты бота
        </a>
    <?php endif; ?>
    <?php if (Role::check('faq')): ?>
        <a href="/shop/faq/index" class="<?= Common::activeSide('faq') ?>">
            <i class="fab far fa-circle"></i> Faq
        </a>
    <?php endif; ?>

    <?php if (Role::check('notice-np')): ?>
        <a href="/shop/notice-np/index" class="<?= Common::activeSide('notice-np') ?>">
            <i class="fab far fa-circle"></i> Уведомления НП
        </a>
    <?php endif; ?>
    <?php if (Role::check('review-slider')): ?>
        <a href="/system/review-slider/index" class="<?= Common::activeSide('review-slider') ?>">
            <i class="fab far fa-circle"></i> Слайд отзывов
        </a>
    <?php endif; ?>
    <?php if (Role::check('staff')): ?>
        <a href="/system/staff/index" class="<?= Common::activeSide('staff') ?>">
            <i class="fab far fa-circle"></i> Штат сотрудников
        </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if (Role::check('seo')): ?>
    <h5 data-toggle="collapse"
        data-target="#seo_left_side"
        aria-expanded="false"
        onclick="openClose('seo_left_side')"
        aria-controls="seo_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['page', 'placeholder', 'site-page-l']) ?>"
        id="heading_seo_left_side">
        <i class="fab fas fa-database"></i> SEO<span class="open-close"><i
                    class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="seo_left_side">
        <?php if (Role::check('page')): ?>
            <a href="/system/page/index" class="<?= Common::activeSide('page') ?>">
                <i class="fab far fa-circle"></i> Страницы сайта
            </a>
        <?php endif; ?>
        <?php if (Role::check('site-page-l')): ?>
            <a href="/system/site-page-l/index" class="<?= Common::activeSide('site-page-l') ?>">
                <i class="fab far fa-circle"></i> Содержание страниц
            </a>
        <?php endif; ?>
        <?php if (Role::check('placeholder')): ?>
            <a href="/bot/placeholder/index" class="<?= Common::activeSide('placeholder') ?>">
                <i class="fab far fa-circle"></i> Формы
            </a>
        <?php endif; ?>
    </div>
<?php endif ?>
<?php if (Role::check('system')): ?>
    <h5 data-toggle="collapse"
        data-target="#setting_left_side"
        aria-expanded="false"
        onclick="openClose('setting_left_side')"
        aria-controls="setting_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['setting', 'bot', 'admin']) ?>"
        id="heading_setting_left_side">
        <i class="fab fas fa-cogs fa-fw"></i> Система <span class="open-close"><i class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="setting_left_side">
        <?php if (Role::check('setting')): ?>
            <a href="/system/setting/index" class="<?= Common::activeSide('setting') ?>">
                <i class="fab far fa-circle"></i> Настройки
            </a>
        <?php endif ?>
        <?php if (Role::check('bot')): ?>
            <a href="/bot/bot/index" class="<?= Common::activeSide('bot') ?>">
                <i class="fab far fa-circle"></i> Bot
            </a>
        <?php endif ?>
        <?php if (Role::check('admin')): ?>
            <a href="/admin/admin/index" class="<?= Common::activeSide('admin') ?>">
                <i class="fab far fa-circle"></i> Админ
            </a>
        <?php endif ?>
    </div>
<?php endif ?>
<?php if (Role::check('dev')): ?>
    <h5 data-toggle="collapse"
        data-target="#logger_left_side"
        aria-expanded="false"
        onclick="openClose('logger_left_side')"
        aria-controls="logger_left_side"
        class="collapse-filter-head <?= Common::activeSideParent(['bot-menu', 'bot-menu-command', 'logger', 'home', 'auth-logger']) ?>"
        id="heading_logger_left_side">
        <i class="fab fab fa-connectdevelop"></i> Dev <span class="open-close"><i class="fas fa-angle-right"></i></span>
    </h5>
    <div class="collapse" id="logger_left_side">
        <?php if (Role::check('auth-logger')): ?>
            <a href="/admin/auth-logger/index"
               class="<?= Common::activeSide('auth-logger') ?>">
                <i class="fab far fa-circle"></i> AuthLogger
            </a>
        <?php endif ?>
        <?php if (Role::check('bot-menu')): ?>
            <a href="/bot/bot-menu/index"
               class="<?= Common::activeSide('bot-menu') ?>">
                <i class="fab far fa-circle"></i> BotMenu
            </a>
        <?php endif ?>
        <?php if (Role::check('bot-menu-command')): ?>
            <a href="/bot/bot-menu-command/index"
               class="<?= Common::activeSide('bot-menu-command') ?>">
                <i class="fab far fa-circle"></i> BotMenuCommand
            </a>
        <?php endif ?>
        <?php if (Role::check('logger')): ?>
            <a href="/bot/logger/index" class="<?= Common::activeSide('logger') ?>">
                <i class="fab far fa-circle"></i> Logger
            </a>
        <?php endif ?>
        <?php if (Role::check('home')): ?>
            <a href="/home/demo" class="<?= Common::activeSide('home') ?>">
                <i class="fab far fa-circle"></i> Demo
            </a>
        <?php endif ?>
    </div>
<?php endif; ?>
<a class="logo-boto fixed-bottom" href="https://boto.agency/ru" target="_blank"></a>

