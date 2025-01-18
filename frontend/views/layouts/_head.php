<?php

use frontend\models\cart\Cart;

?>
<header class="header">
    <div class="header__wrap container"><a class="header__logo" href="/">
            <div class="img-wrap"><img src="/img/logo-4.svg" alt="logo"></div>
        </a>
        <nav class="header__nav">
            <ul class="navigation__list">
                <li class="navigation__item"><a class="link" href="/catalog" target=""><span>Каталог</span></a></li>
                <li class="navigation__item"><a class="link" href="/production" target=""><span>О продукции</span></a>
                </li>
                <li class="navigation__item"><a class="link" href="/about" target=""><span>О нас</span></a></li>
                <li class="navigation__item"><a class="link" href="/faq" target=""><span>FAQ</span></a></li>
            </ul>
            <div class="header__basket" onclick="return getCart()">
                <div class="img-wrap"><img src="/assets/img/icons/basket.svg" alt="basket"></div>
                <span class="header__basket--number cart_qty_head">
                    <?= Cart::qtyTotalSession() ?>
                </span>
            </div>
        </nav>
        <div class="header__nav-toggler">
            <div class="burger-shape">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</header>
