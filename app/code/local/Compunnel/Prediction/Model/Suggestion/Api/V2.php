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

/**
 * SOAP v1 adaptor with WSI compliance for Recommendation Engine
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */
class Compunnel_Prediction_Model_Suggestion_Api_V2 extends Compunnel_Prediction_Model_Suggestion_Api
{
    /**
     * SOAP v1 method with WSI compliance for retrieving suggestions from
     * Prediction server
     *
     * @param array   $filters    input array of filters from Magento API call
     * @param integer $store      Magento store ID
     * @param object  $attributes List of additional attributes that are needed
     *
     * @return array
     */
    public function items($filters = null, $store = null, $attributes = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->getStoreId($store))
            ->addAttributeToSelect('name')
            ->setCurPage(1);

        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);

        $data = array();

        if (isset($filters['qty'])) {
            $data['num'] = $filters['qty'];
        } else {
            $data['num'] = 5;
        }

        if (isset($filters['category_id'])) {
        }
        if (isset($filters['category_name'])) {
        }

        $data = $this->extractCustomerParameters($filters, $data);

        $data = $this->extractProductParameters($filters, $data);

        $data = $this->extractCartParameters($filters, $data);

        $result = Mage::helper('prediction')
            ->makeRecommendationCall(
                $data,
                $this->getStoreId($store)
            );
        $results = json_decode($result, true);
        if (isset($results['itemScores']) && !empty($results['itemScores'])) {
            $apiProducts = array();
            foreach ($results['itemScores'] as $key => $value) {
                $apiProducts[] = $value['item'];
            }

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $apiProducts))
                ->addStoreFilter($this->getStoreId($store))
                ->addAttributeToSelect('name')
                ->setCurPage(1);;
        }

        $collection->setPageSize($data['num']);
        $collection->setVisibility(
            Mage::getSingleton('catalog/product_visibility')
                ->getVisibleInCatalogIds()
        );

        $additionalAttributes = array();
        if (!empty($attributes->attributes)) {
            $additionalAttributes = array_merge(
                $additionalAttributes,
                $attributes->attributes
            );
        }

        $result = array();
        $additionalData = array();
        foreach ($collection as $product) {
            $product = $this->initializeProduct(
                $product->getId(),
                $this->getStoreId($store)
            );
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
                'final_price'       => $product->getFinalPrice(),
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
                        $result[$key]['additional_attributes'] = array(
                            $_additionalAttributeCounter => array(
                                'key'   => $_value['key'],
                                'value' => $_value['value']
                            )
                        );
                        $_additionalAttributeCounter++;
                    }
                }
            }
        }
        return $result;
    }
}
