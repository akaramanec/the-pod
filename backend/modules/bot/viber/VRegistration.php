<?php

namespace backend\modules\bot\viber;

use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use Yii;

class VRegistration extends VCommon
{

    public function phone()
    {
        $this->saveClick(ClickStatistic::START);
        $this->areYouRegistered();
        $this->sharePhone($this->text('yourPhone'));
    }

    public function phoneSave()
    {
        Yii::$app->vb->customer->phone = Yii::$app->vb->data->value;
        if (Yii::$app->vb->customer->save()) {
            $this->saveClick(ClickStatistic::PHONE);
            return Yii::$app->vb->action('VCommon_start');
        } else {
            $this->errors(Yii::$app->vb->customer->errors);
            return $this->phone();
        }
    }

    public function firstName()
    {
        $this->areYouRegistered();
        $this->saveCommand('VRegistration_firstNameSave');
        return $this->sendMessage($this->text('firstName'));
    }

    public function firstNameSave()
    {
        Yii::$app->vb->customer->first_name = Yii::$app->vb->data->value;
        if (Yii::$app->vb->customer->save()) {
            $this->saveClick(ClickStatistic::FIRST_NAME);
            $this->saveCommandNull();
            return $this->lastName();
        } else {
            $this->errors(Yii::$app->vb->customer->errors);
            return $this->firstName();
        }
    }

    public function lastName()
    {
        $this->areYouRegistered();
        $this->saveCommand('VRegistration_lastNameSave');
        return $this->sendMessage($this->text('lastName'));
    }

    public function lastNameSave()
    {
        Yii::$app->vb->customer->last_name = Yii::$app->vb->data->value;
        if (Yii::$app->vb->customer->save()) {
            $this->saveClick(ClickStatistic::LAST_NAME);
            $this->saveCommandNull();
            return $this->dataConfirmation();
        } else {
            $this->errors(Yii::$app->vb->customer->errors);
            return $this->lastName();
        }
    }

    public function dataConfirmation()
    {
        $this->text .= $this->text('dataConfirmationRegistration');
        $this->text .= $this->textCustomer(Yii::$app->vb->customer);
        $this->sendMessage($this->text);

        $button[] = $this->button($this->checkField(Yii::$app->vb->customer->first_name), ['action' => 'VRegistration_firstName']);
        $button[] = $this->button($this->checkField(Yii::$app->vb->customer->last_name), ['action' => 'VRegistration_lastName']);
        $button[] = $this->button('Подтвердить', ['action' => 'VRegistration_dataConfirmationSave']);
        $buttonDataConfirmation[] = $this->buttonImgText('Подтвердить', '/img/vb/empty-min.jpg', ['action' => 'VRegistration_dataConfirmationSave']);
        $this->carouselAndKeyboard($button, $buttonDataConfirmation, 3, 6);
    }

    private function checkField($field)
    {
        return $field ? $field : 'Пусто';
    }

    private function areYouRegistered()
    {
        if ($this->checkCustomer()) {
            $this->mainMenu($this->text('areYouRegistered'));
            exit(__METHOD__);
        }
    }

    public function dataConfirmationSave()
    {
        $this->areYouRegistered();
        Yii::$app->vb->customer->status = Customer::STATUS_ACTIVE;
        if ($this->checkCustomer() && Yii::$app->vb->customer->save()) {
            $this->saveClick(ClickStatistic::REGISTRATION_CONFIRMATION);
            $this->saveCommandNull();
            if ($this->session->get('referralInput')) {
                return Yii::$app->vb->action('VCatalog_productReferral');
            } else {
                return Yii::$app->vb->action('VOrder_delivery');
            }
        } else {
            $this->errors(Yii::$app->vb->customer->errors);
            return $this->dataConfirmation();
        }
    }
}
