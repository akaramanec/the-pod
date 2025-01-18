<?php

namespace backend\modules\customer\models\search;

use backend\modules\bot\models\Logger;
use backend\modules\shop\models\Order;
use src\helpers\DieAndDumpHelper;
use src\validators\Phone;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\Customer;

class CustomerBloggerSearch extends Customer
{
    public $query;

    public function rules()
    {
        return [
            [['id', 'status', 'blogger', 'parent_id'], 'integer'],
            ['phone', Phone::class],
            [['platform_id', 'first_name', 'last_name', 'username', 'bot_id', 'phone', 'email', 'customerCount', 'ordersCount', 'sumDebt', 'dateFrom', 'dateTo'], 'safe'],
            [['first_name', 'last_name', 'username', 'phone', 'email'], 'trim'],
            [['dateFrom', 'dateTo'], 'date']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['customerCount', 'ordersCount', 'dateFrom', 'dateTo']);
    }

    public function search($params)
    {
        $dateSearch = '';
        if (isset($params['dateFrom']) && isset($params['dateTo'])) {
            $dateSearch = ' AND (main.created_at > "' . $params['dateFrom'] . ' 00:00:00" AND main.created_at < "' . $params['dateTo'] . ' 23:59:59")';
        }

        $this->query = Customer::find()
            ->alias('customer')
            ->select($this->getSelect($dateSearch))
            ->groupBy(['customer.id'])
            ->where(['customer.status' => [Customer::STATUS_SUBSCRIBED, Customer::STATUS_ACTIVE]])
            ->andWhere(['customer.blogger' => Customer::BLOGGER_TRUE]);

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'sort' => [
                'defaultOrder' => ['customerCountTotal' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['customer.id' => SORT_ASC],
                        'desc' => ['customer.id' => SORT_DESC],
                    ],
                    'first_name' => [
                        'asc' => ['customer.first_name' => SORT_ASC],
                        'desc' => ['customer.first_name' => SORT_DESC],
                    ],
                    'last_name' => [
                        'asc' => ['customer.last_name' => SORT_ASC],
                        'desc' => ['customer.last_name' => SORT_DESC],
                    ],
                    'status' => [
                        'asc' => ['customer.status' => SORT_ASC],
                        'desc' => ['customer.status' => SORT_DESC],
                    ],
                    'customerCountTotal' => [
                        'asc' => ['customerCountTotal' => SORT_ASC],
                        'desc' => ['customerCountTotal' => SORT_DESC],
                    ],
                    'customerCountTotalDisabled' => [
                        'asc' => ['customerCountTotalDisabled' => SORT_ASC],
                        'desc' => ['customerCountTotalDisabled' => SORT_DESC],
                    ],
                    'customerCountTotalActive' => [
                        'asc' => ['customerCountTotalActive' => SORT_ASC],
                        'desc' => ['customerCountTotalActive' => SORT_DESC],
                    ],
                    'ordersCount' => [
                        'asc' => ['ordersCount' => SORT_ASC],
                        'desc' => ['ordersCount' => SORT_DESC],
                    ],
                ],
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $this->query->andFilterWhere([
            'customer.id' => $this->id
        ]);

        $this->query->andFilterWhere(['like', 'customer.first_name', $this->first_name])
            ->andFilterWhere(['like', 'customer.last_name', $this->last_name])
            ->andFilterWhere(['like', 'customer.username', $this->username])
            ->andFilterWhere(['like', 'customer.phone', $this->phone])
            ->andFilterWhere(['like', 'customer.email', $this->email]);

        return $dataProvider;
    }

    public static function statisticDateFrom()
    {
        if (isset($_SESSION['dateFrom'])) {
            return $_SESSION['dateFrom'];
        }
        return null;
    }

    public static function statisticDateTo()
    {
        if (isset($_SESSION['dateTo'])) {
            return $_SESSION['dateTo'];
        }
        return null;
    }

    public static function statisticDate()
    {
        if (($dateFrom = self::statisticDateFrom()) && ($dateTo = self::statisticDateTo())) {
            return $dateFrom . ' - ' . $dateTo;
        }
        return '';
    }

    private function getSelect($dateSearch)
    {
        $select = [
            'customer.id',
            'customer.img',
            'customer.first_name',
            'customer.last_name',
            'customer.phone',
        ];

        $customerCountTotal = '(SELECT count(id) FROM bot_customer main WHERE main.parent_id = customer.id AND main.status IN (' . Customer::STATUS_ACTIVE . ',' . Customer::STATUS_SUBSCRIBED . ',' . Customer::STATUS_UNSUBSCRIBED . ')' . $dateSearch . ')  AS customerCountTotal';

        $customerCountTotalDisabled = '(SELECT count(id) FROM bot_customer main WHERE main.parent_id = customer.id AND main.status = ' . Customer::STATUS_UNSUBSCRIBED . ' ' . $dateSearch . ') AS customerCountTotalDisabled';

        $customerCountTotalActive = '(SELECT count(id) FROM bot_customer main WHERE main.parent_id = customer.id AND main.status IN (' . Customer::STATUS_ACTIVE . ',' . Customer::STATUS_SUBSCRIBED . ')' . $dateSearch . ') AS customerCountTotalActive';

        $orderCount = '(SELECT count(id) FROM shop_order main WHERE main.customer_id IN (SELECT id FROM bot_customer child WHERE child.parent_id = customer.id) AND main.status = ' . Order::STATUS_CLOSE_SUCCESS . ' ' . $dateSearch . ') AS ordersCount';

        return array_merge($select, [
            $customerCountTotal,
            $customerCountTotalDisabled,
            $customerCountTotalActive,
            $orderCount,
        ]);
    }
}
