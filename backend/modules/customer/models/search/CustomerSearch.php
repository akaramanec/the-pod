<?php

namespace backend\modules\customer\models\search;

use src\helpers\DatePeriodSelectorHelper;
use src\helpers\DieAndDumpHelper;
use src\validators\Phone;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\Customer;

class CustomerSearch extends Customer
{
    public $query;
    /**
     * @var mixed
     */
    public $dateFrom;
    /**
     * @var mixed
     */
    public $dateTo;
    /**
     * @var string
     */
    public $customerMark;

    public function rules()
    {
        return [
            [['id', 'status', 'blogger', 'parent_id'], 'integer'],
            [['customerMark'], 'string'],
//            ['phone', Phone::class],
            [['platform_id', 'first_name', 'last_name', 'username', 'bot_id', 'phone', 'email', 'black_list', 'regular_customer'], 'safe'],
            [['black_list', 'regular_customer'], 'boolean'],
            [['first_name', 'last_name', 'username', 'phone', 'email'], 'trim'],
        ];
    }

    public function __construct($config = [], $dateRange = null)
    {
        parent::__construct($config);
        if ($dateRange && $dateRange['dateFrom'] && $dateRange['dateTo']) {
            $this->dateFrom = $dateRange['dateFrom'];
            $this->dateTo = $dateRange['dateTo'];
        }
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = Customer::find()
            ->alias('customer')
            ->joinWith(['bot AS bot']);
        if (isset($params['CustomerSearch']['status']) && $params['CustomerSearch']['status'] != "") {
            $this->query->andFilterWhere([
                'customer.status' => $params['CustomerSearch']['status']
            ]);
        } else {
            $this->query->andFilterWhere([
                'customer.status' => [
                    Customer::STATUS_SUBSCRIBED,
                    Customer::STATUS_ACTIVE,
                ]
            ]);
        }
        if ($this->dateFrom && $this->dateTo) {
            $this->query->andFilterWhere(['>=', 'customer.updated_at', DatePeriodSelectorHelper::strToTime($this->dateFrom)]);
            $this->query->andFilterWhere(['<=', 'customer.updated_at', DatePeriodSelectorHelper::strToTime($this->dateTo)]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
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
                ],
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $this->query->andFilterWhere([
            'customer.id' => $this->id,
            'customer.bot_id' => $this->bot_id,
            'customer.parent_id' => $this->parent_id,
            'customer.status' => $this->status,
            'customer.blogger' => $this->blogger,
            'bot.platform' => $this->platform_id,
        ]);

        if (isset($this->customerMark)) {
            if ($this->customerMark == 'black_list') {
                $this->query->andFilterWhere([
                    'customer.black_list' => true,
                ]);
            }
            if ($this->customerMark == 'regular_customer') {
                $this->query->andFilterWhere([
                    'customer.regular_customer' => true,
                ]);
            }

        }

        $this->query->andFilterWhere(['like', 'customer.first_name', $this->first_name])
            ->andFilterWhere(['like', 'customer.last_name', $this->last_name])
            ->andFilterWhere(['like', 'customer.username', $this->username])
            ->andFilterWhere(['like', 'customer.phone', $this->phone])
            ->andFilterWhere(['like', 'customer.email', $this->email]);


        return $dataProvider;
    }
}
