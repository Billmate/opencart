<?php

class ModelPaymentBmsetup extends Model
{
    const BILLMATE_EVENTS_CODE = 'billmate_events';

    public function registerEvents()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent(
            self::BILLMATE_EVENTS_CODE,
            'catalog/model/checkout/order/addOrderHistory/after',
            'extension/event/bmrequest/process'
        );
    }

    public function unregisterEvents()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent(self::BILLMATE_EVENTS_CODE);
    }
}