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

    protected function _initRule()
    {
        $ruleId = (int) $this->getRequest()->getParam('id');
        $rule = Mage::getModel('prediction/blacklist_rule');

        $rule->setData('_edit_mode', true);
        if ($ruleId) {
            try {
                $rule->load($ruleId);
            }
            catch (Exception $e) {
                Mage::logException($e);
            }
        }
        Mage::register('current_prediction_blacklist_homepage_rule', $rule);
        return $rule;
    }

    public function relatedAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->getLayout()->getBlock('prediction.blacklist.homepage.edit.tab.products')->setProducts($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    public function relatedGridAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->getLayout()->getBlock('prediction.blacklist.homepage.edit.tab.products')->setProducts($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Recommendation Engine'))->_title($this->__('Homepage Blacklist Rules'));

        $id = $this->getRequest()->getParam('id');
        $model = $this->_initRule();

        if ($id) {
            if (!$model->getRuleId()) {
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

        $this->_initAction()->getLayout()->getBlock('prediction_blacklist_homepage_edit')->setData('action', $this->getUrl('*/prediction_blacklist_homepage/save'));

        $breadcrumb = $id ? Mage::helper('prediction')->__('Edit Rule') : Mage::helper('prediction')->__('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();
    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('prediction/blacklist_rule');
                Mage::dispatchEvent('adminhtml_controller_prediction_blacklist_rule_prepare_save', array('request' => $this->getRequest()));

                $ruleData = $this->getRequest()->getPost('rule');

                $ruleData = $this->_filterDates($ruleData, array('from_date', 'to_date'));

                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('prediction')->__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new Varien_Object($ruleData));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($ruleData);
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $links = $this->getRequest()->getPost('links');
                if (isset($links['related'])) {
                    $model->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
                }

                $model->loadPost($ruleData);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('prediction')->__('The rule has been saved.'));

                Mage::getSingleton('adminhtml/session')->setPageData(false);
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('prediction')->__('An error occurred while saving the rule data. Please review the log and try again.')
                    );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('prediction/blacklist_rule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('prediction')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('prediction')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('prediction')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('prediction/blacklist/blacklist_homepage');
    }

}
