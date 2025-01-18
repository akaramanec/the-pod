<?php

use backend\modules\media\models\Img;

/**
 * @var object $page
 * @var $staffs \backend\modules\system\models\Staff
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' => Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);

function formatToPhoneLink(string $phoneNumber)
{
    return str_replace(['(', ')', ' ', '-'], '', $phoneNumber);
}

?>
<main class="main about">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="container">
        <div class="main__wrap">
            <h1 class="main__title">
                <div class="img-wrap"><img src="/assets/img/icons/logo.svg"
                                           alt="logo"></div>
                <ul>
                    <li>команда из<span></span><b>5 человек</b></li>
                    <li>более<span></span><b>4 лет</b>опыта</li>
                    <li><span></span>единственные официальные импортеры продукции vaporlax в украине</li>
                </ul>
            </h1>
        </div>
    </div>
</main>
<section class="about__text">
    <div class="container"><h2>THE POD</h2>
        Достижения
        <p>Летом 2018 года мы начали заниматься продажей JUUL, позанимавшись год мы упустили возможность взять
            эксклюзив. Каждый месяц у нас росли продажи, так как людям было интересно пользоваться им. К нам приходили
            новые люди и возвращались старые клиенты. Альтернативу JUUL мы увидели в одноразовых электронных девайсах,
            их не надо заправлять и менять картриджи, не надо заряжать и не жалко потерять. И начался поиск достойных
            брендов и вкусов. В Украину мы завезли порядка 50 видов девайсов и нашли 7 достойных вариантов, которые вы и
            видите в нашем ассортименте.
        </p>
        <p class="mb-2">На данный момент мы можем Вам предложить :</p>
        <ul>
            <li>-ElfBar</li>
            <li>-Romio</li>
            <li>-Vaporlax</li>
            <li>-Moti</li>
            <li>-Vapeman</li>
            <li>-Hqd</li>
            <li>-Hugo</li>
        </ul>

        <p> Выделить одну линейку достаточно трудно, как показывает статистика наших продаж определенным лидером
            является Elf Bar, это действительно оправданно! Колличество затяжек от 800 до 2500, лаконичный простой
            дизайн,
            лёгкость тяги и насыщенность вкуса не оставит никого равнодушным, а выбор вкуса серьезно затруднит, ведь их
            более 40. Данный вид девайсов уже захватывает рынок и мы это ускорим с помощью нашего чат - бота. </p>

        <p>Нашей командой была разработана глобальная система, с помощью которой компания становится лидером продаж, а
            наши клиенты успешно зарабатывают </p>

        <p> Блогер с количеством подписчиков 30.000 заработал более 3.000 грн в первый месяц сотрудничества, этим мы
            можем смело подтвердить что система работает и является выгодной для обеих сторон. </p>

        <p> На данный момент у нас есть 50 блогеров, которые работают вместе с нами, это новая волна людей, которые
            хотят попробовать что-то новое и мы предоставляем качественный продукт, лучший сервис и самое важное
            заработок без вложений, которые будут расти каждый день. С любовью,команда ThePod! </p>

    </div>
</section>
<section class="about__command">
    <div class="container"><h2>Наша команда</h2>
        <div class="command__content">
            <?php foreach ($staffs as $key => $staff): ?>
                <div class="command__cart">
                    <div class="img-wrap">
                        <img src="<?= Img::mainPath(STAFF, $staff->id, $staff->img, '440x440') ?>" alt="tony2">
                    </div>
                    <div class="card-command-info">
                        <h4><?= $staff->name ?></h4>
                        <p><?= $staff->description ?></p>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
<section class="about__contacts">
    <div class="container">
        <div class="contacts__wrap">
            <div class="contacts__phones">
                <h2>контакты</h2>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['landline'])) : ?>
                    <?php $phone = Yii::$app->site->contact->addition['landline'] ?>
                    <div class="footer__phones--item">
                        <span>стационарный</span>
                        <a class="link" href="tel:<?= $phone; ?>">
                            <span><?= $phone; ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['kyivstar'])) : ?>
                    <?php $phone = Yii::$app->site->contact->addition['kyivstar'] ?>
                    <div class="footer__phones--item">
                        <span>kyivstar</span>
                        <a class="link" href="tel:<?= $phone; ?>">
                            <span><?= $phone; ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['life'])) : ?>
                    <?php $phone = Yii::$app->site->contact->addition['life'] ?>
                    <div class="footer__phones--item">
                        <span>life</span>
                        <a class="link" href="tel:<?= $phone; ?>">
                            <span><?= $phone; ?></span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['address'])) : ?>
                    <div class="footer__phones--adress">
                        <span>адресс</span>
                        <a class="link" href="tel:contacts">
                            <span><?= Yii::$app->site->contact->addition['address'] ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="contacts__map" style="background-color: #000;">
                <?= Yii::$app->site->contact->addition['map'] ?>
            </div>
        </div>
        <a class="base-button btn--light catalog__button"
           href="/catalog"
           target=""><span>перейти в каталог</span></a></div>
</section>
