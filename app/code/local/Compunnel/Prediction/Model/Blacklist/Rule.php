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

    public function applyHomepageBlacklist($data)
    {
        return $this->getLinkInstance()->applyHomepageBlacklist($data);
    }

}
