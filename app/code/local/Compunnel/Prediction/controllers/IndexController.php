<?php
    class Compunnel_Prediction_IndexController extends Mage_Core_Controller_Front_Action
    {
        public function indexAction()
        {
            $location = $this->getRequest()->getParam('location', false);

            if (!$location) {
                return null;
            }

            if ($this->getRequest()->getParam('product', false)) {
                $this->_initCurrentProduct();
            }

            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('prediction_index_block_' . $location);
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput();
            $this->getResponse()->setBody($output);
            return $output;
        }

        protected function _initCurrentProduct()
        {
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $productId  = (int) $this->getRequest()->getParam('product');

            $params = new Varien_Object();
            $params->setCategoryId($categoryId);

            return Mage::helper('catalog/product')->initProduct($productId, $this, $params);
        }
    }
