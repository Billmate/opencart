<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';

class ModelPaymentBillmateConfig extends Model
{
    /**
     * @return array
     */
    public function getAllowedStatuses()
    {
        return array_keys($this->getProcessorsMap());
    }

    /**
     * @return array
     */
    public function getProcessorsMap()
    {
        /***
         * 2 - Processing
         * 7 - Cancelled
         * 11 - Refunded
         */
        return $processorsMap = [
            2 => 'activate',
            7 => 'cancel',
            11 => 'refund',
        ];
    }

    public function getBMConnection($method_code = 'billmate_cardpay')
    {
        $merchantId = (int)$this->config->get($method_code . '_merchant_id');
        $secretKey  = $this->config->get($method_code . '_secret');
        $testMode   = $this->config->get($method_code . '_test');
        require_once dirname(DIR_APPLICATION) . '/billmate/Billmate.php';
        return new BillMate($merchantId, $secretKey, true, $testMode);
    }

}