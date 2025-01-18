<?php
/**
 * @var $this \yii\web\View
 * @var $title string
 * @var $description string
 * @var $url string
 * @var $img string
 */
if ($title) {
    $this->title = $title;
    $this->registerMetaTag(['property' => 'og:title', 'content' => $title]);
}
if ($description) {
    $this->registerMetaTag(['name' => 'description', 'content' => $description]);
    $this->registerMetaTag(['property' => 'og:description', 'content' => $description]);
}
if ($url) {
    $this->registerMetaTag(['property' => 'og:url', 'content' => $url]);
}
if ($img) {
    $this->registerMetaTag(['property' => 'og:image', 'content' => $img]);
}
