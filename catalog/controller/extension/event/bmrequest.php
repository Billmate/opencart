<?php

class ControllerExtensionEventBmrequest extends Controller
{
    const ORDER_ID_KEY = 0;

    const STATUS_ID_KEY = 1;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('payment/state/modifier');
    }

    /**
     * @param $route string
     * @param $orderData float
     * @param $action
     */
    public function process($route, $orderData , $action)
    {
        if (!isset($this->session->data['api_id'])) {
            return ;
        }

        $orderId = $orderData[self::ORDER_ID_KEY];
        $newStatusId = $orderData[self::STATUS_ID_KEY];
        $this->getStateModifier()->updateBmService($orderId, $newStatusId);
    }

    /**
     * @return ModelPaymentStateModifier
     */
    protected function getStateModifier()
    {
        return $this->model_payment_state_modifier;
    }
}