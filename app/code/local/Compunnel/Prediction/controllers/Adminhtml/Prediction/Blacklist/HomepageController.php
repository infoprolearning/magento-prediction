<?php
    class Compunnel_Prediction_Adminhtml_Prediction_Blacklist_HomepageController extends Mage_Adminhtml_Controller_Action
    {

        protected function _initAction()
        {
            $this->_title($this->__('Recommendation Engine'))->_title($this->__('Blacklist'))->_title($this->__('Homepage Blacklist'));

            $this->loadLayout()->_setActiveMenu('prediction/blacklist/blacklist_homepage')
                ->_addBreadcrumb(Mage::helper('prediction')->__('Recommendation Engine'), Mage::helper('prediction')->__('Recommendation Engine'))
                ->_addBreadcrumb(Mage::helper('prediction')->__('Manage Homepage Blacklist'), Mage::helper('prediction')->__('Manage Homepage Blacklist'));
            return $this;
        }

        public function indexAction()
        {
            $this->_initAction()->renderLayout();
        }

        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('prediction/blacklist/blacklist_homepage');
        }

    }
