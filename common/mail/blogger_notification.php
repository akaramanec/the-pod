<?php


use blog\models\CustomerBlog;

/**
 * @var $this \yii\web\View
 * @var $customerBlog CustomerBlog
 */
?>
<h3>Доступы в админ-панель блогера</h3>
<p><strong>Link</strong>: <a href="<?= Yii::$app->params['blogUrl']; ?>"><?= Yii::$app->params['blogUrl']; ?></a></p>
<p><strong>Username</strong>: <?= $customerBlog->username ?></p>
<p><strong>Password</strong>: <?= $customerBlog->pass ?></p>



