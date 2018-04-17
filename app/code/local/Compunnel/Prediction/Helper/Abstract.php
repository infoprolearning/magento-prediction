<?php
    class Compunnel_Prediction_Helper_Abstract extends Mage_Core_Helper_Abstract
    {
        const RECOMMENDATION_LOCATION_HOME = 'home_recommendations';

        public function isEngineEnabled($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/general/enabled', $storeId);
        }

        public function getEngineUrl($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/general/engine_url', $storeId);
        }

        public function getAppId($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/general/access_key', $storeId);
        }

        public function getHomeTitleGuest($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/home_recommendations/title_guest', $storeId);
        }

        public function getHomeTitleCustomer($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/home_recommendations/title_logged', $storeId);
        }

        public function getHomeTitleVisitor($storeId = '')
        {
            return $this->_getAdminConfiguration('prediction/home_recommendations/title_past', $storeId);
        }

        protected function getRecommendationQty($location, $storeId)
        {
            $path = 'prediction/' . $location . '/qty';
            return $this->_getAdminConfiguration($path, $storeId);
        }

        private function _getAdminConfiguration($path, $storeId = '')
        {
            if ($storeId == '') {
                $storeId = Mage::app()->getStore()->getId();
            }
            return Mage::getStoreConfig($path, $storeId);
        }
    }
