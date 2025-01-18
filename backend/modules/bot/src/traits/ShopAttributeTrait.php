<?php

namespace backend\modules\bot\src\traits;

use backend\modules\bot\models\BotCommand;
use backend\modules\shop\models\CustomerFilter;
use Yii;

trait ShopAttributeTrait
{

    private $_attributeNow;
    private $_attributeNext;
    private $_attributePrev;
    public function branches()
    {
        return [
            'branchQuickOrder' => [
                1 => [
                    'slug' => 'qty-of-puffs'
                ],
                2 => [
                    'slug' => 'draft-temperature'
                ],
                3 => [
                    'slug' => 'aftertaste'
                ]
            ],
            'branchFlavor' => [
                1 => [
                    'slug' => 'qty-of-puffs'
                ],
                2 => [
                    'slug' => 'flavor'
                ]
            ]
        ];
    }

//    public function attribute()
//    {
//        $branchAttribute = $this->session->get('branchAttribute');
//        if (isset(Yii::$app->tm->data->now)) {
//            $branchAttribute
//        }
//        $countBranchAttribute = count($branchAttribute);
//        if ($root === true) {
//            $attributeActive = $branchAttribute[1];
//            $this->_attributeNext = 2;
//            $this->attributePrev = false;
//        }
//    }
//
//
//
//    public function sendAttribute($root = false)
//    {
//
//        CustomerFilter::saveItemTm();
//        $attribute = Attribute::bySlug($attributeActive['slug']);
//        $button = $this->attributeButtons($attribute, 'sendAttribute');
//
//        if ($this->_attributeNext && $this->attributePrev === false) {
//            $button[] = [["text" => BotCommand::text('continue'), "callback_data" => $this->encode(['action' => '/TCatalog_attributeNext', 'attributeNext' => $this->_attributeNext])]];
//        }
//
//
//        $messageId = $this->session->messageId('mainMessageId');
//        $this->delCommon();
//        if ($messageId) {
//            $this->edit($attribute->name, $button, $messageId);
//        } else {
//            $this->button($attribute->name, $button);
//            return $this->saveSessionMessageId('mainMessageId');
//        }
//    }
}
