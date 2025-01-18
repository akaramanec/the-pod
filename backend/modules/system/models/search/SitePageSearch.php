<?php

namespace backend\modules\system\models\search;

use backend\modules\system\models\SitePage;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class SitePageSearch extends SitePage
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['name']);
    }

    public function search($params)
    {
        $query = SitePage::find()->joinWith(['langRu']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
//                'defaultOrder' => ['sort'],
                'attributes' => [
                    'name' => [
                        'asc' => ['name' => SORT_ASC],
                        'desc' => ['name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
