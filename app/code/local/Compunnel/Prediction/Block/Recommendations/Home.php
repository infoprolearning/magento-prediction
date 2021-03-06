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
  * Prediction home page recommendation block
  *
  * @category Compunnel
  * @package  Compunnel_Prediction
  * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
  * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
  * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
  */
class Compunnel_Prediction_Block_Recommendations_Home extends Compunnel_Prediction_Block_Recommendations_Abstract
{
    /**
     * Magento constructor to set initial properties on block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setTitle($this->__('Inspired by your purchases'));
            $this->setTitle(Mage::helper('prediction')->getHomeTitleCustomer());
        } else {
            if (Mage::helper('prediction')->isVisitorNew()) {
                $this->setTitle(Mage::helper('prediction')->getHomeTitleGuest());
            } else {
                $this->setTitle(Mage::helper('prediction')->getHomeTitleVisitor());
            }
        }

        $this->_listingMedium = 'recommended';
        $this->_listingSource = 'home';
    }

    /**
     * Initialize product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $data = array();
            $data['num'] = (int) Mage::helper('prediction')
                ->getNoOfRecommendations(
                    Compunnel_Prediction_Helper_Abstract::RECOMMENDATION_LOCATION_HOME
                );
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerData = Mage::getSingleton('customer/session')
                    ->getCustomer();
                $data['user'] = $customerData->getId();
            }

            $data = Mage::getModel('prediction/blacklist_rule')
                ->applyHomepageBlacklist($data);

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
