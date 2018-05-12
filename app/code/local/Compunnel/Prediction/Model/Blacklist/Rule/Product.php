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

    public function getProductsByRuleId($ruleId)
    {
        return $this->_getResource()->getProductsByRuleId($ruleId);
    }

    public function applyHomepageBlacklist($data, $store = 0)
    {
        if ($store == 0) {
            $store = Mage::app()->getStore()->getId();
        }
        return $this->_getResource()->applyHomepageBlacklist($data, $store);
    }

}
