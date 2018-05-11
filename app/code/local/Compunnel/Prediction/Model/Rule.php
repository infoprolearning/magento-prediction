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
class Compunnel_Prediction_Model_Rule extends Mage_Core_Model_Abstract
{

    protected function _afterSave()
    {
        $this->getLinkInstance()->saveProductRelations($this);
        $result = parent::_afterSave();

        return $result;
    }

    public function loadPost(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, array('from_date', 'to_date')) && $value) {
                $value = Mage::app()->getLocale()->date(
                    $value,
                    Varien_Date::DATE_INTERNAL_FORMAT,
                    null,
                    false
                    );
            }
            $this->setData($key, $value);
        }

        return $this;
    }

    public function validateData(Varien_Object $object)
    {
        $result   = array();
        $fromDate = $toDate = null;

        if ($object->hasFromDate() && $object->hasToDate()) {
            $fromDate = $object->getFromDate();
            $toDate = $object->getToDate();
        }

        if ($fromDate && $toDate) {
            $fromDate = new Zend_Date($fromDate, Varien_Date::DATE_INTERNAL_FORMAT);
            $toDate = new Zend_Date($toDate, Varien_Date::DATE_INTERNAL_FORMAT);

            if ($fromDate->compare($toDate) === 1) {
                $result[] = Mage::helper('prediction')->__('End Date must be greater than Start Date.');
            }
        }

        if ($object->hasStoreIds()) {
            $storeIds = $object->getStoreIds();
            if (empty($storeIds)) {
                $result[] = Mage::helper('prediction')->__('Stores must be specified.');
            }
        }
        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (empty($customerGroupIds)) {
                $result[] = Mage::helper('rule')->__('Customer Groups must be specified.');
            }
        }

        return !empty($result) ? $result : true;
    }

    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }
        return $this->_getData('store_ids');
    }

    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

}
