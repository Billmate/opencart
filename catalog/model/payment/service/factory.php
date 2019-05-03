<?php
class ModelPaymentServiceFactory extends Model
{
    /**
     * ModelPaymentServiceFactory constructor.
     *
     * @param $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('payment/billmate/config');
    }

    /**
     * @param $statusId
     */
    public function getProcessor($statusId)
    {
        $processorsMap = $this->getProcessorsMap();
        if (isset($processorsMap[$statusId])) {
            $typeProcess = $processorsMap[$statusId];
            $this->load->model('payment/service/processor/' . $typeProcess);
            $modelCode='model_payment_service_processor_'. $typeProcess;
            return $this->{$modelCode};
        }
        throw new \Exception(
            'Error: Could not load service processor for status ' . $statusId . '!'
        );
    }

    /**
     * @return array
     */
    public function getProcessorsMap()
    {
        return $this->getBillmateConfig()->getProcessorsMap();
    }

    /**
     * @return ModelPaymentBillmateConfig
     */
    private function getBillmateConfig()
    {
        return $this->model_payment_billmate_config;
    }
}