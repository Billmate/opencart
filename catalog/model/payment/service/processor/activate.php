<?php
require_once(DIR_APPLICATION . 'model/payment/service/processor/status.php');

class ModelPaymentServiceProcessorActivate extends ModelPaymentServiceProcessorStatus
{
    public function process($orderId)
    {
        $requestData = $this->getRequestData($orderId);
        if (!$requestData) {
            return;
        }
        $bmRequestData = [
            'PaymentData' => $requestData
        ];

        $this->activatePayment($bmRequestData);
    }
}