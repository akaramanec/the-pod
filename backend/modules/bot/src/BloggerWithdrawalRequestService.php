<?php

namespace backend\modules\bot\src;

use backend\modules\customer\models\BloggerWithdrawalRequest;

class BloggerWithdrawalRequestService
{

    public static function setSum(int $customerId, float $sum)
    {
        $modelWithdrawalRequest = self::getBloggerWithdrawalRequest($customerId);
        $modelWithdrawalRequest->sum = $sum;
        $modelWithdrawalRequest->save();
    }

    public static function setCardId(int $customerId, int $cardId)
    {
        $modelWithdrawalRequest = self::getBloggerWithdrawalRequest($customerId);
        $modelWithdrawalRequest->bot_customer_card_id = $cardId;
        $modelWithdrawalRequest->status = BloggerWithdrawalRequest::STATUS_CREATING;
        $modelWithdrawalRequest->save();
    }

    public static function setStatusNew(int $customerId)
    {
        $modelWithdrawalRequest = self::getBloggerWithdrawalRequest($customerId);
        $modelWithdrawalRequest->status = BloggerWithdrawalRequest::STATUS_NEW;
        if ($modelWithdrawalRequest->save()) {
            return $modelWithdrawalRequest;
        }

        return null;
    }

    /**
     * @param int $customerId
     * @return BloggerWithdrawalRequest
     */
    private static function getBloggerWithdrawalRequest(int $customerId): BloggerWithdrawalRequest
    {
        $model = BloggerWithdrawalRequest::findOne([
                'bot_customer_id' => $customerId,
                'status' => BloggerWithdrawalRequest::STATUS_CREATING
            ]) ?? new BloggerWithdrawalRequest();

        if (empty($model->id)) {
            $model->bot_customer_id = $customerId;
            $model->status = BloggerWithdrawalRequest::STATUS_CREATING;
            $model->sum = 0;
        }

        return $model;
    }

}