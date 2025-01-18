<?php

namespace backend\modules\bot\src;

use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\BotLogger;

/**
 * @property ApiNp $_apiNp
 */
class DocumentNp
{
    private $_apiNp;

    public function __construct(ApiNp $apiNp)
    {
        $this->_apiNp = $apiNp;
    }

    public function getDocumentList($dateTimeFrom, $dateTimeTo)
    {
        $data = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'getDocumentList',
            'methodProperties' => [
                'DateTimeFrom' => $dateTimeFrom,
                'DateTimeTo' => $dateTimeTo,
                'Page' => '1',
                'GetFullList' => '0'
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function internetDocumentSave($expressNpData)
    {
        $data = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'save',
            'methodProperties' => [
                'NewAddress' => $expressNpData->NewAddress,
                'PayerType' => $expressNpData->PayerType,
                'PaymentMethod' => $expressNpData->PaymentMethod,
                'CargoType' => $expressNpData->CargoType,
                'VolumeGeneral' => $expressNpData->VolumeGeneral,
                'Weight' => $expressNpData->Weight,
                'ServiceType' => $expressNpData->ServiceType,
                'SeatsAmount' => $expressNpData->SeatsAmount,
                'Description' => $expressNpData->Description,
                'Cost' => $expressNpData->Cost,
                'CitySender' => $expressNpData->CitySender,
                'Sender' => $expressNpData->Sender,
                'SenderAddress' => $expressNpData->SenderAddress,
                'ContactSender' => $expressNpData->ContactSender,
                'SendersPhone' => $expressNpData->SendersPhone,
                'RecipientCityName' => $expressNpData->RecipientCityName,
                'RecipientArea' => $expressNpData->RecipientArea,
                'RecipientAreaRegions' => $expressNpData->RecipientAreaRegions,
                'RecipientAddressName' => $expressNpData->RecipientAddressName,
                'RecipientHouse' => $expressNpData->RecipientHouse,
                'RecipientFlat' => $expressNpData->RecipientFlat,
                'RecipientName' => $expressNpData->RecipientName,
                'RecipientType' => $expressNpData->RecipientType,
                'RecipientsPhone' => $expressNpData->RecipientsPhone,
                'DateTime' => $expressNpData->DateTime,
                'BackwardDeliveryData' => $expressNpData->BackwardDeliveryData
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function internetDocumentUpdate($expressNpData, $ref)
    {
        $data = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'update',
            'methodProperties' => [
                'Ref' => $ref,
                'NewAddress' => $expressNpData->NewAddress,
                'PayerType' => $expressNpData->PayerType,
                'PaymentMethod' => $expressNpData->PaymentMethod,
                'CargoType' => $expressNpData->CargoType,
                'VolumeGeneral' => $expressNpData->VolumeGeneral,
                'Weight' => $expressNpData->Weight,
                'ServiceType' => $expressNpData->ServiceType,
                'SeatsAmount' => $expressNpData->SeatsAmount,
                'Description' => $expressNpData->Description,
                'Cost' => $expressNpData->Cost,
                'CitySender' => $expressNpData->CitySender,
                'Sender' => $expressNpData->Sender,
                'SenderAddress' => $expressNpData->SenderAddress,
                'ContactSender' => $expressNpData->ContactSender,
                'SendersPhone' => $expressNpData->SendersPhone,
                'RecipientCityName' => $expressNpData->RecipientCityName,
                'RecipientArea' => $expressNpData->RecipientArea,
                'RecipientAreaRegions' => $expressNpData->RecipientAreaRegions,
                'RecipientAddressName' => $expressNpData->RecipientAddressName,
                'RecipientHouse' => $expressNpData->RecipientHouse,
                'RecipientFlat' => $expressNpData->RecipientFlat,
                'RecipientName' => $expressNpData->RecipientName,
                'RecipientType' => $expressNpData->RecipientType,
                'RecipientsPhone' => $expressNpData->RecipientsPhone,
                'DateTime' => $expressNpData->DateTime,
                'BackwardDeliveryData' => $expressNpData->BackwardDeliveryData
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function delete($DocumentRefs)
    {
        $data = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'delete',
            'methodProperties' => [
                'DocumentRefs' => $DocumentRefs
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function getStatusDocuments($phone = [])
    {
        $data = [
            'modelName' => 'TrackingDocument',
            'calledMethod' => 'getStatusDocuments',
            'methodProperties' => [
                'Documents' => $phone
            ]
        ];
        BotLogger::save_input($data, 'getStatusDocuments');
        return $this->_apiNp->post($data);
    }
}
