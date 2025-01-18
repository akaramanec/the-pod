<?php

namespace backend\modules\system\models;

class Addition extends \yii\base\Model
{
    /**
     * @var mixed
     */
    public $email;
    /**
     * @var mixed
     */
    public $map;
    /**
     * @var mixed
     */
    public $address;
    /**
     * @var mixed
     */
    public $workTime;
    /**
     * @var mixed
     */
    public $life;
    /**
     * @var mixed
     */
    public $kyivstar;
    /**
     * @var mixed
     */
    public $landline;

    public function __construct(SitePageL $sitePageL, $config = [])
    {
        parent::__construct($config);
        $this->email = $sitePageL->addition['email'];
        $this->map = $sitePageL->addition['map'];
        $this->address = $sitePageL->addition["address"];
        $this->workTime = $sitePageL->addition["workTime"];
        $this->life = $sitePageL->addition["life"];
        $this->kyivstar = $sitePageL->addition["kyivstar"];
        $this->landline = $sitePageL->addition["landline"];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'map' => 'Карта',
            'address' => 'Наш адрес',
            'workTime' => 'Время работы',
            'life' => 'Телефон life',
            'kyivstar' => 'Телефон kyivstar',
            'landline' => 'Телефон стационарный',
        ];
    }
}