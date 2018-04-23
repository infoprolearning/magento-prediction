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

        public function gridAction()
        {
            $this->loadLayout();
            $this->renderLayout();
        }

        public function newAction()
        {
            $this->_forward('edit');
        }

        public function editAction()
        {
            $this->_title($this->__('Recommendation Engine'))->_title($this->__('Homepage Blacklist Rules'));

            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('prediction/blacklist_rule');

            if ($id) {
                $model->load($id);
                if (! $model->getRuleId()) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('prediction')->__('This rule no longer exists.')
                        );
                    $this->_redirect('*/*');
                    return;
                }
            }

            $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

            $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
            if (!empty($data)) {
                $model->addData($data);
            }

            Mage::register('current_prediction_blacklist_homepage_rule', $model);

            $this->_initAction()->getLayout()->getBlock('prediction_blacklist_homepage_edit')->setData('action', $this->getUrl('*/prediction_blacklist_homepage/save'));

            $breadcrumb = $id ? Mage::helper('prediction')->__('Edit Rule') : Mage::helper('prediction')->__('New Rule');
            $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();
        }

        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('prediction/blacklist/blacklist_homepage');
        }

    }
