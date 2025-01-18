<?php

use backend\modules\media\models\Img;

/**
 * @var object $page
 * @var $faqs \backend\modules\shop\models\Faq
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' =>  Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
?>
<main class="main faq">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div class="container">
        <div class="accordion"
             id="faqAccordion">
            <?php $x = 1; ?>
            <?php foreach ($faqs as $faq): ?>
                <div class="accordion__item">
                    <header class="accordion__header"
                            id="heading_<?= $x ?>">
                        <button class="accordion__toggle"
                                type="button"
                                data-toggle="collapse"
                                data-target="#collapse_<?= $x ?>"
                                aria-expanded="true"
                                aria-controls="collapse_<?= $x ?>">
                            <?= $faq->name ?>
                        </button>
                    </header>

                    <div id="collapse_<?= $x ?>"
                         class="collapse accordion__body <?= $x == 1 ? 'show' : '' ?>"
                         aria-labelledby="heading_<?= $x ?>"
                         data-parent="#faqAccordion">
                        <div class="accordion__content">
                            <p class="accordion__text"> <?= $faq->text ?></p>

                        </div>
                    </div>
                </div>
                <?php $x++; ?>
            <?php endforeach ?>
        </div>
    </div>

</main>
