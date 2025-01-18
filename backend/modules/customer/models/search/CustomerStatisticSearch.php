<?php

namespace backend\modules\customer\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\customer\models\Customer;


class CustomerStatisticSearch extends Customer
{

    public function rules()
    {
        return [
            [['id', 'group_id', 'status'], 'integer'],
            [['platform_id', 'first_name', 'last_name', 'username', 'bot_id', 'phone', 'email', 'updated_at', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['status_homework', 'platform', 'name_course']);
    }

    public function search($params)
    {
        $query = Customer::find()
            ->alias('customer')
            ->joinWith(['bot AS bot'])
            ->with(['homework'])
            ->orderBy('bot.platform');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'customer.id' => $this->id,
            'customer.bot_id' => $this->bot_id,
            'customer.status' => $this->status,
            'customer.updated_at' => $this->updated_at,
            'customer.created_at' => $this->created_at,
            'bot.platform' => $this->platform_id,
        ]);

        $query->orFilterWhere(['like', 'customer.first_name', $this->first_name])
            ->orFilterWhere(['like', 'customer.last_name', $this->first_name])
            ->orFilterWhere(['like', 'customer.username', $this->first_name])
            ->orFilterWhere(['like', 'customer.phone', $this->first_name])
            ->orFilterWhere(['like', 'customer.email', $this->first_name]);

        return $dataProvider;
    }
}
