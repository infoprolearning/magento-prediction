<?php
    class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
    {

        public function __construct()
        {
            parent::__construct();
            $this->setId('adminhtml_blacklist_homepage_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('prediction')->__('Rule Information'));
        }

        protected function _beforeToHtml()
        {
            $this->addTab('main', array(
                'label'     => Mage::helper('prediction')->__('General Information'),
                'title'     => Mage::helper('prediction')->__('General Information'),
                'content'   => $this->getLayout()->createBlock('prediction/adminhtml_blacklist_homepage_edit_tab_main')->toHtml(),
                'active'    => true
                ));

            $this->addTab('labels', array(
                'label'     => Mage::helper('prediction')->__('Products'),
                'title'     => Mage::helper('prediction')->__('Products'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
                ));

            return parent::_beforeToHtml();
        }

    }
