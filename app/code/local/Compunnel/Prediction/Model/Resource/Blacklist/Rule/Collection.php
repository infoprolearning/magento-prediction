<?php
    class Compunnel_Prediction_Model_Resource_Blacklist_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
    {

        protected function _construct()
        {
            $this->_init('prediction/blacklist_rule');
        }

    }
