<?php


namespace backend\modules\bot\src;

/**
 * @property ApiNp $_apiNp
 */
class CounterpartyNp
{
    private $_apiNp;

    public function __construct(ApiNp $apiNp)
    {
        $this->_apiNp = $apiNp;
    }

    /**
     * Recipient, Sender
     */
    public function getCounterparties($counterpartyProperty)
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'getCounterparties',
            'methodProperties' => [
                'CounterpartyProperty' => $counterpartyProperty,
                'Page' => '1'
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function getCounterpartyContactPersons($ref)
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'getCounterpartyContactPersons',
            'methodProperties' => [
                'Ref' => $ref,
                'Page' => '1',
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function getCounterpartyAddresses($ref)
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'getCounterpartyAddresses',
            'methodProperties' => [
                'Ref' => $ref,
                'CounterpartyProperty' => 'Sender'
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function save($customer, $CounterpartyProperty = 'Recipient')
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'save',
            'methodProperties' => [
                'FirstName' => $customer->first_name,
                'MiddleName' => '',
                'LastName' => $customer->last_name,
                'Phone' => $customer->phone,
                'Email' => $customer->email,
                'CounterpartyType' => 'PrivatePerson',
                'CounterpartyProperty' => $CounterpartyProperty
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function update($customer, $CounterpartyProperty, $ref, $cityRef)
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'update',
            'methodProperties' => [
                'Ref' => $ref,
                'CityRef' => $cityRef,
                'FirstName' => $customer->first_name,
                'MiddleName' => '',
                'LastName' => $customer->last_name,
                'Phone' => $customer->phone,
                'Email' => $customer->email,
                'CounterpartyType' => 'PrivatePerson',
                'CounterpartyProperty' => $CounterpartyProperty
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function getCounterpartyOptions($ref)
    {
        $data = [
            'modelName' => 'Counterparty',
            'calledMethod' => 'getCounterpartyOptions',
            'methodProperties' => [
                'Ref' => $ref
            ]
        ];
        return $this->_apiNp->post($data);
    }
}
