<?php

namespace backend\modules\customer\models;

use backend\modules\bot\models\Bot;
use backend\modules\shop\models\AttributeValue;
use backend\modules\shop\models\CustomerFilter;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBloggerFixed;
use blog\models\CacheBloggerFixed;
use blog\models\CustomerBlog;
use blog\models\CustomerLinkReferral;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\db\PdoValue;
use yii\helpers\ArrayHelper;


/**
 *
 * @property-read mixed $parent
 * @property-read mixed $customerLinkReferral
 * @property-read mixed $attributeValue
 * @property-read mixed $bot
 * @property-read mixed $orderPayBloggerFixed
 * @property-read CustomerBlog $blog
 * @property-read void $tagsId
 * @property-read mixed $customerFilter
 * @property-read mixed $children
 * @property-read mixed $tag
 * @property-read mixed $tagLink
 * @property-read mixed $childrenCount
 * @property-read mixed $order
 * @property int $id [int(10) unsigned]
 * @property int $parent_id [int(10) unsigned]
 * @property int $bot_id [int(10) unsigned]
 * @property string $command [varchar(100)]
 * @property string $platform_id [varchar(100)]
 * @property bool $status [tinyint(4)]
 * @property string $phone [varchar(30)]
 * @property string $first_name [varchar(255)]
 * @property string $last_name [varchar(255)]
 * @property string $username [varchar(255)]
 * @property string $email [varchar(100)]
 * @property int $discount [int(11)]
 * @property bool $blogger [tinyint(3) unsigned]
 * @property string $img [varchar(40)]
 * @property string $updated_at [datetime]
 * @property string $created_at [datetime]
 * @property BotCustomerCard[] $cards
 * @property bool $black_list [tinyint(1)]
 * @property bool $regular_customer [tinyint(1)]
 */
class Customer extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 1;
    const STATUS_EDIT = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_BLACKLIST = 4;
    const STATUS_NEW = 5;
    const STATUS_SUBSCRIBED = 6;
    const STATUS_UNSUBSCRIBED = 8;

    const CUSTOMER_SITE = 1;

    const BLOGGER_FALSE = 1;
    const BLOGGER_TRUE = 3;

    const SELECT_STATUSES = [Customer::STATUS_SUBSCRIBED, Customer::STATUS_UNSUBSCRIBED, Customer::STATUS_ACTIVE];

    public $showPhone;
    public $tags = [];

    public $customerCountTotal;
    public $customerCountTotalDisabled;
    public $customerCountTotalActive;
    public $ordersCount;

    public $countCustomerAll;
    public $countCustomerAllSubscribed;
    public $countCustomerAllUnsubscribed;
    public $countCustomerAllActive;
    public $countCustomerL1All;
    public $countCustomerL2All;
    public $countCustomerL3All;
    public $countCustomerL1Subscribed;
    public $countCustomerL2Subscribed;
    public $countCustomerL3Subscribed;
    public $countCustomerL1Unsubscribed;
    public $countCustomerL2Unsubscribed;
    public $countCustomerL3Unsubscribed;
    public $countCustomerL1Active;
    public $countCustomerL2Active;
    public $countCustomerL3Active;
    public $countOrdersL1;
    public $countOrdersL2;
    public $countOrdersL3;
    public $countOrdersAll;
    public $lvl3Need = false;
    public $customersLevel;

    public static function tableName()
    {
        return 'bot_customer';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_id' => 'Bot',
            'parent_id' => 'Родитель',
            'platform_id' => 'Chat',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'username' => 'Username',
            'blogger' => 'Блогер',
            'phone' => 'Тел.',
            'email' => 'Email',
            'status' => 'Статус',
            'discount' => 'Скидка %',
            'created_at' => 'Добавлен',
            'tags' => 'Теги',
            'black_list' => 'Черный список',
            'regular_customer' => 'Постоянный клиент',
        ];
    }

    public function getOrder()
    {
        return $this->hasMany(Order::class, ['customer_id' => 'id']);
    }

    public function getParent()
    {
        return $this->hasOne(Customer::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Customer::class, ['parent_id' => 'id']);
    }

    public function getChildrenCount()
    {
        return $this->hasMany(Customer::class, ['parent_id' => 'id']);
    }

    public function getBlog()
    {
        return $this->hasOne(CustomerBlog::class, ['customer_id' => 'id']);
    }

    public function getBot()
    {
        return $this->hasOne(Bot::class, ['id' => 'bot_id']);
    }

    public function getTagLink()
    {
        return $this->hasMany(CustomerTagLink::class, ['customer_id' => 'id']);
    }

    public function getTag()
    {
        return $this->hasMany(CustomerTag::class, ['id' => 'tag_id'])->viaTable('bot_customer_tag_link', ['customer_id' => 'id']);
    }

    public function getAttributeValue()
    {
        return $this->hasMany(AttributeValue::class, ['id' => 'attribute_value_id'])->viaTable('shop_customer_filter', ['customer_id' => 'id']);
    }

    public function getCustomerFilter()
    {
        return $this->hasMany(CustomerFilter::class, ['customer_id' => 'id']);
    }

    public function getOrderPayBloggerFixed()
    {
        return $this->hasMany(OrderPayBloggerFixed::class, ['customer_id' => 'id']);
    }

    public function getCustomerLinkReferral()
    {
        return $this->hasMany(CustomerLinkReferral::class, ['customer_id' => 'id']);
    }

    public function afterFind()
    {
        $this->showPhone = $this->phone ? '+' . $this->phone : '';
    }

    public function checkAuthAllCustomer()
    {
        return $this->phone &&
            $this->first_name &&
            $this->last_name &&
            $this->status == Customer::STATUS_ACTIVE;
    }

    public function getTagsId()
    {
        $this->tags = ArrayHelper::getColumn($this->tagLink, 'tag_id');
    }

    public function saveTags()
    {
        CustomerTagLink::deleteAll(['customer_id' => $this->id]);
        if ($this->tags) {
            foreach ($this->tags as $tag_id) {
                $t = new CustomerTagLink();
                $t->customer_id = $this->id;
                $t->tag_id = $tag_id;
                $t->save();
            }
        }
    }

    public static function listBlogger()
    {
        $blogger = self::find()
            ->where(['blogger' => self::BLOGGER_TRUE])
            ->andWhere(['in', 'status', [self::STATUS_ACTIVE, self::STATUS_SUBSCRIBED]])
            ->orderBy('last_name asc')->all();
        $b = [];
        foreach ($blogger as $item) {
            $b[$item->id] = self::fullName($item);
        }
        return $b;
    }

    public static function fullName($customer)
    {
        return $customer->last_name . ' ' . $customer->first_name;
    }

    public static function fullNameParent($customer)
    {
        if ($customer && isset($customer->parent)) {
            return $customer->parent->last_name . ' ' . $customer->parent->first_name;
        }
    }

    public function parentFullName()
    {
        if ($this->parent) {
            return $this->parent->last_name . ' ' . $this->parent->first_name;
        }
    }

    public function getCards()
    {
        return $this->hasMany(BotCustomerCard::class, ['bot_customer_id' => 'id']);
    }

    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_SUBSCRIBED:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_ACTIVE:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_INACTIVE:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_UNSUBSCRIBED:
                $s = '<div class="badge badge-default text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_SUBSCRIBED => 'Подписался',
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_INACTIVE => 'Не активен',
            self::STATUS_UNSUBSCRIBED => 'Unsubscribed',
        ];
    }

    public static function statusBlogger($status)
    {
        switch ($status) {
            case self::BLOGGER_FALSE:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesBloggerAll()[$status] . '</div>';
                break;
            case self::BLOGGER_TRUE:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesBloggerAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesBloggerAll()
    {
        return [
            self::BLOGGER_FALSE => 'Нет',
            self::BLOGGER_TRUE => 'Блогер',
        ];
    }

    public static function customerMarks()
    {
        return [
            'black_list' => 'Черный список',
            'regular_customer' => 'Постоянный клиент',
            '' => ' '
        ];
    }

    public function linkRefTm()
    {
        return Yii::$app->params['chatTm'] . '?start=' . $this->id;
    }

    public function linkRefVb()
    {
        return Yii::$app->params['homeUrl'] . '/referral-vb/' . $this->id;
    }

    public function countCustomerSubscribed($dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT COUNT(*) FROM bot_customer WHERE parent_id=:parent_id AND status=:status";
        $params = [
            ':parent_id' => $this->id,
            ':status' => Customer::STATUS_SUBSCRIBED
        ];
        if ($dateFrom && $dateTo) {
            $params[':dateFrom'] = $dateFrom;
            $params[':dateTo'] = $dateTo;
            $sql .= " AND created_at BETWEEN :dateFrom AND :dateTo";
        }
        return Yii::$app->db->createCommand($sql, $params)->queryScalar();
    }

    public static function byPlatformAndTag($platform, $tag)
    {
        return self::find()
            ->alias('customer')
            ->joinWith(['bot AS bot', 'tagLink AS tagLink'])
            ->where(['customer.status' => Customer::STATUS_ACTIVE])
            ->andWhere(['!=', 'customer.id', self::CUSTOMER_SITE])
            ->andWhere(['!=', 'customer.id', Yii::$app->params['groupTmCustomerId']])
            ->orWhere(['tagLink.tag_id' => $tag])
            ->andWhere(['bot.platform' => $platform]);
    }

    public static function byPlatform($platform)
    {
        return self::find()
            ->alias('customer')
            ->joinWith(['bot AS bot'])
            ->where(['customer.status' => Customer::STATUS_ACTIVE])
            ->andWhere(['!=', 'customer.id', self::CUSTOMER_SITE])
            ->andWhere(['bot.platform' => $platform]);
    }

    public static function fullPhoneFormat(string $phone): string
    {
        switch (strlen($phone)) {
            case 12:
                $phone = '+' . $phone;
                break;
            case 10:
                $phone = '+38' . $phone;
                break;
            case 9:
                $phone = '+380' . $phone;
                break;
        }
        return $phone;
    }

//    /** @var array $date [dateFrom => date, dateTo => date] */
//    public function getCustomerCountTotal(array $date)
//    {
//        $cacheBloggerFixed = new CacheBloggerFixed();
//        $cacheBloggerFixed->setCache($this, $date);
//        return $cacheBloggerFixed->blog->customerCount ?? 0;
//    }
//
//    /** @var array $date [dateFrom => date, dateTo => date] */
//    public function getCustomerCount(array $date)
//    {
//        $cacheBloggerFixed = new CacheBloggerFixed();
//        $cacheBloggerFixed->setCache($this, $date);
//        return $cacheBloggerFixed->blog->customerCount ?? 0;
//    }
//
//    /** @var array $date [dateFrom => date, dateTo => date] */
//    public function getOrdersCount(array $date)
//    {
//        $cacheBloggerFixed = new CacheBloggerFixed();
//        $cacheBloggerFixed->setCache($this, $date);
//        return $cacheBloggerFixed->ordersCount ?? 0;
//    }
//
//    /** @var array $date [dateFrom => date, dateTo => date] */
//    public function getSumDebt(array $date)
//    {
//        $cacheBloggerFixed = new CacheBloggerFixed();
//        $cacheBloggerFixed->setCache($this, $date);
//        return $cacheBloggerFixed->blog->sumDebt ?? 0;
//    }
//
//    /** @var array $date [dateFrom => date, dateTo => date] */
//    public function getSumTotalPayed(array $date)
//    {
//        $cacheBloggerFixed = new CacheBloggerFixed();
//        $cacheBloggerFixed->setCache($this, $date);
//        return $cacheBloggerFixed->blog->sumTotalPayed ?? 0;
//    }

    public static function blackList($black_list)
    {
        return $black_list ? '<div class="badge badge-dark text-wrap">Черный список</div>' : '';
    }

    public static function regularCustomer($regular_customer)
    {
        return $regular_customer ? '<div class="badge badge-success text-wrap">Постоянный клиент</div>' : '';
    }

    public function setAllBloggerData()
    {

        if ($this->lvl3Need) {
            $this->customersLevel = 3;
        } else {
            $this->customersLevel = 2;
        }
        $this->setAllOrdersData();
        $this->setAllForOneLevel();
        $this->setTotal();
    }

    public function getCustomersL1Ids(array $statuses = self::SELECT_STATUSES)
    {
        $query = (new \yii\db\Query())->select('id')
            ->from('bot_customer')
            ->where(['parent_id' => $this->id])
            ->andWhere(['in', 'status', $statuses]);

        return ArrayHelper::getColumn($query->all(), function ($element) {
            return (int)$element['id'];
        });
    }

    public function countCustomerL1(array $statuses)
    {
        return count($this->getCustomersL1Ids($statuses));
    }

    public function countOrdersL1()
    {
        $ids = $this->getCustomersL1Ids();
        return $this->getOrdersCountSumByIds($ids);
    }

    public function getCustomersL2Ids(array $statuses = self::SELECT_STATUSES)
    {
        $query = (new \yii\db\Query())->select('id')
            ->from('bot_customer')
            ->where(['parent_id' => $this->getCustomersL1Ids()])
            ->andWhere(['in', 'status', $statuses]);

        return ArrayHelper::getColumn($query->all(), function ($element) {
            return (int)$element['id'];
        });
    }

    public function countCustomerL2(array $statuses)
    {
        return count($this->getCustomersL2Ids($statuses));
    }

    public function countOrdersL2()
    {
        $ids = $this->getCustomersL2Ids();
        return $this->getOrdersCountSumByIds($ids);
    }

    public function getCustomersL3Ids(array $statuses = self::SELECT_STATUSES)
    {
        $query = (new \yii\db\Query())->select('id')
            ->from('bot_customer')
            ->where(['parent_id' => $this->getCustomersL2Ids()])
            ->andWhere(['in', 'status', $statuses]);

        return ArrayHelper::getColumn($query->all(), function ($element) {
            return (int)$element['id'];
        });
    }

    public function countCustomerL3(array $statuses)
    {
        return count($this->getCustomersL3Ids($statuses));
    }

    public function countOrdersL3()
    {
        $ids = $this->getCustomersL3Ids();
        return $this->getOrdersCountSumByIds($ids);
    }

    /**
     * @param array $ids
     * @return array ['count', 'sum']
     */
    private function getOrdersCountSumByIds(array $ids): array
    {
        $query = (new \yii\db\Query())->select(['COUNT(id) AS count', 'SUM(cache_sum_total) AS sum'])
            ->from('shop_order')
            ->where(['customer_id' => $ids])
            ->andWhere(['status' => Order::STATUS_CLOSE_SUCCESS]);

        $query = $query->all();
        $data = [
            'count' => $query[0]['count'],
            'sum' => $query[0]['sum']
        ];
        return $data;
    }

    public static function  selectStatusType($selectStatus)
    {
        return self::selectStatusTypes()[$selectStatus];
    }

    public static function selectStatusTypes()
    {
        return [
            Customer::STATUS_ACTIVE => 'Active',
            Customer::STATUS_SUBSCRIBED => 'Subscribed',
            Customer::STATUS_UNSUBSCRIBED => 'Unsubscribed'
        ];
    }

    private function setAllForOneLevel(): void
    {
        for ($i = 1; $i <= $this->customersLevel; $i++) {
            $levelCountCustomerFunction = 'countCustomerL' . $i;
            $levelAll = $levelCountCustomerFunction . 'All';
            $this->{$levelAll} = $this->{$levelCountCustomerFunction}(self::SELECT_STATUSES);
            foreach (self::selectStatusTypes() as $status => $valueType) {
                $levelValue = $levelCountCustomerFunction . $valueType;
                $this->{$levelValue} = $this->{$levelCountCustomerFunction}([$status]);
            }
        }
    }

    private function setTotal(): void
    {
        $all = 'countCustomerAll';
        foreach (self::selectStatusTypes() as $valueType) {
            $allValue = $all . $valueType;
            $this->{$allValue} = 0;
            for ($i = 1; $i <= $this->customersLevel; $i++) {
                $level = 'countCustomerL' . $i;
                $levelValue = $level . $valueType;
                $this->{$allValue} += $this->{$levelValue};
            }
        }

        $this->{$all} = 0;
        for ($i = 1; $i <= $this->customersLevel; $i++) {
            $level = 'countCustomerL' . $i;
            $levelAll = $level . 'All';
            $this->{$all} += $this->{$levelAll};
        }
    }

    private function setAllOrdersData()
    {
        $this->countOrdersAll['count'] = 0;
        $this->countOrdersAll['sum'] = 0;
        $this->countOrdersAll['percent'] = 0;
        $percent = 'percent_level_';
        for ($i = 1; $i <= $this->customersLevel; $i++) {
            $level = 'countOrdersL' . $i;
            $percentLevel = $percent . $i;
            $this->{$level} = $this->{$level}();
            $this->{$level}['percent'] = $this->{$level}['sum'] - ((1 - $this->blog->{$percentLevel} / 100) * $this->{$level}['sum']);
            $this->countOrdersAll['count'] += $this->{$level}['count'];
            $this->countOrdersAll['sum'] += $this->{$level}['sum'];
            $this->countOrdersAll['percent'] += $this->{$level}['percent'];
        }
    }
}
