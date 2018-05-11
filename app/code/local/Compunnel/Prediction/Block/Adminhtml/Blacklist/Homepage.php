<?php
class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'prediction';
        $this->_controller = 'adminhtml_blacklist_homepage';
        $this->_headerText = Mage::helper('prediction')->__('Blacklist Rules');
        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('prediction')->__('Add New Rule'));
        }
        else {
            $this->_removeButton('add');
        }
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('prediction/blacklist/blacklist_homepage/' . $action);
    }

}
