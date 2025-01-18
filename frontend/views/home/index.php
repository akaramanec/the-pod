<?php

use backend\modules\media\models\Img;

/**
 * @var $this \yii\web\View
 * @var $productMod \backend\modules\shop\models\ProductMod
 * @var $reviewSlider \backend\modules\system\models\ReviewSlider
 * @var object $page
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' => Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
Yii::$app->site->microMarking = [
    '@context' => 'http://schema.org',
    '@type' => 'Organization',
    'url' => Yii::$app->params['homeUrl'],
    'name' => 'Интернет магазин ' . Yii::$app->name,
    'logo' => Yii::$app->params['homeUrl'] . '/img/logo-400x400.jpg',
    'kyivstar' => Yii::$app->site->contact->addition['kyivstar'],
    'life' => Yii::$app->site->contact->addition['life'],
    'email' => Yii::$app->site->contact->addition['email'],
    'address' => Yii::$app->site->contact->addition['address']
	
];

?>
 <link rel="stylesheet" href="indexbuttom.css">

<main class="main">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="container ">
        <div class="main__wrap main__container-title">
            <h1 class="main__title">
                <span> 7 брендов<span> более 100 вкусов</span></span>
                <p>Стильный дизайн</p>
                <p>Практичность девайса и разнообразие вкусов не оставит равнодушным</p></h1>
			<div class="main__buttoms">
                <a class="base-button btn--light main__button main__button"
                   href="catalog"
                   target=""><span>перейти в каталог</span></a>
                <!--a class="base-button btn--light main__button main__button"
                   href="dicount"
                   target=""><span >Купить со скидкой %</span></a-->
            </div>
        </div>
    </div>
</main>
<section class="products__wrap">
    <div class="products__item">
        <div class="container">
            <div class="products__content product-1">
                <div class="products__img">
                    <div class="img-wrap"><img src="/assets/img//product/im1.png"
                                               alt="im1"></div>
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
                    <p></p>
                    <a class="base-button btn--light product__button"
                       href="/production"
                       target=""><span>детальнее</span></a></div>
            </div>
        </div>
    </div>
</section>
<section class="main-catalog">
    <div class="container">
        <div class="catalog__content"><h2>Популярные товары</h2>
            <div class="catalog__wrap">
                <?php foreach ($productMod as $mod): ?>
                    <?= $this->render('@frontend/views/common/_product_item_vertical', [
                        'mod' => $mod,
                    ]) ?>
                <?php endforeach ?>
            </div>
            <a class="base-button btn--dark catalog__button"
               href="/catalog"
               target=""><span>перейти в каталог</span></a>
        </div>
    </div>
</section>
<section class="main-chatbot">
    <div class="container">
        <div class="main-chatbot__content">
            <div class="main-chatbot__description">
                <h2>Чат-бот</h2>
                <p class="mb-2">ThePod позиционирует себя как прогрессивная компания, для нас важен комфорт и
                    практичность!</p>
                <p class="mb-2">Чат-бот это лучшее что мы открыли для себя, с его помощью мы оптимизировали работу всей
                    команды.</p>
                <br/>
                <p class="mb-2 chatbot-item-baner">выбор девайса и вкуса</p>
                <ul class="mb-3">
                    <li>Ассортимент очень большой, хотелось до каждого донести его в самой удобной форме! В боте
                        клиент может следовать своим предпочтениям по вкусовым качествам, ценовой политике и даже
                        дизайну.
                    </li>
                </ul>
                <br/>
                <p class="mb-2 chatbot-item-baner">экономия времени и нервов</p>
                <ul class="mb-3">
                    <li>Ожидание обратной связи утомляет как вас так и нас</li>
                </ul>
                <br/>
                <p class="mb-2 chatbot-item-baner">оформление заказа</p>
                <ul class="mb-3">
                    <li>Бот, в отличие от наших менеджеров, готов к заказам 24/7! Мы связываемся с вами только на
                        этапе подтверждения заказа,через любой удобный месседжер,пару минут и вы видите ттн, пару часов
                        и
                        заказ на почте, один день и все что ты выбрал у тебя в руках.
                    </li>
                </ul>
                <div class="main-chatbot__buttons">
                    <a href="<?= Yii::$app->site->linkTm() ?>"
                       target="_blank"
                       class="base-button main-chatbot__btn btn--light"
                       type="button">перейти в telegram
                    </a>
                </div>
            </div>
            <div class="main-chatbot__phones">
                <div class="device device-iphone-x">
                    <div class="device-frame">
                        <img class="device-content"
                             src="img/phone1.jpg" alt="bot1">
                        <div class="device-stripe"></div>
                        <div class="device-header"></div>
                        <div class="device-sensors"></div>
                        <div class="device-btns"></div>
                        <div class="device-power"></div>
                    </div>
                </div>
                <div class="device device-iphone-x">
                    <div class="device-frame"><img class="device-content"
                                                   src="img/phone2.jpg" alt="bot2">
                        <div class="device-stripe"></div>
                        <div class="device-header"></div>
                        <div class="device-sensors"></div>
                        <div class="device-btns"></div>
                        <div class="device-power"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="main-reviews">
    <div class="container">
        <div class="reviews__title"><h2>Отзывы</h2></div>
        <div class="reviews__content owl-theme">
            <?php foreach ($reviewSlider as $key => $rsItem): ?>
                <div class="reviews__item">
                    <div class="reviews__img">
                        <div class="img-wrap">
                            <img src="<?= Img::review($rsItem) ?>"
                                 alt="<?= $rsItem->name ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
<div class="start-popup">
    <div class="pop-up">
        <div class="img-wrap"><img src="/assets/img/icons/logo.svg"
                                   alt="logo"></div>
        <p>Для использования веб-сайта ThePod вам должно быть не менее 18 лет. Пожалуйста, подтвердите свой возраст
            перед входом на сайт.</p>
        <h2>Вам исполнилось 18 лет?</h2>
        <div class="main-chatbot__buttons">
            <button class="base-button btn--light yes">да</button>
            <button class="base-button btn--light no">нет</button>
        </div>
    </div>
</div>

