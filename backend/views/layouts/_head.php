<?php

use backend\modules\media\models\Img;
use yii\helpers\Url;
$head = new \src\helpers\Head();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand" href="/"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav mr-auto"></ul>
        <ul class="navbar-nav">
<!--            <li class="nav-item">-->
<!--                <div class="nav-icon">-->
<!--                    <a href="--><?php //= Yii::$app->params['homeUrl']; ?><!--" target="_blank" title="Перейти на сайт">-->
<!--                        <i class="fas fa-chalkboard-teacher"></i>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </li>-->
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->params['chatTm']; ?>" target="_blank" title="Telegram">
                    <i class="fa fa-telegram"></i>Telegram
                </a>
            </li>
            <li class="nav-item dropdown <?= $head->activeNew($head->orderCountCheck) ?>">
                <a class="nav-link dropdown-toggle" href="#" id="checkMove" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-bell"></i><span class="count-bell"><?= $head->orderCountCheck ?></span>
                    Заказы
                </a>
                <?php if ($head->orderCheck): ?>
                    <div class="dropdown-menu" aria-labelledby="checkMove">
                        <?php foreach ($head->orderCheck as $orderCheck): ?>
                            <a class="dropdown-item"
                               href="<?= Url::to(['/shop/order/update', 'id' => $orderCheck->id]) ?>">ID <?= $orderCheck->id ?></a>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <div class="dropdown-menu" aria-labelledby="checkMove">
                        <a class="dropdown-item"
                           href="">Новых заказов нет</a>
                    </div>
                <?php endif; ?>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::$app->user->identity->email ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item"
                       href="/logout">
                        Выйти
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
