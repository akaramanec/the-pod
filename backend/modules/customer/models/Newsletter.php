<?php


namespace backend\modules\customer\models;

use backend\modules\customer\models\search\CustomerSearchNewsletter;
use backend\modules\media\models\FileSave;
use backend\modules\media\models\Images;
use backend\modules\media\models\ImgSave;
use DateTime;
use src\behavior\Timestamp;
use src\helpers\DieAndDumpHelper;
use Yii;

/**
 * @property int $id
 * @property string $text
 * @property string $setting
 * @property string|null $img
 * @property string|null $video
 * @property int $status
 * @property int $admin_id
 * @property string|null $created_at
 * @property-read mixed $images
 * @property string|null $updated_at
 * @property string $date_departure [datetime]
 */
class Newsletter extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_SEND = 2;
    const STATUS_IN_WORK = 3;
    const STATUS_SENT = 4;
    const STATUS_ERROR = 5;
    const ENTITY = NEWSLETTER;


    public static function tableName()
    {
        return 'newsletter';
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImgSave::class,
                'entityImg' => NEWSLETTER,
            ],
            [
                'class' => FileSave::class,
                'entityFile' => NEWSLETTER,
            ],
            [
                'class' => Timestamp::class,
            ],
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_NEW],
            [['status', 'admin_id'], 'required'],
            [['status', 'admin_id'], 'integer'],
            [['text'], 'string'],
            [['setting', 'date_departure',
                'created_at', 'updated_at', 'sendTelegram', 'activeCustomer', 'subscribedCustomer',
                'sendEmail', 'sendViber', 'tag_customer', 'tagsId', 'sendNow'], 'safe'],
            [['img', 'video'], 'string', 'max' => 50],
            [['mainImg'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 1],
            [['mainFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp4', 'maxFiles' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Сообщение',
            'setting' => 'Настройки',
            'img' => 'Img',
            'video' => 'Video',
            'mainImg' => 'Изображение',
            'mainFile' => 'Видео',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'sendEmail' => 'Отправить и на почту',
            'tagsId' => 'Теги',
            'sendNow' => 'Отправить сейчас',
            'date_departure' => 'Дата отправки',
            'customerBlogger' => 'Блогер',
            'notCustomerBlogger' => 'Не блогер',
            'activeCustomer' => 'Активный',
            'subscribedCustomer' => 'Подписавшийся',
        ];
    }

    public function getImages()
    {
        return $this->hasMany(Images::class, ['entity_id' => 'id'])->andWhere(['entity' => NEWSLETTER]);
    }

    public function status()
    {
        $css = [
            self::STATUS_SEND => 'primary',
            self::STATUS_IN_WORK => 'warning',
            self::STATUS_SENT => 'default'
        ];
        $count = '';
        if ($this->status == self::STATUS_IN_WORK) {
            $total = NewsletterMessages::find()->where(['newsletter_id' => $this->id])->count();
            $sent_total = NewsletterMessages::find()->where(['newsletter_id' => $this->id])->andWhere(['status' => self::STATUS_SENT])->count();
            $error_total = NewsletterMessages::find()->where(['newsletter_id' => $this->id])->andWhere(['status' => self::STATUS_ERROR])->count();
            $count = '<br> Отправлено - ' . $sent_total . "\nОшибка - " . $error_total . "\nУчаствующих - " . $total . "</br>";
        }
        return '<div class="badge badge-' . $css[$this->status] . ' text-wrap">' . $this->statusesAll()[$this->status] . $count .  '</div>';
    }

    public function statusesAll()
    {
        return [
            self::STATUS_SEND => 'В очереди',
            self::STATUS_IN_WORK => 'В работе',
            self::STATUS_SENT => 'Отправлено'
        ];
    }

    public function dateDeparture()
    {
        if ((int)$this->sendNow) {
            $this->date_departure = Yii::$app->common->datetimeNow;
        }
        if (!$this->date_departure) {
            throw new \Exception('Вы не указали дату отправки');
        }
        if (new DateTime(Yii::$app->common->datetimeNow) > new DateTime($this->date_departure)) {
            throw new \Exception('Дата отправки меньше текущей');
        }
    }

    public function checkContent()
    {
        if (empty($this->text)) {
            throw new \Exception('Контент отсутствует');
        }
    }

    public $qtyCustomer = 0;
    public $customerBlogger;
    public $notCustomerBlogger;
    public $activeCustomer;
    public $subscribedCustomer;
    public $sendTelegram;
    public $sendViber;
    public $sendEmail;
    public $tagsId = [];
    public $sendNow;

    public function setSetting()
    {
        if (isset($this->setting['qtyCustomer'])) {
            $this->qtyCustomer = $this->setting['qtyCustomer'];
        }
        if (isset($this->setting['customerBlogger'])) {
            $this->customerBlogger = $this->setting['customerBlogger'];
        }
        if (isset($this->setting['notCustomerBlogger'])) {
            $this->notCustomerBlogger = $this->setting['notCustomerBlogger'];
        }
        if (isset($this->setting['activeCustomer'])) {
            $this->activeCustomer = $this->setting['activeCustomer'];
        }
        if (isset($this->setting['subscribedCustomer'])) {
            $this->subscribedCustomer = $this->setting['subscribedCustomer'];
        }
        if (isset($this->setting['sendTelegram'])) {
            $this->sendTelegram = $this->setting['sendTelegram'];
        }
        if (isset($this->setting['sendViber'])) {
            $this->sendViber = $this->setting['sendViber'];
        }
        if (isset($this->setting['sendEmail'])) {
            $this->sendEmail = $this->setting['sendEmail'];
        }
        if (isset($this->setting['tagsId'])) {
            $this->tagsId = $this->setting['tagsId'];
        }
        if (isset($this->setting['sendNow'])) {
            $this->sendNow = $this->setting['sendNow'];
        }
    }

    public function buildSetting()
    {
        $this->setting = [
            'qtyCustomer' => $this->qtyCustomer,
            'customerBlogger' => $this->customerBlogger,
            'activeCustomer' => $this->activeCustomer,
            'subscribedCustomer' => $this->subscribedCustomer,
            'notCustomerBlogger' => $this->notCustomerBlogger,
            'sendTelegram' => $this->sendTelegram,
            'sendViber' => $this->sendViber,
            'sendEmail' => $this->sendEmail,
            'tagsId' => $this->tagsId,
            'sendNow' => $this->sendNow,
        ];
    }

    public function saveMessages()
    {
        $this->setSetting();
        $search = new CustomerSearchNewsletter($this);
        if ($search->dataProvider->count > 0) {
            NewsletterMessages::deleteAll(['newsletter_id' => $this->id]);
            foreach ($search->dataProvider->getKeys() as $customer_id) {
                $message = new NewsletterMessages();
                $message->newsletter_id = $this->id;
                $message->customer_id = $customer_id;
                $message->save();
            }
        }
    }

    public function setQtyCustomer()
    {
        if ($this->customerId) {
            $this->qtyCustomer = count($this->customerId);
        }
    }

    public static function getModel()
    {
        $model = self::find()
            ->where(['status' => self::STATUS_NEW])
            ->andWhere(['admin_id' => Yii::$app->user->id])
            ->limit(1)->one();
        if ($model) {
            return $model;
        }
        $model = new self();
        $model->admin_id = Yii::$app->user->id;
        $model->save();
        return $model;
    }

}
