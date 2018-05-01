<?php
    class Compunnel_Prediction_Model_Blacklist_Rule_Product extends Mage_Core_Model_Abstract
    {

        protected function _construct()
        {
            $this->_init('prediction/blacklist_rule_product');
        }

        public function saveProductRelations($rule)
        {
            $data = $rule->getRelatedLinkData();
            if (!is_null($data)) {
                $this->_getResource()->saveProductLinks($rule, $data);
            }
        }

    }
