<?php
/**
 * PHP version 5
 * 
 * @category  Compunnel
 * @package   Compunnel_Prediction
 * @author    Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @copyright 2018 Compunnel (https://www.compunnel.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link      https://bitbucket.org/prateekatcompunnel/apac-prediction
 */
class Compunnel_Prediction_Model_Resource_Blacklist_Rule_Product extends Mage_Core_Model_Resource_Db_Abstract
{

    const SECONDS_IN_DAY = 86400;

    protected function _construct()
    {
        $this->_init('prediction/blacklist_rule_product', 'rule_product_id');
    }

    public function getProductsByRuleId($ruleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('prediction/blacklist_rule_product'), array('product_id'))
            ->where('rule_id = ?', $ruleId);
        $rawData = $adapter->fetchCol($select);

        $uniqueEntries = array();
        foreach ($rawData as $_productId) {
            $uniqueEntries[$_productId] = 0;
        }
        return array_keys($uniqueEntries);
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

    public function applyHomepageBlacklist($data, $store)
    {
        $roleId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('rp' => $this->getTable('prediction/blacklist_rule_product')))
            ->joinLeft(
                array("rs" => $this->getTable('prediction/blacklist_rule')),
                "rp.rule_id = rs.rule_id",
                array("is_active" => "rs.is_active")
            )
            //->where($read->quoteInto('rp.from_time = 0 or rp.from_time <= ?', $toDate) . ' OR ' . $read->quoteInto('rp.to_time = 0 or rp.to_time >= ?', $fromDate))
            ->where('rp.store_id = ?', $store)
            ->where('rp.customer_group_id = ?', $roleId)
            ->where('rs.is_active = 1');
        $results = $read->query($select);
        $blacklist = array();
        foreach ($results as $key => $value) {
            $blacklist[] = $value['product_id'];
        }

        $data['blacklistItems'] = $blacklist;
        return $data;
    }

}
