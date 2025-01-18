<?php

namespace frontend\models\cart;

use backend\modules\bot\telegram\TCommon;
use backend\modules\customer\models\Customer;
use backend\modules\media\models\Img;
use frontend\models\OrderCustomer;
use NumberFormatter;
use src\behavior\OrderPaymentUpdate;
use src\services\AdditionalDiscount;
use Yii;
use yii\helpers\Html;

/**
 * @var $data CartInterface
 */
class Cart
{
    const CURRENCY = 'грн.';

    public $data;
    public $items = [];
    public $qtyTotal = 0;
    public $sumTotal = 0;
    public $sumTotalDiscount = 0;
    public $cacheSumTotal = 0;
    public $sumTotalNpUponReceipt = 0;
    public $currency = 'грн.';

    public $additionalDiscountSum = 0;
    public $sumPayByBonus = 0;

    public function build($data)
    {
        if ($data instanceof CartInterface) {
            $this->data = $data;
        }
        $this->sumPayByBonus = isset($this->data->order) ? $this->data->order->blogger_bonus : 0;
        $this->items();
    }

    public function items()
    {
        if ($this->data->items) {
            foreach ($this->data->items as $item) {

                /* Тимчасово для нижки на сайті */
                $price = $item->product->price;
                AdditionalDiscount::updateProductItemPrice($price,
                    $item->product->name,
                    (
                        ($this->data instanceof CartDataSession) ||
                        (isset($this->data->order) && $this->data->order->customer instanceof OrderCustomer)
                    )
                );
                $item->product->price = $price;

                /* **************************** */

                $qtyItem = $this->data->qtyItem($item->id);
                $priceItemTotal = $this->priceItemTotal($qtyItem, $item->product->price);
                $additionalDiscountModel = new AdditionalDiscount($item->product->name,
                    isset($this->data->order) ? date(AdditionalDiscount::DATE_FORMAT, strtotime($this->data->order->created_at)) : date(AdditionalDiscount::DATE_FORMAT),
                    $priceItemTotal
                );
                $additionalDiscountItemSum = $additionalDiscountModel->sumDiscount();
                $this->items[$item->id] = [
                    'modId' => $item->id,
                    'img' => isset($item->product->img) ? Img::productImageWithCheckSupportExtension($item->product->img, Yii::$app->params['sizeProduct']['mid']) : null,
                    'productName' => $item->product->name,
                    'productPrice' => $item->product->price,
                    'qtyItem' => $qtyItem,
                    'codeProduct' => $item->product->code,
                    'uuidProduct' => $item->product->uuid,
                    'priceItemProductTotal' => $priceItemTotal,
                    'url' => '/product/' . $item->product->slug,
                    'slug' => $item->product->slug,
                    'additionalDiscountItemSum' => $additionalDiscountItemSum,
                    'additionalDiscount' => $additionalDiscountModel->getDiscount(),
                ];
                $this->qtyTotal += $qtyItem;
                $this->sumTotal += $priceItemTotal;
                $this->additionalDiscountSum += $additionalDiscountItemSum;
            }
            $this->setSumTotalDiscount();
//            $this->setSumTotalNpUponReceipt(); // comment while not need recalculate C.O.D. payments
            $this->setCacheSumTotal();
            return $this->items;
        }
    }

    public function setCacheSumTotal()
    {
        if ($this->sumTotalNpUponReceipt) {
            $this->cacheSumTotal = $this->sumTotalNpUponReceipt;
        } elseif ($this->sumTotalDiscount) {
            $this->cacheSumTotal = $this->sumTotalDiscount;
        } else {
            $this->cacheSumTotal = $this->sumTotal;
        }

        /* Только для пользователей бота */
        if (!empty($this->additionalDiscountSum)
            && (isset($this->data->order) && $this->data->order->customer instanceof Customer)
        ) {
            $this->cacheSumTotal -= $this->additionalDiscountSum;
        }
    }

    public function setSumTotalDiscount()
    {
        if ($this->data->discountPercent) {
            $this->sumTotalDiscount = $this->percent($this->sumTotal);
        }
    }

    public function setSumTotalNpUponReceipt()
    {
        if ($this->data->order->isNpUponReceipt()) {
            if ($this->sumTotalDiscount) {
                $this->sumTotalNpUponReceipt = $this->getUponReceiptPrice($this->sumTotalDiscount);
            } else {
                $this->sumTotalNpUponReceipt = $this->getUponReceiptPrice($this->sumTotal);
            }
        }
    }

    public function percent($price)
    {
        return (1 - $this->data->discountPercent / 100) * $price;
    }

    public function priceItemTotal($qty, $price)
    {
        return $qty * $price;
    }

    public function showPrice($price)
    {
        if ($price == 0) {
            return 0;
        }
        return $price . " " . self::CURRENCY;
    }

    public function cartText()
    {
        $text = '';
        foreach ($this->items as $item) {
            $text .= $item['productName'] . PHP_EOL;
            $text .= $item['qtyItem'] . ' шт. - ' . $this->showPrice($item['priceItemProductTotal']) . PHP_EOL;
        }
        $text .= 'Цена всего: ' . $this->showPrice($this->sumTotal) . PHP_EOL;
        if ($this->sumTotalDiscount) {
            $text .= 'Цена всего со скидкой: ' . $this->showPrice($this->sumTotalDiscount);
        }
        return $text;
    }

    public function cartTextTm()
    {
        $common = new TCommon();
        $productsText = '';
        foreach ($this->items as $item) {
            $placeholder = [
                '{{name}}' => $item['productName'],
                '{{qty}}' => $item['qtyItem'],
                '{{productPrice}}' => '',
                '{{discountProduct}}' => ''
            ];
            if ($this->data->discountPercent) {
                $placeholder['{{productPrice}}'] = '<strike>' . $this->showPrice($item['priceItemProductTotal']) . '</strike>';
                $placeholder['{{discountProduct}}'] = $this->showPrice($this->percent($item['priceItemProductTotal']) - $item['additionalDiscountItemSum']);
            } else {
                $placeholder['{{priceProduct}}'] = $this->showPrice($item['priceItemProductTotal']);
            }
            $productsText .= strtr(($common)->text('cartProductTextTm'), $placeholder) . PHP_EOL . PHP_EOL;
        }
        $placeholder = [
            '{{productsText}}' => $productsText,
        ];

        $messagePayBlogger = '';
        if (!empty($this->sumPayByBonus)) {
            $messagePayBlogger = $common->text('messagePayBlogger');
            /** @var Customer $customer */
            $customer = Yii::$app->tm->customer;
            $bloggerPlaceholder = [
                '{{payBlogger}}' => $this->showPrice($this->sumPayByBonus),
                '{{balanceBlogger}}' => $this->showPrice($customer->blog->sumDebt - $this->sumPayByBonus)
            ];
            $messagePayBlogger = strtr($messagePayBlogger, $bloggerPlaceholder);
        }

        $placeholder['{{payBlogger}}'] = $messagePayBlogger;

        if ($this->sumTotalDiscount) {
            $placeholder['{{percent}}'] = $this->data->discountPercent . "%" . AdditionalDiscount::getMessage($this->data->order->created_at);
            $placeholder['{{priceTotal}}'] = '<strike>' . $this->showPrice($this->sumTotal) . '</strike>';
            $placeholder['{{discountTotal}}'] = $this->showPrice($this->sumTotalDiscount - $this->additionalDiscountSum);
            $message = $common->text('cartTextTmDiscount');
        } else {
            $placeholder['{{priceTotal}}'] = $this->showPrice($this->sumTotal);
            return strtr($common->text('cartTextTm'), $placeholder);
        }
        if (!empty($this->sumPayByBonus)) {
            $sum = (!empty($this->sumTotalDiscount) ? $this->sumTotalDiscount : $this->sumTotal) - $this->sumPayByBonus - ($this->additionalDiscountSum ?: 0);
            $placeholder['{{discountTotal}}'] = '';
            $placeholder['{{priceTotal}}'] = $this->showPrice($sum);
        }
        return strtr($message, $placeholder);
    }

    public function delItem($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    public function addToCart($id, $qty)
    {
        if (isset($_SESSION['cart'][$id])) {
            $qty = $qty + $_SESSION['cart'][$id]['qty'];
            $_SESSION['cart'][$id]['qty'] = $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'qty' => $qty,
            ];
        }
    }

    public static function qtyItem($id)
    {
        if (isset($_SESSION['cart'][$id]['qty'])) {
            return $_SESSION['cart'][$id]['qty'];
        }
        return 1;
    }

    public function qty($id, $qty)
    {
        return $_SESSION['cart'][$id]['qty'] = $qty;
    }

    public static function qtyTotalSession()
    {
        if (isset($_SESSION['qtyTotal']) && $_SESSION['qtyTotal'] >= 1) {
            return $_SESSION['qtyTotal'];
        } else {
            return 0;
        }
    }

    public function setSessionQtyTotal()
    {
        $_SESSION['qtyTotal'] = $this->qtyTotal;
    }

    public function clearCart()
    {
        if (isset($_SESSION['order_id'])) {
            unset($_SESSION['order_id']);
        }
        if (isset($_SESSION['qtyTotal'])) {
            unset($_SESSION['qtyTotal']);
        }
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        if (isset($_SESSION['item_city'])) {
            unset($_SESSION['item_city']);
        }
        if (isset($_SESSION['item_branch'])) {
            unset($_SESSION['item_branch']);
        }
    }

    public static function showPriceStatic($price, $currency = 'UAH')
    {
        if ($price == 0) {
            return 0;
        }
        $fmt = new NumberFormatter('ru_RU', NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($price, $currency);
    }

    public function wholeNumber($qty)
    {
        if ((is_int($qty) || ctype_digit($qty)) && (int)$qty > 0) {
            return $qty;
        }
        return 0;
    }

    public function validateOrder()
    {
        if ($this->sumTotal < 1) {
            throw new \Exception('Сумма заказа не может быть меньше одного рубля');
        }

        if (!$this->items) {
            throw new \Exception('Корзина пуста');
        }

    }

    public function getUponReceiptPrice($sum)
    {
        $sum += $sum * (OrderPaymentUpdate::COMMISSION_PERCENTAGE / 100);
        $sum += OrderPaymentUpdate::COMMISSION_UAH;
        return $sum;
    }

    public function getOrderPriceWithBloggerBonus()
    {
        $bonusPayed = $this->sumPayByBonus;
        $sumOrder = (!empty($this->sumTotalDiscount) ? $this->sumTotalDiscount : $this->sumTotal);
        $discount = $this->additionalDiscountSum ?: 0;
        return ($sumOrder - $bonusPayed - $discount);
    }
}
