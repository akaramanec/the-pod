<?php

use backend\modules\media\models\Img;

/**
 * @var object $page
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' => Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
?>
<main class="main products">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="container">
        <div class="main__wrap">
            <h1 class="main__title">
                <span>1500</span>затяжек прямо у тебя в кармане
                <p>Под-система, которая моментально готова к работе. </p>
                <p> Просто доставайте девайс из упаковки и наслаждайтесь вкусом.</p>
            </h1>
            <a class="base-button btn--light main__button"
               href="catalog"
               target=""><span>перейти в каталог</span></a></div>
    </div>
</main>
<section class="products__wrap">
    <div class="products__item">
        <div class="container">
            <div class="products__content product-1">
                <div class="products__img">
                    <div class="img-wrap">
                        <img src="/assets/img//product/im1.png"
                             alt="im1">
                    </div>
                </div>
                <div class="products__description">
                    <h2>Почему именно The Pod?</h2>
                    <p>Компания ThePod имеет опыт в продажах, мы знаем что нужно клиенту!</p>
                    <ul>
                        <li>• большой ассортимент</li>
                        <li>• чат-бот</li>
                        <li>• отзывчивые менеджеры</li>
                        <li>• уютный офис в центре Киева</li>
                        <li>• дегустация - это то, чего вы не увидите у конкурентов</li>
                        <li>• отправки в день заказа</li>
                        <li>• регулярные новинки</li>
                        <li>• разработанная и результативная система заработка для клиентов</li>
                    </ul>
                    <p>Это не планы на будущее, это то, что мы уже имеем на сегодняшний день!</p>
                    <p>Но нет предела совершенству, наша команда развивается и прислушивается ко всем пожеланиям и
                        отзывам!</p>
                </div>
            </div>
        </div>
    </div>
    <div class="products__item">
        <div class="container">
            <div class="products__content product-2">
                <div class="products__img">
                    <div class="img-wrap">
                        <img src="/assets/img//product/im1.png"
                             alt="im1">
                    </div>
                </div>
                <div class="products__description">
                    <h2>Преимущество одноразовых электронных девайсов!</h2>
                    <p>Мы не стремимся продать вам очередной девайс, мы хотим предложить вам удобство и
                        практичность.</p>
                    <ul>
                        <li>• ОЭС не нуждается в дополнительной заправке</li>
                        <li>• Девайс не требует зарядки</li>
                        <li>• Девайс не нужно чистить</li>
                        <li>• Вы сами выбираете количество затяжек в девайсе в зависимости от ваших потребностей</li>
                        <li>• Простота и удобство в использовании</li>
                        <li>• Красивый дизайн</li>
                        <li>• Широкий ассортимент вкусов</li>
                    </ul>
                    <p> ThePod предлагает не усложнять и наслаждаться комфортом. </p>
                </div>
            </div>
        </div>
    </div>
    <div class="products__item">
        <div class="container">
            <div class="products__content product-3">
                <div class="products__img">
                    <div class="img-wrap"><img src="/assets/img//product/im1.png"
                                               alt="im1"></div>
                </div>
                <div class="products__description">
                    <h2>почему это удобно?</h2>
                    <p>VAPORLAX — это одноразовый девайс на определённое количество затяжек, в
                        которую уже изначально заправлена жидкость.</p>
                    <p> Такой девайс имеет много преимуществ и исключает ряд неудобств для курильщика.</p>
                    <ul>
                        <li>• VAPORLAX не нуждается в дополнительной заправке</li>
                        <li>• Не требует зарядки</li>
                        <li>• Не нужно чистить</li>
                        <li>• Вы сами выбираете количество затяжек в зависимости от ваших потребностей</li>
                        <li>• Высокие показатели автономности</li>
                        <li>• Простота и удобство в использовании</li>
                        <li>• Красивый дизайн</li>
                        <li>• Широкий ассортимент вкусов</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <a class="base-button btn--light product__button"
       href="catalog"
       target=""><span>В каталог</span></a></section>
