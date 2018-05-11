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
 * Prediction abstract helper
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */

class Compunnel_Prediction_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    const RECOMMENDATION_LOCATION_HOME = 'home_recommendations';

    /**
     * Checks if recommendation engine is enabled
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return bool
     */
    public function isEngineEnabled($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/general/enabled',
            $storeId
        );
    }

    /**
     * Get public URL of recommendation engine
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    public function getEngineUrl($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/general/engine_url',
            $storeId
        );
    }

    /**
     * Get identifier of app deployed on recommendation engine
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    public function getAppId($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/general/access_key',
            $storeId
        );
    }

    /**
     * Get title of bucket on homepage if user is guest
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    public function getHomeTitleGuest($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/home_recommendations/title_guest',
            $storeId
        );
    }

    /**
     * Get title of bucket on homepage if user is a logged-in customer
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    public function getHomeTitleCustomer($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/home_recommendations/title_logged',
            $storeId
        );
    }

    /**
     * Get title of bucket on homepage if user is a visitor with history,
     * but not logged-in
     *
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    public function getHomeTitleVisitor($storeId = '')
    {
        return $this->_getAdminConfiguration(
            'prediction/home_recommendations/title_past',
            $storeId
        );
    }

    /**
     * Get qty of products to be displayed at a given location
     *
     * @param string  $location area where titles will be displayed
     * @param integer $storeId  numerical ID of Magento store
     *
     * @return integer
     */
    protected function getRecommendationQty($location, $storeId)
    {
        $path = 'prediction/' . $location . '/qty';
        return $this->_getAdminConfiguration(
            $path,
            $storeId
        );
    }

    /**
     * Get configuration saved in Magento admin area
     *
     * @param string  $path    admin configuration path
     * @param integer $storeId numerical ID of Magento store
     *
     * @return string
     */
    private function _getAdminConfiguration($path, $storeId = '')
    {
        if ($storeId == '') {
            $storeId = Mage::app()->getStore()->getId();
        }
        return Mage::getStoreConfig($path, $storeId);
    }
}
