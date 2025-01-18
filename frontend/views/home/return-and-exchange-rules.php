<?php

use backend\modules\media\models\Img;

/**
 * @var object $page
 */
echo \frontend\widgets\Meta::widget([
    'title' => $page->lang->meta['title'],
    'description' => $page->lang->meta['description'],
    'img' =>  Img::mainPath(SITE_PAGE, $page->id, $page->img, '450x253')
]);
?>



<main class="">
    <?= $this->render('@frontend/views/layouts/_head') ?>
    <div  style="padding-top: 250px; min-height: 100vh" class="container">
        <?= $page->lang->content ?>
    </div>

</main>




