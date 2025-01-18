<?php

namespace backend\modules\shop\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\shop\models\Order;
use yii\db\Expression;

/**
 *
 */
class OrderSearch extends Order
{
    public $query;
    public $r_list;
    private $_last_name;
    private $_first_name;

    public function rules()
    {
        return [
            [['id', 'status', 'payment_method', 'blogger', 'source'], 'integer'],
            [['customer_id'], 'safe'],
            [['customer_id'], 'trim'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['blogger']);
    }

    public function search($params)
    {
        $this->query = Order::find()
            ->alias('order')
            ->joinWith([
                'botCustomer AS botCustomer',
                'botCustomer.parent AS parent',
                'orderCustomer AS orderCustomer',
                'np AS np'
            ])
            ->with(['payBlogger'])
            ->orderBy('created_at desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->status == Order::STATUS_NEW) {
            $this->query->andFilterWhere(['in', 'order.status', array_keys(Order::statusesNew())]);
        } else {
            $this->query->andFilterWhere([
                'order.status' => $this->status
            ]);
        }

        $this->query->andFilterWhere([
            'order.id' => $this->id,
            'order.created_at' => $this->created_at,
            'order.updated_at' => $this->updated_at,
            'order.payment_method' => $this->payment_method,
        ]);

        if ($this->source != Order::SOURCE_NON) {
            $this->query->andFilterWhere(['order.source' => $this->source]);
        } else {
            $null = new Expression('NULL');
            $this->query->andFilterWhere(['is', 'order.source', $null]);
        }

        $this->parserCustomer();
        $this->query->andFilterWhere(['or',
            ['like', 'botCustomer.first_name', $this->_first_name],
            ['like', 'botCustomer.last_name', $this->_last_name],
            ['like', 'orderCustomer.first_name', $this->_first_name],
            ['like', 'orderCustomer.last_name', $this->_last_name],
        ]);
        return $dataProvider;
    }

    public function parserCustomer()
    {
        $c = explode(' ', $this->customer_id);
        if (count($c) == 1) {
            $this->_first_name = $c[0];
            $this->_last_name = $c[0];
        }
        if (count($c) == 2) {
            $this->_first_name = $c[0];
            $this->_last_name = $c[1];
        }
    }
}
