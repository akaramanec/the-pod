<?php

namespace backend\modules\shop\service\analytics\helpers;

use backend\modules\customer\models\CustomerTag;
use backend\modules\customer\models\CustomerTagLink;
use backend\modules\shop\models\Order;
use backend\modules\shop\service\analytics\enum\AnalyticsCustomerTagEnum;
use Yii;

class AnalyticsOrderHelper
{
    /**
     * @param int $tag
     * @param string $dateFrom
     * @param string $dateTo
     */
    public static function addCustomersTag(int $tagId, string $dateFrom, string $dateTo)
    {
        $tagModel = CustomerTag::findOne($tagId);

        $conditionByCountOrderList = [
            AnalyticsCustomerTagEnum::ONE_ORDER_TAG => "=1",
            AnalyticsCustomerTagEnum::TO_FIVE_ORDER_TAG => "BETWEEN 2 AND 5",
            AnalyticsCustomerTagEnum::MORE_FIVE_ORDER_TAG => ">5"
        ];

        $countOrderCondition = $conditionByCountOrderList[$tagModel->name];
        $sql = "SELECT customer.id 
FROM bot_customer AS customer
LEFT JOIN shop_order AS sho ON sho.customer_id = customer.id
WHERE sho.created_at BETWEEN :dateFrom AND :dateTo AND sho.status = :statusSuccess
GROUP BY customer.id
HAVING COUNT(sho.id) $countOrderCondition";
        $params = [
            ':statusSuccess' => Order::STATUS_CLOSE_SUCCESS,
            ':dateFrom' => $dateFrom,
            ':dateTo' => $dateTo
        ];

        $customersId = Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->queryAll();
        $customerTags = array_map(function ($item) use ($tagId) {
            return [$item['id'], $tagId];
        }, $customersId);

        CustomerTagLink::deleteAll(['tag_id'=>$tagId]);
        Yii::$app->db->createCommand()
            ->batchInsert(CustomerTagLink::tableName(), ['customer_id', 'tag_id'], $customerTags)
            ->execute();
    }
}