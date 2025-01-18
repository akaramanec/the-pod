<?php

namespace console\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\telegram\TCommon;
use backend\modules\shop\models\OrderPoll;
use backend\modules\shop\models\Poll;
use common\helpers\DieAndDumpHelper;
use src\helpers\Date;
use Yii;
use yii\console\Controller;
use yii\db\ActiveQuery;


/**
 *
 * @property-write OrderPoll $orderPollStatus
 */
class PollController extends Controller
{
    public function actionAfterSuccessPoll()
    {
        $poll = $this->afterSuccessPoll();
        $pollOrders = $this->afterSuccessOrderPollQuery($poll);

        /** @var OrderPoll $pollOrder */
        foreach ($pollOrders->each() as $pollOrder) {
            if ($pollOrder->order->customer->bot->platform == Bot::TELEGRAM) {
                $this->sendPollMessageTelegram($pollOrder, $poll);
                $this->setOrderPollStatus($pollOrder);
            }
        }
        BotLogger::save_input(['status' => 'ok'], __METHOD__);
    }

    private function afterSuccessPoll()
    {
        $poll = Poll::find()
            ->where(['status' => Poll::STATUS_ACTIVE])
            ->andWhere(['name' => Poll::AFTER_ORDER_SUCCESS])
            ->limit(1)
            ->one();

        if ($poll === null) {
            BotLogger::save_input([
                'message' => 'poll after order success inactive',
            ], __METHOD__);
            exit();
        }
        return $poll;
    }

    private function afterSuccessOrderPollQuery(Poll $poll): ActiveQuery
    {

        if (isset($poll->first_send_after)) {
            $firstDate = Date::minusPlus(Date::datetime_now(), "-$poll->first_send_after day");
        }

        if (isset($poll->second_send_after)) {
            $secondDate = Date::minusPlus(Date::datetime_now(), "-$poll->second_send_after day");
        }

        if (isset($firstDate) && isset($secondDate)) {
            $activeQuery = OrderPoll::find()
                ->alias('orderPoll')
                ->joinWith('poll as poll')
                ->where(['poll.name' => Poll::AFTER_ORDER_SUCCESS])
                ->andWhere(['or',
                    ['and',
                        ['orderPoll.status' => OrderPoll::STATUS_NOT_POLL],
                        ['<=', 'orderPoll.updated_at', $firstDate],
                    ],
                    ['and',
                        ['orderPoll.status' => OrderPoll::STATUS_FIRST_POLL],
                        ['<=', 'orderPoll.updated_at', $secondDate],
                    ]
                ]);
            return $activeQuery;
        }
        BotLogger::save_input([
            'message' => 'cant set query',
        ], __METHOD__);
        exit();
    }

    private function setOrderPollStatus(OrderPoll $pollOrder): void
    {
        if ($pollOrder->status == OrderPoll::STATUS_NOT_POLL) {
            $pollOrder->status = OrderPoll::STATUS_FIRST_POLL;
        } elseif ($pollOrder->status == OrderPoll::STATUS_FIRST_POLL) {
            $pollOrder->status = OrderPoll::STATUS_SECOND_POLL;
        }
        $pollOrder->save();
    }

    private function sendPollMessageTelegram(OrderPoll $pollOrder, Poll $poll)
    {
        Yii::$app->tm->customer = $pollOrder->order->customer;
        Yii::$app->tm->platformId = $pollOrder->order->customer->platform_id;
        $botSender = new TCommon();

        $buttons[] = [
            [
                'text' => 'Да',
                'callback_data' => json_encode([
                    'action' => '/TPoll_answer',
                    'o_p_id' => $pollOrder->id,
                    'a_id' =>  $pollOrder::ANSWER_YES
                ])
            ],

            [
                'text' => 'Нет',
                'callback_data' => json_encode([
                    'action' => '/TPoll_answer',
                    'o_p_id' => $pollOrder->id,
                    'a_id' =>  $pollOrder::ANSWER_NO
                ])
            ]
        ];
        $text = $poll->question;
        $botSender->button($text, $buttons);
    }
}
