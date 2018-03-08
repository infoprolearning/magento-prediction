<?php
    class Compunnel_Prediction_Block_Recommendations_Abstract extends Mage_Catalog_Block_Product_Abstract {

        const DEFAULT_PRODUCTS_COUNT = 5;

        protected $_productsCount;

        protected $_productCollection;

        protected $_defaultColumnCount = 5;

        public function getColumnCount() {
            if (!$this->_getData('column_count')) {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
            return (int) $this->_getData('column_count');
        }

        public function getLoadedProductCollection() {
            return $this->_getProductCollection();
        }

        protected function _getProductCollection() {
            if (is_null($this->_productCollection)) {
                $collection = Mage::getResourceModel('catalog/product_collection');
                $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

                $collection = $this->_addProductAttributesAndPrices($collection)
                    ->addStoreFilter()
                    ->setPageSize($this->getProductsCount())
                    ->setCurPage(1);
                $this->_productCollection = $collection;
            }

            return $this->_productCollection;
        }

        protected function _beforeToHtml() {
            $this->setProductCollection($this->_getProductCollection());
            return parent::_beforeToHtml();
        }

        public function setProductsCount($count) {
            $this->_productsCount = $count;
            return $this;
        }

        public function getProductsCount() {
            if (null === $this->_productsCount) {
                $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
            }
            return $this->_productsCount;
        }

        public function addColumnCount($count) {
            $this->setData('column_count', $count);
        }

    }
