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
 * SOAP version 1 adaptor for Recommendation Engine
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */
class Compunnel_Prediction_Model_Suggestion_Api extends Mage_Api_Model_Resource_Abstract
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
        );

    protected $_storeIdSessionField = 'store_id';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * End-point for list method in SOAP v1
     *
     * @param array   $filters    array of filters to apply on query
     * @param integer $store      numerical ID of Magento store
     * @param array   $attributes additional attributes that are required
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
                $this->getStoreId(
                    $store
                )
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
            if (!empty($attributes)) {
                foreach ($attributes as $key => $value) {
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
                foreach ($additionalData as $_key => $_value) {
                    if ($value['product_id'] == $_value['product_id']) {
                        $result[$key][$_value['key']] = $_value['value'];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Extract customer related attributes from API request and add them
     * as part of API request data
     *
     * @param array $filters input parameters of Magento API call
     * @param array $data    request array for API call to forward
     *
     * @return array
     */
    protected function extractCustomerParameters($filters, $data)
    {
        if (isset($filters['customer_id'])) {
            $data['user'] = $filters['customer_id'];
        } else {
            if (isset($filters['customer_email'])) {
                $customer = Mage::getModel('customer/customer')
                    ->loadByEmail(
                        $filters['customer_email']
                    );
                if ($customer->getId()) {
                    $data['user'] = $customer->getId();
                }
            }
        }
        return $data;
    }

    /**
     * Extract product related filters from API request and add them
     * as part of Prediction API call
     *
     * @param array $filters input parameters from Magento API call
     * @param array $data    request array for Prediction API call
     *
     * @return array
     */
    protected function extractProductParameters($filters, $data)
    {
        if (isset($filters['product_id'])) {
            $data['item'] = $filters['product_id'];
        } else {
            if (isset($filters['product_sku'])) {
                $productId = Mage::getModel('catalog/product')
                    ->getIdBySku($filters['product_sku']);
                if ($productId) {
                    $data['item'] = $productId;
                }
            }
        }
        return $data;
    }

    /**
     * Extract cart related attributes from API request and add them
     * as part of Prediction API request data
     *
     * @param array $filters input parameters from Magento API call
     * @param array $data    request array for Prediction API call
     *
     * @return array
     */
    protected function extractCartParameters($filters, $data)
    {
        $cartItemIds = array();
        if (isset($filters['cart_item_ids'])) {
            $cartItemIds = explode(',', $filters['cart_item_ids']);
        } else {
            if (isset($filters['cart_item_skus'])) {
                $cartItemSkus = explode(',', $filters['cart_item_skus']);
                foreach ($cartItemSkus as $key => $value) {
                    $cartItemId = Mage::getModel('catalog/product')
                        ->getIdBySku($value);
                    if ($cartItemId) {
                        $cartItemIds[] = $cartItemId;
                    }
                }
            }
        }

        if (!empty($cartItemIds)) {
            $data['itemSet'] = $cartItemIds;
        }
        return $data;
    }

    /**
     * Initialize Magento product
     *
     * @param integer $productId Magento product ID
     * @param integer $store     Magento store ID
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function initializeProduct($productId, $store = null)
    {
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($store);
        $product->load($productId);
        if ($product->getId()) {
            return $product;
        }
        $this->_fault('unable_to_load_product');
    }

    /**
     * Generate Magento store ID from input value
     *
     * @param object $store Store object
     *
     * @return void
     */
    protected function getStoreId($store = null)
    {
        if (is_null($store)) {
            if ($this->_getSession()->hasData($this->_storeIdSessionField)) {
                $store = $this->_getSession()
                    ->getData(
                        $this->_storeIdSessionField
                    );
            } else {
                $store = 0;
            }
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        }
        catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }
        return $storeId;
    }

}
