<?php

class ModelPaymentBillmateService extends Model
{
    /**
     * @var string
     */
    protected $_table = 'billmate_order_invoice';

	public function __construct($registry)
    {
        parent::__construct($registry);
    }

    /**
     * @param $invoiceId int
     * @param $orderId int
     */
    public function addInvoiceIdToOrder($orderId, $invoiceId)
	{
	    if ($invoiceId) {
            $this->createInvoiceTable();
            $this->db->query(
                'INSERT INTO ' . DB_PREFIX . $this->_table . ' (`order_id`, `invoice_id`)
                VALUES ( ' . (int)$orderId . ',"' . $this->db->escape($invoiceId) . '")
                ON DUPLICATE KEY UPDATE `invoice_id` = "' . $this->db->escape($invoiceId) . '"'
            );
        }
	}

    /**
     * @param $orderId
     *
     * @return mixed
     */
	public function getInvoiceId($orderId)
	{
        $query = $this->db->query("SELECT `invoice_id` FROM `" . DB_PREFIX . $this->_table . "`
            WHERE order_id = '" . (int)$orderId . "' LIMIT 1" );

        $result = $query->row;
        if($result) {
            return $result['invoice_id'];
        }
        
        return false;
	}

	protected function createInvoiceTable()
    {
        $this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $this->_table . "` (
		  `bm_invoice_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL ,
		  `invoice_id` varchar(255) NOT NULL ,
		  PRIMARY KEY (`bm_invoice_id`),
		  UNIQUE (`order_id`)
		);");
    }
}
