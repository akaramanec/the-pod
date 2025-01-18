<?php

namespace backend\modules\shop\models\search;

use common\helpers\DieAndDumpHelper;
use src\helpers\Date;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\OrderPoll;

/**
 * OrderPollSearch represents the model behind the search form of `backend\modules\shop\models\OrderPoll`.
 */
class OrderPollSearch extends OrderPoll
{
    public function rules(): array
    {
        return [
            [['id', 'order_id', 'poll_id', 'status', 'answer_first', 'answer_second'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = OrderPoll::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->updated_at !== '') {
            switch ($this->updated_at) {
                case 'quarter':
                    $dateFrom = Date::minusPlus(Date::datetime_now(), "-3 month");
                    break;
                case 'month':
                    $dateFrom = Date::minusPlus(Date::datetime_now(), "-1 month");
                    break;
                case 'week':
                    $dateFrom = Date::minusPlus(Date::datetime_now(), "-7 day");
                    break;
                case 'day':
                default:
                    $dateFrom = Date::minusPlus(Date::datetime_now(), "-1 day");
            }
            $query->andFilterWhere(['between', 'updated_at', $dateFrom, Date::datetime_now()]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'poll_id' => $this->poll_id,
            'status' => $this->status,
            'answer_first' => $this->answer_first,
            'answer_second' => $this->answer_second,
        ]);

        return $dataProvider;
    }
}
