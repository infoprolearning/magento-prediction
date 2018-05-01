<?php
    class Compunnel_Prediction_Model_Blacklist_Rule extends Compunnel_Prediction_Model_Rule
    {

        protected $_linkInstance;

        protected function _construct()
        {
            $this->_init('prediction/blacklist_rule');
        }

        public function getLinkInstance()
        {
            if (!$this->_linkInstance) {
                $this->_linkInstance = Mage::getSingleton('prediction/blacklist_rule_product');
            }

            return $this->_linkInstance;
        }

    }
