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
class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_blacklist_homepage';
        $this->_blockGroup = 'prediction';

        parent::__construct();
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_prediction_blacklist_homepage_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('prediction')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('prediction')->__('New Rule');
        }
    }

}
