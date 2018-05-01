<?php
    class Compunnel_Prediction_Model_Resource_Blacklist_Rule_Product extends Mage_Core_Model_Resource_Db_Abstract
    {

        const SECONDS_IN_DAY = 86400;

        protected function _construct()
        {
            $this->_init('prediction/blacklist_rule_product', 'rule_product_id');
        }

        public function saveProductLinks($rule, $data)
        {
            if (!is_array($data)) {
                $data = array();
            }

            $ruleId = $rule->getId();
            $write  = $this->_getWriteAdapter();
            $write->beginTransaction();
            $this->cleanProductData();

            if (!$rule->getIsActive()) {
                $write->commit();
                return $this;
            }

            try {
                $this->insertRuleData($rule, $data);
                $write->commit();
            }
            catch (Exception $e) {
                $write->rollback();
                throw $e;
            }

            return $this;
        }

        public function cleanProductData($ruleId, array $productIds = array())
        {
            $write = $this->_getWriteAdapter();
            $conditions = array('rule_id = ?' => $ruleId);
            if (count($productIds) > 0) {
                $conditions['product_id IN (?)'] = $productIds;
            }
            $write->delete($this->getTable('prediction/blacklist_rule_product'), $conditions);
        }

        public function insertRuleData($rule, array $data)
        {
            $write = $this->_getWriteAdapter();

            $customerGroupIds = $rule->getCustomerGroupIds();
            $storeIds = $rule->getStoreIds();
            $fromTime = (int) strtotime($rule->getFromDate());
            $toTime = (int) strtotime($rule->getToDate());
            $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;

            $rows = array();
            foreach ($data as $_key => $productId) {
                foreach ($storeIds as $storeId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = array(
                            'rule_id' => $rule->getId(),
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                            'product_id' => $productId,
                            'store_id' => $storeId,
                            'customer_group_id' => $customerGroupId
                            );

                        if (count($rows) == 1000) {
                            $write->insertMultiple($this->getTable('prediction/blacklist_rule_product'), $rows);
                            $rows = array();
                        }
                    }
                }
            }

            if (!empty($rows)) {
                $write->insertMultiple($this->getTable('prediction/blacklist_rule_product'), $rows);
            }
        }

    }
