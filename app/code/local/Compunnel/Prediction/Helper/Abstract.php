<?php
    class Compunnel_Prediction_Helper_Abstract extends Mage_Core_Helper_Abstract
    {
        public function isModuleEnabled($storeId = '')
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

        private function _getAdminConfiguration($path, $storeId = '')
        {
            if ($storeId == '') {
                $storeId = Mage::app()->getStore()->getId();
            }
            return Mage::getStoreConfig($path, $storeId);
        }
    }
