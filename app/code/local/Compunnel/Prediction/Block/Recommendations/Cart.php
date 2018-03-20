<?php
    class Compunnel_Prediction_Block_Recommendations_Cart extends Compunnel_Prediction_Block_Recommendations_Abstract {

        protected function _construct() {
            parent::_construct();
            $this->setTitle($this->__('Titles Based on Your History'));
            $this->_listingMedium = 'recommended';
            $this->_listingSource = 'cart';
        }

        protected function _getProductCollection() {
            if (is_null($this->_productCollection)) {

                $data = array();
                $data['num'] = 5;
                $data['itemSet'] = $this->_getCartProductIds();

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

        protected function _getCartProductIds() {
            $ids = $this->getData('_cart_product_ids');
            if (is_null($ids)) {
                $ids = array();
                foreach ($this->getQuote()->getAllItems() as $item) {
                    if ($product = $item->getProduct()) {
                        $ids[] = $product->getId();
                    }
                }
                $this->setData('_cart_product_ids', $ids);
            }
            return $ids;
        }

        public function getQuote() {
            return Mage::getSingleton('checkout/session')->getQuote();
        }

    }
