<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\BloggerWithdrawalRequestService;
use backend\modules\bot\src\storage\BloggerWithdrawalRequestStorage;
use backend\modules\customer\models\BloggerWithdrawalRequest;
use backend\modules\customer\models\BotCustomerCard;
use backend\modules\customer\models\Customer;
use backend\modules\system\models\SettingItem;
use blog\models\CacheBlogger;
use blog\models\CacheBloggerFixed;
use blog\models\CustomerBlog;
use frontend\models\cart\Cart;
use Yii;

class TCabinet extends TCommon
{
    const SESSION_NAME = 'TCabinetSession';

    const BACK_TO_GET_INFO = 'start';
    const BACK_TO_MY_BONUS = 'bonus';
    const BACK_TO_GET_MONEY = 'getMoney';
    const BACK_TO_SELECT_MONEY = 'selectMoney';

    /** @var Customer $customer */
    protected $customer;

    /** @var float $minWithdrawSum */
    private $minWithdrawSum = 500;

    public function __construct()
    {
        parent::__construct();

        $settingItemModel = SettingItem::findOne(['slug' => 'minSumWithdrawalRequest']);
//        $this->minWithdrawSum = isset($settingItemModel) ? floatval($settingItemModel->value) : 500;
        $this->customer = \Yii::$app->tm->customer;
    }

    /* Стартовое сообзение */
    public function getInfo()
    {
        $this->delMsg(true);

        $text = $this->text('messageSelectReferralDivision');

        $buttons = null;

        switch ($this->customer->blogger) {
            case Customer::BLOGGER_FALSE:
                $buttons[] = [
                    ["text" => $this->text('buttonSmokeFree'), "callback_data" => $this->encode(['action' => '/TCabinet_smokeFree'])]
                ];
                break;
            case Customer::BLOGGER_TRUE:
                $buttons[][] = ["text" => $this->text('buttonMyBonus'), "callback_data" => $this->encode(['action' => '/TCabinet_myBonus'])];
                $buttons[][] = ["text" => $this->text('buttonStatistics'), "callback_data" => $this->encode(['action' => '/TCabinet_statistics'])];
                $buttons[][] = ["text" => $this->text('buttonCopyLink'), "callback_data" => $this->encode(['action' => '/TCabinet_copyLink'])];
                break;
        }

        $response = $this->button($text, $buttons);

        if ($response['ok'] && isset($response['result']) && isset($response['result']['message_id'])) {
            $this->initSession($response['result']['message_id']);
        }

        return true;
    }


    /* Стать блогером (START)*/
    public function smokeFree()
    {
        $text = $this->text('messageSmokeFree');
        $buttons = [
            [
                ["text" => $this->text('buttonWant'), "callback_data" => $this->encode(['action' => '/TCabinet_makeBlogger'])]
            ],
            [
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_GET_INFO])]
            ]
        ];

        $this->sendOrEditMessage($text, $buttons);

        return true;
    }

    public function makeBlogger()
    {

        if (Yii::$app->tm->customer->status != Customer::STATUS_ACTIVE) {
            $this->session->set('createBlogger', '/TCabinet_makeBlogger');
            $this->deleteMessage();
            return Yii::$app->tm->action('/TAuth_phone');
        }

        $referral = $this->customer->linkRefTm();
        $this->customer->blogger = Customer::BLOGGER_TRUE;
        if (!empty($referral) && $this->customer->save()) {
            $customerBlog = CustomerBlog::getModel($this->customer);
            $customerBlog::saveModel($customerBlog, $this->customer);
            $this->delMsg(true);

            $this->sendMessage($this->text('messageCreatedReferral'));

            $referralText = $this->text('messageReferral');

            if ($this->customer->bot->platform == Bot::TELEGRAM) {

                $placeholder['{{referral_link}}'] = $referral ?? '';
                $referralText = strtr($referralText, $placeholder);
                $this->sendMessage($referralText);
            }
        }

    }
    /* Стать блогером (END)*/

    /* Получить силку блогура */
    public function copyLink()
    {
        $referral = $this->customer->linkRefTm();
        $referralText = $this->text('messageReferral');
        if ($this->customer->bot->platform == Bot::TELEGRAM) {
            $placeholder['{{referral_link}}'] = $referral ?? '';
            $referralText = strtr($referralText, $placeholder);
            $buttons = [[
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_GET_INFO])]
            ]];
            $this->sendOrEditMessage($referralText, $buttons);
        }
    }

    /* проверка бонусов */
    public function myBonus()
    {
        $buttons = [];
        if (!empty($this->customer->blog->customerCount)) {

            $text = $this->text('messageMyBonus');
            $placeholder = [
                '{{sumTotalOrders}}' => $this->customer->blog->sumTotalOrders,
                '{{sumTotalPayed}}' => $this->customer->blog->sumTotalPayed,
                '{{sumTotalDebit}}' => round($this->customer->blog->sumDebt, 2),
            ];

            $text = strtr($text, $placeholder);
            BotLogger::save_input($this->customer->blog);
//            if ($this->customer->blog->sumDebt > $this->minWithdrawSum) {
                $buttons[][] = ["text" => $this->text('buttonGetMoney'), "callback_data" => $this->encode(['action' => '/TCabinet_getMoney'])];
//            }


        } else {
            $text = $this->text('messageMyBonusNo');
            $buttons[][] = ["text" => $this->text('buttonCopyLink'), "callback_data" => $this->encode(['action' => '/TCabinet_copyLink'])];
        }

        $buttons[][] = ["text" => $this->text('buttonReferralInfo'), "callback_data" => $this->encode(['action' => '/TCabinet_referralInfo'])];
        $buttons[][] = ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_GET_INFO])];

        $this->sendOrEditMessage($text, $buttons);
        return true;
    }

    /* проверка бонусов */
    public function statistics()
    {
        $buttons = [];
        if (!empty($this->customer->blog->customerCount)) {

            $text = $this->text('messageMyStatistics');
            $levelOne = 0;
            $levelTwo = 0;
            foreach ($this->customer->blog->customerLevel as $item) {
                switch ($item['level']) {
                    case 1 :
                        $levelOne++;
                        break;
                    case 2:
                        $levelTwo++;
                        break;
                }
            }
            $placeholder = [
                '{{customersCount}}' => $this->customer->blog->customerCount,
                '{{customersLevel1}}' => $levelOne,
                '{{customersLevel2}}' => $levelTwo,
                '{{ordersCount}}' => $this->customer->blog->ordersCount
            ];
            $text = strtr($text, $placeholder);
            BotLogger::save_input($this->customer->blog);
        } else {
            $text = $this->text('messageMyBonusNo');
            $buttons[][] = ["text" => $this->text('buttonCopyLink'), "callback_data" => $this->encode(['action' => '/TCabinet_copyLink'])];
        }

        $buttons[][] = ["text" => $this->text('buttonReferralInfo'), "callback_data" => $this->encode(['action' => '/TCabinet_referralInfo'])];
        $buttons[][] = ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_GET_INFO])];

        $this->sendOrEditMessage($text, $buttons);
        return true;
    }

    /* 📖 Как это работает */
    public function referralInfo()
    {
        $text = $this->text('messageReferralInfo');

        $placeholder = [
            '{{minSumExchange}}' => $this->minWithdrawSum,
        ];

        $text = strtr($text, $placeholder);

        $buttons = [
            [
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_MY_BONUS])]
            ],
        ];
        $this->sendOrEditMessage($text, $buttons);
    }

    /* 💳 Вывод средст (START) */
    public function getMoney()
    {
//        messageNoWithdrawalMoney


        if ($this->customer->blog->sumDebt > $this->minWithdrawSum) {
            $text = $this->text('messageGetMoney');

            $placeholder = [
                '{{sumDebt}}' => round($this->customer->blog->sumDebt, 2),
            ];

            $text = strtr($text, $placeholder);

            $buttons = [
                [
                    ["text" => $this->text('buttonWithdrawAll'), "callback_data" => $this->encode(['action' => '/TCabinet_selectSum', 'all' => true])]
                ],
                [
                    ["text" => $this->text('buttonWithdrawPart'), "callback_data" => $this->encode(['action' => '/TCabinet_selectSum', 'all' => false])]
                ],
                [
                    ["text" => $this->text('buttonByPod'), "callback_data" => $this->encode(['action' => '/TCatalog_clickKeyboardQuickOrder'])]
                ]
            ];
        } else {
            $text = $this->text('messageNoWithdrawalMoney');

            $placeholder = [
                '{{minWithdrawSum}}' => $this->minWithdrawSum,
                '{{lefSumUntilWithdrawal}}' => $this->minWithdrawSum - round($this->customer->blog->sumDebt, 2),
            ];

            $text = strtr($text, $placeholder);
        }


        $buttons[][] = ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_MY_BONUS])];


        $this->sendOrEditMessage($text, $buttons);

    }

    public function selectSum($backAction = false)
    {
        $isAllSum = Yii::$app->tm->data->all ?? true;

        if ((!$isAllSum) || $backAction) {
            $this->saveCommand('/TCabinet_saveSum');

            $text = $this->text('messageEnterWithdrawSum');

            $placeholder = [
                '{{sumMin}}' => $this->minWithdrawSum,
                '{{sumMax}}' => round($this->customer->blog->sumDebt, 2),
            ];

            $text = strtr($text, $placeholder);
            $buttons = [[
                ["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCabinet_back', 'type' => self::BACK_TO_GET_MONEY])]
            ]];

            $this->sendOrEditMessage($text, $buttons);

        } else {
            BloggerWithdrawalRequestService::setSum($this->customer->id, (float)round($this->customer->blog->sumDebt, 2));
            $this->selectCard();
        }

    }

    public function saveSum()
    {

        $this->updateSessionDeleteIds([\Yii::$app->tm->messageId]);

        $placeholder = [
            ',' => '.',
        ];

        $sum = strtr(Yii::$app->tm->data->value, $placeholder);

        if (is_numeric($sum) && $sum >= $this->minWithdrawSum && $sum <= $this->customer->blog->sumDebt) {
            BloggerWithdrawalRequestService::setSum($this->customer->id, (float)$sum);
            $this->delMsg();
            $this->selectCard();
        } else {
            $text = $this->text('messageErrorSumValidate');
            $response = $this->sendMessage($text);
            if ($response['ok'] && isset($response['result']) && isset($response['result']['message_id'])) {
                $this->updateSessionDeleteIds([$response['result']['message_id']]);
            }
        }
    }

    public function selectCard()
    {

        $buttons = [];
        $isCreatCard = Yii::$app->tm->data->create ?? false;
        if (empty($this->customer->cards) || $isCreatCard) {
            $this->saveCommand('/TCabinet_saveCard');
            $text = $this->text('messageEnterYourCard');
        } else {
            $text = $this->text('messageSelectYourCard');
            $this->selectCardButtons($buttons);
        }

        $buttons[][] = ["text" => $this->text('buttonEditSum'),
            "callback_data" => $this->encode([
                'action' => '/TCabinet_back',
                'type' => self::BACK_TO_SELECT_MONEY
            ])];

        $this->sendOrEditMessage($text, $buttons);

    }

    private function selectCardButtons(array &$buttons)
    {
        $cards = $this->customer->cards;
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $buttons[][] = [
                    "text" => $card->number,
                    "callback_data" => $this->encode([
                        'action' => '/TCabinet_saveCard',
                        'card' => $card->id
                    ])
                ];
            }
        }

        $buttons[][] = [
            "text" => $this->text('buttonAddCard'),
            "callback_data" => $this->encode([
                'action' => '/TCabinet_selectCard',
                'create' => true
            ])
        ];
    }

    public function saveCard()
    {
        $cardModel = null;

        if (!empty(Yii::$app->tm->data->card)) {
            $cardModel = BotCustomerCard::findOne(Yii::$app->tm->data->card);
            BloggerWithdrawalRequestService::setCardId($this->customer->id, $cardModel->id);
        } elseif (!empty(Yii::$app->tm->data->value)) {
            $this->updateSessionDeleteIds([\Yii::$app->tm->messageId]);
            $placeholder = [
                ' ' => '',
            ];

            $card = strtr(Yii::$app->tm->data->value, $placeholder);

            $pattern = "/^[1-9]{1}[0-9]{15}$/";
            if (preg_match($pattern, $card)) {
                $cardId = $this->getSessionValue('editCardId');
                $cardModel = !empty($cardId) ? BotCustomerCard::findOne($cardId) : new BotCustomerCard();
                $cardModel->number = $card;
                $cardModel->bot_customer_id = $this->customer->id;
                if ($cardModel->save()) {
                    $this->dellSessionValue('editCardId');
                    BloggerWithdrawalRequestService::setCardId($this->customer->id, $cardModel->id);
                }
            }
        }

        if (!empty($cardModel)) {
            $placeholderMessage = [
                '{{cardNumber}}' => $cardModel->number,
            ];

            $message = strtr($this->text('messageApproveCard'), $placeholderMessage);

            $buttons = [
                [
                    ["text" => $this->text('buttonApprove'), "callback_data" => $this->encode(['action' => '/TCabinet_approveCard'])]
                ],
                [
                    ["text" => $this->text('buttonEditCard'), "callback_data" => $this->encode(['action' => '/TCabinet_editCardd', 'card' => $cardModel->id])]
                ]
            ];
            $this->delMsg();
            $this->sendOrEditMessage($message, $buttons);

        } else {
            $response = $this->sendMessage("messageErrorCardValidate");
            if ($response['ok'] && isset($response['result']) && isset($response['result']['message_id'])) {
                $this->updateSessionDeleteIds([$response['result']['message_id']]);
            }
        }

    }

    public function editCardd()
    {
        if (!empty(Yii::$app->tm->data->card)) {
            $this->addSessionValue('editCardId', Yii::$app->tm->data->card);
            $this->saveCommand('/TCabinet_saveCard');
            $text = $this->text('messageEnterYourCard');

            $buttons[][] = ["text" => $this->text('buttonEditSum'),
                "callback_data" => $this->encode([
                    'action' => '/TCabinet_back',
                    'type' => self::BACK_TO_SELECT_MONEY
                ])
            ];

            $this->sendOrEditMessage($text, $buttons);

        }

        return true;
    }

    public function approveCard()
    {
        $modelRequest = BloggerWithdrawalRequestService::setStatusNew($this->customer->id);

        if ($modelRequest) {
            $this->customer->blog->sumDebt -= $modelRequest->sum;
            $this->customer->blog->save();
            $cacheBlogger = new CacheBloggerFixed();
            $cacheBlogger->customerBlogger = $this->customer;
            $cacheBlogger->blog = $this->customer->blog;
            $cacheBlogger->setCache($cacheBlogger->customerBlogger);
        }

        $this->delMsg(true);
        $this->sendMessage($this->text('messageSuccessWithdrawalRequest'));

        return true;
    }

    /* 💳 Вывод средст (END) */

    /* Действия назад */
    public function back()
    {
        $this->saveCommand(null);

        switch (Yii::$app->tm->data->type) {
            case self::BACK_TO_GET_INFO:
                $this->getInfo();
                break;
            case self::BACK_TO_MY_BONUS:
                $this->myBonus();
                break;
            case self::BACK_TO_GET_MONEY:
                $this->getMoney();
                break;
            case self::BACK_TO_SELECT_MONEY:
                $this->selectSum(true);
                break;
        }

        return true;
    }

    /* -----------------------------< Cabinet Session >------------------------------------------- */

    private function initSession($messageId)
    {
        $this->delMsg(false);
        $this->session->set(self::SESSION_NAME, [
            'mainMessageId' => $messageId ?? \Yii::$app->tm->messageId,
            'deleteIds' => [],//[$messageId ?? \Yii::$app->tm->messageId]
        ]);
    }

    private function updateSessionMessageId(int $messageId)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        $sessionData['mainMessageId'] = $messageId;
        $this->session->set(self::SESSION_NAME, $sessionData);
    }

    private function updateSessionDeleteIds(array $messageIds)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        foreach ($messageIds as $messageId) {
            array_unshift($sessionData['deleteIds'], $messageId);
        }
        $this->session->set(self::SESSION_NAME, $sessionData);
    }

    private function getSessionMainMessageId()
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        return $sessionData['mainMessageId'] ?? null;
    }

    private function addSessionValue($key, $value)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        $sessionData[$key] = $value;
        $this->session->set(self::SESSION_NAME, $sessionData);
    }

    private function getSessionValue($key, $isDelete = false)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        $value = $sessionData[$key] ?? null;

        if (!empty($value) && $isDelete) {
            unset($sessionData[$key]);
            $this->session->set(self::SESSION_NAME, $sessionData);
        }

        return $value;
    }

    private function dellSessionValue($key)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);
        unset($sessionData[$key]);
        $this->session->set(self::SESSION_NAME, $sessionData);
    }

    private function delMsg($isDelMainMessage = false)
    {
        $sessionData = $this->session->get(self::SESSION_NAME);

        if (isset($sessionData['deleteIds'])) {
            foreach ($sessionData['deleteIds'] as $message_id) {
                $this->deleteMessageByMessageId($message_id);
            }
            $sessionData['deleteIds'] = [];
        }

        if ($isDelMainMessage && isset($sessionData['mainMessageId'])) {
            $this->deleteMessageByMessageId($sessionData['mainMessageId']);
            $sessionData['mainMessageId'] = null;
        }

        $this->session->set(self::SESSION_NAME, $sessionData);
    }

    private function sendOrEditMessage($text, $buttons)
    {
        $messageId = $this->getSessionMainMessageId();

        if ($messageId) {
            $this->edit($text, $buttons, $messageId);
        } else {
            $response = $this->button($text, $buttons);
            if ($response['ok'] && isset($response['result']) && isset($response['result']['message_id'])) {
                $this->updateSessionMessageId($response['result']['message_id']);
            }
        }
    }
    /* ----------------------------- </ Cabinet Session >------------------------------------------- */
}