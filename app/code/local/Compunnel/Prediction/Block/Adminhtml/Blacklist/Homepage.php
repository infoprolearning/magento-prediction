<?php
    class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage extends Mage_Adminhtml_Block_Widget_Grid_Container
    {

        public function __construct()
        {
            $this->_blockGroup = 'prediction';
            $this->_controller = 'adminhtml_blacklist_homepage';
            $this->_headerText = Mage::helper('prediction')->__('Blacklist Rules');
            $this->_addButtonLabel = Mage::helper('prediction')->__('Add New Rule');
            parent::__construct();
        }

    }
