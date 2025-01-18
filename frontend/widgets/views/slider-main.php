<?php

/**
 * @var $sliders $array
 */

use backend\modules\media\models\Img;
$slider_count = count($sliders);
$i = 0;
?>
<div id="carouselHome"
     class="carousel slide"
     data-ride="carousel"
     data-interval="6000">
    <ol class="carousel-indicators">
        <?php if ($slider_count >= 1) { ?>
            <li data-target="#carouselHome"
                data-slide-to="0"
                class="active"></li>
        <?php } ?>
        <?php if ($slider_count > 1) {
            foreach (range(1, $slider_count - 1) as $count) { ?>
                <li data-target="#carouselHome"
                    data-slide-to="<?= $count ?>"></li>
            <?php } ?>
        <?php } ?>
    </ol>
    <div class="carousel-inner">
        <?php foreach ($sliders as $s): ?>
            <div class="carousel-item <?= ($i == 0) ? 'active' : '' ?>">
                <div class="d-none d-lg-block">
                    <a href="<?= $s['link'] ?>">
                        <img src="<?= Img::mainPath(SLIDER_HOME, $s['id'], $s['img_big'], '1920x600') ?>"
                             class="d-block w-100"
                             alt="<?= $s['name'] ?>" width="100%">
                    </a>
                </div>
                <div class="d-none d-lg-none d-md-block ">
                    <a href="<?= $s['link'] ?>">
                        <img src="<?= Img::mainPath(SLIDER_HOME, $s['id'], $s['img_mid'], '1000x400') ?>"
                             class="d-block w-100"
                             alt="<?= $s['name'] ?>" width="100%">
                    </a>
                </div>
                <div class="d-md-none d-xs-block">
                    <a href="<?= $s['link'] ?>">
                        <img src="<?= Img::mainPath(SLIDER_HOME, $s['id'], $s['img_min'], '600x600') ?>"
                             class="d-block w-100"
                             alt="<?= $s['name'] ?>" width="100%">
                    </a>
                </div>

                <?php if ($s['name']): ?>
                    <div class="carousel-caption">
                        <a class="slider-title"
                           href="<?= $s['link'] ?>">
                            <h3><?= $s['name'] ?></h3>
                        </a>
                        <p><?= $s['description'] ?></p>
                        <a href="<?= $s['link'] ?>"
                           class="btn-primary btn">Подробнее</a>
                    </div>
                <?php endif; ?>
            </div>
            <?php $i++; ?>
        <?php endforeach ?>
    </div>
    <a class="carousel-control-prev"
       href="#carouselHome"
       role="button"
       data-slide="prev">
        <span class="carousel-control-prev-icon"
              aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next"
       href="#carouselHome"
       role="button"
       data-slide="next">
        <span class="carousel-control-next-icon"
              aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
