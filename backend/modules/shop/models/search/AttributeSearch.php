<?php

namespace backend\modules\shop\models\search;

use src\validators\SlugValidator;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\Attribute;

class AttributeSearch extends Attribute
{
    public $query;

    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status', 'type', 'sort'], 'integer'],
            [['name', 'uuid'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['uuid'], 'unique'],
            [['slug'], 'unique'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->query = Attribute::find()
            ->alias('attribute')
            ->orderBy('sort asc');

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $this->query->andFilterWhere([
            'id' => $this->id,
        ]);

        $this->query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}
