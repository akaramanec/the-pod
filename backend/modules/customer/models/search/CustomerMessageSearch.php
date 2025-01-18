<?php

namespace backend\modules\customer\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\CustomerMessage;


class CustomerMessageSearch extends CustomerMessage
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['message', 'customer_id', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = CustomerMessage::find()
            ->alias('message')
            ->joinWith(['customer AS customer'])
            ->orderBy([
                'message.status' => SORT_ASC,
                'message.id' => SORT_DESC
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'message.id' => $this->id,
            'message.created_at' => $this->created_at,
            'message.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'message.message', $this->message])
            ->andFilterWhere(['or',
                ['like', 'customer.first_name', $this->customer_id],
                ['like', 'customer.last_name', $this->customer_id],
            ]);
        return $dataProvider;
    }
}
