<footer class="footer">
	<div class="footer__delivery">

<a class="link" href="/return-and-exchange-rules" target="">
                <span>правило возврата и обмена</span>
            </a>
				<a class="link" href="" target="" >
                <span>доставка и оплата</span>
            </a>
	</div>
	</div>
    <div class="container">
        <div class="footer__info">
            <a class="footer__logo" href="/">
                <div class="img-wrap"><img src="/img/logo-4.svg" alt="logo"></div>
            </a>
            <nav class="footer__nav">
                <ul class="navigation__list">
                    <li class="navigation__item"><a class="link" href="/catalog" target=""><span>Каталог</span></a></li>
                    <li class="navigation__item"><a class="link" href="/production"
                                                    target=""><span>О продукции</span></a></li>
                    <li class="navigation__item"><a class="link" href="/about" target=""><span>О нас</span></a></li>
                    <li class="navigation__item"><a class="link" href="/faq" target=""><span>FAQ</span></a></li>
                </ul>
            </nav>
            <div class="footer__links">
                <a class="link" href="<?= Yii::$app->site->linkTm() ?>" target="">
                    <span>Чат-бот Telegram</span>
                </a>
                <a class="link hidden" href="<?= Yii::$app->site->linkVb() ?>" target=""><span>Чат-бот Viber</span></a>
            </div>
            <div class="footer__phones">
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['kyivstar'])) : ?>
                    <div class="footer__phones--item">
                        <span>kyivstar</span>
                        <a class="link" href="tel:<?= Yii::$app->site->contact->addition['kyivstar'] ?>"><span>
                            <?= Yii::$app->site->contact->addition['kyivstar'] ?>
                        </span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['life'])) : ?>
                    <div class="footer__phones--item">
                        <span>life</span>
                        <a class="link" href="tel:<?= Yii::$app->site->contact->addition['life'] ?>"><span>
                            <?= Yii::$app->site->contact->addition['life'] ?>
                        </span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (isset(Yii::$app->site->contact->addition) && !empty(Yii::$app->site->contact->addition['address'])) : ?>
                    <div class="footer__phones--adress">
                        <span>Адрес</span>
                        <a class="link" href="tel:contacts">
                        <span>
                            <?= Yii::$app->site->contact->addition['address'] ?>
                        </span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="footer__pocitics">
        <div class="container">
            <a class="link" href="/privacy-policy" target="">
                <span>Политика конфиденциальности</span>
            </a>
            <a class="link" href="/return-and-exchange-rules" target="">
                <span></span>
            </a>
            <a href="/">
                <div class="img-wrap">
                    <img src="/assets/img/icons/boto-footer.svg" alt="boto-footer">
                </div>
            </a>
        </div>
    </div>
</footer>
