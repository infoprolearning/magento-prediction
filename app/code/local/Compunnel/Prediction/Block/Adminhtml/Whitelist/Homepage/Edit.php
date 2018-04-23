<?php
    class Compunnel_Prediction_Block_Adminhtml_Whitelist_Homepage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {

        public function __construct()
        {
            $this->_objectId = 'id';
            $this->_controller = 'adminhtml_whitelist_homepage';

            parent::__construct();

            $this->_addButton('save_and_continue_edit', array(
                'class'   => 'save',
                'label'   => Mage::helper('prediction')->__('Save and Continue Edit'),
                'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
                ), 10);
        }

        public function getHeaderText()
        {
            $rule = Mage::registry('current_prediction_whitelist_homepage_rule');
            if ($rule->getRuleId()) {
                return Mage::helper('prediction')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
            }
            else {
                return Mage::helper('prediction')->__('New Rule');
            }
        }

    }
