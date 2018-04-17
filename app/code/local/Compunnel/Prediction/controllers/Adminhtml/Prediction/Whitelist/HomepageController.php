<?php
    class Compunnel_Prediction_Adminhtml_Prediction_Whitelist_HomepageController extends Mage_Adminhtml_Controller_Action
    {

        protected function _initAction()
        {
            $this->_title($this->__('Recommendation Engine'))->_title($this->__('Whitelist'))->_title($this->__('Homepage Whitelist'));

            $this->loadLayout()->_setActiveMenu('prediction/whitelist/whitelist_homepage')
                ->_addBreadcrumb(Mage::helper('prediction')->__('Recommendation Engine'), Mage::helper('prediction')->__('Recommendation Engine'))
                ->_addBreadcrumb(Mage::helper('prediction')->__('Manage Homepage Whitelist'), Mage::helper('prediction')->__('Manage Homepage Whitelist'));
            return $this;
        }

        public function indexAction()
        {
            $this->_initAction()->renderLayout();
        }

        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('prediction/whitelist/whitelist_homepage');
        }

    }
