<?php

use backend\modules\media\models\GetImg;
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
            <li class="nav-item">
                <div class="nav-icon">
                    <a href="<?= Yii::$app->params['homeUrl']; ?>" target="_blank" title="Перейти на сайт">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::$app->user->identity->username ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item"
                       href="/logout">
                        Выйти
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <div class="avatar"
                     style="background-image: url(<?= GetImg::pathCustomer(Yii::$app->user->identity->customer) ?>);">
                </div>

            </li>
        </ul>
    </div>
</nav>
