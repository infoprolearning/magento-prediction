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
class Compunnel_Prediction_Block_Recommendations_Product extends Compunnel_Prediction_Block_Recommendations_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Items you might be inrested in'));
        $this->_listingMedium = 'recommended';
        $this->_listingSource = 'product';
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection) && Mage::registry('product')) {
            $data = array();
            $product = Mage::registry('product');

            $itemId = $product->getId();
            $data['item'] = $itemId;
            $data['num'] = 5;
            $data['returnSelf'] = false;

            $result = Mage::helper('prediction')->makeRecommendationCall($data);

            $results = json_decode($result, true);
            if (isset($results['itemScores']) && !empty($results['itemScores'])) {
                $apiProducts = array();
                foreach ($results['itemScores'] as $key => $value) {
                    $apiProducts[] = $value['item'];
                }

                $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('entity_id', array('in' => $apiProducts));

                $collection->setVisibility(
                    Mage::getSingleton('catalog/product_visibility')
                        ->getVisibleInCatalogIds()
                );

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
