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
class Compunnel_Prediction_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $entityInfo = $this->_getMainEntityInfo();
        $this->_init($entityInfo['table_alias'], $entityInfo['primary_key']);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasStoreIds()) {
            $storeIds = $object->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->bindRuleToEntity($object->getId(), $storeIds, 'store');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        parent::_afterSave($object);
        return $this;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));
        $object->setData('store_ids', (array)$this->getStoreIds($object->getId()));
        return parent::_afterLoad($object);
    }

    public function getAssociatedEntityIds($ruleId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable($entityInfo['associations_table']), array($entityInfo['entity_id_field']))
            ->where($entityInfo['rule_id_field'] . ' = ?', $ruleId);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getStoreIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'store');
    }

    public function getCustomerGroupIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'customer_group');
    }

    protected function _getMainEntityInfo()
    {
        if (isset($this->_entityMap)) {
            return $this->_entityMap;
        }

        $e = Mage::exception('Mage_Core', Mage::helper('prediction')->__('There is no information about main entity.'));
        throw $e;
    }

    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        $e = Mage::exception('Mage_Core', Mage::helper('prediction')->__('There is no information about associated entity type "%s".', $entityType));
        throw $e;
    }

    public function bindRuleToEntity($ruleIds, $entityIds, $entityType, $deleteOldResults = true)
    {
        if (empty($ruleIds) || empty($entityIds)) {
            return $this;
        }

        $adapter    = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($ruleIds)) {
            $ruleIds = array((int) $ruleIds);
        }
        if (!is_array($entityIds)) {
            $entityIds = array((int) $entityIds);
        }

        $data  = array();
        $count = 0;

        $adapter->beginTransaction();

        try {
            foreach ($ruleIds as $ruleId) {
                foreach ($entityIds as $entityId) {
                    $data[] = array(
                        $entityInfo['entity_id_field'] => $entityId,
                        $entityInfo['rule_id_field'] => $ruleId
                        );
                    $count++;
                    if (($count % 1000) == 0) {
                        $adapter->insertOnDuplicate(
                            $this->getTable($entityInfo['associations_table']),
                            $data,
                            array($entityInfo['rule_id_field'])
                            );
                        $data = array();
                    }
                }
            }
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $this->getTable($entityInfo['associations_table']),
                    $data,
                    array($entityInfo['rule_id_field'])
                    );
            }

            if ($deleteOldResults) {
                $adapter->delete(
                    $this->getTable($entityInfo['associations_table']),
                    $adapter->quoteInto($entityInfo['rule_id_field']   . ' IN (?) AND ', $ruleIds) .
                    $adapter->quoteInto($entityInfo['entity_id_field'] . ' NOT IN (?)',  $entityIds)
                    );
            }
        }
        catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();
        return $this;
    }

}
