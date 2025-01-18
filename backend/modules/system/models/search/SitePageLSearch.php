<?php

namespace backend\modules\system\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\system\models\SitePageL;

/**
 * SitePageLSearch represents the model behind the search form of `backend\modules\system\models\SitePageL`.
 */
class SitePageLSearch extends SitePageL
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'page_id'], 'integer'],
            [['lang', 'name', 'description', 'meta', 'content', 'addition'], 'safe'],
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
        $query = SitePageL::find();

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
            'page_id' => $this->page_id,
        ]);

        $query->andFilterWhere(['like', 'lang', $this->lang])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'meta', $this->meta])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'addition', $this->addition]);

        return $dataProvider;
    }
}
