<?php


namespace backend\modules\bot\src;

/**
 * @property ApiNp $_apiNp
 */
class ContactPersonNp
{
    private $_apiNp;

    public function __construct(ApiNp $apiNp)
    {
        $this->_apiNp = $apiNp;
    }

    public function save($counterpartyRef)
    {
        $data = [
            'modelName' => 'ContactPerson',
            'calledMethod' => 'save',
            'methodProperties' => [
                'CounterpartyRef' => $counterpartyRef,
                'FirstName' => 'Вася',
                'MiddleName' => 'Борисович',
                'LastName' => 'Кравченко',
                'Phone' => '+380997979789'
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function update($counterpartyRef, $ref)
    {
        $data = [
            'modelName' => 'ContactPerson',
            'calledMethod' => 'update',
            'methodProperties' => [
                'CounterpartyRef' => $counterpartyRef,
                'Ref' => $ref,
                'FirstName' => 'Иван',
                'LastName' => 'Иванов',
                'MiddleName' => 'Иванович',
                'Phone' => '+380997979789',
            ]
        ];
        return $this->_apiNp->post($data);
    }

    public function delete($ref)
    {
        $data = [
            'modelName' => 'ContactPerson',
            'calledMethod' => 'delete',
            'methodProperties' => [
                'Ref' => $ref,
            ]
        ];
        return $this->_apiNp->post($data);
    }
}
