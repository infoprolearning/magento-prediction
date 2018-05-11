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
class Compunnel_Prediction_Model_Suggestion_Api extends Mage_Api_Model_Resource_Abstract
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
        );

    protected $_storeIdSessionField = 'store_id';

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    public function items($filters = null, $store = null, $attributes = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->setCurPage(1);

        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);

        $data = array();

        if (isset($filters['qty'])) {
            $data['num'] = $filters['qty'];
        }
        else {
            $data['num'] = 5;
        }

        if (isset($filters['category_id'])) {
        }
        if (isset($filters['category_name'])) {
        }
        if (isset($filters['customer_id'])) {
            $data['user'] = $filters['customer_id'];
        }
        if (isset($filters['customer_email'])) {
            $customer = Mage::getModel('customer/customer')->loadByEmail($filters['customer_email']);
            if ($customer->getId()) {
                $data['user'] = $customer->getId();
            }
        }

        $result = Mage::helper('prediction')->makeRecommendationCall($data, $this->_getStoreId($store));
        $results = json_decode($result, true);
        if (isset($results['itemScores']) && !empty($results['itemScores'])) {
            $apiProducts = array();
            foreach ($results['itemScores'] as $key => $value) {
                $apiProducts[] = $value['item'];
            }

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in' => $apiProducts))
                ->addStoreFilter($this->_getStoreId($store))
                ->addAttributeToSelect('name')
                ->setCurPage(1);;
        }

        $collection->setPageSize($data['num']);
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

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

    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField) ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
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
