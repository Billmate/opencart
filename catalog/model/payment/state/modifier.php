<?php
ini_set('display_errors',1);
class ModelPaymentStateModifier extends Model
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('payment/service/factory');
        $this->load->model('payment/billmate/config');
    }

    /**
     * @param $orderId
     * @param $newStatusId
     */
    public function updateBmService($orderId, $newStatusId)
    {
        if ($this->isStatusAllowedToProcess($newStatusId)) {
            $requestModel = $this->createServiceRequestModel($newStatusId);
            $requestModel->process($orderId);
        }
    }

    /**
     * @param $statusId
     *
     * @return ModelPaymentServiceProcessorInterface
     */
    private function createServiceRequestModel($statusId)
    {
        return $this->serviceFactory()->getProcessor($statusId);
    }

    /**
     * @param $newStatusId
     *
     * @return bool
     */
    private function isStatusAllowedToProcess($newStatusId)
    {
        $allowedStatuses = $this->getBillmateConfig()->getAllowedStatuses();
        return in_array($newStatusId, $allowedStatuses);
    }

    /**
     * @return ModelPaymentServiceFactory
     */
    private function serviceFactory()
    {
        return $this->model_payment_service_factory;
    }

    /**
     * @return ModelPaymentBillmateConfig
     */
    private function getBillmateConfig()
    {
        return $this->model_payment_billmate_config;
    }
}
