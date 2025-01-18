<?php


namespace backend\modules\bot\src;


class SenderNp
{
    public $CounterpartyRef;
    public $ContactPersonsDescription;
    public $ContactPersonsPhones;
    public $ContactPersonsEmail;
    public $ContactPersonsRef;
    public $ContactPersonsLastName;
    public $ContactPersonsFirstName;
    public $ContactPersonsMiddleName;
    private $_counterpartyNp;

    public function __construct($apiNp)
    {
        $this->_counterpartyNp = new CounterpartyNp($apiNp);
        $this->setCounterparty();
        $this->setContactPersons();
    }

    public function setCounterparty()
    {

        $getCounterparties = $this->_counterpartyNp->getCounterparties('Sender');
        if (isset($getCounterparties->data[0]->Ref)) {
            $this->CounterpartyRef = $getCounterparties->data[0]->Ref;
            return true;
        }
        throw new \Exception('Отсутствует контрагент');
    }

    public function setContactPersons()
    {
        $ContactPersons = $this->_counterpartyNp->getCounterpartyContactPersons($this->CounterpartyRef);
        if (isset($ContactPersons->data[0])) {
            $this->ContactPersonsDescription = $ContactPersons->data[0]->Description;
            $this->ContactPersonsPhones = $ContactPersons->data[0]->Phones;
            $this->ContactPersonsEmail = $ContactPersons->data[0]->Email;
            $this->ContactPersonsRef = $ContactPersons->data[0]->Ref;
            $this->ContactPersonsLastName = $ContactPersons->data[0]->LastName;
            $this->ContactPersonsFirstName = $ContactPersons->data[0]->FirstName;
            $this->ContactPersonsMiddleName = $ContactPersons->data[0]->MiddleName;
            return true;
        }
        throw new \Exception('Отсутствует контактное лицо контрагента');
    }
}
