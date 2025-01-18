<?php

namespace backend\modules\customer\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\BloggerWithdrawalRequest;

/**
 * BloggerWithdrawalRequestSearch represents the model behind the search form of `backend\modules\customer\models\BloggerWithdrawalRequest`.
 */
class BloggerWithdrawalRequestSearch extends BloggerWithdrawalRequest
{

    public $bot_customer_first_name;
    public $bot_customer_last_name;
    public $bot_customer_username;
    public $card_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bot_customer_id', 'bot_customer_card_id', 'status'], 'integer'],
            [['sum'], 'number'],
            [['bot_customer_first_name', 'bot_customer_last_name', 'bot_customer_username'], 'string'],
            [['created_at', 'updated_at', 'card_number'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BloggerWithdrawalRequest::find()
            ->alias('bwr')
            ->where(['!=', 'bwr.status', BloggerWithdrawalRequest::STATUS_CREATING]);

        $query->joinWith('botCustomer AS bc');
        $query->joinWith('botCustomerCard AS bcc');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bwr.id' => $this->id,
            'bwr.sum' => $this->sum,
            'bwr.status' => $this->status,
            'bwr.created_at' => $this->created_at,
            'bwr.updated_at' => $this->updated_at,
        ]);
        if (!empty($this->bot_customer_first_name)
            || !empty($this->bot_customer_last_name)
            || !empty($this->bot_customer_username)
        ) {
            if (!empty($this->bot_customer_first_name)) {

                $query->andFilterWhere(["like", 'bc.first_name', $this->bot_customer_first_name]);
            }
            if (!empty($this->bot_customer_last_name)) {
                $query->andFilterWhere(["like", 'bc.last_name', $this->bot_customer_last_name]);
            }
            if (!empty($this->bot_customer_username)) {
                $query->andFilterWhere(["like", 'bc.username', $this->bot_customer_username]);
            }
        }

        if (!empty($this->card_number)) {
            $query->andFilterWhere(["like", 'bcc.number', $this->card_number]);
            $query->groupBy('bwr.id');
        }

        return $dataProvider;
    }
}
