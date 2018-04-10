<?php
    class Compunnel_Prediction_Model_Suggestion_Api_V2 extends Compunnel_Prediction_Model_Suggestion_Api
    {
        public function items($filters = null, $store = null, $attributes = null)
        {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addStoreFilter($this->_getStoreId($store))
                ->addAttributeToSelect('name')
                ->setCurPage(1);

            $apiHelper = Mage::helper('api');
            $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);

            if (isset($filters['qty'])) {
                $collection->setPageSize($filters['qty']);
            }

            $additionalAttributes = array();
            if (!empty($attributes->attributes)) {
                $additionalAttributes = array_merge($additionalAttributes, $attributes->attributes);
            }

            $result = array();
            $additionalData = array();
            foreach ($collection as $product) {
                $product = $this->initializeProduct($product->getId(), $this->_getStoreId($store));
                $result[] = array(
                    'product_id'        => $product->getId(),
                    'sku'               => $product->getSku(),
                    'name'              => $product->getName(),
                    'set'               => $product->getAttributeSetId(),
                    'type'              => $product->getTypeId(),
                    'category_ids'      => $product->getCategoryIds(),
                    'website_ids'       => $product->getWebsiteIds(),
                    'short_description' => $product->getShortDescription(),
                    'status'            => $product->getStatus(),
                    'url_key'           => $product->getUrlKey(),
                    'url_path'          => $product->getUrlPath(),
                    'visibility'        => $product->getVisibility(),
                    'price'             => $product->getPrice(),
                    'special_price'     => $product->getSpecialPrice(),
                    'special_from_date' => $product->getSpecialFromDate(),
                    'special_to_date'   => $product->getSpecialToDate(),
                    'tax_class_id'      => $product->getTaxClassId()
                    );
                if (!empty($additionalAttributes)) {
                    foreach ($additionalAttributes as $key => $value) {
                        $additionalData[] = array(
                            'product_id' => $product->getId(),
                            'key' => $value,
                            'value' => $product->getData($value)
                        );
                    }
                }
            }

            if (!empty($additionalData)) {
                foreach ($result as $key => $value) {
                    $_additionalAttributeCounter = 0;
                    foreach ($additionalData as $_key => $_value) {
                        if ($value['product_id'] == $_value['product_id']) {
                            $result[$key]['additional_attributes'][$_additionalAttributeCounter]['key'] = $_value['key'];
                            $result[$key]['additional_attributes'][$_additionalAttributeCounter]['value'] = $_value['value'];
                            $_additionalAttributeCounter++;
                        }
                    }
                }
            }
            return $result;
        }
    }
