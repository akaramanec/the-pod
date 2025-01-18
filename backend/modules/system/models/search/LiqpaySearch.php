<?php

namespace backend\modules\system\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\system\models\Liqpay;

/**
 * LiqpaySearch represents the model behind the search form of `backend\modules\system\models\Liqpay`.
 */
class LiqpaySearch extends Liqpay
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['test_public_key', 'test_private_key', 'public_key', 'private_key'], 'safe'],
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
        $query = Liqpay::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'test_public_key', $this->test_public_key])
            ->andFilterWhere(['like', 'test_private_key', $this->test_private_key])
            ->andFilterWhere(['like', 'public_key', $this->public_key])
            ->andFilterWhere(['like', 'private_key', $this->private_key]);

        return $dataProvider;
    }
}
