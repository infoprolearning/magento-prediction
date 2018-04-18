<?php
    class Compunnel_Prediction_Model_Resource_Whitelist_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
    {

        protected function _construct()
        {
            $this->_init('prediction/whitelist_rule');
        }

    }
