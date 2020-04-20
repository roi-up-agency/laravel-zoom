<?php

namespace RoiUp\Zoom\Events;

class ZoomEvent
{

    protected $event;
    protected $accountId;
    protected $operator;
    protected $operatorId;
    protected $object;

    public function __construct($event)
    {
        $payload            = (object)$event->payload;

        $this->event        = $event->event;
        $this->accountId    = $payload->account_id;
        $this->operator     = !empty($payload->operator) ? $payload->operator : null;
        $this->operatorId   = !empty($payload->operator_id) ? $payload->operator_id : null;
        $this->object       = $payload->object;
        if(isset($this->object['id'])){
            $this->object['id'] = (string)$this->object['id'];
        }
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getOperatorId()
    {
        return $this->operatorId;
    }

    /**
     * @param mixed $operatorId
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    public static function getEventClass($eventName){
        $map = EventsMapping::$MAP;
        return isset($map[$eventName]) ? $map[$eventName] : null;
    }

}