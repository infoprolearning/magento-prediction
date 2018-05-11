<?php
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
