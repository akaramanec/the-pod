<?php

use frontend\models\cart\Cart;
use src\helpers\Date;
use yii\bootstrap4\Html;

/**
 * @var $this \yii\web\View
 * @var boolean $boolean
 * @var integer $integer
 * @var float $float
 * @var string $string
 * @var $customer \backend\modules\customer\models\Customer
 */
?>
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="bg">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th scope="row">Имя</th>
                    <td><?= $customer->first_name ?></td>
                </tr>
                <tr>
                    <th scope="row">Фамилия</th>
                    <td><?= $customer->last_name ?></td>
                </tr>
                <tr>
                    <th scope="row">Username</th>
                    <td><?= $customer->blog->username ?></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td><?= $customer->email ?></td>
                </tr>
                <tr>
                    <th scope="row">Тел.</th>
                    <td><?= $customer->phone ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="bg-no-hidden">
            <?= $this->render('@backend/modules/shop/views/order/_search_product', [
                'action' => '/home/add-product',
                'model' => $customer,
                'placeholder' => 'Найти продукт, и сформировать на него реферальную  ссылку',
            ]); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="bg">
            <h4 class="mb-3">Реферальная ссылка Telegram</h4>
            <h4 class="text-warning mb-2">
                <strong>
                    <?= $customer->linkRefTm() ?>
                </strong>
            </h4>
            <?php if ($customer->customerLinkReferral): ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Реферальные ссылки на продукт:</th>
                        <th width="40"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($customer->customerLinkReferral as $prodReferralTm): ?>
                        <tr>
                            <td><?= $customer->linkRefTm() ?>_<?= $prodReferralTm->productCode ?></td>
                            <td>
                                <?= Html::a('<i class="fas fa-trash"></i>',
                                    [
                                        '/home/delete-product',
                                        'id' => $prodReferralTm->id
                                    ],
                                    [
                                        'title' => 'Удалить',
                                        'class' => 'btn btn-outline-dark',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                                    ]); ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="bg">
            <h4 class="mb-3">Реферальная ссылка Viber</h4>
            <h4 class="text-warning mb-2">
                <strong>
                    <?= $customer->linkRefVb() ?>
                </strong>
            </h4>
            <?php if ($customer->customerLinkReferral): ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Реферальные ссылки на продукт:</th>
                        <th width="40"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($customer->customerLinkReferral as $prodReferralVb): ?>
                        <tr>
                            <td><?= $customer->linkRefVb() ?>_<?= $prodReferralVb->productCode ?></td>
                            <td>
                                <?= Html::a('<i class="fas fa-trash"></i>',
                                    [
                                        '/home/delete-product',
                                        'id' => $prodReferralVb->id
                                    ],
                                    [
                                        'title' => 'Удалить',
                                        'class' => 'btn btn-outline-dark',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                        'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                                    ]); ?>
                            </td>
                        </tr>

                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="bg">
            <p class="float-left bg-text bg-text-info mr-2 mb-2">
                Ваш процент уровень 1: <?= $customer->blog->percent_level_1 ?>%</p>
            <p class="float-left bg-text bg-text-info mr-2 mb-2">
                Ваш процент уровень 2: <?= $customer->blog->percent_level_2 ?>%</p>
            <p class="float-left bg-text bg-text-success mr-2 mb-2">
                Всего: <?= Cart::showPriceStatic($customer->blog->sumTotalOrders) ?></p>
            <p class="float-left bg-text bg-text-primary mr-2 mb-2">
                Оплачено: <?= Cart::showPriceStatic($customer->blog->sumTotalPayed) ?></p>
            <p class="float-left bg-text bg-text-warning">
                Задолженность: <?= Cart::showPriceStatic($customer->blog->sumDebt) ?>
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg">
            <?php if ($customer->orderPayBloggerFixed): ?>
                <p>Выплаты</p>
                <div class="transactions-blogger-view">
                    <table width="100%">
                        <tbody>
                        <?php foreach ($customer->orderPayBloggerFixed as $pay): ?>
                            <tr>
                                <td width="50%"><?= Date::format_datetime($pay->created_at) ?></td>
                                <td><?= Cart::showPriceStatic($pay->sum) ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Выплат нет</p>
            <?php endif; ?>
        </div>
    </div>
</div>
