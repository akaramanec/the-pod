<?php


namespace backend\modules\bot\src;

/**
 * @property ApiNp $_apiNp
 */
class AddressNp
{
    private $_apiNp;

    public function __construct(ApiNp $apiNp)
    {
        $this->_apiNp = $apiNp;
    }

    public function save($counterpartyRef)
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'save',
            'methodProperties' => [
                'CounterpartyRef' => $counterpartyRef,
                'StreetRef' => 'd4450bdb-0a58-11de-b6f5-001d92f78697',
                'BuildingNumber' => '7',
                'Flat' => '2',
                'Note' => 'Комментарий'
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function searchCity($q)
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlements',
            'methodProperties' => [
                'CityName' => $q,
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function searchStreets($q, $ref)
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlementStreets',
            'methodProperties' => [
                'StreetName' => $q,
                'SettlementRef' => $ref,
                'Limit' => 5
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function warehouses($ref)
    {
        $data = [
            'modelName' => 'AddressGeneral',
            'calledMethod' => 'getWarehouses',
            'methodProperties' => [
                'SettlementRef' => $ref,
                'Language' => 'ru',
//                'Page' => 0,
//                'Limit' => 20,
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function getAreas()
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'getAreas'
        ];
        return $this->_apiNp->post($data);
    }

    /**
     * $Ref ref_city
     */
    public function getSettlements($Ref = '', $RegionRef = '', $AreaRef = '')
    {

        $data = [
            'modelName' => 'AddressGeneral',
            'calledMethod' => 'getSettlements',
            'methodProperties' => [
                'AreaRef' => $AreaRef,
                'Ref' => $Ref,
                'RegionRef' => $RegionRef,
                'Page' => '1',
            ]
        ];
        return $this->_apiNp->post($data);
    }

    /**
     * $Ref delivery_city
     */
    public function getCities($Ref = '', $findByString = '')
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'getCities',
            'methodProperties' => [
                'Ref' => $Ref,
                'FindByString' => $findByString
            ]
        ];
        return $this->_apiNp->post($data);
    }

    /**
     * $Ref delivery_city
     */
    public function getStreet($CityRef = '', $findByString = '')
    {
        $data = [
            'modelName' => 'Address',
            'calledMethod' => 'getStreet',
            'methodProperties' => [
                'CityRef' => $CityRef,
                'FindByString' => $findByString
            ]
        ];
        return $this->_apiNp->post($data);
    }
}
