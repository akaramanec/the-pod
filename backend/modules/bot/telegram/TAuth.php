<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\Logger;
use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use Yii;

class TAuth extends TCommon
{

    public function agePolicy()
    {
        $button[] = [
            ['text' => "Да", 'callback_data' => json_encode(['action' => '/TAuth_agePolicySave', 'value' => 'yes'])],
            ['text' => "Нет", 'callback_data' => json_encode(['action' => '/TAuth_agePolicySave', 'value' => 'no'])],
        ];
        $this->button($this->text('agePolicy'), $button);
        $this->session->saveCommonRequest($this->request);
        exit(__METHOD__ . __LINE__);
    }

    public function agePolicySave()
    {
        if (Yii::$app->tm->data->value == 'yes') {
            Yii::$app->tm->customer->status = Customer::STATUS_SUBSCRIBED;
            Yii::$app->tm->customer->save();
            return Yii::$app->tm->action('/start');
        }
        $this->delCommon();
        $this->saveCommandNull();
        $this->sendMessage($this->text('noAgePolicy'));
        $this->session->saveCommonRequest($this->request);
        exit(__METHOD__ . __LINE__);
    }

    public function phone()
    {
        $this->setMainMessageId();
        $this->deleteMessageByMessageId(Yii::$app->tm->messageId);
        $this->saveClick(ClickStatistic::START);
        $button[] = [['text' => $this->text('yourPhoneButton'), 'request_contact' => true]];
        $this->contact($this->text('yourPhone'), $button);
        return $this->session->saveCommonRequest($this->request);
    }

    public function phoneSave()
    {
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        Yii::$app->tm->customer->phone = Yii::$app->tm->data->value;
        if (Yii::$app->tm->customer->save()) {
            $this->saveClick(ClickStatistic::PHONE);
            $this->saveCommandNull();
            return $this->firstName();
        } else {
            $this->errors(Yii::$app->tm->customer->errors);
            $this->session->saveCommonRequest($this->request);
            return $this->phone();
        }
    }

    public function firstName()
    {
        $this->saveCommand('/TAuth_firstNameSave');
        $this->sendMessage($this->text('firstName'));
        return $this->session->saveCommonRequest($this->request);
    }

    public function firstNameSave()
    {
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        Yii::$app->tm->customer->first_name = Yii::$app->tm->data->value;
        if (Yii::$app->tm->customer->save()) {
            $this->saveClick(ClickStatistic::FIRST_NAME);
            $this->saveCommandNull();
            return $this->lastName();
        } else {
            $this->errors(Yii::$app->tm->customer->errors);
            $this->session->saveCommonRequest($this->request);
            return $this->firstName();
        }
    }

    public function lastName()
    {
        $this->saveCommand('/TAuth_lastNameSave');
        $this->sendMessage($this->text('lastName'));
        return $this->session->saveCommonRequest($this->request);

    }

    public function lastNameSave()
    {
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        Yii::$app->tm->customer->last_name = Yii::$app->tm->data->value;
        if (Yii::$app->tm->customer->save()) {
            $this->saveClick(ClickStatistic::LAST_NAME);
            $this->saveCommandNull();
            return $this->dataConfirmation();
        } else {
            $this->errors(Yii::$app->tm->customer->errors);
            $this->session->saveCommonRequest($this->request);
            return $this->lastName();
        }
    }

    public function dataConfirmation()
    {
        $this->text .= $this->textCustomer(Yii::$app->tm->customer);
        $this->text .= $this->text('dataConfirmationRegistration');
        $button[] = [['text' => $this->checkField(Yii::$app->tm->customer->first_name), 'callback_data' => json_encode(['action' => '/TAuth_firstName'])]];
        $button[] = [['text' => $this->checkField(Yii::$app->tm->customer->last_name), 'callback_data' => json_encode(['action' => '/TAuth_lastName'])]];
        $button[] = [['text' => $this->menu->dataConfirmationSave, 'callback_data' => json_encode(['action' => '/TAuth_dataConfirmationSave'])]];
        $this->button($this->text, $button);
        $this->session->saveCommonRequest($this->request);
    }

    private function checkField($field)
    {
        return $field ? $field : 'Пусто';
    }

    public function dataConfirmationSave()
    {
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        Yii::$app->tm->customer->status = Customer::STATUS_ACTIVE;
        if ($this->checkCustomer() && Yii::$app->tm->customer->save()) {
            $this->saveClick(ClickStatistic::REGISTRATION_CONFIRMATION);
            $this->delCommon();
            $this->saveCommandNull();

            if($this->session->get('createBlogger')) {
                $this->startContinue();
                $action = $this->session->get('createBlogger');
                $this->session->del('createBlogger');
                return Yii::$app->tm->action($action);
            }
            if ($this->session->get('referralInput')) {
                $this->startContinue();
                return Yii::$app->tm->action('/TCatalog_productReferral');
            } else {
                $this->startContinue();
                return Yii::$app->tm->action('/TOrder_delivery');
            }
        } else {
            $this->errors(Yii::$app->tm->customer->errors);
            $this->session->saveCommonRequest($this->request);
            return $this->dataConfirmation();
        }
    }

    public function unsubscribed()
    {
//        return Yii::$app->tm->customer->delete();
        Yii::$app->tm->customer->status = Customer::STATUS_UNSUBSCRIBED;
        Yii::$app->tm->customer->save();
        exit(__METHOD__ . __LINE__);
    }
}
