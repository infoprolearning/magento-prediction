<?php
    class Compunnel_Prediction_Model_Resource_Blacklist_Rule_Product extends Mage_Core_Model_Resource_Db_Abstract
    {

        protected function _construct()
        {
            $this->_init('prediction/blacklist_rule_product', 'rule_product_id');
        }

    }
