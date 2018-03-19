<?php
    class Compunnel_Prediction_Block_Recommendations_Home extends Compunnel_Prediction_Block_Recommendations_Abstract {

        protected function _construct() {
            parent::_construct();
            $this->setTitle($this->__('Titles you might be interested in'));
        }

        protected function _getProductCollection() {
            if (is_null($this->_productCollection)) {

                $data = array();
                $data['num'] = 5;
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $customerData = Mage::getSingleton('customer/session')->getCustomer();
                    $data['user'] = $customerData->getId();
                }

                $result = Mage::helper('prediction')->makeRecommendationCall($data);

                $results = json_decode($result, true);
                if (isset($results['itemScores']) && !empty($results['itemScores'])) {
                    $apiProducts = array();
                    foreach ($results['itemScores'] as $key => $value) {
                        $apiProducts[] = $value['item'];
                    }

                    $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToFilter('entity_id', array('in' => $apiProducts));

                    $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

                    $collection = $this->_addProductAttributesAndPrices($collection)
                        ->addStoreFilter()
                        ->setPageSize($this->getProductsCount())
                        ->setCurPage(1);
                    $this->_productCollection = $collection;
                }
            }

            return $this->_productCollection;
        }

    }
